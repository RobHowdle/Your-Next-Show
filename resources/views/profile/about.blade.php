<header>
  <h2 class="text-md font-heading font-medium text-white">
    {{ __('About You') }}
  </h2>
</header>
<form id="saveDescription" method="POST">
  @csrf
  @method('PUT')
  <div class="group mb-6">
    <x-input-label-dark for="about">Tell us about you... where you started, why you started, what you do
      etc</x-input-label-dark>
    <x-textarea-input id="description"
      name="description">{{ old('description', $profileData['description'] ?? '') }}</x-textarea-input>
    @error('description')
      <p class="yns_red mt-1 text-sm">{{ $message }}</p>
    @enderror
  </div>

  <div class="flex items-center gap-4">
    <button type="submit"
      class="mt-8 rounded-lg border border-white bg-white px-4 py-2 font-heading font-bold text-black transition duration-150 ease-in-out hover:border-yns_yellow hover:text-yns_yellow">Save</button>
    @if (session('status') === 'profile-updated')
      <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
        class="text-sm text-gray-600 dark:text-gray-400">{{ __('Saved.') }}</p>
    @endif
  </div>
</form>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    if (typeof jQuery !== 'undefined') {
      const aboutContent = @json(old('description', $profileData['description'] ?? ''));
      $('#description').summernote({
        placeholder: 'Tell us about you...',
        tabsize: 2,
        height: 300,
        toolbar: [
          ['style', ['style']],
          ['font', ['bold', 'underline', 'clear']],
          ['color', ['color']],
          ['para', ['ul', 'ol', 'paragraph']],
          ['insert', ['link']],
          ['view', ['fullscreen', 'codeview', 'help']]
        ],
        callbacks: {
          onInit: function() {
            if (aboutContent) {
              $('#description').summernote('code', aboutContent);
            }
          }
        }
      });
    } else {
      console.error('jQuery is not loaded');
    }

    $('#saveDescription').on('submit', function(e) {
      e.preventDefault();

      const form = $(this);
      const formData = new FormData(this);

      $.ajax({
        url: '{{ route($dashboardType . '.update', ['dashboardType' => $dashboardType, 'user' => $user]) }}',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
          console.log(response);
          if (response.success) {
            showSuccessNotification(response.message);
            setTimeout(() => {
              window.location.href = response.redirect;
            }, 2000);
          } else {
            alert('Failed to update profile');
          }
        },
        error: function(xhr, status, error) {
          const response = xhr.responseJSON;
          showFailureNotification(response);
        }
      });
    });
  });
</script>
