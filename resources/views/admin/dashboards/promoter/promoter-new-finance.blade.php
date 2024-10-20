<x-app-layout>
  <x-slot name="header">
    <x-sub-nav :promoterId="$promoter->id" />
  </x-slot>

  <div class="mx-auto w-full max-w-screen-2xl py-16">
    <div class="relative mb-8 shadow-md sm:rounded-lg">
      <div class="grid grid-cols-[1.75fr_1.25fr] rounded-lg border border-white">
        <div class="rounded-l-lg border-r border-r-white bg-yns_dark_gray px-8 py-8">
          <p class="mb-10 text-4xl font-bold text-white">New Budget</p>
          <form id="finances-form" method="POST">
            @csrf
            <div class="mb-4 grid grid-cols-2 gap-x-8 gap-y-4">
              <div class="group">
                <x-input-label-dark>Desired Profit</x-input-label-dark>
                <x-number-input-pound id="desired_profit" name="desired_profit"></x-number-input-pound>
                @error('desired_profit')
                  <p class="yns_red mt-1 text-sm">{{ $message }}</p>
                @enderror
              </div>
            </div>
            <div class="grid grid-cols-2 gap-x-8 gap-y-4">
              <div class="group">
                <x-input-label-dark>Budget Name</x-input-label-dark>
                <x-text-input id="budget_name" name="budget_name"></x-text-input>
                @error('budget_name')
                  <p class="yns_red mt-1 text-sm">{{ $message }}</p>
                @enderror
              </div>

              <div class="group">
                <x-input-label-dark>Date From</x-input-label-dark>
                <x-date-input id="date_from" name="date_from"></x-date-input>
                @error('date_from')
                  <p class="yns_red mt-1 text-sm">{{ $message }}</p>
                @enderror
              </div>

              <div class="group">
                <x-input-label-dark>Date To</x-input-label-dark>
                <x-date-input id="date_to" name="date_to"></x-date-input>
                @error('date_to')
                  <p class="yns_red mt-1 text-sm">{{ $message }}</p>
                @enderror
              </div>

              <div class="group">
                <x-input-label-dark>Link To Event</x-input-label-dark>
                <x-text-input id="link_to_event" name="link_to_event"></x-text-input>
                @error('link_to_event')
                  <p class="yns_red mt-1 text-sm">{{ $message }}</p>
                @enderror
              </div>
            </div>

            <p class="my-4 text-xl font-bold">Incoming</p>
            <div class="grid grid-cols-2 gap-x-8 gap-y-4">
              <div class="income group">
                <x-input-label-dark>Presale Tickets</x-input-label-dark>
                <x-number-input-pound id="income_presale" name="income_presale"></x-number-input-pound>
                @error('income_presale')
                  <p class="yns_red mt-1 text-sm">{{ $message }}</p>
                @enderror
              </div>

              <div class="income group">
                <x-input-label-dark>On The Door Tickets</x-input-label-dark>
                <x-number-input-pound id="income_otd" name="income_otd"></x-number-input-pound>
                @error('income_otd')
                  <p class="yns_red mt-1 text-sm">{{ $message }}</p>
                @enderror
              </div>
            </div>

            <button id="add-income-row"
              class="mt-8 rounded-lg border border-white bg-white px-4 py-2 font-heading text-black transition duration-150 ease-in-out hover:border-yns_yellow hover:text-yns_yellow">Add
              Row <span class="fas fa-plus"></span></button>

            <p class="my-4 text-xl font-bold">Outgoings</p>
            <div class="grid grid-cols-2 gap-x-8 gap-y-4">
              <div class="outgoing group">
                <x-input-label-dark>Venue</x-input-label-dark>
                <x-number-input-pound id="outgoing_venue" name="outgoing_venue"></x-number-input-pound>
                @error('outgoing_venue')
                  <p class="yns_red mt-1 text-sm">{{ $message }}</p>
                @enderror
              </div>

              <div class="outgoing group">
                <x-input-label-dark>Band (s)</x-input-label-dark>
                <x-number-input-pound id="outgoing_band" name="outgoing_band"></x-number-input-pound>
                @error('outgoing_band')
                  <p class="yns_red mt-1 text-sm">{{ $message }}</p>
                @enderror
              </div>

              <div class="outgoing group">
                <x-input-label-dark>Promotion</x-input-label-dark>
                <x-number-input-pound id="outgoing_promotion" name="outgoing_promotion"></x-number-input-pound>
                @error('outgoing_promotion')
                  <p class="yns_red mt-1 text-sm">{{ $message }}</p>
                @enderror
              </div>

              <div class="outgoing group">
                <x-input-label-dark>Rider</x-input-label-dark>
                <x-number-input-pound id="outgoing_rider" name="outgoing_rider"></x-number-input-pound>
                @error('outgoing_rider')
                  <p class="yns_red mt-1 text-sm">{{ $message }}</p>
                @enderror
              </div>
            </div>

            <button id="add-outgoing-row"
              class="mt-8 rounded-lg border border-white bg-white px-4 py-2 font-heading text-black transition duration-150 ease-in-out hover:border-yns_yellow hover:text-yns_yellow">Add
              Row
              <span class="fas fa-plus"></span></button>
            <button type="submit"
              class="mt-8 flex w-full justify-center rounded-lg border border-yns_cyan bg-yns_cyan px-4 py-2 font-heading text-xl text-black transition duration-150 ease-in-out hover:border-yns_yellow hover:text-yns_yellow">Save</button>
          </form>
        </div>
        <div class="bg-yns_dark_blue px-8 py-8">
          <p class="mb-6 text-4xl font-bold text-white">Preview</p>
          <div class="border-b-2 border-b-yns_light_gray">
            <p class="py-4 font-heading text-xl font-bold">Incoming</p>
            <p>Presale Tickets: <span id="preview_income_presale"></span></p>
            <p>On The Door Tickets: <span id="preview_income_otd"></span></p>
            <p>Other: <span id="preview_income_other"></span></p>

            <p class="mt-4 py-4 font-heading text-xl font-bold">Outgoings</p>
            <p>Venue: <span id="preview_outgoing_venue"></span></p>
            <p>Band(s): <span id="preview_outgoing_band"></span></p>
            <p>Promotion: <span id="preview_outgoing_promotion"></span></p>
            <p>Rider: <span id="preview_outgoing_rider"></span></p>
            <p class="mb-8">Other: <span id="preview_outgoing_other"></span></p>
          </div>
          <p class="mt-8">Total Incoming: <span id="income_total" name="income_total"></span></p>
          <p>Total Outgoings: <span id="outgoing_total" name="outgoing_total"></span></p>
          <p class="mt-4 text-lg font-bold">Total Profit: <span id="profit_total" name="profit_total"></span></p>
          <p class="mt-4 text-lg"><span id="desired_profit_remaining" name="desired_profit_remaining"></span></p>

          <button
            class="mt-8 rounded-lg border border-white bg-white px-4 py-2 font-heading text-black transition duration-150 ease-in-out hover:border-yns_yellow hover:text-yns_yellow">Export
            To PDF <span class="fas fa-file-export"></span></button>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>


