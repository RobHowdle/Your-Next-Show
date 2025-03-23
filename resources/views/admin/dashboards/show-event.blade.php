<x-app-layout :dashboardType="$dashboardType" :modules="$modules">
  <x-slot name="header">
    <x-sub-nav :userId="$userId" />
  </x-slot>

  <div class="mx-auto w-full max-w-screen-2xl py-16">
    <div class="mx-auto w-full max-w-screen-2xl px-4 sm:px-6 lg:px-8">
      <div class="overflow-hidden rounded-xl border border-yns_yellow bg-yns_dark_gray/50 backdrop-blur-xl">
        <!-- Header Section -->
        <div class="border-b border-yns_yellow px-6 py-8">
          <div class="flex flex-col gap-6 sm:flex-row sm:items-start sm:justify-between">
            <div class="space-y-2">
              <h1 class="font-heading text-3xl font-bold text-white sm:text-4xl">{{ $event->event_name }}</h1>
              <div class="flex items-center text-gray-400">
                <span class="fas fa-calendar-day mr-2"></span>
                <span>{{ $event->event_date->format('l, jS F Y') }}</span>
                <span class="mx-2">•</span>
                <span class="fas fa-clock mr-2"></span>
                <span>{{ $eventStartTime }}@if ($eventEndTime)
                    - {{ $eventEndTime }}
                  @endif
                </span>
              </div>
            </div>

            <div class="flex flex-wrap gap-3">
              @if (!$isPastEvent)
                @if ($event->ticket_url)
                  <a href="{{ $event->ticket_url }}" target="_blank"
                    class="inline-flex items-center rounded-lg bg-yns_yellow px-4 py-2 text-sm font-semibold text-gray-900 transition hover:bg-yns_dark_orange hover:text-white">
                    <span class="fas fa-ticket-alt mr-2"></span>
                    Get Tickets
                  </a>
                @endif
                <button id="addToCalendarButton"
                  class="inline-flex items-center rounded-lg bg-gray-800 px-4 py-2 text-sm font-semibold text-white ring-1 ring-gray-700 transition hover:bg-gray-700 hover:ring-yns_yellow">
                  <span class="fas fa-calendar-plus mr-2"></span>
                  Add To Calendar
                </button>
              @endif
              <a href="{{ route('admin.dashboard.edit-event', ['id' => $event->id, 'dashboardType' => $dashboardType]) }}"
                class="inline-flex items-center rounded-lg bg-gray-800 px-4 py-2 text-sm font-semibold text-white ring-1 ring-gray-700 transition hover:bg-gray-700 hover:ring-yns_yellow">
                <span class="fas fa-edit mr-2"></span>
                Edit Event
              </a>
              <button id="delete-event-btn" data-id="{{ $event->id }}"
                class="inline-flex items-center rounded-lg bg-gray-800 px-4 py-2 text-sm font-semibold text-red-500 ring-1 ring-gray-700 transition hover:bg-red-500 hover:text-white hover:ring-red-500">
                <span class="fas fa-trash mr-2"></span>
                Delete Event
              </button>
            </div>
          </div>
        </div>

        <!-- Tabs Navigation -->
        @if ($event->user_id === Auth::id())
          <div class="mt-8 border-b border-yns_yellow">
            <nav class="flex gap-x-8 px-4 sm:px-6 lg:px-8" aria-label="Tabs">
              <button id="info-tab" type="button"
                class="group inline-flex items-center border-b-2 border-transparent px-1 py-4 text-sm font-medium text-gray-400 transition duration-200 hover:border-yns_yellow hover:text-yns_yellow">
                <span class="fas fa-info-circle mr-2"></span>
                Event Info
              </button>
              <button id="stats-tab" type="button"
                class="group inline-flex items-center border-b-2 border-transparent px-1 py-4 text-sm font-medium text-gray-400 transition duration-200 hover:border-yns_yellow hover:text-yns_yellow">
                <span class="fas fa-chart-bar mr-2"></span>
                Event Stats
                @if ($stats['hasNewStats'] ?? false)
                  <span class="ml-2 flex h-2 w-2">
                    <span
                      class="absolute inline-flex h-2 w-2 animate-ping rounded-full bg-yns_yellow opacity-75"></span>
                    <span class="relative inline-flex h-2 w-2 rounded-full bg-yns_yellow"></span>
                  </span>
                @endif
              </button>
            </nav>
          </div>
        @endif

        <div class="mt-6 space-y-6">
          <div id="info-content" class="tab-content">
            <!-- Content Grid -->
            <div class="grid grid-cols-1 gap-8 p-6 lg:grid-cols-3">
              <!-- Event Details -->
              <div class="lg:col-span-2 lg:border-r lg:border-yns_yellow lg:pr-8">
                <!-- Lineup Section -->
                <div class="mb-8 space-y-6 border-b border-yns_yellow pb-8">
                  @if ($headliner)
                    <div>
                      <h3 class="mb-2 font-heading text-lg font-medium text-gray-400">Headliner</h3>
                      <a href="{{ route('singleService', ['serviceType' => 'Artist', 'name' => '$headliner->name']) }}"
                        class="text-xl font-semibold text-white hover:text-yns_yellow">{{ $headliner->name }}</a>
                    </div>
                  @endif

                  @if ($mainSupport)
                    <div>
                      <h3 class="mb-2 font-heading text-lg font-medium text-gray-400">Main Support</h3>
                      <a href="{{ route('singleService', ['serviceType' => 'Artist', 'name' => '$mainSupport->name']) }}"
                        class="text-lg font-semibold text-white hover:text-yns_yellow">{{ $mainSupport->name }}</a>
                    </div>
                  @endif

                  @if ($otherBands && count($otherBands) > 0)
                    <div>
                      <h3 class="mb-2 font-heading text-lg font-medium text-gray-400">Other Acts</h3>
                      <div class="flex flex-wrap gap-2">
                        @foreach ($otherBands as $band)
                          <a href="{{ route('singleService', ['serviceType' => 'Artist', 'name' => '$band->name']) }}"
                            class="rounded-full bg-gray-800 px-3 py-1 text-sm text-gray-300 hover:bg-gray-700 hover:text-yns_yellow">
                            {{ $band->name }}
                          </a>
                        @endforeach
                      </div>
                    </div>
                  @endif

                  @if ($opener)
                    <div>
                      <h3 class="mb-2 font-heading text-lg font-medium text-gray-400">Opener</h3>
                      <a href="{{ route('singleService', ['serviceType' => 'Artist', 'name' => '$opener->name']) }}"
                        class="text-base font-semibold text-white hover:text-yns_yellow">{{ $opener->name }}</a>
                    </div>
                  @endif
                </div>

                <!-- Event Info -->
                <div class="space-y-4">
                  @if ($event->event_description)
                    <div class="flex items-start">
                      <span class="fas fa-info-circle mt-1 text-gray-400"></span>
                      <p class="ml-3 text-gray-300">{{ $event->event_description }}</p>
                    </div>
                  @endif

                  <div class="flex items-start">
                    <span class="fas fa-map-marker-alt mt-1 text-gray-400"></span>
                    <div class="ml-3">
                      @forelse($event->venues as $venue)
                        <a href="{{ route('venues', $venue->id) }}"
                          class="text-white hover:text-yns_yellow">{{ $venue->location }}</a>
                      @empty
                        <p class="text-gray-400">No Venue Assigned</p>
                      @endforelse
                    </div>
                  </div>

                  <div class="flex items-start">
                    <span class="fas fa-bullhorn mt-1 text-gray-400"></span>
                    <div class="ml-3">
                      @forelse($event->promoters as $promoter)
                        <a href="{{ route('promoters', $promoter->id) }}"
                          class="text-white hover:text-yns_yellow">{{ $promoter->name }}</a>
                      @empty
                        <p class="text-gray-400">No Promoter Assigned</p>
                      @endforelse
                    </div>
                  </div>

                  <div class="flex items-start">
                    <span class="fas fa-ticket-alt mt-1 text-gray-400"></span>
                    <p class="ml-3 text-gray-300">{{ formatCurrency($event->on_the_door_ticket_price) }} On The Door
                    </p>
                  </div>
                </div>
              </div>

              <!-- Event Poster -->
              <div>
                <div class="relative">
                  <img src="{{ asset($event->poster_url) }}" alt="{{ $event->event_name }} Poster"
                    class="w-full cursor-pointer rounded-lg object-cover transition hover:opacity-75" id="eventPoster"
                    onclick="openModal()">
                  <button
                    class="absolute right-4 top-4 rounded-full bg-black/50 p-2 text-white backdrop-blur-sm transition hover:bg-black/70"
                    onclick="openModal()">
                    <span class="fas fa-search-plus"></span>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <div id="stats-content" class="tab-content">
            <div class="p-6">
              <!-- Stats Grid -->
              <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <!-- Ticket Sales -->
                <div class="rounded-lg bg-gray-800/50 p-6">
                  <div class="flex items-center justify-between">
                    <h3 class="text-sm font-medium text-gray-400">Ticket Sales</h3>
                    <span class="fas fa-ticket-alt text-yns_yellow"></span>
                  </div>
                  <p class="mt-2 text-3xl font-bold text-white">{{ $stats['ticketsSold'] ?? 0 }}</p>
                  <p class="mt-1 text-sm text-gray-400">
                    @if (isset($stats['ticketsAvailable']))
                      {{ round(($stats['ticketsSold'] / $stats['ticketsAvailable']) * 100) }}% of capacity
                    @endif
                  </p>
                </div>

                <!-- Revenue -->
                <div class="rounded-lg bg-gray-800/50 p-6">
                  <div class="flex items-center justify-between">
                    <h3 class="text-sm font-medium text-gray-400">Revenue</h3>
                    <span class="fas fa-pound-sign text-yns_yellow"></span>
                  </div>
                  <p class="mt-2 text-3xl font-bold text-white">{{ formatCurrency($stats['totalRevenue'] ?? 0) }}
                  </p>
                  <p class="mt-1 text-sm text-gray-400">From ticket sales</p>
                </div>

                <!-- Page Views -->
                <div class="rounded-lg bg-gray-800/50 p-6">
                  <div class="flex items-center justify-between">
                    <h3 class="text-sm font-medium text-gray-400">Page Views</h3>
                    <span class="fas fa-eye text-yns_yellow"></span>
                  </div>
                  <p class="mt-2 text-3xl font-bold text-white">{{ $stats['pageViews'] ?? 0 }}</p>
                  <p class="mt-1 text-sm text-gray-400">Last 30 days</p>
                </div>

                <!-- Click Through Rate -->
                <div class="rounded-lg bg-gray-800/50 p-6">
                  <div class="flex items-center justify-between">
                    <h3 class="text-sm font-medium text-gray-400">Ticket Link CTR</h3>
                    <span class="fas fa-mouse-pointer text-yns_yellow"></span>
                  </div>
                  <p class="mt-2 text-3xl font-bold text-white">{{ $stats['clickThroughRate'] ?? '0%' }}</p>
                  <p class="mt-1 text-sm text-gray-400">Conversion rate</p>
                </div>
              </div>

              <!-- Charts Section -->
              <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
                <!-- Sales Over Time -->
                <div class="rounded-lg bg-gray-800/50 p-6">
                  <h3 class="mb-4 text-sm font-medium text-gray-400">Sales Timeline</h3>
                  <div>
                    <canvas id="salesChart" class="h-64"></canvas>
                  </div>
                </div>

                <!-- Traffic Sources -->
                <div class="rounded-lg bg-gray-800/50 p-6">
                  <h3 class="mb-4 text-sm font-medium text-gray-400">Traffic Sources</h3>
                  <div>
                    <canvas id="trafficChart" class="h-64"></canvas>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal -->
  <div id="modal"
    class="fixed inset-0 z-50 hidden bg-black bg-opacity-75 p-4 backdrop-blur-sm transition-opacity duration-300 ease-in-out">
    <div class="flex min-h-full items-center justify-center">
      <div class="relative max-h-[90vh] max-w-3xl overflow-hidden rounded-lg bg-yns_dark_gray p-4">
        <button
          class="absolute right-4 top-4 rounded-full bg-black/50 p-2 text-white backdrop-blur-sm transition hover:bg-black/70"
          onclick="closeModal()">
          <span class="fas fa-times"></span>
        </button>
        <img src="{{ asset($event->poster_url) }}" alt="Enlarged Event Poster" class="max-h-full max-w-full">
      </div>
    </div>
  </div>
