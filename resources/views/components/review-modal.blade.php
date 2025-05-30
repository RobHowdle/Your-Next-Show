<div id="review-modal" tabindex="-1" class="fixed inset-0 z-[9999] hidden place-items-center px-4 backdrop-blur-sm">
  <div class="relative mx-auto w-full max-w-4xl border border-gray-800 bg-yns_dark_blue/90 shadow-2xl">
    <div class="review-popup relative rounded-lg">
      <!-- Modal Header -->
      <div class="flex items-center justify-between rounded-t border-b border-gray-800 p-6">
        <h3 class="font-heading text-2xl font-bold text-white">
          Leave a review for {{ $title }}
        </h3>
        <button type="button" data-modal-hide="review-modal"
          class="text-gray-400 transition-colors hover:text-yns_yellow">
          <span class="fas fa-times text-xl"></span>
          <span class="sr-only">Close modal</span>
        </button>
      </div>

      <!-- Modal Body -->
      <div class="p-6">
        <form action="{{ $getFormAction() }}" method="POST">
          @csrf
          <input type="hidden" name="service_type" value="{{ $serviceType }}">
          <input type="hidden" name="service" value="{{ $service }}">
          <input type="hidden" name="reviewer_ip" id="reviewer_ip">

          <!-- Rating Fields -->
          <div class="space-y-6">
            @php
              $reviewFields = config('review_fields.' . strtolower($service), []);
              $emptyIcon = asset('storage/images/system/ratings/empty.png');
              $fullIcon = asset('storage/images/system/ratings/full.png');
              $hotIcon = asset('storage/images/system/ratings/hot.png');
            @endphp

            @foreach ($reviewFields as $field => $label)
              <div class="rating-block flex items-center justify-between gap-4">
                <p class="text-lg text-gray-300">{{ $label }}:</p>
                <div class="rating flex gap-2" data-rating-group="{{ $field }}">
                  @for ($i = 1; $i <= 5; $i++)
                    <input type="radio" name="{{ $field }}" value="{{ $i }}"
                      id="{{ $field }}-{{ $i }}" class="rating-icon peer hidden" />
                    <label for="{{ $field }}-{{ $i }}"
                      class="rating-label relative h-6 w-6 cursor-pointer" data-rating="{{ $i }}"
                      style="background-image: url('{{ $emptyIcon }}'); 
                   background-size: contain;
                   background-position: center;">
                    </label>
                  @endfor
                </div>
              </div>
            @endforeach
          </div>

          <!-- Author Name -->
          <div class="mt-6">
            <x-input-label-dark for="review_author" :value="__('Your Name')" class="text-lg text-gray-300" />
            <x-text-input name="review_author" id="review_author"
              class="mt-2 block w-full rounded-lg border border-gray-700 bg-black/50 p-3 text-white placeholder-gray-400 backdrop-blur-sm"
              required autocomplete="name" />
          </div>

          <!-- Review Message -->
          <div class="mt-6">
            <x-input-label-dark for="review_message" :value="__('Your Review')" class="text-lg text-gray-300" />
            <x-textarea-input name="review_message" id="review_message"
              class="mt-2 block w-full rounded-lg border border-gray-700 bg-black/50 p-3 text-white placeholder-gray-400 backdrop-blur-sm"
              required rows="4" />
          </div>

          <!-- Submit Button -->
          <div class="mt-6">
            <button type="submit"
              class="w-full rounded-lg bg-gradient-to-t from-yns_dark_orange to-yns_yellow px-6 py-3 font-medium text-black transition-all hover:from-yns_yellow hover:to-yns_yellow md:w-auto">
              Submit Review
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<style>
  .rating-label {
    transition: background-image 0.2s ease-in-out;
  }

  .rating-label:hover {
    transform: scale(1.1);
  }
</style>
