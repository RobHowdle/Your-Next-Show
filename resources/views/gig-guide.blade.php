<x-guest-layout>
  <div class="min-h-screen pt-16 md:pt-24 lg:pt-28 xl:pt-32">
    <div class="mx-auto max-w-screen-2xl px-4 py-12 sm:px-6 lg:px-8">
      <!-- Hero Section with existing background -->
      <div class="relative mb-8 overflow-hidden rounded-2xl bg-yns_dark_blue p-4 shadow-2xl xl:mb-16 xl:p-8">
        <div class="relative z-10">
          <h1
            class="mb-4 text-center font-heading text-3xl font-bold text-white md:text-left md:text-4xl lg:text-5xl xl:text-7xl">
            Andy's Gig Guide</h1>
          <div class="max-w-3xl space-y-4">
            <p class="text-center text-base text-gray-300 md:text-left md:text-lg">Back in the day, Andy C Jennings would
              create a Gig Guide on his Facebook
              page,
              helping promote local music across the country.</p>
            <p class="text-center text-sm text-gray-400 md:text-left">We've dedicated this digital version to Andy,
              bringing his legacy into the
              modern age.</p>
          </div>
        </div>
      </div>

      <!-- Enhanced Filter Section -->
      <div class="mb-8 rounded-xl border border-gray-800 bg-yns_dark_blue/50 p-6 backdrop-blur-sm">
        <div class="flex flex-col items-center justify-between gap-6 sm:flex-row">
          <div class="flex flex-wrap items-center gap-4">
            <div class="flex items-center space-x-3">
              <label for="distance" class="text-lg font-medium text-white">Find gigs within</label>
              <select id="distance"
                class="w-32 rounded-lg border-2 border-yns_yellow bg-yns_dark_blue px-4 py-2 text-white transition-all hover:border-yellow-400 focus:outline-none focus:ring-2 focus:ring-yellow-500">
                <option value="5">5 miles</option>
                <option value="10">10 miles</option>
                <option value="25">25 miles</option>
                <option value="50">50 miles</option>
                <option value="100">100 miles</option>
              </select>
            </div>
            <div id="locationStatus"
              class="animate-fade-in hidden w-full rounded-full bg-gradient-to-r from-green-600 to-green-500 px-4 py-2 text-sm text-white shadow-lg sm:w-auto">
              <div class="flex items-center gap-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                </svg>
                Using your location
              </div>
            </div>
          </div>
          <button id="refreshButton"
            class="group hidden items-center gap-2 rounded-lg bg-yns_yellow px-6 py-2 font-medium text-black transition-all hover:bg-yellow-400">
            <svg class="h-4 w-4 transition-transform group-hover:rotate-180" fill="none" stroke="currentColor"
              viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            Refresh Results
          </button>
        </div>
      </div>

      <!-- Enhanced Results Section -->
      <div class="overflow-hidden rounded-xl border border-gray-800 bg-yns_dark_blue/50 backdrop-blur-sm">
        <table class="w-full border-collapse text-left">
          <thead class="hidden md:table-header-group">
            <tr class="border-b border-gray-800 bg-yns_dark_blue/50">
              <th class="px-6 py-4 text-sm font-medium text-gray-400">DATE & TIME</th>
              <th class="px-6 py-4 text-sm font-medium text-gray-400">EVENT</th>
              <th class="px-6 py-4 text-sm font-medium text-gray-400">VENUE</th>
              <th class="px-6 py-4 text-sm font-medium text-gray-400">APPROX DISTANCE</th>
            </tr>
          </thead>
          <tbody id="gigsTableBody" class="divide-y divide-gray-800"></tbody>
        </table>
      </div>
    </div>
  </div>
</x-guest-layout>

