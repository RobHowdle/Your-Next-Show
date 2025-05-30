<x-app-layout :dashboardType="$dashboardType" :modules="$modules">
  <x-slot name="header">
    <x-sub-nav :userId="$userId" />
  </x-slot>

  <div class="relative min-h-screen">
    <div class="relative mx-auto w-full max-w-screen-2xl py-8">
      <div class="px-4">
        {{-- Header Section --}}
        <div class="relative mb-8">
          <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-gray-900 via-black to-gray-900 opacity-75"></div>
          <div class="relative px-4 py-6 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-7xl">
              <div class="flex items-center justify-between">
                <div>
                  <h1 class="font-heading text-2xl font-bold text-white md:text-3xl">Add Team Member</h1>
                  <p class="mt-2 text-gray-400">Search and invite users to join {{ $service->name }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        {{-- Search Section --}}
        <div class="rounded-xl border border-gray-800 bg-gray-900/60 p-6 backdrop-blur-sm">
          <div class="mb-6">
            <label for="user-search" class="mb-2 block text-sm font-medium text-gray-400">
              Search by email address
            </label>
            <div class="relative">
              <input type="email" id="user-search"
                class="block w-full rounded-lg border border-gray-800 bg-black/50 p-4 pl-12 text-white placeholder-gray-500 focus:border-gray-700 focus:ring-0"
                placeholder="Enter exact email address...">
              <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                <i class="fas fa-search text-gray-500"></i>
              </div>
            </div>
          </div>

          {{-- Results Section --}}
          <div id="search-results" class="space-y-4">
            {{-- Results will be dynamically inserted here --}}
          </div>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>

<script>
  jQuery(document).ready(function() {
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    let debounceTimer;
    const minLength = 5; // Minimum characters before search

    jQuery('#user-search').on('input', function() {
      const query = jQuery(this).val();
      const dashboardType = "{{ $dashboardType }}";
      const resultsContainer = jQuery('#search-results');

      if (debounceTimer) {
        clearTimeout(debounceTimer);
      }

      // Clear results if query is too short
      if (query.length < minLength || !query.includes('@')) {
        resultsContainer.html(`
                <div class="rounded-lg border border-gray-800 bg-gray-900/50 p-4">
                    <p class="text-center text-sm text-gray-500">
                        ${query.length > 0 ? 'Please enter a valid email address' : 'Enter an email address to search'}
                    </p>
                </div>
            `);
        return;
      }

      debounceTimer = setTimeout(function() {
        resultsContainer.html(`
                <div class="flex items-center justify-center rounded-lg border border-gray-800 bg-gray-900/50 p-4">
                    <i class="fas fa-circle-notch fa-spin mr-2 text-gray-500"></i>
                    <span class="text-gray-500">Searching...</span>
                </div>
            `);

        $.ajax({
          url: "{{ route('admin.dashboard.search-users', ['dashboardType' => $dashboardType]) }}",
          method: "GET",
          data: {
            query: query
          },
          success: function(data) {
            resultsContainer.empty();

            if (Array.isArray(data.result) && data.result.length > 0) {
              data.result.forEach(function(user) {
                resultsContainer.append(`
                    <div class="flex items-center justify-between rounded-lg border border-gray-800 bg-gray-900/50 p-4">
                        <div class="flex items-center space-x-4">
                            <div class="h-10 w-10 flex-shrink-0 overflow-hidden rounded-full bg-gray-800">
                                <div class="flex h-full w-full items-center justify-center bg-gray-800 text-lg font-bold text-white">
                                    ${user.name[0]}
                                </div>
                            </div>
                            <div>
                                <h3 class="font-medium text-white">${user.name}</h3>
                                <p class="text-sm text-gray-400">${user.email}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <select class="role-select rounded-lg border border-gray-800 bg-black/50 px-3 py-2 text-sm text-white">
                                <option value="service-member">Team Member</option>
                                <option value="service-manager">Manager</option>
                            </select>
                            <button class="addUserBtn inline-flex items-center rounded-lg bg-yns_yellow px-4 py-2 text-sm font-medium text-gray-900 transition duration-200 hover:bg-yellow-400" data-user-id="${user.id}">
                                Add to Team
                            </button>
                        </div>
                    </div>
                `);
              });
            } else {
              resultsContainer.html(`
                            <div class="rounded-lg border border-gray-800 bg-gray-900/50 p-4">
                                <p class="text-center text-sm text-gray-500">No user found with this email address</p>
                            </div>
                        `);
            }
          },
          error: function(xhr) {
            resultsContainer.html(`
                        <div class="rounded-lg border border-red-900/50 bg-red-950/50 p-4">
                            <p class="text-center text-sm text-red-500">Error searching for users</p>
                        </div>
                    `);
          }
        });
      }, 300);
    });

    jQuery('#search-results').on('click', '.addUserBtn', function() {
      const userId = jQuery(this).data('user-id');
      const role = jQuery(this).closest('.flex').find('.role-select').val();
      const dashboardType = "{{ $dashboardType }}";
      const currentServiceId = "{{ $currentServiceId }}";

      $.ajax({
        url: "{{ route('admin.dashboard.link-user', ['dashboardType' => ':dashboardType', 'id' => ':id']) }}"
          .replace(':dashboardType', dashboardType)
          .replace(':id', userId),
        type: 'POST',
        data: {
          user_id: userId,
          currentServiceId: currentServiceId,
          role: role
        },
        success: function(response) {
          showSuccessNotification(response.message);
          window.location.href =
            "{{ route('admin.dashboard.users', ['dashboardType' => $dashboardType]) }}";
        },
        error: function(xhr) {
          showFailureNotification('Error adding user: ' + xhr.responseJSON.message);
        }
      });
    });
  });
</script>
