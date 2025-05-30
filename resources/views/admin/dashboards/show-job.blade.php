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
                  <h1 class="font-heading text-2xl font-bold text-white md:text-3xl">
                    {{ $job->pivot->first()->name }} - {{ ucwords(str_replace(['_', '-'], ' ', $job->job_type)) }}
                  </h1>
                  <p class="mt-2 text-gray-400">Job Details</p>
                </div>
                @if ($job->job_status === 'completed')
                  <div class="flex flex-row">
                    <p>You cannot alter completed jobs.</p>
                  </div>
                @else
                  <div class="flex space-x-4">
                    <button type="button" id="complete-job"
                      class="inline-flex h-10 items-center rounded-lg bg-yns_yellow px-4 text-sm font-medium text-gray-900 transition duration-200 hover:bg-yellow-400">
                      <i class="fas fa-check-circle mr-2"></i>Complete Job
                    </button>
                    <a href="{{ route('admin.dashboard.jobs.edit', ['dashboardType' => $dashboardType, 'job' => $job->id]) }}"
                      class="inline-flex h-10 items-center rounded-lg border border-gray-800 bg-transparent px-4 text-sm font-medium text-white transition hover:bg-gray-800">
                      <i class="fas fa-edit mr-2"></i>Edit Job
                    </a>
                  </div>
                @endif
              </div>
            </div>
          </div>
        </div>

        {{-- Content Section --}}
        <div class="mx-auto max-w-5xl">
          <div class="rounded-xl border border-gray-800 bg-gray-900/60 p-6 backdrop-blur-sm">
            {{-- Client & Job Details --}}
            <div class="mb-6">
              <h2 class="mb-4 font-heading text-lg font-semibold text-white">Client & Job Details</h2>
              <div class="grid gap-6 md:grid-cols-2">
                <div>
                  <p class="mb-2 text-sm font-medium text-gray-400">Client</p>
                  <p class="text-white">{{ $job->pivot->first()->name }}</p>
                </div>
                <div>
                  <p class="mb-2 text-sm font-medium text-gray-400">Job Type</p>
                  <p class="text-white">{{ ucwords(str_replace(['_', '-'], ' ', $job->job_type)) }}</p>
                </div>
              </div>
            </div>

            {{-- Timeline --}}
            <div class="mb-6">
              <h2 class="mb-4 font-heading text-lg font-semibold text-white">Timeline</h2>
              <div class="grid gap-6 md:grid-cols-3">
                <div>
                  <p class="mb-2 text-sm font-medium text-gray-400">Start Date</p>
                  <p class="text-white">{{ Carbon\Carbon::parse($job->job_start_date)->format('d/m/Y') }}</p>
                </div>
                <div>
                  <p class="mb-2 text-sm font-medium text-gray-400">Estimated Lead Time</p>
                  <p class="text-white">{{ $job->lead_time }} {{ $job->lead_time_unit }}</p>
                </div>
                @if ($job->job_status === 'completed')
                  <div>
                    <p class="mb-2 text-sm font-medium text-gray-400">Completed Date</p>
                    <p class="text-white">{{ Carbon\Carbon::parse($job->job_end_date)->format('d/m/Y') }}</p>
                  </div>
                @endif
              </div>
            </div>

            {{-- Status & Priority --}}
            <div class="mb-6">
              <h2 class="mb-4 font-heading text-lg font-semibold text-white">Status Information</h2>
              <div class="grid gap-6 md:grid-cols-2">
                <div>
                  <p class="mb-2 text-sm font-medium text-gray-400">Priority</p>
                  <p class="text-white">{{ ucwords($job->priority) }}</p>
                </div>
                <div>
                  <p class="mb-2 text-sm font-medium text-gray-400">Status</p>
                  <p class="text-white">{{ ucwords($job->job_status) }}</p>
                </div>
              </div>
            </div>

            {{-- Financial Details --}}
            <div class="mb-6">
              <h2 class="mb-4 font-heading text-lg font-semibold text-white">Financial Details</h2>
              <div class="grid gap-6 md:grid-cols-2">
                <div>
                  <p class="mb-2 text-sm font-medium text-gray-400">Estimated Amount</p>
                  <p class="text-white">{{ formatCurrency($job->estimated_amount) }}</p>
                </div>
                @if ($job->job_status === 'completed')
                  <div>
                    <p class="mb-2 text-sm font-medium text-gray-400">Final Amount</p>
                    <p class="text-white">{{ formatCurrency($job->final_amount) }}</p>
                  </div>
                @endif
              </div>
            </div>

            {{-- Description --}}
            <div class="mb-6">
              <h2 class="mb-4 font-heading text-lg font-semibold text-white">Description</h2>
              <div>
                <p class="mb-2 text-sm font-medium text-gray-400">Job Scope</p>
                <p class="text-white">{{ $job->scope }}</p>
              </div>
            </div>

            {{-- Attachments --}}
            <div>
              <h2 class="mb-4 font-heading text-lg font-semibold text-white">Attachments</h2>
              <div>
                <p class="mb-2 text-sm font-medium text-gray-400">Uploaded Files</p>
                @if ($job->documents && $job->documents->count() > 0)
                  <div class="space-y-2">
                    @foreach ($job->documents as $document)
                      <a href="{{ Storage::url($document->file_path) }}"
                        class="block inline-flex items-center text-yns_yellow hover:underline" target="_blank">
                        <i class="fas fa-file mr-2"></i>
                        {{ $document->original_name }}
                      </a>
                    @endforeach
                  </div>
                @else
                  <p class="text-gray-400">No files uploaded</p>
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Complete Job Modal --}}
  <div id="complete-job-modal" class="fixed inset-0 hidden items-center justify-center bg-black/50 backdrop-blur-sm">
    <div class="w-full max-w-md rounded-xl border border-gray-800 bg-gray-900/95 p-6">
      <h2 class="mb-4 font-heading text-lg font-semibold text-white">Complete Job</h2>
      <form id="complete-job-form" class="space-y-4">
        <div>
          <label for="final-amount" class="mb-2 block text-sm font-medium text-gray-400">Final Amount</label>
          <div class="relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">£</span>
            <input type="number" id="final-amount" name="final_amount" value="{{ $job->estimated_amount }}"
              class="w-full rounded-lg border border-gray-800 bg-black/50 py-2.5 pl-8 pr-4 text-white placeholder-gray-500 focus:border-gray-700 focus:ring-0"
              step="0.01">
          </div>
        </div>
        <div>
          <label for="completion-date" class="mb-2 block text-sm font-medium text-gray-400">Completion Date</label>
          <input type="date" id="completion-date" name="completion_date" value="{{ now()->format('Y-m-d') }}"
            class="w-full rounded-lg border border-gray-800 bg-black/50 px-4 py-2.5 text-white focus:border-gray-700 focus:ring-0">
        </div>
        <div class="flex justify-end space-x-3">
          <button type="button" id="cancel-complete"
            class="inline-flex h-10 items-center rounded-lg border border-gray-800 bg-transparent px-4 text-sm font-medium text-white transition hover:bg-gray-800">
            Cancel
          </button>
          <button type="submit"
            class="inline-flex h-10 items-center rounded-lg bg-yns_yellow px-4 text-sm font-medium text-gray-900 transition duration-200 hover:bg-yellow-400">
            <i class="fas fa-check-circle mr-2"></i>Complete Job
          </button>
        </div>
      </form>
    </div>
  </div>
