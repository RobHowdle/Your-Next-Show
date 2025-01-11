<x-app-layout :dashboardType="$dashboardType" :modules="$modules">
  <x-slot name="header">
    <x-sub-nav :userId="$userId" />
  </x-slot>

  <div class="mx-auto w-full max-w-screen-2xl py-16">
    <div class="relative mb-8 shadow-md sm:rounded-lg">
      <div class="min-w-screen-xl mx-auto max-w-screen-xl rounded-lg border border-white bg-yns_dark_gray text-white">
        <div class="header px-8 pt-8">
          <h1 class="mb-8 font-heading text-4xl font-bold">New Event</h1>
        </div>
        <form id="eventForm" method="POST" enctype="multipart/form-data" data-dashboard-type="{{ $dashboardType }}">
          @csrf

          <div class="grid grid-cols-3 gap-x-8 px-8 py-8">
            <div class="col">
              <input type="hidden" id="dashboard_type" value="{{ $dashboardType }}">
              <div class="group mb-4">
                <x-input-label-dark :required="true">Event Name</x-input-label-dark>
                <x-text-input id="event_name" name="event_name" :required="true" :value="old('event_name')"></x-text-input>
                @error('event_name')
                  <p class="yns_red mt-1 text-sm">{{ $message }}</p>
                @enderror
              </div>
              <div class="group mb-4">
                <x-input-label-dark :required="true">Event Date</x-input-label-dark>
                <x-date-input id="event_date" name="event_date"
                  class="w-full rounded-lg border-gray-300 focus:border-yellow-500 focus:ring-yellow-500"
                  :required="true" value="{{ old('event_date') }}"></x-date-input>
                @error('event_date')
                  <p class="yns_red mt-1 text-sm">{{ $message }}</p>
                @enderror
              </div>

              <div class="group mb-4">
                <x-input-label-dark :required="true">Event Start Time</x-input-label-dark>
                <x-time-input id="event_start_time" name="event_start_time"
                  class="w-full rounded-lg border-gray-300 focus:border-yellow-500 focus:ring-yellow-500"
                  :required="true" value="{{ old('event_start_time') }}"></x-time-input>
                @error('event_start_time')
                  <p class="yns_red mt-1 text-sm">{{ $message }}</p>
                @enderror
              </div>

              <div class="group mb-4">
                <x-input-label-dark>Event End Time</x-input-label-dark>
                <x-time-input id="event_end_time" name="event_end_time"
                  class="w-full rounded-lg border-gray-300 focus:border-yellow-500 focus:ring-yellow-500"
                  value="{{ old('event_end_time') }}"></x-time-input>
                @error('event_end_time')
                  <p class="yns_red mt-1 text-sm">{{ $message }}</p>
                @enderror
              </div>
              @if ($dashboardType === 'promoter')
                <div class="group mb-4 hidden">
                  <x-input-label-dark :required="true">Promoter</x-input-label-dark>
                  <span>This is supposed to be hidden...naughty naughty</span>
                  <x-text-input class="w-auto" id="promoter_id" name="promoter_id" value="{{ $role->id }}"
                    :required="true"></x-text-input>
                  @error('promoter_id')
                    <p class="yns_red mt-1 text-sm">{{ $message }}</p>
                  @enderror
                </div>
              @else
                <div class="group mb-4">
                  <x-input-label-dark>Promoter</x-input-label-dark>
                  <x-text-input id="promoter_name" name="promoter_name" autocomplete="off"
                    :value="old('')"></x-text-input>
                  <ul id="promoter-suggestions"
                    class="max-h-60 absolute z-10 hidden overflow-auto border border-gray-300 bg-white">
                  </ul>
                  <x-input-label-dark>Promoter ID</x-input-label-dark>
                  <x-text-input id="promoter_ids" name="promoter_ids" :value="old('')"></x-text-input>
                  <ul id="promoter-suggestions"
                    class="absolute z-10 mt-1 hidden rounded-md border border-gray-300 bg-white shadow-lg">
                  </ul>
                  @error('promoter_name')
                    <p class="yns_red mt-1 text-sm">{{ $message }}</p>
                  @enderror
                </div>
              @endif

              <div class="group mb-4">
                <x-input-label-dark :required="true">Description</x-input-label-dark>
                <x-textarea-input id="event_description" name="event_description" class="w-full" :required="true"
                  :value="old('')"></x-textarea-input>
                @error('event_description')
                  <p class="yns_red mt-1 text-sm">{{ $message }}</p>
                @enderror
              </div>

              <div class="group mb-4">
                <x-input-label-dark>Facebook Event Link</x-input-label-dark>
                <x-text-input id="facebook_event_url" name="facebook_event_url" :value="old('')"></x-text-input>
                @error('facebook_event_url')
                  <p class="yns_red mt-1 text-sm">{{ $message }}</p>
                @enderror
              </div>
              <div class="group mb-4">
                <x-input-label-dark>Pre Sale Ticket Link</x-input-label-dark>
                <x-text-input id="ticket_url" name="ticket_url" :value="old('')"></x-text-input>
                @error('ticket_url')
                  <p class="yns_red mt-1 text-sm">{{ $message }}</p>
                @enderror
              </div>
              <div class="group mb-4">
                <x-input-label-dark>Door Ticket Price</x-input-label-dark>
                <x-number-input-pound id="on_the_door_ticket_price" name="on_the_door_ticket_price"
                  :value="old('')"></x-number-input-pound>
                @error('on_the_door_ticket_price')
                  <p class="yns_red mt-1 text-sm">{{ $message }}</p>
                @enderror
              </div>
            </div>
            <div class="col">
              <div class="group">
                <x-input-label-dark :required="true">Poster</x-input-label-dark>
                <x-input-file id="poster_url" name="poster_url" :required="true" :value="old('')"></x-input-file>
                <div class="mt-4">
                  <img id="posterPreview" src="#" alt="Poster Preview" class="hidden h-auto w-400">
                </div>
                @error('poster_url')
                  <p class="yns_red mt-1 text-sm">{{ $message }}</p>
                @enderror
              </div>
            </div>
            <div class="col">
              <div class="group mb-4">
                <x-input-label-dark :required="true">Venue</x-input-label-dark>
                <x-text-input id="venue_name" name="venue_name" autocomplete="off" :required="true"
                  :value="old('')"></x-text-input>
                <ul id="venue-suggestions"
                  class="max-h-60 absolute z-10 hidden overflow-auto border border-gray-300 bg-white">
                </ul>
                <x-input-label-dark :required="true">Venue ID</x-input-label-dark>
                <x-text-input id="venue_id" name="venue_id" :value="old('')" :required="true"></x-text-input>
                @error('venue_name')
                  <p class="yns_red mt-1 text-sm">{{ $message }}</p>
                @enderror
              </div>
              <div class="group" id="band-rows-container">
                <!-- Headline Band -->
                <div class="group mb-4">
                  <x-input-label-dark :required="true">Headline Band</x-input-label-dark>
                  <x-text-input id="headliner-search" name="headliner" autocomplete="off" :required="true"
                    :value="old('')"></x-text-input>
                  <ul id="headliner-suggestions"
                    class="max-h-60 absolute z-10 hidden overflow-auto border border-gray-300 bg-white"></ul>
                  <x-input-label-dark :required="true">Headliner Band ID</x-input-label-dark>
                  <x-text-input id="headliner_id" name="headliner_id" :value="old('')"
                    :required="true"></x-text-input>
                  @error('headliner')
                    <p class="yns_red mt-1 text-sm">{{ $message }}</p>
                  @enderror
                </div>

                <!-- Main Support -->
                <div class="group mb-4">
                  <x-input-label-dark>Main Support</x-input-label-dark>
                  <x-text-input id="main-support-search" name="main_support" autocomplete="off"
                    :value="old('')"></x-text-input>
                  <ul id="main-support-suggestions"
                    class="max-h-60 absolute z-10 hidden overflow-auto border border-gray-300 bg-white"></ul>
                  <x-input-label-dark>Main Support Band ID</x-input-label-dark>
                  <x-text-input id="main_support_id" name="main_support_id" :value="old('')"></x-text-input>
                  @error('mainSupport')
                    <p class="yns_red mt-1 text-sm">{{ $message }}</p>
                  @enderror
                </div>

                <!-- Bands (Comma-Separated Input) -->
                <div class="group mb-4" id="bandsContainer">
                  <x-input-label-dark>Bands</x-input-label-dark>
                  <x-text-input id="bands-search" name="bands" class="band-input" autocomplete="off"
                    placeholder="Type band name and press Enter, separated by commas"
                    :value="old('')"></x-text-input>
                  <ul id="bands-suggestions"
                    class="max-h-60 absolute z-10 hidden overflow-auto border border-gray-300 bg-white"></ul>
                  <x-input-label-dark>Bands IDs</x-input-label-dark>
                  <x-text-input id="bands_ids" name="bands_ids" :value="old('')" />
                  @error('bands')
                    <p class="yns_red mt-1 text-sm">{{ $message }}</p>
                  @enderror
                </div>

                <!-- Opening Band -->
                <div class="group mb-4">
                  <x-input-label-dark>Opening Band</x-input-label-dark>
                  <x-text-input id="opener-search" name="opener" autocomplete="off"
                    :value="old('')"></x-text-input>
                  <ul id="opener-suggestions"
                    class="max-h-60 absolute z-10 hidden overflow-auto border border-gray-300 bg-white"></ul>
                  <x-input-label-dark>Opening Band ID</x-input-label-dark>
                  <x-text-input id="opener_id" name="opener_id" :value="old('')"></x-text-input>
                  @error('opener')
                    <p class="yns_red mt-1 text-sm">{{ $message }}</p>
                  @enderror
                </div>
              </div>
            </div>

            <button type="submit"
              class="mt-7 rounded-lg border border-white bg-white px-4 py-2 font-heading text-black transition duration-150 ease-in-out hover:border-yns_yellow hover:text-yns_yellow">Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</x-app-layout>
