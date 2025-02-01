<x-app-layout :dashboardType="$dashboardType" :modules="$modules">
  <x-slot name="header">
    <x-sub-nav :userId="$userId" />
  </x-slot>

  <div class="mx-auto w-full max-w-screen-2xl py-16">
    <div class="relative mb-8 shadow-md sm:rounded-lg">
      <div class="min-w-screen-xl mx-auto max-w-screen-xl rounded-lg border border-white bg-yns_dark_gray text-white">
        <div class="header border-b border-b-white px-8 pt-8">
          <div class="flex flex-row justify-between">
            <h1 class="font-heading text-4xl font-bold">
              {{ $job->pivot->first()->name }} - {{ ucwords(str_replace(['_', '-'], ' ', $job->job_type)) }}
            </h1>
            <a href="{{ route('admin.dashboard.jobs.edit', ['dashboardType' => $dashboardType, 'job' => $job->id]) }}"
              class="inline-flex items-center rounded-md border border-white bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-black transition duration-150 ease-in-out hover:border-yns_yellow hover:text-yns_yellow focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Edit
              Job</a>
          </div>
          <div class="grid grid-cols-2 gap-4 py-8">
            <div class="group">
              <p class="font-bold text-white">Client</p>
              <p class="text-white">{{ $job->pivot->first()->name }}</p>
            </div>
            <div class="group">
              <p class="font-bold text-white">Job Type</p>
              <p class="text-white">{{ ucwords(str_replace(['_', '-'], ' ', $job->job_type)) }}</p>
            </div>
            <div class="group">
              <p class="font-bold text-white">Start Date</p>
              <p class="text-white">{{ Carbon\Carbon::parse($job->job_start_date)->format('d/m/Y') }}</p>
            </div>
            <div class="group">
              <p class="font-bold text-white">Estimated Lead Time</p>
              <p class="text-white">{{ $job->lead_time }} {{ $job->lead_time_unit }}</p>
            </div>
            <div class="group">
              <p class="font-bold text-white">Priority</p>
              <p class="text-white">{{ ucwords($job->priority) }}</p>
            </div>
            <div class="group">
              <p class="font-bold text-white">Status</p>
              <p class="text-white">{{ ucwords($job->job_status) }}</p>
            </div>
            <div class="group">
              <p class="font-bold text-white">Amount</p>
              <p class="text-white">{{ formatCurrency($job->estimated_amount) }}</p>
            </div>
            @if ($job->job_status === 'completed')
              <div class="group">
                <p class="font-bold text-white">Final Amount</p>
                <p class="text-white">{{ formatCurrency($job->final_amount) }}</p>
              </div>
              <div class="group">
                <p class="font-bold text-white">Completed Date</p>
                <p class="text-white">{{ Carbon\Carbon::parse($job->job_end_date)->format('d/m/Y') }}</p>
              </div>
            @endif
            <div class="group">
              <p class="font-bold text-white">Scope</p>
              <p class="text-white">{{ $job->scope }}</p>
            </div>
          </div>

          <div class="group">
            <p class="font-bold text-white">Uploaded Files</p>
            <p class="text-white">{{ $job->scope_url }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
