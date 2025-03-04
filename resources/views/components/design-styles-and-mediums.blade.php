@props([
    'styles' => null,
    'print' => null,
    'environments' => null,
    'workingTimes' => null,
    'dashboardType',
    'user',
])

@php
  $options = match ($dashboardType) {
      'designer' => [
          'styles' => config('design-options.designer.styles'),
          'prints' => config('design-options.designer.prints'),
      ],
      'photographer' => [
          'styles' => config('design-options.photographer.styles'),
          'environments' => config('environment_types'),
      ],
      'videographer' => [
          'styles' => config('design-options.videographer.styles'),
          'environments' => config('environment_types'),
      ],
      default => [],
  };
@endphp


<form id="stylesAndPrint" method="POST" class="mt-8">
  @csrf
  @method('PUT')

  {{-- Mobile Select --}}
  <div class="mb-6 md:hidden">
    <select id="mobileTabSelect" class="w-full rounded-lg border border-gray-700 bg-black/50 p-3 text-white">
      <option value="styles-tab">
        @if ($dashboardType === 'designer')
          Design Styles
        @elseif($dashboardType === 'photographer')
          Photography Styles
        @elseif($dashboardType === 'videographer')
          Videography Styles
        @endif
      </option>

      @if ($dashboardType === 'designer')
        <option value="prints-tab">Print Types</option>
      @endif

      @if (in_array($dashboardType, ['photographer', 'videographer']))
        <option value="environments-tab">Environments</option>
        <option value="times-tab">Working Times</option>
      @endif
    </select>
  </div>

  {{-- Desktop Tabs --}}
  <div class="mb-6 hidden border-b border-gray-700 md:block">
    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
      <button type="button"
        class="tab-button whitespace-nowrap border-b-2 border-yns_yellow px-1 py-4 text-sm font-medium text-yns_yellow"
        data-tab="styles-tab">
        @if ($dashboardType === 'designer')
          Design Styles
        @elseif($dashboardType === 'photographer')
          Photography Styles
        @elseif($dashboardType === 'videographer')
          Videography Styles
        @endif
      </button>

      @if ($dashboardType === 'designer')
        <button type="button"
          class="tab-button whitespace-nowrap border-b-2 border-transparent px-1 py-4 text-sm font-medium text-gray-400 hover:border-gray-300 hover:text-gray-300"
          data-tab="prints-tab">
          Print Types
        </button>
      @endif

      @if (in_array($dashboardType, ['photographer', 'videographer']))
        <button type="button"
          class="tab-button whitespace-nowrap border-b-2 border-transparent px-1 py-4 text-sm font-medium text-gray-400 hover:border-gray-300 hover:text-gray-300"
          data-tab="environments-tab">
          Environments
        </button>

        <button type="button"
          class="tab-button whitespace-nowrap border-b-2 border-transparent px-1 py-4 text-sm font-medium text-gray-400 hover:border-gray-300 hover:text-gray-300"
          data-tab="times-tab">
          Working Times
        </button>
      @endif
    </nav>
  </div>

  <div class="tab-content relative block min-h-[200px]">
    {{-- Styles Tab --}}
    <div class="tab-pane block" id="styles-tab">
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($options['styles'] as $style)
          <div
            class="flex items-center space-x-3 rounded-xl border border-gray-700 bg-black/20 p-4 transition-colors hover:border-gray-600">
            <x-input-checkbox id="style_{{ $style }}" name="styles[]" value="{{ $style }}"
              :checked="isset($styles) && in_array($style, $styles)" />
            <span class="text-white">{{ ucfirst(str_replace('-', ' ', $style)) }}</span>
          </div>
        @endforeach
      </div>
    </div>

    {{-- Print Types Tab (Designer Only) --}}
    @if ($dashboardType === 'designer')
      <div class="tab-pane hidden" id="prints-tab">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
          @foreach ($options['prints'] as $printType)
            <div
              class="flex items-center space-x-3 rounded-xl border border-gray-700 bg-black/20 p-4 transition-colors hover:border-gray-600">
              <x-input-checkbox id="print_{{ $printType }}" name="prints[]" value="{{ $printType }}"
                :checked="isset($print) && in_array($printType, $print)" />
              <span class="text-white">{{ ucfirst(str_replace('-', ' ', $printType)) }}</span>
            </div>
          @endforeach
        </div>
      </div>
    @endif

    {{-- Environments Tab (Photographer & Videographer Only) --}}
    <div class="tab-pane hidden" id="environments-tab">
      <div class="space-y-4">
        @foreach ($options['environments'] as $category => $environmentTypes)
          <div class="overflow-hidden rounded-xl border border-gray-700 bg-black/20">
            <button type="button"
              class="test-accordion flex w-full items-center justify-between p-4 text-white hover:bg-black/30">
              <span class="text-sm font-semibold">{{ $category }}</span>
              <svg class="h-5 w-5 transform transition-transform" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>
            <div class="test-content hidden border-t border-gray-700 p-4">
              <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($environmentTypes as $environment)
                  <div class="flex items-center space-x-2">
                    <x-input-checkbox id="environment_{{ Str::slug($environment) }}"
                      name="environments[{{ $category }}][]" value="{{ $environment }}" :checked="isset($environments[$category]) && in_array($environment, $environments[$category])" />
                    <span class="text-white">{{ $environment }}</span>
                  </div>
                @endforeach
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>

    {{-- Working Times Tab (Photographer & Videographer Only) --}}
    <div class="tab-pane hidden" id="times-tab">
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @php
          $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        @endphp

        @foreach ($days as $day)
          <div class="rounded-xl border border-gray-700 bg-black/20 p-4">
            <div class="mb-3 flex items-center justify-between">
              <h3 class="font-medium text-white">{{ $day }}</h3>
              <div class="flex gap-2">
                <button type="button"
                  class="all-day-btn rounded bg-gray-700 px-2 py-1 text-xs text-white transition-colors hover:bg-gray-600"
                  data-day="{{ strtolower($day) }}">
                  All Day
                </button>
                <button type="button"
                  class="unavailable-btn rounded bg-gray-700 px-2 py-1 text-xs text-white transition-colors hover:bg-gray-600"
                  data-day="{{ strtolower($day) }}">
                  Unavailable
                </button>
              </div>
            </div>
            <div class="time-inputs space-y-3" data-day="{{ strtolower($day) }}">
              <div>
                <label class="mb-1 block text-sm text-gray-400">Start Time</label>
                <input type="time"
                  class="working-times-input w-full rounded-lg border border-gray-700 bg-black/50 p-2 text-white"
                  data-day="{{ strtolower($day) }}" data-type="start"
                  value="{{ $workingTimes[strtolower($day)]['start'] ?? '' }}">
              </div>
              <div>
                <label class="mb-1 block text-sm text-gray-400">End Time</label>
                <input type="time"
                  class="working-times-input w-full rounded-lg border border-gray-700 bg-black/50 p-2 text-white"
                  data-day="{{ strtolower($day) }}" data-type="end"
                  value="{{ $workingTimes[strtolower($day)]['end'] ?? '' }}">
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </div>
</form>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Tab functionality variables
    const mobileSelect = document.getElementById('mobileTabSelect');
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabPanes = document.querySelectorAll('.tab-pane');

    function showTab(tabId) {
      // Hide all panes
      tabPanes.forEach(pane => {
        pane.classList.add('hidden');
        pane.classList.remove('block');
      });

      // Show selected pane
      const selectedPane = document.getElementById(`${tabId}`);
      if (selectedPane) {
        selectedPane.classList.remove('hidden');
        selectedPane.classList.add('block');
      }

      // Update button states
      tabButtons.forEach(btn => {
        const isActive = btn.dataset.tab === tabId;
        btn.classList.toggle('border-yns_yellow', isActive);
        btn.classList.toggle('text-yns_yellow', isActive);
        btn.classList.toggle('border-transparent', !isActive);
        btn.classList.toggle('text-gray-400', !isActive);
      });

      // Update mobile select
      if (mobileSelect) {
        mobileSelect.value = tabId;
      }
    }

    // Tab button click handlers
    tabButtons.forEach(button => {
      button.addEventListener('click', (e) => {
        e.preventDefault();
        showTab(button.dataset.tab);
      });
    });

    // Mobile select change handler
    if (mobileSelect) {
      mobileSelect.addEventListener('change', (e) => {
        showTab(e.target.value);
      });
    }

    // Show first tab by default
    const firstButton = document.querySelector('.tab-button');
    if (firstButton) {
      showTab(firstButton.dataset.tab);
    }

    // Accordion functionality
    document.querySelectorAll('.test-accordion').forEach(header => {
      header.addEventListener('click', function(e) {
        e.preventDefault();

        const content = this.nextElementSibling;
        const icon = this.querySelector('svg');

        // Toggle this accordion
        content.classList.toggle('hidden');
        icon.classList.toggle('rotate-180');

        // Close other accordions
        document.querySelectorAll('.test-accordion').forEach(otherHeader => {
          if (otherHeader !== this) {
            const otherContent = otherHeader.nextElementSibling;
            const otherIcon = otherHeader.querySelector('svg');
            otherContent.classList.add('hidden');
            otherIcon.classList.remove('rotate-180');
          }
        });
      });
    });

    // Working times functionality
    document.querySelectorAll('.all-day-btn').forEach(button => {
      button.addEventListener('click', function() {
        const day = this.dataset.day;
        const inputs = document.querySelectorAll(`.time-inputs[data-day="${day}"] input`);
        inputs[0].value = '00:00';
        inputs[1].value = '23:59';
      });
    });

    document.querySelectorAll('.unavailable-btn').forEach(button => {
      button.addEventListener('click', function() {
        const day = this.dataset.day;
        const inputs = document.querySelectorAll(`.time-inputs[data-day="${day}"] input`);
        inputs[0].value = '';
        inputs[1].value = '';
      });
    });
  });

  // Update the updateDesignerProfile function in your script
  function updateProfile() {
    // Collect styles data
    let selectedStyles = jQuery('input[name="styles[]"]:checked').map(function() {
      return jQuery(this).val();
    }).get();

    // Collect prints data (if designer)
    let selectedPrints = jQuery('input[name="prints[]"]:checked').map(function() {
      return jQuery(this).val();
    }).get();

    // Collect environments data (if photographer/videographer)
    let environmentsData = {};
    jQuery('input[name^="environments["]').each(function() {
      if (this.checked) {
        let matches = this.name.match(/environments\[(.*?)\]/);
        if (matches) {
          let category = matches[1];
          if (!environmentsData[category]) {
            environmentsData[category] = [];
          }
          environmentsData[category].push(this.value);
        }
      }
    });

    // Collect working times data
    let workingTimesData = {};
    jQuery('.working-times-input').each(function() {
      let day = jQuery(this).data('day');
      let type = jQuery(this).data('type');
      let value = jQuery(this).val();

      if (!workingTimesData[day]) {
        workingTimesData[day] = {};
      }
      workingTimesData[day][type] = value;
    });

    // Send combined data
    jQuery.ajax({
      url: `/profile/${dashboardType}/update/${userId}`,
      method: 'POST',
      data: {
        _token: '{{ csrf_token() }}',
        _method: 'PUT',
        styles: selectedStyles,
        prints: selectedPrints,
        environments: environmentsData,
        working_times: workingTimesData
      },
      success: function(response) {
        showSuccessNotification(response.message);
      },
      error: function(xhr, status, error) {
        showFailureNotification('Error updating profile');
        console.error('Error:', error);
      }
    });
  }
</script>
