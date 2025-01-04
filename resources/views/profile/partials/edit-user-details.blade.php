<section>
  <header>
    <h2 class="text-md font-heading font-medium text-white">
      {{ __('Change your user details') }}
    </h2>
  </header>
  <form method="POST" action="{{ route('profile.update', ['dashboardType' => $dashboardType, 'user' => $user->id]) }}"
    class="mt-6 space-y-6">
    @csrf
    @method('PUT')
    <div>
      <x-input-label-dark for="firstName" :value="__('First Name')" />
      <x-text-input id="firstName" class="mt-1 block w-full" type="text" name="firstName" :value="old('firstName', $firstName ?? '')" required
        autofocus autocomplete="firstName" />
      <x-input-error :messages="$errors->get('firstName')" class="mt-2" />
    </div>
    <div class="mt-4">
      <x-input-label-dark for="lastName" :value="__('Last Name')" />
      <x-text-input id="lastName" class="mt-1 block w-full" type="text" name="lastName" :value="old('lastName', $lastName ?? '')" required
        autofocus autocomplete="lastName" />
      <x-input-error :messages="$errors->get('lastName')" class="mt-2" />
    </div>

    <div class="mt-4">
      <x-input-label-dark for="email" :value="__('Email')" />
      <x-text-input id="email" class="mt-1 block w-full" type="email" name="email" :value="old('email', $email ?? '')" required
        autocomplete="username" />
      <x-input-error :messages="$errors->get('email')" class="mt-2" />
    </div>

    <div class="mt-4">
      <x-input-label-dark for="password" :value="__('Password')" />
      <x-text-input id="password" class="mt-1 block w-full" type="password" name="password"
        autocomplete="new-password" />
      <x-input-error :messages="$errors->get('password')" class="mt-2" />
    </div>

    <div class="mt-4">
      <x-input-label-dark for="password_confirmation" :value="__('Confirm Password')" />
      <x-text-input id="password_confirmation" class="mt-1 block w-full" type="password" name="password_confirmation"
        autocomplete="new-password" />
      <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
    </div>

    <div class="group mb-6">
      @php
        $dataId = 1;
      @endphp
      <x-google-address-picker :postalTown="old('postal_town', $postalTown ?? '')" :dataId="$dataId" id="location_{{ $dataId }}" name="location"
        label="Location" placeholder="Enter an address" :value="old('location', $location ?? '')" :latitude="old('latitude', $latitude ?? '')" :longitude="old('longitude', $longitude ?? '')" />
    </div>

    {{-- <div class="mt-4">
      <table id="role-table" class="min-w-full table-auto border-collapse">
        <thead>
          <tr>
            <th class="px-4 py-2 text-left">Current Role(s)</th>
            <th class="px-4 py-2 text-left">Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($userRole as $role)
            <tr class="border-b">
              <td class="px-4 py-2 capitalize">{{ $role->name }}</td>
              <td class="px-4 py-2">
                <x-button type="button" id="edit-role" data-role-id="{{ $role->id }}"
                  data-role-name="{{ $role->name }}" label="Edit"></x-button>
                <x-button type="button" id="delete-role" data-role-id="{{ $role->id }}"
                  data-role-name="{{ $role->name }}" label="Delete"></x-button>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>

      <div class="mt-4">
        <x-button type="button" id="add-role" label="Add Role"></x-button>
      </div>
    </div> --}}

    <div id="editRoleModal" class="fixed inset-0 hidden items-center justify-center bg-gray-500 bg-opacity-75">
      <div class="rounded bg-black p-4 text-white shadow-md">
        <h2 id="editModalTitle" class="text-lg font-semibold">Edit Role</h2>
        <input type="hidden" id="edit-role-id" />
        <div class="mt-2">
          <x-input-label-dark for="role" :value="__('Role')" />
          <select id="role" name="role"
            class="mt-1 block w-full rounded-md border-yns_red capitalize shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600"
            autofocus autocomplete="role">
            @foreach ($roles as $role)
              <option value="{{ $role->id }}" {{ $userRole->first()->id == $role->id ? 'selected' : '' }}>
                {{ $role->name }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="mt-4">
          <button id="save-edit-role-btn" class="rounded bg-blue-500 px-4 py-2 text-white">Save Changes</button>
          <button onclick="closeModal('editRoleModal')" class="rounded bg-gray-300 px-4 py-2 text-black">Cancel</button>
        </div>
      </div>
    </div>

    @php
      $userRoleIds = $userRole->pluck('id')->toArray();
    @endphp

    <div id="addRoleModal" tabindex="-1" class="fixed inset-0 z-[9999] hidden place-items-center px-4">
      <div class="relative mx-auto w-full max-w-4xl border border-white bg-white dark:bg-black">
        <div class="review-popup relative rounded-lg bg-white dark:bg-black">
          <div class="flex items-center justify-between rounded-t border-b p-4 md:p-5">
            <div class="group">
              <h3 class="text-xl font-semibold">
                Add Role
              </h3>
              <span>Add a new role to your account</span>
            </div>
            <button type="button" data-modal-hide="review-modal" class="text-white hover:text-yns_light_gray">
              <span class="fas fa-times"></span>
              <span class="sr-only">Close modal</span>
            </button>
          </div>
          <div class="p-4 md:p-5">
            <input type="hidden" id="add-role-id" />
            <select type="select" id="new-role" name="role"
              class="w-full rounded-md border-yns_red shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-yns_red dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600"
              required autofocus autocomplete="role">
              @foreach ($roles as $role)
                <option value="{{ $role->id }}" @if (in_array($role->id, $userRoleIds)) disabled @endif>
                  {{ ucfirst($role->name) }}</option>
              @endforeach
            </select>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />

            <div class="mt-4 flex flex-row gap-6">
              <x-button id="save-add-role-btn" label="Add Role"></x-button>
              <x-button onclick="closeModal('addRoleModal')" label="Cancel"></x-button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div id="deleteRoleModal" class="fixed inset-0 hidden items-center justify-center bg-gray-500 bg-opacity-75">
      <div class="w-96 rounded bg-black p-8 text-white shadow-md">
        <h2 id="deleteModalTitle" class="text-lg font-semibold">Delete Role</h2>
        <input type="hidden" id="add-role-id" />
        <div class="mt-2">
          <p>Are you sure you want to delete this role? You will loose all access to this dashboard.</p>
        </div>
        <div class="mt-4 flex flex-row gap-6">
          <x-button id="save-delete-role-btn" label="Delete Role"></x-button>
          <x-button onclick="closeModal('deleteRoleModal')" label="Cancel"></x-button>
        </div>
      </div>
    </div>

    <div class="flex items-center gap-4">
      <button type="submit"
        class="mt-8 rounded-lg border border-white bg-white px-4 py-2 font-heading font-bold text-black transition duration-150 ease-in-out hover:border-yns_yellow hover:text-yns_yellow">Save</button>
      @if (session('status') === 'profile-updated')
        <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
          class="text-sm text-gray-600 dark:text-gray-400">{{ __('Saved.') }}</p>
      @endif
    </div>
  </form>
</section>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const addRoleBtn = document.getElementById('add-role');
    const deleteRoleBtns = document.querySelectorAll('#delete-role');

    if (addRoleBtn) {
      addRoleBtn.addEventListener('click', showAddRoleModal);
    }

    deleteRoleBtns.forEach(button => {
      button.addEventListener('click', function(event) {
        const roleId = event.target.getAttribute('data-role-id');
        const roleName = event.target.getAttribute('data-role-name');
        showDeleteRoleModal(roleId, roleName);
      });
    });
  })

  function showEditRoleModal(roleId, roleName) {
    const editModalTitle = document.getElementById('editModalTitle');
    const editRoleInput = document.getElementById('edit-role-name');
    const saveEditButton = document.getElementById('save-edit-role-btn');

    editModalTitle.textContent = 'Edit Role';
    editRoleInput.value = roleName;
    document.getElementById('edit-role-id').value = roleId;

    saveEditButton.onclick = function() {
      saveEditRole(roleId);
    };

    // Remove the hidden class to show the edit modal
    document.getElementById('editRoleModal').classList.remove('hidden');
  }

  function showAddRoleModal() {
    const addModalTitle = document.getElementById('addModalTitle');
    const saveAddButton = document.getElementById('save-add-role-btn');
    const dashboardType = "{{ $dashboardType }}";
    const userId = "{{ $user->id }}";

    saveAddButton.onclick = function() {
      const roleSelect = document.getElementById('new-role');
      saveAddRole(roleSelect.value);
    };

    // Remove the hidden class to show the add modal
    document.getElementById('addRoleModal').classList.remove('hidden');
    document.getElementById('addRoleModal').classList.add('flex');
  }

  function showDeleteRoleModal(roleId, roleName) {
    const deleteModalTitle = document.getElementById('deleteModalTitle');
    const saveDeleteButton = document.getElementById('save-delete-role-btn');

    deleteModalTitle.textContent = 'Delete Role';

    // Store role information on the button
    saveDeleteButton.setAttribute('data-role-id', roleId);
    saveDeleteButton.setAttribute('data-role-name', roleName);

    // Update the button's onclick to handle deletion
    saveDeleteButton.onclick = function() {
      const roleId = saveDeleteButton.getAttribute('data-role-id');
      saveDeleteRole(roleId);
    };

    // Show the modal
    document.getElementById('deleteRoleModal').classList.remove('hidden');
  }

  // Close modal function
  window.closeModal = function(modalId) {
    document.getElementById(modalId).classList.add('hidden');
  }

  function saveAddRole(roleSelect) {
    const dashboardType = "{{ $dashboardType }}";
    const userId = "{{ $user->id }}";

    if (!roleSelect) {
      showFailureNotification('Please select a role');
      return;
    }

    fetch(`/profile/${dashboardType}/${userId}/add-role`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: JSON.stringify({
          id: userId,
          roleId: roleSelect,
          dashboardType: dashboardType,
        }),
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Close the modal
          closeModal('addRoleModal');
          showSuccessNotification(data.message);

          // Update the roles table dynamically
          const roleTableBody = document.querySelector('#role-table tbody');

          // Create a new row for the added role
          const newRow = document.createElement('tr');
          newRow.classList.add('border-b');
          newRow.innerHTML = `
                <td class="px-4 py-2 capitalize">${data.newRoleName}</td>
                <td class="px-4 py-2">
                    <x-button type="button" id="edit-role" data-role-id="${data.newRoleId}" data-role-name="${data.newRoleName}" label="Edit"></x-button>
                </td>
            `;

          // Append the new row to the table
          roleTableBody.appendChild(newRow);
        } else {
          showFailureNotification(data.message || 'Failed to add role');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showFailureNotification('An error occurred while adding the role');
      });
  }

  function removeRole(userId, roleId) {
    console.log(`Removing role ${roleId} for user ${userId}`);
  }

  function saveEditRole(roleId) {
    const newRole = document.getElementById('role').value;
    const dashboardType = "{{ $dashboardType }}";
    const userId = "{{ $user->id }}";

    fetch(`/profile/${dashboardType}/${userId}/edit-role`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: JSON.stringify({
          roleId: roleId,
          newRole: newRole,
        }),
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Close the modal
          closeModal('editRoleModal');
          showSuccessNotification(data.message);

          // Add new row to the table
          const table = document.getElementById('role-table');
          const newRow = table.insertRow();

          // Insert cells into the row and populate with the new role data
          const roleCell = newRow.insertCell(0);
          const actionCell = newRow.insertCell(1);

          roleCell.textContent = newRole;

          // You can also add an action button (e.g., delete/edit) to this row
          const editButton = document.createElement('button');
          editButton.textContent = 'Edit';
          editButton.className = 'btn btn-warning';
          editButton.onclick = () => {
            // Trigger the edit role modal, passing the current role data
            openEditRoleModal(roleId, newRole);
          };

          actionCell.appendChild(editButton);

          // Optional: You may also want to clear the form or reset the inputs
          document.getElementById('role').value = '';
        } else {
          showFailureNotification(data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showFailureNotification('An unexpected error occurred: ' + error.message);
      });
  }

  function saveDeleteRole(roleId) {
    const dashboardType = "{{ $dashboardType }}";
    const userId = "{{ $user->id }}";

    fetch(`/profile/${dashboardType}/${userId}/delete-role`, {
        method: 'DELETE',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({
          id: userId,
          roleId: roleId,
          dashboardType: dashboardType
        }),
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          showSuccessNotification('Role deleted successfully');
          closeModal('deleteRoleModal');
          location.reload();
        } else {
          showFailureNotification('Failed to delete role');
        }
      })
      .catch(error => {
        console.error('Error deleting role:', error);
        showFailureNotification('Error occurred while deleting role');
      });
  }
</script>
