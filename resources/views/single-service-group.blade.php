<x-guest-layout>
  <x-slot name="header">
    <h1 class="text-whitel text-center font-heading text-6xl">
      {{ $serviceName }}</h1>
  </x-slot>

  <x-other-service-table :singleServices="$singleServices" :genres="$genres" :serviceName="$serviceName">
    @forelse ($singleServices as $service)
      <tr class="border-gray-700 odd:bg-black even:bg-gray-900">
        <th scope="row"
          class="whitespace-nowrap px-2 py-2 font-sans text-white md:px-6 md:py-3 md:text-base lg:px-8 lg:py-4 lg:text-lg">
          <a href="{{ route('singleService', ['serviceName' => $service->services, 'serviceId' => $service->id]) }}"
            class="transition duration-150 ease-in-out hover:text-yns_yellow">{{ $service->name }}</a>
        </th>
        <td
          class="rating-wrapper px:2 py:2 hidden whitespace-nowrap sm:text-base md:px-6 md:py-3 lg:flex lg:px-8 lg:py-4">
          {!! $overallReviews[$service->id] !!}
        </td>
        <td
          class="whitespace-nowrap px-2 py-2 font-sans text-white md:px-6 md:py-3 md:text-base lg:px-8 lg:py-4 lg:text-lg">
          {{ $service->postal_town }}
        </td>

        <td
          class="hidden whitespace-nowrap px-2 py-2 align-middle text-white md:block md:px-6 md:py-3 md:text-base lg:px-8 lg:py-4 lg:text-lg">
          <x-contact-and-social-links :item="$service" />
        </td>
      </tr>
    @empty
      <tr class="border-b border-white bg-gray-900">
        <td colspan="4"
          class="px-2 py-2 text-center text-2xl text-white md:px-6 md:py-3 md:text-base lg:px-8 lg:py-4 lg:text-lg">No
          services found</td>
      </tr>
    @endforelse
  </x-other-service-table>
