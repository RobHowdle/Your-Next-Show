<x-guest-layout>
  <div class="mx-auto min-h-screen max-w-7xl pb-20">
    <div class="px-4 pt-36 sm:px-6 lg:px-8">
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
      <div class="relative mb-8 overflow-hidden rounded-2xl bg-yns_dark_blue shadow-2xl">
        <h1 class="mb-4 mt-8 text-center font-heading text-4xl font-bold text-white md:text-5xl lg:text-6xl">
          Find Your Next <span class="text-yns_yellow">{{ $serviceType }}</span>
        </h1>
        <p class="mx-auto mb-8 mt-4 max-w-2xl text-center text-lg text-gray-400">
          Discover and connect with {{ $serviceName }} to enhance your events
        </p>

        <!-- Search and Filter Section -->
        <div class="mt-4 grid gap-6 p-6 md:grid-cols-2 lg:grid-cols-3">
          <!-- Search Box -->
          <div class="col-span-full">
            <div class="relative">
              <input type="text" id="services-search" placeholder="Search by service name or location..."
                class="w-full rounded-lg border border-gray-700 bg-black/50 p-4 pl-12 text-white placeholder-gray-400 backdrop-blur-sm">
              <span class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></span>
            </div>
          </div>

          @if ($serviceName === 'photography')
            <!-- Genres Filter -->
            <div class="space-y-4 rounded-lg border border-gray-700 bg-black/50 p-4 backdrop-blur-sm">
              <h3 class="font-heading text-lg font-semibold text-white">Genres</h3>
              <div class="max-h-48 grid grid-cols-2 space-y-2 overflow-y-auto">
                @foreach ($genres as $genre)
                  <label class="flex items-center space-x-2">
                    <input type="checkbox" name="genres[]" value="{{ $genre }}"
                      class="filter-checkbox rounded border-gray-600 bg-gray-700 text-yns_yellow focus:ring-yns_yellow">
                    <span class="text-sm text-gray-300">{{ $genre }}</span>
                  </label>
                @endforeach
              </div>
            </div>

            <!-- Locations Filter -->
            <div class="space-y-4 rounded-lg border border-gray-700 bg-black/50 p-4 backdrop-blur-sm">
              <h3 class="font-heading text-lg font-semibold text-white">Locations</h3>
              <div class="max-h-48 grid grid-cols-2 space-y-2 overflow-y-auto">
                @foreach ($locations as $location)
                  <label class="flex items-center space-x-2">
                    <input type="checkbox" name="locations[]" value="{{ $location }}"
                      class="filter-checkbox-locations rounded border-gray-600 bg-gray-700 text-yns_yellow focus:ring-yns_yellow">
                    <span class="text-sm text-gray-300">{{ $location }}</span>
                  </label>
                @endforeach
              </div>
            </div>

            <!-- Conditions Filter -->
            <div class="space-y-4 rounded-lg border border-gray-700 bg-black/50 p-4 backdrop-blur-sm">
              <h3 class="font-heading text-lg font-semibold text-white">Environment Types</h3>
              @foreach ($photographyEnvironments as $category => $environments)
                <div class="mt-4">
                  <h4 class="mb-2 text-sm font-bold text-gray-400">{{ $category }}</h4>
                  <div class="grid grid-cols-2 gap-2">
                    @foreach ($environments as $environment)
                      <label class="flex items-center space-x-2">
                        <input type="checkbox" name="photography_filters[{{ $category }}][]"
                          value="{{ $environment }}"
                          class="environment-checkbox rounded border-gray-600 bg-gray-700 text-yns_yellow focus:ring-yns_yellow">
                        <span class="text-sm text-gray-300">{{ $environment }}</span>
                      </label>
                    @endforeach
                  </div>
                </div>
              @endforeach
            </div>
          @elseif($serviceName === 'artist')
            <div class="space-y-4 rounded-lg border border-gray-700 bg-black/50 p-4 backdrop-blur-sm">
              <h3 class="font-heading text-lg font-semibold text-white">Genres</h3>
              <div class="max-h-48 grid grid-cols-2 space-y-2 overflow-y-auto">
                @foreach ($genres as $genre)
                  <label class="flex items-center space-x-2">
                    <input type="checkbox" name="genres[]" value="{{ $genre }}"
                      class="filter-checkbox rounded border-gray-600 bg-gray-700 text-yns_yellow focus:ring-yns_yellow">
                    <span class="text-sm text-gray-300">{{ $genre }}</span>
                  </label>
                @endforeach
              </div>
            </div>

            <!-- Band Type Filter -->
            <div class="space-y-4 rounded-lg border border-gray-700 bg-black/50 p-4 backdrop-blur-sm">
              <h3 class="font-heading text-lg font-semibold text-white">Band Type</h3>
              <div class="space-y-2">
                @foreach ($bandTypes as $type)
                  <label class="flex items-center space-x-2">
                    <input type="checkbox" name="band_types[]" value="{{ $type }}"
                      class="filter-checkbox-band-type rounded border-gray-600 bg-gray-700 text-yns_yellow focus:ring-yns_yellow">
                    <span class="text-sm text-gray-300">{{ Str::title(str_replace('-', ' ', $type)) }}</span>
                  </label>
                @endforeach
              </div>
            </div>

            <!-- Location Filter -->
            <div class="space-y-4 rounded-lg border border-gray-700 bg-black/50 p-4 backdrop-blur-sm">
              <h3 class="font-heading text-lg font-semibold text-white">Locations</h3>
              <div class="max-h-48 grid grid-cols-2 space-y-2 overflow-y-auto">
                @foreach ($locations as $location)
                  <label class="flex items-center space-x-2">
                    <input type="checkbox" name="locations[]" value="{{ $location }}"
                      class="filter-checkbox-locations rounded border-gray-600 bg-gray-700 text-yns_yellow focus:ring-yns_yellow">
                    <span class="text-sm text-gray-300">{{ $location }}</span>
                  </label>
                @endforeach
              </div>
            </div>
          @endif
        </div>
      </div>

      <!-- Results Section -->
      <div class="overflow-hidden rounded-lg border border-gray-800 bg-yns_dark_blue shadow-xl">
        <div class="overflow-x-auto">
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

        <div class="px-6 py-4">
          {{ $singleServices->links() }}
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

    // Event Listeners Setup
    initializeEventListeners();

    function initializeEventListeners() {
      // Search Input
      searchInput.addEventListener('input', handleSearchInput);

      // Sorting
      document.querySelectorAll('.sortable').forEach(header => {
        header.addEventListener('click', handleSortClick);
      });

      // Filter Checkboxes
      setupFilterCheckboxes();
    }

    function setupFilterCheckboxes() {
      // Common filters
      document.querySelectorAll('input[name="genres[]"]').forEach(checkbox => {
        checkbox.addEventListener('change', () => applyFilters());
      });

      document.querySelectorAll('input[name="locations[]"]').forEach(checkbox => {
        checkbox.addEventListener('change', () => applyFilters());
      });

      // Service-specific filters
      if (serviceType.toLowerCase() === 'photographer') {
        document.querySelectorAll('input[name^="photography_filters"]').forEach(checkbox => {
          checkbox.addEventListener('change', () => applyFilters());
        });
      } else if (serviceType.toLowerCase() === 'artist') {
        document.querySelectorAll('input[name="band_types[]"]').forEach(checkbox => {
          checkbox.addEventListener('change', () => applyFilters());
        });
      } else if (serviceType.toLowerCase() === 'designer') {
        document.querySelectorAll('input[name="band_types[]"]').forEach(checkbox => {
          checkbox.addEventListener('change', () => applyFilters());
        });
      } else if (serviceType.toLowerCase() === 'videographer') {
        document.querySelectorAll('input[name="band_types[]"]').forEach(checkbox => {
          checkbox.addEventListener('change', () => applyFilters());
        });
      }
    }

    function handleSearchInput(e) {
      clearTimeout(debounceTimeout);
      debounceTimeout = setTimeout(() => {
        filters.search = e.target.value;
        applyFilters();
      }, 300);
    }

    function handleSortClick() {
      const field = this.dataset.sort;

      if (currentSort.field === field) {
        currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
      } else {
        currentSort.field = field;
        currentSort.direction = 'asc';
      }

      updateSortIcons(this);
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

    function getCheckedValues(name) {
      return Array.from(document.querySelectorAll(`input[name="${name}"]:checked`))
        .map(cb => cb.value);
    }

    function applyFilters() {
      const searchQuery = searchInput.value;
      const serviceSpecificFilters = getServiceSpecificFilters();
      const selectedLocations = getCheckedValues('locations[]');

      // Update AJAX call to handle locations properly
      $.ajax({
        url: `/services/${serviceType}/filter`,
        method: 'POST',
        data: {
          _token: $('meta[name="csrf-token"]').attr('content'),
          filters: {
            search: searchQuery,
            locations: selectedLocations,
            ...serviceSpecificFilters
          },
          serviceType: serviceType,
          sort: currentSort
        },
        success: function(response) {
          const tbody = document.getElementById('resultsTableBody');
          tbody.innerHTML = '';

          if (!response.results || response.results.length === 0) {
            tbody.innerHTML = `
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-400">
                            No services found matching your criteria
                        </td>
                    </tr>
                `;
            return;
          }

          // Only show results that match the selected locations if any are selected
          const filteredResults = selectedLocations.length > 0 ?
            response.results.filter(service => selectedLocations.includes(service.postal_town)) :
            response.results;

          filteredResults.forEach(service => {
            tbody.appendChild(createTableRow(service));
          });
        },
        error: (error) => console.error('Error applying filters:', error)
      });
    }

    function updateSortIcons(clickedHeader) {
      document.querySelectorAll('.sortable .fas').forEach(icon => {
        icon.className = 'fas fa-sort';
      });

      const icon = clickedHeader.querySelector('.fas');
      icon.className = `fas fa-sort-${currentSort.direction === 'asc' ? 'up' : 'down'}`;
    }

    function updateResultsTable(response) {
      const tbody = document.getElementById('resultsTableBody');
      tbody.innerHTML = '';

      if (!response.results || response.results.length === 0) {
        tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-400">
                        No services found matching your criteria
                    </td>
                </tr>
            `;
        return;
      }

      response.results.forEach(service => {
        tbody.appendChild(createTableRow(service));
      });
    }

    function createTableRow(service) {
      const row = document.createElement('tr');
      row.className = 'hover:bg-black/20';
      row.innerHTML = generateRowHTML(service);
      return row;
    }

    function generateRowHTML(service) {
      const rating = service.average_rating || 'Not rated';
      const verifiedBadge = service.is_verified ? generateVerifiedBadge() : '';

      return `
          <td class="px-6 py-4">
              <a href="/services/${serviceType}/${service.name.toLowerCase().replace(/\s+/g, '-')}" 
                class="font-medium text-white hover:text-yns_yellow">
                  ${service.name}
                  ${verifiedBadge}
              </a>
          </td>
          <td class="px-6 py-4 text-gray-300">
              <div class="flex items-center">
                  ${service.rating_icons ? 
                      `<div class="rating-wrapper flex items-center">
                          ${service.rating_icons}
                      </div>` : 
                      'Not rated'
                  }
              </div>
          </td>
            <td class="px-6 py-4 text-gray-300">${service.postal_town || 'Location not specified'}</td>
            <td class="px-6 py-4">
                <button onclick='showContactModal(${JSON.stringify({
                    name: service.name,
                    preferred_contact: service.preferred_contact,
                    contact_email: service.contact_email,
                    contact_number: service.contact_number,
                    platforms: service.platforms
                })})' class="inline-flex items-center gap-2 rounded-lg bg-yns_yellow px-4 py-2 text-sm font-medium text-black transition-all hover:bg-yellow-400">
                    Contact Options
                </button>
            </td>
        `;
    }

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

    document.querySelectorAll('.environment-checkbox').forEach(checkbox => {
      checkbox.addEventListener('change', function() {
        filters.environments = Array.from(document.querySelectorAll('.environment-checkbox:checked'))
          .map(cb => cb.value);
        applyFilters();
      });
    });
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

    if (serviceData.preferred_contact) {
      renderPreferredContact(serviceData, preferredContact);
    }

    renderOtherContacts(serviceData, otherContacts);
    modal.classList.remove('hidden');
  }

  function renderPreferredContact(serviceData, container) {
    const preferred = createContactLink(
      serviceData.preferred_contact,
      getContactValue(serviceData),
      serviceData.preferred_contact
    );

    if (preferred) {
      container.innerHTML = `
            <h4 class="mb-2 font-bold text-yns_yellow">Preferred Contact Method</h4>
            ${preferred}
        `;
    }
  }

  function renderOtherContacts(serviceData, container) {
    let html = '<h4 class="mb-2 font-bold text-white">Other Contact Methods</h4><div class="grid gap-2">';

    if (serviceData.contact_email && serviceData.preferred_contact !== 'email') {
      html += createContactLink('email', serviceData.contact_email);
    }

    if (serviceData.contact_number && serviceData.preferred_contact !== 'phone') {
      html += createContactLink('phone', serviceData.contact_number);
    }

    if (serviceData.platforms) {
      serviceData.platforms.forEach(platform => {
        if (platform.platform !== serviceData.preferred_contact) {
          html += createContactLink(platform.platform, platform.url);
        }
      });
    }

    html += '</div>';
    container.innerHTML = html;
  }

  function createContactLink(type, value) {
    const config = getContactConfig(type, value);
    return `
        <a href="${config.href}" target="_blank" 
           class="flex items-center gap-2 rounded-lg bg-black/20 px-4 py-2 text-white transition-colors hover:bg-black/40">
            <span class="${config.iconClass}"></span>
            ${config.label}
        </a>
    `;
  }

  function getContactConfig(type, value) {
    const configs = {
      email: {
        href: `mailto:${value}`,
        iconClass: 'fas fa-envelope'
      },
      phone: {
        href: `tel:${value}`,
        iconClass: 'fas fa-phone'
      },
      facebook: {
        href: value,
        iconClass: 'fab fa-facebook'
      },
      twitter: {
        href: value,
        iconClass: 'fab fa-twitter'
      },
      instagram: {
        href: value,
        iconClass: 'fab fa-instagram'
      },
      tiktok: {
        href: value,
        iconClass: 'fab fa-tiktok'
      },
      youtube: {
        href: value,
        iconClass: 'fab fa-youtube'
      },
      spotify: {
        href: value,
        iconClass: 'fab fa-spotify'
      },
      website: {
        href: value,
        iconClass: 'fas fa-globe'
      }
    };

    return {
      ...configs[type] || {
        href: value,
        iconClass: 'fas fa-link'
      },
      label: type.charAt(0).toUpperCase() + type.slice(1)
    };
  }

  function getContactValue(serviceData) {
    if (serviceData.preferred_contact === 'email') return serviceData.contact_email;
    if (serviceData.preferred_contact === 'phone') return serviceData.contact_number;

    const platform = serviceData.platforms?.find(p => p.platform === serviceData.preferred_contact);
    return platform ? platform.url : '';
  }

  function closeContactModal() {
    document.getElementById('contactModal').classList.add('hidden');
  }
</script>
