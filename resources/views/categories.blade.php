@extends('layout')

@section('title', 'Style Categories - Glamdar')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl sm:text-5xl font-display font-bold text-indigo-900 mb-4">
                Style Categories
            </h1>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                Explore fashion styles and discover looks that match your personal style
            </p>
        </div>

        <!-- Categories Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-12">
            <!-- Casual Style -->
            <a href="{{ route('home', ['category' => 'Casual']) }}" class="group bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="relative h-48 bg-gradient-to-br from-blue-400 to-blue-600">
                    <div class="absolute inset-0 flex items-center justify-center">
                        <i class="fas fa-tshirt text-6xl text-white opacity-80"></i>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-indigo-600 transition-colors">Casual</h3>
                    <p class="text-gray-600 text-sm">Everyday comfortable and relaxed styles</p>
                    <div class="mt-4 flex items-center text-indigo-600 text-sm font-medium">
                        Browse styles <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                    </div>
                </div>
            </a>

            <!-- Formal Style -->
            <a href="{{ route('home', ['category' => 'Formal']) }}" class="group bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="relative h-48 bg-gradient-to-br from-gray-700 to-gray-900">
                    <div class="absolute inset-0 flex items-center justify-center">
                        <i class="fas fa-user-tie text-6xl text-white opacity-80"></i>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-indigo-600 transition-colors">Formal</h3>
                    <p class="text-gray-600 text-sm">Elegant and sophisticated looks for special occasions</p>
                    <div class="mt-4 flex items-center text-indigo-600 text-sm font-medium">
                        Browse styles <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                    </div>
                </div>
            </a>

            <!-- Streetwear -->
            <a href="{{ route('home', ['category' => 'Streetwear']) }}" class="group bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="relative h-48 bg-gradient-to-br from-purple-500 to-pink-600">
                    <div class="absolute inset-0 flex items-center justify-center">
                        <i class="fas fa-headphones text-6xl text-white opacity-80"></i>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-indigo-600 transition-colors">Streetwear</h3>
                    <p class="text-gray-600 text-sm">Urban and trendy fashion from the streets</p>
                    <div class="mt-4 flex items-center text-indigo-600 text-sm font-medium">
                        Browse styles <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                    </div>
                </div>
            </a>

            <!-- Vintage -->
            <a href="{{ route('home', ['category' => 'Vintage']) }}" class="group bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="relative h-48 bg-gradient-to-br from-amber-400 to-orange-600">
                    <div class="absolute inset-0 flex items-center justify-center">
                        <i class="fas fa-clock text-6xl text-white opacity-80"></i>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-indigo-600 transition-colors">Vintage</h3>
                    <p class="text-gray-600 text-sm">Classic and retro styles from past decades</p>
                    <div class="mt-4 flex items-center text-indigo-600 text-sm font-medium">
                        Browse styles <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                    </div>
                </div>
            </a>

            <!-- Bohemian -->
            <a href="{{ route('home', ['category' => 'Bohemian']) }}" class="group bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="relative h-48 bg-gradient-to-br from-green-400 to-teal-600">
                    <div class="absolute inset-0 flex items-center justify-center">
                        <i class="fas fa-leaf text-6xl text-white opacity-80"></i>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-indigo-600 transition-colors">Bohemian</h3>
                    <p class="text-gray-600 text-sm">Free-spirited and artistic fashion styles</p>
                    <div class="mt-4 flex items-center text-indigo-600 text-sm font-medium">
                        Browse styles <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                    </div>
                </div>
            </a>

            <!-- Minimalist -->
            <a href="{{ route('home', ['category' => 'Minimalist']) }}" class="group bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="relative h-48 bg-gradient-to-br from-gray-300 to-gray-500">
                    <div class="absolute inset-0 flex items-center justify-center">
                        <i class="fas fa-minus text-6xl text-white opacity-80"></i>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-indigo-600 transition-colors">Minimalist</h3>
                    <p class="text-gray-600 text-sm">Clean, simple, and timeless designs</p>
                    <div class="mt-4 flex items-center text-indigo-600 text-sm font-medium">
                        Browse styles <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                    </div>
                </div>
            </a>

            <!-- Sporty -->
            <a href="{{ route('home', ['category' => 'Sporty']) }}" class="group bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="relative h-48 bg-gradient-to-br from-red-500 to-orange-600">
                    <div class="absolute inset-0 flex items-center justify-center">
                        <i class="fas fa-running text-6xl text-white opacity-80"></i>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-indigo-600 transition-colors">Sporty</h3>
                    <p class="text-gray-600 text-sm">Athletic and active lifestyle fashion</p>
                    <div class="mt-4 flex items-center text-indigo-600 text-sm font-medium">
                        Browse styles <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                    </div>
                </div>
            </a>

            <!-- Elegant -->
            <a href="{{ route('home', ['category' => 'Elegant']) }}" class="group bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="relative h-48 bg-gradient-to-br from-indigo-600 to-purple-700">
                    <div class="absolute inset-0 flex items-center justify-center">
                        <i class="fas fa-gem text-6xl text-white opacity-80"></i>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-indigo-600 transition-colors">Elegant</h3>
                    <p class="text-gray-600 text-sm">Refined and polished sophisticated looks</p>
                    <div class="mt-4 flex items-center text-indigo-600 text-sm font-medium">
                        Browse styles <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                    </div>
                </div>
            </a>

            <!-- Romantic -->
            <a href="{{ route('home', ['category' => 'Romantic']) }}" class="group bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="relative h-48 bg-gradient-to-br from-pink-400 to-rose-600">
                    <div class="absolute inset-0 flex items-center justify-center">
                        <i class="fas fa-heart text-6xl text-white opacity-80"></i>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-indigo-600 transition-colors">Romantic</h3>
                    <p class="text-gray-600 text-sm">Feminine and delicate romantic styles</p>
                    <div class="mt-4 flex items-center text-indigo-600 text-sm font-medium">
                        Browse styles <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                    </div>
                </div>
            </a>

            <!-- Edgy -->
            <a href="{{ route('home', ['category' => 'Edgy']) }}" class="group bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="relative h-48 bg-gradient-to-br from-gray-800 to-black">
                    <div class="absolute inset-0 flex items-center justify-center">
                        <i class="fas fa-skull text-6xl text-white opacity-80"></i>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-indigo-600 transition-colors">Edgy</h3>
                    <p class="text-gray-600 text-sm">Bold and daring fashion statements</p>
                    <div class="mt-4 flex items-center text-indigo-600 text-sm font-medium">
                        Browse styles <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                    </div>
                </div>
            </a>

            <!-- Preppy -->
            <a href="{{ route('home', ['category' => 'Preppy']) }}" class="group bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="relative h-48 bg-gradient-to-br from-blue-500 to-cyan-600">
                    <div class="absolute inset-0 flex items-center justify-center">
                        <i class="fas fa-graduation-cap text-6xl text-white opacity-80"></i>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-indigo-600 transition-colors">Preppy</h3>
                    <p class="text-gray-600 text-sm">Classic and polished collegiate style</p>
                    <div class="mt-4 flex items-center text-indigo-600 text-sm font-medium">
                        Browse styles <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                    </div>
                </div>
            </a>

            <!-- Chic -->
            <a href="{{ route('home', ['category' => 'Chic']) }}" class="group bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="relative h-48 bg-gradient-to-br from-rose-500 to-pink-600">
                    <div class="absolute inset-0 flex items-center justify-center">
                        <i class="fas fa-star text-6xl text-white opacity-80"></i>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-indigo-600 transition-colors">Chic</h3>
                    <p class="text-gray-600 text-sm">Stylish and fashionable contemporary looks</p>
                    <div class="mt-4 flex items-center text-indigo-600 text-sm font-medium">
                        Browse styles <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                    </div>
                </div>
            </a>

            <!-- Beach -->
            <a href="{{ route('home', ['category' => 'Beach']) }}" class="group bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="relative h-48 bg-gradient-to-br from-cyan-400 to-blue-500">
                    <div class="absolute inset-0 flex items-center justify-center">
                        <i class="fas fa-umbrella-beach text-6xl text-white opacity-80"></i>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-indigo-600 transition-colors">Beach</h3>
                    <p class="text-gray-600 text-sm">Relaxed and breezy vacation styles</p>
                    <div class="mt-4 flex items-center text-indigo-600 text-sm font-medium">
                        Browse styles <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- Quick Links Section -->
        <div class="bg-white rounded-2xl shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-bold text-indigo-900 mb-6">Browse by Item Type</h2>
            <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-6 gap-4">
                <a href="{{ route('home', ['type' => 'tops']) }}" class="text-center p-4 bg-gray-50 rounded-lg hover:bg-indigo-50 transition-colors group">
                    <i class="fas fa-tshirt text-3xl text-indigo-600 mb-2 group-hover:scale-110 transition-transform"></i>
                    <p class="text-sm font-medium text-gray-700">Tops</p>
                </a>
                <a href="{{ route('home', ['type' => 'bottoms']) }}" class="text-center p-4 bg-gray-50 rounded-lg hover:bg-indigo-50 transition-colors group">
                    <i class="fas fa-socks text-3xl text-indigo-600 mb-2 group-hover:scale-110 transition-transform"></i>
                    <p class="text-sm font-medium text-gray-700">Bottoms</p>
                </a>
                <a href="{{ route('home', ['type' => 'dresses']) }}" class="text-center p-4 bg-gray-50 rounded-lg hover:bg-indigo-50 transition-colors group">
                    <i class="fas fa-vest text-3xl text-indigo-600 mb-2 group-hover:scale-110 transition-transform"></i>
                    <p class="text-sm font-medium text-gray-700">Dresses</p>
                </a>
                <a href="{{ route('home', ['type' => 'outerwear']) }}" class="text-center p-4 bg-gray-50 rounded-lg hover:bg-indigo-50 transition-colors group">
                    <i class="fas fa-tshirt text-3xl text-indigo-600 mb-2 group-hover:scale-110 transition-transform"></i>
                    <p class="text-sm font-medium text-gray-700">Outerwear</p>
                </a>
                <a href="{{ route('home', ['type' => 'footwear']) }}" class="text-center p-4 bg-gray-50 rounded-lg hover:bg-indigo-50 transition-colors group">
                    <i class="fas fa-shoe-prints text-3xl text-indigo-600 mb-2 group-hover:scale-110 transition-transform"></i>
                    <p class="text-sm font-medium text-gray-700">Footwear</p>
                </a>
                <a href="{{ route('home', ['type' => 'accessories']) }}" class="text-center p-4 bg-gray-50 rounded-lg hover:bg-indigo-50 transition-colors group">
                    <i class="fas fa-gem text-3xl text-indigo-600 mb-2 group-hover:scale-110 transition-transform"></i>
                    <p class="text-sm font-medium text-gray-700">Accessories</p>
                </a>
            </div>
        </div>

        <!-- Back to Home -->
        <div class="text-center">
            <a href="{{ route('home') }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Back to Home
            </a>
        </div>
    </div>
</div>
@endsection

