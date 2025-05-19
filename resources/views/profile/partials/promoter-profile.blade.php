<button @click="activeTab = 'basicInfo'" class="hover:text-yns_pink block py-2 text-white transition"
  :class="activeTab === 'basicInfo' ? 'text-yns_yellow' : 'text-yns_pink bg-black/20'">
  <i class="fa-solid fa-circle-user h-5 w-5"></i>
  <span x-show="publicProfileOpen && sidebarOpen" class="ml-3 transition-opacity duration-300">Basic Information</span>
</button>
<button @click="activeTab = 'description'" class="hover:text-yns_pink block py-2 text-white transition"
  :class="activeTab === 'description' ? 'text-yns_yellow' : 'text-yns_pink bg-black/20'">
  <i class="fa-solid fa-circle-info h-5 w-5"></i>
  <span x-show="publicProfileOpen && sidebarOpen" class="ml-3 transition-opacity duration-300">About</span>
</button>
<button @click="activeTab = 'genresAndTypes'" class="hover:text-yns_pink block py-2 text-white transition"
  :class="activeTab === 'genresAndTypes' ? 'text-yns_yellow' : 'text-yns_pink bg-black/20'">
  <i class="fa-solid fa-music h-5 w-5"></i>
  <span x-show="publicProfileOpen && sidebarOpen" class="ml-3 transition-opacity duration-300">Genres & Band
    Types</span>
</button>
<button @click="activeTab = 'myVenues'" class="hover:text-yns_pink block py-2 text-white transition"
  :class="activeTab === 'myVenues' ? 'text-yns_yellow' : 'text-yns_pink bg-black/20'">
  <i class="fa-solid fa-people-group h-5 w-5"></i>
  <span x-show="publicProfileOpen && sidebarOpen" class="ml-3 transition-opacity duration-300">My Venues</span>
</button>
<button @click="activeTab = 'myEvents'" class="hover:text-yns_pink block py-2 text-white transition"
  :class="activeTab === 'myEvents' ? 'text-yns_yellow' : 'text-yns_pink bg-black/20'">
  <i class="fa-solid fa-calendar-days h-5 w-5"></i>
  <span x-show="publicProfileOpen && sidebarOpen" class="ml-3 transition-opacity duration-300">My Events</span>
</button>
<button @click="activeTab = 'myBands'" class="hover:text-yns_pink block py-2 text-white transition"
  :class="activeTab === 'myBands' ? 'text-yns_yellow' : 'text-yns_pink bg-black/20'">
  <i class="fa-solid fa-users-line h-5 w-5"></i>
  <span x-show="publicProfileOpen && sidebarOpen" class="ml-3 transition-opacity duration-300">My Artists</span>
</button>
@if (isset($modules['jobs']) && $modules['jobs']['is_enabled'])
  <button @click="activeTab = 'packages'" class="hover:text-yns_pink block py-2 text-white transition"
    :class="activeTab === 'packages' ? 'text-yns_yellow' : 'text-yns_pink bg-black/20'">
    <i class="fa-solid fa-box-open h-5 w-5"></i>
    <span x-show="publicProfileOpen && sidebarOpen" class="ml-3 transition-opacity duration-300">Packages</span>
  </button>
@endif
