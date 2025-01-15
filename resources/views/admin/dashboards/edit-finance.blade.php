<x-app-layout :dashboardType="$dashboardType" :modules="$modules">
  <x-slot name="header">
    <x-sub-nav :userId="$userId" />
  </x-slot>

  <div class="mx-auto w-full max-w-screen-2xl py-16">
    <div class="relative mb-8 shadow-md sm:rounded-lg">
      <div class="grid grid-cols-[1.75fr_1.25fr] rounded-lg border border-white">
        <div class="rounded-l-lg border-r border-r-white bg-yns_dark_gray px-8 py-8">
          <p class="mb-10 text-4xl font-bold text-white">Editng Budget</p>
          <form id="finances-form" method="POST">
            @csrf
            <div class="mb-4 grid grid-cols-2 gap-x-8 gap-y-4">
              <div class="group">
                <x-input-label-dark>Desired Profit</x-input-label-dark>
                <x-number-input-pound id="desired_profit" name="desired_profit"
                  :value="old('desired_profit', $finance->desired_profit)"></x-number-input-pound>
                @error('desired_profit')
                  <p class="yns_red mt-1 text-sm">{{ $message }}</p>
                @enderror
              </div>
            </div>
            <div class="grid grid-cols-2 gap-x-8 gap-y-4">
              <div class="group">
                <x-input-label-dark :required="true">Budget Name</x-input-label-dark>
                <x-text-input id="budget_name" name="budget_name" :required="true" :value="old('budget_name', $finance->name)"></x-text-input>
                @error('budget_name')
                  <p class="yns_red mt-1 text-sm">{{ $message }}</p>
                @enderror
              </div>

              <div class="group">
                <x-input-label-dark :required="true">Date From</x-input-label-dark>
                <x-date-input id="date_from" name="date_from" :required="true" :value="old('date_from', $finance->date_from)"></x-date-input>
                @error('date_from')
                  <p class="yns_red mt-1 text-sm">{{ $message }}</p>
                @enderror
              </div>

              <div class="group">
                <x-input-label-dark :required="true">Date To</x-input-label-dark>
                <x-date-input id="date_to" name="date_to" :required="true" :value="old('date_to', $finance->date_to)"></x-date-input>
                @error('date_to')
                  <p class="yns_red mt-1 text-sm">{{ $message }}</p>
                @enderror
              </div>

              <div class="group">
                <x-input-label-dark>Link To Event</x-input-label-dark>
                <x-text-input id="external_link" name="external_link" :value="old('external_link', $finance->external_link)"></x-text-input>
                @error('external_link')
                  <p class="yns_red mt-1 text-sm">{{ $message }}</p>
                @enderror
              </div>
            </div>

            @php
              $incoming = json_decode($finance->incoming, true) ?? [];
              $otherIncoming = json_decode($finance->other_incoming, true) ?? [];

              // Get standard income values
              $presale = collect($incoming)->where('field', 'income_presale')->first()['value'] ?? 0;
              $otd = collect($incoming)->where('field', 'income_otd')->first()['value'] ?? 0;
            @endphp

            <p class="my-4 text-xl font-bold">Incoming</p>
            <div class="grid grid-cols-2 gap-x-8 gap-y-4">
              {{-- Presale Income --}}
              <div class="income group">
                <x-input-label-dark>Presale Tickets</x-input-label-dark>
                <x-number-input-pound id="income_presale" name="income_presale"
                  :value="old('income_presale', $presale)"></x-number-input-pound>
                @error('income_presale')
                  <p class="yns_red mt-1 text-sm">{{ $message }}</p>
                @enderror
              </div>

              {{-- On The Door Income --}}
              <div class="income group">
                <x-input-label-dark>On The Door Tickets</x-input-label-dark>
                <x-number-input-pound id="income_otd" name="income_otd" :value="old('income_otd', $otd)"></x-number-input-pound>
                @error('income_otd')
                  <p class="yns_red mt-1 text-sm">{{ $message }}</p>
                @enderror
              </div>
              {{-- Other Income Items --}}
              @foreach ($otherIncoming as $income)
                <div class="income custom-row group grid grid-cols-[1fr,auto] gap-4">
                  <div>
                    <x-input-label-dark>{{ $income['label'] }}</x-input-label-dark>
                    <x-number-input-pound id="income_other_{{ $loop->index }}" name="income_other[]"
                      :value="old('income_other.' . $loop->index, $income['value'])"></x-number-input-pound>
                    <input type="hidden" name="income_label[]" value="{{ $income['label'] }}">
                  </div>
                  <button type="button"
                    class="remove-income-row mt-6 h-10 rounded-lg border border-red-500 bg-red-500 px-4 py-2 font-heading text-white transition duration-150 ease-in-out hover:border-red-700 hover:bg-red-700"
                    onclick="this.closest('.custom-row').remove(); calculateTotals();">
                    <span class="fas fa-times"></span>
                  </button>
                </div>
              @endforeach
            </div>

            <button id="add-income-row"
              class="mt-8 rounded-lg border border-green-500 bg-green-500 px-4 py-2 font-heading text-white transition duration-150 ease-in-out hover:border-green-700 hover:bg-green-700">
              <span class="fas fa-plus-circle mr-2"></span>Add Income
            </button>

            @php
              $outgoing = json_decode($finance->outgoing, true) ?? [];
              $otherOutgoing = json_decode($finance->other_outgoing, true) ?? [];

              // Get standard outgoing values
              $venue = collect($outgoing)->where('field', 'outgoing_venue')->first()['value'] ?? 0;
              $band = collect($outgoing)->where('field', 'outgoing_band')->first()['value'] ?? 0;
              $promotion = collect($outgoing)->where('field', 'outgoing_promotion')->first()['value'] ?? 0;
              $rider = collect($outgoing)->where('field', 'outgoing_rider')->first()['value'] ?? 0;
            @endphp

            <p class="my-4 text-xl font-bold">Outgoings</p>
            <div class="grid grid-cols-2 gap-x-8 gap-y-4">
              {{-- Standard Outgoings --}}
              <div class="outgoing group">
                <x-input-label-dark>Venue</x-input-label-dark>
                <x-number-input-pound id="outgoing_venue" name="outgoing_venue"
                  :value="old('outgoing_venue', $venue)"></x-number-input-pound>
                @error('outgoing_venue')
                  <p class="yns_red mt-1 text-sm">{{ $message }}</p>
                @enderror
              </div>

              <div class="outgoing group">
                <x-input-label-dark>Artist(s)</x-input-label-dark>
                <x-number-input-pound id="outgoing_band" name="outgoing_band" :value="old('outgoing_band', $band)"></x-number-input-pound>
                @error('outgoing_band')
                  <p class="yns_red mt-1 text-sm">{{ $message }}</p>
                @enderror
              </div>

              <div class="outgoing group">
                <x-input-label-dark>Promotion</x-input-label-dark>
                <x-number-input-pound id="outgoing_promotion" name="outgoing_promotion"
                  :value="old('outgoing_promotion', $promotion)"></x-number-input-pound>
                @error('outgoing_promotion')
                  <p class="yns_red mt-1 text-sm">{{ $message }}</p>
                @enderror
              </div>

              <div class="outgoing group">
                <x-input-label-dark>Rider</x-input-label-dark>
                <x-number-input-pound id="outgoing_rider" name="outgoing_rider"
                  :value="old('outgoing_rider', $rider)"></x-number-input-pound>
                @error('outgoing_rider')
                  <p class="yns_red mt-1 text-sm">{{ $message }}</p>
                @enderror
              </div>

              {{-- Other Outgoing Items --}}
              @foreach ($otherOutgoing as $outgoing)
                <div class="outgoing custom-row group grid grid-cols-[1fr,auto] gap-4">
                  <div>
                    <x-input-label-dark>{{ $outgoing['label'] }}</x-input-label-dark>
                    <x-number-input-pound id="outgoing_other_{{ $loop->index }}" name="outgoing_other[]"
                      :value="old('outgoing_other.' . $loop->index, $outgoing['value'])"></x-number-input-pound>
                    <input type="hidden" name="outgoing_label[]" value="{{ $outgoing['label'] }}">
                  </div>
                  <button type="button"
                    class="remove-outgoing-row mt-6 h-10 rounded-lg border border-red-500 bg-red-500 px-4 py-2 font-heading text-white transition duration-150 ease-in-out hover:border-red-700 hover:bg-red-700"
                    onclick="this.closest('.custom-row').remove(); calculateTotals();">
                    <span class="fas fa-times"></span>
                  </button>
                </div>
              @endforeach
            </div>

            <button id="add-outgoing-row"
              class="mt-8 rounded-lg border border-red-500 bg-red-500 px-4 py-2 font-heading text-white transition duration-150 ease-in-out hover:border-red-700 hover:bg-red-700">
              <span class="fas fa-minus-circle mr-2"></span>Add Outgoing
            </button>
            <div class="group mt-4">
              <x-button type="submit" label="Save"></x-button>
            </div>
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
          <p class="mt-4 text-lg"><span id="desired_profit_remaining" name="desired_profit_remaining"></span>
          </p>

          <button
            class="mt-8 rounded-lg border border-white bg-white px-4 py-2 font-heading text-black transition duration-150 ease-in-out hover:border-yns_yellow hover:text-yns_yellow">Export
            To PDF <span class="fas fa-file-export"></span></button>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>

