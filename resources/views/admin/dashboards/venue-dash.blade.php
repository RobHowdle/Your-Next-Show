<x-app-layout :dashboardType="$dashboardType" :modules="$modules">
  <x-slot name="header">
    <x-sub-nav :userId="$userId" />
  </x-slot>

  <div class="mx-auto w-full max-w-screen-2xl px-4 py-8 sm:py-16">
    {{-- Stats Section --}}
    <div class="relative mb-8 shadow-md sm:rounded-lg">
      <div class="mx-auto max-w-screen-xl rounded-lg bg-yns_dark_gray p-2 text-center text-white md:px-6 md:py-8">
        <x-greeting />
        <p class="mb-8 font-heading text-lg sm:mb-12 sm:text-xl">This week you have:</p>
        <div class="grid grid-cols-2 gap-6 sm:grid-cols-4">
          <a href="{{ route('admin.dashboard.show-events', ['dashboardType' => $dashboardType]) }}"
            class="flex flex-col items-center text-center transition duration-150 ease-in-out hover:text-yns_yellow">
            <span class="fas fa-calendar-alt mb-3 h-10 w-10 sm:mb-4 sm:h-14 sm:w-14"></span>
            <span class="text-sm sm:text-base">{{ $eventsCount }} Event{{ $eventsCount > 1 ? 's' : '' }}</span>
          </a>
          <a href="#"
            class="pointer-events-none flex cursor-not-allowed flex-col items-center text-center opacity-disabled transition duration-150 ease-in-out hover:text-yns_yellow">
            <span class="fas fa-guitar mb-3 h-10 w-10 sm:mb-4 sm:h-14 sm:w-14"></span>
            <span class="text-sm sm:text-base">6 Available Bands</span>
          </a>
          <a href="{{ route('dashboard.reviews', ['dashboardType' => $dashboardType]) }}"
            class="flex flex-col items-center text-center transition duration-150 ease-in-out hover:text-yns_yellow">
            <span class="fas fa-star mb-3 h-10 w-10 sm:mb-4 sm:h-14 sm:w-14"></span>
            <span class="text-sm sm:text-base">{{ $pendingReviews }} Pending
              Review{{ $pendingReviews > 1 ? 's' : '' }}</span>
          </a>
          <a href="{{ route('admin.dashboard.todo-list', ['dashboardType' => $dashboardType]) }}"
            class="flex flex-col items-center text-center transition duration-150 ease-in-out hover:text-yns_yellow">
            <span class="fas fa-list mb-3 h-10 w-10 sm:mb-4 sm:h-14 sm:w-14"></span>
            <span class="text-sm sm:text-base">{{ $todoItemsCount }} Todo
              Item{{ $todoItemsCount > 1 ? 's' : '' }}</span>
          </a>
        </div>
      </div>
    </div>

    {{-- Quick Links Section --}}
    <div class="relative shadow-md sm:rounded-lg">
      <div class="mx-auto max-w-screen-xl rounded-lg bg-yns_dark_gray p-6 text-center text-white sm:px-16 sm:py-12">
        <p class="mb-6 font-heading text-lg font-bold sm:mb-8 sm:text-xl">Quick Links</p>

        {{-- Create Actions --}}
        <div class="mb-8 grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-5 lg:gap-6">
          <a href="{{ route('admin.dashboard.new-user', ['dashboardType' => $dashboardType]) }}"
            class="group flex flex-col items-center p-2 text-center transition duration-150 ease-in-out hover:text-yns_yellow">
            <span
              class="fas fa-user mb-2 h-10 w-10 rounded-lg bg-white p-2 text-black transition duration-150 group-hover:text-yns_yellow sm:mb-4 sm:h-14 sm:w-14"></span>
            <span class="text-sm sm:text-base">New User</span>
          </a>

          <a href="{{ route('admin.dashboard.create-new-event', ['dashboardType' => $dashboardType]) }}"
            class="group flex flex-col items-center p-2 text-center transition duration-150 ease-in-out hover:text-yns_yellow">
            <span
              class="fas fa-calendar-alt mb-2 h-10 w-10 rounded-lg bg-white p-2 text-black transition duration-150 group-hover:text-yns_yellow sm:mb-4 sm:h-14 sm:w-14"></span>
            <span class="text-sm sm:text-base">New Event</span>
          </a>

          <a href="{{ route('admin.dashboard.create-new-finance', ['dashboardType' => $dashboardType]) }}"
            class="group flex flex-col items-center p-2 text-center transition duration-150 ease-in-out hover:text-yns_yellow">
            <span
              class="fas fa-pound-sign mb-2 h-10 w-10 rounded-lg bg-white p-2 text-black transition duration-150 group-hover:text-yns_yellow sm:mb-4 sm:h-14 sm:w-14"></span>
            <span class="text-sm sm:text-base">New Budget</span>
          </a>

          <button id="new-note-button"
            class="group flex flex-col items-center p-2 text-center transition duration-150 ease-in-out hover:text-yns_yellow">
            <span
              class="fas fa-sticky-note mb-2 h-10 w-10 rounded-lg bg-white p-2 text-black transition duration-150 group-hover:text-yns_yellow sm:mb-4 sm:h-14 sm:w-14"></span>
            <span class="text-sm sm:text-base">New Note</span>
          </button>

          <a href="{{ route('admin.dashboard.todo-list', ['dashboardType' => $dashboardType]) }}"
            class="group flex flex-col items-center p-2 text-center transition duration-150 ease-in-out hover:text-yns_yellow">
            <span
              class="fas fa-list mb-2 h-10 w-10 rounded-lg bg-white p-2 text-black transition duration-150 group-hover:text-yns_yellow sm:mb-4 sm:h-14 sm:w-14"></span>
            <span class="text-sm sm:text-base">New Todo</span>
          </a>
        </div>

        {{-- View Actions --}}
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-5 lg:gap-6">
          <a href="{{ route('admin.dashboard.users', ['dashboardType' => $dashboardType]) }}"
            class="group flex flex-col items-center p-2 text-center transition duration-150 ease-in-out hover:text-yns_yellow">
            <span
              class="fas fa-users mb-2 h-10 w-10 rounded-lg bg-white p-2 text-black transition duration-150 group-hover:text-yns_yellow sm:mb-4 sm:h-14 sm:w-14"></span>
            <span class="text-sm sm:text-base">Users</span>
          </a>

          <a href="{{ route('admin.dashboard.show-events', ['dashboardType' => $dashboardType]) }}"
            class="group flex flex-col items-center p-2 text-center transition duration-150 ease-in-out hover:text-yns_yellow">
            <span
              class="fas fa-calendar-alt mb-2 h-10 w-10 rounded-lg bg-white p-2 text-black transition duration-150 group-hover:text-yns_yellow sm:mb-4 sm:h-14 sm:w-14"></span>
            <span class="text-sm sm:text-base">Events</span>
          </a>

          <a href="{{ route('admin.dashboard.show-finances', ['dashboardType' => $dashboardType]) }}"
            class="group flex flex-col items-center p-2 text-center transition duration-150 ease-in-out hover:text-yns_yellow">
            <span
              class="fas fa-pound-sign mb-2 h-10 w-10 rounded-lg bg-white p-2 text-black transition duration-150 group-hover:text-yns_yellow sm:mb-4 sm:h-14 sm:w-14"></span>
            <span class="text-sm sm:text-base">Budgets</span>
          </a>

          <a href="{{ route('admin.dashboard.notes', ['dashboardType' => $dashboardType]) }}"
            class="group flex flex-col items-center p-2 text-center transition duration-150 ease-in-out hover:text-yns_yellow">
            <span
              class="fas fa-sticky-note mb-2 h-10 w-10 rounded-lg bg-white p-2 text-black transition duration-150 group-hover:text-yns_yellow sm:mb-4 sm:h-14 sm:w-14"></span>
            <span class="text-sm sm:text-base">Notes</span>
          </a>

          <a href="{{ route('admin.dashboard.todo-list', ['dashboardType' => $dashboardType]) }}"
            class="group flex flex-col items-center p-2 text-center transition duration-150 ease-in-out hover:text-yns_yellow">
            <span
              class="fas fa-list mb-2 h-10 w-10 rounded-lg bg-white p-2 text-black transition duration-150 group-hover:text-yns_yellow sm:mb-4 sm:h-14 sm:w-14"></span>
            <span class="text-sm sm:text-base">Todo List</span>
          </a>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
