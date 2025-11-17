<?php

namespace App\Http\Controllers;

use App\Models\StyleFavourite;
use App\Models\PhotoAnalysis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StyleFavouriteController extends Controller
{
    /**
     * Display the favourites page
     */
    public function index()
    {
        $userId = Auth::id();
        
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Please log in to view your favourites');
        }

        $favourites = StyleFavourite::where('user_id', $userId)
            ->with(['photoAnalysis.user', 'photoAnalysis.productLinks'])
            ->with(['photoAnalysis' => function($query) {
                $query->withCount(['likes', 'styleFavourites']);
            }])
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('favourites.index', compact('favourites'));
    }

    /**
     * Toggle favourite status for a style
     */
    public function toggle($photoAnalysisId)
    {
        $photoAnalysis = PhotoAnalysis::findOrFail($photoAnalysisId);
        $userId = Auth::id();
        $sessionId = session()->getId();

        // Check if already favourited
        $favourite = null;
        if ($userId) {
            $favourite = StyleFavourite::where('user_id', $userId)
                ->where('photo_analysis_id', $photoAnalysisId)
                ->first();
        } else {
            $favourite = StyleFavourite::where('session_id', $sessionId)
                ->where('photo_analysis_id', $photoAnalysisId)
                ->whereNull('user_id')
                ->first();
        }

        if ($favourite) {
            // Unfavourite
            $favourite->delete();
            $isFavourited = false;
        } else {
            // Favourite
            StyleFavourite::create([
                'user_id' => $userId,
                'photo_analysis_id' => $photoAnalysisId,
                'session_id' => $userId ? null : $sessionId,
            ]);
            $isFavourited = true;
        }

        $favouritesCount = $photoAnalysis->styleFavourites()->count();

        return response()->json([
            'success' => true,
            'is_favourited' => $isFavourited,
            'favourites_count' => $favouritesCount
        ]);
    }

    /**
     * Check if a style is favourited
     */
    public function check($photoAnalysisId)
    {
        $photoAnalysis = PhotoAnalysis::findOrFail($photoAnalysisId);
        $userId = Auth::id();
        $sessionId = session()->getId();

        $isFavourited = $photoAnalysis->isFavouritedBy($userId, $sessionId);
        $favouritesCount = $photoAnalysis->styleFavourites()->count();

        return response()->json([
            'success' => true,
            'is_favourited' => $isFavourited,
            'favourites_count' => $favouritesCount
        ]);
    }
}
