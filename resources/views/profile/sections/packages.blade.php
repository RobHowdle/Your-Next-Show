<div class="rounded-lg bg-gray-800/50 p-6 backdrop-blur-sm">
  @php
    // Parse packages data to ensure we have valid packages to display
    $parsedPackages = [];

    // Check if we have packages data
    if (isset($profileData['packages'])) {
        $packages = $profileData['packages'];

        // Handle string (JSON) format - might be double encoded
        if (is_string($packages)) {
            $decoded = json_decode($packages, true);

            // Check if it's still a string after first decode (double encoded)
        if (is_string($decoded)) {
            $decoded = json_decode($decoded, true);
        }

        $packages = $decoded;
    }

    // Handle different array formats
    if (is_array($packages)) {
        // Check if it's a sequential array of packages
            if (array_keys($packages) === range(0, count($packages) - 1)) {
                // Already in the right format, just convert any objects to arrays
                foreach ($packages as $index => $package) {
                    $parsedPackages[$index] = is_object($package) ? (array) $package : $package;
                }
            } else {
                // It's a single package as an associative array
            $parsedPackages[0] = $packages;
        }
    } elseif (is_object($packages)) {
        // Single package as object
        $parsedPackages[0] = (array) $packages;
    }

    // Store the parsed packages back
    $profileData['packages'] = $parsedPackages;
    }
  @endphp

  <header class="mb-6 border-b border-gray-700 pb-4">
    <h2 class="font-heading text-lg font-medium text-white">
      {{ __('Packages') }}
    </h2>
    <p class="mt-1 text-sm text-gray-400">
      {{ __('Create and manage your service packages.') }}
    </p>
  </header>

  <div class="mt-8 grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3" id="packages-container">
    {{-- Loop through existing packages --}}
    @if (isset($profileData['packages']) && !empty($profileData['packages']))
      @foreach ($profileData['packages'] as $index => $package)
        <div class="package-card flex flex-col rounded-lg bg-gray-800 p-6 shadow-lg">
          <form class="package-form flex h-full flex-col">
            @csrf
            <div class="flex-1 space-y-4">
              <div>
                <x-input-label-dark>Package Title</x-input-label-dark>
                <x-text-input name="packages[{{ $index }}][title]"
                  value="{{ is_object($package) ? $package->title : $package['title'] ?? '' }}" class="w-full" />
              </div>
              <div>
                <x-input-label-dark>Description</x-input-label-dark>
                <x-textarea-input name="packages[{{ $index }}][description]"
                  rows="3">{{ is_object($package) ? $package->description : $package['description'] ?? '' }}</x-textarea-input>
              </div>
              <div>
                <x-input-label-dark>Lead Time</x-input-label-dark>
                <div class="flex flex-row gap-2">
                  <x-number-input name="packages[{{ $index }}][lead_time]"
                    value="{{ is_object($package) ? $package->lead_time : $package['lead_time'] ?? '' }}"
                    class="w-full" />
                  <select
                    class="w-full rounded-md border-yns_red px-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-yns_red dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600"
                    name="packages[{{ $index }}][lead_time_unit]">
                    @php
                      $selectedUnit = is_object($package) ? $package->lead_time_unit : $package['lead_time_unit'] ?? '';
                    @endphp
                    <option value="hours" {{ $selectedUnit === 'hours' ? 'selected' : '' }}>Hours</option>
                    <option value="days" {{ $selectedUnit === 'days' ? 'selected' : '' }}>Days</option>
                    <option value="weeks" {{ $selectedUnit === 'weeks' ? 'selected' : '' }}>Weeks</option>
                    <option value="months" {{ $selectedUnit === 'months' ? 'selected' : '' }}>Months</option>
                  </select>
                </div>
              </div>
              <div>
                <x-input-label-dark>Job Type</x-input-label-dark>
                <select name="packages[{{ $index }}][job_type]"
                  class="w-full rounded-md border-yns_red shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-yns_red dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600">
                  @php
                    $jobTypes = config('job_types.' . strtolower($dashboardType)) ?? [];
                    $selectedJobType = is_object($package) ? $package->job_type : $package['job_type'] ?? '';
                    $hasJobTypes = !empty($jobTypes);

                    // Check if the selected job type exists but doesn't match any config options
