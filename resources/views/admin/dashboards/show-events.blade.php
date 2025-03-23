<x-app-layout :dashboardType="$dashboardType" :modules="$modules">
  <x-slot name="header">
    <x-sub-nav :userId="$userId" />
  </x-slot>

  <div class="min-h-screen py-8">
    <div class="mx-auto max-w-screen-2xl px-4 sm:px-6 lg:px-8">
      <div class="relative overflow-hidden rounded-xl border border-gray-800 bg-gray-800 backdrop-blur-xl">
        <div class="p-6 md:p-8">
          <!-- Header Section -->
          <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
              <h1 class="text-2xl font-bold text-white">My Events</h1>
              <p class="mt-1 text-sm text-gray-400">Manage and monitor all your events</p>
            </div>
            @if ($dashboardType != 'standard')
              <a href="{{ route('admin.dashboard.create-new-event', ['dashboardType' => $dashboardType]) }}"
                class="inline-flex items-center justify-center rounded-lg bg-yns_yellow px-4 py-2.5 text-sm font-semibold text-gray-900 shadow-sm transition duration-200 hover:bg-yns_dark_orange hover:text-white">
                <span class="fas fa-plus mr-2"></span>
                New Event
              </a>
            @else
              <a href="{{ route('gig-guide') }}"
                class="inline-flex items-center justify-center rounded-lg bg-yns_yellow px-4 py-2.5 text-sm font-semibold text-gray-900 shadow-sm transition duration-200 hover:bg-yns_dark_orange hover:text-white">
                <span class="fas fa-search mr-2"></span>
                Find Events
              </a>
            @endif
          </div>

          <!-- Tabs Navigation -->
          <div class="mb-6 border-b border-gray-700">
            <nav class="-mb-px flex gap-x-8" aria-label="Tabs">
              <button id="upcoming-tab"
                class="group inline-flex items-center border-b-2 border-transparent px-1 py-4 text-sm font-medium text-gray-400 transition duration-200 hover:border-yns_yellow hover:text-yns_yellow"
                aria-current="page">
                <span class="fas fa-calendar mr-2"></span>
                Upcoming Events
                <span
                  class="ml-3 rounded-full bg-gray-700 px-2.5 py-0.5 text-xs font-medium text-gray-300 group-hover:bg-yns_yellow group-hover:text-gray-900">
                  {{ $initialUpcomingEvents->count() }}
                </span>
              </button>
              <button id="past-tab"
                class="group inline-flex items-center border-b-2 border-transparent px-1 py-4 text-sm font-medium text-gray-400 transition duration-200 hover:border-yns_yellow hover:text-yns_yellow">
                <span class="fas fa-history mr-2"></span>
                Past Events
                <span
                  class="ml-3 rounded-full bg-gray-700 px-2.5 py-0.5 text-xs font-medium text-gray-300 group-hover:bg-yns_yellow group-hover:text-gray-900">
                  {{ $pastEvents->count() }}
                </span>
              </button>
            </nav>
          </div>

          <!-- Events Grid -->
          <div id="tab-content" class="relative">
            <div id="upcoming-events" class="event-grid">
              <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @if ($initialUpcomingEvents && $initialUpcomingEvents->isNotEmpty())
                  @foreach ($initialUpcomingEvents as $event)
                    <div
                      class="event-card group relative overflow-hidden rounded-lg border border-gray-700 bg-gray-800/50 p-6 shadow-sm transition duration-200 hover:border-yns_yellow hover:bg-gray-800"
                      data-id="{{ $event->id }}">
                      <div class="flex items-center gap-4">
                        <div class="h-16 w-16 flex-shrink-0 overflow-hidden rounded-lg border border-gray-700">
                          <img src="{{ asset($event->poster_url) }}" alt="{{ $event->event_name }}"
                            class="h-full w-full object-cover transition duration-200 group-hover:scale-110">
                        </div>
                        <div class="flex-1 overflow-hidden">
                          <h3 class="truncate text-lg font-medium text-white">
                            <a href="{{ route('admin.dashboard.show-event', ['id' => $event->id, 'dashboardType' => $dashboardType]) }}"
                              class="hover:text-yns_yellow">
                              {{ $event->event_name }}
                            </a>
                          </h3>
                          <p class="mt-1 flex items-center text-sm text-gray-400">
                            <span class="fas fa-calendar-day mr-1.5"></span>
                            {{ $event->event_date->format('D, j M, Y') }}
                          </p>
                        </div>
                      </div>

                      <div class="mt-4 border-t border-gray-700 pt-4">
                        <div class="flex items-center justify-between">
                          <div class="flex items-center space-x-4">
                            @if ($event->venues->isNotEmpty())
                              <span class="inline-flex items-center text-sm text-gray-400">
                                <span class="fas fa-map-marker-alt mr-1.5 text-yns_yellow"></span>
                                {{ $event->venues->first()->location }}
                              </span>
                            @endif
                          </div>
                          <div class="flex items-center gap-2">
                            <a href="{{ route('admin.dashboard.edit-event', ['id' => $event->id, 'dashboardType' => $dashboardType]) }}"
                              class="rounded-full p-1.5 text-gray-400 transition duration-200 hover:bg-gray-700 hover:text-yns_yellow">
                              <span class="fas fa-edit"></span>
                            </a>
                            <button
                              class="delete-event rounded-full p-1.5 text-gray-400 transition duration-200 hover:bg-gray-700 hover:text-red-500"
                              data-id="{{ $event->id }}">
                              <span class="fas fa-trash"></span>
                            </button>
                          </div>
                        </div>
                      </div>

                      @if ($event->ticket_url)
                        <div class="absolute right-4 top-4">
                          <span
                            class="inline-flex items-center rounded-full bg-yns_yellow/20 px-2.5 py-0.5 text-xs font-medium text-yns_yellow">
                            Tickets Available
                          </span>
                        </div>
                      @endif
                    </div>
                  @endforeach
                @else
                  <div class="col-span-full rounded-lg border border-gray-700 bg-gray-800/50 p-12 text-center">
                    <span class="fas fa-calendar-plus mb-4 text-4xl text-gray-600"></span>
                    <h3 class="mt-2 text-sm font-medium text-white">No upcoming events</h3>
                    <p class="mt-1 text-sm text-gray-400">Get started by creating a new event.</p>
                  </div>
                @endif
              </div>
            </div>
          </div>

          <!-- Load More Button -->
          <div class="mt-8 flex justify-center">
            @if ($showLoadMoreUpcoming)
              <button id="load-more-upcoming"
                class="inline-flex items-center rounded-md bg-gray-800 px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm ring-1 ring-inset ring-gray-700 transition duration-200 hover:bg-gray-700 hover:ring-yns_yellow">
                <span class="fas fa-spinner mr-2"></span>
                Load More Events
              </button>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>

