<div class="venue-tab-content mt-4 overflow-auto font-sans text-lg text-white">
  <div id="about" class="text-center md:text-left">
    @if (empty($singleService->description))
      <p>We're still working on this! Come back later to read about us!</p>
    @else
      <p>{{ $singleService->description }}</p>
    @endif
  </div>
  <div id="members" class="max-h-80 flex h-full flex-col gap-4 overflow-auto text-center md:text-left">
    @if (empty($singleArtistData['members']))
      <div class="service min-w-[calc(50%-1rem)] flex-1">
        @foreach ($singleArtistData['members'] as $member)
          <p>{{ $member->first_name . ' ' . $member->last_name }}</p>
        @endforeach
      </div>
    @else
      <p>We haven't got our members listed yet! Come back soon!</p>
    @endif
  </div>
  <div id="music">
    <p class="mb-4 text-center text-2xl font-bold">Listen To Us</p>
    <p class="mb-4 text-center">Our music is available on the following platforms. Feel free to give us a
      follow to stay updated with our releases!</p>

    @php
      $streamUrls = $singleArtistData['streamUrls'] ?? [];
      if (is_string($streamUrls)) {
          $streamUrls = json_decode($streamUrls, true) ?? [];
      }
      $defaultPlatform = $streamUrls['default'] ?? 'spotify';
      $embedHtml = '';

      if (isset($streamUrls[$defaultPlatform]) && !empty($streamUrls[$defaultPlatform][0])) {
          $platformUrl = $streamUrls[$defaultPlatform][0];

          switch ($defaultPlatform) {
              case 'spotify':
                  // Extract Spotify artist ID from URL
                  preg_match('/artist\/([\w\d]+)/', $platformUrl, $matches);
                  $spotifyId = $matches[1] ?? '';
                  if ($spotifyId) {
                      $embedHtml =
                          '<iframe src="https://open.spotify.com/embed/artist/' .
                          $spotifyId .
                          '" width="100%" height="380" frameborder="0" allowtransparency="true" 
            allow="encrypted-media"></iframe>';
                  }
                  break;
              case 'apple-music':
                  $embedHtml =
                      '<iframe allow="autoplay *; encrypted-media *;" frameborder="0" height="450" style="width:100%;max-width:auto;overflow:hidden;background:transparent;" sandbox="allow-forms allow-popups allow-same-origin allow-scripts allow-storage-access-by-user-activation allow-top-navigation-by-user-activation" src="https://embed.music.apple.com/gb/artist/fateweaver/1731474400"></iframe>';
                  break;

              case 'soundcloud':
                  $embedHtml =
                      '<iframe width="100%" height="300" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=' .
                      urlencode($platformUrl) .
                      '"></iframe>';
                  break;

              case 'bandcamp':
                  // Bandcamp requires specific embed code from their site
                  $embedHtml =
                      '<iframe style="border: 0; width: 100%; height: 420px;" src="' . $platformUrl . '"></iframe>';
                  break;

              case 'youtube-music':
                  $videoId = last(explode('/', parse_url($platformUrl, PHP_URL_PATH)));
                  $embedHtml =
                      '<iframe width="100%" height="315" src="https://www.youtube.com/embed/' .
                      $videoId .
                      '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
                  break;

              case 'amazon-music':
                  $embedHtml =
                      '<a href="' .
                      $platformUrl .
                      '" target="_blank" class="text-blue-600 underline">Listen on Amazon Music</a>';
                  break;
          }
      }
    @endphp

    @if ($embedHtml)
      <p class="my-4 text-center">Listen on {{ ucfirst($defaultPlatform) }}:</p>
      <div id="embed-iframe" class="mb-4 text-center">
        {!! $embedHtml !!}
      </div>
    @endif

    @php
      // Get stream URLs and decode if string
      $streamUrls = $singleArtistData['streamUrls'] ?? [];
      if (is_string($streamUrls)) {
          $streamUrls = json_decode($streamUrls, true) ?? [];
      }

      $defaultPlatform = $streamUrls['default'] ?? 'spotify';
      $otherLinks = [];

      // Convert stream URLs to other links format
      foreach ($streamUrls as $platform => $urls) {
          // Skip the default platform and 'default' key
          if ($platform !== $defaultPlatform && $platform !== 'default' && is_array($urls) && !empty($urls[0])) {
              $otherLinks[] = [
                  'platform' => $platform,
                  'url' => $urls[0],
              ];
          }
      }

      $linkCount = !empty($otherLinks) ? count($otherLinks) : 0;
    @endphp

    @if ($linkCount > 0)
      <p class="my-4 text-center text-2xl font-bold">Also Catch Us On</p>
      <div class="streaming-platforms grid-cols-{{ min($linkCount, 6) }} grid place-items-center items-center gap-4">
        @foreach ($otherLinks as $link)
          <a href="{{ $link['url'] }}" target="_blank" class="streaming-platforms" rel="noopener noreferrer">
            <img src="{{ asset('storage/images/system/streaming/' . strtolower($link['platform']) . '.png') }}"
              alt="{{ ucfirst($link['platform']) }} Streaming Link" class="streaming-platform-logo">
          </a>
        @endforeach
      </div>
    @endif
  </div>

  <div id="reviews">
    @if ($singleService->recentReviews)
      <p class="text-center">Want to know what we're like? Check out our reviews!</p>
      <div class="ratings-block mt-4 flex flex-col items-center gap-4">
        <p class="grid grid-cols-2">Communication:
          <span class="rating-wrapper flex flex-row gap-3">
            {!! $singleArtistData['renderRatingIcons']($singleArtistData['bandAverageCommunicationRating']) !!}
          </span>
        </p>
        <p class="grid grid-cols-2">Music:
          <span class="rating-wrapper flex flex-row gap-3">
            {!! $singleArtistData['renderRatingIcons']($singleArtistData['bandAverageMusicRating']) !!}

          </span>
        </p>
        <p class="grid grid-cols-2">Promotion:
          <span class="rating-wrapper flex flex-row gap-3">
            {!! $singleArtistData['renderRatingIcons']($singleArtistData['bandAveragePromotionRating']) !!}

          </span>
        </p>
        <p class="grid grid-cols-2">Gig Quality:
          <span class="rating-wrapper flex flex-row gap-3">
            {!! $singleArtistData['renderRatingIcons']($singleArtistData['bandAverageGigQualityRating']) !!}

          </span>
        </p>
      </div>

      <div class="reviews-block mt-8 flex flex-col gap-4">
        @foreach ($singleService->recentReviews as $review)
          <div class="review text-center font-sans">
            <p class="flex flex-col">"{{ $review->review }}" <span>- {{ $review->author }}</span></p>
          </div>
        @endforeach
      </div>
    @else
      <p>No reviews yet! Check back later!</p>
    @endif
  </div>

  <div id="socials">
    @if ($singleService->platforms)
      @foreach ($singleService->platforms as $platform)
        @if ($platform['platform'] == 'facebook')
          <a class="mb-4 mr-2 flex items-center hover:text-yns_yellow" href="{{ $platform['url'] }}" target="_blank">
            <span class="fab fa-facebook mr-4 h-10"></span> {{ ucfirst($platform['platform']) }}
          </a>
        @elseif($platform['platform'] == 'twitter')
          <a class="mb-4 mr-2 flex items-center hover:text-yns_yellow" href="{{ $platform['url'] }}" target="_blank">
            <span class="fab fa-twitter mr-4 h-10"></span> {{ ucfirst($platform['platform']) }}
          </a>
        @elseif($platform['platform'] == 'instagram')
          <a class="mb-4 mr-2 flex items-center hover:text-yns_yellow" href="{{ $platform['url'] }}" target="_blank">
            <span class="fab fa-instagram mr-4 h-10"></span> {{ ucfirst($platform['platform']) }}
          </a>
        @elseif($platform['platform'] == 'snapchat')
          <a class="mb-4 mr-2 flex items-center hover:text-yns_yellow" href="{{ $platform['url'] }}" target="_blank">
            <span class="fab fa-snapchat-ghost mr-4 h-10"></span> {{ ucfirst($platform['platform']) }}
          </a>
        @elseif($platform['platform'] == 'tiktok')
          <a class="mb-4 mr-2 flex items-center hover:text-yns_yellow" href="{{ $platform['url'] }}" target="_blank">
            <span class="fab fa-tiktok mr-4 h-10"></span> {{ ucfirst($platform['platform']) }}
          </a>
        @elseif($platform['platform'] == 'youtube')
          <a class="mb-4 mr-2 flex items-center hover:text-yns_yellow" href="{{ $platform['url'] }}" target="_blank">
            <span class="fab fa-youtube mr-4 h-10"></span> {{ ucfirst($platform['platform']) }}
          </a>
        @endif
      @endforeach
    @else
      <p>No socials here yet! Check back later!</p>
    @endif
  </div>
</div>
