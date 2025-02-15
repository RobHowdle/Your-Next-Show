<x-guest-layout>

  <div class="mx-auto min-h-screen max-w-7xl pb-20">
    <div class="px-4 pt-36 sm:px-6 lg:px-8">
      <div class="relative mb-8 overflow-hidden rounded-2xl bg-yns_dark_blue shadow-2xl">
        <h1 class="mb-4 mt-8 text-center font-heading text-4xl font-bold text-white md:text-5xl lg:text-6xl">
          Find Your Next <span class="text-yns_yellow">Service</span>
        </h1>
        <p class="mx-auto mb-8 mt-4 max-w-2xl text-center text-lg text-gray-400">
          Discover additional services to enhance your events and performances
        </p>
      </div>
    </div>

    <div class="mt-16">
      @if ($otherServices->isNotEmpty())
        <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
          @foreach ($otherServices as $other)
            <div class="group relative overflow-hidden rounded-lg border border-gray-800 bg-black/20">
              <div class="relative h-0 pb-[100%]">
                <img src="{{ $other->otherServiceList->image_url }}" alt="{{ $other->otherServiceList->service_name }}"
                  class="absolute inset-0 h-full w-full object-cover transition-transform duration-300 group-hover:scale-105">
                <!-- Updated gradient overlay -->
                <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/50 to-transparent"></div>
              </div>

              <a href="{{ route('singleServiceGroup', ['serviceType' => lcfirst($other->otherServiceList->service_name)]) }}"
                class="absolute inset-0 flex flex-col items-center justify-end p-6 text-center">
                <h3
                  class="font-heading text-xl font-bold text-white transition duration-300 ease-in-out group-hover:text-yns_yellow">
                  {{ $other->otherServiceList->service_name }}
                </h3>
                <!-- Added backdrop blur and padding to the count -->
                <p class="mt-2 rounded bg-black/30 px-3 py-1 text-sm text-gray-200 backdrop-blur-sm">
                  {{ $serviceCounts[$other->other_service_id] ?? 0 }} available
                </p>
              </a>
            </div>
          @endforeach
          <h3 class="col-span-4 mt-8 text-center font-heading text-xl font-bold text-white">With many more on the way!
          </h3>

        </div>
      @else
        <div class="rounded-lg border border-gray-800 bg-black/20 p-8 text-center">
          <h3 class="font-heading text-xl font-bold text-white">No Services Available</h3>
          <p class="mt-2 text-gray-400">
            We don't have any services listed at the moment. Please check back later.
          </p>
        </div>
      @endif
    </div>
  </div>
</x-guest-layout>
