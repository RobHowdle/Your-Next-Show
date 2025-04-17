<header>
  <h2 class="text-md mb-6 font-heading font-medium text-white">{{ __(ucfirst($dashboardType) . ' Details') }}</h2>
</header>

<form id="saveBasicInformation" method="POST" class="grid grid-cols-1 gap-6 lg:grid-cols-2" enctype="multipart/form-data">
  @csrf
  @method('PUT')

  {{-- Primary & Contact Information Combined Card --}}
  <div class="rounded-lg bg-gray-800/50 p-6 backdrop-blur-sm">
    <h3 class="mb-4 text-lg font-bold text-white">Basic Information</h3>
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
      {{-- Left Column --}}
      <div class="space-y-4">
        <div class="group">
          <x-input-label-dark for="name">{{ ucfirst($dashboardType) }} Name</x-input-label-dark>
          <x-text-input id="name" name="name" value="{{ old('name', $profileData['name'] ?? '') }}"
            class="w-full" />
          @error('name')
            <p class="yns_red mt-1 text-sm">{{ $message }}</p>
          @enderror
        </div>

        <div class="group">
          <x-input-label-dark for="contact_email">Email</x-input-label-dark>
          <x-text-input id="contact_email" name="contact_email"
            value="{{ old('contact_email', $profileData['contact_email'] ?? '') }}" class="w-full" />
        </div>

        <div class="group">
          <x-input-label-dark for="contact_number">Phone</x-input-label-dark>
          <x-text-input id="contact_number" name="contact_number"
            value="{{ old('contact_number', $profileData['contact_number'] ?? '') }}" class="w-full" />
        </div>
      </div>

      {{-- Right Column --}}
      <div class="space-y-4">
        <div class="group">
          <x-input-label-dark for="contact_name">Contact Name</x-input-label-dark>
          <x-text-input id="contact_name" name="contact_name"
            value="{{ old('contact_name', $profileData['contact_name'] ?? '') }}" class="w-full" />
        </div>

        <div class="group">
          <x-input-label-dark for="preferred_contact">Preferred Contact</x-input-label-dark>
          <select id="preferred_contact" name="preferred_contact"
            class="w-full rounded-md border-yns_red bg-gray-900 px-2 py-2 text-gray-300">
            <option value="">Select preferred method</option>
            @if (!empty($profileData['contact_email']))
              <option value="email"
                {{ old('preferred_contact', $profileData['preferred_contact'] ?? '') === 'email' ? 'selected' : '' }}>
                Email</option>
            @endif
            @if (!empty($profileData['contact_number']))
              <option value="phone"
                {{ old('preferred_contact', $profileData['preferred_contact'] ?? '') === 'phone' ? 'selected' : '' }}>
                Phone</option>
            @endif
            @foreach ($profileData['activePlatforms'] as $platform)
              <option value="{{ $platform }}"
                {{ old('preferred_contact', $profileData['preferred_contact'] ?? '') === $platform ? 'selected' : '' }}>
                {{ ucfirst($platform) }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="group">
          <x-google-address-picker :postalTown="old('postalTown', $profileData['postalTown'] ?? '')" data-id="2" id="location" name="location" label="Location"
            placeholder="Enter an address" :value="old('location', $profileData['location'] ?? '')" :latitude="old('lat', $profileData['lat'] ?? '')" :longitude="old('long', $profileData['long'] ?? '')" />
        </div>
      </div>
    </div>
  </div>

  {{-- Logo Upload Card --}}
  <div class="rounded-lg bg-gray-800/50 p-6 backdrop-blur-sm">
    <h3 class="mb-4 text-lg font-bold text-white">Logo</h3>
    <div class="flex flex-col items-center gap-4">
      <img id="logo-preview"
        src="{{ !empty($profileData['logo_url']) ? Storage::url($profileData['logo_url']) : asset('images/system/yns_no_image_found.png') }}"
        alt="Logo Preview" class="h-32 w-auto rounded-lg object-contain"
        onerror="this.src='{{ asset('images/system/yns_no_image_found.png') }}'">
      <x-input-file id="logo_url" name="logo_url" onchange="previewLogo(event)" class="w-full" />
    </div>
  </div>

  {{-- Social Links Card --}}
  @if (isset($profileData['platformsToCheck']) && is_array($profileData['platformsToCheck']))
    <div class="col-span-full rounded-lg bg-gray-800/50 p-6 backdrop-blur-sm">
      <h3 class="mb-4 text-lg font-bold text-white">Social Links</h3>
      <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($profileData['platformsToCheck'] as $platform)
          <div class="group">
            <x-input-label-dark for="{{ $platform }}">{{ ucfirst($platform) }}</x-input-label-dark>
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
                value="{{ old('contact_links.' . $platform . '.' . $index, $link) }}" class="mb-2 w-full" />
            @endforeach
            @if (empty($links))
              <x-text-input id="{{ $platform }}-new" name="contact_links[{{ $platform }}][]"
                value="{{ old('contact_links.' . $platform . '.new', '') }}"
                placeholder="Add {{ ucfirst($platform) }} link" class="w-full" />
            @endif
          </div>
        @endforeach
      </div>
    </div>
  @endif

  {{-- Action Buttons --}}
  <div class="col-span-full mt-6 flex items-center justify-between rounded-lg bg-gray-800/50 p-4 backdrop-blur-sm">
    <button type="button" onclick="confirmLeaveCompany()"
      class="rounded-lg border border-red-600 bg-red-600 px-4 py-2 font-heading font-bold text-white transition hover:bg-red-700">
      Leave Company
    </button>
    <div class="flex items-center gap-4">
      <button type="submit"
        class="rounded-lg border border-yns_yellow bg-yns_yellow px-4 py-2 font-heading font-bold text-black transition hover:bg-yns_yellow/90">
        Save Changes
      </button>
      @if (session('status') === 'profile-updated')
        <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-sm text-gray-400">
          {{ __('Saved.') }}</p>
      @endif
    </div>
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
        url: '{{ route(strtolower($dashboardType) . '.update', ['dashboardType' => $dashboardType, 'user' => $user]) }}',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
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

  function confirmLeaveCompany() {
    showConfirmationNotification({
      title: 'Are you sure?',
      text: "You're about to leave this company. This action cannot be undone!",
      onConfirm: () => {
        const service = '{{ $profileData['name'] }}';
        const userId = '{{ $user->id }}';
        const dashboardType = '{{ $dashboardType }}';
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        fetch(`/profile/${dashboardType}/${userId}/leave-service`, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': csrfToken,
              'Accept': 'application/json'
            },
            body: JSON.stringify({
              service: service,
              userId: userId
            })
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              showSuccessNotification('Successfully left company');
              window.location.href = '/dashboard';
            } else {
              showFailureNotification(data.message || 'Failed to leave service');
            }
          })
          .catch(error => {
            console.error('Error:', error);
            showFailureNotification('An error occurred while leaving the service');
          });
      }
    });
  }
</script>
