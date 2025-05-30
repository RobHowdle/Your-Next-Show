<header class="mb-6 border-b border-gray-700 pb-4">
  <h2 class="font-heading text-lg font-medium text-white">
    {{ __('Your Artists') }}
  </h2>
  <p class="mt-1 text-sm text-gray-400">
    {{ __('Artists you have worked with in the past') }}
  </p>
</header>

@php
  $uniqueArtists = $profileData['uniqueArtists'] ?? collect();
@endphp

<div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
  @forelse ($uniqueArtists as $artist)
    <div class="flex flex-col rounded-lg bg-gray-800/70 p-6 shadow-lg">
      <div class="mb-2 flex items-center justify-between">
        <h3 class="text-lg font-bold text-white">{{ $artist->name }}</h3>
        <button type="button"
          class="show-artist-events-btn ml-2 rounded bg-yns_yellow px-2 py-1 text-xs font-semibold text-black hover:bg-yellow-400"
          data-artist-events-id="artist-events-{{ $artist->id }}" data-artistname="{{ $artist->name }}">
          Show Events ({{ $artist->events?->count() ?? 0 }})
        </button>
      </div>
      <div class="mb-1">
        <span class="text-sm text-gray-300">
          <strong>Location:</strong>
          {{ $artist->location ?? 'No Location' }}
        </span>
      </div>
      <div class="mb-1">
        <span class="text-sm text-gray-300">
          <strong>Venues:</strong>
          @if (isset($artist->venues) && $artist->venues->count())
            <ul class="ml-4 list-disc">
              @foreach ($artist->venues as $venue)
                <li>{{ $venue?->name ?? 'Unknown Venue' }}</li>
              @endforeach
            </ul>
          @else
            No Venues
          @endif
        </span>
      </div>
      <div class="mb-4">
        <span class="text-sm text-gray-300">
          <strong>Genre:</strong>
          @php
            $genreData = $artist->genre;
            if (is_string($genreData)) {
                $decoded = json_decode($genreData, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $genreData = $decoded;
                }
            }
          @endphp
          @if (is_array($genreData))
            @php
              $genreNames = collect($genreData)
                  ->map(function ($genreData, $genreName) {
                      $main = ucwords(str_replace('_', ' ', $genreName));
                      $subs = '';
                      if (
                          isset($genreData['subgenres']) &&
                          is_array($genreData['subgenres']) &&
                          count($genreData['subgenres'])
                      ) {
                          $subs =
                              ' (' .
                              collect($genreData['subgenres'])
                                  ->map(function ($sub) {
                                      return ucwords(str_replace('_', ' ', $sub));
                                  })
                                  ->implode(', ') .
                              ')';
                      }
                      return $main . $subs;
                  })
                  ->implode(', ');
            @endphp
            {{ $genreNames ?: 'No Genre Available' }}
          @else
            {{ $genreData ?: 'No Genre Available' }}
          @endif
        </span>
      </div>
      <div class="mt-auto pt-4">
        <a target="_blank" href="{{ route('singleService', ['serviceType' => 'artist', 'name' => $artist->name]) }}"
          class="inline-block rounded bg-yns_yellow px-4 py-2 font-semibold text-black transition hover:bg-yellow-400">
          View Artist
        </a>
      </div>
    </div>
  @empty
    <div class="col-span-full">
      <p class="py-8 text-center text-gray-400">No artists found.</p>
    </div>
  @endforelse
</div>

@foreach ($uniqueArtists as $artist)
  <script type="application/json" id="artist-events-{{ $artist->id }}">
    {!! json_encode($artist->events ? $artist->events->map(function ($event) use ($dashboardType) {
      return [
        'id' => $event->id,
        'name' => $event->event_name,
        'date' => $event->event_date,
        'url' => route('admin.dashboard.show-event', ['dashboardType' => $dashboardType, 'id' => $event->id]),
      ];
    })->values() : []) !!}
  </script>
@endforeach

<!-- Artist Events Modal -->
<div id="artist-events-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-70">
  <div class="w-full max-w-lg rounded-lg bg-gray-900 p-6 shadow-lg">
    <div class="mb-4 flex items-center justify-between">
      <h3 id="artist-events-modal-title" class="text-lg font-bold text-white"></h3>
      <button id="close-artist-events-modal" class="text-gray-400 hover:text-white">&times;</button>
    </div>
    <ul id="artist-events-modal-list" class="mb-2 space-y-2"></ul>
    <p class="text-gray-400" id="artist-events-modal-empty" style="display:none;">No events found for this artist.</p>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.show-artist-events-btn').forEach(function(btn) {
      btn.addEventListener('click', function(e) {
        e.preventDefault();
        const eventsId = this.getAttribute('data-artist-events-id');
        let events = [];
        if (eventsId) {
          const scriptTag = document.getElementById(eventsId);
          if (scriptTag) {
            try {
              events = JSON.parse(scriptTag.textContent);
            } catch (err) {
              events = [];
            }
          }
        }
        const artistName = this.getAttribute('data-artistname');
        const modal = document.getElementById('artist-events-modal');
        const title = document.getElementById('artist-events-modal-title');
        const list = document.getElementById('artist-events-modal-list');
        const empty = document.getElementById('artist-events-modal-empty');
        title.textContent = artistName;
        list.innerHTML = '';
        if (Array.isArray(events) && events.length) {
          events.forEach(function(event) {
            const li = document.createElement('li');
            const a = document.createElement('a');
            a.href = event.url;
            a.className =
              'block rounded bg-gray-800 px-4 py-2 text-white transition hover:bg-yns_yellow hover:text-black';
            a.innerHTML =
              `<span class='font-medium'>${event.name}</span> <span class='ml-2 text-xs text-gray-400'>(${event.date})</span>`;
            li.appendChild(a);
            list.appendChild(li);
          });
          empty.style.display = 'none';
        } else {
          empty.style.display = '';
        }
        modal.style.display = 'flex';
      });
    });
    document.getElementById('close-artist-events-modal').addEventListener('click', function() {
      document.getElementById('artist-events-modal').style.display = 'none';
    });
    document.getElementById('artist-events-modal').addEventListener('click', function(e) {
      if (e.target === this) this.style.display = 'none';
    });
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') document.getElementById('artist-events-modal').style.display = 'none';
    });
  });
</script>
