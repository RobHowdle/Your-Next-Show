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
            value="{{ old('capacity', $venueData->capacity ?? '') }}" required />
          <x-input-error :messages="$errors->get('capacity')" class="mt-2" />
        </div>

        <!-- What3Words -->
        <div>
          <x-input-label-dark for="w3w">What3Words Address</x-input-label-dark>
          <x-text-input id="w3w" name="w3w" class="mt-1 block w-full"
            value="{{ old('w3w', $venueData->w3w ?? '') }}" />
          <div id="suggestions" class="mt-2"></div>
          <x-input-error :messages="$errors->get('w3w')" class="mt-2" />
        </div>
      </div>
    </div>

    <div class="flex items-center justify-end gap-4 border-t border-gray-700 pt-6">
      <button type="submit"
        class="rounded-lg border border-yns_yellow bg-yns_yellow px-4 py-2 font-heading font-bold text-black transition hover:bg-yns_yellow/90">
        Save Changes
      </button>
      @if (session('status') === 'profile-updated')
        <p class="text-sm text-gray-400">{{ __('Saved.') }}</p>
      @endif
    </div>
  </form>
</div>

@push('scripts')
  <script>
    $(document).ready(function() {
      $('#venueDetailsForm').on('submit', function(e) {
        e.preventDefault();

        const formData = {
          capacity: $('#capacity').val(),
          w3w: $('#w3w').val(),
        };

        $.ajax({
          url: '{{ route('venue.update', ['dashboardType' => $dashboardType, 'user' => $user]) }}',
          method: 'POST',
          data: formData,
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          success: function(response) {
            // Show success notification
            if (typeof showNotification === 'function') {
              showNotification('success', 'Venue details updated successfully');
            }
          },
          error: function(xhr, status, error) {
            // Show error notification
            if (typeof showNotification === 'function') {
              showNotification('error', 'Failed to update venue details');
            }
            console.error('Error:', error);
          }
        });
      });
    });
  </script>
@endpush
