<header>
  <h2 class="text-md font-heading font-medium text-white">
    {{ __('Your Bands') }}
  </h2>
</header>

<div class="group mb-6">
  <x-input-label-dark>Bands you've worked with</x-input-label-dark>
  <table class="mt-4 w-full border border-white text-left font-sans text-xl rtl:text-right">
    <thead class="border-b border-b-white text-xl text-white underline dark:bg-black">
      <tr class="border-gray-700 odd:dark:bg-black even:dark:bg-gray-900">
        <th class="max-w-md whitespace-normal break-words px-6 py-4 font-sans text-white">Band Name</th>
        <th class="max-w-md whitespace-normal break-words px-6 py-4 font-sans text-white">Location</th>
        <th class="max-w-md whitespace-normal break-words px-6 py-4 font-sans text-white">Genre</th>
        <th class="max-w-md whitespace-normal break-words px-6 py-4 font-sans text-white">Actions</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($uniqueBands as $artist)
        <tr class="border-gray-700 odd:dark:bg-black even:dark:bg-gray-900">
          <td class="max-w-md whitespace-normal break-words px-6 py-4 font-sans text-white">{{ $artist->name }}</td>
          <td class="max-w-md whitespace-normal break-words px-6 py-4 font-sans text-white">
            {{ $artist->location ?? 'No Location' }}
          </td>
          <td class="max-w-md whitespace-normal break-words px-6 py-4 font-sans text-white">
            {{ $artist->genre ?? 'No Genre Available' }}
          </td>
          <td class="max-w-md whitespace-normal break-words px-6 py-4 text-center font-sans text-white">
            {{-- <a href="{{ route('admin.dashboard.show-band', ['dashboardType' => $dashboardType, 'id' => $artist->id]) }}"
              class="text-blue-500 hover:underline">View</a> --}}
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
