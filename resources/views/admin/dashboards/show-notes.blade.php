<x-app-layout :dashboardType="$dashboardType" :modules="$modules">
  <x-slot name="header">
    <x-sub-nav :userId="$userId" />
  </x-slot>

  <div class="mx-auto w-full max-w-screen-2xl py-16">
    <div class="mx-auto w-full max-w-screen-2xl px-4 sm:px-6 lg:px-8">
      <div class="overflow-hidden rounded-xl border border-gray-800 bg-gray-900/60 backdrop-blur-xl">
        <div class="divide-y divide-gray-800">
          <!-- Header -->
          <div class="flex items-center justify-between p-6">
            <h2 class="text-xl font-medium text-white">Notes</h2>
            <div class="flex items-center space-x-4">
              <div class="flex rounded-lg border border-gray-800 bg-gray-900">
                <button id="pending-notes-btn"
                  class="rounded-l-lg bg-gray-800 px-4 py-2 text-sm font-medium text-white transition duration-200 hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-yellow-500/70">
                  Pending
                </button>
                <button id="completed-notes-btn"
                  class="rounded-r-lg px-4 py-2 text-sm font-medium text-white transition duration-200 hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-yellow-500/70">
                  Completed
                </button>
              </div>
              <button id="new-note-btn"
                class="inline-flex items-center rounded-lg bg-yns_yellow px-4 py-2 text-sm font-medium text-gray-900 transition duration-200 hover:bg-yellow-400 focus:outline-none focus:ring-2 focus:ring-yellow-500/70">
                <span class="fas fa-plus mr-2"></span>
                New Note
              </button>
            </div>
          </div>

          <!-- Notes Grid -->
          <div class="p-6">
            <div id="notesGrid" class="grid gap-6 transition-opacity duration-200 sm:grid-cols-2 lg:grid-cols-3">
              <!-- Notes will be loaded here -->
            </div>

            <!-- Load More Button -->
            <div class="mt-8 flex justify-center">
              <button id="load-more-btn"
                class="inline-flex hidden items-center rounded-lg border border-gray-800 px-4 py-2 text-sm font-medium text-white transition duration-200 hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-yellow-500/70">
                <span class="fas fa-spinner mr-2"></span>
                Load More
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
<style>
  .modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 50;
  }

  .modal-content {
    width: 100%;
    max-width: 500px;
  }
</style>
<script>
  jQuery(document).ready(function() {
    // State management
    let currentPage = 1;
    const dashboardType = "{{ $dashboardType }}";

    // DOM Elements
    const elements = {
      grid: $('#notesGrid'),
      loadMoreBtn: $('#load-more-btn'),
      completedBtn: $('#completed-notes-btn'),
      pendingBtn: $('#pending-notes-btn')
    };

    // Initial load
    loadNotes(currentPage, false);

    // Event Handlers
    function setupEventListeners() {
      // Note action buttons
      $(document).on('click', '.complete-note-btn', handleNoteComplete);
      $(document).on('click', '.delete-note-btn', handleNoteDelete);
      $(document).on('click', '.uncomplete-note-btn', handleNoteUncomplete);

      // Filter buttons
      elements.completedBtn.on('click', () => loadFilteredNotes(true));
      elements.pendingBtn.on('click', () => loadFilteredNotes(false));

      // Load more
      elements.loadMoreBtn.on('click', handleLoadMore);

      document.addEventListener('noteCreated', function(event) {
        if (event.detail.dashboardType === dashboardType) {
          refreshNotes();
        }
      });
    }

    // Note Actions
    function handleNoteComplete() {
      const noteId = $(this).data('note-id');
      updateNoteStatus(noteId, 'complete');
    }

    function handleNoteDelete() {
      const noteId = $(this).data('note-id');
      deleteNote(noteId);
    }

    function handleNoteUncomplete() {
      const noteId = $(this).data('note-id');
      updateNoteStatus(noteId, 'uncomplete');
    }

    // API Calls
    function loadNotes(page, completed = false) {
      $.ajax({
        url: `/dashboard/${dashboardType}/notes`,
        type: 'GET',
        data: {
          page,
          completed
        },
        success: handleNotesResponse,
        error: handleError
      });
    }

    function updateNoteStatus(noteId, action) {
      const routes = {
        complete: '{{ route('admin.dashboard.complete-note', ['dashboardType' => '__dashboardType__', 'id' => 'NOTE_ID']) }}',
        uncomplete: '{{ route('admin.dashboard.uncomplete-note', ['dashboardType' => '__dashboardType__', 'id' => 'NOTE_ID']) }}'
      };

      $.ajax({
        url: routes[action]
          .replace('__dashboardType__', dashboardType)
          .replace('NOTE_ID', noteId),
        type: 'POST',
        data: {
          _token: '{{ csrf_token() }}'
        },
        success: (response) => {
          showSuccessNotification(response.message);
          refreshNotes();
        },
        error: handleError
      });
    }

    function deleteNote(noteId) {
      $.ajax({
        url: '{{ route('admin.dashboard.delete-note', ['dashboardType' => '__dashboardType__', 'id' => 'NOTE_ID']) }}'
          .replace('__dashboardType__', dashboardType)
          .replace('NOTE_ID', noteId),
        type: 'DELETE',
        data: {
          _token: '{{ csrf_token() }}'
        },
        success: (response) => {
          showSuccessNotification(response.message);
          refreshNotes();
        },
        error: handleError
      });
    }

    // Handlers
    function handleNotesResponse(response) {
      if (currentPage === 1) {
        elements.grid.empty();
      }
      elements.grid.append(response.view);
      updateUIState(response);
    }

    function handleError(xhr) {
      showFailureNotification(xhr.responseText || 'An error occurred');
    }

    // UI Updates
    function updateUIState(response) {
      elements.loadMoreBtn.toggle(response.hasMore);
      elements.completedBtn.toggle(!response.completed);
      elements.pendingBtn.toggle(response.completed);
    }

    function loadFilteredNotes(completed) {
      currentPage = 1;
      loadNotes(1, completed);
    }

    function refreshNotes() {
      currentPage = 1;
      loadNotes(currentPage, elements.completedBtn.is(':hidden'));
    }

    function handleLoadMore() {
      currentPage++;
      loadNotes(currentPage, elements.completedBtn.is(':hidden'));
    }

    // Initialize
    setupEventListeners();
  });
</script>
