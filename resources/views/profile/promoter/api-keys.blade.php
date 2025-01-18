<header>
  <h2 class="text-md font-heading font-medium text-white">
    {{ __('Update the API Keys to link with your ticket selling platform') }}
  </h2>
</header>
<div class="p-4">
  <div class="mb-4 flex justify-between">
    <h2 class="text-xl font-bold">API Keys</h2>
    <button id="show-new-key-form-btn" class="bg-yns_pink hover:bg-yns_dark_pink rounded-lg px-4 py-2 text-white">
      Add New Key
    </button>
  </div>

  {{-- New Key Form --}}
  <div id="new-key-form" class="mb-4 hidden">
    <form id="api-key-form" action="" class="grid grid-cols-4 gap-4 border-b border-gray-700 pb-4">
      <div>
        <x-input-label-dark>Provider</x-input-label-dark>
        <select id="provider-select" class="mt-1 w-full rounded-md bg-gray-700">
          <option value="">Select Provider</option>
          @foreach ($promoterData['apiProviders'] as $category => $providers)
            <optgroup label="{{ ucfirst($category) }}">
              @foreach ($providers as $key => $provider)
                <option value="{{ $key }}">{{ $provider['name'] }}</option>
              @endforeach
            </optgroup>
          @endforeach
        </select>
      </div>
      <div>
        <x-input-label-dark>API Key</x-input-label-dark>
        <x-text-input type="text" id="api-key-input" class="mt-1 w-full" :required="true" />
      </div>
      <div>
        <x-input-label-dark>API Secret</x-input-label-dark>
        <x-text-input type="password" id="api-secret-input" class="mt-1 w-full" />
      </div>
      <div class="flex items-end">
        <x-primary-button type="submit">Save</x-primary-button>
        <x-secondary-button id="cancel-btn" class="ml-2">Cancel</x-secondary-button>
      </div>
    </form>
  </div>

  <script>
    $(document).ready(function() {
      // Show the new key form
      $('#show-new-key-form-btn').on('click', function() {
        $('#new-key-form').slideDown(); // Show the form
      });

      // Hide the new key form when cancel is clicked
      $('#cancel-btn').on('click', function(e) {
        e.preventDefault(); // Prevent default behavior
        $('#new-key-form').slideUp(); // Hide the form
      });

      // Submit the form via AJAX
      $('#api-key-form').on('submit', function(e) {
        e.preventDefault(); // Prevent the default form submission

        // Collect form data
        let formData = {
          dashboardType: "{{ $dashboardType }}",
          id: '{{ $promoterData['promoterId'] }}',
          provider: $('#provider-select').val(),
          api_key: $('#api-key-input').val(),
          api_secret: $('#api-secret-input').val(),
        };

        let dashboardType = "{{ $dashboardType }}";
        let promotionCompanyId = '{{ $promoterData['promoterId'] }}';

        console.log(dashboardType, promotionCompanyId);
        let url = `/api/profile/${dashboardType}/${promotionCompanyId}/update-api-keys`;

        // Perform the AJAX request
        $.ajax({
          url: url,
          method: 'POST',
          data: formData,
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'), // CSRF token for Laravel
          },
          success: function(response) {
            // Handle successful response
            alert(response.message); // Example success message
            $('#new-key-form').slideUp(); // Hide the form
            $('#api-key-form')[0].reset(); // Reset the form fields
          },
          error: function(xhr) {
            // Handle error response
            let errors = xhr.responseJSON.errors;
            let errorMessage = "Error saving API key:\n";
            $.each(errors, function(key, value) {
              errorMessage += `- ${value}\n`;
            });
            alert(errorMessage); // Show error messages
          },
        });
      });
    });
  </script>
