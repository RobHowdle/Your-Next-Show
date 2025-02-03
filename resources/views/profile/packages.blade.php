<header>
  <h2 class="text-md font-heading font-medium text-white">
    {{ __('Packages') }}
  </h2>
</header>

<div class="mt-8 grid grid-cols-3 gap-4" id="packages-container">
  {{-- Loop through existing packages --}}
  @if (isset($profileData['packages']) && !empty($profileData['packages']))
    @foreach ($profileData['packages'] as $index => $package)
      <div class="package-card rounded-lg bg-gray-800 p-6 shadow-lg">
        <form class="package-form">
          @csrf
          <div class="space-y-4">
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
              <div class="group">
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
                    <option class="px-2" value="hours" {{ $selectedUnit === 'hours' ? 'selected' : '' }}>Hours
                    </option>
                    <option class="px-2" value="days" {{ $selectedUnit === 'days' ? 'selected' : '' }}>Days</option>
                    <option class="px-2" value="weeks" {{ $selectedUnit === 'weeks' ? 'selected' : '' }}>Weeks
                    </option>
                    <option class="px-2" value="months" {{ $selectedUnit === 'months' ? 'selected' : '' }}>Months
                    </option>
                  </select>
                </div>
              </div>
            </div>

            <div>
              <x-input-label-dark>Job Type</x-input-label-dark>
              <select name="packages[{{ $index }}][job_type]"
                class="w-full rounded-md border-yns_red shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-yns_red dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600">
                @php
                  $jobTypes = config('job_types.' . strtolower($dashboardType));
                  $selectedJobType = is_object($package) ? $package->job_type : $package['job_type'] ?? '';
                @endphp

                @foreach ($jobTypes as $clientType => $types)
                  <optgroup label="{{ ucfirst($clientType) }}">
                    @foreach ($types as $type)
                      <option value="{{ $type['id'] }}" {{ $selectedJobType === $type['id'] ? 'selected' : '' }}>
                        {{ $type['name'] }}
                      </option>
                    @endforeach
                  </optgroup>
                @endforeach
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
          <button type="button" class="remove-package mt-4 text-red-500">Delete Package</button>
        </form>
      </div>
    @endforeach
  @endif

  {{-- Add New Package Card --}}
  <div
    class="add-package-card flex cursor-pointer items-center justify-center rounded-lg border-2 border-dashed border-gray-600 p-6 hover:border-yns_yellow">
    <div class="text-center">
      <span class="text-4xl text-gray-400">+</span>
      <p class="mt-2 text-gray-400">Add New Package</p>
    </div>
  </div>
</div>

{{-- Save Button --}}
<div class="mt-6 flex justify-end">
  <button type="button" id="save-packages"
    class="rounded-lg border border-white bg-white px-4 py-2 font-heading font-bold text-black transition duration-150 ease-in-out hover:border-yns_yellow hover:text-yns_yellow">
    Save Packages
  </button>
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
      const jobTypes = @json(config('job_types.' . strtolower($dashboardType)));

      div.className = 'package-card bg-gray-800 p-6 rounded-lg shadow-lg';
      let jobTypeOptions = '';

      for (const [clientType, types] of Object.entries(jobTypes)) {
        jobTypeOptions += `<optgroup label="${clientType.charAt(0).toUpperCase() + clientType.slice(1)}">`;
        types.forEach(type => {
          jobTypeOptions += `<option value="${type.id}">${type.name}</option>`;
        });
        jobTypeOptions += '</optgroup>';
      }

      div.innerHTML = `
            <form class="package-form">
                @csrf
                <div class="space-y-4">
                    <div>
                        <x-input-label-dark>Package Title</x-input-label-dark>
                        <x-text-input name="packages[${index}][title]" class="w-full"/>
                    </div>
                    
                    <div>
                        <x-input-label-dark>Description</x-input-label-dark>
                        <x-textarea-input name="packages[${index}][description]" rows="3"></x-textarea-input>
                    </div>
                    
                    <div>
                        <x-input-label-dark>Price</x-input-label-dark>
                        <x-text-input type="number" name="packages[${index}][price]" class="w-full"/>
                    </div>

                    <div>
                        <x-input-label-dark>Job Type</x-input-label-dark>
                        <select name="packages[${index}][job_type]" class="w-full rounded-md border-yns_red shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-yns_red dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600">
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
                                <option value="days">Days</option>
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
                <button type="button" class="remove-package mt-4 text-red-500">Delete Package</button>
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

      packageForms.forEach((form) => {
        // Get all form inputs
        const formInputs = form.elements;

        // Find the actual index from form field names
        const titleField = form.querySelector('input[name^="packages["]');
        const indexMatch = titleField.name.match(/packages\[(\d+)\]/);
        const index = indexMatch ? indexMatch[1] : 0;

        const packageData = {
          title: formInputs[`packages[${index}][title]`].value,
          description: formInputs[`packages[${index}][description]`].value,
          price: formInputs[`packages[${index}][price]`].value,
          job_type: formInputs[`packages[${index}][job_type]`].value,
          lead_time: formInputs[`packages[${index}][lead_time]`].value,
          lead_time_unit: formInputs[`packages[${index}][lead_time_unit]`].value,
          items: Array.from(form.querySelectorAll(`input[name="packages[${index}][items][]"]`))
            .map(input => input.value)
            .filter(item => item.trim() !== '')
        };

        // Only add package if it has required fields
        if (packageData.title && packageData.job_type) {
          packages.push(packageData);
        }
      });

      console.log('Packages to save:', packages);

      fetch(`/profile/${dashboardType}/${userId}/packages/update`, { // Remove 'api' prefix
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
              console.error('Raw response:', text);
              throw new Error('Network response was not ok');
            });
          }
          return response.json();
        })
        .then(data => {
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
          showFailureNotification(error.message);
          console.error('Error:', error);
        });
    };
    document.querySelector('#save-packages').addEventListener('click', savePackages);
  });
</script>
