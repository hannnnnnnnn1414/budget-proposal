<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AfterSalesService;
use App\Models\Approval;
use App\Models\BookNewspaper;
use App\Models\BudgetPlan;
use App\Models\BusinessDuty;
use App\Models\Departments;
use App\Models\GeneralExpense;
use App\Models\InsurancePrem;
use App\Models\OfficeOperation;
use App\Models\OperationalSupport;
use App\Models\Remarks;
use App\Models\RepairMaint;
use App\Models\RepresentationExpense;
use App\Models\SupportMaterial;
use App\Models\TrainingEducation;
use App\Models\Utilities;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;

class ApprovalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // ApprovalController.php
    public function index($dpt_id)
    {
        $notificationController = new NotificationController();
        $notifications = $notificationController->getNotifications();
        // $generalExpenses = GeneralExpense::where('dpt_id', $dpt_id)
        //     ->where('status', 2)
        //     ->get();
        // $supportMaterials = SupportMaterial::where('dpt_id', $dpt_id)
        //     ->where('status', 2)
        //     ->get();
        // $insurancePrems = InsurancePrem::where('dpt_id', $dpt_id)
        //     ->where('status', 2)
        //     ->get();
        // $utilities = Utilities::where('dpt_id', $dpt_id)
        //     ->where('status', 2)
        //     ->get();
        // $businessDuties = BusinessDuty::where('dpt_id', $dpt_id)
        //     ->where('status', 2)
        //     ->get();
        // $repExpenses = RepresentationExpense::where('dpt_id', $dpt_id)
        //     ->where('status', 2)
        //     ->get();
        // $trainingEdus = TrainingEducation::where('dpt_id', $dpt_id)
        //     ->where('status', 2)
        //     ->get();
        // $aftersales = AfterSalesService::where('dpt_id', $dpt_id)
        //     ->where('status', 2)
        //     ->get();
        $budgetPlans = BudgetPlan::where('dpt_id', $dpt_id)
            ->where('status', 2)
            ->get();

        $approvals = collect($budgetPlans);


        // Gabungkan semua menjadi satu collection
        // $approvals = collect()
        //     ->merge($generalExpenses)
        //     ->merge($supportMaterials)
        //     ->merge($insurancePrems)
        //     ->merge($utilities)
        //     ->merge($businessDuties)
        //     ->merge($repExpenses)
        //     ->merge($trainingEdus)
        //     ->merge($aftersales);


        return view('approvals.index', [
            'approvals' => $approvals,
            'dpt_id' => $dpt_id,
            'notifications' => $notifications
        ]);
    }

    public function approvalDetail()
{
    $notificationController = new NotificationController();
    $notifications = $notificationController->getNotifications();
    $sect = session('sect');
    $dept = session('dept');
    $npk = session('npk');

    // Log session untuk debugging
    Log::info('Session Data in approvalDetail', ['sect' => $sect, 'dept' => $dept, 'npk' => $npk]);

    // Tentukan status berdasarkan sektor untuk APPROVED dan DISAPPROVED
    $status = null;
    if ($sect == 'Kadept') {
        $status = [3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
    } elseif ($sect == 'Kadiv') {
        $status = [4, 5, 6, 7, 8, 9, 10, 11, 12];
    } elseif ($sect == 'DIC') {
        $status = [5, 6, 7, 8, 9, 10, 11, 12];
    } elseif ($sect == 'Kadept' && $dept == '6121') {
        $status = [3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
    }

    // Jika tidak ada status yang sesuai, kembalikan tampilan kosong
    if ($status === null) {
        Log::info('No status matched in approvalDetail', ['sect' => $sect, 'dept' => $dept, 'npk' => $npk]);
        return view('approvals.detail', ['approvals' => collect(), 'notifications' => $notifications]);
    }

    // Gunakan allowed_departments dari sesi
    $departments = session('allowed_departments', [$dept]); // Default ke dept pengguna jika tidak ada allowed_departments

    Log::info('Departments in approvalDetail', ['departments' => $departments]);

    // Ambil semua sub_id berdasarkan status yang sesuai
    $subIds = Approval::whereIn('status', $status)->pluck('sub_id');

    // Ambil data dari BudgetPlan
    $budgetPlans = BudgetPlan::select('sub_id', 'status', 'purpose', 'dpt_id')
        ->whereIn('sub_id', $subIds)
        ->whereIn('dpt_id', $departments) // Gunakan daftar departemen dari sesi
        ->whereIn('status', $status) // Pastikan status sesuai
        ->groupBy('sub_id', 'status', 'purpose', 'dpt_id')
        ->get();

    $approvals = collect($budgetPlans);

    // Log data yang diambil untuk debugging
    Log::info('Approvals in approvalDetail', ['approvals' => $approvals->toArray(), 'subIds' => $subIds->toArray()]);

    return view('approvals.detail', compact('approvals', 'notifications'));
}

    public function pendingApprovals(Request $request)
{
    $notificationController = new NotificationController();
    $notifications = $notificationController->getNotifications();
    $sect = session('sect');
    $dept = session('dept');
    $npk = session('npk');

    // Ambil parameter acc_id dari query string
    $acc_id = $request->query('acc_id');

    Log::info('Session Data', ['sect' => $sect, 'dept' => $dept, 'npk' => $npk, 'acc_id' => $acc_id]);

    $status = null;
    if ($sect == 'Kadept') {
        $status = [2]; // Menunggu approval Kadept
    } elseif ($sect == 'Kadiv') {
        $status = [3]; // Menunggu approval Kadiv
    } elseif ($sect == 'DIC') {
        $status = [4]; // Menunggu approval DIC
    } elseif ($sect == 'Kadept' && $dept == '6121') {
        $status = [2]; // Menunggu approval Kadept untuk dept 6121
    }

    if ($status === null) {
        Log::info('No status matched, returning empty view');
        return view('approvals.pending', ['approvals' => collect(), 'groupedAccounts' => collect(), 'notifications' => $notifications]);
    }

    $departments = [];
    if ($sect == 'Kadiv' && $npk == 'P1133') {
        $departments = Departments::whereIn('dpt_id', ['4111', '4131', '4141', '1111'])->pluck('dpt_id')->toArray();
    } elseif ($sect == 'DIC' && $npk == 'P1144') {
        $departments = Departments::whereIn('dpt_id', ['4111', '4131', '4141', '1111'])->pluck('dpt_id')->toArray();
    } else {
        $departments = [$dept];
    }

    Log::info('Departments for Pending Approvals', ['departments' => $departments]);

    if (empty($departments)) {
        Log::warning('No departments found', ['npk' => $npk, 'sect' => $sect]);
        return view('approvals.pending', ['approvals' => collect(), 'groupedAccounts' => collect(), 'notifications' => $notifications]);
    }

    $subIds = Approval::whereIn('status', $status)->pluck('sub_id');

    Log::info('Sub IDs for status', ['status' => $status, 'subIds' => $subIds->toArray(), 'acc_id' => $acc_id]);

    // Untuk Kadept, tampilkan daftar submission langsung
    if ($sect == 'Kadept') {
        $query = BudgetPlan::select('sub_id', 'status', 'purpose', 'dpt_id')
            ->whereIn('sub_id', $subIds)
            ->where('dpt_id', $dept)
            ->whereIn('status', $status);
        
        // Tambahkan filter acc_id jika ada
        if ($acc_id) {
            $query->where('acc_id', $acc_id);
        }

        $approvals = $query->groupBy('sub_id', 'status', 'purpose', 'dpt_id')
            ->get();

        Log::info('Approvals for Kadept', ['approvals' => $approvals->toArray(), 'acc_id' => $acc_id]);

        return view('approvals.pending', compact('approvals', 'notifications'));
    } else {
        // Untuk Kadiv dan DIC, tampilkan per akun
        $query = BudgetPlan::select('sub_id', 'status', 'purpose', 'dpt_id', 'acc_id', 'amount')
            ->whereIn('sub_id', $subIds)
            ->whereIn('dpt_id', $departments)
            ->whereIn('status', $status);
        
        // Tambahkan filter acc_id jika ada
        if ($acc_id) {
            $query->where('acc_id', $acc_id);
        }

        $budgetPlans = $query->groupBy('sub_id', 'status', 'purpose', 'dpt_id', 'acc_id', 'amount')
            ->get();

        Log::info('Budget Plans for Kadiv or DIC', ['budgetPlans' => $budgetPlans->toArray(), 'acc_id' => $acc_id]);

        $groupedAccounts = $budgetPlans->groupBy(['dpt_id', 'acc_id'])->map(function ($deptGroup) {
            return $deptGroup->map(function ($accGroup) {
                return [
                    'acc_id' => $accGroup->first()->acc_id,
                    'count_submissions' => $accGroup->count(),
                    'total_amount' => $accGroup->sum(function ($item) {
                        return $item->amount ?? 0;
                    }),
                    'dept_name' => Departments::where('dpt_id', $accGroup->first()->dpt_id)->first()->department ?? 'Unknown',
                ];
            });
        });

        return view('approvals.pending', compact('groupedAccounts', 'notifications'));
    }
}

    public function approve(Request $request, $sub_id)
    {
        try {
            // Cari approval berdasarkan sub_id
            $approval = Approval::where('sub_id', $sub_id)->firstOrFail();
            $sect = session('sect');
            $npk = session('npk');

            // Tentukan status berikutnya berdasarkan peran
            $nextStatus = null;
            if ($sect == 'Kadept') {
                $nextStatus = 3; // Approved by KADEP
            } elseif ($sect == 'Kadiv') {
                $nextStatus = 4; // Approved by KADIV
            } elseif ($sect == 'DIC') {
                $nextStatus = 5; // Approved by DIC
            } elseif ($sect == 'Kadept' && session('dept') == '6121') {
                $nextStatus = 7; // Approved by KADEPT Budgeting
            }

            if ($nextStatus === null) {
                throw new \Exception('Invalid role for approval');
            }

            // Update status approval
            $approval->status = $nextStatus;
            $approval->approve_by = $npk; // Simpan NPK approver
            $approval->save();

            // Update status di BudgetPlan
            $budgetPlan = BudgetPlan::where('sub_id', $sub_id)->first();
            if ($budgetPlan) {
                $budgetPlan->status = $nextStatus;
                $budgetPlan->save();
            }

            // Simpan pesan sukses ke session
            Session::flash('success', 'Submission approved successfully.');

            return response()->json(['message' => 'Submission approved successfully'], 200);
        } catch (\Exception $e) {
            Log::error('Approval Error: ', ['error' => $e->getMessage(), 'sub_id' => $sub_id]);
            return response()->json(['message' => 'Failed to approve submission.'], 500);
        }
    }

   public function reject(Request $request, $sub_id)
    {
        try {
            // Log input request untuk debugging
            Log::info('Reject Request Data', ['sub_id' => $sub_id, 'request' => $request->all()]);

            // Validasi input remark
            $validated = $request->validate([
                'remark' => 'required|string|max:255',
            ], [
                'remark.required' => 'Reason for rejection is required.',
                'remark.max' => 'Reason for rejection cannot exceed 255 characters.',
            ]);


            // Cari approval berdasarkan sub_id
            $approval = Approval::where('sub_id', $sub_id)->first();
            if (!$approval) {
                Log::error('Approval not found', ['sub_id' => $sub_id]);
                return response()->json(['message' => 'Submission not found.'], 404);
            }

            $sect = session('sect');
            $npk = session('npk');

            // Validasi npk
            if (empty($npk)) {
                Log::error('NPK not found in session', ['sub_id' => $sub_id]);
                return response()->json(['message' => 'User session invalid.'], 403);
            }

            // Tentukan status disapproval berdasarkan peran
            $disapproveStatus = null;
            if ($sect == 'Kadept') {
                $disapproveStatus = 8; // Disapproved by KADEP
            } elseif ($sect == 'Kadiv') {
                $disapproveStatus = 9; // Disapproved by KADIV
            } elseif ($sect == 'DIC') {
                $disapproveStatus = 10; // Disapproved by DIC
            } elseif ($sect == 'Kadept' && session('dept') == '6121') {
                $disapproveStatus = 12; // Disapproved by KADEPT Budgeting
            }

            if ($disapproveStatus === null) {
                Log::error('Invalid role for disapproval', ['sect' => $sect, 'sub_id' => $sub_id]);
                return response()->json(['message' => 'Invalid role for disapproval.'], 403);
            }

            // Update status approval
            $approval->status = $disapproveStatus;
            $approval->approve_by = $npk;
            $approval->save();

            // Simpan remark ke tabel Remarks
            $remark = Remarks::create([
                'sub_id' => $sub_id,
                'remark' => $validated['remark'],
                'remark_by' => $npk,
                'status' => $disapproveStatus,
            ]);

            if (!$remark) {
                Log::error('Failed to save remark', ['sub_id' => $sub_id, 'remark' => $validated['remark']]);
                return response()->json(['message' => 'Failed to save rejection reason.'], 500);
            }

            // Update status di BudgetPlan
            $budgetPlan = BudgetPlan::where('sub_id', $sub_id)->first();
            if ($budgetPlan) {
                $budgetPlan->status = $disapproveStatus;
                $budgetPlan->save();
            } else {
                Log::warning('BudgetPlan not found', ['sub_id' => $sub_id]);
            }


            Log::info('Submission rejected successfully', [
                'sub_id' => $sub_id,
                'status' => $disapproveStatus,
                'remark' => $validated['remark']
            ]);

            // Simpan pesan sukses ke session
            Session::flash('success', 'Submission rejected successfully.');

            return response()->json(['message' => 'Submission rejected successfully'], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation Error in reject: ', [
                'errors' => $e->errors(),
                'sub_id' => $sub_id
            ]);
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Rejection Error: ', [
                'error' => $e->getMessage(),
                'sub_id' => $sub_id
            ]);
            return response()->json([
                'message' => 'Failed to reject submission.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function approveByAccount(Request $request, $acc_id, $dpt_id)
    {
        try {
            $sect = session('sect');
            $npk = session('npk');

            if ($sect !== 'DIC' || $npk !== 'P1144') {
                throw new \Exception('Unauthorized role or user for account approval');
            }

            $subIds = Approval::where('status', 4)
                ->whereIn('sub_id', function ($query) use ($acc_id, $dpt_id) {
                    $query->select('sub_id')
                        ->from('budget_plans')
                        ->where('acc_id', $acc_id)
                        ->where('dpt_id', $dpt_id)
                        ->where('status', 4);
                })
                ->pluck('sub_id');

            if ($subIds->isEmpty()) {
                Log::warning('No submissions found for approval', ['acc_id' => $acc_id, 'dpt_id' => $dpt_id]);
                return response()->json(['message' => 'No submissions found for approval.'], 404);
            }

            Approval::whereIn('sub_id', $subIds)->update([
                'status' => 5,
                'approve_by' => $npk,
            ]);

            BudgetPlan::whereIn('sub_id', $subIds)->update([
                'status' => 5,
            ]);

            Log::info('Approved submissions for account', ['acc_id' => $acc_id, 'dpt_id' => $dpt_id, 'subIds' => $subIds->toArray()]);

            Session::flash('success', 'All submissions for account approved successfully.');

            return response()->json(['message' => 'All submissions for account approved successfully'], 200);
        } catch (\Exception $e) {
            Log::error('Account Approval Error: ', ['error' => $e->getMessage(), 'acc_id' => $acc_id, 'dpt_id' => $dpt_id]);
            return response()->json(['message' => 'Failed to approve submissions.', 'error' => $e->getMessage()], 500);
        }
    }

    public function rejectByAccount(Request $request, $acc_id, $dpt_id)
    {
        try {
            Log::info('Reject By Account Request Data', [
                'acc_id' => $acc_id,
                'dpt_id' => $dpt_id,
                'request' => $request->all()
            ]);

            // Validasi input remark
            $validated = $request->validate([
                'remark' => 'required|string|max:255',
            ], [
                'remark.required' => 'Reason for rejection is required.',
                'remark.max' => 'Reason for rejection cannot exceed 255 characters.',
            ]);

            $sect = session('sect');
            $npk = session('npk');

            if ($sect !== 'DIC' || $npk !== 'P1144') {
                Log::error('Unauthorized role or user for account rejection', [
                    'sect' => $sect,
                    'npk' => $npk,
                    'acc_id' => $acc_id,
                    'dpt_id' => $dpt_id
                ]);
                return response()->json(['message' => 'Unauthorized role or user for account rejection'], 403);
            }


            // Cari sub_id yang sesuai
            $subIds = Approval::where('status', 4)
                ->whereIn('sub_id', function ($query) use ($acc_id, $dpt_id) {
                    $query->select('sub_id')
                        ->from('budget_plans')
                        ->where('acc_id', $acc_id)
                        ->where('dpt_id', $dpt_id)
                        ->where('status', 4);
                })
                ->pluck('sub_id');

            if ($subIds->isEmpty()) {
                Log::warning('No submissions found for rejection', [
                    'acc_id' => $acc_id,
                    'dpt_id' => $dpt_id
                ]);
                return response()->json(['message' => 'No submissions found for rejection.'], 404);
            }

            // Update status di tabel approvals
            Approval::whereIn('sub_id', $subIds)->update([
                'status' => 10,
                'approve_by' => $npk,
            ]);

            // Update status di tabel budget_plans
            BudgetPlan::whereIn('sub_id', $subIds)->update([
                'status' => 10,
            ]);

            // Simpan remark ke tabel remarks
            foreach ($subIds as $sub_id) {
                Remarks::create([
                    'sub_id' => $sub_id,
                    'remark' => $validated['remark'],
                    'remark_by' => $npk,
                    'status' => 10,
                ]);
            }


            Log::info('Rejected submissions for account', [
                'acc_id' => $acc_id,
                'dpt_id' => $dpt_id,
                'subIds' => $subIds->toArray(),
                'remark' => $validated['remark']
            ]);

            Session::flash('success', 'All submissions for account rejected successfully.');

            return response()->json(['message' => 'All submissions for account rejected successfully'], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation Error in rejectByAccount: ', [
                'errors' => $e->errors(),
                'acc_id' => $acc_id,
                'dpt_id' => $dpt_id
            ]);
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Rejection Error: ', [
                'error' => $e->getMessage(),
                'acc_id' => $acc_id,
                'dpt_id' => $dpt_id
            ]);
            return response()->json([
                'message' => 'Failed to reject submissions.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function accountDetail($acc_id, $dpt_id)
{
    $notificationController = new NotificationController();
    $notifications = $notificationController->getNotifications();
    $sect = session('sect');
    $dept = session('dept');
    $npk = session('npk');

    Log::info('Session Data in accountDetail', [
        'sect' => $sect,
        'dept' => $dept,
        'npk' => $npk,
        'acc_id' => $acc_id,
        'dpt_id' => $dpt_id
    ]);

    // Tentukan status berdasarkan sect
    $status = null;
    if ($sect == 'Kadept') {
        $status = [2]; // Menunggu approval Kadept
    } elseif ($sect == 'Kadiv') {
        $status = [3]; // Menunggu approval Kadiv
    } elseif ($sect == 'DIC') {
        $status = [4]; // Menunggu approval DIC
    } elseif ($sect == 'Kadept' && $dept == '6121') {
        $status = [2]; // Menunggu approval Kadept untuk dept 6121
    }

    if ($status === null) {
        Log::info('No status matched for accountDetail', [
            'sect' => $sect,
            'dept' => $dept,
            'npk' => $npk
        ]);
        return view('approvals.account-detail', [
            'approvals' => collect(),
            'notifications' => $notifications,
            'acc_id' => $acc_id,
            'dpt_id' => $dpt_id
        ]);
    }

    // Validasi departemen
    $departments = [$dpt_id];
    if ($sect == 'Kadiv' && $npk == 'P1133') {
        $allowedDepartments = Departments::whereIn('dpt_id', ['4111', '4131', '4141', '1111'])
            ->pluck('dpt_id')
            ->toArray();
        if (!in_array($dpt_id, $allowedDepartments)) {
            $departments = [];
            Log::info('Invalid department access attempt', [
                'npk' => $npk,
                'dpt_id' => $dpt_id
            ]);
        }
    } elseif ($sect == 'DIC' && $npk == 'P1144') {
        $departments = [$dpt_id];
    } else {
        if ($dept != $dpt_id) {
            $departments = [];
            Log::info('Department mismatch', [
                'dept' => $dept,
                'dpt_id' => $dpt_id
            ]);
        }
    }

    // Ambil pengajuan langsung dari BudgetPlan dengan distinct
    $approvals = BudgetPlan::select('sub_id', 'status', 'purpose', 'dpt_id', 'acc_id')
        ->where('acc_id', $acc_id)
        ->whereIn('dpt_id', $departments)
        ->whereIn('status', $status)
        ->distinct()
        ->get();

    Log::info('Approvals in accountDetail', [
        'approvals' => $approvals->toArray(),
        'acc_id' => $acc_id,
        'dpt_id' => $dpt_id,
        'status' => $status,
        'departments' => $departments
    ]);

    return view('approvals.account-detail', compact('approvals', 'notifications', 'acc_id', 'dpt_id'));
}

public function approveByDepartment(Request $request, $dpt_id)
    {
        try {
            $sect = session('sect');
            $npk = session('npk');

            // Hanya Kadiv dengan npk P1133 yang diizinkan
            if ($sect !== 'Kadiv' || $npk !== 'P1133') {
                throw new \Exception('Unauthorized role or user for department approval');
            }

            // Validasi departemen
            $allowedDepartments = Departments::whereIn('dpt_id', ['4111', '4131', '4141', '1111'])
                ->pluck('dpt_id')
                ->toArray();
            if (!in_array($dpt_id, $allowedDepartments)) {
                Log::error('Invalid department for approval', ['dpt_id' => $dpt_id, 'npk' => $npk]);
                return response()->json(['message' => 'Invalid department for approval.'], 403);
            }

            // Cari sub_id yang sesuai dengan status 3 (menunggu approval Kadiv)
            $subIds = Approval::where('status', 3)
                ->whereIn('sub_id', function ($query) use ($dpt_id) {
                    $query->select('sub_id')
                        ->from('budget_plans')
                        ->where('dpt_id', $dpt_id)
                        ->where('status', 3);
                })
                ->pluck('sub_id');

            if ($subIds->isEmpty()) {
                Log::warning('No submissions found for department approval', ['dpt_id' => $dpt_id]);
                return response()->json(['message' => 'No submissions found for approval in this department.'], 404);
            }

            // Update status di tabel approvals ke status 4 (Approved by Kadiv)
            Approval::whereIn('sub_id', $subIds)->update([
                'status' => 4,
                'approve_by' => $npk,
            ]);

            // Update status di tabel budget_plans
            BudgetPlan::whereIn('sub_id', $subIds)->update([
                'status' => 4,
            ]);

            Log::info('Approved submissions for department', [
                'dpt_id' => $dpt_id,
                'subIds' => $subIds->toArray()
            ]);

            Session::flash('success', 'All submissions for department approved successfully.');

            return response()->json(['message' => 'All submissions for department approved successfully'], 200);
        } catch (\Exception $e) {
            Log::error('Department Approval Error: ', [
                'error' => $e->getMessage(),
                'dpt_id' => $dpt_id
            ]);
            return response()->json([
                'message' => 'Failed to approve submissions.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // [MODIFIKASI] Fungsi untuk menolak semua pengajuan dalam satu departemen
    public function rejectByDepartment(Request $request, $dpt_id)
    {
        try {
            Log::info('Reject By Department Request Data', [
                'dpt_id' => $dpt_id,
                'request' => $request->all()
            ]);

            // Validasi input remark
            $validated = $request->validate([
                'remark' => 'required|string|max:255',
            ], [
                'remark.required' => 'Reason for rejection is required.',
                'remark.max' => 'Reason for rejection cannot exceed 255 characters.',
            ]);

            $sect = session('sect');
            $npk = session('npk');

            // Hanya Kadiv dengan npk P1133 yang diizinkan
            if ($sect !== 'Kadiv' || $npk !== 'P1133') {
                Log::error('Unauthorized role or user for department rejection', [
                    'sect' => $sect,
                    'npk' => $npk,
                    'dpt_id' => $dpt_id
                ]);
                return response()->json(['message' => 'Unauthorized role or user for department rejection'], 403);
            }

            // Validasi departemen
            $allowedDepartments = Departments::whereIn('dpt_id', ['4111', '4131', '4141', '1111'])
                ->pluck('dpt_id')
                ->toArray();
            if (!in_array($dpt_id, $allowedDepartments)) {
                Log::error('Invalid department for rejection', ['dpt_id' => $dpt_id, 'npk' => $npk]);
                return response()->json(['message' => 'Invalid department for rejection.'], 403);
            }

            // Cari sub_id yang sesuai dengan status 3 (menunggu approval Kadiv)
            $subIds = Approval::where('status', 3)
                ->whereIn('sub_id', function ($query) use ($dpt_id) {
                    $query->select('sub_id')
                        ->from('budget_plans')
                        ->where('dpt_id', $dpt_id)
                        ->where('status', 3);
                })
                ->pluck('sub_id');

            if ($subIds->isEmpty()) {
                Log::warning('No submissions found for department rejection', ['dpt_id' => $dpt_id]);
                return response()->json(['message' => 'No submissions found for rejection in this department.'], 404);
            }

            // Update status di tabel approvals ke status 9 (Disapproved by Kadiv)
            Approval::whereIn('sub_id', $subIds)->update([
                'status' => 9,
                'approve_by' => $npk,
            ]);

            // Update status di tabel budget_plans
            BudgetPlan::whereIn('sub_id', $subIds)->update([
                'status' => 9,
            ]);

            // Simpan remark ke tabel remarks untuk setiap sub_id
            foreach ($subIds as $sub_id) {
                Remarks::create([
                    'sub_id' => $sub_id,
                    'remark' => $validated['remark'],
                    'remark_by' => $npk,
                    'status' => 9,
                ]);
            }

            Log::info('Rejected submissions for department', [
                'dpt_id' => $dpt_id,
                'subIds' => $subIds->toArray(),
                'remark' => $validated['remark']
            ]);

            Session::flash('success', 'All submissions for department rejected successfully.');

            return response()->json(['message' => 'All submissions for department rejected successfully'], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation Error in rejectByDepartment: ', [
                'errors' => $e->errors(),
                'dpt_id' => $dpt_id
            ]);
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Rejection Error: ', [
                'error' => $e->getMessage(),
                'dpt_id' => $dpt_id
            ]);
            return response()->json([
                'message' => 'Failed to reject submissions.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // public function detail($acc_id)
    // {
    //     $submissions = collect();

    //     if (in_array($acc_id, ['SGAADVERT', 'SGACOM', 'SGAOFFICESUP'])) {
    //         $submissions = OfficeOperation::select('sub_id', 'status', 'month')
    //             ->where('status', '!=', 0)
    //             ->where('acc_id', $acc_id)
    //             ->groupBy('sub_id', 'status', 'month')
    //             ->get();
    //     } elseif (in_array($acc_id, ['SGAASOCIATION', 'SGABCHARGES', 'SGACONTRIBUTION', 'FOHPACKING', 'SGARYLT', 'FOHAUTOMOBILE', 'FOHPROF', 'FOHRENT', 'FOHTAXPUB', 'SGAAUTOMOBILE', 'SGAPROF', 'SGATAXPUB'])) {
    //         $submissions = GeneralExpense::select('sub_id', 'status', 'month')
    //             ->where('status', '!=', 0)
    //             ->where('acc_id', $acc_id)
    //             ->groupBy('sub_id', 'status', 'month')
    //             ->get();
    //     } elseif (in_array($acc_id, ['SGAMARKT', 'FOHTECHDO', 'FOHRECRUITING', 'SGARECRUITING', 'SGARENT'])) {
    //         $submissions = OperationalSupport::select('sub_id', 'status', 'month')
    //             ->where('status', '!=', 0)
    //             ->where('acc_id', $acc_id)
    //             ->groupBy('sub_id', 'status', 'month')
    //             ->get();
    //     } elseif (in_array($acc_id, ['FOHTOOLS', 'FOHFS', 'FOHINDMAT', 'FOHREPAIR'])) {
    //         $submissions = SupportMaterial::select('sub_id', 'status', 'month')
    //             ->where('status', '!=', 0)
    //             ->where('acc_id', $acc_id)
    //             ->groupBy('sub_id', 'status', 'month')
    //             ->get();
    //     } elseif (in_array($acc_id, ['FOHENTERTAINT', 'FOHREPRESENTATION', 'SGAENTERTAINT', 'SGAREPRESENTATION'])) {
    //         $submissions = RepresentationExpense::select('sub_id', 'status', 'month')
    //             ->where('status', '!=', 0)
    //             ->where('acc_id', $acc_id)
    //             ->groupBy('sub_id', 'status', 'month')
    //             ->get();
    //     } elseif (in_array($acc_id, ['FOHINSPREM', 'SGAINSURANCE'])) {
    //         $submissions = InsurancePrem::select('sub_id', 'status', 'month')
    //             ->where('status', '!=', 0)
    //             ->where('acc_id', $acc_id)
    //             ->groupBy('sub_id', 'status', 'month')
    //             ->get();
    //     } elseif (in_array($acc_id, ['FOHPOWER', 'SGAPOWER'])) {
    //         $submissions = Utilities::select('sub_id', 'status', 'month')
    //             ->where('status', '!=', 0)
    //             ->where('acc_id', $acc_id)
    //             ->groupBy('sub_id', 'status', 'month')
    //             ->get();
    //     } elseif (in_array($acc_id, ['FOHTRAV', 'SGATRAV'])) {
    //         $submissions = BusinessDuty::select('sub_id', 'status', 'month')
    //             ->where('status', '!=', 0)
    //             ->where('acc_id', $acc_id)
    //             ->groupBy('sub_id', 'status', 'month')
    //             ->get();
    //     } elseif (in_array($acc_id, ['FOHTRAINING', 'SGATRAINING'])) {
    //         $submissions = TrainingEducation::select('sub_id', 'status', 'month')
    //             ->where('status', '!=', 0)
    //             ->where('acc_id', $acc_id)
    //             ->groupBy('sub_id', 'status', 'month')
    //             ->get();
    //     } elseif ($acc_id === 'SGABOOK') {
    //         $submissions = BookNewspaper::select('sub_id', 'status', 'month')
    //             ->where('status', '!=', 0)
    //             ->where('acc_id', $acc_id)
    //             ->groupBy('sub_id', 'status', 'month')
    //             ->get();
    //     } elseif ($acc_id === 'SGAREPAIR') {
    //         $submissions = RepairMaint::select('sub_id', 'status', 'month')
    //             ->where('status', '!=', 0)
    //             ->where('acc_id', $acc_id)
    //             ->groupBy('sub_id', 'status', 'month')
    //             ->get();
    //     } elseif ($acc_id === 'SGAAFTERSALES') {
    //         $submissions = AfterSalesService::select('sub_id', 'status', 'month')
    //             ->where('status', '!=', 0)
    //             ->where('acc_id', $acc_id)
    //             ->groupBy('sub_id', 'status', 'month')
    //             ->get();
    //     }

    //     return view('submissions.detail', compact('submissions'));
    // }

    // public function approval($dpt_id, Request $request)
    // {
    //     $drafts = collect();

    //     if (in_array($dpt_id, ['SGAADVERT', 'SGACOM', 'SGAOFFICESUP'])) {
    //         $drafts = OfficeOperation::with('dept')
    //             ->where('dpt_id', $dpt_id)
    //             ->where('status', '!=', 2)
    //             ->get();
    //     } elseif (in_array($dpt_id, ['SGAASOCIATION', 'SGABCHARGES', 'SGACONTRIBUTION', 'FOHPACKING', 'SGARYLT', 'FOHAUTOMOBILE', 'FOHPROF', 'FOHRENT', 'FOHTAXPUB', 'SGAAUTOMOBILE', 'SGAPROF', 'SGATAXPUB'])) {
    //         $drafts = GeneralExpense::with('dept')
    //             ->where('status', '=', 2)
    //             ->get();
    //     } elseif (in_array($dpt_id, ['SGAMARKT', 'FOHTECHDO', 'FOHRECRUITING', 'SGARECRUITING', 'SGARENT'])) {
    //         $drafts = OperationalSupport::with('dept')
    //             ->where('status', '=', 2)
    //             ->get();
    //     } elseif (in_array($dpt_id, ['FOHTOOLS', 'FOHFS', 'FOHINDMAT', 'FOHREPAIR'])) {
    //         $drafts = SupportMaterial::with('dept')
    //             ->where('status', '=', 2)
    //             ->get();
    //     } elseif (in_array($dpt_id, ['FOHENTERTAINT', 'FOHREPRESENTATION', 'SGAENTERTAINT', 'SGAREPRESENTATION'])) {
    //         $drafts = RepresentationExpense::with('dept')
    //             ->where('status', '=', 2)
    //             ->get();
    //     } elseif (in_array($dpt_id, ['FOHINSPREM', 'SGAINSURANCE'])) {
    //         $drafts = InsurancePrem::with('dept')
    //             ->where('status', '=', 2)
    //             ->get();
    //     } elseif (in_array($dpt_id, ['FOHPOWER', 'SGAPOWER'])) {
    //         $drafts = Utilities::with('dept')
    //             ->where('status', '=', 2)
    //             ->get();
    //     } elseif (in_array($dpt_id, ['FOHTRAV', 'SGATRAV'])) {
    //         $drafts = BusinessDuty::with('dept')
    //             ->where('status', '=', 2)
    //             ->get();
    //     } elseif (in_array($dpt_id, ['FOHTRAINING', 'SGATRAINING'])) {
    //         $drafts = TrainingEducation::with('dept')
    //             ->where('status', '=', 2)
    //             ->get();
    //     } elseif ($dpt_id === 'SGABOOK') {
    //         $drafts = BookNewspaper::with('dept')
    //             ->where('status', '=', 2)
    //             ->get();
    //     } elseif ($dpt_id === 'SGAREPAIR') {
    //         $drafts = RepairMaint::with('dept')
    //             ->where('status', '=', 2)
    //             ->get();
    //     }

    //     return view('approvals.detail', compact('drafts'));
    // }

    public function history($sub_id)
    {
        $approvals = Approval::with('user')
            ->where('sub_id', $sub_id)
            ->orderBy('created_at', 'asc')
            ->get();

        $remarks = Remarks::with('user')
            ->where('sub_id', $sub_id)
            ->orderBy('created_at', 'asc')
            ->get();

        $statusMap = [
            1 => 'Created',
            2 => 'Requested',
            3 => 'Approved by KADEP',
            4 => 'Approved by KADIV',
            5 => 'Approved by DIC',
            6 => 'Approved by PIC Budgeting',
            7 => 'Approved by KADEPT Budgeting',
            8 => 'Disapproved by KADEP',
            9 => 'Disapproved by KADIV',
            10 => 'Disapproved by DIC',
            11 => 'Disapproved by PIC Budgeting',
            12 => 'Disapproved by KADEP Budgeting',
        ];

        $history = [];

        $groupedApprovals = $approvals->unique(function ($item) {
            return $item->status . '-' . $item->approve_by;
        });

        foreach ($groupedApprovals as $item) {
            $historyItem = [
                'status' => $statusMap[$item->status] ?? 'Unknown',
                'approver' => $item->user ? $item->user->name : 'System',
                'npk' => $item->approve_by,
                'date' => $item->created_at->format('d M Y, H:i'),
                'status_code' => $item->status,
                'remark' => null,
            ];

            if ($item->status !== 1 && $item->status !== 2) {
                $remark = $remarks->firstWhere('remark_by', $item->approve_by);
                if ($remark) {
                    $historyItem['remark'] = $remark->remark;
                }
            }

            $history[] = $historyItem;
        }

        // Optional: urutkan berdasarkan status_code
        usort($history, function ($a, $b) {
            return $a['status_code'] <=> $b['status_code'];
        });

        return view('approvals.history', compact('history'));
    }




    /**
     * Show the form for creating a new resource.
     */
    public function addRemark()
    {
        //
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
