<header>
  <h2 class="text-md font-heading font-medium text-white">
    {{ __('Your Artists') }}
  </h2>
</header>
<div class="group mb-6">
  <x-input-label-dark>Artists you've recently worked with</x-input-label-dark>
  <table class="mt-4 w-full border border-white text-left font-sans text-xl rtl:text-right">
    <thead class="border-b border-b-white text-xl text-white underline dark:bg-black">
      <tr class="border-gray-700 odd:dark:bg-black even:dark:bg-gray-900">
        <th class="max-w-md whitespace-normal break-words px-6 py-4 font-sans text-white">Artist</th>
        <th class="max-w-md whitespace-normal break-words px-6 py-4 font-sans text-white">Location</th>
        <th class="max-w-md whitespace-normal break-words px-6 py-4 font-sans text-white">Genre</th>
        <th class="max-w-md whitespace-normal break-words px-6 py-4 font-sans text-white">Average Rating</th>
        <th class="max-w-md whitespace-normal break-words px-6 py-4 font-sans text-white">Actions</th>
      </tr>
    </thead>
    <tbody>
      @if (
          !isset($profileData['uniqueBands']) ||
              (is_array($profileData['uniqueBands']) && empty($profileData['uniqueBands'])) ||
              (is_object($profileData['uniqueBands']) && $profileData['uniqueBands']->isEmpty()))
        <tr class="border-gray-700 odd:dark:bg-black even:dark:bg-gray-900">
          <td class="max-w-md whitespace-normal break-words px-6 py-4 text-center font-sans text-white" colspan="5">
            No artists found.
          </td>
        </tr>
      @else
        @foreach ($profileData['uniqueBands'] as $band)
          <tr class="border-gray-700 odd:dark:bg-black even:dark:bg-gray-900">
            <td class="max-w-md whitespace-normal break-words px-6 py-4 font-sans text-white">{{ $band['name'] }}</td>
            <td class="max-w-md whitespace-normal break-words px-6 py-4 font-sans text-white">
              {{ $band['location'] ?? 'No Location' }}
            </td>
            <td class="max-w-md whitespace-normal break-words px-6 py-4 font-sans text-white">
              {{ $band['genre'] ?? 'No Genre Available' }}
            </td>
            <td class="max-w-md whitespace-normal break-words px-6 py-4 font-sans text-white">
              {{ $band['average_rating'] ?? 'No Rating Available' }}
            </td>
            <td class="max-w-md whitespace-normal break-words px-6 py-4 text-center font-sans text-white">
              <a href="{{ route('singleService', ['serviceType' => $band['services'], 'name' => $band['name']]) }}"
                target="_blank" class="text-blue-500 hover:underline">View</a>
            </td>
          </tr>
        @endforeach
      @endif
    </tbody>
  </table>
</div>
