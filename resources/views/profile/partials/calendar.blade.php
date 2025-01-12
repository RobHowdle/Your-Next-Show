<header>
  <h2 class="font-heading text-xl font-medium text-white">
    {{ __('Calendar') }}
  </h2>
</header>
<div class="group mb-8">
  <x-input-label-dark for="calendar">Your unique calendar can be used to track the events you create or are attending.
    Sync your
    calendar with your personal calendar to have your events shown on your device.
    <span class="bold">The sync will only work one way, events in your personal calendar will not sync to the YNS
      Calendar.</span>
  </x-input-label-dark>
  <div id="calendar" data-user-id="{{ Auth::check() ? Auth::user()->id : '' }}"
    data-dashboard-type="{{ $dashboardType }}">
  </div>
</div>

<div class="group mb-6 flex flex-row items-center gap-4">
  @if (Auth::user()->google_access_token)
    <form action="{{ route('google.unlink', ['dashboardType' => $dashboardType]) }}" method="POST">
      @csrf
      <x-button type="submit" fa="fa-brands fa-google mr-2" label="Unlink Google Calendar"></x-button>
    </form>

    <form action="{{ route('google.sync', ['dashboardType' => $dashboardType]) }}" method="POST">
      @csrf
      <x-button type="submit" fa="fa-solid fa-arrows-rotate" label="Manual Google Sync"></x-button>
    </form>
  @else
    <x-button href="{{ route('google.redirect', ['dashboardType' => $dashboardType]) }}" label="Link Google Calendar"
      fa="fa-brands fa-google mr-2"></x-button>
  @endif
</div>
<div class="group">

</div>
{{-- <div class="group">
    <button id="sync-all-events-apple"
        class="rounded bg-green-500 px-4 py-2 font-semibold text-white hover:bg-green-600"
        title="Sync All Events to Apple Calendar">
        Sync All Events to Apple Calendar
    </button>
    </div>
  --}}
