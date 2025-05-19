@props(['id' => null, 'name' => null, 'class' => null, 'value' => null, 'disabled' => false, 'html' => false])

<textarea id="{{ $id }}" name="{{ $name }}" value="{{ $value }}" {{ $disabled ? 'disabled' : '' }}
  {!! $attributes->merge([
      'class' => "border-yns_red w-full bg-gray-900 px-2 py-2 text-gray-300 focus:border-indigo-500 focus:border-indigo-600 focus:ring-indigo-500 rounded-md shadow-sm $class",
  ]) !!}>
  @if ($html)
{!! $slot !!}
@else
{{ $slot }}
@endif
</textarea>