</x-app-layout>

<style>
  .backdrop-blur-sm {
    backdrop-filter: blur(8px);
  }

  @keyframes fadeIn {
    from {
      opacity: 0;
      transform: scale(0.95);
    }

    to {
      opacity: 1;
      transform: scale(1);
    }
  }

  #modal.opacity-100 {
    animation: fadeIn 0.3s ease-out;
  }

  .tab-content {
    transition: all 0.3s ease-in-out;
  }

  .tab-content.hidden {
    display: none;
    opacity: 0;
    transform: translateY(10px);
  }

  .tab-content:not(.hidden) {
    display: block;
    opacity: 1;
    transform: translateY(0);
  }
</style>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const infoTab = document.getElementById('info-tab');
    const statsTab = document.getElementById('stats-tab');
    const infoContent = document.getElementById('info-content');
    const statsContent = document.getElementById('stats-content');

    // Store chart instances and observers
    let charts = {
      sales: null,
      traffic: null
    };
    let resizeObserver = null;

    function createBaseChartConfig(type, data, options = {}) {
      return {
        type,
        data,
        options: {
          ...options,
          responsive: true,
          maintainAspectRatio: false,
          animation: false, // Disable all animations
          resizeDelay: 0, // No resize delay
          responsiveAnimationDuration: 0 // No animation on resize
        }
      };
    }

    function destroyCharts() {
      // Disconnect resize observer
      if (resizeObserver) {
        resizeObserver.disconnect();
        resizeObserver = null;
      }

      // Destroy existing charts
      Object.values(charts).forEach(chart => {
        if (chart) {
          chart.destroy();
        }
      });
      charts = {
        sales: null,
        traffic: null
      };
    }

    function initializeCharts() {
      destroyCharts();

      const salesCanvas = document.getElementById('salesChart');
      const trafficCanvas = document.getElementById('trafficChart');

      if (!salesCanvas || !trafficCanvas) return;

      // Create sales chart with static configuration
      charts.sales = new Chart(salesCanvas, createBaseChartConfig('line', {
        labels: @json($stats['salesData']->pluck('date')),
        datasets: [{
          label: 'Ticket Sales',
          data: @json($stats['salesData']->pluck('count')),
          borderColor: '#D59220',
          backgroundColor: 'rgba(213, 146, 32, 0.1)',
          tension: 0.4,
          fill: true
        }]
      }, {
        plugins: {
          legend: {
            display: false
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            grid: {
              color: 'rgba(255, 255, 255, 0.1)'
            },
            ticks: {
              color: '#9CA3AF'
            }
          },
          x: {
            grid: {
              display: false
            },
            ticks: {
              color: '#9CA3AF'
            }
          }
        }
      }));

      // Create traffic chart with static configuration
      charts.traffic = new Chart(trafficCanvas, createBaseChartConfig('doughnut', {
        labels: @json($stats['trafficSources']->pluck('source')),
        datasets: [{
          data: @json($stats['trafficSources']->pluck('count')),
          backgroundColor: ['#D59220', '#F03F37', '#9022BB', '#29C0D2']
        }]
      }, {
        plugins: {
          legend: {
            position: 'bottom',
            labels: {
              color: '#9CA3AF',
              padding: 20,
              usePointStyle: true
            }
          }
        }
      }));

      // Set up resize observer for the container
      const container = statsContent;
      resizeObserver = new ResizeObserver(entries => {
        for (const chart of Object.values(charts)) {
          if (chart) {
            chart.resize();
          }
        }
      });
      resizeObserver.observe(container);
    }

    function switchTab(clickedTab) {
      const isInfoTab = clickedTab === infoTab;

      // Update tab styles
      infoTab.classList.toggle('border-yns_yellow', isInfoTab);
      infoTab.classList.toggle('text-yns_yellow', isInfoTab);
      statsTab.classList.toggle('border-yns_yellow', !isInfoTab);
      statsTab.classList.toggle('text-yns_yellow', !isInfoTab);

      // Toggle content visibility
      infoContent.classList.toggle('hidden', !isInfoTab);
      statsContent.classList.toggle('hidden', isInfoTab);

      // Handle charts
      if (!isInfoTab) {
        requestAnimationFrame(() => {
          requestAnimationFrame(() => {
            initializeCharts();
          });
        });
      } else {
        destroyCharts();
      }
    }

    // Add click handlers
    infoTab?.addEventListener('click', () => switchTab(infoTab));
    statsTab?.addEventListener('click', () => switchTab(statsTab));

    // Set initial state
    switchTab(infoTab);

    // Clean up when leaving page
    window.addEventListener('beforeunload', destroyCharts);
  });

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
