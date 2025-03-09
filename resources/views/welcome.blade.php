<x-guest-layout>
  <div class="fixed inset-0 z-0">
    <div class="absolute inset-0 bg-gradient-to-b from-black/30 via-black/30 to-black/30"></div>
  </div>

  <!-- Scrollable Content -->
  <div class="relative z-10">
    <!-- Hero/Search Section -->
    <section
      class="flex min-h-screen items-center justify-center px-4 pb-12 pt-32 sm:px-6 sm:pt-32 md:pt-40 lg:px-8 lg:pt-20">
      <div class="w-full max-w-4xl text-center">
        <h1 class="font-heading text-4xl font-bold text-white sm:text-5xl md:text-6xl lg:text-7xl">
          Your Next Show
          <span class="mt-2 block text-2xl font-normal text-yns_yellow md:text-3xl">
            Connecting Bands with Venues
            <span class="block text-lg text-gray-300 md:text-xl">and so much more...</span>
          </span>
        </h1>

        <!-- Search Form -->
        <div class="mt-8 sm:mt-12">
          <form action="{{ route('venues.filterByCoordinates') }}" method="GET" class="mx-auto max-w-3xl transform">
            @csrf
            <div class="group relative">
              <input type="search" id="address-input" name="search_query"
                class="map-input w-full rounded-full border-2 border-white/20 bg-black/30 py-4 pl-6 pr-16 text-lg text-white backdrop-blur-sm transition-all placeholder:text-gray-400 focus:border-yns_yellow focus:bg-black/50 focus:outline-none focus:ring-2 focus:ring-yns_yellow md:py-6 md:text-xl"
                placeholder="Enter a location to find venues...">
              <button type="submit" id="search-button"
                class="absolute right-2 top-1/2 -translate-y-1/2 rounded-full bg-yns_yellow p-3 text-black transition-all hover:bg-yellow-400 md:p-4">
                <span class="fas fa-search text-lg md:text-xl"></span>
              </button>
            </div>
            <input type="hidden" id="address-latitude" name="latitude">
            <input type="hidden" id="address-longitude" name="longitude">
          </form>
        </div>

        <div class="mt-6">
          <span class="text-gray-400">or</span>
          <a href="{{ url('/venues') }}"
            class="ml-2 text-lg text-white underline decoration-yns_yellow underline-offset-4 transition-colors hover:text-yns_yellow">
            Browse all venues
          </a>
        </div>


        <!-- Features Grid -->
        <div class="mt-16 grid grid-cols-1 gap-8 px-4 md:grid-cols-2 lg:grid-cols-3">
          <div class="rounded-xl bg-black/30 p-6 backdrop-blur-sm">
            <span class="fas fa-map-marker-alt mb-4 text-3xl text-yns_yellow"></span>
            <h3 class="mb-2 font-heading text-xl font-bold text-white">Find Local Venues</h3>
            <p class="text-gray-300">Discover perfect venues in your area for your next performance</p>
          </div>
          <div class="rounded-xl bg-black/30 p-6 backdrop-blur-sm">
            <span class="fas fa-users mb-4 text-3xl text-yns_yellow"></span>
            <h3 class="mb-2 font-heading text-xl font-bold text-white">Connect with Promoters</h3>
            <p class="text-gray-300">Link up with local promoters who share your musical interests</p>
          </div>
          <div class="rounded-xl bg-black/30 p-6 backdrop-blur-sm md:col-span-2 lg:col-span-1">
            <span class="fas fa-calendar-alt mb-4 text-3xl text-yns_yellow"></span>
            <h3 class="mb-2 font-heading text-xl font-bold text-white">Manage Your Shows</h3>
            <p class="text-gray-300">Keep track of your gigs with our custom dashboard</p>
          </div>
        </div>
      </div>
    </section>

    <!-- About Section -->
    <section class="flex min-h-screen items-center justify-center bg-black/30 px-4 backdrop-blur-sm sm:px-6 lg:px-8">
      <div class="w-full max-w-7xl">
        <div class="relative grid lg:grid-cols-2 lg:gap-12">
          <!-- Text Content -->
          <div class="py-12">
            <h2 class="font-heading text-3xl font-bold text-white md:text-4xl">
              About <span class="text-yns_yellow">Your Next Show</span>
            </h2>
            <div class="mt-6 space-y-6 text-lg text-gray-300">
              <p>
                We're revolutionizing how bands and venues connect in the UK music scene. Our platform makes it
                simple to discover, contact, and book venues that match your band's style and requirements.
              </p>
              <p>
                We believe knowledge shouldn't come with a price tag. That's why we've taken the decision to remain
                completely ad-free
                and will never offer paid membership options. By setting this example, we ensure everyone has equal
                access to opportunities
                in the music community.
              </p>
              <p>
                Whether you're a metal band looking for your next gig spot or a venue owner seeking to fill your
                calendar with amazing talent, Your Next Show brings the UK music community together on an even playing
                field.
              </p>
              <div class="mt-8 flex flex-wrap gap-4">
                <div class="flex items-center gap-2">
                  <span class="fas fa-check text-yns_yellow"></span>
                  <span class="text-white">Verified Venues</span>
                </div>
                <div class="flex items-center gap-2">
                  <span class="fas fa-check text-yns_yellow"></span>
                  <span class="text-white">Direct Communication</span>
                </div>
                <div class="flex items-center gap-2">
                  <span class="fas fa-check text-yns_yellow"></span>
                  <span class="text-white">Community Driven</span>
                </div>
              </div>
              <div class="mt-8">
                <a href="{{ route('register') }}"
                  class="inline-flex items-center gap-2 rounded-lg bg-yns_yellow px-6 py-3 font-medium text-black transition-all hover:bg-yellow-400">
                  Join Our Community
                  <span class="fas fa-arrow-right"></span>
                </a>
              </div>
            </div>
          </div>

          <!-- Stats/Features -->
          <div class="relative mb-10 flex items-center lg:mb-0">
            <div class="grid gap-8 sm:grid-cols-2">
              <div class="rounded-xl border border-white/10 bg-black/30 p-6 backdrop-blur-sm">
                <span class="text-3xl font-bold text-yns_yellow">{{ $venues }}</span>
                <p class="mt-2 text-sm text-gray-400">Venues</p>
              </div>
              <div class="rounded-xl border border-white/10 bg-black/30 p-6 backdrop-blur-sm">
                <span class="text-3xl font-bold text-yns_yellow">{{ $artists }}</span>
                <p class="mt-2 text-sm text-gray-400">Registered Artists</p>
              </div>
              <div class="rounded-xl border border-white/10 bg-black/30 p-6 backdrop-blur-sm">
                <span class="text-3xl font-bold text-yns_yellow">50+</span>
                <p class="mt-2 text-sm text-gray-400">Cities Covered</p>
              </div>
              <div class="rounded-xl border border-white/10 bg-black/30 p-6 backdrop-blur-sm">
                <span class="text-3xl font-bold text-yns_yellow">24/7</span>
                <p class="mt-2 text-sm text-gray-400">Support Available</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="flex h-screen items-center justify-center px-4 sm:px-6 lg:px-8">
      <div class="w-full max-w-4xl text-center">
        <h2 class="font-heading text-3xl font-bold text-white md:text-4xl">
          Support <span class="text-yns_yellow">The Project</span>
        </h2>
        <div class="mx-auto mt-4 max-w-2xl space-y-4 text-lg text-gray-300">
          <p>
            Your Next Show will always be free to use and ad-free. That's our promise to the music community.
          </p>
          <p>
            While donations are never required, they help support ongoing development and keep the platform running
            smoothly.
          </p>
        </div>
        <div class="mt-8">
          <a href="https://buymeacoffee.com/yournextshow" target="_blank" rel="noopener noreferrer"
            class="inline-flex items-center gap-2 rounded-lg bg-[#FFDD00] px-6 py-3 font-medium text-black transition-all hover:bg-[#FFDD00]/90">
            <span class="fas fa-coffee mr-2 text-2xl"></span>
            Buy me a coffee
          </a>
        </div>
      </div>
    </section>
  </div>
</x-guest-layout>

<script>
  function initialize() {
    const input = document.getElementById('address-input');
    const form = input.closest('form');
    const autocomplete = new google.maps.places.Autocomplete(input, {
      componentRestrictions: {
        country: 'gb'
      },
      fields: ['geometry', 'name', 'formatted_address']
    });

    autocomplete.addListener('place_changed', function() {
      const place = autocomplete.getPlace();

      if (!place.geometry) {
        window.alert("No details available for input: '" + place.name + "'");
        input.value = "";
        return;
      }

      // Update hidden fields with coordinates
      document.getElementById('address-latitude').value = place.geometry.location.lat();
      document.getElementById('address-longitude').value = place.geometry.location.lng();

      // Submit the form automatically
      form.submit();
    });
  }
</script>

<script
  src="https://maps.googleapis.com/maps/api/js?key={{ config('google.maps_api_key') }}&libraries=places&callback=initialize"
  async defer></script>
