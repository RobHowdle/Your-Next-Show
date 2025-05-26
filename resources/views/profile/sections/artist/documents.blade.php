<!-- Document Management Template for Artists -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<div class="rounded-lg bg-gray-800/50 p-6 backdrop-blur-sm">
  <header class="mb-6 border-b border-gray-700 pb-4">
    <div class="flex items-center justify-between">
      <h2 class="font-heading text-lg font-medium text-white">
        {{ __('Document Management') }}
      </h2>
      <a href="{{ route('admin.dashboard.document.create', ['dashboardType' => $dashboardType]) }}"
        class="inline-flex items-center rounded-lg bg-yns_yellow px-4 py-2 text-sm font-medium text-gray-900 transition duration-200 hover:bg-yellow-400">
        <i class="fas fa-plus mr-2"></i>
        New Document
      </a>
    </div>
    <p class="mt-1 text-sm text-gray-400">
      {{ __('Upload, manage and share your important documents.') }}
    </p>
  </header>

  <!-- Document Grid -->
  <div id="documents-grid" class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
    @if (isset($profileData['documents']) && count($profileData['documents']) > 0)
      @foreach ($profileData['documents'] as $document)
        <div
          class="group relative overflow-hidden rounded-xl border border-gray-800 bg-gray-900/60 shadow-md transition hover:border-gray-700 hover:shadow-lg">
          <!-- Document Preview -->
          <div class="relative aspect-[4/3] bg-black/20 p-4">
            @php
              $extension = pathinfo($document->file_path, PATHINFO_EXTENSION);
              $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
              $isPdf = strtolower($extension) === 'pdf';
            @endphp

            <div class="flex h-full w-full items-center justify-center">
              @if ($isImage)
                <img src="{{ Storage::disk('public')->url($document->file_path) }}" alt="{{ $document->title }}"
                  class="max-h-full max-w-full rounded object-contain">
              @elseif($isPdf)
                <div class="text-center">
                  <i class="far fa-file-pdf mb-2 text-5xl text-red-500"></i>
                  <p class="text-sm text-white">PDF Document</p>
                </div>
              @else
                <div class="text-center">
                  <i class="far fa-file mb-2 text-5xl text-gray-500"></i>
                  <p class="text-sm text-white">{{ strtoupper($extension) }} Document</p>
                </div>
              @endif
            </div>

            <!-- Action Button Overlay -->
            <div
              class="absolute inset-0 flex items-center justify-center bg-black/75 opacity-0 transition duration-300 group-hover:opacity-100">
              <a href="{{ route('admin.dashboard.document.download', ['dashboardType' => $dashboardType, 'id' => $document->id]) }}"
                class="mx-1 rounded-lg bg-blue-600 px-3 py-2 text-sm font-medium text-white transition hover:bg-blue-700">
                <i class="fas fa-download mr-1"></i> Download
              </a>
              <a href="{{ route('admin.dashboard.document.show', ['dashboardType' => $dashboardType, 'id' => $document->id]) }}"
                class="mx-1 rounded-lg bg-gray-700 px-3 py-2 text-sm font-medium text-white transition hover:bg-gray-600">
                <i class="fas fa-eye mr-1"></i> View
              </a>
            </div>
          </div>

          <!-- Document Info -->
          <div class="p-4">
            <div class="mb-2 flex items-center justify-between">
              <h3 class="line-clamp-1 font-heading text-base font-medium text-white" title="{{ $document->title }}">
                {{ $document->title }}
              </h3>

              <!-- Visibility Toggle -->
              <div class="flex items-center">
                <label class="inline-flex cursor-pointer items-center">
                  <input type="checkbox" class="toggle-checkbox document-visibility-toggle sr-only"
                    data-id="{{ $document->id }}" data-is-private="{{ $document->private ? 1 : 0 }}"
                    {{ !$document->private ? 'checked' : '' }}>
                  <div
                    class="toggle-bg flex h-6 w-11 items-center rounded-full bg-gray-700 p-1 transition-colors duration-300">
                    <div class="toggle-circle h-4 w-4 rounded-full bg-white transition-transform duration-300"></div>
                  </div>
                  <span class="sr-only">Toggle Visibility</span>
                </label>
                <span
                  class="visibility-status {{ !$document->private ? 'text-green-400' : 'text-gray-400' }} ml-2 text-xs">
                  {{ !$document->private ? 'Public' : 'Private' }}
                </span>
              </div>
            </div>

            <!-- Document Meta -->
            <div class="mb-3 flex flex-wrap text-xs text-gray-400">
              <span class="mr-3 flex items-center">
                <i class="far fa-calendar mr-1"></i>
                {{ \Carbon\Carbon::parse($document->created_at)->format('d M, Y') }}
              </span>
              <span class="flex items-center">
                <i class="far fa-file mr-1"></i>
                {{ strtoupper(pathinfo($document->file_path, PATHINFO_EXTENSION)) }}
              </span>
            </div>

            <!-- Category Tags -->
            @php
              $categories = json_decode($document->category ?? '[]');
            @endphp
            @if (!empty($categories))
              <div class="mb-3 flex flex-wrap gap-1">
                @foreach ($categories as $category)
                  <span class="rounded bg-gray-800 px-2 py-1 text-xs text-gray-300">
                    {{ $category }}
                  </span>
                @endforeach
              </div>
            @endif

            <!-- Description -->
            @if ($document->description)
              <p class="mb-4 line-clamp-2 text-sm text-gray-400" title="{{ $document->description }}">
                {{ $document->description }}
              </p>
            @endif

            <!-- Action Buttons -->
            <div class="mt-4 flex items-center justify-between border-t border-gray-800 pt-4">
              <a href="{{ route('admin.dashboard.document.edit', ['dashboardType' => $dashboardType, 'id' => $document->id]) }}"
                class="rounded border border-gray-700 px-3 py-1 text-xs text-gray-300 transition hover:bg-gray-800">
                <i class="fas fa-edit mr-1"></i> Edit
              </a>

              <!-- Delete Button and Form -->
              <form id="delete-document-{{ $document->id }}" method="POST" class="inline-block"
                action="{{ route('admin.dashboard.document.delete', ['dashboardType' => $dashboardType, 'id' => $document->id]) }}">
                @csrf
                @method('DELETE')
                <button type="button"
                  class="delete-document rounded border border-red-900/50 px-3 py-1 text-xs text-red-500 transition hover:bg-red-900/20"
                  data-document-id="{{ $document->id }}">
                  <i class="fas fa-trash-alt mr-1"></i> Delete
                </button>
              </form>
            </div>
          </div>
        </div>
      @endforeach
    @else
      <!-- Empty State -->
      <div class="col-span-full rounded-xl border border-gray-800 bg-gray-900/60 p-8 text-center backdrop-blur-sm">
        <div class="mx-auto mb-4 h-12 w-12 rounded-full bg-gray-800 p-3 text-gray-400">
          <i class="fas fa-folder-open"></i>
        </div>
        <h3 class="mb-2 font-heading text-lg font-semibold text-white">No Documents Found</h3>
        <p class="text-sm text-gray-400">Get started by creating your first document.</p>
        <a href="{{ route('admin.dashboard.document.create', ['dashboardType' => $dashboardType]) }}"
          class="mt-4 inline-flex items-center rounded-lg bg-yns_yellow px-4 py-2 text-sm font-medium text-gray-900 transition duration-200 hover:bg-yellow-400">
          <i class="fas fa-plus mr-2"></i>
          New Document
        </a>
      </div>
    @endif
  </div>
