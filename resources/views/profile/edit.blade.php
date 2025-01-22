<x-app-layout :dashboardType="$dashboardType" :modules="$modules">
  <div x-data="{
      sidebarOpen: localStorage.getItem('sidebarOpen') === 'true',
      activeTab: 'profile',
      settingsOpen: false,
      publicProfileOpen: false,
      init() {
          this.$watch('sidebarOpen', value => localStorage.setItem('sidebarOpen', value))
      }
  }" class="from-yns_pink min-h-screen bg-gradient-to-br to-yns_purple">
    <div class="relative h-full">
      {{-- Grid Container --}}
      <div class="grid transition-all duration-300"
        :class="sidebarOpen ? 'grid-cols-[300px,1fr]' : 'grid-cols-[80px,1fr]'">

        {{-- Sidebar --}}
        <div class="relative h-screen bg-opac_8_black transition-all duration-300">
          {{-- Navigation Header --}}
          <div class="flex items-center justify-between px-4 py-6">
            <p class="font-heading font-bold text-white" x-show="sidebarOpen">Navigation</p>
            <button @click="sidebarOpen = !sidebarOpen"
              class="bg-yns_pink rounded-full p-1.5 text-white transition-transform duration-300">
              <i class="fa-solid" :class="sidebarOpen ? 'fa-chevron-left' : 'fa-bars'"></i>
            </button>
          </div>

          {{-- Navigation Items --}}
          <div class="mt-4 flex flex-col gap-2">
            {{-- Profile --}}
            <button @click="activeTab = 'profile'" class="group flex items-center px-8 py-2 text-white transition"
              :class="activeTab === 'profile' ? 'text-yns_yellow' : 'text-yns_pink bg-black/20'">
              <i class="fa-solid fa-user h-5 w-5"></i>
              <span x-show="sidebarOpen" class="ml-3 transition-opacity duration-300">Profile</span>
            </button>

            {{-- Public Profiles --}}
            <div>
              <button @click="publicProfileOpen = !publicProfileOpen"
                class="hover:text-yns_pink flex w-full items-center justify-between px-8 py-2 text-white transition">
                <div class="flex items-center">
                  <i class="fa-solid fa-circle-user h-5 w-5"></i>
                  <span x-show="sidebarOpen" class="ml-3">Public Profile</span>
                </div>
                <i x-show="sidebarOpen" class="fa-solid fa-chevron-down transition-transform duration-300"
                  :class="{ 'rotate-180': publicProfileOpen }"></i>
              </button>

              <div x-show="publicProfileOpen && sidebarOpen" class="space-y-1 pl-12">
                {{-- Role Specific Navigation --}}
                @if ($dashboardType == 'promoter')
                  @include('profile.partials.promoter-profile', [
                      'promoterData' => $promoterData,
                  ])
                @elseif($dashboardType == 'artist')
                  @include('profile.partials.band-profile', [
                      'bandData' => $bandData,
                  ])
                @elseif($dashboardType == 'venue')
                  @include('profile.partials.venue-profile', [
                      'venueData' => $venueData,
                  ])
                @elseif($dashboardType == 'photographer')
                  @include('profile.partials.photographer-profile', [
                      'photographerUserData' => $photographerUserData ?? [],
                  ])
                @elseif($dashboardType == 'standard')
                  @include('profile.partials.standard-profile', [
                      'standardUserData' => $standardUserData,
                  ])
                @elseif($dashboardType == 'designer')
                  @include('profile.partials.designer-profile', [
                      'designerData' => $designerData,
                  ])
                @elseif($dashboardType == 'videographer')
                  @include('profile.partials.videographer-profile', [
                      'standardUserData' => $standardUserData,
                  ])
                @endif
              </div>
            </div>

            {{-- Settings --}}
            <div>
              <button @click="settingsOpen = !settingsOpen"
                class="hover:text-yns_pink flex w-full items-center justify-between px-8 py-2 text-white transition">
                <div class="flex items-center">
                  <i class="fa-solid fa-gear h-5 w-5"></i>
                  <span x-show="sidebarOpen" class="ml-3">Settings</span>
                </div>
                <i x-show="sidebarOpen" class="fa-solid fa-chevron-down transition-transform duration-300"
                  :class="{ 'rotate-180': settingsOpen }"></i>
              </button>

              <div x-show="settingsOpen && sidebarOpen" class="space-y-1 pl-12">
                <button @click="activeTab = 'modules'" class="hover:text-yns_pink block py-2 text-white transition"
                  :class="activeTab === 'modules' ? 'text-yns_yellow' : 'text-yns_pink bg-black/20'">
                  <i class="fa-solid fa-layer-group h-5 w-5"></i>
                  <span x-show="settingsOpen && sidebarOpen" class="ml-3 transition-opacity duration-300">Modules</span>
                </button>
                <button @click="activeTab = 'api'" class="hover:text-yns_pink block py-2 text-white transition"
                  :class="activeTab === 'api' ? 'text-yns_yellow' : 'text-yns_pink bg-black/20'">
                  <i class="fa-solid fa-key h-5 w-5"></i>
                  <span x-show="settingsOpen && sidebarOpen" class="ml-3 transition-opacity duration-300">API
                    Keys</span>
                </button>
              </div>
            </div>

            {{-- Communications --}}
            <button @click="activeTab = 'communications'"
              class="hover:text-yns_pink group flex items-center px-8 py-2 text-white transition"
              :class="activeTab === 'communications' ? 'text-yns_yellow' : 'text-yns_pink bg-black/20'">
              <i class="fa-solid fa-comments h-5 w-5"></i>
              <span x-show="sidebarOpen" class="ml-3 transition-opacity duration-300">Communications</span>
            </button>
          </div>
        </div>

        {{-- Content Area --}}
        <div class="p-8">
          <div class="mx-auto max-w-7xl space-y-6 rounded-lg bg-opac_8_black p-4 shadow sm:p-8">
            {{ $user->id }}
            <div x-show="activeTab === 'profile'">
              @include('profile.partials.edit-user-details')
            </div>
            <div x-show="activeTab === 'basicInfo'">
              @include('profile.basic-information-form', [
                  'profileData' => match ($dashboardType) {
                      'venue' => $venueData,
                      'promoter' => $promoterData,
                      'artist' => $bandData,
                      'photographer' => $photographerData,
                      'designer' => $designerData,
                      'videographer' => $videographerData,
                      default => [],
                  },
              ])
            </div>
            <div x-show="activeTab === 'description'">
              @include('profile.about', [
                  'profileData' => match ($dashboardType) {
                      'venue' => $venueData,
                      'promoter' => $promoterData,
                      'artist' => $bandData,
                      'photographer' => $photographerData,
                      'designer' => $designerData,
                      'videographer' => $videographerData,
                      default => [],
                  },
              ])
            </div>
            <div x-show="activeTab === 'genresAndTypes'">
              @include('profile.genres-and-types', [
                  'profileData' => match ($dashboardType) {
                      'venue' => $venueData,
                      'promoter' => $promoterData,
                      'artist' => $bandData,
                      'photographer' => $photographerData,
                      'designer' => $designerData,
                      'videographer' => $videographerData,
                      default => [],
                  },
              ])
            </div>
            @if ($dashboardType === 'venue')
              <div x-show="activeTab === 'capacity'">
                @include('profile.venue.capacity', [
                    'profileData' => match ($dashboardType) {
                        'venue' => $venueData,
                        default => [],
                    },
                ])
              </div>
            @endif
            @if ($dashboardType === 'venue')
              <div x-show="activeTab === 'inHouseGear'">
                @include('profile.venue.in-house-gear', [
                    'profileData' => match ($dashboardType) {
                        'venue' => $venueData,
                        default => [],
                    },
                ])
              </div>
            @endif
            <div x-show="activeTab === 'myEvents'">
              @include('profile.events', [
                  'profileData' => match ($dashboardType) {
                      'venue' => $venueData,
                      'promoter' => $promoterData,
                      'artist' => $bandData,
                      'photographer' => $photographerData,
                      'designer' => $designerData,
                      'videographer' => $videographerData,
                      default => [],
                  },
              ])
            </div>
            <div x-show="activeTab === 'myBands'">
              @include('profile.artists', [
                  'profileData' => match ($dashboardType) {
                      'venue' => $venueData,
                      'promoter' => $promoterData,
                      'artist' => $bandData,
                      'photographer' => $photographerData,
                      'designer' => $designerData,
                      'videographer' => $videographerData,
                      default => [],
                  },
              ])
            </div>
            @if ($dashboardType === 'venue')
              <div x-show="activeTab === 'additionalInfo'">
                @include('profile.venue.additional-info', [
                    'profileData' => match ($dashboardType) {
                        'venue' => $venueData,
                        default => [],
                    },
                ])
              </div>
            @endif
            <div x-show="activeTab === 'modules'">
              @include('profile.settings.modules', [
                  'modules' => $modules,
                  'dashboardType' => $dashboardType,
                  'userId' => $user->id,
              ])
            </div>
            <div x-show="activeTab === 'api'">
              @include('profile.settings.api-keys')
            </div>
            <div x-show="activeTab === 'communications'">
              @include('profile.partials.communication-settings')
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
