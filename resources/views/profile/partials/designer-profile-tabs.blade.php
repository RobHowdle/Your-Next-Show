<div x-show="selectedTab === 2" class="bg-opac_8_black p-4 shadow sm:rounded-lg sm:p-8" x-cloak>
  <div class="w-full">
    <p class="text-xl font-bold">Your Details</p>
    @include('profile.designer.basic-information-form', [
        'name' => $designerData['designerName'],
        'promoterLocation' => $designerData['designerLocation'],
        'promoterPostalTown' => $designerData['designerPostalTown'],
        'promoterLat' => $designerData['designerLat'],
        'promoterLong' => $designerData['designerLong'],
        'contact_name' => $designerData['contact_name'],
        'email' => $designerData['contact_email'],
        'contact_number' => $designerData['contact_number'],
        'platformsToCheck' => $designerData['platformsToCheck'],
        'platforms' => $designerData['platforms'],
        'logo' => $designerData['logo'],
    ])
  </div>
</div>
<div x-show="selectedTab === 3" class="bg-opac_8_black p-4 shadow sm:rounded-lg sm:p-8" x-cloak>
  <div class="w-full">
    <p class="text-xl font-bold">About You</p>

    @include('profile.designer.about', [
        'description' => $designerData['description'],
    ])
  </div>
</div>
@php
  $dashboardData = $designerData ?? ($photographerUserData ?? ($videographerUserData ?? []));
@endphp
<div x-show="selectedTab === 4" class="bg-opac_8_black p-4 shadow sm:rounded-lg sm:p-8" x-cloak>
  <div class="w-full">
    <p class="text-xl font-bold">Portfolio</p>
    @include('profile.designer.portfolio', [
        'waterMarkedPortfolioImages' => $dashboardData['waterMarkedPortfolioImages'],
    ])
  </div>
</div>
<div x-show="selectedTab === 5" class="bg-opac_8_black p-4 shadow sm:rounded-lg sm:p-8" x-cloak>
  <div class="w-full">
    <p class="text-xl font-bold">Genres & Band Types</p>
    @include('profile.designer.my-genres', [
        'genres' => $designerData['genres'],
        'designerGenres' => $designerData['designerGenres'],
        'designer' => $designerData['designer'],
        'bandTypes' => $designerData['bandTypes'],
    ])
  </div>
</div>
<div x-show="selectedTab === 6" class="bg-opac_8_black p-4 shadow sm:rounded-lg sm:p-8" x-cloak>
  <div class="w-full">
    <p class="text-xl font-bold">Design Styles, Print Mediums & Working Times</p>
    @include('profile.designer.styles-and-times', [
        'dashboardType' => $dashboardType,
        'userRole' => $userRole,
        'userId' => $userId,
        'designer' => $designerData['designer'],
        'workingTimes' => $designerData['workingTimes'],
    ])
  </div>
</div>
