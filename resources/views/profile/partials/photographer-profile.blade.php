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
<button @click="activeTab = 'portfolio'" class="hover:text-yns_pink block py-2 text-white transition"
  :class="activeTab === 'portfolio' ? 'text-yns_yellow' : 'text-yns_pink bg-black/20'">
  <i class="fa-solid fa-people-group h-5 w-5"></i>
  <span x-show="publicProfileOpen && sidebarOpen" class="ml-3 transition-opacity duration-300">Portfolio</span>
</button>
<button @click="activeTab = 'designAndHours'" class="hover:text-yns_pink block py-2 text-white transition"
  :class="activeTab === 'designAndHours' ? 'text-yns_yellow' : 'text-yns_pink bg-black/20'">
  <i class="fa-solid fa-clock h-5 w-5"></i>
  <span x-show="publicProfileOpen && sidebarOpen" class="ml-3 transition-opacity duration-300">Styles & Hours</span>
</button>
<button @click="activeTab = 'packages'" class="hover:text-yns_pink block py-2 text-white transition"
  :class="activeTab === 'packages' ? 'text-yns_yellow' : 'text-yns_pink bg-black/20'">
  <i class="fa-solid fa-handshake h-5 w-5"></i>
  <span x-show="publicProfileOpen && sidebarOpen" class="ml-3 transition-opacity duration-300">Packages</span>
</button>
