<x-guest-layout>
  <x-slot name="header">
    <h1 class="text-center font-heading text-6xl text-white">
      {{ __('Other') }}
    </h1>
  </x-slot>
  <div class="mx-auto my-6 w-full max-w-screen-2xl pt-32">
    <div class="relative px-2 shadow-md sm:rounded-lg">
      <div
        class="min-w-screen-xl mx-auto max-w-screen-xl bg-opac_8_black px-4 py-4 text-white md:px-6 md:py-4 lg:px-8 lg:py-6 xl:px-10 xl:py-8 2xl:px-12 2xl:py-10 3xl:px-16 3xl:py-12">
        <div class="header flex justify-center md:justify-start md:gap-4">
          @php
            $imagePath = public_path($singleService->logo_url);
          @endphp
          @if ($singleService->logo_url && file_exists($imagePath))
            <img src="{{ asset($singleService->logo_url) }}" alt="{{ $singleService->name }} Logo"
              class="_250img hidden md:block">
          @else
            <img src="{{ asset('images/system/yns_no_image_found.png') }}" alt="No Image"
              class="_250img hidden md:block">
          @endif
          <div class="header-text flex flex-col justify-center gap-2">
            <h1 class="text-sans text-center text-xl md:text-left xl:text-2xl 2xl:text-4xl">{{ $singleService->name }}
            </h1>
            @if ($singleService->location)
              <div class="group flex flex-row items-center justify-center gap-1 md:justify-start xl:gap-2">
                <i class="fa-solid fa-location-dot mr-2"></i>
                <a class="text-md text-center font-sans underline transition duration-150 ease-in-out hover:text-yns_yellow md:text-left lg:text-lg xl:text-xl 2xl:text-2xl"
                  href="javascript:void(0)" target="_blank" id="open-map-link">{{ $singleService->location }}</a>
              </div>
            @endif
            <div class="text-center md:text-left">
              <x-contact-and-social-links :item="$singleService" />
            </div>
            <div class="rating-wrapper flex flex-row justify-center gap-1 md:justify-start xl:gap-2">
              <p class="h-full place-content-center font-sans md:place-content-end">
                Overall Rating ({{ $serviceData['reviewCount'] ?? 0 }})
              </p>
              <div class="ratings flex">
                {!! $serviceData['overallReviews'][$singleService->id] !!}
              </div>
            </div>
            <div class="leave-review">
              <button
                class="w-full rounded bg-gradient-to-t from-yns_dark_orange to-yns_yellow px-6 py-2 text-sm text-black transition duration-150 ease-in-out hover:bg-yns_yellow md:w-auto"
                data-modal-toggle="review-modal" type="button">Leave a review</button>
            </div>
          </div>
        </div>

        <div class="body">
          {{-- Tabs --}}
          @include("components.{$singleService->services}-service-tabs")

          {{-- Tab Content --}}
          @include("components.{$singleService->services}-service-tab-content", [
              'serviceData' => $serviceData,
          ])
          {{-- <x-suggestion-block :promoterWithHighestRating="$promoterWithHighestRating" :photographerWithHighestRating="$photographerWithHighestRating" :videographerWithHighestRating="$videographerWithHighestRating" :bandWithHighestRating="$bandWithHighestRating"
            :designerWithHighestRating="$designerWithHighestRating" /> --}}
          <x-review-modal title="{{ $singleService->name }}" serviceType="{{ $singleService->services }}"
            profileId="{{ $singleService->id }}" service="{{ $singleService->name }}" />
        </div>
      </div>
    </div>
  </div>
</x-guest-layout>
<script src="https://open.spotify.com/embed/iframe-api/v1" async></script>

<script>
  document.addEventListener("DOMContentLoaded", function() {
    const openMapLink = document.getElementById("open-map-link");
    const lat = "{{ $singleService->latitude }}";
    const long = "{{ $singleService->longitude }}";

    // Function to detect if the user is on a mobile device
    function isMobileDevice() {
      return /Mobi|Android/i.test(navigator.userAgent);
    }

    // Function to open the map
    function openMap() {
      // First, check if it's a mobile device
      if (isMobileDevice()) {
        // For mobile, try geo URI
        const geoURI = `geo:${promoterLatitude},${promoterLongitude}`;
        window.location.href = geoURI;
      } else {
        // If not mobile, fall back to Google Maps
        window.open(`https://www.google.com/maps?q=${lat},${long}`, '_blank');
      }
    }

    // Attach click event listeners
    openMapLink.addEventListener("click", openMap);
  });
</script>
