<header>
  <h2 class="text-md font-heading font-medium text-white">
    {{ __('Upload a max of 6 images to showcase your work, add a URL to your main portfolio') }} </h2>
</header>

<div class="mb-8 grid grid-cols-3 items-center gap-4 space-y-6">
  @foreach ($waterMarkedPortfolioImages as $image)
    <div class="overflow-hidden rounded shadow-md">
      <img src="{{ asset($image) }}" alt="Portfolio Image" class="h-auto w-full">
    </div>
  @endforeach
</div>

@include('admin.dashboards.forms.portfolio-images-form', [
    'dashboardType' => $dashboardType,
    'waterMarkedPortfolioImages' => $dashboardData['waterMarkedPortfolioImages'],
])

<!-- Dropzone form for file upload -->
<form action="{{ route('portfolio.upload', ['dashboardType' => $dashboardType]) }}"
  class="dropzone mt-8 border border-white bg-transparent" id="my-dropzone">
  @csrf
  <div class="dz-message" data-dz-message>
    <span>Drag and drop files here or click to upload</span>
  </div>
  <input type="hidden" id="portfolio_image_path" name="portfolio_image_path">
  <input type="hidden" name="serviceable_id" value="{{ $dashboardData['serviceableId'] }}">
  <input type="hidden" name="serviceable_type" value="{{ $dashboardData['serviceableType'] }}">
</form>

<script>
  // Dropzone configuration
  Dropzone.autoDiscover = false;

  document.addEventListener("DOMContentLoaded", function() {
    const myDropzone = new Dropzone("#my-dropzone", {
      paramName: "file", // The name that will be used to transfer the file
      maxFilesize: 2, // MB
      acceptedFiles: ".jpeg,.png,.jpg,.gif",
      addRemoveLinks: true, // Allows files to be removed
      init: function() {
        let uploadedFilePaths = []; // Array to hold the file paths

        // Handle successful uploads
        this.on("success", function(file, response) {
          uploadedFilePaths.push(response.path); // Add path to array
          document.getElementById("portfolio_image_path").value = JSON.stringify(
            uploadedFilePaths); // Update hidden field
        });

        // Handle file removal
        this.on("removedfile", function(file) {
          const path = file.previewElement.dataset.path; // Store file path in dataset when uploading
          const index = uploadedFilePaths.indexOf(path);
          if (index >= 0) {
            uploadedFilePaths.splice(index, 1); // Remove path
            document.getElementById("portfolio_image_path").value = JSON.stringify(uploadedFilePaths);
          }
        });
      }

    });
  });
</script>
