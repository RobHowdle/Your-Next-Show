<x-app-layout :dashboardType="$dashboardType" :modules="$modules">
  <x-slot name="header">
    <x-sub-nav :userId="$userId" />
  </x-slot>

  {{-- Main Container with Background --}}
  <div class="relative min-h-screen">

    <div class="relative mx-auto w-full max-w-screen-2xl py-8">
      <div class="px-4">
        {{-- Header Section --}}
        <div class="relative mb-8">
          {{-- Background with overlay --}}
          <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-gray-900 via-black to-gray-900 opacity-75"></div>

          {{-- Content --}}
          <div class="relative px-4 py-6 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-7xl">
              <h1 class="font-heading text-3xl font-bold text-white md:text-4xl">
                New Event
              </h1>
              <p class="mt-2 text-gray-400">Add your event details and configuration</p>
            </div>
          </div>
        </div>

        {{-- Main Form Container --}}
        <div class="rounded-xl border border-gray-800 bg-gray-900/60 backdrop-blur-md backdrop-saturate-150">
          <form id="eventForm" method="POST" enctype="multipart/form-data" data-dashboard-type="{{ $dashboardType }}">
            @csrf
            <input type="hidden" id="dashboard_type" value="{{ $dashboardType }}">
            @if ($profileData['hasMultiplePlatforms'])
              <div class="group mb-4">
                <x-input-label-dark>Ticket Platform</x-input-label-dark>
                <select id="ticket_platform" name="ticket_platform"
                  class="focus:border-yns_pink rounded-md border-gray-300 bg-gray-700 text-white">
                  <option value="">Select Platform</option>
                  @foreach ($profileData['apiKeys'] as $apiKey)
                    <option value="{{ $apiKey['name'] }}">{{ $apiKey['display_name'] }}</option>
                  @endforeach
                </select>
              </div>
            @elseif($profileData['singlePlatform'])
              <div class="group mb-4">
                <input type="hidden" id="ticket_platform" name="ticket_platform"
                  value="{{ $profileData['singlePlatform']['name'] }}">
                <button type="button" id="importEventButton"
                  class="inline-flex items-center rounded-lg bg-opac_8_black px-6 py-3 font-heading text-sm font-semibold text-white transition duration-150 ease-in-out hover:bg-black/70">
                  <i class="fa-solid fa-download mr-2"></i>
                  Import from {{ $profileData['singlePlatform']['display_name'] }}
                </button>
              </div>
            @endif

            {{-- Add these hidden fields --}}
            <input type="hidden" id="platform_event_id" name="platform_event_id">
            <input type="hidden" id="platform_event_url" name="platform_event_url">

            {{-- Event source display --}}
            <div id="eventSource" class="mb-4 hidden text-sm text-gray-400">
              Event imported from <span id="platformName"></span>
            </div>

            <div class="grid gap-8 p-6 lg:grid-cols-2 lg:p-8">
              {{-- Left Column: Core Event Information --}}
              <div class="space-y-6">
                {{-- Basic Event Details --}}
                <div class="rounded-lg border border-gray-800 bg-black/50 p-6">
                  <h2 class="mb-6 font-heading text-xl font-bold text-white">Event Information</h2>
                  <div class="space-y-6">
                    {{-- Event Name --}}
                    <div>
                      <x-input-label-dark :required="true">Event Name</x-input-label-dark>
                      <x-text-input id="event_name" name="event_name" :required="true"
                        class="mt-1 block w-full"></x-text-input>
                    </div>

                    {{-- Date and Time Section --}}
                    <div class="grid gap-4 sm:grid-cols-2">
                      <div class="col-span-2">
                        <x-input-label-dark :required="true">Event Date</x-input-label-dark>
                        <x-date-input id="event_date" name="event_date" :required="true"
                          class="mt-1 block w-full"></x-date-input>
                      </div>
                      <div>
                        <x-input-label-dark :required="true">Start Time</x-input-label-dark>
                        <x-time-input id="event_start_time" name="event_start_time" :required="true"
                          class="mt-1 block w-full"></x-time-input>
                      </div>
                      <div>
                        <x-input-label-dark>End Time</x-input-label-dark>
                        <x-time-input id="event_end_time" name="event_end_time"
                          class="mt-1 block w-full"></x-time-input>
                      </div>
                      <div class="col-span-2">
                        <x-input-label-dark :required="true">Event Genres</x-input-label-dark>
                        <div class="mt-2 grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4">
                          @foreach ($genres as $genre)
                            <label class="flex items-center space-x-2">
                              <input type="checkbox" name="genres[]" @if (in_array($genre, old('genres', $event->genres ?? []))) checked @endif
                                class="rounded border-gray-700 bg-gray-800 text-yns_yellow focus:ring-yns_yellow">
                              <span class="text-sm text-white">{{ $genre }}</span>
                            </label>
                          @endforeach
                        </div>
                      </div>
                    </div>

                    {{-- Description --}}
                    <div>
                      <x-input-label-dark :required="true">Description</x-input-label-dark>
                      <x-textarea-input id="event_description" name="event_description" :required="true"
                        class="mt-1 block w-full" rows="4"></x-textarea-input>
                    </div>
                  </div>
                </div>

                {{-- Venue Section --}}
                <div class="rounded-lg border border-gray-800 bg-black/50 p-6">
                  <h2 class="mb-6 font-heading text-xl font-bold text-white">Venue Details</h2>
                  <div>
                    <x-input-label-dark :required="true">Venue Name</x-input-label-dark>
                    <x-text-input id="venue_name" name="venue_name" autocomplete="off" :required="true"
                      class="mt-1 block w-full"></x-text-input>
                    <ul id="venue-suggestions"
                      class="absolute z-10 mt-1 hidden w-full rounded-lg border border-gray-700 bg-gray-800"></ul>
                    <x-text-input id="venue_id" name="venue_id" :required="true" type="hidden"></x-text-input>
                  </div>
                </div>

                {{-- Pricing & Links --}}
                <div class="rounded-lg border border-gray-800 bg-black/50 p-6">
                  <h2 class="mb-6 font-heading text-xl font-bold text-white">Tickets & Links</h2>
                  <div class="space-y-6">
                    <div>
                      <x-input-label-dark>Door Price (£)</x-input-label-dark>
                      <x-number-input-pound id="on_the_door_ticket_price" name="on_the_door_ticket_price"
                        class="mt-1 block w-full" />
                    </div>
                    <div>
                      <x-input-label-dark>Pre-sale Link</x-input-label-dark>
                      <x-text-input id="ticket_url" name="ticket_url" type="url" placeholder="https://"
                        class="mt-1 block w-full"></x-text-input>
                    </div>
                    <div>
                      <x-input-label-dark>Facebook Event</x-input-label-dark>
                      <x-text-input id="facebook_event_url" name="facebook_event_url" type="url"
                        placeholder="https://facebook.com/events/" class="mt-1 block w-full"></x-text-input>
                    </div>
                  </div>
                </div>
              </div>

              {{-- Right Column: Media and Lineup --}}
              <div class="space-y-6">
                {{-- Event Poster --}}
                <div class="rounded-lg border border-gray-800 bg-black/50 p-6">
                  <h2 class="mb-6 font-heading text-xl font-bold text-white">Event Poster <span
                      class="text-yns_red">*</span>
                  </h2>
                  <div class="space-y-4">
                    <img id="posterPreview" src="#" alt="Event Poster"
                      class="mb-4 hidden h-auto w-full rounded-lg border border-gray-800 object-cover">
                    <x-input-file id="poster_url" name="poster_url" accept="image/*"
                      class="mt-1 block w-full"></x-input-file>
                  </div>
                </div>

                {{-- Promoter Section --}}
                <div class="rounded-lg border border-gray-800 bg-black/50 p-6">
                  <h2 class="mb-6 font-heading text-xl font-bold text-white">Event Promoter</h2>
                  <div class="space-y-4">
                    <div>
                      <x-input-label-dark>Promoter Name</x-input-label-dark>
                      <x-text-input id="promoter_name" name="promoter_name" autocomplete="off"
                        placeholder="Type promoter name and press Enter" class="mt-1 block w-full"></x-text-input>
                      <ul id="promoter-suggestions"
                        class="absolute z-10 mt-1 hidden w-full rounded-lg border border-gray-700 bg-gray-800"></ul>
                      <x-input-label-dark class="hidden">Promoter ID</x-input-label-dark>
                      <x-text-input id="promoter_ids" name="promoter_ids" type="hidden"
                        class="mt-1 w-full"></x-text-input>
                    </div>
                  </div>
                </div>

                {{-- Event Lineup --}}
                <div class="rounded-lg border border-gray-800 bg-black/50 p-6">
                  <h2 class="mb-6 font-heading text-xl font-bold text-white">Event Lineup</h2>
                  <div class="space-y-6">
                    {{-- Headliner --}}
                    <div>
                      <x-input-label-dark :required="true">Headliner</x-input-label-dark>
                      <x-text-input id="headliner-search" name="headliner" autocomplete="off" :required="true"
                        class="mt-1 block w-full"></x-text-input>
                      <ul id="headliner-suggestions"
                        class="absolute z-10 mt-1 hidden w-full rounded-lg border border-gray-700 bg-gray-800"></ul>
                      <x-text-input id="headliner_id" name="headliner_id" :required="true"
                        class="hidden"></x-text-input>
                    </div>

                    {{-- Support Acts --}}
                    <div class="grid gap-6 sm:grid-cols-2">
                      <div>
                        <x-input-label-dark>Main Support</x-input-label-dark>
                        <x-text-input id="main-support-search" name="main_support" autocomplete="off"
                          class="mt-1 block w-full"></x-text-input>
                        <ul id="main-support-suggestions"
                          class="absolute z-10 mt-1 hidden w-full rounded-lg border border-gray-700 bg-gray-800">
                        </ul>
                        <x-text-input id="main_support_id" name="main_support_id" class="hidden"></x-text-input>
                      </div>

                      {{-- Additional Bands --}}
                      <div>
                        <x-input-label-dark>Additional Support Acts</x-input-label-dark>
                        <x-text-input id="bands-search" name="bands" class="band-input" autocomplete="off"
                          placeholder="Type band name and press Enter" class="mt-1 block w-full"></x-text-input>
                        <ul id="bands-suggestions"
                          class="absolute z-10 mt-1 hidden w-full rounded-lg border border-gray-700 bg-gray-800">
                        </ul>
                        <x-text-input id="bands_ids" name="bands_ids" class="hidden"></x-text-input>
                      </div>
                    </div>

                    {{-- Opening Act --}}
                    <div>
                      <x-input-label-dark>Opening Act</x-input-label-dark>
                      <x-text-input id="opener-search" name="opener" autocomplete="off"
                        class="mt-1 block w-full"></x-text-input>
                      <ul id="opener-suggestions"
                        class="absolute z-10 mt-1 hidden w-full rounded-lg border border-gray-700 bg-gray-800"></ul>
                      <x-text-input id="opener_id" name="opener_id" class="hidden"></x-text-input>
                    </div>
                  </div>
                </div>

                {{-- Save Button --}}
                <div class="flex justify-end pt-4">
                  <button type="submit"
                    class="inline-flex items-center rounded-lg bg-yns_yellow px-6 py-3 font-heading text-sm font-semibold text-black transition duration-150 ease-in-out hover:bg-yns_yellow/90">
                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Save Changes
                  </button>
                </div>
              </div>
            </div>
            <div id="platformEventModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
              <div class="flex min-h-screen items-end justify-center px-4 pb-20 pt-4 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                  <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>
                <div
                  class="inline-block transform overflow-hidden rounded-lg bg-yns_dark_gray text-left align-bottom shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:align-middle">
                  <div class="bg-yns_dark_gray px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                      <div class="mt-3 w-full text-center sm:ml-4 sm:mt-0 sm:text-left">
                        <h3 class="text-lg font-medium leading-6 text-white">
                          Link Platform Event
                        </h3>
                        <div class="mt-4">
                          <input type="text" id="platformEventSearch"
                            class="w-full rounded-md border-gray-600 bg-gray-700 text-white"
                            placeholder="Search events...">
                        </div>
                        <div id="platformEventResults" class="max-h-60 mt-4 overflow-y-auto">
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="bg-yns_dark_gray px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <button type="button"
                      class="close-modal rounded-md bg-gray-600 px-4 py-2 text-white hover:bg-gray-700">
                      Close
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
<script>
  $(document).ready(function() {
    const dashboardType = "{{ $dashboardType }}";
    const integrationConfig = @json(config('integrations.ticket_platforms'));
    const promoterData = @json($serviceData ?? null);

    // Initialize the date pickers
    const datePicker = flatpickr('#event_date', {
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

    // Auto-populate promoter data if user is a promoter
    if (dashboardType === 'promoter' && promoterData) {
      console.log(promoterData);
      // Set the promoter name in the input field
      $('#promoter_name').val(promoterData.promoter_name);

      // Set the promoter ID in the hidden field
      $('#promoter_ids').val(promoterData.promoter_id);

      // Disable the promoter input field since it's pre-populated
      $('#promoter_name').prop('disabled', true);
    }

    // Poster Preview
    // Update your file preview handler
    $('#poster_url').on('change', function(event) {
      const file = event.target.files[0];
      const maxSize = 10 * 1024 * 1024; // 10MB in bytes

      if (file) {
        if (file.size > maxSize) {
          showFailureNotification('File size exceeds 10MB limit');
          this.value = ''; // Clear the file input
          $('#posterPreview').addClass('hidden').attr('src', '#');
          $('#uploaded_file_path').val(''); // Clear the hidden input
          return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
          // Update preview image
          $('#posterPreview').attr('src', e.target.result).removeClass('hidden');

          // Create FormData and upload file
          const formData = new FormData();
          formData.append('file', file);

          $.ajax({
            url: `/dashboard/${dashboardType}/events/upload`,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
              if (response.success) {
                // Update both preview and details sections
                $('#uploaded_file_path').val(response.path);
                $('#posterPreview').attr('src', response.url).removeClass('hidden');
                showSuccessNotification('File uploaded successfully');
              }
            },
            error: function(error) {
              console.error('Upload error:', error);
              showFailureNotification('Error uploading file');
              $('#posterPreview').addClass('hidden');
              $('#uploaded_file_path').val('');
            }
          });
        };
        reader.readAsDataURL(file);
      }
    });

    // Platform integration handling
    const ticketPlatform = $('#ticket_platform');
    const linkButton = $('#linkPlatformEvent');
    const modal = $('#platformEventModal');
    const searchInput = $('#platformEventSearch');
    const resultsContainer = $('#platformEventResults');
    const platformEventId = $('#platform_event_id');
    const platformEventUrl = $('#platform_event_url');

    // Define searchEvents function in the proper scope
    function searchEvents(query) {
      $.ajax({
        url: `/api/platforms/${ticketPlatform.val()}/search`,
        method: 'GET',
        data: {
          query
        },
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        xhrFields: {
          withCredentials: true
        },
        success: function(response) {
          resultsContainer.empty();
          if (response.events && response.events.length > 0) {
            response.events.forEach(function(event) {
              const eventDate = new Date(event.date).toLocaleDateString();
              $(`<div class="cursor-pointer rounded-lg p-3 text-white hover:bg-gray-600">
                        <p class="font-medium">${event.name}</p>
                        <p class="text-sm text-gray-400">${eventDate} - ${event.venue}</p>
                        <p class="text-xs text-gray-400">${event.tickets_available ? 'Tickets available' : 'Sold out'}</p>
                    </div>`)
                .on('click', function() {
                  handleEventSelection(event);
                })
                .appendTo(resultsContainer);
            });
          } else {
            resultsContainer.html('<p class="text-gray-400 p-3">No events found</p>');
          }
        },
        error: function(error) {
          console.error('Error searching events:', error);
          resultsContainer.html('<p class="text-red-500 p-3">Error searching events</p>');
        }
      });
    }

    // Handle event selection
    function handleEventSelection(event) {
      // Store Eventbrite IDs
      platformEventId.val(event.id);
      platformEventUrl.val(event.url).trigger('change');

      // Show the event source
      $('#platformName').text($('#ticket_platform option:selected').text() ||
        '{{ $profileData['singlePlatform']['display_name'] ?? '' }}');
      $('#eventSource').removeClass('hidden');

      // Populate event details
      $('#event_name').val(event.name);

      // Set date and times
      datePicker.setDate(event.date);
      $('#event_start_time').val(event.start_time);
      $('#event_end_time').val(event.end_time);
      $('#ticket_url').val(event.url);

      // Set venue if available
      if (event.venue) {
        $('#venue_name').val(event.venue);

        // Add this new code to search for venue ID
        $.ajax({
          url: `/dashboard/${dashboardType}/events/venues/search`,
          method: 'GET',
          data: {
            q: event.venue
          },
          success: function(response) {
            const venues = response.venues || [];
            // If we find an exact match, set the venue ID
            const exactMatch = venues.find(v => v.name.toLowerCase() === event.venue.toLowerCase());
            if (exactMatch) {
              $('#venue_id').val(exactMatch.id);
            } else {
              // If no match found, create new venue
              $.ajax({
                url: `/dashboard/${dashboardType}/events/venues/create`,
                method: 'POST',
                headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                  name: event.venue
                },
                success: function(response) {
                  if (response.success && response.venue) {
                    $('#venue_id').val(response.venue.id);
                    showSuccessNotification('Venue created successfully');
                  }
                },
                error: function(error) {
                  console.error('Error creating venue:', error);
                  showFailureNotification('Failed to create venue');
                }
              });
            }
          },
          error: function(error) {
            console.error('Error searching venues:', error);
          }
        });
      }

      // Set description if available
      if (event.description) {
        $('#event_description').val(event.description);
      }

      // Close modal and update button text
      modal.addClass('hidden');
      if ($('#importEventButton').length) {
        $('#importEventButton').text('Change Platform Event');
      } else {
        linkButton.text('Change Platform Event').removeClass('hidden');
      }
    }

    // Handle single platform import button
    $('#importEventButton').on('click', function() {
      modal.removeClass('hidden');
      searchInput.val('');
      resultsContainer.empty();
      searchEvents('');
    });

    // Search input handler with debounce
    let searchTimeout;
    searchInput.on('input', function() {
      clearTimeout(searchTimeout);
      const query = $(this).val();

      searchTimeout = setTimeout(() => {
        if (query.length >= 3 || query.length === 0) {
          searchEvents(query);
        }
      }, 300);
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

      // Add platform data to formData
      if (ticketPlatform.val()) {
        formData.append('ticket_platform', ticketPlatform.val());
        formData.append('platform_event_id', platformEventId.val());
        formData.append('platform_event_url', platformEventUrl.val());
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
          url: `/dashboard/${dashboardType}/events/promoters/create`,
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
              url: `/dashboard/${dashboardType}/events/promoters/search?q=${searchQuery}`,
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
          url: `/dashboard/${dashboardType}/events/venues/create`,
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
              url: `/dashboard/${dashboardType}/events/venues/search?q=${searchQuery}`,
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
            url: `/dashboard/${dashboardType}/events/bands/search?q=${searchQuery}`,
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
        url: `/dashboard/${dashboardType}/events/bands/create`,
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
            url: `/dashboard/${dashboardType}/events/bands/search?q=${bandName}`,
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
