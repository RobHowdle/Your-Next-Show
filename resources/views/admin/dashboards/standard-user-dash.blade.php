<x-app-layout :dashboardType="$dashboardType" :modules="$modules">
  <x-slot name="header">
    <x-sub-nav :userId="$userId" />
  </x-slot>

  <div class="mx-auto w-full max-w-screen-2xl py-16">
    <div class="relative mb-8 shadow-md sm:rounded-lg">
      <div
        class="min-w-screen-xl mx-auto max-w-screen-xl rounded-lg bg-yns_dark_gray px-16 py-12 text-center text-white">
        <x-greeting />
        <p class="mb-12 font-heading text-xl">This week you have:</p>
        <div class="grid grid-cols-2 items-center">
          <a href="{{ route('admin.dashboard.show-events', ['dashboardType' => $dashboardType]) }}"
            class="flex flex-col items-center text-center transition duration-150 ease-in-out hover:text-yns_yellow">
            <span class="fas fa-calendar-alt mb-4 h-14 w-14"></span>Event History Coming Soon!
            {{-- {{ $eventsCount }} Event{{ $eventsCount > 1 ? 's' : '' }} --}}
          </a>
          <a href="#"
            class="flex flex-col items-center text-center transition duration-150 ease-in-out hover:text-yns_yellow">
            <span class="fa-solid fa-location-dot mb-4 h-14 w-14"></span>
            Events in your area coming soon!
          </a>
        </div>
      </div>
    </div>

    <div class="relative shadow-md sm:rounded-lg">
      <div
        class="min-w-screen-xl mx-auto max-w-screen-xl rounded-lg bg-yns_dark_gray px-16 py-12 text-center text-white">
        <p class="mb-8 font-heading text-xl font-bold">Quick Links</p>
        <div class="grid grid-cols-3 items-center gap-y-12">
          <a href="{{ route('admin.dashboard.show-events', ['dashboardType' => $dashboardType]) }}"
            class="group flex flex-col items-center text-center transition duration-150 ease-in-out hover:text-yns_yellow">
            <span
              class="fas fa-calendar-alt mb-4 h-14 w-14 rounded-lg bg-white px-1 py-1 text-black transition duration-150 ease-in-out group-hover:text-yns_yellow"></span>
            Events
          </a>
          <a href="{{ route('admin.dashboard.show-finances', ['dashboardType' => $dashboardType]) }}"
            class="group flex flex-col items-center text-center transition duration-150 ease-in-out hover:text-yns_yellow">
            <span
              class="fas fa-clock-rotate-left mb-4 h-14 w-14 rounded-lg bg-white px-1 py-1 text-black transition duration-150 ease-in-out group-hover:text-yns_yellow"></span>
            Event History
          </a>
          <a href="{{ route('admin.dashboard.notes', ['dashboardType' => $dashboardType]) }}"
            class="group pointer-events-none flex cursor-not-allowed flex-col items-center text-center opacity-disabled transition duration-150 ease-in-out hover:text-yns_yellow">
            <span
              class="fa-solid fa-address-card mb-4 h-14 w-14 rounded-lg bg-white px-1 py-1 text-black transition duration-150 ease-in-out group-hover:text-yns_yellow"></span>
            LMLC
          </a>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
