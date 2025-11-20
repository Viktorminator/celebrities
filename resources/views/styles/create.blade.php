@extends('layout')

@section('title', 'Add Your Style - Glamdar')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Back Button -->
        <a href="{{ route('styles.index') }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 mb-6 font-medium">
            <i class="fas fa-arrow-left mr-2"></i>Back to My Styles
        </a>

        @if($limitReached)
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-6">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle text-yellow-600 mr-3 text-xl"></i>
                    <div>
                        <h3 class="text-lg font-semibold text-yellow-800">Style Limit Reached</h3>
                        <p class="text-yellow-700 mt-1">You have reached your current plan limit of {{ $styleLimit }} styles. <a href="{{ route('subscriptions') }}" class="underline font-medium">Upgrade your subscription</a> to add more styles.</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="p-8">
                <div class="mb-6">
                    <h1 class="text-3xl font-display font-bold text-indigo-900 mb-2">Add Your Style</h1>
                    <p class="text-gray-600">Share your fashion look with the community. Upload 1-5 images.</p>
                </div>

                <form id="upload-form" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <!-- Step 1: Image Upload (Multiple) -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                            <i class="fas fa-image mr-2 text-indigo-600"></i>Upload Images (1-5) *
                        </label>
                        <div id="upload-container" class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-indigo-400 hover:bg-indigo-50 transition-all duration-200 min-h-[200px] flex flex-col justify-center">
                            <input
                                type="file"
                                id="photo-input"
                                accept="image/jpeg,image/png,image/gif,image/jpg"
                                class="hidden"
                                multiple
                                aria-label="Upload images"
                            >
                            <div id="upload-area" class="cursor-pointer">
                                <i class="fas fa-cloud-upload-alt text-5xl text-gray-400 mb-4"></i>
                                <p class="text-lg font-medium text-gray-700 mb-2">Click to upload or drag and drop</p>
                                <p class="text-sm text-gray-500">PNG, JPG, JPEG, GIF (max 10MB each, 1-5 images)</p>
                            </div>
                        </div>

                        <div id="images-preview" class="hidden mt-4 grid grid-cols-2 md:grid-cols-3 gap-4">
                            <!-- Preview images will be inserted here -->
                        </div>
                        <p id="image-count" class="text-sm text-gray-500 mt-2 hidden"></p>
                    </div>

                    <!-- Step 2: Links Section -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                            <i class="fas fa-link mr-2 text-indigo-600"></i>Product Links
                        </label>
                        <p class="text-xs text-gray-500 mb-3">Add links to where others can find similar items (optional)</p>
                        <div id="links-container" class="space-y-3">
                            <div class="link-item p-4 border border-gray-200 rounded-lg bg-gray-50">
                                <div class="flex items-start justify-between mb-3">
                                    <h4 class="font-medium text-gray-700">Link #<span class="link-number">1</span></h4>
                                    <button type="button" class="remove-link-btn text-red-600 hover:text-red-800 hidden" aria-label="Remove link">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <div class="grid grid-cols-2 gap-3 mb-3">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Title *</label>
                                        <input
                                            type="text"
                                            name="links[0][title]"
                                            placeholder="Product name"
                                            class="link-title w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white"
                                            required
                                        >
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Platform</label>
                                        <select name="links[0][platform]" class="link-platform w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white">
                                            <option value="Amazon">Amazon</option>
                                            <option value="Google Shopping">Google Shopping</option>
                                            <option value="Other" selected>Other</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">URL *</label>
                                        <input
                                            type="url"
                                            name="links[0][url]"
                                            placeholder="https://example.com/product"
                                            class="link-url w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white"
                                            required
                                        >
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Price</label>
                                        <input
                                            type="text"
                                            name="links[0][price]"
                                            placeholder="e.g., $29.99"
                                            class="link-price w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white"
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="button" id="add-link-btn" class="mt-2 w-full px-4 py-2 border-2 border-dashed border-gray-300 rounded-lg text-gray-600 hover:border-indigo-500 hover:text-indigo-600 transition-colors">
                            <i class="fas fa-plus mr-2"></i>Add Another Link
                        </button>
                    </div>

                    <!-- Step 3: Tags/Categories -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                            <i class="fas fa-tags mr-2 text-indigo-600"></i>Tags / Categories
                        </label>
                        <p class="text-xs text-gray-500 mb-3">Select or add tags to help others find your style</p>

                        <!-- Popular Tags -->
                        <div class="mb-4">
                            <p class="text-xs font-medium text-gray-600 mb-2">Popular Tags:</p>
                            <div id="popular-tags" class="flex flex-wrap gap-2">
                                <button type="button" class="tag-btn px-3 py-1 bg-gray-100 hover:bg-indigo-100 text-gray-700 hover:text-indigo-700 rounded-full text-sm font-medium transition-colors" data-tag="Casual">Casual</button>
                                <button type="button" class="tag-btn px-3 py-1 bg-gray-100 hover:bg-indigo-100 text-gray-700 hover:text-indigo-700 rounded-full text-sm font-medium transition-colors" data-tag="Formal">Formal</button>
                                <button type="button" class="tag-btn px-3 py-1 bg-gray-100 hover:bg-indigo-100 text-gray-700 hover:text-indigo-700 rounded-full text-sm font-medium transition-colors" data-tag="Streetwear">Streetwear</button>
                                <button type="button" class="tag-btn px-3 py-1 bg-gray-100 hover:bg-indigo-100 text-gray-700 hover:text-indigo-700 rounded-full text-sm font-medium transition-colors" data-tag="Vintage">Vintage</button>
                                <button type="button" class="tag-btn px-3 py-1 bg-gray-100 hover:bg-indigo-100 text-gray-700 hover:text-indigo-700 rounded-full text-sm font-medium transition-colors" data-tag="Bohemian">Bohemian</button>
                                <button type="button" class="tag-btn px-3 py-1 bg-gray-100 hover:bg-indigo-100 text-gray-700 hover:text-indigo-700 rounded-full text-sm font-medium transition-colors" data-tag="Minimalist">Minimalist</button>
                                <button type="button" class="tag-btn px-3 py-1 bg-gray-100 hover:bg-indigo-100 text-gray-700 hover:text-indigo-700 rounded-full text-sm font-medium transition-colors" data-tag="Sporty">Sporty</button>
                                <button type="button" class="tag-btn px-3 py-1 bg-gray-100 hover:bg-indigo-100 text-gray-700 hover:text-indigo-700 rounded-full text-sm font-medium transition-colors" data-tag="Elegant">Elegant</button>
                                <button type="button" class="tag-btn px-3 py-1 bg-gray-100 hover:bg-indigo-100 text-gray-700 hover:text-indigo-700 rounded-full text-sm font-medium transition-colors" data-tag="Romantic">Romantic</button>
                                <button type="button" class="tag-btn px-3 py-1 bg-gray-100 hover:bg-indigo-100 text-gray-700 hover:text-indigo-700 rounded-full text-sm font-medium transition-colors" data-tag="Edgy">Edgy</button>
                                <button type="button" class="tag-btn px-3 py-1 bg-gray-100 hover:bg-indigo-100 text-gray-700 hover:text-indigo-700 rounded-full text-sm font-medium transition-colors" data-tag="Chic">Chic</button>
                                <button type="button" class="tag-btn px-3 py-1 bg-gray-100 hover:bg-indigo-100 text-gray-700 hover:text-indigo-700 rounded-full text-sm font-medium transition-colors" data-tag="Beach">Beach</button>
                            </div>
                        </div>

                        <!-- Selected Tags Display -->
                        <div id="selected-tags" class="flex flex-wrap gap-2 mb-3 min-h-[40px]">
                            <p class="text-xs text-gray-400 self-center">No tags selected</p>
                        </div>
                    </div>

                    <!-- Description (Optional) -->
                    <div>
                        <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-align-left mr-2 text-indigo-600"></i>Description (Optional)
                        </label>
                        <textarea
                            id="description"
                            name="description"
                            rows="3"
                            placeholder="Tell us about this style..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm resize-none"
                        ></textarea>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end gap-3 pt-6 border-t border-gray-200">
                        <a href="{{ route('styles.index') }}" class="px-6 py-2 text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 text-sm font-medium transition-colors">
                            Cancel
                        </a>
                        <button type="submit" id="submit-btn" class="px-6 py-2 bg-pink-500 text-white rounded-lg hover:bg-pink-600 disabled:opacity-50 disabled:cursor-not-allowed text-sm font-medium transition-colors flex items-center gap-2" {{ $limitReached ? 'disabled' : '' }}>
                            <i class="fas fa-upload"></i>
                            <span id="submit-btn-text">Submit Style</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const uploadContainer = document.getElementById('upload-container');
    const uploadArea = document.getElementById('upload-area');
    const photoInput = document.getElementById('photo-input');
    const imagesPreview = document.getElementById('images-preview');
    const imageCount = document.getElementById('image-count');
    const uploadForm = document.getElementById('upload-form');
    const submitBtn = document.getElementById('submit-btn');
    const addLinkBtn = document.getElementById('add-link-btn');
    const linksContainer = document.getElementById('links-container');
    const tagButtons = document.querySelectorAll('.tag-btn');
    const addCustomTagBtn = document.getElementById('add-custom-tag-btn');
    const selectedTagsContainer = document.getElementById('selected-tags');

    let selectedTags = new Set();
    let selectedFiles = [];

    // File input handling
    uploadArea.addEventListener('click', () => {
        photoInput.click();
    });

    // Drag and drop
    uploadContainer.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadContainer.classList.add('border-indigo-400', 'bg-indigo-50');
    });

    uploadContainer.addEventListener('dragleave', () => {
        uploadContainer.classList.remove('border-indigo-400', 'bg-indigo-50');
    });

    uploadContainer.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadContainer.classList.remove('border-indigo-400', 'bg-indigo-50');
        const files = Array.from(e.dataTransfer.files);
        handleFiles(files);
    });

    // File selection
    photoInput.addEventListener('change', (e) => {
        const files = Array.from(e.target.files);
        handleFiles(files);
        // Clear the input value to prevent it from being included in FormData
        e.target.value = '';
    });

    function handleFiles(files) {
        // Filter only image files
        const imageFiles = files.filter(file => file.type.startsWith('image/'));

        if (imageFiles.length === 0) {
            alert('Please select valid image files.');
            return;
        }

        // Check total count (max 5)
        if (selectedFiles.length + imageFiles.length > 5) {
            alert('You can upload a maximum of 5 images. Please select fewer images.');
            return;
        }

        // Validate each file
        const maxSize = 10 * 1024 * 1024; // 10MB
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];

        for (const file of imageFiles) {
            if (file.size > maxSize) {
                alert(`File "${file.name}" is too large. Maximum size is 10MB.`);
                continue;
            }
            if (!allowedTypes.includes(file.type)) {
                alert(`File "${file.name}" is not a valid image type.`);
                continue;
            }
            selectedFiles.push(file);
        }

        // Update preview
        updatePreview();
    }

    function updatePreview() {
        if (selectedFiles.length === 0) {
            imagesPreview.classList.add('hidden');
            imageCount.classList.add('hidden');
            uploadContainer.classList.remove('hidden');
            submitBtn.disabled = true;
            return;
        }

        uploadContainer.classList.add('hidden');
        imagesPreview.classList.remove('hidden');
        imageCount.classList.remove('hidden');
        imagesPreview.innerHTML = '';

        selectedFiles.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = (e) => {
                const div = document.createElement('div');
                div.className = 'relative group';
                div.innerHTML = `
                    <img src="${e.target.result}" alt="Preview ${index + 1}" class="w-full h-48 object-cover rounded-lg border-2 border-gray-200">
                    <button type="button" onclick="removeImage(${index})" class="absolute top-2 right-2 bg-red-500 hover:bg-red-600 text-white rounded-full p-2 opacity-0 group-hover:opacity-100 transition-opacity">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                    <p class="text-xs text-gray-500 mt-1 truncate">${file.name}</p>
                `;
                imagesPreview.appendChild(div);
            };
            reader.readAsDataURL(file);
        });

        imageCount.textContent = `${selectedFiles.length} image(s) selected (max 5)`;
        submitBtn.disabled = false;
    }

    window.removeImage = function(index) {
        selectedFiles.splice(index, 1);
        updatePreview();
        // Update file input
        const dt = new DataTransfer();
        selectedFiles.forEach(file => dt.items.add(file));
        photoInput.files = dt.files;
    };

    let linkCounter = 1;

    // Add link functionality
    addLinkBtn.addEventListener('click', () => {
        linkCounter++;
        const linkItem = document.createElement('div');
        linkItem.className = 'link-item p-4 border border-gray-200 rounded-lg bg-gray-50';
        linkItem.innerHTML = `
            <div class="flex items-start justify-between mb-3">
                <h4 class="font-medium text-gray-700">Link #<span class="link-number">${linkCounter}</span></h4>
                <button type="button" class="remove-link-btn text-red-600 hover:text-red-800" aria-label="Remove link">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <div class="grid grid-cols-2 gap-3 mb-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Title *</label>
                    <input
                        type="text"
                        name="links[${linkCounter - 1}][title]"
                        placeholder="Product name"
                        class="link-title w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white"
                        required
                    >
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Platform</label>
                    <select name="links[${linkCounter - 1}][platform]" class="link-platform w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white">
                        <option value="Amazon">Amazon</option>
                        <option value="Google Shopping">Google Shopping</option>
                        <option value="Other" selected>Other</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">URL *</label>
                    <input
                        type="url"
                        name="links[${linkCounter - 1}][url]"
                        placeholder="https://example.com/product"
                        class="link-url w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white"
                        required
                    >
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Price</label>
                    <input
                        type="text"
                        name="links[${linkCounter - 1}][price]"
                        placeholder="e.g., $29.99"
                        class="link-price w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white"
                    >
                </div>
            </div>
        `;
        linksContainer.appendChild(linkItem);

        updateLinkButtons();

        linkItem.querySelector('.remove-link-btn').addEventListener('click', () => {
            linkItem.remove();
            updateLinkNumbers();
            updateLinkButtons();
        });
    });

    function updateLinkButtons() {
        const linkItems = linksContainer.querySelectorAll('.link-item');
        linkItems.forEach((item) => {
            const removeBtn = item.querySelector('.remove-link-btn');
            if (linkItems.length > 1) {
                removeBtn.classList.remove('hidden');
            } else {
                removeBtn.classList.add('hidden');
            }
        });
    }

    function updateLinkNumbers() {
        const linkItems = linksContainer.querySelectorAll('.link-item');
        linkItems.forEach((item, index) => {
            item.querySelector('.link-number').textContent = index + 1;
            // Update input names to maintain sequential indices
            const inputs = item.querySelectorAll('input, select');
            inputs.forEach(input => {
                if (input.name) {
                    input.name = input.name.replace(/links\[\d+\]/, `links[${index}]`);
                }
            });
        });
    }

    // Tag functionality
    tagButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const tag = btn.dataset.tag;
            toggleTag(tag, btn);
        });
    });

    function toggleTag(tag, btn) {
        if (selectedTags.has(tag)) {
            selectedTags.delete(tag);
            btn.classList.remove('bg-indigo-600', 'text-white');
            btn.classList.add('bg-gray-100', 'text-gray-700');
        } else {
            selectedTags.add(tag);
            btn.classList.remove('bg-gray-100', 'text-gray-700');
            btn.classList.add('bg-indigo-600', 'text-white');
        }
        updateSelectedTags();
    }

    function addTag(tag) {
        if (!selectedTags.has(tag)) {
            selectedTags.add(tag);
            updateSelectedTags();
        }
    }

    function updateSelectedTags() {
        if (selectedTags.size === 0) {
            selectedTagsContainer.innerHTML = '<p class="text-xs text-gray-400 self-center">No tags selected</p>';
        } else {
            selectedTagsContainer.innerHTML = Array.from(selectedTags).map(tag => `
                <span class="inline-flex items-center gap-1 px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-sm font-medium">
                    ${tag}
                    <button type="button" class="remove-tag-btn text-indigo-500 hover:text-indigo-700" data-tag="${tag}">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </span>
            `).join('');

            selectedTagsContainer.querySelectorAll('.remove-tag-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const tag = btn.dataset.tag;
                    selectedTags.delete(tag);
                    updateSelectedTags();
                    const popularBtn = Array.from(tagButtons).find(b => b.dataset.tag === tag);
                    if (popularBtn) {
                        popularBtn.classList.remove('bg-indigo-600', 'text-white');
                        popularBtn.classList.add('bg-gray-100', 'text-gray-700');
                    }
                });
            });
        }
    }

    // Form submission
    uploadForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        if (selectedFiles.length === 0) {
            alert('Please select at least one image.');
            return;
        }

        if (selectedFiles.length > 5) {
            alert('You can upload a maximum of 5 images.');
            return;
        }

        submitBtn.disabled = true;
        const submitBtnText = document.getElementById('submit-btn-text');
        submitBtnText.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Uploading...';

        try {
            const formData = new FormData(uploadForm);

            // Clear any existing photos from form data (in case file input had files)
            formData.delete('photos[]');

            // Add all selected files from our managed array
            selectedFiles.forEach((file) => {
                formData.append('photos[]', file);
            });

            const response = await fetch('{{ route("styles.store") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            if (!response.ok) {
                let errorData = null;
                try {
                    errorData = await response.json();
                } catch (parseError) {
                    // ignore
                }

                if (response.status === 413) {
                    throw new Error('File too large. Please choose images smaller than 10MB.');
                } else if (response.status === 422) {
                    throw new Error(errorData?.errors?.photos?.[0] || 'Invalid file format.');
                } else if (response.status === 403 && errorData?.upgrade_url) {
                    alert(errorData.message || 'You have reached your current plan limit.');
                    window.location.href = errorData.upgrade_url;
                    return;
                } else {
                    throw new Error(errorData?.message || `Server error (${response.status}). Please try again.`);
                }
            }

            const result = await response.json();

            if (result.success && result.redirect_url) {
                window.location.href = result.redirect_url;
            } else {
                throw new Error(result.message || 'Upload failed');
            }
        } catch (error) {
            alert('Error: ' + error.message);
            submitBtn.disabled = false;
            submitBtnText.innerHTML = 'Submit Style';
        }
    });
});
</script>
@endsection

