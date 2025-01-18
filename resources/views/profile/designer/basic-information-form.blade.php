<header>
  <h2 class="text-md font-heading font-medium text-white">
    {{ __('Let\'s get the basics out of the way!') }}
  </h2>
</header>
<form method="POST" action="{{ route('designer.update', ['dashboardType' => $dashboardType, 'user' => $user]) }}"
  class="grid grid-cols-3 gap-x-8 gap-y-8 space-y-6" enctype="multipart/form-data">
  @csrf
  @method('PUT')
  <div class="col-start-1 col-end-2">
    <div class="group mb-6">
      <x-input-label-dark for="name">Designer Company Name:</x-input-label-dark>
      <x-text-input id="name" name="name" value="{{ old('name', $name) }}"></x-text-input>
      @error('name')
        <p class="yns_red mt-1 text-sm">{{ $message }}</p>
      @enderror
    </div>

    <div class="group mb-6">
      <x-input-label-dark for="designerName">Contact Name:</x-input-label-dark>
      <x-text-input id="contact_name" name="contact_name"
        value="{{ old('contact_name', $contact_name) }}"></x-text-input>
      @error('contact_name')
        <p class="yns_red mt-1 text-sm">{{ $message }}</p>
      @enderror
    </div>

    <div class="group mb-6">
      <x-google-address-picker :postalTown="old('designerPostalTown', $designerData['designerPostalTown'] ?? '')" data-id="2" id="location" name="location" label="Location"
        placeholder="Enter an address" :value="old('designerLocation', $designerData['designerLocation'] ?? '')" :latitude="old('designerLat', $designerData['designerLat'] ?? '')" :longitude="old('designerLong', $designerData['designerLong'] ?? '')" />
    </div>

    <div class="group mb-6">
      <x-input-label-dark for="email">Email:</x-input-label-dark>
      <x-text-input id="contact_email" name="contact_email"
        value="{{ old('contact_email', $designerData['contact_email']) }}"></x-text-input>
      @error('contact_email')
        <p class="yns_red mt-1 text-sm">{{ $message }}</p>
      @enderror
    </div>

    <div class="group mb-6">
      <x-input-label-dark for="phone">Contact Phone:</x-input-label-dark>
      <x-text-input id="contact_number" name="contact_number"
        value="{{ old('contact_number', $designerData['contact_number']) }}"></x-text-input>
      @error('contact_number')
        <p class="yns_red mt-1 text-sm">{{ $message }}</p>
      @enderror
    </div>
  </div>

  @if (is_array($platformsToCheck))
    <div class="col-start-2 col-end-3">
      @foreach ($platformsToCheck as $platform)
        <div class="group mb-6">
          <x-input-label-dark for="{{ $platform }}">{{ ucfirst($platform) }}:</x-input-label-dark>

          @php
            // Ensure the links for the platform are correctly handled as an array
            $links =
                isset($platforms[$platform]) && is_array($platforms[$platform])
                    ? $platforms[$platform]
                    : ($platforms[$platform]
                        ? [$platforms[$platform]]
                        : []);
          @endphp

          @foreach ($links as $index => $link)
            <x-text-input id="{{ $platform }}-{{ $index }}" name="contact_links[{{ $platform }}][]"
              value="{{ old('contact_links.' . $platform . '.' . $index, $link) }}">
            </x-text-input>
          @endforeach

          <!-- If no links exist, provide a way to add one -->
          @if (empty($links))
            <x-text-input id="{{ $platform }}-new" name="contact_links[{{ $platform }}][]"
              value="{{ old('contact_links.' . $platform . '.new', '') }}"
              placeholder="Add a {{ ucfirst($platform) }} link">
            </x-text-input>
          @endif

          @error('contact_links.' . $platform . '.*')
            <p class="yns_red mt-1 text-sm">{{ $message }}</p>
          @enderror
        </div>
      @endforeach
    </div>
  @endif

  <div class="col-start-3 col-end-4">
    <div class="group mb-6 flex flex-col items-center">
      <x-input-label-dark for="logo" class="text-left">Logo:</x-input-label-dark>
      <x-input-file id="logo" name="logo" onchange="previewLogo(event)"></x-input-file>

      <!-- Preview Image -->
      <img id="logo-preview" src="{{ $logo }}" alt="Logo Preview" class="mt-4 h-80 w-80 object-cover"
        style="display: {{ $logo ? 'block' : 'none' }};">

      @error('logo')
        <p class="yns_red mt-1 text-sm">{{ $message }}</p>
      @enderror
    </div>
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
  function previewLogo(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('logo-preview');

    if (file) {
      const reader = new FileReader();

      reader.onload = function(e) {
        preview.src = e.target.result; // Set the preview to the file data
        preview.style.display = 'block'; // Show the preview image
        console.log('Preview URL:', e.target.result); // Log the preview URL
      };

      reader.readAsDataURL(file); // Read the file as a data URL
    }
  }
</script>
