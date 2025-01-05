<x-guest-layout>
  <div class="mx-auto min-h-screen w-full max-w-xl pt-44">
    <p class="px-8 py-8 text-center font-heading text-4xl font-bold text-white">Login</p>
    <div class="rounded bg-black p-8 font-sans">
      <x-auth-session-status class="mb-4" :status="session('status')" />
      <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="group">
          <x-input-label-dark for="email" :value="__('Email')" :required="true" />
          <x-text-input id="email" class="mt-1 block w-full" type="email" name="email" :value="old('email')" required
            autofocus autocomplete="email" oninput="this.value = this.value.toLowerCase();" />
          <x-input-error :messages="$errors->get('email')" class="mt-2" :required="true" />
        </div>

        <div class="group mt-4">
          <x-input-label-dark for="password" :value="__('Password')" :required="true" />
          <div class="relative">
            <x-text-input id="password" class="mt-1 block w-full" type="password" name="password" required
              autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" :required="true" />
            <button type="button" onclick="togglePasswordVisibility()"
              class="password-toggle-icon absolute inset-y-0 right-0 flex items-center px-3">
              <svg class="h-5 w-5 text-gray-400" id="password-eye" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
              </svg>
            </button>
          </div>
        </div>

        <div class="group mt-4 block">
          <label for="remember_me" class="inline-flex items-center">
            <input id="remember_me" type="checkbox"
              class="rounded border-yns_red shadow-sm focus:ring-indigo-500 dark:bg-gray-900 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800"
              name="remember">
            <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
          </label>
        </div>

        <div class="mt-4 flex items-center justify-end gap-4">
          @if (Route::has('password.request'))
            <a class="rounded-md text-sm text-white underline transition duration-150 ease-in-out hover:text-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
              href="{{ route('password.request') }}">
              {{ __('Forgot your password?') }}
            </a>
          @endif

          <x-primary-button>
            {{ __('Log in') }}
          </x-primary-button>
        </div>
      </form>
    </div>
  </div>
</x-guest-layout>
