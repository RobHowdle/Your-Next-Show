<!-- Make sure jQuery is loaded -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<div class="rounded-lg bg-gray-800/50 p-6 backdrop-blur-sm">
  <header class="mb-6 border-b border-gray-700 pb-4">
    <h2 class="font-heading text-lg font-medium text-white">
      {{ __('Stream Links') }}
    </h2>
    <p class="mt-1 text-sm text-gray-400">
      {{ __('Add your music streaming platform links and select a default.') }}
    </p>
  </header>

  <form id="streamLinksForm" method="POST" action="javascript:void(0);" class="space-y-6">
    @csrf
    @method('PUT')

    <div class="rounded-lg bg-black/20 p-6">
      <div class="grid gap-6">
        <!-- Spotify -->
        <div class="flex items-center gap-4">
          <div class="flex-1">
            <x-input-label-dark for="stream_links_spotify">Spotify</x-input-label-dark>
            <x-text-input id="stream_links_spotify" name="stream_links[spotify]" type="url"
              class="mt-1 block w-full" placeholder="https://open.spotify.com/artist/..."
              value="{{ old('stream_links.spotify', $profileData['streamLinks']['spotify'] ?? '') }}" />
          </div>
          <div class="flex items-center">
            <input type="radio" id="default_spotify" name="default_platform" value="spotify"
              class="h-4 w-4 border-gray-600 bg-gray-800 text-yns_yellow focus:ring-2 focus:ring-yns_yellow"
              {{ old('default_platform', $profileData['default_platform'] ?? '') === 'spotify' ? 'checked' : '' }}>
            <label for="default_spotify" class="ml-2 text-sm text-gray-400">Default</label>
          </div>
        </div>

        <!-- Apple Music -->
        <div class="flex items-center gap-4">
          <div class="flex-1">
            <x-input-label-dark for="stream_links_apple_music">Apple Music</x-input-label-dark>
            <x-text-input id="stream_links_apple_music" name="stream_links[apple_music]" type="url"
              class="mt-1 block w-full" placeholder="https://music.apple.com/artist/..."
              value="{{ old('stream_links.apple_music', $profileData['streamLinks']['apple_music'] ?? '') }}" />
          </div>
          <div class="flex items-center">
            <input type="radio" id="default_apple_music" name="default_platform" value="apple_music"
              class="h-4 w-4 border-gray-600 bg-gray-800 text-yns_yellow focus:ring-2 focus:ring-yns_yellow"
              {{ old('default_platform', $profileData['default_platform'] ?? '') === 'apple_music' ? 'checked' : '' }}>
            <label for="default_apple_music" class="ml-2 text-sm text-gray-400">Default</label>
          </div>
        </div>

        <!-- YouTube Music -->
        <div class="flex items-center gap-4">
          <div class="flex-1">
            <x-input-label-dark for="stream_links_youtube_music">YouTube Music</x-input-label-dark>
            <x-text-input id="stream_links_youtube_music" name="stream_links[youtube_music]" type="url"
              class="mt-1 block w-full" placeholder="https://music.youtube.com/channel/..."
              value="{{ old('stream_links.youtube_music', $profileData['streamLinks']['youtube_music'] ?? '') }}" />
          </div>
          <div class="flex items-center">
            <input type="radio" id="default_youtube_music" name="default_platform" value="youtube_music"
              class="h-4 w-4 border-gray-600 bg-gray-800 text-yns_yellow focus:ring-2 focus:ring-yns_yellow"
              {{ old('default_platform', $profileData['default_platform'] ?? '') === 'youtube_music' ? 'checked' : '' }}>
            <label for="default_youtube_music" class="ml-2 text-sm text-gray-400">Default</label>
          </div>
        </div>

        <!-- Amazon Music -->
        <div class="flex items-center gap-4">
          <div class="flex-1">
            <x-input-label-dark for="stream_links_amazon_music">Amazon Music</x-input-label-dark>
            <x-text-input id="stream_links_amazon_music" name="stream_links[amazon_music]" type="url"
              class="mt-1 block w-full" placeholder="https://music.amazon.com/artists/..."
              value="{{ old('stream_links.amazon_music', $profileData['streamLinks']['amazon_music'] ?? '') }}" />
          </div>
          <div class="flex items-center">
            <input type="radio" id="default_amazon_music" name="default_platform" value="amazon_music"
              class="h-4 w-4 border-gray-600 bg-gray-800 text-yns_yellow focus:ring-2 focus:ring-yns_yellow"
              {{ old('default_platform', $profileData['default_platform'] ?? '') === 'amazon_music' ? 'checked' : '' }}>
            <label for="default_amazon_music" class="ml-2 text-sm text-gray-400">Default</label>
          </div>
        </div>

        <!-- Bandcamp -->
        <div class="flex items-center gap-4">
          <div class="flex-1">
            <x-input-label-dark for="stream_links_bandcamp">Bandcamp</x-input-label-dark>
            <x-text-input id="stream_links_bandcamp" name="stream_links[bandcamp]" type="url"
              class="mt-1 block w-full" placeholder="https://yourbandname.bandcamp.com"
              value="{{ old('stream_links.bandcamp', $profileData['streamLinks']['bandcamp'] ?? '') }}" />
          </div>
          <div class="flex items-center">
            <input type="radio" id="default_bandcamp" name="default_platform" value="bandcamp"
              class="h-4 w-4 border-gray-600 bg-gray-800 text-yns_yellow focus:ring-2 focus:ring-yns_yellow"
              {{ old('default_platform', $profileData['default_platform'] ?? '') === 'bandcamp' ? 'checked' : '' }}>
            <label for="default_bandcamp" class="ml-2 text-sm text-gray-400">Default</label>
          </div>
        </div>

        <!-- SoundCloud -->
        <div class="flex items-center gap-4">
          <div class="flex-1">
            <x-input-label-dark for="stream_links_soundcloud">SoundCloud</x-input-label-dark>
            <x-text-input id="stream_links_soundcloud" name="stream_links[soundcloud]" type="url"
              class="mt-1 block w-full" placeholder="https://soundcloud.com/yourbandname"
              value="{{ old('stream_links.soundcloud', $profileData['streamLinks']['soundcloud'] ?? '') }}" />
          </div>
          <div class="flex items-center">
            <input type="radio" id="default_soundcloud" name="default_platform" value="soundcloud"
              class="h-4 w-4 border-gray-600 bg-gray-800 text-yns_yellow focus:ring-2 focus:ring-yns_yellow"
              {{ old('default_platform', $profileData['default_platform'] ?? '') === 'soundcloud' ? 'checked' : '' }}>
            <label for="default_soundcloud" class="ml-2 text-sm text-gray-400">Default</label>
          </div>
        </div>
      </div>
    </div>

    <div class="flex items-center justify-end gap-4 border-t border-gray-700 pt-6">
      <button type="submit"
        class="rounded-lg border border-yns_yellow bg-yns_yellow px-4 py-2 font-heading font-bold text-black transition hover:bg-yns_yellow/90">
        Save Stream Links
      </button>
      @if (session('status') === 'profile-updated')
        <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
          class="text-sm text-gray-400">
          {{ __('Saved.') }}
        </p>
      @endif
    </div>
  </form>
