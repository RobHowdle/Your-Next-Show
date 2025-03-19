<div class="grid gap-8 lg:grid-cols-3">
  <div class="lg:col-span-2">
    <div class="min-h-[400px] rounded-b-xl rounded-r-xl border border-gray-800 bg-yns_dark_blue/75 p-8 backdrop-blur-sm">
      <!-- About Tab -->
      <div id="about" class="tab-panel hidden">
        <div class="prose-invert prose mb-4 max-w-none">
          {!! $singleService->description ?? 'We\'re currently working on our description. Check back soon!' !!}
        </div>
      </div>

      <!-- Members Tab -->
      <div id="members" class="tab-panel hidden">
        @if (!empty($serviceData['members']))
          <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($serviceData['members'] as $member)
              <div class="rounded-lg border border-gray-700 bg-black/30 p-6 backdrop-blur-sm">
                <div class="flex flex-col items-center space-y-3">
                  <h3 class="text-lg font-semibold text-white">
                    {{ $member['name'] }}
                  </h3>
                  @if (!empty($member['role']))
                    <p class="text-sm text-gray-400">{{ $member['role'] }}</p>
                  @endif
                </div>
              </div>
            @endforeach
          </div>
        @else
          <div class="flex flex-col items-center justify-center space-y-4 text-center">
            <span class="fas fa-users text-4xl text-gray-400"></span>
            <p class="text-gray-300">We haven't got our members listed yet! Come back soon!</p>
          </div>
        @endif
      </div>

      <div id="music" class="tab-panel hidden">
        @if (!empty($serviceData['streamUrls']))
          <p class="mb-4 text-center text-2xl font-bold">Listen To Us</p>
          <p class="mb-4 text-center">Our music is available on the following platforms. Feel free to give us a
            follow to stay updated with our releases!</p>

          @php
            $streamUrls = $serviceData['streamUrls'] ?? [];
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
                            '<iframe style="border: 0; width: 100%; height: 420px;" src="' .
                            $platformUrl .
                            '"></iframe>';
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
            $streamUrls = $serviceData['streamUrls'] ?? [];
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
            <div
              class="streaming-platforms grid-cols-{{ min($linkCount, 6) }} grid place-items-center items-center gap-4">
              @foreach ($otherLinks as $link)
                <a href="{{ $link['url'] }}" target="_blank" class="streaming-platforms" rel="noopener noreferrer">
                  <img src="{{ asset('storage/images/system/streaming/' . strtolower($link['platform']) . '.png') }}"
                    alt="{{ ucfirst($link['platform']) }} Streaming Link" class="streaming-platform-logo">
                </a>
              @endforeach
            </div>
          @endif
        @else
          <div class="flex flex-col items-center justify-center space-y-4 text-center">
            <span class="fas fa-music text-4xl text-gray-400"></span>
            <p class="text-gray-300">We're currently setting up our streaming links. Check back soon to listen to our
              music!</p>
          </div>
        @endif
      </div>

      <!-- Reviews Tab -->
      <div id="reviews" class="tab-panel hidden">
        <div class="space-y-6">
          @if ($singleService->recentReviews && $singleService->recentReviews > 0)
            <p class="text-center">Want to know what we're like? Check out our reviews!</p>

            <!-- Detailed Ratings -->
            <div class="ratings-block mt-4 flex flex-col items-center gap-4">
              <p class="grid grid-cols-1 text-center md:grid-cols-2 md:text-left">
                Communication:
                <span class="rating-wrapper flex flex-row gap-3">
                  {!! $renderRatingIcons($averageCommunicationRating) !!}
                </span>
              </p>
              <p class="grid grid-cols-1 text-center md:grid-cols-2 md:text-left">
                Music:
                <span class="rating-wrapper flex flex-row gap-3">
                  {!! $renderRatingIcons($bandAverageMusicRating) !!}
                </span>
              </p>
              <p class="grid grid-cols-1 text-center md:grid-cols-2 md:text-left">
                Promotion:
                <span class="rating-wrapper flex flex-row gap-3">
                  {!! $renderRatingIcons($bandAveragePromotionRating) !!}
                </span>
              </p>
              <p class="grid grid-cols-1 text-center md:grid-cols-2 md:text-left">
                Gig Quality:
                <span class="rating-wrapper flex flex-row gap-3">
                  {!! $renderRatingIcons($bandAverageGigQualityRating) !!}
                </span>
              </p>
            </div>

            <!-- Review Comments -->
            <div class="reviews-block mt-8 flex flex-col gap-4">
              @foreach ($singleService->recentReviews as $review)
                <div class="review text-center font-sans">
                  <p class="flex flex-col">
                    "{{ $review->review }}"
                    <span>- {{ $review->author }}</span>
                  </p>
                </div>
              @endforeach
            </div>
          @else
            <p class="mt-4 text-center">
              No reviews available for {{ $singleService->name }} yet. Be the first to leave a review!
            </p>
          @endif
        </div>
      </div>
    </div>
  </div>

  <!-- Sidebar -->
  <div class="hidden space-y-8 lg:block">
    <div class="rounded-xl border border-gray-800 bg-yns_dark_blue/75 p-6 backdrop-blur-sm">
      <h2 class="mb-6 font-heading text-xl font-bold text-white">Quick Facts</h2>
      <div class="space-y-4">
        @if ($singleService->contact_name)
          <div class="flex items-center gap-3 text-gray-300">
            <span class="fas fa-user text-yns_yellow"></span>
            <span>Contact: {{ $singleService->contact_name }}</span>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>
