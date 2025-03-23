<x-app-layout :dashboardType="$dashboardType" :modules="$modules">
  <div class="mx-auto w-full max-w-screen-2xl py-16">
    <div class="relative mb-8 shadow-md sm:rounded-lg">
      <div class="min-w-screen-xl mx-auto max-w-screen-xl rounded-lg bg-yns_dark_gray text-white">
        <!-- Header Row -->
        <div class="rounded-t-lg border-b border-white/10 bg-yns_dark_blue px-8 py-6">
          <div class="flex items-center justify-between">
            <h1 class="font-heading text-3xl font-bold">Financial Overview</h1>
            <div class="flex items-center gap-4">
              <div class="flex items-center gap-4">
                <x-select id="finance-filter" name="finance-filter"
                  class="min-w-[120px] rounded-lg border border-white/10 bg-white/5 px-4 py-2 text-white"
                  :options="[
                      'day' => 'Day',
                      'week' => 'Week',
                      'month' => 'Month',
                      'year' => 'Year',
                  ]" :selected="['day']" />
                <x-text-input id="date-picker"
                  class="rounded-lg border border-white/10 bg-white/5 px-4 py-2 text-white" />
              </div>
              <div class="ml-4 flex gap-4">
                <form action="{{ route('admin.dashboard.finances.export', ['dashboardType' => $dashboardType]) }}"
                  method="POST" id="exportForm">
                  @csrf
                  <input type="hidden" name="filter" id="export-filter">
                  <input type="hidden" name="date" id="export-date">
                  <button type="submit" id="exportButton"
                    class="border-yns_blue bg-yns_blue rounded-lg border px-4 py-2 font-heading text-white transition duration-150 ease-in-out hover:border-yns_dark_blue hover:bg-white hover:text-yns_dark_blue">
                    <span class="fas fa-file-export mr-2"></span>Export
                  </button>
                </form>
                <a href="{{ route('admin.dashboard.create-new-finance', ['dashboardType' => $dashboardType]) }}"
                  class="rounded-lg border border-green-500 bg-green-500 px-4 py-2 font-heading text-white transition duration-150 ease-in-out hover:border-green-700 hover:bg-green-700">
                  <span class="fas fa-plus-circle mr-2"></span>New Budget
                </a>
              </div>
            </div>
          </div>
        </div>

        <div class="p-8">
          <!-- Summary Cards Row -->
          <div class="mb-8 grid grid-cols-1 gap-6 md:grid-cols-3">
            <div class="rounded-lg border border-white/10 bg-yns_dark_blue p-6">
              <div class="flex flex-col">
                <span class="text-lg text-white/70">Total Income</span>
                <span id="totalIncoming" class="mt-2 font-heading text-2xl font-bold text-green-400">£0.00</span>
              </div>
            </div>
            <div class="rounded-lg border border-white/10 bg-yns_dark_blue p-6">
              <div class="flex flex-col">
                <span class="text-lg text-white/70">Total Outgoing</span>
                <span id="totalOutgoing" class="mt-2 font-heading text-2xl font-bold text-red-400">£0.00</span>
              </div>
            </div>
            <div class="rounded-lg border border-white/10 bg-yns_dark_blue p-6">
              <div class="flex flex-col">
                <span class="text-lg text-white/70">Total Profit</span>
                <span id="totalProfit" class="mt-2 font-heading text-2xl font-bold text-yns_yellow">£0.00</span>
              </div>
            </div>
          </div>

          <!-- Charts Grid -->
          <div class="mb-8 grid grid-cols-1 gap-6 lg:grid-cols-[1fr_2fr]">
            <!-- Summary Chart -->
            <div class="rounded-lg border border-white/10 bg-yns_dark_blue p-6">
              <h3 class="mb-4 font-heading text-lg font-bold">Financial Summary</h3>
              <div class="chart-container relative h-[300px]">
                <canvas id="summaryChart"></canvas>
              </div>
            </div>

            <!-- Trend Charts -->
            <div class="rounded-lg border border-white/10 bg-yns_dark_blue p-6">
              <h3 class="mb-4 font-heading text-lg font-bold">Monthly Trends</h3>
              <div class="chart-container relative h-[300px]">
                <canvas id="trendChart"></canvas>
              </div>
            </div>
          </div>

          <div class="rounded-lg border border-white/10 bg-yns_dark_blue p-6">
            <h3 class="mb-4 font-heading text-lg font-bold">Available Records</h3>
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-white/10">
                <thead>
                  <tr>
                    <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Name</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Location</th>
                    <th class="px-4 py-3 text-right text-sm font-medium text-white/70">Actions</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-white/10" id="financeRecords">
                  <!-- Records will be inserted here via JavaScript -->
                </tbody>
              </table>

              <!-- Pagination -->
              <div class="mt-4 flex items-center justify-between">
                <div class="text-sm text-white/70">
                  Showing <span id="recordsStart">0</span> to <span id="recordsEnd">0</span> of <span
                    id="recordsTotal">0</span> records
                </div>
                <div class="flex gap-2" id="pagination">
                  <!-- Pagination buttons will be inserted here -->
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Loading Indicator -->
  <div id="loading" class="fixed inset-0 z-50 hidden bg-black/50">
    <div class="flex h-full items-center justify-center">
      <div class="rounded-lg bg-white p-6">
        <span class="fas fa-circle-notch fa-spin text-yns_blue text-2xl"></span>
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
    serviceableType: "{{ $serviceableType ?? '' }}",
    dashboardType: "{{ $dashboardType }}",
    serviceableId: "{{ $serviceableId ?? '' }}",
    charts: {
      summary: null,
      trend: null
    },
    pagination: {
      currentPage: 1,
      perPage: 10,
      total: 0
    }
  };

  // Helper Functions
  const formatCurrency = (amount) => CURRENCY_FORMAT.format(amount || 0);

  const updateExportForm = () => {
    const filter = $('#finance-filter').val();
    const currentDate = $('#date-picker').val();

    $('#export-filter').val(filter);
    $('#export-date').val(currentDate);
  };

  const updateTotals = (response) => {
    try {
      $('#totalIncoming').text(formatCurrency(response?.totalIncome || 0));
      $('#totalOutgoing').text(formatCurrency(response?.totalOutgoing || 0));
      $('#totalProfit').text(formatCurrency(response?.totalProfit || 0));
    } catch (error) {
      console.error('Error updating totals:', error);
    }
  };

  const updateFinanceRecords = (records) => {
    if (!records.length) {
      $('#financeRecords').html(`
            <tr>
                <td colspan="3" class="px-4 py-8 text-center text-white/70">
                    No records found for the selected period
                </td>
            </tr>
        `);
      updatePagination(0);
      return;
    }

    state.pagination.total = records.length;
    const start = (state.pagination.currentPage - 1) * state.pagination.perPage;
    const end = Math.min(start + state.pagination.perPage, records.length);
    const paginatedRecords = records.slice(start, end);

    const recordsHtml = paginatedRecords.map(record => `
        <tr class="transition duration-150 ease-in-out hover:bg-white/5">
            <td class="whitespace-nowrap px-4 py-3 text-sm">
                <span class="font-medium text-white">${record.name}</span>
            </td>
            <td class="whitespace-nowrap px-4 py-3 text-sm text-white/70">
                ${record.location || 'N/A'}
            </td>
            <td class="whitespace-nowrap px-4 py-3 text-right text-sm">
                <a href="${record.link}" 
                   class="rounded-lg border border-yns_blue px-3 py-1 text-yns_blue transition duration-150 ease-in-out hover:bg-yns_blue hover:text-white">
                    View
                </a>
            </td>
        </tr>
    `).join('');

    $('#financeRecords').html(recordsHtml);
    updatePagination(records.length);
  };

  const updatePagination = (total) => {
    const {
      currentPage,
      perPage
    } = state.pagination;
    const totalPages = Math.ceil(total / perPage);
    const start = total ? (currentPage - 1) * perPage + 1 : 0;
    const end = Math.min(start + perPage - 1, total);

    // Update the records count
    $('#recordsStart').text(start);
    $('#recordsEnd').text(end);
    $('#recordsTotal').text(total);

    // Generate pagination buttons
    if (totalPages <= 1) {
      $('#pagination').empty();
      return;
    }

    const buttons = [];

    // Previous button
    buttons.push(`
        <button 
            class="rounded border border-white/10 px-3 py-1 text-sm ${currentPage === 1 ? 'cursor-not-allowed opacity-50' : 'hover:bg-white/5'}"
            ${currentPage === 1 ? 'disabled' : ''}
            onclick="changePage(${currentPage - 1})">
            Previous
        </button>
    `);

    // Page numbers
    for (let i = 1; i <= totalPages; i++) {
      if (
        i === 1 ||
        i === totalPages ||
        (i >= currentPage - 1 && i <= currentPage + 1)
      ) {
        buttons.push(`
                <button 
                    class="rounded px-3 py-1 text-sm ${i === currentPage ? 'bg-yns_blue text-white' : 'hover:bg-white/5'}"
                    onclick="changePage(${i})">
                    ${i}
                </button>
            `);
      } else if (
        i === currentPage - 2 ||
        i === currentPage + 2
      ) {
        buttons.push(`<span class="px-2">...</span>`);
      }
    }

    // Next button
    buttons.push(`
        <button 
            class="rounded border border-white/10 px-3 py-1 text-sm ${currentPage === totalPages ? 'cursor-not-allowed opacity-50' : 'hover:bg-white/5'}"
            ${currentPage === totalPages ? 'disabled' : ''}
            onclick="changePage(${currentPage + 1})">
            Next
        </button>
    `);

    $('#pagination').html(buttons.join(''));
  };

  // Add the page change handler
  const changePage = (page) => {
    state.pagination.currentPage = page;
    fetchFinanceData(
      $('#date-picker').val(),
      $('#finance-filter').val()
    );
  };

  // Chart Management
  const createChart = (ctx, config) => {
    try {
      return new Chart(ctx, config);
    } catch (error) {
      console.error('Chart initialization error:', error);
      return null;
    }
  };

  const initializeCharts = () => {
    const summaryCtx = document.getElementById('summaryChart')?.getContext('2d');
    const trendCtx = document.getElementById('trendChart')?.getContext('2d');


    // Destroy existing charts if they exist
    if (state.charts.summary) {
      state.charts.summary.destroy();
      state.charts.summary = null;
    }
    if (state.charts.trend) {
      state.charts.trend.destroy();
      state.charts.trend = null;
    }

    if (!summaryCtx || !trendCtx) {
      console.error('Could not find chart contexts');
      return;
    }

    const baseConfig = {
      type: 'doughnut',
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '75%',
        plugins: {
          legend: {
            position: 'bottom',
            labels: {
              color: '#ffffff', // Force white color
              font: {
                family: 'Inter',
                size: 14,

              },
              padding: 20
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

    // Initialize summary chart
    if (summaryCtx) {
      state.charts.summary = new Chart(summaryCtx, {
        type: 'doughnut',
        data: {
          labels: ['Income', 'Outgoing', 'Profit'],
          datasets: [{
            data: [0, 0, 0],
            backgroundColor: [
              'rgba(34, 197, 94, 0.8)',
              'rgba(239, 68, 68, 0.8)',
              'rgba(255, 198, 0, 0.8)'
            ],
            borderColor: [
              'rgba(34, 197, 94, 1)',
              'rgba(239, 68, 68, 1)',
              'rgba(255, 198, 0, 1)'
            ],
            borderWidth: 2,
            hoverOffset: 4
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          cutout: '75%',
          plugins: {
            legend: {
              position: 'bottom',
              align: 'center',
              labels: {
                color: 'white',
                font: {
                  family: 'Inter',
                  size: 14,
                  weight: '500'
                },
                padding: 20,
                usePointStyle: true,
                filter: function(item, chart) {
                  return true; // Show all labels
                }
              }
            },
            tooltip: {
              callbacks: {
                label: function(context) {
                  const total = context.dataset.data.reduce((a, b) => a + b, 0);
                  const percentage = ((context.raw / total) * 100).toFixed(1);
                  return `${context.label}: ${percentage}%`;
                }
              }
            }
          }
        }
      });
    }

    // Initialize trend chart if needed
    if (trendCtx) {
      state.charts.trend = new Chart(trendCtx, {
        type: 'line',
        data: {
          labels: [],
          datasets: []
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: 'bottom',
              labels: {
                color: 'white',
                font: {
                  family: 'Inter',
                  size: 14
                }
              }
            }
          },
          scales: {
            y: {
              ticks: {
                color: 'white',
                callback: function(value) {
                  return formatCurrency(value);
                }
              }
            },
            x: {
              ticks: {
                color: 'white'
              }
            }
          }
        }
      });
    };
  }

  // Add after the helper functions
  const showEmptyState = (container) => {
    const emptyStateHtml = `
        <div class="absolute inset-0 flex items-center justify-center">
            <div class="text-center">
                <p class="text-lg text-white/70 place-items-center">
                    <span class="fas fa-chart-line mb-3 block text-3xl opacity-50"></span>
                    No Financial Data Found for Selected Date(s)
                </p>
            </div>
        </div>
    `;

    $(container).closest('.relative').append(emptyStateHtml);
  };

  const clearEmptyStates = () => {
    $('.chart-container .absolute').remove();
  };

  // Update the updateCharts function
  const updateCharts = (data) => {
    clearEmptyStates();

    // Check if we have any data
    const hasData = data && (
      data.totalIncome ||
      data.totalOutgoing ||
      data.totalProfit ||
      (data.dates && data.dates.length)
    );

    if (!hasData) {
      showEmptyState('#summaryChart');
      showEmptyState('#trendChart');
      return;
    }

    // Update summary chart
    if (state.charts.summary) {
      state.charts.summary.data.datasets[0].data = [
        data.totalIncome || 0,
        data.totalOutgoing || 0,
        data.totalProfit || 0
      ];
      state.charts.summary.update();
    }

    // Update trend chart
    if (state.charts.trend) {
      state.charts.trend.data.labels = data.dates;
      state.charts.trend.data.datasets = [{
          label: 'Income',
          data: data.incomeData,
          borderColor: 'rgba(34, 197, 94, 1)',
          backgroundColor: 'rgba(34, 197, 94, 0.1)',
          fill: true,
          tension: 0.4
        },
        {
          label: 'Outgoing',
          data: data.outgoingData,
          borderColor: 'rgba(239, 68, 68, 1)',
          backgroundColor: 'rgba(239, 68, 68, 0.1)',
          fill: true,
          tension: 0.4
        },
        {
          label: 'Profit',
          data: data.profitData,
          borderColor: 'rgba(255, 198, 0, 1)',
          backgroundColor: 'rgba(255, 198, 0, 0.1)',
          fill: true,
          tension: 0.4
        }
      ];

      state.charts.trend.update();
    }
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

      if (response) {
        updateCharts(response);
        updateTotals(response);
        updateFinanceRecords(response.financeRecords || []);
      }

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
    const initialFilter = FILTER_TYPES.DAY;

    // Initialize Charts
    initializeCharts();

    // Setup Date Picker
    const datePicker = flatpickr("#date-picker", {
      dateFormat: "Y-m-d",
      defaultDate: today,
      onChange: (selectedDates) => {
        if (selectedDates && selectedDates.length > 0) {
          fetchFinanceData(selectedDates[0], $('#finance-filter').val());
          updateExportForm();
        }
      }
    });

    // Add export form handler
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // Update your export form submission
    $('#exportForm').on('submit', function(e) {
      e.preventDefault();

      const filter = $('#finance-filter').val();
      const currentDate = $('#date-picker').val();

      const formData = new FormData();
      formData.append('filter', filter);
      formData.append('date', currentDate);
      formData.append('_token', csrfToken);

      fetch(this.action, {
          method: 'POST',
          body: formData,
          headers: {
            'X-Requested-With': 'XMLHttpRequest'
          }
        })
        .then(response => {
          if (!response.ok) {
            return response.json().then(err => Promise.reject(err));
          }
          return response.blob();
        })
        .then(blob => {
          const url = window.URL.createObjectURL(blob);
          const a = document.createElement('a');
          a.href = url;
          a.download = 'finances_report.pdf';
          document.body.appendChild(a);
          a.click();
          window.URL.revokeObjectURL(url);
        })
        .catch(error => {
          console.error('Export error:', error);
          Swal.fire({
            icon: 'error',
            title: 'Export Failed',
            text: error.message || 'Failed to generate finance report'
          });
        });
    });

    // Setup Filter Change Handler
    $('#finance-filter').change(function() {
      const filter = $(this).val();
      configureDatePicker(filter, datePicker);
      fetchFinanceData(datePicker.selectedDates[0], filter);
      updateExportForm();
    });

    // Trigger initial fetch
    $('#finance-filter').val(initialFilter);
    fetchFinanceData(today, initialFilter);
  });
</script>
