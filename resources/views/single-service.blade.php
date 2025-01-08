<x-guest-layout>
  <x-slot name="header">
    <h1 class="text-center font-heading text-6xl text-white">
      {{ __('Other') }}
    </h1>
  </x-slot>
  <div class="mx-auto my-6 w-full max-w-screen-2xl pt-32">
    <div class="relative px-2 shadow-md sm:rounded-lg">
      <div
        class="min-w-screen-xl mx-auto max-w-screen-xl bg-opac_8_black px-4 py-4 text-white md:px-6 md:py-4 lg:px-8 lg:py-6 xl:px-10 xl:py-8 2xl:px-12 2xl:py-10 3xl:px-16 3xl:py-12">
        <div class="header flex justify-center md:justify-start md:gap-4">
          @php
            $imagePath = public_path($singleService->logo_url);
          @endphp
          @if ($singleService->logo_url && file_exists($imagePath))
            <img src="{{ asset($singleService->logo_url) }}" alt="{{ $singleService->name }} Logo"
              class="_250img hidden md:block">
          @else
            <img src="{{ asset('images/system/yns_no_image_found.png') }}" alt="No Image"
              class="_250img hidden md:block">
          @endif
          <div class="header-text flex flex-col justify-center gap-2">
            <h1 class="text-sans text-center text-xl md:text-left xl:text-2xl 2xl:text-4xl">{{ $singleService->name }}
            </h1>
            @if ($singleService->location)
              <div class="group flex flex-row items-center justify-center gap-1 md:justify-start xl:gap-2">
                <i class="fa-solid fa-location-dot mr-2"></i>
                <a class="text-md text-center font-sans underline transition duration-150 ease-in-out hover:text-yns_yellow md:text-left lg:text-lg xl:text-xl 2xl:text-2xl"
                  href="javascript:void(0)" target="_blank" id="open-map-link">{{ $singleService->location }}</a>
              </div>
            @endif
            <div class="text-center md:text-left">
              <x-contact-and-social-links :item="$singleService" />
            </div>
            <div class="rating-wrapper flex flex-row justify-center gap-1 md:justify-start xl:gap-2">
              <p class="h-full place-content-center font-sans md:place-content-end">Overall Rating
                @if ($singleService->services == 'Artist')
                  ({{ $singleArtistData['reviewCount'] ?? 0 }})
                @elseif($singleService->services == 'Photography')
                  ({{ $singlePhotographerData['reviewCount'] ?? 0 }})
                @elseif($singleService->services == 'Videographer')
                  ({{ $singleVideographerData['reviewCount'] ?? 0 }})
                @elseif($singleService->services == 'Designer')
                  ({{ $singleDesignerData['reviewCount'] ?? 0 }})
                @endif
              </p>
              <div class="ratings flex">
                {!! $overallReviews[$singleService->id] !!}
              </div>
            </div>
            <div class="leave-review">
              <button
                class="w-full rounded bg-gradient-to-t from-yns_dark_orange to-yns_yellow px-6 py-2 text-sm text-black transition duration-150 ease-in-out hover:bg-yns_yellow md:w-auto"
                data-modal-toggle="review-modal" type="button">Leave a review</button>
            </div>
          </div>
        </div>

        <div class="body">
          @if ($singleService->services == 'Artist')
            <div class="h-auto border-b border-gray-700 py-4">
              <ul class="align-center flex text-center text-sm font-medium text-gray-400 sm:flex-wrap">
                <li class="tab w-full px-4 py-2 sm:px-6 sm:py-3 md:w-auto">
                  <a href="#" data-tab="about" class="tabLinks text-base text-white hover:text-yns_yellow">
                    <span class="fas fa-info-circle mr-2"></span>About
                  </a>
                </li>
                <li class="tab w-full px-4 py-2 sm:px-6 sm:py-3 md:w-auto">
                  <a href="#" data-tab="members" class="tabLinks text-base text-white hover:text-yns_yellow">
                    <span class="fas fa-users mr-2"></span>Members
                  </a>
                </li>
                <li class="tab w-full px-4 py-2 sm:px-6 sm:py-3 md:w-auto">
                  <a href="#" data-tab="music" class="tabLinks text-base text-white hover:text-yns_yellow">
                    <span class="fas fa-music mr-2"></span>Music
                  </a>
                </li>
                <li class="tab w-full px-4 py-2 sm:px-6 sm:py-3 md:w-auto">
                  <a href="#" data-tab="reviews" class="tabLinks text-base text-white hover:text-yns_yellow">
                    <span class="fas fa-star mr-2"></span>Reviews
                  </a>
                </li>
                <li class="tab w-full px-4 py-2 sm:px-6 sm:py-3 md:w-auto">
                  <a href="#" data-tab="socials" class="tabLinks text-base text-white hover:text-yns_yellow">
                    <span class="fas fa-icons mr-2"></span>Socials
                  </a>
                </li>
              </ul>
            </div>
          @elseif ($singleService->services == 'Photography')
            <div class="h-auto border-b border-gray-700 py-4">
              @php
                $spotifyUrl = 'https://open.spotify.com/track/4PTG3Z6ehGkBFwjybzWkR8?si=23c6845e25df4307';
              @endphp
              <ul class="align-center flex text-center text-sm font-medium text-gray-400 sm:flex-wrap">
                <li class="tab w-full px-4 py-2 sm:px-6 sm:py-3 md:w-auto">
                  <a href="#" data-tab="overview" class="tabLinks text-base text-white hover:text-yns_yellow">
                    <span class="fas fa-info-circle mr-2"></span>Overview
                  </a>
                </li>
                <li class="tab w-full px-4 py-2 sm:px-6 sm:py-3 md:w-auto">
                  <a href="#" data-tab="services" class="tabLinks text-base text-white hover:text-yns_yellow">
                    <span class="fas fa-cog mr-2"></span>Services
                  </a>
                </li>
                <li class="tab w-full px-4 py-2 sm:px-6 sm:py-3 md:w-auto">
                  <a href="#" data-tab="portfolio" class="tabLinks text-base text-white hover:text-yns_yellow">
                    <span class="fas fa-folder mr-2"></span>Portfolio
                  </a>
                </li>
                <li class="tab w-full px-4 py-2 sm:px-6 sm:py-3 md:w-auto">
                  <a href="#" data-tab="reviews" class="tabLinks text-base text-white hover:text-yns_yellow">
                    <span class="fas fa-star mr-2"></span>Reviews
                  </a>
                </li>
                <li class="tab w-full px-4 py-2 sm:px-6 sm:py-3 md:w-auto">
                  <a href="#" data-tab="socials" class="tabLinks text-base text-white hover:text-yns_yellow">
                    <span class="fas fa-plus mr-2"></span>Socials
                  </a>
                </li>
              </ul>
            </div>
          @elseif ($singleService->services == 'Videography')
            <div class="h-auto border-b border-gray-700 py-4">
              @php
                $spotifyUrl = 'https://open.spotify.com/track/4PTG3Z6ehGkBFwjybzWkR8?si=23c6845e25df4307';
              @endphp
              <ul class="align-center flex text-center text-sm font-medium text-gray-400 sm:flex-wrap">
                <li class="tab w-full px-4 py-2 sm:px-6 sm:py-3 md:w-auto">
                  <a href="#" data-tab="overview" class="tabLinks text-base text-white hover:text-yns_yellow">
                    <span class="fas fa-info-circle mr-2"></span>Overview
                  </a>
                </li>
                <li class="tab w-full px-4 py-2 sm:px-6 sm:py-3 md:w-auto">
                  <a href="#" data-tab="services" class="tabLinks text-base text-white hover:text-yns_yellow">
                    <span class="fas fa-cog mr-2"></span>Services
                  </a>
                </li>
                <li class="tab w-full px-4 py-2 sm:px-6 sm:py-3 md:w-auto">
                  <a href="#" data-tab="portfolio" class="tabLinks text-base text-white hover:text-yns_yellow">
                    <span class="fas fa-folder mr-2"></span>Portfolio
                  </a>
                </li>
                <li class="tab w-full px-4 py-2 sm:px-6 sm:py-3 md:w-auto">
                  <a href="#" data-tab="reviews" class="tabLinks text-base text-white hover:text-yns_yellow">
                    <span class="fas fa-star mr-2"></span>Reviews
                  </a>
                </li>
                <li class="tab w-full px-4 py-2 sm:px-6 sm:py-3 md:w-auto">
                  <a href="#" data-tab="socials" class="tabLinks text-base text-white hover:text-yns_yellow">
                    <span class="fas fa-plus mr-2"></span>Socials
                  </a>
                </li>
              </ul>
            </div>
          @elseif ($singleService->services == 'Designer')
            <div class="h-auto border-b border-gray-700 py-4">
              @php
                $spotifyUrl = 'https://open.spotify.com/track/4PTG3Z6ehGkBFwjybzWkR8?si=23c6845e25df4307';
              @endphp
              <ul class="align-center flex text-center text-sm font-medium text-gray-400 sm:flex-wrap">
                <li class="tab w-full px-4 py-2 sm:px-6 sm:py-3 md:w-auto">
                  <a href="#" data-tab="overview" class="tabLinks text-base text-white hover:text-yns_yellow">
                    <span class="fas fa-info-circle mr-2"></span>Overview
                  </a>
                </li>
                <li class="tab w-full px-4 py-2 sm:px-6 sm:py-3 md:w-auto">
                  <a href="#" data-tab="services" class="tabLinks text-base text-white hover:text-yns_yellow">
                    <span class="fas fa-cog mr-2"></span>Services
                  </a>
                </li>
                </li>
                <li class="tab w-full px-4 py-2 sm:px-6 sm:py-3 md:w-auto">
                  <a href="#" data-tab="portfolio" class="tabLinks text-base text-white hover:text-yns_yellow">
                    <span class="fas fa-folder mr-2"></span>Portfolio
                  </a>
                </li>
                <li class="tab w-full px-4 py-2 sm:px-6 sm:py-3 md:w-auto">
                  <a href="#" data-tab="reviews" class="tabLinks text-base text-white hover:text-yns_yellow">
                    <span class="fas fa-star mr-2"></span>Reviews
                  </a>
                </li>
                <li class="tab w-full px-4 py-2 sm:px-6 sm:py-3 md:w-auto">
                  <a href="#" data-tab="socials" class="tabLinks text-base text-white hover:text-yns_yellow">
                    <span class="fas fa-plus mr-2"></span>Socials
                  </a>
                </li>
              </ul>
            </div>
          @endif

          @if ($singleService->services == 'Artist')
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
                  $streamUrls = $streamUrls ?? new stdClass();
                  $spotifyUrl = isset($streamUrls->spotify[0])
                      ? $streamUrls->spotify[0]
                      : 'https://open.spotify.com/track/4PTG3Z6ehGkBFwjybzWkR8?si=23c6845e25df4307';
                  $otherLinks = [];

                  foreach ($streamUrls as $platform => $links) {
                      if ($platform !== 'spotify' && isset($links[0]) && $links[0] !== null) {
                          $otherLinks[] = ['platform' => $platform, 'url' => $links[0]];
                      }
                  }
                @endphp

                @if ($spotifyUrl)
                  <p class="my-4 text-center">Listen on Spotify:</p>
                  <div id="embed-iframe" class="mb-4 text-center">
                    <a href="{{ $spotifyUrl }}" target="_blank" class="text-blue-600 underline"
                      rel="noopener noreferrer">
                      Open in Spotify
                    </a>
                  </div>
                @endif

                @php
                  $linkCount = count($otherLinks);
                @endphp
                @if ($linkCount > 0)
                  <p class="my-4 text-center text-2xl font-bold">Also Catch Us On</p>
                  <div
                    class="streaming-platforms grid-cols-{{ $linkCount }} grid place-items-center items-center gap-4">
                    @foreach ($otherLinks as $link)
                      <a href="{{ $link['url'] }}" target="_blank" class="streaming-platforms"
                        rel="noopener noreferrer">
                        <img
                          src="{{ asset('storage/images/system/streaming/' . strtolower($link['platform']) . '.png') }}"
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
                      <a class="mb-4 mr-2 flex items-center hover:text-yns_yellow" href="{{ $platform['url'] }}"
                        target="_blank">
                        <span class="fab fa-facebook mr-4 h-10"></span> {{ ucfirst($platform['platform']) }}
                      </a>
                    @elseif($platform['platform'] == 'twitter')
                      <a class="mb-4 mr-2 flex items-center hover:text-yns_yellow" href="{{ $platform['url'] }}"
                        target="_blank">
                        <span class="fab fa-twitter mr-4 h-10"></span> {{ ucfirst($platform['platform']) }}
                      </a>
                    @elseif($platform['platform'] == 'instagram')
                      <a class="mb-4 mr-2 flex items-center hover:text-yns_yellow" href="{{ $platform['url'] }}"
                        target="_blank">
                        <span class="fab fa-instagram mr-4 h-10"></span> {{ ucfirst($platform['platform']) }}
                      </a>
                    @elseif($platform['platform'] == 'snapchat')
                      <a class="mb-4 mr-2 flex items-center hover:text-yns_yellow" href="{{ $platform['url'] }}"
                        target="_blank">
                        <span class="fab fa-snapchat-ghost mr-4 h-10"></span> {{ ucfirst($platform['platform']) }}
                      </a>
                    @elseif($platform['platform'] == 'tiktok')
                      <a class="mb-4 mr-2 flex items-center hover:text-yns_yellow" href="{{ $platform['url'] }}"
                        target="_blank">
                        <span class="fab fa-tiktok mr-4 h-10"></span> {{ ucfirst($platform['platform']) }}
                      </a>
                    @elseif($platform['platform'] == 'youtube')
                      <a class="mb-4 mr-2 flex items-center hover:text-yns_yellow" href="{{ $platform['url'] }}"
                        target="_blank">
                        <span class="fab fa-youtube mr-4 h-10"></span> {{ ucfirst($platform['platform']) }}
                      </a>
                    @endif
                  @endforeach
                @else
                  <p>No socials here yet! Check back later!</p>
                @endif
              </div>
            </div>
          @elseif ($singleService->services == 'Photography')
            <div class="venue-tab-content mt-4 overflow-auto font-sans text-lg text-white">
              <div id="overview" class="text-center md:text-left">
                @if (empty($singlePhotographerData['description']))
                  <p>We're still working on this! Come back later to read about us!</p>
                @else
                  <p class="text-xl font-bold">About Us</p>
                  <p>{{ $singlePhotographerData['description'] }}</p>
                @endif

                @if (empty($singlePhotographerData['environmentTypes']))
                  <p>We're still working on this! Come back later to read the types of environments we like to work in!
                  </p>
                @else
                  @if ($singlePhotographerData['types'])
                    <p class="mt-4 text-xl font-bold">Environments & Types</p>
                    <p>We've listed below the types of environments and settings we like to work in. If
                      you have any questions please feel free to <a
                        class="underline transition duration-150 ease-in-out hover:text-yns_yellow"
                        href="mailto:{{ $singleService->contact_email }}">contact
                        us</a></p>
                    <div class="mt-4 grid grid-cols-2 gap-4">
                      <div class="group">
                        <p class="mt-2 underline">Types</p>
                        <ul>
                          @foreach ($singlePhotographerData['types'] as $type)
                            <li>{{ $type }}</li>
                          @endforeach
                        </ul>
                      </div>
                    </div>
                  @endif
                  @if ($singlePhotographerData['settings'])
                    <div class="group">
                      <p class="mt-2 underline">Settings</p>
                      <ul>
                        @foreach ($singlePhotographerData['settings'] as $setting)
                          <li>{{ $setting }}</li>
                        @endforeach
                      </ul>
                    </div>
                  @endif
                @endif
              </div>

              <div id="services" class="overflow-auto md:flex md:flex-wrap md:gap-8">
                @if ($singlePhotographerData['packages'] != [])
                  @foreach ($singlePhotographerData['packages'] as $package)
                    @foreach ($package as $p)
                      <div class="service mb-6 min-w-[calc(50%-1rem)] md:mb-0 md:flex-1">
                        <p class="font-semibold">{{ $p->title }}</p>

                        @if (is_array($p->details))
                          <ul class="list-inside list-disc">
                            @foreach ($p->details as $bullet)
                              <li>{{ $bullet }}</li>
                            @endforeach
                          </ul>

                          <p class="mt-4">We've listed below the types of environments and settings we like to work
                            in. If you have any questions please feel free to <a
                              class="underline transition duration-150 ease-in-out hover:text-yns_yellow"
                              href="mailto:{{ $singleService->contact_email }}">contact
                              us</a>
                          </p>
                        @endif

                        <p class="mt-4 text-lg font-bold">From {{ formatCurrency($p->price) }}</p>
                      </div>
                    @endforeach
                  @endforeach
                  <p class="mt-4">All services are subject to location and travel costs. Please <a
                      class="underline transition duration-150 ease-in-out hover:text-yns_yellow"
                      href="mailto:{{ $singleService->contact_email }}">contact
                      us</a> with any
                    queries.</p>
                  @if ($singleService->portfolio_link)
                    <p class="mt-2">You can view our portfolio by <a class="underline hover:text-yns_yellow"
                        href="{{ $singleService->portfolio_link }}" target="_blank">clicking here.</a></p>
                  @endif
                @else
                  <p>We haven't got our services set up yet! Come back soon!</p>
                @endif
              </div>

              <div id="portfolio" class="overflow-auto md:flex md:flex-wrap md:gap-8">
                @if ($singlePhotographerData['portfolioImages'] != '[]')
                  @foreach ($singlePhotographerData['portfolioImages'] as $image)
                    <div class="portfolio-image mb-6 min-w-[calc(50%-1rem)] md:mb-0 md:flex-1">
                      <img src="{{ asset($image) }}" alt="Portfolio Image" class="h-auto w-full">
                    </div>
                  @endforeach
                  @if ($singlePhotographerData['portfolioLink'])
                    <p class="mt-2">You can view our full portfolio here - <a
                        class="underline hover:text-yns_yellow" href="{{ $singlePhotographerData['portfolioLink'] }}"
                        target="_blank">{{ $singlePhotographerData['portfolioLink'] }}</a></p>
                  @endif
                @else
                  <p>We haven't got our portfolio set up yet, check back later!</p>
                @endif
              </div>

              <div id="reviews">
                @if ($singleService->recentReviews)
                  <p class="text-center">Want to know what we're like? Check out our reviews!</p>
                  <div class="ratings-block mt-4 flex flex-col items-center gap-4">
                    <p class="grid grid-cols-2">Communication:
                      <span class="rating-wrapper flex flex-row gap-3">
                        {!! $singlePhotographerData['renderRatingIcons'](
                            $singlePhotographerData['photographerAverageCommunicationRating'],
                        ) !!}
                      </span>
                    </p>
                    <p class="grid grid-cols-2">Flexibility:
                      <span class="rating-wrapper flex flex-row gap-3">
                        {!! $singlePhotographerData['renderRatingIcons'](
                            $singlePhotographerData['photographerAverageFlexibilityRating'],
                        ) !!}

                      </span>
                    </p>
                    <p class="grid grid-cols-2">Professionalism:
                      <span class="rating-wrapper flex flex-row gap-3">
                        {!! $singlePhotographerData['renderRatingIcons'](
                            $singlePhotographerData['photographerAverageProfessionalismRating'],
                        ) !!}

                      </span>
                    </p>
                    <p class="grid grid-cols-2">Photo Quality:
                      <span class="rating-wrapper flex flex-row gap-3">
                        {!! $singlePhotographerData['renderRatingIcons'](
                            $singlePhotographerData['photographerAveragePhotoQualityRating'],
                        ) !!}
                      </span>
                    </p>
                    <p class="grid grid-cols-2">Price:
                      <span class="rating-wrapper flex flex-row gap-3">
                        {!! $singlePhotographerData['renderRatingIcons'](
                            $singlePhotographerData['photographerAveragePhotoQualityRating'],
                        ) !!}
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
                      <a class="mb-4 mr-2 flex items-center hover:text-yns_yellow" href="{{ $platform['url'] }}"
                        target="_blank">
                        <span class="fab fa-facebook mr-4 h-10"></span> {{ ucfirst($platform['platform']) }}
                      </a>
                    @elseif($platform['platform'] == 'twitter')
                      <a class="mb-4 mr-2 flex items-center hover:text-yns_yellow" href="{{ $platform['url'] }}"
                        target="_blank">
                        <span class="fab fa-twitter mr-4 h-10"></span> {{ ucfirst($platform['platform']) }}
                      </a>
                    @elseif($platform['platform'] == 'instagram')
                      <a class="mb-4 mr-2 flex items-center hover:text-yns_yellow" href="{{ $platform['url'] }}"
                        target="_blank">
                        <span class="fab fa-instagram mr-4 h-10"></span> {{ ucfirst($platform['platform']) }}
                      </a>
                    @elseif($platform['platform'] == 'snapchat')
                      <a class="mb-4 mr-2 flex items-center hover:text-yns_yellow" href="{{ $platform['url'] }}"
                        target="_blank">
                        <span class="fab fa-snapchat-ghost mr-4 h-10"></span> {{ ucfirst($platform['platform']) }}
                      </a>
                    @elseif($platform['platform'] == 'tiktok')
                      <a class="mb-4 mr-2 flex items-center hover:text-yns_yellow" href="{{ $platform['url'] }}"
                        target="_blank">
                        <span class="fab fa-tiktok mr-4 h-10"></span> {{ ucfirst($platform['platform']) }}
                      </a>
                    @elseif($platform['platform'] == 'youtube')
                      <a class="mb-4 mr-2 flex items-center hover:text-yns_yellow" href="{{ $platform['url'] }}"
                        target="_blank">
                        <span class="fab fa-youtube mr-4 h-10"></span> {{ ucfirst($platform['platform']) }}
                      </a>
                    @endif
                  @endforeach
                @else
                  <p>No socials here yet! Check back later!</p>
                @endif
              </div>
            </div>
          @elseif ($singleService->services == 'Designer')
            <div class="venue-tab-content mt-4 overflow-auto font-sans text-lg text-white">
              <div id="about" class="text-center md:text-left">
                @if (empty($singleDesignerData['description']))
                  <p>We're still working on this! Come back later to read about us!</p>
                @else
                  <p>{{ $singleDesignerData['description'] }}</p>
                @endif
              </div>

              <div id="services" class="overflow-auto md:flex md:flex-wrap md:gap-8">
                @if ($singleDesignerData['packages'])
                  @foreach ($singleDesignerData['packages'] as $package)
                    @foreach ($package as $p)
                      <div class="service mb-6 min-w-[calc(50%-1rem)] md:mb-0 md:flex-1">
                        <p class="font-semibold">{{ $p->title }}</p>

                        @if (is_array($p->details))
                          <ul class="list-inside list-disc">
                            @foreach ($p->details as $bullet)
                              <li>{{ $bullet }}</li>
                            @endforeach
                          </ul>
                        @endif

                        <p class="mt-4 text-lg font-bold">From {{ formatCurrency($p->price) }}</p>
                      </div>
                    @endforeach
                  @endforeach
                  <p class="mt-4">All services are subject to location and travel costs. Please <a
                      class="underline hover:text-yns_yellow"
                      href="mailto:{{ $singleService->contact_email }}">contact
                      us</a> with any
                    queries.</p>
                  @if ($singleService->portfolio_link)
                    <p class="mt-2">You can view our portfolio by <a class="underline hover:text-yns_yellow"
                        href="{{ $singleService->portfolio_link }}" target="_blank">clicking here.</a></p>
                  @endif
                @else
                  <p>We haven't got our services set up yet! Come back soon!</p>
                @endif
              </div>

              <div id="portfolio" class="overflow-auto md:flex md:flex-wrap md:gap-8">
                @if ($singleDesignerData['portfolioImages'] !== '[]')
                  @foreach ($singleDesignerData['portfolioImages'] as $image)
                    <div class="portfolio-image mb-6 min-w-[calc(50%-1rem)] md:mb-0 md:flex-1">
                      <img src="{{ asset($image) }}" alt="Portfolio Image" class="h-auto w-full">
                    </div>
                  @endforeach
                  @if ($singleDesignerData['portfolioLink'])
                    <p class="mt-2">You can view our full portfolio here - <a
                        class="underline hover:text-yns_yellow" href="{{ $singleDesignerData['portfolioLink'] }}"
                        target="_blank">{{ $singleDesignerData['portfolioLink'] }}</a></p>
                  @endif
                @else
                  <p>We haven't got our portfolio set up yet, check back later!</p>
                @endif
              </div>

              <div id="reviews">
                @if ($singleService->recentReviews)

                  <p class="text-center">Want to know what we're like? Check out our reviews!</p>
                  <div class="ratings-block mt-4 flex flex-col items-center gap-4">
                    <p class="grid grid-cols-2">Communication:
                      <span class="rating-wrapper flex flex-row gap-3">
                        {!! $singleDesignerData['renderRatingIcons']($singleDesignerData['designerAverageCommunicationRating']) !!}
                      </span>
                    </p>
                    <p class="grid grid-cols-2">Flexibility:
                      <span class="rating-wrapper flex flex-row gap-3">
                        {!! $singleDesignerData['renderRatingIcons']($singleDesignerData['designerAverageFlexibilityRating']) !!}

                      </span>
                    </p>
                    <p class="grid grid-cols-2">Professionalism:
                      <span class="rating-wrapper flex flex-row gap-3">
                        {!! $singleDesignerData['renderRatingIcons']($singleDesignerData['designerAverageProfessionalismRating']) !!}

                      </span>
                    </p>
                    <p class="grid grid-cols-2">Design Quality:
                      <span class="rating-wrapper flex flex-row gap-3">
                        {!! $singleDesignerData['renderRatingIcons']($singleDesignerData['designerAverageDesignQualityRating']) !!}
                      </span>
                    </p>
                    <p class="grid grid-cols-2">Price:
                      <span class="rating-wrapper flex flex-row gap-3">
                        {!! $singleDesignerData['renderRatingIcons']($singleDesignerData['designerAveragePriceRating']) !!}
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
                      <a class="mb-4 mr-2 flex items-center hover:text-yns_yellow" href="{{ $platform['url'] }}"
                        target="_blank">
                        <span class="fab fa-facebook mr-4 h-10"></span> {{ ucfirst($platform['platform']) }}
                      </a>
                    @elseif($platform['platform'] == 'twitter')
                      <a class="mb-4 mr-2 flex items-center hover:text-yns_yellow" href="{{ $platform['url'] }}"
                        target="_blank">
                        <span class="fab fa-twitter mr-4 h-10"></span> {{ ucfirst($platform['platform']) }}
                      </a>
                    @elseif($platform['platform'] == 'instagram')
                      <a class="mb-4 mr-2 flex items-center hover:text-yns_yellow" href="{{ $platform['url'] }}"
                        target="_blank">
                        <span class="fab fa-instagram mr-4 h-10"></span> {{ ucfirst($platform['platform']) }}
                      </a>
                    @elseif($platform['platform'] == 'snapchat')
                      <a class="mb-4 mr-2 flex items-center hover:text-yns_yellow" href="{{ $platform['url'] }}"
                        target="_blank">
                        <span class="fab fa-snapchat-ghost mr-4 h-10"></span> {{ ucfirst($platform['platform']) }}
                      </a>
                    @elseif($platform['platform'] == 'tiktok')
                      <a class="mb-4 mr-2 flex items-center hover:text-yns_yellow" href="{{ $platform['url'] }}"
                        target="_blank">
                        <span class="fab fa-tiktok mr-4 h-10"></span> {{ ucfirst($platform['platform']) }}
                      </a>
                    @elseif($platform['platform'] == 'youtube')
                      <a class="mb-4 mr-2 flex items-center hover:text-yns_yellow" href="{{ $platform['url'] }}"
                        target="_blank">
                        <span class="fab fa-youtube mr-4 h-10"></span> {{ ucfirst($platform['platform']) }}
                      </a>
                    @endif
                  @endforeach
                @else
                  <p>No socials here yet! Check back later!</p>
                @endif
              </div>
            </div>
          @elseif ($singleService->services == 'Videography')
            <div class="venue-tab-content mt-4 overflow-auto font-sans text-lg text-white">
              <div id="overview" class="text-center md:text-left">
                @if (empty($singleVideographerData['description']))
                  <p>We're still working on this! Come back later to read about us!</p>
                @else
                  <p>{{ $singleVideographerData['description'] }}</p>
                @endif

                @if (empty($singlePhotographerData['environmentTypes']))
                  <p>We're still working on this! Come back later to read the types of environments we like to work in!
                  </p>
                @else
                  @if ($singleVideographerData['types'])
                    <p class="mt-4 text-xl font-bold">Environments & Types</p>
                    <p>We've listed below the types of environments and settings we like to work in. If
                      you have any questions please feel free to <a
                        class="underline transition duration-150 ease-in-out hover:text-yns_yellow"
                        href="mailto:{{ $singleService->contact_email }}">contact
                        us</a></p>
                    <div class="mt-4 grid grid-cols-2 gap-4">
                      <div class="group">
                        <p class="mt-2 underline">Types</p>
                        <ul>
                          @foreach ($singleVideographerData['types'] as $type)
                            <li>{{ $type }}</li>
                          @endforeach
                        </ul>
                      </div>
                    </div>
                  @endif
                  @if ($singleVideographerData['settings'])
                    <div class="group">
                      <p class="mt-2 underline">Settings</p>
                      <ul>
                        @foreach ($singleVideographerData['settings'] as $setting)
                          <li>{{ $setting }}</li>
                        @endforeach
                      </ul>
                    </div>
                  @endif
                @endif
              </div>

              <div id="services" class="overflow-auto md:flex md:flex-wrap md:gap-8">
                @if (isset($singleVideographerData['packages']) && $singleVideographerData['packages'])
                  @foreach ($singleVideographerData['packages'] as $package)
                    @foreach ($package as $p)
                      <div class="service mb-6 min-w-[calc(50%-1rem)] md:mb-0 md:flex-1">
                        <p class="font-semibold">{{ $p->title }}</p>

                        @if (is_array($p->details))
                          <ul class="list-inside list-disc">
                            @foreach ($p->details as $bullet)
                              <li>{{ $bullet }}</li>
                            @endforeach
                          </ul>
                        @endif

                        <p class="mt-4 text-lg font-bold">From {{ formatCurrency($p->price) }}</p>
                      </div>
                    @endforeach
                  @endforeach
                  <p class="mt-4">All services are subject to location and travel costs. Please <a
                      class="underline hover:text-yns_yellow"
                      href="mailto:{{ $singleService->contact_email }}">contact
                      us</a> with any
                    queries.</p>
                  @if ($singleService->portfolio_link)
                    <p class="mt-2">You can view our portfolio by <a class="underline hover:text-yns_yellow"
                        href="{{ $singleService->portfolio_link }}" target="_blank">clicking here.</a></p>
                  @endif
                @else
                  <p>We haven't got our services set up yet! Come back soon!</p>
                @endif
              </div>

              <div id="portfolio" class="overflow-auto md:flex md:flex-wrap md:gap-8">
                @if (isset($singleVideographerData['portfolioImages']) && $singleVideographerData['portfolioImages'])
                  @foreach ($singleDesignerData['portfolioImages'] as $image)
                    <div class="portfolio-image mb-6 min-w-[calc(50%-1rem)] md:mb-0 md:flex-1">
                      <img src="{{ asset($image) }}" alt="Portfolio Image" class="h-auto w-full">
                    </div>
                  @endforeach
                  @if ($singleVideographerData['portfolioLink'])
                    <p class="mt-2">You can view our full portfolio here - <a
                        class="underline hover:text-yns_yellow" href="{{ $singleVideographerData['portfolioLink'] }}"
                        target="_blank">{{ $singleVideographerData['portfolioLink'] }}</a></p>
                  @endif
                @else
                  <p>We haven't got our portfolio set up yet, check back later!</p>
                @endif
              </div>

              <div id="reviews">
                @if ($singleService->recentReviews)
                  <p class="text-center">Want to know what we're like? Check out our reviews!</p>
                  <div class="ratings-block mt-4 flex flex-col items-center gap-4">
                    <p class="grid grid-cols-2">Communication:
                      <span class="rating-wrapper flex flex-row gap-3">
                        @if (isset($singleVideographerData['renderRatingIcons']) && is_callable($singleVideographerData['renderRatingIcons']))
                          {!! $singleVideographerData['renderRatingIcons'](
                              $singleVideographerData['videographyAverageCommunicationRating'],
                          ) !!}
                        @endif
                      </span>
                    </p>
                    <p class="grid grid-cols-2">Flexibility:
                      <span class="rating-wrapper flex flex-row gap-3">
                        @if (isset($singleVideographerData['renderRatingIcons']) && is_callable($singleVideographerData['renderRatingIcons']))
                          {!! $singleVideographerData['renderRatingIcons']($singleVideographerData['videographyAverageFlexibilityRating']) !!}
                        @endif
                      </span>
                    </p>
                    <p class="grid grid-cols-2">Professionalism:
                      <span class="rating-wrapper flex flex-row gap-3">
                        @if (isset($singleVideographerData['renderRatingIcons']) && is_callable($singleVideographerData['renderRatingIcons']))
                          {!! $singleVideographerData['renderRatingIcons'](
                              $singleVideographerData['videographyAverageProfessionalismRating'],
                          ) !!}
                        @endif
                      </span>
                    </p>
                    <p class="grid grid-cols-2">Video Quality:
                      <span class="rating-wrapper flex flex-row gap-3">
                        @if (isset($singleVideographerData['renderRatingIcons']) && is_callable($singleVideographerData['renderRatingIcons']))
                          {!! $singleVideographerData['renderRatingIcons'](
                              $singleVideographerData['videographyAverageVideoQualityRating'],
                          ) !!}
                        @endif
                      </span>
                    </p>
                    <p class="grid grid-cols-2">Price:
                      <span class="rating-wrapper flex flex-row gap-3">
                        @if (isset($singleVideographerData['renderRatingIcons']) && is_callable($singleVideographerData['renderRatingIcons']))
                          {!! $singleVideographerData['renderRatingIcons']($singleVideographerData['videographyAveragePriceRating']) !!}
                        @endif
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
                      <a class="mb-4 mr-2 flex items-center hover:text-yns_yellow" href="{{ $platform['url'] }}"
                        target="_blank">
                        <span class="fab fa-facebook mr-4 h-10"></span> {{ ucfirst($platform['platform']) }}
                      </a>
                    @elseif($platform['platform'] == 'twitter')
                      <a class="mb-4 mr-2 flex items-center hover:text-yns_yellow" href="{{ $platform['url'] }}"
                        target="_blank">
                        <span class="fab fa-twitter mr-4 h-10"></span> {{ ucfirst($platform['platform']) }}
                      </a>
                    @elseif($platform['platform'] == 'instagram')
                      <a class="mb-4 mr-2 flex items-center hover:text-yns_yellow" href="{{ $platform['url'] }}"
                        target="_blank">
                        <span class="fab fa-instagram mr-4 h-10"></span> {{ ucfirst($platform['platform']) }}
                      </a>
                    @elseif($platform['platform'] == 'snapchat')
                      <a class="mb-4 mr-2 flex items-center hover:text-yns_yellow" href="{{ $platform['url'] }}"
                        target="_blank">
                        <span class="fab fa-snapchat-ghost mr-4 h-10"></span> {{ ucfirst($platform['platform']) }}
                      </a>
                    @elseif($platform['platform'] == 'tiktok')
                      <a class="mb-4 mr-2 flex items-center hover:text-yns_yellow" href="{{ $platform['url'] }}"
                        target="_blank">
                        <span class="fab fa-tiktok mr-4 h-10"></span> {{ ucfirst($platform['platform']) }}
                      </a>
                    @elseif($platform['platform'] == 'youtube')
                      <a class="mb-4 mr-2 flex items-center hover:text-yns_yellow" href="{{ $platform['url'] }}"
                        target="_blank">
                        <span class="fab fa-youtube mr-4 h-10"></span> {{ ucfirst($platform['platform']) }}
                      </a>
                    @endif
                  @endforeach
                @else
                  <p>No socials here yet! Check back later!</p>
                @endif
              </div>
            </div>
          @endif
          {{-- <x-suggestion-block :promoterWithHighestRating="$promoterWithHighestRating" :photographerWithHighestRating="$photographerWithHighestRating" :videographerWithHighestRating="$videographerWithHighestRating" :bandWithHighestRating="$bandWithHighestRating"
            :designerWithHighestRating="$designerWithHighestRating" /> --}}
          <x-review-modal title="{{ $singleService->name }}" route="submit-venue-review"
            profileId="{{ $singleService->id }}" />
        </div>
      </div>
    </div>
  </div>
