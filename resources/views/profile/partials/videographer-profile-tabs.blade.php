<div x-show="selectedTab === 2" class="bg-opac_8_black p-4 shadow sm:rounded-lg sm:p-8" x-cloak>
  <div class="w-full">
    <p class="text-xl font-bold">Your Details</p>
    @include('profile.photographer.basic-information-form', [
        'name' => $videographerData['photographerName'],
        'photographerLocation' => $videographerData['photographerLocation'],
        'photographerPostalTown' => $videographerData['photographerPostalTown'],
        'photographerLat' => $videographerData['photographerLat'],
        'photographerLong' => $videographerData['photographerLong'],
        'contact_name' => $videographerData['contact_name'],
        'contact_email' => $videographerData['contact_email'],
        'contact_number' => $videographerData['contact_number'],
        'platforms' => $videographerData['platforms'],
        'platformsToCheck' => $videographerData['platformsToCheck'],
        'logo' => $videographerData['logo'],
    ])
  </div>
</div>
<div x-show="selectedTab === 3" class="bg-opac_8_black p-4 shadow sm:rounded-lg sm:p-8" x-cloak>
  <div class="w-full">
    <p class="text-xl font-bold">About You</p>
    @include('profile.photographer.about', [
        'description' => $videographerData['description'],
    ])
  </div>
</div>
@php
  $dashboardData = $videographerData ?? ($designerData ?? ($videographerUserData ?? []));
@endphp
<div x-show="selectedTab === 4" class="bg-opac_8_black p-4 shadow sm:rounded-lg sm:p-8" x-cloak>
  <div class="w-full">
    <p class="text-xl font-bold">Portfolio</p>
    @include('profile.photographer.portfolio', [
        'waterMarkedPortfolioImages' => $dashboardData['waterMarkedPortfolioImages'],
    ])
  </div>
</div>
<div x-show="selectedTab === 5" class="bg-opac_8_black p-4 shadow sm:rounded-lg sm:p-8" x-cloak>
  <div class="w-full">
    <p class="text-xl font-bold">Genres & Band Types</p>
    @include('profile.photographer.my-genres', [
        'dashboardType' => $dashboardType,
        'userRole' => $userRole,
        'genres' => $videographerData['genres'],
        'photographerGenres' => $videographerData['photographerGenres'],
        'userId' => $userId,
        'photographer' => $videographerData['photographer'],
        'isAllGenres' => $videographerData['isAllGenres'],
        'bandTypes' => $videographerData['bandTypes'],
    ])
  </div>
</div>
<div x-show="selectedTab === 6" class="bg-opac_8_black p-4 shadow sm:rounded-lg sm:p-8" x-cloak>
  <div class="w-full">
    <p class="text-xl font-bold">Environments & Working Times</p>
    @include('profile.photographer.environments-and-times', [
        'dashboardType' => $dashboardType,
        'userRole' => $userRole,
        'userId' => $userId,
        'photographer' => $videographerData['photographer'],
        'environmentTypes' => $videographerData['environmentTypes'],
        'groups' => $videographerData['groups'],
        'workingTimes' => $videographerData['workingTimes'],
    ])
  </div>
</div>
