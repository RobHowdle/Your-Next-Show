<header>
  <h2 class="text-md font-heading font-medium text-white">
    {{ __('Your Venues') }}
  </h2>
</header>
<form method="POST" action="{{ route('venue.update', ['dashboardType' => $dashboardType, 'user' => $user->id]) }}">
  @csrf
  @method('PUT')
  <div class="group mb-6">
    <x-input-label-dark for="myVenues">Tell us about the venues you've worked with</x-input-label-dark>
    <x-textarea-input class="summernote" id="inHouseGear" name="inHouseGear"></x-textarea-input>
    @error('inHouseGear')
      <p class="yns_red mt-1 text-sm">{{ $message }}</p>
    @enderror
  </div>
  <div id="suggestions"
    style="display:none; position: absolute; background: white; border: 1px solid #ccc; z-index: 1000;"></div>


  <div class="flex items-center gap-4">
    <button type="submit"
      class="mt-8 rounded-lg border border-white bg-white px-4 py-2 font-heading font-bold text-black transition duration-150 ease-in-out hover:border-yns_yellow hover:text-yns_yellow">Save</button>
    @if (session('status') === 'profile-updated')
      <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
        class="text-sm text-gray-600 dark:text-gray-400">{{ __('Saved.') }}</p>
    @endif
  </div>
</form>
@push('scripts')
  <script>
    var inHouseGearContent = @json(old('inHouseGear', $inHouseGear));
    jQuery(document).ready(function() {
      initialiseSummernote("#inHouseGear", inHouseGearContent);
    });
  </script>
@endpush
