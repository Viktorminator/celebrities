<?php

namespace App\Http\Controllers;

use App\Models\Favourite;
use App\Models\ProductLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavouriteController extends Controller
{
    /**
     * Toggle favourite status for a product link
     */
    public function toggle($productLinkId)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'You must be logged in to favourite links'
            ], 401);
        }

        $productLink = ProductLink::findOrFail($productLinkId);
        $userId = Auth::id();

        $favourite = Favourite::where('user_id', $userId)
            ->where('product_link_id', $productLinkId)
            ->first();

        if ($favourite) {
            // Unfavourite
            $favourite->delete();
            $isFavourited = false;
        } else {
            // Favourite
            Favourite::create([
                'user_id' => $userId,
                'product_link_id' => $productLinkId,
            ]);
            $isFavourited = true;
        }

        $favouritesCount = $productLink->favourites()->count();

        return response()->json([
            'success' => true,
            'is_favourited' => $isFavourited,
            'favourites_count' => $favouritesCount
        ]);
    }

    /**
     * Check if a product link is favourited by the current user
     */
    public function check($productLinkId)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => true,
                'is_favourited' => false,
                'favourites_count' => ProductLink::findOrFail($productLinkId)->favourites()->count()
            ]);
        }

        $productLink = ProductLink::findOrFail($productLinkId);
        $isFavourited = $productLink->isFavouritedBy(Auth::id());
        $favouritesCount = $productLink->favourites()->count();

        return response()->json([
            'success' => true,
            'is_favourited' => $isFavourited,
            'favourites_count' => $favouritesCount
        ]);
    }
}
