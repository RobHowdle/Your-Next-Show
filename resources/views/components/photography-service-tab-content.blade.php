<div class="grid gap-8 lg:grid-cols-3">
  <div class="lg:col-span-2">
    <div class="min-h-[400px] rounded-b-xl rounded-r-xl border border-gray-800 bg-yns_dark_blue/75 p-8 backdrop-blur-sm">
      <!-- About Tab -->
      <div id="about" class="tab-panel hidden">
        <div class="prose-invert prose mb-4 max-w-none">
          <h3 class="mb-4 font-bold text-white">Description</h3>
          {!! $singleService->description ?? 'We\'re currently working on our description. Check back soon!' !!}
        </div>
        @if (!empty($serviceData['types']) || !empty($serviceData['settings']))
          <div class="mt-8 space-y-8">
            <h3 class="text-2xl font-bold text-white">Environments & Settings</h3>
            <p class="text-gray-300">
              We've listed below the types of environments and settings we like to work in. If you have any questions
              please feel free to
              <a href="mailto:{{ $singleService->contact_email }}"
                class="underline transition duration-150 ease-in-out hover:text-yns_yellow">
                contact us
              </a>
            </p>

            <div class="grid gap-6 md:grid-cols-2">
              <!-- Types Section -->
              <!-- Types Section -->
              <div class="rounded-lg border border-gray-700 bg-black/30 p-6 backdrop-blur-sm">
                <h4 class="mb-4 font-heading text-lg font-semibold text-white">Photography Types</h4>
                @if (!empty($serviceData['types']))
                  <ul class="space-y-2">
                    @foreach ($serviceData['types'] as $type)
                      <li class="flex items-center gap-2 text-gray-300">
                        <span class="fas fa-camera text-yns_yellow"></span>
                        {{ $type }}
                      </li>
                    @endforeach
                  </ul>
                @else
                  <p class="text-gray-300">No photography types specified</p>
                @endif
              </div>

              <!-- Settings Section -->
              <div class="rounded-lg border border-gray-700 bg-black/30 p-6 backdrop-blur-sm">
                <h4 class="mb-4 font-heading text-lg font-semibold text-white">Environment Settings</h4>
                @if (!empty($serviceData['settings']))
                  <ul class="space-y-2">
                    @foreach ($serviceData['settings'] as $setting)
                      <li class="flex items-center gap-2 text-gray-300">
                        <span class="fas fa-lightbulb text-yns_yellow"></span>
                        {{ $setting }}
                      </li>
                    @endforeach
                  </ul>
                @else
                  <p class="text-gray-300">No environment settings specified</p>
                @endif
              </div>
            </div>
          </div>
        @else
          <div class="mt-8 text-center">
            <p class="text-gray-300">
              We're still working on listing our environment types and settings. Check back later!
            </p>
          </div>
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
