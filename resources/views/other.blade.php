<x-guest-layout>
  <div class="mx-auto min-h-screen max-w-7xl pb-20">
    <!-- Adjusted padding for mobile -->
    <div class="px-4 pt-24 sm:px-6 sm:pt-36 lg:px-8">
      <div class="relative mb-8 overflow-hidden rounded-2xl bg-yns_dark_blue shadow-2xl">
        <!-- Responsive text sizes -->
        <h1 class="mb-4 mt-8 text-center font-heading text-3xl font-bold text-white md:text-4xl lg:text-6xl">
          Find Your Next <span class="text-yns_yellow">Service</span>
        </h1>
        <p class="mx-auto mb-8 mt-4 max-w-2xl px-4 text-center text-base text-gray-400 sm:text-lg">
          Discover additional services to enhance your events and performances
        </p>
      </div>
    </div>

    <!-- Adjusted grid spacing and columns for different screen sizes -->
    <div class="mt-8 px-4 sm:mt-16 sm:px-6 lg:px-8">
      @if ($otherServices->isNotEmpty())
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-6 lg:grid-cols-3 lg:gap-8 xl:grid-cols-4">
          @foreach ($otherServices as $other)
            <div class="group relative overflow-hidden rounded-lg border border-gray-800 bg-black/20">
              <!-- Adjusted aspect ratio container -->
              <div class="relative h-0 pb-[75%] sm:pb-[100%]">
                <img src="{{ $other->otherServiceList->image_url }}" alt="{{ $other->otherServiceList->service_name }}"
                  class="absolute inset-0 h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                  loading="lazy">
                <!-- Enhanced gradient overlay for better text visibility -->
                <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/50 to-transparent"></div>
              </div>

              <a href="{{ route('singleServiceGroup', ['serviceType' => lcfirst($other->otherServiceList->service_name)]) }}"
                class="absolute inset-0 flex flex-col items-center justify-end p-4 text-center sm:p-6">
                <h3
                  class="font-heading text-lg font-bold text-white transition duration-300 ease-in-out group-hover:text-yns_yellow sm:text-xl">
                  {{ $other->otherServiceList->service_name }}
                </h3>
                <!-- Enhanced service count display -->
                <p class="mt-2 rounded-full bg-black/40 px-4 py-1.5 text-sm text-gray-200 backdrop-blur-sm">
                  {{ $serviceCounts[$other->other_service_id] ?? 0 }} available
                </p>
              </a>
            </div>
          @endforeach

          <!-- Responsive heading for "more coming" message -->
          <div class="col-span-full mt-8">
            <h3 class="text-center font-heading text-lg font-bold text-white sm:text-xl">
              With many more on the way!
            </h3>
          </div>
        </div>
      @else
        <!-- Enhanced empty state -->
        <div class="mx-4 rounded-lg border border-gray-800 bg-black/20 p-6 text-center sm:mx-0 sm:p-8">
          <h3 class="font-heading text-lg font-bold text-white sm:text-xl">No Services Available</h3>
          <p class="mt-2 text-sm text-gray-400 sm:text-base">
            We don't have any services listed at the moment. Please check back later.
          </p>
        </div>
      @endif
    </div>
  </div>
</x-guest-layout>
