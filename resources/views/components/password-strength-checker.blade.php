@props(['inputId' => 'password'])

<div class="space-y-2">
  {{-- Password Strength Indicator --}}
  <div class="mt-2">
    <div class="h-1 w-full rounded-full bg-gray-800">
      <div id="{{ $inputId }}-strength-meter" class="h-full w-0 rounded-full transition-all duration-300"></div>
    </div>
    <span id="{{ $inputId }}-strength-text" class="text-xs text-gray-400"></span>
  </div>

  {{-- Password Requirements --}}
  <div id="{{ $inputId }}-requirements" class="mt-2 text-xs text-gray-400">
    <div class="grid grid-cols-1 gap-2 md:grid-cols-2">
      @foreach ($requirements as $key => $requirement)
        <span id="{{ $inputId }}-{{ $key }}-requirement" class="requirement flex items-center">
          {{-- Success Check Icon --}}
          <svg class="success-icon mr-1 hidden h-3 w-3 text-green-400" fill="currentColor" viewBox="0 0 20 20">
            <path d="M6 10l2 2 6-6-1.5-1.5L8 10.5l-3.5-3.5L3 8l3 3z" />
          </svg>

          {{-- Failure X Icon --}}
          <svg class="failure-icon mr-1 hidden h-3 w-3 text-red-400" fill="currentColor" viewBox="0 0 20 20">
            <path
              d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
          </svg>

          {{-- Loading Spinner --}}
          <svg class="loading-icon mr-1 hidden h-3 w-3 animate-spin text-gray-400" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
            <path class="opacity-75" fill="currentColor"
              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
          </svg>

          {{ $requirement['text'] }}
        </span>
      @endforeach
    </div>
  </div>
  @if ($apiError)
    <div class="mt-2 text-xs text-red-400" role="alert">
      {{ $apiError }}
    </div>
  @endif
</div>
