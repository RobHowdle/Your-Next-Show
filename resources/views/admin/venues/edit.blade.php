<x-admin-layout>
  <div class="container mx-auto px-4 py-8">
    <!-- Page Header -->
    <div class="mb-8 flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold text-white">{{ $venue->name }}</h1>
        <p class="mt-2 text-gray-400">Update venue information and settings</p>
      </div>
      <div class="flex space-x-3">
        <a href="{{ route('admin.venues') }}"
          class="rounded-lg border border-gray-600 px-4 py-2 text-sm font-medium text-gray-300 hover:bg-gray-700">
          Back to Venues
        </a>
      </div>
    </div>

    <!-- Main Form -->
    <div class="grid gap-6 lg:grid-cols-3">
      <!-- Left Column - Basic Info & Status -->
      <div class="space-y-6 lg:col-span-2">
        <div class="rounded-lg border border-gray-700 bg-gray-800 p-6">
          <form id="venueEditForm" action="{{ route('admin.venues.update', $venue->id) }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Basic Information -->
            <div class="mb-6">
              <h2 class="mb-4 text-xl font-semibold text-white">Basic Information</h2>
              <div class="grid gap-4 md:grid-cols-2">
                <div class="md:col-span-2">
                  <label for="name" class="block text-sm font-medium text-gray-400">Venue Name</label>
                  <input type="text" name="name" id="name" value="{{ old('name', $venue->name) }}"
                    class="mt-1 w-full rounded-lg border-gray-600 bg-gray-700 text-white" required>
                  @error('name')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                  @enderror
                </div>

                <div>
                  <label for="capacity" class="block text-sm font-medium text-gray-400">Capacity</label>
                  <input type="number" name="capacity" id="capacity" value="{{ old('capacity', $venue->capacity) }}"
                    class="mt-1 w-full rounded-lg border-gray-600 bg-gray-700 text-white" required>
                  @error('capacity')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                  @enderror
                </div>

                <div>
                  <label for="what3words" class="block text-sm font-medium text-gray-400">What3Words</label>
                  <input type="text" name="w3w" id="w3w" value="{{ old('w3w', $venue->w3w) }}"
                    class="mt-1 w-full rounded-lg border-gray-600 bg-gray-700 text-white" required>
                  @error('w3w')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                  @enderror
                </div>

                <div class="md:col-span-2">
                  <x-google-address-picker :postalTown="old('postal_town', $venue->postal_town)" data-id="venue-location" id="location" name="location"
                    label="Location" placeholder="Enter venue address" :value="old('location', $venue->location)" :latitude="old('latitude', $venue->latitude)"
                    :longitude="old('longitude', $venue->longitude)" />
                </div>
              </div>
            </div>

            <!-- Contact Information -->
            <div class="mb-6">
              <h2 class="mb-4 text-xl font-semibold text-white">Contact Information</h2>
              <div class="grid gap-4 md:grid-cols-2">
                <div>
                  <label for="contact_name" class="block text-sm font-medium text-gray-400">Contact Name</label>
                  <input type="text" name="contact_name" id="contact_name"
                    value="{{ old('contact_name', $venue->contact_name) }}"
                    class="mt-1 w-full rounded-lg border-gray-600 bg-gray-700 text-white" required>
                </div>

                <div>
                  <label for="contact_email" class="block text-sm font-medium text-gray-400">Email</label>
                  <input type="email" name="contact_email" id="contact_email"
                    value="{{ old('contact_email', $venue->contact_email) }}"
                    class="mt-1 w-full rounded-lg border-gray-600 bg-gray-700 text-white" required>
                </div>

                <div>
                  <label for="contact_phone" class="block text-sm font-medium text-gray-400">Phone</label>
                  <input type="tel" name="contact_phone" id="contact_phone"
                    value="{{ old('contact_phone', $venue->contact_phone) }}"
                    class="mt-1 w-full rounded-lg border-gray-600 bg-gray-700 text-white" required>
                </div>

                <div>
                  <label for="preferred_contact" class="block text-sm font-medium text-gray-400">Preferred Contact
                    Method</label>
                  <select name="preferred_contact" id="preferred_contact"
                    class="mt-1 w-full rounded-lg border-gray-600 bg-gray-700 text-white" required>
                    <option value="">Select method</option>
                    <option value="email"
                      {{ old('preferred_contact', $venue->preferred_contact) == 'email' ? 'selected' : '' }}>Email
                    </option>
                    <option value="phone"
                      {{ old('preferred_contact', $venue->preferred_contact) == 'phone' ? 'selected' : '' }}>Phone
                    </option>
                    <option value="website"
                      {{ old('preferred_contact', $venue->preferred_contact) == 'website' ? 'selected' : '' }}>Website
                    </option>
                  </select>
                </div>

                <div class="md:col-span-2">
                  <label for="contact_link" class="block text-sm font-medium text-gray-400">Website/Social Link</label>
                  <input type="url" name="contact_link" id="contact_link"
                    value="{{ old('contact_link', $venue->contact_link) }}"
                    class="mt-1 w-full rounded-lg border-gray-600 bg-gray-700 text-white">
                </div>
              </div>
            </div>

            <!-- Details & Equipment -->
            <div class="mb-6">
              <h2 class="mb-4 text-xl font-semibold text-white">Details & Equipment</h2>
              <div class="space-y-4">
                <div>
                  <label for="in_house_gear" class="block text-sm font-medium text-gray-400">In-House Equipment</label>
                  <textarea id="in_house_gear" name="in_house_gear" class="summernote">{{ old('in_house_gear', $venue->in_house_gear) }}</textarea>
                </div>

                <div>
                  <label for="additional_info" class="block text-sm font-medium text-gray-400">Additional
                    Information</label>
                  <textarea id="additional_info" name="additional_info" class="summernote">{{ old('additional_info', $venue->additional_info) }}</textarea>
                </div>
              </div>
            </div>

            <!-- Submit Button -->
            <div class="mt-6 flex justify-end space-x-3">
              <button type="submit"
                class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                Save Changes
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Right Column - Status, Logo, Deposit -->
      <div class="space-y-6">
        <!-- Status Card -->
        <div class="rounded-lg border border-gray-700 bg-gray-800 p-6">
          <h2 class="mb-4 text-xl font-semibold text-white">Venue Status</h2>
          <div class="space-y-4">
            <label class="flex items-center space-x-3">
              <input type="checkbox" name="is_active" value="1"
                {{ old('is_active', $venue->is_active) ? 'checked' : '' }}
                class="rounded border-gray-600 bg-gray-700 text-blue-600">
              <span class="text-gray-300">Active for bookings</span>
            </label>

            <!-- Service User Assignment -->
            <div class="relative">
              <label class="block text-sm font-medium text-gray-400">Verified By</label>
              <input type="text" id="service_user_search" placeholder="Search for a service user..."
                class="mt-1 w-full rounded-lg border-gray-600 bg-gray-700 text-white">
              <div id="service_user_results"
                class="absolute z-10 mt-1 hidden w-full rounded-lg border border-gray-600 bg-gray-700"></div>
              <input type="hidden" name="service_user_id" id="service_user_id"
                value="{{ old('service_user_id', $venue->service_user_id) }}">
              <div id="selected_user" class="mt-2 text-sm text-gray-400">
                @if ($venue->serviceUser)
                  Currently verified by: {{ $venue->serviceUser->name }}
                @endif
              </div>
            </div>
          </div>
        </div>

        <!-- Logo Card -->
        <div class="rounded-lg border border-gray-700 bg-gray-800 p-6">
          <h2 class="mb-4 text-xl font-semibold text-white">Venue Logo</h2>
          <div class="space-y-4">
            @if ($venue->logo)
              <div class="mb-4">
                <img src="{{ Storage::url($venue->logo) }}" alt="Venue Logo" class="w-full rounded-lg object-cover">
              </div>
            @endif
            <input type="file" name="logo" id="logo" accept="image/*"
              class="w-full rounded-lg border-gray-600 bg-gray-700 text-white">
          </div>
        </div>

        <!-- Deposit Card -->
        <div class="rounded-lg border border-gray-700 bg-gray-800 p-6">
          <h2 class="mb-4 text-xl font-semibold text-white">Deposit Requirements</h2>
          <div class="space-y-4">
            <label class="flex items-center space-x-3">
              <input type="checkbox" name="requires_deposit" id="requires_deposit" value="1"
                {{ old('requires_deposit', $venue->requires_deposit) ? 'checked' : '' }}
                class="rounded border-gray-600 bg-gray-700 text-blue-600">
              <span class="text-gray-300">Deposit Required</span>
            </label>

            <div id="deposit_amount_container"
              class="{{ old('requires_deposit', $venue->requires_deposit) ? '' : 'hidden' }}">
              <label for="deposit_amount" class="block text-sm font-medium text-gray-400">Amount (£)</label>
              <input type="number" name="deposit_amount" id="deposit_amount"
                value="{{ old('deposit_amount', $venue->deposit_amount) }}"
                class="mt-1 w-full rounded-lg border-gray-600 bg-gray-700 text-white" step="0.01" min="0">
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">

    <script>
      document.addEventListener('DOMContentLoaded', function() {
        // Initialize Summernote
        $('.summernote').summernote({
          height: 200,
          toolbar: [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough']],
            ['para', ['ul', 'ol']],
          ],
          callbacks: {
            onImageUpload: function(files) {
              // Disable image upload
              return false;
            }
          }
        });

        // Handle deposit checkbox
        const depositCheckbox = document.getElementById('requires_deposit');
        const depositAmountContainer = document.getElementById('deposit_amount_container');

        depositCheckbox.addEventListener('change', function() {
          depositAmountContainer.classList.toggle('hidden', !this.checked);
        });

        // Service User Search
        const searchInput = document.getElementById('service_user_search');
        const resultsContainer = document.getElementById('service_user_results');
        let searchTimeout;

        searchInput.addEventListener('input', function() {
          clearTimeout(searchTimeout);
          searchTimeout = setTimeout(() => {
            const query = this.value;
            if (query.length >= 2) {
              fetch(`/admin/users/search?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                  resultsContainer.innerHTML = '';
                  resultsContainer.classList.remove('hidden');

                  data.forEach(user => {
                    const div = document.createElement('div');
                    div.className = 'p-2 hover:bg-gray-600 cursor-pointer';
                    div.textContent = user.name;
                    div.addEventListener('click', () => {
                      document.getElementById('service_user_id').value = user.id;
                      document.getElementById('selected_user').textContent =
                        `Currently assigned to: ${user.name}`;
                      resultsContainer.classList.add('hidden');
                      searchInput.value = '';
                    });
                    resultsContainer.appendChild(div);
                  });
                });
            }
          }, 300);
        });

        // Hide results when clicking outside
        document.addEventListener('click', function(e) {
          if (!searchInput.contains(e.target) && !resultsContainer.contains(e.target)) {
            resultsContainer.classList.add('hidden');
          }
        });
      });
    </script>
  @endpush
</x-admin-layout>
