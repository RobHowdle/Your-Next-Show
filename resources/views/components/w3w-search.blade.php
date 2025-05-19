{{-- In resources/views/components/what3words-input.blade.php --}}
@props(['id' => 'w3w', 'name' => 'w3w', 'value' => null, 'label' => 'What3Words Address'])

<div x-data="what3wordsInput()" class="w3w-component">
  <x-input-label-dark for="{{ $id }}">{{ $label }}</x-input-label-dark>

  <div class="relative">
    <input id="{{ $id }}" name="{{ $name }}" type="text" x-model="address" x-on:input="handleInput"
      class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 text-white focus:border-yns_yellow focus:ring-yns_yellow sm:text-sm"
      placeholder="Enter address or location" autocomplete="off" />

    <div x-show="isLoading" class="absolute right-3 top-1/2 -translate-y-1/2">
      <svg class="h-5 w-5 animate-spin text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
        viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor"
          d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
        </path>
      </svg>
    </div>
  </div>

  <div x-show="results.length > 0" class="mt-2 overflow-hidden rounded-md border border-gray-700 bg-gray-900">
    <template x-for="(result, index) in results" :key="index">
      <div x-on:click="selectResult(result)"
        class="cursor-pointer border-b border-gray-700 p-2 last:border-b-0 hover:bg-gray-800">
        <div class="flex items-center">
          <span class="mr-2 text-yns_yellow">///</span>
          <span x-text="result.words" class="text-sm text-white"></span>
        </div>
        <p x-text="result.nearestPlace" class="mt-1 text-xs text-gray-400"></p>
      </div>
    </template>
  </div>

  <div x-show="selectedW3W" class="mt-2 text-sm">
    <div class="flex items-center text-white">
      <span class="mr-2 text-yns_yellow">///</span>
      <span x-text="selectedW3W"></span>
    </div>
  </div>

  {{-- Hidden input for form submission --}}
  <input type="hidden" :name="'{{ $name }}'" :value="selectedW3W" />
</div>

@push('scripts')
  <script>
    function what3wordsInput() {
      return {
        address: '{{ $value }}',
        results: [],
        selectedW3W: '{{ $value }}',
        isLoading: false,
        timeout: null,

        handleInput() {
          clearTimeout(this.timeout);

          if (!this.address || this.address.length < 3) {
            this.results = [];
            return;
          }

          this.isLoading = true;

          // Determine if input looks like a what3words address (contains dots)
          const isW3WFormat = this.address.includes('.');
          const endpoint = isW3WFormat ?
            '{{ route('what3words.suggest') }}' :
            '{{ route('what3words.convert-address') }}';

          const payload = isW3WFormat ? {
            w3w: this.address
          } : {
            address: this.address
          };

          this.timeout = setTimeout(() => {
            fetch(endpoint, {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/json',
                  'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(payload)
              })
              .then(response => response.json())
              .then(data => {
                this.isLoading = false;
                console.log('API Response:', data);

                if (data.success) {
                  if (data.suggestions) {
                    this.results = data.suggestions;
                  } else if (data.result) {
                    this.results = [{
                      words: data.result.words,
                      nearestPlace: data.result.nearestPlace
                    }];
                  }
                } else {
                  this.results = [];
                }
              })
              .catch(error => {
                this.isLoading = false;
                console.error('Error:', error);
                this.results = [];
              });
          }, 500);
        },

        selectResult(result) {
          this.selectedW3W = result.words;
          this.address = result.words;
          this.results = [];
        }
      };
    }
  </script>
@endpush
