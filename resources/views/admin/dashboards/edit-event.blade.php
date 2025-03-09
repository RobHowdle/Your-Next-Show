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
                Editing Event <span class="text-yns_yellow">#{{ $event->id }}</span>
              </h1>
              <p class="mt-2 text-gray-400">Update your event details and configuration</p>
            </div>
          </div>
        </div>

        {{-- Main Form Container --}}
        <div class="rounded-xl border border-gray-800 bg-gray-900/60 backdrop-blur-md backdrop-saturate-150">
          <form id="eventForm" method="POST" enctype="multipart/form-data" data-dashboard-type="{{ $dashboardType }}">
            @csrf
            @method('PUT')
            <input type="hidden" id="dashboard_type" value="{{ $dashboardType }}">

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
                      <x-text-input id="event_name" name="event_name" :required="true" :value="old('event_name', $event->event_name)"
                        class="mt-1 block w-full"></x-text-input>
                    </div>

                    {{-- Date and Time Section --}}
                    <div class="grid gap-4 sm:grid-cols-2">
                      <div class="col-span-2">
                        <x-input-label-dark :required="true">Event Date</x-input-label-dark>
                        <x-date-input id="event_date" name="event_date" :required="true"
                          value="{{ old('event_date', $eventDate) }}" class="mt-1 block w-full"></x-date-input>
                      </div>
                      <div>
                        <x-input-label-dark :required="true">Start Time</x-input-label-dark>
                        <x-time-input id="event_start_time" name="event_start_time" :required="true"
                          value="{{ old('event_start_time', $event->event_start_time) }}"
                          class="mt-1 block w-full"></x-time-input>
                      </div>
                      <div>
                        <x-input-label-dark>End Time</x-input-label-dark>
                        <x-time-input id="event_end_time" name="event_end_time"
                          value="{{ old('event_end_time', $event->event_end_time) }}"
                          class="mt-1 block w-full"></x-time-input>
                      </div>
                      <div class="col-span-2">
                        <x-input-label-dark :required="true">Event Genres</x-input-label-dark>
                        <div class="mt-2 grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4">
                          @foreach ($genres as $genre)
                            <label class="flex items-center space-x-2">
                              <input type="checkbox" name="genres[]" value="{{ $genre }}"
                                @if (in_array($genre, old('genres', $event->genres ?? []))) checked @endif
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
                        class="mt-1 block w-full"
                        rows="4">{{ old('event_description', $event->event_description) }}</x-textarea-input>
                    </div>
                  </div>
                </div>

                {{-- Venue Section --}}
                <div class="rounded-lg border border-gray-800 bg-black/50 p-6">
                  <h2 class="mb-6 font-heading text-xl font-bold text-white">Venue Details</h2>
                  <div>
                    <x-input-label-dark :required="true">Venue Name</x-input-label-dark>
                    <x-text-input id="venue_name" name="venue_name" autocomplete="off" :required="true"
                      :value="old('venue_name', optional($event->venues->first())->name ?? '')" class="mt-1 block w-full"></x-text-input>
                    <ul id="venue-suggestions"
                      class="absolute z-10 mt-1 hidden w-full rounded-lg border border-gray-700 bg-gray-800"></ul>
                    <x-text-input id="venue_id" name="venue_id" :required="true" :value="old('venue_id', optional($event->venues->first())->id ?? '')"
                      class="hidden"></x-text-input>
                  </div>
                </div>

                {{-- Pricing & Links --}}
                <div class="rounded-lg border border-gray-800 bg-black/50 p-6">
                  <h2 class="mb-6 font-heading text-xl font-bold text-white">Tickets & Links</h2>
                  <div class="space-y-6">
                    <div>
                      <x-input-label-dark>Door Price (£)</x-input-label-dark>
                      <x-number-input-pound id="on_the_door_ticket_price" name="on_the_door_ticket_price"
                        :value="old('on_the_door_ticket_price', $event->on_the_door_ticket_price ?? '')" class="mt-1 block w-full" />
                    </div>
                    <div>
                      <x-input-label-dark>Pre-sale Link</x-input-label-dark>
                      <x-text-input id="ticket_url" name="ticket_url" type="url" :value="old('ticket_url', $event->ticket_url)"
                        placeholder="https://" class="mt-1 block w-full"></x-text-input>
                    </div>
                    <div>
                      <x-input-label-dark>Facebook Event</x-input-label-dark>
                      <x-text-input id="facebook_event_url" name="facebook_event_url" type="url" :value="old('facebook_event_url', $event->facebook_event_url)"
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
                    @if ($event->poster_url)
                      <img src="{{ url($event->poster_url) }}" alt="Current Event Poster"
                        class="mb-4 h-auto w-full rounded-lg border border-gray-800 object-cover">
                    @endif
                    <x-input-file id="poster_url" name="poster_url" accept="image/*" class="mt-1 block w-full"
                      :value="old('poster_url', $event->poster_url)"></x-input-file>
                  </div>
                </div>

                {{-- Promoter Section --}}
                <div class="rounded-lg border border-gray-800 bg-black/50 p-6">
                  <h2 class="mb-6 font-heading text-xl font-bold text-white">Event Promoter</h2>
                  <div class="space-y-4">
                    <div>
                      <x-input-label-dark>Promoter Name</x-input-label-dark>
                      <x-text-input id="promoter_name" name="promoter_name" autocomplete="off" :value="old(
                          'promoter_name',
                          $promoters->isNotEmpty() ? $promoters->pluck('name')->join(', ') : '',
                      )"
                        placeholder="Type promoter name and press Enter" class="mt-1 block w-full"></x-text-input>
                      <ul id="promoter-suggestions"
                        class="absolute z-10 mt-1 hidden w-full rounded-lg border border-gray-700 bg-gray-800"></ul>
                      <x-text-input id="promoter_ids" name="promoter_ids" :value="old(
                          'promoter_ids',
                          $promoters->isNotEmpty() ? $promoters->pluck('id')->join(',') : '',
                      )"
                        class="hidden"></x-text-input>
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
                        :value="old('headliner', optional($headliner)->name ?? '')" class="mt-1 block w-full"></x-text-input>
                      <ul id="headliner-suggestions"
                        class="absolute z-10 mt-1 hidden w-full rounded-lg border border-gray-700 bg-gray-800"></ul>
                      <x-text-input id="headliner_id" name="headliner_id" :required="true" :value="old('headliner_id', optional($headliner)->id ?? '')"
                        class="hidden"></x-text-input>
                    </div>

                    {{-- Support Acts --}}
                    <div class="grid gap-6 sm:grid-cols-2">
                      <div>
                        <x-input-label-dark>Main Support</x-input-label-dark>
                        <x-text-input id="main-support-search" name="main_support" autocomplete="off"
                          :value="old('main_support', optional($mainSupport)->name ?? '')" class="mt-1 block w-full"></x-text-input>
                        <ul id="main-support-suggestions"
                          class="absolute z-10 mt-1 hidden w-full rounded-lg border border-gray-700 bg-gray-800"></ul>
                        <x-text-input id="main_support_id" name="main_support_id" :value="old('main_support_id', optional($mainSupport)->id ?? '')"
                          class="hidden"></x-text-input>
                      </div>

                      {{-- Additional Bands --}}
                      <div>
                        <x-input-label-dark>Additional Support Acts</x-input-label-dark>
                        <x-text-input id="bands-search" name="bands" class="band-input" autocomplete="off"
                          placeholder="Type band name and press Enter" :value="collect($bandObjects)->pluck('name')->join(', ')"
                          class="mt-1 block w-full"></x-text-input>
                        <ul id="bands-suggestions"
                          class="absolute z-10 mt-1 hidden w-full rounded-lg border border-gray-700 bg-gray-800"></ul>
                        <x-text-input id="bands_ids" name="bands_ids" :value="collect($bandObjects)->pluck('id')->join(',')"
                          class="hidden"></x-text-input>
                      </div>
                    </div>

                    {{-- Opening Act --}}
                    <div>
                      <x-input-label-dark>Opening Act</x-input-label-dark>
                      <x-text-input id="opener-search" name="opener" autocomplete="off" :value="old('opener', optional($opener)->name ?? '')"
                        class="mt-1 block w-full"></x-text-input>
                      <ul id="opener-suggestions"
                        class="absolute z-10 mt-1 hidden w-full rounded-lg border border-gray-700 bg-gray-800"></ul>
                      <x-text-input id="opener_id" name="opener_id" :value="old('opener_id', optional($opener)->id ?? '')" class="hidden"></x-text-input>
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
          </form>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