</div>

<!-- CSS for Visibility Toggle -->
<style>
  /* Toggle styling */
  .toggle-bg {
    transition: background-color 0.3s ease;
  }

  .toggle-checkbox:checked+.toggle-bg {
    background-color: #10B981;
    /* Green when enabled */
  }

  .toggle-checkbox:checked+.toggle-bg .toggle-circle {
    transform: translateX(100%);
  }
</style>

<!-- JavaScript for Document Management -->
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Handle document deletion
    document.querySelectorAll('.delete-document').forEach(button => {
      button.addEventListener('click', function(e) {
        e.preventDefault();
        const documentId = this.dataset.documentId;

        showConfirmationNotification({
          text: 'Are you sure you want to delete this document?',
          confirmButtonText: 'Yes, delete it',
          cancelButtonText: 'No, keep it',
          onConfirm: () => {
            const form = document.getElementById(`delete-document-${documentId}`);
            if (!form) {
              console.error('Delete form not found');
              return;
            }

            fetch(form.action, {
                method: 'POST',
                headers: {
                  'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                  'Accept': 'application/json'
                },
                body: new FormData(form)
              })
              .then(response => {
                if (!response.ok) {
                  throw new Error('Network response was not ok');
                }
                return response.json();
              })
              .then(data => {
                if (data.success) {
                  showSuccessNotification('Document deleted successfully');
                  // Remove the document card from the grid
                  const documentCard = button.closest('.group');
                  const gridContainer = documentCard.parentElement;
                  documentCard.remove();

                  // If no documents left, show empty state
                  if (document.querySelectorAll('.group').length === 0) {
                    const emptyState = `
                  <div class="col-span-full rounded-xl border border-gray-800 bg-gray-900/60 p-8 text-center backdrop-blur-sm">
                    <div class="mx-auto mb-4 h-12 w-12 rounded-full bg-gray-800 p-3 text-gray-400">
                      <i class="fas fa-folder-open"></i>
                    </div>
                    <h3 class="mb-2 font-heading text-lg font-semibold text-white">No Documents Found</h3>
                    <p class="text-sm text-gray-400">Get started by creating your first document.</p>
                    <a href="/dashboard/${dashboardType}/document/create"
                      class="mt-4 inline-flex items-center rounded-lg bg-yns_yellow px-4 py-2 text-sm font-medium text-gray-900 transition duration-200 hover:bg-yellow-400">
                      <i class="fas fa-plus mr-2"></i>
                      New Document
                    </a>
                  </div>
                `;
                    document.getElementById('documents-grid').innerHTML = emptyState;
                  }
                } else {
                  throw new Error(data.message || 'Error deleting document');
                }
              })
              .catch(error => {
                console.error('Error:', error);
                showErrorNotification(error.message || 'Error deleting document');
              });
          }
        });
      });
    });

    // Handle document visibility toggle
    document.querySelectorAll('.document-visibility-toggle').forEach(checkbox => {
      checkbox.addEventListener('change', function() {
        const documentId = this.dataset.id;
        const isPrivate = this.checked ? 0 : 1; // Checked = Public = not private
        const statusText = this.closest('.group').querySelector('.visibility-status');

        // Send AJAX request to update document visibility
        fetch(`/profile/${dashboardType}/documents/${documentId}/toggle-visibility`, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
              'Accept': 'application/json'
            },
            body: JSON.stringify({
              private: isPrivate,
              documentId: documentId
            })
          })
          .then(response => {
            if (!response.ok) {
              // If the server returns an error, revert the checkbox
              this.checked = !this.checked;
              throw new Error('Failed to update document visibility');
            }
            return response.json();
          })
          .then(data => {
            if (data.success) {
              // Update status text
              statusText.textContent = !isPrivate ? 'Public' : 'Private';
              statusText.className =
                `ml-2 text-xs visibility-status ${!isPrivate ? 'text-green-400' : 'text-gray-400'}`;

              // Show success notification
              showSuccessNotification(data.message ||
                `Document is now ${!isPrivate ? 'public' : 'private'}`);
            } else {
              // If there's a logical error, revert the checkbox
              this.checked = !this.checked;
              throw new Error(data.message || 'Error updating document visibility');
            }
          })
          .catch(error => {
            console.error('Error:', error);
            showErrorNotification(error.message);
          });
      });
    });
  });

  // Helper function for success notifications
  function showSuccessNotification(message) {
    // Check if custom notification function exists
    if (typeof window.showNotification === 'function') {
      window.showNotification(message, 'success');
    } else {
      alert(message); // Fallback to alert
    }
  }

  // Helper function for error notifications
  function showErrorNotification(message) {
    // Check if custom notification function exists
    if (typeof window.showNotification === 'function') {
      window.showNotification(message, 'error');
    } else {
      alert('Error: ' + message); // Fallback to alert
    }
  }

  // Helper function for confirmation dialogs
  function showConfirmationNotification(options) {
    // If the application has a custom confirmation dialog, use it
    if (typeof window.showConfirmationDialog === 'function') {
      window.showConfirmationDialog(options);
    } else {
      // Fallback to standard confirm
      if (confirm(options.text)) {
        options.onConfirm();
      }
    }
  }
</script>
