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
      <div class="mb-4">
        <x-multi-select id="tags" name="tags[]" :options="collect($tags)
            ->mapWithKeys(function ($tag) {
                return [$tag => $tag];
            })
            ->toArray()" />
      </div>
      @error('tags')
        <p class="yns_red mt-1 text-sm">{{ $message }}</p>
      @enderror
    </div>

    <input type="hidden" id="uploaded_file_path" name="uploaded_file_path">

    <button type="submit"
      class="mt-8 flex w-full justify-center rounded-lg border border-yns_cyan bg-yns_cyan px-4 py-2 font-heading text-xl text-black transition duration-150 ease-in-out hover:border-yns_yellow hover:text-yns_yellow">Upload
      Document</button>
  </div>
</form>

<script>
  document.addEventListener("DOMContentLoaded", function() {
    jQuery("#tags").select2({
      tags: true,
      tokenSeparators: [","]
    });
  });

  // Handle form submission
  jQuery("#document-form").on("submit", function(event) {
    event.preventDefault();

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
        var errors = xhr.responseJSON.errors;
        var errorMessages = Object.values(errors).flat().join("\n");
        showFailureNotification(errorMessages);
      }
    });
  });
</script>
