<div x-show="selectedTab === 2" class="bg-opac_8_black p-4 shadow sm:rounded-lg sm:p-8" x-cloak>
  <div class="w-full">
    @include('profile.venue.basic-information-form', [
        'name' => $venueData['venueName'],
        'venueLocation' => $venueData['venueLocation'],
        'venuePostalTown' => $venueData['venuePostalTown'],
        'venueLat' => $venueData['venueLat'],
        'venueLong' => $venueData['venueLong'],
        'contact_name' => $venueData['contact_name'],
        'contact_number' => $venueData['contact_number'],
        'contact_email' => $venueData['contact_email'],
        'platforms' => $venueData['platforms'],
        'platformsToCheck' => $venueData['platformsToCheck'],
        'logo' => $venueData['logo'],
    ]) </div>
</div>
<div x-show="selectedTab === 3" class="bg-opac_8_black p-4 shadow sm:rounded-lg sm:p-8" x-cloak>
  <div class="w-full">
    @include('profile.venue.about', [
        'description' => $venueData['description'],
    ]) </div>
</div>
<div x-show="selectedTab === 4" class="bg-opac_8_black p-4 shadow sm:rounded-lg sm:p-8" x-cloak>
  <div class="w-full">
    @include('profile.venue.capacity', [
        'capacity' => $venueData['capacity'],
    ]) </div>
</div>
<div x-show="selectedTab === 5" class="bg-opac_8_black p-4 shadow sm:rounded-lg sm:p-8" x-cloak>
  <div class="w-full">
    @include('profile.venue.in-house-gear', [
        'inHouseGear' => $venueData['inHouseGear'],
    ]) </div>
</div>
<div x-show="selectedTab === 6" class="bg-opac_8_black p-4 shadow sm:rounded-lg sm:p-8" x-cloak>
  <div class="w-full">
    @include('profile.venue.my-events', [
        'myEvents' => $venueData['myEvents'],
    ]) </div>
</div>
<div x-show="selectedTab === 7" class="bg-opac_8_black p-4 shadow sm:rounded-lg sm:p-8" x-cloak>
  <div class="w-full">
    @include('profile.venue.my-bands', [
        'uniqueBands' => $venueData['uniqueBands'],
    ]) </div>
</div>
<div x-show="selectedTab === 8" class="bg-opac_8_black p-4 shadow sm:rounded-lg sm:p-8" x-cloak>
  <div class="w-full">
    @include('profile.venue.my-genres', [
        'genres' => $venueData['genres'],
        'promoterGenres' => $venueData['venueGenres'],
        'venue' => $venueData['venue'],
        'bandTypes' => $venueData['bandTypes'],
    ]) </div>
</div>
<div x-show="selectedTab === 9" x-init="if (selectedTab === 9) { initializeMaps() }" class="bg-opac_8_black p-4 shadow sm:rounded-lg sm:p-8" x-cloak>
  <div class="w-full">
    @include('profile.venue.additional-info', [
        'additionalInfo' => $venueData['additionalInfo'],
    ]) </div>
</div>