</x-guest-layout>
<script>
  // Search Bar
  function initialize() {
    jQuery('form').on('keyup keypress', function(e) {
      var keyCode = e.keyCode || e.which;
      if (keyCode === 13) {
        return false;
      }
    });
    const locationInputs = document.getElementsByClassName("map-input");

    const autocompletes = [];
    const geocoder = new google.maps.Geocoder;
    for (let i = 0; i < locationInputs.length; i++) {

      const input = locationInputs[i];
      const fieldKey = input.id.replace("-input", "");
      const isEdit = document.getElementById(fieldKey + "-latitude").value != '' && document.getElementById(fieldKey +
        "-longitude").value != '';

      const latitude = parseFloat(document.getElementById(fieldKey + "-latitude").value) || 59.339024834494886;
      const longitude = parseFloat(document.getElementById(fieldKey + "-longitude").value) || 18.06650573462189;

      const map = new google.maps.Map(document.getElementById(fieldKey + '-map'), {
        center: {
          lat: latitude,
          lng: longitude
        },
        zoom: 13
      });
      const marker = new google.maps.Marker({
        map: map,
        position: {
          lat: latitude,
          lng: longitude
        },
      });

      marker.setVisible(isEdit);

      const autocomplete = new google.maps.places.Autocomplete(input);
      autocomplete.key = fieldKey;
      autocompletes.push({
        input: input,
        map: map,
        marker: marker,
        autocomplete: autocomplete
      });
    }

    for (let i = 0; i < autocompletes.length; i++) {
      const input = autocompletes[i].input;
      const autocomplete = autocompletes[i].autocomplete;
      const map = autocompletes[i].map;
      const marker = autocompletes[i].marker;

      google.maps.event.addListener(autocomplete, 'place_changed', function() {
        marker.setVisible(false);
        const place = autocomplete.getPlace();

        geocoder.geocode({
          'placeId': place.place_id
        }, function(results, status) {
          if (status === google.maps.GeocoderStatus.OK) {
            const lat = results[0].geometry.location.lat();
            const lng = results[0].geometry.location.lng();
            setLocationCoordinates(autocomplete.key, lat, lng);
          }
        });

        if (!place.geometry) {
          window.alert("No details available for input: '" + place.name + "'");
          input.value = "";
          return;
        }

        if (place.geometry.viewport) {
          map.fitBounds(place.geometry.viewport);
        } else {
          map.setCenter(place.geometry.location);
          map.setZoom(17);
        }
        marker.setPosition(place.geometry.location);
        marker.setVisible(true);
      });
    }
  }

  // Attach event listener for filter checkboxes
  jQuery('.filter-checkbox').change(function() {
    applyFilters();
  });

  // Attach event listener for search input
  let debounceTimeout;
  $('#address-input').on('input', function() {
    clearTimeout(debounceTimeout);
    debounceTimeout = setTimeout(() => {
      applyFilters();
    }, 300); // Adjust debounce delay as needed
  });

  // Event handler for "All Types" checkbox
  $("#all-bands").change(function() {
    var isChecked = $(this).prop("checked");
    $(".filter-checkbox").prop("checked", isChecked);
    applyFilters();
  });

  $("#all-genres").change(function() {
    var isChecked = $(this).prop("checked");
    $(".genre-checkbox, .subgenre-checkbox").prop("checked", isChecked);
    applyFilters();
  });

  $('.genre-checkbox').change(function() {
    var genreId = $(this).data('genre-id');
    var isChecked = $(this).prop('checked');
    $(`.subgenre-checkbox[data-genre-id="${genreId}"]`).prop('checked', isChecked);

    // Uncheck "All Genres" if a single genre is deselected
    if (!isChecked) {
      $('#all-genres').prop('checked', false);
    }
    applyFilters();
  });

  $('.subgenre-checkbox').change(function() {
    // Uncheck "All Genres" if any subgenre is deselected
    $('#all-genres').prop('checked', false);
    applyFilters();
  });

  function applyFilters() {
    var bandTypeValue = [];
    var selectedGenres = [];
    var selectedSubgenres = [];
    var searchQuery = $('#address-input').val();

    // Collect selected band types
    $('.filter-checkbox:checked').each(function() {
      bandTypeValue.push($(this).val());
    });

    // Collect selected genres
    $('.genre-checkbox:checked').each(function() {
      selectedGenres.push($(this).val());
    });

    // Collect selected subgenres
    $('.subgenre-checkbox:checked').each(function() {
      selectedSubgenres.push($(this).val());
      console.log(selectedSubgenres);
    });

    // Combine genres and subgenres
    var serviceType = '{{ $serviceName }}';

    // Send AJAX request to fetch filtered data
    $.ajax({
      url: `/other/${serviceType}/filter`,
      method: 'POST',
      data: {
        _token: $('meta[name="csrf-token"]').attr('content'),
        search_query: searchQuery,
        band_type: bandTypeValue,
        genres: selectedGenres,
        subgenres: selectedSubgenres,
        serviceType: serviceType,
      },
      success: function(data) {
        console.log(Array.isArray(data.results));
        // Extract venues array and pass it to the function
        if (data.results && Array.isArray(data.results)) {
          updateTable(data);
        } else {
          console.error("The 'otherServices' field is not an array or is missing:", data.otherServices);
        }
      },
      error: function(err) {
        console.error('Error applying filters:', err);
      }
    });
  }

  function updateResultsTable(filteredOtherServices, pagination) {
    if (!Array.isArray(filteredOtherServices)) {
      console.error("filteredOtherServices is not an array:", filteredOtherServices);
      return;
    }

    console.log(typeof(filteredOtherServices));

    var otherRoute = "{{ route('singleService', [':serviceName', ':serviceId']) }}";

    var rowsHtml = filteredOtherServices.map(function(otherService) {
      var finalRoute = otherRoute
        .replace(':serviceName', otherService.service_type)
        .replace(':serviceId', otherService.id);

      var ratingHtml = getRatingHtml(otherService.average_rating);
      var platformsHtml = '';

      if (otherService.platforms && Array.isArray(otherService.platforms)) {
        platformsHtml = otherService.platforms.map(function(platform) {
          try {
            // Access platform properties directly
            const platformType = platform.platform;
            const url = platform.url;

            let icon = '';
            switch (platformType.toLowerCase()) {
              case 'facebook':
                icon = 'fab fa-facebook';
                break;
              case 'instagram':
                icon = 'fab fa-instagram';
                break;
              case 'twitter':
                icon = 'fab fa-twitter';
                break;
              case 'website':
                icon = 'fas fa-globe';
                break;
              case 'snapchat':
                icon = 'fab fa-snapchat-ghost';
                break;
              case 'youtube':
                icon = 'fab fa-youtube';
                break;
              case 'tiktok':
                icon = 'fab fa-tiktok';
                break;
              case 'bluesky':
                icon = 'fa-brands fa-bluesky';
                break;
              default:
                icon = 'fas fa-link';
            }

            return `<a href="${url}" target="_blank" class="hover:text-yns_yellow mr-2 transition duration-150 ease-in-out">
        <span class="${icon}"></span>
      </a>`;
          } catch (e) {
            console.error('Error processing platform:', platform);
            return '';
          }
        }).join('');
      }

      return `
      <tr class="border-gray-700 odd:bg-black even:bg-gray-900">
        <th scope="row" class="whitespace-nowrap px-2 py-2 font-sans text-white md:px-6 md:py-3 md:text-base lg:px-8 lg:py-4 lg:text-lg">
          <a href="${finalRoute}" class="venue-link transition duration-150 ease-in-out hover:text-yns_yellow">${otherService.name}</a>
        </th>
        <td class="rating-wrapper px:2 py:2 hidden whitespace-nowrap sm:text-base md:px-6 md:py-3 lg:flex lg:px-8 lg:py-4">
          ${ratingHtml}
        </td>
        <td class="whitespace-nowrap px-2 py-2 font-sans text-white md:px-6 md:py-3 md:text-base lg:px-8 lg:py-4 lg:text-lg">
          ${otherService.postal_town || ''}
        </td>
        <td class="hidden whitespace-nowrap px-2 py-2 align-middle text-white md:block md:px-6 md:py-3 md:text-base lg:px-8 lg:py-4 lg:text-lg">
          ${otherService.contact_number ? 
            `<a href="tel:${otherService.contact_number}" class="hover:text-yns_yellow mr-2 transition duration-150 ease-in-out">
              <span class="fas fa-phone"></span>
            </a>` : ''}
          ${otherService.contact_email ? 
            `<a href="mailto:${otherService.contact_email}" class="hover:text-yns_yellow mr-2 transition duration-150 ease-in-out">
              <span class="fas fa-envelope"></span>
            </a>` : ''}
          ${platformsHtml}
        </td>
      </tr>`;
    }).join('');

    $('#resultsTableBody').html(rowsHtml);
  }

  // Function to generate HTML for the rating
  function getRatingHtml(rating) {
    if (rating === undefined || rating === null) return ''; // Return empty if no rating

    var ratingHtml = '';
    var totalIcons = 5;
    var fullIcons = Math.floor(rating);
    var fraction = rating - fullIcons;
    var emptyIcon = "{{ asset('storage/images/system/ratings/empty.png') }}";
    var fullIcon = "{{ asset('storage/images/system/ratings/full.png') }}";
    var hotIcon = "{{ asset('storage/images/system/ratings/hot.png') }}";

    // Special case: all icons are hot
    if (rating === totalIcons) {
      ratingHtml = Array(totalIcons).fill('<img src="' + hotIcon + '" alt="Hot Icon" />').join('');
    } else {
      // Add full icons
      for (var i = 0; i < fullIcons; i++) {
        ratingHtml += '<img src="' + fullIcon + '" alt="Full Icon" />';
      }

      // Handle the fractional icon
      if (fraction > 0) {
        ratingHtml += '<div class="partially-filled-icon" style="width: ' + (fraction * 48) +
          'px; overflow: hidden; display:inline-block;">' +
          '<img src="' + fullIcon + '" alt="Partial Full Icon" />' +
          '</div>';
      }

      // Add empty icons to fill the rest
      var iconsDisplayed = fullIcons + (fraction > 0 ? 1 : 0);
      var remainingIcons = totalIcons - iconsDisplayed;

      for (var j = 0; j < remainingIcons; j++) {
        ratingHtml += '<img src="' + emptyIcon + '" alt="Empty Icon" />';
      }
    }

    return ratingHtml;
  }

  // Update the updateTable function to pass the filtered venues to updateVenuesTable
  function updateTable(data) {
    console.log("Data:", data); // Check the entire data object

    // If the data doesn't have otherServices, we'll log an error and exit
    if (!data || !data.results) {
      console.error("Other services data is missing or undefined.");
      return; // Exit the function if the data is not structured as expected
    }

    var otherServices = data.results;
    var pagination = data.pagination;

    console.log("Other Services:", otherServices);

    // Check if data is not null or empty array
    if (otherServices && otherServices.length > 0) {
      updateResultsTable(otherServices, pagination);
    } else {
      var noOtherServicesRow = `
            <tr class=" border-gray-700 odd:dark:bg-black even:dark:bg-gray-900">
                <td colspan="5" class="whitespace-nowrap font-sans text-white sm:px-2 sm:py-3 sm:text-base md:px-6 md:py-2 md:text-lg lg:px-8 lg:py-4 uppercase text-center">No Services Found</td>
            </tr>
        `;
      jQuery('#resultsTableBody').html(noOtherServicesRow);
    }
  }

  function setLocationCoordinates(key, lat, lng) {
    const latitudeField = document.getElementById(key + "-" + "latitude");
    const longitudeField = document.getElementById(key + "-" + "longitude");
    latitudeField.value = lat;
    longitudeField.value = lng;
  }
</script>
