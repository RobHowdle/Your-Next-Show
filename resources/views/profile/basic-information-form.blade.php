<header>
  <h2 class="text-md mb-4 font-heading font-medium text-white">
    {{ __(ucfirst($dashboardType) . ' Details') }}
  </h2>
</header>

<form id="saveBasicInformation" method="POST" class="grid grid-cols-3 gap-x-8 gap-y-8" enctype="multipart/form-data">
  @csrf
  @method('PUT')
  <div class="col-start-1 col-end-2">
    <div class="group mb-6">
      <x-input-label-dark for="name">{{ ucfirst($dashboardType) }} Name:</x-input-label-dark>
      <x-text-input id="name" name="name" value="{{ old('name', $profileData['name']) }}"></x-text-input>
      @error('name')
        <p class="yns_red mt-1 text-sm">{{ $message }}</p>
      @enderror
    </div>

    <div class="group mb-6">
      <x-input-label-dark for="contact_name">Contact Name:</x-input-label-dark>
      <x-text-input id="contact_name" name="contact_name"
        value="{{ old('contact_name', $profileData['contact_name']) }}"></x-text-input>
      @error('contact_name')
        <p class="yns_red mt-1 text-sm">{{ $message }}</p>
      @enderror
    </div>

    <div class="group mb-6">
      <x-google-address-picker :postalTown="old('postalTown', $profileData['postalTown'] ?? '')" data-id="2" id="location" name="location" label="Location"
        placeholder="Enter an address" :value="old('location', $profileData['location'] ?? '')" :latitude="old('lat', $profileData['lat'] ?? '')" :longitude="old('long', $profileData['long'] ?? '')" />
    </div>

    @if ($dashboardType === 'venue')
      <div class="group mb-6">
        <x-input-label-dark for="w3w">What3Words:</x-input-label-dark>
        <x-text-input id="w3w" name="w3w" value="{{ old('w3w', $profileData['w3w'] ?? '') }}"></x-text-input>
        <div id="suggestions"></div>
      </div>
    @endif

    <div class="group mb-6">
      <x-input-label-dark for="email">Email:</x-input-label-dark>
      <x-text-input id="contact_email" name="contact_email"
        value="{{ old('contact_email', $profileData['contact_email']) }}"></x-text-input>
      @error('contact_email')
        <p class="yns_red mt-1 text-sm">{{ $message }}</p>
      @enderror
    </div>

    <div class="group mb-6">
      <x-input-label-dark for="contact_number">Contact Phone:</x-input-label-dark>
      <x-text-input id="contact_number" name="contact_number"
        value="{{ old('contact_number', $profileData['contact_number']) }}"></x-text-input>
      @error('contact_number')
        <p class="yns_red mt-1 text-sm">{{ $message }}</p>
      @enderror
    </div>
  </div>

  @if (isset($profileData['platformsToCheck']) && is_array($profileData['platformsToCheck']))
    <div class="col-start-2 col-end-3">
      @foreach ($profileData['platformsToCheck'] as $platform)
        <div class="group mb-6">
          <x-input-label-dark for="{{ $platform }}">{{ ucfirst($platform) }}:</x-input-label-dark>

          @php
            $links =
                isset($profileData['platforms'][$platform]) && is_array($profileData['platforms'][$platform])
                    ? $profileData['platforms'][$platform]
                    : (isset($profileData['platforms'][$platform])
                        ? [$profileData['platforms'][$platform]]
                        : []);
          @endphp

          @foreach ($links as $index => $link)
            <x-text-input id="{{ $platform }}-{{ $index }}" name="contact_links[{{ $platform }}][]"
              value="{{ old('contact_links.' . $platform . '.' . $index, $link) }}">
            </x-text-input>
          @endforeach

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

  <div class="group mb-6 flex flex-col items-center">
    <x-input-label-dark for="logo_url" class="text-left">Logo:</x-input-label-dark>
    <x-input-file id="logo_url" name="logo_url" onchange="previewLogo(event)"></x-input-file>

    <img id="logo-preview"
      src="{{ !empty($profileData['logo_url']) ? Storage::url($profileData['logo_url']) : asset('images/system/yns_no_image_found.png') }}"
      alt="Logo Preview" class="mt-4 h-80 w-80 object-cover"
      onerror="this.src='{{ asset('images/system/yns_no_image_found.png') }}'">

    @error('logo_url')
      <p class="yns_red mt-1 text-sm">{{ $message }}</p>
    @enderror
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
      preview.src = URL.createObjectURL(file);
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

    $('#saveBasicInformation').on('submit', function(e) {
      e.preventDefault();

      const form = $(this);
      const formData = new FormData(this);

      $.ajax({
        url: '{{ route($dashboardType . '.update', ['dashboardType' => $dashboardType, 'user' => $user]) }}',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
          console.log(response);
          if (response.success) {
            showSuccessNotification(response.message);
            setTimeout(() => {
              window.location.href = response.redirect;
            }, 2000);
          } else {
            alert('Failed to update profile');
          }
        },
        error: function(xhr, status, error) {
          const response = xhr.responseJSON;
          showFailureNotification(response);
        }
      });
    });
  });
</script>
