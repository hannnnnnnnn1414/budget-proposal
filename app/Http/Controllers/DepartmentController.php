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
    public function index()
    {
        $notificationController = new NotificationController();
        $notifications = $notificationController->getNotifications();

        $departments = Departments::where('status', 1)->get();

        $allBudgetPlans = BudgetPlan::with('account')
            ->orderBy('created_at', 'desc')
            ->get();

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

        $acc_id = $request->query('acc_id');
        $sub_id = $request->query('sub_id');

        if ($sect == 'PIC' && in_array($dept, ['6121', '4131', '4111'])) {
            $status = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
        } elseif ($sect == 'Kadept' && in_array($dept, ['6121', '4131', '4111'])) {
            $status = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
        }

        if ($status === null) {
            return view('departments.detail', ['approvals' => collect()]);
        }

        $budgetPlans = BudgetPlan::select('sub_id', 'status', 'purpose')
            ->where('dpt_id', $dpt_id)
            ->where('status', '!=', 0)
            ->whereIn('status', $status);

        if ($sub_id) {
            $budgetPlans->where('sub_id', $sub_id);
        }

        if ($acc_id && $acc_id !== 'all') {
            $budgetPlans->where('acc_id', $acc_id);
        }

        $budgetPlans = $budgetPlans->groupBy('sub_id', 'status', 'purpose')
            ->get();

        $approvals = collect($budgetPlans);

        return view('departments.detail', compact('approvals', 'notifications', 'acc_id', 'dpt_id', 'sub_id'));
    }

    public function create() {}

    public function store(Request $request) {}

    public function show(string $id) {}

    public function edit(string $id) {}

    public function update(Request $request, string $id) {}

    public function destroy(string $id) {}
}
