<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\Card;
use App\Models\DetectedItem;
use App\Models\ProductLink;
use App\Models\StyleImage;
use App\Services\GoogleVisionService;
use App\Services\AmazonProductService;

class PhotoAnalysisController extends Controller
{
    protected $visionService;
    protected $amazonService;

    public function __construct(GoogleVisionService $visionService, AmazonProductService $amazonService)
    {
        $this->visionService = $visionService;
        $this->amazonService = $amazonService;
    }

    public function analyzePhoto(Request $request)
    {
        $request->validate([
            'photos' => 'required|array|min:1|max:5',
            'photos.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240',
            'links' => 'nullable|array',
            'links.*.title' => 'required_with:links.*.url|string|max:255',
            'links.*.url' => 'required_with:links.*.title|url|max:500',
            'links.*.platform' => 'nullable|string|max:255',
            'links.*.price' => 'nullable|string|max:255',
            'tags' => 'nullable|string',
            'description' => 'nullable|string|max:1000',
        ], [
            'photos.required' => 'Please select at least one image to analyze.',
            'photos.min' => 'Please select at least one image.',
            'photos.max' => 'You can upload a maximum of 5 images.',
            'photos.*.required' => 'Each image is required.',
            'photos.*.image' => 'All files must be images.',
            'photos.*.mimes' => 'All images must be of type: jpeg, png, jpg, gif.',
            'photos.*.max' => 'Each image may not be greater than 10MB.',
            'links.*.title.required_with' => 'Link title is required when URL is provided.',
            'links.*.url.required_with' => 'Link URL is required when title is provided.',
            'links.*.url.url' => 'Please provide valid URLs for product links.',
        ]);

        $user = $request->user();
        $photos = $request->file('photos');
        $photoCount = count($photos);

        // Check if user has enough style limit (counts as 1 style regardless of image count)
        if ($user) {
            $currentStyleCount = Card::where('user_id', $user->id)->count();
            $styleLimit = $user->styleLimit();

            if ($currentStyleCount + 1 > $styleLimit) {
                return response()->json([
                    'success' => false,
                    'message' => "You can only add {$styleLimit} styles total. You currently have {$currentStyleCount} styles. Please upgrade your subscription to add more.",
                    'upgrade_url' => route('subscriptions'),
                ], 403);
            }
        } else {
            // For non-authenticated users, we still need to check basic limits
            // This shouldn't happen since the route requires auth, but just in case
            return response()->json([
                'success' => false,
                'message' => 'You must be logged in to add styles.',
            ], 401);
        }

        DB::beginTransaction();

        try {
            // Process form data (shared across all images)
            $linksData = $request->input('links', []);
            // Filter out empty links (where both title and url are empty)
            $links = array_filter($linksData, function($link) {
                return !empty($link['title']) && !empty($link['url']);
            });
            $tags = $request->input('tags') ? explode(',', $request->input('tags')) : [];
            $tags = array_map('trim', $tags);
            $tags = array_filter($tags);
            $description = $request->input('description');

            // Store all images in an array
            $allImages = [];
            $firstImage = null;
            $allDetectedItems = [];
            $allConfidenceScores = [];
            $allSimilarProducts = [];
            $totalFileSize = 0;
            $firstDetectedItem = null;
            $processedPaths = []; // Track processed paths to prevent duplicates

            // Process and store all uploaded photos
            foreach ($photos as $index => $photo) {
                // Store the uploaded image
                $filename = Str::uuid() . '.' . $photo->getClientOriginalExtension();
                $path = $photo->storeAs('uploads', $filename, 'local');
                $imageUrl = route('serve-upload', ['filename' => $filename]);

                // Skip if we've already processed this path (prevent duplicates)
                if (in_array($path, $processedPaths)) {
                    continue;
                }

                // Get image dimensions
                $dimensions = getimagesize($photo->getPathname());
                $dimensionsStr = $dimensions ? "{$dimensions[0]}x{$dimensions[1]}" : null;

                // Store image info
                $imageData = [
                    'path' => $path,
                    'url' => $imageUrl,
                    'filename' => $filename,
                    'original_filename' => $photo->getClientOriginalName(),
                    'file_size' => $photo->getSize(),
                    'dimensions' => $dimensionsStr,
                ];

                $allImages[] = $imageData;
                $processedPaths[] = $path;
                $totalFileSize += $photo->getSize();

                // Store first image for main image_path/image_url (backward compatibility)
                if ($index === 0) {
                    $firstImage = $imageData;
                }

                // Perform clothing detection with Google Vision
                $fullPath = storage_path('app/private/' . $path);
                $detectionResult = $this->visionService->detectClothing($fullPath);

                if ($detectionResult['success']) {
                    // Process each detected item
                    foreach ($detectionResult['items'] as $item) {
                        $allDetectedItems[] = $item['description'];
                        $allConfidenceScores[$item['description']] = $item['confidence'];

                        // Search for products on Amazon
                        $productLinks = $this->amazonService->searchProducts(
                            $item['description'],
                            $item['category']
                        );

                        // Add to similar products
                        foreach ($productLinks as $product) {
                            $allSimilarProducts[] = [
                                'name' => $product['title'],
                                'price' => $product['price'],
                                'image' => $product['image_url'],
                                'category' => $item['category'],
                                'brand' => 'Amazon',
                                'url' => $product['url'],
                                'similarity_score' => $item['confidence'] / 100
                            ];
                        }
                    }
                }
            }

            // Create ONE photo analysis record with all images
            $photoAnalysis = Card::create([
                'user_id' => auth()->check() ? auth()->id() : null,
                'image_path' => $firstImage['path'], // First image for backward compatibility
                'image_url' => $firstImage['url'], // First image for backward compatibility
                'file_size' => $totalFileSize,
                'dimensions' => $firstImage['dimensions'],
                'status' => 'processing',
                'analysis_metadata' => [
                    'upload_time' => now()->toDateTimeString(),
                    'user_links' => array_values($links),
                    'user_tags' => $tags,
                    'description' => $description,
                ]
            ]);

            // Create StyleImage records for each image (ensure no duplicates)
            $existingPaths = [];
            foreach ($allImages as $imageIndex => $imageData) {
                // Skip if we've already processed this path (prevent duplicates in array)
                if (in_array($imageData['path'], $existingPaths)) {
                    continue;
                }

                // Check if StyleImage already exists for this path and card_id (prevent DB duplicates)
                $existingImage = StyleImage::where('card_id', $photoAnalysis->id)
                    ->where('path', $imageData['path'])
                    ->first();

                if ($existingImage) {
                    continue; // Skip if already exists
                }

                StyleImage::create([
                    'photo_analysis_id' => $photoAnalysis->id,
                    'path' => $imageData['path'],
                    'url' => $imageData['url'],
                    'filename' => $imageData['filename'] ?? basename($imageData['path'] ?? ''),
                    'original_filename' => $imageData['original_filename'] ?? null,
                    'file_size' => $imageData['file_size'] ?? null,
                    'dimensions' => $imageData['dimensions'] ?? null,
                    'position' => $imageIndex,
                ]);

                $existingPaths[] = $imageData['path'];
            }

            // Process detected items and create database records
            $uniqueDetectedItems = array_unique($allDetectedItems);
            foreach ($uniqueDetectedItems as $itemDescription) {
                // Create a detected item for the first occurrence
                $detectedItem = DetectedItem::create([
                    'photo_analysis_id' => $photoAnalysis->id,
                    'category' => 'general', // We'll use general since we're aggregating
                    'description' => $itemDescription,
                    'confidence' => $allConfidenceScores[$itemDescription] ?? 50,
                ]);

                // Store first detected item for user-provided links
                if (!$firstDetectedItem) {
                    $firstDetectedItem = $detectedItem;
                }

                // Search for products on Amazon
                $productLinks = $this->amazonService->searchProducts(
                    $itemDescription,
                    'general'
                );

                // Save product links to database
                foreach ($productLinks as $product) {
                    ProductLink::create([
                        'user_id' => auth()->check() ? auth()->id() : null,
                        'detected_item_id' => $detectedItem->id,
                        'platform' => $product['platform'],
                        'title' => $product['title'],
                        'url' => $product['url'],
                        'price' => $product['price'],
                        'image_url' => $product['image_url'],
                        'asin' => $product['asin'],
                        'search_query' => $product['search_query'],
                    ]);
                }
            }

            // Add user-provided links to the first detected item (or create a general item if none detected)
            if (!empty($links)) {
                $targetItem = $firstDetectedItem;

                // If no items detected, create a general item for user links
                if (!$targetItem) {
                    $targetItem = DetectedItem::create([
                        'photo_analysis_id' => $photoAnalysis->id,
                        'category' => 'general',
                        'description' => 'User provided links',
                        'confidence' => 100,
                    ]);
                }

                // Add user-provided links
                foreach ($links as $linkData) {
                    if (!empty($linkData['title']) && !empty($linkData['url'])) {
                        ProductLink::create([
                            'user_id' => auth()->check() ? auth()->id() : null,
                            'detected_item_id' => $targetItem->id,
                            'platform' => $linkData['platform'] ?? 'Other',
                            'title' => $linkData['title'],
                            'url' => $linkData['url'],
                            'price' => $linkData['price'] ?? null,
                            'image_url' => null,
                            'asin' => null,
                            'search_query' => $linkData['title'],
                        ]);
                    }
                }
            }

            // Mark as completed
            $photoAnalysis->update([
                'status' => 'completed',
            ]);

            DB::commit();

            // Return JSON response with redirect URL
            return response()->json([
                'success' => true,
                'analysis_id' => $photoAnalysis->id,
                'redirect_url' => auth()->check() ? route('styles.index') : route('analysis-results') . '?id=' . $photoAnalysis->id,
                'image_urls' => array_column($allImages, 'url'),
                'detected_items' => array_unique($allDetectedItems),
                'confidence_scores' => $allConfidenceScores,
                'similar_products' => $allSimilarProducts,
                'analysis_metadata' => [
                    'upload_time' => now(),
                    'total_images' => $photoCount,
                    'total_items_detected' => count(array_unique($allDetectedItems))
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            // Update status to failed if record was created
            if (isset($photoAnalysis)) {
                $photoAnalysis->update(['status' => 'failed']);
            }

            Log::error('Photo analysis failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create style: ' . $e->getMessage()
            ], 500);
        }
    }

    public function serveUpload($filename)
    {
        $path = storage_path('app/private/uploads/' . $filename);

        if (!file_exists($path)) {
            abort(404, 'Image not found');
        }

        return response()->file($path);
    }

    /**
     * Get analysis by ID
     */
    public function getAnalysis($id)
    {
        try {
            $analysis = Card::with(['detectedItems.productLinks.favourites', 'images'])
                ->findOrFail($id);

            // Add visits, favourites, and likes data
            $userId = auth()->id();
            $hasActiveSubscription = $userId ? auth()->user()->hasActiveSubscription() : false;

            // Load likes count
            $analysis->loadCount('likes');
            if ($userId) {
                $analysis->is_liked = $analysis->isLikedBy($userId);
            } else {
                $analysis->is_liked = false;
            }

            // Add visits and favourites info to product links
            foreach ($analysis->detectedItems as $item) {
                foreach ($item->productLinks as $link) {
                    $link->loadCount('favourites');
                    if ($userId) {
                        $link->is_favourited = $link->isFavouritedBy($userId);
                    } else {
                        $link->is_favourited = false;
                    }
                    // Only show visits to registered/subscribed users
                    if (!$userId || !$hasActiveSubscription) {
                        $link->visits = null; // Hide visits from non-subscribed users
                    }
                }
            }

            return response()->json([
                'success' => true,
                'analysis' => $analysis,
                'user_has_subscription' => $hasActiveSubscription
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Analysis not found'
            ], 404);
        }
    }

    /**
     * Get analysis history
     */
    public function getAnalysisHistory(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 20);

            $query = Card::with(['detectedItems', 'images']);

            // If user is authenticated, show only their analyses
            if (auth()->check()) {
                $query->where('user_id', auth()->id());
            } else {
                // For non-authenticated users, show only public analyses (no user_id)
                $query->whereNull('user_id');
            }

            $analyses = $query->orderBy('created_at', 'desc')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'analyses' => $analyses
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve history'
            ], 500);
        }
    }

    /**
     * Delete an analysis
     */
    public function deleteAnalysis($id)
    {
        try {
            $analysis = Card::with('images')->findOrFail($id);

            // Check if user owns this analysis
            if (auth()->check() && $analysis->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            if ($analysis->images->isNotEmpty()) {
                foreach ($analysis->images as $image) {
                    if ($image->path) {
                        Storage::disk('local')->delete($image->path);
                    }
                }
            } elseif ($analysis->image_path) {
                Storage::disk('local')->delete($analysis->image_path);
            }

            // Delete the analysis (cascade will delete related items)
            $analysis->delete();

            return response()->json([
                'success' => true,
                'message' => 'Analysis deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete analysis'
            ], 500);
        }
    }

    // ========================================
    // LEGACY METHODS (kept for compatibility)
    // ========================================

    /**
     * Legacy method - kept for backward compatibility
     * This is your old implementation that calls Python microservice
     */
    private function performClothingAnalysis($photo)
    {
        try {
            $fashionApiUrl = env('FASHION_API_URL', 'http://fashion-detector:8000');

            // Get the photo path
            $photoPath = storage_path('app/private/' . $photo);

            if (!file_exists($photoPath)) {
                throw new \Exception('Photo file not found: ' . $photoPath);
            }

            // Make request to Python microservice
            $response = Http::timeout(30)
                ->attach('file', file_get_contents($photoPath), basename($photoPath))
                ->post($fashionApiUrl . '/detect');

            if (!$response->successful()) {
                throw new \Exception('Fashion API request failed: ' . $response->body());
            }

            $data = $response->json();

            // Transform the response to match your expected format
            $detectedItems = [];
            $confidenceScores = [];

            foreach ($data['items'] as $item) {
                $itemDescription = $item['description'];
                $detectedItems[] = $itemDescription;
                $confidenceScores[$itemDescription] = $item['confidence'];
            }

            return [
                'detected_items' => $detectedItems,
                'confidence_scores' => $confidenceScores,
                'raw_response' => $data
            ];

        } catch (\Exception $e) {
            Log::error('Clothing analysis failed', [
                'error' => $e->getMessage(),
                'photo' => $photo
            ]);

            // Return fallback mock data
            return [
                'detected_items' => ['Analysis temporarily unavailable'],
                'confidence_scores' => ['Analysis temporarily unavailable' => 0.0],
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Legacy method - kept for backward compatibility
     */
    private function findSimilarProducts($detectedItems)
    {
        $mockProducts = [
            [
                'name'             => 'Classic Blue Denim Jacket',
                'price'            => 89.99,
                'image'            => 'https://images.unsplash.com/photo-1544022613-e87ca75a784a?w=200&h=200&fit=crop',
                'category'         => 'Jackets',
                'brand'            => 'Denim Co.',
                'similarity_score' => 0.95
            ],
            [
                'name'             => 'Essential White T-Shirt',
                'price'            => 24.99,
                'image'            => 'https://images.unsplash.com/photo-1521572163474-37898b6baf30?w=200&h=200&fit=crop',
                'category'         => 'T-Shirts',
                'brand'            => 'Basic Wear',
                'similarity_score' => 0.92
            ],
            [
                'name'             => 'Slim Black Jeans',
                'price'            => 69.99,
                'image'            => 'https://images.unsplash.com/photo-1475178626620-a4d074967452?w=200&h=200&fit=crop',
                'category'         => 'Jeans',
                'brand'            => 'Urban Denim',
                'similarity_score' => 0.88
            ],
            [
                'name'             => 'White Canvas Sneakers',
                'price'            => 79.99,
                'image'            => 'https://images.unsplash.com/photo-1549298916-b41d501d3772?w=200&h=200&fit=crop',
                'category'         => 'Shoes',
                'brand'            => 'Canvas Co.',
                'similarity_score' => 0.85
            ],
            [
                'name'             => 'Silver Sports Watch',
                'price'            => 199.99,
                'image'            => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=200&h=200&fit=crop',
                'category'         => 'Watches',
                'brand'            => 'TimeStyle',
                'similarity_score' => 0.78
            ]
        ];

        return $mockProducts;
    }
}
