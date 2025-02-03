@props(['name', 'label', 'selected', 'groups', 'dashboardType'])

<x-input-label-dark class="mb-6">{{ $label }}</x-input-label-dark>
<div class="grid grid-cols-3">
  @foreach ($groups as $groupName => $items)
    <fieldset class="mb-4">
      <legend class="text-sm font-semibold text-white">{{ $groupName }}</legend>
      <div class="mt-2 space-y-1">
        @foreach ($items as $item)
          <div class="flex items-center">
            <input id="{{ $name . '_' . Str::slug($item) }}" name="{{ $name }}[]" value="{{ $item }}"
              type="checkbox"
              class="focus:ring-3 h-4 w-4 rounded border border-gray-300 bg-gray-50 text-yns_cyan focus:ring-yns_cyan dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-yns_red dark:focus:ring-yns_cyan dark:focus:ring-offset-yns_cyan"
              @if (is_array($selected) && in_array($item, $selected)) checked @endif>
            <x-input-label-dark for="{{ $name . '_' . Str::slug($item) }}" class="ml-3 block text-sm text-white">
              {{ $item }}
            </x-input-label-dark>
          </div>
        @endforeach
      </div>
    </fieldset>
  @endforeach
</div>
