@extends('layout')

@section('title', 'Style - Glamdar')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Back Button -->
        <a href="{{ route('home') }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 mb-6 font-medium">
            <i class="fas fa-arrow-left mr-2"></i>Back to Home
        </a>

        @if($style)
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="grid md:grid-cols-2 gap-0">
                    <!-- Image Section with Carousel -->
                    @php
                        $allImages = $style->getAllImages();
                        $imageCount = count($allImages);
                    @endphp
                    <div class="bg-gray-100 relative" id="style-image-carousel-{{ $style->id }}">
                        <div class="relative w-full h-full min-h-[400px] overflow-hidden">
                            @foreach($allImages as $index => $image)
                                <img src="{{ $image['url'] }}"
                                     alt="Style image {{ $index + 1 }}"
                                     class="style-carousel-image w-full h-full object-cover min-h-[400px] {{ $index === 0 ? 'active' : 'hidden' }}"
                                     data-index="{{ $index }}" />
                            @endforeach
                        </div>
                        <!-- Navigation Arrows -->
                        @if($imageCount > 1)
                        <button onclick="previousStyleImage({{ $style->id }})"
                                class="absolute left-4 top-1/2 transform -translate-y-1/2 z-10 p-3 bg-black bg-opacity-50 text-white rounded-full hover:bg-opacity-70 transition-opacity">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button onclick="nextStyleImage({{ $style->id }})"
                                class="absolute right-4 top-1/2 transform -translate-y-1/2 z-10 p-3 bg-black bg-opacity-50 text-white rounded-full hover:bg-opacity-70 transition-opacity">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                        <!-- Image Indicators -->
                        <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex gap-2 z-10">
                            @for($i = 0; $i < $imageCount; $i++)
                                <div class="style-indicator w-2 h-2 rounded-full {{ $i === 0 ? 'bg-white' : 'bg-white bg-opacity-50' }}"
                                     data-index="{{ $i }}"
                                     onclick="showStyleImage({{ $style->id }}, {{ $i }})"></div>
                            @endfor
                        </div>
                        @endif
                    </div>

                    <!-- Details Section -->
                    <div class="p-8">
                        <!-- Header with Like and Favourite buttons -->
                        <div class="flex items-start justify-between mb-6">
                            <div class="flex-1">
                                @if($style->user)
                                    <p class="text-sm text-gray-500 mb-2">By <span class="font-medium text-gray-700">{{ $style->user->name }}</span></p>
                                @endif
                                <div class="flex items-center gap-4 mb-4">
                                    <!-- Likes -->
                                    <div class="flex items-center gap-2">
                                        @if(auth()->check() && $style->user_id !== auth()->id())
                                            <button onclick="toggleLike({{ $style->id }})"
                                                    class="flex items-center gap-2 px-4 py-2 rounded-full {{ $style->is_liked ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} transition-colors like-btn-{{ $style->id }}">
                                                <i class="fas fa-thumbs-up {{ $style->is_liked ? 'text-white' : 'text-blue-500' }}"></i>
                                                <span>{{ $style->is_liked ? 'Liked' : 'Like' }}</span>
                                                <span class="ml-2" id="likes-count-{{ $style->id }}">{{ $style->likes_count ?? 0 }}</span>
                                            </button>
                                        @else
                                            <div class="flex items-center gap-2 px-4 py-2 bg-gray-100 rounded-full">
                                                <i class="fas fa-thumbs-up text-blue-500"></i>
                                                <span class="font-medium">{{ $style->likes_count ?? 0 }}</span>
                                                <span class="text-gray-500 text-sm">likes</span>
                                            </div>
                                        @endif
                                    </div>
                                    <!-- Favourites -->
                                    <div class="flex items-center gap-2">
                                        <button onclick="toggleStyleFavourite({{ $style->id }})"
                                                class="flex items-center gap-2 px-4 py-2 rounded-full {{ $style->is_favourited ? 'bg-red-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} transition-colors style-fav-btn-{{ $style->id }}">
                                            <i class="fas fa-heart {{ $style->is_favourited ? 'text-white' : 'text-red-500' }}"></i>
                                            <span>{{ $style->is_favourited ? 'Favourited' : 'Favourite' }}</span>
                                            <span class="ml-2" id="style-fav-count-{{ $style->id }}">{{ $style->style_favourites_count ?? 0 }}</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tags -->
                        @if($style->styleTags && $style->styleTags->count() > 0)
                            <div class="mb-6">
                                <h3 class="text-sm font-semibold text-gray-700 mb-3">Tags</h3>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($style->styleTags as $styleTag)
                                        <span class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-sm font-medium">{{ $styleTag->tag }}</span>
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

                        <!-- Product Links -->
                        @if($style->productLinks && $style->productLinks->count() > 0)
                            <div class="mb-6">
                                <h3 class="text-sm font-semibold text-gray-700 mb-3">Product Links ({{ $style->productLinks->count() }})</h3>
                                <div class="space-y-2 max-h-96 overflow-y-auto">
                                    @foreach($style->productLinks as $link)
                                        <div class="relative p-3 bg-gray-50 rounded-lg hover:bg-indigo-50 transition-colors group">
                                            @if(auth()->check())
                                                <div class="absolute top-2 right-2">
                                                    <button onclick="toggleFavourite({{ $link->id }}); return false;"
                                                            class="p-1 {{ $link->is_favourited ? 'text-red-600' : 'text-gray-400' }} hover:bg-red-50 rounded favourite-btn-{{ $link->id }}"
                                                            title="Favourite">
                                                        <i class="fas fa-heart text-xs"></i>
                                                    </button>
                                                </div>
                                            @endif
                                            <a href="#" onclick="trackLinkClick({{ $link->id }}, '{{ $link->url }}'); return false;" target="_blank" rel="noopener noreferrer" class="block pr-8">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-sm font-medium text-gray-900 group-hover:text-indigo-600 truncate">
                                                            {{ $link->title !== 'User Provided Link' ? $link->title : 'Custom Link' }}
                                                        </p>
                                                        <p class="text-xs text-gray-500 truncate mt-1">{{ $link->url }}</p>
                                                        <div class="flex items-center gap-2 mt-1">
                                                            @if($link->platform)
                                                                <span class="inline-block px-2 py-0.5 bg-indigo-100 text-indigo-600 rounded text-xs">{{ $link->platform }}</span>
                                                            @endif
                                                            @if($link->price && $link->price !== 'N/A')
                                                                <span class="text-xs font-semibold text-green-600">{{ $link->price }}</span>
                                                            @endif
                                                            @if($hasActiveSubscription)
                                                                <span class="text-xs text-gray-500 flex items-center gap-1" id="visits-{{ $link->id }}">
                                                                    <i class="fas fa-eye"></i>
                                                                    <span>{{ $link->visits ?? 0 }}</span>
                                                                </span>
                                                            @endif
                                                            @if(auth()->check())
                                                                <span class="text-xs text-gray-500 flex items-center gap-1" id="favourites-count-{{ $link->id }}">
                                                                    <i class="fas fa-heart"></i>
                                                                    <span>{{ $link->favourites_count ?? 0 }}</span>
                                                                </span>
                                                            @endif
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

                        <!-- Actions (only for style owner) -->
                        @if(auth()->check() && $style->user_id === auth()->id())
                            <div class="mt-6 flex gap-3">
                                <a href="{{ route('styles.edit', $style->id) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm font-medium">
                                    <i class="fas fa-edit mr-2"></i>Edit Style
                                </a>
                                <button type="button" onclick="showDeleteModal({{ $style->id }})" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors text-sm font-medium">
                                    <i class="fas fa-trash mr-2"></i>Delete Style
                                </button>
                            </div>
                        @endif
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

