<div class="rounded-lg bg-gray-800/50 p-6 backdrop-blur-sm">
  <header class="mb-6 border-b border-gray-700 pb-4">
    <h2 class="font-heading text-lg font-medium text-white">
      {{ __('Genres and Artist Types') }}
    </h2>
    <p class="mt-1 text-sm text-gray-400">
      {{ __('Select the genres and types that best describe your music.') }}
    </p>
  </header>

  <form class="space-y-6" id="genres-and-types-form">
    @csrf
    @method('PUT')

    {{-- Artist Types Section --}}
    <div class="rounded-lg bg-black/20 p-6">
      <h3 class="mb-4 font-heading text-lg font-medium text-white">Artist Types</h3>
      <div class="grid sm:grid-cols-2 sm:gap-3 lg:grid-cols-3 lg:gap-4">
        <input type="hidden" id="band-types-data" name="band_types_data">

        <div class="flex items-center">
          <input id="all-types" name="band_type[]" type="checkbox" value="all"
            class="filter-checkbox h-4 w-4 rounded border-gray-700 bg-gray-900 text-yns_yellow focus:ring-yns_yellow" />
          <label for="all-types" class="ml-2 text-sm font-medium text-gray-300">All Types</label>
        </div>

        <div class="flex items-center">
          <input id="original" name="original" type="checkbox" value="original-bands"
            class="filter-checkbox band-type-checkbox h-4 w-4 rounded border-gray-700 bg-gray-900 text-yns_yellow focus:ring-yns_yellow" />
          <label for="original" class="ml-2 text-sm font-medium text-gray-300">Original</label>
        </div>

        <div class="flex items-center">
          <input id="cover" name="cover" type="checkbox" value="cover-bands"
            class="filter-checkbox band-type-checkbox h-4 w-4 rounded border-gray-700 bg-gray-900 text-yns_yellow focus:ring-yns_yellow" />
          <label for="cover" class="ml-2 text-sm font-medium text-gray-300">Covers</label>
        </div>

        <div class="flex items-center">
          <input id="tribute" name="tribute" type="checkbox" value="tribute-bands"
            class="filter-checkbox band-type-checkbox h-4 w-4 rounded border-gray-700 bg-gray-900 text-yns_yellow focus:ring-yns_yellow" />
          <label for="tribute" class="ml-2 text-sm font-medium text-gray-300">Tributes</label>
        </div>
      </div>
    </div>

    {{-- Genres Section --}}
    <div class="rounded-lg bg-black/20 p-6">
      <h3 class="mb-4 font-heading text-lg font-medium text-white">Music Genres</h3>

      {{-- Master "All Genres" Section --}}
      <div class="mb-4 border-b border-gray-700 pb-4">
        <div class="flex items-center gap-2">
          <x-input-checkbox id="master-all-genres"
            class="master-all-genres-checkbox h-4 w-4 rounded border-gray-700 bg-gray-900 text-yns_yellow focus:ring-yns_yellow" />
          <x-input-label-dark>All Genres</x-input-label-dark>
        </div>
      </div>
      <x-input-label-dark class="mt-6">Select your genre(s):</x-input-label-dark>
      <div class="grid sm:grid-cols-2 sm:gap-3 lg:grid-cols-3 lg:gap-4">
        @php
          // Handle genres
          $profileGenres = [];
          if (!empty($profileData['profileGenres'])) {
              if (is_string($profileData['profileGenres'])) {
                  try {
                      $decoded = json_decode($profileData['profileGenres'], true);
                      $profileGenres = $decoded ?: [];
                  } catch (\Exception $e) {
                      $profileGenres = [];
                  }
              } else {
                  $profileGenres = $profileData['profileGenres'] ?: [];
              }
          }

          // Handle band types
          $profileBandTypes = [];
          if (!empty($profileData['bandTypes'])) {
              if (is_string($profileData['bandTypes'])) {
                  try {
                      $decoded = json_decode($profileData['bandTypes'], true);
                      $profileBandTypes = $decoded ?: [];
                  } catch (\Exception $e) {
                      $profileBandTypes = [];
                  }
              } else {
                  $profileBandTypes = $profileData['bandTypes'] ?: [];
              }
          }
        @endphp


        <!-- Individual Genres Sections -->
        @foreach ($profileData['genres'] as $index => $genre)
          <div class="border-b border-slate-200">
            <button type="button" onclick="toggleAccordion({{ $index }})" id="genre-{{ $index }}"
              class="accordion-btn flex w-full items-center justify-between py-5 text-white">
              <span class="genre-name">{{ $genre['name'] }}</span>
              <div class="group flex items-center gap-4">
                <span class="status mr-4" data-genre="{{ $genre['name'] }}"></span>
                <span id="icon-{{ $index }}"
                  class="accordion-icon text-slate-800 transition-transform duration-300">
                </span>
              </div>
            </button>

            <div id="subgenres-accordion-{{ $index }}"
              class="max-h-0 content grid grid-cols-2 overflow-hidden transition-all duration-300 ease-in-out">
              <!-- All [Genre] checkbox -->
              <div class="all-genre-wrapper flex items-center gap-2 pb-2 text-sm text-white">
                <x-input-checkbox class="genre-all-checkbox" id="all-{{ Str::slug($genre['name']) }}"
                  data-genre="{{ $genre['name'] }}" name="genre[{{ $genre['name'] }}][all]" value="true" />
                <x-input-label-dark>All {{ $genre['name'] }}</x-input-label-dark>
              </div>

              <!-- Subgenres -->
              @foreach ($genre['subgenres'] as $subgenre)
                <div class="subgenre-wrapper flex items-center gap-2 pb-2 text-sm text-white">
                  <x-input-checkbox class="subgenre-checkbox"
                    id="subgenre-{{ Str::slug($genre['name']) }}-{{ Str::slug($subgenre) }}"
                    data-genre="{{ $genre['name'] }}" data-subgenre="{{ $subgenre }}"
                    name="genre[{{ $genre['name'] }}][subgenres][]" value="{{ $subgenre }}" />
                  <x-input-label-dark>{{ $subgenre }}</x-input-label-dark>
                </div>
              @endforeach
            </div>
          </div>
        @endforeach
      </div>
    </div>
