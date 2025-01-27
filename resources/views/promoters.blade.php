<x-guest-layout>
  <x-slot name="header">
    <h1 class="text-center font-heading text-6xl text-white">Promoters</h1>
  </x-slot>

  <x-promoters-table :promoters="$promoters" :genres="$genres" :promoterVenueCount="$promoterVenueCount">
    @forelse ($promoters as $promoter)
      <tr class="border-gray-700 odd:bg-black even:bg-gray-900">
        <th scope="row"
          class="whitespace-nowrap px-2 py-2 font-sans text-white md:px-6 md:py-3 md:text-base lg:px-8 lg:py-4 lg:text-lg">
          <a href="{{ route('promoter', $promoter['name']) }}"
            class="promoter-link transition duration-150 ease-in-out hover:text-yns_yellow">{{ $promoter->name }}</a>
        </th>
        <td
          class="rating-wrapper px:2 py:2 hidden whitespace-nowrap sm:text-base md:px-6 md:py-3 lg:flex lg:px-8 lg:py-4">
          {!! $overallReviews[$promoter->id] !!}
        </td>
        <td
          class="whitespace-nowrap px-2 py-2 font-sans text-white md:px-6 md:py-3 md:text-base lg:px-8 lg:py-4 lg:text-lg">
          {{ $promoter->postal_town }}
        </td>
        <td
          class="hidden whitespace-nowrap px-2 py-2 align-middle text-white md:block md:px-6 md:py-3 md:text-base lg:px-8 lg:py-4 lg:text-lg">
          <x-contact-and-social-links :item="$promoter" />
        </td>
        @if ($promoterVenueCount != 0)
          <td
            class="{{ $promoters ? 'md:block' : 'hidden' }} whitespace-nowrap px-2 py-2 font-sans text-white md:px-6 md:py-3 md:text-base lg:px-8 lg:py-4 lg:text-lg">
            @foreach ($promoters->venues as $venue)
              <a class="hover:text-yns_yellow" href="{{ url('venues', $venue['name']) }}">{{ $venue['name'] }}</a>
            @endforeach
          </td>
        @endif
      </tr>
    @empty
      <tr class="border-b border-white bg-gray-900">
        <td colspan="4"
          class="px-2 py-2 text-center text-2xl text-white md:px-6 md:py-3 md:text-base lg:px-8 lg:py-4 lg:text-lg">No
          promoters found</td>
      </tr>
    @endforelse
  </x-promoters-table>