<!-- Delete Confirmation Modal (only shown for style owner) -->
@if(auth()->check() && $style && $style->user_id === auth()->id())
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
                <form id="delete-form" method="POST" action="{{ route('styles.destroy', $style->id) }}" class="inline">
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
@endif

<script>
// Style image carousel functions
function showStyleImage(styleId, index) {
    const carousel = document.getElementById(`style-image-carousel-${styleId}`);
    if (!carousel) return;

    const images = carousel.querySelectorAll('.style-carousel-image');
    const indicators = carousel.querySelectorAll('.style-indicator');

    images.forEach((img, i) => {
        if (i === index) {
            img.classList.remove('hidden');
            img.classList.add('active');
        } else {
            img.classList.add('hidden');
            img.classList.remove('active');
        }
    });

    indicators.forEach((ind, i) => {
        if (i === index) {
            ind.classList.remove('bg-opacity-50');
            ind.classList.add('bg-white');
        } else {
            ind.classList.add('bg-opacity-50');
            ind.classList.remove('bg-white');
        }
    });
}

function nextStyleImage(styleId) {
    const carousel = document.getElementById(`style-image-carousel-${styleId}`);
    if (!carousel) return;

    const images = carousel.querySelectorAll('.style-carousel-image');
    const currentIndex = Array.from(images).findIndex(img => img.classList.contains('active'));
    const nextIndex = (currentIndex + 1) % images.length;

    showStyleImage(styleId, nextIndex);
}

