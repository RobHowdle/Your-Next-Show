<x-app-layout :dashboardType="$dashboardType" :modules="$modules">
  <x-slot name="header">
    <x-sub-nav :userId="$userId" />
  </x-slot>

  <div class="relative min-h-screen">
    <div class="relative mx-auto w-full max-w-screen-2xl py-8">
      <div class="px-4">
        <div class="relative mb-8">
          <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-gray-900 via-black to-gray-900 opacity-75"></div>

          <div class="relative px-4 py-6 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-7xl">
              <h1 class="font-heading text-3xl font-bold text-white md:text-4xl">
                New Event
              </h1>
              <p class="mt-2 text-gray-400">Upload and manage your documents, logos, contracts etc</p>
            </div>
          </div>
        </div>

        <div class="rounded-xl border border-gray-800 bg-gray-900/60 backdrop-blur-md backdrop-saturate-150">
          <div class="grid gap-8 p-6 lg:grid-cols-2 lg:p-8">
            <div class="space-y-4">
              <div class="rounded-lg border border-gray-800 bg-black/50 p-6">
                {{-- Document Details --}}
                <form id="document-form" method="POST"
                  action={{ route('admin.dashboard.store-document', ['dashboardType' => $dashboardType]) }}
                  enctype="multipart/form-data">
                  @csrf
                  <div class="mb-4 grid grid-cols-1 gap-x-8 gap-y-4">
                    <div class="group">
                      <x-input-label-dark>Document Title</x-input-label-dark>
                      <x-text-input id="title" name="title" required></x-text-input>
                      @error('title')
                        <p class="yns_red mt-1 text-sm">{{ $message }}</p>
                      @enderror
                    </div>

                    <div class="group">
                      <x-input-label-dark>Description</x-input-label-dark>
                      <x-textarea-input class="w-full" id="description" name="description"></x-textarea-input>
                      @error('description')
                        <p class="yns_red mt-1 text-sm">{{ $message }}</p>
                      @enderror
                    </div>

                    <div class="group">
                      <x-input-label-dark>Tags</x-input-label-dark>
                      <x-multi-select name="tags[]" id="tags" :options="$tags" class="w-full" />
                      @error('tags')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                      @enderror
                    </div>

                    <div class="group mt-2">
                      <x-input-label-dark>User Id:</x-input-label-dark>
                      <x-text-input name="serviceable_id" value="{{ $serviceableId }}" />
                    </div>
                    <div class="group mt-2">
                      <x-input-label-dark>Service Type:</x-input-label-dark>
                      <x-text-input name="serviceable_type" value="{{ $serviceableType }}" />
                    </div>
                    <div class="group mt-2">
                      <x-input-label-dark>Local Path:</x-input-label-dark>
                      <x-text-input name="uploaded_file_path" id="uploaded_file_path" />
                    </div>

                    <button type="submit"
                      class="mt-8 flex w-full justify-center rounded-lg border border-yns_cyan bg-yns_cyan px-4 py-2 font-heading text-xl text-black transition duration-150 ease-in-out hover:border-yns_yellow hover:text-yns_yellow">Upload
                      Document</button>
                  </div>
                </form>
              </div>
            </div>
            {{-- Document Upload --}}
            <div class="space-y-4">
              <div class="rounded-lg border border-gray-800 bg-black/50 p-6">
                <h2 class="mb-6 font-heading text-xl font-bold text-white">Upload Document</h2>
                <div class="space-y-4">
                  <div class="dropzone-container rounded-lg border-2 border-dashed border-gray-700 bg-black/30 p-6">
                    <form
                      action="{{ route('admin.dashboard.document.file.upload', ['dashboardType' => $dashboardType]) }}"
                      class="dropzone bg-transparent" id="my-dropzone">
                      <div class="dz-message" data-dz-message>
                        <i class="fa-solid fa-arrow-up-from-bracket"></i>
                        <span>Drag and drop files here or click to upload</span>
                        <span class="accepted-formats mt-2 text-sm text-gray-500"></span>
                      </div>
                      <div class="group mt-2">
                        <x-input-label-dark>User Id:</x-input-label-dark>
                        <x-text-input name="serviceable_id" value="{{ $serviceableId }}" />
                      </div>
                      <div class="group mt-2">
                        <x-input-label-dark>Service Type:</x-input-label-dark>
                        <x-text-input name="serviceable_type" value="{{ $serviceableType }}" />
                      </div>
                      <div class="group mt-2">
                        <x-input-label-dark>Local Path:</x-input-label-dark>
                        <x-text-input name="uploaded_file_path" id="uploaded_file_path" />
                      </div>
                    </form>
                  </div>

                  {{-- Document Preview --}}
                  <div id="preview-container" class="mt-6 rounded-lg border border-gray-800 bg-black/30 p-4">
                    <h3 class="mb-4 text-sm font-medium text-white">Preview</h3>
                    <div class="dz-preview dz-file-preview">
                      <div class="dz-details space-y-2">
                        <div class="dz-filename text-sm text-gray-400"><span data-dz-name></span></div>
                      </div>
                      <div class="mt-2">
                        <div class="dz-progress h-2 rounded-full bg-gray-700">
                          <span class="dz-upload h-full rounded-full bg-primary" data-dz-uploadprogress></span>
                        </div>
                      </div>
                      <div class="dz-error-message text-destructive mt-2 text-sm"><span data-dz-errormessage></span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
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
  // Dropzone
  document.addEventListener("DOMContentLoaded", function() {
    Dropzone.autoDiscover = false;
    const tags = document.querySelector("#tags");

    // Initialize Select2 for the tags input
    $('#tags').select2({
      theme: 'default',
      allowClear: true,
      tags: true,
      tokenSeparators: [',', ' '],
      minimumResultsForSearch: Infinity, // Hide search when not needed
    });

    if (Dropzone.instances.length) {
      Dropzone.instances.forEach((dropzone) => dropzone.destroy());
    }

    const myDropzone = new Dropzone("#my-dropzone", {
      paramName: "file", // The name that will be used to transfer the file
      maxFilesize: 25, // MB
      acceptedFiles: ".pdf,.doc,.docx,.txt,.png,.jpg,.jpeg,.heic,.heif", // Add image types for preview
      addRemoveLinks: true, // Enable file removal
      dictRemoveFile: "Remove file", // Text for remove file link
      headers: {
        "X-CSRF-TOKEN": document
          .querySelector('meta[name="csrf-token"]')
          .getAttribute("content"),
      },
      init: function() {
        this.on("addedfile", function(file) {
          // Clear any existing previews in the preview container
          const previewContainer =
            document.getElementById("preview-container");
          previewContainer.innerHTML = ""; // Clear existing preview

          // Create a new preview element
          const previewElement = document.createElement("div");
          previewElement.className = "dz-file-preview";

          const details = document.createElement("div");
          details.className = "dz-details";

          const filename = document.createElement("div");
          filename.className = "dz-filename";
          filename.innerHTML = `<span data-dz-name>${file.name}</span>`;

          const size = document.createElement("div");
          size.className = "dz-size";
          size.innerHTML = `<span data-dz-size>${formatSize(
                    file.size
                )}</span>`; // Use the custom formatSize function

          details.appendChild(filename);
          details.appendChild(size);
          previewElement.appendChild(details);

          // Preview handling for different file types
          if (file.type.startsWith("image/") ||
            file.type === "image/heic" ||
            file.type === "image/heif") {
            const img = document.createElement("img");
            img.setAttribute("data-dz-thumbnail", "");
            // For HEIC files, convert them to JPEG first
            if (file.type === "image/heic" || file.type === "image/heif") {
              heic2any({
                blob: file,
                toType: "image/jpeg",
                quality: 0.8
              }).then(function(conversionResult) {
                img.src = URL.createObjectURL(conversionResult);
              }).catch(function(e) {
                console.error(e);
                img.src = 'path/to/fallback/image.png';
              });
            } else {
              img.src = URL.createObjectURL(file); // Create a local URL for the image
            }
            previewElement.appendChild(img);
          } else if (file.type === "application/pdf") {
            const viewer = document.createElement("iframe");
            viewer.src = URL.createObjectURL(file);
            viewer.width = "100%";
            viewer.height = "auto"; // Adjust height as needed
            previewElement.appendChild(viewer);
          } else if (
            file.type === "application/msword" ||
            file.type ===
            "application/vnd.openxmlformats-officedocument.wordprocessingml.document"
          ) {
            // Handle Word documents
            const wordMessage = document.createElement("div");
            wordMessage.innerText =
              "Preview not available for Word documents.";
            previewElement.appendChild(wordMessage);
          } else {
            // Handle other file types
            const otherMessage = document.createElement("div");
            otherMessage.innerText =
              "Preview not available for this file type.";
            previewElement.appendChild(otherMessage);
          }

          // Append the preview element to the preview container
          previewContainer.appendChild(previewElement);
        });

        this.on("removedfile", function(file) {
          // Clear the hidden input when file is removed
          document.getElementById("uploaded_file_path").value = "";

          const previewContainer = document.getElementById("preview-container");
          previewContainer.innerHTML = `
                <h3 class="mb-4 text-sm font-medium text-white">Preview</h3>
                <div class="dz-preview dz-file-preview">
                    <div class="dz-details space-y-2">
                        <div class="dz-filename text-sm text-gray-400">No file selected</div>
                    </div>
                </div>
            `;

          // If you need to also remove the file from server
          const filePath = file.serverPath; // Assuming you stored the server path
          if (filePath) {
            $.ajax({
              url: "{{ route('admin.dashboard.document.file.delete', ['dashboardType' => $dashboardType]) }}",
              type: 'POST',
              data: {
                path: filePath,
                _token: '{{ csrf_token() }}'
              },
              success: function(response) {
                if (typeof showSuccessNotification === 'function') {
                  showSuccessNotification('File removed successfully');
                }
              },
              error: function(error) {
                if (typeof showErrorNotification === 'function') {
                  showErrorNotification('Error removing file');
                }
              }
            });
          }
        });

        this.on("success", function(file, response) {
          // Update both file path inputs
          const filePathInputs = document.querySelectorAll('input[name="uploaded_file_path"]');
          filePathInputs.forEach(input => {
            input.value = response.path;
          });

          // Store the path on the file object for removal later
          file.serverPath = response.path;
        });

        this.on("error", function(file, errorMessage, xhr) {
          console.error('Upload error:', errorMessage);
          if (xhr) {
            console.error('Server response:', xhr.responseText);
          }
          // Show error notification
          if (typeof showErrorNotification === 'function') {
            showErrorNotification(errorMessage);
          }
        });
        this.on("removedfile", function(file) {});
      },
    });

    document.querySelector(".accepted-formats").innerText =
      "Accepted formats: " +
      myDropzone.options.acceptedFiles
      .split(",")
      .map((format) => format.trim())
      .join(", ");

    // Custom function to format file sizes
    function formatSize(bytes) {
      const sizes = ["Bytes", "KB", "MB", "GB"];
      if (bytes === 0) return "0 Byte";
      const i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
      return Math.round(bytes / Math.pow(1024, i), 2) + " " + sizes[i];
    }

    // Handle form submission
    const form = document.getElementById('document-form');
    form.addEventListener('submit', async function(e) {
      e.preventDefault(); // Prevent default form submission

      // Check if file was uploaded
      const uploadedFilePath = document.querySelector('input[name="uploaded_file_path"]').value;
      if (!uploadedFilePath) {
        if (typeof showErrorNotification === 'function') {
          showErrorNotification('Please upload a file first');
        }
        return false;
      }

      // Get form data
      const formData = new FormData(form);

      // Get tags as an array and stringify them
      const tags = $('#tags').select2('data').map(tag => tag.text.trim());
      formData.set('tags', JSON.stringify(tags)); // Send as JSON string

      try {
        const response = await fetch(form.action, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
          },
          body: formData,
          credentials: 'same-origin'
        });

        const data = await response.json();

        if (data.success) {
          if (typeof showSuccessNotification === 'function') {
            showSuccessNotification(data.message);
          }
          window.location.href = data.redirect_url;
        } else {
          throw new Error(data.message || 'Error uploading document');
        }
      } catch (error) {
        if (typeof showErrorNotification === 'function') {
          showErrorNotification(error.message);
        }
        console.error('Error:', error);
      }
    });
  });
</script>
