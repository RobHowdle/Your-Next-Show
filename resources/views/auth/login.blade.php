<x-guest-layout>
  <div class="flex min-h-screen flex-col xl:flex-row">
    {{-- Benefits Section --}}
    <div class="relative mt-24 hidden bg-black lg:flex xl:mt-0 xl:w-1/2">
      <div class="absolute inset-0">
        <div class="absolute inset-0 bg-gradient-to-r from-black to-transparent"></div>
        <img src="{{ asset('images/system/register_bg.jpg') }}" class="h-full w-full object-cover opacity-50"
          alt="Background">
      </div>
      <div
        class="relative z-10 flex w-full flex-col items-center justify-center p-12 xl:ml-10 xl:items-start 2xl:ml-16 3xl:ml-20">
        <h2 class="font-heading text-4xl font-bold text-white">Welcome Back</h2>
        <p class="mt-4 text-lg text-gray-200">Access your account and continue your journey</p>

        <div class="mt-8 space-y-6">
          {{-- Login Benefits --}}
          <div class="flex items-center space-x-4">
            <div class="shrink-0 rounded-full bg-yns_yellow p-2">
              <svg class="h-6 w-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
            </div>
            <div class="w-[280px] rounded-lg bg-black/40 p-3 backdrop-blur-sm">
              <h3 class="font-heading text-xl font-bold text-white">Manage Your Listings</h3>
              <p class="text-gray-200">Update and manage your venue information</p>
            </div>
          </div>

          <div class="flex items-center space-x-4">
            <div class="shrink-0 rounded-full bg-yns_yellow p-2">
              <svg class="h-6 w-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
            </div>
            <div class="w-[280px] rounded-lg bg-black/40 p-3 backdrop-blur-sm">
              <h3 class="font-heading text-xl font-bold text-white">Track Bookings</h3>
              <p class="text-gray-200">Monitor and manage your venue bookings</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Login Form Section --}}
    <div class="relative flex w-full items-center justify-center px-4 py-6 lg:mt-0 xl:w-1/2">
      {{-- Animated Background --}}
      <div class="absolute inset-0 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-black via-gray-900 to-black"></div>
        <div class="absolute inset-0 opacity-5">
          <div class="absolute inset-0 bg-repeat opacity-10"
            style="background-image: url('data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M54.627 0l.83.828-1.415 1.415L51.8 0h2.827zM5.373 0l-.83.828L5.96 2.243 8.2 0H5.374zM48.97 0l3.657 3.657-1.414 1.414L46.143 0h2.828zM11.03 0L7.372 3.657 8.787 5.07 13.857 0H11.03zm32.284 0L49.8 6.485 48.384 7.9l-7.9-7.9h2.83zM16.686 0L10.2 6.485 11.616 7.9l7.9-7.9h-2.83zM37.656 0l8.485 8.485-1.414 1.414L36.242 0h1.414z' fill='%239C92AC' fill-opacity='0.4' fill-rule='evenodd'/%3E%3C/svg%3E');">
          </div>
        </div>
        <div class="absolute inset-0">
          <div
            class="bg-gradient-radial absolute inset-0 animate-pulse from-yns_yellow/20 via-transparent to-transparent">
          </div>
        </div>
      </div>

      {{-- Content Container --}}
      <div class="relative z-10 w-full max-w-md">
        <div class="mb-8 text-center">
          <h2 class="font-heading text-3xl font-bold text-white">Sign In</h2>
          <p class="mt-2 text-lg text-gray-300">Access your account</p>
        </div>

        <div class="rounded-lg border border-gray-800 bg-gray-900/50 p-4 backdrop-blur-sm md:p-6">
          <x-auth-session-status class="mb-4" :status="session('status')" />
          <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf
            {{-- Email Input --}}
            <div>
              <x-input-label-dark for="email" :value="__('Email')" :required="true" />
              <x-text-input id="email" class="mt-1 block w-full" type="email" name="email" :value="old('email')"
                required autofocus autocomplete="email" oninput="this.value = this.value.toLowerCase();" />
              <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs" />
            </div>

            {{-- Password Input --}}
            <div>
              <x-input-label-dark for="password" :value="__('Password')" :required="true" />
              <div class="relative">
                <x-text-input id="password" class="mt-1 block w-full" type="password" name="password" required
                  autocomplete="current-password" />
                <button type="button" onclick="togglePasswordVisibility()"
                  class="password-toggle-icon absolute right-2 top-1/2 -translate-y-1/2">
                  <svg class="h-5 w-5 text-gray-400" id="password-eye" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                </button>
              </div>
              <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs" />
            </div>

            {{-- Remember Me --}}
            <div class="flex items-center justify-between">
              <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox"
                  class="rounded border-gray-700 bg-gray-900 text-yns_yellow shadow-sm focus:ring-yns_yellow"
                  name="remember">
                <span class="ms-2 text-sm text-gray-300">{{ __('Remember me') }}</span>
              </label>
              @if (Route::has('password.request'))
                <a class="text-sm text-gray-300 hover:text-white" href="{{ route('password.request') }}">
                  {{ __('Forgot password?') }}
                </a>
              @endif
            </div>

            {{-- Submit Section --}}
            <div class="flex flex-col-reverse gap-4 pt-4 sm:flex-row sm:items-center sm:justify-between">
              <a class="text-center text-sm text-gray-400 hover:text-white sm:text-left" href="{{ route('register') }}">
                Need an account?
              </a>
              <x-primary-button class="w-full sm:w-auto">
                {{ __('Sign In') }}
              </x-primary-button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</x-guest-layout>

<style>
  @layer utilities {
    .bg-gradient-radial {
      background-image: radial-gradient(var(--tw-gradient-stops));
    }
  }
</style>
<script>
  function togglePasswordVisibility() {
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('password-eye');

    if (passwordInput.type === 'password') {
      passwordInput.type = 'text';
      eyeIcon.innerHTML = `
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
            `;
    } else {
      passwordInput.type = 'password';
      eyeIcon.innerHTML = `
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            `;
    }
  }
</script>
