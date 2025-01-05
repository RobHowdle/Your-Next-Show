@props(['id', 'type' => 'button'])

<button id="{{ $id }}" type="{{ $type }}"
  {{ $attributes->merge([
      'type' => 'button',
      'class' => 'inline-flex items-center px-4 py-2 rounded-md font-semibold text-xs text-black uppercase tracking-widest 
                      bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-150 ease-in-out hover:border-yns_yellow hover:text-yns_yellow transition duration-150 ease-in-out border-white border',
  ]) }}>
  {{ $slot }}
</button>
