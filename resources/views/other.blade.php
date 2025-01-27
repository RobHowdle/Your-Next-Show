<x-guest-layout>
  <x-slot name="header">
    <h1 class="text-center font-heading text-6xl text-white">
      {{ __('Other') }}
    </h1>
  </x-slot>

  <div class="mx-auto min-h-screen w-full max-w-screen-2xl place-content-center px-2 pt-24 md:pt-32">
    <h1 class="py-4 text-center font-heading text-3xl text-white md:py-6 md:text-4xl xl:text-5xl 4xl:text-6xl">Other
      Services</h1>
    <div class="relative shadow-md sm:rounded-lg">
      <div class="mx-auto grid grid-cols-2 gap-x-6 gap-y-16 px-2 xl:grid-cols-4 xl:px-8">
        @forelse ($otherServices as $other)
          <div class="service-block justify-self-center text-center">
            <a class="service-overlay"
              href="{{ route('singleServiceGroup', ['serviceType' => lcfirst($other->otherServiceList->service_name)]) }}">
              <img src={{ $other->otherServiceList->image_url }}>
              <p
                class="bg-black px-0 py-2 text-white transition duration-150 ease-in-out hover:text-yns_yellow md:px-4 xl:px-6">
                {{ $other->otherServiceList->service_name }} (
                {{ $serviceCounts[$other->other_service_id] ?? 0 }})</p>
            </a>
          </div>
        @empty
          <div class="col-start-1 col-end-5 bg-opac_8_black p-20 text-center text-2xl text-white">
            <p>Sorry! We don't have anything here yet. Come back later and try again.</p>
          </div>
        @endforelse
      </div>
    </div>
  </div>
</x-guest-layout>
