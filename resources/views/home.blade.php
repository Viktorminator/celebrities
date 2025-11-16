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
                        <a href="?category=Fashion" class="whitespace-nowrap px-4 py-2 {{ request('category') == 'Fashion' ? 'bg-indigo-600 text-white' : 'bg-gray-100 hover:bg-gray-200' }} rounded-full text-sm font-medium transition-colors">Fashion</a>
                        <a href="?category=Beauty" class="whitespace-nowrap px-4 py-2 {{ request('category') == 'Beauty' ? 'bg-indigo-600 text-white' : 'bg-gray-100 hover:bg-gray-200' }} rounded-full text-sm font-medium transition-colors">Beauty</a>
                        <a href="?category=Fitness" class="whitespace-nowrap px-4 py-2 {{ request('category') == 'Fitness' ? 'bg-indigo-600 text-white' : 'bg-gray-100 hover:bg-gray-200' }} rounded-full text-sm font-medium transition-colors">Fitness</a>
                        <a href="?category=Tech" class="whitespace-nowrap px-4 py-2 {{ request('category') == 'Tech' ? 'bg-indigo-600 text-white' : 'bg-gray-100 hover:bg-gray-200' }} rounded-full text-sm font-medium transition-colors">Tech</a>
                    </div>
                </div>
            </section>

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

@endsection
