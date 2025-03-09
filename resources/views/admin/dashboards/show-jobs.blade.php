<x-app-layout :dashboardType="$dashboardType" :modules="$modules">
  <x-slot name="header">
    <x-sub-nav :userId="$userId" />
  </x-slot>

  <div class="mx-auto w-full max-w-screen-2xl py-16">
    <div class="relative mb-8 shadow-md sm:rounded-lg">
      <div
        class="min-w-screen-xl mx-auto rounded-lg border border-gray-800 bg-black p-8 text-white shadow-[0_4px_20px_-4px_rgba(0,0,0,0.9),inset_0_2px_0_rgba(255,255,255,0.1)]">
        <div class="header mb-8">
          <div class="flex flex-row justify-between">
            <h1 class="font-heading text-4xl font-bold">My Jobs</h1>
            <a href="{{ route('admin.dashboard.jobs.create', ['dashboardType' => $dashboardType]) }}"
              class="inline-flex items-center rounded-lg border border-gray-800 bg-gray-900 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-gray-800 hover:shadow-[0_0_15px_rgba(255,255,255,0.07)]">
              New Job
            </a>
          </div>
        </div>

        <table class="w-full text-left font-sans rtl:text-right" id="jobs">
          <thead class="border-b border-gray-800 text-white">
            <tr>
              <th scope="col" class="px-2 py-2 text-base md:px-6 md:py-3 md:text-xl lg:px-8 lg:py-4 lg:text-2xl">
                Client
              </th>
              <th scope="col" class="px-2 py-2 text-base md:px-6 md:py-3 md:text-xl lg:px-8 lg:py-4 lg:text-2xl">
                Job Type
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
          <tbody class="divide-y divide-gray-800">
            @if ($jobs)
              @forelse ($jobs as $job)
                <tr class="transition duration-150 ease-in-out hover:bg-gray-900/50">
                  <th scope="row"
                    class="whitespace-nowrap px-2 py-2 font-sans text-white md:px-6 md:py-3 md:text-base lg:px-8 lg:py-4 lg:text-lg">
                    {{ $job->name }}{{ $job->id }}
                  </th>
                  <td
                    class="whitespace-nowrap px-2 py-2 font-sans text-white md:px-6 md:py-3 md:text-base lg:px-8 lg:py-4 lg:text-lg">
                    {{ Str::of($job->job_type)->replace(['-', '_'], ' ')->title() }}
                  </td>
                  <td
                    class="{{ $className }} whitespace-nowrap px-2 py-2 font-sans md:px-6 md:py-3 md:text-base lg:px-8 lg:py-4 lg:text-lg">
                    {{ $formattedJobEndDate }}
                  </td>
                  <td
                    class="whitespace-nowrap px-2 py-2 font-sans text-white md:px-6 md:py-3 md:text-base lg:px-8 lg:py-4 lg:text-lg">
                    {{ Str::of($job->job_status)->replace(['-', '_'], ' ')->title() }}
                  </td>
                  <td
                    class="flex flex-row gap-2 px-2 py-2 text-center font-sans md:px-6 md:py-3 md:text-base lg:px-8 lg:py-4 lg:text-lg">
                    <a href="{{ route('admin.dashboard.jobs.view', ['dashboardType' => $dashboardType, 'job' => $job->pivot_job_id]) }}"
                      class="w-full rounded-lg border border-gray-800 bg-gray-900 px-4 py-2 text-white transition duration-150 ease-in-out hover:bg-gray-800">
                      <span class="fas fa-eye"></span>
                    </a>
                    <a href="{{ route('admin.dashboard.jobs.edit', ['dashboardType' => $dashboardType, 'job' => $job->pivot_job_id]) }}"
                      class="w-full rounded-lg border border-gray-800 bg-gray-900 px-4 py-2 text-white transition duration-150 ease-in-out hover:bg-gray-800">
                      <span class="fas fa-pencil"></span>
                    </a>
                    <a href="#"
                      class="delete-job w-full rounded-lg border border-gray-800 bg-gray-900 px-4 py-2 text-white transition duration-150 ease-in-out hover:bg-gray-800"
                      data-job-id="{{ $job->pivot_job_id }}">
                      <span class="fas fa-trash-can"></span>
                    </a>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6"
                    class="px-2 py-2 text-center font-sans text-gray-400 md:px-6 md:py-3 md:text-base lg:px-8 lg:py-4 lg:text-lg">
                    No jobs found
                  </td>
                </tr>
              @endforelse
            @endif
          </tbody>
        </table>
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
