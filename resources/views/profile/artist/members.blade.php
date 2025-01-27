<header>
  <h2 class="text-md font-heading font-medium text-white">{{ __('Members') }}</h2>
</header>

<form id="members-form" method="POST">
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
      <div class="member-row flex gap-4">
        <div class="w-1/2">
          <x-input-label-dark>Member Name</x-input-label-dark>
          <x-text-input name="members[{{ $index }}][name]" value="{{ $member['name'] ?? '' }}" class="w-full" />
        </div>
        <div class="w-1/2">
          <x-input-label-dark>Role</x-input-label-dark>
          <x-text-input name="members[{{ $index }}][role]" value="{{ $member['role'] ?? '' }}" class="w-full" />
        </div>
        <button type="button" class="remove-member mb-2 self-end text-red-500">×</button>
      </div>
    @endforeach
  </div>

  <button type="button" id="add-member" class="mt-4 text-yns_yellow hover:underline">
    + Add Member
  </button>

  <div class="mt-8">
    <button type="submit"
      class="rounded-lg border border-white bg-white px-4 py-2 font-heading font-bold text-black transition duration-150 ease-in-out hover:border-yns_yellow hover:text-yns_yellow">
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
            <div class="member-row flex gap-4">
                <div class="w-1/2">
                    <x-input-label-dark>Member Name</x-input-label-dark>
                    <x-text-input 
                        name="members[${index}][name]"
                        class="w-full"
                    />
                </div>
                <div class="w-1/2">
                    <x-input-label-dark>Role</x-input-label-dark>
                    <x-text-input 
                        name="members[${index}][role]"
                        class="w-full"
                    />
                </div>
                <button type="button" class="remove-member text-red-500 self-end mb-2">×</button>
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

    // Form submission
    form.addEventListener('submit', function(e) {
      e.preventDefault();
      const formData = new FormData(this);

      fetch(`/profile/${dashboardType}/band-profile-update/${user}`, {
          method: 'POST',
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
            }, 3000);
          } else {
            throw new Error(data.message || 'Failed to update members');
          }
        })
        .catch(error => {
          showFailureNotification(error.message);
          console.error('Error:', error);
        });
    });
  });
</script>
