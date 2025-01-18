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
        <x-input-checkbox class="all-genres-checkbox" id="all-genres-checkbox" data-genre="all" data-all="true"
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
          <x-input-checkbox class="genre-checkbox" id="all-{{ strtolower($genre['name']) }}-{{ $index }}"
            onclick="toggleSubgenresCheckboxes({{ $index }})" data-all="true"
            name="all-{{ $genre['name'] }}-{{ $index }}" data-genre="{{ $genre['name'] }}"
            value="all-{{ strtolower($genre['name']) }}">
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
    bandTypesData = {
      allTypes: allTypesCheckbox.checked,
      bandTypes: Array.from(bandTypeCheckboxes)
        .filter(cb => cb.checked)
        .map(cb => cb.value)
    };
    document.getElementById('band-types-data').value =
      JSON.stringify(bandTypesData);

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
      .then(response => response.json().then(data => ({
        status: response.status,
        body: data
      })))
      .then(({
        status,
        body
      }) => {
        if (status === 422) {
          // Format validation errors
          const errors = body.errors;
          const errorMessages = Object.values(errors)
            .flat()
            .join('\n');
          throw new Error(errorMessages || 'Validation failed');
        }
        if (!body.success) {
          throw new Error(body.message);
        }
        showSuccessNotification(body.message);
      })
      .catch(error => {
        console.error("Error:", error);
        showFailureNotification(error.message || "An error occurred");
      });
  }

  // Genres
  let genresData = {};

  function setInitialGenres(savedGenres) {
    if (!savedGenres) return;

    try {
      const data = typeof savedGenres === 'string' ? JSON.parse(savedGenres) : savedGenres;

      // Loop through each genre in saved data
      Object.entries(data).forEach(([genreName, genreData]) => {
        // Find genre's "All" checkbox
        const allGenreCheckbox = document.querySelector(`.genre-checkbox[data-genre="${genreName}"]`);
        console.log(allGenreCheckbox);

        // Set the "All [Genre]" checkbox if all is true
        if (allGenreCheckbox && genreData.all === true) {
          allGenreCheckbox.checked = true;
        }

        // Find and check individual subgenre checkboxes
        if (genreData.subgenres) {
          genreData.subgenres.flat().forEach(subgenre => {
            const subgenreCheckbox = document.querySelector(`#subgenre-${subgenre}`);
            if (subgenreCheckbox) {
              subgenreCheckbox.checked = true;
            }
          });
        }
      });

      // Handle "All Genres" checkbox
      const allGenresSelected = Object.values(data).every(genre => genre.all === true);
      const allGenresCheckbox = document.getElementById('all-genres-checkbox');
      if (allGenresCheckbox) {
        allGenresCheckbox.checked = allGenresSelected;
      }

      // Initialize genresData state
      genresData = data;

      // Update status indicators for all genres
      genres.forEach(genre => {
        updateGenreStatus(genre.name);
      });
    } catch (error) {
      console.error('Error setting initial genres:', error);
    }
  }

  // Initialize with saved data
  if (typeof profileGenres !== 'undefined') {
    setInitialGenres(profileGenres);
  }

  // Handle "All Genres" checkbox
  document.getElementById('all-genres-checkbox').addEventListener('change', function(e) {
    const isChecked = e.target.checked;

    // Update all genre and subgenre checkboxes
    document.querySelectorAll('.genre-checkbox, .subgenre-checkbox').forEach(checkbox => {
      checkbox.checked = isChecked;
    });

    if (isChecked) {
      // Set all genres to selected with all subgenres
      genres.forEach(genre => {
        genresData[genre.name] = {
          all: true,
          subgenres: genre.subgenres
        };
      });
    } else {
      // Reset all selections
      genresData = {};
    }

    sendGenresData(genresData);
  });

  // Handle individual genre "All" checkboxes
  document.querySelectorAll('.genre-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function(e) {
      const genreName = this.dataset.genre;
      const isChecked = e.target.checked;
      const subgenreCheckboxes = document.querySelectorAll(`[data-parent="${genreName}"]`);

      // Update all subgenre checkboxes for this genre
      subgenreCheckboxes.forEach(subCheckbox => {
        subCheckbox.checked = isChecked;
      });

      // Update genresData
      if (isChecked) {
        genresData[genreName] = {
          all: true,
          subgenres: Array.from(subgenreCheckboxes).map(cb => cb.value)
        };
      } else {
        delete genresData[genreName];
      }

      sendGenresData(genresData);
    });
  });

  // Handle individual subgenre checkboxes
  document.querySelectorAll('.subgenre-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function(e) {
      const genreName = this.dataset.parent;
      const subgenreValue = this.value;
      const allGenreCheckbox = document.querySelector(`[data-genre="${genreName}"]`);
      const allSubgenreCheckboxes = document.querySelectorAll(`[data-parent="${genreName}"]`);
      const allChecked = Array.from(allSubgenreCheckboxes).every(cb => cb.checked);

      // Update "All" checkbox for this genre
      if (allGenreCheckbox) {
        allGenreCheckbox.checked = allChecked;
      }

      // Update genresData
      if (!genresData[genreName]) {
        genresData[genreName] = {
          all: false,
          subgenres: []
        };
      }

      if (this.checked) {
        genresData[genreName].subgenres.push(subgenreValue);
      } else {
        genresData[genreName].subgenres = genresData[genreName].subgenres
          .filter(sg => sg !== subgenreValue);
      }

      genresData[genreName].all = allChecked;

      // Clean up empty genres
      if (genresData[genreName].subgenres.length === 0) {
        delete genresData[genreName];
      }

      sendGenresData(genresData);
    });
  });

  function sendGenresData(data) {
    fetch(`/profile/${dashboardType}/save-genres`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
          genres: data
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          showSuccessNotification(data.message);
        } else {
          throw new Error(data.message);
        }
      })
      .catch(error => {
        showFailureNotification(error.message);
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

  function updateGenreStatus(genreName) {
    const subgenreCheckboxes = document.querySelectorAll(`[data-parent="${genreName}"]`);
    const statusIndicator = document.querySelector(`[data-genre="${genreName}"].status`);
    const totalSubgenres = subgenreCheckboxes.length;
    const checkedSubgenres = Array.from(subgenreCheckboxes).filter(cb => cb.checked).length;

    if (!statusIndicator) return;

    if (checkedSubgenres === 0) {
      statusIndicator.textContent = '';
    } else if (checkedSubgenres === totalSubgenres) {
      statusIndicator.innerHTML = '✓';
      statusIndicator.classList.add('text-green-500');
    } else {
      statusIndicator.textContent = `${checkedSubgenres}/${totalSubgenres}`;
      statusIndicator.classList.remove('text-green-500');
    }
  }

  // Add to your existing event listeners
  document.querySelectorAll('.subgenre-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
      const genreName = this.dataset.parent;
      updateGenreStatus(genreName);
      // ...existing checkbox change code...
    });
  });
</script>
