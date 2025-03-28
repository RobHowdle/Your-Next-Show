@foreach ($todoItems as $item)
  <div class="todo-item" data-id="{{ $item->id }}">
    <div class="h-full rounded-lg border border-white/10 bg-white/5 p-6">
      <div class="flex h-full flex-col justify-between">
        <div>
          <p class="mb-4 text-lg">{{ $item->item }}</p>
          @if ($item->due_date)
            <p class="mb-2 text-sm text-white/70">
              Due: {{ \Carbon\Carbon::parse($item->due_date)->format('j M Y') }}
            </p>
          @endif
          <p class="text-sm text-white/70">
            Created: {{ $item->created_at->format('j M Y') }}
          </p>
        </div>
        <div class="mt-4 flex justify-between gap-2">
          <button
            class="delete-task-btn flex-1 rounded-lg border border-white/10 bg-white/5 px-4 py-2 text-white transition hover:border-yns_red hover:text-yns_red"
            data-task-id="{{ $item->id }}">
            <span class="fas fa-trash-alt mr-2"></span>Delete
          </button>
          @if (!$item->completed)
            <button
              class="complete-task-btn flex-1 rounded-lg border border-white/10 bg-white/5 px-4 py-2 text-white transition hover:border-yns_yellow hover:text-yns_yellow"
              data-task-id="{{ $item->id }}">
              <span class="fas fa-check mr-2"></span>Complete
            </button>
          @endif
        </div>
      </div>
    </div>
  </div>
@endforeach
