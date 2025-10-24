<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AfterSalesService;
use App\Models\BookNewspaper;
use App\Models\BudgetCode;
use App\Models\BudgetPlan;
use App\Models\BusinessDuty;
use App\Models\Departments;
use App\Models\GeneralExpense;
use App\Models\InsurancePrem;
use App\Models\OfficeOperation;
use App\Models\OperationalSupport;
use App\Models\RepairMaint;
use App\Models\RepresentationExpense;
use App\Models\SupportMaterial;
use App\Models\Training;
use App\Models\TrainingEducation;
use App\Models\Utilities;
use App\Models\Workcenter;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    private function getSheetTemplates()
    {
        return [
            'CAPEX' => [
                'No',
                'Item',
                'Asset Class',
                'Prioritas',
                'Alasan',
                'Keterangan',
                'Workcenter',
                'Department',
                'Quantity',
                'Price',
                'Jan',
                'Feb',
                'Mar',
                'Apr',
                'May',
                'Jun',
                'Jul',
                'Aug',
                'Sep',
                'Oct',
                'Nov',
                'Dec',
                'Total'
            ],
            'ADVERTISING & PROMOTION' => [
                'No',
                // 'Item Type',
                'Item',
                'Description',
                // 'Quantity',
                // 'Price',
                // 'Amount',
                'Workcenter',
                'Department',
                // 'R/NR',
                'Jan',
                'Feb',
                'Mar',
                'Apr',
                'May',
                'Jun',
                'Jul',
                'Aug',
                'Sep',
                'Oct',
                'Nov',
                'Dec',
                'Total'
            ],
            'AFTER SALES SERVICE' => [
                'No',
                // 'Item Type',
                'Item',
                'Customer',
                // 'Quantity',
                // 'Price',
                // 'Amount',
                'Workcenter',
                'Department',
                // 'R/NR',
                'Jan',
                'Feb',
                'Mar',
                'Apr',
                'May',
                'Jun',
                'Jul',
                'Aug',
                'Sep',
                'Oct',
                'Nov',
                'Dec',
                'Total'
            ],
            'INDIRECT MATERIAL' => [
                'No',
                // 'Item Type',
                'Item',
                'Description',
                // 'Unit',
                // 'Quantity',
                // 'Price',
                // 'Amount',
                'Workcenter',
                'Department',
                'R/NR',
                'Line Of Business',
                'Jan',
                'Feb',
                'Mar',
                'Apr',
                'May',
                'Jun',
                'Jul',
                'Aug',
                'Sep',
                'Oct',
                'Nov',
                'Dec',
                'Total'
            ],
            'FACTORY SUPPLY' => [
                'No',
                // 'Item Type',
                'Item',
                'Description',
                // 'Unit',
                // 'Quantity',
                // 'Price',
                // 'Amount',
                'Workcenter',
                'Department',
                'R/NR',
                'Line Of Business',
                'Jan',
                'Feb',
                'Mar',
                'Apr',
                'May',
                'Jun',
                'Jul',
                'Aug',
                'Sep',
                'Oct',
                'Nov',
                'Dec',
                'Total'
            ],
            'CONS TOOLS' => [
                'No',
                // 'Item Type',
                'Item',
                'Description',
                // 'Unit',
                // 'Quantity',
                // 'Price',
                // 'Amount',
                'Workcenter',
                'Department',
                'R/NR',
                'Line Of Business',
                'Jan',
                'Feb',
                'Mar',
                'Apr',
                'May',
                'Jun',
                'Jul',
                'Aug',
                'Sep',
                'Oct',
                'Nov',
                'Dec',
                'Total'
            ],

            // Sheet 6-18: FOH (Factory Overhead) Categories
            'REPAIR & MAINTENANCE FOH' => [
                'No',
                // 'Item Type',
                'Item',
                'Description',
                // 'Unit',
                // 'Quantity',
                // 'Price',
                // 'Amount',
                'Workcenter',
                'Department',
                'R/NR',
                'Line Of Business',
                'Jan',
                'Feb',
                'Mar',
                'Apr',
                'May',
                'Jun',
                'Jul',
                'Aug',
                'Sep',
                'Oct',
                'Nov',
                'Dec',
                'Total'
            ],
            'INSURANCE PREM FOH' => [
                'No',
                'Description',
                'Insurance Company',
                // 'Quantity',
                // 'Price',
                // 'Amount',
                'Workcenter',
                'Department',
                // 'R/NR',
                'Jan',
                'Feb',
                'Mar',
                'Apr',
                'May',
                'Jun',
                'Jul',
                'Aug',
                'Sep',
                'Oct',
                'Nov',
                'Dec',
                'Total'
            ],
            'TAX & PUBLIC DUES FOH' => [
                'No',
                // 'Item Type',
                'Item',
                'Description',
                // 'Quantity',
                // 'Price',
                // 'Amount',
                'Workcenter',
                'Department',
                // 'R/NR',
                'Jan',
                'Feb',
                'Mar',
                'Apr',
                'May',
                'Jun',
                'Jul',
                'Aug',
                'Sep',
                'Oct',
                'Nov',
                'Dec',
                'Total'
            ],
            'PROFESIONAL FEE FOH' => [
                'No',
                // 'Item Type',
                'Item',
                'Description',
                // 'Quantity',
                // 'Price',
                // 'Amount',
                'Workcenter',
                'Department',
                // 'R/NR',
                'Jan',
                'Feb',
                'Mar',
                'Apr',
                'May',
                'Jun',
                'Jul',
                'Aug',
                'Sep',
                'Oct',
                'Nov',
                'Dec',
                'Total'
            ],
            'UTILITIES FOH' => [
                'No',
                // 'Item Type',
                'Item',
                'KWH (Used)',
                // 'Quantity',
                // 'Price',
                // 'Amount',
                'Workcenter',
                'Department',
                'Line of Business',
                'Jan',
                'Feb',
                'Mar',
                'Apr',
                'May',
                'Jun',
                'Jul',
                'Aug',
                'Sep',
                'Oct',
                'Nov',
                'Dec',
                'Total'
            ],
            'PACKING & DELIVERY' => [
                'No',
                // 'Item Type',
                'Item',
                'Description',
                // 'Quantity',
                // 'Price',
                // 'Amount',
                'Workcenter',
                'Department',
                // 'R/NR',
                'Jan',
                'Feb',
                'Mar',
                'Apr',
                'May',
                'Jun',
                'Jul',
                'Aug',
                'Sep',
                'Oct',
                'Nov',
                'Dec',
                'Total'
            ],
            'AUTOMOBILE FOH' => [
                'No',
                // 'Item Type',
                'Item',
                'Description',
                // 'Quantity',
                // 'Price',
                // 'Amount',
                'Workcenter',
                'Department',
                // 'R/NR',
                'Jan',
                'Feb',
                'Mar',
                'Apr',
                'May',
                'Jun',
                'Jul',
                'Aug',
                'Sep',
                'Oct',
                'Nov',
                'Dec',
                'Total'
            ],
            'RENT EXPENSE FOH' => [
                'No',
                // 'Item Type',
                'Item',
                'Description',
                // 'Quantity',
                // 'Price',
                // 'Amount',
                'Workcenter',
                'Department',
                // 'Line of Business',
                'Jan',
                'Feb',
                'Mar',
                'Apr',
                'May',
                'Jun',
                'Jul',
                'Aug',
                'Sep',
                'Oct',
                'Nov',
                'Dec',
                'Total'
            ],
            'BUSINESS DUTY FOH' => [
                'No',
                'Trip Propose',
                'Destination',
                // 'Description',
                'Days',
                // 'Quantity',
                // 'Price',
                // 'Amount',
                'Workcenter',
                'Department',
                // 'R/NR',
                'Jan',
                'Feb',
                'Mar',
                'Apr',
                'May',
                'Jun',
                'Jul',
                'Aug',
                'Sep',
                'Oct',
                'Nov',
                'Dec',
                'Total'
            ],
            'ENTERTAINMENT FOH' => [
                'No',
                // 'Item Type',
                'Item',
                'Description',
                'Beneficiary',
                // 'Quantity',
                // 'Price',
                // 'Amount',
                'Workcenter',
                'Department',
                // 'R/NR',
                'Jan',
                'Feb',
                'Mar',
                'Apr',
                'May',
                'Jun',
                'Jul',
                'Aug',
                'Sep',
                'Oct',
                'Nov',
                'Dec',
                'Total'
            ],
            'REPRESENTATION FOH' => [
                'No',
                // 'Item Type',
                'Item',
                'Description',
                'Beneficiary',
                // 'Quantity',
                // 'Price',
                // 'Amount',
                'Workcenter',
                'Department',
                // 'R/NR',
                'Jan',
                'Feb',
                'Mar',
                'Apr',
                'May',
                'Jun',
                'Jul',
                'Aug',
                'Sep',
                'Oct',
                'Nov',
                'Dec',
                'Total'
            ],
            'TRAINING & EDUCATION FOH' => [
                'No',
                'Participant',
                'Jenis Training',
                'Quantity',
                'Price',
                // 'Amount',
                'Workcenter',
                'Department',
                // 'R/NR',
                'Jan',
                'Feb',
                'Mar',
                'Apr',
                'May',
                'Jun',
                'Jul',
                'Aug',
                'Sep',
                'Oct',
                'Nov',
                'Dec',
                'Total'
            ],
            'TECHNICAL DEVELOPMENT FOH' => [
                'No',
                // 'Item Type',
                'Item',
                'Description',
                // 'Quantity',
                // 'Price',
                // 'Amount',
                'Workcenter',
                'Department',
                // 'R/NR',
                'Jan',
                'Feb',
                'Mar',
                'Apr',
                'May',
                'Jun',
                'Jul',
                'Aug',
                'Sep',
                'Oct',
                'Nov',
                'Dec',
                'Total'
            ],
            'RECRUITMENT FOH' => [
                'No',
                // 'Item Type',
                'Item',
                'Description',
                'Position',
                'Price',
                // 'Amount',
                'Workcenter',
                'Department',
                // 'R/NR',
                'Jan',
                'Feb',
                'Mar',
                'Apr',
                'May',
                'Jun',
                'Jul',
                'Aug',
                'Sep',
                'Oct',
                'Nov',
                'Dec',
                'Total'
            ],
            'REPAIR & MAINTENANCE OPEX' => [
                'No',
                // 'Item Type',
                'Item',
                'Description',
                // 'Quantity',
                // 'Price',
                // 'Amount',
                'Workcenter',
                'Department',
                // 'R/NR',
                'Jan',
                'Feb',
                'Mar',
                'Apr',
                'May',
                'Jun',
                'Jul',
                'Aug',
                'Sep',
                'Oct',
                'Nov',
                'Dec',
                'Total'
            ],
            'INSURANCE PREM OPEX' => [
                'No',
                'Description',
                'Insurance Company',
                // 'Quantity',
                // 'Price',
                // 'Amount',
                'Workcenter',
                'Department',
                // 'R/NR',
                'Jan',
                'Feb',
                'Mar',
                'Apr',
                'May',
                'Jun',
                'Jul',
                'Aug',
                'Sep',
                'Oct',
                'Nov',
                'Dec',
                'Total'
            ],
            'TAX & PUBLIC DUES OPEX' => [
                'No',
                // 'Item Type',
                'Item',
                'Description',
                // 'Quantity',
                // 'Price',
                // 'Amount',
                'Workcenter',
                'Department',
                // 'R/NR',
                'Jan',
                'Feb',
                'Mar',
                'Apr',
                'May',
                'Jun',
                'Jul',
                'Aug',
                'Sep',
                'Oct',
                'Nov',
                'Dec',
                'Total'
            ],
            'RENT EXPENSE OPEX' => [
                'No',
                // 'Item Type',
                'Item',
                'Description',
                // 'Quantity',
                // 'Price',
                // 'Amount',
                'Workcenter',
                'Department',
                // 'R/NR',
                'Jan',
                'Feb',
                'Mar',
                'Apr',
                'May',
                'Jun',
                'Jul',
                'Aug',
                'Sep',
                'Oct',
                'Nov',
                'Dec',
                'Total'
            ],
            'AUTOMOBILE OPEX' => [
                'No',
                // 'Item Type',
                'Item',
                'Description',
                // 'Quantity',
                // 'Price',
                // 'Amount',
                'Workcenter',
                'Department',
                // 'R/NR',
                'Jan',
                'Feb',
                'Mar',
                'Apr',
                'May',
                'Jun',
                'Jul',
                'Aug',
                'Sep',
                'Oct',
                'Nov',
                'Dec',
                'Total'
            ],
            'UTILITIES OPEX' => [
                'No',
                // 'Item Type',
                'Item',
                'KWH (Used)',
                // 'Quantity',
                // 'Price',
                // 'Amount',
                'Workcenter',
                'Department',
                'Line of Business',
                'Jan',
                'Feb',
                'Mar',
                'Apr',
                'May',
                'Jun',
                'Jul',
                'Aug',
                'Sep',
                'Oct',
                'Nov',
                'Dec',
                'Total'
            ],
            'BANK CHARGES' => [
                'No',
                // 'Item Type',
                'Item',
                'Description',
                // 'Quantity',
                // 'Price',
                // 'Amount',
                'Workcenter',
                'Department',
                // 'R/NR',
                'Jan',
                'Feb',
                'Mar',
                'Apr',
                'May',
                'Jun',
                'Jul',
                'Aug',
                'Sep',
                'Oct',
                'Nov',
                'Dec',
                'Total'
            ],
            'ROYALTY' => [
                'No',
                // 'Item Type',
                'Item',
                'Description',
                // 'Quantity',
                // 'Price',
                // 'Amount',
                'Workcenter',
                'Department',
                // 'R/NR',
                'Jan',
                'Feb',
                'Mar',
                'Apr',
                'May',
                'Jun',
                'Jul',
                'Aug',
                'Sep',
                'Oct',
                'Nov',
                'Dec',
                'Total'
            ],
            'BOOK NEWSPAPER' => [
                'No',
                // 'Item Type',
                'Item',
                'Description',
                // 'Quantity',
                // 'Price',
                // 'Amount',
                'Workcenter',
                'Department',
                // 'R/NR',
                'Jan',
                'Feb',
                'Mar',
                'Apr',
                'May',
                'Jun',
                'Jul',
                'Aug',
                'Sep',
                'Oct',
                'Nov',
                'Dec',
                'Total'
            ],
            'COMMUNICATION' => [
                'No',
                // 'Item Type',
                'Item',
                'Description',
                // 'Quantity',
                // 'Price',
                // 'Amount',
                'Workcenter',
                'Department',
                // 'R/NR',
                'Jan',
                'Feb',
                'Mar',
                'Apr',
                'May',
                'Jun',
                'Jul',
                'Aug',
                'Sep',
                'Oct',
                'Nov',
                'Dec',
                'Total'
            ],
            'PROFESIONAL FEE OPEX' => [
                'No',
                // 'Item Type',
                'Item',
                'Description',
                // 'Quantity',
                // 'Price',
                // 'Amount',
                'Workcenter',
                'Department',
                // 'R/NR',
                'Jan',
                'Feb',
                'Mar',
                'Apr',
                'May',
                'Jun',
                'Jul',
                'Aug',
                'Sep',
                'Oct',
                'Nov',
                'Dec',
                'Total'
            ],
            'CONTRIBUTION' => [
                'No',
                // 'Item Type',
                'Item',
                'Description',
                // 'Quantity',
                // 'Price',
                // 'Amount',
                'Workcenter',
                'Department',
                // 'R/NR',
                'Jan',
                'Feb',
                'Mar',
                'Apr',
                'May',
                'Jun',
                'Jul',
                'Aug',
                'Sep',
                'Oct',
                'Nov',
                'Dec',
                'Total'
            ],
            'RECRUITMENT OPEX' => [
                'No',
                // 'Item Type',
                'Item',
                'Description',
                'Position',
                'Price',
                // 'Amount',
                'Workcenter',
                'Department',
                // 'R/NR',
                'Jan',
                'Feb',
                'Mar',
                'Apr',
                'May',
                'Jun',
                'Jul',
                'Aug',
                'Sep',
                'Oct',
                'Nov',
                'Dec',
                'Total'
            ],
            'BUSINESS DUTY OPEX' => [
                'No',
                'Trip Propose',
                'Destination',
                // 'Description',
                'Days',
                // 'Quantity',
                // 'Price',
                // 'Amount',
                'Workcenter',
                'Department',
                // 'R/NR',
                'Jan',
                'Feb',
                'Mar',
                'Apr',
                'May',
                'Jun',
                'Jul',
                'Aug',
                'Sep',
                'Oct',
                'Nov',
                'Dec',
                'Total'
            ],
            'ENTERTAINMENT OPEX' => [
                'No',
                // 'Item Type',
                'Item',
                'Description',
                'Beneficiary',
                // 'Quantity',
                // 'Price',
                // 'Amount',
                'Workcenter',
                'Department',
                // 'R/NR',
                'Jan',
                'Feb',
                'Mar',
                'Apr',
                'May',
                'Jun',
                'Jul',
                'Aug',
                'Sep',
                'Oct',
                'Nov',
                'Dec',
                'Total'
            ],
            'REPRESENTATION OPEX' => [
                'No',
                // 'Item Type',
                'Item',
                'Description',
                'Beneficiary',
                // 'Quantity',
                // 'Price',
                // 'Amount',
                'Workcenter',
                'Department',
                // 'R/NR',
                'Jan',
                'Feb',
                'Mar',
                'Apr',
                'May',
                'Jun',
                'Jul',
                'Aug',
                'Sep',
                'Oct',
                'Nov',
                'Dec',
                'Total'
            ],
            'TRAINING & EDUCATION OPEX' => [
                'No',
                'Participant',
                'Jenis Training',
                'Quantity',
                'Price',
                // 'Amount',
                'Workcenter',
                'Department',
                // 'R/NR',
                'Jan',
                'Feb',
                'Mar',
                'Apr',
                'May',
                'Jun',
                'Jul',
                'Aug',
                'Sep',
                'Oct',
                'Nov',
                'Dec',
                'Total'
            ],
            'MARKETING ACTIVITY' => [
                'No',
                // 'Item Type',
                'Item',
                'Description',
                // 'Quantity',
                // 'Price',
                // 'Amount',
                'Workcenter',
                'Department',
                // 'R/NR',
                'Jan',
                'Feb',
                'Mar',
                'Apr',
                'May',
                'Jun',
                'Jul',
                'Aug',
                'Sep',
                'Oct',
                'Nov',
                'Dec',
                'Total'
            ],
            'OFFICE SUPPLY' => [
                'No',
                // 'Item Type',
                'Item',
                'Description',
                // 'Quantity',
                // 'Price',
                // 'Amount',
                'Workcenter',
                'Department',
                // 'R/NR',
                'Jan',
                'Feb',
                'Mar',
                'Apr',
                'May',
                'Jun',
                'Jul',
                'Aug',
                'Sep',
                'Oct',
                'Nov',
                'Dec',
                'Total'
            ],
            'ASSOCIATION' => [
                'No',
                // 'Item Type',
                'Item',
                'Description',
                // 'Quantity',
                // 'Price',
                // 'Amount',
                'Workcenter',
                'Department',
                // 'R/NR',
                'Jan',
                'Feb',
                'Mar',
                'Apr',
                'May',
                'Jun',
                'Jul',
                'Aug',
                'Sep',
                'Oct',
                'Nov',
                'Dec',
                'Total'
            ],
            'OUTSOURCING FEE' => [
                'No',
                // 'Item Type',
                'Item',
                'Description',
                // 'Quantity',
                // 'Price',
                // 'Amount',
                'Workcenter',
                'Department',
                // 'R/NR',
                'Jan',
                'Feb',
                'Mar',
                'Apr',
                'May',
                'Jun',
                'Jul',
                'Aug',
                'Sep',
                'Oct',
                'Nov',
                'Dec',
                'Total'
            ],
            'EMPLOYEE COMP SGA' => [
                'No',
                // 'Item Type',
                'Ledger Account',
                'Ledger Account Description',
                // 'Quantity',
                // 'Price',
                // 'Amount',
                'Workcenter',
                'Department',
                'Line of Business',
                'R/NR',
                'Jan',
                'Feb',
                'Mar',
                'Apr',
                'May',
                'Jun',
                'Jul',
                'Aug',
                'Sep',
                'Oct',
                'Nov',
                'Dec',
                'Total'
            ],
            'EMPLOYEE COMP FOHDL' => [
                'No',
                // 'Item Type',
                'Ledger Account',
                'Ledger Account Description',
                // 'Quantity',
                // 'Price',
                // 'Amount',
                'Workcenter',
                'Department',
                'Line of Business',
                'R/NR',
                'Jan',
                'Feb',
                'Mar',
                'Apr',
                'May',
                'Jun',
                'Jul',
                'Aug',
                'Sep',
                'Oct',
                'Nov',
                'Dec',
                'Total'
            ],
            'EMPLOYEE COMP FOHIL' => [
                'No',
                // 'Item Type',
                'Ledger Account',
                'Ledger Account Description',
                // 'Quantity',
                // 'Price',
                // 'Amount',
                'Workcenter',
                'Department',
                'Line of Business',
                'R/NR',
                'Jan',
                'Feb',
                'Mar',
                'Apr',
                'May',
                'Jun',
                'Jul',
                'Aug',
                'Sep',
                'Oct',
                'Nov',
                'Dec',
                'Total'
            ],
            'PURCHASE MATERIAL' => [
                'No',
                'Item',
                'Business Partner',
                'Description',
                // 'Quantity',
                // 'Price',
                // 'Amount',
                'Workcenter',
                'Department',
                'Line of Business',
                'R/NR',
                'Jan',
                'Feb',
                'Mar',
                'Apr',
                'May',
                'Jun',
                'Jul',
                'Aug',
                'Sep',
                'Oct',
                'Nov',
                'Dec',
                'Total'
            ],
            'DEPRECIATION OPEX' => [
                'No',
                // 'Item Type',
                'Item',
                'Description',
                // 'Unit',
                // 'Quantity',
                // 'Price',
                // 'Amount',
                'Workcenter',
                'Department',
                'R/NR',
                'Line Of Business',
                'Jan',
                'Feb',
                'Mar',
                'Apr',
                'May',
                'Jun',
                'Jul',
                'Aug',
                'Sep',
                'Oct',
                'Nov',
                'Dec',
                'Total'
            ]
        ];
    }

    public function index($acc_id,   Request $request)
    {
        Log::info('Accessing reports.index with acc_id: ' . $acc_id);
        $notificationController = new NotificationController();
        $notifications = $notificationController->getNotifications();
        // Get account data
        $account = Account::where('acc_id', $acc_id)->firstOrFail();

        // Get filter parameters from the request
        $yearFilter = $request->input('year', ''); // Use current year as default
        $workcenterFilter = $request->input('workcenter', '');
        $budgetFilter = $request->input('budget_name', '');

        $currentYear = date('Y   ');
        $deptId = session('dept');

        // Base query for all data
        $query = ['acc_id' => $acc_id, 'status' => 7, 'dpt_id' => $deptId];

        // Apply filters only if they are provided
        if ($workcenterFilter) {
            $query['wct_id'] = $workcenterFilter;
        }
        if ($budgetFilter) {
            $query['bdc_id'] = $budgetFilter;
        }

        // Collect all report data with filters
        $reports = BudgetPlan::with(['item', 'dept', 'workcenter', 'budget'])
            ->where($query)
            ->when($yearFilter, function ($q) use ($yearFilter) {
                return $q->whereYear('updated_at', $yearFilter);
            }, function ($q) use ($currentYear) {
                return $q->whereYear('updated_at', $currentYear);
            })
            ->get();

        // Fetch years for the dropdown
        $years = BudgetPlan::where('acc_id', $acc_id)
            ->where('status', 7)
            ->where('dpt_id', $deptId)
            ->pluck('updated_at')
            ->map(fn($date) => $date->year)
            ->unique()
            ->sort()
            ->values();
        // Fetch workcenters for the dropdown
        $workcenters = BudgetPlan::where('acc_id', $acc_id)
            ->where('status', 7)
            ->where('dpt_id', $deptId)
            ->pluck('wct_id')
            ->unique()
            ->values();

        $workcenters = Workcenter::whereIn('wct_id', $workcenters)->get();

        // Fetch budget codes for the dropdown
        $budgetCodes = BudgetPlan::where('acc_id', $acc_id)
            ->where('status', 7)
            ->where('dpt_id', $deptId)
            ->pluck('bdc_id')
            ->unique()
            ->values();

        $budgetCodes = BudgetCode::whereIn('bdc_id', $budgetCodes)->get();

        // Determine view template
        if (in_array($acc_id, ['SGABOOK', 'SGAREPAIR', 'SGAMARKT', 'FOHTECHDO', 'FOHRECRUITING', 'SGARECRUITING', 'SGARENT', 'SGAADVERT', 'SGACOM', 'SGAOFFICESUP', 'SGAASOCIATION', 'SGABCHARGES', 'SGACONTRIBUTION', 'FOHPACKING', 'SGARYLT', 'FOHAUTOMOBILE', 'FOHPROF', 'FOHRENT', 'FOHTAXPUB', 'SGAAUTOMOBILE', 'SGAPROF', 'SGATAXPUB'])) {
            $viewName = 'reports.general-all';
        } elseif (in_array($acc_id, ['FOHTOOLS', 'FOHFS', 'FOHINDMAT', 'FOHREPAIR', 'SGADEPRECIATION'])) {
            $viewName = 'reports.support-all';
        } elseif (in_array($acc_id, ['FOHENTERTAINT', 'FOHREPRESENTATION', 'SGAENTERTAINT', 'SGAREPRESENTATION'])) {
            $viewName = 'reports.represent-all';
        } elseif (in_array($acc_id, ['FOHINSPREM', 'SGAINSURANCE'])) {
            $viewName = 'reports.insurance-all';
        } elseif (in_array($acc_id, ['FOHPOWER', 'SGAPOWER'])) {
            $viewName = 'reports.utilities-all';
        } elseif (in_array($acc_id, ['FOHTRAV', 'SGATRAV'])) {
            $viewName = 'reports.business-all';
        } elseif (in_array($acc_id, ['FOHTRAINING', 'SGATRAINING'])) {
            $viewName = 'reports.training-all';
        } elseif ($acc_id === 'SGAAFTERSALES') {
            $viewName = 'reports.aftersales-all';
        }

        return view($viewName, [
            'reports' => $reports,
            'acc_id' => $acc_id,
            'account_name' => $account->account,
            'years' => $years,
            'dpt_id' => $deptId, // Add this line to pass deptId as dpt_id
            'workcenters' => $workcenters,
            'budgetCodes' => $budgetCodes,
            'selectedYear' => $yearFilter,
            'selectedWorkcenter' => $workcenterFilter,
            'selectedBudget' => $budgetFilter,
            'notifications' => $notifications
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function reportAll(Request $request)
    {
        $notificationController = new NotificationController();
        $notifications = $notificationController->getNotifications();

        // Get filter parameters
        $departmentFilter = $request->input('department', '');
        $workcenterFilter = $request->input('workcenter', '');
        $yearFilter = $request->input('year', '');
        $accountFilter = $request->input('account', '');
        $budgetFilter = $request->input('budget_name', '');
        $submissionFilter = $request->input('submission', '');

        $currentYear = date('Y');

        // Base query - HANYA data dengan status = 7
        $query = BudgetPlan::where('status', 7);

        // Apply filters
        if ($departmentFilter) {
            $query->where('dpt_id', $departmentFilter);
        }
        if ($workcenterFilter) {
            $query->where('wct_id', $workcenterFilter);
        }
        if ($accountFilter) {
            $query->where('acc_id', $accountFilter);
        }
        if ($budgetFilter) {
            $query->where('bdc_id', $budgetFilter);
        }

        // PERBAIKAN: Filter submission type yang benar
        if ($submissionFilter === 'expense') {
            // Untuk EXPENSE: tampilkan semua KECUALI CAPEX
            $query->where('acc_id', '!=', 'CAPEX');
        } elseif ($submissionFilter === 'capex/asset') {
            // Untuk CAPEX/ASSET: tampilkan hanya CAPEX
            $query->where('acc_id', 'CAPEX');
        }

        // Filter tahun
        $query->when($yearFilter, function ($q) use ($yearFilter) {
            return $q->whereYear('updated_at', $yearFilter);
        }, function ($q) use ($currentYear) {
            return $q->whereYear('updated_at', $currentYear);
        });

        // Fetch data
        $allData = $query->get();

        // Process calculations untuk setiap account
        $reports = [];
        $accounts = Account::all();

        foreach ($accounts as $account) {
            // PERBAIKAN: Logika skip account yang konsisten dengan filter option
            if ($submissionFilter === 'expense' && $account->acc_id === 'CAPEX') {
                continue; // Skip CAPEX untuk expense
            } elseif ($submissionFilter === 'capex/asset' && $account->acc_id !== 'CAPEX') {
                continue; // Skip non-CAPEX untuk capex/asset
            }

            // Filter data by acc_id
            $items = $allData->where('acc_id', $account->acc_id);

            if ($items->isEmpty()) {
                continue;
            }

            // Initialize monthly totals - SUM OF PRICE
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
                $amount = $item->price; // Hanya ambil price
                $month = strtoupper(substr($item->month, 0, 3));

                if (array_key_exists($month, $monthlyTotals)) {
                    $monthlyTotals[$month] += $amount;
                    $total += $amount;
                }
            }

            // Tampilkan account jika ada data (total > 0) ATAU jika tidak ada filter
            $shouldDisplay = $total > 0 ||
                (!$workcenterFilter && !$yearFilter && !$accountFilter &&
                    !$budgetFilter && !$departmentFilter && !$submissionFilter);

            if ($shouldDisplay) {
                $reports[] = (object)[
                    'acc_id' => $account->acc_id,
                    'account' => $account->account,
                    'monthly_totals' => $monthlyTotals,
                    'total' => $total
                ];
            }
        }

        // Fetch data untuk dropdown filters (hanya data dengan status = 7)
        $years = BudgetPlan::where('status', 7)
            ->pluck('updated_at')
            ->map(fn($date) => $date->year)
            ->unique()->sort()->values();

        $workcenters = Workcenter::whereIn(
            'wct_id',
            BudgetPlan::where('status', 7)->pluck('wct_id')->unique()
        )->get();

        $budgets = BudgetCode::whereIn(
            'bdc_id',
            BudgetPlan::where('status', 7)->pluck('bdc_id')->unique()
        )->get();

        $filteredAccounts = Account::whereIn(
            'acc_id',
            BudgetPlan::where('status', 7)->pluck('acc_id')->unique()
        )->get();

        $departments = Departments::whereIn(
            'dpt_id',
            BudgetPlan::where('status', 7)->pluck('dpt_id')->unique()
        )->get();

        return view('reports.report', [
            'reports' => $reports,
            'departments' => $departments,
            'workcenters' => $workcenters,
            'years' => $years,
            'budgets' => $budgets,
            'accounts' => $filteredAccounts,
            'selectedWorkcenter' => $workcenterFilter,
            'selectedYear' => $yearFilter,
            'selectedAccount' => $accountFilter,
            'selectedBudget' => $budgetFilter,
            'selectedDept' => $departmentFilter,
            'selectedSubmission' => $submissionFilter,
            'notifications' => $notifications,
        ]);
    }

    public function report($acc_id, Request $request)
    {
        $notificationController = new NotificationController();
        $notifications = $notificationController->getNotifications();
        // Get account data
        $account = Account::all();

        // Get filter parameters from the request
        $yearFilter = $request->input('year', '');
        $workcenterFilter = $request->input('workcenter', '');
        $budgetFilter = $request->input('budget_name', '');

        // Base query for all data
        $query = ['acc_id' => $acc_id, 'status' => 7];

        // Apply filters only if they are provided
        if ($workcenterFilter) {
            $query['wct_id'] = $workcenterFilter;
        }
        if ($budgetFilter) {
            $query['bdc_id'] = $budgetFilter;
        }

        // Collect all report data with filters
        $reports = collect()
            ->merge(GeneralExpense::with(['item', 'dept', 'workcenter'])->where($query)->when($yearFilter, function ($q) use ($yearFilter) {
                return $q->whereYear('updated_at', $yearFilter);
            })->get())
            ->merge(SupportMaterial::with(['item', 'dept', 'workcenter'])->where($query)->when($yearFilter, function ($q) use ($yearFilter) {
                return $q->whereYear('updated_at', $yearFilter);
            })->get())
            ->merge(InsurancePrem::with(['item', 'dept', 'workcenter'])->where($query)->when($yearFilter, function ($q) use ($yearFilter) {
                return $q->whereYear('updated_at', $yearFilter);
            })->get())
            ->merge(Utilities::with(['item', 'dept', 'workcenter'])->where($query)->when($yearFilter, function ($q) use ($yearFilter) {
                return $q->whereYear('updated_at', $yearFilter);
            })->get())
            ->merge(BusinessDuty::with(['item', 'dept', 'workcenter'])->where($query)->when($yearFilter, function ($q) use ($yearFilter) {
                return $q->whereYear('updated_at', $yearFilter);
            })->get())
            ->merge(RepresentationExpense::with(['item', 'dept', 'workcenter'])->where($query)->when($yearFilter, function ($q) use ($yearFilter) {
                return $q->whereYear('updated_at', $yearFilter);
            })->get())
            ->merge(TrainingEducation::with(['item', 'dept', 'workcenter'])->where($query)->when($yearFilter, function ($q) use ($yearFilter) {
                return $q->whereYear('updated_at', $yearFilter);
            })->get())
            ->merge(AfterSalesService::with(['item', 'dept', 'workcenter'])->where($query)->when($yearFilter, function ($q) use ($yearFilter) {
                return $q->whereYear('updated_at', $yearFilter);
            })->get());

        // Fetch years for the dropdown
        $years = collect()
            ->merge(GeneralExpense::where('acc_id', $acc_id)->where('status', 7)->pluck('updated_at'))
            ->merge(SupportMaterial::where('acc_id', $acc_id)->where('status', 7)->pluck('updated_at'))
            ->merge(InsurancePrem::where('acc_id', $acc_id)->where('status', 7)->pluck('updated_at'))
            ->merge(Utilities::where('acc_id', $acc_id)->where('status', 7)->pluck('updated_at'))
            ->merge(BusinessDuty::where('acc_id', $acc_id)->where('status', 7)->pluck('updated_at'))
            ->merge(RepresentationExpense::where('acc_id', $acc_id)->where('status', 7)->pluck('updated_at'))
            ->merge(TrainingEducation::where('acc_id', $acc_id)->where('status', 7)->pluck('updated_at'))
            ->merge(AfterSalesService::where('acc_id', $acc_id)->where('status', 7)->pluck('updated_at'))
            ->map(function ($date) {
                return $date->year;
            })
            ->unique()
            ->sort()
            ->values();

        // Fetch workcenters for the dropdown
        $workcenters = collect()
            ->merge(GeneralExpense::where('acc_id', $acc_id)->where('status', 7)->pluck('wct_id'))
            ->merge(SupportMaterial::where('acc_id', $acc_id)->where('status', 7)->pluck('wct_id'))
            ->merge(InsurancePrem::where('acc_id', $acc_id)->where('status', 7)->pluck('wct_id'))
            ->merge(Utilities::where('acc_id', $acc_id)->where('status', 7)->pluck('wct_id'))
            ->merge(BusinessDuty::where('acc_id', $acc_id)->where('status', 7)->pluck('wct_id'))
            ->merge(RepresentationExpense::where('acc_id', $acc_id)->where('status', 7)->pluck('wct_id'))
            ->merge(TrainingEducation::where('acc_id', $acc_id)->where('status', 7)->pluck('wct_id'))
            ->merge(AfterSalesService::where('acc_id', $acc_id)->where('status', 7)->pluck('wct_id'))
            ->unique()
            ->values();

        $workcenters = Workcenter::whereIn('wct_id', $workcenters)->get();

        // Fetch budget codes for the dropdown
        $budgetCodes = collect()
            ->merge(GeneralExpense::where('acc_id', $acc_id)->where('status', 7)->pluck('bdc_id'))
            ->merge(SupportMaterial::where('acc_id', $acc_id)->where('status', 7)->pluck('bdc_id'))
            ->merge(InsurancePrem::where('acc_id', $acc_id)->where('status', 7)->pluck('bdc_id'))
            ->merge(Utilities::where('acc_id', $acc_id)->where('status', 7)->pluck('bdc_id'))
            ->merge(BusinessDuty::where('acc_id', $acc_id)->where('status', 7)->pluck('bdc_id'))
            ->merge(RepresentationExpense::where('acc_id', $acc_id)->where('status', 7)->pluck('bdc_id'))
            ->merge(TrainingEducation::where('acc_id', $acc_id)->where('status', 7)->pluck('bdc_id'))
            ->merge(AfterSalesService::where('acc_id', $acc_id)->where('status', 7)->pluck('bdc_id'))
            ->unique()
            ->values();

        $budgetCodes = BudgetCode::whereIn('bdc_id', $budgetCodes)->get();

        // Determine view template
        if (in_array($acc_id, ['SGABOOK', 'SGAREPAIR', 'SGAMARKT', 'FOHTECHDO', 'FOHRECRUITING', 'SGARECRUITING', 'SGARENT', 'SGAADVERT', 'SGACOM', 'SGAOFFICESUP', 'SGAASOCIATION', 'SGABCHARGES', 'SGACONTRIBUTION', 'FOHPACKING', 'SGARYLT', 'FOHAUTOMOBILE', 'FOHPROF', 'FOHRENT', 'FOHTAXPUB', 'SGAAUTOMOBILE', 'SGAPROF', 'SGATAXPUB'])) {
            $viewName = 'reports.general-all';
        } elseif (in_array($acc_id, ['FOHTOOLS', 'FOHFS', 'FOHINDMAT', 'FOHREPAIR', 'SGADEPRECIATION'])) {
            $viewName = 'reports.support-all';
        } elseif (in_array($acc_id, ['FOHENTERTAINT', 'FOHREPRESENTATION', 'SGAENTERTAINT', 'SGAREPRESENTATION'])) {
            $viewName = 'reports.represent-all';
        } elseif (in_array($acc_id, ['FOHINSPREM', 'SGAINSURANCE'])) {
            $viewName = 'reports.insurance-all';
        } elseif (in_array($acc_id, ['FOHPOWER', 'SGAPOWER'])) {
            $viewName = 'reports.utilities-all';
        } elseif (in_array($acc_id, ['FOHTRAV', 'SGATRAV'])) {
            $viewName = 'reports.business-all';
        } elseif (in_array($acc_id, ['FOHTRAINING', 'SGATRAINING'])) {
            $viewName = 'reports.training-all';
        } elseif ($acc_id === 'SGAAFTERSALES') {
            $viewName = 'reports.aftersales-all';
        }

        return view($viewName, [
            'reports' => $reports,
            'acc_id' => $acc_id,
            'account_name' => $account->account,
            'years' => $years,
            'workcenters' => $workcenters,
            'budgetCodes' => $budgetCodes,
            'selectedYear' => $yearFilter,
            'selectedWorkcenter' => $workcenterFilter,
            'selectedBudget' => $budgetFilter,
            'notifications' => $notifications
        ]);
    }

    public function reportAllSect(Request $request)
    {
        $notificationController = new NotificationController();
        $notifications = $notificationController->getNotifications();

        // Get the dept_id from the authenticated user
        $deptId = Auth::user()->dept; // Fallback to 'all' if dept_id is not set

        // Fetch all accounts
        $accounts = Account::all();

        // Get filter parameters from the request
        $workcenterFilter = $request->input('workcenter', '');
        $accountFilter = $request->input('account', '');
        $budgetFilter = $request->input('budget_name', '');
        $year = $request->input('year', date('Y')); // Default to current year if not provided

        // Fetch workcenters for the dropdown
        $workcentersQuery = collect();
        $models = [
            BudgetPlan::class,
            // Tambahkan model lain jika diperlukan: SupportMaterial::class, InsurancePrem::class, dll.
        ];

        foreach ($models as $model) {
            $query = $model::where('status', 7);
            if ($deptId !== 'all') {
                $query->where('dpt_id', $deptId);
            }
            $workcentersQuery = $workcentersQuery->merge($query->pluck('wct_id'));
        }

        $workcenters = Workcenter::whereIn('wct_id', $workcentersQuery->unique()->values())->get();

        // Fetch years for the dropdown
        $yearsQuery = collect();
        foreach ($models as $model) {
            $query = $model::where('status', 7);
            if ($deptId !== 'all') {
                $query->where('dpt_id', $deptId);
            }
            $yearsQuery = $yearsQuery->merge($query->pluck('updated_at')->map(fn($date) => $date->year));
        }

        // Add the selected year to the years list if not already present
        $years = $yearsQuery->unique()->sort()->values();
        if (!$years->contains($year)) {
            $years->push($year);
            $years = $years->unique()->sort()->values();
        }

        // Fetch accounts for the dropdown
        $filteredAccountsQuery = collect();
        foreach ($models as $model) {
            $query = $model::where('status', 7);
            if ($deptId !== 'all') {
                $query->where('dpt_id', $deptId);
            }
            $filteredAccountsQuery = $filteredAccountsQuery->merge($query->pluck('acc_id'));
        }

        $filteredAccounts = Account::whereIn('acc_id', $filteredAccountsQuery->unique()->values())->get();

        // Fetch budgets for the dropdown
        $budgetsQuery = collect();
        foreach ($models as $model) {
            $query = $model::where('status', 7);
            if ($deptId !== 'all') {
                $query->where('dpt_id', $deptId);
            }
            $budgetsQuery = $budgetsQuery->merge($query->pluck('bdc_id'));
        }

        $budgets = BudgetCode::whereIn('bdc_id', $budgetsQuery->unique()->values())->get();

        // Base query for all data
        $query = ['status' => 7];
        if ($deptId !== 'all') {
            $query['dpt_id'] = $deptId;
        }
        if ($accountFilter) {
            $query['acc_id'] = $accountFilter;
        }
        if ($workcenterFilter) {
            $query['wct_id'] = $workcenterFilter;
        }
        if ($budgetFilter) {
            $query['bdc_id'] = $budgetFilter;
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

            // Always include all accounts, even if total is 0
            $reports[] = (object)[
                'acc_id' => $account->acc_id,
                'account' => $account->account,
                'monthly_totals' => $monthlyTotals,
                'total' => $total
            ];
        }

        // Get department name
        $department = $deptId === 'all' ? (object)['department' => 'All Departments', 'dpt_id' => 'all']
            : Departments::where('dpt_id', $deptId)->firstOrFail();

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

        return view('reports.report-all', [
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

    public function departmentList(Request $request)
    {
        $notificationController = new NotificationController();
        $notifications = $notificationController->getNotifications();

        // Get filter parameters
        $acc_id = $request->input('acc_id');
        $selectedMonth = $request->input('month');
        $selectedYear = $request->input('year', date('Y'));
        $workcenterFilter = $request->input('workcenter', '');
        $accountFilter = $request->input('account', '');
        $budgetFilter = $request->input('budget_name', '');
        $departmentFilter = $request->input('department', '');

        // Fetch years for dropdown
        $years = BudgetPlan::where('status', 7)
            ->pluck('updated_at')
            ->map(function ($date) {
                return $date->year;
            })
            ->unique()
            ->sort()
            ->values();

        // Fetch departments for dropdown
        $departments = BudgetPlan::where('status', 7)
            ->pluck('dpt_id')
            ->unique()
            ->values();
        $departments = Departments::whereIn('dpt_id', $departments)->get();

        // Base query for BudgetPlan
        $query = ['status' => 7, 'acc_id' => $acc_id];

        // Apply filters
        if ($workcenterFilter) {
            $query['wct_id'] = $workcenterFilter;
        }
        if ($accountFilter) {
            $query['acc_id'] = $accountFilter;
        }
        if ($budgetFilter) {
            $query['bdc_id'] = $budgetFilter;
        }
        if ($departmentFilter) {
            $query['dpt_id'] = $departmentFilter;
        }

        // Fetch budget plans (without month filter to get all months for monthly_totals)
        $budgetPlans = BudgetPlan::where($query)
            ->when($selectedYear, function ($q) use ($selectedYear) {
                return $q->whereYear('updated_at', $selectedYear);
            })
            ->get();

        // Group by department and calculate totals
        $departmentData = $budgetPlans->groupBy('dpt_id')->map(function ($items, $dpt_id) use ($selectedMonth) {
            $department = Departments::where('dpt_id', $dpt_id)->first();
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
                $amount = $item->quantity * $item->price;
                $monthKey = strtoupper(substr($item->month, 0, 3));
                if (array_key_exists($monthKey, $monthlyTotals)) {
                    $monthlyTotals[$monthKey] += $amount;
                    // Only include in total if no month filter or matches selected month
                    if (!$selectedMonth || $monthKey === $selectedMonth) {
                        $total += $amount;
                    }
                }
            }

            // If a month is selected, override total to reflect only that month's amount
            if ($selectedMonth && isset($monthlyTotals[$selectedMonth])) {
                $total = $monthlyTotals[$selectedMonth];
            }

            return [
                'dpt_id' => $dpt_id,
                'department' => $department ? $department->department : 'Unknown',
                'monthly_totals' => $monthlyTotals,
                'total' => $total,
            ];
        })->values();

        // Filter departmentData by selected month if applicable
        if ($selectedMonth) {
            $departmentData = $departmentData->filter(function ($dept) use ($selectedMonth) {
                return $dept['monthly_totals'][$selectedMonth] > 0;
            })->values();
        }

        // Fetch account details
        $account = Account::where('acc_id', $acc_id)->first();

        return view('reports.report-dept', [
            'departmentData' => $departmentData,
            'account' => $account,
            'selectedMonth' => $selectedMonth,
            'selectedYear' => $selectedYear,
            'years' => $years,
            'departments' => $departments,
            'selectedWorkcenter' => $workcenterFilter,
            'selectedAccount' => $accountFilter,
            'selectedBudget' => $budgetFilter,
            'selectedDept' => $departmentFilter,
            'notifications' => $notifications,
        ]);
    }

    public function printMonthlyAccount($acc_id, $dpt_id, $month, Request $request)
    {
        // Get account data
        $account = Account::where('acc_id', $acc_id)->first();

        // Get filter parameters from the request
        $yearFilter = $request->input('year', '');
        $workcenterFilter = $request->input('workcenter', '');
        $budgetFilter = $request->input('budget_name', '');

        $currentYear = date('Y');

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

        // Validate month
        if (!array_key_exists($month, $monthMap)) {
            abort(400, 'Invalid month provided');
        }

        // Base query for all data
        $query = [
            'acc_id' => $acc_id,
            'status' => 7,
            'month' => $monthMap[$month],
            'dpt_id' => $dpt_id // Apply the month filter
        ];

        // Apply additional filters if provided
        if ($workcenterFilter) {
            $query['wct_id'] = $workcenterFilter;
        }
        if ($budgetFilter) {
            $query['bdc_id'] = $budgetFilter;
        }

        // Collect all report data with filters
        $reports = collect()
            ->merge(BudgetPlan::with(['item', 'dept', 'workcenter'])->where($query)->when($yearFilter, function ($q) use ($yearFilter) {
                return $q->whereYear('updated_at', $yearFilter);
            }, function ($q) use ($currentYear) {
                return $q->whereYear('updated_at', $currentYear);
            })->get());

        // Initialize PhpSpreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Account: ' . ($account ? $account->name : 'Unknown') . ' - ' . $monthMap[$month]);

        // Define headers based on acc_id
        $headers = [];
        if (in_array($acc_id, ['SGABOOK', 'SGAREPAIR', 'SGAMARKT', 'FOHTECHDO', 'FOHRECRUITING', 'SGARECRUITING', 'SGARENT', 'SGAADVERT', 'SGACOM', 'SGAOFFICESUP', 'SGAASOCIATION', 'SGABCHARGES', 'SGACONTRIBUTION', 'FOHPACKING', 'SGARYLT', 'FOHAUTOMOBILE', 'FOHPROF', 'FOHRENT', 'FOHTAXPUB', 'SGAAUTOMOBILE', 'SGAPROF', 'SGATAXPUB'])) {
            $headers = ['No.', 'Item', 'Description', 'Qty', 'Price', 'Amount', 'Workcenter', 'Department', 'R/NR', $month];
        } elseif ($acc_id === 'SGAAFTERSALES') {
            $headers = ['No.', 'Item', 'Customer', 'Qty', 'Price', 'Amount', 'Workcenter', 'Department', 'R/NR', $month];
        } elseif (in_array($acc_id, ['FOHINSPREM', 'SGAINSURANCE'])) {
            $headers = ['No.', 'Description', 'Insurance Company', 'Qty', 'Price', 'Amount', 'Workcenter', 'Department', 'R/NR', $month];
        } elseif (in_array($acc_id, ['FOHTOOLS', 'FOHFS', 'FOHINDMAT', 'FOHREPAIR', 'SGADEPRECIATION'])) {
            $headers = ['No.', 'Item', 'Description', 'Unit', 'Quantity', 'Price', 'Amount', 'Workcenter', 'Department', 'R/NR', 'Line Of Business', $month];
        } elseif (in_array($acc_id, ['FOHPOWER', 'SGAPOWER'])) {
            $headers = ['No.', 'Item', 'KWH (Used)', 'Qty', 'Price', 'Amount', 'Workcenter', 'Department', 'R/NR', $month];
        } elseif (in_array($acc_id, ['FOHTRAV', 'SGATRAV'])) {
            $headers = ['No.', 'Item', 'Description', 'Days', 'Quantity', 'Price', 'Amount', 'Workcenter', 'Department', 'R/NR', $month];
        } elseif (in_array($acc_id, ['FOHENTERTAINT', 'FOHREPRESENTATION', 'SGAENTERTAINT', 'SGAREPRESENTATION'])) {
            $headers = ['No.', 'Item', 'Description', 'Beneficiary', 'Qty', 'Price', 'Amount', 'Workcenter', 'Department', 'R/NR', $month];
        } elseif (in_array($acc_id, ['FOHTRAINING', 'SGATRAINING'])) {
            $headers = ['No.', 'Participant', 'Jenis Training', 'Qty', 'Price', 'Amount', 'Workcenter', 'Department', 'R/NR', $month];
        } else {
            $headers = ['No.', 'Item', 'Description', 'Qty', 'Price', 'Amount', 'Workcenter', 'Department', 'R/NR', $month];
        }

        // Set headers
        $sheet->fromArray($headers, null, 'A1');

        // Initialize grand total
        $grandTotal = 0;

        // Populate data
        $rowNumber = 2;
        foreach ($reports as $index => $report) {
            // Calculate total
            $total = (in_array($acc_id, [
                'SGAINSURANCE',
                'FOHINSPREM',
                'SGAAFTERSALES',
                'FOHTAXPUB',
                'SGATAXPUB',
                'FOHPROF',
                'SGAPROF',
                'FOHPACKING',
                'FOHAUTOMOBILE',
                'SGAAUTOMOBILE',
                'FOHRENT',
                'SGABCHARGES',
                'SGARYLT',
                'SGACONTRIBUTION',
                'SGAASOCIATION',
                'FOHTECHDO',
                'FOHRECRUITING',
                'SGARECRUITING',
                'SGARENT',
                'SGAMARKT',
                'SGAREPAIR',
                'FOHTRAINING',
                'SGATRAINING',
                'FOHPOWER',
                'SGAPOWER'
            ])) ? $report->amount : ($report->quantity * $report->price);
            $grandTotal += $total;

            // Define row data based on acc_id
            $rowData = [];
            if (in_array($acc_id, ['SGABOOK', 'SGAREPAIR', 'SGAMARKT', 'FOHTECHDO', 'FOHRECRUITING', 'SGARECRUITING', 'SGARENT', 'SGAADVERT', 'SGACOM', 'SGAOFFICESUP', 'SGAASOCIATION', 'SGABCHARGES', 'SGACONTRIBUTION', 'FOHPACKING', 'SGARYLT', 'FOHAUTOMOBILE', 'FOHPROF', 'FOHRENT', 'FOHTAXPUB', 'SGAAUTOMOBILE', 'SGAPROF', 'SGATAXPUB'])) {
                $rowData = [
                    $index + 1,
                    $report->item ? ($report->item->item ?? '') : ($report->itm_id ?? ''),
                    $report->description,
                    $report->quantity,
                    number_format($report->price, 0, ',', '.'),
                    number_format($report->quantity * $report->price, 0, ',', '.'),
                    $report->workcenter->workcenter ?? '',
                    $report->dept->dpt_id ?? '',
                    $report->budget->bdc_id ?? '',
                    number_format($total, 0, ',', '.')
                ];
            } elseif ($acc_id === 'SGAAFTERSALES') {
                $rowData = [
                    $index + 1,
                    $report->item ? ($report->item->item ?? '') : ($report->itm_id ?? ''),
                    $report->description,
                    $report->quantity,
                    number_format($report->price, 0, ',', '.'),
                    number_format($report->quantity * $report->price, 0, ',', '.'),
                    $report->workcenter->workcenter ?? '',
                    $report->dept->dpt_id ?? '',
                    $report->budget->bdc_id ?? '',
                    number_format($total, 0, ',', '.')
                ];
            } elseif (in_array($acc_id, ['SGAINSURANCE', 'FOHINSPREM'])) {
                $rowData = [
                    $index + 1,
                    $report->description,
                    $report->insurance_company ?? '',
                    $report->quantity,
                    number_format($report->price, 0, ',', '.'),
                    number_format($report->quantity * $report->price, 0, ',', '.'),
                    $report->workcenter->workcenter ?? '',
                    $report->dept->dpt_id ?? '',
                    $report->budget->bdc_id ?? '',
                    number_format($total, 0, ',', '.')
                ];
            } elseif (in_array($acc_id, ['FOHINDMAT', 'FOHFS', 'FOHTOOLS', 'FOHREPAIR', 'SGADEPRECIATION'])) {
                $rowData = [
                    $index + 1,
                    $report->item ? ($report->item->item ?? '') : ($report->itm_id ?? ''),
                    $report->description,
                    $report->unit ?? '',
                    $report->quantity,
                    number_format($report->price, 0, ',', '.'),
                    number_format($report->quantity * $report->price, 0, ',', '.'),
                    $report->workcenter->workcenter ?? '',
                    $report->dept->dpt_id ?? '',
                    $report->budget->bdc_id ?? '',
                    $report->line_business->lob_id ?? '',
                    number_format($total, 0, ',', '.')
                ];
            } elseif (in_array($acc_id, ['FOHPOWER', 'SGAPOWER'])) {
                $rowData = [
                    $index + 1,
                    $report->item ? ($report->item->item ?? '') : ($report->itm_id ?? ''),
                    $report->kwh_used ?? '',
                    $report->quantity,
                    number_format($report->price, 0, ',', '.'),
                    number_format($report->quantity * $report->price, 0, ',', '.'),
                    $report->workcenter->workcenter ?? '',
                    $report->dept->dpt_id ?? '',
                    $report->budget->bdc_id ?? '',
                    number_format($total, 0, ',', '.')
                ];
            } elseif (in_array($acc_id, ['FOHTRAV', 'SGATRAV'])) {
                $rowData = [
                    $index + 1,
                    $report->item ? ($report->item->item ?? '') : ($report->itm_id ?? ''),
                    $report->description,
                    $report->days ?? '',
                    $report->quantity,
                    number_format($report->price, 0, ',', '.'),
                    number_format($report->quantity * $report->price, 0, ',', '.'),
                    $report->workcenter->workcenter ?? '',
                    $report->dept->dpt_id ?? '',
                    $report->budget->bdc_id ?? '',
                    number_format($total, 0, ',', '.')
                ];
            } elseif (in_array($acc_id, ['FOHENTERTAINT', 'SGAENTERTAINT', 'FOHREPRESENTATION', 'SGAREPRESENTATION'])) {
                $rowData = [
                    $index + 1,
                    $report->item ? ($report->item->item ?? '') : ($report->itm_id ?? ''),
                    $report->description,
                    $report->beneficiary ?? '',
                    $report->quantity,
                    number_format($report->price, 0, ',', '.'),
                    number_format($report->quantity * $report->price, 0, ',', '.'),
                    $report->workcenter->workcenter ?? '',
                    $report->dept->dpt_id ?? '',
                    $report->budget->bdc_id ?? '',
                    number_format($total, 0, ',', '.')
                ];
            } elseif (in_array($acc_id, ['FOHTRAINING', 'SGATRAINING'])) {
                $rowData = [
                    $index + 1,
                    $report->participant ?? '',
                    $report->jenis_training,
                    $report->quantity,
                    number_format($report->price, 0, ',', '.'),
                    number_format($report->quantity * $report->price, 0, ',', '.'),
                    $report->workcenter->workcenter ?? '',
                    $report->dept->dpt_id ?? '',
                    $report->budget->bdc_id ?? '',
                    number_format($total, 0, ',', '.')
                ];
            } else {
                $rowData = [
                    $index + 1,
                    $report->item ? ($report->item->item ?? '') : ($report->itm_id ?? ''),
                    $report->description,
                    $report->quantity,
                    number_format($report->price, 0, ',', '.'),
                    number_format($report->quantity * $report->price, 0, ',', '.'),
                    $report->workcenter->workcenter ?? '',
                    $report->dept->dpt_id ?? '',
                    $report->budget->bdc_id ?? '',
                    number_format($total, 0, ',', '.')
                ];
            }

            // Write row data
            $sheet->fromArray($rowData, null, 'A' . $rowNumber);
            $rowNumber++;
        }

        // Add footer with totals
        $footerRow = array_fill(0, count($headers), '');
        $footerRow[0] = 'TOTAL';
        $footerRow[count($headers) - 1] = number_format($grandTotal, 0, ',', '.');

        $sheet->fromArray($footerRow, null, 'A' . $rowNumber);

        // Style the header row
        $sheet->getStyle('A1:' . chr(65 + count($headers) - 1) . '1')->getFont()->setBold(true);
        $sheet->getStyle('A1:' . chr(65 + count($headers) - 1) . '1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('FFFF0000');
        $sheet->getStyle('A1:' . chr(65 + count($headers) - 1) . '1')->getFont()
            ->getColor()
            ->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);

        // Style the footer row
        $sheet->getStyle('A' . $rowNumber . ':' . chr(65 + count($headers) - 1) . $rowNumber)->getFont()->setBold(true);
        $sheet->getStyle('A' . $rowNumber . ':' . chr(65 + count($headers) - 1) . $rowNumber)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('FFFF0000');
        $sheet->getStyle('A' . $rowNumber . ':' . chr(65 + count($headers) - 1) . $rowNumber)->getFont()
            ->getColor()
            ->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);

        // Auto-size columns
        foreach (range('A', chr(65 + count($headers) - 1)) as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Set response headers and output the Excel file
        $writer = new Xlsx($spreadsheet);
        $fileName = "Report_{$acc_id}_DPT{$dpt_id}_" . ($yearFilter ? "Year{$yearFilter}_" : '') . ($workcenterFilter ? "WC{$workcenterFilter}_" : '') . ($budgetFilter ? "BC{$budgetFilter}_" : '') . "Month{$month}_" . now()->format('Ymd') . ".xlsx";

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function printAccount($acc_id, $dpt_id, Request $request)
    {
        // Get account data
        $account = Account::where('acc_id', $acc_id)->first();

        // Get filter parameters from the request
        $yearFilter = $request->input('year', '');
        $workcenterFilter = $request->input('workcenter', '');
        $budgetFilter = $request->input('budget_name', '');

        $currentYear = date('Y');

        // Base query for all data
        $query = [
            'acc_id' => $acc_id,
            'status' => 7,
            'dpt_id' => $dpt_id,
        ];

        // Apply filters if provided
        if ($workcenterFilter) {
            $query['wct_id'] = $workcenterFilter;
        }
        if ($budgetFilter) {
            $query['bdc_id'] = $budgetFilter;
        }

        // Collect all report data with filters
        $reports = collect()
            ->merge(BudgetPlan::with(['item', 'dept', 'workcenter'])->where($query)->when($yearFilter, function ($q) use ($yearFilter) {
                return $q->whereYear('updated_at', $yearFilter);
            }, function ($q) use ($currentYear) {
                // Default filter: current year when no year filter is selected
                return $q->whereYear('updated_at', $currentYear);
            })->get());

        // Initialize PhpSpreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Account: ' . ($account ? $account->name : 'Unknown'));

        // Define headers based on acc_id
        $headers = [];
        if (in_array($acc_id, ['SGABOOK', 'SGAREPAIR', 'SGAMARKT', 'FOHTECHDO', 'FOHRECRUITING', 'SGARECRUITING', 'SGARENT', 'SGAADVERT', 'SGACOM', 'SGAOFFICESUP', 'SGAASOCIATION', 'SGABCHARGES', 'SGACONTRIBUTION', 'FOHPACKING', 'SGARYLT', 'FOHAUTOMOBILE', 'FOHPROF', 'FOHRENT', 'FOHTAXPUB', 'SGAAUTOMOBILE', 'SGAPROF', 'SGATAXPUB'])) {
            $headers = ['No.', 'Item', 'Description', 'Qty', 'Price', 'Amount', 'Workcenter', 'Department', 'R/NR', 'JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC', 'Total'];
        } elseif ($acc_id === 'SGAAFTERSALES') {
            $headers = ['No.', 'Item', 'Customer', 'Qty', 'Price', 'Amount', 'Workcenter', 'Department', 'R/NR', 'JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC', 'Total'];
        } elseif (in_array($acc_id, ['FOHINSPREM', 'SGAINSURANCE'])) {
            $headers = ['No.', 'Description', 'Insurance Company', 'Qty', 'Price', 'Amount', 'Workcenter', 'Department', 'R/NR', 'JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC', 'Total'];
        } elseif (in_array($acc_id, ['FOHTOOLS', 'FOHFS', 'FOHINDMAT', 'FOHREPAIR', 'SGADEPRECIATION'])) {
            $headers = ['No.', 'Item', 'Description', 'Unit', 'Quantity', 'Price', 'Amount', 'Workcenter', 'Department', 'R/NR', 'Line Of Business', 'JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC', 'Total'];
        } elseif (in_array($acc_id, ['FOHPOWER', 'SGAPOWER'])) { // Utilities
            $headers = ['No.', 'Item', 'KWH (Used)', 'Qty', 'Price', 'Amount', 'Workcenter', 'Department', 'R/NR', 'JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC', 'Total'];
        } elseif (in_array($acc_id, ['FOHTRAV', 'SGATRAV'])) { // Business
            $headers = ['No.', 'Item', 'Description', 'Days', 'Quantity', 'Price', 'Amount', 'Workcenter', 'Department', 'R/NR', 'JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC', 'Total'];
        } elseif (in_array($acc_id, ['FOHENTERTAINT', 'FOHREPRESENTATION', 'SGAENTERTAINT', 'SGAREPRESENTATION'])) {
            $headers = ['No.', 'Item', 'Description', 'Beneficiary', 'Qty', 'Price', 'Amount', 'Workcenter', 'Department', 'R/NR', 'JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC', 'Total'];
        } elseif (in_array($acc_id, ['FOHTRAINING', 'SGATRAINING'])) {
            $headers = ['No.', 'Participant', 'Jenis Training', 'Qty', 'Price', 'Amount', 'Workcenter', 'Department', 'R/NR', 'JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC', 'Total'];
        } else {
            $headers = ['No.', 'Item', 'Description', 'Qty', 'Price', 'Amount', 'Workcenter', 'Department', 'R/NR', 'JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC', 'Total'];
        }

        // Set headers
        $sheet->fromArray($headers, null, 'A1');

        // Initialize monthly totals
        $monthlyTotals = array_fill_keys(['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'], 0);
        $grandTotal = 0;

        // Populate data
        $rowNumber = 2;
        foreach ($reports as $index => $report) {
            // Calculate total
            $total = (in_array($acc_id, [
                'SGAINSURANCE',
                'FOHINSPREM',
                'SGAAFTERSALES',
                'FOHTAXPUB',
                'SGATAXPUB',
                'FOHPROF',
                'SGAPROF',
                'FOHPACKING',
                'FOHAUTOMOBILE',
                'SGAAUTOMOBILE',
                'FOHRENT',
                'SGABCHARGES',
                'SGARYLT',
                'SGACONTRIBUTION',
                'SGAASOCIATION',
                'FOHTECHDO',
                'FOHRECRUITING',
                'SGARECRUITING',
                'SGARENT',
                'SGAMARKT',
                'SGAREPAIR',
                'FOHTRAINING',
                'SGATRAINING',
                'FOHPOWER',
                'SGAPOWER'
            ])) ? $report->amount : ($report->quantity * $report->price);
            $grandTotal += $total;

            $monthValues = array_fill_keys(['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'], '');
            if (!empty($report->month)) {
                $month = strtoupper(substr($report->month, 0, 3));
                if (array_key_exists($month, $monthValues)) {
                    $monthValues[$month] = $total;
                    $monthlyTotals[$month] += $total;
                }
            }

            // Define row data based on acc_id
            $rowData = [];
            if (in_array($acc_id, ['SGABOOK', 'SGAREPAIR', 'SGAMARKT', 'FOHTECHDO', 'FOHRECRUITING', 'SGARECRUITING', 'SGARENT', 'SGAADVERT', 'SGACOM', 'SGAOFFICESUP', 'SGAASOCIATION', 'SGABCHARGES', 'SGACONTRIBUTION', 'FOHPACKING', 'SGARYLT', 'FOHAUTOMOBILE', 'FOHPROF', 'FOHRENT', 'FOHTAXPUB', 'SGAAUTOMOBILE', 'SGAPROF', 'SGATAXPUB'])) {
                $rowData = [
                    $index + 1,
                    $report->item ? ($report->item->item ?? '') : ($report->itm_id ?? ''),
                    $report->description,
                    $report->quantity,
                    number_format($report->price, 0, ',', '.'),
                    number_format($report->quantity * $report->price, 0, ',', '.'),
                    $report->workcenter->workcenter ?? '',
                    $report->dept->dpt_id ?? '',
                    $report->budget->bdc_id ?? '',
                    $monthValues['JAN'] ? number_format($monthValues['JAN'], 0, ',', '.') : '',
                    $monthValues['FEB'] ? number_format($monthValues['FEB'], 0, ',', '.') : '',
                    $monthValues['MAR'] ? number_format($monthValues['MAR'], 0, ',', '.') : '',
                    $monthValues['APR'] ? number_format($monthValues['APR'], 0, ',', '.') : '',
                    $monthValues['MAY'] ? number_format($monthValues['MAY'], 0, ',', '.') : '',
                    $monthValues['JUN'] ? number_format($monthValues['JUN'], 0, ',', '.') : '',
                    $monthValues['JUL'] ? number_format($monthValues['JUL'], 0, ',', '.') : '',
                    $monthValues['AUG'] ? number_format($monthValues['AUG'], 0, ',', '.') : '',
                    $monthValues['SEP'] ? number_format($monthValues['SEP'], 0, ',', '.') : '',
                    $monthValues['OCT'] ? number_format($monthValues['OCT'], 0, ',', '.') : '',
                    $monthValues['NOV'] ? number_format($monthValues['NOV'], 0, ',', '.') : '',
                    $monthValues['DEC'] ? number_format($monthValues['DEC'], 0, ',', '.') : '',
                    number_format($total, 0, ',', '.')
                ];
            } elseif ($acc_id === 'SGAAFTERSALES') {
                $rowData = [
                    $index + 1,
                    $report->item->item ?? '',
                    $report->description,
                    $report->quantity,
                    number_format($report->price, 0, ',', '.'),
                    number_format($report->quantity * $report->price, 0, ',', '.'),
                    $report->workcenter->workcenter ?? '',
                    $report->dept->dpt_id ?? '',
                    $report->budget->bdc_id ?? '',
                    $monthValues['JAN'] ? number_format($monthValues['JAN'], 0, ',', '.') : '',
                    $monthValues['FEB'] ? number_format($monthValues['FEB'], 0, ',', '.') : '',
                    $monthValues['MAR'] ? number_format($monthValues['MAR'], 0, ',', '.') : '',
                    $monthValues['APR'] ? number_format($monthValues['APR'], 0, ',', '.') : '',
                    $monthValues['MAY'] ? number_format($monthValues['MAY'], 0, ',', '.') : '',
                    $monthValues['JUN'] ? number_format($monthValues['JUN'], 0, ',', '.') : '',
                    $monthValues['JUL'] ? number_format($monthValues['JUL'], 0, ',', '.') : '',
                    $monthValues['AUG'] ? number_format($monthValues['AUG'], 0, ',', '.') : '',
                    $monthValues['SEP'] ? number_format($monthValues['SEP'], 0, ',', '.') : '',
                    $monthValues['OCT'] ? number_format($monthValues['OCT'], 0, ',', '.') : '',
                    $monthValues['NOV'] ? number_format($monthValues['NOV'], 0, ',', '.') : '',
                    $monthValues['DEC'] ? number_format($monthValues['DEC'], 0, ',', '.') : '',
                    number_format($total, 0, ',', '.')
                ];
            } elseif (in_array($acc_id, ['SGAINSURANCE', 'FOHINSPREM'])) {
                $rowData = [
                    $index + 1,
                    $report->description,
                    $report->insurance_company ?? '',
                    $report->quantity,
                    number_format($report->price, 0, ',', '.'),
                    number_format($report->quantity * $report->price, 0, ',', '.'),
                    $report->workcenter->workcenter ?? '',
                    $report->dept->dpt_id ?? '',
                    $report->budget->bdc_id ?? '',
                    $monthValues['JAN'] ? number_format($monthValues['JAN'], 0, ',', '.') : '',
                    $monthValues['FEB'] ? number_format($monthValues['FEB'], 0, ',', '.') : '',
                    $monthValues['MAR'] ? number_format($monthValues['MAR'], 0, ',', '.') : '',
                    $monthValues['APR'] ? number_format($monthValues['APR'], 0, ',', '.') : '',
                    $monthValues['MAY'] ? number_format($monthValues['MAY'], 0, ',', '.') : '',
                    $monthValues['JUN'] ? number_format($monthValues['JUN'], 0, ',', '.') : '',
                    $monthValues['JUL'] ? number_format($monthValues['JUL'], 0, ',', '.') : '',
                    $monthValues['AUG'] ? number_format($monthValues['AUG'], 0, ',', '.') : '',
                    $monthValues['SEP'] ? number_format($monthValues['SEP'], 0, ',', '.') : '',
                    $monthValues['OCT'] ? number_format($monthValues['OCT'], 0, ',', '.') : '',
                    $monthValues['NOV'] ? number_format($monthValues['NOV'], 0, ',', '.') : '',
                    $monthValues['DEC'] ? number_format($monthValues['DEC'], 0, ',', '.') : '',
                    number_format($total, 0, ',', '.')
                ];
            } elseif (in_array($acc_id, ['FOHINDMAT', 'FOHFS', 'FOHTOOLS', 'FOHREPAIR', 'SGADEPRECIATION'])) {
                $rowData = [
                    $index + 1,
                    $report->item ? ($report->item->item ?? '') : ($report->itm_id ?? ''),
                    $report->description,
                    $report->unit ?? '',
                    $report->quantity,
                    number_format($report->price, 0, ',', '.'),
                    number_format($report->quantity * $report->price, 0, ',', '.'),
                    $report->workcenter->workcenter ?? '',
                    $report->dept->dpt_id ?? '',
                    $report->budget->bdc_id ?? '',
                    $report->line_business->lob_id ?? '',
                    $monthValues['JAN'] ? number_format($monthValues['JAN'], 0, ',', '.') : '',
                    $monthValues['FEB'] ? number_format($monthValues['FEB'], 0, ',', '.') : '',
                    $monthValues['MAR'] ? number_format($monthValues['MAR'], 0, ',', '.') : '',
                    $monthValues['APR'] ? number_format($monthValues['APR'], 0, ',', '.') : '',
                    $monthValues['MAY'] ? number_format($monthValues['MAY'], 0, ',', '.') : '',
                    $monthValues['JUN'] ? number_format($monthValues['JUN'], 0, ',', '.') : '',
                    $monthValues['JUL'] ? number_format($monthValues['JUL'], 0, ',', '.') : '',
                    $monthValues['AUG'] ? number_format($monthValues['AUG'], 0, ',', '.') : '',
                    $monthValues['SEP'] ? number_format($monthValues['SEP'], 0, ',', '.') : '',
                    $monthValues['OCT'] ? number_format($monthValues['OCT'], 0, ',', '.') : '',
                    $monthValues['NOV'] ? number_format($monthValues['NOV'], 0, ',', '.') : '',
                    $monthValues['DEC'] ? number_format($monthValues['DEC'], 0, ',', '.') : '',
                    number_format($total, 0, ',', '.')
                ];
            } elseif (in_array($acc_id, ['FOHPOWER', 'SGAPOWER'])) { // Utilities
                $rowData = [
                    $index + 1,
                    $report->item ? ($report->item->item ?? '') : ($report->itm_id ?? ''),
                    $report->kwh_used ?? '',
                    $report->quantity,
                    number_format($report->price, 0, ',', '.'),
                    number_format($report->quantity * $report->price, 0, ',', '.'),
                    $report->workcenter->workcenter ?? '',
                    $report->dept->dpt_id ?? '',
                    $report->budget->bdc_id ?? '',
                    $monthValues['JAN'] ? number_format($monthValues['JAN'], 0, ',', '.') : '',
                    $monthValues['FEB'] ? number_format($monthValues['FEB'], 0, ',', '.') : '',
                    $monthValues['MAR'] ? number_format($monthValues['MAR'], 0, ',', '.') : '',
                    $monthValues['APR'] ? number_format($monthValues['APR'], 0, ',', '.') : '',
                    $monthValues['MAY'] ? number_format($monthValues['MAY'], 0, ',', '.') : '',
                    $monthValues['JUN'] ? number_format($monthValues['JUN'], 0, ',', '.') : '',
                    $monthValues['JUL'] ? number_format($monthValues['JUL'], 0, ',', '.') : '',
                    $monthValues['AUG'] ? number_format($monthValues['AUG'], 0, ',', '.') : '',
                    $monthValues['SEP'] ? number_format($monthValues['SEP'], 0, ',', '.') : '',
                    $monthValues['OCT'] ? number_format($monthValues['OCT'], 0, ',', '.') : '',
                    $monthValues['NOV'] ? number_format($monthValues['NOV'], 0, ',', '.') : '',
                    $monthValues['DEC'] ? number_format($monthValues['DEC'], 0, ',', '.') : '',
                    number_format($total, 0, ',', '.')
                ];
            } elseif (in_array($acc_id, ['FOHTRAV', 'SGATRAV'])) { // Business
                $rowData = [
                    $index + 1,
                    $report->item ? ($report->item->item ?? '') : ($report->itm_id ?? ''),
                    $report->description,
                    $report->days ?? '',
                    $report->quantity,
                    number_format($report->price, 0, ',', '.'),
                    number_format($report->quantity * $report->price, 0, ',', '.'),
                    $report->workcenter->workcenter ?? '',
                    $report->dept->dpt_id ?? '',
                    $report->budget->bdc_id ?? '',
                    $monthValues['JAN'] ? number_format($monthValues['JAN'], 0, ',', '.') : '',
                    $monthValues['FEB'] ? number_format($monthValues['FEB'], 0, ',', '.') : '',
                    $monthValues['MAR'] ? number_format($monthValues['MAR'], 0, ',', '.') : '',
                    $monthValues['APR'] ? number_format($monthValues['APR'], 0, ',', '.') : '',
                    $monthValues['MAY'] ? number_format($monthValues['MAY'], 0, ',', '.') : '',
                    $monthValues['JUN'] ? number_format($monthValues['JUN'], 0, ',', '.') : '',
                    $monthValues['JUL'] ? number_format($monthValues['JUL'], 0, ',', '.') : '',
                    $monthValues['AUG'] ? number_format($monthValues['AUG'], 0, ',', '.') : '',
                    $monthValues['SEP'] ? number_format($monthValues['SEP'], 0, ',', '.') : '',
                    $monthValues['OCT'] ? number_format($monthValues['OCT'], 0, ',', '.') : '',
                    $monthValues['NOV'] ? number_format($monthValues['NOV'], 0, ',', '.') : '',
                    $monthValues['DEC'] ? number_format($monthValues['DEC'], 0, ',', '.') : '',
                    number_format($total, 0, ',', '.')
                ];
            } elseif (in_array($acc_id, ['FOHENTERTAINT', 'SGAENTERTAINT', 'FOHREPRESENTATION', 'SGAREPRESENTATION'])) {
                $rowData = [
                    $index + 1,
                    $report->item ? ($report->item->item ?? '') : ($report->itm_id ?? ''),
                    $report->description,
                    $report->beneficiary ?? '',
                    $report->quantity,
                    number_format($report->price, 0, ',', '.'),
                    number_format($report->quantity * $report->price, 0, ',', '.'),
                    $report->workcenter->workcenter ?? '',
                    $report->dept->dpt_id ?? '',
                    $report->budget->bdc_id ?? '',
                    $monthValues['JAN'] ? number_format($monthValues['JAN'], 0, ',', '.') : '',
                    $monthValues['FEB'] ? number_format($monthValues['FEB'], 0, ',', '.') : '',
                    $monthValues['MAR'] ? number_format($monthValues['MAR'], 0, ',', '.') : '',
                    $monthValues['APR'] ? number_format($monthValues['APR'], 0, ',', '.') : '',
                    $monthValues['MAY'] ? number_format($monthValues['MAY'], 0, ',', '.') : '',
                    $monthValues['JUN'] ? number_format($monthValues['JUN'], 0, ',', '.') : '',
                    $monthValues['JUL'] ? number_format($monthValues['JUL'], 0, ',', '.') : '',
                    $monthValues['AUG'] ? number_format($monthValues['AUG'], 0, ',', '.') : '',
                    $monthValues['SEP'] ? number_format($monthValues['SEP'], 0, ',', '.') : '',
                    $monthValues['OCT'] ? number_format($monthValues['OCT'], 0, ',', '.') : '',
                    $monthValues['NOV'] ? number_format($monthValues['NOV'], 0, ',', '.') : '',
                    $monthValues['DEC'] ? number_format($monthValues['DEC'], 0, ',', '.') : '',
                    number_format($total, 0, ',', '.')
                ];
            } elseif (in_array($acc_id, ['FOHTRAINING', 'SGATRAINING'])) {
                $rowData = [
                    $index + 1,
                    $report->participant ?? '',
                    $report->jenis_training,
                    $report->quantity,
                    number_format($report->price, 0, ',', '.'),
                    number_format($report->quantity * $report->price, 0, ',', '.'),
                    $report->workcenter->workcenter ?? '',
                    $report->dept->dpt_id ?? '',
                    $report->budget->bdc_id ?? '',
                    $monthValues['JAN'] ? number_format($monthValues['JAN'], 0, ',', '.') : '',
                    $monthValues['FEB'] ? number_format($monthValues['FEB'], 0, ',', '.') : '',
                    $monthValues['MAR'] ? number_format($monthValues['MAR'], 0, ',', '.') : '',
                    $monthValues['APR'] ? number_format($monthValues['APR'], 0, ',', '.') : '',
                    $monthValues['MAY'] ? number_format($monthValues['MAY'], 0, ',', '.') : '',
                    $monthValues['JUN'] ? number_format($monthValues['JUN'], 0, ',', '.') : '',
                    $monthValues['JUL'] ? number_format($monthValues['JUL'], 0, ',', '.') : '',
                    $monthValues['AUG'] ? number_format($monthValues['AUG'], 0, ',', '.') : '',
                    $monthValues['SEP'] ? number_format($monthValues['SEP'], 0, ',', '.') : '',
                    $monthValues['OCT'] ? number_format($monthValues['OCT'], 0, ',', '.') : '',
                    $monthValues['NOV'] ? number_format($monthValues['NOV'], 0, ',', '.') : '',
                    $monthValues['DEC'] ? number_format($monthValues['DEC'], 0, ',', '.') : '',
                    number_format($total, 0, ',', '.')
                ];
            } else {
                $rowData = [
                    $index + 1,
                    $report->item ? ($report->item->item ?? '') : ($report->itm_id ?? ''),
                    $report->description,
                    $report->quantity,
                    number_format($report->price, 0, ',', '.'),
                    number_format($report->quantity * $report->price, 0, ',', '.'),
                    $report->workcenter->workcenter ?? '',
                    $report->dept->dpt_id ?? '',
                    $report->budget->bdc_id ?? '',
                    $monthValues['JAN'] ? number_format($monthValues['JAN'], 0, ',', '.') : '',
                    $monthValues['FEB'] ? number_format($monthValues['FEB'], 0, ',', '.') : '',
                    $monthValues['MAR'] ? number_format($monthValues['MAR'], 0, ',', '.') : '',
                    $monthValues['APR'] ? number_format($monthValues['APR'], 0, ',', '.') : '',
                    $monthValues['MAY'] ? number_format($monthValues['MAY'], 0, ',', '.') : '',
                    $monthValues['JUN'] ? number_format($monthValues['JUN'], 0, ',', '.') : '',
                    $monthValues['JUL'] ? number_format($monthValues['JUL'], 0, ',', '.') : '',
                    $monthValues['AUG'] ? number_format($monthValues['AUG'], 0, ',', '.') : '',
                    $monthValues['SEP'] ? number_format($monthValues['SEP'], 0, ',', '.') : '',
                    $monthValues['OCT'] ? number_format($monthValues['OCT'], 0, ',', '.') : '',
                    $monthValues['NOV'] ? number_format($monthValues['NOV'], 0, ',', '.') : '',
                    $monthValues['DEC'] ? number_format($monthValues['DEC'], 0, ',', '.') : '',
                    number_format($total, 0, ',', '.')
                ];
            }

            // Write row data
            $sheet->fromArray($rowData, null, 'A' . $rowNumber);
            $rowNumber++;
        }

        // Add footer with totals
        $footerRow = array_fill(0, count($headers), '');
        $footerRow[0] = 'TOTAL';
        $monthStartIndex = count($headers) - count($monthlyTotals) - 1;
        $footerRow[$monthStartIndex] = number_format($monthlyTotals['JAN'], 0, ',', '.');
        $footerRow[$monthStartIndex + 1] = number_format($monthlyTotals['FEB'], 0, ',', '.');
        $footerRow[$monthStartIndex + 2] = number_format($monthlyTotals['MAR'], 0, ',', '.');
        $footerRow[$monthStartIndex + 3] = number_format($monthlyTotals['APR'], 0, ',', '.');
        $footerRow[$monthStartIndex + 4] = number_format($monthlyTotals['MAY'], 0, ',', '.');
        $footerRow[$monthStartIndex + 5] = number_format($monthlyTotals['JUN'], 0, ',', '.');
        $footerRow[$monthStartIndex + 6] = number_format($monthlyTotals['JUL'], 0, ',', '.');
        $footerRow[$monthStartIndex + 7] = number_format($monthlyTotals['AUG'], 0, ',', '.');
        $footerRow[$monthStartIndex + 8] = number_format($monthlyTotals['SEP'], 0, ',', '.');
        $footerRow[$monthStartIndex + 9] = number_format($monthlyTotals['OCT'], 0, ',', '.');
        $footerRow[$monthStartIndex + 10] = number_format($monthlyTotals['NOV'], 0, ',', '.');
        $footerRow[$monthStartIndex + 11] = number_format($monthlyTotals['DEC'], 0, ',', '.');
        $footerRow[count($headers) - 1] = number_format($grandTotal, 0, ',', '.');

        $sheet->fromArray($footerRow, null, 'A' . $rowNumber);

        // Style the header row
        $sheet->getStyle('A1:' . chr(65 + count($headers) - 1) . '1')->getFont()->setBold(true);
        $sheet->getStyle('A1:' . chr(65 + count($headers) - 1) . '1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('FFFF0000');
        $sheet->getStyle('A1:' . chr(65 + count($headers) - 1) . '1')->getFont()
            ->getColor()
            ->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);

        // Style the footer row
        $sheet->getStyle('A' . $rowNumber . ':' . chr(65 + count($headers) - 1) . $rowNumber)->getFont()->setBold(true);
        $sheet->getStyle('A' . $rowNumber . ':' . chr(65 + count($headers) - 1) . $rowNumber)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('FFFF0000');
        $sheet->getStyle('A' . $rowNumber . ':' . chr(65 + count($headers) - 1) . $rowNumber)->getFont()
            ->getColor()
            ->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);

        // Auto-size columns
        foreach (range('A', chr(65 + count($headers) - 1)) as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Set response headers and output the Excel file
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $fileName = "Report_{$acc_id}_" . ($yearFilter ? "Year{$yearFilter}_" : '') . ($workcenterFilter ? "WC{$workcenterFilter}_" : '') . ($budgetFilter ? "BC{$budgetFilter}_" : '') . now()->format('Ymd') . ".xlsx";

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function downloadAll($dpt_id, Request $request)
    {
        // Get filter parameters from the request
        $departmentFilter = $request->input('department', '');
        $workcenterFilter = $request->input('workcenter', '');
        $yearFilter = $request->input('year', '');
        $accountFilter = $request->input('account', '');

        // $dpt_id = Auth::user()->dept;

        $currentYear = date('Y');
        // Fetch all accounts
        $accounts = Account::all();

        // Base query for all data
        $query = ['status' => 7, 'dpt_id' => $dpt_id];

        // Apply filters only if they are provided
        if ($workcenterFilter) {
            $query['wct_id'] = $workcenterFilter;
        }
        if ($accountFilter) {
            $query['acc_id'] = $accountFilter;
        }
        if ($departmentFilter) {
            $query['dpt_id'] = $departmentFilter;
        }


        // Fetch data based on filters
        $allData = collect()
            ->merge(BudgetPlan::where($query)->when($yearFilter, function ($q) use ($yearFilter) {
                return $q->whereYear('updated_at', $yearFilter);
            }, function ($q) use ($currentYear) {
                // Default filter: current year when no year filter is selected
                return $q->whereYear('updated_at', $currentYear);
            })->get());

        // Initialize PhpSpreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Define headers
        $headers = [
            'CODE',
            'ACCOUNT/BUDGET',
            'JAN',
            'FEB',
            'MAR',
            'APR',
            'MAY',
            'JUN',
            'JUL',
            'AUG',
            'SEP',
            'OCT',
            'NOV',
            'DEC',
            'Total'
        ];

        // Set headers
        $sheet->fromArray($headers, null, 'A1');

        // Initialize grand totals
        $grandMonthlyTotals = [
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
        $grandTotal = 0;

        // Process data for each account
        $rowNumber = 2; // Start from row 2 (after headers)
        $reports = [];
        foreach ($accounts as $account) {
            // Filter data by acc_id
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

            // Calculate monthly totals
            foreach ($items as $item) {
                $amount = $item->quantity * $item->price;
                $month = strtoupper(substr($item->month, 0, 3));
                if (array_key_exists($month, $monthlyTotals)) {
                    $monthlyTotals[$month] += $amount;
                    $total += $amount;
                }
            }

            // Include all accounts when no filters are applied
            if (!$workcenterFilter && !$yearFilter && !$accountFilter && !$departmentFilter) {
                $reports[] = (object)[
                    'acc_id' => $account->acc_id,
                    'account' => $account->account,
                    'monthly_totals' => $monthlyTotals,
                    'total' => $total
                ];
            } else {
                // Only include accounts with non-zero totals when filters are applied
                if ($total > 0) {
                    $reports[] = (object)[
                        'acc_id' => $account->acc_id,
                        'account' => $account->account,
                        'monthly_totals' => $monthlyTotals,
                        'total' => $total
                    ];
                }
            }
        }

        // Populate data in the spreadsheet
        foreach ($reports as $report) {
            $rowData = [
                $report->acc_id,
                $report->account,
                $report->monthly_totals['JAN'] > 0 ? number_format($report->monthly_totals['JAN'], 0, ',', '.') : '-',
                $report->monthly_totals['FEB'] > 0 ? number_format($report->monthly_totals['FEB'], 0, ',', '.') : '-',
                $report->monthly_totals['MAR'] > 0 ? number_format($report->monthly_totals['MAR'], 0, ',', '.') : '-',
                $report->monthly_totals['APR'] > 0 ? number_format($report->monthly_totals['APR'], 0, ',', '.') : '-',
                $report->monthly_totals['MAY'] > 0 ? number_format($report->monthly_totals['MAY'], 0, ',', '.') : '-',
                $report->monthly_totals['JUN'] > 0 ? number_format($report->monthly_totals['JUN'], 0, ',', '.') : '-',
                $report->monthly_totals['JUL'] > 0 ? number_format($report->monthly_totals['JUL'], 0, ',', '.') : '-',
                $report->monthly_totals['AUG'] > 0 ? number_format($report->monthly_totals['AUG'], 0, ',', '.') : '-',
                $report->monthly_totals['SEP'] > 0 ? number_format($report->monthly_totals['SEP'], 0, ',', '.') : '-',
                $report->monthly_totals['OCT'] > 0 ? number_format($report->monthly_totals['OCT'], 0, ',', '.') : '-',
                $report->monthly_totals['NOV'] > 0 ? number_format($report->monthly_totals['NOV'], 0, ',', '.') : '-',
                $report->monthly_totals['DEC'] > 0 ? number_format($report->monthly_totals['DEC'], 0, ',', '.') : '-',
                number_format($report->total, 0, ',', '.')
            ];

            // Write row data
            $sheet->fromArray($rowData, null, 'A' . $rowNumber);

            // Update grand totals
            foreach ($report->monthly_totals as $month => $amount) {
                $grandMonthlyTotals[$month] += $amount;
            }
            $grandTotal += $report->total;

            $rowNumber++;
        }

        // Add footer with grand totals
        $footerRow = array_fill(0, count($headers), '');
        $footerRow[0] = 'GRAND TOTAL';
        $footerRow[1] = '';
        $footerRow[2] = number_format($grandMonthlyTotals['JAN'], 0, ',', '.');
        $footerRow[3] = number_format($grandMonthlyTotals['FEB'], 0, ',', '.');
        $footerRow[4] = number_format($grandMonthlyTotals['MAR'], 0, ',', '.');
        $footerRow[5] = number_format($grandMonthlyTotals['APR'], 0, ',', '.');
        $footerRow[6] = number_format($grandMonthlyTotals['MAY'], 0, ',', '.');
        $footerRow[7] = number_format($grandMonthlyTotals['JUN'], 0, ',', '.');
        $footerRow[8] = number_format($grandMonthlyTotals['JUL'], 0, ',', '.');
        $footerRow[9] = number_format($grandMonthlyTotals['AUG'], 0, ',', '.');
        $footerRow[10] = number_format($grandMonthlyTotals['SEP'], 0, ',', '.');
        $footerRow[11] = number_format($grandMonthlyTotals['OCT'], 0, ',', '.');
        $footerRow[12] = number_format($grandMonthlyTotals['NOV'], 0, ',', '.');
        $footerRow[13] = number_format($grandMonthlyTotals['DEC'], 0, ',', '.');
        $footerRow[14] = number_format($grandTotal, 0, ',', '.');

        $sheet->fromArray($footerRow, null, 'A' . $rowNumber);

        // Style the header row
        $sheet->getStyle('A1:O1')->getFont()->setBold(true);
        $sheet->getStyle('A1:O1')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('FFFF0000'); // Red background
        $sheet->getStyle('A1:O1')->getFont()
            ->getColor()
            ->setARGB(Color::COLOR_WHITE);

        // Style the footer row
        $sheet->getStyle('A' . $rowNumber . ':O' . $rowNumber)->getFont()->setBold(true);
        $sheet->getStyle('A' . $rowNumber . ':O' . $rowNumber)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('FFFF0000'); // Red background
        $sheet->getStyle('A' . $rowNumber . ':O' . $rowNumber)->getFont()
            ->getColor()
            ->setARGB(Color::COLOR_WHITE);

        // Auto-size columns
        foreach (range('A', 'O') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Set response headers and output the Excel file
        $writer = new Xlsx($spreadsheet);
        $fileName = "Summary Plan Master Budget" . now()->format('Ymd') . ".xlsx";

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function downloadAllReport(Request $request)
    {
        // Get filter parameters dari request
        $departmentFilter = $request->input('department', '');
        $workcenterFilter = $request->input('workcenter', '');
        $yearFilter = $request->input('year', '');
        $accountFilter = $request->input('account', '');
        $submissionFilter = $request->input('submission', '');

        $currentYear = date('Y');

        // Fetch all accounts
        $accounts = Account::all();

        // Base query - HANYA data dengan status = 7 (approved)
        $query = ['status' => 7];

        // Apply filters hanya jika provided
        if ($workcenterFilter) {
            $query['wct_id'] = $workcenterFilter;
        }
        if ($accountFilter) {
            $query['acc_id'] = $accountFilter;
        }
        if ($departmentFilter) {
            $query['dpt_id'] = $departmentFilter;
        }

        // Filter submission type
        if ($submissionFilter === 'asset') {
            $query['acc_id'] = ['!=', 'CAPEX'];
        } elseif ($submissionFilter === 'expenditure') {
            $query['acc_id'] = 'CAPEX';
        }

        // Fetch data berdasarkan filters dengan status = 7
        $allData = BudgetPlan::where($query)
            ->when($yearFilter, function ($q) use ($yearFilter) {
                return $q->whereYear('updated_at', $yearFilter);
            }, function ($q) use ($currentYear) {
                return $q->whereYear('updated_at', $currentYear);
            })
            ->get();

        // Initialize PhpSpreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set title
        $sheet->setCellValue('A1', 'SUMMARY PLAN MASTER BUDGET');
        $sheet->setCellValue('A2', 'Generated on: ' . now()->format('d F Y H:i:s'));

        // Add filter info jika ada
        $filterInfo = ' ';
        $filters = [];
        if ($yearFilter) $filters[] = "Year: $yearFilter";
        if ($departmentFilter) $filters[] = "Department: $departmentFilter";
        if ($workcenterFilter) $filters[] = "Workcenter: $workcenterFilter";
        if ($accountFilter) $filters[] = "Account: $accountFilter";
        if ($submissionFilter) $filters[] = "Submission: $submissionFilter";

        if (!empty($filters)) {
            $sheet->setCellValue('A3', $filterInfo . implode(', ', $filters));
        }

        // Define headers
        $headers = [
            'CODE',
            'ACCOUNT/BUDGET',
            'JAN',
            'FEB',
            'MAR',
            'APR',
            'MAY',
            'JUN',
            'JUL',
            'AUG',
            'SEP',
            'OCT',
            'NOV',
            'DEC',
            'Total'
        ];

        // Set headers (mulai dari row 5)
        $sheet->fromArray($headers, null, 'A5');

        // Initialize grand totals
        $grandMonthlyTotals = [
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
        $grandTotal = 0;

        // Process data untuk setiap account
        $rowNumber = 6; // Start dari row 6 (setelah headers)
        $reports = [];

        foreach ($accounts as $account) {
            // Skip accounts berdasarkan filter submission
            if ($submissionFilter === 'asset' && $account->acc_id === 'CAPEX') {
                continue;
            } elseif ($submissionFilter === 'expenditure' && $account->acc_id !== 'CAPEX') {
                continue;
            }

            // Filter data by acc_id
            $items = $allData->where('acc_id', $account->acc_id);

            if ($items->isEmpty()) {
                continue; // Skip account yang tidak ada datanya
            }

            // Initialize monthly totals - SUM OF PRICE
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
                // PERBAIKAN: Gunakan SUM dari PRICE saja (sesuai dengan reportAll)
                $amount = $item->price;

                $month = strtoupper(substr($item->month, 0, 3));

                if (array_key_exists($month, $monthlyTotals)) {
                    $monthlyTotals[$month] += $amount;
                    $total += $amount;
                }
            }

            // Tampilkan account jika ada data (total > 0) ATAU jika tidak ada filter
            $shouldDisplay = $total > 0 ||
                (!$workcenterFilter && !$yearFilter && !$accountFilter && !$departmentFilter && !$submissionFilter);

            if ($shouldDisplay) {
                $reports[] = [
                    'acc_id' => $account->acc_id,
                    'account' => $account->account,
                    'monthly_totals' => $monthlyTotals,
                    'total' => $total
                ];
            }
        }

        // Populate data dalam spreadsheet
        foreach ($reports as $report) {
            $rowData = [
                $report['acc_id'],
                $report['account'],
                $report['monthly_totals']['JAN'] > 0 ? number_format($report['monthly_totals']['JAN'], 0, ',', '.') : '-',
                $report['monthly_totals']['FEB'] > 0 ? number_format($report['monthly_totals']['FEB'], 0, ',', '.') : '-',
                $report['monthly_totals']['MAR'] > 0 ? number_format($report['monthly_totals']['MAR'], 0, ',', '.') : '-',
                $report['monthly_totals']['APR'] > 0 ? number_format($report['monthly_totals']['APR'], 0, ',', '.') : '-',
                $report['monthly_totals']['MAY'] > 0 ? number_format($report['monthly_totals']['MAY'], 0, ',', '.') : '-',
                $report['monthly_totals']['JUN'] > 0 ? number_format($report['monthly_totals']['JUN'], 0, ',', '.') : '-',
                $report['monthly_totals']['JUL'] > 0 ? number_format($report['monthly_totals']['JUL'], 0, ',', '.') : '-',
                $report['monthly_totals']['AUG'] > 0 ? number_format($report['monthly_totals']['AUG'], 0, ',', '.') : '-',
                $report['monthly_totals']['SEP'] > 0 ? number_format($report['monthly_totals']['SEP'], 0, ',', '.') : '-',
                $report['monthly_totals']['OCT'] > 0 ? number_format($report['monthly_totals']['OCT'], 0, ',', '.') : '-',
                $report['monthly_totals']['NOV'] > 0 ? number_format($report['monthly_totals']['NOV'], 0, ',', '.') : '-',
                $report['monthly_totals']['DEC'] > 0 ? number_format($report['monthly_totals']['DEC'], 0, ',', '.') : '-',
                number_format($report['total'], 0, ',', '.')
            ];

            // Write row data
            $sheet->fromArray($rowData, null, 'A' . $rowNumber);

            // Update grand totals
            foreach ($report['monthly_totals'] as $month => $amount) {
                $grandMonthlyTotals[$month] += $amount;
            }
            $grandTotal += $report['total'];

            $rowNumber++;
        }

        // Add footer dengan grand totals
        $footerRow = array_fill(0, count($headers), '');
        $footerRow[0] = 'GRAND TOTAL';
        $footerRow[1] = '';
        $footerRow[2] = number_format($grandMonthlyTotals['JAN'], 0, ',', '.');
        $footerRow[3] = number_format($grandMonthlyTotals['FEB'], 0, ',', '.');
        $footerRow[4] = number_format($grandMonthlyTotals['MAR'], 0, ',', '.');
        $footerRow[5] = number_format($grandMonthlyTotals['APR'], 0, ',', '.');
        $footerRow[6] = number_format($grandMonthlyTotals['MAY'], 0, ',', '.');
        $footerRow[7] = number_format($grandMonthlyTotals['JUN'], 0, ',', '.');
        $footerRow[8] = number_format($grandMonthlyTotals['JUL'], 0, ',', '.');
        $footerRow[9] = number_format($grandMonthlyTotals['AUG'], 0, ',', '.');
        $footerRow[10] = number_format($grandMonthlyTotals['SEP'], 0, ',', '.');
        $footerRow[11] = number_format($grandMonthlyTotals['OCT'], 0, ',', '.');
        $footerRow[12] = number_format($grandMonthlyTotals['NOV'], 0, ',', '.');
        $footerRow[13] = number_format($grandMonthlyTotals['DEC'], 0, ',', '.');
        $footerRow[14] = number_format($grandTotal, 0, ',', '.');

        $sheet->fromArray($footerRow, null, 'A' . $rowNumber);

        // Style the title row
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->mergeCells('A1:O1');
        $sheet->getStyle('A2')->getFont()->setItalic(true);
        if (!empty($filters)) {
            $sheet->getStyle('A3')->getFont()->setItalic(true);
        }

        // Style the header row
        $sheet->getStyle('A5:O5')->getFont()->setBold(true);
        $sheet->getStyle('A5:O5')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('FFFF0000'); // Red background
        $sheet->getStyle('A5:O5')->getFont()
            ->getColor()
            ->setARGB(Color::COLOR_WHITE);

        // Style the footer row
        $sheet->getStyle('A' . $rowNumber . ':O' . $rowNumber)->getFont()->setBold(true);
        $sheet->getStyle('A' . $rowNumber . ':O' . $rowNumber)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('FFFF0000'); // Red background
        $sheet->getStyle('A' . $rowNumber . ':O' . $rowNumber)->getFont()
            ->getColor()
            ->setARGB(Color::COLOR_WHITE);

        // Auto-size columns
        foreach (range('A', 'O') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Set alignment untuk kolom angka
        $sheet->getStyle('C6:O' . $rowNumber)->getAlignment()->setHorizontal('right');

        // Set response headers dan output Excel file
        $writer = new Xlsx($spreadsheet);

        // Generate filename dengan filter info
        $fileName = "Summary_Plan_Master_Budget";
        if ($yearFilter) $fileName .= "_" . $yearFilter;
        if ($departmentFilter) $fileName .= "_Dept" . $departmentFilter;
        if ($workcenterFilter) $fileName .= "_WC" . $workcenterFilter;
        if ($accountFilter) $fileName .= "_Acc" . $accountFilter;
        $fileName .= "_" . now()->format('Ymd_His') . ".xlsx";

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');
        header('Pragma: no-cache');
        header('Expires: 0');

        $writer->save('php://output');
        exit;
    }

    public function downloadDetailReport(Request $request)
    {
        ini_set('max_execution_time', 0);

        // Get filter parameters dari request - SAMA PERSIS DENGAN SUMMARY
        $departmentFilter = $request->input('department', '');
        $workcenterFilter = $request->input('workcenter', '');
        $yearFilter = $request->input('year', '');
        $accountFilter = $request->input('account', '');
        $submissionFilter = $request->input('submission', '');

        $currentYear = date('Y');

        // Base query - HANYA data dengan status = 7 (approved)
        $query = ['status' => 7];

        // Apply filters jika ada - SAMA DENGAN SUMMARY
        if ($workcenterFilter) {
            $query['wct_id'] = $workcenterFilter;
        }
        if ($accountFilter) {
            $query['acc_id'] = $accountFilter;
        }
        if ($departmentFilter) {
            $query['dpt_id'] = $departmentFilter;
        }

        // Filter submission type - SAMA DENGAN SUMMARY
        if ($submissionFilter === 'asset') {
            $query['acc_id'] = ['!=', 'CAPEX'];
        } elseif ($submissionFilter === 'expenditure') {
            $query['acc_id'] = 'CAPEX';
        }

        // Fetch data berdasarkan filters dengan status = 7
        $allData = collect();

        BudgetPlan::where($query)
            ->when($yearFilter, function ($q) use ($yearFilter) {
                return $q->whereYear('updated_at', $yearFilter);
            }, function ($q) use ($currentYear) {
                return $q->whereYear('updated_at', $currentYear);
            })
            ->with([
                'dept',
                'workcenter',
                'budget',
                'line_business',
                'insurance',
                'item',
                'acc',
                'approvals',
            ])
            ->chunk(1000, function ($chunk) use (&$allData) {
                $allData = $allData->merge($chunk);
            });

        // Jika tidak ada data, return error
        if ($allData->isEmpty()) {
            return response()->json([
                'error' => 'Tidak ada data untuk ditampilkan.'
            ], 404);
        }

        // Initialize PhpSpreadsheet
        $spreadsheet = new Spreadsheet();

        // Hapus sheet default
        $spreadsheet->removeSheetByIndex(0);

        $sheets = $this->getSheetTemplates();

        // Mapping acc_id ke template name
        $templateMapping = [
            'CAPEX' => 'CAPEX',
            'SGAADVERT' => 'ADVERTISING & PROMOTION',
            'SGACOM' => 'COMMUNICATION',
            'SGAOFFICESUP' => 'OFFICE SUPPLY',
            'SGAAFTERSALES' => 'AFTER SALES SERVICE',
            'FOHINDMAT' => 'INDIRECT MATERIAL',
            'FOHFS' => 'FACTORY SUPPLY',
            'FOHREPAIR' => 'REPAIR & MAINTENANCE FOH',
            'SGADEPRECIATION' => 'DEPRECIATION OPEX',
            'FOHTOOLS' => 'CONS TOOLS',
            'FOHINSPREM' => 'INSURANCE PREM FOH',
            'SGAINSURANCE' => 'INSURANCE PREM OPEX',
            'FOHTAXPUB' => 'TAX & PUBLIC DUES FOH',
            'SGATAXPUB' => 'TAX & PUBLIC DUES OPEX',
            'FOHPROF' => 'PROFESIONAL FEE FOH',
            'SGAPROF' => 'PROFESIONAL FEE OPEX',
            'FOHAUTOMOBILE' => 'AUTOMOBILE FOH',
            'SGAAUTOMOBILE' => 'AUTOMOBILE OPEX',
            'FOHRENT' => 'RENT EXPENSE FOH',
            'FOHPACKING' => 'PACKING & DELIVERY',
            'SGABCHARGES' => 'BANK CHARGES',
            'SGARYLT' => 'ROYALTY',
            'SGACONTRIBUTION' => 'CONTRIBUTION',
            'SGAASSOCIATION' => 'ASSOCIATION',
            'FOHPOWER' => 'UTILITIES FOH',
            'SGAPOWER' => 'UTILITIES OPEX',
            'FOHTRAV' => 'BUSINESS DUTY FOH',
            'SGATRAV' => 'BUSINESS DUTY OPEX',
            'FOHTRAINING' => 'TRAINING & EDUCATION FOH',
            'SGATRAINING' => 'TRAINING & EDUCATION OPEX',
            'FOHTECHDO' => 'TECHNICAL DEVELOPMENT FOH',
            'FOHRECRUITING' => 'RECRUITMENT FOH',
            'SGARECRUITING' => 'RECRUITMENT OPEX',
            'SGARENT' => 'RENT EXPENSE OPEX',
            'SGAMARKT' => 'MARKETING ACTIVITY',
            'SGAREPAIR' => 'REPAIR & MAINTENANCE OPEX',
            'SGABOOK' => 'BOOK NEWSPAPER',
            'FOHENTERTAINT' => 'ENTERTAINMENT FOH',
            'SGAENTERTAINT' => 'ENTERTAINMENT OPEX',
            'FOHREPRESENTATION' => 'REPRESENTATION FOH',
            'SGAREPRESENTATION' => 'REPRESENTATION OPEX',
            'SGAOUTSOURCING' => 'OUTSOURCING FEE',
            'SGAEMPLOYCOMP' => 'EMPLOYEE COMP SGA',
            'FOHEMPLOYCOMPDL' => 'EMPLOYEE COMP FOHDL',
            'FOHEMPLOYCOMPIL' => 'EMPLOYEE COMP FOHIL',
            'PURCHASEMATERIAL' => 'PURCHASE MATERIAL',
        ];

        // Kelompokkan data berdasarkan account type
        $dataByAccount = $allData->groupBy('acc_id');

        foreach ($dataByAccount as $accountType => $accountData) {
            // Skip accounts berdasarkan filter submission - SAMA DENGAN SUMMARY
            if ($submissionFilter === 'asset' && $accountType === 'CAPEX') {
                continue;
            } elseif ($submissionFilter === 'expenditure' && $accountType !== 'CAPEX') {
                continue;
            }

            // Buat sheet baru untuk setiap account type
            $sheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet);
            $templateKey = $templateMapping[$accountType] ?? $accountType;

            // Gunakan nama yang lebih pendek untuk sheet (max 31 karakter)
            $sheetName = substr($templateKey, 0, 31);
            $sheet->setTitle($sheetName);
            $spreadsheet->addSheet($sheet);

            $headers = $sheets[$templateKey] ?? throw new Exception("Template untuk $templateKey tidak ditemukan");


            // TAMBAHKAN FILTER INFO DI SETIAP SHEET
            $sheet->setCellValue('A1', 'Filter Information:');
            $filterRow = 2; // Mulai dari row 2

            $filters = [];
            if ($yearFilter) $filters[] = "Year: $yearFilter";
            if ($departmentFilter) $filters[] = "Department: $departmentFilter";
            if ($workcenterFilter) $filters[] = "Workcenter: $workcenterFilter";
            if ($accountFilter) $filters[] = "Account: $accountFilter";
            if ($submissionFilter) $filters[] = "Submission: $submissionFilter";

            if (!empty($filters)) {
                foreach ($filters as $filter) {
                    $sheet->setCellValue('A' . $filterRow++, $filter);
                }
            }

            $headerStartRow = $filterRow + 1; // 1 baris kosong setelah filter info
            $sheet->fromArray($headers, null, 'A' . $headerStartRow);

            $dataStartRow = $headerStartRow + 1; // Mulai data 1 baris setelah header

            // Kelompokkan data berdasarkan key yang unik (sesuai account type)
            $groupedData = [];

            foreach ($accountData as $item) {
                // Tentukan key grouping berdasarkan account type
                if (in_array($accountType, ['FOHINSPREM', 'SGAINSURANCE'])) {
                    $key = $item->description . '|' . $item->ins_id;
                    $groupKey = [
                        'description' => $item->description,
                        'insurance_company' => $item->insurance ? $item->insurance->company_name : $item->ins_id,
                    ];
                } elseif (in_array($accountType, ['FOHREPRESENTATION', 'FOHENTERTAINT', 'SGAENTERTAINT', 'SGAREPRESENTATION'])) {
                    $key = $item->description . '|' . ($item->beneficiary ?? '') . '|' . ($item->itm_id ?? '');
                    $groupKey = [
                        'description' => $item->description,
                        'beneficiary' => $item->beneficiary ?? '-',
                        'item' => $item->itm_id ?? '-',
                    ];
                } elseif ($accountType === 'SGAAFTERSALES') {
                    $key = $item->description . '|' . ($item->customer ?? '') . '|' . ($item->itm_id ?? '');
                    $groupKey = [
                        'description' => $item->description,
                        'customer' => $item->customer ?? '-',
                        'item' => $item->itm_id ?? '-',
                    ];
                } elseif (in_array($accountType, ['FOHPOWER', 'SGAPOWER'])) {
                    $key = $item->description . '|' . ($item->itm_id ?? '') . '|' . ($item->kwh ?? '') . '|' . ($item->lob_id ?? '');
                    $groupKey = [
                        'description' => $item->description,
                        'item' => $item->itm_id ?? '-',
                        'kwh' => $item->kwh ?? '-',
                        'line_business' => $item->line_business ? $item->line_business->line_of_business : ($item->lob_id ?? '-'),
                    ];
                } elseif ($accountType === 'SGADEPRECIATION') {
                    $key = $item->description . '|' . ($item->itm_id ?? '');
                    $groupKey = [
                        'description' => $item->description,
                        'item' => $item->itm_id ?? '-',
                    ];
                } elseif (in_array($accountType, ['FOHTRAV', 'SGATRAV'])) {
                    $key = $item->description . '|' . ($item->trip_propose ?? '') . '|' . ($item->destination ?? '') . '|' . ($item->days ?? '');
                    $groupKey = [
                        'description' => $item->description,
                        'trip_propose' => $item->trip_propose ?? '-',
                        'destination' => $item->destination ?? '-',
                        'days' => $item->days ?? '-',
                    ];
                } elseif (in_array($accountType, ['FOHTRAINING', 'SGATRAINING'])) {
                    $key = $item->description . '|' . ($item->participant ?? '') . '|' . ($item->jenis_training ?? '') . '|' . ($item->quantity ?? '') . '|' . ($item->price ?? '');
                    $groupKey = [
                        'description' => $item->description,
                        'participant' => $item->participant ?? '-',
                        'jenis_training' => $item->jenis_training ?? '-',
                        'quantity' => $item->quantity ?? '-',
                        'price' => $item->price ?? '-',
                    ];
                } elseif (in_array($accountType, ['FOHRECRUITING', 'SGARECRUITING'])) {
                    $key = $item->description . '|' . ($item->position ?? '') . '|' . ($item->itm_id ?? '') . '|' . ($item->price ?? '');
                    $groupKey = [
                        'description' => $item->description,
                        'position' => $item->position ?? '-',
                        'item' => $item->itm_id ?? '-',
                        'price' => $item->price ?? '-',
                    ];
                } elseif (in_array($accountType, ['SGAEMPLOYCOMP', 'FOHEMPLOYCOMPDL', 'FOHEMPLOYCOMPIL'])) {
                    $key = ($item->ledger_account_description ?? '') . '|' . ($item->ledger_account ?? '') . '|' . $item->wct_id . '|' . $item->lob_id . '|' . $item->bdc_id;
                    $groupKey = [
                        'ledger_account' => $item->ledger_account ?? '-',
                        'ledger_account_description' => $item->ledger_account_description ?? '-',
                    ];
                } elseif (in_array($accountType, ['FOHREPAIR', 'SGAREPAIR'])) {
                    $key = ($item->itm_id ?? '') . '|' . $item->description . '|' . $item->wct_id;
                    $groupKey = [
                        'item' => $item->itm_id ?? '-',
                        'description' => $item->description,
                        'workcenter' => $item->workcenter ? $item->workcenter->workcenter : '-',
                    ];
                } elseif ($accountType === 'PURCHASEMATERIAL') {
                    $key = ($item->itm_id ?? '') . '|' . $item->description . '|' . $item->wct_id . '|' . ($item->business_partner ?? '');
                    $groupKey = [
                        'item' => $item->itm_id ?? '-',
                        'description' => $item->description,
                        'workcenter' => $item->workcenter ? $item->workcenter->workcenter : '-',
                        'business_partner' => $item->business_partner ?? '-',
                    ];
                } elseif ($accountType === 'CAPEX') {
                    // Debug: cek apakah asset_class ada


                    $key = ($item->itm_id ?? '') . '|' . $item->asset_class . '|' . $item->description . '|' . $item->wct_id;
                    $groupKey = [
                        'item' => $item->itm_id ?? '-',
                        'asset_class' => $item->asset_class ?? '-',
                        'prioritas' => $item->prioritas ?? '-',
                        'keterangan' => $item->keterangan ?? '-',
                        'workcenter' => $item->workcenter ? $item->workcenter->workcenter : '-',
                        'department' => $item->dept ? $item->dept->department : '-',
                        'quantity' => $item->quantity ?? '-',
                        'price' => $item->price ?? '-',
                        'budget_code' => $item->budget ? $item->budget->bdc_id : ($item->bdc_id ?? '-'),
                        'line_business' => $item->line_business ? $item->line_business->line_of_business : ($item->lob_id ?? '-'),
                        'business_partner' => $item->business_partner ?? '-',
                        'alasan' => $item->alasan ?? '-',
                    ];
                } else {
                    $key = ($item->itm_id ?? '') . '|' . $item->description . '|' . $item->wct_id;
                    $groupKey = [
                        'item' => $item->itm_id ?? '-',
                        'description' => $item->description,
                        'workcenter' => $item->workcenter ? $item->workcenter->workcenter : '-',
                    ];
                }

                if (!isset($groupedData[$key])) {
                    $groupedData[$key] = array_merge($groupKey, [
                        'workcenter' => $item->workcenter ? $item->workcenter->workcenter : '-',
                        'department' => $item->dept ? $item->dept->department : '-',
                        'budget_code' => $item->budget ? $item->budget->bdc_id : ($item->bdc_id ?? '-'), // R/NR
                        'line_business' => $item->line_business ? $item->line_business->line_of_business : ($item->lob_id ?? '-'), // Line Of Business
                        'months' => [
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
                            'DEC' => 0
                        ],
                        'total' => 0
                    ]);
                }

                // Tentukan bulan dari field 'month'
                $monthValue = $item->month ?: date('M', strtotime($item->created_at));
                $month = strtoupper(substr($monthValue, 0, 3));

                // Gunakan PRICE untuk nilai bulanan
                if (isset($groupedData[$key]['months'][$month])) {
                    $groupedData[$key]['months'][$month] += $item->price ?? 0;
                    $groupedData[$key]['total'] += $item->price ?? 0;
                }
            }

            // Tulis data ke sheet - MULAI DARI dataStartRow
            $rowNumber = $dataStartRow;
            $no = 1;


            foreach ($groupedData as $data) {
                $rowData = [$no++];

                // Loop melalui headers dan tambahkan data yang sesuai
                foreach ($headers as $header) {
                    if (in_array($header, [
                        'No',
                        'Jan',
                        'Feb',
                        'Mar',
                        'Apr',
                        'May',
                        'Jun',
                        'Jul',
                        'Aug',
                        'Sep',
                        'Oct',
                        'Nov',
                        'Dec',
                        'Total'
                    ])) {
                        continue;
                    }

                    // Map header ke field data berdasarkan account type
                    switch ($header) {
                        case 'Asset Class':
                            $rowData[] = $data['asset_class'] ?? '-';
                            break;
                        case 'Prioritas':
                            $rowData[] = $data['prioritas'] ?? '-';
                            break;
                        case 'Alasan':
                            $rowData[] = $data['alasan'] ?? '-';
                            break;
                        case 'Keterangan':
                            $rowData[] = $data['keterangan'] ?? '-';
                            break;
                        case 'Description':
                            $rowData[] = $data['description'] ?? '-';
                            break;
                        case 'Insurance Company':
                            $rowData[] = $data['insurance_company'] ?? '-';
                            break;
                        case 'Beneficiary':
                            $rowData[] = $data['beneficiary'] ?? '-';
                            break;
                        case 'Item':
                            $rowData[] = $data['item'] ?? '-';
                            break;
                        case 'Customer':
                            $rowData[] = $data['customer'] ?? '-';
                            break;
                        case 'KWH (Used)':
                            $rowData[] = $data['kwh'] ?? '-';
                            break;
                        case 'Line of Business':
                            $rowData[] = $data['line_business'] ?? '-';
                            break;
                        case 'Trip Propose':
                            $rowData[] = $data['trip_propose'] ?? '-';
                            break;
                        case 'Destination':
                            $rowData[] = $data['destination'] ?? '-';
                            break;
                        case 'Days':
                            $rowData[] = $data['days'] ?? '-';
                            break;
                        case 'Participant':
                            $rowData[] = $data['participant'] ?? '-';
                            break;
                        case 'Jenis Training':
                            $rowData[] = $data['jenis_training'] ?? '-';
                            break;
                        case 'Quantity':
                            $rowData[] = $data['quantity'] ?? '-';
                            break;
                        case 'Price':
                            $rowData[] = $data['price'] ?? '-';
                            break;
                        case 'Position':
                            $rowData[] = $data['position'] ?? '-';
                            break;
                        case 'Ledger Account':
                            $rowData[] = $data['ledger_account'] ?? '-';
                            break;
                        case 'Ledger Account Description':
                            $rowData[] = $data['ledger_account_description'] ?? '-';
                            break;
                        case 'Business Partner':
                            $rowData[] = $data['business_partner'] ?? '-';
                            break;
                        case 'Workcenter':
                            $rowData[] = $data['workcenter'] ?? '-';
                            break;
                        case 'Department':
                            $rowData[] = $data['department'] ?? '-';
                            break;
                        case 'R/NR':
                            $rowData[] = $data['budget_code'] ?? '-';
                            break;
                        case 'Line Of Business':
                            $rowData[] = $data['line_business'] ?? '-';
                            break;
                        default:
                            $rowData[] = '-';
                            break;
                    }
                }

                // Tambahkan data bulanan
                $rowData[] = $data['months']['JAN'] > 0 ? $data['months']['JAN'] : '';
                $rowData[] = $data['months']['FEB'] > 0 ? $data['months']['FEB'] : '';
                $rowData[] = $data['months']['MAR'] > 0 ? $data['months']['MAR'] : '';
                $rowData[] = $data['months']['APR'] > 0 ? $data['months']['APR'] : '';
                $rowData[] = $data['months']['MAY'] > 0 ? $data['months']['MAY'] : '';
                $rowData[] = $data['months']['JUN'] > 0 ? $data['months']['JUN'] : '';
                $rowData[] = $data['months']['JUL'] > 0 ? $data['months']['JUL'] : '';
                $rowData[] = $data['months']['AUG'] > 0 ? $data['months']['AUG'] : '';
                $rowData[] = $data['months']['SEP'] > 0 ? $data['months']['SEP'] : '';
                $rowData[] = $data['months']['OCT'] > 0 ? $data['months']['OCT'] : '';
                $rowData[] = $data['months']['NOV'] > 0 ? $data['months']['NOV'] : '';
                $rowData[] = $data['months']['DEC'] > 0 ? $data['months']['DEC'] : '';
                $rowData[] = $data['total'] > 0 ? $data['total'] : '';


                $sheet->fromArray($rowData, null, 'A' . $rowNumber);
                $rowNumber++;
            }

            // Jika tidak ada data di sheet ini, tambahkan pesan
            if ($rowNumber == $dataStartRow) {
                $sheet->setCellValue('A' . $dataStartRow, 'Tidak ada data untuk ditampilkan');
                $lastColumn = $sheet->getHighestColumn();
                $sheet->mergeCells('A' . $dataStartRow . ':' . $lastColumn . $dataStartRow);
                $sheet->getStyle('A' . $dataStartRow)->getAlignment()->setHorizontal('center');
            }

            // Auto-size kolom
            $lastColumn = $sheet->getHighestColumn();
            foreach (range('A', $lastColumn) as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }

            // Set alignment untuk kolom angka
            $dataRowCount = max(1, $rowNumber - 1);
            $sheet->getStyle('F' . $dataStartRow . ':' . $lastColumn . $dataRowCount)->getAlignment()->setHorizontal('right');

            // Add borders untuk data
            $sheet->getStyle('A' . ($dataStartRow - 1) . ':' . $lastColumn . $dataRowCount)->getBorders()
                ->getAllBorders()
                ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

            // Style header data
            $sheet->getStyle('A' . ($dataStartRow - 1) . ':' . $lastColumn . ($dataStartRow - 1))->getFont()->setBold(true);
            $sheet->getStyle('A' . ($dataStartRow - 1) . ':' . $lastColumn . ($dataStartRow - 1))->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB('FFE6E6FA');
        }

        // Set sheet pertama sebagai aktif (Filter Info sheet)
        $spreadsheet->setActiveSheetIndex(0);

        // Generate filename dengan filter info - SAMA DENGAN SUMMARY
        $fileName = "Detail_Plan_Master_Budget";
        if ($yearFilter) $fileName .= "_" . $yearFilter;
        if ($departmentFilter) $fileName .= "_Dept" . $departmentFilter;
        if ($workcenterFilter) $fileName .= "_WC" . $workcenterFilter;
        if ($accountFilter) $fileName .= "_Acc" . $accountFilter;
        if ($submissionFilter) $fileName .= "_" . $submissionFilter;
        $fileName .= "_" . now()->format('Ymd_His') . ".xlsx";

        // Set response headers dan output Excel file
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');
        header('Pragma: no-cache');
        header('Expires: 0');

        $writer->save('php://output');
        exit;
    }

    public function downloadReportSect(Request $request)
    {
        // Get filter parameters from the request
        $workcenterFilter = $request->input('workcenter', '');
        $yearFilter = $request->input('year', '');
        $accountFilter = $request->input('account', '');
        $currentYear = date('Y');
        // Fetch all accounts
        $accounts = Account::all();

        // Base query for all data
        $query = ['status' => 7];

        // Apply filters only if they are provided
        if ($workcenterFilter) {
            $query['wct_id'] = $workcenterFilter;
        }
        if ($accountFilter) {
            $query['acc_id'] = $accountFilter;
        }

        // Fetch data based on filters
        $allData = collect()
            ->merge(BudgetPlan::where($query)->when($yearFilter, function ($q) use ($yearFilter) {
                return $q->whereYear('updated_at', $yearFilter);
            }, function ($q) use ($currentYear) {
                // Default filter: current year when no year filter is selected
                return $q->whereYear('updated_at', $currentYear);
            })->get());

        // Initialize PhpSpreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Define headers
        $headers = [
            'CODE',
            'ACCOUNT/BUDGET',
            'JAN',
            'FEB',
            'MAR',
            'APR',
            'MAY',
            'JUN',
            'JUL',
            'AUG',
            'SEP',
            'OCT',
            'NOV',
            'DEC',
            'Total'
        ];

        // Set headers
        $sheet->fromArray($headers, null, 'A1');

        // Initialize grand totals
        $grandMonthlyTotals = [
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
        $grandTotal = 0;

        // Process data for each account
        $rowNumber = 2; // Start from row 2 (after headers)
        $reports = [];
        foreach ($accounts as $account) {
            // Filter data by acc_id
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

            // Calculate monthly totals
            foreach ($items as $item) {
                $amount = $item->quantity * $item->price;
                $month = strtoupper(substr($item->month, 0, 3));
                if (array_key_exists($month, $monthlyTotals)) {
                    $monthlyTotals[$month] += $amount;
                    $total += $amount;
                }
            }

            // Include all accounts when no filters are applied
            if (!$workcenterFilter && !$yearFilter && !$accountFilter) {
                $reports[] = (object)[
                    'acc_id' => $account->acc_id,
                    'account' => $account->account,
                    'monthly_totals' => $monthlyTotals,
                    'total' => $total
                ];
            } else {
                // Only include accounts with non-zero totals when filters are applied
                if ($total > 0) {
                    $reports[] = (object)[
                        'acc_id' => $account->acc_id,
                        'account' => $account->account,
                        'monthly_totals' => $monthlyTotals,
                        'total' => $total
                    ];
                }
            }
        }

        // Populate data in the spreadsheet
        foreach ($reports as $report) {
            $rowData = [
                $report->acc_id,
                $report->account,
                $report->monthly_totals['JAN'] > 0 ? number_format($report->monthly_totals['JAN'], 0, ',', '.') : '-',
                $report->monthly_totals['FEB'] > 0 ? number_format($report->monthly_totals['FEB'], 0, ',', '.') : '-',
                $report->monthly_totals['MAR'] > 0 ? number_format($report->monthly_totals['MAR'], 0, ',', '.') : '-',
                $report->monthly_totals['APR'] > 0 ? number_format($report->monthly_totals['APR'], 0, ',', '.') : '-',
                $report->monthly_totals['MAY'] > 0 ? number_format($report->monthly_totals['MAY'], 0, ',', '.') : '-',
                $report->monthly_totals['JUN'] > 0 ? number_format($report->monthly_totals['JUN'], 0, ',', '.') : '-',
                $report->monthly_totals['JUL'] > 0 ? number_format($report->monthly_totals['JUL'], 0, ',', '.') : '-',
                $report->monthly_totals['AUG'] > 0 ? number_format($report->monthly_totals['AUG'], 0, ',', '.') : '-',
                $report->monthly_totals['SEP'] > 0 ? number_format($report->monthly_totals['SEP'], 0, ',', '.') : '-',
                $report->monthly_totals['OCT'] > 0 ? number_format($report->monthly_totals['OCT'], 0, ',', '.') : '-',
                $report->monthly_totals['NOV'] > 0 ? number_format($report->monthly_totals['NOV'], 0, ',', '.') : '-',
                $report->monthly_totals['DEC'] > 0 ? number_format($report->monthly_totals['DEC'], 0, ',', '.') : '-',
                number_format($report->total, 0, ',', '.')
            ];

            // Write row data
            $sheet->fromArray($rowData, null, 'A' . $rowNumber);

            // Update grand totals
            foreach ($report->monthly_totals as $month => $amount) {
                $grandMonthlyTotals[$month] += $amount;
            }
            $grandTotal += $report->total;

            $rowNumber++;
        }

        // Add footer with grand totals
        $footerRow = array_fill(0, count($headers), '');
        $footerRow[0] = 'GRAND TOTAL';
        $footerRow[1] = '';
        $footerRow[2] = number_format($grandMonthlyTotals['JAN'], 0, ',', '.');
        $footerRow[3] = number_format($grandMonthlyTotals['FEB'], 0, ',', '.');
        $footerRow[4] = number_format($grandMonthlyTotals['MAR'], 0, ',', '.');
        $footerRow[5] = number_format($grandMonthlyTotals['APR'], 0, ',', '.');
        $footerRow[6] = number_format($grandMonthlyTotals['MAY'], 0, ',', '.');
        $footerRow[7] = number_format($grandMonthlyTotals['JUN'], 0, ',', '.');
        $footerRow[8] = number_format($grandMonthlyTotals['JUL'], 0, ',', '.');
        $footerRow[9] = number_format($grandMonthlyTotals['AUG'], 0, ',', '.');
        $footerRow[10] = number_format($grandMonthlyTotals['SEP'], 0, ',', '.');
        $footerRow[11] = number_format($grandMonthlyTotals['OCT'], 0, ',', '.');
        $footerRow[12] = number_format($grandMonthlyTotals['NOV'], 0, ',', '.');
        $footerRow[13] = number_format($grandMonthlyTotals['DEC'], 0, ',', '.');
        $footerRow[14] = number_format($grandTotal, 0, ',', '.');

        $sheet->fromArray($footerRow, null, 'A' . $rowNumber);

        // Style the header row
        $sheet->getStyle('A1:O1')->getFont()->setBold(true);
        $sheet->getStyle('A1:O1')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('FFFF0000'); // Red background
        $sheet->getStyle('A1:O1')->getFont()
            ->getColor()
            ->setARGB(Color::COLOR_WHITE);

        // Style the footer row
        $sheet->getStyle('A' . $rowNumber . ':O' . $rowNumber)->getFont()->setBold(true);
        $sheet->getStyle('A' . $rowNumber . ':O' . $rowNumber)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('FFFF0000'); // Red background
        $sheet->getStyle('A' . $rowNumber . ':O' . $rowNumber)->getFont()
            ->getColor()
            ->setARGB(Color::COLOR_WHITE);

        // Auto-size columns
        foreach (range('A', 'O') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Set response headers and output the Excel file
        $writer = new Xlsx($spreadsheet);
        $fileName = "Summary_Report_" . now()->format('Ymd') . ".xlsx";

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $itm_id) {}
}
