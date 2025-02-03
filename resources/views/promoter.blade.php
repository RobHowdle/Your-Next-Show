<x-guest-layout>
  <x-slot name="header">
    <h1 class="text-center font-heading text-6xl text-white">
      {{ __('Promoter') }}
    </h1>
  </x-slot>

  <div class="mx-auto my-6 w-full max-w-screen-2xl pt-32">
    <div class="relative px-2 shadow-md sm:rounded-lg">
      <div
        class="min-w-screen-xl mx-auto max-w-screen-xl bg-opac_8_black px-4 py-4 text-white md:px-6 md:py-4 lg:px-8 lg:py-6 xl:px-10 xl:py-8 2xl:px-12 2xl:py-10 3xl:px-16 3xl:py-12">
        <div class="header flex justify-center md:justify-start md:gap-4">
          @if ($promoter->logo_url)
            <img src="{{ asset($promoter->logo_url) }}" alt="{{ $promoter->name }} Logo" class="_250img hidden md:block">
          @else
            <img src="{{ asset('images/system/yns_no_image_found.png') }}" alt="No Image"
              class="_250img hidden md:block">
          @endif
          <div class="header-text flex flex-col justify-center gap-2">
            <h1 class="text-sans text-center text-xl md:text-left xl:text-2xl 2xl:text-4xl">{{ $promoter->name }}</h1>
            @if ($promoter->location)
              <div class="group flex flex-row items-center justify-center gap-1 md:justify-start xl:gap-2">
                <i class="fa-solid fa-location-dot mr-2"></i>
                <a class="text-md text-center font-sans underline transition duration-150 ease-in-out hover:text-yns_yellow md:text-left lg:text-lg xl:text-xl 2xl:text-2xl"
                  href="javascript:void(0)" target="_blank" id="open-map-link">{{ $promoter->location }}</a>
              </div>
            @endif
            <div class="text-center md:text-left">
              <x-contact-and-social-links :item="$promoter" />
            </div>
            <div class="rating-wrapper flex flex-row justify-center gap-1 md:justify-start xl:gap-2">
              <p class="h-full place-content-center font-sans md:place-content-end">Overall Rating
                ({{ $reviewCount }}): </p>
              <div class="ratings flex">
                {!! $overallReviews[$promoter->id] !!}
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
          <div class="h-auto border-b border-gray-700 py-4">
            <ul class="align-center flex text-center text-sm font-medium text-gray-400 sm:flex-wrap">
              <li class="tab w-full px-4 py-2 sm:px-6 sm:py-3 md:w-auto">
                <a href="#" data-tab="about" class="tabLinks text-base text-white hover:text-yns_yellow">
                  <span class="fas fa-info-circle mr-2"></span>About
                </a>
              </li>
              <li class="tab w-full px-4 py-2 sm:px-6 sm:py-3 md:w-auto">
                <a href="#" data-tab="my-venues" class="tabLinks text-base text-white hover:text-yns_yellow">
                  <span class="fas fa-cogs mr-2"></span>My Venues
                </a>
              </li>
              <li class="tab w-full px-4 py-2 sm:px-6 sm:py-3 md:w-auto">
                <a href="#" data-tab="bands-and-genres"
                  class="tabLinks text-base text-white hover:text-yns_yellow">
                  <span class="fas fa-guitar mr-2"></span>Genre & Types
                </a>
              </li>
              <li class="tab w-full px-4 py-2 sm:px-6 sm:py-3 md:w-auto">
                <a href="#" data-tab="reviews" class="tabLinks text-base text-white hover:text-yns_yellow">
                  <span class="fas fa-star mr-2"></span> Reviews
                </a>
              </li>
            </ul>
          </div>

          <div class="venue-tab-content mt-4 overflow-auto font-sans text-lg text-white">
            <div id="about">
              @if (!$promoter->description)
                <p>We're still working on this! Come back later to read about us!</p>
              @else
                <p>{!! $promoter->description !!}</p>
              @endif
            </div>

            <div id="my-venues" class="max-h-80 flex h-full flex-col gap-4 overflow-auto">
              @if (!$promoter->my_venues)
                <p>We're still working on this! Come back later to read about us!</p>
              @else
                <p>{{ $promoter->my_venues }}</p>
              @endif
            </div>

            <div id="bands-and-genres">
              @php
                $bandTypes = json_decode($promoter->band_type ?? '[]');
              @endphp
              @if ($bandTypes == [])
                <p class="mb-2">We don't have any specific band types that we prefer to work with, please <a
                    class="underline hover:text-yns_yellow" href="mailto:{{ $promoter->contact_email }}">contact us.</a>
                  if you would like to enquire about promoting your band.</p>
              @else
                <p class="mb-2"><span class="bold">{{ $promoter->name }}</span> specifically promotes
                  @foreach ($bandTypes as $type)
                    @switch($type)
                      @case('original-bands')
                        Original Bands
                      @break

                      @case('cover-bands')
                        Cover Bands
                      @break

                      @case('tribute-bands')
                        Tribute Bands
                      @break

                      @case('all')
                        All Band Types
                      @break

                      @default
                    @endswitch
                  @endforeach
              @endif
              </p>
              @php
                $genres = json_decode($promoter->genre ?? '[]');
              @endphp
              <p class="mb-2">
                @if ($promoter->genre != '[]')
                  We like to work mostly with artists in the
                  @foreach ($genres as $index => $genre)
                    {{ $genre }}@if ($index < count($genres) - 1)
                      ,
                    @endif
                  @endforeach
                  genres.
                @else
                  We haven't got round to adding our genres yet, we're on it!
                @endif
              </p>
            </div>

            <div id="reviews">
              @if ($promoter->recentReviews)
                <p class="text-center">Want to know what we're like? Check out our reviews!</p>
                <div class="ratings-block mt-4 flex flex-col items-center gap-4">
                  <p class="grid grid-cols-2">Communication:
                    <span class="rating-wrapper flex flex-row gap-3">
                      {!! $renderRatingIcons($averageCommunicationRating) !!}
                    </span>
                  </p>
                  <p class="grid grid-cols-2">Rate Of Pay:
                    <span class="rating-wrapper flex flex-row gap-3">
                      {!! $renderRatingIcons($averageRopRating) !!}
                    </span>
                  </p>
                  <p class="grid grid-cols-2">Promotion:
                    <span class="rating-wrapper flex flex-row gap-3">
                      {!! $renderRatingIcons($averagePromotionRating) !!}
                    </span>
                  </p>
                  <p class="grid grid-cols-2">Gig Quality:
                    <span class="rating-wrapper flex flex-row gap-3">
                      {!! $renderRatingIcons($averageQualityRating) !!}
                    </span>
                  </p>
                </div>

                <div class="reviews-block mt-8 flex flex-col gap-4">
                  @foreach ($promoter->recentReviews as $review)
                    <div class="review text-center font-sans">
                      <p class="flex flex-col">"{{ $review->review }}" <span>- {{ $review->author }}</span></p>
                    </div>
                  @endforeach
                </div>
              @else
                <p class="mt-4 text-center">No reviews avaliable for {{ $promoter->name }} yet. Be the first to leave a
                  review!</p>
              @endif
            </div>
          </div>
        </div>
        {{-- <x-suggestion-block :existingVenues="$existingVenues" :venueWithHighestRating="$venueWithHighestRating" :photographerWithHighestRating="$photographerWithHighestRating" :videographerWithHighestRating="$videographerWithHighestRating" :bandWithHighestRating="$bandWithHighestRating"
          :designerWithHighestRating="$designerWithHighestRating" /> --}}

        <x-review-modal title="{{ $promoter->name }}" serviceType="promoter" profileId="{{ $promoter->id }}"
          service="{{ $promoter->name }}" />
      </div>
    </div>
  </div>
</x-guest-layout>
<script>
  document.addEventListener("DOMContentLoaded", function() {
    const openMapLink = document.getElementById("open-map-link");
    const promoterLatitude = "{{ $promoter->latitude }}";
    const promoterLongitude = "{{ $promoter->longitude }}";

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
        window.open(`https://www.google.com/maps?q=${promoterLatitude},${promoterLongitude}`, '_blank');
      }
    }

    // Attach click event listeners
    openMapLink.addEventListener("click", openMap);
  });
</script>
