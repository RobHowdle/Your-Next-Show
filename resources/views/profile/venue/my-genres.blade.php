<header>
  <h2 class="text-md font-heading font-medium text-white">
    {{ __('The genres and band types you prefer to work with') }}
  </h2>
</header>

<x-input-label-dark class="mt-6">Select your genres:</x-input-label-dark>
<div class="grid sm:grid-cols-2 sm:gap-3 lg:grid-cols-3 lg:gap-4">
  @php
    $venueGenres = is_string($venue->genre) ? json_decode($venue->genre, true) : $venue->genre;
    $venueBandType = is_string($venue->band_type) ? json_decode($venue->band_type, true) : $venue->band_type;
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
  @foreach ($genres as $index => $genre)
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

<x-input-label-dark class="mt-8">Select your band types</x-input-label-dark>
<div class="grid sm:grid-cols-2 sm:gap-3 lg:grid-cols-3 lg:gap-4">
  <div class="flex items-center">
    <input id="all-bands" name="band_type[]" type="checkbox" value="all"
      class="filter-checkbox focus:ring-3 h-4 w-4 rounded border border-gray-300 bg-gray-50 focus:ring-blue-300 dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800 dark:focus:ring-blue-600 dark:focus:ring-offset-gray-800" />
    <label for="all-bands" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">All
      Types</label>
  </div>
  <div class="flex items-center">
    <input id="original-bands" name="original-bands" type="checkbox" value="original-bands"
      class="filter-checkbox focus:ring-3 h-4 w-4 rounded border border-gray-300 bg-gray-50 focus:ring-blue-300 dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800 dark:focus:ring-blue-600 dark:focus:ring-offset-gray-800" />
    <label for="original-bands" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">Original</label>
  </div>
  <div class="flex items-center">
    <input id="cover-bands" name="cover-bands" type="checkbox" value="cover-bands"
      class="filter-checkbox focus:ring-3 h-4 w-4 rounded border border-gray-300 bg-gray-50 focus:ring-blue-300 dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800 dark:focus:ring-blue-600 dark:focus:ring-offset-gray-800" />
    <label for="cover-bands" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">Covers</label>
  </div>
  <div class="flex items-center">
    <input id="tribute-bands" name="tribute-bands" type="checkbox" value="tribute-bands"
      class="filter-checkbox focus:ring-3 h-4 w-4 rounded border border-gray-300 bg-gray-50 focus:ring-blue-300 dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800 dark:focus:ring-blue-600 dark:focus:ring-offset-gray-800" />
    <label for="tribute-bands" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">Tributes</label>
  </div>
</div>

