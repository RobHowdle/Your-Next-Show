<div class="rounded-lg bg-gray-800/50 p-6 backdrop-blur-sm">
  <header class="mb-6 border-b border-gray-700 pb-4">
    <h2 class="font-heading text-lg font-medium text-white">
      {{ __('Venue Details') }}
    </h2>
    <p class="mt-1 text-sm text-gray-400">
      {{ __('Manage your venue\'s specific information here.') }}
    </p>
  </header>

  <form id="venueDetailsForm" class="space-y-6">
    @csrf
    @method('PUT')

    <div class="rounded-lg bg-black/20 p-6">
      <h3 class="mb-4 font-heading text-lg font-medium text-white">Location & Capacity</h3>
      <div class="grid gap-4">
        <!-- Venue Capacity -->
        <div>
          <x-input-label-dark for="capacity" value="Venue Capacity" />
          <x-text-input id="capacity" name="capacity" type="number" class="mt-1 block w-full"
            value="{{ old('capacity', $profileData['capacity'] ?? '') }}" required />
          <x-input-error :messages="$errors->get('capacity')" class="mt-2" />
        </div>

        <!-- What3Words -->
        <div>
          <x-input-label-dark for="w3w">What3Words Address</x-input-label-dark>
          <x-w3w-search id="venue_w3w" name="venue[w3w]" :value="old('w3w', $venueData['w3w'] ?? '')"
            label="Venue Location (What3Words)"></x-w3w-search>
          <x-input-error :messages="$errors->get('w3w')" class="mt-2" />
        </div>
      </div>
    </div>

    <div class="flex items-center justify-end gap-4 border-t border-gray-700 pt-6">
      <button type="button" onclick="submitVenueForm()"
        class="rounded-lg border border-yns_yellow bg-yns_yellow px-4 py-2 font-heading font-bold text-black transition hover:bg-yns_yellow/90">
        Save Changes
      </button>
      @if (session('status') === 'profile-updated')
        <p class="text-sm text-gray-400">{{ __('Saved.') }}</p>
      @endif
    </div>
  </form>
</div>

<script>
  function submitVenueForm() {
    console.log('Submit button clicked');

    // Get form values
    var capacity = document.getElementById('capacity').value;

    // Find the hidden input within the w3w component 
    var w3wValue = document.querySelector('input[name="venue[w3w]"]').value;
    console.log('W3W value:', w3wValue);

    // Create form data object
    var formData = new FormData();
    formData.append('capacity', capacity);
    formData.append('venue[w3w]', w3wValue);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    formData.append('_method', 'PUT');

    // Send AJAX request
    fetch('{{ route('venue.update', ['dashboardType' => $dashboardType, 'user' => $user]) }}', {
        method: 'POST', // Use POST with _method: PUT for Laravel
        body: formData,
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
      })
      .then(response => response.json())
      .then(data => {
        console.log('Response:', data);
        if (data.success) {
          // Show success message
          showSuccessNotification(data.message);
          setTimeout(() => {
            window.location.href = response.redirect;
          }, 2000);
        } else {
          // Show error message
          showFailureNotification(data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showFailureNotification(error);
      });
  }
</script>
