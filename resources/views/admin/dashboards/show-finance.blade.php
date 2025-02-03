<x-app-layout :dashboardType="$dashboardType" :modules="$modules">
  <x-slot name="header">
    <x-sub-nav :userId="$userId" />
  </x-slot>

  <div class="mx-auto w-full max-w-screen-2xl py-16">
    <div class="relative mb-8 shadow-md sm:rounded-lg">
      <div class="min-w-screen-xl mx-auto max-w-screen-xl rounded-lg bg-yns_dark_gray text-white">
        <div class="rounded-l-lg bg-yns_dark_blue px-8 py-8">
          <div class="mb-8 flex flex-col justify-between text-white">
            <div class="mb-8 flex flex-row items-center justify-between">
              <p class="font-heading text-3xl font-bold">Finance Record: #{{ $finance->id }}</p>
              <div class="group flex gap-4">
                <a href="{{ route('admin.dashboard.edit-finance', ['dashboardType' => $dashboardType, 'id' => $finance->id]) }}"
                  class="rounded-lg border bg-white px-4 py-2 font-bold text-black transition duration-150 ease-in-out hover:border-yns_yellow hover:text-yns_yellow">Edit<span
                    class="fas fa-edit ml-2"></span></a>
                <form
                  action="{{ route('admin.dashboard.export-finance', ['dashboardType' => $dashboardType, 'finance' => $finance->id]) }}"
                  method="POST">
                  @csrf
                  <button type="submit"
                    class="rounded-lg border bg-white px-4 py-2 font-bold text-black transition duration-150 ease-in-out hover:border-yns_yellow hover:text-yns_yellow">Export<span
                      class="fas fa-file-download ml-2"></span></button>
                </form>
              </div>
            </div>
            <div class="flex flex-col">
              <p class="mb-2 mt-4 font-heading text-2xl">{{ $finance->finance_type }}</p>
              <p class="mb-2 font-heading">Name: {{ $finance->name }}</p>
              <div class="grid grid-cols-2">
                <p class="mb-2 font-heading">Date From:
                  {{ \Carbon\Carbon::parse($finance->date_from)->format('jS M Y') }}</p>
                <p class="mb-2 font-heading">Date To: {{ \Carbon\Carbon::parse($finance->date_to)->format('jS M Y') }}
                </p>
              </div>
              <p class="mb-2 font-heading">Link to Event: {{ $finance->external_link ?? 'None' }}</p>
              <div class="grid grid-cols-2">
                <p class="mb-2 font-heading">Created By: {{ $finance->user->first_name }}
                  {{ $finance->user->last_name }}</p>
                <p class="mb-2 font-heading">Linked Promoter: {{ $finance->serviceable->name }}</p>
              </div>
              {{-- Income Section --}}
              <p class="mb-2 mt-4 font-heading text-2xl">Incoming</p>

              @php
                $incoming = json_decode($finance->incoming, true) ?? [];
                $otherIncoming = json_decode($finance->other_incoming, true) ?? [];

                $fieldNames = [
                    'income_presale' => 'Presale',
                    'income_otd' => 'On The Door',
                ];
              @endphp

              {{-- Standard Income Items --}}
              @if (!empty($incoming))
                <ul class="grid grid-cols-2">
                  @foreach ($incoming as $income)
                    <li class="mb-2 font-heading">
                      {{ $fieldNames[$income['field']] ?? $income['field'] }}:
                      {{ formatCurrency($income['value']) }}
                    </li>
                  @endforeach
                </ul>
              @endif

              {{-- Other Income Items with Labels --}}
              @if (!empty($otherIncoming))
                <ul class="grid grid-cols-2">
                  @foreach ($otherIncoming as $other)
                    <li class="mb-2 font-heading">
                      {{ $other['label'] }}: {{ formatCurrency($other['value']) }}
                    </li>
                  @endforeach
                </ul>
              @endif

              {{-- Outgoing Section --}}
              <p class="mb-2 mt-4 font-heading text-2xl">Outgoing</p>

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

              {{-- Standard Outgoing Items --}}
              @if (!empty($outgoings))
                <ul class="grid grid-cols-2">
                  @foreach ($outgoings as $outgoin)
                    <li class="mb-2 font-heading">
                      {{ $fieldNames[$outgoin['field']] ?? $outgoin['field'] }}:
                      {{ formatCurrency($outgoin['value']) }}
                    </li>
                  @endforeach
                </ul>
              @endif

              {{-- Other Income Items with Labels --}}
              @if (!empty($otherOutgoing))
                <ul class="grid grid-cols-2">
                  @foreach ($otherOutgoing as $other)
                    <li class="mb-2 font-heading">
                      {{ $other['label'] }}: {{ formatCurrency($other['value']) }}
                    </li>
                  @endforeach
                </ul>
              @endif
              <p class="mb-2 mt-4 font-heading text-2xl">Totals</p>
              <div class="grid grid-cols-2">
                <p class="mb-2 font-heading">Total Incoming: {{ formatCurrency($finance->total_incoming) ?? 'None' }}
                </p>
                <p class="mb-2 font-heading">Total Outgoing: {{ formatCurrency($finance->total_outgoing) ?? 'None' }}
                </p>
                <p class="mb-2 font-heading">Desired Profit: {{ formatCurrency($finance->desired_profit) ?? 'None' }}
                </p>
                <p class="mb-2 font-heading">Total Profit: {{ formatCurrency($finance->total_profit) ?? 'None' }}</p>
                <p class="mb-2 font-heading">Total Profit Shortfall:
                  {{ formatCurrency($finance->total_remaining_to_desired_profit) ?? 'None' }}
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
