<div class="rounded-lg bg-gray-800/50 p-6 backdrop-blur-sm">
  <header class="mb-6 border-b border-gray-700 pb-4">
    <h2 class="font-heading text-lg font-medium text-white">
      {{ __('In-House Equipment') }}
    </h2>
    <p class="mt-1 text-sm text-gray-400">
      {{ __('Manage your venue\'s in-house equipment and rental policies.') }}
    </p>
  </header>

  <form id="in-house-gear-form" class="space-y-6">
    @csrf
    @method('PUT')

    <div class="rounded-lg bg-black/20 p-6">
      <h3 class="mb-4 font-heading text-lg font-medium text-white">Equipment Details</h3>
      <div class="grid gap-4">
        <div>
          <x-input-label-dark for="inHouseGear">List your available equipment</x-input-label-dark>
          <x-textarea-input id="inHouseGear" name="inHouseGear"
            class="mt-1 block w-full">{{ old('inHouseGear', $profileData['inHouseGear'] ?? '') }}</x-textarea-input>
          @error('inHouseGear')
            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
          @enderror
        </div>
      </div>
    </div>

    <div class="rounded-lg bg-black/20 p-6">
      <h3 class="mb-4 font-heading text-lg font-medium text-white">Rental Policy</h3>
      <div class="space-y-4">
        <div>
          <x-input-label-dark>Do you require a deposit for hiring your equipment?</x-input-label-dark>
          <div class="mt-2 flex gap-4">
            <label class="flex items-center gap-2">
              <input type="radio" name="deposit_required" value="yes"
                class="rounded border-gray-700 bg-gray-900 text-yns_yellow focus:ring-yns_yellow"
                {{ old('deposit_required', $profileData['depositRequired'] ?? '') === 'yes' ? 'checked' : '' }}>
              <span class="text-white">Deposit Required</span>
            </label>

            <label class="flex items-center gap-2">
              <input type="radio" name="deposit_required" value="no"
                class="rounded border-gray-700 bg-gray-900 text-yns_yellow focus:ring-yns_yellow"
                {{ old('deposit_required', $profileData['depositRequired'] ?? '') === 'no' ? 'checked' : '' }}>
              <span class="text-white">No Deposit Required</span>
            </label>
          </div>
        </div>

        <div id="deposit_amount_wrapper" style="display: none;">
          <x-input-label-dark for="deposit_amount">Standard Deposit Amount</x-input-label-dark>
          <x-number-input-pound id="deposit_amount" name="deposit_amount" class="mt-1 block w-full"
            value="{{ old('deposit_amount', $profileData['depositAmount'] ?? '') }}" />
          <p class="mt-1 text-sm text-gray-400">This amount can be adjusted per booking</p>
        </div>
      </div>
    </div>

    <div class="flex items-center justify-end gap-4 border-t border-gray-700 pt-6">
      <button type="submit"
        class="rounded-lg border border-yns_yellow bg-yns_yellow px-4 py-2 font-heading font-bold text-black transition hover:bg-yns_yellow/90">
        Save Changes
      </button>
      @if (session('status') === 'profile-updated')
        <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-sm text-gray-400">
          {{ __('Saved.') }}
        </p>
      @endif
    </div>
  </form>
</div>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Summernote Initialization
    if (typeof jQuery !== 'undefined') {
      const inHouseGearContent = @json(old('inHouseGear', $profileData['inHouseGear'] ?? ''));
      $('#inHouseGear').summernote({
        placeholder: 'Tell us about you...',
        tabsize: 2,
        height: 300,
        toolbar: [
          ['style', ['style']],
          ['font', ['bold', 'underline', 'clear']],
          ['color', ['color']],
          ['para', ['ul', 'ol', 'paragraph']],
          ['insert', ['link']],
          ['view', ['fullscreen', 'codeview', 'help']]
        ],
        callbacks: {
          onInit: function() {
            if (inHouseGearContent) {
              $('#inHouseGear').summernote('code', inHouseGearContent);
            }
          }
        }
      });
    } else {
      console.error('jQuery is not loaded');
    }

    // Form Submission
    const form = document.getElementById('in-house-gear-form');
    const depositRadios = document.querySelectorAll('input[name="deposit_required"]');
    const depositAmountWrapper = document.getElementById('deposit_amount_wrapper');
    const dashboardType = '{{ $dashboardType }}';

    const savedDepositRequired = @json(old('deposit_required', $profileData['depositRequired'] ?? ''));

    function toggleDepositAmount(value) {
      if (value === 'yes') {
        depositAmountWrapper.style.display = 'block';
      } else {
        depositAmountWrapper.style.display = 'none';
      }
    }

    // Set initial state based on saved data
    if (savedDepositRequired) {
      const radioToCheck = document.querySelector(
        `input[name="deposit_required"][value="${savedDepositRequired}"]`);
      if (radioToCheck) {
        radioToCheck.checked = 'yes';
        toggleDepositAmount(savedDepositRequired);
      }
    }

    // Event listeners for radio buttons
    depositRadios.forEach(radio => {
      radio.addEventListener('change', (e) => {
        toggleDepositAmount(e.target.value);
      });
    });

    if (form) {
      form.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch(this.action, {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
              'Accept': 'application/json'
            },
            body: formData
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              showSuccessNotification(data.message || 'Successfully updated');
            } else {
              throw new Error(data.message || 'Update failed');
            }
          })
          .catch(error => {
            console.error('Error:', error);
            showFailureNotification(error.message || 'An error occurred');
          });
      });
    }
  });
</script>
