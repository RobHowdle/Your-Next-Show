<x-guest-layout>
  <div class="relative min-h-screen pb-6 pt-28 md:pb-20 md:pt-36">
    <div class="mx-auto max-w-screen-2xl px-2 md:px-4 lg:px-8">
      <!-- Hero Section -->
      <div class="relative mb-4 overflow-hidden rounded-2xl bg-yns_dark_blue shadow-2xl md:mb-8">
        <div class="absolute inset-0">
          <div class="absolute inset-0 bg-gradient-to-r from-black to-transparent"></div>
          @if ($promoter->logo_url && file_exists(public_path($promoter->logo_url)))
            <img src="{{ asset($promoter->logo_url) }}" alt="{{ $promoter->name }}"
              class="h-full w-full object-cover opacity-20">
          @endif
        </div>

        <div class="relative z-10 grid gap-8 px-6 py-6 md:py-12 lg:grid-cols-2 lg:gap-12 lg:px-8">
          <!-- Left Column: promoter Info -->
          <div class="space-y-6">
            <div class="space-y-4">
              <h1 class="font-heading text-4xl font-bold text-white md:text-5xl lg:text-6xl">
                {{ $promoter->name }}
                @if ($promoter->is_verified)
                  <span
                    class="ml-2 inline-flex items-center rounded-full bg-yns_yellow/10 px-2 py-1 text-sm text-yns_yellow">
                    <svg class="mr-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                      <path
                        d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" />
                    </svg>
                    Verified promoter
                  </span>
                @endif
              </h1>

              <div class="space-y-3">
                @if ($promoter->location)
                  <a href="javascript:void(0)" id="open-map-link"
                    class="group flex items-center text-gray-300 transition-colors hover:text-yns_yellow">
                    <span class="fas fa-location-dot mr-2"></span>
                    <span>{{ $promoter->location }}</span>
                  </a>
                @endif

                <div class="rating-wrapper flex h-full items-center justify-start gap-2">
                  <span class="flex h-full items-center text-gray-300">Overall Rating ({{ $reviewCount }}):</span>
                  <div class="flex h-full items-center">
                    {!! $overallReviews[$promoter->id] !!}
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
                  <x-contact-and-social-links :item="$promoter" class="flex flex-row flex-wrap gap-4" />
                </div>
              </div>
            </div>
          </div>

          <!-- Right Column: promoter Logo -->
          <div class="relative hidden lg:block">
            @if ($promoter->logo_url && file_exists(public_path($promoter->logo_url)))
              <img src="{{ asset($promoter->logo_url) }}" alt="{{ $promoter->name }}" class="_250img">
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
            @if ($promoter->contact_name)
              <div class="flex items-center gap-3 text-gray-300">
                <span class="fas fa-user text-yns_yellow"></span>
                <span>Contact: {{ $promoter->contact_name }}</span>
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
            <button data-tab="venues"
              class="tabLinks flex w-full items-center justify-center rounded-lg px-4 py-3 text-sm font-medium text-gray-400 transition-all hover:bg-black/20 hover:text-white md:w-auto md:justify-start">
              <span class="fas fa-cogs mr-2"></span>
              Venues
            </button>
            <button data-tab="genres"
              class="tabLinks flex w-full items-center justify-center rounded-lg px-4 py-3 text-sm font-medium text-gray-400 transition-all hover:bg-black/20 hover:text-white md:w-auto md:justify-start">
              <span class="fas fa-guitar mr-2"></span>
              Genre & Types
            </button>
            <button data-tab="events"
              class="tabLinks flex w-full items-center justify-center rounded-lg px-4 py-3 text-sm font-medium text-gray-400 transition-all hover:bg-black/20 hover:text-white md:w-auto md:justify-start">
              <span class="fas fa-calendar mr-2"></span>
              Upcoming Events
            </button>
            <button data-tab="reviews"
              class="tabLinks flex w-full items-center justify-center rounded-lg px-4 py-3 text-sm font-medium text-gray-400 transition-all hover:bg-black/20 hover:text-white md:w-auto md:justify-start">
              <span class="fas fa-star mr-2"></span>
              Reviews
            </button>
          </nav>
        </div>
      </div>

      <!-- Tab Contents -->
      <div class="grid gap-8 lg:grid-cols-3">
        <div class="lg:col-span-2">
          <div
            class="min-h-[400px] rounded-xl rounded-r-xl border border-gray-800 bg-yns_dark_blue/75 p-4 backdrop-blur-sm lg:rounded-b-xl lg:p-8">
            <!-- About Tab -->
            <div id="about" class="tab-panel hidden">
              <div class="prose-invert prose max-w-none">
                {!! $promoter->description !!}
              </div>
            </div>

            <!-- In House Gear Tab -->
            <div id="venues" class="tab-panel hidden">
              @if (!$promoter->my_venues)
                <p>We haven't gotten to this part yet! Check back soon...</p>
              @else
                {!! $promoter->my_venues !!}
              @endif
            </div>

            <!-- Genres Tab -->
            <div id="genres" class="tab-panel hidden">
              @php
                $bandTypes = json_decode($promoter->band_type ?? '[]');
              @endphp
              @if ($bandTypes == [])
                <p class="text-left">We don't have any specific band types listed, please <a
                    class="underline hover:text-yns_yellow" href="mailto:{{ $promoter->contact_email }}">contact us.</a>
                  if you would like to enquire about
                  booking
                  your band.</p>
              @else
                <p class="mb-2">The band types that we usually work with at <span
                    class="bold">{{ $promoter->name }}</span>
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
                    class="underline hover:text-yns_yellow" href="mailto:{{ $promoter->email }}">contact us.</a></p>
              @endif

              @php
                $genres = json_decode($promoter->genre ?? '{}');
              @endphp

              @if (empty((array) $genres))
                <p class="mt-4">We don't have a preference on genres of music at {{ $promoter->name }}. If you would
                  like to enquire about a show, please <a class="underline hover:text-yns_yellow"
                    href="mailto:{{ $promoter->contact_email }}">contact us.</a></p>
              @else
                <p class="mt-4">The genres that we usually work with at {{ $promoter->name }} are:</p>

                <ul class="genre-list columns-1 gap-2 md:columns-3 md:gap-4">
                  @foreach ($genreNames as $genre)
                    <li class="ml-6">{{ $genre }}</li>
                  @endforeach
                </ul>

                <p class="mt-4">If you would like to enquire about a show, please <a
                    class="underline hover:text-yns_yellow" href="mailto:{{ $promoter->contact_email }}">contact
                    us.</a>
                </p>
              @endif
            </div>

            <div id="events" class="tab-panel hidden">
              <div class="space-y-6">
                @if ($upcomingEvents && $upcomingEvents->count() > 0)
                  <p class="text-center text-lg text-gray-300">Upcoming events with {{ $promoter->name }} in the next
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
                    No upcoming events scheduled with {{ $promoter->name }} in the next month.
                  </p>
                @endif
              </div>
            </div>

            <!-- Reviews Tab -->
            <div id="reviews" class="tab-panel hidden">
              <div class="space-y-6">
                @if ($promoter->recentReviews && $promoter->recentReviews->count() > 0)
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
                    @foreach ($promoter->recentReviews as $review)
                      <div class="review text-center font-sans">
                        <p class="flex flex-col">
                          "{{ $review->review }}"
                          <span>- {{ $review->author }}</span>
                        </p>
                      </div>
                    @endforeach
                  </div>
                @else
                  <p class="mt-4 text-left">
                    No reviews available for {{ $promoter->name }} yet. Be the first to leave a review!
                  </p>
                @endif
              </div>
            </div>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="hidden space-y-8 lg:block">
          <div class="rounded-xl border border-gray-800 bg-yns_dark_blue/75 p-6 backdrop-blur-sm">
            <h2 class="mb-6 font-heading text-xl font-bold text-white">Quick Facts</h2>
            <div class="space-y-4">
              @if ($promoter->contact_name)
                <div class="flex items-center gap-3 text-gray-300">
                  <span class="fas fa-user text-yns_yellow"></span>
                  <span>Contact: {{ $promoter->contact_name }}</span>
                </div>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Add necessary modals -->
  <x-review-modal title="{{ $promoter->name }}" serviceType="promoter" profileId="{{ $promoter->id }}"
    service="{{ $promoter->name }}" />
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
    const promoterLatitude = "{{ $promoter->latitude }}";
    const promoterLongitude = "{{ $promoter->longitude }}";
    const w3wAddress = "{{ $promoter->w3w }}";

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
        const geoURI = `geo:${promoterLatitude},${promoterLongitude}`;
        window.location.href = geoURI;
      } else {
        // If not mobile, fall back to Google Maps
        window.open(`https://www.google.com/maps?q=${promoterLatitude},${promoterLongitude}`, '_blank');
      }
    }

    // Attach click event listeners
    openMapLink.addEventListener("click", openMap);
    openW3WLink && openW3WLink.addEventListener("click",
      openW3W); // Conditional check in case the element doesn't exist
  });
</script>
