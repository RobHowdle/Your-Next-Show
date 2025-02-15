<div x-data="eventbriteLink()" x-show="show" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
  <div class="flex min-h-screen items-end justify-center px-4 pb-20 pt-4 text-center sm:block sm:p-0">
    <div x-show="show" class="fixed inset-0 transition-opacity" aria-hidden="true">
      <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
    </div>

    <div
      class="inline-block transform overflow-hidden rounded-lg bg-white text-left align-bottom shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:align-middle">
      <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
        <div class="sm:flex sm:items-start">
          <div class="mt-3 w-full text-center sm:ml-4 sm:mt-0 sm:text-left">
            <h3 class="text-lg font-medium leading-6 text-gray-900">
              Link Eventbrite Event
            </h3>
            <div class="mt-4">
              <input type="text" x-model="searchQuery" @input.debounce="searchEvents"
                placeholder="Search your Eventbrite events..."
                class="w-full rounded-md border border-gray-300 px-3 py-2">
            </div>
            <div class="max-h-60 mt-4 overflow-y-auto">
              <template x-for="event in events" :key="event.id">
                <div class="cursor-pointer rounded-lg p-3 hover:bg-gray-100" @click="selectEvent(event)">
                  <p x-text="event.name.text" class="font-medium"></p>
                  <p x-text="formatDate(event.start.local)" class="text-sm text-gray-600"></p>
                </div>
              </template>
            </div>
          </div>
        </div>
      </div>
      <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
        <button type="button" @click="close"
          class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:mt-0 sm:w-auto sm:text-sm">
          Close
        </button>
      </div>
    </div>
  </div>
</div>

<script>
  function eventbriteLink() {
    return {
      show: false,
      searchQuery: '',
      events: [],
      selectedEvent: null,

      async searchEvents() {
        if (this.searchQuery.length < 2) return;

        try {
          const response = await fetch(`/api/events/search-eventbrite?search=${this.searchQuery}`);
          const data = await response.json();
          this.events = data.events;
        } catch (error) {
          console.error('Error searching events:', error);
        }
      },

      async selectEvent(event) {
        try {
          const response = await fetch('/api/events/link', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
              event_id: '{{ $event->id }}',
              platform_name: 'eventbrite',
              platform_event_id: event.id,
              platform_event_url: event.url,
              platform_event_data: event
            })
          });

          if (response.ok) {
            this.close();
            window.location.reload();
          }
        } catch (error) {
          console.error('Error linking event:', error);
        }
      },

      formatDate(date) {
        return new Date(date).toLocaleDateString();
      },

      close() {
        this.show = false;
        this.searchQuery = '';
        this.events = [];
      }
    }
  }
</script>
