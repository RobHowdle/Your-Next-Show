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
                  <h1 class="font-heading text-2xl font-bold text-white md:text-3xl">Edit Document</h1>
                  <p class="mt-2 text-gray-400">Update your document information</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        {{-- Main Content Container --}}
        <div class="rounded-xl border border-gray-800 bg-gray-900/60 backdrop-blur-md backdrop-saturate-150">
          <div class="p-6 lg:p-8">
            <form id="document-form" method="POST" enctype="multipart/form-data"
              action="{{ route('admin.dashboard.document.update', ['dashboardType' => $dashboardType, 'id' => $document->id]) }}">
              @csrf
              @method('PUT')

              {{-- Grid Layout --}}
              <div class="grid gap-8 lg:grid-cols-2">
                {{-- Left Column: Document Details --}}
                <div class="space-y-6">
                  <div class="rounded-lg border border-gray-800 bg-black/50 p-6">
                    <h2 class="mb-6 font-heading text-xl font-bold text-white">Document Details</h2>

                    {{-- Document Title --}}
                    <div class="mb-6">
                      <x-input-label-dark :required="true">Document Title</x-input-label-dark>
                      <x-text-input id="title" name="title" value="{{ old('title', $document->title) }}"
                        :required="true" class="mt-1 block w-full" />
                    </div>

                    {{-- Description --}}
                    <div class="mb-6">
                      <x-input-label-dark>Description</x-input-label-dark>
                      <x-textarea-input id="description" name="description" rows="4"
                        class="mt-1 block w-full">{{ old('description', $document->description) }}</x-textarea-input>
                    </div>

                    {{-- Tags --}}
                    <div>
                      <x-input-label-dark>Tags</x-input-label-dark>
                      <div class="mt-1">
                        @php
                          // Decode saved categories
                          $savedCategories = json_decode($document->category ?? '[]', true);
                          // Handle both array and string cases
                          $savedCategories = is_array($savedCategories) ? $savedCategories : [$savedCategories];
                          // Clean up the tags
                          $savedCategories = array_map(function ($tag) {
                              return trim($tag, '[]"');
                          }, array_filter($savedCategories));

                          // Merge saved categories with config tags and ensure uniqueness
                          $allTags = array_values(array_unique(array_merge($tags, $savedCategories)));
                        @endphp

                        <x-multi-select id="tags" name="tags[]" :options="$allTags" :selected="old('tags', $savedCategories)" />
                      </div>
                    </div>

                    {{-- Submit Button --}}
                    <div class="mt-8 flex justify-end">
                      <button type="submit"
                        class="inline-flex items-center rounded-lg bg-yns_yellow px-6 py-3 text-sm font-medium text-gray-900 transition duration-200 hover:bg-yellow-400">
                        <i class="fas fa-save mr-2"></i>
                        Save Changes
                      </button>
                    </div>
                  </div>
                </div>

                {{-- Right Column: File Upload --}}
                <div class="space-y-6">
                  <div class="rounded-lg border border-gray-800 bg-black/50 p-6">
                    <h2 class="mb-6 font-heading text-xl font-bold text-white">Document File</h2>

                    {{-- Current File Info --}}
                    <div class="mb-6">
                      <h3 class="mb-2 text-sm font-medium text-white">Current File</h3>
                      <div class="rounded-lg border border-gray-800 bg-gray-900/50 p-4">
                        <div class="flex items-center gap-4">
                          @php
                            $extension = pathinfo($document->file_path, PATHINFO_EXTENSION);
                            $iconClass = match ($extension) {
                                'pdf' => 'fa-file-pdf text-red-500',
                                'doc', 'docx' => 'fa-file-word text-blue-500',
                                'jpg', 'jpeg', 'png', 'gif' => 'fa-file-image text-green-500',
                                default => 'fa-file-alt text-gray-500',
                            };
                          @endphp
                          <i class="fas {{ $iconClass }} text-2xl"></i>
                          <span class="text-sm text-gray-300">{{ basename($document->file_path) }}</span>
                        </div>
                      </div>
                    </div>

                    {{-- File Upload --}}
                    <div id="my-dropzone"
                      class="dropzone relative flex min-h-[200px] cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed border-gray-700 bg-black/30 p-6 transition-all hover:border-gray-600">
                      <div class="dz-message flex flex-col items-center justify-center">
                        <i class="fas fa-cloud-upload-alt mb-2 text-3xl text-gray-400"></i>
                        <p class="mb-2 text-sm text-gray-400">Drag and drop a new file here or click to upload</p>
                        <p class="accepted-formats text-xs text-gray-500"></p>
                      </div>
                      <div class="dz-preview dz-file-preview hidden">
                        <div class="dz-details space-y-2">
                          <div class="dz-filename"><span data-dz-name></span></div>
                          <div class="dz-size" data-dz-size></div>
                          <img data-dz-thumbnail class="max-h-32 rounded" />
                        </div>
                        <div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress></span></div>
                      </div>
                    </div>
                    <input type="hidden" id="uploaded_file_path" name="uploaded_file_path"
                      value="{{ $document->file_path }}">
                  </div>

                  {{-- Preview Section --}}
                  <div class="rounded-lg border border-gray-800 bg-black/50 p-6">
                    <h2 class="mb-4 font-heading text-xl font-bold text-white">Preview</h2>
                    <div id="preview-container">
                      @if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif']))
                        <img src="{{ Storage::url($document->file_path) }}" alt="Preview"
                          class="max-h-64 rounded-lg object-contain">
                      @elseif($extension === 'pdf')
                        <div class="flex items-center gap-4 rounded-lg bg-gray-800 p-4">
                          <i class="fas fa-file-pdf text-2xl text-red-500"></i>
                          <span class="text-sm text-gray-300">PDF Preview Not Available</span>
                        </div>
                      @else
                        <div class="flex items-center gap-4 rounded-lg bg-gray-800 p-4">
                          <i class="fas {{ $iconClass }} text-2xl"></i>
                          <span class="text-sm text-gray-300">Preview Not Available</span>
                        </div>
                      @endif
                    </div>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
