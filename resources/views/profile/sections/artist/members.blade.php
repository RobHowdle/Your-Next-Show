<header>
  <h2 class="text-md font-heading font-medium text-white">{{ __('Members') }}</h2>
</header>

<form id="members-form" method="POST" enctype="multipart/form-data">
  @csrf
  @method('PUT')

  <div class="members-container space-y-4">
    @php
      $defaultMembers = [
          ['role' => 'Drums', 'name' => ''],
          ['role' => 'Bass', 'name' => ''],
          ['role' => 'Guitar', 'name' => ''],
          ['role' => 'Guitar', 'name' => ''],
          ['role' => 'Vocals', 'name' => ''],
      ];

      $members = isset($profileData['members']) ? $profileData['members'] : $defaultMembers;
    @endphp

    @foreach ($members as $index => $member)
      <div class="member-row rounded-lg border border-gray-800 bg-black/20 p-4 backdrop-blur-sm">
        <div class="flex flex-col gap-4 md:flex-row">
          <!-- Profile Picture -->
          <div class="flex w-full flex-col items-center md:w-1/4">
            <div
              class="profile-pic-container relative mb-2 h-24 w-24 overflow-hidden rounded-full border border-gray-700 bg-gray-900">
              <img src="{{ $member['profile_pic'] ?? asset('images/user-placeholder.png') }}"
                alt="{{ $member['name'] ?? 'Band member' }}" class="member-preview-img h-full w-full object-cover" />
              <div
                class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-50 opacity-0 transition-opacity hover:opacity-100">
                <label class="cursor-pointer rounded-full bg-yns_yellow p-2 text-black hover:bg-yellow-400">
                  <i class="fas fa-camera"></i>
                  <input type="file" name="member_pic[{{ $index }}]" class="member-profile-input hidden"
                    accept="image/*" />
                </label>
              </div>
            </div>
            <input type="hidden" name="members[{{ $index }}][profile_pic]"
              value="{{ $member['profile_pic'] ?? '' }}" class="member-profile-path" />
          </div>

          <!-- Member Details -->
          <div class="w-full space-y-4 md:w-3/4">
            <div class="flex flex-wrap gap-4 md:flex-nowrap">
              <div class="w-full md:w-1/2">
                <x-input-label-dark>Member Name</x-input-label-dark>
                <x-text-input name="members[{{ $index }}][name]" value="{{ $member['name'] ?? '' }}"
                  class="w-full" required />
              </div>
              <div class="w-full md:w-1/2">
                <x-input-label-dark>Role</x-input-label-dark>
                <x-text-input name="members[{{ $index }}][role]" value="{{ $member['role'] ?? '' }}"
                  class="w-full" />
              </div>
            </div>

            <div class="w-full">
              <x-input-label-dark>Bio</x-input-label-dark>
              <textarea name="members[{{ $index }}][bio]" rows="2"
                class="w-full rounded-md border-gray-700 bg-black/20 text-white shadow-sm focus:border-yns_yellow focus:ring-2 focus:ring-yns_yellow">{{ $member['bio'] ?? '' }}</textarea>
            </div>
          </div>
        </div>

        <div class="mt-2 flex justify-end">
          <button type="button" class="remove-member text-red-500 hover:text-red-700">
            <i class="fas fa-trash-alt mr-1"></i>
            Remove
          </button>
        </div>
      </div>
    @endforeach
  </div>

  <button type="button" id="add-member"
    class="mt-4 inline-flex items-center rounded-lg bg-yns_yellow px-4 py-2 text-sm font-medium text-gray-900 transition duration-200 hover:bg-yellow-400">
    <i class="fas fa-plus mr-2"></i>
    Add Member
  </button>

  <div class="mt-8">
    <button type="submit"
      class="rounded-lg border border-white bg-white px-4 py-2 font-heading font-bold text-black transition duration-150 ease-in-out hover:border-yns_yellow hover:text-yns_yellow">
      <i class="fas fa-save mr-2"></i>
      Save Members
    </button>
  </div>
