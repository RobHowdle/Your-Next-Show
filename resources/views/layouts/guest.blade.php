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

<body class="guest relative font-sans antialiased" x-data="{
    sidebarOpen: false,
    loading: true,
    init() {
        setTimeout(() => {
            this.loading = false
        }, 500)
    }
}">
  <div class="absolute inset-0 bg-cover bg-fixed bg-center bg-no-repeat"
    style="background-image: url('{{ asset('storage/images/system/hero-bg.jpg') }}'); z-index: -1;"></div>
  <x-loading-overlay x-show="loading" />
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

          <!-- Replace the existing sidebar div -->
          <div x-show="sidebarOpen" class="fixed inset-0 z-50 flex justify-end"
            x-transition:enter="transition transform ease-out duration-300" x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0" x-transition:leave="transition transform ease-in duration-300"
            x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full" x-cloak>
            <!-- Sidebar Content -->
            <div
              class="relative w-screen bg-gradient-to-br from-yns_dark_blue via-black to-yns_dark_blue backdrop-blur-md md:w-full">
              <!-- Close Button -->
              <button @click="sidebarOpen = false"
                class="absolute right-4 top-6 rounded-full bg-black/50 px-0 py-2 text-gray-400 transition-colors hover:text-yns_yellow">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
              </button>

              <!-- Logo Section -->
              <div class="flex items-center space-x-3 px-2 py-4">
                <img src="{{ asset('images/system/yns_logo.png') }}" class="h-16" alt="Logo">
                <span class="font-heading text-xl font-bold text-white">{{ config('app.name') }}</span>
              </div>

              <!-- Navigation Links -->
              <div class="mt-6 space-y-1 px-3">
                <a href="{{ url('/venues') }}"
                  class="{{ request()->is('venues*') ? 'bg-yns_yellow/10 text-yns_yellow' : 'text-white hover:bg-white/5 hover:text-yns_yellow' }} group flex items-center rounded-lg px-4 py-3 text-lg font-semibold transition-all lg:hidden">
                  <i class="fa-solid fa-location-dot mr-3 w-5"></i>
                  Venues
                </a>
                <a href="{{ url('/promoters') }}"
                  class="{{ request()->is('promoters*') ? 'bg-yns_yellow/10 text-yns_yellow' : 'text-white hover:bg-white/5 hover:text-yns_yellow' }} group flex items-center rounded-lg px-4 py-3 text-lg font-semibold transition-all lg:hidden">
                  <i class="fa-solid fa-users mr-3 w-5"></i>
                  Promoters
                </a>
                <a href="{{ url('/services') }}"
                  class="{{ request()->is('services*') ? 'bg-yns_yellow/10 text-yns_yellow' : 'text-white hover:bg-white/5 hover:text-yns_yellow' }} group flex items-center rounded-lg px-4 py-3 text-lg font-semibold transition-all lg:hidden">
                  <i class="fa-solid fa-guitar mr-3 w-5"></i>
                  Other Services
                </a>

                <!-- Auth Links -->
                @auth
                  <a href="{{ url('/dashboard') }}"
                    class="group flex items-center rounded-lg px-4 py-3 text-lg font-semibold text-white transition-all hover:bg-white/5 hover:text-yns_yellow lg:hidden">
                    <i class="fa-solid fa-gauge mr-3 w-5"></i>
                    Dashboard
                  </a>
                @else
                  <a href="{{ url('/login') }}"
                    class="group flex items-center rounded-lg px-4 py-3 text-lg font-semibold text-white transition-all hover:bg-white/5 hover:text-yns_yellow lg:hidden">
                    <i class="fa-solid fa-right-to-bracket mr-3 w-5"></i>
                    Login
                  </a>
                @endauth

                <!-- Additional Links -->
                @guest
                  <a href="{{ route('register') }}"
                    class="group flex items-center rounded-lg px-4 py-3 text-lg font-semibold text-white transition-all hover:bg-white/5 hover:text-yns_yellow">
                    <i class="fa-solid fa-user-plus mr-3 w-5"></i>
                    Register
                  </a>
                @endguest

                <div class="my-4 border-t border-white/10"></div>

                <a href="{{ route('gig-guide') }}"
                  class="group flex items-center rounded-lg px-4 py-3 text-lg font-semibold text-white transition-all hover:bg-white/5 hover:text-yns_yellow">
                  <i class="fa-solid fa-calendar-days mr-3 w-5"></i>
                  Gig Guide
                </a>
                <a href="{{ route('public-events') }}"
                  class="group flex items-center rounded-lg px-4 py-3 text-lg font-semibold text-white transition-all hover:bg-white/5 hover:text-yns_yellow">
                  <i class="fa-solid fa-music mr-3 w-5"></i>
                  Events
                </a>
              </div>
            </div>

            <!-- Backdrop -->
            <div @click="sidebarOpen = false" class="flex-1 bg-black/80 backdrop-blur-sm"></div>
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
</body>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js" defer></script>

</html>
