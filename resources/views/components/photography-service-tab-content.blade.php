<div class="grid gap-8 lg:grid-cols-3">
  <div class="lg:col-span-2">
    <div class="min-h-[400px] rounded-b-xl rounded-r-xl border border-gray-800 bg-yns_dark_blue/75 p-8 backdrop-blur-sm">
      <!-- About Tab -->
      <div id="about" class="tab-panel hidden">
        <div class="prose-invert prose mb-4 max-w-none">
          <h3 class="mb-4 font-bold text-white">Description</h3>
          {!! $singleService->description ?? 'We\'re currently working on our description. Check back soon!' !!}
        </div>
        @if (empty($serviceData['environmentTypes']))
          <div class="prose-invert prose max-w-none">
            <h3 class="mb-4 font-bold text-white">Environment Types</h3>
            <p>We're still working on this! Come back later to read the types of environments we like to work in!
            </p>
          </div>
        @else
          @if ($serviceData['types'])
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
                  @foreach ($serviceData['types'] as $type)
                    <li>{{ $type }}</li>
                  @endforeach
                </ul>
              </div>
            </div>
          @endif
          @if ($serviceData['settings'])
            <div class="group">
              <p class="mt-2 underline">Settings</p>
              <ul>
                @foreach ($serviceData['settings'] as $setting)
                  <li>{{ $setting }}</li>
                @endforeach
              </ul>
            </div>
          @endif
        @endif
      </div>

      <!-- Services Tab -->
      <div id="portfolio" class="tab-panel hidden">
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
                href="{{ $serviceData['portfolioLink'] }}" target="_blank">{{ $serviceData['portfolioLink'] }}</a>
            </p>
          @endif
        @else
          <p>We haven't got our portfolio set up yet, check back later!</p>
        @endif
      </div>

      @include('components.frontend-package', [
          'singleServiceData' => $serviceData,
          'singleService' => $singleService,
      ])

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
                Rate Of Pay:
                <span class="rating-wrapper flex flex-row gap-3">
                  {!! $renderRatingIcons($averageRopRating) !!}
                </span>
              </p>
              <p class="grid grid-cols-1 text-center md:grid-cols-2 md:text-left">
                Promotion:
                <span class="rating-wrapper flex flex-row gap-3">
                  {!! $renderRatingIcons($averagePromotionRating) !!}
                </span>
              </p>
              <p class="grid grid-cols-1 text-center md:grid-cols-2 md:text-left">
                Gig Quality:
                <span class="rating-wrapper flex flex-row gap-3">
                  {!! $renderRatingIcons($averageQualityRating) !!}
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

      <!-- Other Tab -->
      <div id="other" class="tab-panel hidden">
        @if ($singleService->capacity)
          <p class="bold pb-2 text-center text-xl md:text-left md:text-2xl">Other Information you may want to
            know about
            {{ $singleService->name }}.
          </p>
          @if ($singleService->contact_name)
            <p class="text-center md:text-left md:text-base">Person(s) To Speak To:
              {{ $singleService->contact_name }}
            </p>
          @endif
          @if ($singleService->capacity)
            <p class="pb-2 text-center md:text-left">Capacity: {{ $singleService->capacity }}</p>
          @endif
          <p class="bold pb-2 pt-2 text-center text-2xl md:text-left">More Info:</p>
          <p class="pb-2 text-center md:text-left">{!! nl2br(e($singleService->additional_info)) !!}</p>
        @else
          <p class="text-center md:text-left">No Further Information Avaliable</p>
        @endif
      </div>
    </div>
  </div>

  <!-- Sidebar -->
  <div class="space-y-8">
    <div class="rounded-xl border border-gray-800 bg-yns_dark_blue/75 p-6 backdrop-blur-sm">
      <h2 class="mb-6 font-heading text-xl font-bold text-white">Quick Facts</h2>
      <div class="space-y-4">
        @if ($singleService->contact_name)
          <div class="flex items-center gap-3 text-gray-300">
            <span class="fas fa-user text-yns_yellow"></span>
            <span>Contact: {{ $singleService->contact_name }}</span>
          </div>
        @endif
        @if ($singleService->capacity)
          <div class="flex items-center gap-3 text-gray-300">
            <span class="fas fa-users text-yns_yellow"></span>
            <span>Capacity: {{ number_format($singleService->capacity) }}</span>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>
