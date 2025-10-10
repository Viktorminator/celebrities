<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PhotoAnalysisController extends Controller
{
    public function analyzePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120'
        ], [
            'photo.required' => 'Please select an image to analyze.',
            'photo.image' => 'The file must be an image.',
            'photo.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif.',
            'photo.max' => 'The image may not be greater than 5MB.'
        ]);

        try {
            // Store the uploaded image in the local disk
            $photo = $request->file('photo');
            $filename = 'uploads/' . Str::uuid() . '.' . $photo->getClientOriginalExtension();
            $path = $photo->storeAs('uploads', basename($filename), 'local'); // Use 'local' disk

            // Generate a custom URL for serving the image
            $imageUrl = route('serve-upload', ['filename' => basename($filename)]);

            // Simulate clothing detection analysis
            $analysisResult = $this->performClothingAnalysis($photo);

            // Find similar products
            $similarProducts = $this->findSimilarProducts($analysisResult['detected_items']);

            return response()->json([
                'success' => true,
                'image_url' => $imageUrl,
                'detected_items' => $analysisResult['detected_items'],
                'confidence_scores' => $analysisResult['confidence_scores'],
                'similar_products' => $similarProducts,
                'analysis_metadata' => [
                    'upload_time' => now(),
                    'file_size' => $photo->getSize(),
                    'dimensions' => getimagesize($photo->getPathname()),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to analyze photo: ' . $e->getMessage()
            ], 500);
        }
    }

    public function serveUpload($filename)
    {
        $path = storage_path('app/private/uploads/' . $filename);
        if (!file_exists($path)) {
            abort(404, 'Image not found');
        }

        return response()->file($path);
    }

    private function performClothingAnalysis($photo)
    {
        $mockDetectedItems = [
            'blue denim jacket',
            'white t-shirt',
            'black jeans',
            'white sneakers',
            'silver watch'
        ];

        $mockConfidenceScores = [
            'blue denim jacket' => 0.92,
            'white t-shirt' => 0.88,
            'black jeans' => 0.85,
            'white sneakers' => 0.79,
            'silver watch' => 0.71
        ];

        sleep(2);

        return [
            'detected_items' => $mockDetectedItems,
            'confidence_scores' => $mockConfidenceScores
        ];
    }

    private function findSimilarProducts($detectedItems)
    {
        $mockProducts = [
            [
                'name'             => 'Classic Blue Denim Jacket',
                'price'            => 89.99,
                'image'            => 'https://images.unsplash.com/photo-1544022613-e87ca75a784a?w=200&h=200&fit=crop',
                'category'         => 'Jackets',
                'brand'            => 'Denim Co.',
                'similarity_score' => 0.95
            ],
            [
                'name'             => 'Essential White T-Shirt',
                'price'            => 24.99,
                'image'            => 'https://images.unsplash.com/photo-1521572163474-37898b6baf30?w=200&h=200&fit=crop',
                'category'         => 'T-Shirts',
                'brand'            => 'Basic Wear',
                'similarity_score' => 0.92
            ],
            [
                'name'             => 'Slim Black Jeans',
                'price'            => 69.99,
                'image'            => 'https://images.unsplash.com/photo-1475178626620-a4d074967452?w=200&h=200&fit=crop',
                'category'         => 'Jeans',
                'brand'            => 'Urban Denim',
                'similarity_score' => 0.88
            ],
            [
                'name'             => 'White Canvas Sneakers',
                'price'            => 79.99,
                'image'            => 'https://images.unsplash.com/photo-1549298916-b41d501d3772?w=200&h=200&fit=crop',
                'category'         => 'Shoes',
                'brand'            => 'Canvas Co.',
                'similarity_score' => 0.85
            ],
            [
                'name'             => 'Silver Sports Watch',
                'price'            => 199.99,
                'image'            => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=200&h=200&fit=crop',
                'category'         => 'Watches',
                'brand'            => 'TimeStyle',
                'similarity_score' => 0.78
            ]
        ];

        return $mockProducts;
    }

    public function getAnalysisHistory()
    {
        return response()->json([
            'analyses' => []
        ]);
    }
}
