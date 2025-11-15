<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\PhotoAnalysis;
use App\Models\DetectedItem;
use App\Models\ProductLink;
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
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240'
        ], [
            'photo.required' => 'Please select an image to analyze.',
            'photo.image' => 'The file must be an image.',
            'photo.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif.',
            'photo.max' => 'The image may not be greater than 10MB.'
        ]);

        DB::beginTransaction();

        try {
            // Store the uploaded image
            $photo = $request->file('photo');
            $filename = Str::uuid() . '.' . $photo->getClientOriginalExtension();
            $path = $photo->storeAs('uploads', $filename, 'local');

            // Generate URL for serving the image
            $imageUrl = route('serve-upload', ['filename' => $filename]);

            // Get image dimensions
            $dimensions = getimagesize($photo->getPathname());
            $dimensionsStr = $dimensions ? "{$dimensions[0]}x{$dimensions[1]}" : null;

            // Create photo analysis record
            $photoAnalysis = PhotoAnalysis::create([
                'user_id' => auth()->check() ? auth()->id() : null,
                'image_path' => $path,
                'image_url' => $imageUrl,
                'file_size' => $photo->getSize(),
                'dimensions' => $dimensionsStr,
                'status' => 'processing',
                'analysis_metadata' => [
                    'upload_time' => now()->toDateTimeString(),
                    'original_filename' => $photo->getClientOriginalName(),
                ]
            ]);

            // Perform clothing detection with Google Vision
            $fullPath = storage_path('app/private/' . $path);
            $detectionResult = $this->visionService->detectClothing($fullPath);

            if (!$detectionResult['success']) {
                throw new \Exception($detectionResult['error'] ?? 'Vision API failed');
            }

            $detectedItems = [];
            $confidenceScores = [];
            $similarProducts = [];

            // Process each detected item
            foreach ($detectionResult['items'] as $item) {
                // Save detected item to database
                $detectedItem = DetectedItem::create([
                    'photo_analysis_id' => $photoAnalysis->id,
                    'category' => $item['category'],
                    'description' => $item['description'],
                    'color' => $item['color'],
                    'confidence' => $item['confidence'],
                    'bounding_box' => $item['bounding_box'],
                    'raw_data' => $item['raw_data']
                ]);

                // Search for products on Amazon
                $productLinks = $this->amazonService->searchProducts(
                    $item['description'],
                    $item['category']
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

                // Build response arrays
                $detectedItems[] = $item['description'];
                $confidenceScores[$item['description']] = $item['confidence'];

                // Add to similar products
                foreach ($productLinks as $product) {
                    $similarProducts[] = [
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

            // Save celebrity detection results
            $photoAnalysis->update([
                'status' => 'completed',
                'detected_celebrities' => $detectionResult['celebrities'] ?? [],
                'face_count' => $detectionResult['face_count'] ?? 0,
                'has_person' => $detectionResult['has_person'] ?? false,
                'context_labels' => $detectionResult['context_labels'] ?? []
            ]);

            DB::commit();

            // Return JSON response with redirect URL
            return response()->json([
                'success' => true,
                'analysis_id' => $photoAnalysis->id,
                'redirect_url' => route('analysis-results') . '?id=' . $photoAnalysis->id,
                'image_url' => $imageUrl,
                'detected_items' => $detectedItems,
                'confidence_scores' => $confidenceScores,
                'similar_products' => $similarProducts,
                'analysis_metadata' => [
                    'upload_time' => now(),
                    'file_size' => $photo->getSize(),
                    'dimensions' => $dimensions,
                    'total_items_detected' => count($detectedItems)
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
                'message' => 'Failed to analyze photo: ' . $e->getMessage()
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
            $analysis = PhotoAnalysis::with(['detectedItems.productLinks'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'analysis' => $analysis
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

            $query = PhotoAnalysis::with(['detectedItems']);
            
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
            $analysis = PhotoAnalysis::findOrFail($id);
            
            // Check if user owns this analysis
            if (auth()->check() && $analysis->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            // Delete the image file
            $imagePath = storage_path('app/private/' . $analysis->image_path);
            if (file_exists($imagePath)) {
                unlink($imagePath);
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
