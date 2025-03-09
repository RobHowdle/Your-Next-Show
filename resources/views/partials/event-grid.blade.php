@forelse($events as $event)
  <div class="group relative overflow-hidden rounded-lg border border-gray-800 bg-gray-900/50 backdrop-blur-sm">
    {{-- Event Content --}}
    <div class="p-4">
      <div class="flex items-center justify-between">
        <span class="rounded bg-yns_yellow/20 px-2.5 py-0.5 text-sm font-medium text-yns_yellow">
          @php
            $genres = json_decode($event->genre);
            echo is_array($genres) ? implode(', ', $genres) : $genres;
          @endphp
        </span>
        <span class="text-sm text-gray-400">{{ \Carbon\Carbon::parse($event->event_date)->format('l jS F Y') }}</span>
      </div>

      <h3 class="mt-2 font-heading text-xl font-bold text-white">{{ $event->event_name }}</h3>
      <p class="mt-1 text-sm text-gray-300">{{ $event->venues->first()->name }}</p>
      <p class="mt-1 text-sm text-gray-400">{{ $event->venues->first()->location }}</p>

      <div class="mt-4 flex items-center justify-between">
        <span class="text-sm text-gray-300">{{ \Carbon\Carbon::parse($event->event_start_time)->format('H:i') }}</span>
        <a href="{{ route('public-event', ['eventId' => $event->id]) }}"
          class="rounded-md bg-yns_yellow px-3 py-1.5 text-sm font-medium text-black hover:bg-yns_yellow/90">
          View Details
        </a>
      </div>
    </div>
  </div>
@empty
  <div class="col-span-full">
    <div class="rounded-lg border border-gray-800 bg-black/50 p-8 text-center backdrop-blur-sm">
      <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
          d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      <h3 class="mt-4 text-lg font-medium text-white">No Events Found</h3>
      <p class="mt-2 text-gray-400">Try adjusting your search filters to find more events.</p>
    </div>
  </div>
@endforelse