$jobTypeExists = !empty($selectedJobType);
$jobTypeInConfig = false;

if ($hasJobTypes && $jobTypeExists) {
    foreach ($jobTypes as $clientType => $types) {
        foreach ($types as $type) {
            if ($type['id'] === $selectedJobType) {
                                    $jobTypeInConfig = true;
                                    break 2;
                                }
                            }
                        }
                    }
                  @endphp

                  @if ($jobTypeExists && !$jobTypeInConfig)
                    {{-- Show the existing job type even if it's not in config --}}
                    <option value="{{ $selectedJobType }}" selected>
                      {{ ucfirst(str_replace('_', ' ', $selectedJobType)) }}</option>
                  @endif

                  @if ($hasJobTypes)
                    @foreach ($jobTypes as $clientType => $types)
                      <optgroup label="{{ ucfirst($clientType) }}">
                        @foreach ($types as $type)
                          <option value="{{ $type['id'] }}"
                            {{ $selectedJobType === $type['id'] ? 'selected' : '' }}>
                            {{ $type['name'] }}
                          </option>
                        @endforeach
                      </optgroup>
                    @endforeach
                  @else
                    <option value="default_package" {{ $selectedJobType === 'default_package' ? 'selected' : '' }}>
                      Default Package</option>
                  @endif
                </select>
              </div>
              <div>
                <x-input-label-dark>Price</x-input-label-dark>
                <x-number-input-pound name="packages[{{ $index }}][price]"
                  value="{{ is_object($package) ? $package->price : $package['price'] ?? '' }}" class="w-full" />
              </div>
              <div class="included-items">
                <x-input-label-dark>Included Items</x-input-label-dark>
                <div class="space-y-2" id="items-container-{{ $index }}">
                  @if (isset($package->items) || isset($package['items']))
                    @foreach (is_object($package) ? $package->items : $package['items'] as $itemIndex => $item)
                      <div class="flex gap-2">
                        <x-text-input name="packages[{{ $index }}][items][]" value="{{ $item }}"
                          class="w-full" />
                        <button type="button" class="remove-item text-red-500">×</button>
                      </div>
                    @endforeach
                  @endif
                </div>
                <button type="button" class="add-item mt-2 text-yns_yellow" data-package="{{ $index }}">+ Add
                  Item</button>
              </div>
            </div>
            <button type="button" class="remove-package mt-4 self-end text-red-500">Delete Package</button>
          </form>
        </div>
      @endforeach
    @else
      <div class="col-span-full">
        <p class="py-8 text-center text-gray-400">No Packages</p>
      </div>
    @endif

    {{-- Add New Package Card --}}
    <div
      class="add-package-card flex min-h-[200px] cursor-pointer items-center justify-center rounded-lg border-2 border-dashed border-gray-600 p-6 hover:border-yns_yellow">
      <div class="text-center">
        <span class="text-4xl text-gray-400">+</span>
        <p class="mt-2 text-gray-400">Add New Package</p>
      </div>
    </div>
  </div>

  {{-- Save Button --}}
  <div class="mt-6 flex justify-end border-t border-gray-700 pt-6">
    <button type="button" id="save-packages"
      class="rounded-lg border border-white bg-white px-4 py-2 font-heading font-bold text-black transition duration-150 ease-in-out hover:border-yns_yellow hover:text-yns_yellow">
      Save Packages
    </button>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const packagesContainer = document.getElementById('packages-container');
    const addPackageCard = document.querySelector('.add-package-card');
    let packageCount = document.querySelectorAll('.package-card').length;
    const userId = {{ $user->id }};

    // Add new package
    addPackageCard.addEventListener('click', function() {
      const newPackage = createPackageCard(packageCount);
      this.parentNode.insertBefore(newPackage, this);
      packageCount++;
    });

    // Remove package
    packagesContainer.addEventListener('click', function(e) {
      if (e.target.classList.contains('remove-package')) {
        e.target.closest('.package-card').remove();
      }
    });

    // Add item to package
    packagesContainer.addEventListener('click', function(e) {
      if (e.target.classList.contains('add-item')) {
        const packageIndex = e.target.dataset.package;
        const itemsContainer = document.getElementById(`items-container-${packageIndex}`);
        const newItem = createItemInput(packageIndex);
        itemsContainer.appendChild(newItem);
      }
    });

    // Remove item
    packagesContainer.addEventListener('click', function(e) {
      if (e.target.classList.contains('remove-item')) {
        e.target.parentElement.remove();
      }
    });

    function createPackageCard(index) {
      const div = document.createElement('div');
      const jobTypes = @json(config('job_types.' . strtolower($dashboardType)) ?? []);
      div.className = 'package-card flex flex-col bg-gray-800 p-6 rounded-lg shadow-lg';
      let jobTypeOptions = '';

      // Check if we have any job types
      let hasJobTypes = false;
      let firstJobTypeValue = '';

      if (Object.keys(jobTypes).length > 0) {
        for (const [clientType, types] of Object.entries(jobTypes)) {
          jobTypeOptions += `<optgroup label="${clientType.charAt(0).toUpperCase() + clientType.slice(1)}">`;
          types.forEach((type, i) => {
            const isSelected = (!hasJobTypes);
            if (!hasJobTypes) {
              firstJobTypeValue = type.id;
              hasJobTypes = true;
            }
            jobTypeOptions +=
              `<option value="${type.id}" ${isSelected ? 'selected' : ''}>${type.name}</option>`;
          });
          jobTypeOptions += '</optgroup>';
        }
      }

      // Special handling for known job types that might not be in config
      const knownJobTypes = ['gig_shoot', 'portrait', 'band_promo', 'event_coverage', 'album_artwork'];
      const dashboardTypeJobTypes = {
        'photographer': ['gig_shoot', 'portrait', 'band_promo', 'event_coverage'],
        'designer': ['album_artwork', 'poster_design', 'logo_design']
      };

      // Add known job types for this dashboard type if they're not already in the config
      if (dashboardTypeJobTypes['{{ $dashboardType }}']) {
        const relevantJobTypes = dashboardTypeJobTypes['{{ $dashboardType }}'];
        if (!hasJobTypes && relevantJobTypes.length > 0) {
          jobTypeOptions += '<optgroup label="Common Job Types">';
          relevantJobTypes.forEach((jobType, i) => {
            const isSelected = (i === 0);
            if (isSelected) {
              firstJobTypeValue = jobType;
              hasJobTypes = true;
            }
            const readableName = jobType.split('_')
              .map(word => word.charAt(0).toUpperCase() + word.slice(1))
              .join(' ');
            jobTypeOptions +=
              `<option value="${jobType}" ${isSelected ? 'selected' : ''}>${readableName}</option>`;
          });
          jobTypeOptions += '</optgroup>';
        }
      }

      // If no job types found, add a default option
      if (!hasJobTypes) {
        jobTypeOptions = '<option value="default_package" selected>Default Package</option>';
      }

      div.innerHTML = `
            <form class="package-form flex h-full flex-col">
                @csrf
                <div class="flex-1 space-y-4">
                    <div>
                        <x-input-label-dark>Package Title</x-input-label-dark>
                        <x-text-input name="packages[${index}][title]" class="w-full" required />
                    </div>
                    
                    <div>
                        <x-input-label-dark>Description</x-input-label-dark>
                        <x-textarea-input name="packages[${index}][description]" rows="3"></x-textarea-input>
                    </div>
                    
                    <div>
                        <x-input-label-dark>Price</x-input-label-dark>
                        <x-number-input-pound name="packages[${index}][price]" class="w-full" />
                    </div>

                    <div>
                        <x-input-label-dark>Job Type</x-input-label-dark>
                        <select name="packages[${index}][job_type]" class="w-full rounded-md border-yns_red shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-yns_red dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600" required>
                            ${jobTypeOptions}
                        </select>
                      </div>
                      
                      <div class="included-items">
                        <div class="group mb-2">
                          <x-input-label-dark>Lead Time</x-input-label-dark>
                          <div class="flex flex-row gap-2">
                              <x-number-input name="packages[${index}][lead_time]" class="w-full"/>
                              <select class="border-yns_red w-full dark:border-yns_red dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                              name="packages[${index}][lead_time_unit]">
                              <option value="hours">Hours</option>
                              <option value="days" selected>Days</option>
                              <option value="weeks">Weeks</option>
                              <option value="months">Months</option>
                              </select>
                          </div>
                        </div>
                        <x-input-label-dark>Included Items</x-input-label-dark>
                        <div class="space-y-2" id="items-container-${index}"></div>
                        <button type="button" class="add-item mt-2 text-yns_yellow" data-package="${index}">+ Add Item</button>
                      </div>
                  </div>
                  <button type="button" class="remove-package mt-4 self-end text-red-500">Delete Package</button>
              </form>
          `;
      return div;
    }

    function createItemInput(packageIndex) {
      const div = document.createElement('div');
      div.className = 'flex gap-2';
      div.innerHTML = `
            <x-text-input name="packages[${packageIndex}][items][]" class="w-full"/>
            <button type="button" class="remove-item text-red-500">×</button>
        `;
      return div;
    }

    function savePackages() {
      const packages = [];
      const packageForms = document.querySelectorAll('.package-form');

      packageForms.forEach((form, formIdx) => {
        const titleField = form.querySelector('input[name^="packages["]');
        if (!titleField) return;

        const indexMatch = titleField.name.match(/packages\[(\d+)\]/);
        const index = indexMatch ? indexMatch[1] : 0;

        const title = form.querySelector(`input[name="packages[${index}][title]"]`)?.value || '';
        const description = form.querySelector(`textarea[name="packages[${index}][description]"]`)?.value || '';
        const price = form.querySelector(`input[name="packages[${index}][price]"]`)?.value || '';
        const jobType = form.querySelector(`select[name="packages[${index}][job_type]"]`)?.value || '';
        const leadTime = form.querySelector(`input[name="packages[${index}][lead_time]"]`)?.value || '';
        const leadTimeUnit = form.querySelector(`select[name="packages[${index}][lead_time_unit]"]`)?.value ||
          '';

        const items = Array.from(form.querySelectorAll(`input[name="packages[${index}][items][]"]`))
          .map(input => input.value)
          .filter(item => item.trim() !== '');

        const packageData = {
          title,
          description,
          price,
          job_type: jobType || 'default_package',
          lead_time: leadTime,
          lead_time_unit: leadTimeUnit,
          items
        };

        if (packageData.title) {
          packages.push(packageData);
        }
      });

      if (packages.length === 0) {
        showFailureNotification('No valid packages to save. Ensure all required fields are completed.');
        return;
      }

      const dashboardType = '{{ $dashboardType }}';

      fetch(`/profile/${dashboardType}/${userId}/packages/update`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
          },
          body: JSON.stringify({
            packages: packages
          })
        })
        .then(response => {
          if (!response.ok) {
            return response.text().then(text => {
              throw new Error(`Network response error (${response.status}): ${text}`);
            });
          }
          return response.json();
        })
        .then((data) => {
          if (data.success) {
            showSuccessNotification(data.message || 'Packages updated successfully');
            setTimeout(() => {
              window.location.reload();
            }, 3000);
          } else {
            throw new Error(data.message || 'Failed to save packages');
          }
        })
        .catch(error => {
          showFailureNotification(error.message || 'An error occurred while saving packages');
          console.error('Error saving packages:', error);
        });
    };

    // Attach main save function
    const saveButton = document.querySelector('#save-packages');
    if (saveButton) {
      saveButton.addEventListener('click', savePackages);
    }
  });
</script>
