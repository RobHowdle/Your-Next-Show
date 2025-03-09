<x-guest-layout>
  <div class="flex min-h-screen">
    <div class="relative flex w-full flex-col">
      {{-- Hero Section --}}
      <div class="relative mt-28 flex w-full items-center justify-center px-4 py-12">
        <div class="relative z-10 w-full max-w-7xl">
          <div class="mb-8 text-center">
            <h1 class="font-heading text-4xl font-bold text-white md:text-5xl">Upcoming Events</h1>
            <p class="mt-4 text-lg text-gray-300">Discover amazing shows and performances near you</p>
          </div>

          {{-- Filter Section --}}
          <div class="mb-8 rounded-lg border border-gray-800 bg-gray-900/50 p-4 backdrop-blur-sm md:p-6">
            <form id="filter-form" class="grid gap-4 md:grid-cols-4">
              {{-- Genre Filter --}}
              <div>
                <label for="genre" class="block text-sm font-medium text-gray-300">Genre</label>
                <select id="genre" name="genre"
                  class="mt-1 block w-full rounded-md border-gray-800 bg-gray-900 px-3 py-2 text-sm text-white focus:border-yns_yellow focus:ring-yns_yellow">
                  <option value="">All Genres</option>
                  @foreach ($genres as $genreName)
                    <option value="{{ $genreName }}" {{ request('genre') === $genreName ? 'selected' : '' }}>
                      {{ $genreName }}
                    </option>
                  @endforeach
                </select>
              </div>

              {{-- Band Type Filter --}}
              <div>
                <label for="band_type" class="block text-sm font-medium text-gray-300">Band Type</label>
                <select id="band_type" name="band_type"
                  class="mt-1 block w-full rounded-md border-gray-800 bg-gray-900 px-3 py-2 text-sm text-white focus:border-yns_yellow focus:ring-yns_yellow">
                  <option value="all">All Types</option>
                  <option value="original-band">Original</option>
                  <option value="cover-bands">Covers</option>
                  <option value="tribute-bands">Tribute</option>
                </select>
              </div>

              {{-- Location Filter --}}
              <div>
                <label for="location" class="block text-sm font-medium text-gray-300">Location</label>
                <input type="text" id="location" name="location" placeholder="Enter town or city"
                  class="mt-1 block w-full rounded-md border-gray-800 bg-gray-900 px-3 py-2 text-sm text-white placeholder-gray-500 focus:border-yns_yellow focus:ring-yns_yellow">
              </div>

              {{-- Search Button --}}
              <div class="flex items-end">
                <button type="submit"
                  class="w-full rounded-md bg-yns_yellow px-4 py-2 text-sm font-semibold text-black hover:bg-yns_yellow/90 focus:outline-none focus:ring-2 focus:ring-yns_yellow focus:ring-offset-2 focus:ring-offset-gray-900">
                  Search Events
                </button>
              </div>
            </form>
          </div>

          <div class="relative">
            {{-- Loading Overlay --}}
            <div id="loading-overlay"
              class="absolute inset-0 z-10 hidden items-center justify-center rounded-lg bg-gray-900/80 backdrop-blur-sm">
              <div class="text-center">
                <div
                  class="inline-block h-8 w-8 animate-spin rounded-full border-4 border-solid border-yns_yellow border-r-transparent align-[-0.125em]"
                  role="status">
                  <span
                    class="!absolute !-m-px !h-px !w-px !overflow-hidden !whitespace-nowrap !border-0 !p-0 ![clip:rect(0,0,0,0)]">Loading...</span>
                </div>
                <p class="mt-2 text-sm text-gray-300">Loading events...</p>
              </div>
            </div>

            {{-- Events Grid Container --}}
            <div id="events-container">
              <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @include('partials.event-grid')
              </div>

              {{-- Pagination --}}
              <div class="mt-8">
                {{ $events->links() }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</x-guest-layout>
<script>
  function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
      const later = () => {
        clearTimeout(timeout);
        func(...args);
      };
      clearTimeout(timeout);
      timeout = setTimeout(later, wait);
    };
  }

  function showLoading() {
    const overlay = document.getElementById('loading-overlay');
    overlay.classList.remove('hidden');
    overlay.classList.add('flex');
  }

  function hideLoading() {
    const overlay = document.getElementById('loading-overlay');
    overlay.classList.add('hidden');
    overlay.classList.remove('flex');
  }

  function updateEvents(e) {
    if (e) e.preventDefault();

    const formData = new FormData(filterForm);
    const params = new URLSearchParams(formData);

    showLoading();

    fetch(`/events/filter?${params.toString()}`, {
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(response => response.text())
      .then(html => {
        document.querySelector('.grid.gap-6').innerHTML = html;
      })
      .catch(error => {
        console.error('Error:', error);
      })
      .finally(() => {
        hideLoading();
      });
  }

  const filterForm = document.getElementById('filter-form');
  const debouncedUpdate = debounce(updateEvents, 300);

  // Add event listeners to all form inputs
  filterForm.querySelectorAll('input, select').forEach(input => {
    input.addEventListener('change', (e) => {
      e.preventDefault();
      debouncedUpdate(e);
    });
  });

  // Prevent form submission
  filterForm.addEventListener('submit', (e) => {
    e.preventDefault();
    updateEvents(e);
  });
</script>
