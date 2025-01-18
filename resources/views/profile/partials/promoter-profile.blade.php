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
        <span class="relative z-10">Basic Information</span>
      </button>
      <button @click="selected = 3; selectedTab = 3"
        :class="{ 'bg-gradient-button': selected === 3, 'bg-yns_dark_gray': selected !== 3 }"
        class="group relative w-full px-8 py-2 text-left text-white transition duration-150 ease-in-out">
        <span class="absolute inset-0 transition-opacity duration-300 ease-in-out"
          :class="{ 'opacity-100': selected === 3, 'opacity-0': selected !== 3 }"></span>
        <span class="relative z-10">About</span>
      </button>
      <button @click="selected = 4; selectedTab = 4"
        :class="{ 'bg-gradient-button': selected === 4, 'bg-yns_dark_gray': selected !== 4 }"
        class="group relative w-full px-8 py-2 text-left text-white transition duration-150 ease-in-out">
        <span class="absolute inset-0 transition-opacity duration-300 ease-in-out"
          :class="{ 'opacity-100': selected === 4, 'opacity-0': selected !== 4 }"></span>
        <span class="relative z-10">My Venues</span>
      </button>
      <button @click="selected = 5; selectedTab = 5"
        :class="{ 'bg-gradient-button': selected === 5, 'bg-yns_dark_gray': selected !== 5 }"
        class="group relative w-full px-8 py-2 text-left text-white transition duration-150 ease-in-out">
        <span class="absolute inset-0 transition-opacity duration-300 ease-in-out"
          :class="{ 'opacity-100': selected === 5, 'opacity-0': selected !== 5 }"></span>
        <span class="relative z-10">My Events</span>
      </button>
      <button @click="selected = 6; selectedTab = 6"
        :class="{ 'bg-gradient-button': selected === 6, 'bg-yns_dark_gray': selected !== 6 }"
        class="group relative w-full px-8 py-2 text-left text-white transition duration-150 ease-in-out">
        <span class="absolute inset-0 transition-opacity duration-300 ease-in-out"
          :class="{ 'opacity-100': selected === 6, 'opacity-0': selected !== 6 }"></span>
        <span class="relative z-10">My Bands</span>
      </button>
      <button @click="selected = 7; selectedTab = 7"
        :class="{ 'bg-gradient-button': selected === 7, 'bg-yns_dark_gray': selected !== 7 }"
        class="group relative w-full px-8 py-2 text-left text-white transition duration-150 ease-in-out">
        <span class="absolute inset-0 transition-opacity duration-300 ease-in-out"
          :class="{ 'opacity-100': selected === 7, 'opacity-0': selected !== 7 }"></span>
        <span class="relative z-10">Genres & Types</span>
      </button>
      <button @click="selected = 8; selectedTab = 8"
        :class="{ 'bg-gradient-button': selected === 8, 'bg-yns_dark_gray': selected !== 8 }"
        class="group relative w-full px-8 py-2 text-left text-white transition duration-150 ease-in-out">
        <span class="absolute inset-0 transition-opacity duration-300 ease-in-out"
          :class="{ 'opacity-100': selected === 8, 'opacity-0': selected !== 8 }"></span>
        <span class="relative z-10">Ticket API Keys</span>
      </button>
    </div>
  </div>
</div>
