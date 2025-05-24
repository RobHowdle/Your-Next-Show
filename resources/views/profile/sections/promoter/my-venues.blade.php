<header class="mb-6 border-b border-gray-700 pb-4">
  <h2 class="font-heading text-lg font-medium text-white">
    {{ __('Your Venues') }}
  </h2>
  <p class="mt-1 text-sm text-gray-400">
    {{ __('Venues you have worked with in the past') }}
  </p>
</header>

<div id="venues-modal" style="display:none;"
  class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-70">
  <div class="relative w-full max-w-lg rounded-lg bg-gray-900 p-8 shadow-lg">
    <button class="absolute right-2 top-2 text-gray-400 hover:text-white" id="close-venues-modal">&times;</button>
    <h2 class="mb-4 text-xl font-bold text-white" id="venues-modal-title"></h2>
    <ul class="space-y-2" id="venues-modal-list"></ul>
    <p class="text-gray-400" id="venues-modal-empty" style="display:none;">No events found for this venue.</p>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.show-events-btn').forEach(function(btn) {
      btn.addEventListener('click', function(e) {
        e.preventDefault();
        const events = JSON.parse(this.getAttribute('data-events'));
        const venueName = this.getAttribute('data-venuename');
        const modal = document.getElementById('venues-modal');
        const title = document.getElementById('venues-modal-title');
        const list = document.getElementById('venues-modal-list');
        const empty = document.getElementById('venues-modal-empty');
        title.textContent = venueName;
        list.innerHTML = '';
        if (events.length) {
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
    document.getElementById('close-venues-modal').addEventListener('click', function() {
      document.getElementById('venues-modal').style.display = 'none';
    });
    document.getElementById('venues-modal').addEventListener('click', function(e) {
      if (e.target === this) this.style.display = 'none';
    });
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') document.getElementById('venues-modal').style.display = 'none';
    });
  });
</script>

<div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
  @forelse ($profileData['groupedVenues'] as $data)
    <div class="flex flex-col rounded-lg bg-gray-800/70 p-6 shadow-lg">
      <div class="mb-4 flex items-center justify-between">
        <div>
          <h3 class="text-lg font-bold text-white">
            {{ optional(optional($data['events']->first())->venues->first())->name ?? 'Unknown Venue' }}
          </h3>
          <p class="text-sm text-gray-400">
            {{ $data['event_count'] }} {{ Str::plural('event', $data['event_count']) }} hosted here
          </p>
        </div>
        <button type="button" class="show-events-btn font-semibold text-yns_yellow hover:underline"
          data-events='@json($data['eventsForJs'] ?? [])' data-venuename="{{ $data['venue']?->name ?? 'Unknown Venue' }}">
          {{ __('Show Events') }}
        </button>
      </div>
      <div class="mt-2 space-y-2">
        @foreach ($data['events'] as $event)
          <a target="_blank"
            href="{{ route('admin.dashboard.show-event', ['dashboardType' => $dashboardType, 'id' => $event->id]) }}"
            class="block rounded bg-gray-900/80 px-4 py-2 text-white transition hover:bg-yns_yellow hover:text-black">
            <span class="font-medium">{{ $event->event_name }}</span>
            <span class="ml-2 text-xs text-gray-400">({{ $event->event_date }})</span>
          </a>
        @endforeach
      </div>
    </div>
  @empty
    <div class="col-span-full">
      <p class="py-8 text-center text-gray-400">No venues found.</p>
    </div>
  @endforelse
</div>
