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
            <form id="new-job-form" method="POST" enctype="multipart/form-data">
              @csrf
              <div class="grid grid-cols-2 gap-4 py-8">
                <div class="group">
                  <x-input-label-dark :required="true">Client</x-input-label-dark>
                  <select id="client-search" name="client_search"
                    class="w-full rounded-md border-yns_red shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-yns_red dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600"></select>
                  <div class="group mt-2 hidden">
                    <x-input-label-dark>Client Type - You're not supposed to see this... sneaky
                      sneaky!</x-input-label-dark>
                    <x-text-input id="client_service" name="client_service"></x-text-input>
                  </div>
                  <div class="group mt-2 hidden">
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
                  <x-input-label-dark :required="true">Deadline Date</x-input-label-dark>
                  <x-date-time-input id="job-deadline-date" name="job_deadline_date"
                    :required="true"></x-date-time-input>
                </div>

                <div class="group">
                  <x-input-label-dark :required="true">Package</x-input-label-dark>
                  <select id="package" name="package"
                    class="w-full rounded-md border-yns_red shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-yns_red dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600">
                    <option value="">Select a package</option>
                    @if ($packages && count($packages))
                      @foreach ($packages as $package)
                        <option value="{{ $package['job_type'] }}">
                          {{ $package['name'] }}
                        </option>
                      @endforeach
                    @endif
                  </select>
                </div>

                <div class="group">
                  <x-input-label-dark :required="true">Estimated Lead Time </x-input-label-dark>
                  <div class="flex flex-row gap-2">
                    <x-text-input id="estimated_lead_time_value" name="estimated_lead_time_value"
                      :required="true"></x-text-input>
                    <select id="estimated_lead_time_unit" name="estimated_lead_time_unit"
                      class="w-full rounded-md border-yns_red shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-yns_red dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600">
                      <option value="hours">Hours</option>
                      <option value="days">Days</option>
                      <option value="weeks">Weeks</option>
                      <option value="months">Months</option>
                    </select>
                  </div>
                </div>

                <div class="group">
                  <x-input-label-dark :required="true">Priority</x-input-label-dark>
                  <x-select id="job-priority" name="job_priority" class="w-full min-w-[120px] flex-grow-0 px-3 py-2"
                    :required="true" :options="[
                        'urgent' => 'Urgent ',
                        'high' => 'High',
                        'standard' => 'Standard',
                        'low' => 'Low',
                        'no-priority' => 'No Priority',
                    ]" :selected="['standard']" />
                </div>

                <div class="group">
                  <x-input-label-dark :required="true">Status</x-input-label-dark>
                  <x-select id="job-status" name="job_status" :required="true"
                    class="w-full min-w-[120px] flex-grow-0 px-3 py-2" :options="[
                        'not-started' => 'Not Started',
                        'in-progress' => 'In Progress',
                        'on-hold' => 'On Hold',
                        'waiting-for-client' => 'Waiting For Client',
                        'waiting-for-payment' => 'Waiting For Payment',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]" :selected="['not-started']" />
                </div>

                <div class="group">
                  <x-input-label-dark>Upload Files</x-input-label-dark>
                  <x-input-file id="job_scope_file" name="job_scope_file"></x-input-file>
                </div>

                <div class="group">
                  <x-input-label-dark :required="true">Amount</x-input-label-dark>
                  <x-number-input-pound id="job-cost" name="job_cost" :required="true"></x-number-input-pound>
                </div>

                <div class="group">
                  <x-input-label-dark>Scope</x-input-label-dark>
                  <x-textarea-input id="job-text-scope" name="job_text_scope" class="w-full"></x-textarea-input>
                </div>
              </div>
              <div class="group mb-4">
                <x-button type="submit" label="Save Job" />
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
  const jobTypes = @json(config('job_types'));
  const packages = @json($packages);

  jQuery(document).ready(function() {
    // Client Search
    jQuery('#client-search').select2({
      placeholder: 'Search for a client',
      containerCssClass: 'custom-select2-container',
      dropdownCssClass: 'custom-select2-dropdown',
      ajax: {
        url: `/api/${dashboardType}/jobs/search-clients`,
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
      const clientType = data.serviceType.toLowerCase();
      const providerType = dashboardType.toLowerCase();

      jQuery('#client_service').val(data.serviceType);
      jQuery('#client_name').val(data.text);

      const jobTypeSelect = jQuery('#job-type');
      jobTypeSelect.empty(); // Clear existing options before adding new ones

      console.log()

      if (jobTypes[providerType] && jobTypes[providerType][clientType]) {
        jobTypes[providerType][clientType].forEach(function(job) {
          jobTypeSelect.append(new Option(job.name, job.id));
        });
      }
    });

    jQuery('#package').on('change', function() {
      const selectedPackageType = $(this).val();
      const selectedPackage = packages.find(p => p.job_type === selectedPackageType);

      if (selectedPackage) {
        jQuery('#estimated_lead_time_value').val(selectedPackage.lead_time);
        jQuery('#estimated_lead_time_unit').val(selectedPackage.lead_time_unit);

        jQuery('#job-cost').val(selectedPackage.price);

        if (jQuery('#job-type').length > 0) {
          jQuery('#job-type').val(selectedPackage.job_type);
        }
      }
    });

    // Handle form submission
    $('#new-job-form').on('submit', function(e) {
      e.preventDefault();
      const formData = new FormData(this);

      // Convert array values to single values
      if (formData.has('client_search[]')) {
        formData.set('client_search', formData.get('client_search[]'));
        formData.delete('client_search[]');
      }

      if (formData.has('job_type[]')) {
        formData.set('job_type', formData.get('job_type[]'));
        formData.delete('job_type[]');
      }

      if (formData.has('job_priority[]')) {
        formData.set('job_priority', formData.get('job_priority[]'));
        formData.delete('job_priority[]');
      }

      if (formData.has('job_status[]')) {
        formData.set('job_status', formData.get('job_status[]'));
        formData.delete('job_status[]');
      }

      $.ajax({
        url: `/dashboard/${dashboardType}/jobs/store`,
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        success: function(response) {
          if (response.success) {
            showSuccessNotification(response.message);
            window.location.href = response.redirect;
          } else {
            showFailureNotification(response.message);
          }
        },
        error: function(xhr) {
          const response = xhr.responseJSON;
          showFailureNotification(response?.message || 'Error creating job');
        }
      });
    });
  });
</script>
