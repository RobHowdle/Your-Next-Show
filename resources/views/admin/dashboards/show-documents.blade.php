<x-app-layout :dashboardType="$dashboardType" :modules="$modules">
  <x-slot name="header">
    <x-sub-nav :userId="$userId" />
  </x-slot>

  <div class="relative min-h-screen">
    <div class="relative mx-auto w-full max-w-screen-2xl py-8">
      <div class="px-4">
        {{-- Header Section --}}
        <div class="relative mb-8">
          {{-- Background with overlay --}}
          <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-gray-900 via-black to-gray-900 opacity-75"></div>

          {{-- Content --}}
          <div class="relative px-4 py-6 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-7xl">
              <div class="flex items-center justify-between">
                <div>
                  <h1 class="font-heading text-2xl font-bold text-white md:text-3xl">Documents</h1>
                  <p class="mt-2 text-gray-400">Manage your documents and files</p>
                </div>
                <a href="{{ route('admin.dashboard.document.create', ['dashboardType' => $dashboardType]) }}"
                  class="inline-flex items-center rounded-lg bg-yns_yellow px-4 py-2 text-sm font-medium text-gray-900 transition duration-200 hover:bg-yellow-400 focus:outline-none focus:ring-2 focus:ring-yellow-500/70">
                  <i class="fas fa-plus mr-2"></i>
                  New Document
                </a>
              </div>
            </div>
          </div>
        </div>

        {{-- Documents Grid --}}
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
          @forelse($documents as $document)
            <div
              class="group relative rounded-xl border border-gray-800 bg-gray-900/60 p-6 backdrop-blur-sm transition hover:border-gray-700">
              {{-- Document Type Icon --}}
              <div class="mb-4">
                @php
                  $extension = pathinfo($document->file_path, PATHINFO_EXTENSION);
                  $iconClass = match ($extension) {
                      'pdf' => 'fa-file-pdf text-yns_red',
                      'doc', 'docx' => 'fa-file-word text-yns_cyan',
                      'jpg', 'jpeg', 'png', 'heic', 'heif' => 'fa-file-image text-yns_teal',
                      default => 'fa-file-alt text-gray-500',
                  };
                @endphp
                <i class="fas {{ $iconClass }} text-3xl"></i>
              </div>

              {{-- Document Info --}}
              <h3 class="mb-2 font-heading text-lg font-semibold text-white">{{ $document->title }}</h3>
              <p class="mb-4 text-sm text-gray-400">Created {{ $document->created_at->diffForHumans() }}</p>

              {{-- Tags --}}
              @if ($document->tags)
                <div class="mb-4 flex flex-wrap gap-2">
                  @foreach ($document->tags as $tag)
                    <span class="rounded-lg bg-gray-800 px-2 py-1 text-xs text-gray-300">
                      {{ $tag }}
                    </span>
                  @endforeach
                </div>
              @endif

              {{-- Actions --}}
              <div class="mt-4 flex items-center justify-end gap-2">
                <a href="{{ route('admin.dashboard.document.show', ['dashboardType' => $dashboardType, 'id' => $document->id]) }}"
                  class="inline-flex items-center justify-center rounded-lg bg-gray-800 p-2 text-gray-300 transition hover:bg-gray-700 hover:text-white"
                  title="View">
                  <i class="fas fa-eye"></i>
                </a>
                <a href="{{ route('admin.dashboard.document.download', ['dashboardType' => $dashboardType, 'id' => $document->id]) }}"
                  class="inline-flex items-center justify-center rounded-lg bg-gray-800 p-2 text-gray-300 transition hover:bg-gray-700 hover:text-white"
                  title="Download">
                  <i class="fas fa-download"></i>
                </a>
                <a href="{{ route('admin.dashboard.document.edit', ['dashboardType' => $dashboardType, 'id' => $document->id]) }}"
                  class="inline-flex items-center justify-center rounded-lg bg-gray-800 p-2 text-gray-300 transition hover:bg-gray-700 hover:text-white"
                  title="Edit">
                  <i class="fas fa-pencil-alt"></i>
                </a>
                <button type="button"
                  class="delete-document inline-flex items-center justify-center rounded-lg bg-red-600/10 p-2 text-red-500 transition hover:bg-red-600/20"
                  title="Delete" data-document-id="{{ $document->id }}">
                  <i class="fas fa-trash-alt"></i>
                </button>
                <form id="delete-document-{{ $document->id }}"
                  action="{{ route('admin.dashboard.document.delete', ['dashboardType' => $dashboardType, 'id' => $document->id]) }}"
                  method="POST" class="hidden">
                  @csrf
                  @method('DELETE')
                </form>
              </div>
            </div>
          @empty
            <div
              class="col-span-full rounded-xl border border-gray-800 bg-gray-900/60 p-8 text-center backdrop-blur-sm">
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
          @endforelse
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Handle all delete buttons
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
                    gridContainer.innerHTML = emptyState;
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
  });
</script>
