@props([
    'dataId',
    'id',
    'name',
    'label',
    'placeholder',
    'value',
    'latitude',
    'longitude',
    'postalTown',
    'required' => false,
])

<div class="google-address-picker">
  <x-input-label-dark for="location_{{ $dataId }}">{{ $label }}
    @if ($required)
      <span class="text-yns_red">*</span>
    @endif
  </x-input-label-dark>

  <input type="text" id="location_{{ $dataId }}"
    class="mt-1 block w-full rounded-md border-yns_red shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-yns_red dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600"
    placeholder="{{ htmlspecialchars($placeholder) }}" data-id="{{ $dataId }}" {{ $required ? 'required' : '' }}>

  <input type="" id="postal_town_{{ $dataId }}" name="postal_town" value="{{ $postalTown }}"
    data-id="{{ $dataId }}" {{ $required ? 'required' : '' }}>
  <input type="" id="latitude_{{ $dataId }}" name="latitude" value="{{ $latitude }}"
    data-id="{{ $dataId }}" {{ $required ? 'required' : '' }}>
  <input type="" id="longitude_{{ $dataId }}" name="longitude" value="{{ $longitude }}"
    data-id="{{ $dataId }}" {{ $required ? 'required' : '' }}>

  <div id="maps-error-{{ $dataId }}" class="mt-2 hidden text-sm text-yns_red"></div>
</div>

<script>
  (() => {
    const pickerId = '{{ $dataId }}';

    function initializeAutocomplete() {
      const input = document.getElementById(`location_${pickerId}`);
      if (!input) return;

      const autocomplete = new google.maps.places.Autocomplete(input, {
        types: ['address']
      });

      autocomplete.addListener('place_changed', () => {
        const place = autocomplete.getPlace();

        if (!place.geometry) {
          console.error('No geometry data received');
          return;
        }

        // Update latitude and longitude
        document.getElementById(`latitude_${pickerId}`).value = place.geometry.location.lat();
        document.getElementById(`longitude_${pickerId}`).value = place.geometry.location.lng();

        // Find postal town
        if (place.address_components) {
          const townComponent = place.address_components.find(
            component =>
            component.types.includes('postal_town') ||
            component.types.includes('locality')
          );

          if (townComponent) {
            document.getElementById(`postal_town_${pickerId}`).value = townComponent.long_name;
          }
        }
      });
    }

    // Initialize when Maps API is loaded
    if (window.google && window.google.maps) {
      initializeAutocomplete();
    } else {
      const checkForMaps = setInterval(() => {
        if (window.google && window.google.maps) {
          clearInterval(checkForMaps);
          initializeAutocomplete();
        }
      }, 100);
    }
  })();
</script>
