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
                  <div class="flex items-center gap-4">
                    <span class="text-gray-400">Document ID: {{ $document->id }}</span>
                    <span class="text-gray-400">•</span>
                    <span class="text-gray-400">Created {{ $document->created_at->diffForHumans() }}</span>
                    <span class="text-gray-400">•</span>
                    <span class="text-gray-400">By
                      {{ $document->user->first_name . ' ' . $document->user->last_name }}</span>
                  </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex space-x-4">
                  <a href="{{ route('admin.dashboard.document.download', ['dashboardType' => $dashboardType, 'id' => $document->id]) }}"
                    class="inline-flex items-center rounded-lg bg-gray-800 px-4 py-2 font-heading text-sm font-semibold text-white transition duration-150 ease-in-out hover:bg-gray-700">
                    <i class="fas fa-download mr-2"></i>
                    Download
                  </a>
                  <x-button
                    href="{{ route('admin.dashboard.document.edit', ['dashboardType' => $dashboardType, 'id' => $document->id]) }}"
                    id="edit-document-btn" fa="fas fa-pencil" label="Edit" />
                  <button
                    class="delete-document inline-flex items-center rounded-lg bg-red-600 px-4 py-2 font-heading text-sm font-semibold text-white transition duration-150 ease-in-out hover:bg-red-700"
                    data-document-id="{{ $document->id }}">
                    <i class="fas fa-trash-alt mr-2"></i>
                    Delete
                  </button>
                  <form id="delete-document-{{ $document->id }}"
                    action="{{ route('admin.dashboard.document.delete', ['dashboardType' => $dashboardType, 'id' => $document->id]) }}"
                    method="POST" class="hidden">
                    @csrf
                    @method('DELETE')
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>

        {{-- Main Content Container --}}
        <div class="rounded-xl border border-gray-800 bg-gray-900/60 backdrop-blur-md backdrop-saturate-150">
          <div class="p-6 lg:p-8">
            {{-- Document Information --}}
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
              {{-- Left Column: Info --}}
              <div class="space-y-6">
                {{-- Title --}}
                <div class="mb-6 rounded-lg border border-gray-800 bg-black/50 p-6">
                  <h2 class="mb-2 font-heading text-xl font-bold text-white">Document Title</h2>
                  <p class="text-gray-300">{{ $document->title }}</p>
                </div>
                {{-- Description --}}
                <div class="rounded-lg border border-gray-800 bg-black/50 p-6">
                  <h2 class="mb-4 font-heading text-xl font-bold text-white">Description</h2>
                  <p class="text-gray-300">{{ $document->description ?: 'No description available' }}</p>
                </div>

                {{-- Tags --}}
                <div class="rounded-lg border border-gray-800 bg-black/50 p-6">
                  <h2 class="mb-4 font-heading text-xl font-bold text-white">Tags</h2>
                  <div class="flex flex-wrap gap-2">
                    @php
                      $categories = json_decode($document->category, true);
                    @endphp

                    @if (is_array($categories) && !empty($categories))
                      @foreach ($categories as $category)
                        <span class="rounded-lg bg-gray-800 px-3 py-1 text-sm text-gray-300">
                          {{ is_array($category) ? implode(', ', $category) : $category }}
                        </span>
                      @endforeach
                    @else
                      <span class="text-gray-400">No tags available</span>
                    @endif
                  </div>
                </div>
              </div>

              {{-- Right Column: Preview --}}
              <div class="rounded-lg border border-gray-800 bg-black/50 p-6">
                <div class="mb-4 flex items-center justify-between">
                  <h2 class="font-heading text-xl font-bold text-white">Document Preview</h2>
                  <button type="button" id="enlarge-preview"
                    class="inline-flex items-center rounded-lg bg-gray-800 px-3 py-1.5 text-sm text-gray-300 hover:bg-gray-700">
                    <i class="fas fa-expand-alt mr-2"></i>
                    Enlarge
                  </button>
                </div>

                <div class="overflow-hidden rounded-lg border border-gray-700 bg-gray-800/50">
                  @php
                    $extension = pathinfo($document->file_path, PATHINFO_EXTENSION);
                  @endphp

                  @if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif']))
                    <div class="flex justify-center p-4">
                      <img src="{{ Storage::url($document->file_path) }}" alt="{{ $document->title }}"
                        class="max-h-64 lg:max-h-80 rounded-lg object-contain" id="preview-image">
                    </div>
                  @elseif($extension === 'pdf')
                    <div class="aspect-video w-full">
                      <embed src="{{ Storage::url($document->file_path) }}" type="application/pdf"
                        class="h-full w-full" id="preview-pdf">
                    </div>
                  @else
                    <div class="flex flex-col items-center justify-center py-12">
                      <i class="fas fa-file-alt text-6xl text-gray-500"></i>
                      <p class="mt-4 text-gray-400">Preview not available for this file type</p>
                    </div>
                  @endif
                </div>
              </div>
            </div>
          </div>
        </div>

        {{-- Modal for enlarged preview --}}
        <div id="preview-modal" class="fixed inset-0 z-50 hidden">
          <div class="fixed inset-0 bg-black/75 backdrop-blur-sm"></div>
          <div class="fixed inset-4 flex items-center justify-center">
            <div class="relative w-full max-w-5xl rounded-xl bg-gray-900 p-4">
              <button type="button" id="close-preview" class="absolute right-4 top-4 text-gray-400 hover:text-white">
                <i class="fas fa-times text-xl"></i>
              </button>
              <div class="mt-8 flex items-center justify-center">
                <div id="modal-content" class="max-h-[80vh] overflow-auto"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Replace the existing delete document script with:
    document.querySelector('.delete-document').addEventListener('click', function(e) {
      e.preventDefault();
      const documentId = this.dataset.documentId;
      const dashboardType = '{{ $dashboardType }}'; // Get from blade

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
                // Use absolute URL for redirect
                window.location.href = `/dashboard/${dashboardType}/documents`;
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

    // Preview enlargement
    document.getElementById('enlarge-preview')?.addEventListener('click', function() {
      const modal = document.getElementById('preview-modal');
      const modalContent = document.getElementById('modal-content');
      const previewImage = document.getElementById('preview-image');
      const previewPdf = document.getElementById('preview-pdf');

      if (previewImage) {
        modalContent.innerHTML =
          `<img src="${previewImage.src}" class="max-h-[80vh] rounded-lg object-contain">`;
      } else if (previewPdf) {
        modalContent.innerHTML =
          `<embed src="${previewPdf.src}" type="application/pdf" class="h-[80vh] w-full rounded-lg">`;
      }

      modal.classList.remove('hidden');
    });

    document.getElementById('close-preview')?.addEventListener('click', function() {
      document.getElementById('preview-modal').classList.add('hidden');
    });

    // Close modal on escape key
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        document.getElementById('preview-modal').classList.add('hidden');
      }
    });

    // Close modal on backdrop click
    document.getElementById('preview-modal')?.addEventListener('click', function(e) {
      if (e.target === this) {
        this.classList.add('hidden');
      }
    });
  });
</script>
