<x-app-layout :dashboardType="$dashboardType" :modules="$modules">
  <x-slot name="header">
    <x-sub-nav :userId="$userId" />
  </x-slot>

  <div class="mx-auto w-full max-w-screen-2xl py-16">
    <div class="relative mb-8 shadow-md sm:rounded-lg">
      <div class="min-w-screen-xl mx-auto max-w-screen-xl rounded-lg border border-white bg-yns_dark_gray text-white">
        <div class="header border-b border-b-white px-8 py-8">
          <div class="flex flex-row items-center justify-between">
            <h1 class="mb-8 font-heading text-4xl font-bold">{{ $document->title }}</h1>
            <div class="group">
              <x-button
                href="{{ route('admin.dashboard.document.edit', ['dashboardType' => $dashboardType, 'id' => $document->id]) }}"
                id="edit-document-btn" fa="fas fa-pencil" label="Edit" />
              <button
                class="delete-document rounded-lg border border-yns_red bg-yns_red px-4 py-2 font-heading text-white transition duration-150 ease-in-out hover:border-yns_red hover:text-yns_red"
                data-document-id="{{ $document->id }}">
                Delete Document
              </button>
            </div>
          </div>
          <form id="delete-document-{{ $document->id }}"
            action="{{ route('admin.dashboard.document.delete', ['dashboardType' => $dashboardType, 'id' => $document->id]) }}"
            method="POST" class="hidden">
            @csrf
            @method('DELETE')
          </form>

          <div class="group mb-4">
            <x-input-label-dark>Description</x-input-label-dark>
            <p class="text-lg">{{ $document->description }}</p>
          </div>

          <div class="group mb-4">
            <x-input-label-dark>Tags</x-input-label-dark>
            <ul class="list-disc pl-5">
              @php
                $categories = json_decode($document->category, true);
              @endphp

              @if (is_array($categories))
                @foreach ($categories as $categoryArray)
                  @if (is_array($categoryArray))
                    @foreach ($categoryArray as $category)
                      <li class="text-lg">{{ $category }}</li>
                    @endforeach
                  @else
                    <li class="text-lg">{{ $categoryArray }}</li>
                  @endif
                @endforeach
              @else
                <li class="text-lg">No tags available</li>
              @endif
            </ul>
          </div>

          <div class="group mb-4">
            <x-input-label-dark>Document Preview</x-input-label-dark>
            <div
              class="mt-4 flex flex-col items-center justify-center rounded-lg border-2 border-dashed border-gray-600 p-6">
              @php
                $extension = pathinfo($document->file_path, PATHINFO_EXTENSION);
              @endphp

              @if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif']))
                <img src="{{ Storage::url($document->file_path) }}" alt="{{ $document->title }}"
                  class="max-h-96 object-contain">
              @elseif($extension === 'pdf')
                <embed src="{{ Storage::url($document->file_path) }}" type="application/pdf" width="100%"
                  height="600px">
              @else
                <div class="text-center">
                  <i class="fas fa-file-alt text-6xl text-gray-400"></i>
                  <p class="mt-4 text-gray-400">Preview not available for this file type</p>
                </div>
              @endif

              <a href="{{ route('admin.dashboard.document.download', ['dashboardType' => $dashboardType, 'id' => $document->id]) }}"
                class="mt-4 inline-flex items-center rounded-lg border border-white bg-white px-4 py-2 font-heading text-black transition duration-150 ease-in-out hover:border-yns_yellow hover:text-yns_yellow">
                <i class="fas fa-download mr-2"></i>
                Download Document
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    document.querySelector('.delete-document').addEventListener('click', function(e) {
      e.preventDefault();
      const documentId = this.dataset.documentId;

      showConfirmationNotification({
        text: 'Are you sure you want to delete this document?',
        confirmButtonText: 'Yes, delete it',
        cancelButtonText: 'No, keep it',
        onConfirm: () => {
          const form = document.getElementById(`delete-document-${documentId}`);
          const formData = new FormData(form);

          fetch(form.action, {
              method: 'POST',
              headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
              },
              body: formData
            })
            .then(response => response.json())
            .then(data => {
              if (data.success) {
                window.location.href =
                  '{{ route('admin.dashboard.documents.index', ['dashboardType' => $dashboardType]) }}';
              }
            })
            .catch(error => console.error('Error:', error));
        }
      });
    });
  });
</script>
