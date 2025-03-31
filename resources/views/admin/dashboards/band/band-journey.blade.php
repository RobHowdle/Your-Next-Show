<x-app-layout :dashboardType="$dashboardType" :modules="$modules">
  <x-slot name="header">
    <x-sub-nav :userId="$userId" />
  </x-slot>

  {{-- Main Container with Background --}}
  <div class="relative min-h-screen">
    <div class="relative mx-auto w-full max-w-screen-2xl py-8">
      <div class="px-4">
        {{-- Header Section --}}
        <div class="relative mb-8">
          {{-- Background with overlay --}}
          <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-gray-900 via-black to-gray-900 opacity-75"></div>

          {{-- Content --}}
          <div class="relative px-4 py-6 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-7xl">
              <h1 class="font-heading text-3xl font-bold text-white md:text-4xl">
                Join or Create Your Artist Profile 🎸
              </h1>
              <p class="mt-2 text-gray-400">Search for your band or create a new profile to get started.</p>
            </div>
          </div>
        </div>

        {{-- Main Form Container --}}
        <div class="rounded-xl border border-gray-800 bg-gray-900/60 backdrop-blur-md backdrop-saturate-150">
          {{-- Search Section --}}
          <div class="p-6 lg:p-8">
            <div class="mb-8">
              <div class="relative">
                <input type="text" id="band-search"
                  class="w-full rounded-lg border border-gray-800 bg-black/50 px-4 py-3 text-white placeholder-gray-500 focus:border-gray-700 focus:ring-0"
                  placeholder="Enter your band name...">
                <div id="search-loading" class="absolute right-3 top-3 hidden">
                  <div class="spinner"></div>
                </div>
              </div>
            </div>

            {{-- Results Table --}}
            <div id="search-results" class="mt-6 hidden">
              <div class="overflow-hidden rounded-xl border border-gray-800 bg-black/50">
                <div class="relative">
                  <table class="w-full" id="bandsTable">
                    <thead>
                      <tr class="border-b border-gray-800 bg-black/50">
                        <th class="whitespace-nowrap px-6 py-4 text-left">
                          <div class="font-heading text-sm font-semibold text-white">Artist Name</div>
                        </th>
                        <th class="whitespace-nowrap px-6 py-4 text-right">
                          <div class="font-heading text-sm font-semibold text-white">Action</div>
                        </th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800">
                      <!-- Results will be inserted here -->
                    </tbody>
                  </table>
                </div>
              </div>
              <p id="noBandsMessage" class="mt-4 hidden text-gray-400">No matching bands found.</p>
            </div>

            {{-- Create Band Form --}}
            <div id="create-band-form" class="mt-8 hidden">
              <form action="{{ route('band.store', ['dashboardType' => $dashboardType]) }}" method="POST"
                enctype="multipart/form-data" class="grid gap-8 lg:grid-cols-2">
                @csrf

                {{-- Left Column --}}
                <div class="space-y-6">
                  {{-- Basic Info --}}
                  <div class="rounded-lg border border-gray-800 bg-black/50 p-6">
                    <h2 class="mb-6 font-heading text-xl font-bold text-white">Basic Information</h2>
                    <div class="space-y-6">
                      <div>
                        <x-input-label-dark>Artist Name</x-input-label-dark>
                        <x-text-input id="name" name="name" value="{{ old('name') }}"
                          class="mt-1 block w-full" required>
                        </x-text-input>
                      </div>

                      <x-google-address-picker id="location" name="location" label="Where are you based?"
                        placeholder="Search for a location..." value="" latitude="" longitude=""
                        dataId="" postalTown="" class="mt-1 block w-full">
                      </x-google-address-picker>
                    </div>
                  </div>

                  {{-- Artist Details --}}
                  <div class="rounded-lg border border-gray-800 bg-black/50 p-6">
                    <h2 class="mb-6 font-heading text-xl font-bold text-white">Artist Details</h2>
                    <div class="space-y-6">
                      <div>
                        <x-input-label-dark>Band Type</x-input-label-dark>
                        <select name="band_type"
                          class="mt-1 block w-full rounded-lg border border-gray-800 bg-black/50 px-4 py-2.5 text-white"
                          required>
                          <option value="">Select Band Type</option>
                          <option value="original">Original</option>
                          <option value="covers">Covers</option>
                          <option value="tribute">Tribute</option>
                        </select>
                      </div>

                      <div>
                        <x-input-label-dark>Genres</x-input-label-dark>
                        <div class="mt-2 grid grid-cols-2 gap-4 md:grid-cols-3">
                          @foreach ($genres as $genre)
                            <label class="flex items-center space-x-2">
                              <input type="checkbox" name="genres[]"
                                value="{{ is_string($genre) ? $genre : $genre['name'] }}"
                                class="rounded border-gray-800 bg-black/50 text-yns_yellow focus:ring-yns_yellow">
                              <span class="text-gray-300">{{ is_string($genre) ? $genre : $genre['name'] }}</span>
                            </label>
                          @endforeach
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                {{-- Right Column --}}
                <div class="space-y-6">
                  {{-- Description --}}
                  <div class="rounded-lg border border-gray-800 bg-black/50 p-6">
                    <h2 class="mb-6 font-heading text-xl font-bold text-white">About Your Band</h2>
                    <div>
                      <x-textarea-input id="description" name="description" rows="4" class="mt-1 block w-full"
                        required>{{ old('description') }}</x-textarea-input>
                    </div>
                  </div>

                  {{-- Contact Information --}}
                  <div class="rounded-lg border border-gray-800 bg-black/50 p-6">
                    <h2 class="mb-6 font-heading text-xl font-bold text-white">Contact Information</h2>
                    <div class="space-y-6">
                      <div class="grid gap-6 md:grid-cols-2">
                        <div>
                          <x-input-label-dark>Contact Name</x-input-label-dark>
                          <x-text-input id="contact_name" name="contact_name" class="mt-1 block w-full" required />
                        </div>
                        <div>
                          <x-input-label-dark>Contact Number</x-input-label-dark>
                          <x-text-input id="contact_number" name="contact_number" class="mt-1 block w-full" required />
                        </div>
                      </div>
                      <div>
                        <x-input-label-dark>Contact Email</x-input-label-dark>
                        <x-text-input id="contact_email" name="contact_email" type="email" class="mt-1 block w-full"
                          required />
                      </div>
                      <div>
                        <x-input-label-dark>Social Media Links</x-input-label-dark>
                        <x-textarea-input id="contact_link" name="contact_link" rows="3" class="mt-1 block w-full"
                          placeholder="Paste your social media URLs, separated by commas"></x-textarea-input>
                        <p class="mt-1 text-sm text-gray-400">
                          Supported platforms: Facebook, Twitter, Instagram, TikTok, YouTube, Snapchat, Bluesky
                        </p>
                      </div>
                    </div>
                  </div>

                  {{-- Submit Button --}}
                  <div class="flex justify-end pt-4">
                    <button type="submit"
                      class="inline-flex items-center rounded-lg bg-gradient-to-r from-yns_yellow to-yns_dark_orange px-6 py-3 font-heading text-sm font-semibold text-black transition duration-300 hover:opacity-90">
                      <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                      </svg>
                      Create Artist Profile
                    </button>
                  </div>
                </div>
              </form>
            </div>
          </div>
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

      // Store the search query for later use
      const searchQuery = query;

      $.ajax({
        url: `/${dashboardType}/band-search`,
        type: 'GET',
        data: {
          query
        },
        success: function(data) {
          if (data.html.trim() === '') {
            // Hide any existing content
            jQuery('#search-results').addClass('hidden');
            jQuery('#create-band-form').addClass('hidden');

            // Show "No results found" message
            jQuery('#noBandsMessage')
              .removeClass('hidden')
              .hide()
              .fadeIn(300);

            // Wait 3 seconds, then show create form
            setTimeout(() => {
              jQuery('#noBandsMessage').fadeOut(300, function() {
                // Pass the original search query to showCreateForm
                showCreateForm(searchQuery);
              });
            }, 3000);
          } else {
            showResults(data.html);
            jQuery('#noBandsMessage').addClass('hidden');
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

    function showCreateForm(searchQuery) {
      // Hide results and show create form with animation
      jQuery('#search-results').addClass('hidden');
      jQuery('#create-band-form')
        .removeClass('hidden')
        .hide()
        .fadeIn(300);

      // If we have a search query, populate the name field
      if (searchQuery) {
        jQuery('#name').val(searchQuery);
      }

      // Clear search box but don't trigger another search
      jQuery('#band-search').val('');
    }

    function showResults(html) {
      // Hide create form and show results
      jQuery('#create-band-form').addClass('hidden');
      jQuery('#bandsTable tbody').html(html);
      jQuery('#search-results')
        .removeClass('hidden')
        .hide()
        .fadeIn(300);
    }

    function clearResults() {
      jQuery('#search-results').addClass('hidden');
      jQuery('#create-band-form').addClass('hidden');
      jQuery('#noBandsMessage').addClass('hidden');
    }

    // Handle join band button clicks
    jQuery(document).on('click', '.join-band-button', function() {
      const serviceId = jQuery(this).data('service-id');
      const dashboardType = "{{ $dashboardType }}";
      const button = jQuery(this);

      // Disable button and show loading state
      button.prop('disabled', true)
        .html('<i class="fas fa-spinner fa-spin mr-2"></i> Joining...');

      $.ajax({
        url: `/${dashboardType}/band-journey/join/${serviceId}`,
        type: 'POST',
        data: {
          serviceable_id: serviceId,
          _token: '{{ csrf_token() }}'
        },
        success: function(response) {
          if (response.success) {
            showSuccessNotification(response.message);
            // Redirect to dashboard after successful join
            window.location.href = response.redirect;
          }
        },
        error: function(xhr) {
          const message = xhr.responseJSON?.message || 'Failed to join band';
          showError(message);
          // Reset button state
          button.prop('disabled', false)
            .html(
              '<svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>Join Band'
            );
        }
      });
    });

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
