<header>
  <h2 class="text-md font-heading font-medium text-white">
    {{ __('Genres and Artist Types') }}
  </h2>
</header>

<x-input-label-dark class="mt-8">Select your artist type(s)</x-input-label-dark>
<div class="grid sm:grid-cols-2 sm:gap-3 lg:grid-cols-3 lg:gap-4">
  <input type="hidden" id="band-types-data" name="band_types_data">

  <div class="flex items-center">
    <input id="all-types" name="band_type[]" type="checkbox" value="all"
      class="filter-checkbox focus:ring-3 h-4 w-4 rounded border border-gray-300 bg-gray-50 focus:ring-blue-300 dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800 dark:focus:ring-blue-600 dark:focus:ring-offset-gray-800" />
    <label for="all-types" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">All
      Types</label>
  </div>
  <div class="flex items-center">
    <input id="original" name="original" type="checkbox" value="original"
      class="filter-checkbox band-type-checkbox focus:ring-3 h-4 w-4 rounded border border-gray-300 bg-gray-50 focus:ring-blue-300 dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800 dark:focus:ring-blue-600 dark:focus:ring-offset-gray-800" />
    <label for="original" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">Original</label>
  </div>
  <div class="flex items-center">
    <input id="cover" name="cover" type="checkbox" value="cover"
      class="filter-checkbox band-type-checkbox focus:ring-3 h-4 w-4 rounded border border-gray-300 bg-gray-50 focus:ring-blue-300 dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800 dark:focus:ring-blue-600 dark:focus:ring-offset-gray-800" />
    <label for="cover" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">Covers</label>
  </div>
  <div class="flex items-center">
    <input id="tribute" name="tribute" type="checkbox" value="tribute"
      class="filter-checkbox band-type-checkbox focus:ring-3 h-4 w-4 rounded border border-gray-300 bg-gray-50 focus:ring-blue-300 dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800 dark:focus:ring-blue-600 dark:focus:ring-offset-gray-800" />
    <label for="tribute" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">Tributes</label>
  </div>
</div>

<x-input-label-dark class="mt-6">Select your genre(s):</x-input-label-dark>
<div class="grid sm:grid-cols-2 sm:gap-3 lg:grid-cols-3 lg:gap-4">
  @php
    $profileGenres = is_string($profileData['profileGenres'])
        ? json_decode($profileData['profileGenres'], true)
        : $profileData['profileGenres'];
    $profileBandTypes = is_string($profileData['bandTypes'])
        ? json_decode($profileData['bandTypes'], true)
        : $profileData['bandTypes'];
  @endphp

  <!-- "All Genres" checkbox -->
  <div class="border-b border-slate-200">
    <button onclick="toggleAccordion('all-genres')" id="all-genres-btn"
      class="accordion-btn flex w-full items-center justify-between py-5 text-white">
      <span class="genre-name">All Genres</span>
      <div class="group flex items-center gap-4">
        <span class="status mr-4" data-genre="All Genres"></span>
        <span id="icon-all-genres" class="accordion-icon text-slate-800 transition-transform duration-300">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="#ffffff" class="h-4 w-4">
            <path
              d="M8.75 3.75a.75.75 0 0 0-1.5 0v3.5h-3.5a.75.75 0 0 0 0 1.5h3.5v3.5a.75.75 0 0 0 1.5 0v-3.5h3.5a.75.75 0 0 0 0-1.5h-3.5v-3.5Z" />
          </svg>
        </span>
      </div>
    </button>
    <div id="subgenres-accordion-all-genres"
      class="max-h-0 content grid grid-cols-2 overflow-hidden transition-all duration-300 ease-in-out">
      <div class="all-genre-wrapper flex items-center gap-2 pb-2 text-sm text-white">
        <x-input-checkbox class="master-all-genres-checkbox" id="master-all-genres" data-type="master-all"
          name="all-genres-checkbox" value="all-genres">
        </x-input-checkbox>
        <x-input-label-dark>All Genres</x-input-label-dark>
      </div>
    </div>
  </div>

  <!-- Genres Accordion -->
  @foreach ($profileData['genres'] as $index => $genre)
    <div class="border-b border-slate-200">
      <button onclick="toggleAccordion({{ $index }})" id="genre-{{ $index }}"
        class="accordion-btn flex w-full items-center justify-between py-5 text-white">
        <span class="genre-name">{{ $genre['name'] }}</span>
        <div class="group flex items-center gap-4">
          <span class="status mr-4" data-genre={{ $genre['name'] }}></span>
          <span id="icon-{{ $index }}" class="accordion-icon text-slate-800 transition-transform duration-300">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="#ffffff" class="h-4 w-4">
              <path
                d="M8.75 3.75a.75.75 0 0 0-1.5 0v3.5h-3.5a.75.75 0 0 0 0 1.5h3.5v3.5a.75.75 0 0 0 1.5 0v-3.5h3.5a.75.75 0 0 0 0-1.5h-3.5v-3.5Z" />
            </svg>
          </span>
        </div>
      </button>
      <div id="subgenres-accordion-{{ $index }}"
        class="max-h-0 content grid grid-cols-2 overflow-hidden transition-all duration-300 ease-in-out">
        <div class="all-genre-wrapper flex items-center gap-2 pb-2 text-sm text-white">
          <x-input-checkbox class="genre-all-checkbox" id="all-{{ strtolower($genre['name']) }}-{{ $index }}"
            data-type="genre-all" data-genre="{{ $genre['name'] }}"
            name="all-{{ $genre['name'] }}-{{ $index }}" value="all-{{ strtolower($genre['name']) }}">
          </x-input-checkbox>
          <x-input-label-dark>All {{ $genre['name'] }}</x-input-label-dark>
        </div>

        @foreach ($genre['subgenres'] as $subIndex => $subgenre)
          @php
            $subgenreSlug = strtolower(str_replace(' ', '_', $subgenre));
          @endphp
          <div class="subgenre-wrapper flex items-center gap-2 pb-2 text-sm text-white">
            <x-input-checkbox class="subgenre-checkbox" id="subgenre-{{ $subgenreSlug }}"
              name="subgenre-{{ $subgenreSlug }}" data-parent="{{ $genre['name'] }}"
              value="{{ $subgenreSlug }}"></x-input-checkbox>
            <x-input-label-dark>{{ $subgenre }}</x-input-label-dark>
          </div>
        @endforeach
      </div>
    </div>
  @endforeach
