<tr class="odd:bg-gray-800 even:bg-gray-600">
  <td
    class="whitespace-nowrap font-sans text-white sm:px-2 sm:py-3 sm:text-base md:px-6 md:py-2 md:text-lg lg:px-8 lg:py-4">
    {{ $promoter->name }}</td>

  <td
    class="whitespace-nowrap font-sans text-white sm:px-2 sm:py-3 sm:text-base md:px-6 md:py-2 md:text-lg lg:px-8 lg:py-4">
    <x-button class="join-promoter-btn" label="Join Promoter" data-promoter-id="{{ $promoter->id }}"></x-button>
  </td>
</tr>
