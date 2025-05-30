<x-app-layout :dashboardType="$dashboardType" :modules="$modules">
  <x-slot name="header">
    <x-sub-nav :userId="$userId" />
  </x-slot>

  <div class="mx-auto w-full max-w-screen-2xl px-4 py-8 sm:px-6 lg:px-8 lg:py-16">
    {{-- Stats Card --}}
    <div class="relative mb-8 shadow-md sm:rounded-lg">
      <div
        class="mx-auto max-w-screen-xl rounded-lg bg-yns_dark_gray p-6 text-center text-white sm:p-8 lg:px-16 lg:py-12">
        <x-greeting />
        <p class="mb-8 font-heading text-xl sm:mb-12">This week you have:</p>
        <div class="grid grid-cols-2 gap-8 sm:grid-cols-2 lg:grid-cols-4">
          <a href="{{ route('admin.dashboard.jobs', ['dashboardType' => $dashboardType]) }}"
            class="flex flex-col items-center text-center transition duration-150 ease-in-out hover:text-yns_yellow">
            <span class="fa-solid fa-pen-ruler mb-4 h-10 w-10 sm:h-12 sm:w-12 lg:h-14 lg:w-14"></span>
            <span class="text-sm sm:text-base">{{ $jobCount }} Job{{ $jobCount !== 1 ? 's' : '' }}</span>
          </a>
          <a href="#"
            class="flex cursor-not-allowed flex-col items-center text-center opacity-disabled transition duration-150 ease-in-out hover:text-yns_yellow">
            <span class="fa-solid fa-pencil mb-4 h-10 w-10 sm:h-12 sm:w-12 lg:h-14 lg:w-14"></span>
            <span class="text-sm sm:text-base">6 Available Jobs</span>
          </a>
          <a href="{{ route('dashboard.reviews', ['dashboardType' => $dashboardType]) }}"
            class="flex flex-col items-center text-center transition duration-150 ease-in-out hover:text-yns_yellow">
            <span class="fas fa-star mb-4 h-10 w-10 sm:h-12 sm:w-12 lg:h-14 lg:w-14"></span>
            <span class="text-sm sm:text-base">{{ $pendingReviews }} Pending Review{{ $pendingReviews > 1 ? 's' : '' }}
            </span>
          </a>
          <a href="{{ route('admin.dashboard.todo-list', ['dashboardType' => $dashboardType]) }}"
            class="flex flex-col items-center text-center transition duration-150 ease-in-out hover:text-yns_yellow">
            <span class="fas fa-list mb-4 h-10 w-10 sm:h-12 sm:w-12 lg:h-14 lg:w-14"></span>
            <span class="text-sm sm:text-base">{{ $todoItemsCount }} Todo
              Item{{ $todoItemsCount > 1 ? 's' : '' }}</span>
          </a>
        </div>
      </div>
    </div>

    <div class="relative shadow-md sm:rounded-lg">
      <div
        class="mx-auto max-w-screen-xl rounded-lg bg-yns_dark_gray p-6 text-center text-white sm:p-8 lg:px-16 lg:py-12">
        <p class="mb-6 font-heading text-xl font-bold sm:mb-8">Quick Links</p>
        <div class="grid grid-cols-2 gap-6 sm:grid-cols-2 lg:grid-cols-4 lg:gap-y-12">
          {{-- New Items Section --}}
          <a href="{{ route('admin.dashboard.document.create', ['dashboardType' => $dashboardType]) }}"
            class="group flex flex-col items-center text-center transition duration-150 ease-in-out hover:text-yns_yellow">
            <span
              class="fas fa-file-alt mb-3 h-10 w-10 rounded-lg bg-white p-2 text-black transition duration-150 ease-in-out group-hover:text-yns_yellow sm:mb-4 sm:h-12 sm:w-12 lg:h-14 lg:w-14">
            </span>
            <span class="text-sm sm:text-base">New Document</span>
          </a>
          <a href="{{ route('admin.dashboard.jobs.create', ['dashboardType' => $dashboardType]) }}"
            class="group flex flex-col items-center text-center transition duration-150 ease-in-out hover:text-yns_yellow">
            <span
              class="fa-solid fa-pen-ruler mb-3 h-10 w-10 rounded-lg bg-white p-2 text-black transition duration-150 ease-in-out group-hover:text-yns_yellow sm:mb-4 sm:h-12 sm:w-12 lg:h-14 lg:w-14">
            </span>
            <span class="text-sm sm:text-base">New Job</span>
          </a>

          <button id="new-note-button"
            class="group flex flex-col items-center text-center transition duration-150 ease-in-out hover:text-yns_yellow">
            <span
              class="fas fa-sticky-note mb-4 h-14 w-14 rounded-lg bg-white px-1 py-1 text-black transition duration-150 ease-in-out group-hover:text-yns_yellow"></span>
            New Note
          </button>
          <a href="{{ route('admin.dashboard.todo-list', ['dashboardType' => $dashboardType]) }}"
            class="group flex flex-col items-center text-center transition duration-150 ease-in-out hover:text-yns_yellow">
            <span
              class="fas fa-list mb-4 h-14 w-14 rounded-lg bg-white px-1 py-1 text-black transition duration-150 ease-in-out group-hover:text-yns_yellow"></span>
            New Todo Item
          </a>

          {{-- View Items Section --}}
          <a href="{{ route('admin.dashboard.documents.index', ['dashboardType' => $dashboardType]) }}"
            class="group flex flex-col items-center text-center transition duration-150 ease-in-out hover:text-yns_yellow">
            <span
              class="fas fa-file-alt mb-3 h-10 w-10 rounded-lg bg-white p-2 text-black transition duration-150 ease-in-out group-hover:text-yns_yellow sm:mb-4 sm:h-12 sm:w-12 lg:h-14 lg:w-14">
            </span>
            <span class="text-sm sm:text-base">Documents</span>
          </a>
          <a href="{{ route('admin.dashboard.jobs', ['dashboardType' => $dashboardType]) }}"
            class="group flex flex-col items-center text-center transition duration-150 ease-in-out hover:text-yns_yellow">
            <span
              class="fa-solid fa-pen-ruler mb-3 h-10 w-10 rounded-lg bg-white p-2 text-black transition duration-150 ease-in-out group-hover:text-yns_yellow sm:mb-4 sm:h-12 sm:w-12 lg:h-14 lg:w-14">
            </span>
            <span class="text-sm sm:text-base">Jobs</span>
          </a>
          <a href="{{ route('admin.dashboard.notes', ['dashboardType' => $dashboardType]) }}"
            class="group flex flex-col items-center text-center transition duration-150 ease-in-out hover:text-yns_yellow">
            <span
              class="fas fa-sticky-note mb-3 h-10 w-10 rounded-lg bg-white p-2 text-black transition duration-150 ease-in-out group-hover:text-yns_yellow sm:mb-4 sm:h-12 sm:w-12 lg:h-14 lg:w-14">
            </span>
            <span class="text-sm sm:text-base">Notes</span>
          </a>
          <a href="{{ route('admin.dashboard.todo-list', ['dashboardType' => $dashboardType]) }}"
            class="group flex flex-col items-center text-center transition duration-150 ease-in-out hover:text-yns_yellow">
            <span
              class="fas fa-list mb-3 h-10 w-10 rounded-lg bg-white p-2 text-black transition duration-150 ease-in-out group-hover:text-yns_yellow sm:mb-4 sm:h-12 sm:w-12 lg:h-14 lg:w-14">
            </span>
            <span class="text-sm sm:text-base">Todo List</span>
          </a>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
