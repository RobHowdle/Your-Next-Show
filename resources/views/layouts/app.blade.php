@props(['dashboardType', 'modules'])

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
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

  <!-- Favicon -->
  <link rel="icon" type="image/png" href="/icons/favicon-96x96.png" sizes="96x96" />
  <link rel="icon" type="image/svg+xml" href="/icons/favicon.svg" />
  <link rel="shortcut icon" href="/icons/favicon.ico" />
  <link rel="apple-touch-icon" sizes="180x180" href="/icons/apple-touch-icon.png" />
  <meta name="apple-mobile-web-app-title" content="YNS" />
  <link rel="manifest" href="/icons/site.webmanifest" />

  <script>
    window.mapsReadyPromise = new Promise((resolve) => {
      window.initMap = function() {
        window.mapsLoaded = true;
        resolve();
      };
    });

    window.handleMapsError = function(error) {
      console.error('Maps API failed to load:', error);
      const errorDivs = document.querySelectorAll('[id^="maps-error-"]');
      errorDivs.forEach(div => {
        div.textContent = 'Google Maps failed to load. Please refresh the page.';
        div.classList.remove('hidden');
      });
    };
  </script>

  <script async defer
    src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&libraries=places&callback=initMap">
  </script>

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

  <!-- Full Calendar -->
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>

  <!-- what3words SDK --->
  <script type="module" defer
    src="https://cdn.what3words.com/javascript-components@4.8.0/dist/what3words/what3words.esm.js"></script>
  <script nomodule defer src="https://cdn.what3words.com/javascript-components@4.8.0/dist/what3words/what3words.js">
  </script>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Using a more specific selector to target only Summernote color pickers
      const backColorPickers = document.querySelectorAll('.note-editor [id="backColorPicker"]');
      const foreColorPickers = document.querySelectorAll('.note-editor [id="foreColorPicker"]');

      // Add unique suffixes
      backColorPickers.forEach((picker, index) => {
        picker.id = `summernote-backColorPicker-${index + 1}`;
      });

      foreColorPickers.forEach((picker, index) => {
        picker.id = `summernote-foreColorPicker-${index + 1}`;
      });
    });
  </script>

  <!-- Other scripts -->
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js" defer></script>
  <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js" defer></script>

  <script type="module">
    import {
      showSuccessNotification,
      showFailureNotification,
      showWarningNotification,
      showConfirmationNotification,
      showScheduledNotification,
      showTestNotification, //DEBUG
    } from "{{ Vite::asset('resources/js/utils/swal.js') }}";
    // Make notifications globally available
    window.showSuccessNotification = showSuccessNotification;
    window.showFailureNotification = showFailureNotification;
    window.showWarningNotification = showWarningNotification;
    window.showConfirmationNotification = showConfirmationNotification;
    window.showScheduledNotification = showScheduledNotification;
    window.showTestNotification = showTestNotification; // DEBUG
  </script>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <!-- Include Summernote -->
  <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
</head>

<body class="relative overflow-x-hidden font-sans antialiased">
  <div class="absolute inset-0 bg-cover bg-fixed bg-center bg-no-repeat"
    style="background-image: url('{{ asset('storage/images/system/hero-bg.jpg') }}'); z-index: -1;"></div>
  <div class="min-h-screen text-white" style="isolation: isolate;">
    @include('layouts.navigation', [
        'dashboardType' => $dashboardType,
        'modules' => $modules,
    ])

    @if (isset($header))
      <header class="bg-yns_dark_blue">
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
          {{ $header }}
        </div>
      </header>
    @endif

    <div class="flex min-h-screen flex-col">
      <div class="{{ request()->routeIs('profile.*') ? '' : 'px-2' }} flex-grow backdrop-brightness-50">
        {{ $slot }}
      </div>
      <x-notes :dashboard-type="$dashboardType"></x-notes>
    </div>
  </div>

  @stack('scripts')
</body>

</html>
