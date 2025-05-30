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

@if (!empty($item->platforms))
  @foreach ($item->platforms as $platform)
    <a class="mr-2 text-gray-300 transition duration-150 ease-in-out hover:text-yns_yellow" href="{{ $platform['url'] }}"
      target="_blank">
      @if ($platform['platform'] == 'facebook')
        <span class="fab fa-facebook"></span>
      @elseif($platform['platform'] == 'instagram')
        <span class="fab fa-instagram"></span>
      @elseif($platform['platform'] == 'x' || $platform['platform'] == 'twitter')
        <span class="fab fa-twitter"></span>
      @elseif($platform['platform'] == 'youtube')
        <span class="fab fa-youtube"></span>
      @elseif($platform['platform'] == 'tiktok')
        <span class="fab fa-tiktok"></span>
      @else
        <span class="fas fa-globe"></span>
      @endif
    </a>
  @endforeach
@endif
