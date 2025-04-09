<div class="space-y-6">
  {{-- Position Requirements --}}
  <div class="space-y-2">
    <x-input-label-dark>Position Type</x-input-label-dark>
    <select name="position_type" class="mt-1 w-full rounded-md border-gray-700 bg-gray-800">
      <option value="headliner">Headliner</option>
      <option value="main_support">Main Support</option>
      <option value="support">Support</option>
      <option value="opener">Opener</option>
    </select>
  </div>

  {{-- Genre Requirements --}}
  <div class="space-y-2">
    @foreach ($genresWithSubgenres as $mainGenre => $subgenres)
      <div class="space-y-2">
        <label class="flex items-center space-x-2">
          <input type="checkbox" name="main_genres[]" value="{{ $mainGenre }}" checked
            class="rounded border-gray-700 bg-gray-800 text-yns_yellow">
          <span class="font-medium text-white">{{ $mainGenre }}</span>
        </label>

        @if (count($subgenres) > 0)
          <div class="ml-6 grid grid-cols-2 gap-2 sm:grid-cols-3">
            @foreach ($subgenres as $subgenre)
              <label class="flex items-center space-x-2">
                <input type="checkbox" name="subgenres[{{ $mainGenre }}][]" value="{{ $subgenre }}"
                  class="rounded border-gray-700 bg-gray-800 text-yns_yellow">
                <span class="text-sm text-gray-300">{{ $subgenre }}</span>
              </label>
            @endforeach
          </div>
        @endif
      </div>
    @endforeach
  </div>

  {{-- Set Times --}}
  <div class="grid gap-4 sm:grid-cols-2">
    <div>
      <x-input-label-dark>Performance Start Time</x-input-label-dark>
      {{-- <x-time-input id="performance_start_time" name="performance_start_time" class="mt-1 block w-full"
        required></x-time-input> --}}
      <input type="text" id="performance_start_time" name="performance_start_time"
        class="w-full rounded-md border-yns_red shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-yns_red dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600"
        required>
    </div>
    <div>
      <x-input-label-dark>Performance End Time</x-input-label-dark>
      {{-- <x-time-input id="performance_end_time" name="performance_end_time" class="mt-1 block w-full"
        required></x-time-input> --}}
      <input type="text" id="performance_end_time" name="performance_end_time"
        class="w-full rounded-md border-yns_red shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-yns_red dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600"
        required>
    </div>
    <div class="col-span-2">
      <x-input-label-dark>Set Length</x-input-label-dark>
      <x-text-input id="set_length" name="set_length" type="text" class="mt-1 block w-full bg-gray-700 text-white"
        readonly placeholder="Calculated automatically"></x-text-input>
    </div>
  </div>

  <div class="space-y-2">
    <x-input-label-dark>Deadline</x-input-label-dark>
    <input type="date" id="application_deadline" name="application_deadline"
      class="w-full rounded-md border-yns_red shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-yns_red dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600"
      required>
  </div>

  <div class="space-y-2">
    <x-input-label-dark>Excluded Ids</x-input-label-dark>
    <x-text-input id="excluded_entities" name="excluded_entities" type="text"
      class="mt-1 block w-full bg-gray-700 text-white"
      placeholder="Comma-separated list of excluded IDs"></x-text-input>
  </div>
