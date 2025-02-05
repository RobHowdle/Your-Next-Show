<x-app-layout :dashboardType="$dashboardType" :modules="$modules">
  <x-slot name="header">
    <x-sub-nav :userId="$userId" />
  </x-slot>

  <div class="mx-auto w-full max-w-screen-2xl py-16">
    <div class="relative mb-8 shadow-md sm:rounded-lg">
      <div class="min-w-screen-xl mx-auto max-w-screen-xl rounded-lg border border-white bg-yns_dark_gray text-white">
        <div class="header border-b border-b-white px-6 py-8">
          <div class="flex flex-row items-center justify-between gap-2">
            <div class="group">
              <h1 class="font-heading text-4xl font-bold">{{ $event->event_name }}</h1>
              <p class="text-xl">Date: {{ $event->event_date->format('jS F Y') }} @ <span
                  class="fas fa-clock fa-sm mr-2"></span>{{ $eventStartTime }} @if ($eventEndTime)
                  - {{ $eventEndTime }}
                @endif
              </p>
              <div class="socials"></div>
            </div>
            <div class="group flex gap-x-4">
              @if (!$isPastEvent)
                <a href="{{ $event->ticket_url ? $event->ticket_url : '#' }}" target="_blank"
                  class="mb-4 rounded-lg bg-white px-4 py-2 text-black transition duration-300 hover:bg-gradient-to-t hover:from-yns_dark_orange hover:to-yns_yellow">
                  Tickets <span class="fas fa-ticket-alt ml-1"></span>
                </a>
                <a href="#" id="addToCalendarButton"
                  class="mb-4 rounded-lg bg-white px-4 py-2 text-black transition duration-300 hover:bg-gradient-to-t hover:from-yns_dark_orange hover:to-yns_yellow">
                  Add To Calendar <span class="fas fa-calendar-alt ml-1"></span>
                </a>
              @endif
              <a href="{{ route('admin.dashboard.edit-event', ['id' => $event->id, 'dashboardType' => $dashboardType]) }}"
                class="mb-4 rounded-lg bg-white px-4 py-2 text-black transition duration-300 hover:bg-gradient-to-t hover:from-yns_dark_orange hover:to-yns_yellow">
                Edit <span class="fas fa-edit ml-1"></span>
              </a>
              <a href="#" data-id="{{ $event->id }}" id="delete-event-btn"
                class="mb-4 rounded-lg bg-white px-4 py-2 text-black transition duration-300 hover:bg-gradient-to-t hover:from-yns_dark_orange hover:to-yns_yellow">
                Delete <span class="fas fa-trash ml-1"></span>
              </a>
            </div>
          </div>
        </div>
        <div class="grid grid-cols-2 gap-x-4 font-heading text-lg">
          <div class="col px-8 pt-8">
            <div class="group border-b border-white">
              @if ($headliner)
                <div class="group mb-4 text-center">
                  <p class="flex flex-col text-2xl font-bold underline">Headliner</p>
                  <a href="{{ route('singleService', ['serviceType' => 'Artist', 'name' => '$headliner->name']) }}"
                    class="font-normal no-underline transition duration-150 ease-in-out hover:text-yns_yellow">{{ $headliner->name ?? 'No Headliner' }}</a>
                </div>
              @endif
              @if ($mainSupport)
                <div class="group mb-4 text-center">
                  <p class="flex flex-col text-xl font-bold underline">Main Support</p>
                  <a href="{{ route('singleService', ['serviceType' => 'Artist', 'name' => '$mainSupport->name']) }}"
                    class="font-normal no-underline transition duration-150 ease-in-out hover:text-yns_yellow">{{ $mainSupport->name ?? 'No Main Support' }}</a>
                </div>
              @endif
              @if ($otherBands)
                <div class="group mb-4 flex flex-col text-center">
                  @if (count($otherBands) > 0)
                    <p class="text-lg font-bold underline">Band</p>
                    <ul class="flex flex-row flex-wrap justify-center">
                      @foreach ($otherBands as $band)
                        <li>
                          <a href="{{ route('singleService', ['serviceType' => 'Artist', 'name' => '$band->name']) }}"
                            class="font-normal no-underline transition duration-150 ease-in-out hover:text-yns_yellow">{{ $band->name }}{{ !$loop->last ? ', ' : '' }}</a>
                        </li>
                        @if (!$loop->last)
                          <li>&nbsp;</li>
                        @endif
                      @endforeach
                  @endif
                  </ul>
                </div>
              @endif
              @if ($opener)
                <div class="group mb-4 text-center">
                  <p class="text-md flex flex-col font-bold underline">Opener</p>
                  <a href="{{ route('singleService', ['serviceType' => 'Artist', 'name' => '$opener->name']) }}"
                    class="font-normal no-underline transition duration-150 ease-in-out hover:text-yns_yellow">{{ $opener->name ?? 'No Opener' }}</a>
                </div>
              @endif
            </div>
            @if ($event->event_description)
              <div class="group my-2 flex flex-row items-center justify-center text-center">
                <span class="fas fa-tag mr-2"></span>{{ $event->event_description }}
              </div>
            @endif
            <div class="group my-2 flex flex-row items-center justify-center">

            </div>
            <div class="group mb-2 flex flex-row items-start justify-center text-center">
              <span class="fas fa-map-marker-alt mr-2"></span>
              @forelse($event->venues as $venue)
                <a class="transition duration-150 ease-in-out hover:text-yns_yellow"
                  href="{{ route('venues', $venue->id) }}">{{ $venue->location }}</a>
              @empty
                No Venue Assigned
              @endforelse

            </div>
            <div class="group mb-2 flex flex-row items-center justify-center text-center">
              <span class="fas fa-bullhorn mr-2"></span>
              @forelse($event->promoters as $promoter)
                <a class="transition duration-150 ease-in-out hover:text-yns_yellow"
                  href="{{ route('promoters', $promoter->id) }}">{{ $promoter->name }}</a>
              @empty
                No Promoter Assigned
              @endforelse
            </div>

            <div class="group mb-2 flex flex-row items-center justify-center text-center">
              <span class="fas fa-ticket-alt mr-2"></span>{{ formatCurrency($event->on_the_door_ticket_price) }} O.T.D
            </div>
          </div>
          <div class="col relative place-content-center">
            <div
              class="absolute right-2 top-12 flex h-12 w-12 place-items-center justify-center rounded-50 bg-opac_8_black p-2 transition duration-150 ease-in-out hover:bg-opac_5_black">
              <span class="fas fa-search-plus"></span>
            </div>

            <img src="{{ asset($event->poster_url) }}" alt="{{ $event->event_name }} Poster"
              class="cursor-pointer object-cover transition duration-150 ease-in-out hover:opacity-75" id="eventPoster"
              onclick="openModal()">
            <div id="modal" class="fixed inset-0 hidden scale-95 transform justify-center duration-300 ease-in-out">
              <div class="flex justify-center rounded-lg bg-opac_8_black p-4">
                <span
                  class="absolute right-2 top-2 cursor-pointer transition duration-150 ease-in-out hover:text-yns_yellow"
                  onclick="closeModal()"><span class="fas fa-times"></span></span>
                <img src="{{ asset($event->poster_url) }}" alt="Enlarged Event Poster" class="max-h-80 max-w-3xl" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</x-app-layout>
