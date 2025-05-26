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

      <!-- Music Tab -->
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

      <!-- Events Tab -->
      <div id="events" class="tab-panel hidden">
        <div class="space-y-6">
          @if ($upcomingEvents && $upcomingEvents->count() > 0)
            <p class="text-center text-lg text-gray-300">Upcoming events in the next
              month:</p>

            <div class="grid gap-4 md:grid-cols-2">
              @foreach ($upcomingEvents as $event)
                <div
                  class="group rounded-lg border border-gray-800 bg-black/20 p-4 transition-all hover:border-yns_yellow">
                  <a href="{{ route('public-event', ['eventId' => $event->id]) }}" class="block">
                    <div class="flex items-start justify-between gap-4">
                      <div>
                        <h3 class="font-heading text-lg font-bold text-white group-hover:text-yns_yellow">
                          {{ $event->event_name }}
                        </h3>
                        <p class="text-sm text-gray-400">
                          {{ Carbon\Carbon::parse($event->event_date)->format('D jS M Y') }}
                        </p>
                        @if ($event->start_time)
                          <p class="text-sm text-gray-400">
                            Doors: {{ Carbon\Carbon::parse($event->start_time)->format('g:ia') }}
                          </p>
                        @endif
                        @if ($event->venues && $event->venues->count())
                          <p class="text-sm text-gray-400">
                            Venue: {{ $event->venues->first()->name }}
                          </p>
                        @endif
                      </div>
                      <div class="text-right">
                        @if ($event->ticket_url)
                          <a href="{{ $event->ticket_url }}" target="_blank"
                            class="inline-flex items-center gap-2 rounded-lg bg-yns_yellow px-4 py-2 text-sm font-medium text-black transition-all hover:bg-yellow-400">
                            <span class="fas fa-ticket"></span>
                            Tickets
                          </a>
                        @elseif(!$event->ticket_url && $event->on_the_door_ticket_price)
                          <p class="text-sm font-bold text-yns_yellow">
                            £{{ number_format($event->on_the_door_ticket_price, 2) }}
                          </p>
                        @endif
                      </div>
                    </div>
                  </a>
                </div>
              @endforeach
            </div>
          @else
            <p class="text-left text-lg text-gray-300">
              No upcoming events scheduled in the next month.
            </p>
          @endif
        </div>
      </div>

      <!-- Documents Tab -->
      <div id="documents" class="tab-panel hidden">
        <div class="space-y-6">
          @php
            // Get artist's public documents
