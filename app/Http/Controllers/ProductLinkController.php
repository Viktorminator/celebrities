<?php

namespace App\Http\Controllers;

use App\Models\ProductLink;
use App\Models\DetectedItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductLinkController extends Controller
{
    /**
     * Store a newly created product link
     */
    public function store(Request $request)
    {
        $request->validate([
            'detected_item_id' => 'required|exists:detected_items,id',
            'platform' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'url' => 'required|url',
            'price' => 'nullable|string|max:255',
            'image_url' => 'nullable|url',
            'asin' => 'nullable|string|max:255',
        ]);

        // Verify the detected item belongs to the user's analysis
        $detectedItem = DetectedItem::with('photoAnalysis')->findOrFail($request->detected_item_id);
        
        if (auth()->check() && $detectedItem->photoAnalysis->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $productLink = ProductLink::create([
            'user_id' => auth()->id(),
            'detected_item_id' => $request->detected_item_id,
            'platform' => $request->platform,
            'title' => $request->title,
            'url' => $request->url,
            'price' => $request->price,
            'image_url' => $request->image_url,
            'asin' => $request->asin,
            'search_query' => $request->title,
        ]);

        return response()->json([
            'success' => true,
            'product_link' => $productLink->load('detectedItem')
        ], 201);
    }

    /**
     * Update the specified product link
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'platform' => 'sometimes|string|max:255',
            'title' => 'sometimes|string|max:255',
            'url' => 'sometimes|url',
            'price' => 'nullable|string|max:255',
            'image_url' => 'nullable|url',
            'asin' => 'nullable|string|max:255',
        ]);

        $productLink = ProductLink::with('detectedItem.photoAnalysis')->findOrFail($id);

        // Check if user owns this product link
        if (auth()->check() && $productLink->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $productLink->update($request->only([
            'platform', 'title', 'url', 'price', 'image_url', 'asin'
        ]));

        return response()->json([
            'success' => true,
            'product_link' => $productLink->fresh()
        ]);
    }

    /**
     * Remove the specified product link
     */
    public function destroy($id)
    {
        $productLink = ProductLink::findOrFail($id);

        // Check if user owns this product link
        if (auth()->check() && $productLink->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $productLink->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product link deleted successfully'
        ]);
    }

    /**
     * Get product links for a detected item
     */
    public function getByDetectedItem($detectedItemId)
    {
        $detectedItem = DetectedItem::with('photoAnalysis')->findOrFail($detectedItemId);

        // Check if user owns this analysis
        if (auth()->check() && $detectedItem->photoAnalysis->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $productLinks = ProductLink::where('detected_item_id', $detectedItemId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'product_links' => $productLinks
        ]);
    }
}
