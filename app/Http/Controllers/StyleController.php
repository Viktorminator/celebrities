<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\StyleImage;
use App\Models\StyleTag;
use App\Models\ProductLink;
use App\Http\Requests\StoreStyleRequest;
use App\Http\Requests\UpdateStyleRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StyleController extends Controller
{
    /**
     * Show the form for creating a new style
     */
    public function create()
    {
        $user = Auth::user();
        $styleLimit = $user?->styleLimit();
        $limitReached = $user?->hasReachedStyleLimit();

        return view('styles.create', compact('styleLimit', 'limitReached'));
    }

    /**
     * Store a newly created style (simple CRUD - no analysis)
     */
    public function store(StoreStyleRequest $request)
    {
        $user = Auth::user();

        $currentStyleCount = Card::where('user_id', $user->id)->count();
        $styleLimit = $user->styleLimit();

        if ($currentStyleCount + 1 > $styleLimit) {
            return response()->json([
                'success'     => false,
                'message'     => "You can only add {$styleLimit} styles total. You currently have {$currentStyleCount} styles. Please upgrade your subscription to add more.",
                'upgrade_url' => route('subscriptions'),
            ], 403);
        }

        try {
            $photos = $request->file('photos');
            $linksData = $request->input('links', []);
            $links = array_filter($linksData, function ($link) {
                return !empty($link['title']) && !empty($link['url']);
            });
            $tags = $request->input('tags') ? explode(',', $request->input('tags')) : [];
            $tags = array_map('trim', $tags);
            $tags = array_filter($tags);
            $description = $request->input('description');

            $allImages = [];

            foreach ($photos as $photo) {
                $filename = Str::uuid() . '.' . $photo->getClientOriginalExtension();
                $path = $photo->storeAs('uploads', $filename, 'local');
                $imageUrl = route('serve-upload', ['filename' => $filename]);

                $dimensions = getimagesize($photo->getPathname());
                $dimensionsStr = $dimensions ? "{$dimensions[0]}x{$dimensions[1]}" : null;

                $imageData = [
                    'path'              => $path,
                    'url'               => $imageUrl,
                    'filename'          => $filename,
                    'original_filename' => $photo->getClientOriginalName(),
                    'file_size'         => $photo->getSize(),
                    'dimensions'        => $dimensionsStr,
                ];

                $allImages[] = $imageData;
            }

            $card = Card::create([
                'user_id'     => $user->id,
                'description' => $description,
                'status'      => 'completed',
            ]);

            foreach ($allImages as $imageIndex => $imageData) {
                StyleImage::create([
                    'card_id'           => $card->id,
                    'path'              => $imageData['path'],
                    'url'               => $imageData['url'],
                    'filename'          => $imageData['filename'],
                    'original_filename' => $imageData['original_filename'],
                    'file_size'         => $imageData['file_size'],
                    'dimensions'        => $imageData['dimensions'],
                    'position'          => $imageIndex,
                ]);
            }

            // Create StyleTag records (tags are categories)
            foreach ($tags as $tag) {
                StyleTag::create([
                    'card_id' => $card->id,
                    'tag'     => $tag,
                ]);
            }

            // Create product links directly
            if (!empty($links)) {
                foreach ($links as $linkData) {
                    ProductLink::create([
                        'user_id'      => $user->id,
                        'platform'     => $linkData['platform'] ?? 'Other',
                        'title'        => $linkData['title'],
                        'url'          => $linkData['url'],
                        'price'        => $linkData['price'] ?? null,
                        'image_url'    => null,
                        'asin'         => null,
                        'search_query' => $linkData['title'],
                    ]);
                }
            }

            return response()->json([
                'success'      => true,
                'message'      => 'Style created successfully',
                'redirect_url' => route('styles.index'),
            ]);

        } catch (\Exception $e) {
            \Log::error('Style creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create style: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display all user's added styles
     */
    public function index()
    {
        $styles = Card::where('user_id', Auth::id())
            ->with(['detectedItems.productLinks', 'productLinks', 'images', 'styleTags'])
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        $user = Auth::user();
        $styleLimit = $user?->styleLimit();
        $limitReached = $user?->hasReachedStyleLimit();

        return view('styles.index', compact('styles', 'styleLimit', 'limitReached'));
    }

    /**
     * Show the form for editing a style
     */
    public function edit($id)
    {
        $style = Card::with(['detectedItems.productLinks.favourites', 'user', 'likes', 'images', 'styleTags'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        // Load counts
        $style->loadCount(['likes', 'productLinks']);

        // Collect all product links (from direct productLinks and from detectedItems)
        $allProductLinks = collect($style->productLinks);
        foreach ($style->detectedItems as $item) {
            $allProductLinks = $allProductLinks->merge($item->productLinks);
        }
        // Remove duplicates and set as productLinks
        $style->productLinks = $allProductLinks->unique('id')->values();

        foreach ($style->productLinks as $link) {
            $link->loadCount('favourites');
            $link->is_favourited = $link->isFavouritedBy(Auth::id());
        }

        $hasActiveSubscription = Auth::user()->hasActiveSubscription();

        return view('styles.edit', compact('style', 'hasActiveSubscription'));
    }

    /**
     * Show a single style detail
     */
    public function show($id)
    {
        $style = Card::with(['detectedItems.productLinks.favourites', 'user', 'likes', 'images', 'styleTags'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        // Load counts
        $style->loadCount(['likes', 'productLinks']);

        // Collect all product links (from direct productLinks and from detectedItems)
        $allProductLinks = collect($style->productLinks);
        foreach ($style->detectedItems as $item) {
            $allProductLinks = $allProductLinks->merge($item->productLinks);
        }
        // Remove duplicates and set as productLinks
        $style->productLinks = $allProductLinks->unique('id')->values();

        foreach ($style->productLinks as $link) {
            $link->loadCount('favourites');
            $link->is_favourited = $link->isFavouritedBy(Auth::id());
        }

        $hasActiveSubscription = Auth::user()->hasActiveSubscription();

        return view('style.view', compact('style', 'hasActiveSubscription'));
    }

    /**
     * View a style (public, no authentication required)
     */
    public function view($id)
    {
        $style = Card::where('status', 'completed')
            ->with(['user', 'productLinks.favourites', 'detectedItems.productLinks', 'images', 'styleTags'])
            ->withCount(['likes', 'styleFavourites', 'productLinks'])
            ->findOrFail($id);

        $userId = auth()->id();
        $sessionId = session()->getId();

        // Check if favourited
        $style->is_favourited = $style->isFavouritedBy($userId, $sessionId);

        // Check if liked (only for authenticated users, and not their own styles)
        if ($userId && $style->user_id !== $userId) {
            $style->is_liked = $style->isLikedBy($userId);
        } else {
            $style->is_liked = false;
        }

        // Collect all product links (from direct productLinks and from detectedItems)
        $allProductLinks = collect($style->productLinks);
        foreach ($style->detectedItems as $item) {
            $allProductLinks = $allProductLinks->merge($item->productLinks);
        }
        // Remove duplicates and set as productLinks
        $style->productLinks = $allProductLinks->unique('id')->values();

        // Load favourites count for product links
        foreach ($style->productLinks as $link) {
            $link->loadCount('favourites');
            if ($userId) {
                $link->is_favourited = $link->isFavouritedBy($userId);
            } else {
                $link->is_favourited = false;
            }
        }

        // Check if user has active subscription (for viewing visit counts)
        $hasActiveSubscription = $userId ? auth()->user()->hasActiveSubscription() : false;

        return view('style.view', compact('style', 'hasActiveSubscription'));
    }

    /**
     * Update a style
     */
    public function update(UpdateStyleRequest $request, $id)
    {
        try {
            $style = Card::where('user_id', Auth::id())->findOrFail($id);
            $style->load('images', 'detectedItems');

            // Process keep_images
            $keepImageIds = collect($request->input('keep_images', []))
                ->filter()
                ->map(fn($id) => (int)$id)
                ->unique()
                ->values();

            $newImages = $request->file('new_images', []);

            // Get valid keep image IDs (only those that belong to this style)
            $validKeepImageIds = $keepImageIds->filter(function ($id) use ($style) {
                return $style->images->contains('id', $id);
            })->values();

            // Delete images that were removed
            $imagesToDelete = $style->images->filter(function ($image) use ($validKeepImageIds) {
                return !$validKeepImageIds->contains($image->id);
            });

            foreach ($imagesToDelete as $image) {
                if ($image->path && Storage::disk('local')->exists($image->path)) {
                    Storage::disk('local')->delete($image->path);
                }
                $image->delete();
            }

            // Reorder kept images and prepare final image list
            $orderedImages = [];
            $position = 0;

            if ($validKeepImageIds->isNotEmpty()) {
                $keptImages = StyleImage::whereIn('id', $validKeepImageIds)
                    ->where('card_id', $style->id)
                    ->get()
                    ->keyBy('id');

                foreach ($validKeepImageIds as $imageId) {
                    if ($keptImages->has($imageId)) {
                        $image = $keptImages->get($imageId);
                        $image->update(['position' => $position]);
                        $orderedImages[] = $image->fresh();
                        $position++;
                    }
                }
            }

            // Process new images
            if (!empty($newImages) && is_array($newImages)) {
                foreach ($newImages as $photo) {
                    // Skip if not a valid uploaded file
                    if (!$photo || !$photo->isValid()) {
                        continue;
                    }

                    $filename = Str::uuid() . '.' . $photo->getClientOriginalExtension();
                    $path = $photo->storeAs('uploads', $filename, 'local');
                    $imageUrl = route('serve-upload', ['filename' => $filename]);

                    // Get image dimensions
                    $dimensions = null;
                    try {
                        $dimensionsArray = getimagesize($photo->getPathname());
                        $dimensions = $dimensionsArray ? "{$dimensionsArray[0]}x{$dimensionsArray[1]}" : null;
                    } catch (\Exception $e) {
                        \Log::warning('Failed to get image dimensions: ' . $e->getMessage());
                    }

                    $styleImage = StyleImage::create([
                        'card_id' => $style->id,
                        'path'              => $path,
                        'url'               => $imageUrl,
                        'filename'          => $filename,
                        'original_filename' => $photo->getClientOriginalName(),
                        'file_size'         => $photo->getSize(),
                        'dimensions'        => $dimensions,
                        'position'          => $position,
                    ]);

                    $orderedImages[] = $styleImage;
                    $position++;
                }
            }

            // Final validation check (should never happen due to form validation)
            if (empty($orderedImages)) {
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'At least one image is required.',
                        'errors'  => ['images' => ['At least one image is required.']]
                    ], 422);
                }

                return redirect()->back()
                    ->withErrors(['images' => 'At least one image is required.'])
                    ->withInput();
            }

            // Update metadata (description)
            $metadata = $style->analysis_metadata ?? [];

            if ($request->has('description')) {
                $metadata['description'] = $request->input('description');
            }

            // Update StyleTag records
            if ($request->has('tags')) {
                // Delete existing tags
                $style->styleTags()->delete();

                // Create new tags
                $tags = array_filter(array_map('trim', $request->input('tags', [])));
                foreach ($tags as $tag) {
                    if (!empty($tag)) {
                        StyleTag::create([
                            'card_id' => $style->id,
                            'tag'               => $tag,
                        ]);
                    }
                }
            }

            // Update main image fields (for backward compatibility)
            $firstImage = $orderedImages[0];
            $totalFileSize = collect($orderedImages)->sum(fn($image) => $image->file_size ?? 0);

            $style->update([
                'analysis_metadata' => $metadata,
                'image_path'        => $firstImage->path,
                'image_url'         => $firstImage->url,
                'file_size'         => $totalFileSize,
                'dimensions'        => $firstImage->dimensions,
            ]);

            // Handle product links
            if ($request->has('links')) {
                $this->updateProductLinks($request, $style);
            }

            // Return appropriate response based on request type
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success'  => true,
                    'message'  => 'Style updated successfully',
                    'redirect' => route('style.view', $id)
                ]);
            }

            return redirect()->route('style.view', $id)
                ->with('success', 'Style updated successfully');

        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors'  => $e->errors()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Error updating style: ' . $e->getMessage(), [
                'style_id' => $id,
                'user_id'  => Auth::id(),
                'trace'    => $e->getTraceAsString()
            ]);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while updating the style. Please try again.',
                    'error'   => config('app.debug') ? $e->getMessage() : null
                ], 500);
            }

            return redirect()->back()
                ->withErrors(['error' => 'An error occurred while updating the style. Please try again.'])
                ->withInput();
        }
    }

    /**
     * Update product links for the style
     */
    private function updateProductLinks($request, $style)
    {
        // Get all user-created links for this style's detected items
        $detectedItemIds = $style->detectedItems->pluck('id')->toArray();

        $userCreatedLinks = \App\Models\ProductLink::where('user_id', Auth::id())
            ->where(function ($query) use ($detectedItemIds) {
                $query->whereNull('detected_item_id')
                    ->orWhereIn('detected_item_id', $detectedItemIds);
            })
            ->get();

        $linkIds = [];

        foreach ($request->input('links', []) as $linkData) {
            // Skip empty links
            if (empty($linkData['title']) || empty($linkData['url'])) {
                continue;
            }

            if (!empty($linkData['id'])) {
                // Update existing link
                $link = \App\Models\ProductLink::where('id', $linkData['id'])
                    ->where('user_id', Auth::id())
                    ->first();

                if ($link) {
                    $link->update([
                        'title'    => $linkData['title'],
                        'url'      => $linkData['url'],
                        'platform' => $linkData['platform'] ?? 'Other',
                        'price'    => $linkData['price'] ?? null,
                    ]);
                    $linkIds[] = $link->id;
                }
            } else {
                // Create new link
                $detectedItemId = $style->detectedItems->first()?->id;

                $link = \App\Models\ProductLink::create([
                    'user_id'          => Auth::id(),
                    'detected_item_id' => $detectedItemId,
                    'platform'         => $linkData['platform'] ?? 'Other',
                    'title'            => $linkData['title'],
                    'url'              => $linkData['url'],
                    'price'            => $linkData['price'] ?? null,
                    'search_query'     => $linkData['title'],
                ]);
                $linkIds[] = $link->id;
            }
        }

        // Delete user-created links that were removed
        $userCreatedLinks->whereNotIn('id', $linkIds)->each(function ($link) {
            $link->delete();
        });
    }

    /**
     * Delete a style
     */
    public function destroy($id)
    {
        $style = Card::where('user_id', Auth::id())
            ->with('images')
            ->findOrFail($id);

        if ($style->images->isNotEmpty()) {
            foreach ($style->images as $image) {
                if ($image->path) {
                    Storage::disk('local')->delete($image->path);
                }
            }
        } elseif ($style->image_path) {
            Storage::disk('local')->delete($style->image_path);
        }

        $style->delete();

        return redirect()->route('styles.index')
            ->with('success', 'Style deleted successfully');
    }
}
