<x-app-layout>
  <x-slot name="header">
    <x-sub-nav :promoterId="$promoter->id" />
  </x-slot>

  <div class="mx-auto w-full max-w-screen-2xl py-16">
    <div class="relative mb-8 shadow-md sm:rounded-lg">
      <div class="min-w-screen-xl mx-auto max-w-screen-xl rounded-lg bg-yns_dark_gray text-white">
        <div class="rounded-l-lg bg-yns_dark_blue px-8 py-8">
          <div class="mb-8 flex flex-col justify-between text-white">
            <form id="finances-form" action="{{ route('promoter.dashboard.finances.update', $finance->id) }}"
              method="POST">
              @csrf
              @method('PATCH')

              <div class="mb-8 flex flex-row items-center justify-between">
                <p class="font-heading text-3xl font-bold">Edit Finance Record: #{{ $finance->id }}</p>
              </div>

              <div class="mb-4 grid grid-cols-2 gap-x-8 gap-y-4">
                <div class="group">
                  <x-input-label-dark>Desired Profit</x-input-label-dark>
                  <x-number-input-pound id="desired_profit" name="desired_profit"
                    value="{{ $finance->desired_profit }}"></x-number-input-pound>
                  @error('desired_profit')
                    <p class="yns_red mt-1 text-sm">{{ $message }}</p>
                  @enderror
                </div>
              </div>
              <div class="grid grid-cols-2 gap-x-8 gap-y-4">
                <div class="group">
                  <x-input-label-dark>Budget Name</x-input-label-dark>
                  <x-text-input id="name" name="name" value="{{ $finance->name }}"></x-text-input>
                  @error('budget_name')
                    <p class="yns_red mt-1 text-sm">{{ $message }}</p>
                  @enderror
                </div>

                <div class="group">
                  <x-input-label-dark>Date From</x-input-label-dark>
                  <x-date-time-input id="date_from" name="date_from"
                    value="{{ $finance->date_from }}"></x-date-time-input>
                  @error('date_from')
                    <p class="yns_red mt-1 text-sm">{{ $message }}</p>
                  @enderror
                </div>

                <div class="group">
                  <x-input-label-dark>Date To</x-input-label-dark>
                  <x-date-time-input id="date_to" name="date_to" value="{{ $finance->date_to }}"></x-date-time-input>
                  @error('date_to')
                    <p class="yns_red mt-1 text-sm">{{ $message }}</p>
                  @enderror
                </div>

                <div class="group">
                  <x-input-label-dark>Link To Event</x-input-label-dark>
                  <x-text-input id="external_link" name="external_link"
                    value="{{ $finance->external_link }}"></x-text-input>
                  @error('link_to_event')
                    <p class="yns_red mt-1 text-sm">{{ $message }}</p>
                  @enderror
                </div>
              </div>

              <p class="my-4 text-xl font-bold">Incoming</p>
              <div class="grid grid-cols-2 gap-x-8 gap-y-4">
                @php
                  $incoming = json_decode($finance->incoming, true);
                  $fieldNames = [
                      'income_presale' => ' Presale',
                      'income_otd' => 'On The Door',
                  ];

                  $incomingValues = [];
                  foreach ($incoming as $item) {
                      $incomingValues[$item['field']] = $item['value'];
                  }
                @endphp

                @foreach ($fieldNames as $key => $label)
                  <div class="income group">
                    <x-input-label-dark>{{ $label }}</x-input-label-dark>
                    <x-number-input-pound id="incoming[{{ $key }}]" name="incoming[{{ $key }}]"
                      value="{{ $incomingValues[$key] ?? 0 }}"></x-number-input-pound>
                  </div>
                  @error('incoming.' . $key)
                    <p class="yns_red mt-1 text-sm">{{ $message }}</p>
                  @enderror
                @endforeach
              </div>

              <div class="grid grid-cols-2 gap-x-8 gap-y-4">
                @php
                  $otherIncoming = json_decode($finance->other_incoming, true);
                @endphp

                @foreach ($otherIncoming as $index => $item)
                  <div class="income group">
                    <x-input-label-dark>{{ $item['field'] ?? 'Other' }}</x-input-label-dark>
                    <x-number-input-pound id="other[{{ $index }}]" name="other[{{ $index }}]"
                      value="{{ $item['value'] ?? 0 }}"></x-number-input-pound>
                    @error('other.' . $index)
                      <p class="yns_red mt-1 text-sm">{{ $message }}</p>
                    @enderror
                  </div>
                @endforeach
              </div>

              <button id="add-income-row"
                class="mt-8 rounded-lg border border-white bg-white px-4 py-2 font-heading text-black transition duration-150 ease-in-out hover:border-yns_yellow hover:text-yns_yellow">Add
                Row <span class="fas fa-plus"></span></button>

              <p class="my-4 text-xl font-bold">Outgoings</p>
              <div class="grid grid-cols-2 gap-x-8 gap-y-4">
                @php
                  $outgoing = json_decode($finance->outgoing, true);
                  $fieldNames = [
                      'outgoing_venue' => 'Venue',
                      'outgoing_band' => 'Band(s)',
                      'outgoing_promotion' => 'Promotion',
                      'outgoing_rider' => 'Rider',
                  ];

                  $outgoingValues = [];
                  foreach ($outgoing as $item) {
                      $outgoingValues[$item['field']] = $item['value'];
                  }
                @endphp

                @foreach ($fieldNames as $key => $label)
                  <div class="outgoing group">
                    <x-input-label-dark>{{ $label }}</x-input-label-dark>
                    <x-number-input-pound id="outgoing[{{ $key }}]" name="outgoing[{{ $key }}]"
                      value="{{ $outgoingValues[$key] ?? 0 }}"></x-number-input-pound>
                  </div>
                  @error('outgoing.' . $key)
                    <p class="yns_red mt-1 text-sm">{{ $message }}</p>
                  @enderror
                @endforeach
              </div>

              <div class="grid grid-cols-2 gap-x-8 gap-y-4">
                @php
                  $otherOutgoing = json_decode($finance->other_outgoing, true);
                @endphp

                @foreach ($otherOutgoing as $index => $item)
                  <div class="outgoing group">
                    <x-input-label-dark>{{ $item['field'] ?? 'Other' }}</x-input-label-dark>
                    <x-number-input-pound id="other[{{ $index }}]" name="other[{{ $index }}]"
                      value="{{ $item['value'] ?? 0 }}"></x-number-input-pound>
                    @error('other.' . $index)
                      <p class="yns_red mt-1 text-sm">{{ $message }}</p>
                    @enderror
                  </div>
                @endforeach
              </div>

              <button id="add-outgoing-row"
                class="mt-8 rounded-lg border border-white bg-white px-4 py-2 font-heading text-black transition duration-150 ease-in-out hover:border-yns_yellow hover:text-yns_yellow">Add
                Row <span class="fas fa-plus"></span></button>

              <div class="grid grid-cols-3 px-8 py-8">
                <div class="income group">
                  <p class="py-4 font-heading text-xl font-bold">Incoming</p>
                  <p>Presale Tickets: <span id="preview_income_presale"></span></p>
                  <p>On The Door Tickets: <span id="preview_income_otd"></span></p>
                  <p>Other: <span id="preview_income_other"></span></p>
                </div>
                <div class="outgoing group">
                  <p class="py-4 font-heading text-xl font-bold">Outgoings</p>
                  <p>Venue: <span id="preview_outgoing_venue"></span></p>
                  <p>Band(s): <span id="preview_outgoing_band"></span></p>
                  <p>Promotion: <span id="preview_outgoing_promotion"></span></p>
                  <p>Rider: <span id="preview_outgoing_rider"></span></p>
                  <p class="mb-8">Other: <span id="preview_outgoing_other"></span></p>
                </div>
                <div class="totals group">
                  <p class="py-4 font-heading text-xl font-bold">Totals</p>
                  <p class="mt-8">Total Incoming: <span id="income_total" name="income_total"></span></p>
                  <p>Total Outgoings: <span id="outgoing_total" name="outgoing_total"></span></p>
                  <p class="mt-4 text-lg font-bold">Total Profit: <span id="profit_total" name="profit_total"></span>
                  </p>
                  <p class="mt-4 text-lg"><span id="desired_profit_remaining" name="desired_profit_remaining"></span>
                  </p>
                  <p class="mt-4 text-lg"><span id="desired_profit_remaining" name="desired_profit_remaining"></span>
                  </p>
                </div>
              </div>

              <button type="submit"
                class="mt-8 flex w-full justify-center rounded-lg border border-yns_cyan bg-yns_cyan px-4 py-2 font-heading text-xl text-black transition duration-150 ease-in-out hover:border-yns_yellow hover:text-yns_yellow">Save</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>

