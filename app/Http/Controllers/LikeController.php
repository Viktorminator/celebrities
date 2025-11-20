<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Card;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    /**
     * Toggle like status for a style (photo analysis)
     */
    public function toggle($photoAnalysisId)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'You must be logged in to like styles'
            ], 401);
        }

        $photoAnalysis = Card::findOrFail($photoAnalysisId);
        $userId = Auth::id();

        // Don't allow users to like their own styles
        if ($photoAnalysis->user_id === $userId) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot like your own style'
            ], 403);
        }

        $like = Like::where('user_id', $userId)
            ->where('photo_analysis_id', $photoAnalysisId)
            ->first();

        if ($like) {
            // Unlike
            $like->delete();
            $isLiked = false;
        } else {
            // Like
            Like::create([
                'user_id' => $userId,
                'photo_analysis_id' => $photoAnalysisId,
            ]);
            $isLiked = true;
        }

        $likesCount = $photoAnalysis->likes()->count();

        return response()->json([
            'success' => true,
            'is_liked' => $isLiked,
            'likes_count' => $likesCount
        ]);
    }

    /**
     * Check if a style is liked by the current user
     */
    public function check($photoAnalysisId)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => true,
                'is_liked' => false,
                'likes_count' => Card::findOrFail($photoAnalysisId)->likes()->count()
            ]);
        }

        $photoAnalysis = Card::findOrFail($photoAnalysisId);
        $isLiked = $photoAnalysis->isLikedBy(Auth::id());
        $likesCount = $photoAnalysis->likes()->count();

        return response()->json([
            'success' => true,
            'is_liked' => $isLiked,
            'likes_count' => $likesCount
        ]);
    }
}
