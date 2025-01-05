<x-app-layout :dashboardType="$dashboardType" :modules="$modules">
  <x-slot name="header">
    <x-sub-nav :userId="$userId" />
  </x-slot>

  <div class="mx-auto w-full max-w-screen-2xl py-16">
    <div class="relative mb-8 shadow-md sm:rounded-lg">
      <div class="min-w-screen-xl mx-auto max-w-screen-xl rounded-lg bg-yns_dark_gray text-white">
        <div class="rounded-lg border border-white px-8 py-4">
          <p class="mb-4 font-heading text-4xl font-bold">Notes</p>
          <form id="newNote" method="POST">
            @csrf
            <div class="border-b border-b-white pb-4">
              <div class="grid grid-cols-2 gap-x-12">
                <div class="col">
                  <div class="group mb-4">
                    <x-input-label-dark>Note Name</x-input-label-dark>
                    <x-text-input id="noteInput" name="note"></x-text-input>
                  </div>
                </div>
                <div class="col">
                  <div class="group mb-4">
                    <x-input-label-dark>Date</x-input-label-dark>
                    <x-date-time-input id="dateInput" name="date"></x-date-time-input>
                  </div>
                </div>

                <div class="col-span-2">
                  <div class="group mb-4">
                    <x-input-label-dark>Text</x-input-label-dark>
                    <x-textarea-input id="textInput" name="text" class="w-full"></x-textarea-input>
                  </div>
                </div>

                <div class="col-span-2 flex flex-row-reverse items-end justify-end gap-2">
                  <x-input-label-dark>Convert to Todo Item</x-input-label-dark>
                  <x-input-checkbox id="isTodoInput" name="isTodo"></x-input-checkbox>
                </div>
              </div>
              <button type="submit" id="newNoteButton"
                class="mt-4 h-10 w-40 rounded-lg border border-white bg-white px-4 py-2 font-heading font-bold text-black transition duration-150 ease-in-out hover:border-yns_yellow hover:text-yns_yellow">Add</button>
            </div>
          </form>
          <div class="grid grid-cols-3 gap-x-4 gap-y-6 pt-6" id="notes">
            @if (!$notes)
              <p>No notes found.</p>
            @else
              @include('components.note-items', ['notes' => $notes])
            @endif
          </div>
          <div class="mt-6 flex flex-row gap-4">
            <button id="load-more-btn"
              class="h-10 w-40 rounded-lg border border-white bg-white px-4 py-2 font-heading font-bold text-black transition duration-150 ease-in-out hover:border-yns_yellow hover:text-yns_yellow">Load
              More</button>
            <button id="completed-notes-btn"
              class="h-10 w-40 rounded-lg border border-white bg-white px-4 py-2 font-heading font-bold text-black transition duration-150 ease-in-out hover:border-yns_yellow hover:text-yns_yellow">View
              Completed</button>
            <button id="uncompleted-notes-btn" style="display: none;"
              class="w-50 h-10 rounded-lg border border-white bg-white px-4 py-2 font-heading font-bold text-black transition duration-150 ease-in-out hover:border-yns_yellow hover:text-yns_yellow">View
              Uncompleted</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