</form>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('members-form');
    const container = document.querySelector('.members-container');
    const addButton = document.getElementById('add-member');
    const dashboardType = '{{ $dashboardType }}';
    const user = '{{ $user->id }}';

    // Add new member row
    addButton.addEventListener('click', function() {
      const index = container.children.length;
      const newRow = `
            <div class="member-row rounded-lg border border-gray-800 bg-black/20 p-4 backdrop-blur-sm">
                <div class="flex flex-col md:flex-row gap-4">
                    <!-- Profile Picture -->
                    <div class="w-full md:w-1/4 flex flex-col items-center">
                        <div class="mb-2 w-24 h-24 rounded-full overflow-hidden bg-gray-900 border border-gray-700 relative profile-pic-container">
                            <img src="${getAssetUrl('images/user-placeholder.png')}" 
                                alt="New band member" 
                                class="w-full h-full object-cover member-preview-img"/>
                            <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 hover:opacity-100 transition-opacity flex items-center justify-center">
                                <label class="cursor-pointer p-2 rounded-full bg-yns_yellow text-black hover:bg-yellow-400">
                                    <i class="fas fa-camera"></i>
                                    <input type="file" name="member_pic[${index}]" class="hidden member-profile-input" accept="image/*"/>
                                </label>
                            </div>
                        </div>
                        <input type="hidden" name="members[${index}][profile_pic]" value="" class="member-profile-path" />
                    </div>
                    
                    <!-- Member Details -->
                    <div class="w-full md:w-3/4 space-y-4">
                        <div class="flex gap-4 flex-wrap md:flex-nowrap">
                            <div class="w-full md:w-1/2">
                                <x-input-label-dark>Member Name</x-input-label-dark>
                                <x-text-input 
                                    name="members[${index}][name]"
                                    class="w-full"
                                />
                            </div>
                            <div class="w-full md:w-1/2">
                                <x-input-label-dark>Role</x-input-label-dark>
                                <x-text-input 
                                    name="members[${index}][role]"
                                    class="w-full"
                                />
                            </div>
                        </div>
                        
                        <div class="w-full">
                            <x-input-label-dark>Bio</x-input-label-dark>
                            <textarea name="members[${index}][bio]" rows="2" 
                                class="w-full rounded-md border-gray-700 bg-black/20 text-white shadow-sm focus:border-yns_yellow focus:ring-2 focus:ring-yns_yellow"></textarea>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end mt-2">
                    <button type="button" class="remove-member text-red-500 hover:text-red-700">
                        <i class="fas fa-trash-alt mr-1"></i>
                        Remove
                    </button>
                </div>
            </div>
        `;
      container.insertAdjacentHTML('beforeend', newRow);
    });

    // Remove member row
    container.addEventListener('click', function(e) {
      if (e.target.classList.contains('remove-member')) {
        e.target.closest('.member-row').remove();
      }
    });

    // Helper function to get asset URL
    function getAssetUrl(path) {
      return `{{ asset('') }}${path}`;
    }

    // Image file handling
    container.addEventListener('change', function(e) {
      if (e.target.classList.contains('member-profile-input')) {
        const file = e.target.files[0];
        if (file) {
          // Create a preview of the selected image
          const reader = new FileReader();
          const previewImg = e.target.closest('.profile-pic-container').querySelector('.member-preview-img');

          reader.onload = function(event) {
            previewImg.src = event.target.result;
          };

          reader.readAsDataURL(file);
        }
      }
    });

    // Form submission
    form.addEventListener('submit', function(e) {
      e.preventDefault();

      const formData = new FormData(this);
      const artistId = '{{ $profileData['artistId'] ?? '' }}';
      const user = '{{ $user->id }}';
      formData.append('artist_id', artistId);
      formData.append('user', user);

      // Log the form data for debugging
      console.log('Form submission data:');
      for (let [key, value] of formData.entries()) {
        console.log(`${key}: ${value}`);
      }

      // Display a loading spinner or message
      const submitButton = this.querySelector('button[type="submit"]');
      const originalButtonText = submitButton.innerHTML;
      submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
      submitButton.disabled = true;

      fetch(`/profile/${dashboardType}/band-profile-update/${user}`, {
          method: 'PUT', // Changed from POST to PUT to match the route definition
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
          },
          body: formData
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            showSuccessNotification('Members updated successfully');
            setTimeout(() => {
              window.location.reload(data.redirect);
            }, 1500);
          } else {
            throw new Error(data.message || 'Failed to update members');
          }
        })
        .catch(error => {
          showFailureNotification(error.message);
          console.error('Error:', error);
          // Restore button state
          submitButton.innerHTML = originalButtonText;
          submitButton.disabled = false;
        });
    });
  });
</script>

<style>
  .profile-pic-container:hover .member-preview-img {
    opacity: 0.7;
  }
</style>
