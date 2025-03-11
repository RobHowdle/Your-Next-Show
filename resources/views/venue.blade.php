<x-guest-layout>
  <div class="relative min-h-screen pb-6 pt-28 md:pb-20 md:pt-36">
    <div class="mx-auto max-w-screen-2xl px-2 md:px-4 lg:px-8">
      <!-- Hero Section -->
      <div class="relative mb-4 overflow-hidden rounded-2xl bg-yns_dark_blue shadow-2xl md:mb-8">
        <div class="absolute inset-0">
          <div class="absolute inset-0 bg-gradient-to-r from-black to-transparent"></div>
          @if ($venue->logo_url && file_exists(public_path($venue->logo_url)))
            <img src="{{ asset($venue->logo_url) }}" alt="{{ $venue->name }}"
              class="h-full w-full object-cover opacity-20">
          @endif
        </div>

        <div class="relative z-10 grid gap-8 px-6 py-6 md:py-12 lg:grid-cols-2 lg:gap-12 lg:px-8">
          <!-- Left Column: Venue Info -->
          <div class="space-y-6">
            <div class="space-y-4">
              <h1 class="font-heading text-4xl font-bold text-white md:text-5xl lg:text-6xl">
                {{ $venue->name }}
                @if ($venue->is_verified)
                  <span
                    class="ml-2 inline-flex items-center rounded-full bg-yns_yellow/10 px-2 py-1 text-sm text-yns_yellow">
                    <svg class="mr-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                      <path
                        d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" />
                    </svg>
                    Verified Venue
                  </span>
                @endif
              </h1>

              <div class="space-y-3">
                @if ($venue->location)
                  <a href="javascript:void(0)" id="open-map-link"
                    class="group flex items-center text-gray-300 transition-colors hover:text-yns_yellow">
                    <span class="fas fa-location-dot mr-2"></span>
                    <span>{{ $venue->location }}</span>
                  </a>
                @endif

                @if ($venue->w3w)
                  <a href="javascript:void(0)" id="open-w3w-link"
                    class="group flex items-center text-gray-300 transition-colors hover:text-yns_yellow">
                    <span class="mr-2">///</span>
                    <span>{{ $venue->w3w }}</span>
                  </a>
                @endif

                <div class="rating-wrapper flex h-full items-center justify-start gap-2">
                  <span class="flex h-full items-center text-gray-300">Overall Rating ({{ $reviewCount }}):</span>
                  <div class="flex h-full items-center">
                    {!! $overallReviews[$venue->id] !!}
                  </div>
                </div>
              </div>

              <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:gap-2 xl:gap-4">
                <div class="order-2 flex-none lg:order-1">
                  <button data-modal-toggle="review-modal"
                    class="group inline-flex w-full items-center justify-center gap-2 rounded-lg bg-yns_yellow px-6 py-3 font-medium text-black transition-all hover:bg-yellow-400 lg:w-auto">
                    <span class="fas fa-star"></span>
                    Leave a Review
                  </button>
                </div>
                <div class="order-1 lg:order-2">
                  <x-contact-and-social-links :item="$venue" class="flex flex-row flex-wrap gap-4" />
                </div>
              </div>
            </div>
          </div>

          <!-- Right Column: Venue Logo -->
          <div class="relative hidden lg:block">
            @if ($venue->logo_url && file_exists(public_path($venue->logo_url)))
              <img src="{{ asset($venue->logo_url) }}" alt="{{ $venue->name }}" class="_250img">
            @else
              <img src="{{ asset('images/system/yns_no_image_found.png') }}" alt="No Image" class="_250img">
            @endif
          </div>
        </div>
      </div>

      <div class="mb-4 space-y-8 lg:hidden">
        <div class="rounded-xl border border-gray-800 bg-yns_dark_blue/75 p-6 backdrop-blur-sm">
          <h2 class="mb-6 font-heading text-xl font-bold text-white">Quick Facts</h2>
          <div class="space-y-4">
            @if ($venue->contact_name)
              <div class="flex items-center gap-3 text-gray-300">
                <span class="fas fa-user text-yns_yellow"></span>
                <span>Contact: {{ $venue->contact_name }}</span>
              </div>
            @endif
            @if ($venue->capacity)
              <div class="flex items-center gap-3 text-gray-300">
                <span class="fas fa-users text-yns_yellow"></span>
                <span>Capacity: {{ number_format($venue->capacity) }}</span>
              </div>
            @endif
          </div>
        </div>
      </div>

      <!-- Tabs Navigation -->
      <div class="mb-4 md:mb-8">
        <div class="rounded-xl bg-yns_dark_blue/75 p-2 backdrop-blur-sm">
          <nav class="grid grid-cols-2 gap-2 md:flex md:flex-wrap" aria-label="Tabs">
            <button data-tab="about"
              class="tabLinks flex w-full items-center justify-center rounded-lg px-4 py-3 text-sm font-medium text-gray-400 transition-all hover:bg-black/20 hover:text-white md:w-auto md:justify-start">
              <span class="fas fa-info-circle mr-2"></span>
              About
            </button>
            <button data-tab="gear"
              class="tabLinks flex w-full items-center justify-center rounded-lg px-4 py-3 text-sm font-medium text-gray-400 transition-all hover:bg-black/20 hover:text-white md:w-auto md:justify-start">
              <span class="fas fa-cogs mr-2"></span>
              In House Gear
            </button>
            <button data-tab="genres"
              class="tabLinks flex w-full items-center justify-center rounded-lg px-4 py-3 text-sm font-medium text-gray-400 transition-all hover:bg-black/20 hover:text-white md:w-auto md:justify-start">
              <span class="fas fa-guitar mr-2"></span>
              Genre & Types
            </button>
            <button data-tab="events"
              class="tabLinks flex w-full items-center justify-center rounded-lg px-4 py-3 text-sm font-medium text-gray-400 transition-all hover:bg-black/20 hover:text-white md:w-auto md:justify-start">
              <span class="fas fa-calendar mr-2"></span>
              Events
            </button>
            <button data-tab="reviews"
              class="tabLinks flex w-full items-center justify-center rounded-lg px-4 py-3 text-sm font-medium text-gray-400 transition-all hover:bg-black/20 hover:text-white md:w-auto md:justify-start">
              <span class="fas fa-star mr-2"></span>
              Reviews
            </button>
            <button data-tab="other"
              class="tabLinks flex w-full items-center justify-center rounded-lg px-4 py-3 text-sm font-medium text-gray-400 transition-all hover:bg-black/20 hover:text-white md:w-auto md:justify-start">
              <span class="fas fa-plus mr-2"></span>
              Other
            </button>
          </nav>
        </div>
      </div>

      <!-- Tab Contents -->
      <div class="grid gap-8 lg:grid-cols-3">
        <div class="lg:col-span-2">
          <div
            class="min-h-[400px] rounded-xl rounded-r-xl border border-gray-800 bg-yns_dark_blue/75 p-8 backdrop-blur-sm">
            <!-- About Tab -->
            <div id="about" class="tab-panel hidden">
              <div class="prose-invert prose max-w-none">
                {!! $venue->description !!}
              </div>
            </div>

            <!-- In House Gear Tab -->
            <div id="gear" class="tab-panel hidden">
              @if (!$venue->in_house_gear || $venue->in_house_gear == 'None')
                <p>We do not have any available in house gear...</p>
              @else
                {!! $venue->in_house_gear !!}
              @endif
            </div>

            <!-- Genres Tab -->
            <div id="genres" class="tab-panel hidden">
              @php
                $bandTypes = json_decode($venue->band_type ?? '[]');
              @endphp
              @if ($bandTypes == [])
                <p class="text-left">We don't have any specific band types listed, please <a
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
                <p class="mt-2 text-left">If you would like to enquire about a show, please <a
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

            <div id="events" class="tab-panel hidden">
              <div class="space-y-6">
                @if ($upcomingEvents && $upcomingEvents->count() > 0)
                  <p class="text-center text-lg text-gray-300">Upcoming events at {{ $venue->name }} in the next
                    month:</p>

                  <div class="grid gap-4 md:grid-cols-2">
                    @foreach ($upcomingEvents as $event)
                      <div
                        class="group rounded-lg border border-gray-800 bg-black/20 p-4 transition-all hover:border-yns_yellow">
                        <a href="{{ route('public-event', ['eventId' => $event->id]) }}" class="block">
                          <div class="flex items-start justify-between gap-4">
                            <div>
                              <h3 class="font-heading text-lg font-bold text-white group-hover:text-yns_yellow">
                                {{ $event->event_name }}
                              </h3>
                              <p class="text-sm text-gray-400">
                                {{ Carbon\Carbon::parse($event->event_date)->format('D jS M Y') }}
                              </p>
                              @if ($event->start_time)
                                <p class="text-sm text-gray-400">
                                  Doors: {{ Carbon\Carbon::parse($event->start_time)->format('g:ia') }}
                                </p>
                              @endif
                            </div>
                            <div class="text-right">
                              @if ($event->ticket_url)
                                <a href="{{ $event->ticket_url }}" target="_blank"
                                  class="inline-flex items-center gap-2 rounded-lg bg-yns_yellow px-4 py-2 text-sm font-medium text-black transition-all hover:bg-yellow-400">
                                  <span class="fas fa-ticket"></span>
                                  Tickets
                                </a>
                              @elseif(!$event->ticket_url && $event->on_the_door_ticket_price)
                                <p class="text-sm font-bold text-yns_yellow">
                                  £{{ number_format($event->on_the_door_ticket_price, 2) }}
                                </p>
                              @endif
                            </div>
                          </div>
                        </a>
                      </div>
                    @endforeach
                  </div>
                @else
                  <p class="text-left text-lg text-gray-300">
                    No upcoming events scheduled at {{ $venue->name }} in the next month.
                  </p>
                @endif
              </div>
            </div>

            <!-- Reviews Tab -->
            <div id="reviews" class="tab-panel hidden">
              <div class="space-y-6">
                @if ($venue->recentReviews && $venue->recentReviews->count() > 0)
                  <p class="text-center">Want to know what we're like? Check out our reviews!</p>

                  <!-- Detailed Ratings -->
                  <div class="ratings-block mt-4 flex flex-col items-center gap-4">
                    <p class="grid grid-cols-1 text-left md:grid-cols-2">
                      Communication:
                      <span class="rating-wrapper flex flex-row gap-3">
                        {!! $renderRatingIcons($averageCommunicationRating) !!}
                      </span>
                    </p>
                    <p class="grid grid-cols-1 text-left md:grid-cols-2">
                      Rate Of Pay:
                      <span class="rating-wrapper flex flex-row gap-3">
                        {!! $renderRatingIcons($averageRopRating) !!}
                      </span>
                    </p>
                    <p class="grid grid-cols-1 text-left md:grid-cols-2">
                      Promotion:
                      <span class="rating-wrapper flex flex-row gap-3">
                        {!! $renderRatingIcons($averagePromotionRating) !!}
                      </span>
                    </p>
                    <p class="grid grid-cols-1 text-left md:grid-cols-2">
                      Gig Quality:
                      <span class="rating-wrapper flex flex-row gap-3">
                        {!! $renderRatingIcons($averageQualityRating) !!}
                      </span>
                    </p>
                  </div>

                  <!-- Review Comments -->
                  <div class="reviews-block mt-8 flex flex-col gap-4">
                    @foreach ($venue->recentReviews as $review)
                      <div class="review text-left font-sans">
                        <p class="flex flex-col">
                          "{{ $review->review }}"
                          <span>- {{ $review->author }}</span>
                        </p>
                      </div>
                    @endforeach
                  </div>
                @else
                  <p class="mt-4 text-left">
                    No reviews available for {{ $venue->name }} yet. Be the first to leave a review!
                  </p>
                @endif
              </div>
            </div>

            <!-- Other Tab -->
            <div id="other" class="tab-panel hidden">
              @if ($venue->capacity)
                <p class="bold pb-2 text-left text-xl md:text-2xl">Other Information you may want to
                  know about
                  {{ $venue->name }}.</p>
                @if ($venue->contact_name)
                  <p class="text-left md:text-base">Person(s) To Speak To: {{ $venue->contact_name }}
                  </p>
                @endif
                @if ($venue->capacity)
                  <p class="pb-2 text-left">Capacity: {{ $venue->capacity }}</p>
                @endif
                <p class="bold pb-2 pt-2 text-left text-2xl">More Info:</p>
                <p class="pb-2 text-left">{!! nl2br(e($venue->additional_info)) !!}</p>
              @else
                <p class="text-left">No Further Information Avaliable</p>
              @endif
            </div>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="hidden space-y-8 lg:block">
          <div class="rounded-xl border border-gray-800 bg-yns_dark_blue/75 p-6 backdrop-blur-sm">
            <h2 class="mb-6 font-heading text-xl font-bold text-white">Quick Facts</h2>
            <div class="space-y-4">
              @if ($venue->contact_name)
                <div class="flex items-center gap-3 text-gray-300">
                  <span class="fas fa-user text-yns_yellow"></span>
                  <span>Contact: {{ $venue->contact_name }}</span>
                </div>
              @endif
              @if ($venue->capacity)
                <div class="flex items-center gap-3 text-gray-300">
                  <span class="fas fa-users text-yns_yellow"></span>
                  <span>Capacity: {{ number_format($venue->capacity) }}</span>
                </div>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Add necessary modals -->
  <x-review-modal title="{{ $venue->name }}" serviceType="venue" profileId="{{ $venue->id }}"
    service="{{ $venue->name }}" />
