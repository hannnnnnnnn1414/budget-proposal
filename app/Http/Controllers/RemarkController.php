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
use App\Models\Departments;
use App\Models\Item;
use App\Models\Remarks;
use App\Models\Training;
use App\Models\User;
use App\Models\Workcenter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RemarkController extends Controller
{
    public function getRemarks($sub_id)
    {
        $currentUserNpk = Auth::user()->npk;

        $remarks = Remarks::where('sub_id', $sub_id)
            ->where('remark_by', $currentUserNpk)
            ->where('remark_type', 'remark')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['remarks' => $remarks]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'sub_id' => 'required',
            'remark' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        $npk = $user->npk;
        $sub_id = $request->sub_id;

        try {
            $submission = BudgetPlan::where('sub_id', $sub_id)->first();

            if (!$submission || !$submission->dpt_id) {
                Log::error("No submission found or missing dpt_id for sub_id {$sub_id}");
                return response()->json([
                    'success' => false,
                    'message' => 'No submission found or missing department information'
                ], 400);
            }

            $submissionDept = $submission->dpt_id;
            $submissionStatus = $submission->status;

            $existingRemark = Remarks::where('sub_id', $sub_id)
                ->where('remark_by', $npk)
                ->where('remark_type', 'remark')
                ->first();

            $isUpdate = $existingRemark ? true : false;

            if ($isUpdate) {
                $existingRemark->remark = $request->remark;
                $existingRemark->status = $submissionStatus;
                $existingRemark->remark_type = 'remark';
                $existingRemark->save();
                $notificationMsg = "Remark for submission ID {$sub_id} account {$submission->acc_id} has been updated by {$user->sect}";
            } else {
                Remarks::create([
                    'remark_by' => $npk,
                    'sub_id' => $sub_id,
                    'remark' => $request->remark,
                    'remark_type' => 'remark',
                    'status' => $submissionStatus,
                ]);
                $notificationMsg = "New remark added for submission ID {$sub_id} account {$submission->acc_id} by {$user->sect}";
            }

            $previousApprovers = Approval::where('sub_id', $sub_id)
                ->where('approve_by', '!=', $npk)
                ->groupBy('approve_by')
                ->pluck('approve_by');

            if ($previousApprovers->isNotEmpty()) {
                $users = User::whereIn('npk', $previousApprovers)
                    ->where('dept', $submissionDept)
                    ->get();

                Log::info("Users to notify for remark on sub_id {$sub_id}: " . json_encode($users->pluck('npk')));

                foreach ($users as $notifyUser) {
                    try {
                        NotificationController::createNotification(
                            $notifyUser->npk,
                            $notificationMsg,
                            $sub_id
                        );
                        Log::info("Notification sent to NPK {$notifyUser->npk} for remark on sub_id {$sub_id}");
                    } catch (\Exception $e) {
                        Log::error("Failed to send notification to NPK {$notifyUser->npk} for sub_id {$sub_id}: " . $e->getMessage());
                    }
                }
            } else {
                Log::info("No previous approvers found for sub_id {$sub_id}");
            }

            return response()->json([
                'success' => true,
                'message' => 'Remark saved successfully'
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to save remark for sub_id {$sub_id}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to save remark: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reply(Request $request)
    {
        $request->validate([
            'sub_id' => 'required',
            'remark' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        $sub_id = $request->sub_id;

        try {
            $originalRemark = Remarks::where('sub_id', $sub_id)
                ->where('remark_type', 'remark')
                ->first();

            if (!$originalRemark) {
                return response()->json([
                    'success' => false,
                    'message' => 'Original remark not found to reply'
                ], 404);
            }

            $npk = $originalRemark->remark_by;

            $submission = BudgetPlan::where('sub_id', $sub_id)->first();

            if (!$submission || !$submission->dpt_id) {
                Log::error("No submission found or missing dpt_id for sub_id {$sub_id}");
                return response()->json([
                    'success' => false,
                    'message' => 'No submission found or missing department information'
                ], 400);
            }

            $submissionDept = $submission->dpt_id;
            $submissionStatus = $submission->status;

            $existingReply = Remarks::where('sub_id', $sub_id)
                ->where('remark_by', $npk)
                ->where('remark_type', 'reply')
                ->first();

            $isUpdate = $existingReply ? true : false;

            if ($isUpdate) {
                $existingReply->remark = $request->remark;
                $existingReply->status = $submissionStatus;
                $existingReply->save();
                $notificationMsg = "Reply to remark for submission ID {$sub_id} account {$submission->acc_id} has been updated";
            } else {
                Remarks::create([
                    'remark_by' => $npk,
                    'sub_id' => $sub_id,
                    'remark' => $request->remark,
                    'remark_type' => 'reply',
                    'status' => $submissionStatus,
                ]);
                $notificationMsg = "New reply added for submission ID {$sub_id} account {$submission->acc_id}";
            }

            $previousApprovers = Approval::where('sub_id', $sub_id)
                ->where('approve_by', '!=', $npk)
                ->groupBy('approve_by')
                ->pluck('approve_by');

            if ($previousApprovers->isNotEmpty()) {
                $users = User::whereIn('npk', $previousApprovers)
                    ->where('dept', $submissionDept)
                    ->get();

                Log::info("Users to notify for reply on sub_id {$sub_id}: " . json_encode($users->pluck('npk')));

                foreach ($users as $notifyUser) {
                    try {
                        NotificationController::createNotification(
                            $notifyUser->npk,
                            $notificationMsg,
                            $sub_id
                        );
                        Log::info("Notification sent to NPK {$notifyUser->npk} for reply on sub_id {$sub_id}");
                    } catch (\Exception $e) {
                        Log::error("Failed to send notification to NPK {$notifyUser->npk} for sub_id {$sub_id}: " . $e->getMessage());
                    }
                }
            } else {
                Log::info("No previous approvers found for sub_id {$sub_id}");
            }

            return response()->json([
                'success' => true,
                'message' => 'Reply saved successfully'
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to save reply for sub_id {$sub_id}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to save reply: ' . $e->getMessage()
            ], 500);
        }
    }


    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'sub_id' => 'required',
    //         'remark' => 'required|string|max:255',
    //     ]);

    //     $user = Auth::user();
    //     $npk = $user->npk;
    //     $sub_id = $request->sub_id;

    //     try {
    //         // Ambil departemen pengajuan dari salah satu model
    //         $models = [
    //             BudgetPlan::class,
    //             // Tambahkan model lain jika diperlukan
    //         ];
    //         $submissionDept = null;
    //         foreach ($models as $model) {
    //             $item = $model::where('sub_id', $sub_id)->first();
    //             if ($item && $item->dpt_id) {
    //                 $submissionDept = $item->dpt_id;
    //                 break;
    //             }
    //         }

    //         if (!$submissionDept) {
    //             Log::error("No submission found for sub_id {$sub_id} or missing dpt_id");
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'No submission found or missing department information'
    //             ], 400);
    //         }

    //         // Cek apakah remark dengan sub_id dan remark_by (user) sudah ada
    //         $existingRemark = Remarks::where('sub_id', $sub_id)
    //             ->where('remark_by', $npk)
    //             ->first();

    //         $isUpdate = $existingRemark ? true : false;

    //         if ($isUpdate) {
    //             // Jika sudah ada, update remark-nya
    //             $existingRemark->remark = $request->remark;
    //             $existingRemark->save();
    //             $notificationMsg = "Remark for submission ID {$sub_id} has been updated by {$user->name}";
    //         } else {
    //             // Jika belum ada, buat remark baru
    //             Remarks::create([
    //                 'remark_by' => $npk,
    //                 'sub_id' => $sub_id,
    //                 'remark' => $request->remark,
    //             ]);
    //             $notificationMsg = "New remark added for submission ID {$sub_id} by {$user->name}";
    //         }

    //         // Kirim notifikasi ke approver sebelumnya
    //         $previousApprovers = Approval::where('sub_id', $sub_id)
    //             ->where('approve_by', '!=', $npk)
    //             ->groupBy('approve_by')
    //             ->pluck('approve_by');

    //         if ($previousApprovers->isNotEmpty()) {
    //             $users = User::whereIn('npk', $previousApprovers)
    //                 ->where('dept', $submissionDept)
    //                 ->get();

    //             Log::info("Users to notify for remark on sub_id {$sub_id}: " . json_encode($users->pluck('npk')));

    //             foreach ($users as $notifyUser) {
    //                 try {
    //                     NotificationController::createNotification(
    //                         $notifyUser->npk,
    //                         $notificationMsg,
    //                         $sub_id
    //                     );
    //                     Log::info("Notification sent to NPK {$notifyUser->npk} for remark on sub_id {$sub_id}");
    //                 } catch (\Exception $e) {
    //                     Log::error("Failed to send notification to NPK {$notifyUser->npk} for sub_id {$sub_id}: " . $e->getMessage());
    //                 }
    //             }
    //         } else {
    //             Log::info("No previous approvers found for sub_id {$sub_id}");
    //         }

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Remark saved successfully'
    //         ]);
    //     } catch (\Exception $e) {
    //         Log::error("Failed to save remark for sub_id {$sub_id}: " . $e->getMessage());
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to save remark: ' . $e->getMessage()
    //         ], 500);
    //     }
    // }


    public function destroy($sub_id, $remark_id)
    {
        $currentUserNpk = Auth::user()->npk;

        // Find the remark by ID and ensure it belongs to the current user and sub_id
        $remark = Remarks::where('id', $remark_id)
            ->where('sub_id', $sub_id)
            ->where('remark_by', $currentUserNpk)
            ->first();

        if (!$remark) {
            return response()->json([
                'success' => false,
                'message' => 'Remark not found or you do not have permission to delete it',
            ], 404);
        }

        try {
            $remark->delete();
            return response()->json([
                'success' => true,
                'message' => 'Remark deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete remark: ' . $e->getMessage(),
            ], 500);
        }
    }

    // public function history($sub_id)
    // {
    //     // Fetch remarks with user details
    //     $remarks = Remarks::with('user')
    //         ->where('sub_id', $sub_id)
    //         ->orderBy('created_at', 'asc')
    //         ->get();

    //     // Build history array
    //     $history = [];

    //     foreach ($remarks as $remark) {
    //         // Get user data including sect and dept
    //         $user = User::where('npk', $remark->remark_by)->first();

    //         // Determine status based on user's sect and dept
    //         $status = 'Remark';
    //         $reply = null;
    //         if ($user) {
    //             switch ($user->sect) {
    //                 case 'Kadept':
    //                     $status = ($user->dept == '6121')
    //                         ? 'Remark by KADEPT BUDGETING'
    //                         : 'Remark by KADEPT';
    //                     break;
    //                 case 'Kadiv':
    //                     $status = 'Remark by KADIV';
    //                     break;
    //                 case 'DIC':
    //                     $status = 'Remark by DIC';
    //                     break;
    //                 case 'PIC':
    //                     $status = ($user->dept == '6121')
    //                         ? 'Remark by PIC BUDGETING'
    //                         : 'Remark by PIC';
    //                     break;
    //                 default:
    //                     $status = 'Remark by ' . $user->sect;
    //                     $reply = $remark->remark;
    //                     break;
    //             }
    //         }

    //         $history[] = [
    //             'status' => $status,
    //             'remarker' => $remark->user ? $remark->user->name : 'System',
    //             'npk' => $remark->remark_by,
    //             'date' => $remark->created_at->format('d M Y, H:i'),
    //             'remark' => $remark->remark,
    //             'reply' => $reply, // Reply for non-restricted users
    //             'sect' => $user ? $user->sect : 'System',
    //             'dept' => $user ? $user->dept : 'System',
    //             'sub_id' => $sub_id, // Add sub_id to each history item
    //         ];
    //     }

    //     // Sort by date ascending
    //     usort($history, function ($a, $b) {
    //         return strtotime($a['date']) <=> strtotime($b['date']);
    //     });

    //     return view('approvals.remark', compact('history', 'sub_id'));
    // }
    public function history($sub_id)
    {
        $remarks = Remarks::with('user')
            ->where('sub_id', $sub_id)
            ->orderBy('created_at', 'asc')
            ->get()
            ->groupBy('remark_by');

        $history = [];

        foreach ($remarks as $npk => $items) {
            $user = User::where('npk', $npk)->first();

            $mainRemark = $items->where('remark_type', 'remark')->first();
            $reply = $items->where('remark_type', 'reply')->first();

            $status = 'Remark';
            if ($user) {
                switch ($user->sect) {
                    case 'Kadept':
                        $status = ($user->dept == '6121')
                            ? 'Remark by KADEPT BUDGETING'
                            : 'Remark by KADEPT';
                        break;
                    case 'Kadiv':
                        $status = 'Remark by KADIV';
                        break;
                    case 'DIC':
                        $status = 'Remark by DIC';
                        break;
                    case 'PIC':
                        $status = ($user->dept == '6121')
                            ? 'Remark by PIC BUDGETING'
                            : 'Remark by PIC';
                        break;
                    default:
                        $status = 'Remark by ' . $user->sect;
                        break;
                }
            }

            $history[] = [
                'status'   => $status,
                'remarker' => $mainRemark?->user?->name ?? 'System',
                'npk'      => $npk,
                'date'     => $mainRemark?->created_at?->format('d M Y, H:i') ?? '-',
                'remark'   => $mainRemark?->remark ?? '-',
                'reply'    => $reply?->remark ?? '',
                'sect'     => $user?->sect ?? '-',
                'dept'     => $user?->dept ?? '-',
                'sub_id'   => $sub_id,
            ];
        }

        usort($history, function ($a, $b) {
            return strtotime($a['date']) <=> strtotime($b['date']);
        });

        return view('approvals.remark', compact('history', 'sub_id'));
    }
}