<script>
  $(document).ready(function() {
    let selectedBands = []; // Normal bands list (multiple bands)
    let selectedBandIds = []; // IDs for normal bands
    // Initialize the date pickers
    flatpickr('#event_date', {
      altInput: true,
      altFormat: "d-m-Y",
      defaultDate: "{{ $eventDate }}",
      formatDate: (date) => {
        // Format date as dd-mm-yyyy
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();
        return `${day}-${month}-${year}`;
      },
      onChange: function(selectedDates, dateStr) {
        if (selectedDates.length > 0) {
          const formattedDate = this.formatDate(selectedDates[0]);
          document.getElementById('event_date').value = formattedDate;
        } else {
          document.getElementById('event_date').value = "{{ $eventDate }}";
        }
      }
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

      $.ajax({
        url: "{{ route('admin.dashboard.update-event', ['dashboardType' => ':dashboardType', 'id' => ':id']) }}"
          .replace(':dashboardType', dashboardType)
          .replace(':id', "{{ $event->id }}"),
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
                    '<li class="suggestion-item text-red-500 px-4 py-2">Error loading promoters</li>')
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
    const venueInput = document.getElementById('venue_name');
    const suggestionsList = document.getElementById('venue-suggestions');

    venueInput.addEventListener('input', function() {
      const query = this.value;

      if (query.length < 3) {
        suggestionsList.innerHTML = '';
        suggestionsList.classList.add('hidden');
        return;
      }

      const dashboardType = document.getElementById('dashboard_type').value;

      fetch(`/dashboard/${dashboardType}/events/search-venues?query=${encodeURIComponent(query)}`)
        .then(response => {
          if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
          }
          return response.json();
        })
        .then(data => {
          suggestionsList.innerHTML = '';
          data.forEach(venue => {
            const suggestionItem = document.createElement('li');
            suggestionItem.textContent = venue.name;
            suggestionItem.setAttribute('data-id', venue.id);
            suggestionItem.classList.add(
              'cursor-pointer',
              'hover:text-yns_yellow',
              'px-4',
              'py-2',
              'bg-opac_8_black',
              'text-white'
            );

            // Fixed the event listener setup here
            suggestionItem.addEventListener('click', function() {
              venueInput.value = venue.name;
              document.getElementById('venue_id').value = venue.id;
              suggestionsList.classList.add('hidden');
            });

            suggestionsList.appendChild(suggestionItem);
          });

          if (data.length) {
            suggestionsList.classList.remove('hidden');
          } else {
            suggestionsList.classList.add('hidden');
          }
        })
        .catch(error => {
          console.error('Error fetching venue suggestions:', error);
          suggestionsList.classList.add('hidden');
        });
    });

    // Venue Hide suggestions when clicking outside
    document.addEventListener('click', function(event) {
      if (!venueInput.contains(event.target) && !suggestionsList.contains(event.target)) {
        suggestionsList.classList.add('hidden');
      }
    });

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

    let headlinerId = null;
    let mainSupportId = null;
    let openerId = null;

    const existingBands = $('#bands-search').val().split(',').map(b => b.trim()).filter(b => b.length > 0);
    const existingIds = $('#bands_ids').val().split(',').filter(id => id.length > 0);

    // Create initial band-ID pairs
    existingBands.forEach((bandName, index) => {
      selectedBands.push({
        name: bandName,
        id: existingIds[index]
      });
      selectedBandIds.push(existingIds[index]);
    });

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
              const removedBand = bands[bandIndex];
              bands.splice(bandIndex, 1);

              // Find and remove the corresponding ID
              const removedBandIndex = selectedBands.findIndex(b => b.name === removedBand);
              if (removedBandIndex !== -1) {
                selectedBands.splice(removedBandIndex, 1);
                selectedBandIds.splice(removedBandIndex, 1);
              }

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
