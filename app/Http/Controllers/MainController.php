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

    // Department Query using Eloquent
    $departmentQuery = Departments::select('departments.department', 'departments.dpt_id')
        ->when($dpt_id, function ($query) use ($dpt_id) {
            return $query->where('dpt_id', $dpt_id);
        });

    $departmentData = $departmentQuery->get()->map(function ($department) use ($year, $submission_type) {
        $selected_year = $year;
        $previous_year = $year - 1;

        // Query for current year
        $queryCurrent = BudgetPlan::where('dpt_id', $department->dpt_id)
            ->where('status', 7)
            ->whereYear('created_at', $selected_year);

        // Query for previous year
        $queryPrevious = BudgetPlan::where('dpt_id', $department->dpt_id)
            ->where('status', 7)
            ->whereYear('created_at', $previous_year);

        // Apply submission_type filter
        if ($submission_type == 'asset') {
            $queryCurrent->where('acc_id', '!=', 'CAPEX');
            $queryPrevious->where('acc_id', '!=', 'CAPEX');
        } elseif ($submission_type == 'expenditure') {
            $queryCurrent->where('acc_id', '=', 'CAPEX');
            $queryPrevious->where('acc_id', '=', 'CAPEX');
        }

        $total_current_year = $queryCurrent->sum('amount');
        $total_previous_year = $queryPrevious->sum('amount');

        $variance = $total_previous_year - $total_current_year;

        $percentage_change = $total_previous_year > 0
            ? (($total_current_year - $total_previous_year) / $total_previous_year) * 100
            : 0;

        return (object) [
            'department' => $department->department,
            'dpt_id' => $department->dpt_id,
            'total_current_year' => $total_current_year,
            'total_previous_year' => $total_previous_year,
            'variance' => $variance,
            'percentage_change' => $percentage_change,
        ];
    });

    // Calculate total for all departments
    $departmentTotal = (object) [
        'department' => 'TOTAL',
        'total_previous_year' => $departmentData->sum('total_previous_year'),
        'total_current_year' => $departmentData->sum('total_current_year'),
        'variance' => $departmentData->sum('variance'),
    ];

    // Calculate total amount for the pie chart
    $totalAmount = $departmentData->sum('total_current_year');
    $departmentDataWithPercentage = $departmentData->map(function ($data) use ($totalAmount) {
        $percentage = $totalAmount > 0 ? ($data->total_current_year / $totalAmount) * 100 : 0;
        return (object) [
            'department' => $data->department,
            'total_current_year' => $data->total_current_year,
            'percentage' => $percentage,
        ];
    })->all();

    $departments = Departments::all();

    // Account Chart - Current Year (only status 7)
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
            $total = $account->afterSalesServices->sum('amount') ?? 0;
            return (object) [
                'account' => $account->account,
                'total' => $total,
            ];
        });

    $accountDataCurrent = $accountQueryCurrent;

    // Account Chart - Previous Year (only status 7)
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
            $total = $account->afterSalesServices->sum('amount') ?? 0;
            return (object) [
                'account' => $account->account,
                'total' => $total,
            ];
        });

    $accountDataPrevious = $accountQueryPrevious;

    // Monthly Data (only status 7) from BudgetPlan
    $monthlyData = BudgetPlan::select(
        'month',
        DB::raw('SUM(amount) as total'),
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

    // Ensure all months are represented
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
        'submission_type'
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
    //                 ->sum('amount');
    //             // + BusinessDuty::where('dpt_id', $department->dpt_id)
    //             // ->where('status', 7)
    //             // ->whereYear('created_at', $selected_year)
    //             // ->sum('amount')
    //             // + GeneralExpense::where('dpt_id', $department->dpt_id)
    //             // ->where('status', 7)
    //             // ->whereYear('created_at', $selected_year)
    //             // ->sum('amount')
    //             // + InsurancePrem::where('dpt_id', $department->dpt_id)
    //             // ->where('status', 7)
    //             // ->whereYear('created_at', $selected_year)
    //             // ->sum('amount')
    //             // + RepresentationExpense::where('dpt_id', $department->dpt_id)
    //             // ->where('status', 7)
    //             // ->whereYear('created_at', $selected_year)
    //             // ->sum('amount')
    //             // + SupportMaterial::where('dpt_id', $department->dpt_id)
    //             // ->where('status', 7)
    //             // ->whereYear('created_at', $selected_year)
    //             // ->sum('amount')
    //             // + TrainingEducation::where('dpt_id', $department->dpt_id)
    //             // ->where('status', 7)
    //             // ->whereYear('created_at', $selected_year)
    //             // ->sum('amount')
    //             // + Utilities::where('dpt_id', $department->dpt_id)
    //             // ->where('status', 7)
    //             // ->whereYear('created_at', $selected_year)
    //             // ->sum('amount');

    //             $total_previous_year = BudgetPlan::where('dpt_id', $department->dpt_id)
    //                 ->where('status', 7)
    //                 ->whereYear('created_at', $previous_year)
    //                 ->sum('amount');
    //             // + BusinessDuty::where('dpt_id', $department->dpt_id)
    //             // ->where('status', 7)
    //             // ->whereYear('created_at', $previous_year)
    //             // ->sum('amount')
    //             // + GeneralExpense::where('dpt_id', $department->dpt_id)
    //             // ->where('status', 7)
    //             // ->whereYear('created_at', $previous_year)
    //             // ->sum('amount')
    //             // + InsurancePrem::where('dpt_id', $department->dpt_id)
    //             // ->where('status', 7)
    //             // ->whereYear('created_at', $previous_year)
    //             // ->sum('amount')
    //             // + RepresentationExpense::where('dpt_id', $department->dpt_id)
    //             // ->where('status', 7)
    //             // ->whereYear('created_at', $previous_year)
    //             // ->sum('amount')
    //             // + SupportMaterial::where('dpt_id', $department->dpt_id)
    //             // ->where('status', 7)
    //             // ->whereYear('created_at', $previous_year)
    //             // ->sum('amount')
    //             // + TrainingEducation::where('dpt_id', $department->dpt_id)
    //             // ->where('status', 7)
    //             // ->whereYear('created_at', $previous_year)
    //             // ->sum('amount')
    //             // + Utilities::where('dpt_id', $department->dpt_id)
    //             // ->where('status', 7)
    //             // ->whereYear('created_at', $previous_year)
    //             // ->sum('amount');


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
    //                 $total = $account->afterSalesServices->sum('amount') ?? 0;
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
    //                 $total = $account->afterSalesServices->sum('amount') ?? 0;
    //                 return (object) [
    //                     'account' => $account->account,
    //                     'total' => $total,
    //                 ];
    //             });

    //         $accountDataPrevious = $accountQueryPrevious;

    //         // Monthly Data (only status 7) from all relevant tables
    //         $monthlyData = BudgetPlan::select(
    //             'month',
    //             DB::raw('SUM(amount) as total'),
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
    //             //         DB::raw('SUM(amount) as total'),
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
    //             //         DB::raw('SUM(amount) as total'),
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
    //             //         DB::raw('SUM(amount) as total'),
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
    //             //         DB::raw('SUM(amount) as total'),
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
    //             //         DB::raw('SUM(amount) as total'),
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
    //             //         DB::raw('SUM(amount) as total'),
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
    //             //         DB::raw('SUM(amount) as total'),
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

    // [MODIFIKASI SEBELUMNYA] Ambil daftar departemen untuk Kadiv
    $departments = [];
    if ($sect == 'Kadiv' && $npk == 'P1133') {
        $departments = Departments::whereIn('dpt_id', ['4111', '4131', '4141', '1111'])
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

    // [MODIFIKASI] Logika untuk Kadiv: kelompokkan data berdasarkan departemen atau akun
    if ($sect == 'Kadiv' && $npk == 'P1133' && $dept_id) {
        // Menampilkan Account Submission Totals untuk departemen tertentu
        $uploadedData = [
            'last_year' => [],
            'outlook' => []
        ];

        // [MODIFIKASI] Ambil data Last Year (periode 2025)
        $lastYearData = BudgetFyLo::where('periode', $year) // Ubah dari $year - 1 ke $year
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
        $outlookData = BudgetFyLo::where('periode', $year + 1) // Ubah dari $year ke $year + 1
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

        // [MODIFIKASI] Menghitung total budget proposal dari BudgetPlan per acc_id untuk tahun 2026
        $budgetProposalByAccount = BudgetPlan::where('status', 7)
            ->where('dpt_id', $dept_id)
            ->whereYear('created_at', $year) // Ubah dari $year ke $year + 1
            ->selectRaw('acc_id, SUM(amount) as total_proposal')
            ->groupBy('acc_id')
            ->pluck('total_proposal', 'acc_id')
            ->toArray();

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
            $lastYearAmount = 0;
            foreach ($uploadedData['last_year'] as $upload) {
                if ($upload['account'] == $accountId) {
                    $lastYearAmount = $upload['amount'];
                    break;
                }
            }

            // Cari data Outlook yang diupload
            $outlookAmount = 0;
            foreach ($uploadedData['outlook'] as $upload) {
                if ($upload['account'] == $accountId) {
                    $outlookAmount = $upload['amount'];
                    break;
                }
            }

            $proposal = $budgetProposalByAccount[$accountId] ?? 0;

            // Apply filters if any
            if ($submission_type == 'asset' && $accountId == 'CAPEX') {
                continue;
            }
            if ($submission_type == 'expenditure' && $accountId != 'CAPEX') {
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
                'percentage_change_last_year' => $lastYearAmount != 0 ? (($proposal - $lastYearAmount) / $lastYearAmount * 100) : 0,
                'percentage_change_outlook' => $outlookAmount != 0 ? (($proposal - $outlookAmount) / $outlookAmount * 100) : 0
            ];
        }

        // Sort account data alphabetically by account name
        usort($accountData, function($a, $b) {
            return strcmp($a->account, $b->account);
        });

        // Calculate totals
        $accountTotal = (object)[
            'account' => 'Total',
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
    } elseif ($sect == 'Kadiv' && $npk == 'P1133') {
        // Menampilkan Department Submission Totals
        $accountData = [];
        $uploadedData = [
            'last_year' => [],
            'outlook' => []
        ];

        foreach ($departments as $department) {
            $dpt_id = $department['dpt_id'];

            // [MODIFIKASI] Ambil data Last Year (periode 2025)
            $lastYearData = BudgetFyLo::where('periode', $year) // Ubah dari $year - 1 ke $year
                ->where('tipe', 'last_year')
                ->where('dept', $dpt_id)
                ->selectRaw('SUM(total) as total')
                ->first()->total ?? 0;

            // [MODIFIKASI] Ambil data Figure Outlook (periode 2026)
            $outlookData = BudgetFyLo::where('periode', $year + 1) // Ubah dari $year ke $year + 1
                ->where('tipe', 'outlook')
                ->where('dept', $dpt_id)
                ->selectRaw('SUM(total) as total')
                ->first()->total ?? 0;

            // [MODIFIKASI] Menghitung total budget proposal dari BudgetPlan per departemen untuk tahun 2026
            $proposal = BudgetPlan::where('status', 7)
                ->where('dpt_id', $dpt_id)
                ->whereYear('created_at', $year) // Ubah dari $year ke $year + 1
                ->sum('amount') ?? 0;

            // [MODIFIKASI BARU] Hitung jumlah pengajuan (status = 3) untuk departemen ini
            $countSubmissions = BudgetPlan::where('status', 3)
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
                'percentage_change_last_year' => $lastYearData ? (($proposal - $lastYearData) / $lastYearData * 100) : 0,
                'percentage_change_outlook' => $outlookData ? (($proposal - $outlookData) / $outlookData * 100) : 0,
                // [MODIFIKASI BARU] Tambahkan properti count_submissions
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
        // Logika asli untuk non-Kadiv
        $uploadedData = [
            'last_year' => [],
            'outlook' => []
        ];

        // [MODIFIKASI] Ambil data Last Year (periode 2025)
        $lastYearData = BudgetFyLo::where('periode', $year) // Ubah dari $year - 1 ke $year
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
        $outlookData = BudgetFyLo::where('periode', $year + 1) // Ubah dari $year ke $year + 1
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
        $budgetProposalByAccount = BudgetPlan::where('status', 7)
            ->where('dpt_id', $dept)
            ->whereYear('created_at', $year) // Ubah dari $year ke $year + 1
            ->selectRaw('acc_id, SUM(amount) as total_proposal')
            ->groupBy('acc_id')
            ->pluck('total_proposal', 'acc_id')
            ->toArray();

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
            $lastYearAmount = 0;
            foreach ($uploadedData['last_year'] as $upload) {
                if ($upload['account'] == $accountId) {
                    $lastYearAmount = $upload['amount'];
                    break;
                }
            }

            // Cari data Outlook yang diupload
            $outlookAmount = 0;
            foreach ($uploadedData['outlook'] as $upload) {
                if ($upload['account'] == $accountId) {
                    $outlookAmount = $upload['amount'];
                    break;
                }
            }

            $proposal = $budgetProposalByAccount[$accountId] ?? 0;

            // Apply filters if any
            if ($submission_type == 'asset' && $accountId == 'CAPEX') {
                continue;
            }
            if ($submission_type == 'expenditure' && $accountId != 'CAPEX') {
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
                'percentage_change_last_year' => $lastYearAmount ? (($proposal - $lastYearAmount) / $lastYearAmount * 100) : 0,
                'percentage_change_outlook' => $outlookAmount ? (($proposal - $outlookAmount) / $outlookAmount * 100) : 0
            ];
        }

        // Sort account data alphabetically by account name
        usort($accountData, function($a, $b) {
            return strcmp($a->account, $b->account);
        });

        // Calculate totals
        $accountTotal = (object)[
            'account' => 'Total',
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
    }

    // Get accounts for filter dropdown
    $accounts = Account::select('acc_id', 'account')->get();

    // [MODIFIKASI] Logging tambahan untuk debugging
    Log::info('indexAll lastYearData: ', $uploadedData['last_year']);
    Log::info('indexAll outlookData: ', $uploadedData['outlook']);
    Log::info('indexAll budgetProposalByAccount: ', $budgetProposalByAccount);
    Log::info('indexAll accountTotal: ', (array) $accountTotal);

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
        'dept_id' // Tambahkan dept_id untuk logika di view
    ));
}

    public function indexAccounts(Request $request)
{
    $dpt_id = $request->input('dpt_id'); // Departemen yang dipilih
    $year = $request->input('year', date('Y')); // Default 2025
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

    // [MODIFIKASI] Ambil data Last Year untuk periode $year (bukan $year - 1)
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

    // [MODIFIKASI] Ambil data Budget Proposal untuk tahun $year + 1
    $budgetProposalByAccount = BudgetPlan::where('status', 7)
        ->where('dpt_id', $dpt_id)
        ->whereYear('created_at', $year)
        ->selectRaw('acc_id, SUM(amount) as total_proposal')
        ->groupBy('acc_id')
        ->pluck('total_proposal', 'acc_id')
        ->toArray();

    // Ambil semua akun dari tabel accounts dan data yang diupload
    $allAccounts = Account::pluck('acc_id')->toArray();
    $accountNames = Account::pluck('account', 'acc_id')->toArray();

    // Gabungkan akun dari last_year, outlook, dan budget proposal
    $uploadedAccounts = array_unique(array_merge(
        array_column($lastYearData, 'account'),
        array_column($outlookData, 'account'),
        array_keys($budgetProposalByAccount)
    ));
    $allAccounts = array_unique(array_merge($allAccounts, $uploadedAccounts));

    // Siapkan data untuk tabel
    $accountData = [];
    foreach ($allAccounts as $accountId) {
        // Cari data Last Year
        $lastYearAmount = 0;
        foreach ($lastYearData as $upload) {
            if ($upload['account'] == $accountId) {
                $lastYearAmount = $upload['amount'];
                break;
            }
        }

        // Cari data Outlook
        $outlookAmount = 0;
        foreach ($outlookData as $upload) {
            if ($upload['account'] == $accountId) {
                $outlookAmount = $upload['amount'];
                break;
            }
        }

        $proposal = $budgetProposalByAccount[$accountId] ?? 0;

        // Terapkan filter submission_type
        if ($submission_type == 'asset' && $accountId != 'CAPEX') {
            continue;
        }
        if ($submission_type == 'expenditure' && $accountId == 'CAPEX') {
            continue;
        }

        // Gunakan nama akun dari tabel accounts, fallback ke acc_id
        $accountName = $accountNames[$accountId] ?? $accountId;

        $accountData[] = (object)[
            'account' => $accountName,
            'acc_id' => $accountId,
            'total_previous_year' => $lastYearAmount,
            'total_current_year_given' => $outlookAmount,
            'total_current_year_requested' => $proposal,
            'variance_last_year' => $proposal - $lastYearAmount,
            'variance_budget_given' => $proposal - $outlookAmount,
            'percentage_change_last_year' => $lastYearAmount != 0 ? (($proposal - $lastYearAmount) / $lastYearAmount * 100) : 0,
            'percentage_change_outlook' => $outlookAmount != 0 ? (($proposal - $outlookAmount) / $outlookAmount * 100) : 0
        ];
    }

    // Urutkan data berdasarkan nama akun
    usort($accountData, function ($a, $b) {
        return strcmp($a->account, $b->account);
    });

    // Hitung total
    $accountTotal = (object)[
        'account' => 'TOTAL',
        'total_previous_year' => array_sum(array_column($accountData, 'total_previous_year')),
        'total_current_year_given' => array_sum(array_column($accountData, 'total_current_year_given')),
        'total_current_year_requested' => array_sum(array_column($accountData, 'total_current_year_requested')),
        'variance_last_year' => array_sum(array_column($accountData, 'variance_last_year')),
        'variance_budget_given' => array_sum(array_column($accountData, 'variance_budget_given')),
        'percentage_change_last_year' => array_sum(array_column($accountData, 'total_previous_year')) != 0 
            ? (array_sum(array_column($accountData, 'variance_last_year')) / array_sum(array_column($accountData, 'total_previous_year')) * 100) 
            : 0,
        'percentage_change_outlook' => array_sum(array_column($accountData, 'total_current_year_given')) != 0 
            ? (array_sum(array_column($accountData, 'variance_budget_given')) / array_sum(array_column($accountData, 'total_current_year_given')) * 100) 
            : 0
    ];

    // Logging untuk debugging
    Log::info('indexAccounts lastYearData: ', $lastYearData);
    Log::info('indexAccounts accountTotal: ', (array) $accountTotal);

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
            'support' => ['model' => BudgetPlan::class, 'acc_ids' => ['FOHTOOLS', 'FOHFS', 'FOHINDMAT', 'FOHREPAIR']],
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
        $status = ($user->sect === 'Kadiv') ? 3 : 
                 (($user->sect === 'DIC') ? 4 : 7);
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
        'support' => ['model' => BudgetPlan::class, 'acc_ids' => ['FOHTOOLS', 'FOHFS', 'FOHINDMAT', 'FOHREPAIR']],
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
}
