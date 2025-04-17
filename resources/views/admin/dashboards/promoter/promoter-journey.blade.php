<x-app-layout :dashboardType="$dashboardType" :modules="$modules">
  <x-slot name="header">
    <x-sub-nav :userId="$userId" />
  </x-slot>

  <div class="mx-auto w-full max-w-screen-2xl py-16">
    <div class="relative mb-8 shadow-md sm:rounded-lg">
      <div class="min-w-screen-xl mx-auto max-w-screen-xl rounded-lg bg-yns_dark_gray px-16 py-12 text-white">
        <p class="mb-3 text-3xl font-bold text-white">Oops, you're not linked to anywhere! Let's fix that!</p>
        <div class="mb-4 grid grid-cols-1 gap-x-8 gap-y-4">
          <div class="group">
            <x-input-label-dark>What is the name of your promotions company?
              <span id="result-count"></span>
            </x-input-label-dark>
            <x-text-input id="promoter-search"></x-text-input>
            <h2 class="my-4 text-xl font-semibold" id="promoter-table-title">Available Promoters</h2>
            <table class="w-full border border-white text-left font-sans rtl:text-right" id="promotersTable">
              <thead class="underline">
                <tr>
                  <th scope="col" class="md-text-2xl sm:px-2 sm:py-2 sm:text-xl md:px-6 md:py-3 lg:px-8 lg:py-4">
                    Promoter Name</th>
                  <th scope="col" class="md-text-2xl sm:px-2 sm:py-2 sm:text-xl md:px-6 md:py-3 lg:px-8 lg:py-4">
                    Action</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
            <p id="noPromoterMessage" class="mt-4 hidden">No promoters available to join at the moment.</p>
          </div>
        </div>
        <div class="mb-4 grid grid-cols-1 gap-x-8 gap-y-4">
          <div class="mb-4">
            <div class="col-span-2" id="create-promoter-form" style="display: none;">
              <p class="col-span-2 mb-3 font-bold">It looks like you're not already in the system - Let's get you added!
              </p>
              <form action="{{ route('promoter.store', ['dashboardType' => $dashboardType]) }}"
                class="grid grid-cols-2 gap-x-8 gap-y-4" id="create-promoter-form" method="POST"
                enctype="multipart/form-data">
                @csrf
                <x-google-address-picker id="location" name="location" label="Where are you based?"
                  placeholder="Search for a location..." value="" latitude="" longitude="" dataId=""
                  postalTown=""></x-google-address-picker>

                <div class="group">
                  <x-input-label-dark>Promoter Name</x-input-label-dark>
                  <x-text-input id="name" name="name" value="{{ old('name') }}"></x-text-input>
                  @error('name')
                    <p class="yns_red mt-1 text-sm">{{ $message }}</p>
                  @enderror
                </div>

                <div class="group">
                  <x-input-label-dark>Tell us a bit about you</x-input-label-dark>
                  <x-textarea-input class="w-full" id="description"
                    name="description">{{ old('description') }}</x-textarea-input>
                  @error('description')
                    <p class="yns_red mt-1 text-sm">{{ $message }}</p>
                  @enderror
                </div>

                <div class="group">
                  <x-input-label-dark for="contact_name">Contact Name</x-input-label-dark>
                  <x-text-input id="contact_name" name="contact_name" />
                </div>

                <div class="group">
                  <x-input-label-dark for="contact_number">Contact Number</x-input-label-dark>
                  <x-text-input id="contact_number" name="contact_number" />
                </div>
                <div class="group">
                  <x-input-label-dark for="contact_email">Contact Email</x-input-label-dark>
                  <x-text-input id="contact_email" name="contact_email" />
                </div>
                <div class="group">
                  <x-input-label-dark for="contact_link">Social Links</x-input-label-dark>
                  <x-text-input id="contact_link" name="contact_link" />
                </div>

                <div class="group">
                  <button type="submit"
                    class="mt-8 flex w-full justify-center rounded-lg border border-yns_cyan bg-yns_cyan px-4 py-2 font-heading text-xl text-black transition duration-150 ease-in-out hover:border-yns_yellow hover:text-yns_yellow">Save</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
</x-app-layout>
<script>
  jQuery(document).ready(function() {
    jQuery('#promoter-search').on('keyup', function() {
      let query = jQuery(this).val();
      const dashboardType = "{{ $dashboardType }}";

      $.ajax({
        url: `/${dashboardType}/promoter-search`,
        type: 'GET',
        data: {
          query: query
        },
        success: function(data) {
          if (data.html.trim() === '') {
            jQuery('#promotersTable').hide();
            jQuery('#promoter-table-title').hide();
            jQuery('#noPromoterMessage').removeClass('hidden');
            jQuery('#create-promoter-form').show();
          } else {
            jQuery('#promotersTable tbody').html(data.html);
            jQuery('#noPromoterMessage').addClass('hidden');
          }
        }
      });
    });

    // Event delegation for dynamically created buttons
    jQuery(document).on('click', '.join-promoter-btn', function() {
      const promoterId = jQuery(this).data('promoter-id'); // Retrieve the id from data-id attribute
      linkUserToPromoter(promoterId); // Call your function
    });

    function linkUserToPromoter(promoterId) {
      const dashboardType = "{{ $dashboardType }}";
      $.ajax({
        url: `/${dashboardType}/promoter-journey/join/${promoterId}`,
        type: 'POST',
        data: {
          _token: '{{ csrf_token() }}',
          serviceable_id: promoterId
        },
        success: function(response) {
          if (response.success) {
            showSuccessNotification(response.message);
            window.location.href = response.redirect;
          }
        },
        error: function(xhr) {
          let errorMessage = xhr.responseJSON.message || 'Something went wrong!';
          showFailureNotification(errorMessage);
        }
      });
    }
  });
</script>
