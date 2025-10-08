@extends('layout')

@section('title', 'Home | CelebStyle')

@section('content')
<div class="min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        {{-- Featured Section --}}
        <section class="mb-10">
            <div class="relative rounded-xl overflow-hidden h-64 sm:h-96 bg-gradient-to-r from-primary to-indigo-600">
                <img 
                    src="https://images.unsplash.com/photo-1600603405959-6d623e92445c?ixlib=rb-1.2.1&auto=format&fit=crop&w=1200&q=80" 
                    alt="Featured celebrities collage" 
                    class="w-full h-full object-cover mix-blend-overlay opacity-50"
                />
                <div class="absolute inset-0 flex flex-col justify-center items-center text-white p-6 text-center">
                    <h1 class="font-display text-3xl md:text-5xl font-bold mb-4">Celebrity Style & Lifestyle</h1>
                    <p class="text-lg md:text-xl max-w-2xl">Discover what your favorite celebrities wear, drive, and use in their everyday lives.</p>
                </div>
            </div>
        </section>

        {{-- Categories --}}
        <section class="mb-8">
            <div class="flex flex-wrap items-center justify-between mb-6">
                <h2 class="text-2xl font-display font-bold text-primary">Browse Categories</h2>
                <div class="flex space-x-2 overflow-x-auto py-2 scrollbar-hide">
                    <a href="?category=All" class="whitespace-nowrap px-4 py-2 {{ request('category', 'All') == 'All' ? 'bg-indigo-600 text-white' : 'bg-gray-100 hover:bg-gray-200' }} rounded-full text-sm font-medium">All Celebrities</a>
                    <a href="?category=Fashion" class="whitespace-nowrap px-4 py-2 {{ request('category') == 'Fashion' ? 'bg-indigo-600 text-white' : 'bg-gray-100 hover:bg-gray-200' }} rounded-full text-sm font-medium">Fashion</a>
                    <a href="?category=Beauty" class="whitespace-nowrap px-4 py-2 {{ request('category') == 'Beauty' ? 'bg-indigo-600 text-white' : 'bg-gray-100 hover:bg-gray-200' }} rounded-full text-sm font-medium">Beauty</a>
                    <a href="?category=Fitness" class="whitespace-nowrap px-4 py-2 {{ request('category') == 'Fitness' ? 'bg-indigo-600 text-white' : 'bg-gray-100 hover:bg-gray-200' }} rounded-full text-sm font-medium">Fitness</a>
                    <a href="?category=Tech" class="whitespace-nowrap px-4 py-2 {{ request('category') == 'Tech' ? 'bg-indigo-600 text-white' : 'bg-gray-100 hover:bg-gray-200' }} rounded-full text-sm font-medium">Tech</a>
                </div>
            </div>
        </section>

        {{-- Celebrity Grid --}}
        <section>
            <h2 class="text-2xl font-display font-bold text-primary mb-6">Popular Celebrities</h2>
            @if(isset($celebrities) && count($celebrities))
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($celebrities as $celebrity)
                        <div class="bg-white rounded-xl shadow-md overflow-hidden">
                            <img src="{{ $celebrity->imageUrl }}" alt="{{ $celebrity->name }}" class="h-72 w-full object-cover" />
                            <div class="p-4">
                                <h3 class="text-lg font-bold text-primary mb-2">{{ $celebrity->name }}</h3>
                                <p class="text-gray-500 mb-4">{{ $celebrity->profession }}</p>
                                <div class="flex items-center justify-between">
                                    <div class="flex space-x-1">
                                        @foreach($celebrity->categories as $cat)
                                            <span class="bg-gray-100 text-gray-600 rounded-full px-2 py-1 text-xs">{{ $cat }}</span>
                                        @endforeach
                                    </div>
                                    <span class="text-xs text-gray-400">View</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-10">
                    <p class="text-gray-500">No celebrities found in this category.</p>
                </div>
            @endif
            <div class="mt-8 flex justify-center">
                <button class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-medium">
                    Load More Celebrities
                </button>
            </div>
        </section>
    </div>
</div>
@endsection 