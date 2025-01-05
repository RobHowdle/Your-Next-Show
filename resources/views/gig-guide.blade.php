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

    <div class="py-8">
      <div class="relative z-0 overflow-x-auto">
        <div class="flex items-center justify-around border border-white bg-black py-4 text-center">
          <div class="group">
            <label for="distance" class="text-lg text-white">Show gigs within:</label>
            <x-select id="distance" name="distance" :options="[
                '5' => '5 miles',
                '10' => '10 miles',
                '20' => '20 miles',
                '50' => '50 miles',
                '100' => '100 miles',
                'all' => 'Show all',
            ]" :selected="['5']" />
          </div>
          <div class="group flex items-center gap-2">
            <x-input-label-dark class="mb-0">Show Other Gigs</x-input-label-dark>
            <x-input-checkbox id="show-other-gigs" name="showOtherGigs"></x-input-checkbox>
          </div>
        </div>
        <table class="w-full border border-white text-left font-sans rtl:text-right">
          <thead class="text-white underline dark:bg-black">
            <tr>
              <th scope="col" class="md-text-2xl sm:px-2 sm:py-2 sm:text-xl md:px-6 md:py-3 lg:px-8 lg:py-4">Event
              </th>
              <th scope="col" class="md-text-2xl sm:px-2 sm:py-2 sm:text-xl md:px-6 md:py-3 lg:px-8 lg:py-4">Location
              </th>
              <th scope="col" class="md-text-2xl sm:px-2 sm:py-2 sm:text-xl md:px-6 md:py-3 lg:px-8 lg:py-4">Approx
                Distance
              </th>
              <th scope="col" class="md-text-2xl sm:px-2 sm:py-2 sm:text-xl md:px-6 md:py-3 lg:px-8 lg:py-4">Date
              </th>
            </tr>
          </thead>
          <tbody id="gigsTableBody">
            @if ($gigsCloseToMe)
              @include('partials.gigs_table_row', ['gigsCloseToMe' => $gigsCloseToMe])
            @endif
            @if ($otherGigs)
              @foreach ($otherGigs as $event)
                @include('partials.gigs_table_row', ['event' => $event, 'distance' => 'All'])
              @endforeach
            @endif
            @if ($gigsCloseToMe == 0 && $otherGigs == 0)
              <tr class="odd:bg-black even:bg-gray-900 dark:border-gray-700">
                <td colspan="4"
                  class="whitespace-nowrap text-center font-sans text-white sm:px-2 sm:py-3 md:px-6 md:py-2 lg:px-8 lg:py-4">
                  No gigs found</td>
              </tr>
            @endif
          </tbody>
        </table>
      </div>
    </div>
  </div>
