<div x-show="selectedTab === 2" class="bg-opac_8_black p-4 shadow sm:rounded-lg sm:p-8" x-cloak>
  <div class="w-full">
    @include('profile.band.basic-information-form', [
        'firstName' => $firstName,
        'lastName' => $lastName,
        'location' => $location,
        'name' => $bandData['name'],
        'contact_name' => $bandData['contact_name'],
        'contact_email' => $bandData['contact_email'],
        'contact_number' => $bandData['contact_number'],
        'platformsToCheck' => $bandData['platformsToCheck'],
        'platforms' => $bandData['platforms'],
        'logo' => $bandData['logo'],
    ]) </div>
</div>
<div x-show="selectedTab === 3" class="bg-opac_8_black p-4 shadow sm:rounded-lg sm:p-8" x-cloak>
  <div class="w-full">
    @include('profile.band.about', [
        'about' => $bandData['about'],
    ]) </div>
</div>
<div x-show="selectedTab === 4" class="bg-opac_8_black p-4 shadow sm:rounded-lg sm:p-8" x-cloak>
  <div class="w-full">
    {{-- @include('profile.band.stream-links', [
        'streamLinks' => $bandData['streamLinks'],
        'streamPlatformsToCheck' => $bandData['streamPlatformsToCheck'],
    ]) --}}
  </div>
</div>
<div x-show="selectedTab === 5" class="bg-opac_8_black p-4 shadow sm:rounded-lg sm:p-8" x-cloak>
  <div class="w-full">
    @include('profile.band.my-events', [
        'userRole' => $userRole,
        'firstName' => $firstName,
        'lastName' => $lastName,
        'email' => $email,
        'location' => $location,
        'myEvents' => $bandData['myEvents'],
        'dashboardType' => $dashboardType,
    ]) </div>
</div>
<div x-show="selectedTab === 6" class="bg-opac_8_black p-4 shadow sm:rounded-lg sm:p-8" x-cloak>
  <div class="w-full">
    @include('profile.band.my-genres', [
        'dashboardType' => $dashboardType,
        'userRole' => $userRole,
        'firstName' => $firstName,
        'lastName' => $lastName,
        'email' => $email,
        'location' => $location,
        'genres' => $bandData['genres'],
        'artistGenres' => $bandData['artistGenres'],
        'artistBandType' => $bandData['bandTypes'],
        'userId' => $userId,
        'artist' => $bandData['artist'],
    ])
  </div>
</div>
<div x-show="selectedTab === 7" class="bg-opac_8_black p-4 shadow sm:rounded-lg sm:p-8" x-cloak>
  <div class="w-full">
    @include('profile.band.members', [
        'dashboardType' => $dashboardType,
        'userRole' => $userRole,
        'userId' => $userId,
        'members' => $bandData['members'],
    ]) </div>
</div>
