@props([
    'styles' => null,
    'print' => null,
    'environments' => null,
    'dashboardType',
    'user',
])

@php
  $designStyles = config('design-options.styles');
  $printTypes = config('design-options.prints');
  $photographyEnvironments = config('environment_types');
@endphp

<form id="stylesAndPrint" method="POST" class="mt-8">
  @csrf
  @method('PUT')
  @if ($dashboardType === 'designer')
    <div class="grid grid-cols-2 gap-4 md:grid-cols-1">
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
      <x-input-label-dark>What environments do you work in?</x-input-label-dark>
      <div class="grid grid-cols-1 gap-4">
        @foreach ($photographyEnvironments as $category => $environmentTypes)
          <div class="mb-4">
            <h4 class="mb-2 text-sm font-semibold text-gray-400">{{ $category }}</h4>
            <div class="grid grid-cols-2 gap-4">
              @foreach ($environmentTypes as $environment)
                <div class="flex items-center space-x-2">
                  <x-input-checkbox id="environment_{{ Str::slug($environment) }}"
                    name="environments[{{ $category }}][]" value="{{ $environment }}" :checked="isset($environments[$category]) && in_array($environment, $environments[$category])" />
                  <span class="text-white">{{ $environment }}</span>
                </div>
              @endforeach
            </div>
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
