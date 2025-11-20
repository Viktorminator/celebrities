@extends('layout')

@section('title', 'Edit Style - Glamdar')

@section('content')
    <div class="min-h-screen bg-gray-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Back Button -->
            <a href="{{ route('style.view', $style->id) }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 mb-6 font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Back to Style
            </a>

            <!-- Success Message -->
            @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-600 mr-3"></i>
                        <p class="text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="p-8">
                    <div class="mb-6">
                        <h1 class="text-3xl font-display font-bold text-indigo-900 mb-2">Edit Style</h1>
                        <p class="text-gray-600">Update your style's description, tags, and product links</p>
                    </div>

                    <form method="POST" action="{{ route('styles.update', $style->id) }}" id="edit-style-form" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Hidden file input for new images -->
                        <input type="file" id="new-images-input" name="new_images[]" accept="image/jpeg,image/png,image/gif,image/jpg" multiple class="hidden">

                        <!-- Style Images Section -->
                        @php
                            // Get images directly from relationship to ensure we have IDs
                            // Build array with all necessary data
                            $allImages = [];
                            foreach($style->images->sortBy('position') as $img) {
                                $allImages[] = [
                                    'id' => $img->id,
                                    'url' => $img->url,
                                    'path' => $img->path,
                                    'original_filename' => $img->original_filename,
                                    'filename' => $img->filename,
                                    'dimensions' => $img->dimensions,
                                    'file_size' => $img->file_size,
                                    'position' => $img->position,
                                ];
                            }
                        @endphp
                        <div class="mb-8">
                            <label class="block text-sm font-semibold text-gray-700 mb-3">
                                <i class="fas fa-images mr-2 text-indigo-600"></i>Images (<span id="image-count">{{ count($allImages) }}</span>/5)
                            </label>

                            <!-- Error Message Container -->
                            <div id="image-error" class="hidden mb-3 bg-red-50 border border-red-200 rounded-lg p-3">
                                <div class="flex items-center">
                                    <i class="fas fa-exclamation-circle text-red-600 mr-2"></i>
                                    <p class="text-red-800 text-sm" id="image-error-text"></p>
                                </div>
                            </div>

                            <!-- Loading Spinner -->
                            <div id="image-loading" class="hidden mb-3 bg-blue-50 border border-blue-200 rounded-lg p-3">
                                <div class="flex items-center">
                                    <i class="fas fa-spinner fa-spin text-blue-600 mr-2"></i>
                                    <p class="text-blue-800 text-sm">Processing images...</p>
                                </div>
                            </div>

                            <div id="images-container" class="space-y-3">
                                @foreach($allImages as $index => $image)
                                    @php
                                        // Ensure we have the image ID - check multiple possible sources
                                        $imageId = $image['id'] ?? $image->id ?? ($image instanceof \App\Models\StyleImage ? $image->id : null);
                                    @endphp
                                    <div class="image-item existing-image relative border-2 border-gray-200 rounded-lg overflow-hidden bg-gray-50"
                                         data-image-index="{{ $index }}"
                                         data-image-id="{{ $imageId }}">
                                        <div class="flex items-center gap-4 p-3">
                                            <!-- Image Preview -->
                                            <div class="w-24 h-24 sm:w-32 sm:h-32 flex-shrink-0 bg-gray-100 rounded-lg overflow-hidden">
                                                <img src="{{ is_array($image) ? $image['url'] : $image->url }}" alt="Image {{ $index + 1 }}" class="w-full h-full object-cover">
                                            </div>
                                            <!-- Image Info -->
                                            <div class="flex-1 py-2">
                                                <p class="text-sm font-medium text-gray-700 mb-1 truncate">
                                                    {{ is_array($image) ? ($image['original_filename'] ?? 'Image ' . ($index + 1)) : ($image->original_filename ?? 'Image ' . ($index + 1)) }}
                                                </p>
                                                <p class="text-xs text-gray-500">
                                                    {{ is_array($image) ? ($image['dimensions'] ?? 'N/A') : ($image->dimensions ?? 'N/A') }}
                                                </p>
                                                <p class="text-xs text-indigo-600 font-medium">ID: {{ $imageId }}</p>
                                            </div>
                                            <!-- Remove Button -->
                                            <button type="button" onclick="removeImage(this)" class="flex-shrink-0 p-2 bg-red-500 text-white rounded-full hover:bg-red-600 transition-colors" title="Remove image">
                                                <i class="fas fa-times text-xs"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Removed Images Section (for restore) -->
                            <div id="removed-images-container" class="hidden mt-4 p-4 bg-gray-100 rounded-lg border border-gray-300">
                                <p class="text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-trash-restore mr-1"></i>Removed Images (click to restore):
                                </p>
                                <div id="removed-images-list" class="flex flex-wrap gap-2">
                                    <!-- Removed images will be added here -->
                                </div>
                            </div>

                            <!-- Add New Image Button -->
                            <button type="button" onclick="addImageInput()" id="add-image-btn" class="mt-4 w-full px-4 py-3 border-2 border-dashed border-gray-300 rounded-lg text-gray-600 hover:border-indigo-500 hover:text-indigo-600 transition-colors flex items-center justify-center gap-2">
                                <i class="fas fa-plus"></i>
                                <span>Add New Image (<span id="current-image-count">{{ count($allImages) }}</span>/5)</span>
                            </button>
                            <p id="max-images-message" class="mt-4 text-sm text-gray-500 text-center hidden">
                                <i class="fas fa-info-circle mr-1"></i>Maximum 5 images reached
                            </p>
                            @error('images')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                            <textarea name="description" id="edit-description" rows="4"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                      placeholder="Describe this style...">{{ $style->analysis_metadata['description'] ?? '' }}</textarea>
                        </div>

                        <!-- Tags -->
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-3">
                                <i class="fas fa-tags mr-2 text-indigo-600"></i>Tags / Categories
                            </label>
                            <p class="text-xs text-gray-500 mb-3">Select or add tags to help others find your style</p>

                            <!-- Popular Tags -->
                            <div class="mb-4">
                                <p class="text-xs font-medium text-gray-600 mb-2">Popular Tags:</p>
                                <div id="popular-tags" class="flex flex-wrap gap-2">
                                    @php
                                        $selectedTags = $style->styleTags->pluck('tag')->toArray();
                                    @endphp
                                    <button type="button" class="tag-btn px-3 py-1 rounded-full text-sm font-medium transition-colors {{ in_array('Casual', $selectedTags) ? 'bg-indigo-600 text-white' : 'bg-gray-100 hover:bg-indigo-100 text-gray-700 hover:text-indigo-700' }}" data-tag="Casual">Casual</button>
                                    <button type="button" class="tag-btn px-3 py-1 rounded-full text-sm font-medium transition-colors {{ in_array('Formal', $selectedTags) ? 'bg-indigo-600 text-white' : 'bg-gray-100 hover:bg-indigo-100 text-gray-700 hover:text-indigo-700' }}" data-tag="Formal">Formal</button>
                                    <button type="button" class="tag-btn px-3 py-1 rounded-full text-sm font-medium transition-colors {{ in_array('Streetwear', $selectedTags) ? 'bg-indigo-600 text-white' : 'bg-gray-100 hover:bg-indigo-100 text-gray-700 hover:text-indigo-700' }}" data-tag="Streetwear">Streetwear</button>
                                    <button type="button" class="tag-btn px-3 py-1 rounded-full text-sm font-medium transition-colors {{ in_array('Vintage', $selectedTags) ? 'bg-indigo-600 text-white' : 'bg-gray-100 hover:bg-indigo-100 text-gray-700 hover:text-indigo-700' }}" data-tag="Vintage">Vintage</button>
                                    <button type="button" class="tag-btn px-3 py-1 rounded-full text-sm font-medium transition-colors {{ in_array('Bohemian', $selectedTags) ? 'bg-indigo-600 text-white' : 'bg-gray-100 hover:bg-indigo-100 text-gray-700 hover:text-indigo-700' }}" data-tag="Bohemian">Bohemian</button>
                                    <button type="button" class="tag-btn px-3 py-1 rounded-full text-sm font-medium transition-colors {{ in_array('Minimalist', $selectedTags) ? 'bg-indigo-600 text-white' : 'bg-gray-100 hover:bg-indigo-100 text-gray-700 hover:text-indigo-700' }}" data-tag="Minimalist">Minimalist</button>
                                    <button type="button" class="tag-btn px-3 py-1 rounded-full text-sm font-medium transition-colors {{ in_array('Sporty', $selectedTags) ? 'bg-indigo-600 text-white' : 'bg-gray-100 hover:bg-indigo-100 text-gray-700 hover:text-indigo-700' }}" data-tag="Sporty">Sporty</button>
                                    <button type="button" class="tag-btn px-3 py-1 rounded-full text-sm font-medium transition-colors {{ in_array('Elegant', $selectedTags) ? 'bg-indigo-600 text-white' : 'bg-gray-100 hover:bg-indigo-100 text-gray-700 hover:text-indigo-700' }}" data-tag="Elegant">Elegant</button>
                                    <button type="button" class="tag-btn px-3 py-1 rounded-full text-sm font-medium transition-colors {{ in_array('Romantic', $selectedTags) ? 'bg-indigo-600 text-white' : 'bg-gray-100 hover:bg-indigo-100 text-gray-700 hover:text-indigo-700' }}" data-tag="Romantic">Romantic</button>
                                    <button type="button" class="tag-btn px-3 py-1 rounded-full text-sm font-medium transition-colors {{ in_array('Edgy', $selectedTags) ? 'bg-indigo-600 text-white' : 'bg-gray-100 hover:bg-indigo-100 text-gray-700 hover:text-indigo-700' }}" data-tag="Edgy">Edgy</button>
                                    <button type="button" class="tag-btn px-3 py-1 rounded-full text-sm font-medium transition-colors {{ in_array('Chic', $selectedTags) ? 'bg-indigo-600 text-white' : 'bg-gray-100 hover:bg-indigo-100 text-gray-700 hover:text-indigo-700' }}" data-tag="Chic">Chic</button>
                                    <button type="button" class="tag-btn px-3 py-1 rounded-full text-sm font-medium transition-colors {{ in_array('Beach', $selectedTags) ? 'bg-indigo-600 text-white' : 'bg-gray-100 hover:bg-indigo-100 text-gray-700 hover:text-indigo-700' }}" data-tag="Beach">Beach</button>
                                </div>
                            </div>

                            <!-- Selected Tags Display -->
                            <div id="selected-tags" class="flex flex-wrap gap-2 mb-3 min-h-[40px]">
                                @if($style->styleTags && $style->styleTags->count() > 0)
                                    @foreach($style->styleTags as $styleTag)
                                        <span class="inline-flex items-center gap-1 px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-sm font-medium">
                                        {{ $styleTag->tag }}
                                        <button type="button" class="remove-tag-btn text-indigo-500 hover:text-indigo-700" data-tag="{{ $styleTag->tag }}">
                                            <i class="fas fa-times text-xs"></i>
                                        </button>
                                    </span>
                                    @endforeach
                                @else
                                    <p class="text-xs text-gray-400 self-center">No tags selected</p>
                                @endif
                            </div>
                        </div>

                        <!-- Product Links -->
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Product Links</label>
                            <div id="links-container" class="space-y-3 mb-3">
                                @foreach($style->productLinks as $index => $link)
                                    <div class="link-item p-4 border border-gray-200 rounded-lg bg-gray-50" data-link-id="{{ $link->id }}">
                                        <div class="flex items-start justify-between mb-3">
                                            <h4 class="font-medium text-gray-700">Link #<span class="link-number">{{ $index + 1 }}</span></h4>
                                            <button type="button" onclick="removeLink(this)" class="text-red-600 hover:text-red-800">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                        <div class="grid grid-cols-2 gap-3 mb-3">
                                            <div>
                                                <label class="block text-xs font-medium text-gray-600 mb-1">Title *</label>
                                                <input type="text" class="link-title w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white"
                                                       value="{{ $link->title !== 'User Provided Link' ? $link->title : '' }}" required>
                                                <input type="hidden" class="link-id" value="{{ $link->id }}">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-600 mb-1">Platform</label>
                                                <select class="link-platform w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white">
                                                    <option value="Amazon" {{ $link->platform === 'Amazon' ? 'selected' : '' }}>Amazon</option>
                                                    <option value="Google Shopping" {{ $link->platform === 'Google Shopping' ? 'selected' : '' }}>Google Shopping</option>
                                                    <option value="Other" {{ !in_array($link->platform, ['Amazon', 'Google Shopping']) ? 'selected' : '' }}>Other</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-xs font-medium text-gray-600 mb-1">URL *</label>
                                                <input type="url" class="link-url w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white"
                                                       value="{{ $link->url }}" required>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-600 mb-1">Price</label>
                                                <input type="text" class="link-price w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white"
                                                       value="{{ $link->price }}" placeholder="e.g., $29.99">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" onclick="addLink()" class="w-full px-4 py-2 border-2 border-dashed border-gray-300 rounded-lg text-gray-600 hover:border-indigo-500 hover:text-indigo-600 transition-colors">
                                <i class="fas fa-plus mr-2"></i>Add New Link
                            </button>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex justify-end gap-3 pt-6 border-t border-gray-200">
                            <a href="{{ route('style.view', $style->id) }}" class="px-6 py-2 text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 text-sm font-medium transition-colors">
                                Cancel
                            </a>
                            <button type="submit" id="submit-btn" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium transition-colors">
                                <i class="fas fa-save mr-2"></i>Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Debug mode - set to true to see console logs
        const DEBUG_MODE = true;

        function debugLog(...args) {
            if (DEBUG_MODE) {
                console.log('[Edit Style]', ...args);
            }
        }

        // Global variables
        let newImageFiles = [];
        let newImagePreviews = [];

        document.addEventListener('DOMContentLoaded', function() {
            debugLog('Page loaded');

            const tagButtons = document.querySelectorAll('.tag-btn');
            const selectedTagsContainer = document.getElementById('selected-tags');
            let selectedTags = new Set(@json($style->styleTags->pluck('tag')->toArray()));

            // Make selectedTags available globally for form submission
            window.selectedStyleTags = selectedTags;

            debugLog('Initial selected tags:', Array.from(selectedTags));

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
                debugLog('Tags after toggle:', Array.from(selectedTags));
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
                            debugLog('Tags after removal:', Array.from(selectedTags));
                        });
                    });
                }
            }
        });

        let linkCounter = {{ $style->productLinks->count() }};

        // Link Management
        function addLink() {
            const container = document.getElementById('links-container');
            const linkItem = document.createElement('div');
            linkItem.className = 'link-item p-4 border border-gray-200 rounded-lg bg-gray-50';
            linkItem.innerHTML = `
        <div class="flex items-start justify-between mb-3">
            <h4 class="font-medium text-gray-700">Link #<span class="link-number">${linkCounter + 1}</span></h4>
            <button type="button" onclick="removeLink(this)" class="text-red-600 hover:text-red-800">
                <i class="fas fa-trash"></i>
            </button>
        </div>
        <div class="grid grid-cols-2 gap-3 mb-3">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Title *</label>
                <input type="text" class="link-title w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white" required>
                <input type="hidden" class="link-id" value="">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Platform</label>
                <select class="link-platform w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white">
                    <option value="Amazon">Amazon</option>
                    <option value="Google Shopping">Google Shopping</option>
                    <option value="Other" selected>Other</option>
                </select>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">URL *</label>
                <input type="url" class="link-url w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white" required>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Price</label>
                <input type="text" class="link-price w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white" placeholder="e.g., $29.99">
            </div>
        </div>
    `;
            container.appendChild(linkItem);
            linkCounter++;
            updateLinkNumbers();
        }

        function removeLink(button) {
            const linkItem = button.closest('.link-item');
            linkItem.remove();
            updateLinkNumbers();
        }

        function updateLinkNumbers() {
            const linkItems = document.querySelectorAll('.link-item');
            linkItems.forEach((item, index) => {
                item.querySelector('.link-number').textContent = index + 1;
            });
        }

        // ============================================
        // IMAGE HANDLING FUNCTIONS
        // ============================================

        function showError(message) {
            const errorDiv = document.getElementById('image-error');
            const errorText = document.getElementById('image-error-text');
            errorText.textContent = message;
            errorDiv.classList.remove('hidden');

            setTimeout(() => {
                errorDiv.classList.add('hidden');
            }, 5000);
        }

        function hideError() {
            const errorDiv = document.getElementById('image-error');
            errorDiv.classList.add('hidden');
        }

        function showLoading() {
            document.getElementById('image-loading').classList.remove('hidden');
        }

        function hideLoading() {
            document.getElementById('image-loading').classList.add('hidden');
        }

        function addImageInput() {
            const totalImages = getTotalVisibleImages();

            if (totalImages >= 5) {
                showError('Maximum 5 images allowed');
                return;
            }

            const input = document.getElementById('new-images-input');
            input.value = '';
            input.click();
        }

        function getTotalVisibleImages() {
            return document.querySelectorAll('.image-item:not(.removed)').length;
        }

        document.getElementById('new-images-input').addEventListener('change', async function(e) {
            const files = Array.from(e.target.files);
            if (files.length === 0) return;

            hideError();
            showLoading();

            const currentImageCount = getTotalVisibleImages();
            const remainingSlots = 5 - currentImageCount;

            if (files.length > remainingSlots) {
                hideLoading();
                showError(`You can only add ${remainingSlots} more image(s). Maximum is 5 images total.`);
                this.value = '';
                return;
            }

            const container = document.getElementById('images-container');
            let processedCount = 0;

            for (const file of files) {
                const validation = validateImageFile(file);
                if (!validation.valid) {
                    showError(validation.error);
                    continue;
                }

                try {
                    const dataUrl = await readFileAsDataURL(file);
                    const fileIndex = newImageFiles.length;
                    newImageFiles.push(file);
                    newImagePreviews.push(dataUrl);

                    const imageItem = createNewImagePreview(file, dataUrl, fileIndex);
                    container.appendChild(imageItem);

                    processedCount++;
                } catch (error) {
                    console.error('Error processing file:', error);
                    showError(`Error processing file "${file.name}"`);
                }
            }

            hideLoading();

            if (processedCount > 0) {
                updateImageCount();
            }

            this.value = '';
            debugLog('New images added. Total new images:', newImageFiles.length);
        });

        function validateImageFile(file) {
            const maxSize = 10 * 1024 * 1024;
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];

            if (file.size > maxSize) {
                return {
                    valid: false,
                    error: `File "${file.name}" is too large. Maximum size is 10MB.`
                };
            }

            if (!allowedTypes.includes(file.type)) {
                return {
                    valid: false,
                    error: `File "${file.name}" is not a valid image type. Allowed: JPEG, PNG, GIF.`
                };
            }

            return { valid: true };
        }

        function readFileAsDataURL(file) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.onload = (e) => resolve(e.target.result);
                reader.onerror = (e) => reject(new Error('Failed to read file'));
                reader.readAsDataURL(file);
            });
        }

        function createNewImagePreview(file, dataUrl, fileIndex) {
            const imageItem = document.createElement('div');
            imageItem.className = 'image-item new-image relative border-2 border-green-200 rounded-lg overflow-hidden bg-green-50';
            imageItem.setAttribute('data-file-index', fileIndex);
            imageItem.innerHTML = `
        <div class="flex items-center gap-4 p-3">
            <div class="w-24 h-24 sm:w-32 sm:h-32 flex-shrink-0 bg-gray-100 rounded-lg overflow-hidden">
                <img src="${dataUrl}" alt="New image" class="w-full h-full object-cover">
            </div>
            <div class="flex-1 py-2">
                <p class="text-sm font-medium text-gray-700 mb-1 truncate">${escapeHtml(file.name)}</p>
                <p class="text-xs text-green-600 font-medium">
                    <i class="fas fa-plus-circle mr-1"></i>New image
                </p>
                <p class="text-xs text-gray-500">${formatFileSize(file.size)}</p>
            </div>
            <button type="button" onclick="removeImage(this)" class="flex-shrink-0 p-2 bg-red-500 text-white rounded-full hover:bg-red-600 transition-colors" title="Remove image">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>
    `;
            return imageItem;
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function formatFileSize(bytes) {
            if (bytes < 1024) return bytes + ' B';
            if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
            return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
        }

        function removeImage(button) {
            const imageItem = button.closest('.image-item');
            const totalImages = getTotalVisibleImages();

            if (totalImages <= 1) {
                showError('At least one image is required');
                return;
            }

            if (imageItem.classList.contains('new-image')) {
                const fileIndex = parseInt(imageItem.getAttribute('data-file-index'));

                if (!isNaN(fileIndex) && fileIndex >= 0 && fileIndex < newImageFiles.length) {
                    newImageFiles.splice(fileIndex, 1);
                    newImagePreviews.splice(fileIndex, 1);

                    document.querySelectorAll('.new-image').forEach((item) => {
                        const oldIndex = parseInt(item.getAttribute('data-file-index'));
                        if (oldIndex > fileIndex) {
                            item.setAttribute('data-file-index', oldIndex - 1);
                        }
                    });
                }

                imageItem.remove();
                updateImageCount();
                debugLog('New image removed. Remaining new images:', newImageFiles.length);
                return;
            }

            if (imageItem.classList.contains('existing-image')) {
                const imageId = imageItem.getAttribute('data-image-id');

                const imageUrl = imageItem.querySelector('img').src;
                const filenameElement = imageItem.querySelector('.flex-1.py-2 .text-sm.font-medium');
                const imageFilename = filenameElement ? filenameElement.textContent.trim() : 'Image';

                imageItem.classList.add('removed');
                imageItem.style.display = 'none';

                addToRemovedImages(imageId, imageUrl, imageFilename, imageItem);
                updateImageCount();
                debugLog('Existing image removed:', imageId);
            }
        }

        function addToRemovedImages(imageId, imageUrl, imageFilename, imageItem) {
            const removedContainer = document.getElementById('removed-images-container');
            const removedList = document.getElementById('removed-images-list');

            removedContainer.classList.remove('hidden');

            const restoreBtn = document.createElement('button');
            restoreBtn.type = 'button';
            restoreBtn.className = 'inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors text-sm';
            restoreBtn.setAttribute('data-image-id', imageId);
            restoreBtn.innerHTML = `
        <img src="${imageUrl}" alt="${escapeHtml(imageFilename)}" class="w-12 h-12 object-cover rounded">
        <span class="text-gray-700 max-w-[150px] truncate">${escapeHtml(imageFilename)}</span>
        <i class="fas fa-undo text-indigo-600"></i>
    `;

            restoreBtn.imageItem = imageItem;

            restoreBtn.addEventListener('click', function() {
                restoreImage(this, imageId);
            });

            removedList.appendChild(restoreBtn);
        }

        function restoreImage(restoreBtn, imageId) {
            const imageItem = restoreBtn.imageItem;

            const totalImages = getTotalVisibleImages();
            if (totalImages >= 5) {
                showError('Cannot restore: maximum 5 images allowed');
                return;
            }

            imageItem.classList.remove('removed');
            imageItem.style.display = '';

            restoreBtn.remove();

            const removedList = document.getElementById('removed-images-list');
            if (removedList.children.length === 0) {
                document.getElementById('removed-images-container').classList.add('hidden');
            }

            updateImageCount();
            debugLog('Image restored:', imageId);
        }

        function updateImageCount() {
            const totalImages = getTotalVisibleImages();

            const imageCountEl = document.getElementById('image-count');
            const currentImageCountEl = document.getElementById('current-image-count');

            if (imageCountEl) imageCountEl.textContent = totalImages;
            if (currentImageCountEl) currentImageCountEl.textContent = totalImages;

            const addBtn = document.getElementById('add-image-btn');
            const maxMsg = document.getElementById('max-images-message');

            if (totalImages >= 5) {
                if (addBtn) addBtn.classList.add('hidden');
                if (maxMsg) maxMsg.classList.remove('hidden');
            } else {
                if (addBtn) addBtn.classList.remove('hidden');
                if (maxMsg) maxMsg.classList.add('hidden');
            }
        }

        // ============================================
        // FORM SUBMISSION HANDLER
        // ============================================

        document.getElementById('edit-style-form').addEventListener('submit', function(e) {
            e.preventDefault();

            debugLog('=== FORM SUBMISSION STARTED ===');

            hideError();

            // Validate images
            const totalImages = getTotalVisibleImages();

            if (totalImages < 1) {
                showError('At least one image is required');
                window.scrollTo({ top: 0, behavior: 'smooth' });
                return false;
            }

            if (totalImages > 5) {
                showError('Maximum 5 images allowed');
                window.scrollTo({ top: 0, behavior: 'smooth' });
                return false;
            }

            debugLog('Image validation passed. Total images:', totalImages);

            // Build FormData manually
            const formData = new FormData();

            // Add CSRF token
            const csrfToken = document.querySelector('input[name="_token"]');
            if (csrfToken) {
                formData.append('_token', csrfToken.value);
                debugLog('Added _token');
            } else {
                debugLog('WARNING: _token not found!');
            }

            // Add _method for Laravel's method spoofing (PUT request)
            formData.append('_method', 'PUT');
            debugLog('Added _method: PUT');

            // Add description
            const description = document.getElementById('edit-description');
            if (description) {
                formData.append('description', description.value);
                debugLog('Added description:', description.value.substring(0, 50) + '...');
            }

            // Add tags from the global Set
            if (window.selectedStyleTags && window.selectedStyleTags.size > 0) {
                const tagsArray = Array.from(window.selectedStyleTags);
                debugLog('Adding tags:', tagsArray);
                tagsArray.forEach(tag => {
                    formData.append('tags[]', tag);
                });
            } else {
                debugLog('No tags selected');
            }

            // Add keep_images[] - only for visible existing images
            const visibleExistingImages = document.querySelectorAll('.image-item.existing-image:not(.removed)');
            debugLog('Visible existing images count:', visibleExistingImages.length);

            const keepImageIds = [];
            visibleExistingImages.forEach((imageItem) => {
                const imageId = imageItem.getAttribute('data-image-id');
                if (imageId) {
                    keepImageIds.push(imageId);
                    formData.append('keep_images[]', imageId);
                }
            });
            debugLog('keep_images[] added:', keepImageIds);

            // Add new image files
            debugLog('New images to upload:', newImageFiles.length);
            newImageFiles.forEach((file, index) => {
                formData.append('new_images[]', file);
                debugLog(`new_images[${index}]:`, file.name, formatFileSize(file.size));
            });

            // Add links data
            const linkItems = document.querySelectorAll('.link-item');
            let linkIndex = 0;

            debugLog('Processing links. Link items found:', linkItems.length);

            linkItems.forEach((item, itemIndex) => {
                const linkIdInput = item.querySelector('.link-id');
                const titleInput = item.querySelector('.link-title');
                const urlInput = item.querySelector('.link-url');
                const platformInput = item.querySelector('.link-platform');
                const priceInput = item.querySelector('.link-price');

                if (!titleInput || !urlInput) {
                    debugLog(`Link ${itemIndex}: missing input elements`);
                    return;
                }

                const linkId = linkIdInput ? linkIdInput.value : '';
                const title = titleInput.value.trim();
                const url = urlInput.value.trim();
                const platform = platformInput ? platformInput.value : 'Other';
                const price = priceInput ? priceInput.value.trim() : '';

                if (title && url) {
                    if (linkId) {
                        formData.append(`links[${linkIndex}][id]`, linkId);
                    }
                    formData.append(`links[${linkIndex}][title]`, title);
                    formData.append(`links[${linkIndex}][url]`, url);
                    formData.append(`links[${linkIndex}][platform]`, platform);
                    if (price) {
                        formData.append(`links[${linkIndex}][price]`, price);
                    }
                    debugLog(`Link ${linkIndex} added:`, { linkId, title: title.substring(0, 30), platform });
                    linkIndex++;
                } else {
                    debugLog(`Link ${itemIndex}: skipped (empty title or url)`);
                }
            });

            debugLog('Total links added to FormData:', linkIndex);

            // Disable submit button
            const submitBtn = document.getElementById('submit-btn');
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Saving...';

            // Debug: Log complete FormData contents
            if (DEBUG_MODE) {
                debugLog('=== COMPLETE FORMDATA CONTENTS ===');
                let counts = { tags: 0, keep_images: 0, new_images: 0, links: 0 };

                for (let [key, value] of formData.entries()) {
                    if (key.startsWith('tags')) counts.tags++;
                    if (key.startsWith('keep_images')) counts.keep_images++;
                    if (key.startsWith('new_images')) counts.new_images++;
                    if (key.startsWith('links')) counts.links++;

                    if (value instanceof File) {
                        debugLog(`${key}: [File] ${value.name} (${formatFileSize(value.size)})`);
                    } else {
                        debugLog(`${key}: ${value}`);
                    }
                }

                debugLog('=== SUMMARY ===');
                debugLog('Total tags:', counts.tags);
                debugLog('Total keep_images:', counts.keep_images);
                debugLog('Total new_images:', counts.new_images);
                debugLog('Total link fields:', counts.links);
                debugLog('===================');
            }

            // Submit via fetch
            debugLog('Submitting to:', this.action);

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            })
                .then(response => {
                    debugLog('Response status:', response.status);
                    debugLog('Response redirected:', response.redirected);

                    if (response.redirected) {
                        debugLog('Following redirect to:', response.url);
                        window.location.href = response.url;
                        return null;
                    }

                    const contentType = response.headers.get('content-type');
                    if (contentType && contentType.includes('application/json')) {
                        return response.json();
                    }

                    if (response.ok) {
                        debugLog('Success response (non-JSON), redirecting');
                        window.location.href = this.action.replace('/update', '');
                        return null;
                    }

                    throw new Error('Unexpected response type');
                })
                .then(data => {
                    if (!data) return;

                    debugLog('Response data:', data);

                    if (data.success) {
                        debugLog('Success! Redirecting to:', data.redirect);
                        window.location.href = data.redirect;
                    } else if (data.errors) {
                        debugLog('Validation errors:', data.errors);
                        let errorMsg = '';
                        Object.keys(data.errors).forEach(field => {
                            const errors = data.errors[field];
                            if (Array.isArray(errors)) {
                                errorMsg += errors.join(', ') + ' ';
                            } else {
                                errorMsg += errors + ' ';
                            }
                        });
                        showError(errorMsg.trim() || 'Validation failed');
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnText;
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    } else if (data.message) {
                        debugLog('Error message:', data.message);
                        showError(data.message);
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnText;
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    debugLog('ERROR:', error);
                    showError('An error occurred while saving. Please try again.');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;
                });

            return false;
        });

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateImageCount();
            debugLog('Initialization complete');
            debugLog('Initial state - Tags:', window.selectedStyleTags ? Array.from(window.selectedStyleTags) : 'not set');

            // Check existing images and their IDs
            const existingImages = document.querySelectorAll('.image-item.existing-image');
            debugLog('Initial state - Total existing images:', existingImages.length);

            let imagesWithoutIds = [];
            existingImages.forEach((img, i) => {
                const imageId = img.getAttribute('data-image-id');
                debugLog(`  Existing image ${i}: ID="${imageId}"`);
                if (!imageId || imageId === '' || imageId === 'null') {
                    imagesWithoutIds.push(i);
                }
            });

            if (imagesWithoutIds.length > 0) {
                console.error('⚠️ WARNING: Images without valid IDs found at indices:', imagesWithoutIds);
                console.error('These images will NOT be included in keep_images[]');
                showError(`Warning: ${imagesWithoutIds.length} image(s) have missing IDs. Please contact support.`);
            }

            debugLog('Initial state - Links:', document.querySelectorAll('.link-item').length);
        });
    </script>
@endsection
