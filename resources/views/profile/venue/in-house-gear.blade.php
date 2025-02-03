<header>
  <h2 class="text-md font-heading font-medium text-white">
    {{ __('In House Gear') }}
  </h2>
</header>
<form id="in-house-gear-form"
  action="{{ route($dashboardType . '.update', ['dashboardType' => $dashboardType, 'user' => $user->id]) }}"
  method="POST">
  @csrf
  @method('PUT')
  <div class="group mb-6">
    <x-input-label-dark for="about">Tell us about you... where you started, why you started, what you do
      etc</x-input-label-dark>
    <x-textarea-input id="inHouseGear"
      name="inHouseGear">{{ old('inHouseGear', $profileData['inHouseGear'] ?? '') }}</x-textarea-input>
    @error('inHouseGear')
      <p class="yns_red mt-1 text-sm">{{ $message }}</p>
    @enderror
  </div>

  <div class="group mb-6">
    <x-input-label-dark>Do you require a deposit for equipment hire?</x-input-label-dark>
    <div class="mt-2 flex gap-4">
      <label class="flex items-center gap-2">
        <input type="radio" name="deposit_required" value="yes" class="form-radio text-yns_cyan"
          {{ old('deposit_required', $profileData['deposit_required'] ?? '') === 'yes' ? 'checked' : '' }}>
        <span class="text-white">Yes</span>
      </label>

      <label class="flex items-center gap-2">
        <input type="radio" name="deposit_required" value="no" class="form-radio text-yns_cyan"
          {{ old('deposit_required', $profileData['deposit_required'] ?? '') === 'no' ? 'checked' : '' }}>
        <span class="text-white">No</span>
      </label>
    </div>
  </div>

  <div id="deposit_amount_wrapper" class="group mb-6" style="display: none;">
    <x-input-label-dark for="deposit_amount">Standard deposit amount (can be adjusted per artist)</x-input-label-dark>
    <x-number-input-pound id="deposit_amount" name="deposit_amount"
      value="{{ old('deposit_amount', $profileData['deposit_amount'] ?? '') }}">
    </x-number-input-pound>
    <p class="mt-1 text-sm text-gray-400">Amount may vary based on equipment hired</p>
  </div>

  <div class="flex items-center gap-4">
    <button type="submit"
      class="mt-8 rounded-lg border border-white bg-white px-4 py-2 font-heading font-bold text-black transition duration-150 ease-in-out hover:border-yns_yellow hover:text-yns_yellow">
      Save
    </button>
    @if (session('status') === 'profile-updated')
      <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
        class="text-sm text-gray-600 dark:text-gray-400">{{ __('Saved.') }}</p>
    @endif
  </div>
</form>
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

    function toggleDepositAmount(value) {
      depositAmountWrapper.style.display = value === 'yes' ? 'block' : 'none';
    }

    const initialValue = document.querySelector('input[name="deposit_required"]:checked')?.value;
    toggleDepositAmount(initialValue);

    depositRadios.forEach(radio => {
      radio.addEventListener('change', (e) => toggleDepositAmount(e.target.value));
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
