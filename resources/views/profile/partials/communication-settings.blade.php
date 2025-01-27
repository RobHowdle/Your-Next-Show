<section>
  <header>
    <h2 class="font-heading text-xl font-medium text-white">{{ __('Communication Settings') }}</h2>
  </header>
  <p class="text-md mb-4 text-white">Manage your communication preferences for the platform.</p>

  <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
    @foreach ($communications as $name => $settings)
      <div class="rounded-lg bg-opac_8_black p-4">
        <div class="flex flex-col space-y-4">
          <div>
            <h3 class="text-lg font-medium text-white">{{ $settings['name'] }}</h3>
            <p class="mt-1 text-sm text-white">{{ $settings['description'] }}</p>
          </div>
          <div class="flex items-center justify-end">
            <label
              class="{{ in_array($name, ['system_announcements', 'legal_or_policy_updates']) ? 'opacity-50' : '' }} relative inline-flex cursor-pointer items-center">
              <input type="checkbox" class="toggle-checkbox sr-only" data-communication="{{ $name }}"
                onchange="updateCommunicationStatus('{{ $name }}', this.checked)"
                {{ $settings['is_enabled'] === 1 ? 'checked' : '' }}
                {{ in_array($name, ['system_announcements', 'legal_or_policy_updates']) ? 'disabled' : '' }}>
              <div
                class="toggle-bg {{ in_array($name, ['system_announcements', 'legal_or_policy_updates']) ? 'cursor-not-allowed' : '' }}">
                <div class="toggle-circle"></div>
              </div>
            </label>
          </div>
        </div>
      </div>
    @endforeach
  </div>
</section>

<script>
  function updateCommunicationStatus(settingName, enabled) {
    const dashboardType = '{{ $dashboardType }}';
    const userId = '{{ $user->id }}';

    $.ajax({
      url: `/api/profile/${dashboardType}/communications/update`,
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      data: {
        setting: settingName,
        enabled: enabled ? 1 : 0,
        userId: userId
      },
      success: function(response) {
        if (response.success) {
          showSuccessNotification(settingName, 'successfully updated.');
        }
      },
      error: function(xhr, status, error) {
        showFailureNotification('Failed to update setting:', error);
      }
    });
  }
</script>

<style>
  .toggle-bg {
    width: 44px;
    height: 24px;
    background-color: #d1d5db;
    border-radius: 12px;
    position: relative;
    cursor: pointer;
    transition: background-color 0.3s ease;
  }

  .toggle-bg.cursor-not-allowed {
    background-color: #10B981;
    pointer-events: none;
    cursor: not-allowed;
  }

  .cursor-not-allowed .toggle-circle {
    transform: translateX(20px);
  }

  .toggle-circle {
    width: 20px;
    height: 20px;
    background-color: white;
    border-radius: 50%;
    position: absolute;
    top: 2px;
    left: 2px;
    transition: transform 0.3s ease;
  }

  .toggle-checkbox:checked+.toggle-bg {
    background-color: #10B981;
  }

  .toggle-checkbox:checked+.toggle-bg .toggle-circle {
    transform: translateX(20px);
  }
</style>
