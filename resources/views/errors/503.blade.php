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

  <script src="https://code.jquery.com/jquery-3.7.1.min.js"
    integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
  <script src="https://kit.fontawesome.com/dd6bff54df.js" crossorigin="anonymous"></script>

  <!-- Scripts -->
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
  <div id="preloader" class="animation">
    <div class="decor">
      <div class="bar"></div>
    </div>
    <p>Loading...</p>
  </div>

  <div class="pre-overlay o-1"></div>
  <div class="pre-overlay o-2"></div>

  <div class="flex h-screen w-full flex-col items-center justify-center gap-24 px-2 backdrop-brightness-50">
    <div class="flex items-center space-x-3 rtl:space-x-reverse">
      <img src="{{ asset('images/system/yns_logo.png') }}" class="h-16"
        alt="{{ config('app.name', 'Laravel') }} Logo" />
      <span
        class="hidden self-center whitespace-nowrap text-lg font-semibold text-white sm:block xl:text-2xl">{{ config('app.name') }}</span>
    </div>

    <div class="text text-center text-white">
      <p class="mb-4 text-4xl">Knock Knock...who's there? Not us.</p>
      <p class="mb-4 text-xl">We'll be back soon! We are currently deploying and testing some big updates to our system.
        Please check
        back later.</p>
      <p class="text-lg">Thank you for your patience!</p>
    </div>

    <div class="flex flex-col items-center space-x-4">
      <p class="mb-4 text-lg">Keep up to date on our socials for when we will be back up and running</p>
      <a href="https://linktr.ee/yournextshow" target="_blank" class="hover:text-yns_blue text-white">
        Linktree
      </a>
    </div>

    <script>
      jQuery(document).ready(function() {
        var startTime = performance.now(); // Record the start time when the document is ready

        // Function to hide the loader and overlay
        function hideLoader() {
          jQuery("#preloader").delay(100).removeClass("animation").addClass("over");
          jQuery(".pre-overlay").css({
            "height": "0%"
          });
        }

        // Function to calculate loading time and decide whether to show the loader
        function checkLoadingTime() {
          var endTime = performance.now(); // Record the end time after the document is fully loaded
          var loadingTime = endTime - startTime; // Calculate the loading time in milliseconds

          // Check if the loading time exceeds a threshold (e.g., 1000 milliseconds)
          if (loadingTime > 1000) {
            // Show the loader if loading time exceeds the threshold
            setTimeout(hideLoader, 4000);
          } else {
            // Hide the loader if loading time is fast
            hideLoader();
          }
        }

        // Call the function to check loading time when the document is fully loaded
        jQuery(window).on('load', function() {
          checkLoadingTime();
        });
      });
    </script>
</body>

</html>
