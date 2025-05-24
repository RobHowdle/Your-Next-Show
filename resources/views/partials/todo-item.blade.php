<div class="todo-item mb-4 rounded-xl border border-gray-800 bg-yns_dark_blue/75 p-4 backdrop-blur-sm"
  data-id="{{ $todo->id }}">
  <div class="flex items-center justify-between">
    <div class="flex items-center space-x-4">
      <input type="checkbox"
        class="toggle-todo form-checkbox h-5 w-5 rounded border-gray-600 bg-gray-700 text-yns_yellow focus:ring-yns_yellow"
        {{ $todo->completed ? 'checked' : '' }} data-id="{{ $todo->id }}">
      <span class="{{ $todo->completed ? 'line-through text-gray-400' : '' }} text-gray-200">
        {{ $todo->item }}
      </span>
    </div>
    <div class="flex items-center space-x-3">
      @if ($todo->due_date)
        <span class="text-sm text-gray-400">
          <i class="fas fa-calendar-alt mr-1"></i>
          @php
            $dueDate =
                $todo->due_date instanceof \Carbon\Carbon
                    ? $todo->due_date
                    : \Illuminate\Support\Carbon::parse($todo->due_date);
          @endphp
          {{ $dueDate->format('M d, Y') }}
        </span>
      @endif
      <button class="delete-todo text-red-400 transition-colors duration-150 hover:text-red-600"
        data-id="{{ $todo->id }}">
        <i class="fas fa-trash"></i>
      </button>
    </div>
  </div>
</div>
