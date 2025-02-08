<header>
  <h2 class="text-md font-heading font-medium text-white">
    {{ __('Integrate Your Ticket Selling Platforms') }}
  </h2>
</header>

<div class="p-4">
  <div class="mb-4 flex justify-between">
    <h2 class="text-xl font-bold text-white">API Integrations</h2>
    <button id="show-new-integration-btn" class="bg-yns_pink hover:bg-yns_dark_pink rounded-lg px-4 py-2 text-white">
      Add New Integration
    </button>
  </div>

  {{-- New Integration Form --}}
  <div id="new-integration-form" class="mb-6 hidden rounded-lg bg-gray-800 p-6">
    <form id="integration-form" class="space-y-6">
      <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
        {{-- Integration Provider --}}
        <div>
          <x-input-label-dark for="provider">Integration Provider</x-input-label-dark>
          <select id="provider" name="provider"
            class="focus:border-yns_pink mt-1 w-full rounded-md border-gray-600 bg-gray-700 text-white">
            <option value="">Select Provider</option>
            <option value="eventbrite">Eventbrite</option>
            <option value="ticketmaster">Ticketmaster</option>
            <option value="fatsoma">Fatsoma</option>
          </select>
          <p class="mt-1 text-sm text-gray-400" id="provider-description"></p>
        </div>

        {{-- Integration Type --}}
        <div>
          <x-input-label-dark for="key_type">Integration Type</x-input-label-dark>
          <select id="key_type" name="key_type"
            class="focus:border-yns_pink mt-1 w-full rounded-md border-gray-600 bg-gray-700 text-white">
            <option value="">Select Type</option>
            <option value="api_key">API Key</option>
            <option value="webhook">Webhook</option>
          </select>
        </div>

        {{-- API Key --}}
        <div>
          <x-input-label-dark for="api_key">API Key</x-input-label-dark>
          <x-text-input type="text" id="api_key" name="api_key" class="mt-1 w-full" required />
        </div>

        {{-- API Secret --}}
        <div>
          <x-input-label-dark for="api_secret">API Secret</x-input-label-dark>
          <x-text-input type="password" id="api_secret" name="api_secret" class="mt-1 w-full" required />
        </div>
      </div>

      <div class="flex items-center gap-4">
        <x-primary-button type="submit">Save Integration</x-primary-button>
        <x-secondary-button type="button" id="cancel-integration">Cancel</x-secondary-button>
      </div>
    </form>
  </div>

  {{-- Existing Integrations --}}
  <div id="existing-integrations" class="space-y-4">
    @foreach ($profileData['apiKeys'] ?? [] as $apiKey)
      <div class="rounded-lg bg-gray-800 p-4">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="text-lg font-semibold text-white">{{ ucfirst($apiKey['name']) }}</h3>
            <p class="text-sm text-gray-400">Last used: {{ $apiKey['last_used_at']?->diffForHumans() ?? 'Never' }}</p>
          </div>
          <div class="flex items-center gap-2">
            <button class="toggle-status" data-id="{{ $apiKey['id'] }}" data-active="{{ $apiKey['is_active'] }}">
              <span
                class="status-badge {{ $apiKey['is_active'] ? 'bg-green-500' : 'bg-red-500' }} rounded-full px-2 py-1 text-xs text-white">
                {{ $apiKey['is_active'] ? 'Active' : 'Inactive' }}
              </span>
            </button>
            <button class="delete-integration text-red-500 hover:text-red-400" data-id="{{ $apiKey['id'] }}">
              <i class="fas fa-trash"></i>
            </button>
          </div>
        </div>
      </div>
    @endforeach
  </div>
</div>

<script>
  $(document).ready(function() {
    const providerInfo = {
      eventbrite: {
        description: 'Connect your Eventbrite account to sync ticket sales and event data.',
        documentation: 'https://www.eventbrite.com/platform/api'
      },
      ticketmaster: {
        description: 'Integrate with Ticketmaster to manage your event listings and sales.',
        documentation: 'https://developer.ticketmaster.com/products-and-docs/apis/getting-started/'
      },
      fatsoma: {
        description: 'Connect your Fatsoma account to track ticket sales and promotions.',
        documentation: 'https://fatsoma.com/for-promoters'
      }
    };

    // Show/Hide Integration Form
    $('#show-new-integration-btn').on('click', function() {
      $('#new-integration-form').slideDown();
    });

    $('#cancel-integration').on('click', function() {
      $('#integration-form')[0].reset();
      $('#new-integration-form').slideUp();
    });

    // Update provider description
    $('#provider').on('change', function() {
      const provider = $(this).val();
      const description = providerInfo[provider]?.description || '';
      $('#provider-description').text(description);
    });

    // Form Submission
    $('#integration-form').on('submit', function(e) {
      e.preventDefault();
      console.log('Form submitted');

      const serviceableInfo = getServiceableId();
      console.log('Serviceable Info:', serviceableInfo);

      if (!serviceableInfo) {
        alert('Error: Could not determine service type');
        return;
      }

      const formData = {
        serviceableType: serviceableInfo.type,
        serviceableId: serviceableInfo.id,
        provider: $('#provider').val(),
        key_type: $('#key_type').val(),
        api_key: $('#api_key').val(),
        api_secret: $('#api_secret').val(),
        _token: $('meta[name="csrf-token"]').attr('content')
      };

      console.log('Form Data:', formData);

      $.ajax({
        url: '{{ route('integrations.store') }}',
        method: 'POST',
        data: formData,
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
          'Accept': 'application/json'
        },
        success: function(response) {
          console.log('Success:', response);
          alert('Integration saved successfully');
          location.reload();
        },
        error: function(xhr, status, error) {
          console.error('Error:', {
            xhr,
            status,
            error
          });
          let errorMessage = "Error saving integration:\n";

          if (xhr.responseJSON && xhr.responseJSON.errors) {
            Object.values(xhr.responseJSON.errors).forEach(error => {
              errorMessage += `- ${error}\n`;
            });
          } else {
            errorMessage += error;
          }

          alert(errorMessage);
        }
      });
    });

    // Toggle Integration Status
    $('.toggle-status').on('click', function() {
      const id = $(this).data('id');
      const currentlyActive = $(this).data('active');

      $.ajax({
        url: `/integrations/${id}/toggle`,
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function() {
          location.reload();
        }
      });
    });

    // Delete Integration
    $('.delete-integration').on('click', function() {
      if (!confirm('Are you sure you want to delete this integration?')) return;

      const id = $(this).data('id');

      $.ajax({
        url: `/integrations/${id}`,
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function() {
          location.reload();
        }
      });
    });

    function getServiceableId() {
      const possibleIds = {
        'promoter': '{{ $profileData['promoterId'] ?? '' }}',
        'venue': '{{ $profileData['venueId'] ?? '' }}',
        'service': '{{ $profileData['serviceId'] ?? '' }}'
      };

      for (const [type, id] of Object.entries(possibleIds)) {
        if (id) {
          return {
            type,
            id
          };
        }
      }

      return null;
    }
  });
</script>
