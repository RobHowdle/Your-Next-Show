@props(['id' => null, 'name' => null, 'value' => null, 'disabled' => false, 'required' => false])

<div class="relative">
  <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
    <span class="text-gray-400 sm:text-sm">£</span>
  </div>
  <input type="number" step="0.01" min="0"
    {{ $attributes->merge([
        'class' =>
            'pl-7 block w-full rounded-md border-yns_red bg-gray-800/50 text-white placeholder-gray-500 focus:border-yns_yellow focus:ring-yns_yellow sm:text-sm',
    ]) }}>
</div>