<style>
  @keyframes fade-in {
    from {
      opacity: 0;
      transform: translateY(-10px);
    }

    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .animate-fade-in {
    animation: fade-in 0.3s ease-out forwards;
  }
</style>
<script>
  $(document).ready(function() {
    let userLatitude = null;
    let userLongitude = null;
    const loadingElement = document.getElementById('loading');
    const tableBody = document.getElementById('gigsTableBody');
    const distanceSelect = document.getElementById('distance');
    const isProfile = false;

    // Check for profile location
    const userLocation = @json($userLocation);

    function updateLocationStatus(isProfile) {
      const statusElement = document.getElementById('locationStatus');
      if (userLatitude && userLongitude) {
        const locationText = isProfile ? 'Using Profile Location' : 'Using Device Location';
        statusElement.querySelector('div').innerHTML = `
          <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
          </svg>
          ${locationText}
        `;
        statusElement.classList.remove('hidden');
        statusElement.classList.add('animate-fade-in');
      }
    }

    async function initializeLocation() {
      if (userLocation && userLocation.latitude && userLocation.longitude) {
        userLatitude = userLocation.latitude;
        userLongitude = userLocation.longitude;
        // Pass true to indicate profile location is being used
        updateLocationStatus(true);
        await fetchGigs(distanceSelect.value);
      } else {
        try {
          const position = await getCurrentPosition();
          userLatitude = position.coords.latitude;
          userLongitude = position.coords.longitude;
          // Pass false to indicate device location is being used
          updateLocationStatus(false);
          await fetchGigs(distanceSelect.value);
        } catch (error) {
          console.error('Geolocation error:', error);
          showErrorMessage('Please enable location services or set your location in your profile');
        }
      }
    }

    function getCurrentPosition() {
      return new Promise((resolve, reject) => {
        if (!navigator.geolocation) {
          reject(new Error('Geolocation is not supported'));
          return;
        }
        navigator.geolocation.getCurrentPosition(resolve, reject);
      });
    }

    function showErrorMessage(message) {
      tableBody.innerHTML = `
    <tr>
      <td colspan="4" class="p-8">
        <div class="flex flex-col items-center justify-center space-y-4">
          <div class="rounded-full bg-red-500/10 p-3">
            <svg class="h-6 w-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
          </div>
          <p class="text-lg font-medium text-white">${message}</p>
          <button onclick="fetchGigs(distanceSelect.value)" 
                  class="mt-4 rounded-lg bg-yns_yellow px-4 py-2 text-sm font-medium text-black hover:bg-yellow-400">
            Try Again
          </button>
        </div>
      </td>
    </tr>
  `;
    }
    async function fetchGigs(distance) {
      if (!userLatitude || !userLongitude) {
        showErrorMessage('Location not available');
        return;
      }

      // Show loading state in table
      tableBody.innerHTML = `
    <tr>
      <td colspan="4" class="p-8">
        <div class="flex flex-col items-center justify-center space-y-4">
          <div class="inline-block h-12 w-12 animate-spin rounded-full border-4 border-yns_yellow border-t-transparent"></div>
          <p class="text-lg font-medium text-white">Finding gigs near you...</p>
          <p class="text-sm text-gray-400">Searching within your selected radius</p>
        </div>
      </td>
    </tr>
  `;

      try {
        const response = await fetch(
          `/gigs/filter?distance=${distance}&latitude=${userLatitude}&longitude=${userLongitude}`);
        if (!response.ok) throw new Error('Network response was not ok');

        const data = await response.json();
        renderGigsList(data.events);
      } catch (error) {
        console.error('Error:', error);
        showErrorMessage('Error loading events');
      }
    }

    function renderGigsList(events) {
      if (!events.length) {
        showErrorMessage('No events found within this distance');
        return;
      }

      tableBody.innerHTML = events.map(event => `
    <tr class="block border-gray-700 md:table-row">
      <td class="block px-4 pt-4 font-sans text-white md:table-cell md:whitespace-nowrap md:px-6 md:py-3 md:text-base lg:px-8 lg:py-4 lg:text-lg">
        <span class="text-xs text-gray-400 md:hidden">DATE & TIME:</span>
        ${formatDate(event.date, event.start_time)}
      </td>
      <td class="block px-4 pt-2 font-sans text-white md:table-cell md:whitespace-nowrap md:px-6 md:py-3 md:text-base lg:px-8 lg:py-4 lg:text-lg">
        <span class="text-xs text-gray-400 md:hidden">EVENT:</span>
        <a href="/events/${event.id}" target="_blank" class="hover:text-yns_yellow">
          ${escapeHtml(event.name)}
          ${event.headliner ? `<br><span class="text-sm text-gray-400">${escapeHtml(event.headliner)}</span>` : ''}
        </a>
      </td>
      <td class="block px-4 pt-2 font-sans text-white md:table-cell md:whitespace-nowrap md:px-6 md:py-3 md:text-base lg:px-8 lg:py-4 lg:text-lg">
        <span class="text-xs text-gray-400 md:hidden">VENUE:</span>
        ${escapeHtml(event.venue_name)}
        <br><span class="text-sm text-gray-400">${escapeHtml(event.venue_town)}</span>
      </td>
      <td class="block px-4 pb-4 pt-2 font-sans text-white md:table-cell md:whitespace-nowrap md:px-6 md:py-3 md:text-base lg:px-8 lg:py-4 lg:text-lg">
        <span class="text-xs text-gray-400 md:hidden">DISTANCE:</span>
        ${event.distance} miles
      </td>
    </tr>
  `).join('');
    }

    function formatDate(date, time) {
      const eventDate = new Date(date);
      const formattedDate = eventDate.toLocaleDateString('en-GB', {
        day: '2-digit',
        month: '2-digit',
        year: '2-digit'
      });

      // Format time to show only hours and minutes
      const [hours, minutes] = time.split(':');
      const formattedTime = `${hours}:${minutes}`;

      return `${formattedDate} ${formattedTime}`;
    }

    function escapeHtml(str) {
      if (!str) return '';
      return str
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
    }

    // Event Listeners
    distanceSelect.addEventListener('change', (e) => {
      fetchGigs(e.target.value);
    });

    // Initialize
    initializeLocation();
  });

  document.getElementById('refreshButton').addEventListener('click', function() {
    this.classList.add('animate-spin');
    fetchGigs(distanceSelect.value).finally(() => {
      this.classList.remove('animate-spin');
    });
  });
</script>
