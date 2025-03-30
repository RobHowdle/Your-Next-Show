<x-app-layout :dashboardType="$dashboardType" :modules="$modules">
  <x-slot name="header">
    <x-sub-nav :userId="$userId" />
  </x-slot>

  <div class="relative min-h-screen">
    <div class="relative mx-auto w-full max-w-screen-2xl py-8">
      <div class="px-4">
        {{-- Header Section --}}
        <div class="relative mb-8">
          {{-- Background with overlay --}}
          <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-gray-900 via-black to-gray-900 opacity-75"></div>

          {{-- Content --}}
          <div class="relative px-4 py-6 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-7xl">
              <div class="flex items-center justify-between">
                <div>
                  <h1 class="font-heading text-2xl font-bold text-white md:text-3xl">Manage Team</h1>
                  <p class="mt-2 text-gray-400">Add or remove team members from your service</p>
                </div>
                @if ($userRole === 'service-owner')
                  <a href="{{ route('admin.dashboard.new-user', ['dashboardType' => $dashboardType]) }}"
                    class="inline-flex items-center rounded-lg bg-yns_yellow px-4 py-2 text-sm font-medium text-gray-900 transition duration-200 hover:bg-yellow-400">
                    <i class="fas fa-user-plus mr-2"></i>
                    Add Team Member
                  </a>
                @endif
              </div>
            </div>
          </div>
        </div>

        <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
          <div class="flex flex-wrap items-center gap-3">
            <select id="roleFilter"
              class="rounded-lg border border-gray-800 bg-black/50 px-3 py-2 text-sm text-white focus:border-gray-700 focus:ring-0">
              <option value="all">All Roles</option>
              <option value="service-owner">Owners</option>
              <option value="service-manager">Managers</option>
              <option value="service-member">Team Members</option>
            </select>

            <select id="sortBy"
              class="rounded-lg border border-gray-800 bg-black/50 px-3 py-2 text-sm text-white focus:border-gray-700 focus:ring-0">
              <option value="name">Sort by Name</option>
              <option value="date">Sort by Date Added</option>
              <option value="role">Sort by Role</option>
            </select>
          </div>

          <div class="relative">
            <input type="text" id="searchFilter"
              class="rounded-lg border border-gray-800 bg-black/50 py-2 pl-9 pr-4 text-sm text-white placeholder-gray-500 focus:border-gray-700 focus:ring-0"
              placeholder="Search team members...">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
              <i class="fas fa-search text-gray-500"></i>
            </div>
          </div>
        </div>

        {{-- Users Grid --}}
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3" id="user-list">
          {{-- Users will be dynamically inserted here --}}
        </div>
      </div>
    </div>
  </div>

  {{-- Add this before closing </x-app-layout> --}}
  <div id="editRoleModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black/70 backdrop-blur-sm"></div>
    <div class="fixed inset-0 overflow-y-auto">
      <div class="flex min-h-full items-center justify-center p-4">
        <div
          class="relative w-full max-w-md rounded-xl border border-gray-800 bg-gray-900/95 p-6 shadow-xl backdrop-blur-sm">
          <h3 class="mb-4 font-heading text-xl font-bold text-white">Edit User Role</h3>
          <div class="mb-6">
            <p class="mb-2 text-sm text-gray-400">User:</p>
            <div class="flex items-center gap-3 rounded-lg border border-gray-800 bg-black/50 p-3">
              <div
                class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-800 text-lg font-bold text-white"
                id="userInitials"></div>
              <div>
                <p class="font-medium text-white" id="userName"></p>
                <p class="text-sm text-gray-400" id="userEmail"></p>
              </div>
            </div>
          </div>
          <div class="mb-6">
            <label for="roleSelect" class="mb-2 block text-sm font-medium text-gray-400">Role</label>
            <select id="roleSelect"
              class="w-full rounded-lg border border-gray-800 bg-black/50 px-3 py-2 text-white focus:border-gray-700 focus:ring-0">
            </select>
          </div>
          <div class="flex justify-end gap-3">
            <button type="button" id="cancelEditRole"
              class="rounded-lg border border-gray-800 bg-transparent px-4 py-2 text-sm font-medium text-white transition hover:bg-gray-800">
              Cancel
            </button>
            <button type="button" id="saveRole"
              class="rounded-lg bg-yns_yellow px-4 py-2 text-sm font-medium text-gray-900 transition hover:bg-yellow-400">
              Save Changes
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
<script src="https://cdn.jsdelivr.net/npm/dayjs@1.11.10/dayjs.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/dayjs@1.11.10/plugin/relativeTime.min.js"></script>

