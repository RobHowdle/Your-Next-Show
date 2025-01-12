<section>
  <header>
    <h2 class="text-md font-heading font-medium text-white">
      {{ __('Change your user details') }}
    </h2>
  </header>
  <form class="mt-6 space-y-6" id="saveProfile">
    @csrf
    @method('PUT')
    <div>
      <x-input-label-dark for="userFirstName" :value="__('First Name')" />
      <x-text-input id="userFirstName" class="mt-1 block w-full" type="text" name="userFirstName" :value="old('userFirstName', $userFirstName ?? '')"
        required autofocus autocomplete="first_name" />
      <x-input-error :messages="$errors->get('userFirstName')" class="mt-2" />
    </div>
    <div class="mt-4">
      <x-input-label-dark for="userLastName" :value="__('Last Name')" />
      <x-text-input id="last_name" class="mt-1 block w-full" type="text" name="userLastName" :value="old('userLastName', $userLastName ?? '')"
        required autofocus autocomplete="userLastName" />
      <x-input-error :messages="$errors->get('userLastName')" class="mt-2" />
    </div>

    <div class="mt-4">
      <x-input-label-dark for="userEmail" :value="__('Email')" />
      <x-text-input id="userEmail" class="mt-1 block w-full" type="email" name="email" :value="old('userEmail', $userEmail ?? '')" required
        autocomplete="email" />
      <x-input-error :messages="$errors->get('userEmail')" class="mt-2" />
    </div>

    <div class="mt-4">
      <x-input-label-dark for="email" :value="__('Date Of Birth')" />
      <x-date-input id="userDob" class="mt-1 block w-full" type="userDob" name="userDob" :value="old('userDob', $userDob ?? '')" required
        autocomplete="date_of_birth" />
      <x-input-error :messages="$errors->get('userDob')" class="mt-2" />
    </div>

    <div class="mt-4">
      <x-input-label-dark for="password" :value="__('Password')" />
      <x-text-input id="password" class="mt-1 block w-full" type="password" name="password"
        autocomplete="new-password" />
      <x-input-error :messages="$errors->get('password')" class="mt-2" />
    </div>

    <div class="mt-4">
      <x-input-label-dark for="password_confirmation" :value="__('Confirm Password')" />
      <x-text-input id="password_confirmation" class="mt-1 block w-full" type="password" name="password_confirmation"
        autocomplete="new-password" />
      <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
    </div>

    <div class="group mb-6">
      @php
        $dataId = 1;
      @endphp
      <x-google-address-picker :postalTown="old('postal_town', $userPostalTown ?? '')" :dataId="$dataId" id="location_{{ $dataId }}" name="location"
        label="Your Location" placeholder="Enter an address" :value="old('location', $userLocation ?? '')" :latitude="old('latitude', $userLat ?? '')" :longitude="old('longitude', $userLong ?? '')" />
    </div>

    <div class="flex items-center gap-4">
      <button type="submit"
        class="mt-8 rounded-lg border border-white bg-white px-4 py-2 font-heading font-bold text-black transition duration-150 ease-in-out hover:border-yns_yellow hover:text-yns_yellow">Save</button>
    </div>
  </form>
</section>
@push('scripts')
  <script>
    $(document).ready(function() {
      const dashboardType = "{{ $dashboardType }}";
      const userId = "{{ $user->id }}";

      $('#saveProfile').on('submit', function(e) {
        e.preventDefault();

        const form = $(this);
        const formData = new FormData(this);

        $.ajax({
          url: '{{ route('profile.update', ['dashboardType' => $dashboardType, 'user' => $user->id]) }}',
          method: 'POST',
          data: formData,
          processData: false,
          contentType: false,
          success: function(response) {
            showSuccessNotification(response.message);
            window.location.href = response.redirect;
          },
          error: function(xhr) {
            const response = xhr.responseJSON;
            showFailureNotification(response);
          }
        });
      });
    })
  </script>
@endpush
