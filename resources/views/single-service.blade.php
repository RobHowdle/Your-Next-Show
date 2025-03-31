<x-guest-layout>
  <div class="relative min-h-screen pb-6 pt-28 md:pb-20 md:pt-36">
    <div class="mx-auto max-w-screen-2xl px-2 md:px-4 lg:px-8">
      <!-- Hero Section -->
      <div class="relative mb-4 overflow-hidden rounded-2xl bg-yns_dark_blue shadow-2xl md:mb-8">
        <div class="absolute inset-0">
          <div class="absolute inset-0 bg-gradient-to-r from-black to-transparent"></div>
          @if ($singleService->logo_url && file_exists(public_path($singleService->logo_url)))
            <img src="{{ asset($singleService->logo_url) }}" alt="{{ $singleService->name }}"
              class="h-full w-full object-cover opacity-20">
          @endif
        </div>

        <div class="relative z-10 grid gap-8 px-6 py-6 md:py-12 lg:grid-cols-2 lg:gap-12 lg:px-8">
          <!-- Left Column: singleService Info -->
          <div class="space-y-4">
            <h1 class="font-heading text-4xl font-bold text-white md:text-5xl lg:text-6xl">
              {{ $singleService->name }}
              @if ($singleService->is_verified)
                <span
                  class="ml-2 inline-flex items-center rounded-full bg-yns_yellow/10 px-2 py-1 text-sm text-yns_yellow">
                  <svg class="mr-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                    <path
                      d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" />
                  </svg>
                  Verified {{ $singleService->services }} Service
                </span>
              @endif
            </h1>

            @if ($singleService->services === 'Artist' && $hasMinors)
              <div class="rounded-xl border border-amber-500/20 bg-amber-500/10 p-4 backdrop-blur-sm">
                <div class="flex">
                  <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-amber-500" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd"
                        d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z"
                        clip-rule="evenodd" />
                    </svg>
                  </div>
                  <div class="ml-3">
                    <h3 class="text-sm font-medium text-amber-500">Minor Alert</h3>
                    <div class="mt-2 text-sm text-amber-400/90">
                      <p>This artist has one or more members under the age of 18.</p>
                    </div>
                  </div>
                </div>
              </div>
            @endif

            <div class="space-y-3">
              @if ($singleService->location)
                <a href="javascript:void(0)" id="open-map-link"
                  class="group flex items-center text-gray-300 transition-colors hover:text-yns_yellow">
                  <span class="fas fa-location-dot mr-2"></span>
                  <span>{{ $singleService->location }}</span>
                </a>
              @endif

              <div class="rating-wrapper flex h-full items-center justify-start gap-2">
                <span class="flex h-full items-center text-gray-300">Overall Rating ({{ $reviewCount }}):</span>
                <div class="flex h-full items-center">
                  {!! $overallReviews[$singleService->id] !!}
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
                <x-contact-and-social-links :item="$singleService" class="flex flex-row flex-wrap gap-4" />
              </div>
            </div>
          </div>

          <!-- Right Column: singleService Logo -->
          <div class="relative hidden lg:block">
            @if ($singleService->logo_url && file_exists(public_path($singleService->logo_url)))
              <img src="{{ asset($singleService->logo_url) }}" alt="{{ $singleService->name }}" class="_250img">
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
            @if ($singleService->contact_name)
              <div class="flex items-center gap-3 text-gray-300">
                <span class="fas fa-user text-yns_yellow"></span>
                <span>Contact: {{ $singleService->contact_name }}</span>
              </div>
            @endif
          </div>
        </div>
      </div>

      <!-- Tabs Navigation -->
      @include('components.' . Str::lower($singleService->services) . '-service-tabs')

      <!-- Tab Contents -->
      @include('components.' . Str::lower($singleService->services) . '-service-tab-content', [
          'serviceData' => $serviceData,
      ])

    </div>
  </div>

  <!-- Add necessary modals -->
  <x-review-modal title="{{ $singleService->name }}" serviceType="singleService" profileId="{{ $singleService->id }}"
    service="{{ $singleService->name }}" />
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
    const singleServiceLatitude = "{{ $singleService->latitude }}";
    const singleServiceLongitude = "{{ $singleService->longitude }}";
    const w3wAddress = "{{ $singleService->w3w }}";

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
        const geoURI = `geo:${singleServiceLatitude},${singleServiceLongitude}`;
        window.location.href = geoURI;
      } else {
        // If not mobile, fall back to Google Maps
        window.open(`https://www.google.com/maps?q=${singleServiceLatitude},${singleServiceLongitude}`, '_blank');
      }
    }

    // Attach click event listeners
    openMapLink.addEventListener("click", openMap);
    openW3WLink && openW3WLink.addEventListener("click",
      openW3W); // Conditional check in case the element doesn't exist
  });
</script>
