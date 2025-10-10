@extends('layout')

@section('title', 'Analysis Results | CelebStyle')

@section('content')
    <div class="min-h-screen bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="p-6 sm:p-8">
                    <h1 class="text-3xl font-display font-bold text-primary mb-6">Analysis Results</h1>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Uploaded Image -->
                        <div class="flex justify-center">
                            <img
                                id="uploaded-image"
                                src=""
                                alt="Uploaded Celebrity Photo"
                                class="w-full max-w-md max-h-[60vh] object-contain rounded-xl bg-gray-50"
                            >
                        </div>

                        <!-- Analysis Results -->
                        <div class="space-y-6 max-h-[60vh] overflow-y-auto pr-4 scrollbar-thin scrollbar-thumb-indigo-400 scrollbar-track-gray-100">
                            <div id="results-content" class="space-y-6">
                                <!-- Results will be populated here -->
                            </div>
                        </div>
                    </div>

                    <!-- Back Button -->
                    <div class="mt-8 flex justify-center">
                        <a href="/" class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-medium">
                            Back to Home
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const uploadedImage = document.getElementById('uploaded-image');
            const resultsContent = document.getElementById('results-content');

            // Retrieve data from sessionStorage
            const analysisResults = JSON.parse(sessionStorage.getItem('analysisResults'));

            if (analysisResults && analysisResults.success) {
                // Set image URL
                uploadedImage.src = analysisResults.image_url || '';
                uploadedImage.alt = analysisResults.image_url ? 'Uploaded Celebrity Photo' : 'No image available';

                // Populate results
                resultsContent.innerHTML = `
            <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                <div class="flex items-center mb-4">
                    <i class="fas fa-check-circle text-green-600 mr-2"></i>
                    <h4 class="font-semibold text-green-800 text-lg">Analysis Complete</h4>
                </div>
                <div class="space-y-6">
                    <!-- Detected Items with Confidence Scores -->
                    <div>
                        <strong class="text-gray-700">Detected Items:</strong>
                        <div class="mt-2 flex flex-wrap gap-2">
                            ${
                    analysisResults.detected_items && analysisResults.confidence_scores
                        ? analysisResults.detected_items.map(item => `
                                    <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm">
                                        ${item} (${(analysisResults.confidence_scores[item] * 100).toFixed(1)}%)
                                    </span>
                                `).join('')
                        : '<span class="text-gray-500">No items detected</span>'
                }
                        </div>
                    </div>
                    <!-- Similar Products -->
                    ${
                    analysisResults.similar_products
                        ? `
                            <div>
                                <strong class="text-gray-700">Similar Products:</strong>
                                <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    ${analysisResults.similar_products.map(product => `
                                        <div class="border rounded-lg p-4 hover:shadow-md transition">
                                            <img src="${product.image}" alt="${product.name}" class="w-full h-32 object-cover rounded mb-3">
                                            <h5 class="font-medium text-sm">${product.name}</h5>
                                            <p class="text-xs text-gray-600">$${product.price}</p>
                                            <p class="text-xs text-gray-500">Brand: ${product.brand}</p>
                                            <p class="text-xs text-gray-500">Category: ${product.category}</p>
                                            <p class="text-xs text-gray-500">Similarity: ${(product.similarity_score * 100).toFixed(1)}%</p>
                                        </div>
                                    `).join('')}
                                </div>
                            </div>
                        `
                        : ''
                }
                    <!-- Analysis Metadata -->
                    ${
                    analysisResults.analysis_metadata
                        ? `
                            <div>
                                <strong class="text-gray-700">Analysis Details:</strong>
                                <div class="mt-2 text-sm text-gray-600">
                                    <p>Upload Time: ${new Date(analysisResults.analysis_metadata.upload_time).toLocaleString()}</p>
                                    <p>File Size: ${(analysisResults.analysis_metadata.file_size / 1024 / 1024).toFixed(2)} MB</p>
                                    <p>Dimensions: ${analysisResults.analysis_metadata.dimensions[0]} x ${analysisResults.analysis_metadata.dimensions[1]} px</p>
                                </div>
                            </div>
                        `
                        : ''
                }
                </div>
            </div>
        `;
            } else {
                uploadedImage.alt = 'No image available';
                resultsContent.innerHTML = `
            <div class="text-center text-gray-500">
                <p>No analysis results available.</p>
            </div>
        `;
            }
        });
    </script>
@endsection
