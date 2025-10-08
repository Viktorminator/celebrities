<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        return response()->json(Product::all());
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);
        return response()->json($product);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|integer',
            'image_url' => 'required|string',
            'celebrity_id' => 'required|exists:celebrities,id',
            'category_id' => 'required|exists:product_categories,id',
            'occasion' => 'nullable|string',
            'event_date' => 'nullable|string',
        ]);

        $product = Product::create($data);
        return response()->json($product, 201);
    }
}
