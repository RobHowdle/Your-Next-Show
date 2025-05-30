<x-app-layout :dashboardType="$dashboardType" :modules="$modules">
  <x-slot name="header">
    <x-sub-nav :userId="$userId" />
  </x-slot>

  <div class="mx-auto w-full max-w-screen-2xl py-16">
    <div class="relative mb-8 shadow-md sm:rounded-lg">
      <div class="grid grid-cols-[1.75fr_1.25fr] rounded-lg border border-white">
        <div class="rounded-l-lg border-r border-r-white bg-yns_dark_gray px-8 py-8">
          <div class="mb-8 flex flex-row items-center justify-between">
            <p class="font-heading text-3xl font-bold text-white">Finance Record: #{{ $finance->id }}</p>
            <div class="group flex gap-4">
              <a href="{{ route('admin.dashboard.edit-finance', ['dashboardType' => $dashboardType, 'id' => $finance->id]) }}"
                class="border-yns_blue bg-yns_blue hover:border-yns_blue hover:text-yns_blue rounded-lg border px-4 py-2 font-heading text-white transition duration-150 ease-in-out hover:bg-white">
                <span class="fas fa-edit mr-2"></span>Edit
              </a>
              <form
                action="{{ route('admin.dashboard.export-finance', ['dashboardType' => $dashboardType, 'finance' => $finance->id]) }}"
                method="POST">
                @csrf
                <button type="submit"
                  class="rounded-lg border border-green-500 bg-green-500 px-4 py-2 font-heading text-white transition duration-150 ease-in-out hover:border-green-700 hover:bg-green-700">
                  <span class="fas fa-file-download mr-2"></span>Export
                </button>
              </form>
            </div>
          </div>

          <div class="grid gap-6">
            <div class="rounded-lg border border-white/10 bg-white/5 p-6">
              <h2 class="mb-4 font-heading text-xl font-bold text-white">Event Details</h2>
              <div class="grid gap-4">
                <div class="grid grid-cols-2 gap-4">
                  <p class="text-white">Name: <span class="text-white/70">{{ $finance->name }}</span></p>
                  <p class="text-white">Type: <span class="text-white/70">{{ $finance->finance_type }}</span></p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                  <p class="text-white">Date From: <span
                      class="text-white/70">{{ \Carbon\Carbon::parse($finance->date_from)->format('jS M Y') }}</span>
                  </p>
                  <p class="text-white">Date To: <span
                      class="text-white/70">{{ \Carbon\Carbon::parse($finance->date_to)->format('jS M Y') }}</span></p>
                </div>
                <p class="text-white">Link to Event: <span
                    class="text-white/70">{{ $finance->external_link ?? 'None' }}</span></p>
                <div class="grid grid-cols-2 gap-4">
                  <p class="text-white">Created By: <span class="text-white/70">{{ $finance->user->first_name }}
                      {{ $finance->user->last_name }}</span></p>
                  <p class="text-white">Linked Service: <span
                      class="text-white/70">{{ $finance->serviceable->name }}</span></p>
                </div>
              </div>
            </div>

            @php
              $incoming = json_decode($finance->incoming, true) ?? [];
              $otherIncoming = json_decode($finance->other_incoming, true) ?? [];

              $fieldNames = [
                  'income_presale' => 'Presale',
                  'income_otd' => 'On The Door',
              ];
            @endphp

            <div class="rounded-lg border border-white/10 bg-white/5 p-6">
              <h2 class="mb-4 font-heading text-xl font-bold text-white">Income</h2>
              <div class="grid grid-cols-2 gap-4">
                @if (!empty($incoming))
                  @foreach ($incoming as $income)
                    <p class="text-white">{{ $fieldNames[$income['field']] ?? $income['field'] }}:
                      <span class="text-white/70">{{ formatCurrency($income['value']) }}</span>
                    </p>
                  @endforeach
                @endif
                @if (!empty($otherIncoming))
                  @foreach ($otherIncoming as $other)
                    <p class="text-white">{{ $other['label'] }}:
                      <span class="text-white/70">{{ formatCurrency($other['value']) }}</span>
                    </p>
                  @endforeach
                @endif
              </div>
            </div>

            @php
              $outgoings = json_decode($finance->outgoing, true) ?? [];
              $otherOutgoing = json_decode($finance->other_outgoing, true) ?? [];

              $fieldNames = [
                  'outgoing_venue' => 'Venue Hire',
                  'outgoing_band' => 'Artist(s)',
                  'outgoing_promotion' => 'Promotion',
                  'outgoing_rider' => 'Rider(s)',
              ];
            @endphp

            <div class="rounded-lg border border-white/10 bg-white/5 p-6">
              <h2 class="mb-4 font-heading text-xl font-bold text-white">Outgoings</h2>
              <div class="grid grid-cols-2 gap-4">
                @if (!empty($outgoings))
                  @foreach ($outgoings as $outgoing)
                    <p class="text-white">{{ $fieldNames[$outgoing['field']] ?? $outgoing['field'] }}:
                      <span class="text-white/70">{{ formatCurrency($outgoing['value']) }}</span>
                    </p>
                  @endforeach
                @endif
                @if (!empty($otherOutgoing))
                  @foreach ($otherOutgoing as $other)
                    <p class="text-white">{{ $other['label'] }}:
                      <span class="text-white/70">{{ formatCurrency($other['value']) }}</span>
                    </p>
                  @endforeach
                @endif
              </div>
            </div>

            <div class="rounded-lg border border-white/10 bg-white/5 p-6">
              <h2 class="mb-4 font-heading text-xl font-bold text-white">Summary</h2>
              <div class="grid grid-cols-2 gap-4">
                <p class="text-white">Total Income: <span
                    class="text-white/70">{{ formatCurrency($finance->total_incoming) }}</span></p>
                <p class="text-white">Total Outgoing: <span
                    class="text-white/70">{{ formatCurrency($finance->total_outgoing) }}</span></p>
                <p class="text-white">Desired Profit: <span
                    class="text-white/70">{{ formatCurrency($finance->desired_profit) }}</span></p>
                <p class="text-white">Total Profit: <span
                    class="text-white/70">{{ formatCurrency($finance->total_profit) }}</span></p>
                <p class="text-white">Profit Shortfall: <span
                    class="text-white/70">{{ formatCurrency($finance->total_remaining_to_desired_profit) }}</span></p>
              </div>
            </div>
          </div>
        </div>

        <div class="rounded-r-lg bg-yns_dark_blue px-8 py-8">
          <div class="mt-8">
            <div class="h-[400px] rounded-lg border border-white/10 bg-white/5 p-6">
              <h2 class="mb-4 font-heading text-xl font-bold text-white">Financial Overview</h2>
              <canvas id="financeChart"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  // Constants & Config
  const CURRENCY_FORMAT = new Intl.NumberFormat('en-GB', {
    style: 'currency',
    currency: 'GBP'
  });

  // State Management
  const state = {
    dashboardType: "{{ $dashboardType }}",
    chart: null
  };

  // Helper Functions
  const formatCurrency = (amount) => CURRENCY_FORMAT.format(amount || 0);

  // Chart Management
  const initializeCharts = () => {
    // Destroy existing chart if it exists
    if (state.chart) {
      state.chart.destroy();
    }

    const data = {
      labels: ['Income', 'Outgoing', 'Profit'],
      datasets: [{
        data: [
          {{ $finance->total_incoming }},
          {{ $finance->total_outgoing }},
          {{ $finance->total_profit }}
        ],
        backgroundColor: [
          'rgba(75, 192, 192, 0.6)', // Green for income
          'rgba(255, 99, 132, 0.6)', // Red for outgoing
          'rgba(255, 206, 86, 0.6)' // Yellow for profit
        ],
        borderColor: [
          'rgba(75, 192, 192, 1)',
          'rgba(255, 99, 132, 1)',
          'rgba(255, 206, 86, 1)'
        ],
        borderWidth: 1
      }]
    };

    const config = {
      type: 'pie',
      data: data,
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'right',
            labels: {
              color: 'white',
              font: {
                family: 'Inter',
                size: 14
              }
            }
          },
          tooltip: {
            callbacks: {
              label: function(context) {
                return `${context.label}: ${formatCurrency(context.raw)}`;
              }
            }
          }
        }
      }
    };

    state.chart = new Chart(
      document.getElementById('financeChart').getContext('2d'),
      config
    );
  };

  // Initialize when document is ready
  document.addEventListener('DOMContentLoaded', initializeCharts);
</script>
