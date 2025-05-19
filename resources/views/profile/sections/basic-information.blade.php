<div class="rounded-lg bg-gray-800/50 p-6 backdrop-blur-sm">
  <header class="mb-6 border-b border-gray-700 pb-4">
    <h2 class="font-heading text-lg font-medium text-white">
      {{ __(ucfirst($dashboardType) . ' Details') }}
    </h2>
    <p class="mt-1 text-sm text-gray-400">
      {{ __('Update your ' . strtolower($dashboardType) . '\'s basic information.') }}
    </p>
  </header>

  <form id="saveBasicInformation" method="POST" class="space-y-6" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
      {{-- Basic Information Card --}}
      <div class="rounded-lg bg-black/20 p-6">
        <h3 class="mb-4 font-heading text-lg font-medium text-white">Contact Information</h3>
        <div class="grid gap-4">
          <div>
            <x-input-label-dark for="name" :required="true">{{ ucfirst($dashboardType) }} Name</x-input-label-dark>
            <x-text-input id="name" name="name" value="{{ old('name', $profileData['name'] ?? '') }}"
              class="mt-1 block w-full" />
            @error('name')
              <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
            @enderror
          </div>

          <div class="grid gap-4 sm:grid-cols-2">
            <div>
              <x-input-label-dark for="contact_email" :value="__('Email')" />
              <x-text-input id="contact_email" name="contact_email"
                value="{{ old('contact_email', $profileData['contact_email'] ?? '') }}" class="mt-1 block w-full" />
            </div>

            <div>
              <x-input-label-dark for="contact_number" :value="__('Phone')" />
              <x-text-input id="contact_number" name="contact_number"
                value="{{ old('contact_number', $profileData['contact_number'] ?? '') }}" class="mt-1 block w-full" />
            </div>
          </div>

          <div class="grid gap-4 sm:grid-cols-2">
            <div>
              <x-input-label-dark for="contact_name" :value="__('Contact Name')" :required="true" />
              <x-text-input id="contact_name" name="contact_name"
                value="{{ old('contact_name', $profileData['contact_name'] ?? '') }}" class="mt-1 block w-full" />
            </div>

            <div>
              <x-input-label-dark for="preferred_contact" :value="__('Preferred Contact')" />
              <select id="preferred_contact" name="preferred_contact"
                class="w-full rounded-md border-yns_red bg-gray-900 px-2 py-2 text-gray-300 shadow-sm focus:border-indigo-600 focus:ring-indigo-500">
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
                @foreach ($profileData['activePlatforms'] ?? [] as $platform)
                  <option value="{{ $platform }}"
                    {{ old('preferred_contact', $profileData['preferred_contact'] ?? '') === $platform ? 'selected' : '' }}>
                    {{ ucfirst($platform) }}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div>
            <x-google-address-picker :postalTown="old('postalTown', $profileData['postalTown'] ?? '')" data-id="2" id="location" name="location" label="Location"
              :required="true" placeholder="Enter an address" :value="old('location', $profileData['location'] ?? '')" :latitude="old('lat', $profileData['lat'] ?? '')" :longitude="old('long', $profileData['long'] ?? '')" />
          </div>
        </div>
      </div>

      {{-- Logo Upload Card --}}
      <div class="rounded-lg bg-black/20 p-6">
        <h3 class="mb-4 font-heading text-lg font-medium text-white">Logo</h3>
        <div class="flex flex-col items-center gap-4">
          <img id="logo-preview" src="{{ $profileData['logo'] ?? asset('images/system/yns_no_image_found.png') }}"
            alt="Logo Preview" class="h-64 w-auto rounded-lg object-contain">
          <x-input-file id="logo_url" name="logo_url" onchange="previewLogo(event)" class="w-full" />
        </div>
      </div>

      {{-- Social Links Card --}}
      @if (isset($profileData['platformsToCheck']) && is_array($profileData['platformsToCheck']))
        <div class="col-span-full rounded-lg bg-black/20 p-6">
          <h3 class="mb-4 font-heading text-lg font-medium text-white">Social Links</h3>
          <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($profileData['platformsToCheck'] as $platformKey => $platformConfig)
              <div>
                <x-input-label-dark for="{{ $platformKey }}" :value="__($platformConfig['name'] ?? ucfirst($platformKey))" />
                @php
                  $links =
                      isset($profileData['platforms'][$platformKey]) &&
                      is_array($profileData['platforms'][$platformKey])
                          ? $profileData['platforms'][$platformKey]
                          : (isset($profileData['platforms'][$platformKey])
                              ? [$profileData['platforms'][$platformKey]]
                              : []);
                @endphp
                @foreach ($links as $index => $link)
                  <x-text-input id="{{ $platformKey }}-{{ $index }}"
                    name="contact_links[{{ $platformKey }}][]"
                    value="{{ old('contact_links.' . $platformKey . '.' . $index, $link) }}"
                    class="mb-2 mt-1 block w-full"
                    placeholder="{{ $platformConfig['placeholder'] ?? 'Add ' . ucfirst($platformKey) . ' link' }}" />
                @endforeach
                @if (empty($links))
                  <x-text-input id="{{ $platformKey }}-new" name="contact_links[{{ $platformKey }}][]"
                    value="{{ old('contact_links.' . $platformKey . '.new', '') }}"
                    placeholder="{{ $platformConfig['placeholder'] ?? 'Add ' . ucfirst($platformKey) . ' link' }}"
                    class="mt-1 block w-full" />
                @endif
              </div>
            @endforeach
          </div>
        </div>
      @endif
    </div>

    {{-- Form Actions --}}
    <div class="flex items-center justify-between gap-4 border-t border-gray-700 pt-6">
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
            {{ __('Saved.') }}
          </p>
        @endif
      </div>
    </div>
  </form>
</div>
<script>
  function previewLogo(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('logo-preview');

    if (file) {
      preview.src = URL.createObjectURL(file);
    }
  }

  jQuery(document).ready(function() {
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
          showFailureNotification(response.message);

          // Clear previous error messages
          $('.validation-error').remove();
          $('.error-border').removeClass('error-border');

          // Handle validation errors
          if (response && response.errors) {
            Object.keys(response.errors).forEach(field => {
              const errorMessage = response.errors[field][0];
              const inputElement = $(`[name="${field}"]`);

              // Handle array inputs like contact_links
              if (field.includes('.')) {
                const parts = field.split('.');
                const baseField = parts[0];
                const platform = parts[1];
                const index = parts[2] || 0;

                $(`[name="${baseField}[${platform}][]"]:eq(${index})`).addClass('error-border')
                  .after(`<p class="validation-error mt-1 text-sm text-red-500">${errorMessage}</p>`);
              } else {
                // Regular inputs
                inputElement.addClass('error-border')
                  .after(`<p class="validation-error mt-1 text-sm text-red-500">${errorMessage}</p>`);
              }
            });

            // Scroll to the first error
            const firstErrorField = Object.keys(response.errors)[0];
            const element = $(`[name="${firstErrorField}"]`);
            if (element.length) {
              $('html, body').animate({
                scrollTop: element.offset().top - 100
              }, 500);
            }
          }
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
