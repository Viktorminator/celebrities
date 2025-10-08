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
        return view('home', compact('celebrities'));
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
