@extends('layout')

@section('title', 'About - Glamdar')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl sm:text-5xl font-display font-bold text-indigo-900 mb-4">
                About <span class="text-pink-500">Glam</span><span class="text-indigo-600">dar</span>
            </h1>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                Your Glamour Radar for discovering and sharing fashion styles
            </p>
        </div>

        <!-- Main Content -->
        <div class="bg-white rounded-2xl shadow-lg p-8 sm:p-12 mb-8">
            <div class="prose prose-lg max-w-none">
                <h2 class="text-2xl font-bold text-indigo-900 mb-6">What is Glamdar?</h2>
                <p class="text-gray-700 mb-6 leading-relaxed">
                    <strong>Glamdar</strong> (Glamour Radar) is a platform designed for fashion enthusiasts, bloggers, customers, and style lovers to <strong>share styles and links to clothes</strong>. Our mission is to make fashion discovery easy and accessible for everyone.
                </p>

                <h2 class="text-2xl font-bold text-indigo-900 mb-6 mt-10">How It Works</h2>
                <div class="space-y-6 mb-8">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center">
                            <span class="text-indigo-600 font-bold text-lg">1</span>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">Upload Your Photos</h3>
                            <p class="text-gray-700 leading-relaxed">
                                Upload photos of fashion looks, outfits, or style inspirations. Our AI-powered system automatically detects clothing items and fashion elements in your images.
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-pink-100 rounded-full flex items-center justify-center">
                            <span class="text-pink-600 font-bold text-lg">2</span>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">Share Product Links</h3>
                            <p class="text-gray-700 leading-relaxed">
                                Add links to where others can find similar items. Whether it's from your favorite online store, a blog post, or a specific product page, you can share direct links to help others discover the same fashion pieces.
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center">
                            <span class="text-indigo-600 font-bold text-lg">3</span>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">Discover & Shop</h3>
                            <p class="text-gray-700 leading-relaxed">
                                Browse through style collections shared by the community. Find inspiration, discover new brands, and shop the looks you love with direct links to products.
                            </p>
                        </div>
                    </div>
                </div>

                <h2 class="text-2xl font-bold text-indigo-900 mb-6 mt-10">Why Glamdar?</h2>
                <div class="grid md:grid-cols-2 gap-6 mb-8">
                    <div class="bg-indigo-50 rounded-xl p-6">
                        <div class="flex items-center mb-3">
                            <i class="fas fa-users text-indigo-600 text-2xl mr-3"></i>
                            <h3 class="text-xl font-semibold text-gray-900">Community-Driven</h3>
                        </div>
                        <p class="text-gray-700">
                            Built by fashion lovers, for fashion lovers. Share your style discoveries and help others find their perfect look.
                        </p>
                    </div>

                    <div class="bg-pink-50 rounded-xl p-6">
                        <div class="flex items-center mb-3">
                            <i class="fas fa-link text-pink-600 text-2xl mr-3"></i>
                            <h3 class="text-xl font-semibold text-gray-900">Direct Links</h3>
                        </div>
                        <p class="text-gray-700">
                            No more searching endlessly. Get direct links to products and stores where you can find the exact items you're looking for.
                        </p>
                    </div>

                    <div class="bg-indigo-50 rounded-xl p-6">
                        <div class="flex items-center mb-3">
                            <i class="fas fa-magic text-indigo-600 text-2xl mr-3"></i>
                            <h3 class="text-xl font-semibold text-gray-900">AI-Powered Detection</h3>
                        </div>
                        <p class="text-gray-700">
                            Our advanced image recognition automatically identifies clothing items, making it easy to catalog and share your fashion finds.
                        </p>
                    </div>

                    <div class="bg-pink-50 rounded-xl p-6">
                        <div class="flex items-center mb-3">
                            <i class="fas fa-share-alt text-pink-600 text-2xl mr-3"></i>
                            <h3 class="text-xl font-semibold text-gray-900">Easy Sharing</h3>
                        </div>
                        <p class="text-gray-700">
                            Share your style analyses with friends, followers, or the community. Every look can be shared with a simple link.
                        </p>
                    </div>
                </div>

                <h2 class="text-2xl font-bold text-indigo-900 mb-6 mt-10">Perfect For</h2>
                <div class="grid md:grid-cols-3 gap-4 mb-8">
                    <div class="text-center p-4 bg-gray-50 rounded-lg">
                        <i class="fas fa-blog text-3xl text-indigo-600 mb-3"></i>
                        <h3 class="font-semibold text-gray-900 mb-2">Fashion Bloggers</h3>
                        <p class="text-sm text-gray-600">Share your outfit posts with direct product links</p>
                    </div>
                    <div class="text-center p-4 bg-gray-50 rounded-lg">
                        <i class="fas fa-shopping-bag text-3xl text-pink-600 mb-3"></i>
                        <h3 class="font-semibold text-gray-900 mb-2">Style Enthusiasts</h3>
                        <p class="text-sm text-gray-600">Discover and save your favorite fashion finds</p>
                    </div>
                    <div class="text-center p-4 bg-gray-50 rounded-lg">
                        <i class="fas fa-heart text-3xl text-indigo-600 mb-3"></i>
                        <h3 class="font-semibold text-gray-900 mb-2">Fashion Lovers</h3>
                        <p class="text-sm text-gray-600">Build your personal style collection</p>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-indigo-600 to-pink-600 rounded-xl p-8 text-white text-center mt-10">
                    <h2 class="text-3xl font-bold mb-4">Ready to Start Sharing?</h2>
                    <p class="text-lg mb-6 opacity-90">Join our community and start sharing your fashion discoveries today!</p>
                    @auth
                        <a href="{{ route('home') }}" class="inline-block bg-white text-indigo-600 px-8 py-3 rounded-full font-semibold hover:bg-gray-100 transition-colors">
                            <i class="fas fa-camera mr-2"></i>Upload Your First Photo
                        </a>
                    @else
                        <div class="flex flex-col sm:flex-row gap-4 justify-center">
                            <a href="{{ route('register') }}" class="inline-block bg-white text-indigo-600 px-8 py-3 rounded-full font-semibold hover:bg-gray-100 transition-colors">
                                <i class="fas fa-user-plus mr-2"></i>Sign Up Free
                            </a>
                            <a href="{{ route('login') }}" class="inline-block bg-transparent border-2 border-white text-white px-8 py-3 rounded-full font-semibold hover:bg-white hover:text-indigo-600 transition-colors">
                                <i class="fas fa-sign-in-alt mr-2"></i>Login
                            </a>
                        </div>
                    @endauth
                </div>
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

