<div x-show="activeTab === 'basicInfo'" class="bg-opac_8_black p-4 shadow sm:rounded-lg sm:p-8" x-cloak>
  <div class="w-full">
    @include('profile.basic-information-form', [
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
<div x-show="activeTab === 'description'" class="bg-opac_8_black p-4 shadow sm:rounded-lg sm:p-8" x-cloak>
  <div class="w-full">
    @include('profile.venue.about', [
        'description' => $venueData['description'],
    ]) </div>
</div>
<div x-show="activeTab === 'genresAndTypes'" class="bg-opac_8_black p-4 shadow sm:rounded-lg sm:p-8" x-cloak>
  <div class="w-full">
    @include('profile.venue.my-genres', [
        'genres' => $venueData['genres'],
        'promoterGenres' => $venueData['venueGenres'],
        'venue' => $venueData['venue'],
        'bandTypes' => $venueData['bandTypes'],
    ]) </div>
</div>
<div x-show="activeTab === 'capacity" class="bg-opac_8_black p-4 shadow sm:rounded-lg sm:p-8" x-cloak>
  <div class="w-full">
    @include('profile.venue.capacity', [
        'capacity' => $venueData['capacity'],
    ]) </div>
</div>
<div x-show="activeTab === 'inHouseGear" class="bg-opac_8_black p-4 shadow sm:rounded-lg sm:p-8" x-cloak>
  <div class="w-full">
    @include('profile.venue.in-house-gear', [
        'inHouseGear' => $venueData['inHouseGear'],
    ]) </div>
</div>
<div x-show="activeTab === 'myEvents" class="bg-opac_8_black p-4 shadow sm:rounded-lg sm:p-8" x-cloak>
  <div class="w-full">
    @include('profile.venue.my-events', [
        'myEvents' => $venueData['myEvents'],
    ]) </div>
</div>
<div x-show="activeTab === 'myBands" class="bg-opac_8_black p-4 shadow sm:rounded-lg sm:p-8" x-cloak>
  <div class="w-full">
    @include('profile.venue.my-bands', [
        'uniqueBands' => $venueData['uniqueBands'],
    ]) </div>
</div>
<div x-show="activeTab === 'additionalInfo" class="bg-opac_8_black p-4 shadow sm:rounded-lg sm:p-8" x-cloak>
  <div class="w-full">
    @include('profile.venue.additional-info', [
        'additionalInfo' => $venueData['additionalInfo'],
    ]) </div>
</div>
@if (isset($modules['jobs']) && $modules['jobs']['is_enabled'])
  <div x-show="activeTab === 'packages" class="bg-opac_8_black p-4 shadow sm:rounded-lg sm:p-8" x-cloak>
    <div class="w-full">
      @include('profile.packages', [
          'profileData' => $venueData['packages'],
      ])
    </div>
  </div>
@endif
<div x-show="activeTab === 'lmlc" class="bg-opac_8_black p-4 shadow sm:rounded-lg sm:p-8" x-cloak>
  <div class="w-full">
    @include('profile.venue.additional-info', [
        'lmlc' => $venueData['lmlc'],
    ]) </div>
</div>
