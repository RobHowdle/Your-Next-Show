<x-guest-layout>
  <!-- Hero Section -->
  <div class="mx-auto min-h-screen max-w-7xl pb-20">
    <div class="px-2 pt-28 md:px-4 md:pt-36 lg:px-8">
      <!-- Hero Section -->
      <div class="relative mb-4 overflow-hidden rounded-2xl bg-yns_dark_blue shadow-2xl md:mb-8">
        <h1 class="mb-4 mt-8 text-center font-heading text-4xl font-bold text-white md:text-5xl lg:text-6xl">
          Find Your Next <span class="text-yns_yellow">Venue</span>
        </h1>

        <div class="mt-4 space-y-4 p-6">
          <!-- Search Box - Full width on all screens -->
          <div class="w-full">
            <div class="relative">
              <input type="text" id="venue-search" placeholder="Search by venue name or location..."
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
            <!-- Venue Type Filter -->
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

            <!-- Capacity Filter -->
            <div class="rounded-lg border border-gray-700 bg-black/50 p-4 backdrop-blur-sm">
              <h3 class="flex items-center justify-between font-heading text-lg font-bold text-white">
                Capacity
                <span class="fas fa-chevron-down text-sm md:hidden"></span>
              </h3>
              <div class="filter-content mt-4 hidden md:block">
                <div class="flex flex-col gap-3">
                  <div class="min-h-0">
                    <label class="mb-1 block text-sm text-gray-400">Minimum</label>
                    <input type="number" id="min-capacity"
                      class="w-full rounded border border-gray-700 bg-black/50 p-2 text-sm text-white" min="0">
                  </div>
                  <div class="min-h-0">
                    <label class="mb-1 block text-sm text-gray-400">Maximum</label>
                    <input type="number" id="max-capacity"
                      class="w-full rounded border border-gray-700 bg-black/50 p-2 text-sm text-white" min="0">
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Results Section -->
    <div class="mx-2 overflow-hidden rounded-lg shadow-xl">
      <div class="border border-gray-800 bg-yns_dark_blue">
        <div class="hidden overflow-x-auto md:block">
          <table class="min-w-full divide-y divide-gray-800">
            <thead>
              <tr>
                <th class="sortable px-6 py-3 text-left" data-sort="name">
                  <button
                    class="flex items-center gap-2 font-heading text-sm font-medium uppercase tracking-wider text-gray-400">
                    Venue Name
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
                <th class="sortable px-6 py-3 text-left" data-sort="capacity">
                  <button
                    class="flex items-center gap-2 font-heading text-sm font-medium uppercase tracking-wider text-gray-400">
                    Capacity
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
              @foreach ($venues as $venue)
                <tr class="hover:bg-black/20">
                  <td class="px-6 py-4">
                    <a href="/venues/{{ Str::slug($venue['name']) }}"
                      class="font-medium text-white hover:text-yns_yellow">
                      {{ $venue['name'] }}
                      @if ($venue['is_verified'])
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
                    @if ($venue['average_rating'])
                      <div class="rating-wrapper flex items-center">
                        {!! $venue['rating_icons'] !!}
                      </div>
                    @else
                      Not rated
                    @endif
                  </td>
                  <td class="px-6 py-4 text-gray-300">
                    {{ $venue['postal_town'] ?: 'Location not specified' }}
                  </td>
                  <td class="px-6 py-4 text-gray-300">
                    {{ $venue['capacity'] ?: 'Not specified' }}
                  </td>
                  <td class="px-6 py-4">
                    <button
                      onclick="showContactModal({{ json_encode([
                          'name' => $venue['name'],
                          'preferred_contact' => $venue['preferred_contact'],
                          'contact_email' => $venue['contact_email'],
                          'contact_number' => $venue['contact_number'],
                          'platforms' => $venue['platforms'],
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
        @foreach ($venues as $venue)
          <div class="pt-4 first:pt-0">
            <!-- Main Card Content -->
            <div class="relative rounded-lg bg-yns_dark_blue/90 p-4 backdrop-blur-sm">
              <!-- Venue Name and Verified Badge -->
              <div class="mb-4 flex items-center gap-4">
                <a href="/venues/{{ Str::slug($venue['name']) }}"
                  class="block font-heading text-xl font-bold text-white hover:text-yns_yellow">
                  {{ $venue['name'] }}
                </a>
                @if ($venue['is_verified'])
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

              <!-- Venue Details Grid -->
              <div class="mb-4 grid grid-cols-2 gap-4">
                <div class="rounded-lg bg-black/20 p-3">
                  <span class="text-sm text-gray-400">Rating</span>
                  <div class="mt-1 flex items-center">
                    @if ($venue['average_rating'])
                      <div class="rating-wrapper flex items-center">
                        {!! $venue['rating_icons'] !!}
                      </div>
                    @else
                      Not rated
                    @endif
                  </div>
                </div>

                <div class="rounded-lg bg-black/20 p-3">
                  <span class="text-sm text-gray-400">Capacity</span>
                  <div class="mt-1 flex items-center">
                    <span class="text-lg font-bold text-white">
                      {{ $venue['capacity'] ?: 'Not specified' }}
                    </span>
                  </div>
                </div>
              </div>

              <!-- Location -->
              <div class="mb-4 rounded-lg bg-black/20 p-3">
                <span class="text-sm text-gray-400">Location</span>
                <span class="ml-2 text-white">{{ $venue['postal_town'] ?: 'Location not specified' }}</span>
              </div>

              <!-- Contact Button -->
              <button
                onclick="showContactModal({{ json_encode([
                    'name' => $venue['name'],
                    'preferred_contact' => $venue['preferred_contact'],
                    'contact_email' => $venue['contact_email'],
                    'contact_number' => $venue['contact_number'],
                    'platforms' => $venue['platforms'],
                ]) }})"
                class="w-full rounded-lg bg-yns_yellow py-3 text-center font-medium text-black transition-all hover:bg-yellow-400">
                <span class="fas fa-envelope mr-2"></span>
                Contact Venue
              </button>
            </div>
          </div>
        @endforeach
      </div>

      <div class="mt-4 bg-yns_dark_blue px-6 py-4" id="pagination-container">
        <!-- Mobile Pagination (hidden on desktop) -->
        <div class="block md:hidden">
          <div class="text-center text-sm text-gray-400">
            {{ $venues->firstItem() }} - {{ $venues->lastItem() }} of {{ $venues->total() }} venues
          </div>
          {{ $venues->links('components.mobile-pagination') }}
        </div>

        <!-- Desktop Pagination (hidden on mobile) -->
        <div class="hidden md:block">
          {{ $venues->links('components.pagination') }}
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
      minCapacity: null,
      maxCapacity: null
    };

    const urlParams = new URLSearchParams(window.location.search);
    const searchQuery = @json($town ?? '');

    if (searchQuery) {
      const searchInput = document.getElementById('venue-search');
      searchInput.value = searchQuery;
      filters.search = searchQuery;
      applyFilters();
    }

    // Search Input Handler
    document.getElementById('venue-search').addEventListener('input', function(e) {
      clearTimeout(debounceTimeout);
      debounceTimeout = setTimeout(() => {
        filters.search = e.target.value;
        applyFilters();
      }, 300);
    });

    // Update capacity input handlers
    document.getElementById('min-capacity').addEventListener('input', function(e) {
      filters.minCapacity = e.target.value ? parseInt(e.target.value) : null;
      applyFilters();
    });

    document.getElementById('max-capacity').addEventListener('input', function(e) {
      filters.maxCapacity = e.target.value ? parseInt(e.target.value) : null;
      applyFilters();
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
        minCapacity: filters.minCapacity,
        maxCapacity: filters.maxCapacity
      };

      $.ajax({
        url: '/venues/filter',
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
              An error occurred while loading venues. Please try again.
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
            url: '/venues/filter',
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
    }

    function updateResultsTable(venues) {
      const tbody = document.getElementById('resultsTableBody');
      const mobileView = document.querySelector('.block.md\\:hidden');

      tbody.innerHTML = '';
      mobileView.innerHTML = '';

      if (venues.length === 0) {
        const noResults =
          `<div class="px-6 py-4 text-center text-gray-400">No venues found matching your criteria</div>`;
        tbody.innerHTML =
          `<tr><td colspan="5" class="px-6 py-4 text-center text-gray-400">No venues found matching your criteria</td></tr>`;
        mobileView.innerHTML = noResults;
        return;
      }

      venues.forEach(venue => {
        const row = document.createElement('tr');
        row.className = 'hover:bg-black/20';

        const capacity = venue.capacity ? venue.capacity : 'Not specified';

        const verifiedBadge = venue.is_verified ? `
          <span class="ml-2 inline-flex items-center rounded-full bg-yns_yellow/10 px-2 py-1 text-xs text-yns_yellow">
            <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
              <path d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
            </svg>
            Verified
          </span>` : '';

        row.innerHTML = `
          <td class="px-6 py-4">
            <a href="/venues/${venue.name.toLowerCase().replace(/\s+/g, '-')}" 
              class="font-medium text-white hover:text-yns_yellow">
              ${venue.name}
              ${verifiedBadge}
            </a>
          </td>
          <td class="px-6 py-4 text-gray-300">
            <div class="rating-wrapper flex items-center">
                ${venue.rating_icons || 'Not rated'}
            </div>
          </td>
          <td class="px-6 py-4 text-gray-300">
            ${venue.postal_town || 'Location not specified'}
          </td>
          <td class="px-6 py-4 text-gray-300">
            ${capacity}
          </td>
          <td class="px-6 py-4">
            <button
              onclick='showContactModal(${JSON.stringify({
                name: venue.name,
                preferred_contact: venue.preferred_contact,
                contact_email: venue.contact_email,
                contact_number: venue.contact_number,
                platforms: venue.platforms
              })})'
              class="inline-flex items-center gap-2 rounded-lg bg-yns_yellow px-4 py-2 text-sm font-medium text-black transition-all hover:bg-yellow-400">
              Contact Options
            </button>
          </td>
        `;

        tbody.appendChild(row);

        // Add mobile card
        const mobileCard = document.createElement('div');
        mobileCard.className = 'p-4';
        mobileCard.innerHTML = `
        <div class="relative rounded-lg bg-yns_dark_blue p-4 backdrop-blur-sm">
          <div class="mb-4">
            <a href="/venues/${venue.name.toLowerCase().replace(/\s+/g, '-')}" 
              class="block font-heading text-xl font-bold text-white hover:text-yns_yellow">
              ${venue.name}
              ${verifiedBadge}
            </a>
          </div>

          <div class="mb-4 grid grid-cols-2 gap-4">
            <div class="rounded-lg bg-black/20 p-3">
                <span class="text-sm text-gray-400">Rating</span>
                <div class="mt-1 rating-wrapper flex items-center">
                    ${venue.rating_icons || 'Not rated'}
                </div>
            </div>

            <div class="rounded-lg bg-black/20 p-3">
              <span class="text-sm text-gray-400">Capacity</span>
              <div class="mt-1 flex items-center">
                <span class="ml-2 text-lg font-bold text-white">${capacity}</span>
              </div>
            </div>
          </div>

          <div class="mb-4 flex items-center rounded-lg bg-black/20 p-3">
            <span class="text-sm text-gray-400">Location</span>
            <span class="ml-2 text-white">${venue.postal_town || 'Location not specified'}</span>
          </div>

          <button
            onclick='showContactModal(${JSON.stringify({
              name: venue.name,
              preferred_contact: venue.preferred_contact,
              contact_email: venue.contact_email,
              contact_number: venue.contact_number,
              platforms: venue.platforms
            })})'
            class="w-full rounded-lg bg-yns_yellow py-3 text-center font-medium text-black transition-all hover:bg-yellow-400">
            <span class="fas fa-envelope mr-2"></span>
            Contact Venue
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

  function showContactModal(venueData) {
    const modal = document.getElementById('contactModal');
    const modalTitle = document.getElementById('modalTitle');
    const preferredContact = document.getElementById('preferredContact');
    const otherContacts = document.getElementById('otherContacts');

    modalTitle.textContent = `Contact ${venueData.name}`;
    preferredContact.innerHTML = '';
    otherContacts.innerHTML = '';

    // Create preferred contact section
    if (venueData.preferred_contact) {
      let preferred = '';
      if (venueData.preferred_contact === 'email' && venueData.contact_email) {
        preferred = createContactLink('email', venueData.contact_email);
      } else if (venueData.preferred_contact === 'phone' && venueData.contact_number) {
        preferred = createContactLink('phone', venueData.contact_number);
      } else if (venueData.platforms) {
        const platform = venueData.platforms.find(p => p.platform === venueData.preferred_contact);
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

    // Create other contacts section
    let otherContactsHTML = '<h4 class="mb-2 font-bold text-white">Other Contact Methods</h4><div class="grid gap-2">';

    // Add email if not preferred
    if (venueData.contact_email && venueData.preferred_contact !== 'email') {
      otherContactsHTML += createContactLink('email', venueData.contact_email);
    }

    // Add phone if not preferred
    if (venueData.contact_number && venueData.preferred_contact !== 'phone') {
      otherContactsHTML += createContactLink('phone', venueData.contact_number);
    }

    // Add platforms if not preferred
    if (venueData.platforms) {
      venueData.platforms.forEach(platform => {
        if (platform.platform !== venueData.preferred_contact) {
          otherContactsHTML += createContactLink(platform.platform, platform.url);
        }
      });
    }

    otherContactsHTML += '</div>';
    otherContacts.innerHTML = otherContactsHTML;

    modal.classList.remove('hidden');
  }

  function createContactLink(type, value) {
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

    return `
    <a href="${href}" target="_blank" 
       class="flex items-center gap-2 rounded-lg bg-black/40 px-4 py-2 text-white transition-colors hover:bg-black/60">
      <span class="${iconClass}"></span>
      ${type.charAt(0).toUpperCase() + type.slice(1)}
    </a>
  `;
  }

  function closeContactModal() {
    document.getElementById('contactModal').classList.add('hidden');
  }
</script>
