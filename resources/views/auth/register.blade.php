<x-guest-layout>
  <div x-data="roleSelector" class="flex min-h-screen flex-col xl:flex-row">
    {{-- Benefits Section --}}
    <div class="relative mt-24 hidden bg-black lg:flex xl:mt-0 xl:w-1/2">
      <div class="absolute inset-0">
        <div class="absolute inset-0 bg-gradient-to-r from-black to-transparent"></div>
        <img src="{{ asset('images/system/register_bg.jpg') }}" class="h-full w-full object-cover opacity-50"
          alt="Background">
      </div>
      <div
        class="relative z-10 flex w-full flex-col items-center justify-center p-12 xl:ml-10 xl:items-start 2xl:ml-16 3xl:ml-20">
        <h2 class="font-heading text-4xl font-bold text-white">Join Our Community</h2>
        <p class="mt-4 text-lg text-gray-300">Unlock exclusive features and opportunities</p>

        <div class="mt-8 space-y-6">
          {{-- Benefit Items --}}
          <div class="flex items-center space-x-4">
            <div class="shrink-0 rounded-full bg-yns_yellow p-2">
              <svg class="h-6 w-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
            </div>
            <div class="w-[300px] rounded-lg bg-black/40 p-3 backdrop-blur-sm">
              <h3 class="font-heading text-xl font-bold text-white">Direct Contact</h3>
              <p class="text-gray-200">Connect directly with venue owners, promoters, artists and more</p>
            </div>
          </div>

          <div class="flex items-center space-x-4">
            <div class="shrink-0 rounded-full bg-yns_yellow p-2">
              <svg class="h-6 w-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
              </svg>
            </div>
            <div class="w-[300px] rounded-lg bg-black/40 p-3 backdrop-blur-sm">
              <h3 class="font-heading text-xl font-bold text-white">Secure Platform</h3>
              <p class="text-gray-200">Your data is protected with enterprise-grade security</p>
            </div>
          </div>

          <div class="flex items-center space-x-4">
            <div class="shrink-0 rounded-full bg-yns_yellow p-2">
              <svg class="h-6 w-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
              </svg>
            </div>
            <div class="w-[300px] rounded-lg bg-black/40 p-3 backdrop-blur-sm">
              <h3 class="font-heading text-xl font-bold text-white">Instant Updates</h3>
              <p class="text-gray-200">Get real-time notifications about new opportunities</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Registration Form Section --}}
    <div class="relative flex w-full items-center justify-center px-4 py-6 lg:mt-0 xl:w-1/2">
      {{-- Animated Background --}}
      <div class="absolute inset-0 overflow-hidden">
        {{-- Base Background --}}
        <div class="absolute inset-0 bg-gradient-to-br from-black via-gray-900 to-black"></div>

        {{-- Animated Pattern --}}
        <div class="absolute inset-0 opacity-5">
          <div class="absolute inset-0 bg-repeat opacity-10"
            style="background-image: url('data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M54.627 0l.83.828-1.415 1.415L51.8 0h2.827zM5.373 0l-.83.828L5.96 2.243 8.2 0H5.374zM48.97 0l3.657 3.657-1.414 1.414L46.143 0h2.828zM11.03 0L7.372 3.657 8.787 5.07 13.857 0H11.03zm32.284 0L49.8 6.485 48.384 7.9l-7.9-7.9h2.83zM16.686 0L10.2 6.485 11.616 7.9l7.9-7.9h-2.83zM37.656 0l8.485 8.485-1.414 1.414L36.242 0h1.414zM22.344 0L13.858 8.485 15.272 9.9l8.485-8.485h-1.414zM32.6 0l9.9 9.9-1.415 1.414L30.443 0H32.6zM27.4 0l-9.9 9.9 1.415 1.414L29.557 0H27.4z' fill='%239C92AC' fill-opacity='0.4' fill-rule='evenodd'/%3E%3C/svg%3E');">
          </div>
        </div>

        {{-- Glowing Accent --}}
        <div class="absolute inset-0">
          <div
            class="bg-gradient-radial absolute inset-0 animate-pulse from-yns_yellow/20 via-transparent to-transparent">
          </div>
        </div>
      </div>

      {{-- Content Container --}}
      <div class="relative z-10 mt-28 w-full max-w-md lg:mt-0 xl:mt-28">
        <div class="mb-8 text-center">
          <h2 class="font-heading text-3xl font-bold text-white">Join Our Community</h2>
          <p class="mt-2 text-lg text-gray-300">Unlock exclusive features and opportunities</p>
        </div>

        <div class="rounded-lg border border-gray-800 bg-gray-900/50 p-4 backdrop-blur-sm md:p-6">
          <form id="registration-form" method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            {{-- Name Fields Side by Side --}}
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
              <div>
                <x-input-label-dark for="first_name" :value="__('First Name')" :required="true" />
                <x-text-input id="first_name" class="mt-1 block w-full" name="first_name" :value="old('first_name')"
                  :required="true" autofocus />
                <x-input-error :messages="$errors->get('first_name')" class="mt-1 text-xs" />
              </div>

              <div>
                <x-input-label-dark for="last_name" :value="__('Last Name')" :required="true" />
                <x-text-input id="last_name" class="mt-1 block w-full" name="last_name" :value="old('last_name')"
                  :required="true" />
                <x-input-error :messages="$errors->get('last_name')" class="mt-1 text-xs" />
              </div>
            </div>

            {{-- Email and DOB Side by Side --}}
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
              <div>
                <x-input-label-dark for="email" :value="__('Email')" :required="true" />
                <x-text-input id="email" class="mt-1 block w-full" type="email" name="email" :value="old('email')"
                  :required="true" />
                <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs" />
              </div>

              <div>
                <x-input-label-dark for="date_of_birth" :value="__('Date of Birth')" :required="true" />
                <x-date-input id="date_of_birth" class="mt-1 block w-full" name="date_of_birth" :value="old('date_of_birth')"
                  :required="true" />
                <x-input-error :messages="$errors->get('date_of_birth')" class="mt-1 text-xs" />
              </div>
            </div>

            {{-- Password Section --}}
            <div class="space-y-2">
              <x-input-label-dark for="password" :value="__('Password')" :required="true" />
              <div class="relative">
                <x-text-input id="password" class="mt-1 block w-full" type="password" name="password"
                  :required="true" />
                <button type="button" onclick="togglePasswordVisibility()"
                  class="password-toggle-icon absolute right-2 top-1/2 -translate-y-1/2">
                  <svg class="h-5 w-5 text-gray-400" id="password-eye" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                </button>
              </div>

              {{-- Add the password strength checker component here --}}
              <x-password-strength-checker input-id="password" />
            </div>

            {{-- Password Confirmation Section --}}
            <div>
              <x-input-label-dark for="password_confirmation" :value="__('Confirm Password')" :required="true" />
              <x-text-input id="password_confirmation" class="mt-1 block w-full" type="password"
                name="password_confirmation" :required="true" />
              <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-xs" />
            </div>

            {{-- Role Selection --}}
            <div>
              <x-input-label-dark for="role" :value="__('Account Type')" :required="true" />
              <div class="mt-2">
                <button type="button" id="role-selector"
                  class="w-full rounded-md border border-gray-700 bg-gray-900 px-4 py-2 text-left text-gray-300 hover:bg-gray-800"
                  @click="showRoleModal = true">
                  <span x-text="selectedRoleText"></span>
                </button>
              </div>

              {{-- Hidden Role Input --}}
              <input type="hidden" name="role" id="role-input" x-model="selectedRole">

              <x-role-selection-modal />

              {{-- Submit Section --}}
              <div class="flex flex-col-reverse gap-4 pt-4 sm:flex-row sm:items-center sm:justify-between">
                <a class="text-center text-sm text-gray-400 hover:text-white sm:text-left"
                  href="{{ route('login') }}">
                  Already registered?
                </a>
                <x-button type="submit" label="Register" id="register-button" />
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
  // Event Listeners
  document.addEventListener('DOMContentLoaded', () => {
    initializePasswordChecker();

    const form = document.getElementById('registration-form');

    form?.addEventListener('submit', async (e) => {
      e.preventDefault();
      const formData = new FormData(e.target);

      try {
        const response = await fetch(e.target.action, {
          method: 'POST',
          headers: {
            'Accept': 'application/json'
          },
          body: formData
        });

        const data = await response.json();

        if (data.success) {
          showSuccessNotification(data.message);
          setTimeout(() => window.location.href = data.redirect, 3000);
        } else {
          throw new Error(data.message || 'Registration failed');
        }
      } catch (error) {
        if (error.response?.status === 422) {
          Object.values(error.response.data.errors)
            .flat()
            .forEach(showFailureNotification);
        } else {
          showFailureNotification(error.message);
        }
      }
    });
  });
</script>