<script>
  jQuery(document).ready(function() {
    let currentPage = 1;
    let dashboardType = "{{ $dashboardType }}";

    // loadNotes(currentPage);

    jQuery('#load-more-btn').on('click', function() {
      currentPage++;
      loadNotes(currentPage);
    });

    jQuery(document).on('click', '.complete-note-btn', function() {
      const noteId = jQuery(this).data('note-id');
      const noteElement = jQuery(this).closest('.note-item')
      completeNote(noteId);

      jQuery('#notes').empty();
      currentPage = 1;
    });

    jQuery(document).on('click', '.delete-note-btn', function() {
      const noteId = jQuery(this).data('note-id');
      const noteElement = jQuery(this).closest('.note-item')
      deleteNote(noteId);

      jQuery('#notes').empty();
      currentPage = 1;
    });

    jQuery(document).on('click', '.uncomplete-note-btn', function() {
      const noteId = jQuery(this).data('note-id');
      const noteElement = jQuery(this).closest('.note-item')
      uncompleteNote(noteId);

      jQuery('#notes').empty();
      currentPage = 1;
    });

    // Function to load notes dynamically
    function loadNotes(page) {
      $.ajax({
        url: '{{ route('admin.dashboard.note-items', ['dashboardType' => '__dashboardType__']) }}'.replace(
          '__dashboardType__', dashboardType),
        type: 'GET',
        data: {
          page: page
        },
        success: function(response) {
          jQuery('#notes').empty();
          jQuery('#notes').append(response.view);
          jQuery('#completed-notes-btn').show();
          jQuery('#uncompleted-notes-btn').hide();
          jQuery('#load-more-btn').show();

          if (!response.hasMore) {
            jQuery('#load-more-btn').hide();
          }
        },
      });
    }

    // Handle new note submission
    jQuery('#newNote').on('submit', function(e) {
      e.preventDefault();

      // Get values from the input fields
      let noteName = jQuery('#noteInput').val();
      let noteText = jQuery('#textInput').val();
      let noteDate = jQuery('#dateInput').val();
      let isTodo = jQuery('#isTodoInput').is(':checked') ? 1 : 0;

      $.ajax({
        url: '{{ route('admin.dashboard.new-note-item', ['dashboardType' => '__dashboardType__']) }}'
          .replace(
            '__dashboardType__', dashboardType),
        type: 'POST',
        data: {
          _token: '{{ csrf_token() }}',
          name: noteName,
          text: noteText,
          date: noteDate,
          is_todo: isTodo
        },
        success: function(response) {
          jQuery('#noteInput').val('');
          jQuery('#textInput').val('');
          jQuery('#dateInput').val('');
          jQuery('#isTodoInput').prop('checked', false);
          currentPage = 1;
          loadNotes(currentPage);
          showSuccessNotification(response.message);
        },
      });
    });

    // Function to complete a note
    function completeNote(noteId) {
      $.ajax({
        url: '{{ route('admin.dashboard.complete-note', ['dashboardType' => '__dashboardType__', 'id' => 'NOTE_ID']) }}'
          .replace('__dashboardType__', dashboardType)
          .replace('NOTE_ID', noteId),
        type: 'POST',
        data: {
          _token: '{{ csrf_token() }}'
        },
        success: function(response) {
          showSuccessNotification(response.message);
          setInterval(() => {
            loadNotes(currentPage);
          }, 500);
        },
        error: function(xhr) {
          showFailureNotification(xhr.responseText);
        }
      });
    }

    jQuery('#completed-notes-btn').on('click', function() {
      currentPage = 1;
      loadCompletedNotes(currentPage);
    });

    // Function to delete a note
    function deleteNote(noteId) {
      $.ajax({
        url: '{{ route('admin.dashboard.delete-note', ['dashboardType' => '__dashboardType__', 'id' => 'NOTE_ID']) }}'
          .replace('__dashboardType__', dashboardType)
          .replace('NOTE_ID', noteId),
        type: 'DELETE',
        data: {
          _token: '{{ csrf_token() }}'
        },
        success: function(response) {
          showSuccessNotification(response.message);
          setInterval(() => {
            loadNotes(currentPage);
          }, 500);
        },
        error: function(xhr) {
          showFailureNotification(xhr.responseText);
        }
      });
    }

    // Function to uncomplete a task
    function uncompleteNote(noteId) {
      $.ajax({
        url: '{{ route('admin.dashboard.uncomplete-note-item', ['dashboardType' => '__dashboardType__', 'id' => 'TASK_ID']) }}'
          .replace('__dashboardType__', dashboardType)
          .replace('NOTE_ID', noteId),
        type: 'POST',
        data: {
          _token: '{{ csrf_token() }}'
        },
        success: function(response) {
          showSuccessNotification(response.message);
          setTimeout(() => {
            loadNotes(currentPage);
          }, 500);
        },
        error: function(xhr) {
          showFailureNotification(xhr.responseText);
        }
      });
    }

    jQuery('#uncompleted-notes-btn').on('click', function() {
      currentPage = 1;
      loadNotes(currentPage);
    });

    function loadCompletedNotes() {
      $.ajax({
        url: '{{ route('admin.dashboard.completed-notes', ['dashboardType' => '__dashboardType__']) }}'
          .replace('__dashboardType__', dashboardType),
        type: 'GET',
        success: function(response) {
          jQuery('#notes').empty();
          jQuery('#notes').append(response.view);
          jQuery('#completed-notes-btn').hide(); // Hide completed button
          jQuery('#uncompleted-notes-btn').show(); // Show uncompleted button
          jQuery('#load-more-btn').show();

          if (!response.hasMore) {
            jQuery('#load-more-btn').hide();
          }
        },
      });
    }
  });
</script>
