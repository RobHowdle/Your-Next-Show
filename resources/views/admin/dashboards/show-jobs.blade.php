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
                  <h1 class="font-heading text-2xl font-bold text-white md:text-3xl">My Jobs</h1>
                  <p class="mt-2 text-gray-400">Manage and track all your jobs</p>
                </div>
                <div class="flex items-center space-x-4">
                  <div class="flex items-center rounded-lg border border-gray-800 bg-gray-900/60 p-1">
                    <button type="button" id="list-view"
                      class="view-toggle rounded-md px-3 py-2 text-sm font-medium text-white" data-view="list">
                      <i class="fas fa-list mr-2"></i>List
                    </button>
                    <button type="button" id="board-view"
                      class="view-toggle rounded-md px-3 py-2 text-sm font-medium text-gray-400" data-view="board">
                      <i class="fas fa-columns mr-2"></i>Board
                    </button>
                  </div>
                  <a href="{{ route('admin.dashboard.jobs.create', ['dashboardType' => $dashboardType]) }}"
                    class="inline-flex items-center rounded-lg bg-yns_yellow px-4 py-2 text-sm font-medium text-gray-900 transition duration-200 hover:bg-yellow-400">
                    <i class="fas fa-plus-circle mr-2"></i>New Job
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>

        {{-- Filters Section --}}
        <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
          <div class="flex flex-wrap items-center gap-3">
            <select id="statusFilter"
              class="rounded-lg border border-gray-800 bg-black/50 px-3 py-2 text-sm text-white focus:border-gray-700 focus:ring-0">
              <option value="all">All Status</option>
              <option value="not-started">Not Started</option>
              <option value="in-progress">In Progress</option>
              <option value="on-hold">On Hold</option>
              <option value="waiting-for-client">Waiting For Client</option>
              <option value="waiting-for-payment">Waiting For Payment</option>
              <option value="completed">Completed</option>
              <option value="cancelled">Cancelled</option>
            </select>
            <select id="sortBy"
              class="rounded-lg border border-gray-800 bg-black/50 px-3 py-2 text-sm text-white focus:border-gray-700 focus:ring-0">
              <option value="deadline">Sort by Deadline</option>
              <option value="client">Sort by Client</option>
              <option value="type">Sort by Type</option>
            </select>
          </div>
          <div class="relative">
            <input type="text" id="searchFilter"
              class="rounded-lg border border-gray-800 bg-black/50 py-2 pl-9 pr-4 text-sm text-white placeholder-gray-500 focus:border-gray-700 focus:ring-0"
              placeholder="Search jobs...">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
              <i class="fas fa-search text-gray-500"></i>
            </div>
          </div>
        </div>

        {{-- Views Container --}}
        <div id="views-container">
          {{-- List View --}}
          <div id="list-view-container" class="rounded-xl border border-gray-800 bg-gray-900/60 backdrop-blur-sm">
            {{-- Existing table code with updated styling --}}
          </div>

          {{-- Board View --}}
          <div id="board-view-container" class="hidden">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
              {{-- Pending Column --}}
              <div class="rounded-xl border border-gray-800 bg-gray-900/60 p-4 backdrop-blur-sm">
                <h3 class="mb-4 text-lg font-semibold text-white">Pending</h3>
                <div class="job-column space-y-4" data-status="pending">
                  {{-- Jobs will be inserted here --}}
                </div>
              </div>

              {{-- In Progress Column --}}
              <div class="rounded-xl border border-gray-800 bg-gray-900/60 p-4 backdrop-blur-sm">
                <h3 class="mb-4 text-lg font-semibold text-white">In Progress</h3>
                <div class="job-column space-y-4" data-status="in-progress">
                  {{-- Jobs will be inserted here --}}
                </div>
              </div>

              {{-- Completed Column --}}
              <div class="rounded-xl border border-gray-800 bg-gray-900/60 p-4 backdrop-blur-sm">
                <h3 class="mb-4 text-lg font-semibold text-white">Completed</h3>
                <div class="job-column space-y-4" data-status="completed">
                  {{-- Jobs will be inserted here --}}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const viewToggles = document.querySelectorAll('.view-toggle');
    const listView = document.getElementById('list-view-container');
    const boardView = document.getElementById('board-view-container');
    const dashboardType = @json($dashboardType);
    const jobs = @json($jobs);

    // View switching
    viewToggles.forEach(toggle => {
      toggle.addEventListener('click', function() {
        const view = this.dataset.view;

        // Update toggle buttons
        viewToggles.forEach(btn => {
          btn.classList.remove('bg-gray-800', 'text-white');
          btn.classList.add('text-gray-400');
        });
        this.classList.add('bg-gray-800', 'text-white');
        this.classList.remove('text-gray-400');

        // Show/hide views
        if (view === 'list') {
          listView.classList.remove('hidden');
          boardView.classList.add('hidden');
        } else {
          listView.classList.add('hidden');
          boardView.classList.remove('hidden');
          renderBoardView();
        }
      });
    });

    function renderBoardView() {
      const columns = document.querySelectorAll('.job-column');
      columns.forEach(column => {
        column.innerHTML = ''; // Clear existing cards
      });

      // Group jobs by status
      jobs.forEach(job => {
        const card = createJobCard(job);
        const column = document.querySelector(`.job-column[data-status="${job.job_status}"]`);
        if (column) column.appendChild(card);
      });
    }

    function createJobCard(job) {
      const div = document.createElement('div');
      div.className = 'rounded-lg border border-gray-800 bg-black/50 p-4';
      div.innerHTML = `
            <div class="mb-3 flex items-center justify-between">
                <h4 class="font-medium text-white">${job.name}</h4>
                <span class="rounded-full bg-gray-800 px-2 py-1 text-xs text-white">
                    ${job.job_type.replace(/[-_]/g, ' ').replace(/\w\S*/g, (w) => (w.replace(/^\w/, (c) => c.toUpperCase())))}
                </span>
            </div>
            <div class="mb-3 text-sm text-gray-400">
                <p class="mb-1"><i class="fas fa-calendar mr-2"></i>${job.deadline}</p>
            </div>
            <div class="flex justify-end gap-2">
                <a href="/admin/dashboard/${dashboardType}/jobs/${job.id}" class="rounded-lg bg-gray-800 p-2 text-white hover:bg-gray-700">
                    <i class="fas fa-eye"></i>
                </a>
                <a href="/admin/dashboard/${dashboardType}/jobs/${job.id}/edit" class="rounded-lg bg-gray-800 p-2 text-white hover:bg-gray-700">
                    <i class="fas fa-pencil"></i>
                </a>
                <button class="delete-job rounded-lg bg-gray-800 p-2 text-white hover:bg-gray-700" data-job-id="${job.id}">
                    <i class="fas fa-trash-can"></i>
                </button>
            </div>
        `;
      return div;
    }

    document.querySelectorAll('.delete-job').forEach(button => {
      button.addEventListener('click', function(e) {
        e.preventDefault();
        const jobId = this.dataset.jobId;

        showConfirmationNotification({
          text: 'Are you sure you want to delete this job?',
          confirmButtonText: 'Yes, delete it',
          cancelButtonText: 'No, keep it',
          onConfirm: () => {
            console.log('confirm');
            const form = document.getElementById(`delete-job-${jobId}`);
            const formData = new FormData(form);

            fetch(form.action, {
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
                  location.reload();
                } else {
                  console.error('Delete failed:', data.message);
                }
              })
              .catch(error => {
                console.error('Error:', error);
              });
          }
        });
      });
    });
  });
</script>
