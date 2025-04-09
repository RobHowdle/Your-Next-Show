<tr class="group transition-colors hover:bg-gray-800/50">
  <td class="whitespace-nowrap px-6 py-4">
    <div class="flex items-center">
      <div class="h-10 w-10 flex-shrink-0">
        <img class="h-10 w-10 rounded-full object-cover" src="{{ $artist->logo_url }}" alt="{{ $artist->name }}">
      </div>
      <div class="ml-4">
        <div class="font-medium text-white">{{ $artist->name }}</div>
        <div class="text-sm text-gray-400">{{ $artist->location }}</div>
      </div>
    </div>
  </td>
  <td class="whitespace-nowrap px-6 py-4 text-right">
    <button type="button" data-service-id="{{ $artist->id }}"
      class="join-band-button inline-flex items-center rounded-lg bg-black/50 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-black/70">
      <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
      </svg>
      Join Band
    </button>
  </td>
</tr>
