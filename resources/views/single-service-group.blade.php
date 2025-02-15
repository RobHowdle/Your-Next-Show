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
                    {{ $service['average_rating'] ?: 'Not rated' }}
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
    const searchInput = document.getElementById('services-search');
    const serviceType = @json($serviceType);

    let currentSort = {
      field: null,
      direction: 'asc'
    };

    let debounceTimeout;

    // Initialize filters
    const filters = {
      search: '',
      genres: [],
      selectedLocations: [],
    };

    const urlParams = new URLSearchParams(window.location.search);
    const searchQuery = @json($town ?? '');

    if (searchQuery) {
      searchInput.value = searchQuery;
      filters.search = searchQuery;
      applyFilters();
    }

    // Search Input Handler
    document.getElementById('services-search').addEventListener('input', function(e) {
      clearTimeout(debounceTimeout);
      debounceTimeout = setTimeout(() => {
        filters.search = e.target.value;
        applyFilters();
      }, 300);
    });

    // Sorting Handlers
    document.querySelectorAll('.sortable').forEach(header => {
      header.addEventListener('click', function() {
        const field = this.dataset.sort;

        // Toggle sort direction
        if (currentSort.field === field) {
          currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
        } else {
          currentSort.field = field;
          currentSort.direction = 'asc';
        }

        // Update sort icons
        updateSortIcons(this);
        applyFilters();
      });
    });

    function updateSortIcons(clickedHeader) {
      // Reset all icons
      document.querySelectorAll('.sortable .fas').forEach(icon => {
        icon.className = 'fas fa-sort';
      });

      // Update clicked header icon
      const icon = clickedHeader.querySelector('.fas');
      icon.className = `fas fa-sort-${currentSort.direction === 'asc' ? 'up' : 'down'}`;
    }

    // Add event listeners for all filter checkboxes
    const genreCheckboxes = document.querySelectorAll('input[name="genres[]"]');
    const locationCheckboxes = document.querySelectorAll('input[name="locations[]"]');
    const photographyCheckboxes = document.querySelectorAll('input[name^="photography_filters"]');

    // Add change listeners
    genreCheckboxes.forEach(checkbox => {
      checkbox.addEventListener('change', () => {
        console.log('Genre changed');
        applyFilters();
      });
    });

    locationCheckboxes.forEach(checkbox => {
      checkbox.addEventListener('change', () => {
        console.log('Location changed');
        applyFilters();
      });
    });

    photographyCheckboxes.forEach(checkbox => {
      checkbox.addEventListener('change', () => {
        console.log('Photography filter changed');
        applyFilters();
      });
    });

    function applyFilters() {
      const searchQuery = searchInput.value;

      // Get all selected values
      const selectedGenres = Array.from(document.querySelectorAll('input[name="genres[]"]:checked'))
        .map(cb => cb.value);

      const selectedLocations = Array.from(document.querySelectorAll('input[name="locations[]"]:checked'))
        .map(cb => cb.value);

      // Get photography specific filters
      const photographyFilterValues = {
        conditions: Array.from(document.querySelectorAll(
            'input[name="photography_filters[Conditions][]"]:checked'))
          .map(cb => cb.value),
        locations: Array.from(document.querySelectorAll('input[name="photography_filters[Locations][]"]:checked'))
          .map(cb => cb.value),
        times: Array.from(document.querySelectorAll('input[name="photography_filters[Times][]"]:checked'))
          .map(cb => cb.value)
      };

      console.log('Selected Genres:', selectedGenres);
      console.log('Selected Locations:', selectedLocations);
      console.log('Photography Filters:', photographyFilterValues);

      // Make the AJAX call
      $.ajax({
        url: `/services/${serviceType}/filter`,
        method: 'POST',
        data: {
          _token: $('meta[name="csrf-token"]').attr('content'),
          search: searchQuery,
          filters: filters,
          serviceType: serviceType,
          sort: currentSort
        },
        success: function(response) {
          console.log('Filter response:', response);
          updateResultsTable(response.results);
        },
        error: function(error) {
          console.error('Error applying filters:', error);
        }
      });
    }

    document.getElementById('services-search').addEventListener('input', function(e) {
      clearTimeout(debounceTimeout);
      debounceTimeout = setTimeout(() => {
        filters.search = e.target.value;
        applyFilters();
      }, 300);
    });


    function updateResultsTable(services) {
      const tbody = document.getElementById('resultsTableBody');
      tbody.innerHTML = '';

      if (services.length === 0) {
        tbody.innerHTML = `
      <tr>
        <td colspan="5" class="px-6 py-4 text-center text-gray-400">
          No services found matching your criteria
        </td>
      </tr>
    `;
        return;
      }

      services.forEach(service => {
        const row = document.createElement('tr');
        row.className = 'hover:bg-black/20';

        const rating = service.average_rating ? service.average_rating : 'Not rated';

        const verifiedBadge = service.is_verified ? `
          <span class="ml-2 inline-flex items-center rounded-full bg-yns_yellow/10 px-2 py-1 text-xs text-yns_yellow">
            <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
              <path d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
            </svg>
            Verified
          </span>` : '';

        row.innerHTML = `
          <td class="px-6 py-4">
            <a href="/services/${serviceType}/${service.name.toLowerCase().replace(/\s+/g, '-')}" 
              class="font-medium text-white hover:text-yns_yellow">
              ${service.name}
              ${verifiedBadge}
            </a>
          </td>
          <td class="px-6 py-4 text-gray-300">
            ${rating}
          </td>
          <td class="px-6 py-4 text-gray-300">
            ${service.postal_town || 'Location not specified'}
          </td>
          <td class="px-6 py-4">
            <button
              onclick='showContactModal(${JSON.stringify({
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
      });
    }

    document.querySelectorAll('.genre-checkbox').forEach(checkbox => {
      checkbox.addEventListener('change', function() {
        filters.genres = Array.from(document.querySelectorAll('.genre-checkbox:checked'))
          .map(cb => cb.value);
        applyFilters();
      });
    });

    document.querySelectorAll('.filter-checkbox').forEach(checkbox => {
      checkbox.addEventListener('change', function() {
        filters.selectedLocations = Array.from(document.querySelectorAll(
            '.filter-checkbox-locations:checked'))
          .map(cb => cb.value);
        applyFilters();
      });
    });
  });

  function showContactModal(serviceData) {
    const modal = document.getElementById('contactModal');
    const modalTitle = document.getElementById('modalTitle');
    const preferredContact = document.getElementById('preferredContact');
    const otherContacts = document.getElementById('otherContacts');

    modalTitle.textContent = `Contact ${serviceData.name}`;

    // Clear previous content
    preferredContact.innerHTML = '';
    otherContacts.innerHTML = '';

    // Helper function to create contact link
    const createContactLink = (type, value, icon) => {
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

    // Add preferred contact method first
    if (serviceData.preferred_contact) {
      let preferred;
      if (serviceData.preferred_contact === 'email') {
        preferred = createContactLink('email', serviceData.contact_email, 'envelope');
      } else if (serviceData.preferred_contact === 'phone') {
        preferred = createContactLink('phone', serviceData.contact_number, 'phone');
      } else {
        const platform = serviceData.platforms.find(p => p.platform === serviceData.preferred_contact);
        if (platform) {
          preferred = createContactLink(platform.platform, platform.url, platform.platform);
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

    if (serviceData.contact_email && serviceData.preferred_contact !== 'email') {
      otherContactsHTML += createContactLink('email', serviceData.contact_email, 'envelope');
    }

    if (serviceData.contact_number && serviceData.preferred_contact !== 'phone') {
      otherContactsHTML += createContactLink('phone', serviceData.contact_number, 'phone');
    }

    serviceData.platforms.forEach(platform => {
      if (platform.platform !== serviceData.preferred_contact) {
        otherContactsHTML += createContactLink(platform.platform, platform.url, platform.platform);
      }
    });

    otherContactsHTML += '</div>';
    otherContacts.innerHTML = otherContactsHTML;

    modal.classList.remove('hidden');
  }

  function closeContactModal() {
    document.getElementById('contactModal').classList.add('hidden');
  }
</script>
