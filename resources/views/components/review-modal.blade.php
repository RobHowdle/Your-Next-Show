<div id="review-modal" tabindex="-1" class="fixed inset-0 z-[9999] hidden place-items-center px-4">
  <div class="relative mx-auto w-full max-w-4xl border border-white bg-black">
    <div class="review-popup relative rounded-lg bg-black">
      <div class="flex items-center justify-between rounded-t border-b p-4 md:p-5">
        <h3 class="text-xl font-semibold">
          Leave a review
        </h3>
        <button type="button" data-modal-hide="review-modal" class="text-white hover:text-yns_light_gray">
          <span class="fas fa-times"></span>
          <span class="sr-only">Close modal</span>
        </button>
      </div>
      <div class="p-4 md:p-5">
        @php
          $route = match (strtolower($serviceType)) {
              'designer' => 'submit-single-service-review',
              'photographer' => 'submit-single-service-review',
              'videographer' => 'submit-single-service-review',
              'venue' => 'submit-single-service-review',
              'promoter' => 'submit-single-service-review',
              'artist' => 'submit-single-service-review',
              default => 'submit-single-service-review',
          };
        @endphp
        <!-- Form -->
        <form
          action="{{ route($route, [
              'serviceType' => strtolower($serviceType),
              'name' => strtolower(str_replace(' ', '-', $service)),
          ]) }}"
          method="POST"> @csrf
          <input type="text" name="service_type" value="{{ $serviceType }}">
          <input type="text" name="reviewer_ip" id="reviewer_ip">

          @php
            $reviewFields = config('review_fields.' . strtolower($serviceType), []);
          @endphp
          @foreach ($reviewFields as $field => $label)
            <div class="rating-block grid grid-cols-2">
              <p>{{ $label }}:</p>
              <div class="rating">
                @for ($i = 1; $i <= 5; $i++)
                  <input type="checkbox" name="{{ $field }}-rating[]" value="{{ $i }}"
                    id="{{ $field }}-rating-{{ $i }}" class="rating-icon" />
                  <label for="{{ $field }}-rating-{{ $i }}" class="rating-label"></label>
                @endfor
              </div>
            </div>
          @endforeach

          <div class="mt-4">
            <x-input-label-dark for="review_author" :value="__('Your Name')" />
            <x-text-input name="review_author" id="review_author" class="mt-1 block w-full" required
              autocomplete="name" />
          </div>
          <div class="mt-4">
            <x-input-label-dark for="review_message" :value="__('Your Review')" />
            <x-textarea-input name="review_message" id="review_message" class="mt-1 block w-full" required />
          </div>
          <button type="submit"
            class="mt-4 w-full rounded bg-gradient-to-t from-yns_dark_orange to-yns_yellow px-6 py-2 text-black md:w-auto">Submit
            Review</button>
        </form>
      </div>
    </div>
  </div>
</div>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const ratingBlocks = document.querySelectorAll('.rating-block');

    ratingBlocks.forEach(block => {
      const checkboxes = block.querySelectorAll('input[type="checkbox"]');

      checkboxes.forEach((checkbox, index) => {
        checkbox.addEventListener('change', function() {
          // Uncheck all checkboxes after the current one
          for (let i = index + 1; i < checkboxes.length; i++) {
            checkboxes[i].checked = false;
          }

          // Check all checkboxes before the current one
          for (let i = 0; i <= index; i++) {
            checkboxes[i].checked = true;
          }
        });
      });
    });

    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
      e.preventDefault();
      const formData = new FormData(this);
      const submitButton = this.querySelector('button[type="submit"]');
      submitButton.disabled = true;

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
            showSuccessNotification('Review submitted successfully');
            document.getElementById('review-modal').style.display = 'none';
            setTimeout(() => window.location.reload(), 2000);
          } else {
            throw new Error(data.message || 'Failed to submit review');
          }
        })
        .catch(error => {
          showFailureNotification(error.message);
          console.error('Error:', error);
        })
        .finally(() => {
          submitButton.disabled = false;
        });
    });
  });
</script>
