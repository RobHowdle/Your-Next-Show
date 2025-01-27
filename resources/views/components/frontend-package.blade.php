<div id="packages" class="overflow-auto md:flex md:flex-wrap md:gap-8">
  @if (!empty($singleServiceData['packages']))
    <div class="grid w-full grid-cols-2 gap-2">
      @foreach ($singleServiceData['packages'] as $package)
        <div class="service mb-6 w-full min-w-[calc(50%-1rem)] p-6 md:mb-0 md:flex-1">
          <p class="font-semibold">
            {{ is_object($package) ? $package->title : $package['title'] }}
          </p>
          <p class="font-normal">
            {{ is_object($package) ? $package->description : $package['description'] }}
          </p>

          @php
            $items = is_object($package) ? $package->items : $package['items'] ?? [];
            $items = is_array($items) ? $items : [];
          @endphp

          @if (!empty($items))
            <p class="mt-4 font-semibold">Package includes:</p>
            <ul class="list-inside list-disc">
              @foreach ($items as $bullet)
                <li>{{ $bullet }}</li>
              @endforeach
            </ul>
          @endif

          <p class="mt-4 text-lg font-bold">
            From {{ formatCurrency(is_object($package) ? $package->price : $package['price']) }}
          </p>
        </div>
      @endforeach
    </div>
    @if (isset($singleService->contact_email) && $singleService->contact_email)
      <p class="mt-4">All services are subject to location and travel costs. Please <a
          class="underline hover:text-yns_yellow" href="mailto:{{ $singleService->contact_email }}">contact
          us</a> with any
        queries.</p>
    @else
      <p>contact us through our social media</p>
    @endif
    @if ($singleService['portfolio_link'])
      <p class="mt-2">You can view our portfolio by <a class="underline hover:text-yns_yellow"
          href="{{ $singleService['portfolio_link'] }}" target="_blank">clicking here.</a></p>
    @endif
  @else
    <p>We haven't got our packages set up yet! Come back soon!</p>
  @endif
</div>
