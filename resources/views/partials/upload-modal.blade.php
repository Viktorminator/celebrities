{{-- Add Your Style Modal --}}
<div id="upload-modal" class="fixed inset-0 bg-black bg-opacity-60 hidden z-[1000] flex items-center justify-center p-4 sm:p-6 transition-opacity duration-300">
    <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden shadow-2xl transform transition-all duration-300 flex flex-col">
        <!-- Header -->
        <div class="flex justify-between items-center p-6 border-b border-gray-200">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Add Your Style</h2>
                <p class="text-sm text-gray-500 mt-1">Share your fashion look with the community</p>
            </div>
            <button id="close-modal" class="text-gray-500 hover:text-gray-700 transition-colors" aria-label="Close modal">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Content -->
        <div class="p-6 flex-1 overflow-y-auto">
            <form id="upload-form" enctype="multipart/form-data" class="space-y-6">
                @csrf
                
                <!-- Step 1: Image Upload -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                        <i class="fas fa-image mr-2 text-indigo-600"></i>Upload Image *
                    </label>
                    <div id="upload-container" class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-indigo-400 hover:bg-indigo-50 transition-all duration-200 min-h-[200px] flex flex-col justify-center">
                        <input
                            type="file"
                            id="photo-input"
                            name="photo"
                            accept="image/jpeg,image/png,image/gif,image/jpg"
                            class="hidden"
                            required
                            aria-label="Upload image"
                        >
                        <div id="upload-area" class="cursor-pointer">
                            <i class="fas fa-cloud-upload-alt text-5xl text-gray-400 mb-4"></i>
                            <p class="text-lg font-medium text-gray-700 mb-2">Click to upload or drag and drop</p>
                            <p class="text-sm text-gray-500">PNG, JPG, JPEG, GIF (max 10MB)</p>
                        </div>
                    </div>

                    <div id="image-preview" class="hidden mt-4">
                        <div class="relative w-full">
                            <div id="preview-loading" class="absolute inset-0 flex items-center justify-center bg-gray-50 rounded-xl hidden z-10">
                                <i class="fas fa-spinner fa-spin text-4xl text-indigo-600"></i>
                            </div>
                            <img id="preview-img" class="w-full h-auto max-h-64 object-contain bg-gray-50 rounded-xl border-2 border-gray-200" alt="Image preview" />
                            <button type="button" id="change-image-btn" class="absolute top-3 right-3 bg-white hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium shadow-md transition-colors" aria-label="Change image">
                                <i class="fas fa-edit mr-1"></i>Change Image
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Links Section -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                        <i class="fas fa-link mr-2 text-indigo-600"></i>Product Links
                    </label>
                    <p class="text-xs text-gray-500 mb-3">Add links to where others can find similar items (optional)</p>
                    <div id="links-container" class="space-y-3">
                        <div class="link-item flex gap-2">
                            <input
                                type="url"
                                name="links[]"
                                placeholder="https://example.com/product"
                                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                            >
                            <button type="button" class="remove-link-btn text-red-600 hover:text-red-700 px-3 py-2 hidden" aria-label="Remove link">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <button type="button" id="add-link-btn" class="mt-2 text-sm text-indigo-600 hover:text-indigo-700 font-medium flex items-center gap-1">
                        <i class="fas fa-plus"></i> Add Another Link
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

                    <!-- Custom Tag Input -->
                    <div class="flex gap-2">
                        <input
                            type="text"
                            id="custom-tag-input"
                            placeholder="Add custom tag..."
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                        >
                        <button type="button" id="add-custom-tag-btn" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium transition-colors">
                            <i class="fas fa-plus mr-1"></i>Add
                        </button>
                    </div>
                    <input type="hidden" id="tags-input" name="tags" value="">
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
            </form>
        </div>

        <!-- Footer -->
        <div class="p-6 pt-0 border-t border-gray-200 flex justify-end gap-3 shrink-0">
            <button type="button" id="cancel-upload" class="px-6 py-2 text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 text-sm font-medium transition-colors">
                Cancel
            </button>
            <button type="submit" form="upload-form" id="submit-btn" class="px-6 py-2 bg-pink-500 text-white rounded-lg hover:bg-pink-600 disabled:opacity-50 disabled:cursor-not-allowed text-sm font-medium transition-colors flex items-center gap-2" disabled>
                <i class="fas fa-upload"></i>
                <span id="submit-btn-text">Submit Style</span>
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
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
    const submitBtn = document.getElementById('submit-btn');
    const changeImageBtn = document.getElementById('change-image-btn');
    const addLinkBtn = document.getElementById('add-link-btn');
    const linksContainer = document.getElementById('links-container');
    const tagButtons = document.querySelectorAll('.tag-btn');
    const customTagInput = document.getElementById('custom-tag-input');
    const addCustomTagBtn = document.getElementById('add-custom-tag-btn');
    const selectedTagsContainer = document.getElementById('selected-tags');
    const tagsInput = document.getElementById('tags-input');
    
    let selectedTags = new Set();

    // Close modal
    function closeModalFunc() {
        modal.classList.add('hidden');
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
            const maxSize = 10 * 1024 * 1024; // 10MB
            if (file.size > maxSize) {
                alert('File size must be less than 10MB. Please choose a smaller image.');
                photoInput.value = '';
                resetForm();
                return;
            }

            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            if (!allowedTypes.includes(file.type)) {
                alert('Please select a valid image file (JPEG, PNG, or GIF).');
                photoInput.value = '';
                resetForm();
                return;
            }

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
                    submitBtn.disabled = false;
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

    // Add link functionality
    addLinkBtn.addEventListener('click', () => {
        const linkItem = document.createElement('div');
        linkItem.className = 'link-item flex gap-2';
        linkItem.innerHTML = `
            <input
                type="url"
                name="links[]"
                placeholder="https://example.com/product"
                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
            >
            <button type="button" class="remove-link-btn text-red-600 hover:text-red-700 px-3 py-2" aria-label="Remove link">
                <i class="fas fa-trash"></i>
            </button>
        `;
        linksContainer.appendChild(linkItem);
        
        // Show remove button on first link if there are multiple
        updateLinkButtons();
        
        // Add remove functionality
        linkItem.querySelector('.remove-link-btn').addEventListener('click', () => {
            linkItem.remove();
            updateLinkButtons();
        });
    });

    function updateLinkButtons() {
        const linkItems = linksContainer.querySelectorAll('.link-item');
        linkItems.forEach((item, index) => {
            const removeBtn = item.querySelector('.remove-link-btn');
            if (linkItems.length > 1) {
                removeBtn.classList.remove('hidden');
            } else {
                removeBtn.classList.add('hidden');
            }
        });
    }

    // Tag functionality
    tagButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const tag = btn.dataset.tag;
            toggleTag(tag, btn);
        });
    });

    addCustomTagBtn.addEventListener('click', () => {
        const tag = customTagInput.value.trim();
        if (tag && !selectedTags.has(tag)) {
            addTag(tag);
            customTagInput.value = '';
        }
    });

    customTagInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            addCustomTagBtn.click();
        }
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
        tagsInput.value = Array.from(selectedTags).join(',');
        
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
            
            // Add remove functionality to tag chips
            selectedTagsContainer.querySelectorAll('.remove-tag-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const tag = btn.dataset.tag;
                    selectedTags.delete(tag);
                    updateSelectedTags();
                    // Update popular tag button state
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
        submitBtn.disabled = true;
        const submitBtnText = document.getElementById('submit-btn-text');
        submitBtnText.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Uploading...';

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
            let errorData = null;
            try {
                errorData = await response.json();
            } catch (parseError) {
                // ignore
            }

            if (response.status === 413) {
                throw new Error('File too large. Please choose an image smaller than 10MB.');
            } else if (response.status === 422) {
                throw new Error(errorData?.errors?.photo?.[0] || 'Invalid file format.');
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
            } else if (result.success && result.analysis_id) {
                window.location.href = `/analysis-results?id=${result.analysis_id}`;
            } else {
                throw new Error(result.message || 'Upload failed');
            }
        } catch (error) {
            alert('Error: ' + error.message);
            submitBtn.disabled = false;
            submitBtnText.innerHTML = 'Submit Style';
        }
    });

    function resetForm() {
        uploadForm.reset();
        imagePreview.classList.add('hidden');
        uploadContainer.classList.remove('hidden');
        previewImg.src = '';
        previewLoading.classList.add('hidden');
        submitBtn.disabled = true;
        selectedTags.clear();
        updateSelectedTags();
        linksContainer.innerHTML = `
            <div class="link-item flex gap-2">
                <input
                    type="url"
                    name="links[]"
                    placeholder="https://example.com/product"
                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                >
                <button type="button" class="remove-link-btn text-red-600 hover:text-red-700 px-3 py-2 hidden" aria-label="Remove link">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
        tagButtons.forEach(btn => {
            btn.classList.remove('bg-indigo-600', 'text-white');
            btn.classList.add('bg-gray-100', 'text-gray-700');
        });
    }
});
</script>
