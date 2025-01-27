<form method="POST" id="stream-links-form" class="grid grid-cols-3 gap-x-8 gap-y-8">
  @csrf
  @method('PUT')

  @if (!empty($profileData['streamPlatformsToCheck']))
    <div class="col-start-1 col-end-2">
      @foreach ($profileData['streamPlatformsToCheck'] as $streamPlatform)
        <div class="group mb-6">
          <div class="flex items-center justify-between">
            <x-input-label-dark for="{{ $streamPlatform }}">
              {{ ucfirst($streamPlatform) }}:
            </x-input-label-dark>

            <div class="flex items-center">
              <input type="checkbox" id="{{ $streamPlatform }}-default" name="default_platform"
                value="{{ $streamPlatform }}"
                {{ isset($profileData['streamLinks']['default']) && $profileData['streamLinks']['default'] === $streamPlatform ? 'checked' : '' }}
                class="form-checkbox h-4 w-4 rounded border-gray-300 text-yns_yellow focus:ring-yns_yellow">
              <label for="{{ $streamPlatform }}-default" class="ml-2 text-sm text-white">
                Default Player
              </label>
              <input type="hidden" name="default_platform" value="{{ $profileData['streamLinks']['default'] ?? '' }}">
            </div>
          </div>

          @php
            $links = $profileData['streamLinks'][$streamPlatform] ?? [];
            $links = is_array($links) ? $links : [$links];
          @endphp

          @foreach ($links as $index => $link)
            <x-text-input id="{{ $streamPlatform }}-{{ $index }}" name="stream_links[{{ $streamPlatform }}][]"
              value="{{ $link }}" class="mt-2 w-full" />
          @endforeach

          @if (empty($links))
            <x-text-input id="{{ $streamPlatform }}-new" name="stream_links[{{ $streamPlatform }}][]"
              placeholder="Add a {{ ucfirst($streamPlatform) }} link" class="mt-2 w-full" />
          @endif
        </div>
      @endforeach

      <button type="submit"
        class="mt-8 rounded-lg border border-white bg-white px-4 py-2 font-heading font-bold text-black transition duration-150 ease-in-out hover:border-yns_yellow hover:text-yns_yellow">
        Save
      </button>
    </div>
  @endif
</form>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const streamLinksForm = document.getElementById('stream-links-form');
    const defaultCheckboxes = document.querySelectorAll('input[name="default_platform"]');
    const dashboardType = '{{ $dashboardType }}';
    const user = '{{ $user->id }}';

    // Ensure only one default can be selected
    defaultCheckboxes.forEach(checkbox => {
      checkbox.addEventListener('change', function() {
        if (this.checked) {
          defaultCheckboxes.forEach(cb => {
            if (cb !== this) cb.checked = false;
          });
        }
      });
    });

    if (streamLinksForm) {
      streamLinksForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        // Add default platform to form data
        const defaultPlatform = document.querySelector('input[name="default_platform"]:checked');
        if (defaultPlatform) {
          formData.append('default_platform', defaultPlatform.value);
        }

        fetch(`/profile/${dashboardType}/band-profile-update/${user}`, {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
              'Accept': 'application/json'
            },
            body: formData
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              showSuccessNotification('Stream links updated successfully');
            } else {
              throw new Error(data.message || 'Failed to update stream links');
            }
          })
          .catch(error => {
            showFailureNotification(error.message);
            console.error('Error:', error);
          });
      });
    }
  });
</script>
