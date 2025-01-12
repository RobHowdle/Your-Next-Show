@props(['id' => null, 'name' => null, 'value' => null, 'disabled' => false, 'required' => false])

<input id="{{ $id }}" name="{{ $name }}" value="{{ $value }}" {{ $disabled ? 'disabled' : '' }}
  {{ $required ? 'required' : '' }} {!! $attributes->merge([
      'class' =>
          'border-yns_red w-full bg-gray-900 px-2 py-2 text-gray-300 focus:border-indigo-500 focus:border-indigo-600 focus:ring-indigo-500 rounded-md shadow-sm',
  ]) !!}>
