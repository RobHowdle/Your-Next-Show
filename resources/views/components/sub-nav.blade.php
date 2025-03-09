<div class="border-t border-gray-800 bg-black/80 backdrop-blur-sm">
  <div class="mx-auto max-w-[1920px] rounded-lg px-4 sm:px-6 lg:px-8">
    <div class="flex min-h-[48px] items-center py-2">
      <div class="grid w-full grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 lg:gap-2 xl:gap-4">
        @php
          $stats = match ($userType) {
              'promoter' => [
                  [
                      'icon' =>
                          'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
                      'label' => 'Events YTD',
                      'value' => $eventsCountPromoterYtd,
                  ],
                  [
                      'icon' =>
                          'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                      'label' => 'Total Profit YTD',
                      'value' => formatCurrency($totalProfitsPromoterYtd) ?? '£0.00',
                  ],
                  [
                      'icon' =>
                          'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z',
                      'label' => 'Overall Rating',
                      'value' => $overallRatingPromoter,
                      'isRating' => true,
                  ],
              ],
              'artist' => [
                  [
                      'icon' =>
                          'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.697l-2-2V7m0 11V9.697m0 0l-2-2V7m2 13h-2',
                      'label' => 'Gigs YTD',
                      'value' => $gigsCountBandYtd,
                  ],
                  [
                      'icon' =>
                          'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                      'label' => 'Total Profit YTD',
                      'value' => formatCurrency($totalProfitsBandYtd) ?? '£0.00',
                  ],
                  [
                      'icon' =>
                          'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z',
                      'label' => 'Overall Rating',
                      'value' => $overallRatingBand,
                      'isRating' => true,
                  ],
              ],
              'designer' => [
                  [
                      'icon' =>
                          'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10',
                      'label' => 'Total Projects',
                      'value' => $jobsCountDesignerYTD,
                  ],
                  [
                      'icon' =>
                          'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                      'label' => 'Total Profit YTD',
                      'value' => formatCurrency($totalProfitsDesignerYtd) ?? '£0.00',
                  ],
                  [
                      'icon' =>
                          'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z',
                      'label' => 'Overall Rating',
                      'value' => $overallRatingDesigner,
                      'isRating' => true,
                  ],
              ],
              'venue' => [
                  [
                      'icon' =>
                          'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
                      'label' => 'Events YTD',
                      'value' => $eventsCountVenueYtd,
                  ],
                  [
                      'icon' =>
                          'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                      'label' => 'Total Profit YTD',
                      'value' => formatCurrency($totalProfitsVenueYtd) ?? '£0.00',
                  ],
                  [
                      'icon' =>
                          'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z',
                      'label' => 'Overall Rating',
                      'value' => $overallRatingVenue,
                      'isRating' => true,
                  ],
              ],
              'photographer' => [
                  [
                      'icon' =>
                          'M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z M15 13a3 3 0 11-6 0 3 3 0 016 0z',
                      'label' => 'Jobs YTD',
                      'value' => $jobsCountPhotographerYtd,
                  ],
                  [
                      'icon' =>
                          'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                      'label' => 'Total Profit YTD',
                      'value' => formatCurrency($totalProfitsPhotographerYtd) ?? '£0.00',
                  ],
                  [
                      'icon' =>
                          'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z',
                      'label' => 'Overall Rating',
                      'value' => $overallPhotographerRating,
                      'isRating' => true,
                  ],
              ],
              'videographer' => [
                  [
                      'icon' =>
                          'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z',
                      'label' => 'Jobs YTD',
                      'value' => $jobsCountVideographerYtd,
                  ],
                  [
                      'icon' =>
                          'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                      'label' => 'Total Profit YTD',
                      'value' => formatCurrency($totalProfitsVideographerYtd) ?? '£0.00',
                  ],
                  [
                      'icon' =>
                          'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z',
                      'label' => 'Overall Rating',
                      'value' => $overallVideographerRating,
                      'isRating' => true,
                  ],
              ],
              'standard' => [
                  [
                      'icon' =>
                          'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
                      'label' => 'Events YTD',
                      'value' => $eventsCountStandardYtd,
                  ],
              ],
              default => [],
          };
        @endphp

        @foreach ($stats as $stat)
          <div
            class="{{ isset($stat['isRating']) ? 'col-span-1 sm:col-span-2 md:col-span-1' : '' }} flex min-h-[32px] items-center justify-center gap-4 overflow-hidden">
            {{-- Mobile Icon --}}
            <span class="flex-shrink-0 lg:hidden">
              <svg class="h-5 w-5 text-yns_yellow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $stat['icon'] }}" />
              </svg>
            </span>

            {{-- Label --}}
            <span class="hidden flex-shrink-0 text-gray-400 lg:block">{{ $stat['label'] }}</span>

            {{-- Value --}}
            @if (isset($stat['isRating']))
              <div class="rating-wrapper flex items-center text-yns_yellow">
                {!! $stat['value'] !!}
              </div>
            @else
              <span class="flex-shrink-0 font-medium text-white">{{ $stat['value'] }}</span>
            @endif
          </div>
        @endforeach

        @if (empty($stats))
          <div class="flex items-center justify-center text-gray-400">Invalid user type</div>
        @endif
      </div>
    </div>
  </div>
</div>

<style>
  .ratings img {
    @apply inline-block h-4 w-4;
  }

  @media (max-width: 991px) {
    .ratings {
      @apply h-5;
    }
  }
</style>
