<x-app-layout :dashboardType="$dashboardType" :modules="$modules">
  <x-slot name="header">
    <x-sub-nav :userId="$userId" />
  </x-slot>

  <div class="mx-auto w-full max-w-screen-2xl py-16">
    <div class="relative mb-8 shadow-md sm:rounded-lg">
      <div class="min-w-screen-xl mx-auto max-w-screen-xl rounded-lg bg-yns_dark_gray text-white">
        <div class="grid grid-cols-[1.25fr_1.75fr] rounded-lg border border-white">
          <div class="rounded-l-lg border-r border-r-white bg-yns_dark_blue px-8 py-8">
            <div class="mb-8 flex flex-row justify-between">
              <a href="{{ route('admin.dashboard.finances.export', ['dashboardType' => $dashboardType]) }}"
                id="exportButton"
                class="disabled rounded-lg border bg-yns_light_gray px-4 py-2 font-bold text-white transition duration-150 ease-in-out hover:border-yns_yellow hover:text-yns_yellow">Export</a>
              <a href="{{ route('admin.dashboard.create-new-finance', ['dashboardType' => $dashboardType]) }}"
                class="rounded-lg border bg-yns_light_gray px-4 py-2 font-bold text-white transition duration-150 ease-in-out hover:border-yns_yellow hover:text-yns_yellow">New
                Budget</a>
            </div>

            <p class="mb-4 font-heading text-2xl font-bold">Incoming/ Outgoing/ Profit</p>
            <p class="font-heading text-xl text-yns_light_gray" id="totalIncoming">Total Incoming: £0.00</p>
            <p class="mb-4 font-heading text-xl text-yns_light_gray" id="totalOutgoing">Total Outgoing: £0.00</p>
            <p class="font-heading text-2xl font-bold text-white" id="totalProfit">Total Profit: £0.00</p>
            <p class="mt-4 font-heading text-xl font-bold text-white">Avaliable Records:</p>
            <ul class="list-disc" id="financeRecords"></ul>
          </div>

          <div class="px-8 py-8">
            <p class="font-heading text-4xl font-bold">Finances</p>
            <div class="group mt-4 flex items-center justify-between space-x-4">
              <x-select id="finance-filter" name="fnance-filter" class="min-w-[120px] flex-grow-0 px-3 py-2"
                :options="[
                    'day' => 'Day',
                    'week' => 'Week',
                    'month' => 'Month',
                    'year' => 'Year',
                ]" :selected="['day']" />

              <x-text-input id="date-picker" class="w-auto text-black"></x-text-input>
            </div>

            <!-- Chart Containers -->
            <div class="h-full w-full">
              <canvas id="incomeChart"></canvas>
              <canvas id="outgoingChart"></canvas>
              <canvas id="profitChart"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>
