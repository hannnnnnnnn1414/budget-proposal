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
    public function index($dpt_id)
    {
        $notificationController = new NotificationController();
        $notifications = $notificationController->getNotifications();
        $budgetPlans = BudgetPlan::where('dpt_id', $dpt_id)
            ->where('status', 2)
            ->get();

        $approvals = collect($budgetPlans);

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

        Log::info('Session Data in approvalDetail', ['sect' => $sect, 'dept' => $dept, 'npk' => $npk]);

        $status = null;

        if ($sect == 'PIC' && $dept == '6121') {
            $status = [5, 6, 7, 11, 12];
        } elseif ($sect == 'Kadept' && $dept == '6121') {
            $status = [6, 7, 12];
        } elseif ($sect == 'Kadept') {
            $status = [3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
        } elseif ($sect == 'Kadiv') {
            $status = [4, 5, 6, 7, 8, 9, 10, 11, 12];
        } elseif ($sect == 'DIC') {
            $status = [4, 5, 6, 7, 8, 9, 10, 11, 12];
        }

        if ($status === null) {
            Log::info('No status matched in approvalDetail', ['sect' => $sect, 'dept' => $dept, 'npk' => $npk]);
            return view('approvals.detail', ['approvals' => collect(), 'notifications' => $notifications]);
        }

        $departments = [$dept];

        if (($sect == 'PIC' || $sect == 'Kadept') && $dept == '6121') {
            $departments = Departments::pluck('dpt_id')->toArray();
        } elseif ($sect == 'Kadept') {
            $departments = [$dept];
        } elseif ($sect == 'Kadiv' || $sect == 'DIC') {
            $dicMappings = [
                '01555' => [
                    '1111',
                    '1116',
                    '1131',
                    '1140',
                    '1151',
                    '1160',
                    '1211',
                    '1224',
                    '1231',
                    '1242',
                    '1311',
                    '1331',
                    '1332',
                    '1333',
                    '1411',
                    '1341',
                    '1351',
                    '1361'
                ],
                '02665' => [
                    '4111',
                    '4131',
                    '4141',
                    '4151',
                    '4161',
                    '4171',
                    '4181',
                    '4211',
                    '4221',
                    '4311',
                    '5111',
                    '7111'
                ],
                'EXP41' => [
                    '3111',
                    '3121',
                    '3131'
                ],
                'EXP38' => [
                    '2111',
                    '2121'
                ],
                'EXP43' => [
                    '6111',
                    '6121'
                ]
            ];

            $kadivMappings = [
                '01577' => ['1111', '1116', '1131', '1140', '1151', '1160', '1211', '1224', '1231', '1242'],
                '01266' => ['1311', '1331', '1332', '1333', '1411'],
                '01961' => ['1341', '1351', '1361'],
                '01466' => ['2111', '2121', '3111', '3121', '3131'],
                '01561' => ['4111', '4131', '4141', '4221', '4311', '7111'],
                '01166' => ['4151', '4161', '4171', '4181', '5111']
            ];

            if ($sect == 'Kadiv') {
                $departments = $kadivMappings[$npk] ?? [$dept];
            } elseif ($sect == 'DIC') {
                $departments = $dicMappings[$npk] ?? [$dept];
            }

            Log::info('Mapping departments for ' . $sect, [
                'npk' => $npk,
                'mapped_departments' => $departments
            ]);
        }

        if (($sect == 'PIC' || $sect == 'Kadept') && $dept == '6121') {
            $departments = Departments::pluck('dpt_id')->toArray();
        } elseif ($sect == 'Kadept') {
            $departments = [$dept];
        }


        $budgetPlans = BudgetPlan::select('sub_id', 'status', 'purpose', 'dpt_id')
            ->whereIn('dpt_id', $departments)
            ->whereIn('status', $status)
            ->groupBy('sub_id', 'status', 'purpose', 'dpt_id')
            ->get();

        $approvals = collect($budgetPlans);

        Log::info('Approvals in approvalDetail', [
            'count' => $approvals->count(),
            'approvals' => $approvals->toArray()
        ]);

        return view('approvals.detail', compact('approvals', 'notifications'));
    }

    public function pendingApprovals(Request $request)
    {
        $notificationController = new NotificationController();
        $notifications = $notificationController->getNotifications();
        $sect = session('sect');
        $dept = session('dept');
        $npk = session('npk');

        $acc_id = $request->query('acc_id');

        Log::info('Session Data', ['sect' => $sect, 'dept' => $dept, 'npk' => $npk, 'acc_id' => $acc_id]);

        $status = null;
        if ($sect == 'Kadept') {
            if ($dept == '6121') {
                $status = [2, 6];
            } else {
                $status = [2, 9];
            }
        } elseif ($sect == 'Kadiv') {
            $status = [3, 10];
        } elseif ($sect == 'DIC') {
            $status = [4, 11];
        } elseif ($sect == 'PIC' && $dept == '6121') {
            $status = [5];
        }

        if ($status === null) {
            Log::info('No status matched, returning empty view');
            return view('approvals.pending', ['approvals' => collect(), 'groupedAccounts' => collect(), 'notifications' => $notifications]);
        }

        $departments = [];

        $dicMappings = [
            '01555' => [
                '1111',
                '1116',
                '1131',
                '1140',
                '1151',
                '1160',
                '1211',
                '1224',
                '1231',
                '1242',
                '1311',
                '1331',
                '1332',
                '1333',
                '1411',
                '1341',
                '1351',
                '1361'
            ],
            '02665' => [
                '4111',
                '4131',
                '4141',
                '4151',
                '4161',
                '4171',
                '4181',
                '4211',
                '4221',
                '4311',
                '5111',
                '7111'
            ],
            'EXP41' => [
                '3111',
                '3121',
                '3131'
            ],
            'EXP38' => [
                '2111',
                '2121'
            ],
            'EXP43' => [
                '6111',
                '6121'
            ]
        ];

        $kadivMappings = [
            '01577' => ['1111', '1116', '1131', '1140', '1151', '1160', '1211', '1224', '1231', '1242'],
            '01266' => ['1311', '1331', '1332', '1333', '1411'],
            '01961' => ['1341', '1351', '1361'],
            '01466' => ['2111', '2121', '3111', '3121', '3131'],
            '01561' => ['4111', '4131', '4141', '4311', '4221', '7111'],
            '01166' => ['4151', '4161', '4171', '4181', '5111']
        ];

        if ($sect == 'Kadiv') {
            $departments = $kadivMappings[$npk] ?? [$dept];
        } elseif ($sect == 'DIC') {
            $departments = $dicMappings[$npk] ?? [$dept];
        } else {
            $departments = [$dept];
        }

        Log::info('Departments for Pending Approvals', ['departments' => $departments]);

        if (empty($departments)) {
            Log::warning('No departments found', ['npk' => $npk, 'sect' => $sect]);
            return view('approvals.pending', ['approvals' => collect(), 'groupedAccounts' => collect(), 'notifications' => $notifications]);
        }

        $genexp = [
            'SGAREPAIR',
            'SGABOOK',
            'SGAMARKT',
            'FOHTECHDO',
            'FOHRECRUITING',
            'SGARECRUITING',
            'SGARENT',
            'SGAADVERT',
            'SGACOM',
            'SGAOFFICESUP',
            'SGAASSOCIATION',
            'SGABCHARGES',
            'SGACONTRIBUTION',
            'FOHPACKING',
            'SGARYLT',
            'FOHAUTOMOBILE',
            'FOHPROF',
            'FOHRENT',
            'FOHTAXPUB',
            'SGAAUTOMOBILE',
            'SGAPROF',
            'SGATAXPUB',
            'SGAOUTSOURCING'
        ];

        if ($sect == 'Kadept') {
            $query = BudgetPlan::select('sub_id', 'status', 'purpose', 'dpt_id')
                ->whereIn('status', $status);

            if ($dept === '4131' && (!$acc_id || in_array($acc_id, $genexp))) {
                $query->whereIn('dpt_id', ['4131', '7111', '1111', '1131', '1151', '1211', '1231']);
            } elseif ($dept === '4111') {
                $query->whereIn('dpt_id', ['4111', '1116', '1140', '1160', '1224', '1242', '7111', '4311']);
            } elseif ($dept === '1332') {
                $query->whereIn('dpt_id', ['1331', '1332', '1333']);
            } else {
                $query->where('dpt_id', $dept);
            }

            if ($acc_id) {
                $query->where('acc_id', $acc_id);
            }

            $approvals = $query->groupBy('sub_id', 'status', 'purpose', 'dpt_id')
                ->get();

            Log::info('Approvals for Kadept', [
                'dept' => $dept,
                'acc_id' => $acc_id,
                'dpt_id' => ($dept === '4131' && (!$acc_id || in_array($acc_id, $genexp))) ? ['4111', '4131', '4141', '4311', '7111', '1111', '1131', '1151', '1211', '1231'] : $dept,
                'approvals' => $approvals->toArray()
            ]);

            return view('approvals.pending', compact('approvals', 'notifications'));
        } else {
            $query = BudgetPlan::select('sub_id', 'status', 'purpose', 'dpt_id', 'acc_id', 'amount')
                ->whereIn('dpt_id', $departments)
                ->whereIn('status', $status);

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
            $approval = Approval::where('sub_id', $sub_id)->firstOrFail();
            $sect = session('sect');
            $npk = session('npk');

            $nextStatus = null;
            if ($sect == 'Kadept') {
                $nextStatus = 3;
            } elseif ($sect == 'Kadiv') {
                $nextStatus = 4;
            } elseif ($sect == 'DIC') {
                $nextStatus = 5;
            } elseif ($sect == 'Kadept' && session('dept') == '6121') {
                $nextStatus = 7;
            }

            if ($nextStatus === null) {
                throw new \Exception('Invalid role for approval');
            }

            $approval->status = $nextStatus;
            $approval->approve_by = $npk;
            $approval->save();

            $budgetPlan = BudgetPlan::where('sub_id', $sub_id)->first();
            if ($budgetPlan) {
                $budgetPlan->status = $nextStatus;
                $budgetPlan->save();
            }

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
            Log::info('Reject Request Data', ['sub_id' => $sub_id, 'request' => $request->all()]);

            $validated = $request->validate([
                'remark' => 'required|string|max:255',
            ], [
                'remark.required' => 'Reason for rejection is required.',
                'remark.max' => 'Reason for rejection cannot exceed 255 characters.',
            ]);


            $approval = Approval::where('sub_id', $sub_id)->first();
            if (!$approval) {
                Log::error('Approval not found', ['sub_id' => $sub_id]);
                return response()->json(['message' => 'Submission not found.'], 404);
            }

            $sect = session('sect');
            $npk = session('npk');

            if (empty($npk)) {
                Log::error('NPK not found in session', ['sub_id' => $sub_id]);
                return response()->json(['message' => 'User session invalid.'], 403);
            }

            $disapproveStatus = null;
            if ($sect == 'Kadept') {
                $disapproveStatus = 8;
            } elseif ($sect == 'Kadiv') {
                $disapproveStatus = 9;
            } elseif ($sect == 'DIC') {
                $disapproveStatus = 10;
            } elseif ($sect == 'Kadept' && session('dept') == '6121') {
                $disapproveStatus = 12;
            }

            if ($disapproveStatus === null) {
                Log::error('Invalid role for disapproval', ['sect' => $sect, 'sub_id' => $sub_id]);
                return response()->json(['message' => 'Invalid role for disapproval.'], 403);
            }

            $approval->status = $disapproveStatus;
            $approval->approve_by = $npk;
            $approval->save();

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

            if (!in_array($sect, ['DIC', 'Kadiv'])) {
                throw new \Exception('Unauthorized role');
            }

            $currentStatus = null;
            $nextStatus = null;

            if ($sect == 'Kadiv') {
                $currentStatus = [3, 10];
                $nextStatus = 4;
            } elseif ($sect == 'DIC') {
                $currentStatus = [4, 11];
                $nextStatus = 5;
            }

            $subIds = BudgetPlan::where('acc_id', $acc_id)
                ->where('dpt_id', $dpt_id)
                ->whereIn('status', $currentStatus)
                ->pluck('sub_id');

            if ($subIds->isEmpty()) {
                Log::warning('No submissions found for approval', [
                    'acc_id' => $acc_id,
                    'dpt_id' => $dpt_id,
                    'currentStatus' => $currentStatus
                ]);
                return response()->json(['message' => 'No submissions found for approval.'], 404);
            }

            BudgetPlan::whereIn('sub_id', $subIds)->update([
                'status' => $nextStatus,
            ]);

            Approval::whereIn('sub_id', $subIds)->update([
                'status' => $nextStatus,
                'approve_by' => $npk,
            ]);

            Log::info('Approved submissions for account', [
                'acc_id' => $acc_id,
                'dpt_id' => $dpt_id,
                'subIds' => $subIds->toArray(),
                'approved_by' => $sect
            ]);

            Session::flash('success', 'All submissions for account approved successfully.');

            return response()->json(['message' => 'All submissions for account approved successfully'], 200);
        } catch (\Exception $e) {
            Log::error('Account Approval Error: ', [
                'error' => $e->getMessage(),
                'acc_id' => $acc_id,
                'dpt_id' => $dpt_id
            ]);
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

            $validated = $request->validate([
                'remark' => 'required|string|max:255',
            ], [
                'remark.required' => 'Reason for rejection is required.',
                'remark.max' => 'Reason for rejection cannot exceed 255 characters.',
            ]);

            $sect = session('sect');
            $npk = session('npk');

            if (!in_array($sect, ['DIC', 'Kadiv'])) {
                Log::error('Unauthorized role for account rejection', [
                    'sect' => $sect,
                    'npk' => $npk,
                    'acc_id' => $acc_id,
                    'dpt_id' => $dpt_id
                ]);
                return response()->json(['message' => 'Unauthorized role for account rejection'], 403);
            }

            $currentStatus = null;
            $rejectStatus = null;

            if ($sect == 'Kadiv') {
                $currentStatus = [3, 10];
                $rejectStatus = 9;
            } elseif ($sect == 'DIC') {
                $currentStatus = [4, 11];
                $rejectStatus = 10;
            }

            $subIds = BudgetPlan::where('acc_id', $acc_id)
                ->where('dpt_id', $dpt_id)
                ->whereIn('status', $currentStatus)
                ->pluck('sub_id');

            if ($subIds->isEmpty()) {
                Log::warning('No submissions found for rejection', [
                    'acc_id' => $acc_id,
                    'dpt_id' => $dpt_id,
                    'currentStatus' => $currentStatus
                ]);
                return response()->json(['message' => 'No submissions found for rejection.'], 404);
            }

            BudgetPlan::whereIn('sub_id', $subIds)->update([
                'status' => $rejectStatus,
            ]);

            Approval::whereIn('sub_id', $subIds)->update([
                'status' => $rejectStatus,
                'approve_by' => $npk,
            ]);

            foreach ($subIds as $sub_id) {
                Remarks::create([
                    'sub_id' => $sub_id,
                    'remark' => $validated['remark'],
                    'remark_by' => $npk,
                    'status' => $rejectStatus,
                ]);
            }

            Log::info('Rejected submissions for account', [
                'acc_id' => $acc_id,
                'dpt_id' => $dpt_id,
                'subIds' => $subIds->toArray(),
                'remark' => $validated['remark'],
                'rejected_by' => $sect
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

        $status = null;
        if ($sect == 'Kadept') {
            $status = [2];
        } elseif ($sect == 'Kadiv') {
            $status = [3];
        } elseif ($sect == 'DIC') {
            $status = [4];
        } elseif ($sect == 'PIC') {
            $status = [5];
        } elseif ($sect == 'Kadept' && $dept == '6121') {
            $status = [2];
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

        $departments = [$dpt_id];
        if ($sect == 'Kadiv' && $npk == '01561') {
            $allowedDepartments = Departments::whereIn('dpt_id', ['1111', '1116', '1131', '1140', '1151', '1160', '1211', '1224', '1231', '1242', '1311', '1331', '1332', '1333', '1341', '1351', '1361', '1411', '4111', '4131', '4141', '4151', '4161', '4171', '4181', '4211', '4311', '5111'])
                ->pluck('dpt_id')
                ->toArray();
            if (!in_array($dpt_id, $allowedDepartments)) {
                $departments = [];
                Log::info('Invalid department access attempt', [
                    'npk' => $npk,
                    'dpt_id' => $dpt_id
                ]);
            }
        } elseif ($sect == 'DIC' && $npk == '02665') {
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

            if (!in_array($sect, ['Kadiv', 'DIC'])) {
                throw new \Exception('Unauthorized role for department approval');
            }

            $dicMappings = [
                '01555' => ['1111', '1116', '1131', '1140', '1151', '1160', '1211', '1224', '1231', '1242', '1311', '1331', '1332', '1333', '1411', '1341', '1351', '1361'],
                '02665' => ['4111', '4131', '4141', '4151', '4161', '4171', '4181', '4211', '4221', '4311', '5111', '7111'],
                'EXP41' => ['3111', '3121', '3131'],
                'EXP38' => ['2111', '2121'],
                'EXP43' => ['6111', '6121']
            ];

            $kadivMappings = [
                '01577' => ['1111', '1116', '1131', '1140', '1151', '1160', '1211', '1224', '1231', '1242'],
                '01266' => ['1311', '1331', '1332', '1333', '1411'],
                '01961' => ['1341', '1351', '1361'],
                '01466' => ['2111', '2121', '3111', '3121', '3131'],
                '01561' => ['4111', '4131', '4141', '4221', '4311', '7111'],
                '01166' => ['4151', '4161', '4171', '4181', '5111']
            ];

            if ($sect == 'Kadiv') {
                $currentStatus = [3, 10];
                $newStatus = 4;
                $approvalStatus = 4;
            } elseif ($sect == 'DIC') {
                $currentStatus = [4, 11];
                $newStatus = 5;
                $approvalStatus = 5;
            }

            $isAuthorized = false;

            if ($sect == 'Kadiv') {
                foreach ($kadivMappings as $kadivNpk => $departments) {
                    if ($kadivNpk == $npk && in_array($dpt_id, $departments)) {
                        $isAuthorized = true;
                        break;
                    }
                }
            } elseif ($sect == 'DIC') {
                foreach ($dicMappings as $dicNpk => $departments) {
                    if ($dicNpk == $npk && in_array($dpt_id, $departments)) {
                        $isAuthorized = true;
                        break;
                    }
                }
            }

            if (!$isAuthorized) {
                Log::error('Unauthorized department approval attempt', [
                    'dpt_id' => $dpt_id,
                    'npk' => $npk,
                    'sect' => $sect
                ]);
                return response()->json(['message' => 'Unauthorized to approve submissions for this department.'], 403);
            }

            $allowedDepartments = [];
            foreach ($kadivMappings as $departments) {
                $allowedDepartments = array_merge($allowedDepartments, $departments);
            }
            foreach ($dicMappings as $departments) {
                $allowedDepartments = array_merge($allowedDepartments, $departments);
            }
            $allowedDepartments = array_unique($allowedDepartments);

            if (!in_array($dpt_id, $allowedDepartments)) {
                Log::error('Invalid department for approval', ['dpt_id' => $dpt_id, 'npk' => $npk]);
                return response()->json(['message' => 'Invalid department for approval.'], 403);
            }

            $subIds = BudgetPlan::where('dpt_id', $dpt_id)
                ->whereIn('status', $currentStatus)
                ->pluck('sub_id');

            BudgetPlan::whereIn('sub_id', $subIds)
                ->whereIn('status', $currentStatus)
                ->update([
                    'status' => $newStatus,
                ]);

            Approval::whereIn('sub_id', $subIds)
                ->whereIn('status', $currentStatus)
                ->update([
                    'status' => $approvalStatus,
                    'approve_by' => $npk,
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

    public function rejectByDepartment(Request $request, $dpt_id)
    {
        try {
            Log::info('Reject By Department Request Data', [
                'dpt_id' => $dpt_id,
                'request' => $request->all()
            ]);

            $validated = $request->validate([
                'remark' => 'required|string|max:255',
            ], [
                'remark.required' => 'Reason for rejection is required.',
                'remark.max' => 'Reason for rejection cannot exceed 255 characters.',
            ]);

            $sect = session('sect');
            $npk = session('npk');

            if (!in_array($sect, ['Kadiv', 'DIC'])) {
                Log::error('Unauthorized role for department rejection', [
                    'sect' => $sect,
                    'npk' => $npk,
                    'dpt_id' => $dpt_id
                ]);
                return response()->json(['message' => 'Unauthorized role for department rejection'], 403);
            }

            $dicMappings = [
                '01555' => ['1111', '1116', '1131', '1140', '1151', '1160', '1211', '1224', '1231', '1242', '1311', '1331', '1332', '1333', '1411', '1341', '1351', '1361'],
                '02665' => ['4111', '4131', '4141', '4151', '4161', '4171', '4181', '4211', '4221', '4311', '5111', '7111'],
                'EXP41' => ['3111', '3121', '3131'],
                'EXP38' => ['2111', '2121'],
                'EXP43' => ['6111', '6121']
            ];

            $kadivMappings = [
                '01577' => ['1111', '1116', '1131', '1140', '1151', '1160', '1211', '1224', '1231', '1242'],
                '01266' => ['1311', '1331', '1332', '1333', '1411'],
                '01961' => ['1341', '1351', '1361'],
                '01466' => ['2111', '2121', '3111', '3121', '3131'],
                '01561' => ['4111', '4131', '4141', '4221', '4311', '7111'],
                '01166' => ['4151', '4161', '4171', '4181', '5111']
            ];

            if ($sect == 'Kadiv') {
                $currentStatus = [3, 10];
                $newStatus = 9;
                $approvalStatus = 9;
            } elseif ($sect == 'DIC') {
                $currentStatus = [4];
                $newStatus = 10;
                $approvalStatus = 10;
            }

            $isAuthorized = false;

            if ($sect == 'Kadiv') {
                foreach ($kadivMappings as $kadivNpk => $departments) {
                    if ($kadivNpk == $npk && in_array($dpt_id, $departments)) {
                        $isAuthorized = true;
                        break;
                    }
                }
            } elseif ($sect == 'DIC') {
                foreach ($dicMappings as $dicNpk => $departments) {
                    if ($dicNpk == $npk && in_array($dpt_id, $departments)) {
                        $isAuthorized = true;
                        break;
                    }
                }
            }

            if (!$isAuthorized) {
                Log::error('Unauthorized department rejection attempt', [
                    'dpt_id' => $dpt_id,
                    'npk' => $npk,
                    'sect' => $sect
                ]);
                return response()->json(['message' => 'Unauthorized to reject submissions for this department.'], 403);
            }

            $allowedDepartments = [];
            foreach ($kadivMappings as $departments) {
                $allowedDepartments = array_merge($allowedDepartments, $departments);
            }
            foreach ($dicMappings as $departments) {
                $allowedDepartments = array_merge($allowedDepartments, $departments);
            }
            $allowedDepartments = array_unique($allowedDepartments);

            if (!in_array($dpt_id, $allowedDepartments)) {
                Log::error('Invalid department for rejection', ['dpt_id' => $dpt_id, 'npk' => $npk]);
                return response()->json(['message' => 'Invalid department for rejection.'], 403);
            }

            $subIds = BudgetPlan::where('dpt_id', $dpt_id)
                ->whereIn('status', $currentStatus)
                ->pluck('sub_id');

            BudgetPlan::whereIn('sub_id', $subIds)
                ->whereIn('status', $currentStatus)
                ->update([
                    'status' => $newStatus,
                ]);

            foreach ($subIds as $sub_id) {
                $latestApproval = Approval::where('sub_id', $sub_id)
                    ->whereIn('status', [3, 10])
                    ->orderBy('created_at', 'desc')
                    ->first();

                if ($latestApproval) {
                    $latestApproval->update([
                        'status' => $approvalStatus,
                        'approve_by' => $npk,
                    ]);

                    Remarks::create([
                        'sub_id' => $sub_id,
                        'remark' => $validated['remark'],
                        'remark_by' => $npk,
                        'status' => $approvalStatus,
                    ]);
                }
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
            5 => 'Acknowledged by DIC',
            6 => 'Approved by PIC Budgeting',
            7 => 'Approved by KADEPT Budgeting',
            8 => 'Disapproved by KADEP',
            9 => 'Disapproved by KADIV',
            10 => 'REQUEST EXPLANATION',
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

        usort($history, function ($a, $b) {
            return $a['status_code'] <=> $b['status_code'];
        });

        return view('approvals.history', compact('history'));
    }




    public function addRemark() {}

    public function store(Request $request) {}

    public function show(string $id) {}

    public function edit(string $id) {}

    public function update(Request $request, string $id) {}

    public function destroy(string $id) {}
}
