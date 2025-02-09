<x-guest-layout>
  <div class="relative min-h-screen pt-36">
    <!-- Event Hero Section -->
    <div class="mx-auto max-w-screen-2xl px-4 sm:px-6 lg:px-8">
      <div class="relative mb-8 overflow-hidden rounded-2xl bg-yns_dark_blue shadow-2xl">
        <div class="absolute inset-0">
          <div class="absolute inset-0 bg-gradient-to-r from-black to-transparent"></div>
          @if ($event->poster_url)
            <img src="{{ asset($event->poster_url) }}" alt="{{ $event->event_name }}"
              class="h-full w-full object-cover opacity-20">
          @endif
        </div>

        <div class="relative z-10 grid gap-8 px-6 py-12 lg:grid-cols-2 lg:gap-12 lg:px-8">
          <!-- Event Details -->
          <div class="space-y-6">
            <div>
              <h1 class="font-heading text-4xl font-bold text-white md:text-5xl lg:text-6xl">{{ $event->event_name }}
              </h1>
              <div class="mt-4 flex items-center gap-4 text-gray-300">
                <div class="flex items-center">
                  <span class="fas fa-calendar mr-2"></span>
                  <span>{{ $event->event_date->format('jS F Y') }}</span>
                </div>
                <div class="flex items-center">
                  <span class="fas fa-clock mr-2"></span>
                  <span>{{ $eventStartTime }}@if ($eventEndTime)
                      - {{ $eventEndTime }}
                    @endif
                  </span>
                </div>
              </div>
            </div>

            @if (!$isPastEvent)
              <div class="flex flex-wrap gap-4">
                @if ($event->ticket_url)
                  <a href="{{ $event->ticket_url }}" target="_blank"
                    class="group inline-flex items-center gap-2 rounded-lg bg-yns_yellow px-6 py-3 font-medium text-black transition-all hover:bg-yellow-400">
                    <span class="fas fa-ticket-alt"></span>
                    Get Tickets
                    <span class="text-sm">({{ formatCurrency($event->on_the_door_ticket_price) }} on the door)</span>
                  </a>
                @endif
                <button id="addToCalendarButton"
                  class="inline-flex items-center gap-2 rounded-lg border border-gray-600 bg-black/30 px-6 py-3 font-medium text-white backdrop-blur-sm transition-all hover:bg-black/50">
                  <span class="fas fa-calendar-plus"></span>
                  Add to Calendar
                </button>
              </div>
            @endif
          </div>

          <!-- Event Poster -->
          @if ($event->poster_url)
            <div class="relative hidden lg:block">
              <img src="{{ asset($event->poster_url) }}" alt="{{ $event->event_name }}"
                class="rounded-lg shadow-2xl transition-transform hover:scale-105" onclick="openModal()"
                style="cursor: pointer;">
            </div>
          @endif
        </div>
      </div>

      <!-- Event Content -->
      <div class="grid gap-8 lg:grid-cols-3">
        <!-- Line Up Section -->
        <div class="lg:col-span-2">
          <div class="rounded-xl border border-gray-800 bg-yns_dark_blue/50 p-6 backdrop-blur-sm">
            <h2 class="mb-6 font-heading text-2xl font-bold text-white">Line Up</h2>
            <div class="space-y-6">
              @if ($headliner)
                <div class="group relative rounded-lg bg-black/30 p-4 backdrop-blur-sm">
                  <div
                    class="absolute -inset-1 rounded-lg bg-gradient-to-r from-yns_yellow to-yns_dark_orange opacity-20 blur">
                  </div>
                  <div class="relative">
                    <span class="text-xs font-semibold uppercase tracking-wide text-gray-400">Headliner</span>
                    <h3 class="mt-1 text-xl font-bold text-white">
                      <a href="{{ route('singleService', ['serviceType' => 'Artist', 'name' => $headliner->name]) }}"
                        class="transition duration-150 ease-in-out hover:text-yns_yellow"
                        target="_blank">{{ $headliner->name }}</a>
                    </h3>
                  </div>
                </div>
              @endif

              @if ($mainSupport)
                <div class="rounded-lg bg-black/20 p-4 backdrop-blur-sm">
                  <span class="text-xs font-semibold uppercase tracking-wide text-gray-400">Main Support</span>
                  <h3 class="mt-1 text-xl font-bold text-white">
                    <a href="{{ route('singleService', ['serviceType' => 'Artist', 'name' => $mainSupport->name]) }}"
                      class="transition duration-150 ease-in-out hover:text-yns_yellow"
                      target="_blank">{{ $mainSupport->name }}</a>
                  </h3>
                </div>
              @endif

              @if (count($otherBands) > 0)
                <div class="rounded-lg bg-black/20 p-4 backdrop-blur-sm">
                  <span class="text-xs font-semibold uppercase tracking-wide text-gray-400">Supporting Acts</span>
                  <div class="mt-2 flex flex-wrap gap-2">
                    @foreach ($otherBands as $band)
                      <a href="{{ route('singleService', ['serviceType' => 'Artist', 'name' => $band->name]) }}"
                        class="rounded-full bg-black/30 px-3 py-1 text-sm text-white transition-all duration-200 hover:bg-yns_yellow hover:text-black"
                        target="_blank">
                        {{ $band->name }}
                      </a>
                    @endforeach
                  </div>
                </div>
              @endif

              @if ($opener)
                <div class="rounded-lg bg-black/20 p-4 backdrop-blur-sm">
                  <span class="text-xs font-semibold uppercase tracking-wide text-gray-400">Opening Act</span>
                  <h3 class="mt-1 text-xl font-bold text-white">
                    <a href="{{ route('singleService', ['serviceType' => 'Artist', 'name' => $opener->name]) }}"
                      class="transition duration-150 ease-in-out hover:text-yns_yellow"
                      target="_blank">{{ $opener->name }}</a>
                  </h3>
                </div>
              @endif
            </div>
          </div>
        </div>

        <!-- Venue & Details Section -->
        <div class="space-y-8">
          <div class="rounded-xl border border-gray-800 bg-yns_dark_blue/50 p-6 backdrop-blur-sm">
            <h2 class="mb-6 font-heading text-2xl font-bold text-white">Venue</h2>
            @forelse($event->venues as $venue)
              <div class="space-y-4">
                <h3 class="text-lg font-medium text-white"><a href="{{ route('venue', ['slug' => $venue->name]) }}"
                    class="transition duration-150 ease-in-out hover:text-yns_yellow">{{ $venue->name }}</a></h3>
                <div class="flex items-start gap-3 text-gray-300">
                  <span class="fas fa-map-marker-alt mt-1"></span>
                  <div>
                    <p>{{ $venue->location }}</p>
                    <p class="mt-2">
                      <a href="https://maps.google.com/?q={{ urlencode($venue->location) }}" target="_blank"
                        class="inline-flex items-center gap-2 text-sm text-yns_yellow hover:underline">
                        <span class="fas fa-directions"></span>
                        Get Directions
                      </a>
                    </p>
                  </div>
                </div>
              </div>
            @empty
              <p class="text-gray-400">Venue to be announced</p>
            @endforelse
          </div>

          @if ($event->event_description)
            <div class="rounded-xl border border-gray-800 bg-yns_dark_blue/50 p-6 backdrop-blur-sm">
              <h2 class="mb-6 font-heading text-2xl font-bold text-white">About</h2>
              <p class="text-gray-300">{{ $event->event_description }}</p>
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>

  <!-- Modal for poster -->
  <div id="posterModal" class="fixed inset-0 z-50 hidden bg-black/90 backdrop-blur-sm transition-opacity duration-300">
    <div class="flex min-h-screen items-center justify-center p-4">
      <div class="relative max-h-[90vh] max-w-4xl">
        <button onclick="closeModal()"
          class="absolute -right-4 -top-4 rounded-full bg-black p-2 text-white hover:bg-gray-900">
          <span class="fas fa-times"></span>
        </button>
        <img src="{{ asset($event->poster_url) }}" alt="{{ $event->event_name }}"
          class="h-auto max-h-[85vh] w-auto rounded-lg">
      </div>
    </div>
  </div>

  <script>
    function openModal() {
      const modal = document.getElementById('posterModal');
      modal.classList.remove('hidden');
      document.body.style.overflow = 'hidden';
    }

    function closeModal() {
      const modal = document.getElementById('posterModal');
      modal.classList.add('hidden');
      document.body.style.overflow = 'auto';
    }
  </script>
</x-guest-layout>
