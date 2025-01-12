@props(['id' => null, 'name' => null, 'disabled' => false, 'value' => null])

@php
  if ($value) {
      // Strip any time component and force date-only format
      $dateValue =
          $value instanceof \Carbon\Carbon
              ? $value->startOfDay()->format('Y-m-d')
              : \Carbon\Carbon::parse($value)->startOfDay()->format('Y-m-d');
  } else {
      $dateValue = null;
  }
@endphp

<input type="date" id="{{ $id }}" name="{{ $name }}" {{ $disabled ? 'disabled' : '' }}
  value="{{ $dateValue }}" {!! $attributes->merge([
      'class' =>
          'border-yns_red w-full dark:border-yns_red dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm',
  ]) !!} />
