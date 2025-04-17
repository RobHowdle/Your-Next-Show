<div
  {{ $attributes->merge(['class' => 'fixed inset-0 z-50 flex items-center justify-center bg-black backdrop-blur-sm transition-all duration-300']) }}
  x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
  x-transition:leave="ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
  <div class="text-center">
    @if ($showMusicLoader)
      <div class="music-loader mb-4">
        <span class="bar"></span>
        <span class="bar"></span>
        <span class="bar"></span>
        <span class="bar"></span>
        <span class="bar"></span>
      </div>
    @endif
    <p class="{{ $textClasses }}">{{ $text }}</p>
  </div>
</div>
