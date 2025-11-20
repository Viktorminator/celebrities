<?php

namespace App\Http\Controllers;

use App\Models\Celebrity;
use Illuminate\Http\Request;

class CelebrityController extends Controller
{
    // Homepage - render home view with celebrities data
    public function home(Request $request)
    {
        // Get the category filter from request
        $category = $request->get('category', 'All');

        // Get recent styles (photo analyses) for the style cards
        $stylesQuery = \App\Models\Card::where('status', 'completed')
            ->whereNotNull('user_id'); // Only show user-uploaded styles

        // Filter by category if not "All"
        // Note: We'll filter in memory for better compatibility across databases
        $allStyles = $stylesQuery->with(['user', 'productLinks', 'images', 'styleTags'])
            ->withCount(['likes', 'styleFavourites'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Filter by category if not "All"
        if ($category !== 'All') {
            $allStyles = $allStyles->filter(function ($style) use ($category) {
                $tags = $style->styleTags->pluck('tag')->toArray();
                return in_array($category, $tags);
            });
        }

        $styles = $allStyles->take(12)->values(); // values() to reindex the collection

        // Check which styles are favourited and liked by current user/session
        $userId = auth()->id();
        $sessionId = session()->getId();
        foreach ($styles as $style) {
            $style->is_favourited = $style->isFavouritedBy($userId, $sessionId);
            // Check if liked (only for authenticated users, and not their own styles)
            if ($userId && $style->user_id !== $userId) {
                $style->is_liked = $style->isLikedBy($userId);
            } else {
                $style->is_liked = false;
            }
        }

        return view('home', compact( 'styles', 'category'));
    }

    // List all celebrities (API)
    public function index()
    {
        return response()->json(Celebrity::all());
    }

    // Show a single celebrity
    public function show($id)
    {
        $celebrity = Celebrity::findOrFail($id);
        return response()->json($celebrity);
    }

    // Store a new celebrity
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'profession' => 'required|string',
            'bio' => 'required|string',
            'image_url' => 'required|string',
            'banner_url' => 'required|string',
            'categories' => 'required|array',
            'likes' => 'integer',
        ]);

        $celebrity = Celebrity::create($data);
        return response()->json($celebrity, 201);
    }
}
