<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Finance;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreUpdateFinanceRequest;

class FinanceController extends Controller
{
    protected function getUserId()
    {
        return Auth::id();
    }

    public function showFinances($dashboardType, Request $request)
    {
        $modules = collect(session('modules', []));
        $user = Auth::user();
        $role = $user->roles->first()->name;

        $linkedCompany = $user->linkedCompany();
        $serviceableId = $linkedCompany->id;
        $serviceableType = get_class($linkedCompany);

        $finances = Finance::where('serviceable_id', $serviceableId)
            ->where('serviceable_type', $serviceableType)
            ->get();

        if (!$linkedCompany) {
            // Handle case where no company is linked
            return response()->json(['error' => 'No company linked to user'], 404);
        }

        $totalIncome = $finances->sum('total_incoming'); // Replace 'incoming' with the correct column
        $totalOutgoing = $finances->sum('total_outgoing'); // Replace 'outgoing' with the correct column
        $totalProfit = $totalIncome - $totalOutgoing;

        if (request()->ajax()) {
            return response()->json([
                'totalIncome' => $totalIncome,
                'totalOutgoing' => $totalOutgoing,
                'totalProfit' => $totalProfit,
                'modules' => $modules,
                'user' => $user,
                'role' => $role,
                'financeRecords' => $finances->map(function ($finance, $dashboardType) {
                    return [
                        'name' => $finance->name, // Replace with actual column
                        'date_from' => $finance->date_from,
                        'date_to' => $finance->date_to,
                        'totalIncome' => $finance->total_incoming,
                        'totalOutgoing' => $finance->total_outgoing,
                        'totalProfit' => $finance->total_profit,
                        'link' => route('admin.dashboard.show-finance', [$dashboardType, $finance->id]), // Replace with correct route if needed
                    ];
                }),
            ]);
        }

        // If not an AJAX request, return the normal view
        return view('admin.dashboards.show-finances', [
            'userId' => $this->getUserId(),
            'dashboardType' => $dashboardType,
            'finances' => $finances,
            'serviceableType' => $serviceableType,
            'serviceableId' => $serviceableId,
            'totalIncome' => $totalIncome,
            'totalOutgoing' => $totalOutgoing,
            'totalProfit' => $totalProfit,
            'modules' => $modules,
            'user' => $user,
            'role' => $role,
        ]);
    }

    public function getFinancesData(Request $request, $dashboardType)
    {
        try {
            $rawDate = $request->get('date');
            $cleanDate = preg_replace('/GMT.*$/', '', $rawDate);
            $date = Carbon::parse(trim($cleanDate));
            $filter = $request->get('filter', 'day');
            $serviceableType = str_replace('AppModels', 'App\\Models\\', $request->get('serviceable_type'));
            $serviceableId = $request->get('serviceable_id');

            $query = Finance::where('serviceable_type', $serviceableType)
                ->where('serviceable_id', $serviceableId);

            // Add different date filtering based on filter type
            if ($filter === 'day') {
                $query->whereDate('date_from', '=', $date->toDateString());
            } else {
                $dateRange = match ($filter) {
                    'week' => [
                        $date->copy()->startOfWeek()->toDateString(),
                        $date->copy()->endOfWeek()->toDateString()
                    ],
                    'month' => [
                        $date->copy()->startOfMonth()->toDateString(),
                        $date->copy()->endOfMonth()->toDateString()
                    ],
                    'year' => [
                        $date->copy()->startOfYear()->toDateString(),
                        $date->copy()->endOfYear()->toDateString()
                    ],
                    default => [$date->toDateString(), $date->toDateString()]
                };

                $query->whereBetween('date_from', $dateRange);
            }

            $finances = $query->get();

            return response()->json([
                'dates' => $finances->pluck('date_from'),
                'incomeData' => $finances->pluck('total_incoming'),
                'outgoingData' => $finances->pluck('total_outgoing'),
                'profitData' => $finances->pluck('total_profit'),
                'financeRecords' => $finances->map(fn($finance) => [
                    'name' => $finance->name,
                    'link' => route('admin.dashboard.show-finance', [
                        'dashboardType' => $dashboardType,
                        'id' => $finance->id
                    ])
                ]),
                'totalIncome' => $finances->sum('total_incoming'),
                'totalOutgoing' => $finances->sum('total_outgoing'),
                'totalProfit' => $finances->sum('total_profit')
            ]);
        } catch (\Exception $e) {
            Log::error('Date parsing error:', [
                'message' => $e->getMessage(),
                'date' => $request->get('date')
            ]);
            return response()->json(['error' => 'Invalid date format'], 400);
        }
    }