</x-guest-layout>
<script>
  $(document).ready(function() {
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
        const isEdit = document.getElementById(fieldKey + "-latitude").value != '' && document.getElementById(
          fieldKey +
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
    $('.filter-checkbox').change(function() {
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

    // Event handler for "All Genres" checkbox
    $("#all-genres").change(function() {
      var isChecked = $(this).prop("checked");
      $(".genre-checkbox, .subgenre-checkbox").prop("checked", isChecked);
      applyFilters();
    });

    // Attach event listener for genre checkboxes
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
      var searchQuery = jQuery('#address-input').val();

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
      });

      var mergedGenres = [...selectedGenres, ...selectedSubgenres];

      // Send AJAX request to fetch filtered data
      $.ajax({
        url: '/promoters/filter',
        method: 'POST',
        data: {
          _token: $('meta[name="csrf-token"]').attr('content'),
          search_query: searchQuery,
          band_type: bandTypeValue,
          genres: mergedGenres,
        },
        success: function(data) {
          // Update table with filtered data
          if (data.promoters && Array.isArray(data.promoters)) {
            updateTable(data);
          } else {
            console.error("The 'promoters' field is not an array or is missing:", data.promoters);
          }
        },
        error: function(err) {
          console.error('Error applying filters:', err);
        }

      });
    }

    // Define the updatepromotersTable function outside of the updateTable function
    function updateResultsTable(filteredPromoters) {
      if (!Array.isArray(filteredPromoters)) {
        console.error("filteredPromoters is not an array:", filteredPromoters);
        return;
      }
      // Generate HTML for the filtered promoters
      var rowsHtml = filteredPromoters.map(function(promoter) {
        var promoterRoute = "{{ route('promoter', ':promoterId') }}";
        var ratingHtml = getRatingHtml(promoter.average_rating);
        var promoterVenueCount = {{ $promoterVenueCount }};
        const className = promoterVenueCount > 0 ? 'md:block' : 'hidden';
        return `
            <tr class="border-gray-700 odd:bg-black even:bg-gray-900">
                <th scope="row" class="whitespace-nowrap px-2 py-2 font-sans text-white md:px-6 md:py-3 md:text-base lg:px-8 lg:py-4 lg:text-lg">
                    <a href="${promoterRoute.replace(':promoterId', promoter.id)}" class="promoter-link transition duration-150 ease-in-out hover:text-yns_yellow">${promoter.name}</a>
                </th>
                <td class="rating-wrapper px:2 py:2 hidden whitespace-nowrap sm:text-base md:px-6 md:py-3 lg:flex lg:px-8 lg:py-4">
                    ${ratingHtml}
                </td>
                <td class="whitespace-nowrap px-2 py-2 font-sans text-white md:px-6 md:py-3 md:text-base lg:px-8 lg:py-4 lg:text-lg">
                    ${promoter.postal_town}
                </td>
                <td class="hidden whitespace-nowrap px-2 py-2 align-middle text-white md:block md:px-6 md:py-3 md:text-base lg:px-8 lg:py-4 lg:text-lg">
                    ${promoter.contact_number ? '<a href="tel:' + promoter.contact_number + '" class="hover:text-yns_yellow mr-2 transition duration-150 ease-in-out"><span class="fas fa-phone"></span></a>' : ''}
                    ${promoter.contact_email ? '<a href="mailto:' + promoter.contact_email + '" class="hover:text-yns_yellow mr-2 transition duration-150 ease-in-out"><span class="fas fa-envelope"></span></a>' : ''}
                    ${promoter.platforms ? promoter.platforms.map(function(platform) {
                        switch (platform.platform) {
                            case 'facebook':
                                return '<a class="hover:text-yns_yellow mr-2 transition duration-150 ease-in-out" href="' + platform.url + '" target="_blank"><span class="fab fa-facebook"></span></a>';
                            case 'twitter':
                                return '<a class="hover:text-yns_yellow mr-2 transition duration-150 ease-in-out" href="' + platform.url + '" target="_blank"><span class="fab fa-twitter"></span></a>';
                            case 'instagram':
                                return '<a class="hover:text-yns_yellow mr-2 transition duration-150 ease-in-out" href="' + platform.url + '" target="_blank"><span class="fab fa-instagram"></span></a>';
                            case 'snapchat':
                                return '<a class="hover:text-yns_yellow mr-2 transition duration-150 ease-in-out" href="' + platform.url + '" target="_blank"><span class="fab fa-snapchat-ghost"></span></a>';
                            case 'tiktok':
                                return '<a class="hover:text-yns_yellow mr-2 transition duration-150 ease-in-out" href="' + platform.url + '" target="_blank"><span class="fab fa-tiktok"></span></a>';
                            case 'youtube':
                                return '<a class="hover:text-yns_yellow mr-2 transition duration-150 ease-in-out" href="' + platform.url + '" target="_blank"><span class="fab fa-youtube"></span></a>';
                            case 'bluesky':
                                return '<a class="hover:text-yns_yellow mr-2 transition duration-150 ease-in-out" href="' + platform.url + '" target="_blank"><span class="fa-brands fa-bluesky"></span></a>';
                            default:
                                return '';
                        }
                    }).join('') : ''}
                </td>
                <td class="whitespace-nowrap ${className} font-sans  text-white px-2 py-2 md:px-6 md:py-3 md:text-base lg:px-8 lg:py-4 lg:text-lg">
                    ${promoter.venues ? promoter.venues.map(function(venue) {
                        return '<a href="' + venue.url + '">' + venue.name + '</a>';
                    }).join('') : ''}
                </td>
            </tr>
        `;
      }).join('');
      // Replace the existing HTML content with the new HTML
      jQuery('#promoters tbody').html(rowsHtml);
    }

    // Function to generate HTML for the rating
    function getRatingHtml(rating) {
      if (rating === undefined || rating === null) return '';

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

    // Update the updateTable function to pass the filtered promoters to updatepromotersTable
    function updateTable(data) {
      var promoters = data.promoters;
      var pagination = data.pagination;

      // Check if data is not null or empty array
      if (data.promoters && data.promoters.length > 0) {
        // Append new rows based on filtered data
        updateResultsTable(data.promoters);
      } else {
        // Display message if no promoters found
        var noPromotersRow = `
            <tr class="border-b border-white bg-gray-900">
                <td colspan="4" class="text-center text-2xl text-white px-2 py-2 md:px-6 md:py-3 md:text-base lg:px-8 lg:py-4 lg:text-lg">No promoters found</td>
            </tr>
        `;
        jQuery('#promoters tbody').html(noPromotersRow);
      }
    }

    function setLocationCoordinates(key, lat, lng) {
      const latitudeField = document.getElementById(key + "-" + "latitude");
      const longitudeField = document.getElementById(key + "-" + "longitude");
      latitudeField.value = lat;
      longitudeField.value = lng;
    }
  });
</script>
