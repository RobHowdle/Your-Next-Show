@php
  $spotifyUrl = 'https://open.spotify.com/track/4PTG3Z6ehGkBFwjybzWkR8?si=23c6845e25df4307';
@endphp
<div class="mb-8">
  <div class="rounded-xl bg-yns_dark_blue/75 p-2 backdrop-blur-sm">
    <nav class="flex flex-wrap gap-2" aria-label="Tabs">
      <button data-tab="about"
        class="tabLinks flex items-center rounded-lg px-4 py-2.5 text-sm font-medium text-gray-400 transition-all hover:bg-black/20 hover:text-white">
        <span class="fas fa-info-circle mr-2"></span>
        About
      </button>
      <button data-tab="portfolio"
        class="tabLinks flex items-center rounded-lg px-4 py-2.5 text-sm font-medium text-gray-400 transition-all hover:bg-black/20 hover:text-white">
        <span class="fas fa-folder mr-2"></span>
        Portfolio
      </button>
      <button data-tab="packages"
        class="tabLinks flex items-center rounded-lg px-4 py-2.5 text-sm font-medium text-gray-400 transition-all hover:bg-black/20 hover:text-white">
        <span class="fas fa-handshake mr-2"></span>
        Packages
      </button>
      <button data-tab="reviews"
        class="tabLinks flex items-center rounded-lg px-4 py-2.5 text-sm font-medium text-gray-400 transition-all hover:bg-black/20 hover:text-white">
        <span class="fas fa-star mr-2"></span>
        Reviews
      </button>
      <button data-tab="other"
        class="tabLinks flex items-center rounded-lg px-4 py-2.5 text-sm font-medium text-gray-400 transition-all hover:bg-black/20 hover:text-white">
        <span class="fas fa-plus mr-2"></span>
        Other
      </button>
    </nav>
  </div>
</div>
