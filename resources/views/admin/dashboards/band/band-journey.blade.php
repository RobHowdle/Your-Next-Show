<x-app-layout :dashboardType="$dashboardType" :modules="$modules">
  <x-slot name="header">
    <x-sub-nav :userId="$userId" />
  </x-slot>

  <div class="min-w-screen-xl mx-auto mt-28 max-w-screen-xl rounded-lg bg-yns_dark_gray px-8 py-12 text-white">
    <div class="mx-auto max-w-4xl">
      <h1 class="mb-8 text-4xl font-bold text-white">Let's Get You Started! 🎸</h1>

      <!-- Search Section -->
      <div class="mb-12">
        <h2 class="mb-4 text-2xl font-semibold">Search for your band</h2>
        <div class="relative">
          <input type="text" id="band-search"
            class="w-full rounded-lg border border-gray-700 bg-gray-800 px-4 py-3 text-white placeholder-gray-400 focus:border-transparent focus:ring-2 focus:ring-yns_yellow"
            placeholder="Enter your band name...">
          <div id="search-loading" class="absolute right-3 top-3 hidden">
            <!-- Add loading spinner here -->
          </div>
        </div>
      </div>

      <!-- Results Table -->
      <div id="search-results" class="mb-12">
        <h2 class="mb-4 text-xl font-semibold" id="band-table-title">Available Artists</h2>
        <div class="overflow-hidden rounded-lg border border-gray-700">
          <table class="w-full" id="bandsTable">
            <thead class="bg-gray-800">
              <tr>
                <th class="px-6 py-4 text-left text-sm font-semibold">Artist Name</th>
                <th class="px-6 py-4 text-right">Action</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-700 bg-gray-900">
              <!-- Results will be inserted here -->
            </tbody>
          </table>
        </div>
        <p id="noBandsMessage" class="mt-4 hidden text-gray-400">No matching bands found.</p>
      </div>

      <!-- Create Band Form -->
      <div id="create-band-form" class="hidden">
        <h2 class="mb-6 text-2xl font-semibold">Create Your Band Profile</h2>
        <form action="{{ route('band.store', ['dashboardType' => $dashboardType]) }}" method="POST"
          enctype="multipart/form-data" class="space-y-6">
          @csrf

          <!-- Basic Info -->
          <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div>
              <x-input-label-dark>Artist Name</x-input-label-dark>
              <x-text-input id="name" name="name" value="{{ old('name') }}" required></x-text-input>
              @error('name')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
              @enderror
            </div>

            <x-google-address-picker id="location" name="location" label="Where are you based?"
              placeholder="Search for a location..." value="" latitude="" longitude="" dataId=""
              postalTown="">
            </x-google-address-picker>
          </div>

          <!-- Band Type -->
          <div class="space-y-2">
            <x-input-label-dark>Band Type</x-input-label-dark>
            <select name="band_type" class="w-full rounded-lg border border-gray-700 bg-gray-800 px-4 py-3 text-white"
              required>
              <option value="">Select Band Type</option>
              <option value="original">Original</option>
              <option value="covers">Covers</option>
              <option value="tribute">Tribute</option>
            </select>
          </div>

          <!-- Genres -->
          <div class="space-y-2">
            <x-input-label-dark>Genres</x-input-label-dark>
            <div class="grid grid-cols-2 gap-4 md:grid-cols-3">
              @foreach ($genres as $genre)
                <label class="flex items-center space-x-2">
                  <input type="checkbox" name="genres[]" value="{{ is_array($genre) ? $genre['name'] : $genre }}"
                    class="rounded border-gray-700 bg-gray-800 text-yns_yellow focus:ring-yns_yellow">
                  <span>{{ is_array($genre) ? $genre['name'] : $genre }}</span>
                </label>
              @endforeach
            </div>
          </div>

          <!-- Description -->
          <div>
            <x-input-label-dark>Tell us about your band</x-input-label-dark>
            <x-textarea-input class="w-full" id="description" name="description"
              rows="4">{{ old('description') }}</x-textarea-input>
          </div>

          <!-- Contact Info -->
          <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div>
              <x-input-label-dark>Contact Name</x-input-label-dark>
              <x-text-input id="contact_name" name="contact_name" required />
            </div>
            <div>
              <x-input-label-dark>Contact Number</x-input-label-dark>
              <x-text-input id="contact_number" name="contact_number" required />
            </div>
            <div>
              <x-input-label-dark>Contact Email</x-input-label-dark>
              <x-text-input id="contact_email" name="contact_email" type="email" required />
            </div>
            <div>
              <x-input-label-dark>Social Media Links</x-input-label-dark>
              <x-textarea-input class="w-full" id="contact_link" name="contact_link" rows="3"
                placeholder="Paste your social media URLs, separated by commas (e.g., https://facebook.com/your-page, https://twitter.com/your-handle)"></x-textarea-input>
              <p class="mt-1 text-sm text-gray-400">
                Supported platforms: Facebook, Twitter, Instagram, TikTok, YouTube, Snapchat, Bluesky
              </p>
            </div>
          </div>

          <div class="pt-4">
            <button type="submit"
              class="w-full rounded-lg bg-gradient-to-r from-yns_yellow to-yns_dark_orange px-6 py-3 font-heading text-lg text-black transition duration-300 hover:opacity-90">
              Create Band Profile
            </button>
          </div>
        </form>
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

  /* Loading spinner */
  .spinner {
    border: 2px solid #f3f3f3;
    border-top: 2px solid #FFB800;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    animation: spin 1s linear infinite;
  }

  @keyframes spin {
    0% {
      transform: rotate(0deg);
    }

    100% {
      transform: rotate(360deg);
    }
  }
