@props([
    'workingTimes' => null,
    'dashboardType',
    'user',
])
<form id="workingTimesForm" method="POST"
  action="{{ route('photographer.update', ['dashboardType' => $dashboardType, 'user' => $user->id]) }}" class="mt-8">
  @csrf
  @method('PUT')

  <div class="grid grid-cols-1 gap-4 md:grid-cols-1">
    <x-input-label-dark class="">When are you available to work?</x-input-label-dark>

    {{-- Loop through days of the week --}}
    @foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
      <div class="flex items-center space-x-4">

        {{-- Time pickers --}}
        <div class="flex w-full items-center space-x-2" id="time_picker_{{ $day }}"
          @if (isset($workingTimes[$day]) && ($workingTimes[$day] === 'all-day' || $workingTimes[$day] === 'unavailable')) style="display:none" @endif>
          {{-- Start Time --}}
          <x-time-input type="time" name="working_times[{{ $day }}][start]"
            value="{{ isset($workingTimes[$day]['start']) ? $workingTimes[$day]['start'] : '' }}"
            class="rounded border-gray-300 text-white" step="900" />
          <span class="text-white">to</span>
          {{-- End Time --}}
          <x-time-input type="time" name="working_times[{{ $day }}][end]"
            value="{{ isset($workingTimes[$day]['end']) ? $workingTimes[$day]['end'] : '' }}"
            class="rounded border-gray-300 text-white" step="900" />
        </div>

        {{-- Checkboxes --}}
        <div class="flex w-full items-center space-x-2">
          {{-- All Day --}}
          <x-input-checkbox id="all_day_{{ $day }}" name="working_times[{{ $day }}]" value="all-day"
            :checked="isset($workingTimes[$day]) && $workingTimes[$day] === 'all-day'" />
          <span class="text-white">All Day</span>

          {{-- Unavailable --}}
          <x-input-checkbox id="unavailable_{{ $day }}" name="working_times[{{ $day }}]"
            value="unavailable" :checked="isset($workingTimes[$day]) && $workingTimes[$day] === 'unavailable'" />
          <span class="text-white">Unavailable</span>
        </div>

        {{-- Error Messages --}}
        @error("working_times.$day.start")
          <div class="text-sm text-red-500">{{ $message }}</div>
        @enderror
        @error("working_times.$day.end")
          <div class="text-sm text-red-500">{{ $message }}</div>
        @enderror
      </div>
    @endforeach
  </div>

  {{-- Submit Button --}}
  <div class="flex items-center gap-4">
    <button type="submit"
      class="mt-8 rounded-lg border border-white bg-white px-4 py-2 font-heading font-bold text-black transition duration-150 ease-in-out hover:border-yns_yellow hover:text-yns_yellow">
      Save
    </button>
    @if (session('status') === 'profile-updated')
      <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
        class="text-sm text-gray-600 dark:text-gray-400">{{ __('Saved.') }}</p>
    @endif
  </div>
</form>


<script>
  document.getElementById('workingTimesForm').addEventListener('submit', function(e) {
    e.preventDefault(); // Prevent the default form submission

    const form = this;
    const formData = new FormData(form);

    console.log(formData);

    // Ensure "All Day" and "Unavailable" checkboxes correctly reflect their respective time pickers' visibility
    document.querySelectorAll('input[type="checkbox"]').forEach(function(checkbox) {
      const day = checkbox.id.replace(/(all_day_|unavailable_)/, ''); // Extract day from checkbox ID
      const timePicker = document.getElementById('time_picker_' + day);

      // Ensure time picker exists before modifying its display
      if (timePicker) {
        // Ensure time picker is hidden when 'All Day' or 'Unavailable' is checked
        if (checkbox.checked && (checkbox.id.startsWith('all_day_') || checkbox.id.startsWith(
            'unavailable_'))) {
          timePicker.style.display = 'none'; // Hide time picker
        } else {
          timePicker.style.display = 'flex'; // Show time picker
        }
      }
    });

    // Now submit the form
    fetch(form.action, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json',
        },
        body: formData,
      })
      .then(response => {
        if (!response.ok) {
          return response.json().then(data => {
            throw new Error(data.message || 'An unexpected error occurred.');
          });
        }
        return response.json();
      })
      .then(data => {
        if (data.success) {
          showSuccessNotification('Success', data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error.message);
        showFailureNotification('Error', error.message);
      });

  });

  // Toggle time pickers based on 'All Day' and 'Unavailable' checkbox state
  document.querySelectorAll('input[type="checkbox"]').forEach(function(checkbox) {
    checkbox.addEventListener('change', function() {
      const day = this.id.replace(/(all_day_|unavailable_)/, ''); // Extract day from checkbox ID
      const timePicker = document.getElementById('time_picker_' + day);

      // Only proceed if timePicker element exists
      if (timePicker) {
        // Handle 'All Day' checkbox
        if (this.id.startsWith('all_day_')) {
          if (this.checked) {
            timePicker.style.display = 'none';
            document.getElementById('unavailable_' + day).checked =
              false; // Uncheck 'Unavailable' if checked
          } else {
            timePicker.style.display = 'flex';
          }
        }

        // Handle 'Unavailable' checkbox
        if (this.id.startsWith('unavailable_')) {
          if (this.checked) {
            timePicker.style.display = 'none';
            document.getElementById('all_day_' + day).checked = false; // Uncheck 'All Day' if checked
          } else {
            timePicker.style.display = 'flex';
          }
        }
      }
    });
  });
</script>
