<x-app-layout :dashboardType="$dashboardType" :modules="$modules">
  <x-slot name="header">
    <x-sub-nav :userId="$userId" />
  </x-slot>

  <div class="mx-auto w-full max-w-screen-2xl py-16">
    <div class="relative mb-8 shadow-md sm:rounded-lg">
      <div class="min-w-screen-xl mx-auto max-w-screen-xl rounded-lg bg-yns_dark_gray text-white">
        <div class="rounded-lg border border-white px-8 py-4">
          <p class="mb-4 font-heading text-4xl font-bold">Todo List</p>
          <form id="newTodoItem" method="POST">
            @csrf
            <div class="flex flex-row items-center gap-8 border-b border-b-white pb-4">
              <div class="group">
                <x-input-label-dark>Item</x-input-label-dark>
                <x-textarea-input class="mt-2 h-32 w-96" id="taskInput" name="task"></x-textarea-input>
              </div>
              <button type="submit" id="addTaskButton"
                class="mt-8 h-10 w-40 rounded-lg border border-white bg-white px-4 py-2 font-heading font-bold text-black transition duration-150 ease-in-out hover:border-yns_yellow hover:text-yns_yellow">Add</button>
            </div>
          </form>
          <div class="grid grid-cols-3 gap-x-4 gap-y-6 pt-6" id="tasks">
            @if (!$todoItems)
              <p>No todo items found.</p>
            @else
              @include('components.todo-items', ['todoItems' => $todoItems])
            @endif
          </div>
          <div class="mt-6 flex flex-row gap-4">
            <button id="load-more-btn"
              class="h-10 w-40 rounded-lg border border-white bg-white px-4 py-2 font-heading font-bold text-black transition duration-150 ease-in-out hover:border-yns_yellow hover:text-yns_yellow">Load
              More</button>
            <button id="completed-task-btn"
              class="h-10 w-40 rounded-lg border border-white bg-white px-4 py-2 font-heading font-bold text-black transition duration-150 ease-in-out hover:border-yns_yellow hover:text-yns_yellow">View
              Completed</button>
            <button id="uncomplete-task-btn" style="display: none;"
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

    loadTasks(currentPage);

    jQuery('#load-more-btn').on('click', function() {
      currentPage++;
      loadTasks(currentPage);
    });

    jQuery(document).on('click', '.complete-task-btn', function() {
      const taskId = jQuery(this).data('task-id');
      const taskElement = jQuery(this).closest('.todo-item');
      completeTask(taskId);

      jQuery('#tasks').empty();
      currentPage = 1;
    });

    jQuery(document).on('click', '.delete-task-btn', function() {
      const taskId = jQuery(this).data('task-id');
      const taskElement = jQuery(this).closest('.todo-item');
      deleteTask(taskId);

      jQuery('#tasks').empty();
      currentPage = 1;
    });

    jQuery(document).on('click', '.uncomplete-task-btn', function() {
      const taskId = jQuery(this).data('task-id');
      const taskElement = jQuery(this).closest('.todo-item');
      uncompleteTask(taskId);

      jQuery('#tasks').empty();
      currentPage = 1;
    });

    // Function to load tasks dynamically
    function loadTasks(page) {
      $.ajax({
        url: '{{ route('admin.dashboard.todo-items', ['dashboardType' => '__dashboardType__']) }}'.replace(
          '__dashboardType__', dashboardType),
        type: 'GET',
        data: {
          page: page
        },
        success: function(response) {
          jQuery('#tasks').append(response.view);
          jQuery('#completed-task-btn').show();
          jQuery('#uncomplete-task-btn').hide();
          jQuery('#load-more-btn').show();

          if (!response.hasMore) {
            jQuery('#load-more-btn').hide();
          }
        },
        error: function(xhr) {
          console.log('Error: ', xhr.responseText);
        }
      });
    }

    // Handle new task submission
    jQuery('#newTodoItem').on('submit', function(e) {
      e.preventDefault();
      let task = jQuery('#taskInput').val();

      $.ajax({
        url: '{{ route('admin.dashboard.new-todo-item', ['dashboardType' => '__dashboardType__']) }}'
          .replace(
            '__dashboardType__', dashboardType),
        type: 'POST',
        data: {
          _token: '{{ csrf_token() }}',
          task: task
        },
        success: function(response) {
          jQuery('#taskInput').val('');
          jQuery('#tasks').empty();
          currentPage = 1;
          loadTasks(currentPage);
          showSuccessNotification(response.message);
        },
        error: function(xhr) {
          console.log('Error: ', xhr.responseText);
        }
      });
    });

    // Function to complete a task
    function completeTask(taskId) {
      $.ajax({
        url: '{{ route('admin.dashboard.complete-todo-item', ['dashboardType' => '__dashboardType__', 'id' => 'TASK_ID']) }}'
          .replace('__dashboardType__', dashboardType)
          .replace('TASK_ID', taskId),
        type: 'POST',
        data: {
          _token: '{{ csrf_token() }}'
        },
        success: function(response) {
          showSuccessNotification(response.message);
          setTimeout(() => {
            loadTasks(currentPage);
          }, 500);
        },
        error: function(xhr) {
          showFailureNotification(xhr.responseText);
        }
      });
    }

    // Show Completed Tasks
    jQuery('#completed-task-btn').on('click', function() {
      $.ajax({
        url: '{{ route('admin.dashboard.completed-todo-items', ['dashboardType' => '__dashboardType__']) }}'
          .replace('__dashboardType__', dashboardType),
        type: 'GET',
        success: function(response) {
          jQuery('#tasks').empty();
          jQuery('#tasks').append(response.view);
          jQuery('#load-more-btn').hide(); // Optionally hide Load More button
          jQuery('#uncomplete-task-btn').show(); // Show uncompleted button
          jQuery('#completed-task-btn').hide(); // Show uncompleted button
        },
        error: function(xhr) {
          console.log('Error: ', xhr.responseText); // Handle error response
        }
      });
    });

    // Function to delete a task
    function deleteTask(taskId) {
      $.ajax({
        url: '{{ route('admin.dashboard.delete-todo-item', ['dashboardType' => '__dashboardType__', 'id' => 'TASK_ID']) }}'
          .replace('__dashboardType__', dashboardType)
          .replace('TASK_ID', taskId),
        type: 'DELETE',
        data: {
          _token: '{{ csrf_token() }}'
        },
        success: function(response) {
          showSuccessNotification(response.message);
          setTimeout(() => {
            loadTasks(currentPage);
          }, 500);
        },
        error: function(xhr) {
          showFailureNotification(xhr.responseText);
        }
      });
    }

    // Function to uncomplete a task
    function uncompleteTask(taskId) {
      $.ajax({
        url: '{{ route('admin.dashboard.uncomplete-todo-item', ['dashboardType' => '__dashboardType__', 'id' => 'TASK_ID']) }}'
          .replace('__dashboardType__', dashboardType)
          .replace('TASK_ID', taskId),
        type: 'POST',
        data: {
          _token: '{{ csrf_token() }}'
        },
        success: function(response) {
          showSuccessNotification(response.message);
          setTimeout(() => {
            loadTasks(currentPage);
          }, 500);
        },
        error: function(xhr) {
          showFailureNotification(xhr.responseText);
        }
      });
    }

    // Function to load completed tasks
    function loadCompletedTasks() {
      $.ajax({
        url: '{{ route('admin.dashboard.completed-todo-items', ['dashboardType' => '__dashboardType__']) }}'
          .replace('__dashboardType__', dashboardType),
        type: 'GET',
        success: function(response) {
          jQuery('#tasks').append(response.view);
          jQuery('#complete-task-btn').hide(); // Hide completed button
          jQuery('#uncomplete-task-btn').show(); // Show uncompleted button
        },
        error: function(xhr) {
          console.log('Error: ', xhr.responseText);
        }
      });
    }
  });
</script>
