<x-app-layout :dashboardType="$dashboardType" :modules="$modules">
  <x-slot name="header">
    <x-sub-nav :userId="$userId" />
  </x-slot>

  <div class="mx-auto w-full max-w-screen-2xl py-16">
    <div class="relative mb-8 shadow-md sm:rounded-lg">
      <div class="min-w-screen-xl mx-auto rounded-lg border border-white bg-yns_dark_gray px-8 py-8 text-white">
        <div class="header mb-8">
          <div class="flex flex-row justify-between">
            <h1 class="font-heading text-4xl font-bold">My Jobs</h1>
            <a href="{{ route('admin.dashboard.jobs.create', ['dashboardType' => $dashboardType]) }}"
              class="inline-flex items-center rounded-md border border-white bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-black transition duration-150 ease-in-out hover:border-yns_yellow hover:text-yns_yellow focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">New
              Job</a>
          </div>
        </div>
        <table class="w-full border border-white text-left font-sans rtl:text-right" id="jobs">
          <thead class="border border-b-white bg-black text-white underline">
            <tr>
              <th scope="col" class="px-2 py-2 text-base md:px-6 md:py-3 md:text-xl lg:px-8 lg:py-4 lg:text-2xl">
                Client
              </th>
              <th scope="col" class="px-2 py-2 text-base md:px-6 md:py-3 md:text-xl lg:px-8 lg:py-4 lg:text-2xl">Job
                Type
              </th>
              <th scope="col" class="px-2 py-2 text-base md:px-6 md:py-3 md:text-xl lg:px-8 lg:py-4 lg:text-2xl">
                Deadline
              </th>
              <th scope="col" class="px-2 py-2 text-base md:px-6 md:py-3 md:text-xl lg:px-8 lg:py-4 lg:text-2xl">
                Status
              </th>
              <th scope="col" class="px-2 py-2 text-base md:px-6 md:py-3 md:text-xl lg:px-8 lg:py-4 lg:text-2xl">
                Actions
              </th>
            </tr>
          </thead>
          <tbody>
            @if ($jobs)
              @forelse ($jobs as $job)
                <tr class="border-gray-700 odd:dark:bg-black even:dark:bg-gray-900">
                  <th scope="row"
                    class="whitespace-nowrap px-2 py-2 font-sans text-white md:px-6 md:py-3 md:text-base lg:px-8 lg:py-4 lg:text-lg">
                    {{ $job->name }}{{ $job->id }}
                  </th>
                  <td
                    class="whitespace-nowrap px-2 py-2 font-sans text-white md:px-6 md:py-3 md:text-base lg:px-8 lg:py-4 lg:text-lg">
                    {{ Str::of($job->job_type)->replace(['-', '_'], ' ')->title() }}
                  </td>
                  @php
                    $jobEndDate = \Carbon\Carbon::parse($job->job_end_date);

                    $className = '';

                    if ($jobEndDate->isPast()) {
                        $className = 'text-yns_red';
                    } elseif ($jobEndDate->isFuture()) {
                        $className = 'text-white';
                    }

                    $formattedJobEndDate = $jobEndDate->format('jS F Y');
                  @endphp
                  <td
                    class="{{ $className }} whitespace-nowrap px-2 py-2 font-sans md:px-6 md:py-3 md:text-base lg:px-8 lg:py-4 lg:text-lg">
                    {{ $formattedJobEndDate }}
                  </td>
                  <td
                    class="wwhitespace-nowrap px-2 py-2 font-sans text-white md:px-6 md:py-3 md:text-base lg:px-8 lg:py-4 lg:text-lg">
                    {{ Str::of($job->job_status)->replace(['-', '_'], ' ')->title() }}
                  </td>
                  <td
                    class="flex flex-row gap-2 px-2 py-2 text-center font-sans text-white md:px-6 md:py-3 md:text-base lg:px-8 lg:py-4 lg:text-lg">
                    <a href="{{ route('admin.dashboard.jobs.view', ['dashboardType' => $dashboardType, 'job' => $job->pivot_job_id]) }}"
                      class="w-full rounded-lg bg-white px-4 py-2 font-heading text-black transition duration-150 ease-in-out hover:text-yns_yellow">
                      <span class="fas fa-eye"></span>
                    </a>
                    <a href="{{ route('admin.dashboard.jobs.edit', ['dashboardType' => $dashboardType, 'job' => $job->pivot_job_id]) }}"
                      class="w-full rounded-lg bg-white px-4 py-2 font-heading text-black transition duration-150 ease-in-out hover:text-yns_dark_orange"><span
                        class="fas fa-pencil"></span></a>
                    <a href="#"
                      class="delete-job w-full rounded-lg bg-white px-4 py-2 font-heading text-black transition duration-150 ease-in-out hover:text-yns_red"
                      data-job-id="{{ $job->pivot_job_id }}">
                      <span class="fas fa-trash-can"></span>
                    </a>
                    <form id="delete-job-{{ $job->pivot_job_id }}"
                      action="{{ route('admin.dashboard.jobs.delete', ['dashboardType' => $dashboardType, 'job' => $job->pivot_job_id]) }}"
                      method="POST" class="hidden">
                      @csrf
                      @method('DELETE')
                    </form>
                  </td>
                </tr>
              @empty
                <tr class="border-b border-white odd:dark:bg-black even:dark:bg-gray-900">
                  <td colspan="6"
                    class="whitespace-nowrap px-2 py-2 text-center font-sans text-white md:px-6 md:py-3 md:text-base lg:px-8 lg:py-4 lg:text-lg">
                    No jobs found</td>
                </tr>
              @endforelse
            @endif
          </tbody>
        </table>
      </div>
    </div>
  </div>
  </div>
</x-app-layout>
<script>
  document.addEventListener('DOMContentLoaded', function() {
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
