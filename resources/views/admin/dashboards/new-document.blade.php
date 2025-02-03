<x-app-layout :dashboardType="$dashboardType" :modules="$modules">
  <x-slot name="header">
    <x-sub-nav :userId="$userId" />
  </x-slot>

  <div class="mx-auto w-full max-w-screen-2xl py-16">
    <div class="relative mb-8 shadow-md sm:rounded-lg">
      <div class="grid grid-cols-[1.75fr_1.25fr] rounded-lg border border-white">
        <div class="rounded-l-lg border-r border-r-white bg-yns_dark_gray px-8 py-8">
          <p class="mb-10 text-4xl font-bold text-white">New Document</p>
          @include('admin.dashboards.forms.new-document-form', [
              'serviceableId' => $serviceableId,
              'service' => $service,
              'serviceableType' => $serviceableType,
              'dashboardType' => $dashboardType,
          ])
          <div class="dropzone-container rounded-lg border border-yns_red bg-yns_dark_blue">
            <form action="{{ route('admin.dashboard.document.file.upload', ['dashboardType' => $dashboardType]) }}"
              class="dropzone bg-transparent" id="my-dropzone">
              <div class="dz-message" data-dz-message>
                <span>Drag and drop files here or click to upload</span>
              </div>
              <x-text-input name="serviceable_id" value="{{ $serviceableId }}" />
              <x-text-input name="serviceable_type" value="{{ $serviceableType }}" />
              <x-text-input id="uploaded_file_path" name="uploaded_file_path" />
            </form>
          </div>
        </div>

        <div class="bg-yns_dark_blue px-8 py-8">
          <p class="mb-6 text-4xl font-bold text-white">Preview</p>
          <div id="preview-container" class="dz-preview dz-file-preview">
            <div class="dz-details">
              <div class="dz-filename"><span data-dz-name></span></div>
              <img data-dz-thumbnail />
            </div>
            <div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress></span></div>
            <div class="dz-error-message"><span data-dz-errormessage></span></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
<script>
  // Dropzone
  document.addEventListener("DOMContentLoaded", function() {
    Dropzone.autoDiscover = false;

    if (Dropzone.instances.length) {
      Dropzone.instances.forEach((dropzone) => dropzone.destroy());
    }

    const myDropzone = new Dropzone("#my-dropzone", {
      paramName: "file", // The name that will be used to transfer the file
      maxFilesize: 2, // MB
      acceptedFiles: ".pdf,.doc,.docx,.ppt,.pptx,.txt,.png,.jpg,.jpeg", // Add image types for preview
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
          if (file.type.startsWith("image/")) {
            const img = document.createElement("img");
            img.setAttribute("data-dz-thumbnail", "");
            img.src = URL.createObjectURL(file); // Create a local URL for the image
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

        this.on("success", function(file, response) {
          let filepath = document.getElementById("uploaded_file_path").value =
            response.path;
        });

        this.on("error", function(file, errorMessage) {
          if (file.size > this.options.maxFilesize * 1024 * 1024) {
            errorMessage =
              `File "${file.name}" is too large. Maximum allowed size is ${this.options.maxFilesize}MB.`;
          } else if (!this.options.acceptedFiles.includes(file.type)) {
            errorMessage = `File "${file.name}" not supported.`;
          }

          showFailureNotification(errorMessage);
          this.removeFile(file);
        });
        this.on("removedfile", function(file) {});
      },
    });

    // Custom function to format file sizes
    function formatSize(bytes) {
      const sizes = ["Bytes", "KB", "MB", "GB"];
      if (bytes === 0) return "0 Byte";
      const i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
      return Math.round(bytes / Math.pow(1024, i), 2) + " " + sizes[i];
    }
  });
</script>
