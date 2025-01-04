<x-guest-layout>
  <div class="mx-auto min-h-screen w-full max-w-xl pt-44">
    <p class="px-8 py-8 text-center font-heading text-4xl font-bold text-white">
      Forgot your password?
    </p>

    <div class="rounded bg-black p-8 font-sans">
      <span>No problem. Just let us know your email address and we will email you a password reset link that will allow
        you to choose a new one.</span>
      <x-auth-session-status class="mb-4" :status="session('status')" />
      <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="group mt-4">
          <x-input-label-dark for="email" :value="__('Email')" />
          <x-text-input id="email" class="mt-1 block w-full" type="email" name="email" :value="old('email')" required
            autofocus />
          <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4 flex items-center justify-end">
          <x-primary-button>
            {{ __('Email Password Reset Link') }}
          </x-primary-button>
        </div>
      </form>

    </div>
  </div>

</x-guest-layout>
