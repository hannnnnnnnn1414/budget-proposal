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
    // DepartmentController.php
    public function index()
    {
        $notificationController = new NotificationController();
        $notifications = $notificationController->getNotifications();

        // Ambil semua department dengan status 1
        $departments = Departments::where('status', 1)->get();

        // Ambil semua budget plans
        $allBudgetPlans = BudgetPlan::with('account')
            ->orderBy('created_at', 'desc')
            ->get();

        // Group manual by sub_id
        $groupedPlans = [];
        foreach ($allBudgetPlans as $plan) {
            $subId = $plan->sub_id;
            if (!isset($groupedPlans[$subId])) {
                $groupedPlans[$subId] = (object) [
                    'id' => $plan->id,
                    'sub_id' => $plan->sub_id,
                    'dpt_id' => $plan->dpt_id,
                    'purpose' => $plan->purpose,
                    'status' => $plan->status,
                    'created_at' => $plan->created_at,
                    'item_count' => 0,
                    'total_amount' => 0,
                    'original' => $plan
                ];
            }

            $groupedPlans[$subId]->item_count++;
            // Pastikan amount tidak null sebelum dijumlahkan
            $groupedPlans[$subId]->total_amount += $plan->amount ?? 0;
        }

        $budgetPlans = collect($groupedPlans)->values();

        $perPage = 10;
        $currentPage = request()->get('page', 1);
        $pagedData = $budgetPlans->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $budgetPlans = new \Illuminate\Pagination\LengthAwarePaginator(
            $pagedData,
            $budgetPlans->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('departments.index', [
            'departments' => $departments,
            'budgetPlans' => $budgetPlans,
            'notifications' => $notifications
        ]);
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
        $sub_id = $request->query('sub_id');


        // Tambah kondisi untuk dept 4131 dan 4111
        if ($sect == 'PIC' && in_array($dept, ['6121', '4131', '4111'])) {
            // $status = [1, 5, 6, 7, 11]; // Tambah status=1 biar match sama upload
            $status = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]; // Tambah status=1 biar match sama upload
        } elseif ($sect == 'Kadept' && in_array($dept, ['6121', '4131', '4111'])) {
            $status = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
        }

        if ($status === null) {
            return view('departments.detail', ['approvals' => collect()]);
        }

        // $subIds = Approval::where('status', $status)->pluck('sub_id');
        $subIds = Approval::whereIn('status', $status)->pluck('sub_id');

        $budgetPlans = BudgetPlan::select('sub_id', 'status', 'purpose')
            ->whereIn('sub_id', $subIds)
            ->where('dpt_id', $dpt_id)
            ->where('status', '!=', 0);

        if ($sub_id) {
            $budgetPlans->where('sub_id', $sub_id);
        }

        if ($acc_id && $acc_id !== 'all') {
            $budgetPlans->where('acc_id', $acc_id);
        }


        $budgetPlans = $budgetPlans->groupBy('sub_id', 'status', 'purpose', 'acc_id')
            ->get();

        $approvals = collect($budgetPlans);

        return view('departments.detail', compact('approvals', 'notifications', 'acc_id', 'dpt_id', 'sub_id'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // $templates = Template::orderBy('template', 'asc')->get()->pluck('template', 'tmp_id');

        // return view('departments.create', ['templates' => $templates]);
    }

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
