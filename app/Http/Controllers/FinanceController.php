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
        $otherIncoming = json_decode($finance->other_incoming, true) ?? [];
        $otherOutgoing = json_decode($finance->other_outgoing, true) ?? [];

        return view('admin.dashboards.edit-finance', [
            'userId' => $this->getUserId(),
            'user' => $user,
            'role' => $role,
            'dashboardType' => $dashboardType,
            'modules' => $modules,
            'finance' => $finance,
            'otherIncoming' => $otherIncoming,
            'otherOutgoing' => $otherOutgoing,
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

    public function exportFinances($dashboardType, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|string',
            'filter' => 'required|string',
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed:', $validator->errors()->toArray());
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            // Get the user's linked company
            $user = Auth::user();
            $linkedCompany = $user->linkedCompany();
            $serviceableId = $linkedCompany->id;
            $serviceableType = get_class($linkedCompany);

            // Initialize query
            $query = Finance::where('serviceable_type', $serviceableType)
                ->where('serviceable_id', $serviceableId);

            // Apply date filtering based on filter type
            $filter = $request->input('filter');
            $dateInput = $request->input('date');

            switch ($filter) {
                case 'day':
                    $query->whereDate('date_from', Carbon::parse($dateInput)->toDateString());
                    break;

                case 'week':
                    $dates = explode(' to ', $dateInput);
                    if (count($dates) === 2) {
                        $query->whereBetween('date_from', [
                            Carbon::parse($dates[0])->startOfDay(),
                            Carbon::parse($dates[1])->endOfDay()
                        ]);
                    }
                    break;

                case 'month':
                    $date = Carbon::parse($dateInput);
                    $query->whereBetween('date_from', [
                        $date->copy()->startOfMonth(),
                        $date->copy()->endOfMonth()
                    ]);
                    break;

                case 'year':
                    $date = Carbon::parse($dateInput);
                    $query->whereBetween('date_from', [
                        $date->copy()->startOfYear(),
                        $date->copy()->endOfYear()
                    ]);
                    break;
            }

            // Get the records and calculate totals
            $records = $query->get();

            $data = [
                'filter' => $filter,
                'date_range' => [
                    'start' => $records->min('date_from'),
                    'end' => $records->max('date_from')
                ],
                'records' => $records->map(function ($record) {
                    return [
                        'name' => $record->name,
                        'date' => $record->date_from,
                        'income' => $record->total_incoming,
                        'outgoing' => $record->total_outgoing,
                        'profit' => $record->total_profit
                    ];
                }),
                'totals' => [
                    'income' => $records->sum('total_incoming'),
                    'outgoing' => $records->sum('total_outgoing'),
                    'profit' => $records->sum('total_profit')
                ],
                'company' => [
                    'name' => $linkedCompany->name,
                    'type' => $dashboardType,
                    'address' => $linkedCompany->location ?? '',
                    'phone' => $linkedCompany->contact_number ?? '',
                    'email' => $linkedCompany->contact_email ?? ''
                ]
            ];

            // Generate the PDF
            $pdf = Pdf::loadView('pdf.finances', compact('data'));

            return response()->stream(
                function () use ($pdf) {
                    echo $pdf->output();
                },
                200,
                [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="finances_report.pdf"'
                ]
            );
        } catch (\Exception $e) {
            Log::error('Error generating finance report:', [
                'message' => $e->getMessage(),
                'filter' => $request->input('filter'),
                'date' => $request->input('date')
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error generating finance report.'
            ], 500);
        }
    }

    public function exportSingleFinance($dashboardType, $id)
    {
        $finance = Finance::findOrFail($id);
        $user = Auth::user();
        $service = null;
        $serviceType = null;

        // Get the correct service and type based on dashboard
        switch ($dashboardType) {
            case 'promoter':
                $service = $user->promoters()->first();
                $serviceType = 'App\Models\Promoter';
                break;
            case 'artist':
            case 'designer':
            case 'photographer':
            case 'videographer':
                $service = $user->otherService(ucfirst($dashboardType))->first();
                $serviceType = 'App\Models\OtherService';
                break;
            case 'venue':
                $service = $user->venues()->first();
                $serviceType = 'App\Models\Venue';
                break;
            default:
                abort(404, 'Invalid dashboard type');
        }

        // Verify the finance record belongs to the correct service
        if ($finance->serviceable_id !== $service->id || $finance->serviceable_type !== $serviceType) {
            abort(403, 'Unauthorized access to finance record');
        }

        // Decode JSON strings into arrays
        $incoming = json_decode($finance->incoming, true) ?? [];
        $otherIncoming = json_decode($finance->other_incoming, true) ?? [];
        $outgoing = json_decode($finance->outgoing, true) ?? [];
        $otherOutgoing = json_decode($finance->other_outgoing, true) ?? [];

        // Transform incoming data into associative array
        $incomingData = [];
        foreach ($incoming as $item) {
            $incomingData[$item['field']] = $item['value'];
        }

        // Transform outgoing data into associative array
        $outgoingData = [];
        foreach ($outgoing as $item) {
            $outgoingData[$item['field']] = $item['value'];
        }

        // Merge the data with the finance model
        $finance = $finance->toArray();
        $finance['income_presale'] = $incomingData['income_presale'] ?? 0;
        $finance['income_otd'] = $incomingData['income_otd'] ?? 0;
        $finance['other_income_items'] = $otherIncoming;

        $finance['outgoing_venue'] = $outgoingData['outgoing_venue'] ?? 0;
        $finance['outgoing_band'] = $outgoingData['outgoing_band'] ?? 0;
        $finance['outgoing_promotion'] = $outgoingData['outgoing_promotion'] ?? 0;
        $finance['outgoing_rider'] = $outgoingData['outgoing_rider'] ?? 0;
        $finance['other_outgoing_items'] = $otherOutgoing;

        // Add company details to the finance array
        $finance['service_name'] = $service->name ?? '';
        $finance['service_type'] = $dashboardType;
        $finance['service_address'] = $service->location ?? '';
        $finance['service_phone'] = $service->contact_number ?? '';
        $finance['service_email'] = $service->contact_email ?? '';

        // Generate the PDF
        $pdf = Pdf::loadView('pdf.finances-single', ['finance' => $finance]);
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