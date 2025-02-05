<div class="grid grid-cols-3 place-items-center text-white">
  @if ($userType === 'promoter')
    <div>Events YTD: {{ $eventsCountPromoterYtd }}</div>
    <div class="flex">Total Profit YTD:
      <div id="amountDisplay" class="ml-2">{{ formatCurrency($totalProfitsPromoterYtd) ?? '£0.00' }}</div>
    </div>
    <div class="rating-wrapper flex flex-row items-center gap-2">
      <p class="h-full place-content-end font-sans">Overall Rating:</p>
      <div class="ratings flex">
        {!! $overallRatingPromoter !!}
      </div>
    </div>
  @elseif ($userType === 'artist')
    <div>Gigs YTD: {{ $gigsCountBandYtd }}</div>
    <div class="flex">Total Profit YTD:
      <div id="amountDisplay" class="ml-2">{{ formatCurrency($totalProfitsBandYtd) ?? '£0.00' }}</div>
    </div>
    <div class="rating-wrapper flex flex-row items-center gap-2">
      <p class="h-full place-content-end font-sans">Overall Rating:</p>
      <div class="ratings flex">
        {!! $overallRatingBand !!}
      </div>
    </div>
  @elseif ($userType === 'designer')
    <div>Total Projects: {{ $jobsCountDesignerYTD }}</div>
    <div class="flex">Total Profit YTD:
      <div id="amountDisplay" class="ml-2">{{ formatCurrency($totalProfitsDesignerYtd) ?? '£0.00' }}</div>
    </div>
    <div class="rating-wrapper flex flex-row items-center gap-2">
      <p class="h-full place-content-end font-sans">Overall Rating:</p>
      <div class="ratings flex">
        {!! $overallRatingDesigner !!}
      </div>
    </div>
  @elseif ($userType === 'venue')
    <div>Events YTD: {{ $eventsCountVenueYtd }}</div>
    <div class="flex">Total Profit YTD:
      <div id="amountDisplay" class="ml-2">{{ formatCurrency($totalProfitsVenueYtd) ?? '£0.00' }}</div>
    </div>
    <div class="rating-wrapper flex flex-row items-center gap-2">
      <p class="h-full place-content-end font-sans">Overall Rating:</p>
      <div class="ratings flex">
        {!! $overallRatingVenue !!}
      </div>
    </div>
  @elseif ($userType === 'photographer')
    <div>Jobs YTD: {{ $jobsCountPhotographerYtd }}</div>
    <div class="flex">Total Profit YTD:
      <div id="amountDisplay" class="ml-2">{{ formatCurrency($totalProfitsPhotographerYtd) ?? '£0.00' }}</div>
    </div>
    <div class="rating-wrapper flex flex-row items-center gap-2">
      <p class="h-full place-content-end font-sans">Overall Rating:</p>
      <div class="ratings flex">
        {!! $overallPhotographerRating !!}
      </div>
    </div>
  @elseif ($userType === 'standard')
    <div>Events YTD: {{ $eventsCountStandardYtd }}</div>
  @elseif ($userType === 'videographer')
    <div>Jobs YTD: {{ $jobsCountVideographerYtd }}</div>
    <div class="flex">Total Profit YTD:
      <div id="amountDisplay" class="ml-2">{{ formatCurrency($totalProfitsVideographerYtd) ?? '£0.00' }}</div>
    </div>
    <div class="rating-wrapper flex flex-row items-center gap-2">
      <p class="h-full place-content-end font-sans">Overall Rating:</p>
      <div class="ratings flex">
        {!! $overallVideographerRating !!}
      </div>
    </div>
  @else
    <div>Invalid user type</div>
  @endif
</div>
