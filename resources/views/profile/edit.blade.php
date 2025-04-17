  <x-app-layout :dashboardType="$dashboardType" :modules="$modules">
    <div x-data="{
        sidebarOpen: localStorage.getItem('sidebarOpen') === 'true',
        activeTab: 'profile',
        settingsOpen: false,
        publicProfileOpen: false,
        loading: true,
        init() {
            this.$watch('sidebarOpen', value => localStorage.setItem('sidebarOpen', value))
            this.$watch('activeTab', value => {
                localStorage.setItem('activeTab', value)
            })
            setTimeout(() => {
                this.loading = false
                window.scrollTo(0, 0)
            }, 100)
        }
    }" class="from-yns_orange relative min-h-screen bg-gradient-to-br to-yns_purple">
      <!-- Loading overlay -->
      <x-loading-overlay x-show="loading" />

      <!-- Main Layout -->
      <div x-show="!loading" x-cloak class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <div class="bg-opac_8_black transition-all duration-300" :class="sidebarOpen ? 'w-[300px]' : 'w-[80px]'"
          style="z-index: 50;">
          <div class="flex items-center px-4 py-6" :class="sidebarOpen ? 'justify-between' : 'justify-center'">
            <p class="font-heading font-bold text-white" x-show="sidebarOpen">Navigation</p>
            <button @click="sidebarOpen = !sidebarOpen"
              class="bg-yns_orange flex h-8 w-8 items-center justify-center rounded-full p-1.5 text-white transition-all duration-300">
              <i class="fa-solid"
                :class="{
                    'fa-chevron-left': sidebarOpen,
                    'fa-bars': !sidebarOpen
                }"></i>
            </button>
          </div>

          <!-- Navigation Items -->
          <div class="mt-4 flex flex-col gap-2 px-4">
            <button @click="activeTab = 'profile'"
              class="group flex items-center gap-3 rounded-lg px-4 py-2 transition-all duration-300"
              :class="activeTab === 'profile'
                  ?
                  'bg-yns_orange text-white' :
                  'text-gray-300 hover:bg-yns_orange hover:bg-opacity-20 hover:text-white'">
              <i class="fa-solid fa-user"></i>
              <span x-show="sidebarOpen">User Details</span>
            </button>

            {{-- Public Profiles --}}
            <div>
              <button @click="publicProfileOpen = !publicProfileOpen"
                class="flex w-full items-center justify-between px-4 py-2 transition-all duration-300"
                :class="publicProfileOpen
                    ?
                    'bg-yns_orange text-white' :
                    'text-gray-300 hover:bg-yns_orange/20 hover:text-white'">
                <div class="flex items-center">
                  <i class="fa-solid fa-circle-user h-5 w-5"></i>
                  <span x-show="sidebarOpen" class="ml-3">Public Profile</span>
                </div>
                <i x-show="sidebarOpen" class="fa-solid fa-chevron-down transition-transform duration-300"
                  :class="{ 'rotate-180': publicProfileOpen }">
                </i>
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
                      'photographerData' => $photographerData ?? [],
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
                      'videographerData' => $videographerData,
                  ])
                @endif
              </div>
            </div>
          </div>
        </div>

        <!-- Main Content Area -->
        <div class="flex-1 overflow-y-auto">
          <div class="container mx-auto p-6">
            <div class="rounded-lg bg-opac_8_black p-6 shadow-lg">
              <!-- Profile Content -->
              <div x-show="activeTab === 'profile'" class="space-y-6">
                @include('profile.partials.edit-user-details')
              </div>

              <!-- Basic Info Content -->
              <div x-show="activeTab === 'basicInfo'" class="space-y-6">
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
              @if ($dashboardType === 'artist')
                <div x-show="activeTab === 'streamLinks'">
                  @include('profile.artist.stream-links', [
                      'profileData' => match ($dashboardType) {
                          'artist' => $bandData,
                          default => [],
                      },
                  ])
                </div>
              @endif
              @if ($dashboardType === 'artist')
                <div x-show="activeTab === 'members'">
                  @include('profile.artist.members', [
                      'profileData' => match ($dashboardType) {
                          'artist' => $bandData,
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
              <div x-show="activeTab === 'designAndHours'">
                @include('profile.designer.styles-and-times', [
                    'profileData' => match ($dashboardType) {
                        'photographer' => $photographerData,
                        'designer' => $designerData,
                        'videographer' => $videographerData,
                        default => [],
                    },
                ])
              </div>
              @if (in_array($dashboardType, ['designer', 'photographer', 'videographer']))
                <div x-show="activeTab === 'portfolio'">
                  @include('profile.portfolio', [
                      'dashboardType' => $dashboardType,
                      'waterMarkedPortfolioImages' => $profileData['waterMarkedPortfolioImages'] ?? [],
                      'profileData' => match ($dashboardType) {
                          'designer' => $designerData,
                          'photographer' => $photographerData,
                          'videographer' => $videographerData,
                          default => [],
                      },
                  ])
                </div>
              @endif
              @if (collect($modules)->contains('name', 'jobs'))
                <div x-show="activeTab === 'packages'">
                  @include('profile.packages', [
                      'profileData' => match ($dashboardType) {
                          'designer' => $designerData,
                          'photographer' => $photographerData,
                          'videographer' => $videographerData,
                          'venue' => $venueData,
                          'promoter' => $promoterData,
                          'artist' => $bandData,
                          default => [],
                      },
                  ])
                </div>
              @endif
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
              @if ($dashboardType === 'venue')
                <div x-show="activeTab === 'lmlc'">
                  @include('profile.venue.lmlc', [
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
                @include('profile.settings.api-keys', [
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
              <div x-show="activeTab === 'communications'">
                @include('profile.partials.communication-settings')
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </x-app-layout>