    public function createFinance($dashboardType)
    {
        $modules = collect(session('modules', []));
        $user = Auth::user();
        $role = $user->roles->first()->name;


        return view('admin.dashboards.new-finance', [
            'userId' => $this->getUserId(),
            'user' => $user,
            'role' => $role,
            'dashboardType' => $dashboardType,
            'modules' => $modules,
        ]);
    }

    public function storeFinance($dashboardType, StoreUpdateFinanceRequest $request)
    {
        $modules = collect(session('modules', []));
        $validated = $request->validated();
        $user = Auth::user();
        $service = null;
        $serviceType = null;

        if ($dashboardType == 'promoter') {
            $service = $user->promoters()->first();
            $serviceType = 'App\Models\Promoter';
        } elseif ($dashboardType == 'artist') {
            $service = $user->otherService("Artist")->first();
            $serviceType = 'App\Models\OtherService';
        } elseif ($dashboardType == 'designer') {
            $service = $user->otherService("Designer")->first();
            $serviceType = 'App\Models\OtherService';
        } elseif ($dashboardType == 'photographer') {
            $service = $user->otherService("Photographer")->first();
            $serviceType = 'App\Models\OtherService';
        } elseif ($dashboardType == 'videographer') {
            $service = $user->otherService("Videographer")->first();
            $serviceType = 'App\Models\OtherService';
        } elseif ($dashboardType == 'venue') {
            $service = $user->venues()->first();
            $serviceType = 'App\Models\Venue';
        }

        $dateFrom = Carbon::createFromFormat('d-m-Y', $validated['date_from'])->format('Y-m-d');
        $dateTo = Carbon::createFromFormat('d-m-Y', $validated['date_to'])->format('Y-m-d');

        try {
            // Income calculations
            $incomeData = $this->formatAndCalculateIncome($validated);
            $outgoingData = $this->formatAndCalculateOutgoing($validated);

            $newPromoterBudget = Finance::create([
                'user_id' => $user->id,
                'serviceable_id' => $service->id,
                'serviceable_type' => $serviceType,
                'finance_type' => 'Budget',
                'name' => $validated['budget_name'],
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'external_link' => $validated['external_link'],
                'incoming' => $incomeData['incoming'],
                'other_incoming' => $incomeData['other_incoming'],
                'outgoing' => $outgoingData['outgoing'],
                'other_outgoing' => $outgoingData['other_outgoing'],
                'desired_profit' => $validated['desired_profit'],
                'total_incoming' => $validated['income_total'],
                'total_outgoing' => $validated['outgoing_total'],
                'total_profit' => $validated['profit_total'],
                'total_remaining_to_desired_profit' => $validated['desired_profit_remaining'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Your Budget Saved!',
                'redirect_url' => route(
                    'admin.dashboard.show-finance',
                    ['dashboardType' => $dashboardType, 'id' => $newPromoterBudget->id,]
                )
            ]);
        } catch (\Exception $e) {
            Log::error('Error saving budget:', ['message' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error saving budget. Please check the form for errors.',
                'errors' => session('errors') ? session('errors')->getBag('default')->getMessages() : []
            ], 422);
        }
    }

    public function showSingleFinance($dashboardType, $id)
    {
        $modules = collect(session('modules', []));
        $user = Auth::user();
        $role = $user->roles->first()->name;

        $finance = Finance::findOrFail($id)->load('user', 'serviceable');

        return view('admin.dashboards.show-finance', [
            'userId' => $this->getUserId(),
            'user' => $user,
            'role' => $role,
            'dashboardType' => $dashboardType,
            'modules' => $modules,
            'finance' => $finance,
        ]);
    }

    public function editFinance($dashboardType, $id)
    {
        $modules = collect(session('modules', []));
        $user = Auth::user();
        $role = $user->roles->first()->name;

        $finance = Finance::findOrFail($id);

        return view('admin.dashboards.edit-finance', [
            'userId' => $this->getUserId(),
            'user' => $user,
            'role' => $role,
            'dashboardType' => $dashboardType,
            'modules' => $modules,
            'finance' => $finance,
        ]);
    }

    public function updateFinance($dashboardType, StoreUpdateFinanceRequest $request, $id)
    {
        $finance = Finance::findOrFail($id);
        $modules = collect(session('modules', []));
        $validated = $request->validated();
        $user = Auth::user();
        $service = null;
        $serviceType = null;

        if ($dashboardType == 'promoter') {
            $service = $user->promoters()->first();
            $serviceType = 'App\Models\Promoter';
        } elseif ($dashboardType == 'artist') {
            $service = $user->otherService("Artist")->first();
            $serviceType = 'App\Models\OtherService';
        } elseif ($dashboardType == 'designer') {
            $service = $user->otherService("Designer")->first();
            $serviceType = 'App\Models\OtherService';
        } elseif ($dashboardType == 'photographer') {
            $service = $user->otherService("Photographer")->first();
            $serviceType = 'App\Models\OtherService';
        } elseif ($dashboardType == 'videographer') {
            $service = $user->otherService("Videographer")->first();
            $serviceType = 'App\Models\OtherService';
        } elseif ($dashboardType == 'venue') {
            $service = $user->venues()->first();
            $serviceType = 'App\Models\Venue';
        }

        try {
            // Format dates
            $dateFrom = Carbon::createFromFormat('d-m-Y', $validated['date_from'])->format('Y-m-d');
            $dateTo = Carbon::createFromFormat('d-m-Y', $validated['date_to'])->format('Y-m-d');

            // Calculate and format income/outgoing data
            $incomeData = $this->formatAndCalculateIncome($validated);
            $outgoingData = $this->formatAndCalculateOutgoing($validated);

            // dd($validated, $incomeData, $outgoingData);

            // Update finance record
            $finance->update([
                'name' => $validated['budget_name'],
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'external_link' => $validated['external_link'],
                'incoming' => json_decode($incomeData['incoming'], true),
                'other_incoming' => $incomeData['other_incoming'],
                'outgoing' => json_decode($outgoingData['outgoing'], true),
                'other_outgoing' => json_decode($outgoingData['other_outgoing'], true),
                'desired_profit' => $validated['desired_profit'],
                'total_incoming' => $validated['income_total'],
                'total_outgoing' => $validated['outgoing_total'],
                'total_profit' => $validated['profit_total'],
                'total_remaining_to_desired_profit' => $validated['desired_profit_remaining'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Budget Updated Successfully!',
                'redirect_url' => route('admin.dashboard.show-finance', [
                    'dashboardType' => $dashboardType,
                    'id' => $finance->id
                ])
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating budget:', ['message' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error updating budget. Please check the form for errors.',
                'errors' => session('errors') ? session('errors')->getBag('default')->getMessages() : []
            ], 422);
        }
    }

    public function exportFinances(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|string',
            'filter' => 'required|string',
            'totalIncome' => 'required|string',
            'totalOutgoing' => 'required|string',
            'totalProfit' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Retrieve the data from the request
        $dateRange = $request->input('date');
        $filterValue = $request->input('filter');
        $totalIncome = $request->input('totalIncome');
        $totalOutgoing = $request->input('totalOutgoing');
        $totalProfit = $request->input('totalProfit');

        // Convert inputs to arrays if necessary
        $totalIncome = is_array($totalIncome) ? $totalIncome : [$totalIncome];
        $totalOutgoing = is_array($totalOutgoing) ? $totalOutgoing : [$totalOutgoing];
        $totalProfit = is_array($totalProfit) ? $totalProfit : [$totalProfit];

        // Prepare the data for the PDF
        $data = [];

        if ($filterValue === 'day') {
            // Handle single day case
            $data[] = [
                'date' => $dateRange,
                'totalIncome' => $totalIncome[0],
                'totalOutgoing' => $totalOutgoing[0],
                'totalProfit' => $totalProfit[0],
            ];
        } elseif ($filterValue === 'week') {
            // Handle week case
            $dates = explode(' to ', $dateRange);

            if (count($dates) !== 2) {
                return response()->json(['errors' => ['Invalid date range format']], 422);
            }

            list($startDate, $endDate) = $dates;

            // Validate the dates
            if (!strtotime($startDate) || !strtotime($endDate)) {
                return response()->json(['errors' => ['Invalid date format']], 422);
            }

            $currentDate = strtotime($startDate);
            $endDateTimestamp = strtotime($endDate);

            while ($currentDate <= $endDateTimestamp) {
                $formattedDate = date('Y-m-d', $currentDate);
                $data[] = [
                    'date' => $formattedDate,
                    'totalIncome' => $totalIncome[0],
                    'totalOutgoing' => $totalOutgoing[0],
                    'totalProfit' => $totalProfit[0],
                ];
                $currentDate = strtotime('+1 day', $currentDate);
            }
        } elseif ($filterValue === 'month') {
            // Handle month case
            $month = $dateRange; // This should be in 'YYYY-MM' format
            $year = substr($month, 0, 4);
            $monthNumber = substr($month, 5, 2);

            // Get the total days in the month
            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $monthNumber, $year);

            for ($day = 1; $day <= $daysInMonth; $day++) {
                $formattedDate = sprintf('%04d-%02d-%02d', $year, $monthNumber, $day);
                $data[] = [
                    'date' => $formattedDate,
                    'totalIncome' => $totalIncome[0],
                    'totalOutgoing' => $totalOutgoing[0],
                    'totalProfit' => $totalProfit[0],
                ];
            }
        } elseif ($filterValue === 'year') {
            // Handle year case
            $year = $dateRange; // This should be a year in 'YYYY' format

            // Validate the year
            if (!preg_match('/^\d{4}$/', $year)) {
                return response()->json(['errors' => ['Invalid year format']], 422);
            }

            // Loop through each month of the year
            for ($month = 1; $month <= 12; $month++) {
                $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                $totalIncomeForMonth = $totalIncome[0]; // Adjust if you want to calculate per month
                $totalOutgoingForMonth = $totalOutgoing[0]; // Adjust if you want to calculate per month
                $totalProfitForMonth = $totalProfit[0]; // Adjust if you want to calculate per month

                for ($day = 1; $day <= $daysInMonth; $day++) {
                    $formattedDate = sprintf('%04d-%02d-%02d', $year, $month, $day);
                    $data[] = [
                        'date' => $formattedDate,
                        'totalIncome' => $totalIncomeForMonth,
                        'totalOutgoing' => $totalOutgoingForMonth,
                        'totalProfit' => $totalProfitForMonth,
                    ];
                }
            }
        }

        // Generate the PDF
        $pdf = Pdf::loadView('pdf.finances', compact('data'));
        $pdfContent = $pdf->output();

        // Return the PDF to the browser
        return response()->stream(function () use ($pdfContent) {
            echo $pdfContent;
        }, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="finances_graph_data.pdf"',
        ]);
    }

