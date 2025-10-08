@extends('layout')

@section('title', $celebrity->name . ' | CelebStyle')

@section('content')
<div class="min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        {{-- Back Button --}}
        <a href="/" class="flex items-center text-sm text-indigo-600 mb-4 hover:underline">
            <i class="fas fa-arrow-left mr-2"></i> Back to all celebrities
        </a>

        {{-- Celebrity Header --}}
        <section class="mb-8">
            <div class="relative rounded-xl overflow-hidden">
                <div class="relative h-48 md:h-80 bg-gradient-to-r from-primary to-indigo-600">
                    <img 
                        src="{{ $celebrity->bannerUrl }}?ixlib=rb-1.2.1&auto=format&fit=crop&w=1500&q=80"
                        alt="{{ $celebrity->name }} banner" 
                        class="w-full h-full object-cover mix-blend-overlay opacity-50" 
                    />
                </div>
                <div class="absolute bottom-0 translate-y-1/2 left-8">
                    <div class="h-24 w-24 md:h-40 md:w-40 rounded-full border-4 border-white overflow-hidden bg-white">
                        <img 
                            src="{{ $celebrity->imageUrl }}?ixlib=rb-1.2.1&auto=format&fit=crop&w=600&q=80"
                            alt="{{ $celebrity->name }}" 
                            class="h-full w-full object-cover" 
                        />
                    </div>
                </div>
            </div>
            <div class="mt-16 md:mt-24 md:ml-44 md:flex md:items-center md:justify-between">
                <div>
                    <h1 class="text-3xl font-display font-bold text-primary">{{ $celebrity->name }}</h1>
                    <p class="text-gray-600">{{ $celebrity->profession }}</p>
                </div>
                <div class="mt-4 md:mt-0 flex space-x-4">
                    <button class="flex items-center space-x-2 px-4 py-2 bg-indigo-600 text-white rounded-full text-sm font-medium">
                        <i class="fas fa-heart"></i>
                        <span>Follow</span>
                    </button>
                    <button class="flex items-center space-x-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-full text-sm font-medium">
                        <i class="fas fa-share-alt"></i>
                        <span>Share</span>
                    </button>
                </div>
            </div>
        </section>

        {{-- Biography Section --}}
        <section class="mb-12 max-w-3xl">
            <h2 class="text-2xl font-display font-semibold text-primary mb-4">About</h2>
            <p class="text-gray-700 leading-relaxed">
                {{ $celebrity->bio }}
            </p>
        </section>

        {{-- Product Categories Tabs --}}
        <section class="mb-8">
            <div class="border-b border-gray-200">
                <div class="flex space-x-8 mb-4">
                    <a href="?category=all" class="pb-2 px-1 border-b-2 {{ request('category', 'all') == 'all' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-indigo-600 hover:border-indigo-600' }} font-medium">All Products</a>
                    @foreach($productCategories as $cat)
                        <a href="?category={{ $cat->id }}" class="pb-2 px-1 border-b-2 {{ request('category') == $cat->id ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-indigo-600 hover:border-indigo-600' }} font-medium">{{ $cat->name }}</a>
                    @endforeach
                </div>
            </div>
            {{-- Products Grid --}}
            <div class="mt-8">
                <h3 class="text-xl font-display font-semibold text-primary mb-4">
                    {{ $currentCategoryName ?? 'All Products' }}
                </h3>
                @if(isset($products) && count($products))
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($products as $product)
                            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                                <img src="{{ $product->imageUrl }}" alt="{{ $product->name }}" class="h-80 w-full object-cover" />
                                <div class="p-4">
                                    <h4 class="text-lg font-bold text-primary mb-2">{{ $product->name }}</h4>
                                    <p class="text-gray-500 mb-4">{{ $product->description }}</p>
                                    <div class="flex justify-between items-center">
                                        <span class="bg-gray-100 text-gray-600 rounded-full px-2 py-1 text-xs">{{ $product->category->name ?? '' }}</span>
                                        <span class="text-xs text-gray-400">${{ $product->price }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-10 bg-gray-50 rounded-lg">
                        <p class="text-gray-500">No products found in this category.</p>
                    </div>
                @endif
            </div>
        </section>

        {{-- Recent Events Section --}}
        <section class="mb-12">
            <h2 class="text-2xl font-display font-semibold text-primary mb-6">Recent Appearances</h2>
            @if(isset($events) && count($events))
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($events as $event)
                        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                            <img src="{{ $event->imageUrl }}" alt="{{ $event->title }}" class="h-44 w-full object-cover" />
                            <div class="p-4">
                                <div class="flex justify-between">
                                    <div>
                                        <h4 class="text-lg font-bold text-primary mb-1">{{ $event->title }}</h4>
                                        <p class="text-gray-500 text-sm">{{ $event->date }}</p>
                                    </div>
                                    <span class="bg-gray-100 text-gray-600 rounded-full px-2 py-1 text-xs">{{ $event->type }}</span>
                                </div>
                                <p class="text-gray-700 mt-3">{{ $event->description }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-10 bg-gray-50 rounded-lg">
                    <p class="text-gray-500">No recent appearances found.</p>
                </div>
            @endif
        </section>
    </div>
</div>
@endsection 