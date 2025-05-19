<x-app-layout :dashboardType="$dashboardType" :modules="$modules">
  <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
    <div class="rounded-lg bg-red-500/10 p-8 text-center">
      <div class="mx-auto max-w-md">
        <h1 class="mb-4 font-heading text-2xl font-bold text-red-500">
          Insufficient Permissions
        </h1>
        <p class="mb-6 text-gray-400">
          You don't have permission to view this event.
        </p>
        <a href="{{ route('admin.dashboard.show-events', ['dashboardType' => $dashboardType]) }}"
          class="inline-flex items-center rounded-lg bg-gray-800 px-4 py-2 text-sm font-semibold text-white ring-1 ring-gray-700 transition hover:bg-gray-700 hover:ring-red-500">
          <span class="fas fa-arrow-left mr-2"></span>
          Back to Events
        </a>
      </div>
    </div>
  </div>
</x-app-layout>
