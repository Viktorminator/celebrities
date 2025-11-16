@extends('layout')

@section('title', 'Analysis Results | Glamdar')

@section('content')
    <div class="min-h-screen bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <!-- Header -->
            <div class="mb-8">
                <a href="/" class="text-indigo-600 hover:text-indigo-800 inline-flex items-center mb-4 font-medium">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Home
                </a>
                <h1 class="font-display text-3xl font-bold text-indigo-900">Fashion Analysis Results</h1>
            </div>

            <!-- Loading State -->
            <div id="loading" class="text-center py-12">
                <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
                <p class="mt-4 text-gray-600">Analyzing your photo...</p>
            </div>

            <!-- Error State -->
            <div id="error" class="hidden bg-red-50 border border-red-200 rounded-xl p-6">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-red-500 text-2xl mr-3"></i>
                    <div>
                        <h3 class="text-lg font-semibold text-red-900">Analysis Failed</h3>
                        <p id="error-message" class="text-red-700 mt-1"></p>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="/"
                       class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        <i class="fas fa-home mr-2"></i> Return Home
                    </a>
                </div>
            </div>

            <!-- Results Container -->
            <div id="results" class="hidden">
                <!-- Share Section -->
                <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                    <h2 class="text-xl font-bold text-indigo-900 mb-4">
                        <i class="fas fa-share-alt mr-2"></i> Share Results
                    </h2>
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                        <input
                            type="text"
                            id="share-url"
                            readonly
                            class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            value=""
                        >
                        <button
                            onclick="copyShareLink()"
                            class="px-6 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium whitespace-nowrap"
                        >
                            <i class="fas fa-copy mr-2"></i> Copy Link
                        </button>
                    </div>
                    <div class="mt-4 flex flex-wrap gap-3">
                        <button onclick="shareOnTwitter()"
                                class="px-4 py-2 bg-blue-400 text-white rounded-lg hover:bg-blue-500 transition-colors font-medium">
                            <i class="fab fa-twitter mr-2"></i> Twitter
                        </button>
                        <button onclick="shareOnFacebook()"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                            <i class="fab fa-facebook mr-2"></i> Facebook
                        </button>
                        <button onclick="shareOnWhatsApp()"
                                class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors font-medium">
                            <i class="fab fa-whatsapp mr-2"></i> WhatsApp
                        </button>
                    </div>
                </div>

                <!-- Image and Metadata -->
                <div class="grid md:grid-cols-2 gap-6 mb-6">
                    <!-- Analyzed Image -->
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <h2 class="text-xl font-bold text-indigo-900 mb-4">Analyzed Image</h2>
                        <img id="analyzed-image" src="" alt="Analyzed photo" class="w-full rounded-lg shadow-sm">
                        <div id="metadata" class="mt-4 space-y-2 text-sm text-gray-600"></div>
                    </div>

                    <!-- Detection Summary -->
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <h2 class="text-xl font-bold text-indigo-900 mb-4">Detection Summary</h2>
                        <div id="detection-summary" class="space-y-3"></div>
                    </div>
                </div>

                <!-- Detected Items -->
                <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                    <h2 class="text-xl font-bold text-indigo-900 mb-4">
                        <i class="fas fa-tshirt mr-2"></i> Detected Items
                    </h2>
                    <div id="detected-items" class="grid md:grid-cols-2 lg:grid-cols-3 gap-4"></div>
                </div>

                <!-- Shopping Links -->
                <div class="bg-white rounded-xl shadow-md p-6">
                    <h2 class="text-xl font-bold text-indigo-900 mb-4">
                        <i class="fas fa-shopping-bag mr-2"></i> Shop Similar Items
                    </h2>
                    <div id="shopping-links" class="space-y-6"></div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="/"
                       class="inline-flex items-center justify-center px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium">
                        <i class="fas fa-camera mr-2"></i> Analyze Another Photo
                    </a>
                    <button onclick="window.print()"
                            class="inline-flex items-center justify-center px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors font-medium">
                        <i class="fas fa-print mr-2"></i> Print Results
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Link Modal -->
    @auth
    <div id="product-link-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 id="product-link-modal-title" class="text-lg font-medium text-gray-900 mb-4">Add Product Link</h3>
                <form id="product-link-form" onsubmit="saveProductLink(event)">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Title *</label>
                            <input type="text" id="product-link-title" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">URL *</label>
                            <input type="url" id="product-link-url" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Platform *</label>
                            <select id="product-link-platform" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="Amazon">Amazon</option>
                                <option value="Google Shopping">Google Shopping</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Price</label>
                            <input type="text" id="product-link-price"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Image URL</label>
                            <input type="url" id="product-link-image-url"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">ASIN (Amazon)</label>
                            <input type="text" id="product-link-asin"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" onclick="closeProductLinkModal()"
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endauth

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Get analysis ID from URL parameter
            const urlParams = new URLSearchParams(window.location.search);
            const analysisId = urlParams.get('id');

            if (!analysisId) {
                showError('No analysis ID provided');
            } else {
                loadAnalysis(analysisId);
            }
        });

        async function loadAnalysis(id) {
            try {
                const response = await fetch(`/api/photo-analysis/${id}`);
                const data = await response.json();

                if (!data.success) {
                    showError(data.message || 'Failed to load analysis');
                    return;
                }

                displayResults(data.analysis);
            } catch (error) {
                showError('Error loading analysis: ' + error.message);
            }
        }

        function displayResults(analysis) {
            window.currentAnalysis = analysis;
            document.getElementById('loading').classList.add('hidden');
            document.getElementById('results').classList.remove('hidden');

            // Set share URL
            const shareUrl = window.location.href;
            document.getElementById('share-url').value = shareUrl;

            // Display image
            document.getElementById('analyzed-image').src = analysis.image_url;

            // Display metadata
            const metadata = analysis.analysis_metadata || {};
            document.getElementById('metadata').innerHTML = `
                <p class="flex items-center"><i class="fas fa-calendar-alt w-5 text-indigo-600 mr-2"></i> <strong class="mr-2">Uploaded:</strong> ${new Date(analysis.created_at).toLocaleString()}</p>
                <p class="flex items-center"><i class="fas fa-file-alt w-5 text-indigo-600 mr-2"></i> <strong class="mr-2">File Size:</strong> ${formatFileSize(analysis.file_size)}</p>
                <p class="flex items-center"><i class="fas fa-image w-5 text-indigo-600 mr-2"></i> <strong class="mr-2">Dimensions:</strong> ${analysis.dimensions || 'N/A'}</p>
                <p class="flex items-center"><i class="fas fa-tags w-5 text-indigo-600 mr-2"></i> <strong class="mr-2">Items Detected:</strong> ${analysis.detected_items.length}</p>
            `;

            // Display detection summary
            const summaryHtml = analysis.detected_items.map(item => `
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <div class="flex items-center space-x-3">
                        <div class="w-3 h-3 rounded-full ${getConfidenceColor(item.confidence)}"></div>
                        <span class="font-medium text-gray-900">${item.description}</span>
                    </div>
                    <span class="text-sm text-gray-600 font-medium">${item.confidence}%</span>
                </div>
            `).join('');
            document.getElementById('detection-summary').innerHTML = summaryHtml || '<p class="text-gray-500">No items detected</p>';

            // Display detected items
            const itemsHtml = analysis.detected_items.map(item => `
                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                    <h3 class="font-semibold text-lg text-indigo-900 mb-3">${item.description}</h3>
                    <div class="space-y-2 text-sm text-gray-600">
                        <p><strong>Category:</strong> <span class="capitalize">${item.category}</span></p>
                        ${item.color ? `<p><strong>Color:</strong> <span class="capitalize">${item.color}</span></p>` : ''}
                        <p><strong>Confidence:</strong> ${item.confidence}%</p>
                    </div>
                    <div class="mt-3">
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-medium ${getConfidenceBadgeClass(item.confidence)}">
                            ${getConfidenceLevel(item.confidence)}
                        </span>
                    </div>
                </div>
            `).join('');
            document.getElementById('detected-items').innerHTML = itemsHtml || '<p class="text-gray-500">No items detected</p>';

            // Display shopping links
            const isAuthenticated = @json(auth()->check());
            const shoppingHtml = analysis.detected_items.map((item, index) => {
                const hasLinks = item.product_links && item.product_links.length > 0;

                return `
                    <div class="border-t border-gray-200 pt-6 first:border-t-0 first:pt-0">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-semibold text-lg text-indigo-900">
                                <i class="fas fa-tag mr-2"></i>${item.description}
                            </h3>
                            ${isAuthenticated ? `
                                <button onclick="showAddProductLinkModal(${item.id})"
                                        class="text-sm bg-indigo-600 text-white px-3 py-1 rounded-lg hover:bg-indigo-700">
                                    <i class="fas fa-plus mr-1"></i>Add Link
                                </button>
                            ` : ''}
                        </div>
                        ${hasLinks ? `
                            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
                                ${item.product_links.map(product => `
                                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-lg hover:border-indigo-300 transition-all group relative">
                                        ${isAuthenticated ? `
                                            <div class="absolute top-2 right-2 flex space-x-1">
                                                <button onclick="showEditProductLinkModal(${product.id}, ${item.id})"
                                                        class="p-1 text-indigo-600 hover:bg-indigo-50 rounded">
                                                    <i class="fas fa-edit text-xs"></i>
                                                </button>
                                                <button onclick="deleteProductLink(${product.id})"
                                                        class="p-1 text-red-600 hover:bg-red-50 rounded">
                                                    <i class="fas fa-trash text-xs"></i>
                                                </button>
                                            </div>
                                        ` : ''}
                                        <a href="${product.url}" target="_blank" rel="nofollow noopener" class="block">
                                            <div class="flex items-start space-x-3">
                                                <i class="fab fa-amazon text-orange-500 text-2xl mt-1 flex-shrink-0"></i>
                                                <div class="flex-1 min-w-0">
                                                    <h4 class="font-medium text-gray-900 group-hover:text-indigo-600 transition-colors line-clamp-2 mb-1">
                                                        ${product.title}
                                                    </h4>
                                                    <p class="text-xs text-gray-500">${product.platform}</p>
                                                    ${product.price && product.price !== 'N/A' ?
                        `<p class="text-lg font-bold text-green-600 mt-2">${product.price}</p>` :
                        `<p class="text-sm text-gray-500 mt-2">View pricing</p>`
                    }
                                                </div>
                                            </div>
                                            <div class="mt-3 flex items-center text-indigo-600 text-sm font-medium">
                                                View on ${product.platform}
                                                <i class="fas fa-external-link-alt ml-2 text-xs"></i>
                                            </div>
                                        </a>
                                    </div>
                                `).join('')}
                            </div>
                        ` : `
                            <p class="text-gray-500 text-center py-4">No shopping links available</p>
                        `}
                    </div>
                `;
            }).join('');
            document.getElementById('shopping-links').innerHTML = shoppingHtml || '<p class="text-gray-500 text-center py-8">No items detected</p>';
        }

        function showError(message) {
            document.getElementById('loading').classList.add('hidden');
            document.getElementById('error').classList.remove('hidden');
            document.getElementById('error-message').textContent = message;
        }

        function getConfidenceColor(confidence) {
            if (confidence >= 80) return 'bg-green-500';
            if (confidence >= 50) return 'bg-yellow-500';
            return 'bg-red-500';
        }

        function getConfidenceBadgeClass(confidence) {
            if (confidence >= 80) return 'bg-green-100 text-green-800';
            if (confidence >= 50) return 'bg-yellow-100 text-yellow-800';
            return 'bg-red-100 text-red-800';
        }

        function getConfidenceLevel(confidence) {
            if (confidence >= 80) return 'High Confidence';
            if (confidence >= 50) return 'Medium Confidence';
            return 'Low Confidence';
        }

        function formatFileSize(bytes) {
            if (!bytes) return 'N/A';
            const units = ['B', 'KB', 'MB', 'GB'];
            let size = bytes;
            let unitIndex = 0;
            while (size > 1024 && unitIndex < units.length - 1) {
                size /= 1024;
                unitIndex++;
            }
            return `${size.toFixed(2)} ${units[unitIndex]}`;
        }

        function copyShareLink() {
            const input = document.getElementById('share-url');
            input.select();
            input.setSelectionRange(0, 99999); // For mobile devices

            try {
                document.execCommand('copy');

                // Show feedback
                const button = event.target.closest('button');
                const originalText = button.innerHTML;
                button.innerHTML = '<i class="fas fa-check mr-2"></i> Copied!';
                button.classList.add('bg-green-600');
                button.classList.remove('bg-indigo-600');

                setTimeout(() => {
                    button.innerHTML = originalText;
                    button.classList.remove('bg-green-600');
                    button.classList.add('bg-indigo-600');
                }, 2000);
            } catch (err) {
                alert('Failed to copy link. Please copy manually.');
            }
        }

        function shareOnTwitter() {
            const url = encodeURIComponent(window.location.href);
            const text = encodeURIComponent('Check out my fashion analysis on Glamdar! 👗✨');
            window.open(`https://twitter.com/intent/tweet?url=${url}&text=${text}`, '_blank', 'width=600,height=400');
        }

        function shareOnFacebook() {
            const url = encodeURIComponent(window.location.href);
            window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank', 'width=600,height=400');
        }

        function shareOnWhatsApp() {
            const url = encodeURIComponent(window.location.href);
            const text = encodeURIComponent('Check out my fashion analysis on Glamdar!');
            window.open(`https://wa.me/?text=${text} ${url}`, '_blank');
        }

        // Product Link Management Functions
        let currentDetectedItemId = null;
        let currentProductLinkId = null;

        function showAddProductLinkModal(detectedItemId) {
            currentDetectedItemId = detectedItemId;
            currentProductLinkId = null;
            document.getElementById('product-link-form').reset();
            document.getElementById('product-link-modal-title').textContent = 'Add Product Link';
            document.getElementById('product-link-modal').classList.remove('hidden');
        }

        function showEditProductLinkModal(productLinkId, detectedItemId) {
            currentProductLinkId = productLinkId;
            currentDetectedItemId = detectedItemId;

            // Find the product link data
            const analysis = window.currentAnalysis;
            let productLink = null;
            analysis.detected_items.forEach(item => {
                if (item.product_links) {
                    const found = item.product_links.find(p => p.id === productLinkId);
                    if (found) productLink = found;
                }
            });

            if (productLink) {
                document.getElementById('product-link-title').value = productLink.title || '';
                document.getElementById('product-link-url').value = productLink.url || '';
                document.getElementById('product-link-platform').value = productLink.platform || 'Amazon';
                document.getElementById('product-link-price').value = productLink.price || '';
                document.getElementById('product-link-image-url').value = productLink.image_url || '';
                document.getElementById('product-link-asin').value = productLink.asin || '';
            }

            document.getElementById('product-link-modal-title').textContent = 'Edit Product Link';
            document.getElementById('product-link-modal').classList.remove('hidden');
        }

        function closeProductLinkModal() {
            document.getElementById('product-link-modal').classList.add('hidden');
            currentDetectedItemId = null;
            currentProductLinkId = null;
        }

        async function saveProductLink(event) {
            event.preventDefault();

            const formData = {
                detected_item_id: currentDetectedItemId,
                title: document.getElementById('product-link-title').value,
                url: document.getElementById('product-link-url').value,
                platform: document.getElementById('product-link-platform').value,
                price: document.getElementById('product-link-price').value,
                image_url: document.getElementById('product-link-image-url').value,
                asin: document.getElementById('product-link-asin').value,
            };

            try {
                const url = currentProductLinkId
                    ? `/api/product-links/${currentProductLinkId}`
                    : '/api/product-links';
                const method = currentProductLinkId ? 'PUT' : 'POST';

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(formData)
                });

                const data = await response.json();

                if (data.success) {
                    closeProductLinkModal();
                    // Reload the analysis to show updated links
                    const analysisId = new URLSearchParams(window.location.search).get('id');
                    loadAnalysis(analysisId);
                } else {
                    alert('Error: ' + (data.message || 'Failed to save product link'));
                }
            } catch (error) {
                alert('Error: ' + error.message);
            }
        }

        async function deleteProductLink(productLinkId) {
            if (!confirm('Are you sure you want to delete this product link?')) {
                return;
            }

            try {
                const response = await fetch(`/api/product-links/${productLinkId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();

                if (data.success) {
                    // Reload the analysis to show updated links
                    const analysisId = new URLSearchParams(window.location.search).get('id');
                    loadAnalysis(analysisId);
                } else {
                    alert('Error: ' + (data.message || 'Failed to delete product link'));
                }
            } catch (error) {
                alert('Error: ' + error.message);
            }
        }
    </script>

    <style>
        @media print {
            .no-print {
                display: none !important;
            }
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
@endsection
