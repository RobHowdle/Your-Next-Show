<x-app-layout :dashboardType="$dashboardType" :modules="$modules">
  <x-slot name="header">
    <x-sub-nav :userId="$userId" />
  </x-slot>

  <div class="mx-auto w-full max-w-screen-2xl py-16">
    <div class="relative mb-8 shadow-md sm:rounded-lg">
      <div class="min-w-screen-xl mx-auto max-w-screen-xl rounded-lg border border-white bg-yns_dark_gray text-white">
        <div class="header border-b border-b-white px-8 pt-8">
          <div class="flex flex-row justify-between">
            <h1 class="font-heading text-4xl font-bold">My Events</h1>
            @if ($dashboardType != 'standard')
              <a href="{{ route('admin.dashboard.create-new-event', ['dashboardType' => $dashboardType]) }}"
                class="rounded-lg bg-white px-4 py-2 text-black transition duration-300 hover:bg-gradient-to-t hover:from-yns_dark_orange hover:to-yns_yellow">New
                Event</a>
            @else
              <a href="{{ route('gig-guide') }}"
                class="rounded-lg bg-white px-4 py-2 text-black transition duration-300 hover:bg-gradient-to-t hover:from-yns_dark_orange hover:to-yns_yellow">Find
                Events</a>
            @endif
          </div>

          <div class="mt-8 flex gap-x-8">
            <p id="upcoming-tab"
              class="cursor-pointer border-b-2 border-b-transparent pb-2 text-white transition duration-150 ease-in-out hover:border-b-yns_yellow hover:text-yns_yellow">
              Upcoming
              Events</p>
            <p id="past-tab"
              class="cursor-pointer border-b-2 border-b-transparent pb-2 text-white transition duration-150 ease-in-out hover:border-b-yns_yellow hover:text-yns_yellow">
              Past
              Events</p>
          </div>
        </div>

        <div id="tab-content" class="px-8 pt-8">
          <div id="upcoming-events" class="event-grid">
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
              @if ($initialUpcomingEvents && $initialUpcomingEvents->isNotEmpty())
                @foreach ($initialUpcomingEvents as $event)
                  @include('admin.dashboards.partials.event_card', ['event' => $event])
                @endforeach
              @else
                <p>No Upcoming Events Found</p>
              @endif
            </div>
          </div>

          <div id="past-events" class="event-grid hidden">
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
              @if ($pastEvents && $pastEvents->isNotEmpty())
                @foreach ($pastEvents as $event)
                  @include('admin.dashboards.partials.event_card', ['event' => $event])
                @endforeach
              @else
                <p>No Past Events Found</p>
              @endif
            </div>
          </div>
        </div>

        <div class="mt-6 text-center">
          @if ($showLoadMoreUpcoming)
            <button id="load-more-upcoming"
              class="mb-4 rounded-lg bg-white px-4 py-2 text-black transition duration-300 hover:bg-gradient-to-t hover:from-yns_dark_orange hover:to-yns_yellow">
              Load More
            </button>
          @else
            <button id="load-more-upcoming" class="hidden"></button>
          @endif
        </div>
        <div id="loading-spinner"
          class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm" aria-hidden="true"
          role="progressbar">
          <div class="h-32 w-32 animate-spin rounded-full border-b-2 border-t-2 border-yns_yellow"></div>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
<style>
  .fade-out {
    opacity: 0;
    transform: scale(0.95);
    transition: opacity 0.3s ease, transform 0.3s ease;
  }

  .event-card {
    transition: opacity 0.3s ease, transform 0.3s ease;
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
      this.elements.upcomingTab.classList.toggle('border-b-yns_yellow', isUpcoming);
      this.elements.pastTab.classList.toggle('border-b-yns_yellow', !isUpcoming);

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
