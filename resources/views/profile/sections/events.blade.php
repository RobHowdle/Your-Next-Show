<header class="mb-6 border-b border-gray-700 pb-4">
  <h2 class="font-heading text-lg font-medium text-white">
    {{ __('Your Events') }}
  </h2>
  <p class="mt-1 text-sm text-gray-400">
    {{ __('A list of all events you have promoted, with quick access to details.') }}
  </p>
</header>

<div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
  @forelse ($profileData['myEvents'] as $event)
    <div class="flex flex-col rounded-lg bg-gray-800/70 p-6 shadow-lg">
      <div class="mb-2 flex items-center justify-between">
        <h3 class="text-lg font-bold text-white">{{ $event->event_name }}</h3>
        <span class="text-xs text-gray-400">{{ $event->event_date }}</span>
      </div>
      <div class="mb-2">
        <span class="text-sm text-gray-300">
          <strong>Venue:</strong>
          @if ($event->venues && $event->venues->count())
            {{ $event->venues->first()->name }}
          @else
            N/A
          @endif
        </span>
      </div>
      @if ($event->lineup ?? false)
        <div class="mb-2">
          <span class="text-sm text-gray-300">
            <strong>Lineup:</strong>
            {{ is_array($event->lineup) ? implode(', ', $event->lineup) : $event->lineup }}
          </span>
        </div>
      @endif
      <div class="mt-auto pt-4">
        <a target="_blank"
          href="{{ route('admin.dashboard.show-event', ['dashboardType' => $dashboardType, 'id' => $event->id]) }}"
          class="inline-block rounded bg-yns_yellow px-4 py-2 font-semibold text-black transition hover:bg-yellow-400">
          View Event
        </a>
      </div>
    </div>
  @empty
    <div class="col-span-full">
      <p class="py-8 text-center text-gray-400">No events found.</p>
    </div>
  @endforelse
</div>
