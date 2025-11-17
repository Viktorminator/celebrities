<?php

namespace App\Http\Controllers;

use App\Models\Celebrity;
use Illuminate\Http\Request;

class CelebrityController extends Controller
{
    // Homepage - render home view with celebrities data
    public function home()
    {
        $celebrities = Celebrity::all();
        
        // Get recent styles (photo analyses) for the style cards
        $styles = \App\Models\PhotoAnalysis::where('status', 'completed')
            ->whereNotNull('user_id') // Only show user-uploaded styles
            ->with(['user', 'productLinks'])
            ->withCount(['likes', 'styleFavourites'])
            ->orderBy('created_at', 'desc')
            ->limit(12)
            ->get();

        // Check which styles are favourited by current user/session
        $userId = auth()->id();
        $sessionId = session()->getId();
        foreach ($styles as $style) {
            $style->is_favourited = $style->isFavouritedBy($userId, $sessionId);
        }

        return view('home', compact('celebrities', 'styles'));
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
