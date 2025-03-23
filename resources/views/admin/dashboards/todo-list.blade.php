<x-app-layout :dashboardType="$dashboardType" :modules="$modules">
  <div class="mx-auto w-full max-w-screen-2xl py-16">
    <div class="relative mb-8 shadow-md sm:rounded-lg">
      <div class="min-w-screen-xl mx-auto max-w-screen-xl rounded-lg bg-yns_dark_gray text-white">
        <div class="rounded-lg border border-white px-8 py-4">
          <div class="mb-6 flex items-center justify-between">
            <p class="font-heading text-4xl font-bold">Todo List</p>
            <div class="flex gap-4">
              <x-white-button id="completed-task-btn" class="{{ $hasCompleted ? '' : 'hidden' }}">
                <span class="fas fa-check-circle mr-2"></span>View Completed
              </x-white-button>
              <x-white-button id="uncomplete-task-btn" class="hidden">
                <span class="fas fa-list mr-2"></span>View Active
              </x-white-button>
            </div>
          </div>

          <!-- Add Todo Form -->
          <form id="newTodoItem" class="mb-8 rounded-lg border border-white/10 bg-white/5 p-6">
            @csrf
            <div class="flex flex-col items-start gap-4">
              <div class="group w-full">
                <x-input-label-dark>New Todo Item</x-input-label-dark>
                <x-textarea-input class="mt-2 h-24" id="taskInput" name="task"
                  placeholder="What needs to be done?"></x-textarea-input>
              </div>
              <div class="w-full">
                <x-input-label-dark>Due Date (Optional)</x-input-label-dark>
                <x-text-input type="date" class="mt-2" id="dueDate" name="due_date"></x-text-input>
              </div>
              <x-primary-button type="submit" id="addTaskButton">
                <span class="fas fa-plus-circle mr-2"></span>Add Item
              </x-primary-button>
            </div>
          </form>

          <!-- Tasks Grid -->
          <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3" id="tasks">
            <x-todo-items :todoItems="$todoItems" />
          </div>

          <!-- Load More Button -->
          <div class="mt-8 flex justify-center">
            <x-white-button id="load-more-btn" class="{{ $hasMorePages ? '' : 'hidden' }}">
              <span class="fas fa-spinner mr-2"></span>Load More
            </x-white-button>
          </div>
        </div>
      </div>
    </div>
  </div>

  @push('scripts')
    <script>
      jQuery(document).ready(function() {
        let currentPage = 1;
        let dashboardType = "{{ $dashboardType }}";
        let isViewingCompleted = false;
        let isLoading = false;

        // Initialize loading spinner
        const spinner = '<span class="fas fa-circle-notch fa-spin"></span>';

        function showLoading(button) {
          button.prop('disabled', true)
            .data('original-content', button.html())
            .html(`${spinner} Loading...`);
        }

        function hideLoading(button) {
          button.prop('disabled', false)
            .html(button.data('original-content'));
        }

        // Form submission
        $('#newTodoItem').on('submit', function(e) {
          e.preventDefault();
          const button = $('#addTaskButton');
          showLoading(button);

          $.ajax({
            url: `/dashboard/${dashboardType}/todo-list/new`,
            method: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false,
            success: function(response) {
              $('#taskInput').val('');
              $('#dueDate').val('');
              if ($('#tasks').find('.todo-item').length === 0) {
                $('#tasks').empty();
              }
              $('#tasks').prepend(response.html);
              updateButtonVisibility();
            },
            complete: function() {
              hideLoading(button);
            }
          });
        });

        // Load more functionality
        $('#load-more-btn').on('click', function() {
          if (isLoading) return;

          const button = $(this);
          showLoading(button);
          isLoading = true;

          $.ajax({
            url: `/dashboard/${dashboardType}/todo-list/load-more`,
            data: {
              page: ++currentPage,
              completed: isViewingCompleted
            },
            success: function(response) {
              $('#tasks').append(response.html);
              if (!response.hasMorePages) {
                button.addClass('hidden');
              }
            },
            complete: function() {
              isLoading = false;
              hideLoading(button);
            }
          });
        });

        // Handle complete/delete buttons
        $(document).on('click', '.complete-task-btn', function() {
          const id = $(this).data('task-id');
          const item = $(this).closest('.todo-item');

          $.ajax({
            url: `/dashboard/${dashboardType}/todo-list/${id}/complete`,
            method: 'POST',
            data: {
              _token: '{{ csrf_token() }}'
            },
            success: function() {
              item.fadeOut(() => item.remove());
              updateButtonVisibility();
            }
          });
        });

        $(document).on('click', '.delete-task-btn', function() {
          const id = $(this).data('task-id');
          const item = $(this).closest('.todo-item');

          $.ajax({
            url: `/dashboard/${dashboardType}/todo-list/${id}`,
            method: 'DELETE',
            data: {
              _token: '{{ csrf_token() }}'
            },
            success: function() {
              item.fadeOut(() => item.remove());
              updateButtonVisibility();
            }
          });
        });

        // Toggle completed/uncompleted views
        $('#completed-task-btn, #uncomplete-task-btn').on('click', function() {
          isViewingCompleted = !isViewingCompleted;
          currentPage = 1;

          $.ajax({
            url: `/dashboard/${dashboardType}/todo-list/load-more`,
            data: {
              completed: isViewingCompleted
            },
            success: function(response) {
              $('#tasks').html(response.html);
              $('#completed-task-btn').toggleClass('hidden');
              $('#uncomplete-task-btn').toggleClass('hidden');
              $('#load-more-btn').toggleClass('hidden', !response.hasMorePages);
            }
          });
        });
      });
    </script>
  @endpush
</x-app-layout>
