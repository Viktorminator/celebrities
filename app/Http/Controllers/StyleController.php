<?php

namespace App\Http\Controllers;

use App\Models\PhotoAnalysis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StyleController extends Controller
{
    /**
     * Display all user's added styles
     */
    public function index()
    {
        $styles = PhotoAnalysis::where('user_id', Auth::id())
            ->with(['detectedItems.productLinks', 'productLinks'])
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        $user = Auth::user();
        $styleLimit = $user?->styleLimit();
        $limitReached = $user?->hasReachedStyleLimit();

        return view('styles.index', compact('styles', 'styleLimit', 'limitReached'));
    }

    /**
     * Show a single style detail
     */
    public function show($id)
    {
        $style = PhotoAnalysis::with(['detectedItems.productLinks.favourites', 'user', 'likes'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        // Load counts
        $style->loadCount(['likes', 'productLinks']);
        foreach ($style->productLinks as $link) {
            $link->loadCount('favourites');
            $link->is_favourited = $link->isFavouritedBy(Auth::id());
        }

        $hasActiveSubscription = Auth::user()->hasActiveSubscription();

        return view('styles.show', compact('style', 'hasActiveSubscription'));
    }

    /**
     * Delete a style
     */
    public function destroy($id)
    {
        $style = PhotoAnalysis::where('user_id', Auth::id())->findOrFail($id);

        // Delete the image file
        $imagePath = storage_path('app/private/' . $style->image_path);
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }

        $style->delete();

        return redirect()->route('styles.index')
            ->with('success', 'Style deleted successfully');
    }
}