</x-app-layout>
<script>
  // Constants & Config
  const CURRENCY_FORMAT = new Intl.NumberFormat('en-GB', {
    style: 'currency',
    currency: 'GBP'
  });

  const FILTER_TYPES = {
    DAY: 'day',
    WEEK: 'week',
    MONTH: 'month',
    YEAR: 'year'
  };

  // State Management
  const state = {
    serviceableType: "{{ $serviceableType }}",
    dashboardType: "{{ $dashboardType }}",
    serviceableId: "{{ $serviceableId }}",
    charts: {
      income: null,
      outgoing: null,
      profit: null
    },
    totals: {
      income: 0,
      outgoing: 0,
      profit: 0
    }
  };

  // Helper Functions
  const formatCurrency = (amount) => CURRENCY_FORMAT.format(amount || 0);

  const updateTotals = (response) => {
    $('#totalIncoming').text(`Total Income: ${formatCurrency(response.totalIncome)}`);
    $('#totalOutgoing').text(`Total Outgoing: ${formatCurrency(response.totalOutgoing)}`);
    $('#totalProfit').text(`Total Profit: ${formatCurrency(response.totalProfit)}`);
  };

  const updateFinanceRecords = (records) => {
    const financeLinks = records.map(record => `
        <li class="ml-4 my-2">
            <a href="${record.link}" 
               class="cursor-pointer hover:text-yns_yellow font-heading transition duration-150 ease-in-out">
                ${record.name}
            </a>
        </li>
    `).join('');

    $('#financeRecords').html(financeLinks);
  };

  // Chart Management
  const initializeCharts = () => {
    const baseConfig = {
      type: "bar",
      options: {
        responsive: true,
        scales: {
          x: {
            ticks: {
              color: "white"
            },
            border: {
              color: 'rgba(255,255,255,0.5)'
            },
            grid: {
              color: 'rgba(255,255,255,0.5)'
            }
          },
          y: {
            ticks: {
              color: 'white'
            },
            border: {
              color: 'rgba(255,255,255,0.5)'
            },
            grid: {
              color: 'rgba(255,255,255,0.5)'
            }
          }
        }
      }
    };

    const incomeConfig = {
      ...baseConfig,
      data: {
        labels: [],
        datasets: [{
          label: "Income",
          data: [],
          backgroundColor: "rgba(75, 192, 192, 0.6)",
          borderColor: "rgba(75, 192, 192, 1)",
          borderWidth: 1
        }]
      }
    };

    const outgoingConfig = {
      ...baseConfig,
      data: {
        labels: [],
        datasets: [{
          label: "Outgoing",
          data: [],
          backgroundColor: "rgba(255, 99, 132, 0.6)",
          borderColor: "rgba(255, 99, 132, 1)",
          borderWidth: 1
        }]
      }
    };

    const profitConfig = {
      ...baseConfig,
      data: {
        labels: [],
        datasets: [{
          label: "Profit",
          data: [],
          backgroundColor: "rgba(255, 206, 86, 0.6)",
          borderColor: "rgba(255, 206, 86, 1)",
          borderWidth: 1
        }]
      }
    };

    state.charts.income = new Chart($('#incomeChart'), incomeConfig);
    state.charts.outgoing = new Chart($('#outgoingChart'), outgoingConfig);
    state.charts.profit = new Chart($('#profitChart'), profitConfig);
  };

  const updateCharts = (data) => {
    // Update charts with response data including dates
    Object.entries(state.charts).forEach(([type, chart]) => {
      chart.data.labels = data.dates;
      chart.data.datasets[0].data = data[`${type}ChartData`];
      chart.update();
    });
  };

  // Data Fetching
  const fetchFinanceData = async (date, filter) => {
    try {
      $('#loading').removeClass('hidden');
      let dateToSend;

      if (filter === FILTER_TYPES.WEEK && Array.isArray(date)) {
        dateToSend = {
          start: date[0].toISOString().split('T')[0],
          end: date[1].toISOString().split('T')[0]
        };
      } else {
        const selectedDate = date instanceof Date ? date : new Date(date);
        dateToSend = selectedDate.toISOString().split('T')[0];
      }

      const response = await $.ajax({
        url: `/dashboard/${state.dashboardType}/finances/data`,
        method: 'GET',
        data: {
          filter,
          date: dateToSend,
          serviceable_id: state.serviceableId,
          serviceable_type: state.serviceableType
        }
      });

      // Update Charts with new data
      if (state.charts.income && response.dates) {
        state.charts.income.data.labels = response.dates;
        state.charts.income.data.datasets[0].data = response.incomeData;
        state.charts.income.update();

        state.charts.outgoing.data.labels = response.dates;
        state.charts.outgoing.data.datasets[0].data = response.outgoingData;
        state.charts.outgoing.update();

        state.charts.profit.data.labels = response.dates;
        state.charts.profit.data.datasets[0].data = response.profitData;
        state.charts.profit.update();
      }

      updateTotals(response);
      updateFinanceRecords(response.financeRecords || []);

    } catch (error) {
      console.error('Fetch Error:', error);
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'Failed to load finance data'
      });
    } finally {
      $('#loading').addClass('hidden');
    }
  };

  // Date Picker Configuration
  const configureDatePicker = (filter, picker) => {
    const config = {
      [FILTER_TYPES.DAY]: {
        dateFormat: 'Y-m-d',
        mode: 'single'
      },
      [FILTER_TYPES.WEEK]: {
        dateFormat: 'Y-m-d',
        mode: 'range',
        defaultDate: [new Date(), new Date().fp_incr(7)]
      },
      [FILTER_TYPES.MONTH]: {
        dateFormat: 'Y-m',
        mode: 'single'
      },
      [FILTER_TYPES.YEAR]: {
        dateFormat: 'Y',
        mode: 'single'
      }
    };

    const settings = config[filter] || config[FILTER_TYPES.DAY];
    Object.entries(settings).forEach(([key, value]) => picker.set(key, value));
  };

  // Initialize Everything
  $(document).ready(() => {
    const today = new Date();

    // Initialize Charts
    initializeCharts();

    // Setup Date Picker
    const datePicker = flatpickr("#date-picker", {
      dateFormat: "Y-m-d",
      defaultDate: today,
      onChange: (selectedDates) => {
        if (selectedDates && selectedDates.length > 0) {
          fetchFinanceData(selectedDates[0], $('#finance-filter').val());
        }
      }
    });

    // Setup Filter Change Handler
    $('#finance-filter').change(function() {
      const filter = $(this).val();
      configureDatePicker(filter, datePicker);
      const currentDate = datePicker.selectedDates[0] || today;
      fetchFinanceData(datePicker.selectedDates[0], filter);
    });
  });
</script>
