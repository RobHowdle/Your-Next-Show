<x-app-layout :dashboardType="$dashboardType" :modules="$modules">
  <x-slot name="header">
    <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
      {{ __('Profile') }}
    </h2>
  </x-slot>

  <div x-data="{ selected: 1, open: false, selectedTab: 1 }" class="grid min-h-screen grid-cols-[1fr,2fr] gap-4">
    <div class="h-full bg-opac_8_black">
      <div class="py-8 font-heading text-xl">
        <p class="mb-8 px-8 font-bold">Settings</p>
        <div class="flex flex-col items-start gap-4">
          <button @click="selected = 1; selectedTab = 1"
            :class="{ 'bg-gradient-button': selected === 1, 'bg-yns_dark_gray': selected !== 1 }"
            class="group relative w-full px-8 py-2 text-left text-white transition duration-150 ease-in-out">
            <span class="absolute inset-0 transition-opacity duration-300 ease-in-out"
              :class="{ 'opacity-100': selected === 1, 'opacity-0': selected !== 1 }"></span>
            <span class="relative z-10">User</span>
          </button>

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
                'venueUserData' => $venueUserData,
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
                'designerUserData' => $designerUserData,
            ])
            {{-- @elseif($dashboardType == 'videographer')
          @include('profile.partials.videographer-profile', [
              'standardUserData' => $standardUserData,
          ]) --}}
          @endif

          @if ($dashboardType !== 'designer')
            <button @click="selected = 10; selectedTab = 10" data-tab="calendar"
              :class="{ 'bg-gradient-button': selected === 10, 'bg-yns_dark_gray': selected !== 10 }"
              class="group relative w-full px-8 py-2 text-left text-white transition duration-150 ease-in-out">
              <span class="absolute inset-0 transition-opacity duration-300 ease-in-out"
                :class="{ 'opacity-100': selected === 10, 'opacity-0': selected !== 10 }"></span>
              <span class="relative z-10">Calendar</span>
            </button>
          @endif

          @if ($dashboardType !== 'standard')
            <button @click="selected = 11; selectedTab =11"
              :class="{ 'bg-gradient-button': selected === 11, 'bg-yns_dark_gray': selected !== 11 }"
              class="group relative w-full px-8 py-2 text-left text-white transition duration-150 ease-in-out">
              <span class="absolute inset-0 transition-opacity duration-300 ease-in-out"
                :class="{ 'opacity-100': selected === 11, 'opacity-0': selected !== 11 }"></span>
              <span class="relative z-10">Settings</span>
            </button>
          @endif

          <button @click="selected = 12; selectedTab =12"
            :class="{ 'bg-gradient-button': selected === 12, 'bg-yns_dark_gray': selected !== 12 }"
            class="group relative w-full px-8 py-2 text-left text-white transition duration-150 ease-in-out">
            <span class="absolute inset-0 transition-opacity duration-300 ease-in-out"
              :class="{ 'opacity-100': selected === 12, 'opacity-0': selected !== 12 }"></span>
            <span class="relative z-10">Communication</span>
          </button>
        </div>
      </div>
    </div>
    <div class="mx-4 my-4 h-auto self-center font-heading">
      <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
        <div x-show="selectedTab === 1" class="bg-opac_8_black p-4 shadow sm:rounded-lg sm:p-8" x-cloak>
          <div class="max-w-xl">
            <p class="text-xl font-bold">User Settings</p>
            @include('profile.partials.edit-user-details', [
                'userRole' => $userRole,
                'userFirstName' => $userFirstName,
                'userLastName' => $userLastName,
                'userEmail' => $userEmail,
                'userDob' => $userDob,
                'userLocation' => $userLocation,
                'userPostalTown' => $userPostalTown,
                'userLat' => $userLat,
                'userLong' => $userLong,
            ])
          </div>
        </div>
        @if ($dashboardType == 'promoter')
          @include('profile.partials.promoter-profile-tabs', [
              'promoterData' => $promoterData,
          ])
        @elseif($dashboardType == 'artist')
          @include('profile.partials.band-profile-tabs', [
              'bandData' => $bandData,
          ])
        @elseif($dashboardType == 'venue')
          @include('profile.partials.venue-profile-tabs', [
              'venueUserData' => $venueUserData,
          ])
        @elseif($dashboardType == 'photographer')
          @include('profile.partials.photographer-profile-tabs', [
              'photographerUserData' => $photographerUserData,
          ])
        @elseif($dashboardType == 'standard')
          @include('profile.partials.standard-profile-tabs', [
              'standardUserData' => $standardUserData,
          ])
        @elseif($dashboardType == 'designer')
          @include('profile.partials.designer-profile-tabs', [
              'designerUserData' => $designerUserData,
          ])
          {{-- @elseif($dashboardType == 'videographer')
          @include('profile.partials.videographer-profile-tabs', [
              'standardUserData' => $standardUserData,
          ]) --}}
        @endif


        <div x-show="selectedTab === 10" class="bg-opac_8_black p-4 shadow sm:rounded-lg sm:p-8" x-cloak>
          <div class="w-full">
            @include('profile.partials.calendar', [
                'dashboardType' => $dashboardType,
            ])
          </div>
        </div>

        <div x-show="selectedTab === 11" class="bg-opac_8_black p-4 shadow sm:rounded-lg sm:p-8" x-cloak>
          <div class="w-full">
            <div class="group">
              @include('profile.partials.settings', [
                  'modules' => $modules,
              ])
            </div>
          </div>
        </div>
        <div x-show="selectedTab === 12" class="bg-opac_8_black p-4 shadow sm:rounded-lg sm:p-8" x-cloak>
          <div class="w-full">
            <div class="group">
              @include('profile.partials.communication-settings', [
                  'communications' => $communications,
              ])
            </div>
          </div>
        </div>
      </div>
    </div>
</x-app-layout>

<style>
  [x-cloak] {
    display: none;
  }
</style>
<script>
  // document.getElementById('sync-all-events-apple').addEventListener('click', function() {
  //   var calendarEl = document.getElementById("calendar");
  //   var userId = calendarEl.getAttribute("data-user-id");
  //   const url = `/profile/events/${userId}/apple/sync`; // Define your route for syncing

  //   // Show loading state if needed
  //   this.textContent = 'Syncing...';

  //   // Make an AJAX request to trigger the download
  //   fetch(url)
  //     .then(response => {
  //       if (response.ok) {
  //         return response.blob(); // Return blob data for the .ics file
  //       }
  //       throw new Error('Network response was not ok.');
  //     })
  //     .then(blob => {
  //       // Create a link element to download the file
  //       const url = window.URL.createObjectURL(blob);
  //       const a = document.createElement('a');
  //       a.href = url;
  //       a.download = 'events.ics'; // Set a name for the file
  //       document.body.appendChild(a);
  //       a.click();
  //       a.remove();
  //       window.URL.revokeObjectURL(url);

  //       // Reset button text
  //       this.textContent = 'Sync All Events to Apple Calendar';
  //     })
  //     .catch(error => {
  //       console.error('Error:', error);
  //       alert('Failed to sync events. Please try again.');
  //       this.textContent = 'Sync All Events to Apple Calendar';
  //     });
  // });
</script>
