<div class="tab-panel hidden" id="packages">
  <div class="overflow-auto md:flex md:flex-wrap md:gap-8">
    @if (!empty($singleServiceData['packages']))
      <div class="grid w-full grid-cols-1 gap-4 md:grid-cols-2">
        @foreach ($singleServiceData['packages'] as $package)
          <div class="service rounded-lg border border-gray-700 bg-black/30 p-6 backdrop-blur-sm">
            <h3 class="mb-2 text-xl font-semibold text-white">
              {{ is_object($package) ? $package->title : $package['title'] }}
            </h3>
            <p class="text-gray-300">
              {{ is_object($package) ? $package->description : $package['description'] }}
            </p>

            @php
              $items = is_object($package) ? $package->items : $package['items'] ?? [];
              $items = is_array($items) ? $items : [];
            @endphp

            @if (!empty($items))
              <p class="mt-4 font-semibold text-white">Package includes:</p>
              <ul class="mt-2 list-inside list-disc space-y-1 text-gray-300">
                @foreach ($items as $bullet)
                  <li>{{ $bullet }}</li>
                @endforeach
              </ul>
            @endif

            <p class="mt-6 text-lg font-bold text-yns_yellow">
              From {{ formatCurrency(is_object($package) ? $package->price : $package['price']) }}
            </p>
          </div>
        @endforeach
      </div>

      <div class="mt-8 space-y-4 text-center text-gray-300">
        @if (isset($singleService->contact_email) && $singleService->contact_email)
          <p>All services are subject to location and travel costs. Please
            <a href="mailto:{{ $singleService->contact_email }}"
              class="text-yns_yellow underline transition hover:text-yns_yellow/80">
              contact us
            </a>
            with any queries.
          </p>
        @else
          <p>Contact us through our social media</p>
        @endif

        @if ($singleService['portfolio_link'])
          <p>You can view our portfolio by
            <a href="{{ $singleService['portfolio_link'] }}" target="_blank"
              class="text-yns_yellow underline transition hover:text-yns_yellow/80">
              clicking here
            </a>.
          </p>
        @endif
      </div>
    @else
      <div class="text-center text-gray-300">
        <p>We haven't got our packages set up yet! Come back soon!</p>
      </div>
    @endif
  </div>
</div>
