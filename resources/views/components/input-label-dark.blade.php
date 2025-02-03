@props(['value', 'class' => null, 'required' => false])

<label {{ $attributes->merge(['class' => 'block font-medium font-heading text-sm text-yns_med_gray mb-2 ' . $class]) }}>
  {{ $value ?? $slot }}
  @if ($required)
    <span class="text-yns_red">*</span>
  @endif
</label>