<script>
  $(document).ready(function() {
    let desiredProfit, incomePresale, incomeOtd, incomeOther = 0,
      outgoingVenue, outgoingBand, outgoingPromotion,
      outgoingRider, outgoingOther = 0,
      incomeTotal = 0,
      outgoingTotal = 0,
      profitTotal = 0;

    function calculateTotals() {
      desiredProfit = parseFloat($('#desired_profit').val()) || 0;
      incomePresale = parseFloat($('#income_presale').val()) || 0;
      incomeOtd = parseFloat($('#income_otd').val()) || 0;
      incomeOther = Array.from($('.income_other')).reduce((sum, input) => sum + (parseFloat($(input).val()) || 0),
        0);
      outgoingVenue = parseFloat($('#outgoing_venue').val()) || 0;
      outgoingBand = parseFloat($('#outgoing_band').val()) || 0;
      outgoingPromotion = parseFloat($('#outgoing_promotion').val()) || 0;
      outgoingRider = parseFloat($('#outgoing_rider').val()) || 0;
      outgoingOther = Array.from($('.outgoing_other')).reduce((sum, input) => sum + (parseFloat($(input).val()) ||
        0), 0);

      // Calculate totals
      incomeTotal = incomePresale + incomeOtd + incomeOther;
      outgoingTotal = outgoingVenue + outgoingBand + outgoingPromotion + outgoingRider + outgoingOther;
      profitTotal = incomeTotal - outgoingTotal;

      let remainingDesiredProfit = 0;
      let numericValue = '';

      // Check if the profits are valid numbers
      if (isNaN(profitTotal) || isNaN(desiredProfit)) {
        remainingDesiredProfit = 'Total profit or desired profit is not a number.';
      } else {
        if (profitTotal === desiredProfit) {
          remainingDesiredProfit = 'You have made your desired profit! Well Done!';
        } else if (profitTotal > desiredProfit) {
          numericValue = profitTotal - desiredProfit; // Isolate the numeric difference
          remainingDesiredProfit = 'You have exceeded your desired profit by ' + formatCurrency(numericValue) + '!';
        } else {
          numericValue = desiredProfit - profitTotal; // Isolate the numeric difference
          remainingDesiredProfit = 'You need ' + formatCurrency(numericValue) + ' to achieve your desired profit.';
        }
      }

      // Update displayed values
      $('#preview_income_presale').text(formatCurrency(incomePresale));
      $('#preview_income_otd').text(formatCurrency(incomeOtd));
      $('#preview_income_other').text(formatCurrency(incomeOther));
      $('#preview_outgoing_venue').text(formatCurrency(outgoingVenue));
      $('#preview_outgoing_band').text(formatCurrency(outgoingBand));
      $('#preview_outgoing_promotion').text(formatCurrency(outgoingPromotion));
      $('#preview_outgoing_rider').text(formatCurrency(outgoingRider));
      $('#preview_outgoing_other').text(formatCurrency(outgoingOther));
      $('#income_total').text(formatCurrency(incomeTotal));
      $('#outgoing_total').text(formatCurrency(outgoingTotal));
      $('#profit_total').text(formatCurrency(profitTotal));
      $('#desired_profit_remaining').text(remainingDesiredProfit);

      // Prepare for form submission
      $('#finances-form').data('numericValue', numericValue);
    }

    // Recalculate on input changes
    $('#finances-form').on('input', 'input', function() {
      calculateTotals();
    });

    // Add income row functionality
    document.getElementById('add-income-row').addEventListener('click', function(event) {
      event.preventDefault();
      const newRow = document.createElement('div');
      newRow.classList.add('grid', 'grid-cols-2', 'gap-x-8', 'gap-y-4', 'items-end');
      newRow.innerHTML = `
        <div class="income group mt-4">
            <x-input-label-dark>Other Income</x-input-label-dark>
            <x-number-input-pound class="income_other" name="income_other"></x-number-input-pound>
        </div>
        <button class="remove-other-income-row rounded-lg h-10 bg-yns_dark_orange px-4 py-2 font-heading text-black border border-yns_dark_orange hover:text-white hover:border-white transition duration-150 ease-in-out">Remove <span class="fas fa-minus"></span></button>
      `;
      this.parentNode.insertBefore(newRow, this);
      newRow.querySelector('.remove-other-income-row').addEventListener('click', function() {
        newRow.remove();
        calculateTotals();
      });
    });

    // Add outgoing row functionality
    document.getElementById('add-outgoing-row').addEventListener('click', function(event) {
      event.preventDefault();
      const newRow = document.createElement('div');
      newRow.classList.add('grid', 'grid-cols-2', 'gap-x-8', 'gap-y-4', 'items-end');
      newRow.innerHTML = `
          <div class="outgoing group mt-4">
              <x-input-label-dark>Other Outgoing</x-input-label-dark>
              <x-number-input-pound class="outgoing_other" name="outgoing_other"></x-number-input-pound>
          </div>
          <button class="remove-other-outgoing-row rounded-lg h-10 bg-yns_dark_orange px-4 py-2 font-heading text-black border border-yns_dark_orange hover:text-white hover:border-white transition duration-150 ease-in-out">Remove <span class="fas fa-minus"></span></button>
      `;
      this.parentNode.insertBefore(newRow, this);
      newRow.querySelector('.remove-other-outgoing-row').addEventListener('click', function() {
        newRow.remove();
        calculateTotals();
      });
    });

    // Handle form submission
    $('#finances-form').on('submit', function(event) {
      event.preventDefault();
      const formData = new FormData(this);

      // Clear previous values for income_other
      formData.delete('income_other[]');
      // Add individual income_other values
      $('.income_other').each(function() {
        const value = parseFloat($(this).val()) || 0; // Get the value, default to 0 if NaN
        formData.append('income_other[]', value); // Append as an array
      });

      // Clear previous values for outgoing_other
      formData.delete('outgoing_other[]');
      // Add individual outgoing_other values
      $('.outgoing_other').each(function() {
        const value = parseFloat($(this).val()) || 0; // Get the value, default to 0 if NaN
        formData.append('outgoing_other[]', value); // Append as an array
      });

      // Append isolated numeric values
      const numericValue = $('#finances-form').data('numericValue'); // Retrieve the stored numeric value
      formData.append('desired_profit_remaining', numericValue);

      // Other values
      formData.append('income_total', $('#income_total').text().replace(/[^0-9.-]+/g, ""));
      formData.append('outgoing_total', $('#outgoing_total').text().replace(/[^0-9.-]+/g, ""));
      formData.append('profit_total', $('#profit_total').text().replace(/[^0-9.-]+/g, ""));

      // AJAX request
      $.ajax({
        url: '{{ route('promoter.dashboard.finances.saveNew') }}',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
          if (response.success === true) {
            showSuccessNotification(response.message);
          } else {
            if (Array.isArray(response.message)) {
              response.message.forEach(function(error) {
                showFailureNotification(error);
              });
            } else {
              showFailureNotification(response.message ||
                'Something went wrong, please try again later!');
            }
          }
        },
        error: function(jqXHR, textStatus, errorThrown) {
          console.error('Error:', textStatus, errorThrown);
          const response = jqXHR.responseJSON;
          showFailureNotification(response.message || 'An error occurred, please try again later.');
        }
      });
    });
  });
</script>
