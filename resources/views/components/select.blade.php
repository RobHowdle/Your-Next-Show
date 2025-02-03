@props(['id' => null, 'name' => null, 'options' => [], 'disabled' => false, 'selected' => [], 'required' => false])

<select id="{{ $id }}" name="{{ $name }}[]" {{ $disabled ? 'disabled' : '' }}
  {{ $required ? 'required' : '' }} {!! $attributes->merge([
      'class' =>
          'border-yns_red dark:border-yns_red dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm',
  ]) !!}>
  @foreach ($options as $value => $label)
    <option value="{{ $value }}" {{ in_array($value, $selected) ? 'selected' : '' }}>
      {{ $label }}
    </option>
  @endforeach
</select>
