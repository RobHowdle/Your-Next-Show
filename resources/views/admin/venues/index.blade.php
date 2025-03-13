<x-admin-layout>
  <div class="container mx-auto px-4 py-8">
    <!-- Page Header -->
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-white">Venues Management</h1>
      <p class="mt-2 text-gray-400">View and manage all venues on the platform</p>
    </div>

    <!-- Filters -->
    <div class="mb-6 rounded-lg border border-gray-700 bg-gray-800 p-4">
      <form action="{{ route('admin.venues') }}" method="GET" class="grid gap-4 md:grid-cols-4">
        <div>
          <label class="block text-sm font-medium text-gray-400">Status</label>
          <select name="status" class="mt-1 w-full rounded-lg border-gray-600 bg-gray-700 text-white">
            <option value="">All</option>
            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
            <option value="deleted" {{ request('status') == 'deleted' ? 'selected' : '' }}>Deleted</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-400">Verification</label>
          <select name="verified" class="mt-1 w-full rounded-lg border-gray-600 bg-gray-700 text-white">
            <option value="">All</option>
            <option value="1" {{ request('verified') == '1' ? 'selected' : '' }}>Verified</option>
            <option value="0" {{ request('verified') == '0' ? 'selected' : '' }}>Pending</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-400">Location</label>
          <input type="text" name="location" value="{{ request('location') }}"
            class="mt-1 w-full rounded-lg border-gray-600 bg-gray-700 text-white" placeholder="Filter by location">
        </div>
        <div class="flex items-end">
          <button type="submit" class="w-full rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
            <i class="fas fa-filter mr-2"></i> Filter
          </button>
        </div>
      </form>
    </div>

    <!-- Venues Table -->
    <div class="overflow-hidden rounded-lg border border-gray-700 bg-gray-800">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="border-b border-gray-700 bg-gray-900">
            <tr>
              <th class="p-4 text-left text-sm font-medium text-gray-400">Venue</th>
              <th class="p-4 text-left text-sm font-medium text-gray-400">Contact</th>
              <th class="p-4 text-left text-sm font-medium text-gray-400">Status</th>
              <th class="p-4 text-left text-sm font-medium text-gray-400">Verified</th>
              <th class="p-4 text-left text-sm font-medium text-gray-400">Location</th>
              <th class="p-4 text-left text-sm font-medium text-gray-400">Capacity</th>
              <th class="p-4 text-left text-sm font-medium text-gray-400">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-700">
            @foreach ($venues as $venue)
              <tr class="hover:bg-gray-700/50">
                <td class="p-4">
                  <div>
                    <div class="font-medium text-white">{{ $venue->name }}</div>
                  </div>
                </td>
                <td class="p-4">
                  <div class="text-white">{{ $venue->contact_email }}</div>
                  <div class="text-sm text-gray-400">{{ $venue->contact_phone }}</div>
                </td>
                <td class="p-4">
                  <span
                    class="{{ $venue->deleted_at ? 'bg-red-500/10 text-red-400' : 'bg-green-500/10 text-green-400' }} inline-flex rounded-full px-2 text-xs font-semibold leading-5">
                    {{ $venue->deleted_at ? 'Deleted' : 'Active' }}
                  </span>
                </td>
                <td class="p-4">
                  @if ($venue->is_verified)
                    <span
                      class="inline-flex rounded-full bg-green-500/10 px-2 text-xs font-semibold leading-5 text-green-400">
                      Verified
                    </span>
                  @else
                    <span
                      class="inline-flex rounded-full bg-yellow-500/10 px-2 text-xs font-semibold leading-5 text-yellow-400">
                      Pending
                    </span>
                  @endif
                </td>
                <td class="p-4 text-gray-300">
                  <div>{{ $venue->location }}</div>
                  <div class="text-sm text-gray-400">{{ $venue->postal_town }}</div>
                </td>
                <td class="p-4 text-gray-300">
                  {{ $venue->capacity ?: 'Not set' }}
                </td>
                <td class="p-4">
                  <div class="flex gap-2">
                    <a href="{{ route('admin.venues.edit', $venue->id) }}"
                      class="rounded bg-blue-600 px-2 py-1 text-xs text-white hover:bg-blue-700">
                      Edit
                    </a>
                    @if ($venue->deleted_at)
                      <button onclick="restoreVenue({{ $venue->id }})"
                        class="rounded bg-green-600 px-2 py-1 text-xs text-white hover:bg-green-700">
                        Restore
                      </button>
                    @else
                      <button onclick="deleteVenue({{ $venue->id }})"
                        class="rounded bg-red-600 px-2 py-1 text-xs text-white hover:bg-red-700">
                        Delete
                      </button>
                    @endif
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
      {{ $venues->links() }}
    </div>
  </div>

  @push('scripts')
    <script>
      function deleteVenue(venueId) {
        if (confirm('Are you sure you want to delete this venue?')) {
          fetch(`/admin/venues/${venueId}`, {
            method: 'DELETE',
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            }
          }).then(response => {
            if (response.ok) {
              window.location.reload();
            }
          });
        }
      }

      function restoreVenue(venueId) {
        if (confirm('Are you sure you want to restore this venue?')) {
          fetch(`/admin/venues/${venueId}/restore`, {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            }
          }).then(response => {
            if (response.ok) {
              window.location.reload();
            }
          });
        }
      }
    </script>
  @endpush
</x-admin-layout>