    public function exportSingleFinance($dashboardType, $id)
    {
        $finance = Finance::findOrFail($id);

        // Generate the PDF
        $pdf = Pdf::loadView('pdf.single_finance', compact('finance'));
        $pdfContent = $pdf->output();

        // Return the PDF to the browser
        return response()->stream(function () use ($pdfContent) {
            echo $pdfContent;
        }, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="finance_data.pdf"',
        ]);
    }

    private function formatAndCalculateIncome($data)
    {
        // Format standard income
        $incoming = json_encode([
            ['field' => 'income_presale', 'value' => floatval($data['income_presale'])],
            ['field' => 'income_otd', 'value' => floatval($data['income_otd'])]
        ]);

        // Format other income
        $otherIncoming = [];
        if (isset($data['income_other']) && is_array($data['income_other'])) {
            foreach ($data['income_other'] as $key => $value) {
                $label = $data['income_label'][$key] ?? 'undefined';
                $otherIncoming[] = [
                    'field' => 'income_other_' . ($key + 1),
                    'label' => $label,
                    'value' => floatval($value)
                ];
            }
        }

        return [
            'incoming' => $incoming,
            'other_incoming' => json_encode($otherIncoming),
            'total_incoming' => floatval($data['income_total'])
        ];
    }

    private function formatAndCalculateOutgoing($data)
    {
        // Format standard outgoing
        $outgoing = json_encode([
            ['field' => 'outgoing_venue', 'value' => floatval($data['outgoing_venue'])],
            ['field' => 'outgoing_band', 'value' => floatval($data['outgoing_band'])],
            ['field' => 'outgoing_promotion', 'value' => floatval($data['outgoing_promotion'])],
            ['field' => 'outgoing_rider', 'value' => floatval($data['outgoing_rider'])]
        ]);

        // Format other outgoing
        $otherOutgoing = [];
        if (isset($data['outgoing_other']) && is_array($data['outgoing_other'])) {
            foreach ($data['outgoing_other'] as $key => $value) {
                $label = $data['outgoing_label'][$key] ?? 'undefined';
                $otherOutgoing[] = [
                    'field' => 'outgoing_other_' . ($key + 1),
                    'label' => $label,
                    'value' => floatval($value)
                ];
            }
        }

        return [
            'outgoing' => $outgoing,
            'other_outgoing' => json_encode($otherOutgoing),
            'total_outgoing' => floatval($data['outgoing_total'])
        ];
    }
}