@extends('layout')

@section('title', 'Style Details - Glamdar')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Back Button -->
        <a href="{{ route('styles.index') }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 mb-6 font-medium">
            <i class="fas fa-arrow-left mr-2"></i>Back to My Styles
        </a>

        @if($style)
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="grid md:grid-cols-2 gap-0">
                    <!-- Image Section -->
                    <div class="bg-gray-100">
                        <img src="{{ $style->image_url }}" alt="Style image" class="w-full h-full object-cover min-h-[400px]">
                    </div>

                    <!-- Details Section -->
                    <div class="p-8">
                        <!-- Tags -->
                        @if($style->analysis_metadata && isset($style->analysis_metadata['user_tags']) && count($style->analysis_metadata['user_tags']) > 0)
                            <div class="mb-6">
                                <h3 class="text-sm font-semibold text-gray-700 mb-3">Tags</h3>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($style->analysis_metadata['user_tags'] as $tag)
                                        <span class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-sm font-medium">{{ $tag }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Description -->
                        @if($style->analysis_metadata && isset($style->analysis_metadata['description']))
                            <div class="mb-6">
                                <h3 class="text-sm font-semibold text-gray-700 mb-2">Description</h3>
                                <p class="text-gray-700">{{ $style->analysis_metadata['description'] }}</p>
                            </div>
                        @endif

                        <!-- Links -->
                        @php
                            $links = $style->productLinks;
                        @endphp
                        @if($links->count() > 0)
                            <div class="mb-6">
                                <h3 class="text-sm font-semibold text-gray-700 mb-3">Product Links ({{ $links->count() }})</h3>
                                <div class="space-y-2">
                                    @foreach($links as $link)
                                        <div class="relative p-3 bg-gray-50 rounded-lg hover:bg-indigo-50 transition-colors group">
                                            <div class="absolute top-2 right-2">
                                                <button onclick="toggleFavourite({{ $link->id }}); return false;"
                                                        class="p-1 {{ $link->is_favourited ? 'text-red-600' : 'text-gray-400' }} hover:bg-red-50 rounded favourite-btn-{{ $link->id }}"
                                                        title="Favourite">
                                                    <i class="fas fa-heart text-xs"></i>
                                                </button>
                                            </div>
                                            <a href="#" onclick="trackLinkClick({{ $link->id }}, '{{ $link->url }}'); return false;" target="_blank" rel="noopener noreferrer" class="block">
                                                <div class="flex items-center justify-between pr-8">
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-sm font-medium text-gray-900 group-hover:text-indigo-600 truncate">
                                                            {{ $link->title !== 'User Provided Link' ? $link->title : 'Custom Link' }}
                                                        </p>
                                                        <p class="text-xs text-gray-500 truncate mt-1">{{ $link->url }}</p>
                                                        <div class="flex items-center gap-2 mt-1">
                                                            @if($link->platform)
                                                                <span class="inline-block px-2 py-0.5 bg-indigo-100 text-indigo-600 rounded text-xs">{{ $link->platform }}</span>
                                                            @endif
                                                            @if($hasActiveSubscription)
                                                                <span class="text-xs text-gray-500 flex items-center gap-1" id="visits-{{ $link->id }}">
                                                                    <i class="fas fa-eye"></i>
                                                                    <span>{{ $link->visits ?? 0 }}</span>
                                                                </span>
                                                            @endif
                                                            <span class="text-xs text-gray-500 flex items-center gap-1" id="favourites-count-{{ $link->id }}">
                                                                <i class="fas fa-heart"></i>
                                                                <span>{{ $link->favourites_count ?? 0 }}</span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <i class="fas fa-external-link-alt text-gray-400 group-hover:text-indigo-600 ml-2"></i>
                                                </div>
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Metadata -->
                        <div class="border-t border-gray-200 pt-6">
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-500">Added:</span>
                                    <span class="text-gray-900 font-medium ml-2">{{ $style->created_at->format('M d, Y g:i A') }}</span>
                                </div>
                                @if($style->dimensions)
                                    <div>
                                        <span class="text-gray-500">Dimensions:</span>
                                        <span class="text-gray-900 font-medium ml-2">{{ $style->dimensions }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="mt-6 flex gap-3">
                            <button type="button" onclick="showDeleteModal({{ $style->id }})" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors text-sm font-medium">
                                <i class="fas fa-trash mr-2"></i>Delete Style
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-white rounded-xl shadow-md p-12 text-center">
                <p class="text-gray-600">Style not found</p>
            </div>
        @endif
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="delete-modal" class="fixed inset-0 bg-black bg-opacity-60 hidden z-[1000] flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl transform transition-all duration-300">
        <div class="p-6">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0 w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mr-4">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Delete Style</h3>
                    <p class="text-sm text-gray-500">This action cannot be undone</p>
                </div>
            </div>
            <p class="text-gray-700 mb-6">Are you sure you want to delete this style? All associated links and data will be permanently removed.</p>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 text-sm font-medium transition-colors">
                    Cancel
                </button>
                <form id="delete-form" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 text-sm font-medium transition-colors">
                        <i class="fas fa-trash mr-2"></i>Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Delete Modal Functions
function showDeleteModal(styleId) {
    const modal = document.getElementById('delete-modal');
    const form = document.getElementById('delete-form');
    form.action = `/styles/${styleId}`;
    modal.classList.remove('hidden');
}

function closeDeleteModal() {
    const modal = document.getElementById('delete-modal');
    modal.classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('delete-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteModal();
    }
});

// Track link clicks
async function trackLinkClick(productLinkId, url) {
    try {
        const response = await fetch(`/api/product-links/${productLinkId}/track`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        const data = await response.json();

        if (data.success) {
            // Update the visits count in the UI (only if visible for subscribed users)
            const visitsElement = document.getElementById(`visits-${productLinkId}`);
            if (visitsElement) {
                visitsElement.querySelector('span').textContent = data.visits;
            }
            // Open the link in a new tab
            window.open(url, '_blank', 'noopener,noreferrer');
        } else {
            // If tracking fails, still open the link
            window.open(url, '_blank', 'noopener,noreferrer');
        }
    } catch (error) {
        // If tracking fails, still open the link
        window.open(url, '_blank', 'noopener,noreferrer');
    }
}

// Toggle favourite
async function toggleFavourite(productLinkId) {
    try {
        const response = await fetch(`/api/favourites/${productLinkId}/toggle`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        });

        const data = await response.json();

        if (data.success) {
            // Update favourite button
            const favBtn = document.querySelector(`.favourite-btn-${productLinkId}`);
            if (favBtn) {
                if (data.is_favourited) {
                    favBtn.classList.remove('text-gray-400');
                    favBtn.classList.add('text-red-600');
                } else {
                    favBtn.classList.remove('text-red-600');
                    favBtn.classList.add('text-gray-400');
                }
            }
            // Update favourites count
            const favCount = document.getElementById(`favourites-count-${productLinkId}`);
            if (favCount) {
                favCount.querySelector('span').textContent = data.favourites_count;
            }
        }
    } catch (error) {
        console.error('Error toggling favourite:', error);
    }
}
</script>
@endsection

