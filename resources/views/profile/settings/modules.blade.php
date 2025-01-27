<section>
  <header>
    <h2 class="font-heading text-xl font-medium text-white">{{ __('Modules') }}</h2>
  </header>
  <p class="text-md mb-4 text-white">Turn on/off the modules you want or don't want to use.</p>

  <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
    @foreach ($modules as $name => $settings)
      <div class="rounded-lg bg-opac_8_black p-4">
        <div class="flex flex-col space-y-4">
          <div>
            <h3 class="text-lg font-medium text-white">{{ config("modules.modules.{$name}.name") }}</h3>
            <p class="mt-1 text-sm text-white">{{ config("modules.modules.{$name}.description") }}</p>
          </div>
          <div class="flex items-center justify-end">
            <label class="relative inline-flex cursor-pointer items-center">
              <input type="checkbox" class="toggle-checkbox sr-only" data-module="{{ $name }}"
                onchange="updateModuleStatus('{{ $name }}', this.checked)"
                {{ $settings['is_enabled'] === 1 ? 'checked' : '' }}>
              <div class="toggle-bg">
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
  function updateModuleStatus(moduleName, enabled) {
    const dashboardType = '{{ $dashboardType }}';
    const userId = '{{ $userId }}';

    $.ajax({
      url: `/api/profile/${dashboardType}/settings/update`,
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      data: {
        module: moduleName,
        enabled: enabled ? 1 : 0,
        userId: userId
      },
      success: function(response) {
        if (response.success) {
          // Update Alpine store
          Alpine.store('userModules', (modules) => ({
            ...modules,
            [moduleName]: {
              ...modules[moduleName],
              is_enabled: enabled ? 1 : 0
            }
          }));
        }
        showSuccessNotification(moduleName, 'successfully updated.');
      },
      error: function(xhr, status, error) {
        showFailureNotification('Failed to update module:', error);
      }
    });
  }
</script>
<style>
  /* Container for the slider background */
  .toggle-bg {
    width: 44px;
    height: 24px;
    background-color: #d1d5db;
    /* Default gray */
    border-radius: 12px;
    position: relative;
    cursor: pointer;
    transition: background-color 0.3s ease;
  }

  /* The moving circle */
  .toggle-circle {
    width: 20px;
    height: 20px;
    background-color: white;
    border-radius: 50%;
    position: absolute;
    top: 2px;
    left: 2px;
    transition: transform 0.3s ease;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
  }

  /* When the checkbox is checked, change background and position */
  .toggle-checkbox:checked+.toggle-bg {
    background-color: #22c55e;
    /* Green background when enabled */
  }

  .toggle-checkbox:checked+.toggle-bg .toggle-circle {
    transform: translateX(20px);
    /* Move the circle to the right */
  }

  /* Add hover effect for better UX */
  .toggle-bg:hover {
    background-color: #e5e7eb;
    /* Slightly lighter gray */
  }

  .toggle-checkbox:checked+.toggle-bg:hover {
    background-color: #16a34a;
    /* Slightly darker green */
  }
</style>
