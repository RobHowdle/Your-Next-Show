<x-app-layout :dashboardType="$dashboardType" :modules="$modules">
  <x-slot name="header">
    <x-sub-nav :userId="$userId" />
  </x-slot>

  <div class="relative min-h-screen">
    <div class="relative mx-auto w-full max-w-screen-2xl py-8">
      <div class="px-4">
        {{-- Header Section --}}
        <div class="relative mb-8">
          <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-gray-900 via-black to-gray-900 opacity-75"></div>
          <div class="relative px-4 py-6 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-7xl">
              <div class="flex items-center justify-between">
                <div>
                  <h1 class="font-heading text-2xl font-bold text-white md:text-3xl">Create New Job</h1>
                  <p class="mt-2 text-gray-400">Add a new job to your dashboard</p>
                </div>
                <a href="{{ route('admin.dashboard.jobs', ['dashboardType' => $dashboardType]) }}"
                  class="inline-flex h-10 items-center rounded-lg border border-gray-800 bg-transparent px-4 text-sm font-medium text-white transition hover:bg-gray-800">
                  <i class="fas fa-arrow-left mr-2"></i>Back to Jobs
                </a>
              </div>
            </div>
          </div>
        </div>

        {{-- Form Section --}}
        <div class="mx-auto max-w-5xl">
          <form id="new-job-form"
            action="{{ route('admin.dashboard.jobs.store', ['dashboardType' => $dashboardType]) }}" method="POST"
            class="space-y-6">
            @csrf
            <div class="rounded-xl border border-gray-800 bg-gray-900/60 p-6 backdrop-blur-sm">
              {{-- Client & Package Section --}}
              <div class="mb-6">
                <h2 class="mb-4 font-heading text-lg font-semibold text-white">Client & Package Details</h2>
                <div class="grid gap-6 md:grid-cols-2">
                  <div class="relative">
                    <label for="client_search" class="mb-2 block text-sm font-medium text-gray-400">Search for a
                      client</label>
                    <div class="relative">
                      <input type="text" id="client_search"
                        class="w-full rounded-lg border border-gray-800 bg-black/50 px-4 py-2.5 pl-10 text-white placeholder-gray-500 focus:border-gray-700 focus:ring-0"
                        placeholder="Start typing to search clients...">
                      <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <i class="fas fa-search text-gray-500"></i>
                      </div>
                    </div>
                    <input type="" id="client_id" name="client_id" required>
                    <input type="" id="client_name" name="client_name" required>
                    <input type="" id="client_service" name="client_service" required>

                    {{-- Search Results Dropdown --}}
                    <div id="client_results"
                      class="absolute z-50 mt-1 hidden w-full overflow-hidden rounded-lg border border-gray-800 bg-gray-900/95 shadow-lg backdrop-blur-sm">
                      <div class="max-h-60 overflow-y-auto py-1" id="results_container">
                        <!-- Results will be dynamically inserted here -->
                      </div>
                    </div>
                  </div>
                  <div>
                    <label for="package" class="mb-2 block text-sm font-medium text-gray-400">Package</label>
                    <select id="package" name="package"
                      class="w-full rounded-lg border border-gray-800 bg-black/50 px-4 py-2.5 text-white focus:border-gray-700 focus:ring-0">
                      <option value="">Select a package</option>
                      @foreach ($packages as $package)
                        <option value="{{ $package->id }}">{{ $package->name }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
              </div>

              {{-- Job Details Section --}}
              <div class="mb-6">
                <h2 class="mb-4 font-heading text-lg font-semibold text-white">Job Details</h2>
                <div class="grid gap-6 md:grid-cols-2">
                  <div>
                    <label for="job_priority" class="mb-2 block text-sm font-medium text-gray-400">Priority</label>
                    <select id="job_priority" name="job_priority" required
                      class="w-full rounded-lg border border-gray-800 bg-black/50 px-4 py-2.5 text-white focus:border-gray-700 focus:ring-0">
                      <option value="urgent">Urgent</option>
                      <option value="high">High</option>
                      <option value="standard" selected>Standard</option>
                      <option value="low">Low</option>
                      <option value="no-priority">No Priority</option>
                    </select>
                  </div>

                  <div>
                    <label for="job_status" class="mb-2 block text-sm font-medium text-gray-400">Status</label>
                    <select id="job_status" name="job_status" required
                      class="w-full rounded-lg border border-gray-800 bg-black/50 px-4 py-2.5 text-white focus:border-gray-700 focus:ring-0">
                      <option value="not-started" selected>Not Started</option>
                      <option value="in-progress">In Progress</option>
                      <option value="on-hold">On Hold</option>
                      <option value="waiting-for-client">Waiting For Client</option>
                      <option value="waiting-for-payment">Waiting For Payment</option>
                      <option value="completed">Completed</option>
                      <option value="cancelled">Cancelled</option>
                    </select>
                  </div>
                </div>
              </div>

              {{-- Timeline Section --}}
              <div class="mb-6">
                <h2 class="mb-4 font-heading text-lg font-semibold text-white">Timeline</h2>
                <div class="grid gap-6 md:grid-cols-3">
                  <div>
                    <label for="job_start_date" class="mb-2 block text-sm font-medium text-gray-400">Start Date</label>
                    <input type="date" id="start_date" name="job_start_date" required
                      class="w-full rounded-lg border border-gray-800 bg-black/50 px-4 py-2.5 text-white focus:border-gray-700 focus:ring-0">
                  </div>

                  <div>
                    <label for="job_end_date" class="mb-2 block text-sm font-medium text-gray-400">Deadline</label>
                    <input type="date" id="job_end_date" name="job_end_date" required
                      class="w-full rounded-lg border border-gray-800 bg-black/50 px-4 py-2.5 text-white focus:border-gray-700 focus:ring-0">
                  </div>

                  <div>
                    <label for="estimated_lead_time_value"
                      class="mb-2 block text-sm font-medium text-gray-400">Estimated Lead Time</label>
                    <div class="flex gap-2">
                      <input type="number" id="estimated_lead_time_value" name="estimated_lead_time_value" required
                        class="w-2/3 rounded-lg border border-gray-800 bg-black/50 px-4 py-2.5 text-white focus:border-gray-700 focus:ring-0"
                        placeholder="Enter time">
                      <select id="estimated_lead_time_unit" name="estimated_lead_time_unit" required
                        class="w-1/3 rounded-lg border border-gray-800 bg-black/50 px-4 py-2.5 text-white focus:border-gray-700 focus:ring-0">
                        <option value="hours">Hours</option>
                        <option value="days">Days</option>
                        <option value="weeks">Weeks</option>
                        <option value="months">Months</option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>

              {{-- Description Section --}}
              <div class="mb-6">
                <h2 class="mb-4 font-heading text-lg font-semibold text-white">Description</h2>
                <div>
                  <label for="scope" class="mb-2 block text-sm font-medium text-gray-400">Job
                    Description</label>
                  <textarea id="scope" name="scope" rows="4" required
                    class="w-full rounded-lg border border-gray-800 bg-black/50 px-4 py-2.5 text-white placeholder-gray-500 focus:border-gray-700 focus:ring-0"
                    placeholder="Enter job description"></textarea>
                </div>
              </div>

              {{-- job_cost Section --}}
              <div class="mb-6">
                <h2 class="mb-4 font-heading text-lg font-semibold text-white">job_cost</h2>
                <div class="grid gap-6 md:grid-cols-2">
                  <div>
                    <label for="job_cost" class="mb-2 block text-sm font-medium text-gray-400">job_cost Amount</label>
                    <div class="relative">
                      <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">£</span>
                      <input type="number" id="job_cost" name="job_cost" step="0.01" required
                        class="w-full rounded-lg border border-gray-800 bg-black/50 py-2.5 pl-8 pr-4 text-white placeholder-gray-500 focus:border-gray-700 focus:ring-0"
                        placeholder="0.00">
                    </div>
                  </div>
                </div>
              </div>

              {{-- File Upload Section --}}
              <div class="mb-6">
                <h2 class="mb-4 font-heading text-lg font-semibold text-white">Attachments</h2>
                <div class="rounded-lg border-2 border-dashed border-gray-800 bg-black/30 p-6">
                  <div class="flex flex-col items-center justify-center">
                    <i class="fas fa-cloud-upload-alt mb-3 text-3xl text-gray-500"></i>
                    <p class="mb-2 text-sm text-gray-400">Drag and drop files here, or</p>
                    <label for="job_scope_file" class="cursor-pointer">
                      <span class="text-sm text-blue-400 hover:text-blue-300">Browse files</span>
                      <input id="job_scope_file" name="attachments[]" type="file" multiple class="hidden"
                        accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png">
                    </label>
                  </div>
                  <div id="file-list" class="mt-4 space-y-2">
                    <!-- Selected files will be listed here -->
                  </div>
                </div>
              </div>
            </div>

            {{-- Submit Button --}}
            <div class="flex justify-end">
              <button type="submit"
                class="inline-flex h-10 items-center rounded-lg bg-yns_yellow px-4 text-sm font-medium text-gray-900 transition duration-200 hover:bg-yellow-400">
                <i class="fas fa-plus-circle mr-2"></i>Create Job
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
<script>
  jQuery(document).ready(function() {
    // Initialize constants
    const dashboardType = "{{ $dashboardType }}";
    const packages = @json($packages);
    const clients = Object.values(@json($clients));

    // DOM Elements
    const form = jQuery('#new-job-form');
    const fileUpload = document.getElementById('job_scope_file');
    const fileList = document.getElementById('file-list');
    const searchInput = document.getElementById('client_search');
    const resultsContainer = document.getElementById('client_results');
    const resultsContent = document.getElementById('results_container');
    const clientIdInput = document.getElementById('client_id');
    let selectedClient = null;

    // Validate required elements
    if (!searchInput || !resultsContainer || !resultsContent || !clientIdInput) {
      console.error('Required search elements not found');
      return;
    }

    // Client Search Functionality
    searchInput.addEventListener('input', debounce(function(e) {
      const searchTerm = e.target.value.toLowerCase().trim();

      if (searchTerm.length < 3) {
        hideResults();
        return;
      }

      const filteredClients = clients.filter(client =>
        client.name.toLowerCase().includes(searchTerm)
      );

      renderResults(filteredClients);
    }, 300));

    // Search Results Display
    searchInput.addEventListener('focus', () => {
      if (searchInput.value.length >= 3) showResults();
    });

    document.addEventListener('click', (e) => {
      if (!searchInput.contains(e.target) && !resultsContainer.contains(e.target)) {
        hideResults();
      }
    });

    function renderResults(results) {
      resultsContent.innerHTML = '';

      if (results.length === 0) {
        resultsContent.innerHTML = `
                <div class="px-4 py-3 text-sm text-gray-400">
                    No matching clients found
                </div>
            `;
        showResults();
        return;
      }

      // Group by type
      const groupedResults = results.reduce((acc, client) => {
        acc[client.type] = acc[client.type] || [];
        acc[client.type].push(client);
        return acc;
      }, {});

      Object.entries(groupedResults).forEach(([type, clients]) => {
        resultsContent.innerHTML += `
                <div class="px-3 py-1.5 text-xs font-semibold text-gray-400 uppercase bg-gray-800/50">
                    ${type}s
                </div>
            `;

        clients.forEach(client => {
          const div = document.createElement('div');
          div.className = 'px-4 py-2 text-white hover:bg-gray-800 cursor-pointer transition duration-150';
          div.innerHTML = client.name;
          div.addEventListener('click', () => selectClient(client));
          resultsContent.appendChild(div);
        });
      });

      showResults();
    }

    function selectClient(client) {
      selectedClient = client;
      searchInput.value = client.name;
      document.getElementById('client_id').value = client.id;
      document.getElementById('client_name').value = client.name;
      document.getElementById('client_service').value = client.type;
      hideResults();
    }

    // Package Selection Handler
    jQuery('#package').on('change', function() {
      const selectedPackageType = jQuery(this).val();
      const selectedPackage = packages.find(p => p.job_type === selectedPackageType);

      if (selectedPackage) {
        jQuery('#estimated_lead_time_value').val(selectedPackage.lead_time);
        jQuery('#estimated_lead_time_unit').val(selectedPackage.lead_time_unit);
        jQuery('#job_cost').val(selectedPackage.price);
      }
    });

    // File Upload Handling
    if (fileUpload && fileList) {
      const dropZone = fileUpload.closest('.border-dashed');

      fileUpload.addEventListener('change', updateFileList);

      if (dropZone) {
        dropZone.addEventListener('dragover', (e) => {
          e.preventDefault();
          dropZone.classList.add('border-yns_yellow');
        });

        dropZone.addEventListener('dragleave', (e) => {
          e.preventDefault();
          dropZone.classList.remove('border-yns_yellow');
        });

        dropZone.addEventListener('drop', (e) => {
          e.preventDefault();
          dropZone.classList.remove('border-yns_yellow');
          fileUpload.files = e.dataTransfer.files;
          updateFileList();
        });
      }
    }

    // Form Submission
    form.on('submit', function(e) {
      e.preventDefault();

      const submitButton = form.find('button[type="submit"]');
      submitButton.prop('disabled', true)
        .html('<i class="fas fa-spinner fa-spin mr-2"></i>Processing...');

      const formData = new FormData(this);

      jQuery.ajax({
        url: form.attr('action'),
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
          'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
          if (response.success) {
            showNotification(response.message || 'Job created successfully', 'success');
            setTimeout(() => {
              window.location.href = response.redirect ||
                route('admin.dashboard.jobs', {
                  dashboardType
                });
            }, 1500);
          } else {
            showNotification(response.message || 'Error creating job', 'error');
          }
        },
        error: function(xhr) {
          const response = xhr.responseJSON;

          if (xhr.status === 422 && response.errors) {
            handleValidationErrors(response.errors);
          }

          showNotification(response?.message || 'Error creating job', 'error');
        },
        complete: function() {
          submitButton.prop('disabled', false)
            .html('<i class="fas fa-plus-circle mr-2"></i>Create Job');
        }
      });
    });

    // Helper Functions
    function updateFileList() {
      fileList.innerHTML = '';
      Array.from(fileUpload.files).forEach(file => {
        const fileItem = document.createElement('div');
        fileItem.className = 'flex items-center justify-between p-2 bg-gray-800 rounded-lg mb-2';
        fileItem.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-file mr-2 text-gray-400"></i>
                    <span class="text-sm text-white">${file.name}</span>
                </div>
                <span class="text-gray-400">${(file.size / 1024).toFixed(2)} KB</span>
            `;
        fileList.appendChild(fileItem);
      });
    }

    function showResults() {
      resultsContainer.classList.remove('hidden');
    }

    function hideResults() {
      resultsContainer.classList.add('hidden');
    }

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

    function handleValidationErrors(errors) {
      Object.keys(errors).forEach(field => {
        const input = jQuery(`[name="${field}"]`);
        input.addClass('border-red-500');
        input.after(`<p class="mt-1 text-xs text-red-500">${errors[field][0]}</p>`);
      });
    }

    function showNotification(message, type = 'success') {
      if (typeof Toastify !== 'undefined') {
        Toastify({
          text: message,
          duration: 3000,
          gravity: 'top',
          position: 'right',
          className: type === 'success' ? 'bg-green-500' : 'bg-red-500',
        }).showToast();
      }
    }
  });
</script>
