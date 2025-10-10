@extends('layout')

@section('title', 'Home | CelebStyle')

@section('content')
    <div class="min-h-screen bg-gray-50">
        {{-- Navigation Menu Container --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            {{-- Featured Section --}}
            <section class="mb-12">
                <div class="relative rounded-xl overflow-hidden h-64 sm:h-80 md:h-96 bg-gradient-to-r from-indigo-500 to-indigo-700">
                    <img
                        src="https://images.unsplash.com/photo-1600603405959-6d623e92445c?ixlib=rb-1.2.1&auto=format&fit=crop&w=1200&q=80"
                        alt="Featured celebrities collage"
                        class="w-full h-full object-cover mix-blend-overlay opacity-50"
                        loading="lazy"
                    />
                    <div class="absolute inset-0 flex flex-col justify-center items-center text-white p-6 text-center">
                        <h1 class="font-display text-3xl sm:text-4xl md:text-5xl font-bold mb-4 tracking-tight">Discover Celebrity Style</h1>
                        <p class="text-base sm:text-lg md:text-xl max-w-2xl mb-6">Explore what your favorite celebrities wear, drive, and use in their daily lives.</p>

                        {{-- Upload Photo Button --}}
                        <div class="flex flex-col sm:flex-row gap-4 items-center">
                            <button
                                id="upload-photo-btn"
                                class="bg-white text-indigo-600 px-6 sm:px-8 py-3 rounded-full font-semibold hover:bg-gray-100 transition-colors duration-200 flex items-center gap-2 shadow-md"
                            >
                                <i class="fas fa-camera text-lg"></i>
                                Upload Photo
                            </button>
                            <p class="text-sm sm:text-base opacity-90">Upload a celebrity photo to find similar clothes</p>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Upload Modal --}}
            <div id="upload-modal" class="fixed inset-0 bg-black bg-opacity-60 hidden z-[1000] flex items-center justify-center p-4 sm:p-6 transition-opacity duration-300">
                <div class="bg-white rounded-2xl max-w-md w-full max-h-[90vh] overflow-hidden shadow-2xl transform transition-all duration-300 flex flex-col">
                    <!-- Header -->
                    <div class="flex justify-between items-center p-6">
                        <h2 class="text-xl font-bold text-gray-900">Upload Celebrity Photo</h2>
                        <button id="close-modal" class="text-gray-500 hover:text-gray-700 transition-colors" aria-label="Close modal">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>

                    <!-- Content -->
                    <div class="p-6 pt-0 flex-1 overflow-y-auto">
                        <form id="upload-form" enctype="multipart/form-data" class="space-y-6">
                            @csrf
                            <div id="upload-container" class="border-2 border-dashed border-gray-200 rounded-xl p-8 text-center hover:border-indigo-400 hover:bg-indigo-50 transition-all duration-200 min-h-[200px] flex flex-col justify-center">
                                <input
                                    type="file"
                                    id="photo-input"
                                    name="photo"
                                    accept="image/jpeg,image/png,image/gif"
                                    class="hidden"
                                    required
                                    aria-label="Upload image"
                                >
                                <div id="upload-area" class="cursor-pointer">
                                    <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-4"></i>
                                    <p class="text-lg font-medium text-gray-700 mb-2">Click to upload or drag and drop</p>
                                    <p class="text-sm text-gray-500">PNG, JPG, JPEG, GIF (max 5MB)</p>
                                </div>
                            </div>

                            <div id="image-preview" class="hidden">
                                <div class="relative w-full">
                                    <div id="preview-loading" class="absolute inset-0 flex items-center justify-center bg-gray-50 rounded-xl hidden">
                                        <i class="fas fa-spinner fa-spin text-4xl text-gray-400"></i>
                                    </div>
                                    <img id="preview-img" class="w-full h-auto max-h-64 object-contain bg-gray-50 rounded-xl" alt="Image preview" />
                                    <button type="button" id="change-image-btn" class="absolute top-3 right-3 bg-white bg-opacity-90 hover:bg-opacity-100 text-gray-700 px-3 py-1 rounded-full text-sm font-medium shadow-sm transition-colors" aria-label="Change image">
                                        <i class="fas fa-edit mr-1"></i>
                                        Change
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Footer -->
                    <div class="p-6 pt-0 flex justify-end gap-3 shrink-0">
                        <button type="button" id="cancel-upload" class="px-4 py-2 text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 text-sm font-medium transition-colors">
                            Cancel
                        </button>
                        <button type="submit" form="upload-form" id="analyze-btn" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed text-sm font-medium transition-colors" disabled>
                            <i class="fas fa-search mr-2"></i>
                            Analyze Photo
                        </button>
                    </div>
                </div>
            </div>

            {{-- Categories --}}
            <section class="mb-12">
                <div class="flex flex-wrap items-center justify-between mb-6">
                    <h2 class="text-2xl font-display font-bold text-indigo-900">Browse Categories</h2>
                    <div class="flex space-x-2 overflow-x-auto py-2 scrollbar-hide">
                        <a href="?category=All" class="whitespace-nowrap px-4 py-2 {{ request('category', 'All') == 'All' ? 'bg-indigo-600 text-white' : 'bg-gray-100 hover:bg-gray-200' }} rounded-full text-sm font-medium transition-colors">All Celebrities</a>
                        <a href="?category=Fashion" class="whitespace-nowrap px-4 py-2 {{ request('category') == 'Fashion' ? 'bg-indigo-600 text-white' : 'bg-gray-100 hover:bg-gray-200' }} rounded-full text-sm font-medium transition-colors">Fashion</a>
                        <a href="?category=Beauty" class="whitespace-nowrap px-4 py-2 {{ request('category') == 'Beauty' ? 'bg-indigo-600 text-white' : 'bg-gray-100 hover:bg-gray-200' }} rounded-full text-sm font-medium transition-colors">Beauty</a>
                        <a href="?category=Fitness" class="whitespace-nowrap px-4 py-2 {{ request('category') == 'Fitness' ? 'bg-indigo-600 text-white' : 'bg-gray-100 hover:bg-gray-200' }} rounded-full text-sm font-medium transition-colors">Fitness</a>
                        <a href="?category=Tech" class="whitespace-nowrap px-4 py-2 {{ request('category') == 'Tech' ? 'bg-indigo-600 text-white' : 'bg-gray-100 hover:bg-gray-200' }} rounded-full text-sm font-medium transition-colors">Tech</a>
                    </div>
                </div>
            </section>

            {{-- Celebrity Grid --}}
            <section>
                <h2 class="text-2xl font-display font-bold text-indigo-900 mb-6">Popular Celebrities</h2>
                @if(isset($celebrities) && count($celebrities))
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        @foreach($celebrities as $celebrity)
                            <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-200">
                                <img src="{{ $celebrity->imageUrl }}" alt="{{ $celebrity->name }}" class="h-72 w-full object-cover" loading="lazy" />
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
                        <p class="text-gray-500 text-lg">No celebrities found in this category.</p>
                    </div>
                @endif
                <div class="mt-8 flex justify-center">
                    <button class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium">
                        Load More Celebrities
                    </button>
                </div>
            </section>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const uploadBtn = document.getElementById('upload-photo-btn');
            const modal = document.getElementById('upload-modal');
            const closeModal = document.getElementById('close-modal');
            const cancelBtn = document.getElementById('cancel-upload');
            const uploadContainer = document.getElementById('upload-container');
            const uploadArea = document.getElementById('upload-area');
            const photoInput = document.getElementById('photo-input');
            const imagePreview = document.getElementById('image-preview');
            const previewImg = document.getElementById('preview-img');
            const previewLoading = document.getElementById('preview-loading');
            const uploadForm = document.getElementById('upload-form');
            const analyzeBtn = document.getElementById('analyze-btn');
            const changeImageBtn = document.getElementById('change-image-btn');

            // Open modal
            uploadBtn.addEventListener('click', () => {
                modal.classList.remove('hidden');
                modal.classList.add('opacity-100');
            });

            // Close modal
            function closeModalFunc() {
                modal.classList.add('hidden');
                modal.classList.remove('opacity-100');
                resetForm();
            }

            closeModal.addEventListener('click', closeModalFunc);
            cancelBtn.addEventListener('click', closeModalFunc);

            // Click outside to close
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    closeModalFunc();
                }
            });

            // File input handling
            uploadArea.addEventListener('click', () => {
                photoInput.click();
            });

            // Drag and drop
            uploadArea.addEventListener('dragover', (e) => {
                e.preventDefault();
                uploadContainer.classList.add('border-indigo-400', 'bg-indigo-50');
            });

            uploadArea.addEventListener('dragleave', () => {
                uploadContainer.classList.remove('border-indigo-400', 'bg-indigo-50');
            });

            uploadArea.addEventListener('drop', (e) => {
                e.preventDefault();
                uploadContainer.classList.remove('border-indigo-400', 'bg-indigo-50');
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    photoInput.files = files;
                    handleFileSelect();
                }
            });

            // File selection
            photoInput.addEventListener('change', handleFileSelect);

            // Change image button
            changeImageBtn.addEventListener('click', () => {
                photoInput.value = '';
                photoInput.click();
            });

            function handleFileSelect() {
                const file = photoInput.files[0];
                if (file) {
                    // Check file size (5MB = 5 * 1024 * 1024 bytes)
                    const maxSize = 5 * 1024 * 1024;
                    if (file.size > maxSize) {
                        alert('File size must be less than 5MB. Please choose a smaller image.');
                        photoInput.value = '';
                        resetForm();
                        return;
                    }

                    // Check file type
                    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                    if (!allowedTypes.includes(file.type)) {
                        alert('Please select a valid image file (JPEG, PNG, or GIF).');
                        photoInput.value = '';
                        resetForm();
                        return;
                    }

                    // Ensure upload container is hidden before showing preview
                    uploadContainer.classList.add('hidden');
                    previewLoading.classList.remove('hidden');
                    imagePreview.classList.remove('hidden');

                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const img = new Image();
                        img.src = e.target.result;
                        img.onload = () => {
                            previewImg.src = img.src;
                            previewLoading.classList.add('hidden');
                            analyzeBtn.disabled = false;
                        };
                        img.onerror = () => {
                            alert('Error loading image. Please try another file.');
                            resetForm();
                        };
                    };
                    reader.onerror = () => {
                        alert('Error reading file. Please try again.');
                        resetForm();
                    };
                    reader.readAsDataURL(file);
                } else {
                    resetForm();
                }
            }

            // Form submission
            uploadForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                analyzeBtn.disabled = true;
                analyzeBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Analyzing...';

                try {
                    const formData = new FormData(uploadForm);
                    const response = await fetch('/analyze-photo', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });

                    if (!response.ok) {
                        if (response.status === 413) {
                            throw new Error('File too large. Please choose an image smaller than 5MB.');
                        } else if (response.status === 422) {
                            const errorData = await response.json();
                            throw new Error(errorData.errors?.photo?.[0] || 'Invalid file format.');
                        } else {
                            throw new Error('Server error (${response.status}). Please try again.');
                        }
                    }

                    const result = await response.json();
                    if (result.success) {
                        sessionStorage.setItem('analysisResults', JSON.stringify(result));
                        window.location.href = '/analysis-results';
                    } else {
                        throw new Error(result.message || 'Analysis failed');
                    }
                } catch (error) {
                    alert('Error: ' + error.message);
                } finally {
                    analyzeBtn.disabled = false;
                    analyzeBtn.innerHTML = '<i class="fas fa-search mr-2"></i>Analyze Photo';
                }
            });

            function resetForm() {
                uploadForm.reset();
                imagePreview.classList.add('hidden');
                uploadContainer.classList.remove('hidden');
                uploadArea.classList.remove('hidden');
                previewImg.src = '';
                previewLoading.classList.add('hidden');
                analyzeBtn.disabled = true;
            }
        });
    </script>
@endsection
