<header>
  <h2 class="text-md font-heading font-medium text-white">
    {{ __('Venue Details') }}
  </h2>
</header>
<form method="POST" action="{{ route('venue.update', ['dashboardType' => $dashboardType, 'user' => $user]) }}"
  class="grid grid-cols-3 gap-x-8 gap-y-8" enctype="multipart/form-data">
  @csrf
  @method('PUT')
  <div class="col-start-1 col-end-2">
    <div class="group mb-6">
      <x-input-label-dark for="name">Venue Name:</x-input-label-dark>
      <x-text-input id="name" name="name" value="{{ old('name', $name) }}"></x-text-input>
      @error('name')
        <p class="yns_red mt-1 text-sm">{{ $message }}</p>
      @enderror
    </div>

    <div class="group mb-6">
      <x-input-label-dark for="contact_name">Contact Name:</x-input-label-dark>
      <x-text-input id="contact_name" name="contact_name"
        value="{{ old('contact_name', $contact_name) }}"></x-text-input>
      @error('contact_name')
        <p class="yns_red mt-1 text-sm">{{ $message }}</p>
      @enderror
    </div>

    <div class="group mb-6">
      <x-google-address-picker :postalTown="old('postalTown', $venueUserData['postal_town'] ?? '')" data-id="2" id="location" name="location" label="Location"
        placeholder="Enter an address" :value="old('location', $venueUserData['location'] ?? '')" :latitude="old('latitude', $venueUserData['latitude'] ?? '')" :longitude="old('longitude', $venueUserData['longitude'] ?? '')" />
    </div>

    <div class="group mb-6">
      <x-input-label-dark for="w3w">What3Words:</x-input-label-dark>
      <x-text-input id="w3w" name="w3w" value="{{ old('w3w', $venueUserData['w3w'] ?? '') }}"></x-text-input>
      <div id="suggestions"></div>
    </div>

    <div class="group mb-6">
      <x-input-label-dark for="email">Email:</x-input-label-dark>
      <x-text-input id="contact_email" name="contact_email"
        value="{{ old('contact_email', $venueUserData['contact_email']) }}"></x-text-input>
      @error('contact_email')
        <p class="yns_red mt-1 text-sm">{{ $message }}</p>
      @enderror
    </div>

    <div class="group mb-6">
      <x-input-label-dark for="contact_number">Contact Phone:</x-input-label-dark>
      <x-text-input id="contact_number" name="contact_number"
        value="{{ old('contact_number', $venueUserData['contact_number']) }}"></x-text-input>
      @error('contact_number')
        <p class="yns_red mt-1 text-sm">{{ $message }}</p>
      @enderror
    </div>
  </div>

  @if (is_array($platformsToCheck))
    <div class="col-start-2 col-end-3">
      @foreach ($platformsToCheck as $platform)
        <div class="group mb-6">
          <x-input-label-dark for="{{ $platform }}">{{ ucfirst($platform) }}:</x-input-label-dark>

          @php
            // Ensure the links for the platform are correctly handled as an array
            $links =
                isset($platforms[$platform]) && is_array($platforms[$platform])
                    ? $platforms[$platform]
                    : ($platforms[$platform]
                        ? [$platforms[$platform]]
                        : []);
          @endphp

          @foreach ($links as $index => $link)
            <x-text-input id="{{ $platform }}-{{ $index }}" name="contact_links[{{ $platform }}][]"
              value="{{ old('contact_links.' . $platform . '.' . $index, $link) }}">
            </x-text-input>
          @endforeach

          <!-- If no links exist, provide a way to add one -->
          @if (empty($links))
            <x-text-input id="{{ $platform }}-new" name="contact_links[{{ $platform }}][]"
              value="{{ old('contact_links.' . $platform . '.new', '') }}"
              placeholder="Add a {{ ucfirst($platform) }} link">
            </x-text-input>
          @endif

          @error('contact_links.' . $platform . '.*')
            <p class="yns_red mt-1 text-sm">{{ $message }}</p>
          @enderror
        </div>
      @endforeach

    </div>
  @endif

  <div class="col-start-3 col-end-4">
    <div class="group mb-6 flex flex-col items-center">
      <x-input-label-dark for="logo" class="text-left">Logo:</x-input-label-dark>
      <x-input-file id="logo" name="logo" onchange="previewLogo(event)"></x-input-file>

      <!-- Preview Image -->
      <img id="logo-preview" src="{{ $logo }}" alt="Logo Preview" class="mt-4 h-80 w-80 object-cover"
        style="display: {{ $logo ? 'block' : 'none' }};">

      @error('logo')
        <p class="yns_red mt-1 text-sm">{{ $message }}</p>
      @enderror
    </div>
  </div>

  <div class="flex items-center gap-4">
    <button type="submit"
      class="mt-8 rounded-lg border border-white bg-white px-4 py-2 font-heading font-bold text-black transition duration-150 ease-in-out hover:border-yns_yellow hover:text-yns_yellow">Save</button>
    @if (session('status') === 'profile-updated')
      <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
        class="text-sm text-gray-600 dark:text-gray-400">{{ __('Saved.') }}</p>
    @endif
  </div>
</form>
<script>
  function previewLogo(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('logo-preview');

    if (file) {
      const reader = new FileReader();

      reader.onload = function(e) {
        preview.src = e.target.result; // Set the preview to the file data
        preview.style.display = 'block'; // Show the preview image
        console.log('Preview URL:', e.target.result); // Log the preview URL
      };

      reader.readAsDataURL(file); // Read the file as a data URL
    }
  }

  jQuery(document).ready(function() {
    // Listen for the 'input' event on the address input field
    jQuery('#w3w').on('input', function() {
      var address = jQuery(this).val();
      console.log(address);

      if (address.length >= 7) { // Send request only if at least 3 characters are entered
        setTimeout(function() {
          $.ajax({
            url: '{{ route('what3words.suggest') }}', // Route to handle the AJAX request
            method: 'POST',
            data: {
              _token: '{{ csrf_token() }}', // Include CSRF token
              w3w: address // Send the current address entered by the user
            },
            success: function(response) {
              // Check if suggestions were found and display them
              if (response.success) {
                var suggestionsHtml = '<strong>Suggested Addresses:</strong><ul>';
                response.suggestions.forEach(function(word) {
                  suggestionsHtml += '<li>' + word.nearestPlace + ' - ' + word.words +
                    '</li>';
                });
                suggestionsHtml += '</ul>';
                jQuery('#suggestions').html(suggestionsHtml);
              } else {
                jQuery('#suggestions').html('<strong>No suggestions found</strong>');
              }
            },
            error: function(xhr, status, error) {
              // Handle any errors
              jQuery('#suggestions').html(
                '<strong>Error occurred while processing your request.</strong>');
            }
          });
        }, 2000);
      } else {
        jQuery('#suggestions').empty(); // Clear suggestions if input is less than 3 characters
      }
    });
  });
</script>