</div>

<script defer>
  const genres = @json($profileData['genres']);
  const dashboardType = "{{ $dashboardType }}";
  let profileGenres = @json($profileData['profileGenres']);
  const profileBandTypes = @json($profileData['bandTypes'] ?? null);

  // Band Types
  const allTypesCheckbox = document.getElementById('all-types');
  const bandTypeCheckboxes = document.querySelectorAll('.band-type-checkbox');
  let bandTypesData = {
    allTypes: false,
    bandTypes: []
  };

  // Initialize from DB data
  function setInitialBandTypes(savedData) {
    if (!savedData) return;

    try {
      const data = typeof savedData === 'string' ? JSON.parse(savedData) : savedData;
      const checkboxes = document.querySelectorAll('.band-type-checkbox');

      // Set initial states without triggering updates
      checkboxes.forEach(checkbox => {
        if (data.includes(checkbox.value)) {
          checkbox.checked = true;
        }
      });

      // Set "All Types" if needed
      if (data.includes('all')) {
        allTypesCheckbox.checked = true;
        checkboxes.forEach(cb => cb.checked = true);
      }
    } catch (error) {
      console.error('Error setting initial band types:', error);
    }
  }

  if (typeof profileBandTypes !== 'undefined') {
    setInitialBandTypes(profileBandTypes);
  }

  // Handle "All Types" changes
  allTypesCheckbox.addEventListener('change', function() {
    bandTypeCheckboxes.forEach(checkbox => {
      checkbox.checked = this.checked;
    });
    updateBandTypesState();
  });

  // Handle individual checkbox changes
  bandTypeCheckboxes.forEach(checkbox => {
    checkbox.addEventListener('change', function() {
      const allChecked = Array.from(bandTypeCheckboxes)
        .every(cb => cb.checked);
      allTypesCheckbox.checked = allChecked;
      updateBandTypesState();
    });
  });

  // Update state and hidden input
  function updateBandTypesState() {
    console.log('Dashboard Type:', dashboardType); // Debug dashboard type

    bandTypesData = {
      allTypes: allTypesCheckbox.checked,
      bandTypes: Array.from(bandTypeCheckboxes)
        .filter(cb => cb.checked)
        .map(cb => cb.value)
    };

    console.log('Band Types Data:', bandTypesData); // Debug data structure
    sendBandTypes(bandTypesData);
  }

  function sendBandTypes(bandTypesData) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    fetch(`/profile/${dashboardType}/save-band-types`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "Accept": "application/json",
          "X-CSRF-TOKEN": csrfToken
        },
        body: JSON.stringify({
          band_types: bandTypesData
        })
      })
      .then(response => response.json())
      .then(data => {
        console.log('Server Response:', data); // Debug server response
        if (!data.success) {
          throw new Error(data.message || 'Failed to save band types');
        }
        showSuccessNotification(data.message);
        if (data.redirect) {
          setTimeout(() => {
            window.location.href = data.redirect;
          }, 2000);
        }
      })
      .catch(error => {
        console.error("Error:", error);
        showFailureNotification(error.message || "An error occurred");
      });
  }

  function toggleAccordion(index) {
    const content = document.getElementById(`subgenres-accordion-${index}`);
    const icon = document.getElementById(`icon-${index}`);

    // Check if the content and icon elements exist
    if (!content || !icon) {
      return; // Early exit if elements are not found
    }

    // SVG for Minus icon
    const minusSVG = `
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="#ffffff" class="w-4 h-4">
        <path d="M3.75 7.25a.75.75 0 0 0 0 1.5h8.5a.75.75 0 0 0 0-1.5h-8.5Z" />
      </svg>
      `;

    // SVG for Plus icon
    const plusSVG = `
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="#ffffff" class="w-4 h-4">
        <path d="M8.75 3.75a.75.75 0 0 0-1.5 0v3.5h-3.5a.75.75 0 0 0 0 1.5h3.5v3.5a.75.75 0 0 0 1.5 0v-3.5h3.5a.75.75 0 0 0 0-1.5h-3.5v-3.5Z" />
      </svg>
      `;

    // Close all other accordion content panels and reset their icons
    const allContents = document.querySelectorAll('.content');
    const allIcons = document.querySelectorAll('.accordion-icon');

    allContents.forEach((otherContent, otherIndex) => {
      // Close the content of any other accordion (except the one clicked)
      if (otherContent !== content) {
        otherContent.style.maxHeight = '0';
        allIcons[otherIndex].innerHTML = plusSVG; // Set the icon to Plus for closed panels
      }
    });

    // Toggle the current content's max-height for smooth opening and closing
    if (content.style.maxHeight && content.style.maxHeight !== '0px') {
      content.style.maxHeight = '0'; // Close it
      icon.innerHTML = plusSVG; // Set the icon to Plus when closed
    } else {
      content.style.maxHeight = content.scrollHeight + 'px'; // Open it
      icon.innerHTML = minusSVG; // Set the icon to Minus when opened
    }
  }
</script>
