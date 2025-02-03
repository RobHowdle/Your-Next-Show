@props(['venues', 'genres', 'venuePromoterCount'])

<div class="mx-auto min-h-screen w-full max-w-screen-2xl px-2 pt-24 md:pt-32">
  <h1 class="py-4 text-center font-heading text-3xl text-white md:py-6 md:text-4xl xl:text-5xl 4xl:text-6xl">Venues</h1>

  <div class="relative shadow-md sm:rounded-lg">
    <x-filter-accordion-and-search :genres="$genres" />
  </div>

  <div class="relative z-0 overflow-x-auto">
    <table class="w-full border border-white text-left font-sans rtl:text-right" id="venues">
      <thead class="bg-black text-white underline">
        <tr>
          <th scope="col" class="px-2 py-2 text-base md:px-6 md:py-3 md:text-xl lg:px-8 lg:py-4 lg:text-2xl">Venue
          </th>
          <th scope="col"
            class="hidden px-2 py-2 text-base md:px-6 md:py-3 md:text-xl lg:flex lg:px-8 lg:py-4 lg:text-2xl">Rating
          </th>
          <th scope="col" class="px-2 py-2 text-base md:px-6 md:py-3 md:text-xl lg:px-8 lg:py-4 lg:text-2xl">
            Location
          </th>
          <th scope="col"
            class="hidden px-2 py-2 text-base md:block md:px-6 md:py-3 md:text-xl lg:px-8 lg:py-4 lg:text-2xl">Contact
          </th>
          <th scope="col"
            class="md-text-2xl {{ $venuePromoterCount > 0 ? 'md:block' : 'hidden' }} px-2 py-2 sm:text-xl md:px-6 md:py-3 lg:px-8 lg:py-4">
            Promoter</th>
        </tr>
      </thead>
      <tbody id="resultsTableBody">
        {{ $slot }}
      </tbody>
    </table>
  </div>
</div>

<div class="mt-4 bg-yns_dark_gray px-4 py-4">
  {{ $venues->links() }}
</div>
