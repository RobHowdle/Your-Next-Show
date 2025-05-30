<x-admin-layout>
  <div class="container mx-auto px-4 py-8">
    <!-- Page Header -->
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-white">Users Management</h1>
      <p class="mt-2 text-gray-400">View and manage all users on the platform</p>
    </div>

    <!-- Filters -->
    <div class="mb-6 rounded-lg border border-gray-700 bg-gray-800 p-4">
      <form action="{{ route('admin.users') }}" method="GET" class="grid gap-4 md:grid-cols-4">
        <div>
          <label class="block text-sm font-medium text-gray-400">Status</label>
          <select name="status" class="mt-1 w-full rounded-lg border-gray-600 bg-gray-700 text-white">
            <option value="">All</option>
            <option value="active">Active</option>
            <option value="deleted">Deleted</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-400">Verified</label>
          <select name="verified" class="mt-1 w-full rounded-lg border-gray-600 bg-gray-700 text-white">
            <option value="">All</option>
            <option value="1">Verified</option>
            <option value="0">Not Verified</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-400">Location</label>
          <input type="text" name="location" class="mt-1 w-full rounded-lg border-gray-600 bg-gray-700 text-white"
            placeholder="Filter by location">
        </div>
        <div class="flex items-end">
          <button type="submit" class="w-full rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
            <i class="fas fa-filter mr-2"></i> Filter
          </button>
        </div>
      </form>
    </div>

    <!-- Users Table -->
    <div class="overflow-hidden rounded-lg border border-gray-700 bg-gray-800">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="border-b border-gray-700 bg-gray-900">
            <tr>
              <th class="p-4 text-left text-sm font-medium text-gray-400">Name</th>
              <th class="p-4 text-left text-sm font-medium text-gray-400">Email</th>
              <th class="p-4 text-left text-sm font-medium text-gray-400">Status</th>
              <th class="p-4 text-left text-sm font-medium text-gray-400">Verified</th>
              <th class="p-4 text-left text-sm font-medium text-gray-400">Last Login</th>
              <th class="p-4 text-left text-sm font-medium text-gray-400">Location</th>
              <th class="p-4 text-left text-sm font-medium text-gray-400">Calendar</th>
              <th class="p-4 text-left text-sm font-medium text-gray-400">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-700">
            @foreach ($users as $user)
              <tr class="hover:bg-gray-700/50">
                <td class="p-4">
                  <div>
                    <div class="font-medium text-white">{{ $user->first_name . ' ' . $user->last_name }}</div>
                    <div class="text-sm text-gray-400">DOB:
                      {{ $user->date_of_birth ? $user->date_of_birth->format('d/m/Y') : 'Not set' }}</div>
                  </div>
                </td>
                <td class="p-4">
                  <div class="text-white">{{ $user->email }}</div>
                  <div class="text-sm text-gray-400">
                    Mailing: {{ $user->mailing_preferences ? 'Subscribed' : 'Unsubscribed' }}
                  </div>
                </td>
                <td class="p-4">
                  <span
                    class="{{ $user->deleted_at ? 'bg-red-500/10 text-red-400' : 'bg-green-500/10 text-green-400' }} inline-flex rounded-full px-2 text-xs font-semibold leading-5">
                    {{ $user->deleted_at ? 'Deleted' : 'Active' }}
                  </span>
                </td>
                <td class="p-4">
                  @if ($user->email_verified_at)
                    <span
                      class="inline-flex rounded-full bg-green-500/10 px-2 text-xs font-semibold leading-5 text-green-400">Verified</span>
                  @else
                    <div class="flex items-center gap-2">
                      <span
                        class="inline-flex rounded-full bg-yellow-500/10 px-2 text-xs font-semibold leading-5 text-yellow-400">Pending</span>
                      <button onclick="resendVerification({{ $user->id }})"
                        class="text-blue-400 hover:text-blue-300">
                        <i class="fas fa-redo-alt"></i>
                      </button>
                    </div>
                  @endif
                </td>
                <td class="p-4 text-gray-300">
                  {{ $user->last_logged_in ? $user->last_logged_in->diffForHumans() : 'Never' }}
                </td>
                <td class="p-4 text-gray-300">
                  {{ $user->location ?: 'Not set' }}
                </td>
                <td class="p-4">
                  <div class="flex gap-2">
                    @if ($user->google_calendar_connected)
                      <i class="fab fa-google text-white" title="Google Calendar Connected"></i>
                    @endif
                    @if ($user->apple_calendar_connected)
                      <i class="fab fa-apple text-white" title="Apple Calendar Connected"></i>
                    @endif
                  </div>
                </td>
                <td class="p-4">
                  <div class="flex gap-2">
                    @if (!$user->isSuperAdmin())
                      <a href="{{ route('admin.users.edit', $user->id) }}"
                        class="rounded bg-blue-600 px-2 py-1 text-xs text-white hover:bg-blue-700">
                        Edit
                      </a>
                      @if ($user->deleted_at)
                        <button onclick="restoreUser({{ $user->id }})"
                          class="rounded bg-green-600 px-2 py-1 text-xs text-white hover:bg-green-700">
                          Restore
                        </button>
                      @else
                        <button onclick="deleteUser({{ $user->id }})"
                          class="rounded bg-red-600 px-2 py-1 text-xs text-white hover:bg-red-700">
                          Delete
                        </button>
                      @endif
                    @else
                      <span class="text-xs text-gray-400">Protected Account</span>
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
      {{ $users->links() }}
    </div>
  </div>

  @push('scripts')
    <script>
      function resendVerification(userId) {
        // Add your verification email resend logic here
      }

      function deleteUser(userId) {
        if (confirm('Are you sure you want to delete this user?')) {
          // Add your delete logic here
        }
      }

      function restoreUser(userId) {
        if (confirm('Are you sure you want to restore this user?')) {
          // Add your restore logic here
        }
      }
    </script>
  @endpush
</x-admin-layout>