</x-guest-layout>
<script src="https://open.spotify.com/embed/iframe-api/v1" async></script>
<script>
  window.onSpotifyIframeApiReady = (IFrameAPI) => {
    const element = document.getElementById('embed-iframe');
    if (!element) {
      // console.error('Embed iframe element not found.');
      return;
    }
    const options = {
      uri: `{{ $spotifyUrl ?? 'https://open.spotify.com/track/4PTG3Z6ehGkBFwjybzWkR8?si=23c6845e25df4307' }}`
    };
    const callback = (EmbedController) => {};
    IFrameAPI.createController(element, options, callback);
  };

  document.addEventListener("DOMContentLoaded", function() {
    const openMapLink = document.getElementById("open-map-link");
    const promoterLatitude = "{{ $singleService->latitude }}";
    const promoterLongitude = "{{ $singleService->longitude }}";

    // Function to detect if the user is on a mobile device
    function isMobileDevice() {
      return /Mobi|Android/i.test(navigator.userAgent);
    }

    // Function to open the map
    function openMap() {
      // First, check if it's a mobile device
      if (isMobileDevice()) {
        // For mobile, try geo URI
        const geoURI = `geo:${promoterLatitude},${promoterLongitude}`;
        window.location.href = geoURI;
      } else {
        // If not mobile, fall back to Google Maps
        window.open(`https://www.google.com/maps?q=${promoterLatitude},${promoterLongitude}`, '_blank');
      }
    }

    // Attach click event listeners
    openMapLink.addEventListener("click", openMap);
  });
</script>
