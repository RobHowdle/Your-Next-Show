<section>
  <header>
    <h2 class="text-md font-heading font-medium text-white">
      {{ __('Change your user details') }}
    </h2>
  </header>
  <form class="mt-6 space-y-6" id="saveProfile">
    @csrf
    @method('PUT')
    <div>
      <x-input-label-dark for="userFirstName" :value="__('First Name')" />
      <x-text-input id="userFirstName" class="mt-1 block w-full" type="text" name="userFirstName" :value="old('userFirstName', $userFirstName ?? '')"
        required autofocus autocomplete="first_name" />
      <x-input-error :messages="$errors->get('userFirstName')" class="mt-2" />
    </div>

    <div class="mt-4">
      <x-input-label-dark for="userLastName" :value="__('Last Name')" />
      <x-text-input id="last_name" class="mt-1 block w-full" type="text" name="userLastName" :value="old('userLastName', $userLastName ?? '')"
        required autofocus autocomplete="userLastName" />
      <x-input-error :messages="$errors->get('userLastName')" class="mt-2" />
    </div>

    <div class="mt-4">
      <x-input-label-dark for="userEmail" :value="__('Email')" />
      <x-text-input id="userEmail" class="mt-1 block w-full" type="email" name="email" :value="old('userEmail', $userEmail ?? '')" required
        autocomplete="email" />
      <x-input-error :messages="$errors->get('userEmail')" class="mt-2" />
    </div>

    <div class="mt-4">
      <x-input-label-dark for="email" :value="__('Date Of Birth')" />
      <x-date-input id="userDob" class="mt-1 block w-full" type="userDob" name="userDob" :value="old('userDob', $userDob ?? '')" required
        autocomplete="date_of_birth" />
      <x-input-error :messages="$errors->get('userDob')" class="mt-2" />
    </div>

    <div class="mt-4">
      <x-input-label-dark for="password" :value="__('Password')" />
      <div class="relative">
        <x-text-input id="password" class="mt-1 block w-full" type="password" name="password"
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
            <svg id="length-icon" class="mr-2 hidden h-4 w-4 text-green-400" fill="currentColor" viewBox="0 0 20 20">
              <path d="M6 10l2 2 6-6-1.5-1.5L8 10.5l-3.5-3.5L3 8l3 3z" />
            </svg>
            Minimum 8 characters
          </span>
        </li>
        <li class="flex items-center">
          <span id="uppercase-requirement" class="requirement flex items-center">
            <svg id="uppercase-icon" class="mr-2 hidden h-4 w-4 text-green-400" fill="currentColor" viewBox="0 0 20 20">
              <path d="M6 10l2 2 6-6-1.5-1.5L8 10.5l-3.5-3.5L3 8l3 3z" />
            </svg>
            At least 1 uppercase letter (A-Z)
          </span>
        </li>
        <li class="flex items-center">
          <span id="lowercase-requirement" class="requirement flex items-center">
            <svg id="lowercase-icon" class="mr-2 hidden h-4 w-4 text-green-400" fill="currentColor" viewBox="0 0 20 20">
              <path d="M6 10l2 2 6-6-1.5-1.5L8 10.5l-3.5-3.5L3 8l3 3z" />
            </svg>
            At least 1 lowercase letter (a-z)
          </span>
        </li>
        <li class="flex items-center">
          <span id="number-requirement" class="requirement flex items-center">
            <svg id="number-icon" class="mr-2 hidden h-4 w-4 text-green-400" fill="currentColor" viewBox="0 0 20 20">
              <path d="M6 10l2 2 6-6-1.5-1.5L8 10.5l-3.5-3.5L3 8l3 3z" />
            </svg>
            At least 1 number (0-9)
          </span>
        </li>
        <li class="flex items-center">
          <span id="special-requirement" class="requirement flex items-center">
            <svg id="special-icon" class="mr-2 hidden h-4 w-4 text-green-400" fill="currentColor" viewBox="0 0 20 20">
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
      <x-input-label-dark for="password_confirmation" :value="__('Confirm Password')" />
      <x-text-input id="password_confirmation" class="mt-1 block w-full" type="password"
        name="password_confirmation" autocomplete="new-password" />
      <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
    </div>

    <div class="group mb-6">
      @php
        $dataId = 1;
      @endphp
      <x-google-address-picker :postalTown="old('postal_town', $userPostalTown ?? '')" :dataId="$dataId" id="location_{{ $dataId }}" name="location"
        label="Your Location" placeholder="Enter an address" :value="old('location', $userLocation ?? '')" :latitude="old('latitude', $userLat ?? '')"
        :longitude="old('longitude', $userLong ?? '')" />
    </div>

    <div class="flex items-center gap-4">
      <button type="submit"
        class="mt-8 rounded-lg border border-white bg-white px-4 py-2 font-heading font-bold text-black transition duration-150 ease-in-out hover:border-yns_yellow hover:text-yns_yellow">Save</button>
    </div>
  </form>
</section>
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

  $(document).ready(function() {
    const dashboardType = "{{ $dashboardType }}";
    const userId = "{{ $user->id }}";
    const passwordInput = document.getElementById('password');
    const confirmInput = document.getElementById('password_confirmation');

    passwordInput?.addEventListener('input', (e) => {
      updatePasswordStrength(e.target.value);
      checkPasswordMatch();
    });

    confirmInput?.addEventListener('input', checkPasswordMatch);


    $('#saveProfile').on('submit', function(e) {
      e.preventDefault();

      const form = $(this);
      const formData = new FormData(this);

      $.ajax({
        url: '{{ route('profile.update', ['dashboardType' => $dashboardType, 'user' => $user->id]) }}',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
          showSuccessNotification(response.message);
          setTimeout(() => {
            window.location.href = response.redirect;
          }, 2000);
        },
        error: function(xhr) {
          const response = xhr.responseJSON;
          showFailureNotification(response);
        }
      });
    });
  })
</script>
