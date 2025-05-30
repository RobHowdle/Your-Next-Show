<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-900">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ config('app.name') }} - Admin</title>
  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

  <!-- Favicon -->
  <link rel="icon" type="image/png" href="/icons/favicon-96x96.png" sizes="96x96" />
  <link rel="icon" type="image/svg+xml" href="/icons/favicon.svg" />
  <link rel="shortcut icon" href="/icons/favicon.ico" />
  <link rel="apple-touch-icon" sizes="180x180" href="/icons/apple-touch-icon.png" />
  <meta name="apple-mobile-web-app-title" content="YNS" />
  <link rel="manifest" href="/icons/site.webmanifest" />

  <!-- Include jQuery -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"
    integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

  <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

  <!-- Include the Dropzone CSS and JS -->
  <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
  <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>

  <!-- Include PDF.js -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.11.338/pdf.min.js"></script>

  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

  <!-- Include Select2 -->
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

  <!-- Include Flatpickr -->
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

  <!-- Font Awesome -->
  <script src="https://kit.fontawesome.com/dd6bff54df.js" crossorigin="anonymous"></script>

  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <!-- Include Alpine.js -->
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>

  <!-- Full Calendar -->
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>

  <!-- what3words SDK --->
  <script type="module" defer
    src="https://cdn.what3words.com/javascript-components@4.8.0/dist/what3words/what3words.esm.js"></script>
  <script nomodule defer src="https://cdn.what3words.com/javascript-components@4.8.0/dist/what3words/what3words.js">
  </script>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="h-full">
  <div class="min-h-screen bg-gray-900">
    <!-- Navigation -->
    <header class="fixed left-0 right-0 top-0 z-50 border-b border-gray-800 bg-gray-900/80 backdrop-blur">
      <div class="mx-auto flex h-16 max-w-screen-2xl items-center justify-between px-4 md:px-8">
        <div class="flex items-center gap-4">
          <a href="{{ route('admin.dashboard') }}" class="text-xl font-bold text-white">
            Admin Dashboard
          </a>
        </div>

        <div class="flex items-center gap-4">
          <span class="text-sm text-gray-400">{{ Auth::user()->name }}</span>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
              class="rounded bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-700">
              Logout
            </button>
          </form>
        </div>
      </div>
    </header>

    <div class="flex">
      <!-- Sidebar -->
      <aside class="fixed bottom-0 left-0 top-16 w-64 border-r border-gray-800 bg-gray-900">
        <nav class="space-y-2 p-4">
          <a href="{{ route('admin.dashboard') }}"
            class="flex items-center gap-3 rounded-lg px-4 py-2 text-gray-300 hover:bg-gray-800 hover:text-white">
            <i class="fas fa-home text-lg text-gray-400"></i>
            Dashboard
          </a>
          <a href="{{ route('admin.users') }}"
            class="flex items-center gap-3 rounded-lg px-4 py-2 text-gray-300 hover:bg-gray-800 hover:text-white">
            <i class="fas fa-users text-lg text-gray-400"></i>
            Users
          </a>
          <a href="{{ route('admin.venues') }}"
            class="flex items-center gap-3 rounded-lg px-4 py-2 text-gray-300 hover:bg-gray-800 hover:text-white">
            <i class="fas fa-building text-lg text-gray-400"></i>
            Venues
          </a>
          <a href="{{ route('admin.promoters') }}"
            class="flex items-center gap-3 rounded-lg px-4 py-2 text-gray-300 hover:bg-gray-800 hover:text-white">
            <i class="fas fa-bullhorn text-lg text-gray-400"></i>
            Promoters
          </a>
          <a href="{{ route('admin.services') }}"
            class="flex items-center gap-3 rounded-lg px-4 py-2 text-gray-300 hover:bg-gray-800 hover:text-white">
            <i class="fas fa-cogs text-lg text-gray-400"></i>
            Services
          </a>
        </nav>
      </aside>

      <!-- Main Content -->
      <main class="flex-1 pt-16 lg:pl-64">
        <div class="min-h-screen bg-gray-900 p-4 md:p-8">
          <!-- Flash Messages -->
          @if (session('success'))
            <div class="mb-4 rounded-md bg-green-500/10 p-4 text-green-400">
              <div class="flex">
                <i class="fas fa-check-circle mr-3 text-green-400"></i>
                <p>{{ session('success') }}</p>
              </div>
            </div>
          @endif

          @if (session('error'))
            <div class="mb-4 rounded-md bg-red-500/10 p-4 text-red-400">
              <div class="flex">
                <i class="fas fa-exclamation-circle mr-3 text-red-400"></i>
                <p>{{ session('error') }}</p>
              </div>
            </div>
          @endif

          <!-- Content -->
          {{ $slot }}
      </main>
    </div>
  </div>
  </div>
</body>

</html>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js" defer></script>

<!-- Google Maps API -->
<script
  src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places&callback=initializeMaps"
  async defer></script>
