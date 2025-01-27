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

  <div id="portfolio" class="overflow-auto md:flex md:flex-wrap md:gap-8">
    @if (!empty($singlePhotographerData['portfolioImages'] ?? []))
      @foreach ($singlePhotographerData['portfolioImages'] as $image)
        <div class="portfolio-image mb-6 min-w-[calc(50%-1rem)] md:mb-0 md:flex-1">
          <img src="{{ asset($image) }}" alt="Portfolio Image" class="h-auto w-full">
        </div>
      @endforeach
      @if ($singlePhotographerData['portfolioLink'])
        <p class="mt-2">You can view our full portfolio here - <a class="underline hover:text-yns_yellow"
            href="{{ $singlePhotographerData['portfolioLink'] }}"
            target="_blank">{{ $singlePhotographerData['portfolioLink'] }}</a></p>
      @endif
    @else
      <p>We haven't got our portfolio set up yet, check back later!</p>
    @endif
  </div>

  @include('components.frontend-package', [
      'singleServiceData' => $singlePhotographerData,
      'singleService' => $singleService,
  ])

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
