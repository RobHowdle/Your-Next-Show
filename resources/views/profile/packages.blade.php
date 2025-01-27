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
              <x-input-label-dark>Price</x-input-label-dark>
              <x-text-input type="number" name="packages[{{ $index }}][price]"
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
      div.className = 'package-card bg-gray-800 p-6 rounded-lg shadow-lg';
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
                    
                    <div class="included-items">
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

      packageForms.forEach((form, index) => {
        const formData = new FormData(form);
        const packageData = {
          title: formData.get(`packages[${index}][title]`),
          description: formData.get(`packages[${index}][description]`),
          price: formData.get(`packages[${index}][price]`),
          items: Array.from(formData.getAll(`packages[${index}][items][]`))
        };
        packages.push(packageData);
      });

      // Send AJAX request
      fetch(`/api/profile/${dashboardType}/${userId}/packages/update`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          },
          body: JSON.stringify({
            packages: packages
          })
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            showSuccessNotification(data.message);
          } else {
            throw new Error(data.message || 'Failed to save packages');
          }
        })
        .catch(error => {
          showFailureNotification(error.message);
          console.error('Error:', error);
        });
    }

    // Add save button event listener
    document.querySelector('#save-packages').addEventListener('click', savePackages);
  });
</script>
