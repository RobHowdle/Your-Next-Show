<header>
  <h2 class="text-md font-heading font-medium text-white">
    {{ __('Tell us about you... Where you started, why you started, what you do
                                                                  etc') }}
  </h2>
</header>
<form method="POST" action="{{ route('photographer.update', ['dashboardType' => $dashboardType, 'user' => $user->id]) }}"
  class="space-y-6">
  @csrf
  @method('PUT')
  <div class="group mb-6">
    <x-textarea-input id="description" name="description">{{ old('description', $description ?? '') }}</x-textarea-input>
    {{-- <x-textarea-input class="summernote" id="about" name="about"></x-textarea-input> --}}
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
@push('script')
  <script>
    //   $(document).ready(function() {
    //     try {
    //       var aboutContent = @json(old('about', $about ?? ''));
    //       $('.summernote').summernote({
    //         height: 300,
    //         toolbar: [
    //           ['style', ['style']],
    //           ['font', ['bold', 'underline', 'clear']],
    //           ['color', ['color']],
    //           ['para', ['ul', 'ol', 'paragraph']],
    //           ['table', ['table']],
    //           ['insert', ['link', 'picture', 'video']],
    //           ['view', ['fullscreen', 'codeview', 'help']]
    //         ]
    //       });

    //       if (aboutContent) {
    //         $('.summernote').summernote('code', aboutContent);
    //       }
    //     } catch (error) {
    //       console.error('Summernote initialization error:', error);
    //     }
    //   });
    // 
  </script>
@endpush