<style>
  .dropzone .dz-remove {
    display: inline-block;
    margin-top: 0.5rem;
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
    color: #ef4444;
    text-decoration: none;
    border-radius: 0.375rem;
  }

  .dropzone .dz-remove:hover {
    color: #dc2626;
    text-decoration: underline;
  }

  .select2-container--default .select2-selection--multiple {
    background-color: rgb(17 24 39) !important;
    border: 1px solid rgb(75 85 99) !important;
    border-radius: 0.375rem !important;
    min-height: 42px !important;
    padding: 4px !important;
  }

  /* Dropdown chevron */
  .select2-container--default .select2-selection--multiple .select2-selection__arrow {
    height: 42px !important;
    position: absolute !important;
    top: 0 !important;
    right: 0 !important;
    width: 25px !important;
  }

  .select2-container--default .select2-selection--multiple .select2-selection__arrow b {
    border: none !important;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='white'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E") !important;
    background-size: 16px !important;
    background-repeat: no-repeat !important;
    background-position: center !important;
    height: 16px !important;
    width: 16px !important;
    left: 50% !important;
    margin-left: -8px !important;
    margin-top: -8px !important;
    position: absolute !important;
    top: 50% !important;
  }

  /* Tag styling */
  .select2-container--default .select2-selection--multiple .select2-selection__choice {
    display: flex !important;
    align-items: center !important;
    background-color: rgb(31 41 55) !important;
    border: 1px solid rgb(75 85 99) !important;
    border-radius: 0.375rem !important;
    color: rgb(209 213 219) !important;
    padding: 2px 6px 2px 8px !important;
    margin: 4px !important;
    gap: 6px !important;
  }

  /* Tag text */
  .select2-container--default .select2-selection--multiple .select2-selection__choice__display {
    padding: 0 !important;
    margin: 0 !important;
    order: 1 !important;
    color: rgb(209 213 219) !important;
  }

  /* Remove button */
  .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
    position: relative !important;
    border: none !important;
    padding: 0 !important;
    margin: 0 !important;
    order: 2 !important;
    color: rgb(239 68 68) !important;
  }

  .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
    background: none !important;
    color: rgb(220 38 38) !important;
  }

  /* Dropdown styling */
  .select2-dropdown {
    background-color: rgb(17 24 39) !important;
    border: 1px solid rgb(75 85 99) !important;
    border-radius: 0.375rem !important;
  }

  /* Dropdown options */
  .select2-results__option {
    padding: 8px 12px !important;
    color: rgb(209 213 219) !important;
  }

  .select2-container--default .select2-results__option--highlighted[aria-selected] {
    background-color: rgb(31 41 55) !important;
    color: white !important;
  }

  /* Search field */
  .select2-container--default .select2-search--inline .select2-search__field {
    color: rgb(209 213 219) !important;
    background-color: transparent !important;
    margin-top: 0 !important;
  }

  /* Focus state */
  .select2-container--default.select2-container--focus .select2-selection--multiple {
    border-color: rgb(75 85 99) !important;
    outline: none !important;
  }
