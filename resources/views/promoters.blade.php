<x-guest-layout>
  <!-- Hero Section -->
  <div class="mx-auto min-h-screen max-w-7xl pb-20">
    <div class="px-2 pt-28 md:px-4 md:pt-36 lg:px-8">
      <!-- Hero Section -->
      <div class="relative mb-4 overflow-hidden rounded-2xl bg-yns_dark_blue shadow-2xl md:mb-8">
        <h1 class="mb-4 mt-8 text-center font-heading text-4xl font-bold text-white md:text-5xl lg:text-6xl">
          Find Your Next <span class="text-yns_yellow">Promoter</span>
        </h1>

        <div class="mt-4 space-y-4 p-6">
          <!-- Search Box - Full width on all screens -->
          <div class="w-full">
            <div class="relative">
              <input type="text" id="promoter-search" placeholder="Search by promoter name or location..."
                class="w-full rounded-lg border border-gray-700 bg-black/50 p-4 pl-12 text-white placeholder-gray-400 backdrop-blur-sm">
              <span class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></span>
            </div>
          </div>

          <!-- Filter Toggle Button - Only visible on mobile -->
          <button id="filter-toggle"
            class="flex w-full items-center justify-between rounded-lg border border-gray-700 bg-black/50 p-4 text-white md:hidden">
            <span>Show Filters</span>
            <span class="fas fa-filter"></span>
          </button>

          <!-- Filters Container -->
          <div id="filters-container" class="hidden space-y-4 md:grid md:grid-cols-3 md:gap-4 md:space-y-0">
            <!-- Band Type Filter -->
            <div class="rounded-lg border border-gray-700 bg-black/50 p-4 backdrop-blur-sm">
              <h3 class="flex items-center justify-between font-heading text-lg font-bold text-white">
                Band Type
                <span class="fas fa-chevron-down text-sm md:hidden"></span>
              </h3>
              <div class="filter-content mt-4 hidden md:block">
                <div class="max-h-[300px] space-y-2 overflow-y-auto pr-2 md:max-h-[120px] lg:max-h-[300px]">
                  @foreach ($bandTypes as $type)
                    <label class="flex items-center gap-2 text-gray-300">
                      <input type="checkbox" name="band_type[]" value="{{ $type }}"
                        class="filter-checkbox rounded border-gray-600 bg-gray-700 text-yns_yellow">
                      <span>{{ Str::title(str_replace('-', ' ', $type)) }}</span>
                    </label>
                  @endforeach
                </div>
              </div>
            </div>

            <!-- Genre Filter -->
            <div class="rounded-lg border border-gray-700 bg-black/50 p-4 backdrop-blur-sm">
              <h3 class="flex items-center justify-between font-heading text-lg font-bold text-white">
                Genres
                <span class="fas fa-chevron-down text-sm md:hidden"></span>
              </h3>
              <div class="filter-content mt-4 hidden md:block">
                <div class="max-h-[300px] overflow-y-auto pr-2 md:max-h-[120px] lg:max-h-[300px]">
                  <div class="grid grid-cols-1 gap-y-2">
                    @foreach ($genres as $genre)
                      <label class="flex items-center gap-2 py-1 text-gray-300">
                        <input type="checkbox" name="genres[]" value="{{ $genre['name'] ?? $genre }}"
                          class="genre-checkbox rounded border-gray-600 bg-gray-700 text-yns_yellow">
                        <span class="truncate">{{ Str::title(str_replace('-', ' ', $genre['name'] ?? $genre)) }}</span>
                      </label>
                    @endforeach
                  </div>
                </div>
              </div>
            </div>

            <!-- Locations Filter -->
            <div class="rounded-lg border border-gray-700 bg-black/50 p-4 backdrop-blur-sm">
              <h3 class="flex items-center justify-between font-heading text-lg font-bold text-white">
                Locations
                <span class="fas fa-chevron-down text-sm md:hidden"></span>
              </h3>
              <div class="filter-content mt-4 hidden md:block">
                <div class="max-h-[300px] overflow-y-auto pr-2 md:max-h-[120px] lg:max-h-[300px]">
                  @foreach ($locations as $location)
                    <label class="location-item flex items-center gap-2 py-1 text-gray-300">
                      <input type="checkbox" name="locations[]" value="{{ $location }}"
                        class="location-checkbox rounded border-gray-600 bg-gray-700 text-yns_yellow">
                      <span class="truncate">{{ $location }}</span>
                    </label>
                  @endforeach
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Results Section -->
    <div class="mx-2 overflow-hidden rounded-lg shadow-xl">
      <div class="hidden overflow-x-auto md:block">
        <div class="hidden overflow-x-auto md:block">
          <table class="min-w-full divide-y divide-gray-800">
            <thead>
              <tr>
                <th class="sortable px-6 py-3 text-left" data-sort="name">
                  <button
                    class="flex items-center gap-2 font-heading text-sm font-medium uppercase tracking-wider text-gray-400">
                    Promoter Name
                    <span class="fas fa-sort"></span>
                  </button>
                </th>
                <th class="sortable px-6 py-3 text-left" data-sort="rating">
                  <button
                    class="flex items-center gap-2 font-heading text-sm font-medium uppercase tracking-wider text-gray-400">
                    Rating
                    <span class="fas fa-sort"></span>
                  </button>
                </th>
                <th class="sortable px-6 py-3 text-left" data-sort="location">
                  <button
                    class="flex items-center gap-2 font-heading text-sm font-medium uppercase tracking-wider text-gray-400">
                    Location
                    <span class="fas fa-sort"></span>
                  </button>
                </th>
                <th class="sortable px-6 py-3 text-left" data-sort="genres">
                  <button
                    class="flex items-center gap-2 font-heading text-sm font-medium uppercase tracking-wider text-gray-400">
                    Genres
                    <span class="fas fa-sort"></span>
                  </button>
                </th>
                <th class="px-6 py-3 text-left">
                  <span class="font-heading text-sm font-medium uppercase tracking-wider text-gray-400">
                    Contact
                  </span>
                </th>
              </tr>
            </thead>
            <tbody id="resultsTableBody" class="divide-y divide-gray-800">
              @foreach ($promoters as $promoter)
                <tr class="hover:bg-black/20">
                  <td class="px-6 py-4">
                    <a href="/promoters/{{ Str::slug($promoter['name']) }}"
                      class="font-medium text-white hover:text-yns_yellow">
                      {{ $promoter['name'] }}
                      @if ($promoter['is_verified'])
                        <span
                          class="ml-2 inline-flex items-center rounded-full bg-yns_yellow/10 px-2 py-1 text-xs text-yns_yellow">
                          <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                            <path
                              d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" />
                          </svg>
                          Verified
                        </span>
                      @endif
                    </a>
                  </td>
                  <td class="px-6 py-4 text-gray-300">
                    @if ($promoter['average_rating'])
                      <div class="rating-wrapper flex items-center">
                        {!! $promoter['rating_icons'] !!}
                      </div>
                    @else
                      Not rated
                    @endif
                  </td>
                  <td class="px-6 py-4 text-gray-300">
                    {{ $promoter['postal_town'] ?: 'Location not specified' }}
                  </td>
                  <td class="px-6 py-4">
                    <button onclick="showGenresModal({{ json_encode(array_keys((array) $promoter['genres'])) }})"
                      class="inline-flex items-center gap-2 rounded-lg bg-black/20 px-3 py-1.5 text-sm text-gray-300 hover:bg-black/40">
                      <span class="fas fa-music"></span>
                      View Genres
                    </button>
                  </td>
                  <td class="px-6 py-4">
                    <button
                      onclick="showContactModal({{ json_encode([
                          'name' => $promoter['name'],
                          'preferred_contact' => $promoter['preferred_contact'],
                          'contact_email' => $promoter['contact_email'],
                          'contact_number' => $promoter['contact_number'],
                          'platforms' => $promoter['platforms'],
                      ]) }})"
                      class="inline-flex items-center gap-2 rounded-lg bg-yns_yellow px-4 py-2 text-sm font-medium text-black transition-all hover:bg-yellow-400">
                      Contact Options
                    </button>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>

      <!-- Mobile View Section -->
      <div class="block md:hidden">
        @foreach ($promoters as $promoter)
          <div class="pt-4 first:pt-0">
            <!-- Main Card Content -->
            <div class="relative rounded-lg bg-yns_dark_blue/90 p-4 backdrop-blur-sm">
              <!-- Promoter Name and Verified Badge -->
              <div class="mb-4 flex items-center gap-4">
                <a href="/promoters/{{ Str::slug($promoter['name']) }}"
                  class="block font-heading text-xl font-bold text-white hover:text-yns_yellow">
                  {{ $promoter['name'] }}
                </a>
                @if ($promoter['is_verified'])
                  <span
                    class="mt-1 inline-flex items-center rounded-full bg-yns_yellow/10 px-2 py-1 text-xs text-yns_yellow">
                    <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                      <path
                        d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" />
                    </svg>
                    Verified
                  </span>
                @endif
              </div>

              <!-- Promoter Details Grid -->
              <div class="mb-4 grid grid-cols-2 gap-4">
                <div class="rounded-lg bg-black/20 p-3">
                  <span class="text-sm text-gray-400">Rating</span>
                  <div class="mt-1 flex items-center">
                    <span class="text-lg font-bold text-white">
                      @if ($promoter['average_rating'])
                        <div class="rating-wrapper flex items-center">
                          {!! $promoter['rating_icons'] !!}
                        </div>
                      @else
                        Not rated
                      @endif
                    </span>
                  </div>
                </div>

                <div class="rounded-lg bg-black/20 p-3">
                  <span class="text-sm text-gray-400">Genres</span>
                  <div>
                    <button onclick="showGenresModal({{ json_encode(array_keys((array) $promoter['genres'])) }})"
                      class="inline-flex items-center gap-2 rounded-lg bg-black/40 px-3 py-1.5 text-white hover:bg-black/60">
                      View Genres
                    </button>
                  </div>
                </div>
              </div>

              <!-- Location -->
              <div class="mb-4 rounded-lg bg-black/20 p-3">
                <span class="text-sm text-gray-400">Location</span>
                <span class="ml-2 text-white">{{ $promoter['postal_town'] ?: 'Location not specified' }}</span>
              </div>

              <!-- Contact Button -->
              <button
                onclick="showContactModal({{ json_encode([
                    'name' => $promoter['name'],
                    'preferred_contact' => $promoter['preferred_contact'],
                    'contact_email' => $promoter['contact_email'],
                    'contact_number' => $promoter['contact_number'],
                    'platforms' => $promoter['platforms'],
                ]) }})"
                class="w-full rounded-lg bg-yns_yellow py-3 text-center font-medium text-black transition-all hover:bg-yellow-400">
                <span class="fas fa-envelope mr-2"></span>
                Contact Promoter
              </button>
            </div>
          </div>
        @endforeach
      </div>

      <div class="mt-4 bg-yns_dark_blue px-6 py-4" id="pagination-container">
        <!-- Mobile Pagination (hidden on desktop) -->
        <div class="block md:hidden">
          <div class="text-center text-sm text-gray-400">
            {{ $promoters->firstItem() }} - {{ $promoters->lastItem() }} of {{ $promoters->total() }} promoters
          </div>
          {{ $promoters->links('components.mobile-pagination') }}
        </div>

        <!-- Desktop Pagination (hidden on mobile) -->
        <div class="hidden md:block">
          {{ $promoters->links('components.pagination') }}
        </div>
      </div>

      {{-- Contact Modal --}}
      <div id="contactModal" class="fixed inset-0 z-50 hidden">
        <div class="fixed inset-0 flex items-center justify-center p-4">
          {{-- Background overlay --}}
          <div class="fixed inset-0 bg-black bg-opacity-75 transition-opacity" aria-hidden="true"></div>

          {{-- Modal panel --}}
          <div
            class="relative w-full max-w-lg transform overflow-hidden rounded-lg bg-black text-left shadow-xl transition-all">
            <div class="bg-black px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
              <div class="sm:flex sm:items-start">
                <div class="mt-3 w-full text-center sm:ml-4 sm:mt-0 sm:text-left">
                  <h3 class="mb-4 font-heading text-2xl font-bold text-white" id="modalTitle"></h3>
                  <div class="mt-4 space-y-4">
                    <div id="preferredContact" class="rounded-lg border border-yns_yellow bg-black/40 p-4">
                      {{-- Preferred contact will be inserted here --}}
                    </div>
                    <div id="otherContacts" class="mt-4 space-y-2">
                      {{-- Other contact methods will be inserted here --}}
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="bg-black/40 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
              <button type="button" onclick="closeContactModal()"
                class="inline-flex w-full justify-center rounded-lg border border-gray-800 bg-black px-4 py-2 text-sm font-medium text-white transition duration-150 ease-in-out hover:bg-gray-900 sm:ml-3 sm:w-auto">
                Close
              </button>
            </div>
          </div>
        </div>
      </div>

      <div id="genresModal" class="fixed inset-0 z-50 hidden overflow-y-auto pt-36">
        <div class="flex min-h-screen items-end justify-center px-4 pb-20 pt-4 text-center sm:block sm:p-0">
          <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-900 opacity-75"></div>
          </div>
          <div
            class="inline-block transform overflow-hidden rounded-lg bg-yns_dark_blue text-left align-bottom shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:align-middle">
            <div class="bg-yns_dark_blue px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
              <div class="sm:flex sm:items-start">
                <div class="mt-3 w-full text-center sm:ml-4 sm:mt-0 sm:text-left">
                  <h3 class="mb-4 text-2xl font-bold leading-6 text-white" id="genresModalTitle">Genres</h3>
                  <div class="mt-4">
                    <div id="genresList" class="grid grid-cols-2 gap-2">
                      <!-- Genres will be inserted here -->
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="bg-yns_dark_blue/50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
              <button type="button" onclick="closeGenresModal()"
                class="mt-3 inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:mt-0 sm:w-auto">
                Close
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
</x-guest-layout>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    let currentSort = {
      field: null,
      direction: 'asc'
    };

    let debounceTimeout;

    // Initialize filters
    const filters = {
      search: '',
      bandTypes: [],
      genres: [],
      locations: [],
    };

    const urlParams = new URLSearchParams(window.location.search);
    const searchQuery = @json($town ?? '');

    if (searchQuery) {
      const searchInput = document.getElementById('promoter-search');
      searchInput.value = searchQuery;
      filters.search = searchQuery;
      applyFilters();
    }

    // Search Input Handler
    document.getElementById('promoter-search').addEventListener('input', function(e) {
      clearTimeout(debounceTimeout);
      debounceTimeout = setTimeout(() => {
        filters.search = e.target.value;
        applyFilters();
      }, 300);
    });

    // Add event listeners for each filter type
    document.querySelectorAll('.filter-checkbox').forEach(checkbox => {
      checkbox.addEventListener('change', function() {
        filters.bandTypes = Array.from(document.querySelectorAll('.filter-checkbox:checked'))
          .map(cb => cb.value);
        applyFilters();
      });
    });

    document.querySelectorAll('.genre-checkbox').forEach(checkbox => {
      checkbox.addEventListener('change', function() {
        filters.genres = Array.from(document.querySelectorAll('.genre-checkbox:checked'))
          .map(cb => cb.value);
        applyFilters();
      });
    });

    document.querySelectorAll('.location-checkbox').forEach(checkbox => {
      checkbox.addEventListener('change', function() {
        filters.locations = Array.from(document.querySelectorAll('.location-checkbox:checked'))
          .map(cb => cb.value);
        applyFilters();
      });
    });

    // Sorting Handlers
    document.querySelectorAll('.sortable').forEach(header => {
      header.addEventListener('click', function() {
        const field = this.dataset.sort;

        if (currentSort.field === field) {
          currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
        } else {
          currentSort.field = field;
          currentSort.direction = 'asc';
        }

        updateSortIcons(this);
        applyFilters();
      });
    });

    // Accordion functionality for mobile filters
    const filterHeaders = document.querySelectorAll('.font-heading');

    filterHeaders.forEach(header => {
      header.addEventListener('click', function() {
        if (window.innerWidth < 768) {
          const content = this.nextElementSibling;
          const icon = this.querySelector('.fas');
          content.classList.toggle('hidden');
          icon.classList.toggle('fa-chevron-down');
          icon.classList.toggle('fa-chevron-up');
        }
      });
    });

    // Reset layout on screen resize
    window.addEventListener('resize', function() {
      if (window.innerWidth >= 768) {
        filtersContainer.classList.remove('hidden');
        document.querySelectorAll('.filter-content').forEach(content => {
          content.classList.remove('hidden');
        });
      }
    });

    function updateSortIcons(clickedHeader) {
      document.querySelectorAll('.sortable .fas').forEach(icon => {
        icon.className = 'fas fa-sort';
      });

      const icon = clickedHeader.querySelector('.fas');
      icon.className = `fas fa-sort-${currentSort.direction === 'asc' ? 'up' : 'down'}`;
    }

    function applyFilters() {
      const tbody = document.getElementById('resultsTableBody');
      const mobileView = document.querySelector('.block.md\\:hidden');
      const loadingHTML = `
        <div class="px-6 py-4 text-center text-gray-400">
          <span class="fas fa-spinner fa-spin mr-2"></span>
          Loading...
        </div>
      `;

      tbody.innerHTML = `<tr><td colspan="5">${loadingHTML}</td></tr>`;
      mobileView.innerHTML = loadingHTML;

      const activeFilters = {
        search: filters.search,
        bandTypes: filters.bandTypes.length > 0 ? filters.bandTypes : null,
        genres: filters.genres.length > 0 ? filters.genres : null,
        locations: filters.locations.length > 0 ? filters.locations : null,
      };

      $.ajax({
        url: '/promoters/filter',
        method: 'POST',
        data: {
          _token: $('meta[name="csrf-token"]').attr('content'),
          filters: activeFilters,
          sort: currentSort
        },
        success: function(response) {
          updateResultsTable(response.results);
          document.getElementById('pagination-container').innerHTML = response.pagination;

          attachPaginationHandlers();
        },
        error: function(error) {
          console.error('Error applying filters:', error);
          const errorHTML = `
          <div class="px-6 py-4 text-center text-red-400">
            <span class="fas fa-exclamation-circle mr-2"></span>
            An error occurred while loading promoters. Please try again.
          </div>
        `;
          tbody.innerHTML = `<tr><td colspan="5">${errorHTML}</td></tr>`;
          mobileView.innerHTML = errorHTML;
        }
      });
    }

    function attachPaginationHandlers() {
      document.querySelectorAll('#pagination-container a').forEach(link => {
        link.addEventListener('click', function(e) {
          e.preventDefault();
          const url = new URL(this.href);
          const page = url.searchParams.get('page');

          $.ajax({
            url: '/promoters/filter',
            method: 'POST',
            data: {
              _token: $('meta[name="csrf-token"]').attr('content'),
              filters: filters,
              sort: currentSort,
              page: page
            },
            success: function(response) {
              updateResultsTable(response.results);
              document.getElementById('pagination-container').innerHTML = response.pagination;
              attachPaginationHandlers();

              // Scroll to top of results
              document.querySelector('.mx-2.overflow-hidden').scrollIntoView({
                behavior: 'smooth'
              });
            },
            error: function(error) {
              console.error('Error fetching page:', error);
            }
          });
        });
      });
    };

    function updateResultsTable(promoters) {
      const tbody = document.getElementById('resultsTableBody');
      const mobileView = document.querySelector('.block.md\\:hidden');

      tbody.innerHTML = '';
      mobileView.innerHTML = '';

      if (!promoters || promoters.length === 0) {
        const noResults =
          `<div class="px-6 py-4 text-center text-gray-400">No promoters found matching your criteria</div>`;
        tbody.innerHTML = `<tr><td colspan="5">${noResults}</td></tr>`;
        mobileView.innerHTML = noResults;
        return;
      }

      promoters.forEach(promoter => {
        const row = document.createElement('tr');
        row.className = 'hover:bg-black/20';

        const verifiedBadge = promoter.is_verified ? `
            <span class="ml-2 inline-flex items-center rounded-full bg-yns_yellow/10 px-2 py-1 text-xs text-yns_yellow">
                <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                </svg>
                Verified
            </span>` : '';

        row.innerHTML = `
            <td class="px-6 py-4">
                <a href="/promoters/${(promoter.name || '').toLowerCase().replace(/\s+/g, '-')}" 
                    class="font-medium text-white hover:text-yns_yellow">
                    ${promoter.name}
                    ${verifiedBadge}
                </a>
            </td>
          <td class="px-6 py-4 text-gray-300">
            <div class="rating-wrapper flex items-center">
                ${promoter.rating_icons || 'Not rated'}
            </div>
          </td>
            <td class="px-6 py-4 text-gray-300">
                ${promoter.postal_town || 'Location not specified'}
            </td>
            <td class="px-6 py-4">
                <button onclick="showGenresModal(${JSON.stringify(Array.isArray(promoter.genres) ? promoter.genres : Object.keys(promoter.genres || {}))})"
                    class="inline-flex items-center gap-2 rounded-lg bg-black/20 px-3 py-1.5 text-sm text-gray-300 hover:bg-black/40">
                    View Genres
                </button>
            </td>
            <td class="px-6 py-4">
                <button onclick='showContactModal(${JSON.stringify({
                    name: promoter.name,
                    preferred_contact: promoter.preferred_contact,
                    contact_email: promoter.contact_email,
                    contact_number: promoter.contact_number,
                    platforms: promoter.platforms || []
                })})'
                    class="inline-flex items-center gap-2 rounded-lg bg-yns_yellow px-4 py-2 text-sm font-medium text-black transition-all hover:bg-yellow-400">
                    Contact Options
                </button>
            </td>
        `;
        tbody.appendChild(row);

        // Mobile card
        const mobileCard = document.createElement('div');
        mobileCard.className = 'p-4';
        mobileCard.innerHTML = `
            <div class="relative rounded-lg bg-yns_dark_blue p-4 backdrop-blur-sm">
                <div class="mb-4">
                    <a href="/promoters/${promoter.name.toLowerCase().replace(/\s+/g, '-')}" 
                        class="block font-heading text-xl font-bold text-white hover:text-yns_yellow">
                        ${promoter.name}
                        ${verifiedBadge}
                    </a>
                </div>

                <div class="mb-4 grid grid-cols-2 gap-4">
                  <div class="rounded-lg bg-black/20 p-3">
                      <span class="text-sm text-gray-400">Rating</span>
                      <div class="mt-1 rating-wrapper flex items-center">
                          ${promoter.rating_icons || 'Not rated'}
                      </div>
                  </div>

                    <div class="rounded-lg bg-black/20 p-3">
                        <span class="text-sm text-gray-400">Genres</span>
                        <div class="mt-1">
                            <button onclick="showGenresModal(${JSON.stringify(Array.isArray(promoter.genres) ? promoter.genres : Object.keys(promoter.genres || {}))})"
                                class="inline-flex items-center gap-2 rounded-lg bg-black/40 px-3 py-1.5 text-white hover:bg-black/60">
                                View Genres
                            </button>
                        </div>
                    </div>
                </div>

                <div class="mb-4 flex items-center rounded-lg bg-black/20 p-3">
                    <span class="text-sm text-gray-400">Location</span>
                    <span class="ml-2 text-white">${promoter.postal_town || 'Location not specified'}</span>
                </div>

                <button onclick='showContactModal(${JSON.stringify({
                    name: promoter.name,
                    preferred_contact: promoter.preferred_contact,
                    contact_email: promoter.contact_email,
                    contact_number: promoter.contact_number,
                    platforms: promoter.platforms || []
                })})'
                    class="w-full rounded-lg bg-yns_yellow py-3 text-center font-medium text-black transition-all hover:bg-yellow-400">
                    <span class="fas fa-envelope mr-2"></span>
                    Contact Promoter
                </button>
            </div>
        `;
        mobileView.appendChild(mobileCard);
      });
    }

    // Filter toggle functionality
    const filterToggle = document.getElementById('filter-toggle');
    const filtersContainer = document.getElementById('filters-container');

    if (filterToggle && filtersContainer) {
      filterToggle.addEventListener('click', function() {
        filtersContainer.classList.toggle('hidden');
        const buttonText = this.querySelector('span');
        if (buttonText) {
          buttonText.textContent = filtersContainer.classList.contains('hidden') ?
            'Show Filters' :
            'Hide Filters';
        }
      });
    }
  });

  function showContactModal(promoterData) {
    const modal = document.getElementById('contactModal');
    const modalTitle = document.getElementById('modalTitle');
    const preferredContact = document.getElementById('preferredContact');
    const otherContacts = document.getElementById('otherContacts');

    modalTitle.textContent = `Contact ${promoterData.name}`;
    preferredContact.innerHTML = '';
    otherContacts.innerHTML = '';

    // Add preferred contact method first
    if (promoterData.preferred_contact) {
      let preferred = '';
      if (promoterData.preferred_contact === 'email' && promoterData.contact_email) {
        preferred = createContactLink('email', promoterData.contact_email);
      } else if (promoterData.preferred_contact === 'phone' && promoterData.contact_number) {
        preferred = createContactLink('phone', promoterData.contact_number);
      } else if (promoterData.platforms) {
        const platform = promoterData.platforms.find(p => p.platform === promoterData.preferred_contact);
        if (platform) {
          preferred = createContactLink(platform.platform, platform.url);
        }
      }

      if (preferred) {
        preferredContact.innerHTML = `
        <h4 class="mb-2 font-bold text-yns_yellow">Preferred Contact Method</h4>
        ${preferred}
      `;
      }
    }

    // Add other contact methods
    let otherContactsHTML =
      '<h4 class="mb-2 font-bold text-white">Other Contact Methods</h4><div class="grid gap-2">';

    if (promoterData.contact_email && promoterData.preferred_contact !== 'email') {
      otherContactsHTML += createContactLink('email', promoterData.contact_email, 'envelope');
    }

    if (promoterData.contact_number && promoterData.preferred_contact !== 'phone') {
      otherContactsHTML += createContactLink('phone', promoterData.contact_number, 'phone');
    }

    if (promoterData.platforms) {
      promoterData.platforms.forEach(platform => {
        if (platform.platform !== promoterData.preferred_contact) {
          otherContactsHTML += createContactLink(platform.platform, platform.url, platform.platform);
        }
      });

      otherContactsHTML += '</div>';
      otherContacts.innerHTML = otherContactsHTML;

      modal.classList.remove('hidden');
    };
  };

  function showGenresModal(genres) {
    const modal = document.getElementById('genresModal');
    const genresList = document.getElementById('genresList');

    // Clear previous content
    genresList.innerHTML = '';

    // Add genres
    if (genres && genres.length > 0) {
      genres.forEach(genre => {
        const genreElement = document.createElement('div');
        genreElement.className = 'flex items-center gap-2 rounded-lg bg-black/20 p-3';
        genreElement.innerHTML = `
        <span class="fas fa-music text-yns_yellow"></span>
        <span class="text-white">${genre}</span>
      `;
        genresList.appendChild(genreElement);
      });
    } else {
      genresList.innerHTML = `
      <div class="col-span-2 text-center text-gray-400">
        No genres specified
      </div>
    `;
    }

    modal.classList.remove('hidden');
  }

  // Helper function to create contact link
  const createContactLink = (type, value) => {
    let href = '';
    let iconClass = '';

    switch (type) {
      case 'email':
        href = `mailto:${value}`;
        iconClass = 'fas fa-envelope';
        break;
      case 'phone':
        href = `tel:${value}`;
        iconClass = 'fas fa-phone';
        break;
      case 'facebook':
        href = value;
        iconClass = 'fab fa-facebook';
        break;
      case 'twitter':
        href = value;
        iconClass = 'fab fa-twitter';
        break;
      case 'instagram':
        href = value;
        iconClass = 'fab fa-instagram';
        break;
      case 'tiktok':
        href = value;
        iconClass = 'fab fa-tiktok';
        break;
      case 'youtube':
        href = value;
        iconClass = 'fab fa-youtube';
        break;
      case 'spotify':
        href = value;
        iconClass = 'fab fa-spotify';
        break;
      case 'website':
        href = value;
        iconClass = 'fas fa-globe';
        break;
      default:
        href = value;
        iconClass = 'fas fa-link';
    }

    return `<a href="${href}" target="_blank" class="flex items-center gap-2 rounded-lg bg-black/20 px-4 py-2 text-white transition-colors hover:bg-black/40">
        <span class="${iconClass}"></span>
        ${type.charAt(0).toUpperCase() + type.slice(1)}
      </a>`;
  };

  function closeGenresModal() {
    document.getElementById('genresModal').classList.add('hidden');
  }

  function closeContactModal() {
    document.getElementById('contactModal').classList.add('hidden');
  }

  // Add this helper function to generate rating icons
  function generateRatingIcons(rating) {
    const fullStars = Math.floor(rating);
    const hasHalfStar = rating % 1 >= 0.5;
    const emptyStars = 5 - Math.ceil(rating);

    let icons = '';

    // Add full stars
    for (let i = 0; i < fullStars; i++) {
      icons += '<i class="fas fa-star text-yns_yellow"></i>';
    }

    // Add half star if needed
    if (hasHalfStar) {
      icons += '<i class="fas fa-star-half-alt text-yns_yellow"></i>';
    }

    // Add empty stars
    for (let i = 0; i < emptyStars; i++) {
      icons += '<i class="far fa-star text-yns_yellow"></i>';
    }

    return icons;
  }
</script>
