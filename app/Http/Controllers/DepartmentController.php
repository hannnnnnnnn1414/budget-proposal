<?php

namespace App\Http\Controllers;

use App\Models\BookNewspaper;
use App\Models\BusinessDuty;
use App\Models\GeneralExpense;
use App\Models\InsurancePrem;
use App\Models\OfficeOperation;
use App\Models\OperationalSupport;
use App\Models\RepairMaint;
use App\Models\RepresentationExpense;
use App\Models\SupportMaterial;
use App\Models\TrainingEducation;
use App\Models\Utilities;
use App\Models\Account;
use App\Models\AfterSalesService;
use App\Models\Approval;
use App\Models\BudgetPlan;
use Illuminate\Http\Request;
use App\Models\Departments;
use App\Models\Item;
use App\Models\Template;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Support\Facades\Auth as FacadesAuth;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $notificationController = new NotificationController();
        $notifications = $notificationController->getNotifications();
        $departments = Departments::where('status', 1)->get();
        return view('departments.index', ['departments' => $departments, 'notifications' => $notifications]);
    }


    public function detail($dpt_id, Request $request)
    {
        $notificationController = new NotificationController();
        $notifications = $notificationController->getNotifications();
        $sect = session('sect');
        $dept = session('dept');
        $status = null;
        
            // Get account_id and year from request
        $acc_id = $request->query('acc_id');


        // Tambah kondisi untuk dept 4131 dan 4111
        if ($sect == 'PIC' && in_array($dept, ['6121', '4131', '4111'])) {
            $status = [1, 5, 6, 7, 11]; // Tambah status=1 biar match sama upload
        } elseif ($sect == 'Kadept' && in_array($dept, ['6121', '4131', '4111'])) {
            $status = [1, 6, 7, 12];
        }

        if ($status === null) {
            return view('departments.detail', ['approvals' => collect()]);
        }

        $subIds = Approval::where('status', $status)->pluck('sub_id');

        // Fetch approvals based on the determined status
        // $generalExpenses = GeneralExpense::select('sub_id', 'status', 'purpose')
        //     ->whereIn('sub_id', $subIds)
        //     ->where('dpt_id', $dpt_id)
        //     ->groupBy('sub_id', 'status', 'purpose')
        //     ->get();

        // $supportMaterials = SupportMaterial::select('sub_id', 'status', 'purpose')
        //     ->whereIn('sub_id', $subIds)
        //     ->where('dpt_id', $dpt_id)
        //     ->groupBy('sub_id', 'status', 'purpose')
        //     ->get();

        // $representationExpenses = RepresentationExpense::select('sub_id', 'status', 'purpose')
        //     ->whereIn('sub_id', $subIds)
        //     ->where('dpt_id', $dpt_id)
        //     ->groupBy('sub_id', 'status', 'purpose')
        //     ->get();

        // $insurancePrem = InsurancePrem::select('sub_id', 'status', 'purpose')
        //     ->whereIn('sub_id', $subIds)
        //     ->where('dpt_id', $dpt_id)
        //     ->groupBy('sub_id', 'status', 'purpose')
        //     ->get();

        // $utilities = Utilities::select('sub_id', 'status', 'purpose')
        //     ->whereIn('sub_id', $subIds)
        //     ->where('dpt_id', $dpt_id)
        //     ->groupBy('sub_id', 'status', 'purpose')
        //     ->get();

        // $businessDuty = BusinessDuty::select('sub_id', 'status', 'purpose')
        //     ->whereIn('sub_id', $subIds)
        //     ->where('dpt_id', $dpt_id)
        //     ->groupBy('sub_id', 'status', 'purpose')
        //     ->get();

        // $trainingEducation = TrainingEducation::select('sub_id', 'status', 'purpose')
        //     ->whereIn('sub_id', $subIds)
        //     ->where('dpt_id', $dpt_id)
        //     ->groupBy('sub_id', 'status', 'purpose')
        //     ->get();

        // $afterSalesService = AfterSalesService::select('sub_id', 'status', 'purpose')
        //     ->whereIn('sub_id', $subIds)
        //     ->where('dpt_id', $dpt_id)
        //     ->groupBy('sub_id', 'status', 'purpose')
        //     ->get();

        $budgetPlans = BudgetPlan::select('sub_id', 'status', 'purpose')
            ->whereIn('sub_id', $subIds)
            ->where('dpt_id', $dpt_id)
            ->where('status', '!=', 0)
            ->groupBy('sub_id', 'status', 'purpose');
        if ($acc_id && $acc_id !== 'all') {
            $budgetPlans->where('acc_id', $acc_id);
        }

        
        $budgetPlans = $budgetPlans->groupBy('sub_id', 'status', 'purpose', 'acc_id')
                ->get();

        $approvals = collect($budgetPlans);
        // Gabungkan semua data hasil query
        // $approvals = collect()
        //     ->merge($generalExpenses)
        //     ->merge($supportMaterials)
        //     ->merge($representationExpenses)
        //     ->merge($insurancePrem)
        //     ->merge($utilities)
        //     ->merge($businessDuty)
        //     ->merge($trainingEducation)
        //     ->merge($afterSalesService);

        return view('departments.detail', compact('approvals', 'notifications', 'acc_id', 'dpt_id'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // $templates = Template::orderBy('template', 'asc')->get()->pluck('template', 'tmp_id');

        // return view('departments.create', ['templates' => $templates]);
    }

    // public function template($id)
    // {
    //     $items = Item::orderBy('item', 'asc')->get()->pluck('item', 'itm_id');
    //     $departments = Departments::orderBy('department', 'asc')->get()->pluck('department', 'dpt_id');

    //     if (!$template) {
    //         return response()->json(['message' => 'Template not found'], 404);
    //     }

    //     switch ($template->tmp_id) {
    //         case 'TMP001':
    //             return view('templates.ads', ['items' => $items, 'departments' => $departments]);
    //         case 'TMP002':
    //             return view('templates.aftersales', ['items' => $items]);
    //         case 'TMP003':
    //             return view('templates.assoc', ['items' => $items]);
    //         case 'TMP004':
    //             return view('templates.bank', ['items' => $items]);
    //         case 'TMP005':
    //             return view('templates.book', ['items' => $items]);
    //         case 'TMP006':
    //             return view('templates.comm', ['items' => $items]);
    //         case 'TMP007':
    //             return view('templates.contrib', ['items' => $items]);
    //         case 'TMP008':
    //             return view('templates.tools', ['items' => $items]);
    //         case 'TMP009':
    //             return view('templates.supply', ['items' => $items]);
    //         case 'TMP010':
    //             return view('templates.imaterial', ['items' => $items]);
    //         case 'TMP011':
    //             return view('templates.marketing', ['items' => $items]);
    //         case 'TMP012':
    //             return view('templates.packing', ['items' => $items]);
    //         case 'TMP013':
    //             return view('templates.royalty', ['items' => $items]);
    //         case 'TMP014':
    //             return view('templates.techdev', ['items' => $items]);
    //         case 'TMP015':
    //             return view('templates.automobile', ['items' => $items]);
    //         case 'TMP016':
    //             return view('templates.business', ['items' => $items]);
    //         case 'TMP017':
    //             return view('templates.entertain', ['items' => $items]);
    //         case 'TMP018':
    //             return view('templates.insurance', ['items' => $items]);
    //         case 'TMP019':
    //             return view('templates.profee', ['items' => $items]);
    //         case 'TMP020':
    //             return view('templates.recruitment', ['items' => $items]);
    //         case 'TMP021':
    //             return view('templates.repair', ['items' => $items]);
    //         case 'TMP022':
    //             return view('templates.rent', ['items' => $items]);
    //         case 'TMP023':
    //             return view('templates.representation', ['items' => $items]);
    //         case 'TMP024':
    //             return view('templates.training', ['items' => $items]);
    //         case 'TMP025':
    //             return view('templates.tax', ['items' => $items]);
    //         case 'TMP026':
    //             return view('templates.utilities', ['items' => $items]);
    //         case 'TMP027':
    //             return view('templates.automobile', ['items' => $items]);
    //         case 'TMP028':
    //             return view('templates.business', ['items' => $items]);
    //         case 'TMP029':
    //             return view('templates.entertain', ['items' => $items]);
    //         case 'TMP030':
    //             return view('templates.insurance', ['items' => $items]);
    //         case 'TMP031':
    //             return view('templates.profee', ['items' => $items]);
    //         case 'TMP032':
    //             return view('templates.recruitment', ['items' => $items]);
    //         case 'TMP033':
    //             return view('templates.repair', ['items' => $items]);
    //         case 'TMP034':
    //             return view('templates.rent', ['items' => $items]);
    //         case 'TMP035':
    //             return view('templates.representation', ['items' => $items]);
    //         case 'TMP036':
    //             return view('templates.training', ['items' => $items]);
    //         case 'TMP037':
    //             return view('templates.tax', ['items' => $items]);
    //         case 'TMP038':
    //             return view('templates.utilities', ['items' => $items]);
    //         case 'TMP039':
    //             return view('templates.supply', ['items' => $items]);
    //         default:
    //             return "<div class='alert alert-info'>No template preview available for this selection.</div>";
    //     }
    // }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
    public function destroy(string $id)
    {
        //
    }
}
