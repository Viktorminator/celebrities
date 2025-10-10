<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CelebStyle')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    {{-- Header --}}
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex-shrink-0 flex items-center">
                    <a href="/" class="text-2xl font-display font-bold text-primary cursor-pointer">
                        <span class="text-pink-500">Celeb</span>Style
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
                        <a href="#" class="text-primary hover:text-indigo-600 px-3 py-2 text-sm font-medium">Categories</a>
                        <a href="#" class="text-primary hover:text-indigo-600 px-3 py-2 text-sm font-medium">About</a>
                    </div>
                </div>
                <div class="hidden md:block">
                    <div class="ml-4 flex items-center md:ml-6">
                        <div class="relative">
                            <input type="text" placeholder="Search celebrities..." class="w-64 bg-gray-100 py-2 pl-10 pr-4 rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-indigo-600" />
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                        </div>
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
                <div class="px-3 py-2 relative">
                    <input type="text" placeholder="Search celebrities..." class="w-full bg-gray-100 py-2 pl-10 pr-4 rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-indigo-600" />
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                </div>
                <a href="/" class="text-primary hover:text-indigo-600 block px-3 py-2 text-base font-medium">Home</a>
                <a href="#" class="text-primary hover:text-indigo-600 block px-3 py-2 text-base font-medium">Trending</a>
                <a href="#" class="text-primary hover:text-indigo-600 block px-3 py-2 text-base font-medium">New Releases</a>
                <a href="#" class="text-primary hover:text-indigo-600 block px-3 py-2 text-base font-medium">Categories</a>
                <a href="#" class="text-primary hover:text-indigo-600 block px-3 py-2 text-base font-medium">About</a>
            </div>
        </div>
    </header>

    <main class="flex-1">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-primary text-white mt-12 py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-xl font-display font-bold mb-4">
                        <span class="text-pink-500">Celeb</span>Style
                    </h3>
                    <p class="text-gray-400">Discover celebrity style and shop their fashion, accessories, and lifestyle products.</p>
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
                        <li><a href="#" class="text-gray-400 hover:text-white">Actors & Actresses</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Musicians</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Athletes</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Influencers</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Fashion Icons</a></li>
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
                    <p class="text-gray-400 mb-4">Subscribe to our newsletter for the latest celebrity style trends.</p>
                    <div class="flex">
                        <input type="email" placeholder="Your email" class="px-4 py-2 bg-gray-800 text-white rounded-l-lg focus:outline-none focus:ring-2 focus:ring-pink-500 w-full" />
                        <button class="bg-pink-500 text-white px-4 py-2 rounded-r-lg font-medium hover:bg-pink-600">Subscribe</button>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-10 pt-6 flex flex-col md:flex-row justify-between items-center">
                <p class="text-gray-500 text-sm">&copy; {{ date('Y') }} CelebStyle. All rights reserved.</p>
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
