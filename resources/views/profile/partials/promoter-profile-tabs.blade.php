<div x-show="activeTab === 'basicInfo'" class="bg-opac_8_black p-4 shadow sm:rounded-lg sm:p-8" x-cloak>
  <div class="w-full">
    <p class="text-xl font-bold">Your Details</p>
    @include('profile.basic-information-form', [
        'name' => $promoterData['promoterName'],
        'venueLocation' => $promoterData['promoterLocation'],
        'promoterPostalTown' => $promoterData['promoterPostalTown'],
        'promoterLat' => $promoterData['promoterLat'],
        'promoterLong' => $promoterData['promoterLong'],
        'contact_name' => $promoterData['contact_name'],
        'email' => $promoterData['contact_email'],
        'contact_number' => $promoterData['contact_number'],
        'platformsToCheck' => $promoterData['platformsToCheck'],
        'platforms' => $promoterData['platforms'],
        'logo' => $promoterData['logo'],
    ])
  </div>
</div>
<div x-show="activeTab === 'description'" class="bg-opac_8_black p-4 shadow sm:rounded-lg sm:p-8" x-cloak>
  <div class="w-full">
    <p class="text-xl font-bold">About You</p>
    @include('profile.about', [
        'description' => $promoterData['description'],
    ])
  </div>
</div>
<div x-show="activeTab === 'myVenues'" class="bg-opac_8_black p-4 shadow sm:rounded-lg sm:p-8" x-cloak>
  <div class="w-full">
    <p class="text-xl font-bold">Your Venues</p>
    @include('profile.promoter.my-venues', [
        'myVenues' => $promoterData['myVenues'],
    ])
  </div>
</div>
<div x-show="activeTab === 'myEvents" class="bg-opac_8_black p-4 shadow sm:rounded-lg sm:p-8" x-cloak>
  <div class="w-full">
    <p class="text-xl font-bold">My Events</p>
    @include('profile.promoter.my-events', [
        'myEvents' => $promoterData['myEvents'],
    ])
  </div>
</div>
<div x-show="activeTab === 'myBands" class="bg-opac_8_black p-4 shadow sm:rounded-lg sm:p-8" x-cloak>
  <div class="w-full">
    <p class="text-xl font-bold">My Artists</p>
    @include('profile.promoter.my-bands', [
        'uniqueBands' => $promoterData['uniqueBands'],
    ])
  </div>
</div>
<div x-show="activeTab === 'genresAndTypes'" class="bg-opac_8_black p-4 shadow sm:rounded-lg sm:p-8" x-cloak>
  <div class="w-full">
    <p class="text-xl font-bold">Genres & Band Types</p>
    @include('profile.promoter.my-genres', [
        'genres' => $promoterData['genres'],
        'promoterGenres' => $promoterData['promoterGenres'],
        'promoter' => $promoterData['promoter'],
        'bandTypes' => $promoterData['bandTypes'],
    ])
  </div>
</div>
@if (isset($modules['jobs']) && $modules['jobs']['is_enabled'])
  <div x-show="activeTab === 'packages" class="bg-opac_8_black p-4 shadow sm:rounded-lg sm:p-8" x-cloak>
    <div class="w-full">
      @include('profile.packages', [
          'profileData' => $promoterData['packages'],
      ])
    </div>
  </div>
@endif
{{-- <div x-show="selectedTab === 8" class="bg-opac_8_black p-4 shadow sm:rounded-lg sm:p-8" x-cloak>
  <div class="w-full">
    <p class="text-xl font-bold">Ticket API Keys</p>
    @include('profile.promoter.api-keys', [
        'dashboardType' => $dashboardType,
        'promoterId' => $promoterData['promoterId'],
        'apiProviders' => $promoterData['apiProviders'],
        'apiKeys' => $promoterData['apiKeys'],
    ])
  </div>
</div> --}}
