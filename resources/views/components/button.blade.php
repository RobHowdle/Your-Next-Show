@props(['href' => null, 'type' => null, 'id', 'label', 'fa' => null])

@if (isset($href) && !empty($href))
  <a href="{{ $href }}" id="{{ $id }}"
    {{ $attributes->merge(['class' => 'rounded-lg bg-white px-4 py-2 text-black transition-all duration-300 ease-in-out hover:bg-gradient-to-t hover:from-yns_dark_orange hover:to-yns_yellow']) }}>
    @if ($fa)
      <span class="{{ $fa }} mr-2"></span>
    @endif
    {{ $label }}
  </a>
@elseif (isset($type) && $type === 'submit')
  <button type="submit" id="{{ $id }}"
    {{ $attributes->merge(['class' => 'cursor-pointer rounded-lg bg-white px-4 py-2 text-black transition-all duration-300 ease-in-out hover:bg-gradient-to-t hover:from-yns_dark_orange hover:to-yns_yellow']) }}>
    @if ($fa)
      <span class="{{ $fa }} mr-2"></span>
    @endif
    {{ $label }}
  </button>
@elseif (isset($type) && $type === 'button')
  <button type="button" id="{{ $id }}"
    {{ $attributes->merge(['class' => 'cursor-pointer rounded-lg bg-white px-4 py-2 text-black transition-all duration-300 ease-in-out hover:bg-gradient-to-t hover:from-yns_dark_orange hover:to-yns_yellow']) }}>
    @if ($fa)
      <span class="{{ $fa }} mr-2"></span>
    @endif
    {{ $label }}
  </button>
@else
  <span id="{{ $id }}"
    {{ $attributes->merge(['class' => 'cursor-pointer rounded-lg bg-white px-4 py-2 text-black transition-all duration-300 ease-in-out hover:bg-gradient-to-t hover:from-yns_dark_orange hover:to-yns_yellow']) }}>
    @if ($fa)
      <span class="{{ $fa }} mr-2"></span>
    @endif
    {{ $label }}
  </span>
@endif
