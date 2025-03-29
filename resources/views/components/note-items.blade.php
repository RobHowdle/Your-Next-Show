@forelse($notes as $note)
  <div class="note-item rounded-lg border border-gray-700 bg-gray-800 p-4">
    <div class="mb-2 flex items-center justify-between">
      <h3 class="text-lg font-medium text-white">{{ $note->name }}</h3>
      <div class="flex space-x-2">
        @if (!$note->completed)
          <button class="complete-note-btn text-green-400 hover:text-green-300" data-note-id="{{ $note->id }}">
            <i class="fas fa-check"></i>
          </button>
        @else
          <button class="uncomplete-note-btn text-yellow-400 hover:text-yellow-300" data-note-id="{{ $note->id }}">
            <i class="fas fa-undo"></i>
          </button>
        @endif
        <button class="delete-note-btn text-red-400 hover:text-red-300" data-note-id="{{ $note->id }}">
          <i class="fas fa-trash"></i>
        </button>
      </div>
    </div>
    <p class="text-gray-300">{{ $note->text }}</p>
    <div class="mt-2 text-sm text-gray-400">
      {{ $note->date ? $note->date->format('M j, Y g:i A') : 'No date set' }}
    </div>
    @if ($note->is_todo)
      <div class="mt-2">
        <span
          class="inline-flex items-center rounded-full bg-yellow-400/10 px-2 py-1 text-xs font-medium text-yellow-400">
          <i class="fas fa-tasks mr-1"></i> Todo
        </span>
      </div>
    @endif
  </div>
@empty
  <div class="col-span-full py-8 text-center text-gray-400">
    No notes found
  </div>
@endforelse
