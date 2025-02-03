<x-app-layout :dashboardType="$dashboardType" :modules="$modules">
  <x-slot name="header">
    <x-sub-nav :userId="$userId" />
  </x-slot>

  <div class="mx-auto w-full max-w-screen-2xl py-16">
    <div class="relative mb-8 shadow-md sm:rounded-lg">
      <div class="min-w-screen-xl mx-auto max-w-screen-xl rounded-lg border border-white bg-yns_dark_gray text-white">
        <form method="POST" enctype="multipart/form-data" id="edit-job-form">
          @csrf
          @method('PUT')

          <div class="header border-b border-b-white px-8 pt-8">
            <h1 class="mb-8 font-heading text-4xl font-bold">
              Editing Job: {{ $job->pivot->first()->name }}
            </h1>

            <div class="grid grid-cols-2 gap-4 py-8">
              <div class="group">
                <x-input-label-dark :required="true">Client</x-input-label-dark>
                <select id="client-search" name="client_search" value="{{ $job->pivot->first()->name }}"
                  class="w-full rounded-md border-yns_red shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-yns_red dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600"></select>
                <div class="group mt-2">
                  <x-input-label-dark>Client Type - You're not supposed to see this... sneaky
                    sneaky!</x-input-label-dark>
                  <x-text-input id="client_service" name="client_service"
                    value="{{ $job->pivot->first()->services }}"></x-text-input>
                </div>
                <div class="group mt-2">
                  <x-input-label-dark>Client Name - You're not supposed to see this... sneaky
                    sneaky!</x-input-label-dark>
                  <x-text-input id="client_name" name="client_name"
                    value="{{ $job->pivot->first()->name }}"></x-text-input>
                </div>
              </div>

              <div class="group">
                <x-input-label-dark :required="true">Package</x-input-label-dark>
                <select id="package" name="package"
                  class="w-full rounded-md border-yns_red shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-yns_red dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600">
                  <option value="">Select a package</option>
                  @if ($packages && count($packages))
                    @foreach ($packages as $package)
                      <option value="{{ $package['job_type'] }}"
                        {{ $job->job_type === $package['job_type'] ? 'selected' : '' }}>
                        {{ $package['name'] }}
                      </option>
                    @endforeach
                  @endif
                </select>
              </div>

              <div class="group">
                <x-input-label-dark>Start Date</x-input-label-dark>
                <x-date-time-input id="job-start-date" name="job_start_date" :value="$job->job_start_date" />
              </div>

              <div class="group">
                <x-input-label-dark>Deadline Date</x-input-label-dark>
                <x-date-time-input id="job-deadline-date" name="job_deadline_date" :value="$job->job_end_date" />
              </div>

              <div class="group">
                <x-input-label-dark :required="true">Estimated Lead Time </x-input-label-dark>
                <div class="flex flex-row gap-2">
                  <x-text-input id="estimated_lead_time_value" name="estimated_lead_time_value" :required="true"
                    value="{{ $job->lead_time }}"></x-text-input>
                  <select id="estimated_lead_time_unit" name="estimated_lead_time_unit"
                    class="w-full rounded-md border-yns_red shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-yns_red dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600">
                    <option value="hours" {{ $job->lead_time_unit === 'hours' ? 'selected' : '' }}>Hours</option>
                    <option value="days" {{ $job->lead_time_unit === 'days' ? 'selected' : '' }}>Days</option>
                    <option value="weeks" {{ $job->lead_time_unit === 'weeks' ? 'selected' : '' }}>Weeks</option>
                    <option value="months" {{ $job->lead_time_unit === 'months' ? 'selected' : '' }}>Months</option>
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
                  ]" :selected="[$job->priority]" />
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
                  ]" :selected="[$job->job_status]" />
              </div>

              <div class="group">
                <x-input-label-dark>Amount</x-input-label-dark>
                <x-number-input-pound id="job-cost" name="job_cost" :value="$job->estimated_amount" />
              </div>

              @if ($job->job_status === 'completed')
                <div class="group">
                  <x-input-label-dark>Final Amount</x-input-label-dark>
                  <x-number-input-pound id="final-amount" name="final_amount" :value="$job->final_amount" />
                </div>
              @endif

              <div class="group col-span-2">
                <x-input-label-dark>Scope</x-input-label-dark>
                <x-textarea-input id="job-text-scope" name="job_text_scope"
                  class="w-full">{{ $job->scope }}</x-textarea-input>
              </div>

              <div class="group col-span-2">
                <x-input-label-dark>Upload New Files</x-input-label-dark>
                <x-input-file id="job_scope_file" name="job_scope_file" />
                @if ($job->scope_url)
                  <p class="mt-2 text-sm text-white">Current file:
                    <a href="{{ route('admin.dashboard.jobs.download', ['dashboardType' => $dashboardType, 'job' => $job->id]) }}"
                      class="text-yns_yellow hover:underline">
                      Download existing file
                    </a>
                  </p>
                @endif
              </div>
            </div>

            <div class="flex justify-end py-4">
              <x-button type="submit" label="Update Job" />
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</x-app-layout>
<script>
  const dashboardType = "{{ $dashboardType }}";
  const jobTypes = @json(config('job_types'));
  const packages = @json($packages);
  const id = "{{ $job->id }}";

  jQuery(document).ready(function() {
    // Client Search
    // Create initial selection data
    const initialClientData = {
      id: "{{ $job->pivot->first()->id }}",
      text: "{{ $job->pivot->first()->name }}",
      serviceType: "{{ $job->pivot->first()->services }}"
    };

    jQuery(document).ready(function() {
      // Client Search with initial data
      const $clientSearch = jQuery('#client-search');

      $clientSearch.select2({
        placeholder: 'Search for a client',
        containerCssClass: 'custom-select2-container',
        dropdownCssClass: 'custom-select2-dropdown',
        ajax: {
          url: `/api/${dashboardType}/jobs/search-clients`,
          dataType: 'json',
          delay: 250,
          data: function(params) {
            return {
              query: params.term
            };
          },
          processResults: function(data) {
            return {
              results: data.map(client => ({
                id: client.id,
                text: client.name,
                serviceType: client.service_type
              }))
            };
          },
          cache: true
        },
        minimumInputLength: 1
      });

      // Set initial selection
      const initialOption = new Option(initialClientData.text, initialClientData.id, true, true);
      $clientSearch.append(initialOption).trigger('change');

      // Set hidden fields
      jQuery('#client_service').val(initialClientData.serviceType);
      jQuery('#client_name').val(initialClientData.text);
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
    $('#edit-job-form').on('submit', function(e) {
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

      const submitButton = $(this).find('button[type="submit"]');
      submitButton.prop('disabled', true).text('Updating...');

      $.ajax({
        url: `/dashboard/${dashboardType}/jobs/${id}/update`,
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
          'Accept': 'application/json',
          'X-HTTP-Method-Override': 'PUT'
        },
        success: function(response) {
          console.log('Success:', response);
          if (response.success) {
            window.location.href = response.redirect;
          }
        },
        error: function(xhr, status, error) {
          console.error('Error Details:', {
            status: xhr.status,
            statusText: xhr.statusText,
            responseText: xhr.responseText
          });

          let errorMessage = 'Failed to update job: ';

          if (xhr.responseJSON && xhr.responseJSON.errors) {
            const errors = xhr.responseJSON.errors;
            errorMessage += Object.values(errors).flat().join('\n');
          } else if (xhr.responseJSON && xhr.responseJSON.message) {
            errorMessage += xhr.responseJSON.message;
          } else {
            errorMessage += error;
          }

          alert(errorMessage);
          submitButton.prop('disabled', false).text('Update Job');
        },
        complete: function() {
          submitButton.prop('disabled', false).text('Update Job');
        }
      });
    });
  });
</script>
