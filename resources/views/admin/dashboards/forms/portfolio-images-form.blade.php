<form method="POST" action="{{ route('portfolio.save', ['dashboardType' => $dashboardType, 'user' => $user->id]) }}"
  id="portfolioImagesForm">
  @csrf
  @method('PUT')

  <div class="group mb-6">
    <x-input-label-dark for="portfolio_link">Portfolio URL</x-input-label-dark>
    <x-text-input id="portfolio_link" name="portfolio_link" :value="old('portfolio_link', $profileData['portfolio_link'])" />
    @error('portfolio_link')
      <p class="yns_red mt-1 text-sm">{{ $message }}</p>
    @enderror
  </div>

  <input type="hidden" id="portfolio_image_path" name="portfolio_image_path">

  <div class="flex items-center gap-4">
    <button type="submit" id="portfolioSubmitButton"
      class="mt-8 rounded-lg border border-white bg-white px-4 py-2 font-heading font-bold text-black transition duration-150 ease-in-out hover:border-yns_yellow hover:text-yns_yellow">Save</button>
  </div>
</form>
<script>
  // Handle form submission
  jQuery("#portfolioImagesForm").on("submit", function(event) {
    event.preventDefault();

    // Synchronize hidden input with Dropzone state
    const portfolioImages = document.getElementById("portfolio_image_path").value;
    if (!portfolioImages || portfolioImages === "[]") {
      showFailureNotification("Please upload at least one portfolio image.");
      return;
    }

    var formData = new FormData(this);

    jQuery.ajax({
      url: jQuery(this).attr('action'),
      type: 'POST',
      headers: {
        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
      },
      data: formData,
      contentType: false,
      processData: false,
      success: function(response) {
        showSuccessNotification(response.message);
        setTimeout(() => {
          window.location.href = response.redirect_url;
        }, 2000);
      },
      error: function(xhr) {
        const errors = xhr.responseJSON.errors || {};
        const errorMessages = Object.values(errors).flat().join("\n");
        showFailureNotification(errorMessages);
      }
    });
  });
</script>
