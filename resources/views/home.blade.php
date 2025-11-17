@extends('layout')

@section('title', 'Home | Glamdar')

@section('content')
    <div class="min-h-screen bg-gray-50">
        {{-- Navigation Menu Container --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            {{-- Categories --}}
            <section class="mb-12">
                <div class="flex flex-wrap items-center justify-between mb-6">
                    <h2 class="text-2xl font-display font-bold text-indigo-900">Browse Categories</h2>
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
            @if(isset($styles) && $styles->count() > 0)
            <section class="mb-12">
                <h2 class="text-2xl font-display font-bold text-indigo-900 mb-6">Latest Styles</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($styles as $style)
                        <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-200 relative group">
                            <!-- Favourite Button -->
                            <button onclick="toggleStyleFavourite({{ $style->id }})" 
                                    class="absolute top-3 right-3 z-10 p-2 {{ $style->is_favourited ? 'bg-red-500 text-white' : 'bg-white text-gray-400' }} rounded-full shadow-md hover:bg-red-500 hover:text-white transition-colors style-fav-btn-{{ $style->id }}"
                                    title="Add to Favourites">
                                <i class="fas fa-heart text-sm"></i>
                            </button>
                            
                            <a href="{{ route('analysis-results', ['id' => $style->id]) }}" class="block">
                                <img src="{{ $style->image_url }}" alt="Style" class="h-64 w-full object-cover" loading="lazy" />
                                <div class="p-4">
                                    @if($style->analysis_metadata && isset($style->analysis_metadata['user_tags']) && count($style->analysis_metadata['user_tags']) > 0)
                                        <div class="flex flex-wrap gap-1 mb-2">
                                            @foreach(array_slice($style->analysis_metadata['user_tags'], 0, 3) as $tag)
                                                <span class="bg-indigo-100 text-indigo-600 rounded-full px-2 py-1 text-xs font-medium">{{ $tag }}</span>
                                            @endforeach
                                            @if(count($style->analysis_metadata['user_tags']) > 3)
                                                <span class="text-gray-400 text-xs">+{{ count($style->analysis_metadata['user_tags']) - 3 }}</span>
                                            @endif
                                        </div>
                                    @endif
                                    @if($style->user)
                                        <p class="text-sm text-gray-500 mb-2">By {{ $style->user->name }}</p>
                                    @endif
                                    <div class="flex items-center justify-between text-xs text-gray-500">
                                        <div class="flex items-center gap-3">
                                            <span class="flex items-center gap-1">
                                                <i class="fas fa-heart text-red-500"></i>
                                                <span id="style-fav-count-{{ $style->id }}">{{ $style->style_favourites_count ?? 0 }}</span>
                                            </span>
                                            <span class="flex items-center gap-1">
                                                <i class="fas fa-thumbs-up text-blue-500"></i>
                                                <span>{{ $style->likes_count ?? 0 }}</span>
                                            </span>
                                        </div>
                                        <span>{{ $style->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </section>
            @endif

            {{-- Style Inspiration Grid --}}
            <section>
                <h2 class="text-2xl font-display font-bold text-indigo-900 mb-6">Fashion Inspiration</h2>
                @if(isset($celebrities) && count($celebrities))
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        @foreach($celebrities as $celebrity)
                            <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-200">
                                <img src="{{ asset('storage/' . $celebrity->image_url) }}" alt="{{ $celebrity->name }}" class="h-72 w-full object-cover" loading="lazy" />
                                <div class="p-4">
                                    <h3 class="text-lg font-bold text-indigo-900 mb-2">{{ $celebrity->name }}</h3>
                                    <p class="text-gray-500 mb-4">{{ $celebrity->profession }}</p>
                                    <div class="flex items-center justify-between">
                                        <div class="flex space-x-1 flex-wrap gap-1">
                                            @foreach($celebrity->categories as $cat)
                                                <span class="bg-indigo-100 text-indigo-600 rounded-full px-2 py-1 text-xs font-medium">{{ $cat }}</span>
                                            @endforeach
                                        </div>
                                        @if(Route::has('celebrity.show'))
                                            <a href="{{ route('celebrity.show', $celebrity->id) }}" class="text-xs text-indigo-600 hover:underline font-medium">View Profile</a>
                                        @else
                                            <span class="text-xs text-gray-400">View</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-10">
                        <p class="text-gray-500 text-lg">No styles found in this category.</p>
                    </div>
                @endif
                <div class="mt-8 flex justify-center">
                    <button class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium">
                        Load More Styles
                    </button>
                </div>
            </section>
        </div>
    </div>

<script>
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
</script>

@endsection