<script>
  function openModal() {
    const modal = document.getElementById('modal');
    modal.classList.remove('hidden');
    setTimeout(() => {
      modal.classList.remove('opacity-0');
      modal.classList.add('opacity-100');
    }, 10);
  }

  function closeModal() {
    const modal = document.getElementById('modal');
    modal.classList.remove('opacity-100');
    modal.classList.add('opacity-0');

    // Hide the modal after the animation ends
    setTimeout(() => {
      modal.classList.add('hidden');
    }, 300);
  }

  jQuery(document).ready(function() {
    jQuery('#delete-event-btn').click(function(e) {
      e.preventDefault();
      const dashboardType = "{{ $dashboardType }}";
      const eventId = "{{ $event->id }}";

      showConfirmationNotification({
        text: 'Are you sure you want to delete this event?'
      }).then((result) => {
        $.ajax({
          url: "{{ route('admin.dashboard.delete-event', ['dashboardType' => ':dashboardType', 'id' => ':id']) }}"
            .replace(':dashboardType', dashboardType)
            .replace(':id', eventId),
          type: 'DELETE',
          data: {
            _token: '{{ csrf_token() }}',
          },
          success: function(response) {
            if (response.success) {
              showSuccessNotification(response.message);
              window.location.href = '/dashboard/promoter/events';
            } else {
              alert('Failed to delete the event.');
            }
          },
          error: function(xhr) {
            showFailureNotification(xhr.responseJSON.message ||
              'An error occurred while deleting the event.');
          }
        });
      });
    });
  });

  document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('addToCalendarButton').addEventListener('click', function(event) {
      event.preventDefault();
      checkCalendars();
    });
  });

  function checkCalendars() {
    const userId = {{ Auth::user()->id }};

    fetch(`/dashboard/promoter/events/${userId}/check-linked-calendars`)
      .then(response => {
        if (!response.ok) {
          throw new Error('Network response was not ok: ' + response.statusText);
        }
        return response.json();
      })
      .then(data => {
        if (data.hasGoogleCalendar) {
          const calendarService = 'google';
          addEventToGoogleCalendar(calendarService);
        } else {
          alert('Please link a Google Calendar to use this feature.');
        }
      })
      .catch(error => {
        console.error('Error checking calendars:', error);
      });
  }

  function addEventToGoogleCalendar(calendarService) {
    const eventId = {{ $event->id }};
    const eventName = @json($event->event_name);
    const eventDate = '{{ $event->event_date }}';
    const eventStartTime = '{{ $event->event_start_time }}';
    const eventEndTime = '{{ $event->event_end_time }}';
    const eventLocation = @json($event->venues->first()->location ?? '');
    const eventDescription = @json($event->event_description ?? '');
    const preSaleURL = @json($event->ticket_url ?? '');
    const otdTicketPrice = {{ $event->on_the_door_ticket_price }};

    fetch('/dashboard/promoter/events/add-to-calendar', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}' // Include CSRF token
        },
        body: JSON.stringify({
          event_id: eventId,
          title: eventName,
          date: eventDate,
          start_time: eventStartTime,
          end_time: eventEndTime,
          location: eventLocation,
          description: eventDescription,
          ticket_url: preSaleURL,
          on_the_door_ticket_price: otdTicketPrice,
          calendar_service: calendarService,
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          showSuccessNotification(data.message);
        } else {
          showFailureNotification(data.message || 'Failed to add event to the calendar.');
        }
      })
      .catch(error => {
        showFailureNotification('An error occurred while adding the event.');
      });
  }
</script>