</div>

<script>
  $(document).ready(function() {
    console.log('Stream Links: Document ready - Setting up form handler');

    // Make sure the form exists before trying to attach event handler
    if ($('#streamLinksForm').length) {
      $('#streamLinksForm').on('submit', function(e) {
        // Prevent the default form submission
        e.preventDefault();
        console.log('Stream Links: Form submitted via jQuery');

        const form = $(this);
        const formData = new FormData(this);

        // Log the data being sent
        console.log('Stream Links: Sending data:');
        for (let [key, value] of formData.entries()) {
          console.log(`  ${key}: ${value}`);
        }

        // Make sure we use the right URL
        $.ajax({
          url: '{{ route('artist.update', ['dashboardType' => $dashboardType, 'user' => $user->id]) }}',
          method: 'POST',
          data: formData,
          processData: false,
          contentType: false,
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'X-Requested-With': 'XMLHttpRequest'
          },
          success: function(response) {
            console.log('Stream Links: Success response:', response);
            if (response.success) {
              showSuccessNotification(response.message);
              setTimeout(() => {
                window.location.href = response.redirect;
              }, 2000);
            } else {
              showFailureNotification('Failed to update stream links');
            }
          },
          error: function(xhr, status, error) {
            console.error('Stream Links: Error:', xhr.responseText);
            showFailureNotification('Error updating stream links');
          }
        });

        return false; // Extra safety to prevent form submission
      });
      console.log('Stream Links: Event handler attached to form');
    } else {
      console.error('Stream Links: Form with ID #streamLinksForm not found');
    }
  });
</script>
