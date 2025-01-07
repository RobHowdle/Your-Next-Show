<div x-show="selectedTab === 2" class="bg-opac_8_black p-4 shadow sm:rounded-lg sm:p-8" x-cloak>
  <div class="w-full">
    @include('profile.venue.basic-information-form', [
        'name' => $venueUserData['venueName'],
        'venueLocation' => $venueUserData['venueLocation'],
        'venuePostalTown' => $venueUserData['venuePostalTown'],
        'venueLat' => $venueUserData['venueLat'],
        'venueLong' => $venueUserData['venueLong'],
        'contact_name' => $venueUserData['contact_name'],
        'contact_number' => $venueUserData['contact_number'],
        'contact_email' => $venueUserData['contact_email'],
        'platforms' => $venueUserData['platforms'],
        'platformsToCheck' => $venueUserData['platformsToCheck'],
        'logo' => $venueUserData['logo'],
    ]) </div>
</div>
<div x-show="selectedTab === 3" class="bg-opac_8_black p-4 shadow sm:rounded-lg sm:p-8" x-cloak>
  <div class="w-full">
    @include('profile.venue.about', [
        'description' => $venueUserData['description'],
    ]) </div>
</div>
<div x-show="selectedTab === 4" class="bg-opac_8_black p-4 shadow sm:rounded-lg sm:p-8" x-cloak>
  <div class="w-full">
    @include('profile.venue.capacity', [
        'capacity' => $venueUserData['capacity'],
    ]) </div>
</div>
<div x-show="selectedTab === 5" class="bg-opac_8_black p-4 shadow sm:rounded-lg sm:p-8" x-cloak>
  <div class="w-full">
    @include('profile.venue.in-house-gear', [
        'inHouseGear' => $venueUserData['inHouseGear'],
    ]) </div>
</div>
<div x-show="selectedTab === 6" class="bg-opac_8_black p-4 shadow sm:rounded-lg sm:p-8" x-cloak>
  <div class="w-full">
    @include('profile.venue.my-events', [
        'myEvents' => $venueUserData['myEvents'],
    ]) </div>
</div>
<div x-show="selectedTab === 7" class="bg-opac_8_black p-4 shadow sm:rounded-lg sm:p-8" x-cloak>
  <div class="w-full">
    @include('profile.venue.my-bands', [
        'uniqueBands' => $venueUserData['uniqueBands'],
    ]) </div>
</div>
<div x-show="selectedTab === 8" class="bg-opac_8_black p-4 shadow sm:rounded-lg sm:p-8" x-cloak>
  <div class="w-full">
    @include('profile.venue.my-genres', [
        'genres' => $venueUserData['genres'],
        'promoterGenres' => $venueUserData['venueGenres'],
        'venue' => $venueUserData['venue'],
    ]) </div>
</div>
<div x-show="selectedTab === 9" x-init="if (selectedTab === 9) { initializeMaps() }" class="bg-opac_8_black p-4 shadow sm:rounded-lg sm:p-8" x-cloak>
  <div class="w-full">
    @include('profile.venue.additional-info', [
        'additionalInfo' => $venueUserData['additionalInfo'],
    ]) </div>
</div>
