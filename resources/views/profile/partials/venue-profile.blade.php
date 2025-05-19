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
<button @click="activeTab = 'capacity'" class="hover:text-yns_pink block py-2 text-white transition"
  :class="activeTab === 'capacity' ? 'text-yns_yellow' : 'text-yns_pink bg-black/20'">
  <i class="fa-solid fa-people-group h-5 w-5"></i>
  <span x-show="publicProfileOpen && sidebarOpen" class="ml-3 transition-opacity duration-300">Capacity</span>
</button>
<button @click="activeTab = 'inHouseGear'" class="hover:text-yns_pink block py-2 text-white transition"
  :class="activeTab === 'inHouseGear' ? 'text-yns_yellow' : 'text-yns_pink bg-black/20'">
  <i class="fa-solid fa-drum h-5 w-5"></i>
  <span x-show="publicProfileOpen && sidebarOpen" class="ml-3 transition-opacity duration-300">In House Gear</span>
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
<button @click="activeTab = 'additionalInfo'" class="hover:text-yns_pink block py-2 text-white transition"
  :class="activeTab === 'additionalInfo' ? 'text-yns_yellow' : 'text-yns_pink bg-black/20'">
  <i class="fa-solid fa-circle-question h-5 w-5"></i>
  <span x-show="publicProfileOpen && sidebarOpen" class="ml-3 transition-opacity duration-300">Additional Info</span>
</button>
@if (isset($modules['jobs']) && $modules['jobs']['is_enabled'])
  <button @click="activeTab = 'packages'" class="hover:text-yns_pink block py-2 text-white transition"
    :class="activeTab === 'packages' ? 'text-yns_yellow' : 'text-yns_pink bg-black/20'">
    <i class="fa-solid fa-box-open h-5 w-5"></i>
    <span x-show="publicProfileOpen && sidebarOpen" class="ml-3 transition-opacity duration-300">Packages</span>
  </button>
@endif
<button @click="activeTab = 'lmlc'" class="hover:text-yns_pink block py-2 text-white transition"
  :class="activeTab === 'lmlc' ? 'text-yns_yellow' : 'text-yns_pink bg-black/20'">
  <i class="fa-solid fa-gift h-5 w-5"></i>
  <span x-show="publicProfileOpen && sidebarOpen" class="ml-3 transition-opacity duration-300">Live Music Loyalty
    Card</span>
</button>
