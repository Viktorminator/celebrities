@extends('layout')

@section('title', 'My Styles - Glamdar')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-display font-bold text-indigo-900 mb-2">My Styles</h1>
                    <p class="text-gray-600">View and manage all your fashion style posts</p>
                </div>
                <a href="{{ route('home') }}" class="bg-pink-500 text-white px-6 py-2 rounded-full text-sm font-medium hover:bg-pink-600 transition-colors flex items-center gap-2">
                    <i class="fas fa-plus"></i>
                    Add New Style
                </a>
            </div>
        </div>

        @if(isset($limitReached) && $limitReached && isset($styleLimit) && $styleLimit !== null)
            <div class="mb-6 rounded-2xl border border-red-100 bg-red-50 p-6">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-red-500 text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-red-700 mb-2">Style limit reached</h3>
                        <p class="text-sm text-red-600 mb-4">
                            You’ve added {{ $styleLimit }} styles with your current plan. Upgrade to add more looks, unlock advanced analytics, and share unlimited styles.
                        </p>
                        <a href="{{ route('subscriptions') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-red-500 text-white rounded-full text-sm font-medium hover:bg-red-600 transition-colors">
                            View Subscription Plans <i class="fas fa-arrow-right text-xs"></i>
                        </a>
                    </div>
                </div>
            </div>
        @endif

        @if($styles->count() > 0)
            <!-- Grid View Toggle -->
            <div class="mb-6 flex items-center justify-between">
                <p class="text-sm text-gray-600">
                    Showing {{ $styles->firstItem() }} - {{ $styles->lastItem() }} of {{ $styles->total() }} styles
                </p>
                <div class="flex gap-2">
                    <button id="grid-view-btn" class="px-3 py-1 bg-indigo-600 text-white rounded-lg text-sm font-medium">
                        <i class="fas fa-th mr-1"></i>Grid
                    </button>
                    <button id="table-view-btn" class="px-3 py-1 bg-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-300">
                        <i class="fas fa-list mr-1"></i>Table
                    </button>
                </div>
            </div>

            <!-- Grid View -->
            <div id="grid-view" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($styles as $style)
                    <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-200">
                        <!-- Image -->
                        <div class="relative h-64 bg-gray-100">
                            <img src="{{ $style->image_url }}" alt="Style image" class="w-full h-full object-cover" loading="lazy">
                            <div class="absolute top-2 right-2">
                                <button type="button" onclick="showDeleteModal({{ $style->id }})" class="bg-red-500 text-white p-2 rounded-full hover:bg-red-600 transition-colors">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </div>
                            @if($style->status === 'processing')
                                <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                                    <div class="text-center text-white">
                                        <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                                        <p class="text-sm">Processing...</p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Content -->
                        <div class="p-4">
                            <!-- Tags -->
                            @if($style->analysis_metadata && isset($style->analysis_metadata['user_tags']) && count($style->analysis_metadata['user_tags']) > 0)
                                <div class="flex flex-wrap gap-1 mb-3">
                                    @foreach(array_slice($style->analysis_metadata['user_tags'], 0, 3) as $tag)
                                        <span class="px-2 py-1 bg-indigo-100 text-indigo-600 rounded-full text-xs font-medium">{{ $tag }}</span>
                                    @endforeach
                                    @if(count($style->analysis_metadata['user_tags']) > 3)
                                        <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded-full text-xs font-medium">+{{ count($style->analysis_metadata['user_tags']) - 3 }}</span>
                                    @endif
                                </div>
                            @endif

                            <!-- Description -->
                            @if($style->analysis_metadata && isset($style->analysis_metadata['description']))
                                <p class="text-sm text-gray-700 mb-3 line-clamp-2">{{ $style->analysis_metadata['description'] }}</p>
                            @endif

                            <!-- Links Count -->
                            @php
                                $linksCount = $style->productLinks->count();
                            @endphp
                            @if($linksCount > 0)
                                <div class="flex items-center text-sm text-indigo-600 mb-3">
                                    <i class="fas fa-link mr-2"></i>
                                    <span>{{ $linksCount }} {{ $linksCount === 1 ? 'link' : 'links' }}</span>
                                </div>
                            @endif

                            <!-- Date -->
                            <div class="flex items-center justify-between text-xs text-gray-500">
                                <span>{{ $style->created_at->format('M d, Y') }}</span>
                                <a href="{{ route('styles.show', $style->id) }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                                    View Details <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Table View -->
            <div id="table-view" class="hidden bg-white rounded-xl shadow-md overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tags</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Links</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($styles as $style)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <img src="{{ $style->image_url }}" alt="Style" class="h-20 w-20 object-cover rounded-lg">
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($style->analysis_metadata && isset($style->analysis_metadata['user_tags']) && count($style->analysis_metadata['user_tags']) > 0)
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($style->analysis_metadata['user_tags'] as $tag)
                                                    <span class="px-2 py-1 bg-indigo-100 text-indigo-600 rounded-full text-xs font-medium">{{ $tag }}</span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-gray-400 text-sm">No tags</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($style->analysis_metadata && isset($style->analysis_metadata['description']))
                                            <p class="text-sm text-gray-700 max-w-xs truncate">{{ $style->analysis_metadata['description'] }}</p>
                                        @else
                                            <span class="text-gray-400 text-sm">No description</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $links = $style->productLinks;
                                        @endphp
                                        @if($links->count() > 0)
                                            <div class="space-y-1">
                                                @foreach($links->take(3) as $link)
                                                    <a href="#" onclick="trackLinkClick({{ $link->id }}, '{{ $link->url }}'); return false;" target="_blank" rel="noopener noreferrer" class="block text-xs text-indigo-600 hover:text-indigo-800 truncate max-w-xs">
                                                        <i class="fas fa-external-link-alt mr-1"></i>{{ strlen($link->url) > 40 ? substr($link->url, 0, 40) . '...' : $link->url }}
                                                        <span class="text-gray-500 ml-1">({{ $link->visits ?? 0 }})</span>
                                                    </a>
                                                @endforeach
                                                @if($links->count() > 3)
                                                    <span class="text-xs text-gray-500">+{{ $links->count() - 3 }} more</span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-gray-400 text-sm">No links</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $style->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('styles.show', $style->id) }}" class="text-indigo-600 hover:text-indigo-900">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button type="button" onclick="showDeleteModal({{ $style->id }})" class="text-red-600 hover:text-red-900">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $styles->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-white rounded-xl shadow-md p-12 text-center">
                <i class="fas fa-images text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No styles yet</h3>
                <p class="text-gray-600 mb-6">Start sharing your fashion styles with the community!</p>
                <a href="{{ route('home') }}" class="inline-block bg-pink-500 text-white px-6 py-3 rounded-full font-medium hover:bg-pink-600 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Add Your First Style
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="delete-modal" class="fixed inset-0 bg-black bg-opacity-60 hidden z-[1000] flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl transform transition-all duration-300">
        <div class="p-6">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0 w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mr-4">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Delete Style</h3>
                    <p class="text-sm text-gray-500">This action cannot be undone</p>
                </div>
            </div>
            <p class="text-gray-700 mb-6">Are you sure you want to delete this style? All associated links and data will be permanently removed.</p>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 text-sm font-medium transition-colors">
                    Cancel
                </button>
                <form id="delete-form" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 text-sm font-medium transition-colors">
                        <i class="fas fa-trash mr-2"></i>Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const gridView = document.getElementById('grid-view');
    const tableView = document.getElementById('table-view');
    const gridViewBtn = document.getElementById('grid-view-btn');
    const tableViewBtn = document.getElementById('table-view-btn');

    if (gridViewBtn && tableViewBtn) {
        gridViewBtn.addEventListener('click', () => {
            gridView.classList.remove('hidden');
            tableView.classList.add('hidden');
            gridViewBtn.classList.add('bg-indigo-600', 'text-white');
            gridViewBtn.classList.remove('bg-gray-200', 'text-gray-700');
            tableViewBtn.classList.remove('bg-indigo-600', 'text-white');
            tableViewBtn.classList.add('bg-gray-200', 'text-gray-700');
        });

        tableViewBtn.addEventListener('click', () => {
            tableView.classList.remove('hidden');
            gridView.classList.add('hidden');
            tableViewBtn.classList.add('bg-indigo-600', 'text-white');
            tableViewBtn.classList.remove('bg-gray-200', 'text-gray-700');
            gridViewBtn.classList.remove('bg-indigo-600', 'text-white');
            gridViewBtn.classList.add('bg-gray-200', 'text-gray-700');
        });
    }
});

// Delete Modal Functions
function showDeleteModal(styleId) {
    const modal = document.getElementById('delete-modal');
    const form = document.getElementById('delete-form');
    form.action = `/styles/${styleId}`;
    modal.classList.remove('hidden');
}

function closeDeleteModal() {
    const modal = document.getElementById('delete-modal');
    modal.classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('delete-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteModal();
    }
});

// Track link clicks
async function trackLinkClick(productLinkId, url) {
    try {
        const response = await fetch(`/api/product-links/${productLinkId}/track`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        const data = await response.json();

        if (data.success) {
            // Update the visits count in the UI if element exists
            const visitsElement = document.getElementById(`visits-${productLinkId}`);
            if (visitsElement) {
                visitsElement.textContent = `(${data.visits})`;
            }
            // Open the link in a new tab
            window.open(url, '_blank', 'noopener,noreferrer');
        } else {
            // If tracking fails, still open the link
            window.open(url, '_blank', 'noopener,noreferrer');
        }
    } catch (error) {
        // If tracking fails, still open the link
        window.open(url, '_blank', 'noopener,noreferrer');
    }
}
</script>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endsection