<script>
  $(document).ready(function() {
    // Initialize the date pickers
    flatpickr('#event_date', {
      altInput: true,
      altFormat: "d-m-Y",
      dateFormat: "d-m-Y",
    });
    flatpickr('#event_start_time', {
      enableTime: true,
      noCalendar: true,
      dateFormat: "H:i",
      time_24hr: true,
    });
    flatpickr('#event_end_time', {
      enableTime: true,
      noCalendar: true,
      dateFormat: "H:i",
      time_24hr: true,
    });

    // Poster Preview
    $('#poster_url').on('change', function(event) {
      const file = event.target.files[0];
      const maxSize = 10 * 1024 * 1024; // 10MB in bytes

      if (file) {
        if (file.size > maxSize) {
          showFailureNotification('File size exceeds 10MB limit');
          this.value = ''; // Clear the file input
          $('#posterPreview').addClass('hidden').attr('src', '#');
          return;
        }
        const reader = new FileReader();
        reader.onload = function(e) {
          $('#posterPreview').attr('src', e.target.result).removeClass('hidden');
        };
        reader.readAsDataURL(file);
      }
    });

    // Handle form submission
    $('#eventForm').on('submit', function(event) {
      event.preventDefault(); // Prevent default form submission

      const dashboardType = "{{ $dashboardType }}"; // Capture the dashboard type from the template
      const bandIds = $('#bands_ids').val().split(',').filter(id => id.trim());
      const promoterIds = $('#promoter_ids').val().split(',').filter(id => id.trim());

      const formData = new FormData(this); // Get form data
      formData.delete('bands_ids');
      bandIds.forEach(id => {
        formData.append('bands_ids[]', id);
      });
      promoterIds.forEach(id => {
        formData.append('promoter_ids[]', id);
      });

      // Debug log FormData
      for (var pair of formData.entries()) {
        console.log(pair[0] + ': ' + pair[1]);
      }

      $.ajax({
        url: "{{ route('admin.dashboard.store-new-event', ['dashboardType' => ':dashboardType']) }}"
          .replace(':dashboardType', dashboardType),
        method: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}' // Include the CSRF token
        },
        success: function(data) {
          if (data.success) {
            showSuccessNotification(data.message); // Show success notification
            setTimeout(() => {
              window.location.href = data.redirect_url; // Redirect after 2 seconds
            }, 2000);
          } else {
            if (data.errors) {
              Object.keys(data.errors).forEach(key => {
                const error = data.errors[key];
                showFailureNotification(error); // Show error notification
              });
            } else {
              showFailureNotification(
                'An unexpected error occurred. Please try again.'); // General error message
            }
          }
        },
        error: function(jqXHR, textStatus, errorThrown) {
          if (jqXHR.status === 413) {
            showFailureNotification('The uploaded file is too large. Maximum size is 10MB.');
          } else {
            showFailureNotification('An error occurred: ' + errorThrown); // Show error notification
          }
        }
      });
    });

    // Promoter Search
    function handlePromoterSearch() {
      const searchInput = $('#promoter_name');
      const suggestionsElement = $('#promoter-suggestions');
      const promoterIdsField = $('#promoter_ids');
      let selectedPromoterIds = [];
      let debounceTimer;

      function createNewPromoter(promoterName, inputElement, suggestionsElement, setterCallback, idField) {
        $.ajax({
          url: '/api/promoters/create',
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          data: {
            name: promoterName
          },
          success: function(response) {
            if (response.success && response.promoter) {
              const promoterId = response.promoter.id;
              let currentIds = promoterIdsField.val() ? promoterIdsField.val().split(',') : [];
              currentIds.push(promoterId);
              promoterIdsField.val(currentIds.join(','));

              setterCallback(response.promoter);
              suggestionsElement.empty().addClass('hidden');
              showSuccessNotification('Promoter created successfully');
            }
          },
          error: function(jqXHR, textStatus, errorThrown) {
            console.error('Error creating promoter:', errorThrown);
            showFailureNotification('Failed to create promoter');
          }
        });
      }

      searchInput.on('input', function() {
        clearTimeout(debounceTimer);
        const searchQuery = this.value.split(',').pop().trim();

        if (searchQuery.length >= 3) {
          debounceTimer = setTimeout(() => {
            $.ajax({
              url: `/api/promoters/search?q=${searchQuery}`,
              method: 'GET',
              success: function(response) {
                suggestionsElement.empty().removeClass('hidden');
                const promoters = response.promoters || [];

                if (promoters.length === 0) {
                  const createOption = $('<li>')
                    .addClass(
                      'suggestion-item cursor-pointer px-4 py-2 bg-opac_8_black text-yns_yellow font-bold'
                    )
                    .html(`<i class="fas fa-plus mr-2"></i>Create new promoter "${searchQuery}"`)
                    .on('click', function() {
                      createNewPromoter(
                        searchQuery,
                        searchInput,
                        suggestionsElement,
                        (promoter) => {
                          const currentValue = searchInput.val();
                          const existingPromoters = currentValue.split(',')
                            .map(p => p.trim())
                            .filter(p => p.length > 0)
                            .slice(0, -1);

                          existingPromoters.push(promoter.name);
                          searchInput.val(existingPromoters.join(', ') + ', ');

                          selectedPromoterIds.push(promoter.id);
                          promoterIdsField.val(selectedPromoterIds.join(','));
                        },
                        promoterIdsField
                      );
                    });
                  suggestionsElement.append(createOption);
                  return;
                }

                promoters.forEach(promoter => {
                  const li = $('<li>')
                    .addClass(
                      'suggestion-item cursor-pointer hover:text-yns_yellow px-4 py-2 bg-opac_8_black text-white'
                    )
                    .text(promoter.name)
                    .on('click', () => {
                      const currentValue = searchInput.val();
                      const existingPromoters = currentValue.split(',')
                        .map(p => p.trim())
                        .filter(p => p.length > 0)
                        .slice(0, -1);

                      existingPromoters.push(promoter.name);
                      searchInput.val(existingPromoters.join(', ') + ', ');

                      selectedPromoterIds.push(promoter.id);
                      promoterIdsField.val(selectedPromoterIds.join(','));

                      suggestionsElement.addClass('hidden');
                    });
                  suggestionsElement.append(li);
                });
              },
              error: function(xhr, status, error) {
                console.error('Search failed:', error);
                suggestionsElement.empty()
                  .append(
                    '<li class="suggestion-item text-red-500 px-4 py-2 bg-opac_8_black">Error loading promoters</li>'
                  )
                  .removeClass('hidden');
              }
            });
          }, 300);
        } else {
          suggestionsElement.addClass('hidden');
        }
      });

      $(document).on('click', function(e) {
        if (!$(e.target).closest('#promoter_name, #promoter-suggestions').length) {
          suggestionsElement.addClass('hidden');
        }
      });
    }

    handlePromoterSearch();

    // Venue Search
    function handleVenueSearch() {
      const searchInput = $('#venue_name');
      const suggestionsElement = $('#venue-suggestions');
      const venueIdField = $('#venue_id');
      let selectedVenueId = null;
      let debounceTimer;

      function createNewVenue(venueName, inputElement, suggestionsElement, setterCallback, idField) {
        $.ajax({
          url: '/api/venues/create',
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          data: {
            name: venueName
          },
          success: function(response) {
            if (response.success && response.venue) {
              const venueId = response.venue.id;
              venueIdField.val(venueId);
              setterCallback(response.venue);
              suggestionsElement.empty().addClass('hidden');
              showSuccessNotification('Venue created successfully');
            }
          },
          error: function(jqXHR, textStatus, errorThrown) {
            console.error('Error creating venue:', errorThrown);
            showFailureNotification('Failed to create venue');
          }
        });
      }

      searchInput.on('input', function() {
        clearTimeout(debounceTimer);
        const searchQuery = this.value.split(',').pop().trim();

        if (searchQuery.length >= 3) {
          debounceTimer = setTimeout(() => {
            $.ajax({
              url: `/api/venues/search?q=${searchQuery}`,
              method: 'GET',
              success: function(response) {
                suggestionsElement.empty().removeClass('hidden');
                const venues = response.venues || [];

                if (venues.length === 0) {
                  const createOption = $('<li>')
                    .addClass(
                      'suggestion-item cursor-pointer px-4 py-2 bg-opac_8_black text-yns_yellow font-bold'
                    )
                    .html(`<i class="fas fa-plus mr-2"></i>Create new venue "${searchQuery}"`)
                    .on('click', function() {
                      createNewVenue(
                        searchQuery,
                        searchInput,
                        suggestionsElement,
                        (venue) => {
                          searchInput.val(venue.name);
                          venueIdField.val(venue.id);
                        },
                        venueIdField
                      );
                    });
                  suggestionsElement.append(createOption);
                  return;
                }

                venues.forEach(venue => {
                  const li = $('<li>')
                    .addClass(
                      'suggestion-item cursor-pointer hover:text-yns_yellow px-4 py-2 bg-opac_8_black text-white'
                    )
                    .text(venue.name)
                    .on('click', () => {
                      searchInput.val(venue.name);
                      venueIdField.val(venue.id);
                      suggestionsElement.addClass('hidden');
                    });
                  suggestionsElement.append(li);
                });
              },
              error: function(xhr, status, error) {
                console.error('Search failed:', error);
                suggestionsElement.empty()
                  .append(
                    '<li class="suggestion-item text-red-500 px-4 py-2 bg-opac_8_black">Error loading venues</li>'
                  )
                  .removeClass('hidden');
              }
            });
          }, 300);
        } else {
          suggestionsElement.addClass('hidden');
        }
      });

      $(document).on('click', function(e) {
        if (!$(e.target).closest('#venue_name, #venue-suggestions').length) {
          suggestionsElement.addClass('hidden');
        }
      });
    }

    handleVenueSearch();


    const headlinerSearchInput = $('#headliner-search');
    const mainSupportSearchInput = $('#main-support-search');
    const openerSearchInput = $('#opener-search');
    const bandSearchInput = $('#bands-search');

    const headlinerSuggestions = $('#headliner-suggestions');
    const mainSupportSuggestions = $('#main-support-suggestions');
    const openerSuggestions = $('#opener-suggestions');
    const bandSuggestions = $('#bands-suggestions');

    const headlinerIdField = $('#headliner_id');
    const mainSupportIdField = $('#main_support_id');
    const openerIdField = $('#opener_id');
    const bandIdsField = $('#bands_ids');

    let selectedBands = []; // Normal bands list (multiple bands)
    let selectedBandIds = []; // IDs for normal bands

    let headlinerId = null;
    let mainSupportId = null;
    let openerId = null;

    // Handle band search for all fields (Headline, Main Support, Opener, Bands)
    function handleBandSearch(inputElement, suggestionsElement, setterCallback, idField) {
      let debounceTimer;

      if (inputElement.attr('id') === 'bands-search') {
        inputElement.on('keydown', function(e) {
          if (e.key === 'Backspace') {
            const value = inputElement.val();
            const cursorPosition = this.selectionStart;
            const bands = value.split(',').map(b => b.trim()).filter(b => b.length > 0);

            let currentPosition = 0;
            let bandIndex = -1;

            for (let i = 0; i < bands.length; i++) {
              currentPosition += bands[i].length + 2;
              if (cursorPosition <= currentPosition) {
                bandIndex = i;
                break;
              }
            }

            if (bandIndex !== -1) {
              e.preventDefault();
              bands.splice(bandIndex, 1);
              selectedBandIds.splice(bandIndex, 1);
              inputElement.val(bands.length ? bands.join(', ') + ', ' : '');
              bandIdsField.val(selectedBandIds.join(','));
            }
          }
        });
      }
      inputElement.on('input', function() {
        clearTimeout(debounceTimer);
        const searchQuery = this.value.split(',').pop().trim();

        if (searchQuery.length >= 3) {
          $.ajax({
            url: `/api/bands/search?q=${searchQuery}`,
            method: 'GET',
            success: function(data) {
              suggestionsElement.empty().removeClass('hidden');

              if (data.bands.length) {
                // Show existing bands
                data.bands.forEach(band => {
                  const suggestionItem = $('<li>')
                    .text(band.name)
                    .addClass(
                      'suggestion-item cursor-pointer hover:text-yns_yellow px-4 py-2 bg-opac_8_black text-white'
                    )
                    .on('click', function() {
                      if (inputElement.attr('id') === 'bands-search') {
                        const currentValue = inputElement.val();
                        const existingBands = currentValue.split(',')
                          .map(b => b.trim())
                          .filter(b => b.length > 0)
                          .slice(0, -1);

                        existingBands.push(band.name);
                        inputElement.val(existingBands.join(', ') + ', ');

                        selectedBandIds.push(band.id);
                        bandIdsField.val(selectedBandIds.join(','));
                      } else {
                        setterCallback(band);
                        idField.val(band.id);
                      }
                      suggestionsElement.empty().addClass('hidden');
                    });
                  suggestionsElement.append(suggestionItem);
                });

                // Add "Create New Band" option if no exact match
                const exactMatch = data.bands.some(band =>
                  band.name.toLowerCase() === searchQuery.toLowerCase()
                );

                if (!exactMatch) {
                  const createOption = $('<li>')
                    .text(`Create new band: "${searchQuery}"`)
                    .addClass(
                      'suggestion-item cursor-pointer hover:text-yns_yellow px-4 py-2 bg-opac_8_black text-white font-bold'
                    )
                    .on('click', function() {
                      createNewBand(searchQuery, inputElement, suggestionsElement, setterCallback,
                        idField);
                    });
                  suggestionsElement.append(createOption);
                }
              } else {
                // No results - show create option
                const createOption = $('<li>')
                  .text(`Create new band: "${searchQuery}"`)
                  .addClass(
                    'suggestion-item cursor-pointer hover:text-yns_yellow px-4 py-2 bg-opac_8_black text-white font-bold'
                  )
                  .on('click', function() {
                    createNewBand(searchQuery, inputElement, suggestionsElement, setterCallback,
                      idField);
                  });
                suggestionsElement.append(createOption);
              }
            },
            error: function(jqXHR, textStatus, errorThrown) {
              console.error('Error fetching bands:', textStatus, errorThrown);
            }
          });
        } else {
          suggestionsElement.empty().addClass('hidden');
        }
      });
    }

    function createNewBand(bandName, inputElement, suggestionsElement, setterCallback, idField) {
      $.ajax({
        url: '/api/bands/create',
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
          name: bandName
        },
        success: function(response) {
          if (inputElement.attr('id') === 'bands-search') {
            const currentValue = inputElement.val();
            const existingBands = currentValue.split(',')
              .map(b => b.trim())
              .filter(b => b.length > 0)
              .slice(0, -1);

            existingBands.push(bandName);
            inputElement.val(existingBands.join(', ') + ', ');

            selectedBandIds.push(response.band.id);
            bandIdsField.val(selectedBandIds.join(','));
          } else {
            setterCallback(response.band);
            idField.val(response.band.id);
          }

          suggestionsElement.empty().addClass('hidden');
          showSuccessNotification('Band created successfully');
        },
        error: function(jqXHR, textStatus, errorThrown) {
          console.error('Error creating band:', errorThrown);
          showFailureNotification('Failed to create band');
        }
      });
    }

    // Initialize band search inputs
    handleBandSearch(headlinerSearchInput, headlinerSuggestions, function(band) {
      headlinerSearchInput.val(band.name);
      headlinerId = band.id;
    }, headlinerIdField);

    handleBandSearch(mainSupportSearchInput, mainSupportSuggestions, function(band) {
      mainSupportSearchInput.val(band.name);
      mainSupportId = band.id;
    }, mainSupportIdField);

    handleBandSearch(openerSearchInput, openerSuggestions, function(band) {
      openerSearchInput.val(band.name);
      openerId = band.id;
    }, openerIdField);

    handleBandSearch(bandSearchInput, bandSuggestions, function(band) {
      const currentValue = bandSearchInput.val().trim();
      const newValue = currentValue ? `${currentValue.split(',').slice(0, -1).join(',')}, ${band.name}` :
        `${band.name}`;
      bandSearchInput.val(newValue + ',');
      selectedBandIds.push(band.id);
      bandIdsField.val(selectedBandIds.join(','));
      const bandItem = $('<li>')
        .text(band.name)
        .addClass('suggestion-item cursor-pointer hover:text-yns_yellow px-4 py-2 bg-opac_8_black text-white');
      bandSuggestions.append(bandItem);
    }, bandIdsField);

    // Handle comma-separated band input
    bandSearchInput.on('keydown', function(event) {
      if (event.key === ',') {
        event.preventDefault();
        const bandName = bandSearchInput.val().split(',').pop().trim();

        if (bandName) {
          $.ajax({
            url: `/api/bands/search?q=${bandName}`,
            method: 'GET',
            success: function(data) {
              if (data.bands.length) {
                const band = data.bands[0];
                const currentValue = bandSearchInput.val().trim();
                const newValue = currentValue ?
                  `${currentValue.split(',').slice(0, -1).join(',')}, ${band.name}` : `${band.name}`;
                bandSearchInput.val(newValue + ',');
                selectedBandIds.push(band.id);
                bandIdsField.val(selectedBandIds.join(','));
                const bandItem = $('<li>')
                  .text(band.name)
                  .addClass(
                    'suggestion-item cursor-pointer hover:text-yns_yellow px-4 py-2 bg-opac_8_black text-white'
                  );
                bandSuggestions.append(bandItem);
              }
            },
            error: function(jqXHR, textStatus, errorThrown) {
              console.error('Error fetching bands:', textStatus, errorThrown);
            }
          });
        }
      }
    });
  });
</script>
