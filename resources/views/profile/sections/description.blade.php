<div class="rounded-lg bg-gray-800/50 p-6 backdrop-blur-sm">
  <header class="mb-6 border-b border-gray-700 pb-4">
    <h2 class="font-heading text-lg font-medium text-white">
      {{ __('About You') }}
    </h2>
    <p class="mt-1 text-sm text-gray-400">
      {{ __('Tell your story and share what makes you unique.') }}
    </p>
  </header>

  <form id="saveDescription" method="POST" class="space-y-6">
    @csrf
    @method('PUT')

    <div class="rounded-lg bg-black/20 p-6">
      <h3 class="mb-4 font-heading text-lg font-medium text-white">Description</h3>
      <div class="grid gap-4">
        <div>
          <x-input-label-dark for="description">Tell us about you... where you started, why you started, what you do
            etc</x-input-label-dark>
          <x-textarea-input id="description" name="description"
            class="mt-1 block w-full">{{ old('description', $profileData['description'] ?? '') }}</x-textarea-input>
          @error('description')
            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
          @enderror
        </div>
      </div>
    </div>

    <div class="flex items-center justify-end gap-4 border-t border-gray-700 pt-6">
      <button type="submit"
        class="rounded-lg border border-yns_yellow bg-yns_yellow px-4 py-2 font-heading font-bold text-black transition hover:bg-yns_yellow/90">
        Save Changes
      </button>
      @if (session('status') === 'profile-updated')
        <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-sm text-gray-400">
          {{ __('Saved.') }}
        </p>
      @endif
    </div>
  </form>
</div>

<script>
  // ...existing code...
</script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    if (typeof jQuery !== 'undefined') {
      const aboutContent = @json(old('description', $profileData['description'] ?? ''));
      // Custom Summernote styling
      const darkThemeStyles = `
        .note-editor.note-frame {
          background-color: rgb(0 0 0 / 0.2);
          border-color: rgb(55 65 81);
          border-radius: 0.5rem;
        }
        .note-editor.note-frame .note-toolbar {
          background-color: rgb(0 0 0 / 0.3);
          border-bottom-color: rgb(55 65 81);
          border-top-left-radius: 0.5rem;
          border-top-right-radius: 0.5rem;
        }
        .note-editor.note-frame .note-editing-area .note-editable {
          background-color: transparent;
          color: rgb(209 213 219);
          padding: 1rem;
        }
        .note-editor.note-frame .note-toolbar .note-btn {
          background-color: rgb(0 0 0 / 0.4);
          border-color: rgb(55 65 81);
          color: rgb(209 213 219);
        }
        .note-editor.note-frame .note-toolbar .note-btn:hover {
          background-color: rgb(0 0 0 / 0.6);
        }
        .note-editor.note-frame .note-toolbar .note-btn.active {
          background-color: rgb(var(--yns-yellow));
          color: rgb(0 0 0);
        }
        .note-editor.note-frame .note-status-output {
          background-color: rgb(0 0 0 / 0.2);
          color: rgb(209 213 219);
        }
        .note-editor.note-frame .note-statusbar {
          background-color: rgb(0 0 0 / 0.3);
          border-top-color: rgb(55 65 81);
        }
      `;

      // Add custom styles to head
      const styleSheet = document.createElement("style");
      styleSheet.textContent = darkThemeStyles;
      document.head.appendChild(styleSheet);

      $('#description').summernote({
        placeholder: 'Tell us about you...',
        tabsize: 2,
        height: 300,
        toolbar: [
          ['style', ['style']],
          ['font', ['bold', 'underline', 'clear']],
          ['color', ['color']],
          ['para', ['ul', 'ol', 'paragraph']],
          ['insert', ['link']],
          ['view', ['fullscreen', 'codeview', 'help']]
        ],
        callbacks: {
          onInit: function() {
            if (aboutContent) {
              $('#description').summernote('code', aboutContent);
            }
          }
        }
      });
    } else {
      console.error('jQuery is not loaded');
    }

    $('#saveDescription').on('submit', function(e) {
      e.preventDefault();

      const form = $(this);
      const formData = new FormData(this);

      $.ajax({
        url: '{{ route($dashboardType . '.update', ['dashboardType' => $dashboardType, 'user' => $user]) }}',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
          console.log(response);
          if (response.success) {
            showSuccessNotification(response.message);
            setTimeout(() => {
              window.location.href = response.redirect;
            }, 2000);
          } else {
            alert('Failed to update profile');
          }
        },
        error: function(xhr, status, error) {
          const response = xhr.responseJSON;
          showFailureNotification(response);
        }
      });
    });
  });
</script>
