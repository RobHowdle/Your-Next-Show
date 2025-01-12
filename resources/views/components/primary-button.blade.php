<button
  {{ $attributes->merge([
      'type' => 'submit',
      'class' => 'inline-flex items-center px-4 py-2 rounded-md font-semibold text-xs text-white uppercase tracking-widest 
                  bg-[length:200%_100%] bg-gradient-to-r from-yns_yellow via-yns_dark_orange to-yns_yellow 
                  bg-[position:0%] hover:bg-[position:100%]
                  focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 
                  transition-all duration-500 ease-in-out border-0',
  ]) }}>
  {{ $slot }}
</button>
