@extends('layout')

@section('title', 'Home | Glamdar')

@section('content')
    <div class="min-h-screen bg-gray-50">
        {{-- Navigation Menu Container --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            {{-- Categories --}}
            <section class="mb-12">
                <div class="flex flex-wrap items-center justify-between mb-6">
                    <div class="flex space-x-2 overflow-x-auto py-2 scrollbar-hide">
                        <a href="?category=All" class="whitespace-nowrap px-4 py-2 {{ request('category', 'All') == 'All' ? 'bg-indigo-600 text-white' : 'bg-gray-100 hover:bg-gray-200' }} rounded-full text-sm font-medium transition-colors">All Styles</a>
                        <a href="?category=Casual" class="whitespace-nowrap px-4 py-2 {{ request('category') == 'Casual' ? 'bg-indigo-600 text-white' : 'bg-gray-100 hover:bg-gray-200' }} rounded-full text-sm font-medium transition-colors">Casual</a>
                        <a href="?category=Formal" class="whitespace-nowrap px-4 py-2 {{ request('category') == 'Formal' ? 'bg-indigo-600 text-white' : 'bg-gray-100 hover:bg-gray-200' }} rounded-full text-sm font-medium transition-colors">Formal</a>
                        <a href="?category=Streetwear" class="whitespace-nowrap px-4 py-2 {{ request('category') == 'Streetwear' ? 'bg-indigo-600 text-white' : 'bg-gray-100 hover:bg-gray-200' }} rounded-full text-sm font-medium transition-colors">Streetwear</a>
                        <a href="?category=Vintage" class="whitespace-nowrap px-4 py-2 {{ request('category') == 'Vintage' ? 'bg-indigo-600 text-white' : 'bg-gray-100 hover:bg-gray-200' }} rounded-full text-sm font-medium transition-colors">Vintage</a>
                        <a href="?category=Bohemian" class="whitespace-nowrap px-4 py-2 {{ request('category') == 'Bohemian' ? 'bg-indigo-600 text-white' : 'bg-gray-100 hover:bg-gray-200' }} rounded-full text-sm font-medium transition-colors">Bohemian</a>
                        <a href="?category=Minimalist" class="whitespace-nowrap px-4 py-2 {{ request('category') == 'Minimalist' ? 'bg-indigo-600 text-white' : 'bg-gray-100 hover:bg-gray-200' }} rounded-full text-sm font-medium transition-colors">Minimalist</a>
                        <a href="?category=Sporty" class="whitespace-nowrap px-4 py-2 {{ request('category') == 'Sporty' ? 'bg-indigo-600 text-white' : 'bg-gray-100 hover:bg-gray-200' }} rounded-full text-sm font-medium transition-colors">Sporty</a>
                        <a href="?category=Elegant" class="whitespace-nowrap px-4 py-2 {{ request('category') == 'Elegant' ? 'bg-indigo-600 text-white' : 'bg-gray-100 hover:bg-gray-200' }} rounded-full text-sm font-medium transition-colors">Elegant</a>
                        <a href="?category=Romantic" class="whitespace-nowrap px-4 py-2 {{ request('category') == 'Romantic' ? 'bg-indigo-600 text-white' : 'bg-gray-100 hover:bg-gray-200' }} rounded-full text-sm font-medium transition-colors">Romantic</a>
                        <a href="?category=Edgy" class="whitespace-nowrap px-4 py-2 {{ request('category') == 'Edgy' ? 'bg-indigo-600 text-white' : 'bg-gray-100 hover:bg-gray-200' }} rounded-full text-sm font-medium transition-colors">Edgy</a>
                        <a href="?category=Chic" class="whitespace-nowrap px-4 py-2 {{ request('category') == 'Chic' ? 'bg-indigo-600 text-white' : 'bg-gray-100 hover:bg-gray-200' }} rounded-full text-sm font-medium transition-colors">Chic</a>
                        <a href="?category=Beach" class="whitespace-nowrap px-4 py-2 {{ request('category') == 'Beach' ? 'bg-indigo-600 text-white' : 'bg-gray-100 hover:bg-gray-200' }} rounded-full text-sm font-medium transition-colors">Beach</a>
                    </div>
                </div>
            </section>

            {{-- Style Cards Section --}}
            @if(isset($styles))
            @if($styles->count() > 0)
            <section class="mb-12">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-0 sm:gap-8">
                    @foreach($styles as $style)
                        <div class="bg-white sm:rounded-xl shadow-none sm:shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-200 relative group border-b sm:border-b-0 border-gray-200">
                            <a href="{{ route('style.view', $style->id) }}" class="block">
                                <!-- Image Container with Instagram-like square on mobile -->
                                @php
                                    $allImages = $style->getAllImages();
                                    $imageCount = count($allImages);
                                @endphp
                                <div class="relative w-full aspect-square sm:aspect-auto sm:h-96 overflow-hidden bg-gray-100" id="image-carousel-{{ $style->id }}">
                                    <!-- Image Carousel -->
                                    <div class="relative w-full h-full overflow-hidden">
                                        @foreach($allImages as $index => $image)
                                            <img src="{{ $image['url'] }}"
                                                 alt="Style {{ $index + 1 }}"
                                                 class="carousel-image w-full h-full object-cover {{ $index === 0 ? 'active' : 'hidden' }}"
                                                 data-index="{{ $index }}"
                                                 loading="{{ $index === 0 ? 'lazy' : 'lazy' }}" />
                                        @endforeach
                                    </div>
                                    <!-- Action Buttons on Top Left -->
                                    <div class="absolute top-3 left-3 sm:top-4 sm:left-4 z-10 flex gap-2">
                                        <!-- Favourite Button -->
                                        <button onclick="event.preventDefault(); toggleStyleFavourite({{ $style->id }}); return false;"
                                                class="p-2 sm:p-2.5 {{ $style->is_favourited ? 'bg-red-500 text-white' : 'bg-white text-gray-400' }} rounded-full shadow-md hover:bg-red-500 hover:text-white transition-colors style-fav-btn-{{ $style->id }}"
                                                title="Add to Favourites">
                                            <i class="fas fa-heart text-xs sm:text-sm"></i>
                                        </button>
                                    </div>
                                    <!-- Like Button on Top Right -->
                                    @if(auth()->check() && $style->user_id !== auth()->id())
                                    <div class="absolute top-3 right-3 sm:top-4 sm:right-4 z-10">
                                        <button onclick="event.preventDefault(); toggleLike({{ $style->id }}); return false;"
                                                class="p-2 sm:p-2.5 {{ isset($style->is_liked) && $style->is_liked ? 'bg-blue-500 text-white' : 'bg-white text-gray-400' }} rounded-full shadow-md hover:bg-blue-500 hover:text-white transition-colors like-btn-{{ $style->id }}"
                                                title="Like">
                                            <i class="fas fa-thumbs-up text-xs sm:text-sm"></i>
                                        </button>
                                    </div>
                                    @endif
                                    <!-- Image Indicators/Bullets -->
                                    @if($imageCount > 1)
                                    <div class="absolute bottom-3 left-1/2 transform -translate-x-1/2 flex gap-1.5 z-10" id="image-indicators-{{ $style->id }}">
                                        @for($i = 0; $i < $imageCount; $i++)
                                            <div class="indicator {{ $i === 0 ? 'active' : '' }}" data-index="{{ $i }}" onclick="event.preventDefault(); event.stopPropagation(); showImage({{ $style->id }}, {{ $i }});"></div>
                                        @endfor
                                    </div>
                                    @endif
                                    <!-- Navigation Arrows (for desktop) -->
                                    @if($imageCount > 1)
                                    <button onclick="event.preventDefault(); event.stopPropagation(); previousImage({{ $style->id }});"
                                            class="absolute left-2 top-1/2 transform -translate-y-1/2 z-10 p-2 bg-black bg-opacity-50 text-white rounded-full hover:bg-opacity-70 transition-opacity hidden sm:block">
                                        <i class="fas fa-chevron-left text-sm"></i>
                                    </button>
                                    <button onclick="event.preventDefault(); event.stopPropagation(); nextImage({{ $style->id }});"
                                            class="absolute right-2 top-1/2 transform -translate-y-1/2 z-10 p-2 bg-black bg-opacity-50 text-white rounded-full hover:bg-opacity-70 transition-opacity hidden sm:block">
                                        <i class="fas fa-chevron-right text-sm"></i>
                                    </button>
                                    @endif
                                </div>
                                <div class="p-4 sm:p-6">
                                    @if($style->styleTags && $style->styleTags->count() > 0)
                                        <div class="flex flex-wrap gap-1.5 sm:gap-2 mb-2 sm:mb-3">
                                            @foreach($style->styleTags->take(3) as $styleTag)
                                                <span class="bg-indigo-100 text-indigo-600 rounded-full px-2 py-1 sm:px-3 sm:py-1.5 text-xs sm:text-sm font-medium">{{ $styleTag->tag }}</span>
                                            @endforeach
                                            @if($style->styleTags->count() > 3)
                                                <span class="text-gray-400 text-xs sm:text-sm">+{{ $style->styleTags->count() - 3 }}</span>
                                            @endif
                                        </div>
                                    @endif
                                    @if($style->user)
                                        <p class="text-sm sm:text-base text-gray-600 mb-2 sm:mb-3 font-medium">By {{ $style->user->name }}</p>
                                        @endif
                                    <div class="text-xs sm:text-sm text-gray-400">
                                        {{ $style->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            </a>
                            </div>
                        @endforeach
                    </div>
            </section>
            @else
            <section class="mb-12">
                <h2 class="text-2xl font-display font-bold text-indigo-900 mb-6">
                    @if(isset($category) && $category !== 'All')
                        {{ $category }} Styles
                    @else
                        Latest Styles
                    @endif
                </h2>
                <div class="bg-white rounded-xl shadow-md p-12 text-center">
                    <i class="fas fa-images text-gray-300 text-6xl mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">No Styles Found</h3>
                    <p class="text-gray-500 mb-6">
                        @if(isset($category) && $category !== 'All')
                            No styles found in the {{ $category }} category. Try browsing other categories!
                @else
                            No styles available yet. Be the first to add a style!
                        @endif
                    </p>
                    @if(isset($category) && $category !== 'All')
                        <a href="{{ route('home') }}" class="inline-block bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition-colors">
                            View All Styles
                        </a>
                    @endif
                    </div>
            </section>
            @endif
                @endif

            {{-- Style Inspiration Grid --}}
            <section>
                <div class="mt-8 flex justify-center">
                    <button class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium">
                        Load More Styles
                    </button>
                </div>
            </section>
        </div>
    </div>

<style>
/* Instagram-like mobile styling */
@media (max-width: 640px) {
    .grid.grid-cols-1 > div {
        margin-bottom: 0;
    }

    /* Image indicators/bullets styling */
    [id^="image-indicators-"] {
        display: flex !important;
    }

    [id^="image-indicators-"] .indicator {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background-color: rgba(255, 255, 255, 0.5);
        border: 1px solid rgba(0, 0, 0, 0.2);
        transition: all 0.3s ease;
    }

    [id^="image-indicators-"] .indicator.active {
        background-color: rgba(255, 255, 255, 0.9);
        width: 20px;
        border-radius: 3px;
    }
}
</style>

<script>
// Image carousel functions
function showImage(styleId, index) {
    const carousel = document.getElementById(`image-carousel-${styleId}`);
    if (!carousel) return;

    const images = carousel.querySelectorAll('.carousel-image');
    const indicators = carousel.querySelectorAll('.indicator');

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
            ind.classList.add('active');
        } else {
            ind.classList.remove('active');
        }
    });
}

function nextImage(styleId) {
    const carousel = document.getElementById(`image-carousel-${styleId}`);
    if (!carousel) return;

    const images = carousel.querySelectorAll('.carousel-image');
    const currentIndex = Array.from(images).findIndex(img => img.classList.contains('active'));
    const nextIndex = (currentIndex + 1) % images.length;

    showImage(styleId, nextIndex);
}

function previousImage(styleId) {
    const carousel = document.getElementById(`image-carousel-${styleId}`);
    if (!carousel) return;

    const images = carousel.querySelectorAll('.carousel-image');
    const currentIndex = Array.from(images).findIndex(img => img.classList.contains('active'));
    const prevIndex = (currentIndex - 1 + images.length) % images.length;

    showImage(styleId, prevIndex);
}

// Touch/swipe support for mobile
document.addEventListener('DOMContentLoaded', function() {
    const carousels = document.querySelectorAll('[id^="image-carousel-"]');

    carousels.forEach(carousel => {
        let startX = 0;
        let currentX = 0;
        let isDragging = false;

        carousel.addEventListener('touchstart', (e) => {
            startX = e.touches[0].clientX;
            isDragging = true;
        });

        carousel.addEventListener('touchmove', (e) => {
            if (!isDragging) return;
            currentX = e.touches[0].clientX;
        });

        carousel.addEventListener('touchend', (e) => {
            if (!isDragging) return;
            isDragging = false;

            const diffX = startX - currentX;
            const styleId = carousel.id.replace('image-carousel-', '');

            if (Math.abs(diffX) > 50) { // Minimum swipe distance
                if (diffX > 0) {
                    nextImage(styleId);
                } else {
                    previousImage(styleId);
                }
            }
        });
    });
});

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
            // Update favourite button
            const favBtn = document.querySelector(`.style-fav-btn-${photoAnalysisId}`);
            if (favBtn) {
                if (data.is_favourited) {
                    favBtn.classList.remove('bg-white', 'text-gray-400');
                    favBtn.classList.add('bg-red-500', 'text-white');
                        } else {
                    favBtn.classList.remove('bg-red-500', 'text-white');
                    favBtn.classList.add('bg-white', 'text-gray-400');
                }
            }
            // Update favourites count
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
            // Update like button
            const likeBtn = document.querySelector(`.like-btn-${photoAnalysisId}`);
            if (likeBtn) {
                if (data.is_liked) {
                    likeBtn.classList.remove('bg-white', 'text-gray-400');
                    likeBtn.classList.add('bg-blue-500', 'text-white');
                } else {
                    likeBtn.classList.remove('bg-blue-500', 'text-white');
                    likeBtn.classList.add('bg-white', 'text-gray-400');
                }
            }
            // Update likes count
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