</style>
<script>
  jQuery(document).ready(function() {
    const searchDebounce = debounce(performSearch, 300);

    jQuery('#band-search').on('keyup', function() {
      const query = jQuery(this).val();
      if (query.length >= 2) {
        searchDebounce(query);
      } else {
        clearResults();
      }
    });

    function performSearch(query) {
      const dashboardType = "{{ $dashboardType }}";
      jQuery('#search-loading').removeClass('hidden');

      $.ajax({
        url: `/${dashboardType}/band-search`,
        type: 'GET',
        data: {
          query
        },
        success: function(data) {
          if (data.html.trim() === '') {
            showCreateForm();
          } else {
            showResults(data.html);
          }
        },
        error: function() {
          showError('Failed to search for bands');
        },
        complete: function() {
          jQuery('#search-loading').addClass('hidden');
        }
      });
    }

    function showCreateForm() {
      jQuery('#search-results').addClass('hidden');
      jQuery('#create-band-form').removeClass('hidden')
        .hide().fadeIn(300);
    }

    function showResults(html) {
      jQuery('#create-band-form').addClass('hidden');
      jQuery('#bandsTable tbody').html(html);
      jQuery('#search-results').removeClass('hidden')
        .hide().fadeIn(300);
    }

    function clearResults() {
      jQuery('#search-results').addClass('hidden');
      jQuery('#create-band-form').addClass('hidden');
    }

    // Form submission
    jQuery('#create-band-form form').on('submit', function(e) {
      e.preventDefault();
      const form = jQuery(this);
      const formData = new FormData(this);

      // Clear previous errors
      form.find('.text-red-500').remove();
      form.find('.border-red-500').removeClass('border-red-500');

      // Collect and format genres
      const genres = [];
      form.find('input[name="genres[]"]:checked').each(function() {
        genres.push(jQuery(this).val());
      });

      // Update FormData with stringified arrays
      formData.set('genres', JSON.stringify(genres));

      // Get band type (will be appended with "-bands" in the controller)
      const bandType = form.find('select[name="band_type"]').val();
      formData.set('band_type', bandType);

      // Format social media links
      const contactLink = form.find('textarea[name="contact_link"]').val();
      formData.set('contact_link', contactLink.trim());

      $.ajax({
        url: form.attr('action'),
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
          if (response.success) {
            showSuccessNotification(response.message);
            window.location.href = response.redirect;
          }
        },
        error: function(xhr) {
          const errors = xhr.responseJSON?.errors || {};
          Object.keys(errors).forEach(field => {
            const input = form.find(`[name="${field}"]`);
            if (input.length) {
              input.addClass('border-red-500');
              input.after(`<p class="text-red-500 text-sm mt-1">${errors[field][0]}</p>`);
            }
          });
        }
      });
    });

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
  });
</script>