</div>

<style>
  .status {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 24px;
    height: 24px;
    padding: 0 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 500;
    background-color: rgba(255, 196, 0, 0.2);
    color: #FFC400;
  }

  /* Hide when count is zero */
  .status.empty {
    display: none;
  }
</style>

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
    event.preventDefault();
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

  // Genres
  const masterAllGenres = document.getElementById('master-all-genres');
  const genreAllCheckboxes = document.querySelectorAll('.genre-all-checkbox');
  const subgenreCheckboxes = document.querySelectorAll('.subgenre-checkbox');

  // Initialize from existing data
  function initializeGenres(savedData) {
    if (!savedData) return;

    try {
      const data = typeof savedData === 'string' ? JSON.parse(savedData) : savedData;

      // Check if all genres are selected
      const allGenresSelected = Object.values(data).every(genre =>
        genre.all === true &&
        genre.subgenres?.length === document.querySelectorAll(`[data-genre="${genre.name}"]`).length
      );

      if (allGenresSelected) {
        masterAllGenres.checked = true;
      }

      // Set individual genres and subgenres
      Object.entries(data).forEach(([genreName, genreData]) => {
        const genreCheckbox = document.querySelector(`[data-genre="${genreName}"].genre-all-checkbox`);
        if (genreCheckbox && genreData.all) {
          genreCheckbox.checked = true;
        }

        genreData.subgenres?.forEach(subgenre => {
          const subgenreCheckbox = document.querySelector(
            `[data-genre="${genreName}"][data-subgenre="${subgenre}"]`);
          if (subgenreCheckbox) {
            subgenreCheckbox.checked = true;
          }
        });
      });
    } catch (error) {
      console.error('Error initializing genres:', error);
    }
  }

  // Master "All Genres" checkbox handler
  masterAllGenres.addEventListener('change', function() {
    const isChecked = this.checked;
    genreAllCheckboxes.forEach(checkbox => checkbox.checked = isChecked);
    subgenreCheckboxes.forEach(checkbox => checkbox.checked = isChecked);
    updateGenresState();
    updateCheckboxCounters();
  });

  // Genre "All [Genre]" checkbox handlers
  genreAllCheckboxes.forEach(checkbox => {
    checkbox.addEventListener('change', function() {
      const genre = this.dataset.genre;
      const relatedSubgenres = document.querySelectorAll(`[data-genre="${genre}"].subgenre-checkbox`);
      relatedSubgenres.forEach(sub => sub.checked = this.checked);
      updateMasterCheckbox();
      updateGenresState();
      updateCheckboxCounters();

    });
  });

  // Individual subgenre checkbox handlers
  subgenreCheckboxes.forEach(checkbox => {
    checkbox.addEventListener('change', function() {
      const genre = this.dataset.genre;
      const genreCheckbox = document.querySelector(`[data-genre="${genre}"].genre-all-checkbox`);
      const relatedSubgenres = document.querySelectorAll(`[data-genre="${genre}"].subgenre-checkbox`);

      // Update genre "All" checkbox
      genreCheckbox.checked = Array.from(relatedSubgenres).every(sub => sub.checked);

      updateMasterCheckbox();
      updateGenresState();
      updateCheckboxCounters();

    });
  });

  function updateMasterCheckbox() {
    masterAllGenres.checked = Array.from(genreAllCheckboxes).every(checkbox => checkbox.checked);
  }

  function updateGenresState() {
    const state = {};

    genreAllCheckboxes.forEach(genreCheckbox => {
      const genre = genreCheckbox.dataset.genre;
      const subgenres = Array.from(document.querySelectorAll(`[data-genre="${genre}"].subgenre-checkbox`))
        .filter(sub => sub.checked)
        .map(sub => sub.dataset.subgenre);

      state[genre] = {
        all: genreCheckbox.checked,
        subgenres: subgenres
      };
    });

    sendGenresUpdate(state);
  }

  function sendGenresUpdate(state) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    fetch(`/profile/${dashboardType}/save-genres`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
          genres: state,
          dashboardType: dashboardType,
        })
      })
      .then(response => response.json())
      .then(data => {
        if (!data.success) throw new Error(data.message || 'Failed to save genres');
        showSuccessNotification(data.message);
      })
      .catch(error => {
        console.error('Error:', error);
        showFailureNotification(error.message || 'An error occurred');
      });
  }

  // Initialize with saved data if available
  if (typeof profileGenres !== 'undefined') {
    initializeGenres(profileGenres);
  }

  document.addEventListener('DOMContentLoaded', function() {
    updateCheckboxCounters();
  });

  // Function to update the checkbox count indicators
  function updateCheckboxCounters() {
    // For each genre section
    genres.forEach((genre, index) => {
      const genreName = genre.name;
      const statusElement = document.querySelector(`.status[data-genre="${genreName}"]`);

      // Find all checked subgenre checkboxes for this genre
      const checkedSubgenres = document.querySelectorAll(
        `[data-genre="${genreName}"].subgenre-checkbox:checked`
      );

      // Get count of checked boxes
      const count = checkedSubgenres.length;

      // Update the status indicator
      if (statusElement) {
        if (count > 0) {
          statusElement.textContent = count;
          statusElement.classList.remove('empty');
        } else {
          statusElement.textContent = '';
          statusElement.classList.add('empty');
        }
      }
    });
  }
</script>