</x-app-layout>
<script>
  jQuery(document).ready(function($) {
    const modal = $('#complete-job-modal');
    const form = $('#complete-job-form');
    const job = @json($job);

    // Complete Job Button Handler
    $('#complete-job').on('click', function() {
      if (job.job_status === 'completed') return;
      modal.removeClass('hidden').addClass('flex');
    });

    // Cancel Button Handler
    $('#cancel-complete').on('click', function() {
      modal.removeClass('flex').addClass('hidden');
    });

    // Form Submission
    form.on('submit', function(e) {
      e.preventDefault();

      const submitButton = $(this).find('button[type="submit"]');
      submitButton.prop('disabled', true)
        .html('<i class="fas fa-spinner fa-spin mr-2"></i>Processing...');

      $.ajax({
        url: '{{ route('admin.dashboard.jobs.complete', ['dashboardType' => $dashboardType, 'job' => $job->id]) }}',
        method: 'POST',
        data: {
          final_amount: $('#final-amount').val(),
          completion_date: $('#completion-date').val(),
          _token: '{{ csrf_token() }}'
        },
        success: function(response) {
          if (response.success) {
            if (typeof Toastify !== 'undefined') {
              Toastify({
                text: 'Job completed successfully',
                duration: 3000,
                gravity: 'top',
                position: 'right',
                className: 'bg-green-500',
              }).showToast();
            }
            setTimeout(() => location.reload(), 1500);
          }
        },
        error: function(xhr) {
          if (typeof Toastify !== 'undefined') {
            Toastify({
              text: 'Error completing job',
              duration: 3000,
              gravity: 'top',
              position: 'right',
              className: 'bg-red-500',
            }).showToast();
          }
          console.error('Error:', xhr.responseText);
        },
        complete: function() {
          submitButton.prop('disabled', false)
            .html('<i class="fas fa-check-circle mr-2"></i>Complete Job');
        }
      });
    });

    // Close modal on outside click
    $(window).on('click', function(e) {
      if ($(e.target).is(modal)) {
        modal.removeClass('flex').addClass('hidden');
      }
    });
  });
</script>