</style>

<script src="https://unpkg.com/heic2any"></script>
<script>
  document.addEventListener("DOMContentLoaded", function() {
    Dropzone.autoDiscover = false;

    // Initialize Select2
    $('#tags').select2({
      theme: 'default',
      allowClear: true,
      tags: true,
      tokenSeparators: [',', ' '],
      minimumResultsForSearch: Infinity,
      data: @json(array_map(function ($tag) {
              return ['id' => $tag, 'text' => $tag];
          }, $allTags)),
    }).val(@json($savedCategories)).trigger('change');

    // Initialize Dropzone
    const myDropzone = new Dropzone("#my-dropzone", {
      url: "{{ route('admin.dashboard.document.file.upload', ['dashboardType' => $dashboardType]) }}",
      method: 'POST',
      paramName: "file",
      maxFilesize: 25,
      acceptedFiles: ".pdf,.doc,.docx,.txt,.png,.jpg,.jpeg,.heic,.heif",
      addRemoveLinks: true,
      dictRemoveFile: "Remove file",
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Accept': 'application/json'
      },
      init: function() {
        // Show existing file if present
        const existingFile = "{{ $document->file_path }}";
        if (existingFile) {
          const mockFile = {
            name: "{{ basename($document->file_path) }}",
            size: 12345,
            type: "{{ mime_content_type(storage_path('app/public/' . $document->file_path)) }}",
            status: Dropzone.ADDED,
            accepted: true
          };

          this.emit("addedfile", mockFile);
          this.emit("complete", mockFile);

          @if (in_array(pathinfo($document->file_path, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif']))
            this.emit("thumbnail", mockFile, "{{ Storage::url($document->file_path) }}");
          @endif

          this.files.push(mockFile);
        }

        this.on("success", function(file, response) {
          document.getElementById("uploaded_file_path").value = response.path;
          file.serverPath = response.path;
        });

        this.on("error", function(file, errorMessage, xhr) {
          if (typeof showErrorNotification === 'function') {
            showErrorNotification(errorMessage);
          }
          console.error('Upload error:', errorMessage);
        });

        this.on("removedfile", function(file) {
          document.getElementById("uploaded_file_path").value = "";
          if (file.serverPath) {
            fetch(
              "{{ route('admin.dashboard.document.file.delete', ['dashboardType' => $dashboardType]) }}", {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/json',
                  'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                  path: file.serverPath
                })
              });
          }
        });
      }
    });

    // Update accepted formats text
    document.querySelector(".accepted-formats").textContent =
      "Accepted formats: " + myDropzone.options.acceptedFiles
      .split(",")
      .map(format => format.trim())
      .join(", ");
  });
</script>