function previousStyleImage(styleId) {
    const carousel = document.getElementById(`style-image-carousel-${styleId}`);
    if (!carousel) return;

    const images = carousel.querySelectorAll('.style-carousel-image');
    const currentIndex = Array.from(images).findIndex(img => img.classList.contains('active'));
    const prevIndex = (currentIndex - 1 + images.length) % images.length;

    showStyleImage(styleId, prevIndex);
}

// Delete Modal Functions
function showDeleteModal(styleId) {
    const modal = document.getElementById('delete-modal');
    if (modal) {
        modal.classList.remove('hidden');
    }
}

function closeDeleteModal() {
    const modal = document.getElementById('delete-modal');
    if (modal) {
        modal.classList.add('hidden');
    }
}

// Close modal when clicking outside
if (document.getElementById('delete-modal')) {
    document.getElementById('delete-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDeleteModal();
        }
    });
}

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
            const visitsElement = document.getElementById(`visits-${productLinkId}`);
            if (visitsElement) {
                visitsElement.querySelector('span').textContent = data.visits;
            }
            window.open(url, '_blank', 'noopener,noreferrer');
        } else {
            window.open(url, '_blank', 'noopener,noreferrer');
        }
    } catch (error) {
        window.open(url, '_blank', 'noopener,noreferrer');
    }
}

// Toggle favourite for product link
async function toggleFavourite(productLinkId) {
    const isAuthenticated = @json(auth()->check());
    if (!isAuthenticated) {
        alert('Please log in to favourite links');
        return;
    }

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
            const favCount = document.getElementById(`favourites-count-${productLinkId}`);
            if (favCount) {
                favCount.querySelector('span').textContent = data.favourites_count;
            }
        }
    } catch (error) {
        console.error('Error toggling favourite:', error);
    }
}

// Toggle style favourite
async function toggleStyleFavourite(photoAnalysisId) {
    try {
        const response = await fetch(`/api/style-favourites/${photoAnalysisId}/toggle`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        });

        const data = await response.json();

        if (data.success) {
            const favBtn = document.querySelector(`.style-fav-btn-${photoAnalysisId}`);
            if (favBtn) {
                if (data.is_favourited) {
                    favBtn.classList.remove('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
                    favBtn.classList.add('bg-red-500', 'text-white');
                    favBtn.querySelector('i').classList.remove('text-red-500');
                    favBtn.querySelector('i').classList.add('text-white');
                    const span = favBtn.querySelector('span');
                    if (span) span.textContent = 'Favourited';
                } else {
                    favBtn.classList.remove('bg-red-500', 'text-white');
                    favBtn.classList.add('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
                    favBtn.querySelector('i').classList.remove('text-white');
                    favBtn.querySelector('i').classList.add('text-red-500');
                    const span = favBtn.querySelector('span');
                    if (span) span.textContent = 'Favourite';
                }
            }
            const favCount = document.getElementById(`style-fav-count-${photoAnalysisId}`);
            if (favCount) {
                favCount.textContent = data.favourites_count;
            }
        } else {
            if (data.message && data.message.includes('log in')) {
                alert('Please log in to favourite styles');
            }
        }
    } catch (error) {
        console.error('Error toggling style favourite:', error);
    }
}

// Toggle like
async function toggleLike(photoAnalysisId) {
    const isAuthenticated = @json(auth()->check());
    if (!isAuthenticated) {
        alert('Please log in to like styles');
        return;
    }

    try {
        const response = await fetch(`/api/likes/${photoAnalysisId}/toggle`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        });

        const data = await response.json();

        if (data.success) {
            const likeBtn = document.querySelector(`.like-btn-${photoAnalysisId}`);
            if (likeBtn) {
                if (data.is_liked) {
                    likeBtn.classList.remove('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
                    likeBtn.classList.add('bg-blue-500', 'text-white');
                    likeBtn.querySelector('i').classList.remove('text-blue-500');
                    likeBtn.querySelector('i').classList.add('text-white');
                    const span = likeBtn.querySelector('span');
                    if (span) span.textContent = 'Liked';
                } else {
                    likeBtn.classList.remove('bg-blue-500', 'text-white');
                    likeBtn.classList.add('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
                    likeBtn.querySelector('i').classList.remove('text-white');
                    likeBtn.querySelector('i').classList.add('text-blue-500');
                    const span = likeBtn.querySelector('span');
                    if (span) span.textContent = 'Like';
                }
            }
            const likesCount = document.getElementById(`likes-count-${photoAnalysisId}`);
            if (likesCount) {
                likesCount.textContent = data.likes_count;
            }
        } else {
            alert(data.message || 'Error liking style');
        }
    } catch (error) {
        console.error('Error toggling like:', error);
    }
}
</script>
@endsection

