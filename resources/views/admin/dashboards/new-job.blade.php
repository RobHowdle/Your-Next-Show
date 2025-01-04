<x-app-layout :dashboardType="$dashboardType" :modules="$modules">
  <x-slot name="header">
    <x-sub-nav :userId="$userId" />
  </x-slot>

  <div class="mx-auto w-full max-w-screen-2xl py-16">
    <div class="relative mb-8 shadow-md sm:rounded-lg">
      <div class="min-w-screen-xl mx-auto max-w-screen-xl rounded-lg border border-white bg-yns_dark_gray text-white">
        <div class="header border-b border-b-white px-8 pt-8">
          <div class="flex flex-col justify-between">
            <h1 class="font-heading text-4xl font-bold">Create Job</h1>
            <form action="{{ route('admin.dashboard.jobs.store', ['dashboardType' => $dashboardType]) }}" method="POST"
              enctype="multipart/form-data">
              @csrf
              <div class="grid grid-cols-2 gap-4 py-8">
                <div class="group">
                  <x-input-label-dark>Client</x-input-label-dark>
                  <select id="client-search" name="client_search"
                    class="mt-1 block w-full rounded-md border-yns_red shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600">
                    <option value="">Search for a client</option>
                  </select>
                  <div class="group hidden">
                    <x-input-label-dark>Client Type - You're not supposed to see this... sneaky
                      sneaky!</x-input-label-dark>
                    <x-text-input id="client_service" name="client_service"></x-text-input>
                  </div>
                  <div class="group hidden">
                    <x-input-label-dark>Client Name - You're not supposed to see this... sneaky
                      sneaky!</x-input-label-dark>
                    <x-text-input id="client_name" name="client_name"></x-text-input>
                  </div>
                </div>

                <div class="group hidden">
                  <x-input-label-dark>Start Date</x-input-label-dark>
                  <x-date-time-input id="job-start-date" name="job_start_date"
                    value="{{ now()->format('Y-m-d\TH:i') }}"></x-date-time-input>
                </div>

                <div class="group">
                  <x-input-label-dark>Deadline Date</x-input-label-dark>
                  <x-date-time-input id="job-deadline-date" name="job_deadline_date"></x-date-time-input>
                </div>

                <div class="group">
                  <x-input-label-dark>Job Type</x-input-label-dark>
                  <select id="job-type" name="job_type"
                    class="mt-1 block w-full rounded-md border-yns_red shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600">
                    <option value="" disabled selected>Select a Job Type</option>
                  </select>
                </div>

                <div class="group">
                  <x-input-label-dark>Priority</x-input-label-dark>
                  <select id="job-priority" name="job_priority"
                    class="mt-1 block w-full rounded-md border-yns_red shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600">
                    <option value="" disabled>Select a priority</option>
                    <option value="urgent">Urgent</option>
                    <option value="high">High</option>
                    <option value="standard" selected>Standard</option>
                    <option value="low">Low</option>
                    <option value="no-priority">No Priority</option>
                  </select>
                </div>

                <div class="group">
                  <x-input-label-dark>Status</x-input-label-dark>
                  <select id="job-status" name="job_status"
                    class="mt-1 block w-full rounded-md border-yns_red shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600">
                    <option value="not-started">Not Started</option>
                    <option value="in-progress">In Progress</option>
                    <option value="on-hold">On Hold</option>
                    <option value="waiting-for-client">Waiting For Client</option>
                    <option value="waiting-for-payment">Waiting For Payment</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                  </select>
                </div>

                <div class="group">
                  <x-input-label-dark>Upload Files</x-input-label-dark>
                  <x-input-file id="job_scope_file" name="job_scope_file"></x-input-file>
                </div>

                <div class="group">
                  <x-input-label-dark>Amount</x-input-label-dark>
                  <x-number-input-pound id="job-cost" name="job_cost"></x-number-input-pound>
                </div>

                <div class="group">
                  <x-input-label-dark>Scope</x-input-label-dark>
                  <x-textarea-input id="job-text-scope" name="job_text_scope" class="w-full"></x-textarea-input>
                </div>

                <div class="group">
                  <button type="submit">Save Job</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
<script>
  const dashboardType = "{{ $dashboardType }}";

  jQuery(document).ready(function() {
    // Client Search
    jQuery('#client-search').select2({
      placeholder: 'Type to search...',
      ajax: {
        url: `{{ route('admin.dashboard.jobs.client-search', ['dashboardType' => $dashboardType]) }}`,
        dataType: 'json',
        delay: 250,
        data: function(params) {
          return {
            query: params.term // Search query
          };
        },
        processResults: function(data) {
          return {
            results: data.map(client => ({
              id: client.id,
              text: `${client.name}`,
              serviceType: client.service_type
            }))
          };
        },
        cache: true
      },
      minimumInputLength: 1
    });

    // Handle selection
    jQuery('#client-search').on('select2:select', function(e) {
      const data = e.params.data;
      jQuery('#client_service').val(data.serviceType);
      jQuery('#client_name').val(data.text);

    });

    // Job Type Select
    const jobTypes = {
      'designer': [{
          id: 'poster_design',
          name: "Poster Design"
        },
        {
          id: 'logo_design',
          name: "Logo Design"
        },
      ],
      'photographer': [{
          id: 'live_shoot',
          name: "Live Shoot"
        },
        {
          id: 'recording_show',
          name: "Recording Shoot"
        },
        {
          id: 'studio_shoot',
          name: "Studio Shoot"
        },
      ],
    };

    function loadJobTypes(dashboard) {
      const jobTypeSelect = jQuery('#job-type');
      // jobTypeSelect.empty(); // Clear existing options before adding new ones

      if (jobTypes[dashboard]) {
        jobTypes[dashboard].forEach(function(job) {
          jobTypeSelect.append(new Option(job.name, job.id));
        });
      }
    }

    loadJobTypes(dashboardType); // Call the function to load job types for the specific dashboard
  });
</script>
