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
    name="location" placeholder="{{ htmlspecialchars($placeholder) }}"
    value="{{ htmlspecialchars(is_array($value) ? $value['location'] ?? '' : $value) }}" data-id="{{ $dataId }}"
    {{ $required ? 'required' : '' }} />

  <input type="" id="postal_town_{{ $dataId }}" name="postal_town" value="{{ $postalTown }}"
    data-id="{{ $dataId }}" {{ $required ? 'required' : '' }}>

  <input type="" id="latitude_{{ $dataId }}" name="latitude" value="{{ $latitude }}"
    data-id="{{ $dataId }}" {{ $required ? 'required' : '' }}>
  <input type="" id="longitude_{{ $dataId }}" name="longitude" value="{{ $longitude }}"
    data-id="{{ $dataId }}" {{ $required ? 'required' : '' }}>
</div>

<script defer>
  function initializeMaps() {
    const addressPickers = document.querySelectorAll('[id^="location_"]');

    addressPickers.forEach((addressPicker) => {
      const index = addressPicker.getAttribute('data-id');

      const autocomplete = new google.maps.places.Autocomplete(addressPicker, {
        types: ['geocode'],
        componentRestrictions: {
          country: 'uk',
        },
      });

      autocomplete.addListener("place_changed", function() {
        const place = autocomplete.getPlace();

        if (place.geometry) {
          const latitude = place.geometry.location.lat();
          const longitude = place.geometry.location.lng();

          // Extract postal town from address components
          let postalTown = "";
          if (place.address_components) {
            for (const component of place.address_components) {
              if (component.types.includes("postal_town")) {
                postalTown = component.long_name;
                break;
              }
            }
          }

          // Update hidden fields
          document.getElementById(`postal_town_${index}`).value = postalTown;
          document.getElementById(`latitude_${index}`).value = latitude;
          document.getElementById(`longitude_${index}`).value = longitude;
        }
      });
    });
  }
</script>
