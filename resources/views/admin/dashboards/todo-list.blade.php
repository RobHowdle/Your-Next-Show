<x-app-layout :dashboardType="$dashboardType" :modules="$modules">
  <x-slot name="header">
    <x-sub-nav :userId="$userId" />
  </x-slot>

  <div class="mx-auto w-full max-w-screen-2xl py-16">
    <div class="relative mb-8 shadow-md sm:rounded-lg">
      <div class="min-w-screen-xl mx-auto max-w-screen-xl rounded-lg bg-yns_dark_gray text-white">
        <div class="rounded-lg border border-white px-8 py-4">
          <p class="mb-4 font-heading text-4xl font-bold">Todo List</p>
          <form id="newTodoItem" method="POST" class="border-b border-b-white pb-4">
            @csrf
            <div class="flex flex-col items-start gap-4">
              <div class="group w-full">
                <x-input-label-dark>What do you need to add to your todo list?</x-input-label-dark>
                <x-textarea-input class="mt-2 h-32" id="taskInput" name="task"></x-textarea-input>
              </div>
              <x-white-button type="submit" id="addTaskButton">Add Item</x-white-button>

            </div>
          </form>
          <div class="grid grid-cols-3 gap-x-4 gap-y-6 pt-6" id="tasks">
            @if ($todoItems->isEmpty())
              <p>No todo items found.</p>
            @else
              @include('components.todo-items', ['todoItems' => $todoItems])
            @endif
          </div>
          <div class="mt-6 flex flex-row gap-4">
            <x-white-button id="load-more-btn">Load More</x-white-button>
            <x-white-button id="completed-task-btn">View Completed</x-white-button>
            <x-white-button id="uncomplete-task-btn">View Uncompleted</x-white-button>
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
    let isViewingCompleted = false;

    // Initial button states
    checkButtonVisibility();

    // Task Loading Functions
    function loadTasks(page) {
      if (page > 1) {
        jQuery.ajax({
          url: `/dashboard/${dashboardType}/todo-list/load-more`,
          method: 'GET',
          data: {
            page: page
          },
          success: function(response) {
            jQuery('#tasks').append(response.html);
            jQuery('#load-more-btn').toggle(response.hasMorePages);
          },
          error: function() {
            showFailureNotification('Failed to load tasks');
          }
        });
      }
    }

    function checkButtonVisibility() {
      Promise.all([
        // Get completed status
        jQuery.ajax({
          url: `/dashboard/${dashboardType}/todo-list/has-completed`,
          method: 'GET'
        }),
        // Get uncompleted status
        jQuery.ajax({
          url: `/dashboard/${dashboardType}/todo-list/has-uncompleted`,
          method: 'GET'
        })
      ]).then(([completedResponse, uncompletedResponse]) => {
        // Show completed button if viewing uncompleted and has completed items
        jQuery('#completed-task-btn').toggle(
          !isViewingCompleted && completedResponse.hasCompleted
        );

        // Show uncompleted button if viewing completed and has uncompleted items
        jQuery('#uncomplete-task-btn').toggle(
          isViewingCompleted && uncompletedResponse.hasUncompleted
        );
      });
    }

    // Click handler for completed items
    jQuery('#completed-task-btn').on('click', function() {
      jQuery.ajax({
        url: `/dashboard/${dashboardType}/todo-list/completed`,
        method: 'GET',
        success: function(response) {
          isViewingCompleted = true;
          jQuery('#tasks').html(response.html);
          checkButtonVisibility();
        },
        error: function() {
          showFailureNotification('Failed to load completed tasks');
        }
      });
    });

    // Click handler for uncompleted items
    jQuery('#uncomplete-task-btn').on('click', function() {
      jQuery.ajax({
        url: `/dashboard/${dashboardType}/todo-list/uncompleted`,
        method: 'GET',
        success: function(response) {
          isViewingCompleted = false;
          jQuery('#tasks').html(response.html);
          checkButtonVisibility();
        },
        error: function() {
          showFailureNotification('Failed to load uncompleted tasks');
        }
      });
    });

    // Initial check
    checkButtonVisibility();

    // Update task completion handler
    function handleTaskCompletion(taskId, taskElement) {
      jQuery.ajax({
        url: `/dashboard/${dashboardType}/todo-list/${taskId}/complete`,
        method: 'POST',
        data: {
          _token: '{{ csrf_token() }}'
        },
        success: function(response) {
          taskElement.fadeOut(300, function() {
            jQuery(this).remove();

            // Check if this was the last item
            if (jQuery('#tasks').children().length === 0) {
              jQuery('#tasks').html('<p>No todo items found.</p>');
            }

            showSuccessNotification(response.message);
            checkButtonVisibility();
          });
        },
        error: function() {
          showFailureNotification('Failed to complete task');
        }
      });
    }

    function handleTaskDeletion(taskId, taskElement) {
      jQuery.ajax({
        url: `/dashboard/${dashboardType}/todo-list/${taskId}`,
        method: 'DELETE',
        data: {
          _token: '{{ csrf_token() }}'
        },
        success: function(response) {
          taskElement.fadeOut(300, function() {
            jQuery(this).remove();

            // Check if this was the last item
            if (jQuery('#tasks').children().length === 0) {
              jQuery('#tasks').html('<p>No todo items found.</p>');
            }

            showSuccessNotification(response.message);
            checkButtonVisibility();
          });
        },
        error: function() {
          showFailureNotification('Failed to delete task');
        }
      });
    }

    // Form Submission Handler
    jQuery('#newTodoItem').on('submit', function(e) {
      e.preventDefault();
      const formData = new FormData(this);

      jQuery.ajax({
        url: `/dashboard/${dashboardType}/todo-list/new`,
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        success: function(response) {
          // Clear form
          jQuery('#taskInput').val('');

          // Remove "No todo items found" if present
          if (jQuery('#tasks p').text() === 'No todo items found.') {
            jQuery('#tasks').empty();
          }

          // Format date to match existing items
          const date = new Date();
          const currentDate = date.toLocaleDateString('en-GB', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
          }).replace(/\//g, '-');

          // Add new todo to list
          const newTodo = `
                <div class="min-h-52 mx-auto w-full max-w-xs rounded-lg bg-yns_dark_blue text-white">
                    <div class="flex h-full flex-col justify-between rounded-lg border border-yns_red px-4 py-4">
                        <p class="mb-4">Todo: ${response.todoItem.item}</p>
                        <p class="mb-2">Created On: ${currentDate}</p>
                        <div class="mt-1 flex flex-row justify-between">
                            <button data-task-id="${response.todoItem.id}" id="delete-task-btn"
                                class="delete-task-btn rounded-lg border border-white bg-yns_dark_gray px-4 py-2 text-white transition duration-150 ease-in-out hover:border-yns_red hover:text-yns_red">
                                Delete
                            </button>
                            <button data-task-id="${response.todoItem.id}" id="complete-task-btn"
                                class="complete-task-btn rounded-lg border border-white bg-yns_dark_gray px-4 py-2 text-white transition duration-150 ease-in-out hover:border-yns_yellow hover:text-yns_yellow">
                                Complete
                            </button>
                        </div>
                    </div>
                </div>
            `;
          jQuery('#tasks').prepend(newTodo);

          // Show notification
          showSuccessNotification(response.message);

          // Update buttons
          checkButtonVisibility();
        },
        error: function(xhr) {
          showFailureNotification(xhr.responseJSON?.message || 'Failed to add task');
        }
      });
    });

    // Load More Handler
    jQuery('#load-more-btn').on('click', function() {
      currentPage++;
      loadTasks(currentPage);
    });

    // Task Completion Click Handler
    jQuery(document).on('click', '.complete-task-btn', function() {
      const taskId = jQuery(this).data('task-id');
      const todoItem = jQuery(this).closest('.min-h-52');

      handleTaskCompletion(taskId, todoItem);
    });

    jQuery(document).on('click', '.delete-task-btn', function() {
      const taskId = jQuery(this).data('task-id');
      const todoItem = jQuery(this).closest('.min-h-52');
      handleTaskDeletion(taskId, todoItem);
    });

    // Initial Load
    loadTasks(currentPage);
  });
</script>
