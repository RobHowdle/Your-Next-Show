<x-guest-layout>
  <div class="mx-auto min-h-screen w-full max-w-xl pt-44">
    <p class="px-8 py-8 text-center font-heading text-4xl font-bold text-white">Register</p>
    <div class="rounded bg-black p-8 font-sans">
      <p class="mb-2 text-white">
        We take security <span class="font-bold">very</span> seriously and are committed to protecting you and your data.
        Please ensure
        that all of your information is accurate and secure.
      </p>

      <form id="registration-form" method="POST" action="{{ route('register') }}">
        @csrf
        <div>
          <x-input-label-dark for="first_name" :value="__('First Name')" :required="true" />
          <x-text-input id="first_name" class="mt-1 block w-full" name="first_name" :value="old('first_name')" :required="true"
            autofocus autocomplete="first_name" />
          <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
        </div>

        <div class="mt-4">
          <x-input-label-dark for="last_name" :value="__('Last Name')" :required="true" />
          <x-text-input id="last_name" class="mt-1 block w-full" name="last_name" :value="old('last_name')" :required="true"
            autofocus autocomplete="last_name" />
          <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
        </div>

        <div class="mt-4">
          <x-input-label-dark for="date_of_birth" :value="__('Date of Birth')" :required="true" />
          <x-date-input id="date_of_birth" class="mt-1 block w-full" name="date_of_birth" :value="old('date_of_birth')"
            :required="true" autofocus autocomplete="date_of_birth" />
          <x-input-error :messages="$errors->get('date_of_birth')" class="mt-2" />
        </div>

        <div class="mt-4">
          <x-input-label-dark for="email" :value="__('Email')" :required="true" />
          <x-text-input id="email" class="mt-1 block w-full" type="email" name="email" :value="old('email')"
            :required="true" autocomplete="email" oninput="this.value = this.value.toLowerCase();" />
          <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
          <x-input-label-dark for="password" :value="__('Password')" :required="true" />
          <div class="relative">
            <x-text-input id="password" class="mt-1 block w-full" type="password" name="password" :required="true"
              autocomplete="new-password" />
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
          <div id="password-strength-container"
            style="width: 100%; height: 10px; background-color: #e0e0e0; border-radius: 5px; margin-top: 5px;">
            <div id="password-strength-meter" style="height: 100%; width: 0%; border-radius: 5px;"></div>
          </div>
          <span id="password-strength-text"></span>
          <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div id="password-requirements" class="mt-2 w-full text-sm text-white">
          <p class="font-bold">Password Requirements:</p>
          <ul>
            <li class="flex items-center">
              <span id="length-requirement" class="requirement flex items-center">
                <svg id="length-icon" class="mr-2 hidden h-4 w-4 text-green-400" fill="currentColor"
                  viewBox="0 0 20 20">
                  <path d="M6 10l2 2 6-6-1.5-1.5L8 10.5l-3.5-3.5L3 8l3 3z" />
                </svg>
                Minimum 8 characters
              </span>
            </li>
            <li class="flex items-center">
              <span id="uppercase-requirement" class="requirement flex items-center">
                <svg id="uppercase-icon" class="mr-2 hidden h-4 w-4 text-green-400" fill="currentColor"
                  viewBox="0 0 20 20">
                  <path d="M6 10l2 2 6-6-1.5-1.5L8 10.5l-3.5-3.5L3 8l3 3z" />
                </svg>
                At least 1 uppercase letter (A-Z)
              </span>
            </li>
            <li class="flex items-center">
              <span id="lowercase-requirement" class="requirement flex items-center">
                <svg id="lowercase-icon" class="mr-2 hidden h-4 w-4 text-green-400" fill="currentColor"
                  viewBox="0 0 20 20">
                  <path d="M6 10l2 2 6-6-1.5-1.5L8 10.5l-3.5-3.5L3 8l3 3z" />
                </svg>
                At least 1 lowercase letter (a-z)
              </span>
            </li>
            <li class="flex items-center">
              <span id="number-requirement" class="requirement flex items-center">
                <svg id="number-icon" class="mr-2 hidden h-4 w-4 text-green-400" fill="currentColor"
                  viewBox="0 0 20 20">
                  <path d="M6 10l2 2 6-6-1.5-1.5L8 10.5l-3.5-3.5L3 8l3 3z" />
                </svg>
                At least 1 number (0-9)
              </span>
            </li>
            <li class="flex items-center">
              <span id="special-requirement" class="requirement flex items-center">
                <svg id="special-icon" class="mr-2 hidden h-4 w-4 text-green-400" fill="currentColor"
                  viewBox="0 0 20 20">
                  <path d="M6 10l2 2 6-6-1.5-1.5L8 10.5l-3.5-3.5L3 8l3 3z" />
                </svg>
                At least 1 special character (@$!%*?&)
              </span>
            </li>
            <li class="flex items-center">
              <span id="not-compromised-requirement" class="requirement flex items-center">
                <svg id="not-compromised-icon" class="mr-2 hidden h-4 w-4 text-green-400" fill="currentColor"
                  viewBox="0 0 20 20">
                  <path d="M6 10l2 2 6-6-1.5-1.5L8 10.5l-3.5-3.5L3 8l3 3z" />
                </svg>
                Must not be compromised
              </span>
            </li>
            <li class="flex items-center">
              <span id="password-match-requirement" class="requirement flex items-center">
                <svg id="password-match-icon" class="mr-2 hidden h-4 w-4 text-green-400" fill="currentColor"
                  viewBox="0 0 20 20">
                  <path d="M6 10l2 2 6-6-1.5-1.5L8 10.5l-3.5-3.5L3 8l3 3z" />
                </svg>
                Passwords match
              </span>
            </li>
          </ul>
        </div>

        <div class="mt-4">
          <x-input-label-dark for="password_confirmation" :value="__('Confirm Password')" :required="true" />
          <x-text-input id="password_confirmation" class="mt-1 block w-full" type="password"
            name="password_confirmation" :required="true" autocomplete="new-password" />
          <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="mt-4">
          <x-input-label-dark for="role" :value="__('Select User Role')" :required="true" />
          <select id="role" name="role"
            class="mt-1 block w-full rounded-md border-yns_red bg-gray-900 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            :required="true" autofocus autocomplete="role">
            @foreach ($roles as $role)
              <option value="{{ $role->id }}">{{ ucfirst($role->name) }}</option>
            @endforeach
          </select>
          <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <div class="mt-4 flex items-center justify-end gap-4">
          <a class="rounded-md text-sm text-white underline transition duration-150 ease-in-out hover:text-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
            href="{{ route('login') }}">
            {{ __('Already registered?') }}
          </a>

          <x-primary-button id="register-button">
            {{ __('Register') }}
          </x-primary-button>
        </div>
      </form>
    </div>
  </div>