</x-guest-layout>
<style>
  .fade-in {
    animation: fadeIn 0.3s ease-in;
  }

  @keyframes fadeIn {
    from {
      opacity: 0;
    }

    to {
      opacity: 1;
    }
  }

  .tab-panel {
    opacity: 0;
    transition: opacity 0.3s ease-in-out;
  }

  .tab-panel.fade-in {
    opacity: 1;
  }
</style>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.tabLinks');
    const panels = document.querySelectorAll('.tab-panel');

    function switchTab(oldTab, newTab) {
      newTab.focus();
      newTab.classList.add('bg-yns_dark_blue', 'text-white');
      oldTab.classList.remove('bg-yns_dark_blue', 'text-white');
      oldTab.classList.add('text-gray-400');
    }

    tabs.forEach(tab => {
      tab.addEventListener('click', e => {
        e.preventDefault();
        const target = document.querySelector(`#${tab.dataset.tab}`);

        panels.forEach(panel => {
          panel.classList.add('hidden');
          panel.classList.remove('fade-in');
        });

        tabs.forEach(t => {
          t.classList.remove('bg-black/20', 'text-white');
          t.classList.add('text-gray-400');
        });

        tab.classList.remove('text-gray-400');
        tab.classList.add('bg-black/20', 'text-white');

        target.classList.remove('hidden');
        setTimeout(() => target.classList.add('fade-in'), 50);
      });
    });

    // Show first tab by default
    tabs[0].click();
  });

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
