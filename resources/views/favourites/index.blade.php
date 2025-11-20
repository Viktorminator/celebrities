@extends('layout')

@section('title', 'My Favourites - Glamdar')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-display font-bold text-indigo-900 mb-2">My Favourites</h1>
            <p class="text-gray-600">Styles you've saved for later</p>
        </div>

        @if($favourites->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($favourites as $favourite)
                    @php
                        $style = $favourite->photoAnalysis;
                    @endphp
                    @if($style)
                        <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-200 relative group">
                            <!-- Favourite Button -->
                            <button onclick="toggleStyleFavourite({{ $style->id }})" 
                                    class="absolute top-3 right-3 z-10 p-2 bg-red-500 text-white rounded-full shadow-md hover:bg-red-600 transition-colors style-fav-btn-{{ $style->id }}"
                                    title="Remove from Favourites">
                                <i class="fas fa-heart text-sm"></i>
                            </button>
                            
                            @php
                                $primaryImage = $style->getAllImages()[0] ?? null;
                            @endphp
                            <a href="{{ route('style.view', $style->id) }}" class="block">
                                @if($primaryImage)
                                    <img src="{{ $primaryImage['url'] }}" alt="Style" class="h-64 w-full object-cover" loading="lazy" />
                                @else
                                    <div class="h-64 w-full bg-gray-100 flex items-center justify-center text-gray-400 text-sm">No image available</div>
                                @endif
                                <div class="p-4">
                                    @if($style->styleTags && $style->styleTags->count() > 0)
                                        <div class="flex flex-wrap gap-1 mb-2">
                                            @foreach($style->styleTags->take(3) as $styleTag)
                                                <span class="bg-indigo-100 text-indigo-600 rounded-full px-2 py-1 text-xs font-medium">{{ $styleTag->tag }}</span>
                                            @endforeach
                                            @if($style->styleTags->count() > 3)
                                                <span class="text-gray-400 text-xs">+{{ $style->styleTags->count() - 3 }}</span>
                                            @endif
                                        </div>
                                    @endif
                                    @if($style->user)
                                        <p class="text-sm text-gray-500 mb-2">By {{ $style->user->name }}</p>
                                    @endif
                                    @if($style->analysis_metadata && isset($style->analysis_metadata['description']))
                                        <p class="text-sm text-gray-700 mb-2 line-clamp-2">{{ $style->analysis_metadata['description'] }}</p>
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
                    @endif
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $favourites->links() }}
            </div>
        @else
            <div class="bg-white rounded-xl shadow-md p-12 text-center">
                <i class="fas fa-heart text-gray-300 text-6xl mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">No Favourites Yet</h3>
                <p class="text-gray-500 mb-6">Start exploring styles and add them to your favourites!</p>
                <a href="{{ route('home') }}" class="inline-block bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition-colors">
                    Browse Styles
                </a>
            </div>
        @endif
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
                    // Remove the card from the page
                    const card = favBtn.closest('.bg-white.rounded-xl');
                    if (card) {
                        card.style.transition = 'opacity 0.3s';
                        card.style.opacity = '0';
                        setTimeout(() => {
                            card.remove();
                            // Check if we need to show the empty state
                            const grid = document.querySelector('.grid');
                            if (grid && grid.children.length === 0) {
                                location.reload();
                            }
                        }, 300);
                    }
                }
            }
            // Update favourites count
            const favCount = document.getElementById(`style-fav-count-${photoAnalysisId}`);
            if (favCount) {
                favCount.textContent = data.favourites_count;
            }
        }
    } catch (error) {
        console.error('Error toggling style favourite:', error);
    }
}
</script>
@endsection

