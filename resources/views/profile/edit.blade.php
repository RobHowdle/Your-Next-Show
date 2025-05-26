<x-app-layout :dashboardType="$dashboardType" :modules="$modules">
  <div x-data="profileManager()" class="from-yns_orange min-h-screen bg-gradient-to-br to-yns_purple">
    <!-- Loading State -->
    <x-loading-overlay x-show="loading" />

    <div x-show="!loading" x-cloak class="flex h-screen overflow-hidden">
      <!-- Sidebar Navigation -->
      <x-profile-sidebar :dashboardType="$dashboardType" :standardUserData="$standardUserData ?? null" />

      <!-- Main Content Area -->
      <div class="flex-1 overflow-y-auto">
        <div class="container mx-auto p-6">
          <div class="rounded-lg bg-opac_8_black p-6 shadow-lg">
            <!-- Tab Contents -->
            <div x-show="activeTab === 'profile'">
              @include('profile.sections.edit-user-details', ['user' => $user])
            </div>

            <div x-show="activeTab === 'basic-information'">
              @include('profile.sections.basic-information', [
                  'profileData' => match ($dashboardType) {
                      'venue' => $venueData,
                      'promoter' => $promoterData,
                      'artist' => $artistData,
                      'photographer' => $photographerData,
                      'designer' => $designerData,
                      'videographer' => $videographerData,
                      default => [],
                  },
              ])
            </div>

            <div x-show="activeTab === 'description'">
              @include('profile.sections.description', [
                  'profileData' => match ($dashboardType) {
                      'venue' => $venueData,
                      'promoter' => $promoterData,
                      'artist' => $artistData,
                      'photographer' => $photographerData,
                      'designer' => $designerData,
                      'videographer' => $videographerData,
                      default => [],
                  },
              ])
            </div>

            <div x-show="activeTab === 'genres-and-types'">
              @include('profile.sections.genres-and-types', [
                  'profileData' => match ($dashboardType) {
                      'venue' => $venueData,
                      'promoter' => $promoterData,
                      'artist' => $artistData,
                      'photographer' => $photographerData,
                      'designer' => $designerData,
                      'videographer' => $videographerData,
                      default => [],
                  },
              ])
            </div>

            <div x-show="activeTab === 'events'">
              @include('profile.sections.events', [
                  'profileData' => match ($dashboardType) {
                      'venue' => $venueData,
                      'promoter' => $promoterData,
                      'artist' => $artistData,
                      'photographer' => $photographerData,
                      'designer' => $designerData,
                      'videographer' => $videographerData,
                      default => [],
                  },
              ])
            </div>

            <!-- Role-Specific Sections -->
            @if ($dashboardType === 'venue')
              <div x-show="activeTab === 'venue-details'">
                @include('profile.sections.venue.venue-details', [
                    'profileData' => $venueData,
                ])
              </div>
              <div x-show="activeTab === 'in-house-gear'">
                @include('profile.sections.venue.in-house-gear', [
                    'profileData' => $venueData,
                ])
              </div>
              @if (isset($modules['jobs']) && $modules['jobs']['is_enabled'])
                <div x-show="activeTab === 'packages'">
                  @include('profile.sections.packages')
                </div>
              @endif
              <div x-show="activeTab === 'lmlc'">
                @include('profile.sections.venue.lmlc')
              </div>
            @endif

            @if ($dashboardType === 'promoter')
              <div x-show="activeTab === 'venues'">
                @include('profile.sections.promoter.my-venues', [
                    'profileData' => $promoterData,
                ])
              </div>
              <div x-show="activeTab === 'bands'">
                @include('profile.sections.promoter.my-bands', [
                    'profileData' => $promoterData,
                ])
              </div>
            @endif

            @if ($dashboardType === 'artist')
              <div x-show="activeTab === 'documents'">
                @include('profile.sections.artist.documents', [
                    'profileData' => $artistData,
                ])
              </div>
              <div x-show="activeTab === 'stream-links'">
                @include('profile.sections.artist.stream-links', [
                    'profileData' => $artistData,
                ])
              </div>
              <div x-show="activeTab === 'members'">
                @include('profile.sections.artist.members', [
                    'profileData' => $artistData,
                ])
              </div>
            @endif

            @if (in_array($dashboardType, ['designer', 'photographer', 'videographer']))
              {{-- @include('profile.sections.creative-tabs') --}}
            @endif

            <!-- Settings Sections -->
            <div x-show="activeTab === 'modules'">
              @include('profile.sections.settings.modules')
            </div>
            <div x-show="activeTab === 'api-keys'">
              @include('profile.sections.settings.api-keys')
            </div>
            <div x-show="activeTab === 'communications'">
              @include('profile.sections.settings.communications')
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  @push('scripts')
    <script>
      function profileManager() {
        return {
          sidebarOpen: localStorage.getItem('sidebarOpen') === 'true',
          activeTab: localStorage.getItem('activeTab') || 'profile',
          loading: true,

          init() {
            this.$watch('sidebarOpen', value => localStorage.setItem('sidebarOpen', value))
            this.$watch('activeTab', value => localStorage.setItem('activeTab', value))

            setTimeout(() => {
              this.loading = false
              window.scrollTo(0, 0)
            }, 100)
          }
        }
      }
    </script>
  @endpush
</x-app-layout>
