@php
  $spotifyUrl = 'https://open.spotify.com/track/4PTG3Z6ehGkBFwjybzWkR8?si=23c6845e25df4307';
@endphp

<div class="mb-4 md:mb-8">
  <div class="rounded-xl bg-yns_dark_blue/75 p-2 backdrop-blur-sm">
    <nav class="grid grid-cols-2 gap-2 md:flex md:flex-wrap" aria-label="Tabs">
      <button data-tab="about"
        class="tabLinks flex w-full items-center justify-center rounded-lg px-4 py-3 text-sm font-medium text-gray-400 transition-all hover:bg-black/20 hover:text-white md:w-auto md:justify-start">
        <span class="fas fa-info-circle mr-2"></span>
        About
      </button>
      <button data-tab="portfolio"
        class="tabLinks flex w-full items-center justify-center rounded-lg px-4 py-3 text-sm font-medium text-gray-400 transition-all hover:bg-black/20 hover:text-white md:w-auto md:justify-start">
        <span class="fas fa-folder mr-2"></span>
        Portfolio
      </button>
      <button data-tab="packages"
        class="tabLinks flex w-full items-center justify-center rounded-lg px-4 py-3 text-sm font-medium text-gray-400 transition-all hover:bg-black/20 hover:text-white md:w-auto md:justify-start">
        <span class="fas fa-handshake mr-2"></span>
        Packages
      </button>
      <button data-tab="reviews"
        class="tabLinks flex w-full items-center justify-center rounded-lg px-4 py-3 text-sm font-medium text-gray-400 transition-all hover:bg-black/20 hover:text-white md:w-auto md:justify-start">
        <span class="fas fa-star mr-2"></span>
        Reviews
      </button>
    </nav>
  </div>
</div>
