<x-app-layout :dashboardType="$dashboardType" :modules="$modules">
  <x-slot name="header">
    <x-sub-nav :userId="$userId" />
  </x-slot>
  <div class="mx-auto w-full max-w-screen-2xl py-16">
    <div class="mx-auto w-full max-w-screen-2xl px-4 sm:px-6 lg:px-8">
      <div class="overflow-hidden rounded-xl border border-gray-800 bg-gray-900/60 backdrop-blur-xl">
        <!-- Header Section -->
        <div class="border-b border-gray-800 px-6 py-8">
          <div class="flex flex-col gap-6 sm:flex-row sm:items-start sm:justify-between">
            <h1 class="font-heading text-3xl font-bold text-white sm:text-4xl">Todo List</h1>
            <div class="flex flex-wrap gap-3">
              <button id="completed-task-btn"
                class="{{ $hasCompleted ? '' : 'hidden' }} inline-flex items-center rounded-lg bg-gray-800 px-4 py-2 text-sm font-semibold text-white ring-1 ring-gray-700 transition hover:bg-gray-700 hover:ring-yns_yellow">
                <span class="fas fa-check-circle mr-2"></span>
                View Completed
              </button>
              <button id="uncomplete-task-btn"
                class="inline-flex hidden items-center rounded-lg bg-gray-800 px-4 py-2 text-sm font-semibold text-white ring-1 ring-gray-700 transition hover:bg-gray-700 hover:ring-yns_yellow">
                <span class="fas fa-list mr-2"></span>
                View Active
              </button>
            </div>
          </div>
        </div>

        <div class="space-y-6 p-6">
          <!-- Add Todo Form -->
          <form id="newTodoItem" class="rounded-lg border border-gray-700 bg-gray-800/50 p-6">
            @csrf
            <div class="space-y-4">
              <div>
                <label for="taskInput" class="block text-sm font-medium text-gray-400">New Todo Item</label>
                <textarea id="taskInput" name="task" rows="3"
                  class="mt-2 block w-full rounded-lg border border-gray-700 bg-gray-800 px-4 py-2 text-white placeholder-gray-500 shadow-sm focus:border-yns_yellow focus:ring-yns_yellow"
                  placeholder="What needs to be done?"></textarea>
              </div>
              <div>
                <label for="dueDate" class="block text-sm font-medium text-gray-400">Due Date (Optional)</label>
                <input type="date" id="dueDate" name="due_date"
                  class="mt-2 block w-full rounded-lg border border-gray-700 bg-gray-800 px-4 py-2 text-white shadow-sm focus:border-yns_yellow focus:ring-yns_yellow">
              </div>
              <button type="submit" id="addTaskButton"
                class="inline-flex items-center rounded-lg bg-yns_yellow px-4 py-2 text-sm font-semibold text-gray-900 transition hover:bg-yns_dark_orange hover:text-white">
                <span class="fas fa-plus-circle mr-2"></span>
                Add Item
              </button>
            </div>
          </form>

          <!-- Tasks Grid -->
          <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3" id="tasks">
            <div class="todo-container space-y-4">
              @forelse($todoItems as $todo)
                @include('partials.todo-item', ['todo' => $todo])
              @empty
                <p class="text-gray-400">No todos found</p>
              @endforelse
            </div>

            @if ($todoItems->hasMorePages())
              <div class="load-more mt-4">
                <button class="rounded bg-blue-500 px-4 py-2 text-white">
                  Load More
                </button>
              </div>
            @endif
          </div>

          <!-- Load More Button -->
          <div class="mt-8 flex justify-center">
            <button id="load-more-btn"
              class="{{ $hasMorePages ? '' : 'hidden' }} inline-flex items-center rounded-lg bg-gray-800 px-4 py-2 text-sm font-semibold text-white ring-1 ring-gray-700 transition hover:bg-gray-700 hover:ring-yns_yellow">
              <span class="fas fa-spinner mr-2"></span>
              Load More
            </button>
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

        function updateButtonVisibility() {
          const hasItems = $('#tasks').find('.todo-item').length > 0;
          const hasCompletedItems = $('.todo-item[data-completed="true"]').length > 0;

          // Update completed tasks button visibility
          $('#completed-task-btn').toggleClass('hidden', !hasCompletedItems);

          // If no items left, show empty state
          if (!hasItems) {
            $('#tasks').html(`
                <div class="col-span-full rounded-lg border border-gray-700 bg-gray-800/50 p-12 text-center">
                    <span class="fas fa-clipboard-list mb-4 text-4xl text-gray-600"></span>
                    <h3 class="mt-2 text-sm font-medium text-white">No ${isViewingCompleted ? 'completed' : 'active'} tasks</h3>
                    <p class="mt-1 text-sm text-gray-400">${isViewingCompleted ? 'Completed tasks will appear here.' : 'Get started by adding a new task.'}</p>
                </div>
            `);
          }
        }

        // Function to fetch todo items
        function fetchTodoItems(page = 1, completed = false) {
          return $.ajax({
            url: `/dashboard/${dashboardType}/todo-list/load-more`,
            method: 'GET',
            data: {
              page,
              completed,
              per_page: 6 // Set items per page
            },
            beforeSend: function() {
              $('#tasks').addClass('opacity-50');
            }
          }).always(function() {
            $('#tasks').removeClass('opacity-50');
          });
        }

        // Function to initialize todo list
        function initializeTodoList() {
          fetchTodoItems(1, false).then(function(response) {
            if (response.html) {
              $('#tasks').html(response.html);

              // Update load more button visibility based on total items
              const totalPages = Math.ceil(response.totalItems / response.itemsPerPage);
              $('#load-more-btn').toggleClass('hidden', !response.hasMorePages);

              // Store total items for reference
              window.todoItemsTotal = response.totalItems;

              updateButtonVisibility();
            } else {
              updateButtonVisibility(); // Show empty state
            }
          }).catch(function(error) {
            console.error('Failed to load tasks:', error);
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: 'Failed to load tasks. Please refresh the page.',
              confirmButtonColor: '#FFB800'
            });
          });
        }

        // Call initialize function on page load
        initializeTodoList();

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
              // Clear form inputs
              $('#taskInput').val('');
              $('#dueDate').val('');

              // If we're viewing completed tasks, don't show the new task
              if (isViewingCompleted) return;

              // Refresh the todo list
              initializeTodoList();
            },
            error: function(xhr) {
              Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to add task. Please try again.',
                confirmButtonColor: '#FFB800'
              });
            },
            complete: function() {
              hideLoading(button);
              currentPage = 1;
            }
          });
        });

        // Load more functionality
        $('#load-more-btn').on('click', function() {
          if (isLoading) return;

          const button = $(this);
          showLoading(button);
          isLoading = true;

          fetchTodoItems(++currentPage, isViewingCompleted).then(function(response) {
            $('#tasks').append(response.html);

            // Hide load more button if no more pages
            if (!response.hasMorePages) {
              button.addClass('hidden');
            }

            updateButtonVisibility();
          }).catch(function(error) {
            console.error('Error loading more tasks:', error);
            --currentPage; // Revert page increment on error
          }).always(function() {
            isLoading = false;
            hideLoading(button);
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