<script>
  let desiredProfit, incomePresale, incomeOtd, incomeOther = 0,
    outgoingVenue, outgoingBand, outgoingPromotion,
    outgoingRider, outgoingOther = 0,
    incomeTotal = 0,
    outgoingTotal = 0,
    profitTotal = 0,
    remainingDesiredProfit = 0;

  // Single document ready with all handlers
  jQuery(document).ready(function() {
    // Initialize date pickers
    flatpickr('#date_from', {
      altInput: true,
      altFormat: "d-m-Y",
      dateFormat: "d-m-Y",
    });

    flatpickr('#date_to', {
      altInput: true,
      altFormat: "d-m-Y",
      dateFormat: "d-m-Y",
    });

    const dashboardType = "{{ $dashboardType }}";

    // Global calculate function
    function calculateTotals() {
      desiredProfit = parseFloat(jQuery('#desired_profit').val()) || 0;
      incomePresale = parseFloat(jQuery('#income_presale').val()) || 0;
      incomeOtd = parseFloat(jQuery('#income_otd').val()) || 0;
      incomeOther = Array.from(jQuery('.income_other')).reduce((sum, input) => sum + (parseFloat(jQuery(input)
          .val()) || 0),
        0);
      outgoingVenue = parseFloat(jQuery('#outgoing_venue').val()) || 0;
      outgoingBand = parseFloat(jQuery('#outgoing_band').val()) || 0;
      outgoingPromotion = parseFloat(jQuery('#outgoing_promotion').val()) || 0;
      outgoingRider = parseFloat(jQuery('#outgoing_rider').val()) || 0;
      outgoingOther = Array.from(jQuery('.outgoing_other')).reduce((sum, input) => sum + (parseFloat(jQuery(input)
          .val()) ||
        0), 0);

      // Calculate totals
      incomeTotal = incomePresale + incomeOtd + incomeOther;
      outgoingTotal = outgoingVenue + outgoingBand + outgoingPromotion + outgoingRider + outgoingOther;
      profitTotal = incomeTotal - outgoingTotal;

      let profitMessage = '';

      // Check if the profits are valid numbers
      if (isNaN(profitTotal) || isNaN(desiredProfit)) {
        profitMessage = 'Total profit or desired profit is not a number.';
        remainingDesiredProfit = 0;
      } else {
        remainingDesiredProfit = desiredProfit - profitTotal;

        if (remainingDesiredProfit === 0) {
          profitMessage = 'You have made your desired profit! Well Done!';
        } else if (remainingDesiredProfit < 0) {
          profitMessage = 'You have exceeded your desired profit by ' + formatCurrency(Math.abs(
            remainingDesiredProfit)) + '!';
        } else {
          profitMessage = 'You need ' + formatCurrency(remainingDesiredProfit) + ' to achieve your desired profit.';
        }
      }

      console.log('Remaining Desired Profit:', remainingDesiredProfit);


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
      jQuery('#desired_profit_remaining').data('value', remainingDesiredProfit);
      jQuery('#desired_profit_remaining').text(profitMessage);
    }

    jQuery(document).ready(function() {
      const dashboardType = "{{ $dashboardType }}";
      calculateTotals();

      // Watch for changes
      jQuery('#finances-form').on('input', function() {
        calculateTotals();
      });
    });

    jQuery('#finances-form').on('submit', function(event) {
      event.preventDefault();
      const formData = new FormData(this);
      console.log(remainingDesiredProfit);

      // Add budget name and other required fields
      formData.append('budget_name', jQuery('#budget_name').val());
      formData.append('date_from', jQuery('#date_from').val());
      formData.append('date_to', jQuery('#date_to').val());
      formData.append('external_link', jQuery('#external_link').val());
      formData.append('desired_profit', jQuery('#desired_profit').val());

      // Standard income fields
      formData.append('income_presale', jQuery('#income_presale').val() || 0);
      formData.append('income_otd', jQuery('#income_otd').val() || 0);

      // Standard outgoing fields
      formData.append('outgoing_venue', jQuery('#outgoing_venue').val() || 0);
      formData.append('outgoing_band', jQuery('#outgoing_band').val() || 0);
      formData.append('outgoing_promotion', jQuery('#outgoing_promotion').val() || 0);
      formData.append('outgoing_rider', jQuery('#outgoing_rider').val() || 0);

      // Clear and re-add arrays
      formData.delete('income_other[]');
      formData.delete('income_label[]');
      jQuery('.income_other').each(function(index) {
        const value = parseFloat(jQuery(this).val()) || 0;
        // Fix label selector to target input directly
        const label = jQuery(this).closest('.income.group').prev().find('input.income_label').val();
        formData.append('income_other[]', value);
        formData.append('income_label[]', label || '');
      });

      formData.delete('outgoing_other[]');
      formData.delete('outgoing_label[]');
      jQuery('.outgoing_other').each(function(index) {
        const value = parseFloat(jQuery(this).val()) || 0;
        // Fix label selector to target input directly
        const label = jQuery(this).closest('.outgoing.group').prev().find('input.outgoing_label').val();
        formData.append('outgoing_other[]', value);
        formData.append('outgoing_label[]', label || '');
      });

      // Add totals
      formData.append('income_total', jQuery('#income_total').text().replace(/[^0-9.-]+/g, ""));
      formData.append('outgoing_total', jQuery('#outgoing_total').text().replace(/[^0-9.-]+/g, ""));
      formData.append('profit_total', jQuery('#profit_total').text().replace(/[^0-9.-]+/g, ""));
      formData.append('desired_profit_remaining', remainingDesiredProfit);

      // AJAX request
      jQuery.ajax({
        url: "{{ route('admin.dashboard.update-finance', ['dashboardType' => ':dashboardType', 'id' => $finance->id]) }}"
          .replace(':dashboardType', dashboardType),
        type: 'POST',
        headers: {
          'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content'),
          'X-HTTP-Method-Override': 'PUT'
        },
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
          console.log('Success:', response);
          if (response.success === true) {
            showSuccessNotification(response.message);
            setTimeout(() => {
              window.location.href = response.redirect_url;
            }, 2000);
          }
        },
        error: function(xhr, status, error) {
          console.error('Error:', xhr.responseJSON);
          showFailureNotification('Error updating budget');
        }
      });
    });

    // Add income row functionality
    document.getElementById('add-income-row').addEventListener('click', function(event) {
      event.preventDefault();
      const newRow = document.createElement('div');
      newRow.classList.add('grid', 'grid-cols-3', 'gap-x-8', 'gap-y-4', 'items-end');
      newRow.innerHTML = `
        <div class="income group mt-4">
            <x-input-label-dark>Label</x-input-label-dark>
            <x-text-input class="income_label" name="income_label[]" placeholder="e.g. Donation"></x-text-input>
        </div>
        <div class="income group mt-4">
            <x-input-label-dark>Amount</x-input-label-dark>
            <x-number-input-pound class="income_other" name="income_other[]"></x-number-input-pound>
        </div>
        <button class="remove-row remove-income-row h-10 rounded-lg border border-red-500 bg-red-500 px-4 py-2 font-heading text-white transition duration-150 ease-in-out hover:border-red-700 hover:bg-red-700">
            <span class="fas fa-trash mr-2"></span>Remove
        </button>
    `;
      this.parentNode.insertBefore(newRow, this);
      newRow.querySelector('.remove-row').addEventListener('click', function() {
        newRow.remove();
        calculateTotals();
      });
    });

    // Add outgoing row functionality
    document.getElementById('add-outgoing-row').addEventListener('click', function(event) {
      event.preventDefault();
      const newRow = document.createElement('div');
      newRow.classList.add('grid', 'grid-cols-3', 'gap-x-8', 'gap-y-4', 'items-end');
      newRow.innerHTML = `
        <div class="outgoing group mt-4">
            <x-input-label-dark>Label</x-input-label-dark>
            <x-text-input class="outgoing_label" name="outgoing_label[]" placeholder="e.g. Security"></x-text-input>
        </div>
        <div class="outgoing group mt-4">
            <x-input-label-dark>Amount</x-input-label-dark>
            <x-number-input-pound class="outgoing_other" name="outgoing_other[]"></x-number-input-pound>
        </div>
        <button class="remove-row h-10 rounded-lg remove-outgoing-row border border-red-500 bg-red-500 px-4 py-2 font-heading text-white transition duration-150 ease-in-out hover:border-red-700 hover:bg-red-700">
            <span class="fas fa-trash mr-2"></span>Remove
        </button>
    `;
      this.parentNode.insertBefore(newRow, this);
      newRow.querySelector('.remove-row').addEventListener('click', function() {
        newRow.remove();
        calculateTotals();
      });
    });

  });
</script>
