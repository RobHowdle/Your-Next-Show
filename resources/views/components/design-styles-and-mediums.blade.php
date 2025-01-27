@props([
    'styles' => null,
    'print' => null,
    'dashboardType',
    'user',
])

@php
  $designStyles = config('design-options.styles');
  $printTypes = config('design-options.prints');
  $photographyStyles = config('photography-options.styles');
@endphp

<form id="stylesAndPrint" method="POST" class="mt-8">
  @csrf
  @method('PUT')
  @if ($dashboardType === 'designer')
    <div class="grid grid-cols-1 gap-4 md:grid-cols-1">
      <x-input-label-dark>What are your design styles?</x-input-label-dark>
      <div class="grid grid-cols-2 gap-4">
        @foreach ($designStyles as $style)
          <div class="flex items-center space-x-2">
            <x-input-checkbox id="style_{{ $style }}" name="styles[]" value="{{ $style }}"
              :checked="isset($styles) && in_array($style, $styles)" />
            <span class="text-white">{{ ucfirst(str_replace('-', ' ', $style)) }}</span>
          </div>
        @endforeach
      </div>

      <x-input-label-dark class="mt-4">What print types do you offer?</x-input-label-dark>

      <div class="grid grid-cols-2 gap-4">
        @foreach ($printTypes as $printType)
          <div class="flex items-center space-x-2">
            <x-input-checkbox id="print_{{ $printType }}" name="prints[]" value="{{ $printType }}"
              :checked="isset($print) && in_array($printType, $print)" />
            <span class="text-white">{{ ucfirst(str_replace('-', ' ', $printType)) }}</span>
          </div>
        @endforeach
      </div>
    </div>
  @elseif($dashboardType === 'photographer')
    <div class="grid grid-cols-1 gap-4 md:grid-cols-1">
      <x-input-label-dark>What are your photography styles?</x-input-label-dark>
      <div class="grid grid-cols-2 gap-4">
        @foreach ($photographyStyles as $style)
          <div class="flex items-center space-x-2">
            <x-input-checkbox id="style_{{ $style }}" name="styles[]" value="{{ $style }}"
              :checked="isset($styles) && in_array($style, $styles)" />
            <span class="text-white">{{ ucfirst(str_replace('-', ' ', $style)) }}</span>
          </div>
        @endforeach
      </div>
    </div>
  @elseif($dashboardType === 'videographer')
    <div class="grid grid-cols-1 gap-4 md:grid-cols-1">
      <x-input-label-dark>What are your videography styles?</x-input-label-dark>
      <div class="grid grid-cols-2 gap-4">
        @foreach ($photographyStyles as $style)
          <div class="flex items-center space-x-2">
            <x-input-checkbox id="style_{{ $style }}" name="styles[]" value="{{ $style }}"
              :checked="isset($styles) && in_array($style, $styles)" />
            <span class="text-white">{{ ucfirst(str_replace('-', ' ', $style)) }}</span>
          </div>
        @endforeach
      </div>
    </div>
  @endif
</form>