<script>
  jQuery(document).ready(function() {
    const dashboardType = "{{ $dashboardType }}";
    dayjs.extend(dayjs_plugin_relativeTime)

    // Add this helper function
    function formatRoleName(roleName) {
      switch (roleName) {
        case 'service-owner':
          return 'Owner';
        case 'service-manager':
          return 'Manager';
        case 'service-member':
          return 'Team Member';
        default:
          return 'N/A';
      }
    }

    let allUsers = []; // Store all users for filtering

    function filterAndSortUsers(users) {
      const roleFilter = jQuery('#roleFilter').val();
      const sortBy = jQuery('#sortBy').val();
      const searchTerm = jQuery('#searchFilter').val().toLowerCase();

      let filteredUsers = users.filter(user => {
        const matchesRole = roleFilter === 'all' || user.role_name === roleFilter;
        const matchesSearch = searchTerm === '' ||
          `${user.first_name} ${user.last_name}`.toLowerCase().includes(searchTerm) ||
          user.email.toLowerCase().includes(searchTerm);
        return matchesRole && matchesSearch;
      });

      filteredUsers.sort((a, b) => {
        switch (sortBy) {
          case 'name':
            return `${a.first_name} ${a.last_name}`.localeCompare(`${b.first_name} ${b.last_name}`);
          case 'date':
            return new Date(b.pivot.created_at) - new Date(a.pivot.created_at);
          case 'role':
            const roleOrder = {
              'service-owner': 1,
              'service-manager': 2,
              'service-member': 3
            };
            return roleOrder[a.role_name] - roleOrder[b.role_name];
          default:
            return 0;
        }
      });

      return filteredUsers;
    }

    function renderUsers(users) {
      const userList = jQuery('#user-list');
      userList.empty();

      const filteredUsers = filterAndSortUsers(users);

      if (filteredUsers.length > 0) {
        filteredUsers.forEach(user => {
          userList.append(`
        <div class="group relative rounded-xl border border-gray-800 bg-gray-900/60 p-6 backdrop-blur-sm transition hover:border-gray-700">
                        <div class="mb-4 flex items-center space-x-4">
                            <div class="h-12 w-12 flex-shrink-0 overflow-hidden rounded-full bg-gray-800">
                                <div class="flex h-full w-full items-center justify-center bg-gray-800 text-xl font-bold text-white">
                                    ${user.first_name[0]}${user.last_name[0]}
                                </div>
                            </div>
                            <div>
                                <h3 class="font-heading text-lg font-semibold text-white">${user.first_name} ${user.last_name}</h3>
                                <p class="text-sm text-gray-400">${user.email}</p>
                            </div>
                        </div>

                        <div class="mb-4 space-y-2">
                          <div class="flex items-center justify-between">
                              <span class="text-sm text-gray-400">Role</span>
                              <span class="rounded-lg bg-gray-800 px-2 py-1 text-xs font-medium text-white">
                                  ${formatRoleName(user.role_name)}
                              </span>
                          </div>
                        </div>

          <div class="mt-4 flex items-center justify-end gap-2">
          ${@json($userRole) === 'service-owner' ? `
            <a href="#" class="edit-user-btn inline-flex items-center justify-center rounded-lg bg-gray-800 p-2 text-gray-300 transition hover:bg-gray-700 hover:text-white" data-user-id="${user.id}" title="Edit">
              <i class="fas fa-pencil-alt"></i>
            </a>
            <button type="button" class="remove-user-btn inline-flex items-center justify-center rounded-lg bg-red-600/10 p-2 text-red-500 transition hover:bg-red-600/20" data-user-id="${user.id}" data-service-id="${user.serviceId}" title="Remove">
              <i class="fas fa-user-minus"></i>
            </button>
          ` : ''}
          </div>
                `);
        });
      } else {
        userList.append(`
          <div class="col-span-full rounded-xl border border-gray-800 bg-gray-900/60 p-8 text-center backdrop-blur-sm">
            <div class="mx-auto mb-4 h-12 w-12 rounded-full bg-gray-800 p-3 text-gray-400">
              <i class="fas fa-users"></i>
            </div>
            <h3 class="mb-2 font-heading text-lg font-semibold text-white">No Results Found</h3>
            <p class="text-sm text-gray-400">Try adjusting your filters or search terms</p>
          </div>
        `);
      }
    }

    function fetchUsers() {
      $.ajax({
        url: '{{ route('admin.dashboard.get-users', ['dashboardType' => '__dashboardType__']) }}'
          .replace('__dashboardType__', dashboardType),
        type: 'GET',
        success: function(response) {
          if (Array.isArray(response.relatedUsers) && response.relatedUsers.length > 0) {
            const service = response.relatedUsers[0]; // Get the first service
            if (Array.isArray(service.linked_users) && service.linked_users.length > 0) {
              allUsers = service.linked_users.map(user => ({
                ...user,
                serviceId: service.id // Add service ID to each user
              }));
              renderUsers(allUsers);
            }
          }
        },
        error: function(xhr) {
          showFailureNotification(xhr.responseJSON.message);
        }
      });
    }

    // Add event listeners for filters
    jQuery('#roleFilter, #sortBy').on('change', function() {
      renderUsers(allUsers);
    });

    jQuery('#searchFilter').on('input', debounce(function() {
      renderUsers(allUsers);
    }, 300));

    // Add this after your existing event listeners
    jQuery('#user-list').on('click', '.edit-user-btn', function(e) {
      e.preventDefault();
      const userId = jQuery(this).data('user-id');
      const user = allUsers.find(u => u.id === userId);
      const currentUserRole = @json($userRole);

      // Set up available roles based on current user's role
      const roleOptions = [];
      if (currentUserRole === 'service-owner') {
        roleOptions.push({
          value: 'service-owner',
          label: 'Owner'
        }, {
          value: 'service-manager',
          label: 'Manager'
        }, {
          value: 'service-member',
          label: 'Team Member'
        });
      } else if (currentUserRole === 'service-manager' && user.role_name !== 'service-owner') {
        roleOptions.push({
          value: 'service-manager',
          label: 'Manager'
        }, {
          value: 'service-member',
          label: 'Team Member'
        });
      } else {
        return; // Team members can't edit roles
      }

      // Populate modal
      jQuery('#userInitials').text(`${user.first_name[0]}${user.last_name[0]}`);
      jQuery('#userName').text(`${user.first_name} ${user.last_name}`);
      jQuery('#userEmail').text(user.email);

      const roleSelect = jQuery('#roleSelect');
      roleSelect.empty();
      roleOptions.forEach(role => {
        roleSelect.append(
          `<option value="${role.value}" ${user.role_name === role.value ? 'selected' : ''}>${role.label}</option>`
          );
      });

      // Store user ID for save handler
      roleSelect.data('user-id', userId);

      // Show modal
      jQuery('#editRoleModal').removeClass('hidden');
    });

    // Handle modal close
    jQuery('#cancelEditRole').on('click', function() {
      jQuery('#editRoleModal').addClass('hidden');
    });

    // Handle role save
    jQuery('#saveRole').on('click', function() {
      const userId = jQuery('#roleSelect').data('user-id');
      const newRole = jQuery('#roleSelect').val();
      const serviceId = allUsers.find(u => u.id === userId).serviceId;

      $.ajax({
        url: '{{ route('admin.dashboard.update-user-role', ['dashboardType' => '__dashboardType__']) }}'
          .replace('__dashboardType__', dashboardType),
        type: 'POST',
        data: {
          _token: '{{ csrf_token() }}',
          user_id: userId,
          service_id: serviceId,
          role: newRole
        },
        success: function(response) {
          showSuccessNotification(response.message);
          jQuery('#editRoleModal').addClass('hidden');
          fetchUsers(); // Refresh the user list
        },
        error: function(xhr) {
          showFailureNotification(xhr.responseJSON.message);
        }
      });
    });

    // Debounce helper function
    function debounce(func, wait) {
      let timeout;
      return function executedFunction(...args) {
        const later = () => {
          clearTimeout(timeout);
          func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
      };
    }

    fetchUsers();

    jQuery('#user-list').on('click', '.remove-user-btn', function(e) {
      e.preventDefault();

      const userId = jQuery(this).data('user-id');
      const serviceId = jQuery(this).data('service-id');
      const url =
        '{{ route('admin.dashboard.delete-user', ['dashboardType' => '__dashboardType__', 'id' => '__userId__']) }}'
        .replace('__dashboardType__', dashboardType)
        .replace('__userId__', userId);

      showConfirmationNotification({
        text: "You are removing this user from this promotions company"
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: url,
            type: 'DELETE',
            data: {
              _token: '{{ csrf_token() }}',
              user_id: userId,
              service_id: serviceId,
            },
            success: function(response) {
              showSuccessNotification(response.message);
              location.reload(); // Reload the page to update the user list
            },
            error: function(xhr) {
              showFailureNotification(xhr.responseJSON.message);
            }
          });
        }
      });
    });
  });
</script>
