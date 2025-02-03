<x-guest-layout>
  <x-slot name="header">
    <h1 class="text-center font-heading text-6xl text-white">
      {{ __('Venue') }}
    </h1>
  </x-slot>

  <div class="mx-auto my-6 w-full max-w-screen-2xl pt-32">
    <div class="relative px-2 shadow-md sm:rounded-lg">
      <div
        class="min-w-screen-xl mx-auto max-w-screen-xl bg-opac_8_black px-4 py-4 text-white md:px-6 md:py-4 lg:px-8 lg:py-6 xl:px-10 xl:py-8 2xl:px-12 2xl:py-10 3xl:px-16 3xl:py-12">
        <div class="header flex justify-center md:justify-start md:gap-4">
          @php
            $imagePath = public_path($venue->logo_url);
          @endphp
          @if ($venue->logo_url && file_exists($imagePath))
            <img src="{{ asset($venue->logo_url) }}" alt="{{ $venue->name }} Logo" class="_250img hidden md:block">
          @else
            <img src="{{ asset('images/system/yns_no_image_found.png') }}" alt="No Image"
              class="_250img hidden md:block">
          @endif
          <div class="header-text flex flex-col items-center justify-center gap-2 md:items-start">
            <h1 class="text-sans text-center text-xl md:text-left xl:text-2xl 2xl:text-4xl">
              {{ $venue->name }}
              @if ($venue->is_verified)
                <span class="verified-badge text-base text-yns_cyan">
                  <i class="fas fa-check-circle"></i> Verified
                </span>
              @endif
            </h1>
            @if ($venue->location)
              <div class="group flex flex-row items-center justify-center gap-1 md:justify-start xl:gap-2">
                <i class="fa-solid fa-location-dot mr-2"></i>
                <a class="text-md text-center font-sans underline transition duration-150 ease-in-out hover:text-yns_yellow md:text-left lg:text-lg xl:text-xl 2xl:text-2xl"
                  href="javascript:void(0)" target="_blank" id="open-map-link">{{ $venue->location }}</a>
              </div>
            @endif
            @if ($venue->w3w)
              <div class="group flex flex-row items-center justify-center gap-1 md:justify-start xl:gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="50 50 50 50" width="16" height="20"
                  class="w3w-icon" fill="currentColor">
                  <path fill="#FFFFFF"
                    d="M67.6525,92.3346a2.7455,2.7455,0,0,1-2.602-3.61L76.0212,55.812a2.7429,2.7429,0,0,1,5.2041,1.7356L70.2546,90.46A2.7429,2.7429,0,0,1,67.6525,92.3346Z" />
                  <path fill="#FFFFFF"
                    d="M51.1965,92.3346a2.7456,2.7456,0,0,1-2.6021-3.61L59.5651,55.812a2.743,2.743,0,0,1,5.2042,1.7356L53.7985,90.46A2.7429,2.7429,0,0,1,51.1965,92.3346Z" />
                  <path fill="#FFFFFF"
                    d="M84.1086,92.3346a2.7456,2.7456,0,0,1-2.6021-3.61L92.4772,55.812a2.743,2.743,0,0,1,5.2042,1.7356L86.7107,90.46A2.743,2.743,0,0,1,84.1086,92.3346Z" />
                </svg>

                <a class="text-md text-center font-sans underline transition duration-150 ease-in-out hover:text-yns_yellow md:text-left lg:text-lg xl:text-xl 2xl:text-2xl"
                  href="javascript:void(0)" target="_blank" id="open-w3w-link">{{ $venue->w3w }}</a>
              </div>
            @endif
            <div class="text-center md:text-left">
              <x-contact-and-social-links :item="$venue" />
            </div>
            <div class="rating-wrapper flex flex-row justify-center gap-1 md:justify-start xl:gap-2">
              <p class="h-full place-content-center font-sans md:place-content-end">Overall Rating
                ({{ $reviewCount }}): </p>
              <div class="ratings flex">
                {!! $overallReviews[$venue->id] !!}
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
                <a href="#" data-tab="in-house-gear" class="tabLinks text-base text-white hover:text-yns_yellow">
                  <span class="fas fa-cogs mr-2"></span>In House Gear
                </a>
              </li>
              <li class="tab w-full px-4 py-2 sm:px-6 sm:py-3 md:w-auto">
                <a href="#" data-tab="band-types-genres"
                  class="tabLinks text-base text-white hover:text-yns_yellow">
                  <span class="fas fa-guitar mr-2"></span>Genre & Types
                </a>
              </li>
              <li class="tab w-full px-4 py-2 sm:px-6 sm:py-3 md:w-auto">
                <a href="#" data-tab="reviews" class="tabLinks text-base text-white hover:text-yns_yellow">
                  <span class="fas fa-star mr-2"></span>Reviews
                </a>
              </li>
              <li class="tab w-full px-4 py-2 sm:px-6 sm:py-3 md:w-auto">
                <a href="#" data-tab="other" class="tabLinks text-base text-white hover:text-yns_yellow">
                  <span class="fas fa-plus mr-2"></span>Other
                </a>
              </li>
            </ul>
          </div>

          <div class="venue-tab-content mt-4 overflow-auto font-sans text-lg text-white">
            <div id="about" class="text-center md:text-left">
              @if (empty($venue->description))
                <p>We're still working on this! Come back later to read about us!</p>
              @else
                <p>{!! $venue->description !!}</p>
              @endif
            </div>

            <div id="in-house-gear" class="max-h-80 flex h-full flex-col gap-4 overflow-auto text-center md:text-left">
              @if (!$venue->in_house_gear || $venue->in_house_gear == 'None')
                <p>We do not have any avaliable in house gear to use so you will be required to bring your own. Please
                  <a class="underline hover:text-yns_yellow" href="mailto:{{ $venue->contact_email }}">contact
                    us.</a> if you have any questions about what you can bring.
                </p>
              @else
                <p>We have the following gear in house. If you require the use of anything imparticular please <a
                    class="underline hover:text-yns_yellow" href="mailto:{{ $venue->contact_email }}">contact
                    us.</a>
                </p>
                <div class="gear-block flex flex-col text-center md:text-left">
                  <p class="text-base text-white">
                    {!! $venue->in_house_gear !!}
                  </p>
                </div>
              @endif
            </div>

            <div id="band-types-genres">
              @php
                $bandTypes = json_decode($venue->band_type ?? '[]');
              @endphp
              @if ($bandTypes == [])
                <p class="text-center md:text-left">We don't have any specific band types listed, please <a
                    class="underline hover:text-yns_yellow" href="mailto:{{ $venue->contact_email }}">contact us.</a>
                  if you would like to enquire about
                  booking
                  your band.</p>
              @else
                <p class="mb-2">The band types that we usually have at <span
                    class="bold">{{ $venue->name }}</span>
                  are:</p>
                <ul class="band-types-list">
                  @foreach ($bandTypes as $type)
                    @switch($type)
                      @case('original-bands')
                        <li class="ml-6">Original Bands</li>
                      @break

                      @case('cover-bands')
                        <li class="ml-6">Cover Bands</li>
                      @break

                      @case('tribute-bands')
                        <li class="ml-6">Tribute Bands</li>
                      @break

                      @case('all')
                        <li class="ml-6">All Band Types</li>
                      @break

                      @default
                    @endswitch
                  @endforeach
                </ul>
                <p class="mt-2 text-center md:text-left">If you would like to enquire about a show, please <a
                    class="underline hover:text-yns_yellow" href="mailto:{{ $venue->email }}">contact us.</a></p>
              @endif

              @php
                $genres = json_decode($venue->genre ?? '[]');
              @endphp
              @if ($venue->genre != '[]')
                <p class="mt-4">The genres that we usually have at {{ $venue->name }} are:</p>

                <ul class="genre-list columns-1 gap-2 md:columns-3 md:gap-4">
                  @foreach ($genreNames as $genre)
                    <li class="ml-6">{{ $genre }}</li>
                  @endforeach
                </ul>

                <p class="mt-4">If you would like to enquire about a show, please <a
                    class="underline hover:text-yns_yellow" href="mailto:{{ $venue->contact_email }}">contact us.</a>
                </p>
              @else
                <p class="mt-4">We don't have a preference on genres of music at {{ $venue->name }}. If you would
                  like to enquire
                  about a show, please <a class="underline hover:text-yns_yellow"
                    href="mailto:{{ $venue->contact_email }}">contact us.</a></p>
              @endif
            </div>

            <div id="reviews">
              @if ($venue->recentReviews)

                <p class="text-center">Want to know what we're like? Check out our reviews!</p>
                <div class="ratings-block mt-4 flex flex-col items-center gap-4">
                  <p class="grid grid-cols-1 text-center md:grid-cols-2 md:text-left">Communication:
                    <span class="rating-wrapper flex flex-row gap-3">
                      {!! $renderRatingIcons($averageCommunicationRating) !!}
                    </span>
                  </p>
                  <p class="grid grid-cols-1 text-center md:grid-cols-2 md:text-left">Rate Of Pay:
                    <span class="rating-wrapper flex flex-row gap-3">
                      {!! $renderRatingIcons($averageRopRating) !!}
                    </span>
                  </p>
                  <p class="grid grid-cols-1 text-center md:grid-cols-2 md:text-left">Promotion:
                    <span class="rating-wrapper flex flex-row gap-3">
                      {!! $renderRatingIcons($averagePromotionRating) !!}
                    </span>
                  </p>
                  <p class="grid grid-cols-1 text-center md:grid-cols-2 md:text-left">Gig Quality:
                    <span class="rating-wrapper flex flex-row gap-3">
                      {!! $renderRatingIcons($averageQualityRating) !!}
                    </span>
                  </p>
                </div>

                <div class="reviews-block mt-8 flex flex-col gap-4">
                  @foreach ($venue->recentReviews as $review)
                    <div class="review text-center font-sans">
                      <p class="flex flex-col">"{{ $review->review }}" <span>- {{ $review->author }}</span></p>
                    </div>
                  @endforeach
                </div>
              @else
                <p class="mt-4 text-center">No reviews avaliable for {{ $venue->name }} yet. Be the first to leave a
                  review!</p>
              @endif
            </div>

            <div id="other">
              @if ($venue->capacity)
                <p class="bold pb-2 text-center text-xl md:text-left md:text-2xl">Other Information you may want to
                  know about
                  {{ $venue->name }}.</p>
                @if ($venue->contact_name)
                  <p class="text-center md:text-left md:text-base">Person(s) To Speak To: {{ $venue->contact_name }}
                  </p>
                @endif
                @if ($venue->capacity)
                  <p class="pb-2 text-center md:text-left">Capacity: {{ $venue->capacity }}</p>
                @endif
                <p class="bold pb-2 pt-2 text-center text-2xl md:text-left">More Info:</p>
                <p class="pb-2 text-center md:text-left">{!! nl2br(e($venue->additional_info)) !!}</p>
              @else
                <p class="text-center md:text-left">No Further Information Avaliable</p>
              @endif
            </div>
          </div>
        </div>
        {{-- <x-suggestion-block :existingPromoters="$existingPromoters" :promoterWithHighestRating="$promoterWithHighestRating" :photographerWithHighestRating="$photographerWithHighestRating" :videographerWithHighestRating="$videographerWithHighestRating" :bandWithHighestRating="$bandWithHighestRating"
          :designerWithHighestRating="$designerWithHighestRating" /> --}}
        <x-review-modal title="{{ $venue->name }}" serviceType="venue" profileId="{{ $venue->id }}"
          service="{{ $venue->name }}" />
      </div>
    </div>
  </div>
