<div class="event-card bg-yns_light_gray1 font-sans text-black shadow-md" data-id="{{ $event->id }}"
  data-dashboard-type="{{ $dashboardType }}">
  <img class="h-52 w-full object-cover"
    src="{{ asset($event->poster_url) ? asset($event->poster_url) : asset('images/system/yns_logo.png') }}"
    alt="{{ $event->event_name }}">
  <div class="body p-4">
    <p class="flex items-center text-sm">
      <span class="fas fa-calendar-alt mr-2"></span>Date: {{ $event->event_date->format('jS F Y') }}
    </p>
    @if ($event->promoter)
      <h2 class="mt-2 text-xl font-semibold uppercase">{{ $promoter->name }}</h2>
    @endif
    <h2 class="mb-2 text-xl font-semibold uppercase">{{ $event->event_name }}</h2>
    @if ($event->venues->count() > 0)
      <ul>
        @foreach ($event->venues as $venue)
          <li>
            <p class="h-10 text-sm text-gray-600">
              <span class="fas fa-map-marker-alt mr-2"></span>{{ $venue->location }}
            </p>
          </li>
        @endforeach
      </ul>
    @else
      <p class="h-10 text-sm text-gray-600">No venues assigned</p>
    @endif

    <div class="mt-2">
      <div class="relative">
        <div class="flex flex-row items-center justify-between">
          <p class="font-sans text-sm">Attendance</p>
          <span class="inline-block text-xs font-semibold uppercase text-yns_dark_gray">{{ $event->attendance }}%</span>
        </div>
        <div class="flex">
          <div class="h-2 w-full rounded-full bg-yns_med_gray">
            <div class="h-2 rounded-full bg-yns_yellow" style="width: {{ $event->attendance }}%;"></div>
          </div>
        </div>
      </div>

      <div class="mt-2">
        <div class="relative">
          <div class="flex flex-row items-center justify-between">
            <p class="font-sans text-sm">Ticket Sales</p>
            <span
              class="inline-block text-xs font-semibold uppercase text-yns_dark_gray">{{ $event->ticket_sales }}%</span>
          </div>
          <div class="flex">
            <div class="h-2 w-full rounded-full bg-yns_med_gray">
              <div class="h-2 rounded-full bg-yns_yellow" style="width: {{ $event->ticket_sales }}%;"></div>
            </div>
          </div>
        </div>
      </div>

      <div class="mt-2">
        <div class="relative">
          <div class="flex flex-row items-center justify-between">
            <p class="font-sans text-sm">Rating</p>
            <span class="inline-block text-xs font-semibold uppercase text-yns_dark_gray">{{ $event->rating }}%</span>
          </div>
          <div class="flex">
            <div class="h-2 w-full rounded-full bg-yns_med_gray">
              <div class="h-2 rounded-full bg-yns_yellow" style="width: {{ $event->rating }}%;"></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="mt-4 flex justify-between">
      <a href="{{ route('admin.dashboard.show-event', ['id' => $event->id, 'dashboardType' => $dashboardType]) }}"
        class="mt-7 rounded-lg border border-white bg-white px-4 py-2 font-heading text-black transition duration-150 ease-in-out hover:border-yns_yellow hover:text-yns_yellow">View
        Details</a>
      <button data-id="{{ $event->id }}"
        class="delete-event mt-7 rounded-lg border border-white bg-white px-4 py-2 font-heading text-black transition duration-150 ease-in-out hover:border-yns_red hover:text-yns_red">Remove</button>
    </div>
  </div>
</div>
