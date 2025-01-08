<x-guest-layout>
  <x-slot name="header">
    <h1 class="text-center font-heading text-6xl text-white">Andy's Gig Guide</h1>
  </x-slot>
  <div class="mx-auto min-h-screen w-full max-w-screen-2xl pt-32">
    <div class="group text-center">
      <h1 class="py-8 font-heading text-6xl text-white">Andy's Gig Guide</h1>
      <div class="bg-opac_8_black px-6 py-8">
        <p class="font-sans text-white">Back in the day, Andy C Jennings would create a Gig Guide on his Facebook page,
          spending time collecting,
          organizing, and listing weekly gigs up and down the country to help promote local music. This was hugely
          appreciated by many people and was often one of the first places they would hear about gigs in their local
          area.
        </p>
        <p class="font-sans text-white">Andy no longer makes these gig guides sadly, as you can imagine, it’s very
          time-consuming and a lot of work to do
          entirely for free.</p>
        <p class="font-sans text-white">As a thank you, we have dedicated our gig guide to Andy and recreated it in a
          similar format (with a few
          YNS tweaks).</p>
      </div>
    </div>

    <div class="mb-4 px-4">
      <label for="distance" class="mr-2 text-white">Show events within:</label>
      <select id="distance" class="rounded border border-gray-300 bg-white px-3 py-2">
        <option value="5" selected>5 miles</option>
        <option value="10">10 miles</option>
        <option value="25">25 miles</option>
        <option value="50">50 miles</option>
        <option value="100">100 miles</option>
      </select>
    </div>

    <!-- Loading State -->
    <div id="loading" class="hidden">
      <div class="flex items-center justify-center p-4">
        <div class="h-8 w-8 animate-spin rounded-full border-4 border-yns_yellow border-t-transparent"></div>
      </div>
    </div>

    <!-- Results Table -->
    <table class="w-full border border-white text-left font-sans">
      <thead class="bg-black text-white">
        <tr>
          <th class="px-2 py-2 text-base md:px-6 md:py-3 md:text-xl lg:px-8 lg:py-4 lg:text-2xl">Date</th>
          <th class="px-2 py-2 text-base md:px-6 md:py-3 md:text-xl lg:px-8 lg:py-4 lg:text-2xl">Event</th>
          <th class="px-2 py-2 text-base md:px-6 md:py-3 md:text-xl lg:px-8 lg:py-4 lg:text-2xl">Venue</th>
          <th class="px-2 py-2 text-base md:px-6 md:py-3 md:text-xl lg:px-8 lg:py-4 lg:text-2xl">Approx Distance</th>
        </tr>
      </thead>
      <tbody id="gigsTableBody"></tbody>
    </table>
  </div>
</x-guest-layout>
<script>
  $(document).ready(function() {
    let userLatitude = null;
    let userLongitude = null;

    // Check for profile location
    const userLocation = @json($userLocation);

    if (userLocation) {
      userLatitude = userLocation.latitude;
      userLongitude = userLocation.longitude;
      fetchGigs(document.getElementById('distance').value);
    } else if (navigator.geolocation) {
      // Fallback to browser geolocation
      navigator.geolocation.getCurrentPosition(position => {
        userLatitude = position.coords.latitude;
        userLongitude = position.coords.longitude;
        fetchGigs(document.getElementById('distance').value);
      }, error => {
        console.error('Geolocation error:', error);
        document.getElementById('gigsTableBody').innerHTML =
          '<tr><td colspan="4" class="p-4 text-center text-white">Please enable location services or set your location in your profile</td></tr>';
      });
    } else {
      document.getElementById('gigsTableBody').innerHTML =
        '<tr><td colspan="4" class="p-4 text-center text-white">Location services not available</td></tr>';
    }

    function fetchGigs(distance) {
      if (!userLatitude || !userLongitude) return;

      const loading = document.getElementById('loading');
      loading.classList.remove('hidden');

      fetch(`/gigs/filter?distance=${distance}&latitude=${userLatitude}&longitude=${userLongitude}`)
        .then(response => response.json())
        .then(data => {
          const tableBody = document.getElementById('gigsTableBody');
          tableBody.innerHTML = '';

          if (!data.events.length) {
            tableBody.innerHTML =
              '<tr><td colspan="4" class="p-4 text-center text-white">No events found within this distance</td></tr>';
            return;
          }

          data.events.forEach(event => {
            const row = `
                            <tr class="border-gray-700 odd:bg-black even:bg-gray-900">
                                <td class="whitespace-nowrap px-2 py-2 font-sans text-white md:px-6 md:py-3 md:text-base lg:px-8 lg:py-4 lg:text-lg">${formatDate(event.date, event.start_time)}</td>
                                <td class="whitespace-nowrap px-2 py-2 font-sans text-white md:px-6 md:py-3 md:text-base lg:px-8 lg:py-4 lg:text-lg">
                                    <a href="/events/${event.id}" class="hover:text-yns_yellow">
                                        ${event.name}<br>
                                        <span class="text-sm text-gray-400">${event.headliner || ''}</span>
                                    </a>
                                </td>
                                <td class="whitespace-nowrap px-2 py-2 font-sans text-white md:px-6 md:py-3 md:text-base lg:px-8 lg:py-4 lg:text-lg">
                                    ${event.venue_name}<br>
                                    <span class="text-sm text-gray-400">${event.venue_town}</span>
                                </td>
                                <td class="whitespace-nowrap px-2 py-2 font-sans text-white md:px-6 md:py-3 md:text-base lg:px-8 lg:py-4 lg:text-lg">${event.distance} miles</td>
                            </tr>
                        `;
            tableBody.innerHTML += row;
          });
        })
        .catch(error => {
          console.error('Error:', error);
          document.getElementById('gigsTableBody').innerHTML =
            '<tr><td colspan="4" class="p-4 text-center text-white">Error loading events</td></tr>';
        })
        .finally(() => {
          loading.classList.add('hidden');
        });
    }

    document.getElementById('distance').addEventListener('change', (e) => {
      console.log('change');
      fetchGigs(e.target.value);
    });

    function formatDate(date, time) {
      const eventDate = new Date(date);
      const formattedDate = eventDate.toLocaleDateString('en-GB', {
        day: '2-digit',
        month: '2-digit',
        year: '2-digit'
      });
      return `${formattedDate} ${time}`;
    }
  });
</script>
