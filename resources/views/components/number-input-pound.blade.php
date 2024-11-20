@props(['id' => null, 'name' => null, 'value' => null, 'disabled' => false])

<div class="flex items-center">
  <span
    class="border-yns_red dark:border-yns_red inline-flex h-10 items-center rounded-l-md border border-r-0 bg-gray-200 px-3 text-gray-700 dark:bg-gray-800 dark:text-gray-300">
    £
  </span>
  <input type="number" id="{{ $id }}" name="{{ $name }}" value="{{ $value }}"
    {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge([
        'class' =>
            'border-yns_red w-full h-10 dark:border-yns_red dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-r-md shadow-sm',
    ]) !!}>
</div>
