<header>
  <h2 class="text-md font-heading font-medium text-white">{{ __('Band Members') }}</h2>
  <p class="text-sm text-gray-400">Manage your band's lineup</p>
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
  <input type="hidden" id="members_json" name="members_json" value="">

  <!-- Existing Band Members -->
  @php
    $existingMembers =
        isset($profileData['members']) && is_array($profileData['members']) && count($profileData['members']) > 0
            ? $profileData['members']
            : [];
  @endphp

  <div class="my-6">
    <h3 class="mb-4 text-lg font-semibold text-white">Current Lineup</h3>

    <div class="existing-members-container grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
      @foreach ($existingMembers as $index => $member)
        <div class="member-row overflow-hidden rounded-lg border border-gray-700 bg-black/30 backdrop-blur-sm">
          <!-- Member Photo -->
          <div class="relative md:w-full">
            <div class="relative aspect-square overflow-hidden bg-gray-800">
              <img
                src="{{ $member['profile_pic'] ? asset($member['profile_pic']) : asset('images/system/yns_no_image_found.png') }}"
                alt="{{ $member['name'] ?? 'Band member' }}" class="member-preview-img h-full w-full object-cover" />

              <!-- Upload photo button -->
              <div class="absolute right-3 top-3">
                <label class="cursor-pointer rounded-full bg-yns_yellow p-2 text-black hover:bg-yellow-400">
                  <i class="fas fa-camera"></i>
                  <input type="file" name="member_pic[{{ $index }}]" class="member-profile-input hidden"
                    accept="image/*" />
                </label>
              </div>
            </div>

            <div class="absolute bottom-0 left-0 w-full bg-gradient-to-t from-black/90 to-transparent p-3">
              <h4 class="truncate text-lg font-bold text-white">{{ $member['name'] ?? 'Unnamed Member' }}</h4>
              <p class="text-sm text-gray-300">{{ $member['role'] ?? 'Role not specified' }}</p>
            </div>
          </div>

          <!-- Member Details -->
          <div class="p-4">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
              <div>
                <label class="mb-1 block text-xs text-gray-400">Name</label>
                <input type="text" name="members[{{ $index }}][name]" value="{{ $member['name'] ?? '' }}"
                  class="w-full rounded-md border-gray-700 bg-black/20 text-sm text-white" required>
              </div>
              <div>
                <label class="mb-1 block text-xs text-gray-400">Role</label>
                <input type="text" name="members[{{ $index }}][role]" value="{{ $member['role'] ?? '' }}"
                  class="w-full rounded-md border-gray-700 bg-black/20 text-sm text-white">
              </div>
              <div class="md:col-span-2">
                <label class="mb-1 block text-xs text-gray-400">Bio</label>
                <textarea name="members[{{ $index }}][bio]" rows="2"
                  class="w-full rounded-md border-gray-700 bg-black/20 text-sm text-white">{{ $member['bio'] ?? '' }}</textarea>
              </div>
            </div>

            <input type="hidden" name="members[{{ $index }}][profile_pic]"
              value="{{ $member['profile_pic'] ?? '' }}" class="member-profile-path" />

            <div class="mt-3 flex justify-end">
              <button type="button" class="remove-member text-sm text-red-500 hover:text-red-700">
                <i class="fas fa-trash-alt mr-1"></i>
                Remove
              </button>
            </div>
          </div>
        </div>
      @endforeach
    </div>
    <!-- Add New Member Button -->
    <div class="my-8">
      <button type="button" id="add-member"
        class="flex items-center justify-center rounded-lg border-2 border-dashed border-gray-600 px-6 py-3 text-gray-400 transition-colors hover:border-yns_yellow hover:text-yns_yellow">
        <i class="fas fa-plus mr-2"></i>
        Add New Member
      </button>
    </div>

    <!-- New Members Container - dynamically populated -->
    <div class="members-container grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
      <!-- New members will appear here -->
    </div>

    <!-- Save Button -->
    <div class="mt-8 flex justify-end">
      <button type="submit"
        class="rounded-lg bg-yns_yellow px-6 py-2 font-bold text-black transition-colors hover:bg-yellow-400">
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
    const membersJsonInput = document.getElementById('members_json');

    // Add new member row
    addButton.addEventListener('click', function() {
      const index = document.querySelectorAll('.member-row').length;
      const newRow = `
    <div class="member-row bg-black/30 backdrop-blur-sm border border-gray-700 rounded-lg overflow-hidden">
      <div class="flex flex-col md:flex-row">
        <!-- Member Photo -->
        <div class="md:w-1/4 lg:w-1/5 relative">
          <div class="relative aspect-square overflow-hidden bg-gray-800">
            <img src="${getAssetUrl('images/user-placeholder.png')}"
              alt="New band member" class="member-preview-img w-full h-full object-cover" />
            
            <!-- Upload photo button -->
            <div class="absolute top-3 right-3">
              <label class="cursor-pointer p-2 rounded-full bg-yns_yellow text-black hover:bg-yellow-400">
                <i class="fas fa-camera"></i>
                <input type="file" name="member_pic[${index}]" class="hidden member-profile-input" accept="image/*"/>
              </label>
            </div>
          </div>
          
          <div class="absolute bottom-0 left-0 w-full p-3 bg-gradient-to-t from-black/90 to-transparent md:hidden">
            <h4 class="truncate text-lg font-bold text-white">New Member</h4>
            <p class="text-sm text-gray-300">Add role</p>
          </div>
        </div>
        
        <!-- Member Details -->
        <div class="p-4 md:w-3/4 lg:w-4/5">
          <div class="hidden md:block mb-3">
            <h4 class="text-lg font-bold text-white">New Member</h4>
            <p class="text-sm text-gray-300">Add role</p>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs text-gray-400 mb-1">Name</label>
              <input type="text" name="members[${index}][name]" placeholder="Member name"
                class="w-full rounded-md border-gray-700 bg-black/20 text-sm text-white" required>
            </div>
            <div>
              <label class="block text-xs text-gray-400 mb-1">Role</label>
              <input type="text" name="members[${index}][role]" placeholder="e.g. Guitarist"
                class="w-full rounded-md border-gray-700 bg-black/20 text-sm text-white">
            </div>
            <div class="md:col-span-2">
              <label class="block text-xs text-gray-400 mb-1">Bio</label>
              <textarea name="members[${index}][bio]" rows="2" placeholder="Short bio..."
                class="w-full rounded-md border-gray-700 bg-black/20 text-sm text-white"></textarea>
            </div>
          </div>
          
          <input type="hidden" name="members[${index}][profile_pic]" value="" class="member-profile-path" />
          
          <div class="flex justify-end mt-3">
            <button type="button" class="remove-member text-sm text-red-500 hover:text-red-700">
              <i class="fas fa-trash-alt mr-1"></i>
              Remove
            </button>
          </div>
        </div>
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
          // Get the current member row
          const memberRow = e.target.closest('.member-row');

          // Find the image preview - updated selector to match your HTML structure
          const previewImg = memberRow.querySelector('.member-preview-img');

          if (previewImg) {
            // Create a preview of the selected image
            const reader = new FileReader();

            reader.onload = function(event) {
              previewImg.src = event.target.result;

              // Mark that this member has a new image
              memberRow.setAttribute('data-has-new-image', 'true');
              console.log('New image selected for member');
            };

            reader.readAsDataURL(file);
          } else {
            console.error('Preview image element not found');
          }
        }
      }
    });

    // Prepare the form before submission
    form.addEventListener('submit', function(e) {
      // Collect member data for the hidden JSON field
      const members = [];

      // Get existing members
      const existingMemberRows = document.querySelectorAll('.existing-members-container .member-row');
      existingMemberRows.forEach((row, index) => {
        collectMemberData(row, members);
      });

      // Get new members
      const newMemberRows = document.querySelectorAll('.members-container .member-row');
      newMemberRows.forEach((row, index) => {
        collectMemberData(row, members);
      });

      // Helper function to collect member data
      function collectMemberData(row, membersArray) {
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

        membersArray.push(member);
      }
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

          console.log('Response:', response);
          submitButton.innerHTML = originalButtonText;
          submitButton.disabled = false;

          // Add a small delay before redirect to ensure notification is shown
          setTimeout(function() {
            if (response.redirect) {
              window.location.href = response.redirect;
            } else {
              // Fallback to reloading the current page if no redirect URL is provided
              window.location.reload();
            }
          }, 1000);
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
