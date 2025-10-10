<?php

namespace App\Http\Controllers;

use App\Models\Celebrity;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $nav = $request->query('nav', 'Popular');
        $category = $request->query('category', 'All');

        $query = Celebrity::query();

        // Filter by navigation menu
        if ($nav === 'Popular') {
            $query->orderBy('views', 'desc');
        } elseif ($nav === 'Trending') {
            $query->orderBy('trending_score', 'desc'); // Assuming a trending_score column
        } elseif ($nav === 'New') {
            $query->orderBy('created_at', 'desc');
        }

        // Filter by category
        if ($category !== 'All') {
            $query->whereHas('categories', function ($q) use ($category) {
                $q->where('name', $category);
            });
        }

        $celebrities = $query->get();

        return view('home', compact('celebrities'));
    }
}
