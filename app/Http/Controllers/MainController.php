<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AfterSalesService;
use App\Models\Approval;
use App\Models\BookNewspaper;
use App\Models\BudgetFyLo;
use App\Models\BudgetCode;
use App\Models\BudgetPlan;
use App\Models\BudgetUpload;
use App\Models\BusinessDuty;
use Illuminate\Http\Request;
use App\Models\Departments;
use App\Models\GeneralExpense;
use App\Models\InsurancePrem;
use App\Models\OfficeOperation;
use App\Models\OperationalSupport;
use App\Models\Remarks;
use App\Models\RepairMaint;
use App\Models\RepresentationExpense;
use App\Models\SupportMaterial;
use App\Models\Template;
use App\Models\TrainingEducation;
use App\Models\Utilities;
use App\Models\Workcenter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MainController extends Controller
{
    public function index(Request $request)
    {
        $dpt_id = $request->input('dpt_id');
        $current_year = $request->input('current_year', date('Y')); // Default 2025
        $previous_year = $request->input('previous_year', date('Y') - 1); // Default 2024
        $submission_type = $request->input('submission_type', ''); // Get submission_type
        $notificationController = new NotificationController();
        $notifications = $notificationController->getNotifications();
        $year = $request->input('year', date('Y')); // Default 2025
        $month = $request->input('month', date('m'));

        $months = [
            'January' => 'January',
            'February' => 'February',
            'March' => 'March',
            'April' => 'April',
            'May' => 'May',
            'June' => 'June',
            'July' => 'July',
            'August' => 'August',
            'September' => 'September',
            'October' => 'October',
            'November' => 'November',
            'December' => 'December'
        ];

        // [MODIFIKASI BARU] Ambil data user yang sedang login
        $user = Auth::user();
        $sect = $user->sect;
        $npk = $user->npk;

        // [MODIFIKASI BARU] Definisikan struktur divisi seperti di indexAll
        $divisions = [
            'PRODUCTION' => [
                'name' => 'Production',
                'departments' => ['1111', '1116', '1131', '1140', '1151', '1160', '1211', '1224', '1231', '1242'],
                'gm' => '01577',
                'dic' => '01555'
            ],
            'PRODUCTION CONTROL' => [
                'name' => 'Production Control',
                'departments' => ['1311', '1331', '1332', '1333', '1411'],
                'gm' => '01266',
                'dic' => '01555'
            ],
            'ENGINEERING' => [
                'name' => 'Engineering',
                'departments' => ['1341', '1351', '1361'],
                'gm' => '01961',
                'dic' => '01555'
            ],
            'PRODUCT ENGINEERING' => [
                'name' => 'Product Engineering',
                'departments' => ['2111', '2121'],
                'gm' => '01466',
                'dic' => 'EXP41'
            ],
            'QUALITY ASSURANCE' => [
                'name' => 'Quality Assurance',
                'departments' => ['3111', '3121', '3131'],
                'gm' => '01466',
                'dic' => 'EXP41'
            ],
            'HRGA & MIS' => [
                'name' => 'HRGA & MIS',
                'departments' => ['4111', '4131', '4141', '4311', '7111', '1111', '1131', '1151', '1211', '1231'],
                'gm' => '01561',
                'dic' => '02665'
            ],
            'MARKETING & PROCUREMENT' => [
                'name' => 'Marketing & Procurement',
                'departments' => ['4161', '4171', '4181', '5111'],
                'gm' => '01166',
                'dic' => '02665'
            ],
            'NO DIVISION' => [
                'name' => 'No Division',
                'departments' => ['4151', '4211', '6111', '6121'],
                'gm' => [
                    '4151' => '01166',
                    '4211' => '',
                    '6111' => '',
                    '6121' => ''
                ],
                'dic' => [
                    '4151' => '02665',
                    '4211' => '02665',
                    '6111' => 'EXP43',
                    '6121' => 'EXP43'
                ]
            ]
        ];

        // [MODIFIKASI BARU] Ambil daftar departemen berdasarkan peran user
        $departments = [];
        if ($sect == 'Kadiv' && in_array($npk, array_column($divisions, 'gm'))) {
            $allowed_depts = [];
            foreach ($divisions as $div) {
                if ($div['gm'] == $npk || (isset($div['gm']) && is_array($div['gm']) && in_array($npk, $div['gm']))) {
                    $allowed_depts = array_merge($allowed_depts, $div['departments']);
                }
            }
            $departments = Departments::whereIn('dpt_id', $allowed_depts)
                ->select('dpt_id', 'department')
                ->get()
                ->map(function ($dept) {
                    return [
                        'dpt_id' => $dept->dpt_id,
                        'department' => $dept->department,
                    ];
                })->toArray();
        } elseif ($sect == 'DIC' && in_array($npk, array_column($divisions, 'dic'))) {
            $allowed_depts = [];
            foreach ($divisions as $div) {
                if ($div['dic'] == $npk || (isset($div['dic']) && is_array($div['dic']) && in_array($npk, $div['dic']))) {
                    $allowed_depts = array_merge($allowed_depts, $div['departments']);
                }
            }
            $departments = Departments::whereIn('dpt_id', $allowed_depts)
                ->select('dpt_id', 'department')
                ->get()
                ->map(function ($dept) {
                    return [
                        'dpt_id' => $dept->dpt_id,
                        'department' => $dept->department,
                    ];
                })->toArray();
        } else {
            $departments = Departments::select('dpt_id', 'department')
                ->when($dpt_id, function ($query) use ($dpt_id) {
                    return $query->where('dpt_id', $dpt_id);
                })
                ->get()
                ->map(function ($dept) {
                    return [
                        'dpt_id' => $dept->dpt_id,
                        'department' => $dept->department,
                    ];
                })->toArray();
        }

        // [MODIFIKASI BARU] Department Query untuk Submission Totals
        $departmentData = collect($departments)->map(function ($department) use ($year, $submission_type, $sect) {
            $dpt_id = $department['dpt_id'];

            // [MODIFIKASI] Ambil data Last Year (periode $year)
            $lastYearData = BudgetFyLo::where('periode', $year)
                ->where('tipe', 'last_year')
                ->where('dept', $dpt_id)
                ->selectRaw('SUM(total) as total')
                ->first()->total ?? 0;

            // [MODIFIKASI] Ambil data Figure Outlook (periode $year + 1)
            $outlookData = BudgetFyLo::where('periode', $year + 1)
                ->where('tipe', 'outlook')
                ->where('dept', $dpt_id)
                ->selectRaw('SUM(total) as total')
                ->first()->total ?? 0;

            // [MODIFIKASI] Menghitung total budget proposal dari BudgetPlan untuk tahun $year
            $queryProposal = BudgetPlan::where('dpt_id', $dpt_id)
                ->whereYear('created_at', $year);

            // Apply submission_type filter
            if ($submission_type == 'asset') {
                $queryProposal->where('acc_id', '!=', 'CAPEX');
            } elseif ($submission_type == 'expenditure') {
                $queryProposal->where('acc_id', '=', 'CAPEX');
            }

            $proposal = $queryProposal->sum('price') ?? 0;

            // [MODIFIKASI BARU] Hitung jumlah pengajuan berdasarkan peran
            $countSubmissions = 0;
            if ($sect == 'Kadiv') {
                $countSubmissions = BudgetPlan::where('status', 3)
                    ->where('dpt_id', $dpt_id)
                    ->whereYear('created_at', $year)
                    ->distinct('sub_id')
                    ->count('sub_id');
            } elseif ($sect == 'DIC') {
                $countSubmissions = BudgetPlan::where('status', 4)
                    ->where('dpt_id', $dpt_id)
                    ->whereYear('created_at', $year)
                    ->distinct('sub_id')
                    ->count('sub_id');
            }

            return (object) [
                'department' => $department['department'],
                'dpt_id' => $dpt_id,
                'total_previous_year' => $lastYearData,
                'total_current_year_given' => $outlookData,
                'total_current_year_requested' => $proposal,
                'variance_last_year' => $proposal - $lastYearData,
                'variance_budget_given' => $proposal - $outlookData,
                'percentage_change_last_year' => $lastYearData != 0
                    ? (($proposal - $lastYearData) / $lastYearData * 100)
                    : ($proposal > 0 ? 100 : 0),
                'percentage_change_outlook' => $outlookData != 0
                    ? (($proposal - $outlookData) / $outlookData * 100)
                    : ($proposal > 0 ? 100 : 0),
                'count_submissions' => $countSubmissions
            ];
        });

        // [MODIFIKASI BARU] Calculate total for all departments
        $departmentTotal = (object) [
            'department' => 'TOTAL',
            'total_previous_year' => $departmentData->sum('total_previous_year'),
            'total_current_year_given' => $departmentData->sum('total_current_year_given'),
            'total_current_year_requested' => $departmentData->sum('total_current_year_requested'),
            'variance_last_year' => $departmentData->sum('variance_last_year'),
            'variance_budget_given' => $departmentData->sum('variance_budget_given'),
            'percentage_change_last_year' => $departmentData->sum('total_previous_year')
                ? ($departmentData->sum('variance_last_year') / $departmentData->sum('total_previous_year') * 100)
                : ($departmentData->sum('total_current_year_requested') > 0 ? 100 : 0),
            'percentage_change_outlook' => $departmentData->sum('total_current_year_given')
                ? ($departmentData->sum('variance_budget_given') / $departmentData->sum('total_current_year_given') * 100)
                : ($departmentData->sum('total_current_year_requested') > 0 ? 100 : 0)
        ];

        // [MODIFIKASI BARU] Calculate total amount for the pie chart
        $totalAmount = $departmentData->sum('total_current_year_requested');
        $departmentDataWithPercentage = $departmentData->map(function ($data) use ($totalAmount) {
            $percentage = $totalAmount > 0 ? ($data->total_current_year_requested / $totalAmount) * 100 : 0;
            return (object) [
                'department' => $data->department,
                'total_current_year' => $data->total_current_year_requested,
                'percentage' => $percentage,
            ];
        })->all();

        // [ASLI] Account Chart - Current Year (only status 7)
        $accountQueryCurrent = Account::select('accounts.account')
            ->with(['afterSalesServices' => function ($query) use ($year, $dpt_id, $submission_type) {
                $query->where('status', 7)
                    ->whereYear('created_at', $year)
                    ->when($dpt_id, function ($q) use ($dpt_id) {
                        return $q->where('dpt_id', $dpt_id);
                    })
                    ->when($submission_type == 'asset', function ($q) {
                        return $q->where('acc_id', '!=', 'CAPEX');
                    })
                    ->when($submission_type == 'expenditure', function ($q) {
                        return $q->where('acc_id', '=', 'CAPEX');
                    });
            }])
            ->get()
            ->map(function ($account) {
                $total = $account->afterSalesServices->sum('price') ?? 0;
                return (object) [
                    'account' => $account->account,
                    'total' => $total,
                ];
            });

        $accountDataCurrent = $accountQueryCurrent;

        // [ASLI] Account Chart - Previous Year (only status 7)
        $accountQueryPrevious = Account::select('accounts.account')
            ->with(['afterSalesServices' => function ($query) use ($year, $dpt_id, $submission_type) {
                $query->where('status', 7)
                    ->whereYear('created_at', $year - 1)
                    ->when($dpt_id, function ($q) use ($dpt_id) {
                        return $q->where('dpt_id', $dpt_id);
                    })
                    ->when($submission_type == 'asset', function ($q) {
                        return $q->where('acc_id', '!=', 'CAPEX');
                    })
                    ->when($submission_type == 'expenditure', function ($q) {
                        return $q->where('acc_id', '=', 'CAPEX');
                    });
            }])
            ->get()
            ->map(function ($account) {
                $total = $account->afterSalesServices->sum('price') ?? 0;
                return (object) [
                    'account' => $account->account,
                    'total' => $total,
                ];
            });

        $accountDataPrevious = $accountQueryPrevious;

        // [ASLI] Monthly Data (only status 7) from BudgetPlan
        $monthlyData = BudgetPlan::select(
            'month',
            DB::raw('SUM(price) as total'),
            DB::raw('YEAR(created_at) as year')
        )
            ->where('status', 7)
            ->whereYear('created_at', $year)
            ->when($dpt_id, function ($query) use ($dpt_id) {
                return $query->where('dpt_id', $dpt_id);
            })
            ->when($submission_type == 'asset', function ($q) {
                return $q->where('acc_id', '!=', 'CAPEX');
            })
            ->when($submission_type == 'expenditure', function ($q) {
                return $q->where('acc_id', '=', 'CAPEX');
            })
            ->groupBy('month', DB::raw('YEAR(created_at)'))
            ->get()
            ->groupBy('month')
            ->map(function ($group) {
                return (object) [
                    'month' => $group->first()->month,
                    'total' => $group->sum('total'),
                ];
            })
            ->values();

        // [ASLI] Ensure all months are represented
        $monthlyDataFormatted = [];
        foreach (array_keys($months) as $monthName) {
            $monthTotal = collect($monthlyData)->firstWhere('month', $monthName);
            $monthlyDataFormatted[] = (object) [
                'month' => $monthName,
                'total' => $monthTotal ? $monthTotal->total : 0
            ];
        }

        $years = BudgetPlan::select(DB::raw('DISTINCT YEAR(created_at) as year'))
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('index', compact(
            'departmentData',
            'departmentTotal',
            'departmentDataWithPercentage',
            'departments',
            'dpt_id',
            'accountDataCurrent',
            'accountDataPrevious',
            'years',
            'current_year',
            'previous_year',
            'notifications',
            'year',
            'month',
            'months',
            'monthlyDataFormatted',
            'submission_type',
            'sect' // [MODIFIKASI BARU] Tambahkan sect untuk logika di view
        ));
    }
    //     public function index(Request $request)
    //     {
    //         $dpt_id = $request->input('dpt_id');
    //         $current_year = $request->input('current_year', date('Y')); // Default 2025
    //         $previous_year = $request->input('previous_year', date('Y') - 1); // Default 2024
    //         $notificationController = new NotificationController();
    //         $notifications = $notificationController->getNotifications();
    //         $year = $request->input('year', date('Y')); // Default 2025, diubah berdasarkan input
    //         $month = $request->input('month', date('m'));
    // $submission_type = $request->input('submission_type', ''); // Add this line to get submission_type
    //         $months = [
    //             'January' => 'January',
    //             'February' => 'February',
    //             'March' => 'March',
    //             'April' => 'April',
    //             'May' => 'May',
    //             'June' => 'June',
    //             'July' => 'July',
    //             'August' => 'August',
    //             'September' => 'September',
    //             'October' => 'October',
    //             'November' => 'November',
    //             'December' => 'December'
    //         ];

    //         // Department Query using Eloquent with AfterSalesService
    //         $departmentQuery = Departments::select('departments.department', 'departments.dpt_id')
    //             ->when($dpt_id, function ($query) use ($dpt_id) {
    //                 return $query->where('dpt_id', $dpt_id);
    //             });

    //         $departmentData = $departmentQuery->get()->map(function ($department) use ($year) {
    //             $selected_year = $year;
    //             $previous_year = $year - 1;

    //             $total_current_year = BudgetPlan::where('dpt_id', $department->dpt_id)
    //                 ->where('status', 7)
    //                 ->whereYear('created_at', $selected_year)
    //                 ->sum('price');
    //             // + BusinessDuty::where('dpt_id', $department->dpt_id)
    //             // ->where('status', 7)
    //             // ->whereYear('created_at', $selected_year)
    //             // ->sum('price')
    //             // + GeneralExpense::where('dpt_id', $department->dpt_id)
    //             // ->where('status', 7)
    //             // ->whereYear('created_at', $selected_year)
    //             // ->sum('price')
    //             // + InsurancePrem::where('dpt_id', $department->dpt_id)
    //             // ->where('status', 7)
    //             // ->whereYear('created_at', $selected_year)
    //             // ->sum('price')
    //             // + RepresentationExpense::where('dpt_id', $department->dpt_id)
    //             // ->where('status', 7)
    //             // ->whereYear('created_at', $selected_year)
    //             // ->sum('price')
    //             // + SupportMaterial::where('dpt_id', $department->dpt_id)
    //             // ->where('status', 7)
    //             // ->whereYear('created_at', $selected_year)
    //             // ->sum('price')
    //             // + TrainingEducation::where('dpt_id', $department->dpt_id)
    //             // ->where('status', 7)
    //             // ->whereYear('created_at', $selected_year)
    //             // ->sum('price')
    //             // + Utilities::where('dpt_id', $department->dpt_id)
    //             // ->where('status', 7)
    //             // ->whereYear('created_at', $selected_year)
    //             // ->sum('price');

    //             $total_previous_year = BudgetPlan::where('dpt_id', $department->dpt_id)
    //                 ->where('status', 7)
    //                 ->whereYear('created_at', $previous_year)
    //                 ->sum('price');
    //             // + BusinessDuty::where('dpt_id', $department->dpt_id)
    //             // ->where('status', 7)
    //             // ->whereYear('created_at', $previous_year)
    //             // ->sum('price')
    //             // + GeneralExpense::where('dpt_id', $department->dpt_id)
    //             // ->where('status', 7)
    //             // ->whereYear('created_at', $previous_year)
    //             // ->sum('price')
    //             // + InsurancePrem::where('dpt_id', $department->dpt_id)
    //             // ->where('status', 7)
    //             // ->whereYear('created_at', $previous_year)
    //             // ->sum('price')
    //             // + RepresentationExpense::where('dpt_id', $department->dpt_id)
    //             // ->where('status', 7)
    //             // ->whereYear('created_at', $previous_year)
    //             // ->sum('price')
    //             // + SupportMaterial::where('dpt_id', $department->dpt_id)
    //             // ->where('status', 7)
    //             // ->whereYear('created_at', $previous_year)
    //             // ->sum('price')
    //             // + TrainingEducation::where('dpt_id', $department->dpt_id)
    //             // ->where('status', 7)
    //             // ->whereYear('created_at', $previous_year)
    //             // ->sum('price')
    //             // + Utilities::where('dpt_id', $department->dpt_id)
    //             // ->where('status', 7)
    //             // ->whereYear('created_at', $previous_year)
    //             // ->sum('price');


    //             $variance = $total_previous_year - $total_current_year;

    //             $percentage_change = $total_previous_year > 0 
    //         ? (($total_current_year - $total_previous_year) / $total_previous_year) * 100 
    //         : 0;
    //             return (object) [
    //                 'department' => $department->department,
    //                 'dpt_id' => $department->dpt_id,
    //                 'total_current_year' => $total_current_year,
    //                 'total_previous_year' => $total_previous_year,
    //                 'variance' => $variance,
    //                 'percentage_change' => $percentage_change, // Add percentage change
    //             ];
    //         });

    //         // Calculate total for all departments
    //         $departmentTotal = (object) [
    //             'department' => 'TOTAL',
    //             'total_previous_year' => $departmentData->sum('total_previous_year'),
    //             'total_current_year' => $departmentData->sum('total_current_year'),
    //             'variance' => $departmentData->sum('variance'),
    //         ];

    //         // Calculate total amount for the pie chart
    //         $totalAmount = $departmentData->sum('total_current_year');
    //         $departmentDataWithPercentage = $departmentData->map(function ($data) use ($totalAmount) {
    //             $percentage = $totalAmount > 0 ? ($data->total_current_year / $totalAmount) * 100 : 0;
    //             return (object) [
    //                 'department' => $data->department,
    //                 'total_current_year' => $data->total_current_year,
    //                 'percentage' => $percentage,
    //             ];
    //         })->all();

    //         $departments = Departments::all();

    //         // Account Chart - Current Year (only status 7)
    //         $accountQueryCurrent = Account::select('accounts.account')
    //             ->with(['afterSalesServices' => function ($query) use ($year, $dpt_id) {
    //                 $query->where('status', 7)
    //                     ->whereYear('created_at', $year)
    //                     ->when($dpt_id, function ($q) use ($dpt_id) {
    //                         return $q->where('dpt_id', $dpt_id);
    //                     });
    //             }])
    //             ->get()
    //             ->map(function ($account) {
    //                 $total = $account->afterSalesServices->sum('price') ?? 0;
    //                 return (object) [
    //                     'account' => $account->account,
    //                     'total' => $total,
    //                 ];
    //             });

    //         $accountDataCurrent = $accountQueryCurrent;

    //         // Account Chart - Previous Year (only status 7)
    //         $accountQueryPrevious = Account::select('accounts.account')
    //             ->with(['afterSalesServices' => function ($query) use ($year, $dpt_id) {
    //                 $query->where('status', 7)
    //                     ->whereYear('created_at', $year - 1)
    //                     ->when($dpt_id, function ($q) use ($dpt_id) {
    //                         return $q->where('dpt_id', $dpt_id);
    //                     });
    //             }])
    //             ->get()
    //             ->map(function ($account) {
    //                 $total = $account->afterSalesServices->sum('price') ?? 0;
    //                 return (object) [
    //                     'account' => $account->account,
    //                     'total' => $total,
    //                 ];
    //             });

    //         $accountDataPrevious = $accountQueryPrevious;

    //         // Monthly Data (only status 7) from all relevant tables
    //         $monthlyData = BudgetPlan::select(
    //             'month',
    //             DB::raw('SUM(price) as total'),
    //             DB::raw('YEAR(created_at) as year')
    //         )
    //             ->where('status', 7)
    //             ->whereYear('created_at', $year)
    //             ->when($dpt_id, function ($query) use ($dpt_id) {
    //                 return $query->where('dpt_id', $dpt_id);
    //             })
    //             ->groupBy('month', DB::raw('YEAR(created_at)'))

    //             // $monthlyData = $monthlyData->unionAll(
    //             //     BusinessDuty::select(
    //             //         'month',
    //             //         DB::raw('SUM(price) as total'),
    //             //         DB::raw('YEAR(created_at) as year')
    //             //     )->where('status', 7)
    //             //     ->whereYear('created_at', $year)
    //             //     ->when($dpt_id, function ($query) use ($dpt_id) {
    //             //         return $query->where('dpt_id', $dpt_id);
    //             //     })
    //             //     ->groupBy('month', DB::raw('YEAR(created_at)'))
    //             // )
    //             // ->unionAll(
    //             //     GeneralExpense::select(
    //             //         'month',
    //             //         DB::raw('SUM(price) as total'),
    //             //         DB::raw('YEAR(created_at) as year')
    //             //     )->where('status', 7)
    //             //     ->whereYear('created_at', $year)
    //             //     ->when($dpt_id, function ($query) use ($dpt_id) {
    //             //         return $query->where('dpt_id', $dpt_id);
    //             //     })
    //             //     ->groupBy('month', DB::raw('YEAR(created_at)'))
    //             // )
    //             // ->unionAll(
    //             //     InsurancePrem::select(
    //             //         'month',
    //             //         DB::raw('SUM(price) as total'),
    //             //         DB::raw('YEAR(created_at) as year')
    //             //     )->where('status', 7)
    //             //     ->whereYear('created_at', $year)
    //             //     ->when($dpt_id, function ($query) use ($dpt_id) {
    //             //         return $query->where('dpt_id', $dpt_id);
    //             //     })
    //             //     ->groupBy('month', DB::raw('YEAR(created_at)'))
    //             // )
    //             // ->unionAll(
    //             //     RepresentationExpense::select(
    //             //         'month',
    //             //         DB::raw('SUM(price) as total'),
    //             //         DB::raw('YEAR(created_at) as year')
    //             //     )->where('status', 7)
    //             //     ->whereYear('created_at', $year)
    //             //     ->when($dpt_id, function ($query) use ($dpt_id) {
    //             //         return $query->where('dpt_id', $dpt_id);
    //             //     })
    //             //     ->groupBy('month', DB::raw('YEAR(created_at)'))
    //             // )
    //             // ->unionAll(
    //             //     SupportMaterial::select(
    //             //         'month',
    //             //         DB::raw('SUM(price) as total'),
    //             //         DB::raw('YEAR(created_at) as year')
    //             //     )->where('status', 7)
    //             //     ->whereYear('created_at', $year)
    //             //     ->when($dpt_id, function ($query) use ($dpt_id) {
    //             //         return $query->where('dpt_id', $dpt_id);
    //             //     })
    //             //     ->groupBy('month', DB::raw('YEAR(created_at)'))
    //             // )
    //             // ->unionAll(
    //             //     TrainingEducation::select(
    //             //         'month',
    //             //         DB::raw('SUM(price) as total'),
    //             //         DB::raw('YEAR(created_at) as year')
    //             //     )->where('status', 7)
    //             //     ->whereYear('created_at', $year)
    //             //     ->when($dpt_id, function ($query) use ($dpt_id) {
    //             //         return $query->where('dpt_id', $dpt_id);
    //             //     })
    //             //     ->groupBy('month', DB::raw('YEAR(created_at)'))
    //             // )
    //             // ->unionAll(
    //             //     Utilities::select(
    //             //         'month',
    //             //         DB::raw('SUM(price) as total'),
    //             //         DB::raw('YEAR(created_at) as year')
    //             //     )->where('status', 7)
    //             //     ->whereYear('created_at', $year)
    //             //     ->when($dpt_id, function ($query) use ($dpt_id) {
    //             //         return $query->where('dpt_id', $dpt_id);
    //             //     })
    //             //     ->groupBy('month', DB::raw('YEAR(created_at)'))
    //             // )
    //             ->get()
    //             ->groupBy('month')
    //             ->map(function ($group) {
    //                 return (object) [
    //                     'month' => $group->first()->month,
    //                     'total' => $group->sum('total'),
    //                 ];
    //             })
    //             ->values();

    //         // Ensure all months are represented
    //         $monthlyDataFormatted = [];
    //         foreach (array_keys($months) as $monthName) {
    //             $monthTotal = collect($monthlyData)->firstWhere('month', $monthName);
    //             $monthlyDataFormatted[] = (object) [
    //                 'month' => $monthName,
    //                 'total' => $monthTotal ? $monthTotal->total : 0
    //             ];
    //         }

    //         $years = BudgetPlan::select(DB::raw('DISTINCT YEAR(created_at) as year'))
    //             ->orderBy('year', 'desc')
    //             ->pluck('year');

    //         // Debugging: Check the monthly data
    //         // dd($monthlyDataFormatted);

    //         // Available years for dropdown
    //         // $years = DB::query()
    //         //     ->select(DB::raw('DISTINCT YEAR(created_at) as year'))
    //         //     ->from(function ($query) {
    //         //         $query->from('after_sales_services')->select('created_at')
    //         //             ->union(DB::table('business_duties')->select('created_at'))
    //         //             ->union(DB::table('general_expenses')->select('created_at'))
    //         //             ->union(DB::table('insurance_prems')->select('created_at'))
    //         //             ->union(DB::table('representation_expenses')->select('created_at'))
    //         //             ->union(DB::table('support_materials')->select('created_at'))
    //         //             ->union(DB::table('training_education')->select('created_at'))
    //         //             ->union(DB::table('utilities')->select('created_at'))
    //         //             ->union(DB::table('office_operations')->select('created_at'))
    //         //             ->union(DB::table('repair_maints')->select('created_at'))
    //         //             ->union(DB::table('operational_supports')->select('created_at'));
    //         //     }, 'combined_years')
    //         //     ->orderBy('year', 'desc')
    //         //     ->pluck('year');

    //         return view('index', compact(
    //             'departmentData',
    //             'departmentTotal',
    //             'departmentDataWithPercentage', // Tambahkan data dengan persentase
    //             'departments',
    //             'dpt_id',
    //             'accountDataCurrent',
    //             'accountDataPrevious',
    //             'submission_type', // Add this to compact
    //             'years',
    //             'current_year',
    //             'previous_year',
    //             'notifications',
    //             'year',
    //             'month',
    //             'months',
    //             'monthlyDataFormatted'
    //         ));
    //     }

    public function indexAll(Request $request)
    {
        // Get authenticated user info
        $user = Auth::user();
        $dept = $user->dept;
        $needsApprovalCount = 0;
        $approvedThisYearCount = 0;
        $notapprovedThisYearCount = 0;
        $currentYear = date('Y');

        // Get notifications
        $notificationController = new NotificationController();
        $notifications = $notificationController->getNotifications();

        // Models for budget calculation
        $models = [
            BudgetPlan::class,
        ];

        // Status mapping
        $statusMap = [
            'Kadept' => 2,
            'Kadiv' => 3,
            'DIC' => 4,
            'PIC P&B' => 6,
            'Kadept P&B' => 7,
        ];
        $approvedStatusMap = [
            'Kadept' => 3,
            'Kadiv' => 4,
            'DIC' => 5,
            'PIC P&B' => 6,
            'Kadept P&B' => 7,
        ];
        $notapprovedStatusMap = [
            'Kadept' => 8,
            'Kadiv' => 9,
            'DIC' => 10,
            'PIC P&B' => 11,
            'Kadept PBD' => 12,
        ];

        $sect = $user->sect;
        $npk = $user->npk;
        $status = $statusMap[$sect] ?? 0;
        $approvedStatus = $approvedStatusMap[$sect] ?? null;
        $notapprovedStatus = $notapprovedStatusMap[$sect] ?? null;

        // Calculate needsApprovalCount
        if ($status) {
            foreach ($models as $model) {
                $needsApprovalCount += $model::where('status', $status)
                    ->where('dpt_id', $dept)
                    ->distinct('sub_id')
                    ->count();
            }
        }

        // Calculate approvedThisYearCount
        if ($approvedStatus) {
            $approvedThisYearCount = Approval::where('status', $approvedStatus)
                ->whereYear('created_at', $currentYear)
                ->whereHas('user', function ($query) use ($dept) {
                    $query->where('dept', $dept);
                })
                ->count();
        } else {
            foreach ($models as $model) {
                $approvedThisYearCount += $model::where('status', 7)
                    ->where('dpt_id', $dept)
                    ->whereYear('created_at', $currentYear)
                    ->distinct('sub_id')
                    ->count();
            }
        }

        // Calculate notapprovedThisYearCount
        if ($notapprovedStatus) {
            $notapprovedThisYearCount = Approval::where('status', $notapprovedStatus)
                ->whereYear('created_at', $currentYear)
                ->whereHas('user', function ($query) use ($dept) {
                    $query->where('dept', $dept);
                })
                ->count();
        } elseif (!in_array($sect, ['Kadept', 'Kadiv', 'DIC', 'PIC P&B', 'Kadept P&B'])) {
            $notapprovedThisYearCount = Approval::whereIn('status', [8, 9, 10, 11, 12])
                ->whereYear('created_at', $currentYear)
                ->whereHas('user', function ($query) use ($dept) {
                    $query->where('dept', $dept);
                })
                ->count();
        }

        // Calculate total budget by year for chart
        $budgetByYear = [];
        foreach ($models as $model) {
            $query = $model::where('status', 7)
                ->where('dpt_id', $dept)
                ->selectRaw('YEAR(created_at) as year, SUM(CASE WHEN ? THEN quantity * price ELSE amount END) as total_budget', [
                    $model == BudgetPlan::class
                ])
                ->groupBy('year')
                ->get();

            foreach ($query as $row) {
                $year = $row->year;
                $total = $row->total_budget;
                if (!isset($budgetByYear[$year])) {
                    $budgetByYear[$year] = 0;
                }
                $budgetByYear[$year] += $total;
            }
        }

        $years = array_keys($budgetByYear);
        $budgetValues = array_values($budgetByYear);
        $totalBudget = $budgetByYear[$currentYear] ?? 0;
        $totalBudgetFormatted = number_format($totalBudget, 2, '.', ',');

        // Get filter parameters
        $submission_type = $request->query('submission_type', '');
        $acc_id = $request->query('acc_id', '');
        $year = $request->query('year', $currentYear);
        // [MODIFIKASI SEBELUMNYA] Tambahkan parameter dept_id untuk filter departemen
        $dept_id = $request->query('dept_id', '');
        // [MODIFIKASI BARU] Tambahkan parameter div_id untuk filter divisi
        $div_id = $request->query('div_id', '');

        // [PENYESUAIAN BARU] Definisikan struktur divisi berdasarkan data baru
        $divisions = [
            'PRODUCTION' => [
                'name' => 'Production',
                'departments' => ['1111', '1116', '1131', '1140', '1151', '1160', '1211', '1224', '1231', '1242'],
                'gm' => '01577',
                'dic' => '01555'
            ],
            'PRODUCTION CONTROL' => [
                'name' => 'Production Control',
                'departments' => ['1311', '1331', '1332', '1333', '1411'],
                'gm' => '01266',
                'dic' => '01555'
            ],
            'ENGINEERING' => [
                'name' => 'Engineering',
                'departments' => ['1341', '1351', '1361'],
                'gm' => '01961',
                'dic' => '01555'
            ],
            'PRODUCT ENGINEERING' => [
                'name' => 'Product Engineering',
                'departments' => ['2111', '2121'],
                'gm' => '01466',
                'dic' => 'EXP41'
            ],
            'QUALITY ASSURANCE' => [
                'name' => 'Quality Assurance',
                'departments' => ['3111', '3121', '3131'],
                'gm' => '01466',
                'dic' => 'EXP41'
            ],
            'HRGA & MIS' => [
                'name' => 'HRGA & MIS',
                'departments' => ['4111', '4131', '4141', '4311', '7111', '1111', '1131', '1151', '1211', '1231'],
                'gm' => '01561',
                'dic' => '02665'
            ],
            'MARKETING & PROCUREMENT' => [
                'name' => 'Marketing & Procurement',
                'departments' => ['4161', '4171', '4181', '5111'],
                'gm' => '01166',
                'dic' => '02665'
            ],
            'NO DIVISION' => [
                'name' => 'No Division',
                'departments' => ['4151', '4211', '6111', '6121'],
                'gm' => [
                    '4151' => '01166',
                    '4211' => '',
                    '6111' => '',
                    '6121' => ''
                ],
                'dic' => [
                    '4151' => '02665',
                    '4211' => '02665',
                    '6111' => 'EXP43',
                    '6121' => 'EXP43'
                ]
            ]
        ];

        // [PENYESUAIAN BARU] Ambil daftar departemen untuk Kadiv atau DIC
        $departments = [];
        if ($sect == 'Kadiv' && in_array($npk, array_column($divisions, 'gm'))) {
            $allowed_depts = [];
            foreach ($divisions as $div) {
                if ($div['gm'] == $npk || (isset($div['gm']) && is_array($div['gm']) && in_array($npk, $div['gm']))) {
                    $allowed_depts = array_merge($allowed_depts, $div['departments']);
                }
            }
            $departments = Departments::whereIn('dpt_id', $allowed_depts)
                ->select('dpt_id', 'department')
                ->get()
                ->map(function ($dept) {
                    return [
                        'dpt_id' => $dept->dpt_id,
                        'department' => $dept->department,
                    ];
                })->toArray();
        } elseif ($sect == 'DIC' && $this->isUserDIC($npk, $divisions) && $div_id && !$dept_id) {
            $allowed_depts = $divisions[$div_id]['departments'] ?? [];
            $departments = Departments::whereIn('dpt_id', $allowed_depts)
                ->select('dpt_id', 'department')
                ->get()
                ->map(function ($dept) {
                    return [
                        'dpt_id' => $dept->dpt_id,
                        'department' => $dept->department,
                    ];
                })->toArray();
        } else {
            $departments = [
                [
                    'dpt_id' => $dept,
                    'department' => Departments::where('dpt_id', $dept)->first()->department ?? 'Unknown',
                ]
            ];
        }

        // Inisialisasi variabel untuk mencegah error undefined
        $totalDataLastYear = collect();
        $totalDataLastYearArray = [];
        $grandTotalLastYear = 0;
        $totalDataOutlookArray = [];
        $grandTotalOutlook = 0;
        $totalDataProposalArray = [];
        $grandTotalProposal = 0;
        $varianceByAccount = [];
        $varianceByAccountOutlook = [];
        $varianceGrandTotalLastYear = 0;
        $varianceGrandTotalLastYearPercentage = 0;
        $varianceGrandTotalOutlook = 0;
        $varianceGrandTotalOutlookPercentage = 0;
        // Inisialisasi $listAccount dan $listAccountNames di awal untuk semua kondisi
        $listAccount = Account::orderBy('account', 'asc')->pluck('acc_id')->toArray();
        $listAccountNames = Account::orderBy('account', 'asc')->pluck('account', 'acc_id')->toArray();

        // [PENYESUAIAN BARU] Logika untuk DIC tanpa div_id dan dept_id
        if ($sect == 'DIC' && $this->isUserDIC($npk, $divisions) && !$div_id && !$dept_id) {
            // Menampilkan Division Submission Totals
            $divisionData = [];
            foreach ($divisions as $divKey => $division) {
                // Cek apakah user DIC bertanggung jawab untuk divisi ini
                if ((is_array($division['dic']) && in_array($npk, $division['dic'])) ||
                    (!is_array($division['dic']) && $division['dic'] == $npk)
                ) {
                    // Ambil data Last Year (periode 2025) untuk semua departemen dalam divisi
                    $lastYearData = BudgetFyLo::where('periode', $year)
                        ->where('tipe', 'last_year')
                        ->whereIn('dept', $division['departments'])
                        ->selectRaw('SUM(total) as total')
                        ->first()->total ?? 0;

                    // Ambil data Figure Outlook (periode 2026) untuk semua departemen dalam divisi
                    $outlookData = BudgetFyLo::where('periode', $year + 1)
                        ->where('tipe', 'outlook')
                        ->whereIn('dept', $division['departments'])
                        ->selectRaw('SUM(total) as total')
                        ->first()->total ?? 0;

                    // Menghitung total budget proposal dari BudgetPlan untuk semua departemen dalam divisi
                    $proposal = BudgetPlan::whereIn('dpt_id', $division['departments'])
                        ->whereYear('created_at', $year)
                        ->selectRaw('SUM(CASE WHEN acc_id = "CAPEX" THEN month_value ELSE price END) as total_proposal')
                        ->first()
                        ->total_proposal ?? 0;

                    // Hitung jumlah pengajuan (status = 4) untuk semua departemen dalam divisi
                    $countSubmissions = BudgetPlan::whereIn('status', [4, 11])
                        ->whereIn('dpt_id', $division['departments'])
                        ->whereYear('created_at', $year)
                        ->distinct('sub_id')
                        ->count('sub_id');

                    // Apply filters if any
                    if ($submission_type == 'asset' && $acc_id == 'CAPEX') {
                        continue;
                    }
                    if ($submission_type == 'expenditure' && $acc_id != 'CAPEX') {
                        continue;
                    }

                    $divisionData[] = (object)[
                        'div_id' => $divKey,
                        'name' => $division['name'],
                        'total_previous_year' => $lastYearData,
                        'total_current_year_given' => $outlookData,
                        'total_current_year_requested' => $proposal,
                        'variance_last_year' => $proposal - $lastYearData,
                        'variance_budget_given' => $proposal - $outlookData,
                        'percentage_change_last_year' => $lastYearData != 0
                            ? (($proposal - $lastYearData) / $lastYearData * 100)
                            : ($proposal > 0 ? 100 : 0),
                        'percentage_change_outlook' => $outlookData != 0
                            ? (($proposal - $outlookData) / $outlookData * 100)
                            : ($proposal > 0 ? 100 : 0),
                        'count_submissions' => $countSubmissions
                    ];
                }
            }

            // Calculate totals for divisions
            $divisionTotal = (object)[
                'name' => 'Total',
                'total_previous_year' => array_sum(array_column($divisionData, 'total_previous_year')),
                'total_current_year_given' => array_sum(array_column($divisionData, 'total_current_year_given')),
                'total_current_year_requested' => array_sum(array_column($divisionData, 'total_current_year_requested')),
                'variance_last_year' => array_sum(array_column($divisionData, 'variance_last_year')),
                'variance_budget_given' => array_sum(array_column($divisionData, 'variance_budget_given')),
                'percentage_change_last_year' => array_sum(array_column($divisionData, 'total_previous_year')) != 0
                    ? (array_sum(array_column($divisionData, 'variance_last_year')) / array_sum(array_column($divisionData, 'total_previous_year')) * 100)
                    : (array_sum(array_column($divisionData, 'total_current_year_requested')) > 0 ? 100 : 0),
                'percentage_change_outlook' => array_sum(array_column($divisionData, 'total_current_year_given')) != 0
                    ? (array_sum(array_column($divisionData, 'variance_budget_given')) / array_sum(array_column($divisionData, 'total_current_year_given')) * 100)
                    : (array_sum(array_column($divisionData, 'total_current_year_requested')) > 0 ? 100 : 0)
            ];

            // Return view untuk menampilkan Division Submission Totals
            return view('index-all-divisions', compact(
                'divisionData',
                'divisionTotal',
                'needsApprovalCount',
                'approvedThisYearCount',
                'notapprovedThisYearCount',
                'totalBudgetFormatted',
                'notifications',
                'years',
                'budgetValues',
                'submission_type',
                'acc_id',
                'year',
                'sect'
            ));
        }

        // [PENYESUAIAN BARU] Logika untuk Kadiv: kelompokkan data berdasarkan departemen atau akun
        if ($sect == 'Kadiv' && in_array($npk, array_column($divisions, 'gm')) && $dept_id) {
            // Menampilkan Account Submission Totals untuk departemen tertentu
            $uploadedData = [
                'last_year' => [],
                'outlook' => []
            ];

            // [MODIFIKASI] Ambil data Last Year (periode 2025)
            $lastYearData = BudgetFyLo::where('periode', $year)
                ->where('tipe', 'last_year')
                ->where('dept', $dept_id)
                ->select('account', 'total')
                ->get()
                ->map(function ($item) {
                    return [
                        'account' => $item->account,
                        'amount' => (float) $item->total
                    ];
                })->toArray();

            // [MODIFIKASI] Ambil data Figure Outlook (periode 2026)
            $outlookData = BudgetFyLo::where('periode', $year + 1)
                ->where('tipe', 'outlook')
                ->where('dept', $dept_id)
                ->select('account', 'total')
                ->get()
                ->map(function ($item) {
                    return [
                        'account' => $item->account,
                        'amount' => (float) $item->total
                    ];
                })->toArray();

            $uploadedData['last_year'] = $lastYearData;
            $uploadedData['outlook'] = $outlookData;

            // [MODIFIKASI] Menghitung total budget proposal dari BudgetPlan per acc_id untuk tahun 2026
            $totalDataProposal = BudgetPlan::whereIn('acc_id', $listAccount)
                ->whereIn('acc_id', $listAccount)
                ->where('dpt_id', $dept_id)
                ->whereYear('created_at', $year)
                ->selectRaw('acc_id, SUM(CASE WHEN acc_id = "CAPEX" THEN month_value ELSE price END) as total_proposal')
                ->groupBy('acc_id')
                ->pluck('total_proposal', 'acc_id')
                ->map(fn($total) => (float) $total);

            $totalDataProposalArray = $totalDataProposal->toArray();
            Log::info('indexAll totalDataProposalArray: ', $totalDataProposalArray);
            $grandTotalProposal = $totalDataProposal->sum();

            $budgetProposalByAccount = $totalDataProposal->toArray(); // Gunakan $totalDataProposal langsung untuk konsistensi

            $totalDataLastYear = BudgetFyLo::where('periode', $year)
                ->where('tipe', 'last_year')
                ->whereIn('account', $listAccount)
                ->where('dept', $dept_id)
                ->select('account', DB::raw('SUM(total) as total'))
                ->groupBy('account')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [
                        $item->account => (float) $item->total
                    ];
                });
            $totalDataLastYearArray = $totalDataLastYear->toArray();
            $grandTotalLastYear = $totalDataLastYear->sum();

            $totalDataOutlook = BudgetFyLo::where('periode', $year + 1)
                ->where('tipe', 'outlook')
                ->whereIn('account', $listAccount)
                ->where('dept', $dept_id)
                ->selectRaw('account, SUM(total) as total_sum')
                ->groupBy('account')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [
                        $item->account => (float) $item->total_sum
                    ];
                });
            $totalDataOutlookArray = $totalDataOutlook->toArray();
            $grandTotalOutlook = $totalDataOutlook->sum();

            // Variance By Account (Proposal vs Last Year)
            foreach ($listAccount as $accId) {
                $proposal = $totalDataProposalArray[$accId] ?? 0;
                $lastYear = $totalDataLastYearArray[$accId] ?? 0;
                $variance = $proposal - $lastYear;

                $variancePercent = $proposal != 0
                    ? ($variance / $proposal * 100)
                    : 0;

                $varianceByAccount[$accId] = [
                    'varianceLastYear' => $variance,
                    'varianceLastYearPercent' => $variancePercent,
                ];
            }


            // Variance Grand Total (Proposal vs Last Year)
            $varianceGrandTotalLastYear = $grandTotalProposal - $grandTotalLastYear;
            $varianceGrandTotalLastYearPercentage = $grandTotalLastYear != 0
                ? ($varianceGrandTotalLastYear / $grandTotalLastYear) * 100
                : ($grandTotalProposal > 0 ? 100 : 0);

            // Variance By Account (Proposal vs outlook)
            foreach ($listAccount as $accId) {
                $proposal = $totalDataProposalArray[$accId] ?? 0;
                $outlook = $totalDataOutlookArray[$accId] ?? 0;
                $variance = $proposal - $outlook;
                // MODIFIKASI: Jika outlook = 0 dan proposal > 0, maka 100%
                $variancePercent = $outlook != 0
                    ? ($variance / $outlook) * 100
                    : ($proposal > 0 ? 100 : 0);

                $varianceByAccountOutlook[$accId] = [
                    'varianceOutlook' => $variance,
                    'varianceOutlookPercent' => $variancePercent,
                ];
            }

            // Variance Grand Total (Proposal vs outlook)
            $varianceGrandTotalOutlook = $grandTotalProposal - $grandTotalOutlook;
            $varianceGrandTotalOutlookPercentage = $grandTotalOutlook != 0
                ? ($varianceGrandTotalOutlook / $grandTotalOutlook) * 100
                : ($grandTotalProposal > 0 ? 100 : 0);

            // Get unique accounts from accounts table
            $allAccounts = Account::pluck('acc_id')->toArray();
            $accountNames = Account::pluck('account', 'acc_id')->toArray();

            // Merge with accounts from uploaded data
            $uploadedAccounts = array_unique(array_merge(
                array_column($uploadedData['last_year'], 'account'),
                array_column($uploadedData['outlook'], 'account')
            ));
            $allAccounts = array_unique(array_merge($allAccounts, $uploadedAccounts));

            // Prepare account data for view
            $accountData = [];
            foreach ($allAccounts as $accountId) {
                // Cari data Last Year yang diupload
                $lastYearAmount = $totalDataLastYearArray[$accountId] ?? 0;

                // Cari data Outlook yang diupload
                $outlookAmount = $totalDataOutlookArray[$accountId] ?? 0;

                // Gunakan data dari $totalDataProposalArray untuk proposal
                $proposal = $totalDataProposalArray[$accountId] ?? 0;

                // Apply filters if any
                if ($submission_type == 'asset' && $accountId != 'CAPEX') { // Perbaiki filter untuk asset
                    continue;
                }
                if ($submission_type == 'expenditure' && $accountId == 'CAPEX') { // Perbaiki filter untuk expenditure
                    continue;
                }
                if ($acc_id && $accountId != $acc_id) {
                    continue;
                }

                // Use account name from accounts table, fallback to acc_id if not found
                $accountName = $accountNames[$accountId] ?? $accountId;

                $accountData[] = (object)[
                    'account' => $accountName,
                    'acc_id' => $accountId,
                    'total_previous_year' => $lastYearAmount,
                    'total_current_year_given' => $outlookAmount,
                    'total_current_year_requested' => $proposal,
                    'variance_last_year' => $proposal - $lastYearAmount,
                    'variance_budget_given' => $proposal - $outlookAmount,
                    'percentage_change_last_year' => $lastYearAmount != 0
                        ? (($proposal - $lastYearAmount) / $lastYearAmount * 100)
                        : ($proposal > 0 ? 100 : 0),
                    'percentage_change_outlook' => $outlookAmount != 0
                        ? (($proposal - $outlookAmount) / $outlookAmount * 100)
                        : ($proposal > 0 ? 100 : 0)
                ];
            }

            // Sort account data alphabetically by account name
            usort($accountData, function ($a, $b) {
                return strcmp($a->account, $b->account);
            });

            // Calculate totals
            $accountTotal = (object)[
                'account' => 'Total',
                'total_previous_year' => $grandTotalLastYear, // Gunakan grand total langsung untuk konsistensi
                'total_current_year_given' => $grandTotalOutlook,
                'total_current_year_requested' => $grandTotalProposal,
                'variance_last_year' => $varianceGrandTotalLastYear,
                'variance_budget_given' => $varianceGrandTotalOutlook,
                'percentage_change_last_year' => $varianceGrandTotalLastYearPercentage,
                'percentage_change_outlook' => $varianceGrandTotalOutlookPercentage
            ];
        } elseif ($sect == 'Kadiv' && in_array($npk, array_column($divisions, 'gm'))) {
            // Menampilkan Department Submission Totals
            $accountData = [];
            $uploadedData = [
                'last_year' => [],
                'outlook' => []
            ];

            foreach ($departments as $department) {
                $dpt_id = $department['dpt_id'];

                // [MODIFIKASI] Ambil data Last Year (periode 2025)
                $lastYearData = BudgetFyLo::where('periode', $year)
                    ->where('tipe', 'last_year')
                    ->where('dept', $dpt_id)
                    ->selectRaw('SUM(total) as total')
                    ->first()->total ?? 0;

                // [MODIFIKASI] Ambil data Figure Outlook (periode 2026)
                $outlookData = BudgetFyLo::where('periode', $year + 1)
                    ->where('tipe', 'outlook')
                    ->where('dept', $dpt_id)
                    ->selectRaw('SUM(total) as total')
                    ->first()->total ?? 0;

                // [MODIFIKASI] Menghitung total budget proposal dari BudgetPlan per departemen untuk tahun 2026
                $proposal = BudgetPlan::where('dpt_id', $dpt_id)
                    ->where('dpt_id', $dpt_id)
                    ->whereYear('created_at', $year)
                    ->selectRaw('SUM(CASE WHEN acc_id = "CAPEX" THEN month_value ELSE price END) as total_proposal')
                    ->first()
                    ->total_proposal ?? 0;

                // [MODIFIKASI BARU] Hitung jumlah pengajuan (status = 3) untuk departemen ini
                $countSubmissions = BudgetPlan::where(function ($query) {
                    $query->where('status', 3)  // Status pending Kadiv
                        ->orWhere('status', 10); // Status rejected by DIC
                })
                    ->where('dpt_id', $dpt_id)
                    ->whereYear('created_at', $year)
                    ->distinct('sub_id')
                    ->count('sub_id');

                // Apply filters if any
                if ($submission_type == 'asset' && $acc_id == 'CAPEX') {
                    continue;
                }
                if ($submission_type == 'expenditure' && $acc_id != 'CAPEX') {
                    continue;
                }

                $accountData[] = (object)[
                    'department' => $department['department'],
                    'dpt_id' => $dpt_id,
                    'total_previous_year' => $lastYearData,
                    'total_current_year_given' => $outlookData,
                    'total_current_year_requested' => $proposal,
                    'variance_last_year' => $proposal - $lastYearData,
                    'variance_budget_given' => $proposal - $outlookData,
                    'percentage_change_last_year' => $lastYearData != 0
                        ? (($proposal - $lastYearData) / $lastYearData * 100)
                        : ($proposal > 0 ? 100 : 0),
                    'percentage_change_outlook' => $outlookData != 0
                        ? (($proposal - $outlookData) / $outlookData * 100)
                        : ($proposal > 0 ? 100 : 0),
                    'count_submissions' => $countSubmissions
                ];
            }

            // Calculate totals
            $accountTotal = (object)[
                'department' => 'Total',
                'total_previous_year' => array_sum(array_column($accountData, 'total_previous_year')),
                'total_current_year_given' => array_sum(array_column($accountData, 'total_current_year_given')),
                'total_current_year_requested' => array_sum(array_column($accountData, 'total_current_year_requested')),
                'variance_last_year' => array_sum(array_column($accountData, 'variance_last_year')),
                'variance_budget_given' => array_sum(array_column($accountData, 'variance_budget_given')),
                'percentage_change_last_year' => array_sum(array_column($accountData, 'total_previous_year'))
                    ? (array_sum(array_column($accountData, 'variance_last_year')) / array_sum(array_column($accountData, 'total_previous_year')) * 100)
                    : 0,
                'percentage_change_outlook' => array_sum(array_column($accountData, 'total_current_year_given'))
                    ? (array_sum(array_column($accountData, 'variance_budget_given')) / array_sum(array_column($accountData, 'total_current_year_given')) * 100)
                    : 0
            ];
        } elseif ($sect == 'DIC' && $this->isUserDIC($npk, $divisions) && $div_id && !$dept_id) {
            // [MODIFIKASI BARU] Menampilkan Department Submission Totals untuk divisi yang dipilih
            $accountData = [];
            $uploadedData = [
                'last_year' => [],
                'outlook' => []
            ];

            foreach ($departments as $department) {
                $dpt_id = $department['dpt_id'];

                // Ambil data Last Year (periode 2025)
                $lastYearData = BudgetFyLo::where('periode', $year)
                    ->where('tipe', 'last_year')
                    ->where('dept', $dpt_id)
                    ->selectRaw('SUM(total) as total')
                    ->first()->total ?? 0;

                // Ambil data Figure Outlook (periode 2026)
                $outlookData = BudgetFyLo::where('periode', $year + 1)
                    ->where('tipe', 'outlook')
                    ->where('dept', $dpt_id)
                    ->selectRaw('SUM(total) as total')
                    ->first()->total ?? 0;

                // Menghitung total budget proposal dari BudgetPlan per departemen untuk tahun 2026
                $proposal = BudgetPlan::where('dpt_id', $dpt_id)
                    ->whereYear('created_at', $year)
                    ->selectRaw('SUM(CASE WHEN acc_id = "CAPEX" THEN month_value ELSE price END) as total_proposal')
                    ->first()
                    ->total_proposal ?? 0;

                // Hitung jumlah pengajuan (status = 4) untuk departemen ini (pending DIC approval)
                $countSubmissions = BudgetPlan::where('status', 4)
                    ->where('dpt_id', $dpt_id)
                    ->whereYear('created_at', $year)
                    ->distinct('sub_id')
                    ->count('sub_id');

                // Apply filters if any
                if ($submission_type == 'asset' && $acc_id == 'CAPEX') {
                    continue;
                }
                if ($submission_type == 'expenditure' && $acc_id != 'CAPEX') {
                    continue;
                }

                $accountData[] = (object)[
                    'department' => $department['department'],
                    'dpt_id' => $dpt_id,
                    'total_previous_year' => $lastYearData,
                    'total_current_year_given' => $outlookData,
                    'total_current_year_requested' => $proposal,
                    'variance_last_year' => $proposal - $lastYearData,
                    'variance_budget_given' => $proposal - $outlookData,
                    'percentage_change_last_year' => $lastYearData != 0
                        ? (($proposal - $lastYearData) / $lastYearData * 100)
                        : ($proposal > 0 ? 100 : 0),
                    'percentage_change_outlook' => $outlookData != 0
                        ? (($proposal - $outlookData) / $outlookData * 100)
                        : ($proposal > 0 ? 100 : 0),
                    'count_submissions' => $countSubmissions
                ];
            }

            // Calculate totals
            $accountTotal = (object)[
                'department' => 'Total',
                'total_previous_year' => array_sum(array_column($accountData, 'total_previous_year')),
                'total_current_year_given' => array_sum(array_column($accountData, 'total_current_year_given')),
                'total_current_year_requested' => array_sum(array_column($accountData, 'total_current_year_requested')),
                'variance_last_year' => array_sum(array_column($accountData, 'variance_last_year')),
                'variance_budget_given' => array_sum(array_column($accountData, 'variance_budget_given')),
                'percentage_change_last_year' => array_sum(array_column($accountData, 'total_previous_year'))
                    ? (array_sum(array_column($accountData, 'variance_last_year')) / array_sum(array_column($accountData, 'total_previous_year')) * 100)
                    : 0,
                'percentage_change_outlook' => array_sum(array_column($accountData, 'total_current_year_given'))
                    ? (array_sum(array_column($accountData, 'variance_budget_given')) / array_sum(array_column($accountData, 'total_current_year_given')) * 100)
                    : 0
            ];
        } else {
            if ($sect == 'DIC') {
                $dept = $dept_id;
            }
            // Logika asli untuk non-Kadiv
            $uploadedData = [
                'last_year' => [],
                'outlook' => []
            ];

            // if ($dept == '4131') {
            //     $targetDepts = ['1111', '1131', '1151', '1211', '1231', '7111'];
            // } else {
            //     $targetDepts = [$dept];
            // }

            // Tentukan target departemen untuk dept 4131
            if ($dept == '4131') {
                $targetDepts = ['4131', '1111', '1131', '1151', '1211', '1231', '7111'];
            } elseif ($dept == '4111') {
                if ($sect == 'Kadept') {
                    $targetDepts = ['4111', '1116', '1140', '1160', '1224', '1242', '7111', '4311'];
                } else {
                    $targetDepts = ['4111', '1116', '1140', '1160', '1224', '1242', '7111'];
                }
            } elseif ($dept == '4111') {
                $targetDepts = ['4111', '1116', '1140', '1160', '1224', '1242', '7111'];
            }
            // [TAMBAHIN INI] Untuk dept 1332, include juga 1333
            elseif ($sect == 'Kadept' && $dept == '1332') {
                $targetDepts = ['1331', '1332', '1333'];
            } elseif ($dept == '1332') {
                $targetDepts = ['1332', '1333'];
            } else {
                $targetDepts = [$dept];
            }


            //fan, 31082025
            //CHANGING POINT
            //get all data account from table account without see dept_id, ordered by account name ascending
            // $listAccount dan $listAccountNames sudah diinisialisasi di awal

            //LAST YEAR FROM UPLOAD
            //get total from table budget_fy_lo base on acc_id without using dept_id
            $totalDataLastYear = BudgetFyLo::where('periode', $year)
                ->where('tipe', 'last_year')
                ->whereIn('account', $listAccount)
                ->whereIn('dept', $targetDepts)
                ->select('account', DB::raw('SUM(total) as total'))
                ->groupBy('account')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [
                        $item->account => (float) $item->total
                    ];
                });
            $totalDataLastYearArray = $totalDataLastYear->toArray();
            $grandTotalLastYear = $totalDataLastYear->sum();

            //FINANCIAL OUTLOOK FROM UPLOAD
            $totalDataOutlook = BudgetFyLo::where('periode', $year + 1)
                ->where('tipe', 'outlook')
                ->whereIn('account', $listAccount)
                ->whereIn('dept', $targetDepts)
                ->selectRaw('account, SUM(total) as total_sum')
                ->groupBy('account')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [
                        $item->account => (float) $item->total_sum
                    ];
                });
            $totalDataOutlookArray = $totalDataOutlook->toArray();
            $grandTotalOutlook = $totalDataOutlook->sum();

            // BUDGET PROPOSAL
            $totalDataProposal = BudgetPlan::whereIn('acc_id', $listAccount)
                ->whereIn('dpt_id', $targetDepts)
                ->whereYear('created_at', $year)
                ->selectRaw('acc_id, SUM(CASE WHEN acc_id = "CAPEX" THEN month_value ELSE price END) as total_proposal')
                ->groupBy('acc_id')
                ->pluck('total_proposal', 'acc_id')
                ->map(fn($total) => (float) $total);

            $totalDataProposalArray = $totalDataProposal->toArray();
            $grandTotalProposal = $totalDataProposal->sum();

            // Variance By Account (Proposal vs Last Year)
            $varianceByAccount = [];

            foreach ($listAccount as $accId) {
                $proposal = $totalDataProposalArray[$accId] ?? 0;
                $lastYear = $totalDataLastYearArray[$accId] ?? 0;
                $variance = $proposal - $lastYear;
                $variancePercent = $lastYear != 0 ? ($variance / $lastYear) * 100 : 0;

                $varianceByAccount[$accId] = [
                    'varianceLastYear' => $variance,
                    'varianceLastYearPercent' => $variancePercent,
                ];
            }

            // Variance Grand Total (Proposal vs Last Year)
            $varianceGrandTotalLastYear = $grandTotalProposal - $grandTotalLastYear;
            $varianceGrandTotalLastYearPercentage = $grandTotalLastYear != 0
                ? ($varianceGrandTotalLastYear / $grandTotalLastYear) * 100
                : 0;

            // Variance By Account (Proposal vs outlook)
            $varianceByAccountOutlook = [];

            foreach ($listAccount as $accId) {
                $proposal = $totalDataProposalArray[$accId] ?? 0;
                $outlook = $totalDataOutlookArray[$accId] ?? 0;
                $variance = $proposal - $outlook;
                $variancePercent = $outlook != 0 ? ($variance / $outlook) * 100 : 0;

                $varianceByAccountOutlook[$accId] = [
                    'varianceOutlook' => $variance,
                    'varianceOutlookPercent' => $variancePercent,
                ];
            }
            // Variance Grand Total (Proposal vs outlook)
            $varianceGrandTotalOutlook = $grandTotalProposal - $grandTotalOutlook;
            $varianceGrandTotalOutlookPercentage = $grandTotalOutlook != 0
                ? ($varianceGrandTotalOutlook / $grandTotalOutlook) * 100
                : 0;

            // END CHANGING POINT

            // [MODIFIKASI] Ambil data Last Year (periode 2025)
            $lastYearData = BudgetFyLo::where('periode', $year)
                ->where('tipe', 'last_year')
                ->where('dept', $dept)
                ->select('account', 'total')
                ->get()
                ->map(function ($item) {
                    return [
                        'account' => $item->account,
                        'amount' => (float) $item->total
                    ];
                })->toArray();

            // [MODIFIKASI] Ambil data Figure Outlook (periode 2026)
            $outlookData = BudgetFyLo::where('periode', $year + 1)
                ->where('tipe', 'outlook')
                ->where('dept', $dept)
                ->select('account', 'total')
                ->get()
                ->map(function ($item) {
                    return [
                        'account' => $item->account,
                        'amount' => (float) $item->total
                    ];
                })->toArray();

            $uploadedData['last_year'] = $lastYearData;
            $uploadedData['outlook'] = $outlookData;

            // [MODIFIKASI] Menghitung total budget proposal dari BudgetPlan per acc_id untuk tahun 2026
            $budgetProposalByAccount = $totalDataProposal->toArray(); // Gunakan $totalDataProposal langsung untuk konsistensi

            // Get unique accounts from accounts table
            $allAccounts = Account::pluck('acc_id')->toArray();
            $accountNames = Account::pluck('account', 'acc_id')->toArray();

            // Merge with accounts from uploaded data
            $uploadedAccounts = array_unique(array_merge(
                array_column($uploadedData['last_year'], 'account'),
                array_column($uploadedData['outlook'], 'account')
            ));
            $allAccounts = array_unique(array_merge($allAccounts, $uploadedAccounts));

            // Prepare account data for view
            $accountData = [];
            foreach ($allAccounts as $accountId) {
                // Cari data Last Year yang diupload
                $lastYearAmount = $totalDataLastYearArray[$accountId] ?? 0;

                // Cari data Outlook yang diupload
                $outlookAmount = $totalDataOutlookArray[$accountId] ?? 0;

                // Gunakan data dari $totalDataProposalArray untuk proposal
                $proposal = $totalDataProposalArray[$accountId] ?? 0;

                // Apply filters if any
                if ($submission_type == 'asset' && $accountId != 'CAPEX') { // Perbaiki filter untuk asset
                    continue;
                }
                if ($submission_type == 'expenditure' && $accountId == 'CAPEX') { // Perbaiki filter untuk expenditure
                    continue;
                }
                if ($acc_id && $accountId != $acc_id) {
                    continue;
                }

                // Use account name from accounts table, fallback to acc_id if not found
                $accountName = $accountNames[$accountId] ?? $accountId;

                $accountData[] = (object)[
                    'account' => $accountName,
                    'acc_id' => $accountId,
                    'total_previous_year' => $lastYearAmount,
                    'total_current_year_given' => $outlookAmount,
                    'total_current_year_requested' => $proposal,
                    'variance_last_year' => $proposal - $lastYearAmount,
                    'variance_budget_given' => $proposal - $outlookAmount,
                    'percentage_change_last_year' => $lastYearAmount != 0
                        ? (($proposal - $lastYearAmount) / $lastYearAmount * 100)
                        : ($proposal > 0 ? 100 : 0),
                    'percentage_change_outlook' => $outlookAmount != 0
                        ? (($proposal - $outlookAmount) / $outlookAmount * 100)
                        : ($proposal > 0 ? 100 : 0)
                ];
            }

            // Sort account data alphabetically by account name
            usort($accountData, function ($a, $b) {
                return strcmp($a->account, $b->account);
            });

            // Calculate totals
            $accountTotal = (object)[
                'account' => 'Total',
                'total_previous_year' => $grandTotalLastYear, // Gunakan grand total langsung untuk konsistensi
                'total_current_year_given' => $grandTotalOutlook,
                'total_current_year_requested' => $grandTotalProposal,
                'variance_last_year' => $varianceGrandTotalLastYear,
                'variance_budget_given' => $varianceGrandTotalOutlook,
                'percentage_change_last_year' => $varianceGrandTotalLastYearPercentage,
                'percentage_change_outlook' => $varianceGrandTotalOutlookPercentage
            ];
        }

        // Get accounts for filter dropdown
        $accounts = Account::select('acc_id', 'account')->get();

        // [MODIFIKASI] Logging tambahan untuk debugging
        // Log::info('indexAll lastYearData: ', $uploadedData['last_year']);
        // Log::info('indexAll outlookData: ', $uploadedData['outlook']);
        // // Log::info('indexAll budgetProposalByAccount: ', $budgetProposalByAccount);
        // Log::info('indexAll accountTotal: ', (array) $accountTotal);

        //fan, 31082025
        //CHANGING POINT

        // Return view dengan variabel yang diperlukan
        return view('index-all', compact(
            'needsApprovalCount',
            'approvedThisYearCount',
            'notapprovedThisYearCount',
            'totalBudgetFormatted',
            'notifications',
            'years',
            'budgetValues',
            'submission_type',
            'acc_id',
            'year',
            'accounts',
            'accountData',
            'accountTotal',
            'uploadedData',
            'departments',
            'sect',
            'dept_id', // Tambahkan dept_id untuk logika di view
            'div_id', // [MODIFIKASI BARU] Tambahkan div_id untuk logika di view
            'totalDataLastYear',
            'listAccount',
            'listAccountNames',
            'totalDataLastYearArray',
            'grandTotalLastYear',
            'totalDataOutlookArray',
            'grandTotalOutlook',
            'totalDataProposalArray',
            'grandTotalProposal',
            'varianceGrandTotalLastYear',
            'varianceGrandTotalLastYearPercentage',
            'varianceByAccount', // ✅ ini yg dipakai di view
            'varianceGrandTotalOutlook',
            'varianceGrandTotalOutlookPercentage',
            'varianceByAccountOutlook'
        ));

        //END
    }

    public function indexAccounts(Request $request)
    {
        $dpt_id = $request->input('dpt_id');
        $year = $request->input('year', date('Y'));
        $submission_type = $request->input('submission_type', '');
        $notificationController = new NotificationController();
        $notifications = $notificationController->getNotifications();

        // Validasi dpt_id
        if (!$dpt_id) {
            return redirect()->route('index')->with('error', 'Departemen tidak valid.');
        }

        // Ambil data departemen
        $department = Departments::where('dpt_id', $dpt_id)->first();
        if (!$department) {
            return redirect()->route('index')->with('error', 'Departemen tidak ditemukan.');
        }

        // Ambil daftar tahun untuk dropdown
        $years = BudgetPlan::select(DB::raw('DISTINCT YEAR(created_at) as year'))
            ->orderBy('year', 'desc')
            ->pluck('year');

        // [PERBAIKAN] Ambil semua account untuk konsistensi dengan indexAll
        $listAccount = Account::orderBy('account', 'asc')->pluck('acc_id')->toArray();
        $accountNames = Account::orderBy('account', 'asc')->pluck('account', 'acc_id')->toArray();

        // [MODIFIKASI] Ambil data Last Year untuk periode $year
        $lastYearData = BudgetFyLo::where('periode', $year)
            ->where('tipe', 'last_year')
            ->where('dept', $dpt_id)
            ->select('account', 'total')
            ->get()
            ->map(function ($item) {
                return [
                    'account' => $item->account,
                    'amount' => (float) ($item->total ?? 0)
                ];
            })->toArray();

        // [MODIFIKASI] Ambil data Figure Outlook untuk periode $year + 1
        $outlookData = BudgetFyLo::where('periode', $year + 1)
            ->where('tipe', 'outlook')
            ->where('dept', $dpt_id)
            ->select('account', 'total')
            ->get()
            ->map(function ($item) {
                return [
                    'account' => $item->account,
                    'amount' => (float) ($item->total ?? 0)
                ];
            })->toArray();

        // [PERBAIKAN] Gunakan query yang sama dengan indexAll
        $totalDataProposal = BudgetPlan::where('dpt_id', $dpt_id)
            ->whereIn('acc_id', $listAccount)
            ->where('dpt_id', $dpt_id)
            ->whereYear('created_at', $year)
            ->selectRaw('acc_id, SUM(price) as total_proposal')
            ->groupBy('acc_id')
            ->pluck('total_proposal', 'acc_id')
            ->map(fn($total) => (float) $total);

        $totalDataProposalArray = $totalDataProposal->toArray();
        $grandTotalProposal = $totalDataProposal->sum();

        // [PERBAIKAN] Hitung data Last Year dengan cara yang sama dengan indexAll
        $totalDataLastYear = BudgetFyLo::where('periode', $year)
            ->where('tipe', 'last_year')
            ->whereIn('account', $listAccount)
            ->where('dept', $dpt_id)
            ->select('account', DB::raw('SUM(total) as total'))
            ->groupBy('account')
            ->get()
            ->mapWithKeys(function ($item) {
                return [
                    $item->account => (float) $item->total
                ];
            });
        $totalDataLastYearArray = $totalDataLastYear->toArray();
        $grandTotalLastYear = $totalDataLastYear->sum();

        // [PERBAIKAN] Hitung data Outlook dengan cara yang sama dengan indexAll
        $totalDataOutlook = BudgetFyLo::where('periode', $year + 1)
            ->where('tipe', 'outlook')
            ->whereIn('account', $listAccount)
            ->where('dept', $dpt_id)
            ->selectRaw('account, SUM(total) as total_sum')
            ->groupBy('account')
            ->get()
            ->mapWithKeys(function ($item) {
                return [
                    $item->account => (float) $item->total_sum
                ];
            });
        $totalDataOutlookArray = $totalDataOutlook->toArray();
        $grandTotalOutlook = $totalDataOutlook->sum();

        // Siapkan data untuk tabel
        $accountData = [];
        foreach ($listAccount as $accountId) {
            $lastYearAmount = $totalDataLastYearArray[$accountId] ?? 0;
            $outlookAmount = $totalDataOutlookArray[$accountId] ?? 0;
            $proposal = $totalDataProposalArray[$accountId] ?? 0;

            // Terapkan filter submission_type
            if ($submission_type == 'asset' && $accountId != 'CAPEX') {
                continue;
            }
            if ($submission_type == 'expenditure' && $accountId == 'CAPEX') {
                continue;
            }

            // Gunakan nama akun dari tabel accounts
            $accountName = $accountNames[$accountId] ?? $accountId;

            $accountData[] = (object)[
                'account' => $accountName,
                'acc_id' => $accountId,
                'total_previous_year' => $lastYearAmount,
                'total_current_year_given' => $outlookAmount,
                'total_current_year_requested' => $proposal,
                'variance_last_year' => $proposal - $lastYearAmount,
                'variance_budget_given' => $proposal - $outlookAmount,
                'percentage_change_last_year' => $lastYearAmount != 0
                    ? (($proposal - $lastYearAmount) / $lastYearAmount * 100)
                    : ($proposal > 0 ? 100 : 0),
                'percentage_change_outlook' => $outlookAmount != 0
                    ? (($proposal - $outlookAmount) / $outlookAmount * 100)
                    : ($proposal > 0 ? 100 : 0)
            ];
        }

        // Urutkan data berdasarkan nama akun
        usort($accountData, function ($a, $b) {
            return strcmp($a->account, $b->account);
        });

        // Hitung total
        $accountTotal = (object)[
            'account' => 'TOTAL',
            'total_previous_year' => $grandTotalLastYear,
            'total_current_year_given' => $grandTotalOutlook,
            'total_current_year_requested' => $grandTotalProposal,
            'variance_last_year' => $grandTotalProposal - $grandTotalLastYear,
            'variance_budget_given' => $grandTotalProposal - $grandTotalOutlook,
            'percentage_change_last_year' => $grandTotalLastYear != 0
                ? (($grandTotalProposal - $grandTotalLastYear) / $grandTotalLastYear * 100)
                : 0,
            'percentage_change_outlook' => $grandTotalOutlook != 0
                ? (($grandTotalProposal - $grandTotalOutlook) / $grandTotalOutlook * 100)
                : 0
        ];

        return view('index-accounts', compact(
            'accountData',
            'accountTotal',
            'department',
            'dpt_id',
            'year',
            'submission_type',
            'notifications',
            'years'
        ));
    }

    public function reportByDepartmentAndYear($dpt_id, $year, Request $request)
    {
        $notificationController = new NotificationController();
        $notifications = $notificationController->getNotifications();

        // Fetch all accounts
        $accounts = Account::all();

        // Get filter parameters from the request
        $workcenterFilter = $request->input('workcenter', '');
        $accountFilter = $request->input('account', '');
        $budgetFilter = $request->input('budget_name', '');

        // Determine department filter
        $deptId = $dpt_id === 'all' ? null : $dpt_id;

        // Fetch workcenters for the dropdown
        $workcentersQuery = collect();
        $models = [
            BudgetPlan::class,
            // Tambahkan model lain jika diperlukan: SupportMaterial::class, InsurancePrem::class, dll.
        ];

        foreach ($models as $model) {
            $query = $model::where('status', 7);
            if ($deptId) {
                $query->where('dpt_id', $deptId);
            }
            $workcentersQuery = $workcentersQuery->merge($query->pluck('wct_id'));
        }

        $workcenters = Workcenter::whereIn('wct_id', $workcentersQuery->unique()->values())->get();

        // Fetch years for the dropdown
        $yearsQuery = collect();
        foreach ($models as $model) {
            $query = $model::where('status', 7);
            if ($deptId) {
                $query->where('dpt_id', $deptId);
            }
            $yearsQuery = $yearsQuery->merge($query->pluck('updated_at')->map(fn($date) => $date->year));
        }

        // Tambahkan tahun yang dipilih ke daftar years jika belum ada
        $years = $yearsQuery->unique()->sort()->values();
        if (!$years->contains($year)) {
            $years->push($year);
            $years = $years->unique()->sort()->values();
        }

        // Fetch accounts for the dropdown
        $filteredAccountsQuery = collect();
        foreach ($models as $model) {
            $query = $model::where('status', 7);
            if ($deptId) {
                $query->where('dpt_id', $deptId);
            }
            $filteredAccountsQuery = $filteredAccountsQuery->merge($query->pluck('acc_id'));
        }

        $filteredAccounts = Account::whereIn('acc_id', $filteredAccountsQuery->unique()->values())->get();

        // Fetch budgets for the dropdown
        $budgetsQuery = collect();
        foreach ($models as $model) {
            $query = $model::where('status', 7);
            if ($deptId) {
                $query->where('dpt_id', $deptId);
            }
            $budgetsQuery = $budgetsQuery->merge($query->pluck('bdc_id'));
        }

        $budgets = BudgetCode::whereIn('bdc_id', $budgetsQuery->unique()->values())->get();

        // Base query for all data
        $query = ['status' => 7];
        if ($deptId) {
            $query['dpt_id'] = $deptId;
        }
        if ($accountFilter) {
            $query['acc_id'] = $accountFilter;
        }

        // Fetch data based on filters
        $allData = collect();
        foreach ($models as $model) {
            $modelQuery = $model::where($query)->whereYear('updated_at', $year);
            $allData = $allData->merge($modelQuery->get());
        }

        // Process calculations for each account
        $reports = [];
        foreach ($accounts as $account) {
            $items = $allData->where('acc_id', $account->acc_id);

            // Initialize monthly totals
            $monthlyTotals = [
                'JAN' => 0,
                'FEB' => 0,
                'MAR' => 0,
                'APR' => 0,
                'MAY' => 0,
                'JUN' => 0,
                'JUL' => 0,
                'AUG' => 0,
                'SEP' => 0,
                'OCT' => 0,
                'NOV' => 0,
                'DEC' => 0,
            ];
            $total = 0;

            foreach ($items as $item) {
                $amount = isset($item->quantity) && isset($item->price) ? $item->quantity * $item->price : $item->amount;
                $month = strtoupper(substr($item->month, 0, 3));
                if (array_key_exists($month, $monthlyTotals)) {
                    $monthlyTotals[$month] += $amount;
                    $total += $amount;
                }
            }

            // Selalu sertakan semua account, bahkan jika totalnya 0
            $reports[] = (object)[
                'acc_id' => $account->acc_id,
                'account' => $account->account,
                'monthly_totals' => $monthlyTotals,
                'total' => $total
            ];
        }

        // Get department name
        $department = $dpt_id === 'all' ? (object)['department' => 'All Departments', 'dpt_id' => 'all']
            : Departments::where('dpt_id', $dpt_id)->firstOrFail();

        // Create a chart for monthly totals across all accounts
        $monthlyChartData = [
            'JAN' => 0,
            'FEB' => 0,
            'MAR' => 0,
            'APR' => 0,
            'MAY' => 0,
            'JUN' => 0,
            'JUL' => 0,
            'AUG' => 0,
            'SEP' => 0,
            'OCT' => 0,
            'NOV' => 0,
            'DEC' => 0,
        ];

        foreach ($reports as $report) {
            foreach ($report->monthly_totals as $month => $amount) {
                $monthlyChartData[$month] += $amount;
            }
        }

        $chart = [
            'type' => 'line',
            'data' => [
                'labels' => array_keys($monthlyChartData),
                'datasets' => [[
                    'label' => "Total Submissions ($year)",
                    'data' => array_values($monthlyChartData),
                    'backgroundColor' => 'rgba(255, 99, 132, 0.6)',
                    'borderColor' => 'rgba(255, 99, 132, 1)',
                    'borderWidth' => 1
                ]]
            ],
            'options' => [
                'responsive' => true,
                'scales' => [
                    'x' => ['title' => ['display' => true, 'text' => 'Month']],
                    'y' => ['beginAtZero' => true, 'title' => ['display' => true, 'text' => 'Total (IDR)']]
                ],
                'plugins' => ['legend' => ['display' => true, 'position' => 'top']]
            ]
        ];

        return view('reports.sum-acc', [
            'reports' => $reports,
            'department' => $department,
            'workcenters' => $workcenters,
            'years' => $years,
            'budgets' => $budgets,
            'accounts' => $filteredAccounts,
            'selectedWorkcenter' => $workcenterFilter,
            'selectedYear' => $year,
            'selectedAccount' => $accountFilter,
            'selectedBudget' => $budgetFilter,
            'notifications' => $notifications,
            'monthlyChart' => $chart
        ]);
    }

    public function reportByAccount(Request $request, $acc_id, $dpt_id, $year = null)
    {
        $notificationController = new NotificationController();
        $notifications = $notificationController->getNotifications();

        // Get account data
        $account = Account::where('acc_id', $acc_id)->firstOrFail();

        // Get filter parameters
        $yearFilter = $request->input('year', $year ?? now()->year);
        $workcenterFilter = $request->input('workcenter', '');
        $budgetFilter = $request->input('budget_name', '');
        $monthFilter = $request->input('month', '');

        // Month mapping from short to full name
        $monthMap = [
            'JAN' => 'January',
            'FEB' => 'February',
            'MAR' => 'March',
            'APR' => 'April',
            'MAY' => 'May',
            'JUN' => 'June',
            'JUL' => 'July',
            'AUG' => 'August',
            'SEP' => 'September',
            'OCT' => 'October',
            'NOV' => 'November',
            'DEC' => 'December'
        ];

        // Base query
        $query = ['acc_id' => $acc_id, 'status' => 7];
        if ($dpt_id !== 'all') {
            $query['dpt_id'] = $dpt_id;
        }
        if ($workcenterFilter) {
            $query['wct_id'] = $workcenterFilter;
        }
        if ($budgetFilter) {
            $query['bdc_id'] = $budgetFilter;
        }
        if ($monthFilter) {
            $query['month'] = $monthMap[$monthFilter] ?? $monthFilter;
        }

        // Fetch data based on acc_id
        $reports = collect();
        $models = [
            'general' => ['model' => BudgetPlan::class, 'acc_ids' => ['SGABOOK', 'SGAREPAIR', 'SGAMARKT', 'FOHTECHDO', 'FOHRECRUITING', 'SGARECRUITING', 'SGARENT', 'SGAADVERT', 'SGACOM', 'SGAOFFICESUP', 'SGAASOCIATION', 'SGABCHARGES', 'SGACONTRIBUTION', 'FOHPACKING', 'SGARYLT', 'FOHAUTOMOBILE', 'FOHPROF', 'FOHRENT', 'FOHTAXPUB', 'SGAAUTOMOBILE', 'SGAPROF', 'SGATAXPUB']],
            'support' => ['model' => BudgetPlan::class, 'acc_ids' => ['FOHTOOLS', 'FOHFS', 'FOHINDMAT', 'FOHREPAIR', 'SGADEPRECIATION']],
            'represent' => ['model' => BudgetPlan::class, 'acc_ids' => ['FOHENTERTAINT', 'FOHREPRESENTATION', 'SGAENTERTAINT', 'SGAREPRESENTATION']],
            'insurance' => ['model' => BudgetPlan::class, 'acc_ids' => ['FOHINSPREM', 'SGAINSURANCE']],
            'utilities' => ['model' => BudgetPlan::class, 'acc_ids' => ['FOHPOWER', 'SGAPOWER']],
            'business' => ['model' => BudgetPlan::class, 'acc_ids' => ['FOHTRAV', 'SGATRAV']],
            'training' => ['model' => BudgetPlan::class, 'acc_ids' => ['FOHTRAINING', 'SGATRAINING']],
            'aftersales' => ['model' => BudgetPlan::class, 'acc_ids' => ['SGAAFTERSALES']],
        ];

        $reportType = 'general';
        foreach ($models as $type => $config) {
            if (in_array($acc_id, $config['acc_ids'])) {
                $reportType = $type;
                $model = $config['model'];
                $reports = $model::with(['item', 'dept', 'workcenter', 'budget'])
                    ->where($query)
                    ->when($yearFilter, fn($q) => $q->whereYear('updated_at', $yearFilter))
                    ->get();
                break;
            }
        }

        // Fetch years
        $years = collect();
        foreach ($models as $config) {
            $model = $config['model'];
            $years = $years->merge(
                $model::where('acc_id', $acc_id)->where('status', 7)->pluck('updated_at')->map(fn($date) => $date->year)
            );
        }
        // Tambahkan tahun yang dipilih ke daftar years jika belum ada
        $years = $years->unique()->sort()->values();
        if (!$years->contains($yearFilter)) {
            $years->push($yearFilter);
            $years = $years->unique()->sort()->values();
        }

        // Fetch workcenters
        $workcentersQuery = collect();
        foreach ($models as $config) {
            $model = $config['model'];
            $workcentersQuery = $workcentersQuery->merge(
                $model::where('acc_id', $acc_id)->where('status', 7)->pluck('wct_id')
            );
        }
        $workcenters = Workcenter::whereIn('wct_id', $workcentersQuery->unique()->values())->get();

        // Fetch budget codes
        $budgetCodesQuery = collect();
        foreach ($models as $config) {
            $model = $config['model'];
            $budgetCodesQuery = $budgetCodesQuery->merge(
                $model::where('acc_id', $acc_id)->where('status', 7)->pluck('bdc_id')
            );
        }
        $budgetCodes = BudgetCode::whereIn('bdc_id', $budgetCodesQuery->unique()->values())->get();

        // Get department
        $department = $dpt_id === 'all' ? (object)['department' => 'All Departments', 'dpt_id' => 'all']
            : Departments::where('dpt_id', $dpt_id)->firstOrFail();

        return view('reports.report-acc', [
            'reports' => $reports,
            'acc_id' => $acc_id,
            'account_name' => $account->account,
            'report_type' => $reportType,
            'years' => $years,
            'workcenters' => $workcenters,
            'budgetCodes' => $budgetCodes,
            'selectedYear' => $yearFilter,
            'selectedWorkcenter' => $workcenterFilter,
            'selectedBudget' => $budgetFilter,
            'selectedMonth' => $monthFilter,
            'notifications' => $notifications,
            'department' => $department,
            'dpt_id' => $dpt_id
        ]);
    }

    public function reportByWorkcenter($wct_id, $year, Request $request)
    {
        $user = Auth::user();
        $dept = $user->dept;
        $currentYear = date('Y');
        $submission_type = $request->query('submission_type', '');
        $acc_id = $request->query('acc_id', '');

        $notificationController = new NotificationController();
        $notifications = $notificationController->getNotifications();

        $models = [BudgetPlan::class];

        $statusMap = [
            'Kadept' => 2,
            'Kadiv' => 3,
            'DIC' => 4,
            'PIC P&B' => 6,
            'Kadept P&B' => 7,
        ];

        $approvedStatusMap = [
            'Kadept' => 3,
            'Kadiv' => 4,
            'DIC' => 5,
            'PIC P&B' => 6,
            'Kadept P&B' => 7,
        ];

        $notapprovedStatusMap = [
            'Kadept' => 8,
            'Kadiv' => 9,
            'DIC' => 10,
            'PIC P&B' => 11,
            'Kadept PBD' => 12,
        ];

        $sect = $user->sect;
        $status = $statusMap[$sect] ?? 0;
        $approvedStatus = $approvedStatusMap[$sect] ?? null;
        $notapprovedStatus = $notapprovedStatusMap[$sect] ?? null;

        $needsApprovalCount = 0;
        $approvedThisYearCount = 0;
        $notapprovedThisYearCount = 0;

        if ($status) {
            foreach ($models as $model) {
                $needsApprovalCount += $model::where('status', $status)
                    ->where('dpt_id', $dept)
                    ->distinct('sub_id')
                    ->count();
            }
        }

        if ($approvedStatus) {
            $approvedThisYearCount = Approval::where('status', $approvedStatus)
                ->whereYear('created_at', $currentYear)
                ->whereHas('user', function ($query) use ($dept) {
                    $query->where('dept', $dept);
                })
                ->count();
        } else {
            foreach ($models as $model) {
                $approvedThisYearCount += $model::where('status', 7)
                    ->where('dpt_id', $dept)
                    ->whereYear('created_at', $currentYear)
                    ->distinct('sub_id')
                    ->count();
            }
        }

        if ($notapprovedStatus) {
            $notapprovedThisYearCount = Approval::where('status', $notapprovedStatus)
                ->whereYear('created_at', $currentYear)
                ->whereHas('user', function ($query) use ($dept) {
                    $query->where('dept', $dept);
                })
                ->count();
        } elseif (!in_array($sect, ['Kadept', 'Kadiv', 'DIC', 'PIC P&B', 'Kadept P&B'])) {
            $notapprovedThisYearCount = Approval::whereIn('status', [8, 9, 10, 11, 12])
                ->whereYear('created_at', $currentYear)
                ->whereHas('user', function ($query) use ($dept) {
                    $query->where('dept', $dept);
                })
                ->count();
        }

        $budgetByYear = [];
        foreach ($models as $model) {
            $query = $model::where('status', 7)
                ->where('dpt_id', $dept)
                ->when($acc_id, function ($query) use ($acc_id) {
                    return $query->where('acc_id', $acc_id);
                })
                ->selectRaw('YEAR(created_at) as year, SUM(CASE WHEN ? THEN quantity * price ELSE amount END) as total_budget', [
                    $model == BudgetPlan::class
                ])
                ->groupBy('year')
                ->get();

            foreach ($query as $row) {
                $year = $row->year;
                $total = $row->total_budget;
                if (!isset($budgetByYear[$year])) {
                    $budgetByYear[$year] = 0;
                }
                $budgetByYear[$year] += $total;
            }
        }

        $years = array_keys($budgetByYear);
        $budgetValues = array_values($budgetByYear);
        $totalBudget = $budgetByYear[$currentYear] ?? 0;
        $totalBudgetFormatted = number_format($totalBudget, 2, '.', ',');

        $workcenterFilter = $wct_id === 'all' ? null : $wct_id;

        $workcenters = Workcenter::select('wct_id', 'workcenter')
            ->whereHas('budget_plans', function ($query) use ($dept) {
                $query->where('dpt_id', $dept);
            })
            ->get();

        $workcenterData = [];
        $workcenterTotal = (object) [
            'workcenter' => 'Total',
            'total_previous_year' => 0,
            'total_current_year' => 0,
            'variance' => 0,
            'percentage_change' => 0,
        ];

        if (in_array($sect, ['Kadiv', 'DIC'])) {
            $workcenterQuery = Workcenter::select('workcenters.wct_id', 'workcenters.workcenter')
                ->leftJoin('budget_plans', function ($join) use ($year, $sect, $acc_id, $dept) {
                    $join->on('workcenters.wct_id', '=', 'budget_plans.wct_id')
                        ->where('budget_plans.dpt_id', $dept)
                        ->whereIn('budget_plans.created_at', function ($query) use ($year) {
                            $query->select('created_at')
                                ->from('budget_plans')
                                ->whereYear('created_at', $year)
                                ->orWhereYear('created_at', $year); // Ubah ke $year + 1
                        });
                })
                ->selectRaw('
                workcenters.wct_id,
                workcenters.workcenter,
                SUM(CASE WHEN YEAR(budget_plans.created_at) = ? AND budget_plans.status = ? THEN budget_plans.quantity * budget_plans.price ELSE 0 END) as total_previous_year,
                SUM(CASE WHEN YEAR(budget_plans.created_at) = ? AND budget_plans.status = 7 THEN budget_plans.quantity * budget_plans.price ELSE 0 END) as total_current_year
            ', [$year, $sect === 'Kadiv' ? 3 : 4, $year + 1]) // Ubah $year - 1 ke $year + 1
                ->when($submission_type == 'asset', function ($query) {
                    return $query->where('budget_plans.acc_id', '!=', 'CAPEX');
                })
                ->when($submission_type == 'expenditure', function ($query) {
                    return $query->where('budget_plans.acc_id', '=', 'CAPEX');
                })
                ->when($workcenterFilter, function ($query) use ($workcenterFilter) {
                    return $query->where('workcenters.wct_id', $workcenterFilter);
                })
                ->when($acc_id, function ($query) use ($acc_id) {
                    return $query->where('budget_plans.acc_id', $acc_id);
                })
                ->groupBy('workcenters.wct_id', 'workcenters.workcenter')
                ->orderByRaw('total_current_year DESC');

            $workcenterData = $workcenterQuery->get()->map(function ($data) use ($year) {
                $data->variance = $data->total_current_year - $data->total_previous_year;
                $data->percentage_change = $data->total_previous_year > 0
                    ? (($data->total_current_year - $data->total_previous_year) / $data->total_previous_year) * 100
                    : 0;
                return $data;
            });

            $workcenterTotal->total_previous_year = $workcenterData->sum('total_previous_year');
            $workcenterTotal->total_current_year = $workcenterData->sum('total_current_year');
            $workcenterTotal->variance = $workcenterTotal->total_current_year - $workcenterTotal->total_previous_year;
            $workcenterTotal->percentage_change = $workcenterTotal->total_previous_year > 0
                ? (($workcenterTotal->total_current_year - $workcenterTotal->total_previous_year) / $workcenterTotal->total_previous_year) * 100
                : 0;
        }

        $account = $acc_id ? Account::where('acc_id', $acc_id)->first() : null;

        return view('reports.workcenterReport', compact(
            'needsApprovalCount',
            'approvedThisYearCount',
            'notapprovedThisYearCount',
            'totalBudgetFormatted',
            'notifications',
            'years',
            'budgetValues',
            'submission_type',
            'wct_id',
            'year',
            'workcenters',
            'workcenterData',
            'workcenterTotal',
            'acc_id',
            'account'
        ));
    }

    // app/Http/Controllers/ReportController.php
    public function detailReport($acc_id, $wct_id, $year, Request $request)
    {
        $user = Auth::user();
        $dept = $user->dept; // Get the department of the logged-in user
        $notificationController = new NotificationController();
        $notifications = $notificationController->getNotifications();

        $status = 7; // Default for previous year
        if ($request->has('current_year')) {
            $status = ($user->sect === 'Kadiv') ? 3 : (($user->sect === 'DIC') ? 4 : 7);
        }

        // Get account data
        $account = Account::where('acc_id', $acc_id)->firstOrFail();

        // Get filter parameters
        $yearFilter = $request->input('year', $year ?? now()->year);
        $workcenterFilter = $request->input('workcenter', $wct_id === 'all' ? '' : $wct_id);
        $budgetFilter = $request->input('budget_name', '');
        $monthFilter = $request->input('month', '');
        $submission_type = $request->input('submission_type', '');

        // Month mapping from short to full name
        $monthMap = [
            'JAN' => 'January',
            'FEB' => 'February',
            'MAR' => 'March',
            'APR' => 'April',
            'MAY' => 'May',
            'JUN' => 'June',
            'JUL' => 'July',
            'AUG' => 'August',
            'SEP' => 'September',
            'OCT' => 'October',
            'NOV' => 'November',
            'DEC' => 'December'
        ];

        // Base query
        $query = [
            'acc_id' => $acc_id,
            // 'status' => 7,
            'dpt_id' => $dept // Restrict to the user's department
        ];
        if ($workcenterFilter && $workcenterFilter !== 'all') {
            $query['wct_id'] = $workcenterFilter;
        }
        if ($budgetFilter) {
            $query['bdc_id'] = $budgetFilter;
        }
        if ($monthFilter) {
            $query['month'] = $monthMap[$monthFilter] ?? $monthFilter;
        }
        if ($submission_type == 'asset') {
            $query['acc_id'] = ['!=', 'CAPEX'];
        } elseif ($submission_type == 'expenditure') {
            $query['acc_id'] = 'CAPEX';
        }


        // Fetch data based on acc_id, wct_id, and dpt_id
        $reports = collect();
        $models = [
            'general' => ['model' => BudgetPlan::class, 'acc_ids' => ['SGABOOK', 'SGAREPAIR', 'SGAMARKT', 'FOHTECHDO', 'FOHRECRUITING', 'SGARECRUITING', 'SGARENT', 'SGAADVERT', 'SGACOM', 'SGAOFFICESUP', 'SGAASOCIATION', 'SGABCHARGES', 'SGACONTRIBUTION', 'FOHPACKING', 'SGARYLT', 'FOHAUTOMOBILE', 'FOHPROF', 'FOHRENT', 'FOHTAXPUB', 'SGAAUTOMOBILE', 'SGAPROF', 'SGATAXPUB']],
            'support' => ['model' => BudgetPlan::class, 'acc_ids' => ['FOHTOOLS', 'FOHFS', 'FOHINDMAT', 'FOHREPAIR', 'SGADEPRECIATION']],
            'represent' => ['model' => BudgetPlan::class, 'acc_ids' => ['FOHENTERTAINT', 'FOHREPRESENTATION', 'SGAENTERTAINT', 'SGAREPRESENTATION']],
            'insurance' => ['model' => BudgetPlan::class, 'acc_ids' => ['FOHINSPREM', 'SGAINSURANCE']],
            'utilities' => ['model' => BudgetPlan::class, 'acc_ids' => ['FOHPOWER', 'SGAPOWER']],
            'business' => ['model' => BudgetPlan::class, 'acc_ids' => ['FOHTRAV', 'SGATRAV']],
            'training' => ['model' => BudgetPlan::class, 'acc_ids' => ['FOHTRAINING', 'SGATRAINING']],
            'aftersales' => ['model' => BudgetPlan::class, 'acc_ids' => ['SGAAFTERSALES']],
        ];

        $reportType = 'general';
        foreach ($models as $type => $config) {
            if (in_array($acc_id, $config['acc_ids'])) {
                $reportType = $type;
                $model = $config['model'];
                $reports = $model::with(['item', 'dept', 'workcenter', 'budget'])
                    ->where($query)
                    ->where('status', $status) // Filter by determined status

                    ->when($yearFilter, fn($q) => $q->whereYear('updated_at', $yearFilter))
                    ->get();
                break;
            }
        }

        // Fetch years
        $years = collect();
        foreach ($models as $config) {
            $model = $config['model'];
            $years = $years->merge(
                $model::where('acc_id', $acc_id)
                    ->where('dpt_id', $dept) // Restrict years to the user's department
                    ->where('status', 7)
                    ->pluck('updated_at')
                    ->map(fn($date) => $date->year)
            );
        }
        $years = $years->unique()->sort()->values();
        if (!$years->contains($yearFilter)) {
            $years->push($yearFilter);
            $years = $years->unique()->sort()->values();
        }

        // Fetch workcenters
        $workcentersQuery = collect();
        foreach ($models as $config) {
            $model = $config['model'];
            $workcentersQuery = $workcentersQuery->merge(
                $model::where('acc_id', $acc_id)
                    ->where('dpt_id', $dept) // Restrict workcenters to the user's department
                    ->where('status', 7)
                    ->pluck('wct_id')
            );
        }
        $workcenters = Workcenter::whereIn('wct_id', $workcentersQuery->unique()->values())->get();

        // Fetch budget codes
        $budgetCodesQuery = collect();
        foreach ($models as $config) {
            $model = $config['model'];
            $budgetCodesQuery = $budgetCodesQuery->merge(
                $model::where('acc_id', $acc_id)
                    ->where('dpt_id', $dept) // Restrict budget codes to the user's department
                    ->where('status', 7)
                    ->pluck('bdc_id')
            );
        }
        $budgetCodes = BudgetCode::whereIn('bdc_id', $budgetCodesQuery->unique()->values())->get();

        // Get department
        $department = (object)['department' => $user->dept_name ?? 'All Departments', 'dpt_id' => $dept];

        return view('reports.detailReport', [
            'reports' => $reports,
            'acc_id' => $acc_id,
            'account_name' => $account->account,
            'report_type' => $reportType,
            'years' => $years,
            'workcenters' => $workcenters,
            'budgetCodes' => $budgetCodes,
            'selectedYear' => $yearFilter,
            'selectedWorkcenter' => $workcenterFilter,
            'selectedBudget' => $budgetFilter,
            'selectedMonth' => $monthFilter,
            'notifications' => $notifications,
            'department' => $department,
            'dpt_id' => $dept, // Use the logged-in user's department
            'wct_id' => $wct_id,
            'submission_type' => $submission_type
        ]);
    }

    public function approveDivision($div_id)
    {
        try {
            // [PERBAIKAN] Gunakan struktur divisi yang sama dengan yang digunakan di indexAll
            $divisions = [
                'PRODUCTION' => ['departments' => ['1111', '1116', '1131', '1140', '1151', '1160', '1211', '1224', '1231', '1242']],
                'PRODUCTION CONTROL' => ['departments' => ['1311', '1331', '1332', '1333', '1411']],
                'ENGINEERING' => ['departments' => ['1341', '1351', '1361']],
                'PRODUCT ENGINEERING' => ['departments' => ['2111', '2121']],
                'QUALITY ASSURANCE' => ['departments' => ['3111', '3121', '3131']],
                'HRGA & MIS' => ['departments' => ['4111', '4131', '4141', '4311', '7111', '1111', '1131', '1151', '1211', '1231']],
                'MARKETING & PROCUREMENT' => ['departments' => ['4161', '4171', '4181', '5111']],
                'NO DIVISION' => ['departments' => ['4151', '4211', '6111', '6121']]
            ];

            $div_id = urldecode($div_id);

            if (!isset($divisions[$div_id])) {
                Log::error('Invalid division ID', ['div_id' => $div_id, 'available_divisions' => array_keys($divisions)]);
                return response()->json(['message' => 'Invalid division ID'], 400);
            }

            // Ambil sub_id yang akan diperbarui
            $subIds = BudgetPlan::whereIn('dpt_id', $divisions[$div_id]['departments'])
                ->whereIn('status', [4, 11]) // Status pending DIC approval
                ->pluck('sub_id');

            if ($subIds->isEmpty()) {
                Log::warning('No submissions to approve for division', ['div_id' => $div_id]);
                return response()->json(['message' => 'No submissions to approve'], 400);
            }

            // Update status di budget_plans
            $updated = BudgetPlan::whereIn('sub_id', $subIds)
                ->whereIn('status', [4, 11])
                ->update(['status' => 5]); // Acknowledged by DIC

            // Buat record approval untuk setiap sub_id
            $npk = session('npk');
            foreach ($subIds as $sub_id) {
                Approval::create([
                    'approve_by' => $npk,
                    'sub_id' => $sub_id,
                    'status' => 5,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                Log::info("Approval record created for sub_id {$sub_id}", [
                    'status' => 5,
                    'approve_by' => $npk,
                    'div_id' => $div_id
                ]);
            }

            if ($updated) {
                Log::info('Division approved successfully', ['div_id' => $div_id, 'affected_rows' => $updated, 'sub_ids' => $subIds->toArray()]);
                return response()->json(['message' => 'All submissions for division approved successfully']);
            } else {
                Log::warning('No submissions to approve for division', ['div_id' => $div_id, 'sub_ids' => $subIds->toArray()]);
                return response()->json(['message' => 'No submissions to approve'], 400);
            }
        } catch (\Exception $e) {
            Log::error('Error approving division: ', ['error' => $e->getMessage(), 'div_id' => $div_id]);
            return response()->json(['message' => 'Error approving division: ' . $e->getMessage()], 500);
        }
    }

    public function rejectDivision(Request $request, $div_id)
    {
        try {
            $validated = $request->validate([
                'remark' => 'required|string|max:255',
            ]);

            $divisions = [
                'PRODUCTION' => ['departments' => ['1111', '1116', '1131', '1140', '1151', '1160', '1211', '1224', '1231', '1242']],
                'PRODUCTION CONTROL' => ['departments' => ['1311', '1331', '1332', '1333', '1411']],
                'ENGINEERING' => ['departments' => ['1341', '1351', '1361']],
                'PRODUCT ENGINEERING' => ['departments' => ['2111', '2121']],
                'QUALITY ASSURANCE' => ['departments' => ['3111', '3121', '3131']],
                'HRGA & MIS' => ['departments' => ['4111', '4131', '4141', '4311', '7111', '1111', '1131', '1151', '1211', '1231']],
                'MARKETING & PROCUREMENT' => ['departments' => ['4161', '4171', '4181', '5111']],
                'NO DIVISION' => ['departments' => ['4151', '4211', '6111', '6121']]
            ];

            $div_id = urldecode($div_id);

            if (!isset($divisions[$div_id])) {
                Log::error('Invalid division ID', ['div_id' => $div_id, 'available_divisions' => array_keys($divisions)]);
                return response()->json(['message' => 'Invalid division ID'], 400);
            }

            $subIds = BudgetPlan::whereIn('dpt_id', $divisions[$div_id]['departments'])
                ->whereIn('status', [4, 11])
                ->pluck('sub_id');

            if ($subIds->isEmpty()) {
                Log::warning('No submissions to reject for division', ['div_id' => $div_id]);
                return response()->json(['message' => 'No submissions to reject'], 400);
            }

            // Update status di budget_plans
            $updated = BudgetPlan::whereIn('sub_id', $subIds)
                ->whereIn('status', [4, 11])
                ->update(['status' => 10]);

            // Buat record approval dan remark untuk setiap sub_id
            $npk = session('npk');
            foreach ($subIds as $sub_id) {
                // Buat entri baru di tabel Approval
                Approval::create([
                    'approve_by' => $npk,
                    'sub_id' => $sub_id,
                    'status' => 10,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                // Buat entri baru di tabel Remarks
                Remarks::create([
                    'sub_id' => $sub_id,
                    'remark' => $validated['remark'],
                    'remark_by' => $npk,
                    'status' => 10,
                ]);

                Log::info("Approval and remark record created for sub_id {$sub_id}", [
                    'status' => 10,
                    'approve_by' => $npk,
                    'div_id' => $div_id,
                    'remark' => $validated['remark']
                ]);
            }

            if ($updated) {
                Log::info('Division rejected successfully', ['div_id' => $div_id, 'affected_rows' => $updated, 'sub_ids' => $subIds->toArray()]);
                return response()->json(['message' => 'All submissions for division rejected successfully']);
            } else {
                Log::warning('No submissions to reject for division', ['div_id' => $div_id, 'sub_ids' => $subIds->toArray()]);
                return response()->json(['message' => 'No submissions to reject'], 400);
            }
        } catch (\Exception $e) {
            Log::error('Error rejecting division: ', ['error' => $e->getMessage(), 'div_id' => $div_id]);
            return response()->json(['message' => 'Error rejecting division: ' . $e->getMessage()], 500);
        }
    }

    private function isUserDIC($npk, $divisions)
    {
        foreach ($divisions as $division) {
            if (is_array($division['dic'])) {
                if (in_array($npk, $division['dic'])) {
                    return true;
                }
            } else {
                if ($division['dic'] == $npk) {
                    return true;
                }
            }
        }
        return false;
    }

    public function listPurposes(Request $request, $acc_id, $dept_id, $year = null, $submission_type = '')
    {
        $user = Auth::user();
        if (!in_array($user->sect, ['Kadiv', 'DIC'])) {
            return redirect()->route('index-all')->with('error', 'Akses ditolak.');
        }

        $year = $year ?? date('Y');
        $notificationController = new NotificationController();
        $notifications = $notificationController->getNotifications();

        // Ambil data Purpose dari BudgetPlan
        $purposes = BudgetPlan::where('acc_id', $acc_id)
            ->where('dpt_id', $dept_id)
            ->whereYear('created_at', $year)
            ->when($submission_type, function ($query, $submission_type) {
                return $submission_type == 'asset' ? $query->where('acc_id', 'CAPEX') : $query->where('acc_id', '!=', 'CAPEX');
            })
            ->select(
                'purpose',
                'sub_id',
                DB::raw('SUM(price) as total_price'),
                DB::raw('MIN(created_at) as created_at')
            )
            ->groupBy('purpose', 'sub_id')
            ->get();

        // Ambil data departemen
        $department = Departments::where('dpt_id', $dept_id)->first();
        if (!$department) {
            return redirect()->route('index-all')->with('error', 'Departemen tidak ditemukan.');
        }

        // Ambil nama akun
        $account = Account::where('acc_id', $acc_id)->first();
        $accountName = $account ? $account->account : $acc_id;

        // Ambil daftar tahun untuk dropdown
        $years = BudgetPlan::select(DB::raw('DISTINCT YEAR(created_at) as year'))
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('purposes-list', compact(
            'purposes',
            'acc_id',
            'dept_id',
            'year',
            'submission_type',
            'notifications',
            'years',
            'department',
            'accountName'
        ));
    }
}
