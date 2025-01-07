<x-app-layout :dashboardType="$dashboardType" :modules="$modules">
  <x-slot name="header">
    <x-sub-nav :userId="$userId" />
  </x-slot>

  <div class="mx-auto w-full max-w-screen-2xl py-16">
    <div class="relative mb-8 shadow-md sm:rounded-lg">
      <div
        class="min-w-screen-xl mx-auto max-w-screen-xl rounded-lg border border-white bg-yns_dark_gray px-8 py-8 text-white">
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
                    {{ $job->name }}
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
                    class="flex flex-col gap-2 px-2 py-2 font-sans text-white md:px-6 md:py-3 md:text-base lg:px-8 lg:py-4 lg:text-lg">
                    <button
                      class="w-full rounded-lg bg-white px-4 py-2 font-heading text-black transition duration-150 ease-in-out hover:text-yns_yellow">View</button>
                    <button
                      class="w-full rounded-lg bg-white px-4 py-2 font-heading text-black transition duration-150 ease-in-out hover:text-yns_dark_orange">Edit</button>
                    <button
                      class="w-full rounded-lg bg-white px-4 py-2 font-heading text-black transition duration-150 ease-in-out hover:text-yns_red">Delete</button>
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
