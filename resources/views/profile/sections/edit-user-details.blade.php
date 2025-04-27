<div class="rounded-lg bg-gray-800/50 p-6 backdrop-blur-sm">
  <header class="mb-6 border-b border-gray-700 pb-4">
    <h2 class="font-heading text-lg font-medium text-white">
      {{ __('User Profile Details') }}
    </h2>
    <p class="mt-1 text-sm text-gray-400">
      {{ __('Update your account\'s profile information.') }}
    </p>
  </header>

  <form id="saveProfile" class="space-y-6">
    @csrf
    @method('PUT')
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
      {{-- Personal Information Card --}}
      <div class="rounded-lg bg-black/20 p-6">
        <h3 class="mb-4 font-heading text-lg font-medium text-white">Personal Information</h3>
        <div class="grid gap-4">
          {{-- Names --}}
          <div class="grid gap-4 sm:grid-cols-2">
            <div>
              <x-input-label-dark for="userFirstName" :value="__('First Name')" />
              <x-text-input id="userFirstName" class="mt-1 block w-full" type="text" name="userFirstName"
                :value="old('userFirstName', $userFirstName ?? '')" required autofocus autocomplete="first_name" />
              <x-input-error :messages="$errors->get('userFirstName')" class="mt-2" />
            </div>

            <div>
              <x-input-label-dark for="userLastName" :value="__('Last Name')" />
              <x-text-input id="last_name" class="mt-1 block w-full" type="text" name="userLastName"
                :value="old('userLastName', $userLastName ?? '')" required autocomplete="userLastName" />
              <x-input-error :messages="$errors->get('userLastName')" class="mt-2" />
            </div>
          </div>

          {{-- Email --}}
          <div>
            <x-input-label-dark for="userEmail" :value="__('Email')" />
            <x-text-input id="userEmail" class="mt-1 block w-full" type="email" name="email" :value="old('userEmail', $userEmail ?? '')"
              required autocomplete="email" />
            <x-input-error :messages="$errors->get('userEmail')" class="mt-2" />
          </div>

          {{-- Date of Birth --}}
          <div>
            <x-input-label-dark for="email" :value="__('Date Of Birth')" />
            <x-date-input id="userDob" class="mt-1 block w-full" type="userDob" name="userDob" :value="old('userDob', $userDob ?? '')"
              required autocomplete="date_of_birth" />
            <x-input-error :messages="$errors->get('userDob')" class="mt-2" />
          </div>

          {{-- Location --}}
          <div>
            <x-google-address-picker :postalTown="old('postal_town', $userPostalTown ?? '')" :dataId="1" id="location_1" name="location"
              label="Your Location" placeholder="Enter an address" :value="old('location', $userLocation ?? '')" :latitude="old('latitude', $userLat ?? '')"
              :longitude="old('longitude', $userLong ?? '')" />
          </div>
        </div>
      </div>

      {{-- Password Section Card --}}
      <div class="rounded-lg bg-black/20 p-6">
        <h3 class="mb-4 font-heading text-lg font-medium text-white">Password</h3>
        <div class="space-y-4">
          <div>
            <x-input-label-dark for="password" :value="__('New Password')" />
            <div class="relative">
              <x-text-input id="password" class="mt-1 block w-full" type="password" name="password"
                autocomplete="new-password" />
              <button type="button" onclick="togglePasswordVisibility('password')"
                class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-400 hover:text-gray-300">
                <svg class="h-5 w-5" id="password-eye" xmlns="http://www.w3.org/2000/svg" fill="none"
                  viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
              </button>
            </div>
            <x-password-strength-checker input-id="password" />
          </div>

          <div>
            <x-input-label-dark for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="mt-1 block w-full" type="password"
              name="password_confirmation" autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
          </div>
        </div>
      </div>

      {{-- Form Actions --}}
      <div class="flex items-center justify-end gap-4 border-t border-gray-700 pt-6">
        <button type="submit"
          class="rounded-lg border border-yns_yellow bg-yns_yellow px-4 py-2 font-heading font-bold text-black transition hover:bg-yns_yellow/90">
          {{ __('Save Changes') }}
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
@push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Initialize password checker
      window.initializePasswordChecker('password');

      // Handle status message
      const statusMessage = document.getElementById('status-message');
      if (statusMessage) {
        setTimeout(() => {
          statusMessage.style.display = 'none';
        }, 2000);
      }
    });
  </script>
@endpush
