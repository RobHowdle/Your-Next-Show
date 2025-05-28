<header>
  <h2 class="text-md font-heading font-medium text-white">{{ __('Members') }}</h2>
</header>

<!-- Show any flash messages -->
@if (session('success'))
  <div class="mb-4 rounded-lg bg-green-100 p-4 text-green-700">
    {{ session('success') }}
  </div>
@endif

@if (session('error'))
  <div class="mb-4 rounded-lg bg-red-100 p-4 text-red-700">
    {{ session('error') }}
  </div>
@endif

<form id="members-form" method="POST"
  action="{{ route('artist.update', ['dashboardType' => $dashboardType, 'user' => $user->id]) }}"
  enctype="multipart/form-data">
  @csrf
  @method('PUT')

  <input type="hidden" name="about" value="{{ $profileData['about'] ?? '' }}">
  <input type="hidden" name="name" value="{{ $profileData['name'] ?? '' }}">
  <input type="hidden" name="artist_id" value="{{ $profileData['artistId'] ?? '' }}">

  <!-- Create a hidden input for members_json that will be populated on submit -->
  <input type="hidden" id="members_json" name="members_json" value="">

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
  // Helper functions for notifications
  // ...existing code...

  document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('members-form');
    const container = document.querySelector('.members-container');
    const addButton = document.getElementById('add-member');
    const membersJsonInput = document.getElementById('members_json');

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
          // Get the current member row and index
          const memberRow = e.target.closest('.member-row');
          const memberIndex = Array.from(container.children).indexOf(memberRow);

          // Create a preview of the selected image
          const reader = new FileReader();
          const previewImg = e.target.closest('.profile-pic-container').querySelector('.member-preview-img');

          reader.onload = function(event) {
            previewImg.src = event.target.result;

            // Mark that this member has a new image
            memberRow.setAttribute('data-has-new-image', 'true');
            console.log(`New image selected for member ${memberIndex}`);
          };

          reader.readAsDataURL(file);
        }
      }
    });

    // Prepare the form before submission
    form.addEventListener('submit', function(e) {
      // Collect member data for the hidden JSON field
      const members = [];
      const memberRows = document.querySelectorAll('.member-row');

      memberRows.forEach((row, index) => {
        const nameInput = row.querySelector('input[name*="[name]"]');
        const roleInput = row.querySelector('input[name*="[role]"]');
        const bioTextarea = row.querySelector('textarea[name*="[bio]"]');
        const profilePicInput = row.querySelector('.member-profile-path');

        // Create a member object
        const member = {
          name: nameInput ? nameInput.value || 'Band Member' : 'Band Member',
          role: roleInput ? roleInput.value || '' : '',
          bio: bioTextarea ? bioTextarea.value || '' : '',
          profile_pic: profilePicInput ? profilePicInput.value || '' : ''
        };

        members.push(member);
      });

      // Update the hidden input with JSON representation
      membersJsonInput.value = JSON.stringify(members);
      console.log('Members JSON prepared for submission:', membersJsonInput.value);

      // Switch to AJAX submission
      e.preventDefault(); // Prevent normal form submission

      // Get the form data
      const formData = new FormData(this);

      // Show a loading spinner
      const submitButton = this.querySelector('button[type="submit"]');
      const originalButtonText = submitButton.innerHTML;
      submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
      submitButton.disabled = true;

      // Submit the form via AJAX
      $.ajax({
        url: this.action,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
          // Handle success
          showSuccessNotification(response.message);
          window.location.href = response.redirect;
          console.log('Response:', response);
          submitButton.innerHTML = originalButtonText;
          submitButton.disabled = false;
        },
        error: function(xhr) {
          // Handle error
          showFailureNotification(xhr);
          console.error('Error:', xhr);
          submitButton.innerHTML = originalButtonText;
          submitButton.disabled = false;
        }
      })
    })
  });
</script>

<style>
  .profile-pic-container:hover .member-preview-img {
    opacity: 0.7;
  }
</style>
