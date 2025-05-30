<div x-show="selectedTab === 2" class="bg-opac_8_black p-4 shadow sm:rounded-lg sm:p-8" x-cloak>
  <div class="w-full">
    @include('profile.band.basic-information-form', [
        'firstName' => $firstName,
        'lastName' => $lastName,
        'location' => $location,
        'name' => $artistData['name'],
        'contact_name' => $artistData['contact_name'],
        'contact_email' => $artistData['contact_email'],
        'contact_number' => $artistData['contact_number'],
        'platformsToCheck' => $artistData['platformsToCheck'],
        'platforms' => $artistData['platforms'],
        'logo' => $artistData['logo'],
    ]) </div>
</div>
<div x-show="selectedTab === 3" class="bg-opac_8_black p-4 shadow sm:rounded-lg sm:p-8" x-cloak>
  <div class="w-full">
    @include('profile.band.about', [
        'about' => $artistData['about'],
    ]) </div>
</div>
<div x-show="selectedTab === 4" class="bg-opac_8_black p-4 shadow sm:rounded-lg sm:p-8" x-cloak>
  <div class="w-full">
    {{-- @include('profile.band.stream-links', [
        'streamLinks' => $artistData['streamLinks'],
        'streamPlatformsToCheck' => $artistData['streamPlatformsToCheck'],
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
        'myEvents' => $artistData['myEvents'],
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
        'genres' => $artistData['genres'],
        'artistGenres' => $artistData['artistGenres'],
        'artistBandType' => $artistData['bandTypes'],
        'userId' => $userId,
        'artist' => $artistData['artist'],
    ])
  </div>
</div>
<div x-show="selectedTab === 7" class="bg-opac_8_black p-4 shadow sm:rounded-lg sm:p-8" x-cloak>
  <div class="w-full">
    @include('profile.band.members', [
        'dashboardType' => $dashboardType,
        'userRole' => $userRole,
        'userId' => $userId,
        'members' => $artistData['members'],
    ]) </div>
</div>
