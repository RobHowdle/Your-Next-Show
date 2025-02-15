@if ($item->contact_number && $item->contact_number != '00000000000')
  <a class="mr-2 transition duration-150 ease-in-out hover:text-yns_yellow"
    href="tel:{{ str_replace(' ', '', $item->contact_number) }}">
    <span class="fas fa-phone"></span>
  </a>
@endif

@if ($item->contact_email && $item->contact_email != 'blank@yournextshow.co.uk')
  <a class="mr-2 transition duration-150 ease-in-out hover:text-yns_yellow" href="mailto:{{ $item->contact_email }}">
    <span class="fas fa-envelope"></span>
  </a>
@endif

@if ($item->platforms)
  @foreach ($item->platforms as $platform)
    @switch($platform['platform'])
      @case('facebook')
        <a class="mr-2 transition duration-150 ease-in-out hover:text-yns_yellow" href="{{ $platform['url'] }}"
          target="_blank">
          <span class="fab fa-facebook"></span>
        </a>
      @break

      @case('twitter')
        <a class="mr-2 transition duration-150 ease-in-out hover:text-yns_yellow" href="{{ $platform['url'] }}"
          target="_blank">
          <span class="fab fa-twitter"></span>
        </a>
      @break

      @case('instagram')
        <a class="mr-2 transition duration-150 ease-in-out hover:text-yns_yellow" href="{{ $platform['url'] }}"
          target="_blank">
          <span class="fab fa-instagram"></span>
        </a>
      @break

      @case('snapchat')
        <a class="mr-2 transition duration-150 ease-in-out hover:text-yns_yellow" href="{{ $platform['url'] }}"
          target="_blank">
          <span class="fab fa-snapchat-ghost"></span>
        </a>
      @break

      @case('tiktok')
        <a class="mr-2 transition duration-150 ease-in-out hover:text-yns_yellow" href="{{ $platform['url'] }}"
          target="_blank">
          <span class="fab fa-tiktok"></span>
        </a>
      @break

      @case('youtube')
        <a class="mr-2 transition duration-150 ease-in-out hover:text-yns_yellow" href="{{ $platform['url'] }}"
          target="_blank">
          <span class="fab fa-youtube"></span>
        </a>
      @break

      @case('bluesky')
        <a class="mr-2 transition duration-150 ease-in-out hover:text-yns_yellow" href="{{ $platform['url'] }}"
          target="_blank">
          <span class="fa-brands fa-bluesky"></span>
        </a>
      @break

      @default
        <a class="mr-2 transition duration-150 ease-in-out hover:text-yns_yellow" href="{{ $platform['url'] }}"
          target="_blank">
          <span class="fas fa-globe"></span>
        </a>
    @endswitch
  @endforeach
@endif