<script>
  jQuery(document).ready(function() {
    let incomeCount = {{ count($otherIncoming) }};
    let outgoingCount = {{ count($otherOutgoing) }};

    // Call calculateTotals on page load
    calculateTotals();

    function calculateTotals() {
      let desiredProfit = parseFloat(jQuery('#desired_profit').val()) || 0;
      let incomePresale = parseFloat(jQuery('#incoming\\[income_presale\\]').val()) || 0;
      let incomeOtd = parseFloat(jQuery('#incoming\\[income_otd\\]').val()) || 0; // Corrected the ID selector
      let incomeOther = Array.from(jQuery('input[name^="other["]')).reduce((sum, input) => sum + (parseFloat(jQuery(
          input)
        .val()) || 0), 0);

      let outgoingVenue = parseFloat(jQuery('#outgoing\\[outgoing_venue\\]').val()) ||
        0; // Corrected the ID selector
      let outgoingBand = parseFloat(jQuery('#outgoing\\[outgoing_band\\]').val()) || 0; // Corrected the ID selector
      let outgoingPromotion = parseFloat(jQuery('#outgoing\\[outgoing_promotion\\]').val()) ||
        0; // Corrected the ID selector
      let outgoingRider = parseFloat(jQuery('#outgoing\\[outgoing_rider\\]').val()) ||
        0; // Corrected the ID selector
      let outgoingOther = Array.from(jQuery('input[name^="other["]')).reduce((sum, input) => sum + (parseFloat(
        jQuery(input)
        .val()) || 0), 0);

      // Calculate totals
      let incomeTotal = incomePresale + incomeOtd + incomeOther;
      let outgoingTotal = outgoingVenue + outgoingBand + outgoingPromotion + outgoingRider + outgoingOther;
      let profitTotal = incomeTotal - outgoingTotal;

      let remainingDesiredProfit = '';
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
      jQuery('#preview_income_presale').text(formatCurrency(incomePresale));
      jQuery('#preview_income_otd').text(formatCurrency(incomeOtd));
      jQuery('#preview_income_other').text(formatCurrency(incomeOther));
      jQuery('#preview_outgoing_venue').text(formatCurrency(outgoingVenue));
      jQuery('#preview_outgoing_band').text(formatCurrency(outgoingBand));
      jQuery('#preview_outgoing_promotion').text(formatCurrency(outgoingPromotion));
      jQuery('#preview_outgoing_rider').text(formatCurrency(outgoingRider));
      jQuery('#preview_outgoing_other').text(formatCurrency(outgoingOther));
      jQuery('#income_total').text(formatCurrency(incomeTotal));
      jQuery('#outgoing_total').text(formatCurrency(outgoingTotal));
      jQuery('#profit_total').text(formatCurrency(profitTotal));
      jQuery('#desired_profit_remaining').text(remainingDesiredProfit);

      // Prepare for form submission
      jQuery('#finances-form').data('numericValue', numericValue);
    }

    // Recalculate on input changes
    jQuery('#finances-form').on('input', 'input', function() {
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
            <x-number-input-pound class="income_other" name="other_income[${incomeCount}]"></x-number-input-pound>
        </div>
        <button class="remove-other-income-row rounded-lg h-10 bg-yns_dark_orange px-4 py-2 font-heading text-black border border-yns_dark_orange hover:text-white hover:border-white transition duration-150 ease-in-out">Remove <span class="fas fa-minus"></span></button>
      `;
      incomeCount++; // Increment the counter
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
            <x-number-input-pound class="outgoing_other" name="other_outgoing[${outgoingCount}]"></x-number-input-pound>
        </div>
        <button class="remove-other-outgoing-row rounded-lg h-10 bg-yns_dark_orange px-4 py-2 font-heading text-black border border-yns_dark_orange hover:text-white hover:border-white transition duration-150 ease-in-out">Remove <span class="fas fa-minus"></span></button>
      `;
      outgoingCount++; // Increment the counter
      this.parentNode.insertBefore(newRow, this);
      newRow.querySelector('.remove-other-outgoing-row').addEventListener('click', function() {
        newRow.remove();
        calculateTotals();
      });
    });

    // Handle form submission for editing
    jQuery('#finances-form').on('submit', function(event) {
      event.preventDefault(); // Prevent the default form submission
      const formData = new FormData(this);

      // Clear previous values for income_other
      if (formData instanceof FormData) {
        formData.delete('other_income[]'); // Corrected to match your naming
        // Add individual income_other values
        jQuery('.income_other').each(function() {
          const value = parseFloat(jQuery(this).val()) || 0; // Get the value, default to 0 if NaN
          formData.append('other_income[]', value); // Append as an array
        });

        // Clear previous values for outgoing_other
        formData.delete('other_outgoing[]'); // Corrected to match your naming
        // Add individual outgoing_other values
        jQuery('.outgoing_other').each(function() {
          const value = parseFloat(jQuery(this).val()) || 0; // Get the value, default to 0 if NaN
          formData.append('other_outgoing[]', value); // Append as an array
        });
      }

      // Append isolated numeric values
      const numericValue = jQuery('#finances-form').data('numericValue'); // Retrieve the stored numeric value
      formData.append('desired_profit_remaining', numericValue);

      // Other values
      formData.append('income_total', jQuery('#income_total').text().replace(/[^0-9.-]+/g, ""));
      formData.append('outgoing_total', jQuery('#outgoing_total').text().replace(/[^0-9.-]+/g, ""));
      formData.append('profit_total', jQuery('#profit_total').text().replace(/[^0-9.-]+/g, ""));


      // Ensure CSRF token is set for AJAX requests
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
        }
      });

      // AJAX request to save edited finance data
      $.ajax({
        url: '{{ route('promoter.dashboard.finances.update', $finance->id) }}', // Ensure the URL is correct
        type: 'PATCH',
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
          console.log(response.success);

          if (response.success) {
            showSuccessNotification(response.message);
            console.log(response.message);

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
