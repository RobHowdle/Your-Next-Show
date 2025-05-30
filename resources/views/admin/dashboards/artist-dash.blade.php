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
        <div class="grid grid-cols-4 items-center">
          <a href="{{ route('admin.dashboard.show-events', ['dashboardType' => $dashboardType]) }}"
            class="flex flex-col items-center text-center transition duration-150 ease-in-out hover:text-yns_yellow">
            <span class="fas fa-calendar-alt mb-4 h-14 w-14"></span>
            {{ $eventsCount }} Event{{ $eventsCount > 1 ? 's' : '' }}
          </a>
          <a href="#"
            class="pointer-events-none flex cursor-not-allowed flex-col items-center text-center opacity-disabled transition duration-150 ease-in-out hover:text-yns_yellow">
            <span class="fas fa-calendar-plus mb-4 h-14 w-14"></span>
            6 Available Events
          </a>
          <a href="{{ route('dashboard.reviews', ['dashboardType' => $dashboardType]) }}"
            class="flex flex-col items-center text-center transition duration-150 ease-in-out hover:text-yns_yellow">
            <span class="fas fa-star mb-4 h-14 w-14"></span>
            {{ $pendingReviews }} Pending Review{{ $pendingReviews > 1 ? 's' : '' }}
          </a>
          <a href="{{ route('admin.dashboard.todo-list', ['dashboardType' => $dashboardType]) }}"
            class="flex flex-col items-center text-center transition duration-150 ease-in-out hover:text-yns_yellow">
            <span class="fas fa-list mb-4 h-14 w-14"></span>
            {{ $todoItemsCount }} Todo Item{{ $todoItemsCount > 1 ? 's' : '' }}
          </a>
        </div>
      </div>
    </div>

    <div class="relative shadow-md sm:rounded-lg">
      <div
        class="min-w-screen-xl mx-auto max-w-screen-xl rounded-lg bg-yns_dark_gray px-16 py-12 text-center text-white">
        <p class="mb-8 font-heading text-xl font-bold">Quick Links</p>
        <div class="grid grid-cols-4 items-center gap-y-12">
          <a href="{{ route('admin.dashboard.document.create', ['dashboardType' => $dashboardType]) }}"
            class="group flex flex-col items-center text-center transition duration-150 ease-in-out hover:text-yns_yellow">
            <span
              class="fas fa-file-alt mb-4 h-14 w-14 rounded-lg bg-white px-1 py-1 text-black transition duration-150 ease-in-out group-hover:text-yns_yellow"></span>
            New Document
          </a>
          <a href="{{ route('admin.dashboard.create-new-event', ['dashboardType' => $dashboardType]) }}"
            class="group flex flex-col items-center text-center transition duration-150 ease-in-out hover:text-yns_yellow">
            <span
              class="fas fa-calendar-alt mb-4 h-14 w-14 rounded-lg bg-white px-1 py-1 text-black transition duration-150 ease-in-out group-hover:text-yns_yellow"></span>
            New Event
          </a>
          <button id="new-note-button"
            class="group flex flex-col items-center text-center transition duration-150 ease-in-out hover:text-yns_yellow">
            <span
              class="fas fa-sticky-note mb-4 h-14 w-14 rounded-lg bg-white px-1 py-1 text-black transition duration-150 ease-in-out group-hover:text-yns_yellow"></span>
            New Note
          </button>
          <a href="#"
            class="group flex flex-col items-center text-center transition duration-150 ease-in-out hover:text-yns_yellow">
            <span
              class="fas fa-list mb-4 h-14 w-14 rounded-lg bg-white px-1 py-1 text-black transition duration-150 ease-in-out group-hover:text-yns_yellow"></span>
            New Todo Item
          </a>

          <a href="{{ route('admin.dashboard.documents.index', ['dashboardType' => $dashboardType]) }}"
            class="group flex flex-col items-center text-center transition duration-150 ease-in-out hover:text-yns_yellow">
            <span
              class="fas fa-file-alt mb-4 h-14 w-14 rounded-lg bg-white px-1 py-1 text-black transition duration-150 ease-in-out group-hover:text-yns_yellow"></span>
            Documents
          </a>
          <a href="{{ route('admin.dashboard.show-events', ['dashboardType' => $dashboardType]) }}"
            class="group flex flex-col items-center text-center transition duration-150 ease-in-out hover:text-yns_yellow">
            <span
              class="fas fa-calendar-alt mb-4 h-14 w-14 rounded-lg bg-white px-1 py-1 text-black transition duration-150 ease-in-out group-hover:text-yns_yellow"></span>
            Events
          </a>
          <a href="{{ route('admin.dashboard.notes', ['dashboardType' => $dashboardType]) }}"
            class="group flex flex-col items-center text-center transition duration-150 ease-in-out hover:text-yns_yellow">
            <span
              class="fas fa-sticky-note mb-4 h-14 w-14 rounded-lg bg-white px-1 py-1 text-black transition duration-150 ease-in-out group-hover:text-yns_yellow"></span>
            Notes
          </a>
          <a href="#"
            class="group flex flex-col items-center text-center transition duration-150 ease-in-out hover:text-yns_yellow">
            <span
              class="fas fa-list mb-4 h-14 w-14 rounded-lg bg-white px-1 py-1 text-black transition duration-150 ease-in-out group-hover:text-yns_yellow"></span>
            Todo List
          </a>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
