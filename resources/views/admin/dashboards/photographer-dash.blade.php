<x-app-layout :dashboardType="$dashboardType" :modules="$modules">
  <x-slot name="header">
    <x-sub-nav :userId="$userId" />
  </x-slot>
  @php
    $user = auth()->user();
    $links = [
        'finances' => $user->can('view_finances')
            ? route('admin.dashboard.show-finances', ['dashboardType' => $dashboardType])
            : null,
        'events' => $user->can('view_events')
            ? route('admin.dashboard.show-events', ['dashboardType' => $dashboardType])
            : null,
        'todo_list' => $user->can('view_todo_list')
            ? route('admin.dashboard.todo-list', ['dashboardType' => $dashboardType])
            : null,
        'reviews' => $user->can('view_reviews')
            ? route('dashboard.reviews', [
                'filter' => 'all',
                'dashboardType' => $dashboardType,
            ])
            : null,
        'notes' => $user->can('view_notes')
            ? route('admin.dashboard.notes', ['dashboardType' => $dashboardType])
            : null,
        'documents' => $user->can('view_documents')
            ? route('admin.dashboard.documents.index', ['dashboardType' => $dashboardType])
            : null,
        'users' => $user->can('view_users')
            ? route('admin.dashboard.users', ['dashboardType' => $dashboardType])
            : null,
        'jobs' => $user->can('view_jobs') ? route('admin.dashboard.jobs', ['dashboardType' => $dashboardType]) : null,
    ];
  @endphp

  <div class="mx-auto w-full max-w-screen-2xl py-8 sm:py-8">
    {{-- Stats Section --}}
    <div class="relative mb-8 px-4 sm:px-0">
      <div
        class="min-w-screen-xl mx-auto max-w-screen-xl rounded-lg border border-gray-800 bg-black p-6 text-center text-white shadow-[0_4px_20px_-4px_rgba(0,0,0,0.9),inset_0_2px_0_rgba(255,255,255,0.1)] sm:p-8">
        <x-greeting />
        <p class="mb-8 font-heading text-lg sm:mb-12 sm:text-xl">This week you have:</p>

        <div class="grid grid-cols-2 gap-6 sm:grid-cols-1 sm:gap-8 lg:grid-cols-4">
          {{-- Jobs --}}
          <a href="{{ route('admin.dashboard.jobs', ['dashboardType' => $dashboardType]) }}"
            class="group flex flex-col items-center rounded-lg border border-gray-800 bg-gray-900 p-4 text-center transition duration-150 ease-in-out hover:bg-gray-800">
            <span
              class="fas fa-pencil-alt mb-3 h-10 w-10 transform text-yns_yellow transition-transform group-hover:scale-110 sm:h-14 sm:w-14"></span>
            <span class="text-sm font-medium sm:text-base">{{ $jobsCount }} Job{{ $jobsCount > 1 ? 's' : '' }}</span>
          </a>

          {{-- Available Jobs (Disabled State) --}}
          <div
            class="flex flex-col items-center rounded-lg border border-gray-800/50 bg-gray-900/50 p-4 text-center opacity-50">
            <span class="fas fa-pen-square mb-3 h-10 w-10 text-gray-400 sm:h-14 sm:w-14"></span>
            <span class="text-sm font-medium text-gray-400 sm:text-base">6 Available Jobs</span>
          </div>
          {{-- Pending Reviews --}}
          <a href="{{ route('dashboard.reviews', ['dashboardType' => $dashboardType]) }}"
            class="group flex flex-col items-center rounded-lg border border-gray-800 bg-gray-900 p-4 text-center transition duration-150 ease-in-out hover:bg-gray-800">
            <span
              class="fas fa-star mb-3 h-10 w-10 transform text-yns_yellow transition-transform group-hover:scale-110 sm:h-14 sm:w-14"></span>
            <span class="text-sm font-medium sm:text-base">{{ $pendingReviews }} Pending
              Review{{ $pendingReviews > 1 ? 's' : '' }}</span>
          </a>

          {{-- Todo Items --}}
          <a href="{{ route('admin.dashboard.todo-list', ['dashboardType' => $dashboardType]) }}"
            class="group flex flex-col items-center rounded-lg border border-gray-800 bg-gray-900 p-4 text-center transition duration-150 ease-in-out hover:bg-gray-800">
            <span
              class="fas fa-list mb-3 h-10 w-10 transform text-yns_yellow transition-transform group-hover:scale-110 sm:h-14 sm:w-14"></span>
            <span class="text-sm font-medium sm:text-base">{{ $todoItemsCount }} Todo
              Item{{ $todoItemsCount > 1 ? 's' : '' }}</span>
          </a>
        </div>
      </div>
    </div>

    <div class="relative shadow-md sm:rounded-lg">
      <div
        class="min-w-screen-xl mx-auto max-w-screen-xl rounded-lg border border-gray-800 bg-black p-6 text-center text-white shadow-[0_4px_20px_-4px_rgba(0,0,0,0.9),inset_0_2px_0_rgba(255,255,255,0.1)] sm:p-8">
        <p class="mb-8 font-heading text-xl font-bold">Quick Links</p>
        <div class="grid grid-cols-2 gap-6 sm:grid-cols-3 sm:gap-8">
          {{-- New Job --}}
          <a href="{{ route('admin.dashboard.jobs.create', ['dashboardType' => $dashboardType]) }}"
            class="group flex flex-col items-center rounded-lg border border-gray-800 bg-gray-900 p-4 text-center transition duration-150 ease-in-out hover:bg-gray-800">
            <span
              class="fas fa-file-alt mb-3 h-10 w-10 transform text-yns_yellow transition-transform group-hover:scale-110 sm:h-14 sm:w-14"></span>
            <span class="text-sm font-medium sm:text-base">New Job</span>
          </a>

          {{-- New Note --}}
          <button id="new-note-button"
            class="group flex flex-col items-center rounded-lg border border-gray-800 bg-gray-900 p-4 text-center transition duration-150 ease-in-out hover:bg-gray-800">
            <span
              class="fas fa-sticky-note mb-3 h-10 w-10 transform text-yns_yellow transition-transform group-hover:scale-110 sm:h-14 sm:w-14"></span>
            <span class="text-sm font-medium sm:text-base">New Note</span>
          </button>

          {{-- New Todo Item --}}
          <a href="{{ route('admin.dashboard.todo-list', ['dashboardType' => $dashboardType]) }}"
            class="group flex flex-col items-center rounded-lg border border-gray-800 bg-gray-900 p-4 text-center transition duration-150 ease-in-out hover:bg-gray-800">
            <span
              class="fas fa-list mb-3 h-10 w-10 transform text-yns_yellow transition-transform group-hover:scale-110 sm:h-14 sm:w-14"></span>
            <span class="text-sm font-medium sm:text-base">New Todo Item</span>
          </a>

          {{-- Jobs --}}
          <a href="{{ route('admin.dashboard.jobs', ['dashboardType' => $dashboardType]) }}"
            class="group flex flex-col items-center rounded-lg border border-gray-800 bg-gray-900 p-4 text-center transition duration-150 ease-in-out hover:bg-gray-800">
            <span
              class="fas fa-file-alt mb-3 h-10 w-10 transform text-yns_yellow transition-transform group-hover:scale-110 sm:h-14 sm:w-14"></span>
            <span class="text-sm font-medium sm:text-base">Jobs</span>
          </a>

          {{-- Notes --}}
          <a href="{{ route('admin.dashboard.notes', ['dashboardType' => $dashboardType]) }}"
            class="group flex flex-col items-center rounded-lg border border-gray-800 bg-gray-900 p-4 text-center transition duration-150 ease-in-out hover:bg-gray-800">
            <span
              class="fas fa-sticky-note mb-3 h-10 w-10 transform text-yns_yellow transition-transform group-hover:scale-110 sm:h-14 sm:w-14"></span>
            <span class="text-sm font-medium sm:text-base">Notes</span>
          </a>

          {{-- Todo List --}}
          <a href="#"
            class="group flex flex-col items-center rounded-lg border border-gray-800 bg-gray-900 p-4 text-center transition duration-150 ease-in-out hover:bg-gray-800">
            <span
              class="fas fa-list mb-3 h-10 w-10 transform text-yns_yellow transition-transform group-hover:scale-110 sm:h-14 sm:w-14"></span>
            <span class="text-sm font-medium sm:text-base">Todo List</span>
          </a>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
