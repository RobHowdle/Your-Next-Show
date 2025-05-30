<div class="fixed bottom-4 right-4 z-50">
  {{-- Toggle Button --}}
  <button id="notes-toggle"
    class="flex h-14 w-14 items-center justify-center rounded-full bg-gray-800 text-white shadow-lg transition-all duration-300 hover:bg-yns_yellow focus:outline-none focus:ring-2 focus:ring-yellow-500/70">
    <i class="fas fa-sticky-note text-2xl"></i>
  </button>

  {{-- Notes Popup --}}
  <div id="notes-popup" class="fixed bottom-20 right-4 hidden w-80">
    <div class="rounded-lg border border-gray-700 bg-gray-900 p-4 shadow-lg">
      <div class="mb-4 flex items-center justify-between">
        <h2 class="text-lg font-medium text-white">Quick Note</h2>
        <div id="notes-status" class="text-sm"></div>
      </div>

      <form id="notes-form" class="space-y-4">
        @csrf
        <div>
          <label class="block text-sm font-medium text-gray-300">Note Title</label>
          <input type="text" name="name" required
            class="mt-1 block w-full rounded-lg border border-gray-700 bg-gray-800 px-3 py-2 text-white placeholder-gray-400 focus:border-yellow-500 focus:outline-none focus:ring-yellow-500/70">
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-300">Date</label>
          <input type="datetime-local" name="date" required
            class="mt-1 block w-full rounded-lg border border-gray-700 bg-gray-800 px-3 py-2 text-white placeholder-gray-400 focus:border-yellow-500 focus:outline-none focus:ring-yellow-500/70"
            value="{{ now()->format('Y-m-d\TH:i') }}">
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-300">Note Text</label>
          <textarea name="text" rows="3" required
            class="mt-1 block w-full rounded-lg border border-gray-700 bg-gray-800 px-3 py-2 text-white placeholder-gray-400 focus:border-yellow-500 focus:outline-none focus:ring-yellow-500/70"></textarea>
        </div>

        <div class="flex items-center">
          <input type="checkbox" name="is_todo"
            class="h-4 w-4 rounded border-gray-700 bg-gray-800 text-yellow-500 focus:ring-yellow-500/70">
          <label class="ml-2 text-sm text-gray-300">Create Todo Item</label>
        </div>

        <div class="flex justify-end">
          <button type="submit"
            class="inline-flex items-center rounded-lg bg-yns_yellow px-4 py-2 text-sm font-medium text-gray-900 transition duration-200 hover:bg-yellow-400 focus:outline-none focus:ring-2 focus:ring-yellow-500/70">
            Add Note
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const popup = {
      button: document.getElementById('notes-toggle'),
      container: document.getElementById('notes-popup'),
      form: document.getElementById('notes-form'),
      status: document.getElementById('notes-status'),
      isOpen: false,
      newNoteButton: document.getElementById('new-note-btn'),

      toggle() {
        this.isOpen = !this.isOpen;
        this.container.classList.toggle('hidden', !this.isOpen);
      },

      close() {
        this.isOpen = false;
        this.container.classList.add('hidden');
      },

      showStatus(message, isError = false) {
        this.status.textContent = message;
        this.status.className = `text-sm ${isError ? 'text-red-400' : 'text-green-400'}`;
      }
    };

    // Trigger popup from New Note button
    if (popup.newNoteButton) {
      popup.newNoteButton.addEventListener('click', (e) => {
        e.stopPropagation();
        popup.toggle();
      });
    }

    // Toggle popup
    popup.button.addEventListener('click', (e) => {
      e.stopPropagation();
      popup.toggle();
    });

    // Close on click outside
    document.addEventListener('click', (e) => {
      if (popup.isOpen && !popup.container.contains(e.target)) {
        popup.close();
      }
    });

    // Close on escape
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && popup.isOpen) {
        popup.close();
      }
    });

    // Handle form submission
    popup.form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const formData = new FormData(popup.form);

      // Create data object with is_todo always present as boolean
      const data = {
        name: formData.get('name'),
        date: formData.get('date'),
        text: formData.get('text'),
        is_todo: formData.get('is_todo') === 'on', // Convert 'on' to true, null/undefined to false
        _token: document.querySelector('meta[name="csrf-token"]').content
      };

      try {
        const response = await fetch(`/dashboard/{{ $dashboardType }}/notes/new`, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(data)
        });

        const responseData = await response.json();

        if (!response.ok) throw new Error(responseData.message || 'Failed to create note');

        // Dispatch custom event to refresh notes
        document.dispatchEvent(new CustomEvent('noteCreated', {
          detail: {
            dashboardType: '{{ $dashboardType }}'
          }
        }));

        popup.showStatus('Note created successfully!');
        popup.form.reset();

        // Auto close after success
        setTimeout(() => popup.close(), 1500);
        loadNotes(currentPage, false);

      } catch (error) {
        popup.showStatus(error.message, true);
      }
    });
  });
</script>

<style>
  .scale-0 {
    transform: scale(0);
  }

  /* Animation classes */
  .notes-enter {
    opacity: 1 !important;
    transform: scale(1) translateY(0) !important;
    pointer-events: auto !important;
  }

  .notes-leave {
    opacity: 0;
    transform: scale(0.95) translateY(10px);
    pointer-events: none;
  }

  /* Add subtle hover effects to form elements */
  input:hover,
  textarea:hover {
    transform: translateY(-1px);
    transition: all 0.2s ease;
  }

  input:focus,
  textarea:focus {
    transform: translateY(-2px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
  }

  button[type="submit"]:active {
    transform: scale(0.98);
  }

  /* Add shake animation for errors */
  @keyframes shake {

    0%,
    100% {
      transform: translateX(0);
    }

    25% {
      transform: translateX(-5px);
    }

    75% {
      transform: translateX(5px);
    }
  }

  .shake {
    animation: shake 0.5s ease-in-out;
  }

  /* Update transition styles */
  #notes-popout {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  }

  /* Remove duplicate transition class */
  .notes-transition {
    display: none;
  }
</style>
