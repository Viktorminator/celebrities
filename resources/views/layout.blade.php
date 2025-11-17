<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Glamdar - Glamour Radar')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    {{-- Header --}}
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex-shrink-0 flex items-center">
                    <a href="/" class="text-2xl font-display font-bold text-primary cursor-pointer">
                        <span class="text-pink-500">Glam</span><span class="text-indigo-600">dar</span>
                    </a>
                </div>
                <div class="hidden md:block">
                    <div class="ml-10 flex items-center space-x-4">
                        <a href="?nav=Popular" class="flex items-center gap-2 px-4 py-2 {{ request('nav', 'Popular') == 'Popular' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} rounded-full text-sm font-medium transition-colors">
                            <i class="fas fa-star"></i>
                            Popular
                        </a>
                        <a href="?nav=Trending" class="flex items-center gap-2 px-4 py-2 {{ request('nav') == 'Trending' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} rounded-full text-sm font-medium transition-colors">
                            <i class="fas fa-fire"></i>
                            Trending
                        </a>
                        <a href="?nav=New" class="flex items-center gap-2 px-4 py-2 {{ request('nav') == 'New' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} rounded-full text-sm font-medium transition-colors">
                            <i class="fas fa-bolt"></i>
                            New
                        </a>
                        <a href="{{ route('categories') }}" class="text-primary hover:text-indigo-600 px-3 py-2 text-sm font-medium">Categories</a>
                        <a href="{{ route('subscriptions') }}" class="text-primary hover:text-indigo-600 px-3 py-2 text-sm font-medium">Subscriptions</a>
                        <a href="{{ route('about') }}" class="text-primary hover:text-indigo-600 px-3 py-2 text-sm font-medium">About</a>
                    </div>
                </div>
                <div class="hidden md:block">
                    <div class="ml-4 flex items-center md:ml-6 space-x-4">
                        @auth
                            <button id="add-style-btn-header" class="bg-pink-500 text-white px-4 py-2 rounded-full text-sm font-medium hover:bg-pink-600 transition-colors flex items-center gap-2">
                                <i class="fas fa-plus"></i>
                                Add Your Style
                            </button>
                            <!-- User Dropdown Menu -->
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="flex items-center space-x-2 text-sm text-gray-700 hover:text-indigo-600 px-3 py-2 focus:outline-none">
                                    <span>{{ Auth::user()->name }}</span>
                                    <i class="fas fa-chevron-down text-xs transition-transform" :class="{'rotate-180': open}"></i>
                                </button>
                                <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50">
                                    <a href="{{ route('styles.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">
                                        <i class="fas fa-images mr-2"></i>My Styles
                                    </a>
                                    <a href="{{ route('favourites.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">
                                        <i class="fas fa-heart mr-2"></i>Favourites
                                    </a>
                                    <a href="{{ route('subscriptions') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">
                                        <i class="fas fa-crown mr-2"></i>Subscription
                                    </a>
                                    <div class="border-t border-gray-200 my-1"></div>
                                    <form action="{{ route('logout') }}" method="POST" class="block">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-red-600">
                                            <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="text-sm text-gray-700 hover:text-indigo-600 px-3 py-2">
                                <i class="fas fa-sign-in-alt mr-1"></i>Login
                            </a>
                            <a href="{{ route('register') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-full text-sm font-medium hover:bg-indigo-700">
                                Sign Up
                            </a>
                        @endauth
                    </div>
                </div>
                <div class="-mr-2 flex md:hidden">
                    <button type="button" id="mobile-menu-button" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-600">
                        <span class="sr-only">Open main menu</span>
                        <i class="fas fa-bars" id="menu-icon"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- Mobile menu --}}
        <div id="mobile-menu" class="md:hidden hidden">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3 bg-white border-t border-gray-200">
                @auth
                    <button id="add-style-btn-mobile" class="w-full bg-pink-500 text-white px-4 py-3 rounded-lg text-base font-medium hover:bg-pink-600 transition-colors flex items-center justify-center gap-2 mb-3 mx-3">
                        <i class="fas fa-plus"></i>
                        Add Your Style
                    </button>
                    <div class="px-3 py-2 border-b border-gray-200 mb-2">
                        <span class="text-sm font-semibold text-gray-900">{{ Auth::user()->name }}</span>
                    </div>
                    <a href="{{ route('styles.index') }}" class="text-primary hover:text-indigo-600 block px-3 py-2 text-base font-medium">
                        <i class="fas fa-images mr-2"></i>My Styles
                    </a>
                    <a href="{{ route('favourites.index') }}" class="text-primary hover:text-indigo-600 block px-3 py-2 text-base font-medium">
                        <i class="fas fa-heart mr-2"></i>Favourites
                    </a>
                    <a href="{{ route('subscriptions') }}" class="text-primary hover:text-indigo-600 block px-3 py-2 text-base font-medium">
                        <i class="fas fa-crown mr-2"></i>Subscription
                    </a>
                    <form action="{{ route('logout') }}" method="POST" class="px-3 py-2">
                        @csrf
                        <button type="submit" class="text-primary hover:text-red-600 block w-full text-left text-base font-medium">
                            <i class="fas fa-sign-out-alt mr-2"></i>Logout
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-primary hover:text-indigo-600 block px-3 py-2 text-base font-medium">
                        <i class="fas fa-sign-in-alt mr-2"></i>Login
                    </a>
                    <a href="{{ route('register') }}" class="text-primary hover:text-indigo-600 block px-3 py-2 text-base font-medium">
                        <i class="fas fa-user-plus mr-2"></i>Sign Up
                    </a>
                @endauth
                <a href="/" class="text-primary hover:text-indigo-600 block px-3 py-2 text-base font-medium">Home</a>
                <a href="#" class="text-primary hover:text-indigo-600 block px-3 py-2 text-base font-medium">Popular</a>
                <a href="#" class="text-primary hover:text-indigo-600 block px-3 py-2 text-base font-medium">Trending</a>
                <a href="#" class="text-primary hover:text-indigo-600 block px-3 py-2 text-base font-medium">New</a>
                <a href="{{ route('categories') }}" class="text-primary hover:text-indigo-600 block px-3 py-2 text-base font-medium">Categories</a>
                <a href="{{ route('subscriptions') }}" class="text-primary hover:text-indigo-600 block px-3 py-2 text-base font-medium">Subscriptions</a>
                <a href="{{ route('about') }}" class="text-primary hover:text-indigo-600 block px-3 py-2 text-base font-medium">About</a>
            </div>
        </div>
    </header>

    <main class="flex-1">
        @yield('content')
    </main>

    @auth
        @include('partials.upload-modal')
    @endauth

    {{-- Footer --}}
    <footer class="bg-primary text-white mt-12 py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-xl font-display font-bold mb-4">
                        <span class="text-pink-500">Glam</span><span class="text-indigo-400">dar</span>
                    </h3>
                    <p class="text-gray-400">Your glamour radar for discovering fashion. Upload photos to find similar styles and shop the looks you love.</p>
                    <div class="flex space-x-4 mt-4">
                        <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-instagram text-xl"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-twitter text-xl"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-facebook text-xl"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-pinterest text-xl"></i></a>
                    </div>
                </div>
                <div>
                    <h4 class="font-semibold text-lg mb-4">Categories</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white">Fashion Bloggers</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Style Inspirations</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Trending Looks</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">User Collections</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Fashion Finds</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold text-lg mb-4">Products</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white">Fashion & Clothing</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Perfumes & Beauty</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Accessories</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Cars & Vehicles</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Home & Lifestyle</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold text-lg mb-4">Stay Updated</h4>
                    <p class="text-gray-400 mb-4">Subscribe to our newsletter for the latest fashion trends and style discoveries.</p>
                    <div class="flex">
                        <input type="email" placeholder="Your email" class="px-4 py-2 bg-gray-800 text-white rounded-l-lg focus:outline-none focus:ring-2 focus:ring-pink-500 w-full" />
                        <button class="bg-pink-500 text-white px-4 py-2 rounded-r-lg font-medium hover:bg-pink-600">Subscribe</button>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-10 pt-6 flex flex-col md:flex-row justify-between items-center">
                <p class="text-gray-500 text-sm">&copy; {{ date('Y') }} Glamdar. All rights reserved.</p>
                <div class="flex space-x-6 mt-4 md:mt-0">
                    <a href="#" class="text-gray-500 hover:text-white text-sm">Privacy Policy</a>
                    <a href="#" class="text-gray-500 hover:text-white text-sm">Terms of Service</a>
                    <a href="#" class="text-gray-500 hover:text-white text-sm">Contact Us</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');
            const menuIcon = document.getElementById('menu-icon');
            const modal = document.getElementById('upload-modal');

            // Handle "Add Your Style" buttons
            const addStyleBtnHeader = document.getElementById('add-style-btn-header');
            const addStyleBtnMobile = document.getElementById('add-style-btn-mobile');

            function openUploadModal() {
                if (modal) {
                    modal.classList.remove('hidden');
                    modal.classList.add('opacity-100');
                    // Close mobile menu if open
                    if (mobileMenu && !mobileMenu.classList.contains('hidden')) {
                        mobileMenu.classList.add('hidden');
                        menuIcon.classList.remove('fa-times');
                        menuIcon.classList.add('fa-bars');
                    }
                }
            }

            if (addStyleBtnHeader) {
                addStyleBtnHeader.addEventListener('click', openUploadModal);
            }

            if (addStyleBtnMobile) {
                addStyleBtnMobile.addEventListener('click', openUploadModal);
            }

            mobileMenuButton.addEventListener('click', function() {
                // Toggle mobile menu visibility
                mobileMenu.classList.toggle('hidden');

                // Toggle icon between hamburger and X
                if (mobileMenu.classList.contains('hidden')) {
                    menuIcon.classList.remove('fa-times');
                    menuIcon.classList.add('fa-bars');
                } else {
                    menuIcon.classList.remove('fa-bars');
                    menuIcon.classList.add('fa-times');
                }
            });

            // Close mobile menu when clicking outside
            document.addEventListener('click', function(event) {
                if (!mobileMenuButton.contains(event.target) && !mobileMenu.contains(event.target)) {
                    mobileMenu.classList.add('hidden');
                    menuIcon.classList.remove('fa-times');
                    menuIcon.classList.add('fa-bars');
                }
            });

            // Close mobile menu when window is resized to desktop size
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 768) { // md breakpoint
                    mobileMenu.classList.add('hidden');
                    menuIcon.classList.remove('fa-times');
                    menuIcon.classList.add('fa-bars');
                }
            });
        });
    </script>
</body>
</html>