$documents = \App\Models\Document::where('serviceable_type', 'App\Models\OtherService')
    ->where('serviceable_id', $singleService->id)
    ->where('private', false)
    ->orderBy('created_at', 'desc')
                ->get();
          @endphp

          @if ($documents && $documents->count() > 0)
            <p class="mb-6 text-center text-lg text-gray-300">Public Documents</p>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
              @foreach ($documents as $document)
                <div
                  class="group relative overflow-hidden rounded-lg border border-gray-800 bg-black/20 p-3 backdrop-blur-sm transition hover:border-yns_yellow/50">
                  <!-- Document Preview -->
                  <div class="relative mb-3 h-24 w-full overflow-hidden rounded bg-gray-900">
                    @php
                      $extension = pathinfo($document->file_path, PATHINFO_EXTENSION);
                      $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
                      $isPdf = strtolower($extension) === 'pdf';
                    @endphp

                    @if ($isImage)
                      <img src="{{ Storage::url($document->file_path) }}" alt="{{ $document->title }}"
                        class="h-full w-full object-cover opacity-80">
                    @elseif($isPdf)
                      <div class="flex h-full w-full items-center justify-center bg-gray-900">
                        <span class="text-4xl text-red-600">
                          <i class="far fa-file-pdf"></i>
                        </span>
                      </div>
                    @else
                      <div class="flex h-full w-full items-center justify-center bg-gray-900">
                        <span class="text-4xl text-gray-600">
                          <i class="far fa-file"></i>
                        </span>
                      </div>
                    @endif

                    <!-- Download Button -->
                    <div
                      class="absolute inset-0 flex items-center justify-center opacity-0 transition-opacity group-hover:opacity-100">
                      <a href="{{ route('document.download', ['id' => $document->id]) }}"
                        class="rounded-lg bg-blue-600 px-3 py-2 text-sm font-medium text-white transition hover:bg-blue-700">
                        <i class="fas fa-download mr-1"></i> Download
                      </a>
                    </div>
                  </div>

                  <!-- Document Info -->
                  <div>
                    <h3 class="line-clamp-1 font-heading text-base font-medium text-white"
                      title="{{ $document->title }}">
                      {{ $document->title }}
                    </h3>

                    <!-- Document Meta -->
                    <div class="mb-2 flex flex-wrap text-xs text-gray-400">
                      <span class="mr-3 flex items-center">
                        <i class="far fa-calendar mr-1"></i>
                        {{ \Carbon\Carbon::parse($document->created_at)->format('d M, Y') }}
                      </span>
                      <span class="flex items-center">
                        <i class="far fa-file mr-1"></i>
                        {{ strtoupper(pathinfo($document->file_path, PATHINFO_EXTENSION)) }}
                      </span>
                    </div>

                    <!-- Category Tags -->
                    @php
                      $categories = json_decode($document->category ?? '[]');
                    @endphp
                    @if (!empty($categories))
                      <div class="mb-2 flex flex-wrap gap-1">
                        @foreach ($categories as $category)
                          <span class="rounded bg-gray-800 px-2 py-1 text-xs text-gray-300">
                            {{ $category }}
                          </span>
                        @endforeach
                      </div>
                    @endif

                    <!-- Description (if available) -->
                    @if ($document->description)
                      <p class="line-clamp-2 text-sm text-gray-400" title="{{ $document->description }}">
                        {{ $document->description }}
                      </p>
                    @endif
                  </div>
                </div>
              @endforeach
            </div>
          @else
            <div class="flex flex-col items-center justify-center space-y-4 py-8 text-center">
              <span class="fas fa-folder-open text-4xl text-gray-400"></span>
              <p class="text-gray-300">No public documents available at this time.</p>
            </div>
          @endif
        </div>
      </div>

      <!-- Reviews Tab -->
      <div id="reviews" class="tab-panel hidden">
        <div class="space-y-6">
          @if (!empty($serviceData['recentReviews']))
            <p class="text-center">Want to know what we're like? Check out our reviews!</p>

            <!-- Detailed Ratings -->
            <div class="ratings-block mt-4 flex flex-col items-center gap-4">
              <p class="grid grid-cols-1 text-center md:grid-cols-2 md:text-left">
                Communication:
                <span class="rating-wrapper flex flex-row gap-3">
                  {!! $serviceData['renderRatingIcons']($serviceData['bandAverageCommunicationRating']) !!}
                </span>
              </p>
              <p class="grid grid-cols-1 text-center md:grid-cols-2 md:text-left">
                Music:
                <span class="rating-wrapper flex flex-row gap-3">
                  {!! $serviceData['renderRatingIcons']($serviceData['bandAverageMusicRating']) !!}
                </span>
              </p>
              <p class="grid grid-cols-1 text-center md:grid-cols-2 md:text-left">
                Promotion:
                <span class="rating-wrapper flex flex-row gap-3">
                  {!! $serviceData['renderRatingIcons']($serviceData['bandAveragePromotionRating']) !!}
                </span>
              </p>
              <p class="grid grid-cols-1 text-center md:grid-cols-2 md:text-left">
                Gig Quality:
                <span class="rating-wrapper flex flex-row gap-3">
                  {!! $serviceData['renderRatingIcons']($serviceData['bandAverageGigQualityRating']) !!}
                </span>
              </p>
            </div>

            <!-- Review Comments -->
            <div class="reviews-block mt-8 flex flex-col gap-4">
              @foreach ($serviceData['recentReviews'] as $review)
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
