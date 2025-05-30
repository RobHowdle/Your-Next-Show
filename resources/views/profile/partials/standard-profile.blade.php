<div class="w-full">
  <button @click="open = !open"
    class="group relative w-full bg-yns_dark_gray px-8 py-2 text-left text-white transition duration-150 ease-in-out">
    <span
      class="absolute inset-0 bg-gradient-button opacity-0 transition-opacity duration-300 ease-in-out group-hover:opacity-100"></span>
    <span class="relative z-10 flex items-center justify-between">
      <span>Public Profile</span>
      <svg x-show="!open" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform duration-200"
        fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
      </svg>
      <svg x-show="open" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform duration-200"
        fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
      </svg>
    </span> </button>

  <div x-show="open" x-transition class="mt-4">
    <div class="flex flex-col items-start gap-4">
      <button @click="selected = 2; selectedTab = 2"
        :class="{ 'bg-gradient-button': selected === 2, 'bg-yns_dark_gray': selected !== 2 }"
        class="group relative w-full px-8 py-2 text-left text-white transition duration-150 ease-in-out">
        <span class="absolute inset-0 transition-opacity duration-300 ease-in-out"
          :class="{ 'opacity-100': selected === 2, 'opacity-0': selected !== 2 }"></span>
        <span class="relative z-10">Genres</span>
      </button>
    </div>
  </div>
</div>
