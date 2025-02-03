<div class="venue-tab-content mt-4 overflow-auto font-sans text-lg text-white">
  <div id="about" class="text-center md:text-left">
    @if (empty($serviceData['description']))
      <p>We're still working on this! Come back later to read about us!</p>
    @else
      <p>{!! $serviceData['description'] !!}</p>
    @endif
  </div>

  <div id="services" class="text-center md:text-left">
    @if (empty($serviceData['services']))
      <p>We haven't got our services listed yet! Come back soon!</p>
    @else
      <p class="text-xl font-bold">Our services include:</p>
      @foreach ($serviceData['services'] as $service)
        <p>{{ ucwords(str_replace(['_', '-'], ' ', ucfirst($service))) }}</p>
      @endforeach
    @endif
  </div>

  <div id="portfolio" class="overflow-auto md:flex md:flex-wrap md:gap-8">
    @if ($serviceData['portfolioImages'] !== '[]')
      <div class="space-4 grid grid-cols-3">
        @foreach ($serviceData['portfolioImages'] as $image)
          <div class="portfolio-image mb-6 w-full min-w-[calc(50%-1rem)] md:mb-0 md:flex-1">
            <img src="{{ asset($image) }}" alt="Portfolio Image" class="h-auto w-full">
          </div>
        @endforeach
      </div>
      @if ($serviceData['portfolioLink'])
        <p class="mt-2">You can view our full portfolio here - <a class="underline hover:text-yns_yellow"
            href="{{ $serviceData['portfolioLink'] }}" target="_blank">{{ $serviceData['portfolioLink'] }}</a></p>
      @endif
    @else
      <p>We haven't got our portfolio set up yet, check back later!</p>
    @endif
  </div>

  @include('components.frontend-package', [
      'singleServiceData' => $serviceData,
      'singleService' => $singleService,
  ])

  <div id="reviews">
    @if ($serviceData['recentReviews'])

      <p class="text-center">Want to know what we're like? Check out our reviews!</p>
      <div class="ratings-block mt-4 flex flex-col items-center gap-4">
        <p class="grid grid-cols-2">Communication:
          <span class="rating-wrapper flex flex-row gap-3">
            {!! $serviceData['renderRatingIcons']($serviceData['designerAverageCommunicationRating']) !!}
          </span>
        </p>
        <p class="grid grid-cols-2">Flexibility:
          <span class="rating-wrapper flex flex-row gap-3">
            {!! $serviceData['renderRatingIcons']($serviceData['designerAverageFlexibilityRating']) !!}

          </span>
        </p>
        <p class="grid grid-cols-2">Professionalism:
          <span class="rating-wrapper flex flex-row gap-3">
            {!! $serviceData['renderRatingIcons']($serviceData['designerAverageProfessionalismRating']) !!}

          </span>
        </p>
        <p class="grid grid-cols-2">Design Quality:
          <span class="rating-wrapper flex flex-row gap-3">
            {!! $serviceData['renderRatingIcons']($serviceData['designerAverageDesignQualityRating']) !!}
          </span>
        </p>
        <p class="grid grid-cols-2">Price:
          <span class="rating-wrapper flex flex-row gap-3">
            {!! $serviceData['renderRatingIcons']($serviceData['designerAveragePriceRating']) !!}
          </span>
        </p>
      </div>

      <div class="reviews-block mt-8 flex flex-col gap-4">
        @foreach ($serviceData['recentReviews'] as $review)
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
    @if ($serviceData['platforms'])
      @foreach ($singleService['platforms'] as $platform)
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
        @elseif($platform['platform'] == 'bluesky')
          <a class="mb-4 mr-2 flex items-center hover:text-yns_yellow" href="{{ $platform['url'] }}" target="_blank">
            <span class="fa-brands fa-bluesky mr-4 h-10"></span> {{ ucfirst($platform['platform']) }}
          </a>
        @elseif($platform['platform'] == 'website')
          <a class="mb-4 mr-2 flex items-center hover:text-yns_yellow" href="{{ $platform['url'] }}" target="_blank">
            <span class="fas fa-globe mr-4 h-10"></span> {{ ucfirst($platform['platform']) }}
          </a>
        @endif
      @endforeach
    @else
      <p>No socials here yet! Check back later!</p>
    @endif
  </div>
</div>