<script defer>
  const genres = @json($genres);
  const dashboardType = "{{ $dashboardType }}";
  let venueGenres = @json($venueUserData['venueGenres']);
  let venueBandTypes = @json($venueUserData['bandTypes']);

  document.addEventListener("DOMContentLoaded", () => {
    document.getElementById("all-genres-checkbox")?.addEventListener("change", (e) => {
      const allGenres = document.getElementById("all-genres-checkbox");

      console.log(allGenres.dataset);

      if (allGenres) {
        //
      }
    });

    setTimeout(() => {
      preCheckCheckboxes(venueGenres);
      preTickBandTypeCheckboxes(venueBandTypes)
    }, 500);
    updateGenreStatus();
  });

  function updateGenreStatus() {
    // Extract genres dynamically from venueGenres
    const genres = Object.keys(venueGenres);

    // Iterate over each genre
    genres.forEach(genre => {

      // Select all elements for the current genre
      const genreElements = document.querySelectorAll(`.subgenre[data-genre="${genre}"]`);

      const totalSubgenres = genreElements.length;
      const selectedSubgenres = Array.from(genreElements).filter(input => input.checked).length;

      const statusElement = document.querySelector(`.status[data-genre="${genre}"]`);
      if (statusElement) {
        if (selectedSubgenres === totalSubgenres) {
          statusElement.innerHTML = '<span class="fa-solid fa-check"></span>';
        } else {
          statusElement.innerHTML = `<span class="count">${selectedSubgenres}/${totalSubgenres}</span>`;
        }
      }
    });
  }

  function preCheckCheckboxes(venueGenres) {
    if (!venueGenres || typeof venueGenres !== "object") {
      return;
    }

    Object.entries(venueGenres).forEach(([genreName, genreData], index) => {
      // Check "All" checkbox for the genre
      const allCheckboxId = `all-${genreName.toLowerCase().replace(/\W/g, '_')}-${index}`;
      const allCheckbox = document.querySelector(`#${allCheckboxId}`);

      if (allCheckbox) {
        allCheckbox.checked = genreData.all === "true";
      }

      // Process subgenres
      const subgenres = Array.isArray(genreData.subgenres[0]) ?
        genreData.subgenres[0] :
        genreData.subgenres || [];
      subgenres.forEach((subgenre) => {
        const subgenreId = 'subgenre-' + subgenre.toLowerCase().replace(/\W/g, '_');
        const subgenreCheckbox = document.querySelector(`#${subgenreId}`);

        if (subgenreCheckbox) {
          subgenreCheckbox.checked = true;
        }
      });
    });
  }

  function preTickBandTypeCheckboxes(savedBandTypes) {
    savedBandTypes.forEach(bandType => {
      const checkbox = document.querySelector(`input[type="checkbox"][value="${bandType}"]`);
      if (checkbox) {
        checkbox.checked = true;
      }
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

  function toggleSubgenresCheckboxes(genreIndex) {
    const genre = genres[genreIndex].name;
    const allCheckbox = document.querySelector(`#all-${genre.toLowerCase().replace(/\s+/g, '_')}-${genreIndex}`);
    const subgenreCheckboxes = document.querySelectorAll(`#subgenres-accordion-${genreIndex} .subgenre-checkbox`);

    if (allCheckbox) {
      const isChecked = allCheckbox.checked;

      subgenreCheckboxes.forEach((checkbox) => {
        checkbox.checked = isChecked;
      });
    }
  }

  // Genre checkbox (All Genre) click event
  document.querySelectorAll('.genre-checkbox').forEach(function(checkbox) {
    checkbox.addEventListener('change', function() {
      if (checkbox && checkbox.dataset) {
        sendCheckedGenres(checkbox);
      }
    });
  });

  document.querySelectorAll('.subgenre-checkbox').forEach(function(checkbox) {
    checkbox.addEventListener('change', function() {
      const genreName = checkbox.dataset.parent;
      const genreIndex = genres.findIndex(genre => genre.name === genreName);

      // Update "All Subgenres" checkbox state
      const allCheckbox = document.querySelector(
        `#all-${genreName.toLowerCase().replace(/\s+/g, '_')}-${genreIndex}`);
      const subgenreCheckboxes = document.querySelectorAll(
        `#subgenres-accordion-${genreIndex} .subgenre-checkbox`);
      const allChecked = Array.from(subgenreCheckboxes).every(cb => cb.checked);

      if (allCheckbox) {
        allCheckbox.checked = allChecked;
      }
    });
  });

  function sendCheckedGenres(checkbox) {
    if (!checkbox || !checkbox.dataset) {
      return;
    }

    let genre;
    let subgenreValue;

    // Determine if it's "All Genres" or an individual subgenre checkbox
    if (checkbox.dataset.genre === 'all') {
      genre = 'all';
      subgenreValue = null;
    } else if (checkbox.dataset.genre) {
      genre = checkbox.dataset.genre; // This is for the "All Genre" checkbox
      subgenreValue = getAllSubgenresForGenre(genre);
    } else if (checkbox.dataset.parent) {
      genre = checkbox.dataset.parent; // This is for individual subgenre checkboxes
      subgenreValue = checkbox.value; // Get the subgenre value from the individual checkbox
    } else {
      return;
    }

    if (!genre) {
      return;
    }

    // Initialize genresData globally if not already done
    let genresData = window.genresData || {};

    if (!genresData[genre]) {
      genresData[genre] = {
        all: false, // Default state for the "All" checkbox
        subgenres: [] // Initialize subgenres as an empty array
      };
    }

    // Get the "All Genres" checkbox for this genre
    const allCheckbox = document.getElementById(`all-${genre.toLowerCase().replace(/\s+/g, '_')}`);

    // Handle "All Genres" checkbox logic
    if (checkbox.dataset.genre === 'all') {
      if (checkbox.checked) {
        genresData[genre].all = true;
      }
    }

    if (checkbox === allCheckbox) {
      if (checkbox.checked) {
        // Mark "All" as true and add all subgenres
        genresData[genre].all = true;

        // Dynamically fetch subgenres for the genre
        const subgenreCheckboxes = document.querySelectorAll(`input[data-parent="${genre}"]`);
        genresData[genre].subgenres = Array.from(subgenreCheckboxes).map(subgenreCheckbox => subgenreCheckbox.value);

        // Check all individual subgenre checkboxes
        subgenreCheckboxes.forEach(subgenreCheckbox => {
          subgenreCheckbox.checked = true;
        });
      } else {
        // Uncheck "All" and clear subgenres
        genresData[genre].all = false;
        genresData[genre].subgenres = []; // Reset subgenres to an empty array

        // Uncheck all individual subgenre checkboxes
        document.querySelectorAll(`input[data-parent="${genre}"]`).forEach(subgenreCheckbox => {
          subgenreCheckbox.checked = false;
        });
      }
    } else {
      // Handle individual subgenre checkbox logic
      if (checkbox.checked) {
        // Add subgenre if it's not already in the list
        if (subgenreValue && !genresData[genre].subgenres.includes(subgenreValue)) {
          genresData[genre].subgenres.push(subgenreValue);
        }
      } else {
        // Remove subgenre if it's unchecked
        genresData[genre].subgenres = genresData[genre].subgenres.filter(subgenre => subgenre !== subgenreValue);
      }

      // Update "All" checkbox state based on individual subgenres
      const allSubgenres = document.querySelectorAll(`input[data-parent="${genre}"]`);
      const checkedSubgenres = document.querySelectorAll(`input[data-parent="${genre}"]:checked`);
      genresData[genre].all = allSubgenres.length === checkedSubgenres.length;

      // Sync the "All Genres" checkbox
      if (allCheckbox) {
        allCheckbox.checked = genresData[genre].all;
      }

      const allGenresCheckbox = document.getElementById("all-genres-checkbox");
      if (allGenresCheckbox) {
        allGenresCheckbox.checked = genresData[genre].all;
      }
    }

    // Store the updated genresData in the global object
    window.genresData = genresData;

    // console.log('Updated genresData:', JSON.stringify(genresData, null, 2));

    // Log the updated genresData for debugging
    sendGenresData(genresData);
  }

  // Send the genres data via AJAX
  function sendGenresData(genresData) {
    $.ajax({
      url: `/profile/${dashboardType}/save-genres`, // Replace with your route
      method: 'POST',
      data: {
        genres: genresData, // The data collected above
        _token: '{{ csrf_token() }}' // CSRF token for security
      },
      success: function(response) {
        showSuccessNotification(response.message);
      },
      error: function(xhr, status, error) {
        showFailureNotification(error.message);
      }
    });
  }

  // Add event listener to each subgenre checkbox
  document.querySelectorAll('.subgenre-checkbox').forEach(function(checkbox) {
    checkbox.addEventListener('change', function() {
      sendCheckedGenres(checkbox); // Trigger the function to update genres data
    });
  });

  // Helper function to get the index of a genre based on its name
  function getGenreIndex(genreName) {
    for (let i = 0; i < genres.length; i++) {
      if (genres[i].name === genreName) {
        return i;
      }
    }
    return 0; // Default index if not found
  }

  // Helper function to get all subgenres for a specific genre
  function getAllSubgenresForGenre(genreName) {
    const genre = genres.find(g => g.name === genreName);
    return genre ? genre.subgenres.map(sub => sub.toLowerCase().replace(/\s+/g, '_')) : [];
  }

  // Band Types
  document.addEventListener("DOMContentLoaded", () => {
    // Event listener for checkbox changes
    const bandCheckboxes = document.querySelectorAll(".filter-checkbox");
    bandCheckboxes.forEach(checkbox => {
      checkbox.addEventListener("change", handleCheckboxChange);
    });
  });

  function handleCheckboxChange() {
    // Gather selected checkboxes
    const selectedBandTypes = Array.from(document.querySelectorAll(".filter-checkbox:checked"))
      .map(input => input.value);

    console.log("Selected band types:", selectedBandTypes); // Debug log

    // Send the data via AJAX
    sendBandTypes(selectedBandTypes);
  }

  function sendBandTypes(bandTypes) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    fetch(`/profile/${dashboardType}/save-band-types`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": csrfToken
        },
        body: JSON.stringify({
          band_types: bandTypes
        })
      })
      .then(response => {
        if (!response.ok) {
          throw new Error("Network response was not ok");
        }
        return response.json(); // Convert response to JSON
      })
      .then(data => {
        // Use `data.message` from the server's JSON response
        showSuccessNotification(data.message);
      })
      .catch(error => {
        // Handle the error
        console.error("Error:", error);
        showFailureNotification(error.message || "An error occurred");
      });

  }
</script>