</x-guest-layout>
<script>
  let userLatitude = {{ $user->latitude ?? 'null' }};
  let userLongitude = {{ $user->longitude ?? 'null' }};
  const loader = document.createElement('div'); // Loader Element for Feedback
  loader.innerHTML = '<p class="text-white">Fetching your location...</p>';

  if (!userLatitude || !userLongitude) {
    getUserLocation((lat, long) => {
      userLatitude = lat;
      userLongitude = long;
      // console.log("Location retrieved via browser: ", userLatitude, userLongitude);
    });
  }

  function getUserLocation(callback) {
    if (userLatitude && userLongitude) {
      callback(userLatitude, userLongitude);
    } else {
      // console.log("No stored location. Attempting to get geolocation from the browser...");
      if (navigator.geolocation) {
        // Show loading feedback
        document.body.appendChild(loader);

        navigator.geolocation.getCurrentPosition(
          function(position) {
            const latitude = position.coords.latitude;
            const longitude = position.coords.longitude;
            document.body.removeChild(loader); // Remove loader
            callback(latitude, longitude);
            fetchGigs(getSelectedDistance(), latitude, longitude, isShowOtherChecked());
          },
          function(error) {
            document.body.removeChild(loader); // Remove loader on error
            // console.log("Error handler hit");
            handleGeolocationError(error);
            fallbackToManualLocation();
          }, {
            timeout: 10000
          } // Timeout after 10 seconds
        );
      } else {
        alert("Geolocation is not supported by this browser.");
        fallbackToManualLocation();
      }
    }
  }

  function handleGeolocationError(error) {
    switch (error.code) {
      case error.PERMISSION_DENIED:
        showFailureNotification(
          "Unable to get your location. Please enable location settings to find gigs near you."
        );
        break;
      case error.POSITION_UNAVAILABLE:
        showFailureNotification(
          "Something went wrong, and we couldn't get the gigs. Please try again later!"
        );
        break;
      case error.TIMEOUT:
        showFailureNotification("Request Timed Out - Sorry about that!");
        break;
      case error.UNKNOWN_ERROR:
        showFailureNotification(
          "Well, this is awkward. Something went wrong - we'll take a look."
        );
        break;
    }
  }

  function fallbackToManualLocation() {
    const manualLocationInput = prompt(
      "We couldn't fetch your location. Please enter your city or postcode:"
    );
    if (manualLocationInput) {
      fetch(`/gigs/manual-location?query=${encodeURIComponent(manualLocationInput)}`)
        .then((response) => response.json())
        .then((data) => {
          if (data.latitude && data.longitude) {
            // console.log("Fallback manual location retrieved: ", data.latitude, data.longitude);
            fetchGigs(getSelectedDistance(), data.latitude, data.longitude, isShowOtherChecked());
          } else {
            showFailureNotification(
              "Unable to determine your location from the provided input. Please try again."
            );
          }
        })
        .catch((error) => {
          // console.error("Error processing manual location:", error);
          showFailureNotification("Something went wrong. Please try again later.");
        });
    }
  }

  function getSelectedDistance() {
    const distanceInput = document.getElementById("distance");
    return distanceInput.value;
  }

  function isShowOtherChecked() {
    return document.getElementById("show-other-gigs").checked;
  }

  document.addEventListener("DOMContentLoaded", () => {
    const distanceInput = document.getElementById("distance");
    distanceInput.addEventListener("change", () => {
      const distance = getSelectedDistance();
      const latitude = userLatitude;
      const longitude = userLongitude;
      const showOther = isShowOtherChecked();

      fetchGigs(distance, latitude, longitude, showOther);
    });
  });

  function fetchGigs(distance, latitude, longitude, showOther) {
    fetch(`/gigs/filter?distance=${distance}&latitude=${latitude}&longitude=${longitude}&showOther=${showOther}`)
      .then((response) => response.json())
      .then((data) => {
        if (data.error) {
          // console.error(data.error);
          return;
        }

        const tableBody = document.getElementById("gigsTableBody");
        tableBody.innerHTML = "";

        data.gigsCloseToMe.forEach((event) => {
          const roundedDistance = parseFloat(event.distance).toFixed(2);
          tableBody.innerHTML += createRow(event, `${roundedDistance} miles`);
        });

        if (data.otherGigs) {
          data.otherGigs.forEach((event) => {
            tableBody.innerHTML += createRow(event, "All");
          });
        }
      })
      .catch((error) => console.error("Error fetching gigs:", error));
  }

  function formatDate(eventDate, eventStartTime) {
    const date = new Date(eventDate);
    const options = {
      day: '2-digit',
      month: '2-digit',
      year: '2-digit'
    };
    const formattedDate = new Intl.DateTimeFormat('en-GB', options).format(date);
    return `${formattedDate} ${eventStartTime}`;
  }


  function createRow(event, distanceLabel) {
    // console.log(event);
    const formattedDateTime = formatDate(event.event_date, event.event_start_time);
    const eventUrl = `/events/${event.id}`;
    const venueUrl = `/venues/${event.venue_id}`;
    return `
        <tr class=" border-gray-700 odd:dark:bg-black even:dark:bg-gray-900">
           <td class="whitespace-nowrap font-sans text-white sm:px-2 sm:py-3 md:px-6 md:py-2 lg:px-8 lg:py-4">
                <a href="${eventUrl}" class="underline hover:text-yns_yellow transition duration-150 ease-in-out">${event.event_name}</a>
            </td>
            <td class="whitespace-nowrap font-sans text-white sm:px-2 sm:py-3 md:px-6 md:py-2 lg:px-8 lg:py-4">
                <a href="${venueUrl}" class="underline hover:text-yns_yellow transition duration-150 ease-in-out">${event.name}</a>
            </td>            
            <td class="whitespace-nowrap font-sans text-white sm:px-2 sm:py-3 md:px-6 md:py-2 lg:px-8 lg:py-4">${distanceLabel}</td>
            <td class="whitespace-nowrap font-sans text-white sm:px-2 sm:py-3 md:px-6 md:py-2 lg:px-8 lg:py-4">${formattedDateTime}</td>
        </tr>
    `;
  }
</script>
