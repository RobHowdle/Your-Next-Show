<div class="bg-opac_8_black transition-all duration-300" :class="sidebarOpen ? 'w-[300px]' : 'w-[80px]'"
  style="z-index: 50;">
  <!-- Sidebar Header -->
  <div class="flex items-center px-4 py-6" :class="sidebarOpen ? 'justify-between' : 'justify-center'">
    <p class="font-heading font-bold text-white" x-show="sidebarOpen">Navigation</p>
    <button @click="sidebarOpen = !sidebarOpen"
      class="bg-yns_orange flex h-8 w-8 items-center justify-center rounded-full p-1.5 text-white transition-all duration-300">
      <i class="fa-solid" :class="sidebarOpen ? 'fa-chevron-left' : 'fa-bars'"></i>
    </button>
  </div>

  <!-- Navigation Items -->
  <nav class="mt-4 flex flex-col gap-2 px-4">
    <x-profile-nav-item icon="user" label="User Details" tab="profile" />
    <x-profile-nav-item icon="info-circle" label="Basic Information" tab="basic-information" />
    <x-profile-nav-item icon="pen" label="Description" tab="description" />
    <x-profile-nav-item icon="music" label="Genres & Artist Types" tab="genres-and-types" />

    <!-- Role Specific Navigation -->
    @if ($dashboardType === 'venue')
      <x-profile-nav-item icon="building" label="Venue Details" tab="venue-details" />
      <x-profile-nav-item icon="drum" label="In-House Gear" tab="in-house-gear" />
      <x-profile-nav-item icon="calendar-days" label="My Events" tab="events" />
      <x-profile-nav-item icon="box-open" label="Packages" tab="packages" />
      <x-profile-nav-item icon="gift" label="Live Music Loyalty Card" tab="lmlc" />
    @endif

    @if ($dashboardType === 'artist')
      <x-profile-nav-item icon="music" label="Stream Links" tab="stream-links" />
      <x-profile-nav-item icon="users" label="Band Members" tab="members" />
      <x-profile-nav-item icon="calendar-days" label="My Events" tab="events" />
    @endif

    <!-- Settings Section -->
    <div x-data="{ open: false }" class="flex flex-col">
      <button @click="open = !open"
        class="flex items-center gap-2 rounded px-2 py-2 transition hover:bg-gray-700 focus:outline-none"
        :class="sidebarOpen ? 'justify-start' : 'justify-center'" type="button">
        <i class="fa-solid fa-cog"></i>
        <span x-show="sidebarOpen" class="font-heading text-white">Settings</span>
        <i class="fa-solid ml-auto" x-show="sidebarOpen" :class="open ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
      </button>
      <div x-show="open" x-transition class="ml-8 mt-1 flex flex-col gap-1" x-cloak>
        <x-profile-nav-item icon="puzzle-piece" label="Modules" tab="modules" />
        <x-profile-nav-item icon="key" label="API Keys" tab="api-keys" />
        <x-profile-nav-item icon="building" label="Communications" tab="communications" />
      </div>
    </div>
  </nav>
</div>
