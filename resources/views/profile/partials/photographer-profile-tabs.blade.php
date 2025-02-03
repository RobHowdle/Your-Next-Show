<div x-show="selectedTab === 2" class="bg-opac_8_black p-4 shadow sm:rounded-lg sm:p-8" x-cloak>
  <div class="w-full">
    <p class="text-xl font-bold">Your Details</p>
    @include('profile.photographer.basic-information-form', [
        'name' => $photographerUserData['photographerName'],
        'photographerLocation' => $photographerUserData['photographerLocation'],
        'photographerPostalTown' => $photographerUserData['photographerPostalTown'],
        'photographerLat' => $photographerUserData['photographerLat'],
        'photographerLong' => $photographerUserData['photographerLong'],
        'contact_name' => $photographerUserData['contact_name'],
        'contact_email' => $photographerUserData['contact_email'],
        'contact_number' => $photographerUserData['contact_number'],
        'platforms' => $photographerUserData['platforms'],
        'platformsToCheck' => $photographerUserData['platformsToCheck'],
        'logo' => $photographerUserData['logo'],
    ])
  </div>
</div>
<div x-show="selectedTab === 3" class="bg-opac_8_black p-4 shadow sm:rounded-lg sm:p-8" x-cloak>
  <div class="w-full">
    <p class="text-xl font-bold">About You</p>
    @include('profile.photographer.about', [
        'description' => $photographerUserData['description'],
    ])
  </div>
</div>
@php
  $dashboardData = $photographerUserData ?? ($designerData ?? ($videographerUserData ?? []));
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
        'genres' => $photographerUserData['genres'],
        'photographerGenres' => $photographerUserData['photographerGenres'],
        'userId' => $userId,
        'photographer' => $photographerUserData['photographer'],
        'isAllGenres' => $photographerUserData['isAllGenres'],
        'bandTypes' => $photographerUserData['bandTypes'],
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
        'photographer' => $photographerUserData['photographer'],
        'environmentTypes' => $photographerUserData['environmentTypes'],
        'groups' => $photographerUserData['groups'],
        'workingTimes' => $photographerUserData['workingTimes'],
    ])
  </div>
</div>