</x-guest-layout>
<script>
  document.addEventListener("DOMContentLoaded", function() {
    const openMapLink = document.getElementById("open-map-link");
    const openW3WLink = document.getElementById("open-w3w-link");
    const venueLatitude = "{{ $venue->latitude }}";
    const venueLongitude = "{{ $venue->longitude }}";
    const w3wAddress = "{{ $venue->w3w }}";

    // Function to open the What3Words link
    function openW3W() {
      const geoURI = `https://what3words.com/${w3wAddress}`;
      window.open(geoURI, '_blank');
    }

    // Function to detect if the user is on a mobile device
    function isMobileDevice() {
      return /Mobi|Android/i.test(navigator.userAgent);
    }

    // Function to open the map
    function openMap() {
      // First, check if it's a mobile device
      if (isMobileDevice()) {
        // For mobile, try geo URI
        const geoURI = `geo:${venueLatitude},${venueLongitude}`;
        window.location.href = geoURI;
      } else {
        // If not mobile, fall back to Google Maps
        window.open(`https://www.google.com/maps?q=${venueLatitude},${venueLongitude}`, '_blank');
      }
    }

    // Attach click event listeners
    openMapLink.addEventListener("click", openMap);
    openW3WLink && openW3WLink.addEventListener("click",
      openW3W); // Conditional check in case the element doesn't exist
  });
</script>
