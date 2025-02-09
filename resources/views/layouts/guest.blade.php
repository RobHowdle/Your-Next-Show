<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ config('app.name', 'Laravel') }}</title>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

  <!-- Favicon -->
  <link rel="icon" type="image/png" href="/icons/favicon-96x96.png" sizes="96x96" />
  <link rel="icon" type="image/svg+xml" href="/icons/favicon.svg" />
  <link rel="shortcut icon" href="/icons/favicon.ico" />
  <link rel="apple-touch-icon" sizes="180x180" href="/icons/apple-touch-icon.png" />
  <meta name="apple-mobile-web-app-title" content="YNS" />
  <link rel="manifest" href="/icons/site.webmanifest" />
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


  <script src="https://kit.fontawesome.com/dd6bff54df.js" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- Scripts -->
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="guest relative font-sans antialiased" x-data="{ sidebarOpen: false }">
  <div class="absolute inset-0 bg-cover bg-fixed bg-center bg-no-repeat"
    style="background-image: url('{{ asset('storage/images/system/hero-bg.jpg') }}'); z-index: -1;"></div>
  <div x-data="{
      startTime: performance.now(),
      loadingTime: 0,
      hideLoader() {
          this.$nextTick(() => {
              document.querySelector('#preloader').classList.remove('animation');
              document.querySelector('#preloader').classList.add('over');
              document.querySelector('.pre-overlay.o-1').style.height = '0%';
              document.querySelector('.pre-overlay.o-2').style.height = '0%';
          });
      },
      checkLoadingTime() {
          this.loadingTime = performance.now() - this.startTime;
          if (this.loadingTime > 1000) {
              setTimeout(this.hideLoader, 4000); // Delay hiding the loader
          } else {
              this.hideLoader(); // Hide immediately if loading is fast
          }
      }
  }" x-init="checkLoadingTime">
    <!-- Preloader structure -->
    <div id="preloader" class="animation">
      <div class="decor">
        <div class="bar"></div>
      </div>
      <p>Loading...</p>
    </div>

    <!-- Overlay elements -->
    <div class="pre-overlay o-1" style="height: 100%;"></div>
    <div class="pre-overlay o-2" style="height: 100%;"></div>
  </div>
  @if (Route::has('login'))
    <nav class="fixed z-10 w-full bg-yns_dark_blue">
      <div class="mx-auto flex max-w-screen-2xl flex-wrap items-center justify-between px-2 py-4 md:px-4 md:py-8">
        <a href="{{ url('/') }}" class="flex items-center space-x-3 rtl:space-x-reverse">
          <img src="{{ asset('images/system/yns_logo.png') }}" class="h-16"
            alt="{{ config('app.name', 'Laravel') }} Logo" />
          <span
            class="hidden self-center whitespace-nowrap text-lg font-semibold text-white sm:block xl:text-2xl">{{ config('app.name') }}</span>
        </a>
        <div class="group flex items-center gap-2">
          <div class="hidden w-full md:w-auto lg:block" id="navbar-default">
            <ul class="flex flex-col items-center p-4 font-medium md:flex-row md:space-x-8 rtl:space-x-reverse">
              <li>
                <a href="{{ url('/venues') }}"
                  class="{{ request()->is('venues*') ? 'text-yns_yellow' : '' }} font-heading text-lg font-semibold text-white transition duration-150 ease-in-out hover:text-yns_yellow focus:rounded-sm focus:outline focus:outline-2 focus:outline-red-500 lg:text-xl xl:text-2xl">Venues</a>
              </li>
              <li>
                <a href="{{ url('/promoters') }}"
                  class="{{ request()->is('promoters*') ? 'text-yns_yellow' : '' }} xl: font-heading text-lg font-semibold text-white transition duration-150 ease-in-out hover:text-yns_yellow focus:rounded-sm focus:outline focus:outline-2 focus:outline-red-500 lg:text-xl xl:text-2xl">Promoters</a>
              </li>
              <li>
                <a href="{{ url('/services') }}"
                  class="{{ request()->is('service*') ? 'text-yns_yellow' : '' }} xl: font-heading text-lg font-semibold text-white transition duration-150 ease-in-out hover:text-yns_yellow focus:rounded-sm focus:outline focus:outline-2 focus:outline-red-500 lg:text-xl xl:text-2xl">Other</a>
              </li>
              @auth
                <li>
                  <a href="{{ url('/dashboard') }}"
                    class="xl: font-heading text-lg font-semibold text-white transition duration-150 ease-in-out hover:text-yns_yellow focus:rounded-sm focus:outline focus:outline-2 focus:outline-red-500 lg:text-xl xl:text-2xl">Dashboard</a>
                </li>
              @else
                <li>
                  <a href="{{ url('/login') }}"
                    class="xl: font-heading text-lg font-semibold text-white transition duration-150 ease-in-out hover:text-yns_yellow focus:rounded-sm focus:outline focus:outline-2 focus:outline-red-500 lg:text-xl xl:text-2xl">Login</a>
                </li>
              @endauth
            </ul>
          </div>
          <!-- Sidebar toggle button -->
          <button @click="sidebarOpen = true" class="inline-flex items-center p-2 text-gray-500 hover:text-gray-700">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
              xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16">
              </path>
            </svg>
          </button>

          <div x-show="sidebarOpen" class="fixed inset-0 z-50 flex justify-end" style="top: 0; left: auto;"
            x-transition:enter="transition transform ease-out duration-300" x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0" x-transition:leave="transition transform ease-in duration-300"
            x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
            class="fixed inset-0 z-50 flex justify-end" x-cloak>
            <div class="relative w-screen bg-gray-800 text-white shadow-lg md:w-64">
              <button @click="sidebarOpen = false" class="absolute left-4 top-12 text-gray-400 hover:text-white">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                  xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                  </path>
                </svg>
              </button>
              <div class="mt-24 space-y-4">
                <a href="{{ url('/venues') }}"
                  class="{{ request()->is('venues*') ? 'dark:text-yns_yellow' : '' }} block px-4 py-2 text-center hover:text-yns_yellow lg:hidden">Venues</a>
                <a href="{{ url('/promoters') }}"
                  class="{{ request()->is('promoters*') ? 'dark:text-yns_yellow' : '' }} block px-4 py-2 text-center hover:text-yns_yellow lg:hidden">Promoters</a>
                <a href="{{ url('/services') }}"
                  class="{{ request()->is('services*') ? 'dark:text-yns_yellow' : '' }} block px-4 py-2 text-center hover:text-yns_yellow lg:hidden">Other</a>
                @auth
                  <a href="{{ url('/dashboard') }}"
                    class="block px-4 py-2 text-center hover:text-yns_yellow lg:hidden">Dashboard</a>
                @else
                  <a href="{{ url('/login') }}"
                    class="block px-4 py-2 text-center hover:text-yns_yellow lg:hidden">Login</a>
                @endauth
                @guest
                  <a href="{{ route('register') }}"
                    class="block px-4 py-2 text-center hover:text-yns_yellow">Register</a>
                @endguest
                <a href="{{ route('gig-guide') }}" class="block px-4 py-2 text-center hover:text-yns_yellow">Gig
                  Guide</a>
                <a href="{{ route('public-events') }}"
                  class="block px-4 py-2 text-center hover:text-yns_yellow">Events</a>
              </div>
            </div>
            <div @click="sidebarOpen = false" class="flex-1 bg-black opacity-50"></div>
          </div>

        </div>
      </div>
    </nav>
  @endif

  <div class="flex min-h-screen flex-col">
    <div class="flex-grow backdrop-brightness-50">
      {{ $slot }}
    </div>
  </div>

  <footer class="w-full text-white transition duration-150 ease-in-out hover:text-yns_yellow">
    <div class="w-full bg-yns_dark_blue px-2 py-4">
      <div
        class="mx-auto block max-w-screen-2xl flex-wrap items-center justify-between gap-4 px-2 py-4 md:flex md:gap-6 md:px-4">
        <a href="{{ url('/') }}"
          class="flex w-full items-center justify-center space-x-3 transition duration-150 ease-in-out hover:text-yns_yellow xl:w-60 xl:justify-start rtl:space-x-reverse">
          <img src="{{ asset('images/system/yns_logo.png') }}" class="h-16"
            alt="{{ config('app.name', 'Laravel') }} Logo" />
          <span
            class="hidden self-center whitespace-nowrap text-lg font-semibold sm:block xl:text-2xl dark:text-white">{{ config('app.name') }}</span>
        </a>
        <ul
          class="flex w-full flex-col justify-center gap-8 py-4 text-center font-heading sm:flex-row md:py-0 xl:w-60">
          <li>
            <a href="/about"
              class="text-white transition duration-150 ease-in-out hover:text-yns_yellow hover:underline">About</a>
          </li>
          <li>
            <a href="/credits"
              class="text-white transition duration-150 ease-in-out hover:text-yns_yellow hover:underline">Credits</a>
          </li>
          <li>
            <a href="/contact"
              class="text-white transition duration-150 ease-in-out hover:text-yns_yellow hover:underline">Contact</a>
          </li>
        </ul>
        <ul class="flex w-full flex-col gap-2 text-center font-heading xl:w-60 xl:text-right">
          <li>
            <a href="https://www.youtube.com/watch?v=Rs2z7OA3XKI" target="_blank"
              class="text-white transition duration-150 ease-in-out hover:text-yns_yellow">We didn't know
              what to put
              here so here is a funny video</a>
          </li>
        </ul>
      </div>
    </div>
    <div class="w-full bg-black px-2 py-2">
      <div
        class="mx-auto flex max-w-screen-2xl flex-col flex-wrap items-center justify-between gap-4 px-2 py-4 md:px-4 lg:flex-row lg:flex-row-reverse">
        <a href="/privacy-policy"
          class="order-1 text-center text-white transition duration-150 ease-in-out hover:text-yns_yellow hover:underline lg:order-none">
          Privacy Policy
        </a>
        <p class="order-last text-center text-yns_med_gray lg:order-none">
          &copy; {{ env('APP_NAME') }} All Rights Reserved {{ date('Y') }}
        </p>
        <a href="#"
          class="order-2 text-center text-white transition duration-150 ease-in-out hover:text-yns_yellow hover:underline lg:order-none">
          Terms & Conditions
        </a>
      </div>
    </div>

  </footer>
  <script>
    function initialize() {
      // Your initialization code here
      console.log('Google Maps API initialized');
    }

    // Ensure the function is available globally
    window.initialize = initialize;
  </script>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js" defer></script>
  <script
    src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places&callback=initialize"
    async defer></script>
</body>

</html>
