<x-guest-layout>
  <div class="mx-auto min-h-screen max-w-7xl pb-20">
    <div class="px-2 pt-28 md:px-4 md:pt-36 lg:px-8">
      @php
        switch ($serviceName) {
            case 'photography':
                $serviceType = 'Photographer';
                break;
            case 'videography':
                $serviceType = 'Videographer';
                break;
            case 'artist':
                $serviceType = 'Artist';
                break;
            case 'designer':
                $serviceType = 'Designer';
                break;
        }
      @endphp

      @props([
          'styles' => null,
          'print' => null,
          'environments' => null,
          'dashboardType',
          'user',
      ])
      <!-- Hero Section -->
      <div class="relative mb-4 overflow-hidden rounded-2xl bg-yns_dark_blue shadow-2xl md:mb-8">
        <h1 class="mb-4 mt-8 text-center font-heading text-4xl font-bold text-white md:text-5xl lg:text-6xl">
          Find Your Next <span class="text-yns_yellow">{{ $serviceType }}</span>
        </h1>

        <!-- Search and Filter Section -->
        <div class="mt-4 space-y-4 p-4 lg:p-6">
          <!-- Search Box -->
          <div class="w-full">
            <div class="relative">
              <input type="text" id="services-search"
                class="w-full rounded-lg border border-gray-700 bg-black/50 p-3 pl-10 text-white placeholder-gray-400 backdrop-blur-sm sm:p-4 sm:pl-12"
                placeholder="Search by name or location...">
              <span class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 sm:left-4"></span>
            </div>
          </div>

          <!-- Filter Toggle Button - Only visible on mobile -->
          <button id="filter-toggle"
            class="flex w-full items-center justify-between rounded-lg border border-gray-700 bg-black/50 p-4 text-white md:hidden">
            <span>Show Filters</span>
            <span class="fas fa-filter"></span>
          </button>

          <div id="filters-container" class="hidden space-y-4 md:grid md:grid-cols-3 md:gap-4 md:space-y-0">
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
                  <div class="grid grid-cols-1 gap-y-2">
                    @foreach ($locations as $location)
                      <label class="flex items-center gap-2 py-1 text-gray-300">
                        <input type="checkbox" name="locations[]" value="{{ $location['name'] ?? $location }}"
                          class="filter-checkbox-locations rounded border-gray-600 bg-gray-700 text-yns_yellow">
                        <span
                          class="truncate">{{ Str::title(str_replace('-', ' ', $location['name'] ?? $location)) }}</span>
                      </label>
                    @endforeach
                  </div>
                </div>
              </div>
            </div>

            @if ($serviceName === 'photography')
              @include('components.photography-group-filters', [
                  'photographyEnvironments' => $photographyEnvironments,
              ])
            @elseif($serviceName === 'artist')
              @include('components.artist-group-filters', ['bandTypes' => $bandTypes])
            @endif
          </div>
        </div>
      </div>

      <!-- Results Section -->
      <div class="block md:hidden">
        @foreach ($singleServices as $service)
          <div class="pt-4 first:pt-0">
            <div class="relative rounded-lg bg-yns_dark_blue/90 p-4 backdrop-blur-sm">
              <!-- Service Name & Verified Badge -->
              <div class="mb-4 flex items-center gap-4">
                <a href="/services/{{ Str::slug($service['service_type']) }}/{{ Str::slug($service['name']) }}"
                  class="block font-heading text-xl font-bold text-white hover:text-yns_yellow">
                  {{ $service['name'] }}
                </a>
                @if ($service['is_verified'])
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

              <!-- Info Grid -->
              <div class="mb-4 grid grid-cols-2 gap-4">
                <!-- Rating -->
                <div class="rounded-lg bg-black/20 p-3">
                  <span class="text-sm text-gray-400">Rating</span>
                  <div class="mt-1 flex items-center">
                    <span class="text-lg font-bold text-white">
                      @if ($service['average_rating'])
                        <div class="rating-wrapper flex items-center">
                          {!! $service['rating_icons'] !!}
                        </div>
                      @else
                        Not rated
                      @endif
                    </span>
                  </div>
                </div>

                @if ($service['genres'] || $service['environments'])
                  <!-- Additional Info -->
                  <div class="space-y-4">
                    <!-- Genres row -->
                    @if ($service['genres'])
                      <div class="rounded-lg bg-black/20 p-3">
                        <span class="text-sm text-gray-400">Genres</span>
                        <div class="mt-1 text-sm text-gray-300">
                          {{ implode(', ', array_slice($service['genres'], 0, 2)) }}
                          @if (count($service['genres']) > 2)
                            <span class="text-gray-400">+{{ count($service['genres']) - 2 }} more</span>
                          @endif
                        </div>
                      </div>
                    @endif

                    <!-- Environments row -->
                    @if ($service['environments'])
                      <div class="rounded-lg bg-black/20 p-3">
                        <span class="text-sm text-gray-400">Environment</span>
                        <div class="mt-1 text-sm text-gray-300">
                          @php
                            $environmentList = [];
                            foreach ($service['environments'] as $category => $environments) {
                                if (is_array($environments)) {
                                    $environmentList = array_merge($environmentList, $environments);
                                }
                            }
                            $displayEnvironments = array_slice($environmentList, 0, 2);
                          @endphp
                          {{ implode(', ', $displayEnvironments) }}
                          @if (count($environmentList) > 2)
                            <span class="text-gray-400">+{{ count($environmentList) - 2 }} more</span>
                          @endif
                        </div>
                      </div>
                    @endif
                  </div>
                @endif

              </div>

              <!-- Location -->
              <div class="mb-4 rounded-lg bg-black/20 p-3">
                <span class="text-sm text-gray-400">Location</span>
                <div class="mt-1 flex items-center">
                  <span class="text-white">{{ $service['postal_town'] ?: 'Location not specified' }}</span>
                </div>
              </div>


              <!-- Contact button row -->
              <button
                onclick="showContactModal({{ json_encode([
                    'name' => $service['name'],
                    'preferred_contact' => $service['preferred_contact'],
                    'contact_email' => $service['contact_email'],
                    'contact_number' => $service['contact_number'],
                    'platforms' => $service['platforms'],
                ]) }})"
                class="w-full rounded-lg bg-yns_yellow py-3 text-center font-medium text-black transition-all hover:bg-yellow-400">
                <span class="fas fa-envelope mr-2"></span>
                Contact {{ ucfirst($serviceType) }}
              </button>
            </div>
          </div>
        @endforeach
      </div>

      <div class="hidden lg:block">
        <div class="overflow-hidden rounded-lg border border-gray-800 bg-yns_dark_blue shadow-xl">
          <table class="min-w-full table-fixed divide-y divide-gray-700">
            <thead>
              <tr>
                <th class="sortable px-6 py-3 text-left" data-sort="name">
                  <button
                    class="flex items-center gap-2 font-heading text-sm font-medium uppercase tracking-wider text-gray-400">
                    Service Name
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
                <th class="px-6 py-3 text-left">
                  <span class="font-heading text-sm font-medium uppercase tracking-wider text-gray-400">
                    Contact
                  </span>
                </th>
              </tr>
            </thead>
            <tbody id="resultsTableBody" class="divide-y divide-gray-700">
              @foreach ($singleServices as $service)
                <tr class="hover:bg-black/20">
                  <td class="px-6 py-4">
                    <a href="/services/{{ Str::slug($service['service_type']) }}/{{ Str::slug($service['name']) }}"
                      class="font-medium text-white hover:text-yns_yellow">
                      {{ $service['name'] }}
                      @if ($service['is_verified'])
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
                    @if ($service['average_rating'])
                      <div class="rating-wrapper flex items-center">
                        {!! $service['rating_icons'] !!}
                      </div>
                    @else
                      Not rated
                    @endif
                  </td>
                  <td class="px-6 py-4 text-gray-300">
                    {{ $service['postal_town'] ?: 'Location not specified' }}
                  </td>
                  <td class="px-6 py-4">
                    <button
                      onclick="showContactModal({{ json_encode([
                          'name' => $service['name'],
                          'preferred_contact' => $service['preferred_contact'],
                          'contact_email' => $service['contact_email'],
                          'contact_number' => $service['contact_number'],
                          'platforms' => $service['platforms'],
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

      <div class="mt-4 bg-yns_dark_blue px-6 py-4" id="pagination-container">
        <!-- Mobile Pagination (hidden on desktop) -->
        <div class="block md:hidden">
          <div class="text-center text-sm text-gray-400">
            {{ $singleServices->firstItem() }} - {{ $singleServices->lastItem() }} of {{ $singleServices->total() }}
            {{ ucfirst($serviceName) }}s {{-- Use the $serviceName variable passed from the controller --}}
          </div>
          {{ $singleServices->links('components.mobile-pagination') }}
        </div>

        <!-- Desktop Pagination (hidden on mobile) -->
        <div class="hidden md:block">
          {{ $singleServices->links('components.pagination') }}
        </div>
      </div>

      <div id="contactModal" class="fixed inset-0 z-50 hidden overflow-y-auto pt-36">
        <div class="flex min-h-screen items-end justify-center px-4 pb-20 pt-4 text-center sm:block sm:p-0">
          <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-900 opacity-75"></div>
          </div>
          <div
            class="inline-block transform overflow-hidden rounded-lg bg-yns_dark_blue text-left align-bottom shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:align-middle">
            <div class="bg-yns_dark_blue px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
              <div class="sm:flex sm:items-start">
                <div class="mt-3 w-full text-center sm:ml-4 sm:mt-0 sm:text-left">
                  <h3 class="mb-4 text-2xl font-bold leading-6 text-white" id="modalTitle"></h3>
                  <div class="mt-4 space-y-4">
                    <div id="preferredContact" class="rounded-lg border border-yns_yellow bg-yns_yellow/10 p-4">
                      <!-- Preferred contact will be inserted here -->
                    </div>
                    <div id="otherContacts" class="mt-4 space-y-2">
                      <!-- Other contact methods will be inserted here -->
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="bg-yns_dark_blue/50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
              <button type="button" onclick="closeContactModal()"
                class="mt-3 inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:mt-0 sm:w-auto">
                Close
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</x-guest-layout>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Initialize core variables
    const searchInput = document.getElementById('services-search');
    const serviceType = @json($serviceType);
    let debounceTimeout;

    // Sorting state
    let currentSort = {
      field: null,
      direction: 'asc'
    };

    // Filter state
    let filters = {
      search: '',
      genres: [],
      locations: [],
      environments: {},
      serviceSpecific: {}
    };

    // Handle initial search from URL params
    const urlParams = new URLSearchParams(window.location.search);
    const searchQuery = @json($town ?? '');

    if (searchQuery) {
      searchInput.value = searchQuery;
      filters.search = searchQuery;
      applyFilters();
    }

    function getServiceSpecificFilters() {
      switch (serviceType.toLowerCase()) {
        case 'photographer':
          const environmentFilters = {};
          document.querySelectorAll('input[name^="photography_filters"]').forEach(checkbox => {
            if (checkbox.checked) {
              const category = checkbox.name.match(/\[(.*?)\]/)[1];
              if (!environmentFilters[category]) {
                environmentFilters[category] = [];
              }
              environmentFilters[category].push(checkbox.value);
            }
          });

          return {
            genres: getCheckedValues('genres[]'),
              environments: environmentFilters,
              locations: getCheckedValues('locations[]')
          };
        case 'artist':
          return {
            genres: getCheckedValues('genres[]'),
              bandTypes: getCheckedValues('band_types[]')
          };
        default:
          return {};
      }
    }

    // Search Input Handler with debounce
    searchInput.addEventListener('input', function(e) {
      clearTimeout(debounceTimeout);
      debounceTimeout = setTimeout(() => {
        filters.search = e.target.value;
        applyFilters();
      }, 300);
    });

    // Genre filter handlers
    document.querySelectorAll('.genre-checkbox').forEach(checkbox => {
      checkbox.addEventListener('change', function() {
        filters.genres = Array.from(document.querySelectorAll('.genre-checkbox:checked'))
          .map(cb => cb.value);
        applyFilters();
      });
    });

    // Location filter handlers
    document.querySelectorAll('.filter-checkbox-locations').forEach(checkbox => {
      checkbox.addEventListener('change', function() {
        filters.locations = Array.from(document.querySelectorAll('.filter-checkbox-locations:checked'))
          .map(cb => cb.value);
        applyFilters();
      });
    });

    // Environment filter handlers (for photography)
    document.querySelectorAll('.environment-checkbox').forEach(checkbox => {
      checkbox.addEventListener('change', function() {
        const category = checkbox.name.match(/\[(.*?)\]/)[1];
        if (!filters.environments[category]) {
          filters.environments[category] = [];
        }

        if (checkbox.checked) {
          filters.environments[category].push(checkbox.value);
        } else {
          filters.environments[category] = filters.environments[category]
            .filter(value => value !== checkbox.value);
        }
        applyFilters();
      });
    });

    // Band type filter handlers (for artists)
    document.querySelectorAll('.filter-checkbox-band-type').forEach(checkbox => {
      checkbox.addEventListener('change', function() {
        filters.bandTypes = Array.from(document.querySelectorAll('.filter-checkbox-band-type:checked'))
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

      // Get all filter values
      const filterData = {
        search: filters.search || '',
        genres: Array.from(document.querySelectorAll('.genre-checkbox:checked')).map(cb => cb.value),
        locations: Array.from(document.querySelectorAll('.filter-checkbox-locations:checked')).map(cb => cb
          .value),
        environments: {},
        bandTypes: Array.from(document.querySelectorAll('.filter-checkbox-band-type:checked')).map(cb => cb.value)
      };

      // Handle environment filters for photography
      if (serviceType.toLowerCase() === 'photographer') {
        document.querySelectorAll('.environment-checkbox:checked').forEach(checkbox => {
          const category = checkbox.name.match(/\[(.*?)\]/)[1];
          if (!filterData.environments[category]) {
            filterData.environments[category] = [];
          }
          filterData.environments[category].push(checkbox.value);
        });
      }

      $.ajax({
        url: `/services/${serviceType.toLowerCase()}/filter`,
        method: 'POST',
        data: {
          _token: $('meta[name="csrf-token"]').attr('content'),
          serviceType: serviceType,
          filters: {
            search: filterData.search,
            genres: filterData.genres.length > 0 ? filterData.genres : null,
            locations: filterData.locations.length > 0 ? filterData.locations : null,
            environments: Object.keys(filterData.environments).length > 0 ? filterData.environments : null,
            bandTypes: filterData.bandTypes.length > 0 ? filterData.bandTypes : null,
            sort: currentSort
          }
        },
        success: function(response) {
          if (response && response.results) {
            updateResultsTable(response.results);
            if (response.pagination) {
              document.getElementById('pagination-container').innerHTML = response.pagination;
              attachPaginationHandlers();
            }
          } else {
            const noResultsHTML = `
                    <div class="px-6 py-4 text-center text-gray-400">
                        No services found matching your criteria
                    </div>
                `;
            tbody.innerHTML = `<tr><td colspan="5">${noResultsHTML}</td></tr>`;
            mobileView.innerHTML = noResultsHTML;
          }
        },
        error: function(error) {
          console.error('Error applying filters:', error);
          const errorHTML = `
                <div class="px-6 py-4 text-center text-red-400">
                    <span class="fas fa-exclamation-circle mr-2"></span>
                    An error occurred while loading services. Please try again.
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
            url: `/services/${serviceType.toLowerCase()}/filter`, // Fixed URL
            method: 'POST',
            data: {
              _token: $('meta[name="csrf-token"]').attr('content'),
              serviceType: serviceType, // Added serviceType
              filters: {
                search: filters.search,
                genres: filters.genres.length > 0 ? filters.genres : null,
                locations: filters.locations.length > 0 ? filters.locations : null,
                environments: Object.keys(filters.environments).length > 0 ? filters.environments :
                  null,
                bandTypes: filters.bandTypes.length > 0 ? filters.bandTypes : null,
                sort: currentSort
              },
              page: page
            },
            success: function(response) {
              if (response && response.results) {
                updateResultsTable(response.results);
                if (response.pagination) {
                  document.getElementById('pagination-container').innerHTML = response.pagination;
                  attachPaginationHandlers();
                }
              }
            },
            error: function(error) {
              console.error('Error fetching page:', error);
            }
          });
        });
      });
    }

    function updateResultsTable(results) {
      // Get table body and mobile view containers
      const tbody = document.getElementById('resultsTableBody');
      const mobileView = document.querySelector('.block.md\\:hidden');

      // Ensure both containers exist
      if (!tbody || !mobileView) {
        console.error('Required containers not found');
        return;
      }

      // Clear existing content
      tbody.innerHTML = '';
      mobileView.innerHTML = '';

      // Handle no results case
      if (!results || results.length === 0) {
        const noResultsHTML = `
            <div class="px-6 py-4 text-center text-gray-400">
                No services found matching your criteria
            </div>
        `;
        tbody.innerHTML = `<tr><td colspan="5">${noResultsHTML}</td></tr>`;
        mobileView.innerHTML = noResultsHTML;
        return;
      }

      // Generate verified badge helper function
      function generateVerifiedBadge() {
        return `
            <span class="ml-2 inline-flex items-center rounded-full bg-yns_yellow/10 px-2 py-1 text-xs text-yns_yellow">
                <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                </svg>
                Verified
            </span>
        `;
      }

      // Update desktop table view
      results.forEach(service => {
        const verifiedBadge = service.is_verified ? generateVerifiedBadge() : '';

        const row = document.createElement('tr');
        row.className = 'hover:bg-black/20';
        row.innerHTML = `
            <td class="px-6 py-4">
                <a href="/services/${service.service_type}/${service.name.toLowerCase().replace(/\s+/g, '-')}" 
                   class="font-medium text-white hover:text-yns_yellow">
                    ${service.name}
                    ${verifiedBadge}
                </a>
            </td>
            <td class="px-6 py-4 text-gray-300">
                <div class="rating-wrapper flex items-center">
                    ${service.rating_icons || 'Not rated'}
                </div>
            </td>
            <td class="px-6 py-4 text-gray-300">
                ${service.postal_town || 'Location not specified'}
            </td>
            <td class="px-6 py-4">
                <button onclick='showContactModal(${JSON.stringify({
                    name: service.name,
                    preferred_contact: service.preferred_contact,
                    contact_email: service.contact_email,
                    contact_number: service.contact_number,
                    platforms: service.platforms
                })})'
                class="inline-flex items-center gap-2 rounded-lg bg-yns_yellow px-4 py-2 text-sm font-medium text-black transition-all hover:bg-yellow-400">
                    Contact Options
                </button>
            </td>
        `;
        tbody.appendChild(row);

        // Update mobile view
        const mobileCard = document.createElement('div');
        mobileCard.className = 'pt-4 first:pt-0';
        mobileCard.innerHTML = `
            <div class="relative rounded-lg bg-yns_dark_blue/90 p-4 backdrop-blur-sm">
                <div class="mb-4 flex items-center justify-between">
                    <a href="/services/${service.service_type}/${service.name.toLowerCase().replace(/\s+/g, '-')}"
                       class="font-medium text-white hover:text-yns_yellow">
                        ${service.name}
                        ${verifiedBadge}
                    </a>
                </div>
                
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="rounded-lg bg-black/20 p-3">
                            <span class="text-sm text-gray-400">Rating</span>
                            <div class="mt-1">
                                <div class="rating-wrapper flex items-center">
                                    ${service.rating_icons || '<span class="text-sm text-gray-300">Not rated</span>'}
                                </div>
                            </div>
                        </div>
                        <div class="rounded-lg bg-black/20 p-3">
                            <span class="text-sm text-gray-400">Location</span>
                            <div class="mt-1 text-sm text-gray-300">
                                ${service.postal_town || 'Location not specified'}
                            </div>
                        </div>
                    </div>

                    <button onclick='showContactModal(${JSON.stringify({
                        name: service.name,
                        preferred_contact: service.preferred_contact,
                        contact_email: service.contact_email,
                        contact_number: service.contact_number,
                        platforms: service.platforms
                    })})'
                    class="w-full rounded-lg bg-yns_yellow py-3 text-center font-medium text-black transition-all hover:bg-yellow-400">
                        <span class="fas fa-envelope mr-2"></span>
                        Contact
                    </button>
                </div>
            </div>
        `;
        mobileView.appendChild(mobileCard);
      });
    }

    // Helper function for genres list
    function generateGenresList(genres) {
      if (!Array.isArray(genres)) return '';
      const displayGenres = genres.slice(0, 2);
      let output = displayGenres.join(', ');
      if (genres.length > 2) {
        output += ` <span class="text-gray-400">+${genres.length - 2} more</span>`;
      }
      return output;
    }

    // Helper function for environments list
    function generateEnvironmentsList(environments) {
      const environmentList = [];
      for (const category in environments) {
        if (Array.isArray(environments[category])) {
          environmentList.push(...environments[category]);
        }
      }
      const displayEnvironments = environmentList.slice(0, 2);
      let output = displayEnvironments.join(', ');
      if (environmentList.length > 2) {
        output += ` <span class="text-gray-400">+${environmentList.length - 2} more</span>`;
      }
      return output;
    }

    document.querySelectorAll('.environment-checkbox').forEach(checkbox => {
      checkbox.addEventListener('change', function() {
        filters.environments = Array.from(document.querySelectorAll('.environment-checkbox:checked'))
          .map(cb => cb.value);
        applyFilters();
      });
    });

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


  // Contact Modal Functions
  function showContactModal(serviceData) {
    const modal = document.getElementById('contactModal');
    const modalTitle = document.getElementById('modalTitle');
    const preferredContact = document.getElementById('preferredContact');
    const otherContacts = document.getElementById('otherContacts');

    modalTitle.textContent = `Contact ${serviceData.name}`;
    preferredContact.innerHTML = '';
    otherContacts.innerHTML = '';

    // Create preferred contact section
    if (serviceData.preferred_contact) {
      let preferred = '';
      if (serviceData.preferred_contact === 'email' && serviceData.contact_email) {
        preferred = createContactLink('email', serviceData.contact_email);
      } else if (serviceData.preferred_contact === 'phone' && serviceData.contact_number) {
        preferred = createContactLink('phone', serviceData.contact_number);
      } else if (serviceData.platforms) {
        const platform = serviceData.platforms.find(p => p.platform === serviceData.preferred_contact);
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
    if (serviceData.contact_email && serviceData.preferred_contact !== 'email') {
      otherContactsHTML += createContactLink('email', serviceData.contact_email);
    }

    // Add phone if not preferred
    if (serviceData.contact_number && serviceData.preferred_contact !== 'phone') {
      otherContactsHTML += createContactLink('phone', serviceData.contact_number);
    }

    // Add platforms if not preferred
    if (serviceData.platforms) {
      serviceData.platforms.forEach(platform => {
        if (platform.platform !== serviceData.preferred_contact) {
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
      case 'x':
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
