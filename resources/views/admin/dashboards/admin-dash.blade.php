<x-admin-layout>
  @role('administrator')
    <div class="container mx-auto px-4 py-8">
      <!-- Page Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-white">Administrator Dashboard</h1>
        <p class="mt-2 text-gray-400">Manage your platform's users, venues, promoters and services.</p>
      </div>

      <!-- Stats Cards -->
      <div class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-lg border border-gray-700 bg-gray-800 p-6">
          <div class="flex items-center justify-between">
            <h3 class="text-sm font-medium text-gray-400">Total Users</h3>
            <span class="fas fa-users text-blue-500"></span>
          </div>
          <p class="mt-2 text-2xl font-bold text-white">{{ $totalUsers }}</p>
        </div>

        <div class="rounded-lg border border-gray-700 bg-gray-800 p-6">
          <div class="flex items-center justify-between">
            <h3 class="text-sm font-medium text-gray-400">Total Venues</h3>
            <span class="fas fa-building text-green-500"></span>
          </div>
          <p class="mt-2 text-2xl font-bold text-white">{{ $totalVenues }}</p>
        </div>

        <div class="rounded-lg border border-gray-700 bg-gray-800 p-6">
          <div class="flex items-center justify-between">
            <h3 class="text-sm font-medium text-gray-400">Total Promoters</h3>
            <span class="fas fa-bullhorn text-purple-500"></span>
          </div>
          <p class="mt-2 text-2xl font-bold text-white">{{ $totalPromoters }}</p>
        </div>

        <div class="rounded-lg border border-gray-700 bg-gray-800 p-6">
          <div class="flex items-center justify-between">
            <h3 class="text-sm font-medium text-gray-400">Total Services</h3>
            <span class="fas fa-cogs text-yellow-500"></span>
          </div>
          <p class="mt-2 text-2xl font-bold text-white">{{ $totalServices }}</p>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="mb-8 rounded-lg border border-gray-700 bg-gray-800 p-6">
        <h2 class="mb-4 text-xl font-bold text-white">Quick Actions</h2>
        <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
          <a href="{{ route('admin.venues.create') }}"
            class="flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-center text-sm font-medium text-white transition-colors hover:bg-blue-700">
            <span class="fas fa-plus-circle"></span>
            Add Venue
          </a>
          <a href="{{ route('admin.promoters.create') }}"
            class="flex items-center justify-center gap-2 rounded-lg bg-green-600 px-4 py-2 text-center text-sm font-medium text-white transition-colors hover:bg-green-700">
            <span class="fas fa-plus-circle"></span>
            Add Promoter
          </a>
          <a href="{{ route('admin.services.create') }}"
            class="flex items-center justify-center gap-2 rounded-lg bg-purple-600 px-4 py-2 text-center text-sm font-medium text-white transition-colors hover:bg-purple-700">
            <span class="fas fa-plus-circle"></span>
            Add Service
          </a>
          <a href="{{ route('admin.users.create') }}"
            class="flex items-center justify-center gap-2 rounded-lg bg-yellow-600 px-4 py-2 text-center text-sm font-medium text-white transition-colors hover:bg-yellow-700">
            <span class="fas fa-plus-circle"></span>
            Add User
          </a>
        </div>
      </div>

      <!-- Management Sections -->
      <div class="grid gap-8 md:grid-cols-2">
        <!-- Users Management -->
        <div class="rounded-lg border border-gray-700 bg-gray-800 p-6">
          <div class="mb-4 flex items-center justify-between">
            <h2 class="text-xl font-bold text-white">Recent Users</h2>
            <a href="{{ route('admin.users') }}" class="text-sm text-blue-500 hover:text-blue-400">View All</a>
          </div>
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead class="border-b border-gray-700">
                <tr>
                  <th class="p-2 text-left text-sm font-medium text-gray-400">Name</th>
                  <th class="p-2 text-left text-sm font-medium text-gray-400">Email</th>
                  <th class="p-2 text-left text-sm font-medium text-gray-400">Role</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-700">
                @foreach ($users as $user)
                  <tr class="hover:bg-gray-700/50">
                    <td class="p-2 text-white">{{ $user->first_name . ' ' . $user->last_name }}</td>
                    <td class="p-2 text-gray-300">{{ $user->email }}</td>
                    <td class="p-2 text-gray-300">{{ $user->roles->first()->name ?? 'No Role' }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>

        <!-- Venues Management -->
        <div class="rounded-lg border border-gray-700 bg-gray-800 p-6">
          <div class="mb-4 flex items-center justify-between">
            <h2 class="text-xl font-bold text-white">Recent Venues</h2>
            <a href="{{ route('admin.venues') }}" class="text-sm text-blue-500 hover:text-blue-400">View All</a>
          </div>
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead class="border-b border-gray-700">
                <tr>
                  <th class="p-2 text-left text-sm font-medium text-gray-400">Name</th>
                  <th class="p-2 text-left text-sm font-medium text-gray-400">Location</th>
                  <th class="p-2 text-left text-sm font-medium text-gray-400">Capacity</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-700">
                @foreach ($venues as $venue)
                  <tr class="hover:bg-gray-700/50">
                    <td class="p-2 text-white">{{ $venue->name }}</td>
                    <td class="p-2 text-gray-300">{{ $venue->location }}</td>
                    <td class="p-2 text-gray-300">{{ $venue->capacity }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  @endrole
</x-admin-layout>