</x-guest-layout>
<script>
  // Utility Functions
  const checkRequirement = (value, test) => {
    const requirement = document.getElementById(`${test}-requirement`);
    const icon = document.getElementById(`${test}-icon`);
    const isValid = {
      length: pwd => pwd.length >= 8,
      uppercase: pwd => /[A-Z]/.test(pwd),
      lowercase: pwd => /[a-z]/.test(pwd),
      number: pwd => /[0-9]/.test(pwd),
      special: pwd => /[@$!%*?&]/.test(pwd)
    } [test](value);

    requirement?.classList.toggle('valid', isValid);
    icon?.classList.toggle('hidden', !isValid);
    return isValid;
  };

  const updatePasswordStrength = (password) => {
    const meter = document.getElementById('password-strength-meter');
    const text = document.getElementById('password-strength-text');
    const requirements = ['length', 'uppercase', 'lowercase', 'number', 'special'];
    const strength = requirements.filter(req => checkRequirement(password, req)).length;

    const levels = {
      0: {
        width: '0%',
        class: '',
        text: ''
      },
      1: {
        width: '25%',
        class: 'weak',
        text: 'Weak'
      },
      2: {
        width: '50%',
        class: 'medium',
        text: 'Medium'
      },
      3: {
        width: '75%',
        class: 'strong',
        text: 'Strong'
      },
      4: {
        width: '100%',
        class: 'strong',
        text: 'Very Strong'
      }
    };

    const {
      width,
      class: className,
      text: strengthText
    } = levels[strength];
    meter.style.width = width;
    meter.className = className;
    text.textContent = strengthText;
  };

  function checkPasswordMatch() {
    const password = document.getElementById('password').value;
    const confirmation = document.getElementById('password_confirmation').value;
    const matchRequirement = document.getElementById('password-match-requirement');
    const matchIcon = document.getElementById('password-match-icon');

    if (password && confirmation) {
      const matches = password === confirmation;
      matchRequirement?.classList.toggle('valid', matches);
      matchIcon?.classList.toggle('hidden', !matches);
    }
  }

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

  // Event Listeners
  document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('registration-form');
    const passwordInput = document.getElementById('password');
    const confirmInput = document.getElementById('password_confirmation');

    passwordInput?.addEventListener('input', (e) => {
      updatePasswordStrength(e.target.value);
      checkPasswordMatch();
    });

    confirmInput?.addEventListener('input', checkPasswordMatch);

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
