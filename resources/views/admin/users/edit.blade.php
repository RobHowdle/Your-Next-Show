<x-admin-layout>
  <div class="container mx-auto px-4 py-8">
    <!-- Page Header -->
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-white">Edit User: {{ $user->name }}</h1>
      <p class="mt-2 text-gray-400">Update user information and preferences</p>
    </div>

    <div class="rounded-lg border border-gray-700 bg-gray-800 p-6">
      <form id="userEditForm" action="{{ route('admin.users.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Basic Information -->
        <div class="mb-6">
          <h2 class="mb-4 text-xl font-semibold text-white">Basic Information</h2>
          <div class="grid gap-4 md:grid-cols-2">
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label for="first_name" class="block text-sm font-medium text-gray-400">First Name</label>
                <input type="text" name="first_name" id="first_name"
                  value="{{ old('first_name', $user->first_name) }}"
                  class="mt-1 w-full rounded-lg border-gray-600 bg-gray-700 text-white" required>
                @error('first_name')
                  <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
              </div>

              <div>
                <label for="last_name" class="block text-sm font-medium text-gray-400">Last Name</label>
                <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $user->last_name) }}"
                  class="mt-1 w-full rounded-lg border-gray-600 bg-gray-700 text-white" required>
                @error('last_name')
                  <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
              </div>
            </div>

            <div>
              <label for="email" class="block text-sm font-medium text-gray-400">Email</label>
              <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                class="mt-1 w-full rounded-lg border-gray-600 bg-gray-700 text-white" required>
              @error('email')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
              @enderror
            </div>

            <div>
              <label for="date_of_birth" class="block text-sm font-medium text-gray-400">Date of Birth</label>
              <input type="date" name="date_of_birth" id="date_of_birth"
                value="{{ old('date_of_birth', optional($user->date_of_birth)->format('Y-m-d')) }}"
                class="mt-1 w-full rounded-lg border-gray-600 bg-gray-700 text-white">
              @error('date_of_birth')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
              @enderror
            </div>

            <div>
              <x-google-address-picker :postalTown="old('postal_town', $user->postal_town)" data-id="user-location" id="location" name="location"
                label="Location" placeholder="Enter an address" :value="old('location', $user->location)" :latitude="old('latitude', $user->latitude)" :longitude="old('longitude', $user->longitude)" />
              @error('location')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
              @enderror
            </div>
          </div>
        </div>

        <!-- Roles and Permissions -->
        <div class="mb-6">
          <h2 class="mb-4 text-xl font-semibold text-white">Roles</h2>
          <div class="grid gap-4 md:grid-cols-3">
            @foreach ($roles as $role)
              <label class="flex items-center space-x-3">
                <input type="checkbox" name="roles[]" value="{{ $role->id }}"
                  {{ $user->roles->contains($role->id) ? 'checked' : '' }}
                  class="rounded border-gray-600 bg-gray-700 text-blue-600">
                <span class="text-gray-300">{{ ucfirst($role->name) }}</span>
              </label>
            @endforeach
          </div>
          @error('roles')
            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
          @enderror
        </div>

        <!-- Preferences -->
        <div class="mb-6">
          <h2 class="mb-4 text-xl font-semibold text-white">Communication Preferences</h2>
          <div class="grid gap-4 md:grid-cols-2">
            @foreach ($systemMailingPrefs as $key => $preference)
              <label class="flex items-center space-x-3">
                <input type="checkbox" name="mailing_preferences[]" value="{{ $key }}"
                  @if (is_array($user->mailing_preferences) &&
                          array_key_exists($key, $user->mailing_preferences) &&
                          $user->mailing_preferences[$key] === true) checked @endif
                  class="rounded border-gray-600 bg-gray-700 text-blue-600">
                <div class="flex flex-col">
                  <span class="text-gray-300">{{ $preference['name'] }}</span>
                  <span class="text-sm text-gray-500">{{ $preference['description'] }}</span>
                </div>
              </label>
            @endforeach
          </div>
          @error('mailing_preferences')
            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
          @enderror
        </div>

        <!-- Calendar Connections -->
        <div class="mb-6">
          <h2 class="mb-4 text-xl font-semibold text-white">Calendar Connections</h2>
          <div class="grid gap-4 md:grid-cols-2">
            <div class="rounded-lg border border-gray-700 p-4">
              <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                  <i class="fab fa-google text-2xl text-white"></i>
                  <span class="text-gray-300">Google Calendar</span>
                </div>
                <span class="{{ $user->google_calendar_connected ? 'text-green-400' : 'text-gray-500' }} text-sm">
                  {{ $user->google_calendar_connected ? 'Connected' : 'Not Connected' }}
                </span>
              </div>
            </div>

            <div class="rounded-lg border border-gray-700 p-4">
              <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                  <i class="fab fa-apple text-2xl text-white"></i>
                  <span class="text-gray-300">Apple Calendar</span>
                </div>
                <span class="{{ $user->apple_calendar_connected ? 'text-green-400' : 'text-gray-500' }} text-sm">
                  {{ $user->apple_calendar_connected ? 'Connected' : 'Not Connected' }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-end space-x-4">
          <a href="{{ route('admin.users') }}"
            class="rounded-lg border border-gray-600 px-4 py-2 text-sm font-medium text-gray-300 hover:bg-gray-700">
            Cancel
          </a>
          <button type="submit"
            class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
            Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>
</x-admin-layout>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('#userEditForm');
    const submitButton = form.querySelector('button[type="submit"]');

    form.addEventListener('submit', function(e) {
      e.preventDefault();

      // Disable submit button to prevent double submission
      submitButton.disabled = true;
      submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Saving...';

      const formData = new FormData(form);

      fetch(form.action, {
          method: 'POST',
          body: formData,
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          }
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            // Show success message
            showSuccessMessage(data.message);

            // Redirect after a short delay
            setTimeout(() => {
              window.location.href = '{{ route('admin.users') }}';
            }, 1500);
          } else {
            showFailureNotification(data.message);
          }
        })
        .catch(error => {
          // Show error message
          const errorAlert = document.createElement('div');
          errorAlert.className = 'fixed top-4 right-4 bg-red-500/10 text-red-400 px-4 py-2 rounded-lg';
          errorAlert.innerHTML = error.message;
          document.body.appendChild(errorAlert);

          // Re-enable submit button
          submitButton.disabled = false;
          submitButton.innerHTML = 'Save Changes';
        });
    });
  });
</script>
