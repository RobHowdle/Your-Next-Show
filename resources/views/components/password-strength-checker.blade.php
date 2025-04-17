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
      <span id="{{ $inputId }}-length-requirement" class="requirement flex items-center">
        <svg class="mr-1 hidden h-3 w-3 text-green-400" fill="currentColor" viewBox="0 0 20 20">
          <path d="M6 10l2 2 6-6-1.5-1.5L8 10.5l-3.5-3.5L3 8l3 3z" />
        </svg>
        Min 8 characters
      </span>
      <span id="{{ $inputId }}-uppercase-requirement" class="requirement flex items-center">
        <svg class="mr-1 hidden h-3 w-3 text-green-400" fill="currentColor" viewBox="0 0 20 20">
          <path d="M6 10l2 2 6-6-1.5-1.5L8 10.5l-3.5-3.5L3 8l3 3z" />
        </svg>
        At least 1 uppercase letter (A-Z)
      </span>
      <span id="{{ $inputId }}-lowercase-requirement" class="requirement flex items-center">
        <svg class="mr-1 hidden h-3 w-3 text-green-400" fill="currentColor" viewBox="0 0 20 20">
          <path d="M6 10l2 2 6-6-1.5-1.5L8 10.5l-3.5-3.5L3 8l3 3z" />
        </svg>
        At least 1 lowercase letter (a-z)
      </span>
      <span id="{{ $inputId }}-special-requirement" class="requirement flex items-center">
        <svg class="mr-1 hidden h-3 w-3 text-green-400" fill="currentColor" viewBox="0 0 20 20">
          <path d="M6 10l2 2 6-6-1.5-1.5L8 10.5l-3.5-3.5L3 8l3 3z" />
        </svg>
        At least 1 special character (@$!%*?&)
      </span>
      <span id="{{ $inputId }}-not-compromised-requirement" class="requirement flex items-center">
        <svg class="mr-1 hidden h-3 w-3 text-green-400" fill="currentColor" viewBox="0 0 20 20">
          <path d="M6 10l2 2 6-6-1.5-1.5L8 10.5l-3.5-3.5L3 8l3 3z" />
        </svg>
        Must not be a compromised password
      </span>
    </div>
  </div>
</div>