<style>
  .event-card {
    opacity: 1;
    transform: translateY(0);
    transition: all 0.3s ease-in-out;
  }

  .event-card.fade-out {
    opacity: 0;
    transform: translateY(-10px);
  }

  /* Tab Indicator Animation */
  #upcoming-tab.active,
  #past-tab.active {
    @apply border-indigo-600 text-indigo-600;
    position: relative;
  }

  #upcoming-tab.active::after,
  #past-tab.active::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    width: 100%;
    height: 2px;
    background-color: currentColor;
    transform: scaleX(0);
    animation: slideIn 0.3s ease forwards;
  }

  @keyframes slideIn {
    to {
      transform: scaleX(1);
    }
  }
</style>
<script>
  class AdminEventManager {
    constructor() {
      // Initialize state
      this.upcomingPage = 1;
      this.pastPage = 1;
      this.currentTab = 'upcoming';
      this.dashboardType = '{{ $dashboardType }}';
      this.isLoading = false;

      // Cache DOM elements
      this.elements = {
        upcomingTab: document.getElementById('upcoming-tab'),
        pastTab: document.getElementById('past-tab'),
        upcomingEvents: document.getElementById('upcoming-events'),
        pastEvents: document.getElementById('past-events'),
        loadMoreUpcoming: document.getElementById('load-more-upcoming'),
        loadMorePast: document.getElementById('load-more-past'),
        loadingSpinner: document.getElementById('loading-spinner')
      };

      this.initializeEventListeners();
    }

    initializeEventListeners() {
      // Tab switching
      this.elements.upcomingTab?.addEventListener('click', () => this.switchTab('upcoming'));
      this.elements.pastTab?.addEventListener('click', () => this.switchTab('past'));

      // Load more buttons
      this.elements.loadMoreUpcoming?.addEventListener('click', (e) => this.loadMore(e, 'upcoming'));
      this.elements.loadMorePast?.addEventListener('click', (e) => this.loadMore(e, 'past'));

      // Delete event delegation
      document.addEventListener('click', async (e) => {
        if (e.target.closest('.delete-event')) {
          e.preventDefault();
          const eventId = e.target.closest('.delete-event').dataset.id;
          await this.deleteEvent(eventId);
        }
      });
    }

    async loadMore(e, type) {
      if (this.isLoading) return;
      e.preventDefault();

      const page = type === 'upcoming' ? ++this.upcomingPage : ++this.pastPage;
      await this.loadEvents(type, page);
    }

    async loadEvents(type, page = 1) {
      this.toggleLoading(true);
      const container = type === 'upcoming' ?
        this.elements.upcomingEvents :
        this.elements.pastEvents;

      try {
        const endpoint = type === 'upcoming' ?
          `/dashboard/${this.dashboardType}/events/load-more-upcoming` :
          `/dashboard/${this.dashboardType}/events/load-more-past`;

        const response = await fetch(`${endpoint}?page=${page}`, {
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          }
        });

        if (!response.ok) throw new Error('Network response was not ok');

        const data = await response.json();

        if (page === 1) {
          container.innerHTML = `
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    ${data.html}
                </div>
            `;
        } else {
          const gridContainer = container.querySelector('.grid');
          if (gridContainer) {
            gridContainer.insertAdjacentHTML('beforeend', data.html);
          }
        }

        // Update load more button visibility
        const loadMoreButton = type === 'upcoming' ?
          this.elements.loadMoreUpcoming :
          this.elements.loadMorePast;

        if (loadMoreButton) {
          loadMoreButton.classList.toggle('hidden', !data.hasMorePages);
        }

      } catch (error) {
        console.error('Error loading events:', error);
        this.showError('Failed to load events. Please try again.');
      } finally {
        this.toggleLoading(false);
      }
    }

    async deleteEvent(eventId) {
      if (!eventId || this.isLoading) return;

      try {
        const confirmed = await Swal.fire({
          title: 'Are you sure?',
          text: 'This event will be permanently deleted.',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#FFB800',
          cancelButtonColor: '#ef4444',
          confirmButtonText: 'Yes, delete it!'
        });

        if (confirmed.isConfirmed) {
          const response = await fetch(`/dashboard/${this.dashboardType}/events/${eventId}/delete`, {
            method: 'DELETE',
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
              'Accept': 'application/json'
            }
          });

          if (!response.ok) throw new Error('Failed to delete event');

          const eventCard = document.querySelector(`.event-card[data-id="${eventId}"]`);
          if (eventCard) {
            eventCard.classList.add('fade-out');
            setTimeout(() => eventCard.remove(), 300);
          }

          await Swal.fire(
            'Deleted!',
            'Event has been deleted.',
            'success'
          );
        }
      } catch (error) {
        console.error('Error deleting event:', error);
        this.showError('Failed to delete event. Please try again.');
      }
    }

    toggleLoading(show) {
      this.isLoading = show;
      if (this.elements.loadingSpinner) {
        if (show) {
          this.elements.loadingSpinner.classList.remove('hidden');
          document.body.style.overflow = 'hidden'; // Prevent scrolling while loading
        } else {
          this.elements.loadingSpinner.classList.add('hidden');
          document.body.style.overflow = ''; // Restore scrolling
        }
      }
    }

    showError(message) {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: message,
        confirmButtonColor: '#FFB800'
      });
    }

    switchTab(tab) {
      if (this.isLoading || this.currentTab === tab) return;

      this.currentTab = tab;
      const isUpcoming = tab === 'upcoming';

      // Update UI
      this.elements.upcomingEvents.classList.toggle('hidden', !isUpcoming);
      this.elements.pastEvents.classList.toggle('hidden', isUpcoming);

      // Update tab styles
      this.elements.upcomingTab.classList.toggle('active', isUpcoming);
      this.elements.pastTab.classList.toggle('active', !isUpcoming);

      // Reset page number when switching tabs
      if (isUpcoming) {
        this.upcomingPage = 1;
      } else {
        this.pastPage = 1;
      }

      // Load initial data if needed
      this.loadEvents(tab, 1);
    }
  }

  // Initialize when DOM is loaded
  document.addEventListener('DOMContentLoaded', () => {
    window.adminEventManager = new AdminEventManager();
  });
</script>
