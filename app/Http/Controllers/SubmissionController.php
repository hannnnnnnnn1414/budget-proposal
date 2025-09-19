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
use App\Models\BudgetCode;
use App\Models\BudgetPlan;
use App\Models\Currency;
use App\Models\Departments;
use App\Models\InsuranceCompany;
use App\Models\Item;
use App\Models\LineOfBusiness;
use App\Models\Remarks;
use App\Models\Training;
use App\Models\User;
use App\Models\Workcenter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class SubmissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $notificationController = new NotificationController();
        $notifications = $notificationController->getNotifications();

        // Ambil semua akun untuk Asset (kecuali CAPEX)
        $assetSubmissions = Account::where('acc_id', '!=', 'CAPEX')->get();

        // Ambil hanya akun CAPEX untuk Expenditure
        $expenditureSubmissions = Account::where('acc_id', '=', 'CAPEX')->get();

        return view('submissions.index', [
            'assetSubmissions' => $assetSubmissions,
            'expenditureSubmissions' => $expenditureSubmissions,
            'notifications' => $notifications
        ]);
    }
    // public function index()
    // {
    //     $notificationController = new NotificationController();
    //     $notifications = $notificationController->getNotifications();
    //     $submissions = Account::all();
    //     return view('submissions.index', ['submissions' => $submissions, 'notifications' => $notifications]);
    // }
    // public function indexKadept()
    // {
    //     return view('sub-kadept');
    // }
    // public function indexKadiv()
    // {
    //     return view('sub-kadiv');
    // }
    // public function indexDIC()
    // {
    //     return view('sub-dic');
    // }

    /**
     * Show the form for creating a new resource.
     */
    public function detail($acc_id)
{
    $notificationController = new NotificationController();
    $notifications = $notificationController->getNotifications();
    $account = Account::where('acc_id', $acc_id)->firstOrFail();
    $account_name = $account->account;
    $submissions = collect();
    $deptId = session('dept'); // Ambil dept dari session sekali saja

    // Daftar acc_id yang termasuk template 'general'
    $genexp = [
        'SGAREPAIR', 'SGABOOK', 'SGAMARKT', 'FOHTECHDO', 'FOHRECRUITING', 'SGARECRUITING',
        'SGARENT', 'SGAADVERT', 'SGACOM', 'SGAOFFICESUP', 'SGAASSOCIATION', 'SGABCHARGES',
        'SGACONTRIBUTION', 'FOHPACKING', 'SGARYLT', 'FOHAUTOMOBILE', 'FOHPROF', 'FOHRENT',
        'FOHTAXPUB', 'SGAAUTOMOBILE', 'SGAPROF', 'SGATAXPUB', 'SGAOUTSOURCING'
    ];

    // Tentukan departemen yang boleh diakses berdasarkan departemen user
    $allowedDepts = [$deptId]; // Default: hanya departemen sendiri

    // Untuk user dengan dept 4131, izinkan melihat multiple departments
    if ($deptId === '4131') {
        $allowedDepts = ['4131', '1111', '1131', '1151', '1211', '1231', '7111'];
    } 
    // Untuk user dengan dept 4111, izinkan melihat multiple departments
    elseif ($deptId === '4111') {
        $allowedDepts = ['4111', '1116', '1140', '1160', '1224', '1242', '7111'];
    }

    // Untuk semua kategori akun, gunakan allowedDepts yang sudah ditentukan
    if (in_array($acc_id, $genexp)) {
        $submissions = BudgetPlan::select('sub_id', 'status', 'created_at', 'purpose')
            ->where('status', '!=', 0)
            ->where('acc_id', $acc_id)
            ->whereIn('dpt_id', $allowedDepts)
            ->get()
            ->unique('sub_id');
    } elseif ($acc_id === 'PURCHASEMATERIAL') {
        $submissions = BudgetPlan::select('sub_id', 'status', 'created_at', 'purpose')
            ->where('status', '!=', 0)
            ->where('acc_id', $acc_id)
            ->whereIn('dpt_id', $allowedDepts)
            ->get()
            ->unique('sub_id');
    } elseif (in_array($acc_id, ['FOHEMPLOYCOMPDL', 'FOHEMPLOYCOMPIL', 'SGAEMPLOYCOMP'])) {
        $submissions = BudgetPlan::select('sub_id', 'status', 'created_at', 'purpose')
            ->where('status', '!=', 0)
            ->where('acc_id', $acc_id)
            ->whereIn('dpt_id', $allowedDepts)
            ->get()
            ->unique('sub_id');
    } elseif (in_array($acc_id, ['FOHTOOLS', 'FOHFS', 'FOHINDMAT', 'FOHREPAIR'])) {
        $submissions = BudgetPlan::select('sub_id', 'status', 'created_at', 'purpose')
            ->where('status', '!=', 0)
            ->where('acc_id', $acc_id)
            ->whereIn('dpt_id', $allowedDepts)
            ->get()
            ->unique('sub_id');
    } elseif (in_array($acc_id, ['FOHENTERTAINT', 'FOHREPRESENTATION', 'SGAENTERTAINT', 'SGAREPRESENTATION'])) {
        $submissions = BudgetPlan::select('sub_id', 'status', 'created_at', 'purpose')
            ->where('status', '!=', 0)
            ->where('acc_id', $acc_id)
            ->whereIn('dpt_id', $allowedDepts)
            ->get()
            ->unique('sub_id');
    } elseif (in_array($acc_id, ['FOHINSPREM', 'SGAINSURANCE'])) {
        $submissions = BudgetPlan::select('sub_id', 'status', 'created_at', 'purpose')
            ->where('status', '!=', 0)
            ->where('acc_id', $acc_id)
            ->whereIn('dpt_id', $allowedDepts)
            ->get()
            ->unique('sub_id');
    } elseif (in_array($acc_id, ['FOHPOWER', 'SGAPOWER'])) {
        $submissions = BudgetPlan::select('sub_id', 'status', 'created_at', 'purpose')
            ->where('status', '!=', 0)
            ->where('acc_id', $acc_id)
            ->whereIn('dpt_id', $allowedDepts)
            ->get()
            ->unique('sub_id');
    } elseif (in_array($acc_id, ['FOHTRAV', 'SGATRAV'])) {
        $submissions = BudgetPlan::select('sub_id', 'status', 'created_at', 'purpose')
            ->where('status', '!=', 0)
            ->where('acc_id', $acc_id)
            ->whereIn('dpt_id', $allowedDepts)
            ->get()
            ->unique('sub_id');
    } elseif (in_array($acc_id, ['FOHTRAINING', 'SGATRAINING'])) {
        $submissions = BudgetPlan::select('sub_id', 'status', 'created_at', 'purpose')
            ->where('status', '!=', 0)
            ->where('acc_id', $acc_id)
            ->whereIn('dpt_id', $allowedDepts)
            ->get()
            ->unique('sub_id');
    } elseif ($acc_id === 'SGAAFTERSALES') {
        $submissions = BudgetPlan::select('sub_id', 'status', 'created_at', 'purpose')
            ->where('status', '!=', 0)
            ->where('acc_id', $acc_id)
            ->whereIn('dpt_id', $allowedDepts)
            ->get()
            ->unique('sub_id');
    } elseif ($acc_id === 'CAPEX') {
        $submissions = BudgetPlan::select('sub_id', 'status', 'created_at', 'purpose')
            // ->where('status', '!=', 0)
            ->where('acc_id', $acc_id)
            ->whereIn('dpt_id', $allowedDepts)
            ->get()
            ->unique('sub_id');
    }

    Log::info("Menampilkan detail untuk acc_id: $acc_id, deptId: $deptId, allowedDepts: " . json_encode($allowedDepts));

        // Untuk debugging, tambahkan ini:
    $debugData = BudgetPlan::where('acc_id', $acc_id)
        ->whereIn('dpt_id', $allowedDepts)
        ->get();
    
    Log::info("Data ditemukan: " . $debugData->count() . " records");
    Log::info("Data: " . json_encode($debugData->toArray()));


    return view('submissions.detail', compact('submissions', 'account_name', 'notifications'));
}

    public function submit($sub_id)
    {
        $user = Auth::user();
        // Log sesi pengguna untuk memastikan autentikasi dan atribut
        Log::info("Memulai fungsi submit untuk sub_id {$sub_id}", [
            'npk' => $user->npk ?? null,
            'sect' => $user->sect ?? null,
            'dept' => $user->dept ?? null
        ]);
        
        if (!$user || !$user->npk || !$user->sect || !$user->dept) {
            Log::error("User tidak terautentikasi atau atribut tidak lengkap untuk sub_id {$sub_id}");
            return redirect()->back()->with('error', 'User tidak terautentikasi atau informasi tidak lengkap.');
        }

        $models = [
            BudgetPlan::class,
            // AfterSalesService::class,
            // InsurancePrem::class,
            // SupportMaterial::class,
            // TrainingEducation::class,
            // Utilities::class,
            // BusinessDuty::class,
            // RepresentationExpense::class,
        ];

        $updated = false;
        $directDIC = ['4211', '6111', '6121']; // Departments that skip Kadiv approval
        $newStatus = null;
        $approvalStatus = null;
        $notificationMsg = '';
        $submitterMsg = '';
        $recipients = collect();
        $submissionDept = null; // Untuk menyimpan dept dari submission
        $submissionDeptName = null; // Untuk menyimpan nama departemen

        // Ambil dept dari salah satu item untuk notifikasi sesuai dept
        foreach ($models as $model) {
            $item = $model::where('sub_id', $sub_id)->first();
            if ($item && $item->dpt_id) {
                $submissionDept = $item->dpt_id;
                $dept = Departments::where('dpt_id', $submissionDept)->first();
                $submissionDeptName = $dept ? $dept->department : $submissionDept; // Fallback ke dpt_id jika tidak ditemukan
                Log::info("Departemen pengajuan ditemukan untuk sub_id {$sub_id}", [
                    'submissionDept' => $submissionDept,
                    'submissionDeptName' => $submissionDeptName
                ]);
                break;
            }
        }

        if (!$submissionDept) {
            Log::error("Tidak ada pengajuan ditemukan untuk sub_id {$sub_id} atau dpt_id tidak ada");
            return redirect()->back()->with('error', 'Pengajuan tidak ditemukan atau informasi departemen tidak ada.');
        }

        foreach ($models as $model) {
            $items = $model::where('sub_id', $sub_id)->get();
            Log::info("Memproses item untuk sub_id {$sub_id}, Model: {$model}", [
                'item_count' => $items->count()
            ]);
            
            foreach ($items as $item) {
                Log::info("Memeriksa item untuk sub_id {$sub_id}, Model: {$model}", [
                    'item_status' => $item->status,
                    'user_sect' => $user->sect,
                    'user_dept' => $user->dept,
                    'submission_dept' => $submissionDept
                ]);

                if (in_array($item->status, [1, 8])) { // User biasa submit
                    $newStatus = 2;
                    $approvalStatus = 2;
                    $notificationMsg = "New submission ID {$sub_id} needs your approval";
                    $submitterMsg = ''; // Tidak perlu notify submitter di sini
                    $recipients = User::where('sect', 'Kadept')
                        ->where('dept', $submissionDept)
                        // ->where('dept', '!=', '6121')
                        ->get();

                    $item->status = $newStatus;
                    if ($item->month === '-') {
                        $item->month = now()->format('d-m-Y');
                    }
                    if ($item->save()) {
                        Log::info("Item disimpan untuk sub_id {$sub_id}, Model: {$model}, Status: {$newStatus}");
                        $updated = true;
                    } else {
                        Log::error("Gagal menyimpan item untuk sub_id {$sub_id}, Model: {$model}, Status: {$newStatus}");
                    }
                } 
                // Kadept approve - MODIFIKASI: Tambah kondisi untuk handle Kadept 4131 approve submission 7111
                elseif (in_array($item->status, [2, 9]) && $user->sect === 'Kadept' && ($user->dept === $submissionDept || 
                    ($user->dept === '4131' && in_array($submissionDept, ['1111', '1131', '1151', '1211', '1231', '7111'])) ||
                    ($user->dept === '4111' && in_array($submissionDept, ['1116', '1140', '1160', '1224', '1242', '7111'])))) { 
                    if (in_array($user->dept, $directDIC)) {
                        $newStatus = 4;
                        $approvalStatus = 4;
                        $notificationMsg = "New submission ID {$sub_id} needs your approval";
                        $submitterMsg = "Your submission ID {$sub_id} account {$item->acc_id} has been forwarded to DIC";
                        $recipients = User::where('sect', 'DIC')
                            ->where('dept', $submissionDept)
                            ->get();
                    } else {
                        $newStatus = 3;
                        $approvalStatus = 3;
                        $notificationMsg = "New submission ID {$sub_id} needs your approval";
                        $submitterMsg = "Your submission ID {$sub_id} account {$item->acc_id} has been approved by Kadept";
                        $recipients = User::where('sect', 'Kadiv')
                            ->where('dept', $submissionDept)
                            ->get();
                    }

                    $item->status = $newStatus;
                    if ($item->save()) {
                        Log::info("Item disimpan untuk sub_id {$sub_id}, Model: {$model}, Status: {$newStatus}");
                        $updated = true;
                    } else {
                        Log::error("Gagal menyimpan item untuk sub_id {$sub_id}, Model: {$model}, Status: {$newStatus}");
                    }
                } 
                // Kadiv approve - MODIFIKASI: Tambah kondisi untuk handle Kadiv yang approve submission dari departemen lain
                elseif (in_array($item->status, [3, 10]) && $user->sect === 'Kadiv' && ($user->dept === $submissionDept || 
                    ($user->dept === '4131' && in_array($submissionDept, ['1111', '1131', '1151', '1211', '1231', '7111'])) ||
                    ($user->dept === '4111' && in_array($submissionDept, ['1116', '1140', '1160', '1224', '1242', '7111'])))) { 
                    $newStatus = 4;
                    $approvalStatus = 4;
                    $notificationMsg = "New submission ID {$sub_id} needs your approval";
                    $submitterMsg = "Your submission ID {$sub_id} account {$item->acc_id} has been approved by Kadiv";
                    $recipients = User::where('sect', 'DIC')
                        ->where('dept', $submissionDept) // Filter DIC by submission department
                        ->get();

                    $item->status = $newStatus;
                    if ($item->save()) {
                        Log::info("Item disimpan untuk sub_id {$sub_id}, Model: {$model}, Status: {$newStatus}");
                        $updated = true;
                    } else {
                        Log::error("Gagal menyimpan item untuk sub_id {$sub_id}, Model: {$model}, Status: {$newStatus}");
                    }
                } 
                // DIC approve - MODIFIKASI: Tambah kondisi untuk handle DIC yang approve submission dari departemen lain
                elseif (in_array($item->status, [4, 11]) && $user->sect === 'DIC' && ($user->dept === $submissionDept || 
                    ($user->dept === '4131' && in_array($submissionDept, ['1111', '1131', '1151', '1211', '1231', '7111'])) ||
                    ($user->dept === '4111' && in_array($submissionDept, ['1116', '1140', '1160', '1224', '1242', '7111'])))) { 
                    Log::info("Blok approval DIC dijalankan untuk sub_id {$sub_id}", [
                        'item_status' => $item->status,
                        'user_sect' => $user->sect,
                        'user_dept' => $user->dept,
                        'submission_dept' => $submissionDept
                    ]);
                    
                    $newStatus = 5;
                    $approvalStatus = 5;
                    $notificationMsg = "New submission ID {$sub_id} from department {$submissionDeptName} needs your approval";
                    $submitterMsg = "Your submission ID {$sub_id} account {$item->acc_id} has been approved by DIC";
                    $recipients = User::where('sect', 'PIC')
                        ->where('dept', '6121')
                        ->get();

                    $item->status = $newStatus;
                    try {
                        if ($item->save()) {
                            Log::info("Item disimpan untuk sub_id {$sub_id}, Model: {$model}, Status: {$newStatus}");
                            Approval::create([
                                'approve_by' => $user->npk,
                                'sub_id' => $sub_id,
                                'status' => $approvalStatus,
                                'created_at' => now(),
                                'updated_at' => now()
                            ]);
                            Log::info("Record approval dibuat untuk sub_id {$sub_id}, Status: {$approvalStatus}, Approved by: {$user->npk}");
                            $updated = true;
                        } else {
                            Log::error("Gagal menyimpan item untuk sub_id {$sub_id}, Model: {$model}, Status: {$newStatus}");
                        }
                    } catch (\Exception $e) {
                        Log::error("Kesalahan saat menyimpan item atau membuat approval untuk sub_id {$sub_id}: " . $e->getMessage());
                    }
                } elseif (in_array($item->status, [5, 12]) && $user->sect === 'PIC' && $user->dept === '6121') { // PIC P&B approve
                    $newStatus = 6;
                    $approvalStatus = 6;
                    $notificationMsg = "New submission ID {$sub_id} from department {$submissionDeptName} needs your approval";
                    $submitterMsg = "Your submission ID {$sub_id} account {$item->acc_id} has been approved by PIC P&B";
                    $recipients = User::where('sect', 'Kadept')
                        ->where('dept', '6121')
                        ->get();

                    $item->status = $newStatus;
                    if ($item->save()) {
                        Log::info("Item disimpan untuk sub_id {$sub_id}, Model: {$model}, Status: {$newStatus}");
                        $updated = true;
                    } else {
                        Log::error("Gagal menyimpan item untuk sub_id {$sub_id}, Model: {$model}, Status: {$newStatus}");
                    }
                } elseif ($item->status == 6 && $user->sect === 'Kadept' && $user->dept === '6121') { // Kadept P&B approve
                    $newStatus = 7;
                    $approvalStatus = 7;
                    $notificationMsg = "Submission ID {$sub_id} has been fully approved";
                    $submitterMsg = "Your submission ID {$sub_id} account {$item->acc_id} has been fully approved by Kadept P&B";
                    $recipients = collect(); // Tidak ada approver berikutnya

                    $item->status = $newStatus;
                    if ($item->save()) {
                        Log::info("Item disimpan untuk sub_id {$sub_id}, Model: {$model}, Status: {$newStatus}");
                        $updated = true;
                    } else {
                        Log::error("Gagal menyimpan item untuk sub_id {$sub_id}, Model: {$model}, Status: {$newStatus}");
                    }
                }
            }
        }

        // Buat approval record dan kirim notifikasi sekali per sub_id
        if ($updated && $newStatus !== null) {
            Approval::create([
                'approve_by' => $user->npk,
                'sub_id' => $sub_id,
                'status' => $approvalStatus,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            Log::info("Record approval tambahan dibuat untuk sub_id {$sub_id}, Status: {$approvalStatus}, Approved by: {$user->npk}");

            // Kirim notifikasi ke approver berikutnya
            foreach ($recipients as $recipient) {
                try {
                    NotificationController::createNotification(
                        $recipient->npk,
                        $notificationMsg,
                        $sub_id
                    );
                    Log::info("Notifikasi dikirim ke {$recipient->sect} NPK {$recipient->npk} untuk sub_id {$sub_id}");
                } catch (\Exception $e) {
                    Log::error("Gagal mengirim notifikasi ke {$recipient->sect} NPK {$recipient->npk} untuk sub_id {$sub_id}: " . $e->getMessage());
                }
            }

            // Kirim notifikasi ke submitter dan approver sebelumnya
            if ($submitterMsg) {
                $approvalRecords = Approval::where('sub_id', $sub_id)
                    ->whereIn('status', [1, 2, 3, 4, 5]) // Sesuai tahap approval
                    ->where('status', '<', $newStatus)
                    ->pluck('approve_by')
                    ->unique();
                $users = User::whereIn('npk', $approvalRecords)
                    ->where('dept', $submissionDept) // Hanya notifikasi sesuai dept
                    ->get();

                foreach ($users as $notifyUser) {
                    try {
                        NotificationController::createNotification(
                            $notifyUser->npk,
                            $submitterMsg,
                            $sub_id
                        );
                        Log::info("Notifikasi dikirim ke NPK {$notifyUser->npk} untuk sub_id {$sub_id}");
                    } catch (\Exception $e) {
                        Log::error("Gagal mengirim notifikasi ke NPK {$notifyUser->npk} untuk sub_id {$sub_id}: " . $e->getMessage());
                    }
                }
            }
        }

        if ($updated) {
            Log::info("Pengajuan berhasil diperbarui untuk sub_id {$sub_id}");
            return redirect()->back()->with('success', 'All related submissions have been sent for review.');
        }

        Log::error("Tidak ada pengajuan yang diperbarui untuk sub_id {$sub_id}");
        return redirect()->back()->with('error', 'No related submissions found or unauthorized action.');
    }

    public function disapprove($sub_id)
    {
        $user = Auth::user();
        if (!$user || !$user->npk || !$user->sect || !$user->dept) {
            Log::error("User not authenticated or missing required attributes for sub_id {$sub_id}");
            return redirect()->back()->with('error', 'User not authenticated or missing required information.');
        }

        $sect = $user->sect;
        $dept = $user->dept;
        $models = [
            BudgetPlan::class,
        ];

        $updated = false;
        $notificationMsg = '';
        $submitterMsg = '';
        $recipients = collect();
        $submissionDept = null;
        $finalDisapprovalStatus = null; // ✅ Tambahkan ini untuk digunakan di luar loop

        // Get department from submission
        foreach ($models as $model) {
            $item = $model::where('sub_id', $sub_id)->first();
            if ($item && $item->dpt_id) {
                $submissionDept = $item->dpt_id;
                break;
            }
        }

        if (!$submissionDept) {
            Log::error("No submission found for sub_id {$sub_id} or missing dpt_id");
            return redirect()->back()->with('error', 'No submission found or missing department information.');
        }

        foreach ($models as $model) {
            $items = $model::where('sub_id', $sub_id)->get();
            foreach ($items as $item) {
                $disapprovalStatus = null;

                // Determine disapproval status and messages
                if (($item->status == 2 || $item->status == 9) && $sect === 'Kadept' && (
                    $dept === $submissionDept || ($dept === '4131' && in_array($submissionDept, ['1111', '1131', '1151', '1211', '1231', '7111'])))) {
                    $disapprovalStatus = 8;
                    // $submitterMsg = "Your submission ID {$sub_id} has been disapproved by Kadept";
                    $notificationMsg = "Submission ID {$sub_id} account {$item->acc_id} has been disapproved by Kadept";

                    if (in_array($submissionDept, ['4211', '6111', '6121'])) {
                        $recipients = User::where(function ($query) {
                            $query->where('sect', 'DIC')->orWhere('sect', 'Kadept');
                        })->where('dept', $submissionDept)
                            ->where('npk', '!=', $user->npk)
                            ->get();
                    } else {
                        $recipients = User::where('sect', 'Kadept')
                            ->where('dept', $submissionDept)
                            ->where('npk', '!=', $user->npk)
                            ->get();
                    }
                } elseif ($item->status == 3 && $sect === 'Kadiv' && $dept === $submissionDept) {
                    $disapprovalStatus = 9;
                    // $submitterMsg = "Your submission ID {$sub_id} has been disapproved by Kadiv";
                    $notificationMsg = "Submission ID {$sub_id} account {$item->acc_id} has been disapproved by Kadiv";
                    $recipients = User::where('sect', 'Kadept')
                        ->where('dept', $submissionDept)
                        ->where('npk', '!=', $user->npk)
                        ->get();
                } elseif ($item->status == 4 && $sect === 'DIC') {
                    $disapprovalStatus = 10;
                    // $submitterMsg = "Your submission ID {$sub_id} has been disapproved by DIC";
                    $notificationMsg = "Submission ID {$sub_id} account {$item->acc_id} has been disapproved by DIC";

                    if (in_array($submissionDept, ['4211', '6111', '6121'])) {
                        $recipients = User::where('sect', 'Kadept')
                            ->whereIn('dept', ['4211', '6111', '6121'])
                            ->where('npk', '!=', $user->npk)
                            ->get();
                    } else {
                        $recipients = User::whereIn('sect', ['Kadiv', 'Kadept'])
                            ->where('dept', $submissionDept)
                            ->where('npk', '!=', $user->npk)
                            ->get();
                    }
                } elseif ($item->status == 5 && $sect === 'PIC' && $dept === '6121') {
                    $disapprovalStatus = 11;
                    // $submitterMsg = "Your submission ID {$sub_id} has been disapproved by PIC P&B";
                    $notificationMsg = "Submission ID {$sub_id} account {$item->acc_id} has been disapproved by PIC P&B";
                    $recipients = User::where('sect', 'Kadept')
                        ->where('dept', '6121')
                        ->where('npk', '!=', $user->npk)
                        ->get();
                } elseif ($item->status == 6 && $sect === 'Kadept' && $dept === '6121') {
                    $disapprovalStatus = 12;
                    // $submitterMsg = "Your submission ID {$sub_id} has been disapproved by Kadept P&B";
                    $notificationMsg = "Submission ID {$sub_id} account {$item->acc_id} has been disapproved by Kadept P&B";
                    $recipients = User::where('sect', 'PIC')
                        ->where('dept', '6121')
                        ->where('npk', '!=', $user->npk)
                        ->get();
                }

                if ($disapprovalStatus !== null) {
                    $item->status = $disapprovalStatus;
                    if ($item->save()) {
                        $updated = true;
                        $finalDisapprovalStatus = $disapprovalStatus; // ✅ simpan untuk digunakan di luar loop

                        Approval::create([
                            'approve_by' => $user->npk,
                            'sub_id' => $sub_id,
                            'status' => $disapprovalStatus
                        ]);

                        Log::info("Item saved for sub_id {$sub_id}, Model: {$model}, Status: {$disapprovalStatus}");
                    } else {
                        Log::error("Failed to save item for sub_id {$sub_id}, Model: {$model}, Status: {$disapprovalStatus}");
                    }
                }
            }
        }

        if ($updated && $finalDisapprovalStatus !== null) {
            // Notify previous approvers
            $approvalRecords = Approval::where('sub_id', $sub_id)
                ->whereIn('status', [1, 2, 3, 4, 5])
                ->where('status', '<', $finalDisapprovalStatus)
                ->pluck('approve_by')
                ->unique();

            $users = User::whereIn('npk', $approvalRecords)
                ->where('dept', $submissionDept)
                ->get();

            $allRecipients = $users->merge($recipients)
                ->unique('npk') // hilangkan duplikat berdasarkan npk
                ->reject(fn($r) => $r->npk === $user->npk); // opsional: hindari notifikasi ke diri sendiri

            // Kirim notifikasi hanya sekali ke masing-masing npk
            foreach ($allRecipients as $recipient) {
                try {
                    NotificationController::createNotification(
                        $recipient->npk,
                        $notificationMsg,
                        $sub_id
                    );
                    Log::info("Notification sent to {$recipient->npk} for disapproval of sub_id {$sub_id}");
                } catch (\Exception $e) {
                    Log::error("Failed to send notification to {$recipient->npk} for sub_id {$sub_id}: " . $e->getMessage());
                }
            }

            return redirect()->back()->with('success', 'Status berhasil diperbarui berdasarkan sub_id.');
        }

        return redirect()->back()->with('error', 'Tidak ada pengajuan yang ditemukan untuk diperbarui.');
    }

    public function reportKadept($sub_id)
    {
        $notificationController = new NotificationController();
        $notifications = $notificationController->getNotifications();
        $deptId = session('dept');

        $budgetPlans = BudgetPlan::with(['item', 'dept', 'workcenter', 'approvals', 'line_business'])
            ->where('sub_id', $sub_id)
            ->where('status', '!=', 0)
            ->get();

        $submissions = collect($budgetPlans);

        $acc_id = '';
        if ($budgetPlans->isNotEmpty()) {
            $acc_id = $budgetPlans->first()->acc_id;
        }

        $account = Account::where('acc_id', $acc_id)->first();
        $account_name = $account ? $account->account : 'Unknown';
        $items = Item::orderBy('item', 'asc')->get()->pluck('item', 'itm_id');
        $departments = Departments::orderBy('department', 'asc')->get()->pluck('department', 'dpt_id');
        $workcenters = Workcenter::orderBy('workcenter', 'asc')->get()->pluck('workcenter', 'wct_id');
        $budgets = BudgetCode::orderBy('budget_name', 'asc')->get()->pluck('budget_name', 'bdc_id');
        $line_businesses = LineOfBusiness::orderBy('line_business', 'asc')->get()->pluck('line_business', 'lob_id');
        $insurances = InsuranceCompany::orderBy('company', 'asc')->get()->pluck('company', 'ins_id');

        // [MODIFIKASI] Mapping angka bulan ke nama bulan
        $monthMapping = [
            0 => 'January',
            1 => 'February', 
            2 => 'March',
            3 => 'April',
            4 => 'May',
            5 => 'June',
            6 => 'July',
            7 => 'August',
            8 => 'September',
            9 => 'October',
            10 => 'November',
            11 => 'December',
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

        $groupedItems = [];
        if ($submissions->isNotEmpty()) {
            $groupedItems = $submissions->groupBy(function($item) {
                return ($item->itm_id ?? '') . '-' . ($item->description ?? '');
            })->map(function($group) use ($monthMapping) {
                $first = $group->first();
                $monthsData = [];
                $totalPrice = 0;
                
                foreach ($group as $submission) {
                    $monthValue = $submission->month;
                    
                    // Konversi nilai bulan ke nama bulan
                    $monthName = isset($monthMapping[$monthValue]) ? $monthMapping[$monthValue] : null;
                    
                    if ($monthName) {
                        $monthsData[$monthName] = $submission->price;
                        $totalPrice += $submission->price;
                    }
                }
                
                return [
                    'item' => $first->itm_id ?? '',
                    'description' => $first->description ?? '',
                    'unit' => $first->unit ?? '',
                    'quantity' => $first->quantity ?? '',
                    'price' => $first->price ?? 0,
                    'workcenter' => $first->workcenter != null ? $first->workcenter->workcenter : $first->wct_id ?? '',
                    'department' => $first->dept != null ? $first->dept->department : $first->dpt_id ?? '',
                    'budget' => $first->budget != null ? $first->budget->budget_name : '-',
                    'line_business' => $first->line_business != null ? $first->line_business->line_business : $first->lob_id ?? '',
                    'months' => $monthsData,
                    'total' => $totalPrice,
                    'sub_id' => $first->sub_id,
                    'id' => $first->id,
                    'status' => $first->status,
                ];
            })->values();
        }

        $months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        $monthLabels = [
            'January' => 'Jan',
            'February' => 'Feb',
            'March' => 'Mar',
            'April' => 'Apr',
            'May' => 'May',
            'June' => 'Jun',
            'July' => 'Jul',
            'August' => 'Aug',
            'September' => 'Sep',
            'October' => 'Oct',
            'November' => 'Nov',
            'December' => 'Dec',
        ];

        $viewData = [
            'account' => $account,
            'items' => $items,
            'insurances' => $insurances,
            'departments' => $departments,
            'workcenters' => $workcenters,
            'line_businesses' => $line_businesses,
            'budgets' => $budgets,
            'acc_id' => $sub_id,
            'submissions' => $submissions,
            'sub_id' => $sub_id,
            'account_name' => $account_name,
            'notifications' => $notifications,
            'groupedItems' => $groupedItems,
            'months' => $months,
            'monthLabels' => $monthLabels,
        ];

        if (in_array($acc_id, ['SGABOOK', 'SGAREPAIR', 'SGAMARKT', 'FOHTECHDO', 'FOHRECRUITING', 'SGARECRUITING', 'SGARENT', 'SGAADVERT', 'SGACOM', 'SGAOFFICESUP', 'SGAASSOCIATION', 'SGABCHARGES', 'SGACONTRIBUTION', 'FOHPACKING', 'SGARYLT', 'FOHAUTOMOBILE', 'FOHPROF', 'FOHRENT', 'FOHTAXPUB', 'SGAAUTOMOBILE', 'SGAPROF', 'SGATAXPUB', 'SGAOUTSOURCING'])) {
            return view('reports.genKadept', $viewData);
        } elseif ($acc_id === 'PURCHASEMATERIAL') { 
            return view('reports.purchaseMaterialReport', $viewData);
        } elseif (in_array($acc_id, ['FOHTOOLS', 'FOHFS', 'FOHINDMAT', 'FOHREPAIR'])) {
            return view('reports.suppKadept', $viewData);
        } elseif (in_array($acc_id, ['FOHEMPLOYCOMPDL', 'FOHEMPLOYCOMPIL', 'SGAEMPLOYCOMP'])) {
            return view('reports.employeeReport', $viewData);
        } elseif (in_array($acc_id, ['FOHENTERTAINT', 'FOHREPRESENTATION', 'SGAENTERTAINT', 'SGAREPRESENTATION'])) {
            return view('reports.repKadept', $viewData);
        } elseif (in_array($acc_id, ['FOHINSPREM', 'SGAINSURANCE'])) {
            return view('reports.insKadept', $viewData);
        } elseif (in_array($acc_id, ['FOHPOWER', 'SGAPOWER'])) {
            return view('reports.utlKadept', $viewData);
        } elseif (in_array($acc_id, ['FOHTRAV', 'SGATRAV'])) {
            return view('reports.businessKadept', $viewData);
        } elseif (in_array($acc_id, ['FOHTRAINING', 'SGATRAINING'])) {
            return view('reports.trainKadept', $viewData);
        } elseif ($acc_id === 'SGAAFTERSALES') {
            return view('reports.afterKadept', $viewData);
        } elseif ($acc_id === 'CAPEX') {
            return view('reports.expKadept', $viewData);
        }

        return redirect()->back()->with('error', 'No submissions found for the given ID.');
    }

    public function report($sub_id)
    {
        $notificationController = new NotificationController();
        $notifications = $notificationController->getNotifications();
        $deptId = session('dept'); // ambil dept dari session

        // Ambil semua data berdasarkan sub_id dan status != 0
        // $generalExpenses = GeneralExpense::with(['item', 'dept', 'workcenter'])
        //     ->where('sub_id', $sub_id)
        //     ->where('status', '!=', 0)
        //     ->get();

        // $supportMaterials = SupportMaterial::with(['item', 'dept', 'workcenter'])
        //     ->where('sub_id', $sub_id)
        //     ->where('status', '!=', 0)
        //     ->get();

        // $insurancePrems = InsurancePrem::with(['item', 'dept', 'workcenter'])
        //     ->where('sub_id', $sub_id)
        //     ->where('status', '!=', 0)
        //     ->get();

        // $utilities = Utilities::with(['item', 'dept', 'workcenter'])
        //     ->where('sub_id', $sub_id)
        //     ->where('status', '!=', 0)
        //     ->get();

        // $businessDuties = BusinessDuty::with(['item', 'dept', 'workcenter'])
        //     ->where('sub_id', $sub_id)
        //     ->where('status', '!=', 0)
        //     ->get();

        // $repExpenses = RepresentationExpense::with(['item', 'dept', 'workcenter'])
        //     ->where('sub_id', $sub_id)
        //     ->where('status', '!=', 0)
        //     ->get();

        // $trainingEdus = TrainingEducation::with(['item', 'dept', 'workcenter'])
        //     ->where('sub_id', $sub_id)
        //     ->where('status', '!=', 0)
        //     ->get();

        // $aftersales = AfterSalesService::with(['item', 'dept', 'workcenter'])
        //     ->where('sub_id', $sub_id)
        //     ->where('status', '!=', 0)
        //     ->get();

        $budgetPlans = BudgetPlan::with(['item', 'dept', 'workcenter', 'approvals', 'line_business']) // [MODIFIKASI] Tambah relasi lineOfBusiness
            ->where('sub_id', $sub_id)
            ->where('status', '!=', 0)
            ->get();

        $submissions = collect($budgetPlans);
        // Gabungkan semua data
        // $submissions = collect()
        //     ->merge($generalExpenses)
        //     ->merge($supportMaterials)
        //     ->merge($insurancePrems)
        //     ->merge($utilities)
        //     ->merge($businessDuties)
        //     ->merge($repExpenses)
        //     ->merge($trainingEdus)
        //     ->merge($aftersales);

        // Ambil acc_id dari salah satu koleksi
        $acc_id = '';
        if ($budgetPlans->isNotEmpty()) {
            $acc_id = $budgetPlans->first()->acc_id;
        }

        // Ambil nama account
        $account = Account::where('acc_id', $acc_id)->first();
        $account_name = $account ? $account->account : 'Tidak Diketahui'; // [MODIFIKASI] Ubah 'Unknown' ke 'Tidak Diketahui'
        $items = Item::orderBy('item', 'asc')->get()->pluck('item', 'itm_id');
        $departments = Departments::orderBy('department', 'asc')->get()->pluck('department', 'dpt_id');
        $workcenters = Workcenter::orderBy('workcenter', 'asc')->get()->pluck('workcenter', 'wct_id');
        $budgets = BudgetCode::orderBy('budget_name', 'asc')->get()->pluck('budget_name', 'bdc_id');
        $line_businesses = LineOfBusiness::orderBy('line_business', 'asc')->get()->pluck('line_business', 'lob_id');
        $insurances = InsuranceCompany::orderBy('company', 'asc')->get()->pluck('company', 'ins_id');
        // [MODIFIKASI] Ambil data currencies
        $currencies = Currency::where('status', 1)->get()->mapWithKeys(function ($currency) {
            return [$currency->cur_id => ['currency' => $currency->currency, 'nominal' => $currency->nominal]];
        });

        // Tentukan view yang sesuai
        $viewData = [
            'account' => $account, // [MODIFIKASI] Tambahkan variabel $account ke viewData
            'items' => $items,
            'insurances' => $insurances,
            'departments' => $departments,
            'workcenters' => $workcenters,
            'line_businesses' => $line_businesses,
            'budgets' => $budgets,
            'acc_id' => $sub_id,
            'submissions' => $submissions,
            'sub_id' => $sub_id,
            'account_name' => $account_name,
            'notifications' => $notifications,
            'currencies' => $currencies // [MODIFIKASI] Tambahkan currencies ke viewData
        ];

        if (in_array($acc_id, ['SGABOOK', 'SGAREPAIR', 'SGAMARKT', 'FOHTECHDO', 'FOHRECRUITING', 'SGARECRUITING', 'SGARENT', 'SGAADVERT', 'SGACOM', 'SGAOFFICESUP', 'SGAASSOCIATION', 'SGABCHARGES', 'SGACONTRIBUTION', 'FOHPACKING', 'SGARYLT', 'FOHAUTOMOBILE', 'FOHPROF', 'FOHRENT', 'FOHTAXPUB', 'SGAAUTOMOBILE', 'SGAPROF', 'SGATAXPUB', 'SGAOUTSOURCING'])) {
            return view('reports.generalReport', $viewData);
        } elseif ($acc_id === 'PURCHASEMATERIAL') { // [MODIFIKASI] Tambah kondisi untuk PURCHASEMATERIAL
            return view('reports.purchaseMaterialReport', $viewData);
        } elseif (in_array($acc_id, ['FOHTOOLS', 'FOHFS', 'FOHINDMAT', 'FOHREPAIR'])) {
            // Kelompokkan data Support Materials berdasarkan bulan
            $groupedSupportMaterials = [];
            if ($submissions->isNotEmpty()) {
                $groupedSupportMaterials = $submissions->groupBy(function($item) {
                    return ($item->itm_id ?? '') . '-' . ($item->description ?? '');
                })->map(function($group) {
                    $first = $group->first();
                    $monthsData = [];
                    $totalPrice = 0;
                    
                    foreach ($group as $submission) {
                        $month = $submission->month;
                        if ($month) {
                            $monthsData[$month] = $submission->price;
                            $totalPrice += $submission->price;
                        }
                    }
                    
                    return [
                        'item' => $first->itm_id ?? '',
                        'description' => $first->description ?? '',
                        'unit' => $first->unit ?? '',
                        'quantity' => $first->quantity ?? '',
                        'price' => $first->price ?? 0,
                        'workcenter' => $first->workcenter != null ? $first->workcenter->workcenter : $first->wct_id ?? '',
                        'department' => $first->dept != null ? $first->dept->department : $first->dpt_id ?? '',
                        'budget' => $first->budget != null ? $first->budget->budget_name : '-',
                        'line_business' => $first->line_business != null ? $first->line_business->line_business : $first->lob_id ?? '',
                        'months' => $monthsData,
                        'total' => $totalPrice,
                        'sub_id' => $first->sub_id,
                        'id' => $first->id,
                        'status' => $first->status,
                    ];
                })->values();
            }
            
            $viewData['groupedItems'] = $groupedSupportMaterials;
            return view('reports.supportReport', $viewData);
        } elseif (in_array($acc_id, ['FOHEMPLOYCOMPDL', 'FOHEMPLOYCOMPIL', 'SGAEMPLOYCOMP'])) {
            return view('reports.employeeReport', $viewData);
        } elseif (in_array($acc_id, ['FOHENTERTAINT', 'FOHREPRESENTATION', 'SGAENTERTAINT', 'SGAREPRESENTATION'])) {
            return view('reports.representReport', $viewData);
        } elseif (in_array($acc_id, ['FOHINSPREM', 'SGAINSURANCE'])) {
            return view('reports.insuranceReport', $viewData);
        } elseif (in_array($acc_id, ['FOHPOWER', 'SGAPOWER'])) {
            return view('reports.utilitiesReport', $viewData);
        } elseif (in_array($acc_id, ['FOHTRAV', 'SGATRAV'])) {
            return view('reports.businessReport', $viewData);
        } elseif (in_array($acc_id, ['FOHTRAINING', 'SGATRAINING'])) {
            return view('reports.trainingReport', $viewData);
        } elseif ($acc_id === 'SGAAFTERSALES') {
            return view('reports.aftersalesReport', $viewData);
        } elseif ($acc_id === 'CAPEX') {
            return view('reports.expenditureReport', $viewData);
        }

        return redirect()->back()->with('error', 'Tidak ada pengajuan ditemukan untuk ID yang diberikan.'); // [MODIFIKASI] Pesan error dalam bahasa Indonesia
    }


    public function account($id) {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {}

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
    public function edit(string $sub_id, string $id)
{
    // [MODIFIKASI] Load BudgetPlan dengan relasi dept dan lineOfBusiness
    $submission = BudgetPlan::with(['dept', 'line_business', 'insurance'])->where('sub_id', $sub_id)->where('id', $id)->first(); // [MODIFIKASI] Tambah relasi insurance

    if (!$submission) {
        return response()->json(['message' => 'Item tidak ditemukan'], 404); // [MODIFIKASI] Pesan error dalam bahasa Indonesia
    }

    // [MODIFIKASI] Tentukan apakah ini laporan asuransi atau pelatihan berdasarkan acc_id
    $isInsurance = in_array($submission->acc_id, ['FOHINSPREM', 'SGAINSURANCE']); // [MODIFIKASI] Perbaiki kondisi untuk FOHINSPREM dan SGAINSURANCE
    $isTraining = in_array($submission->acc_id, ['FOHTRAINING', 'SGATRAINING']); // [MODIFIKASI] Tambah pengecekan untuk pelatihan
    $isRepresentation = in_array($submission->acc_id, ['FOHREPRESENTATION', 'SGAREPRESENTATION']); // [MODIFIKASI] Tambah pengecekan untuk representasi

    // [MODIFIKASI] Kembalikan data JSON sesuai tipe laporan
    $response = [
        'description' => $submission->description,
        'customer' => $submission->customer ?? '',
        'cur_id' => $submission->cur_id,
        'price' => $submission->price,
        'amount' => $submission->amount ?? 0,
        'dpt_id' => $submission->dpt_id,
        'wct_id' => $submission->wct_id,
        'month' => $submission->month,
        'acc_id' => $submission->acc_id,
        'purpose' => $submission->purpose ?? '',
        'department' => $submission->dept->department ?? '-',
        'lob_id' => $submission->lob_id,
        'kwh' => $submission->kwh,
        'bdc_id' => $submission->bdc_id,
        'quantity' => $submission->quantity, // [MODIFIKASI] Tambah quantity
        'trip_propose' => $submission->trip_propose ?? '', // [MODIFIKASI] Tambah trip_propose
        'destination' => $submission->destination ?? '', // [MODIFIKASI] Tambah destination
        'days' => $submission->days ?? '', // [MODIFIKASI] Tambah days
        'beneficiary' => $submission->beneficiary ?? '', // [MODIFIKASI] Tambah beneficiary
        'itm_id' => $submission->itm_id ?? '', // [MODIFIKASI] Tambah itm_id untuk semua kasus
        'business_partner' => $submission->business_partner ?? '', // [MODIFIKASI] Tambah business_partner untuk memastikan data dikembalikan
    ];

    if ($isInsurance) {
        $response['ins_id'] = $submission->ins_id ?? '';
    } elseif ($isTraining) {
        $response['participant'] = $submission->participant ?? '';
        $response['jenis_training'] = $submission->jenis_training ?? '';
    } elseif (in_array($submission->acc_id, ['FOHTRAV', 'SGATRAV'])) {
        $response['trip_propose'] = $submission->trip_propose ?? '';
        $response['destination'] = $submission->destination ?? '';
        $response['days'] = $submission->days ?? '';
    } elseif ($isRepresentation) {
        $response['beneficiary'] = $submission->beneficiary ?? ''; // [MODIFIKASI] Pastikan beneficiary dikembalikan untuk representasi
        $response['itm_id'] = $submission->itm_id ?? ''; // [MODIFIKASI] Pastikan itm_id dikembalikan untuk representasi
    } else {
        $response['itm_id'] = $submission->itm_id;
    }

    // if ($isInsurance) {
    //     $response['ins_id'] = $submission->ins_id ?? ''; // [MODIFIKASI] Tambah ins_id untuk laporan asuransi
    // } elseif ($isTraining) {
    //     $response['participant'] = $submission->participant ?? ''; // [MODIFIKASI] Tambah participant untuk laporan pelatihan
    //     $response['jenis_training'] = $submission->jenis_training ?? ''; // [MODIFIKASI] Tambah jenis_training untuk laporan pelatihan
    // } else {
    //     $response['itm_id'] = $submission->itm_id; // [MODIFIKASI] Gunakan itm_id untuk laporan umum
    // }

    return response()->json($response);
}


    /**
     * Update the specified resource in storage.
     */
    /**
     * Update the specified resource in storage.
     */
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $sub_id, string $id)
{
    $submission = BudgetPlan::where('sub_id', $sub_id)->where('id', $id)->first();
    if (!$submission) {
        Log::error("Pengajuan tidak ditemukan untuk sub_id {$sub_id}, id {$id}");
        return response()->json(['error' => 'Pengajuan tidak ditemukan.'], 404);
    }

    $isInsurance = in_array($submission->acc_id, ['FOHINSPREM', 'SGAINSURANCE']);
    $isTraining = in_array($submission->acc_id, ['FOHTRAINING', 'SGATRAINING']);
    $isTravel = in_array($submission->acc_id, ['FOHTRAV', 'SGATRAV']);
    $isRepresentation = in_array($submission->acc_id, ['FOHENTERTAINT', 'SGAENTERTAINT', 'FOHREPRESENTATION', 'SGAREPRESENTATION']); // [MODIFIKASI] Tambah FOHREPRESENTATION dan SGAREPRESENTATION
    
    // Aturan validasi
    $rules = [
        'description' => 'nullable|string',
        'customer' => 'nullable|string',
        'cur_id' => 'required|string',
        'price' => 'required|numeric|min:0',
        'amount' => 'required|numeric|min:0',
        'dpt_id' => 'required|string',
        'wct_id' => 'nullable|string',
        'month' => 'required|string',
        'kwh' => 'required_if:acc_id,FOHPOWER,SGAPOWER|numeric|min:0',
        'lob_id' => 'nullable|exists:line_of_businesses,lob_id',
        'bdc_id' => 'nullable|string',
        'quantity' => 'required_if:acc_id,FOHTRAINING,SGATRAINING|numeric|min:1',
        'itm_id' => 'nullable|string', // [MODIFIKASI] Tambah validasi itm_id untuk semua kasus, opsional
        'business_partner' => 'nullable|string', // [MODIFIKASI] Tambah validasi itm_id untuk semua kasus, opsional
    ];

    if ($isInsurance) {
        $rules['ins_id'] = 'required|string|exists:insurance_companies,ins_id';
    } elseif ($isTraining) {
        $rules['participant'] = 'required|string';
        $rules['jenis_training'] = 'required|string';
    } elseif ($isTravel) {
        $rules['trip_propose'] = 'required|string|max:255';
        $rules['destination'] = 'required|string|max:255';
        $rules['days'] = 'required|numeric|min:1';
    } elseif ($isRepresentation) {
        $rules['beneficiary'] = 'required|string|max:255'; // [MODIFIKASI] Validasi beneficiary untuk representasi
        $rules['itm_id'] = 'required|string|max:255'; // [MODIFIKASI] Validasi itm_id untuk representasi
    } else {
        $rules['itm_id'] = 'required|string';
    }

    $validated = $request->validate($rules);

    // Konversi harga ke IDR menggunakan cur_id
    $price = $request->price;
    $currency_id = $request->cur_id;

    if ($currency_id) {
        $currency = Currency::where('cur_id', $currency_id)->first();
        if ($currency && $currency->currency !== 'IDR') {
            $price = $price * $currency->nominal; // Convert to IDR
        }
    }

    // Set parameter untuk update
    $params = $validated;
    $params['price'] = $price;
    $params['amount'] = in_array($request->acc_id, ['FOHPOWER', 'SGAPOWER', 'FOHINSPREM', 'SGAINSURANCE']) ? $price : $request->amount;

    // Check user authentication
    $user = Auth::user();
    if (!$user || !$user->npk || !$user->sect || !$user->dept) {
        Log::error("User tidak terautentikasi atau atribut tidak lengkap untuk sub_id {$sub_id}, id {$id}");
        return response()->json([
            'success' => false,
            'message' => 'User tidak terautentikasi atau informasi tidak lengkap.'
        ], 401);
    }

    $submissionDept = $submission->dpt_id;
    if (!$submissionDept) {
        Log::error("Tidak ada departemen ditemukan untuk sub_id {$sub_id}, id {$id}");
        return response()->json(['error' => 'Informasi departemen tidak ditemukan.'], 400);
    }

    // Update the submission
   try {
    Log::info("Data yang diterima untuk update sub_id {$sub_id}, id {$id}: " . json_encode($params)); // [MODIFIKASI] Tambah logging untuk debug params

    if ($submission->update($params)) {
        $submission->refresh(); // [MODIFIKASI] Refresh model untuk memastikan data terbaru

        // Send notifications
        $sessionKey = "notification_sent_{$sub_id}_{$id}";
        if (!session()->has($sessionKey)) {
            $notificationMsg = "Item " . (
                $isInsurance ? $submission->ins_id :
                ($isTraining ? $submission->participant :
                ($isTravel ? $submission->trip_propose :
                ($isRepresentation ? ($params['beneficiary'] ?? $submission->beneficiary ?? 'tidak ada') : ($submission->itm_id ?? 'tidak ada'))))
                ) . " dari pengajuan ID {$sub_id} akun {$submission->acc_id} telah diperbarui oleh {$user->sect}."; // [MODIFIKASI] Gunakan itm_id untuk non-spesifik
                $approvalRecords = Approval::where('sub_id', $sub_id)
                    ->whereIn('status', [1, 2, 3, 4, 5, 6])
                    ->where('approve_by', '!=', $user->npk)
                    ->pluck('approve_by')
                    ->unique();

                $users = User::whereIn('npk', $approvalRecords)
                    ->where('dept', $submissionDept)
                    ->get();

                foreach ($users as $notifyUser) {
                    try {
                        NotificationController::createNotification(
                            $notifyUser->npk,
                            $notificationMsg,
                            $sub_id
                        );
                        Log::info("Notifikasi dikirim ke NPK {$notifyUser->npk} untuk item yang diperbarui ID {$id} di sub_id {$sub_id}");
                    } catch (\Exception $e) {
                        Log::error("Gagal mengirim notifikasi ke NPK {$notifyUser->npk} untuk sub_id {$sub_id}: " . $e->getMessage());
                    }
                }

                session([$sessionKey => true]);
            }

            return response()->json(['success' => true, 'message' => 'Data berhasil diperbarui.']);
        } else {
            Log::error("Gagal memperbarui item untuk sub_id {$sub_id}, id {$id}, Model: " . get_class($submission));
            return response()->json(['error' => 'Gagal memperbarui pengajuan.'], 500);
        }
    } catch (\Exception $e) {
        Log::error("Terjadi kesalahan saat memperbarui item untuk sub_id {$sub_id}, id {$id}: " . $e->getMessage());
        return response()->json(['error' => 'Gagal memperbarui pengajuan: ' . $e->getMessage()], 500);
    }
}

    public function getItemName(Request $request)
    {
        $itm_id = $request->input('itm_id');
        $item = Item::where('itm_id', $itm_id)->first();

        if ($item) {
            return response()->json(['item' => $item]);
        } else {
            return response()->json(['item' => null], 404);
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $sub_id)
    {
        $updated = false;

        $budgetPlans = BudgetPlan::where('sub_id', $sub_id)->get();
        if ($budgetPlans->isNotEmpty()) {
            BudgetPlan::where('sub_id', $sub_id)->delete();
            $updated = true;
        }

        // Update all records in GeneralExpense with the given sub_id
        // $generalExpenses = GeneralExpense::where('sub_id', $sub_id)->get();
        // if ($generalExpenses->isNotEmpty()) {
        //     GeneralExpense::where('sub_id', $sub_id)->update(['status' => 0]);
        //     $updated = true;
        // }

        // // Update all records in SupportMaterial with the given sub_id
        // $supportMaterials = SupportMaterial::where('sub_id', $sub_id)->get();
        // if ($supportMaterials->isNotEmpty()) {
        //     SupportMaterial::where('sub_id', $sub_id)->update(['status' => 0]);
        //     $updated = true;
        // }

        // // Update all records in InsurancePrem with the given sub_id
        // $insurancePrems = InsurancePrem::where('sub_id', $sub_id)->get();
        // if ($insurancePrems->isNotEmpty()) {
        //     InsurancePrem::where('sub_id', $sub_id)->update(['status' => 0]);
        //     $updated = true;
        // }

        // // Update all records in Utilities with the given sub_id
        // $utilities = Utilities::where('sub_id', $sub_id)->get();
        // if ($utilities->isNotEmpty()) {
        //     Utilities::where('sub_id', $sub_id)->update(['status' => 0]);
        //     $updated = true;
        // }

        // // Update all records in BusinessDuty with the given sub_id
        // $businessDuties = BusinessDuty::where('sub_id', $sub_id)->get();
        // if ($businessDuties->isNotEmpty()) {
        //     BusinessDuty::where('sub_id', $sub_id)->update(['status' => 0]);
        //     $updated = true;
        // }

        // // Update all records in RepresentationExpense with the given sub_id
        // $repExpenses = RepresentationExpense::where('sub_id', $sub_id)->get();
        // if ($repExpenses->isNotEmpty()) {
        //     RepresentationExpense::where('sub_id', $sub_id)->update(['status' => 0]);
        //     $updated = true;
        // }

        // // Update all records in TrainingEducation with the given sub_id
        // $trainingEdus = TrainingEducation::where('sub_id', $sub_id)->get();
        // if ($trainingEdus->isNotEmpty()) {
        //     TrainingEducation::where('sub_id', $sub_id)->update(['status' => 0]);
        //     $updated = true;
        // }

        // // Update all records in AfterSalesService with the given sub_id
        // $aftersales = AfterSalesService::where('sub_id', $sub_id)->get();
        // if ($aftersales->isNotEmpty()) {
        //     AfterSalesService::where('sub_id', $sub_id)->update(['status' => 0]);
        //     $updated = true;
        // }

        // Return appropriate response based on whether any records were updated
        if ($updated) {
            return redirect()->back()->with('success', 'The submissions have been deleted.');
        }

        return redirect()->back()->with('error', 'No submissions found for the given ID.');
    }

    public function delete(Request $request, string $sub_id, string $id)
    {
        $user = Auth::user();
        if (!$user || !$user->npk || !$user->sect || !$user->dept) {
            Log::error("User not authenticated or missing required attributes for sub_id {$sub_id}");
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated or missing required information.'
            ]);
        }

        // First check how many active items exist for this submission
        $totalItems = 0;
        $models = [
            BudgetPlan::class,
            // AfterSalesService::class,
            // InsurancePrem::class,
            // SupportMaterial::class,
            // TrainingEducation::class,
            // Utilities::class,
            // BusinessDuty::class,
            // RepresentationExpense::class,
        ];

        foreach ($models as $model) {
            $totalItems += $model::where('sub_id', $sub_id)
                ->where('status', '!=', 0)
                ->count();
        }

        // If this is the last item, don't allow deletion
        if ($totalItems <= 1) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete the last item. There must be at least one item in the submission.'
            ]);
        }

        // Otherwise proceed with deletion
        $submissionDept = null;
        $directDIC = ['4211', '6111', '6121']; // Departments that skip Kadiv approval
        foreach ($models as $model) {
            $item = $model::where('sub_id', $sub_id)
                ->where('id', $id)
                ->first();

            if ($item) {
                $submissionDept = $item->dpt_id; // Get department ID for notifications
                $item->status = 0;
                if ($item->save()) {
                    Log::info("Item deleted for sub_id {$sub_id}, Model: {$model}, ID: {$id}");

                    // Send notifications to submitter and previous approvers
                    $notificationMsg = "Item {$item->itm_id} from submission ID {$sub_id} account {$item->acc_id} has been deleted by {$user->sect}.";
                    $approvalRecords = Approval::where('sub_id', $sub_id)
                        ->whereIn('status', [1, 2, 3, 4, 5, 6])
                        ->where('approve_by', '!=', $user->npk)
                        ->pluck('approve_by')
                        ->unique();
                    $users = User::whereIn('npk', $approvalRecords)
                        ->where('dept', $submissionDept)
                        ->get();

                    foreach ($users as $notifyUser) {
                        try {
                            NotificationController::createNotification(
                                $notifyUser->npk,
                                $notificationMsg,
                                $sub_id
                            );
                            Log::info("Notification sent to NPK {$notifyUser->npk} for deleted item ID {$id} in sub_id {$sub_id}");
                        } catch (\Exception $e) {
                            Log::error("Failed to send notification to NPK {$notifyUser->npk} for sub_id {$sub_id}: " . $e->getMessage());
                        }
                    }

                    return response()->json([
                        'success' => true,
                        'message' => 'Item has been removed from submission.'
                    ]);
                } else {
                    Log::error("Failed to save deletion for sub_id {$sub_id}, Model: {$model}, ID: {$id}");
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to delete item.'
                    ]);
                }
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Item not found in submission.'
        ]);
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();

        $sheets = [
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
            'EMPLOYEE COMP' => [
                'No',
                // 'Item Type',
                'Type',
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
            ]
        ];
        $firstSheet = array_key_first($sheets);
        $spreadsheet->getActiveSheet()->setTitle($firstSheet);
        $spreadsheet->getActiveSheet()->fromArray($sheets[$firstSheet], null, 'A1');

        // Apply formulas to the first sheet
        $header = $sheets[$firstSheet];
        $sheet = $spreadsheet->getActiveSheet();
        $quantityCol = array_search('Quantity', $header);
        $priceCol = array_search('Price', $header);
        $amountCol = array_search('Amount', $header);
        $janCol = array_search('Jan', $header);
        $decCol = array_search('Dec', $header);
        $totalCol = array_search('Total', $header);
        $typeCol = array_search('Type', $header); // Identify Item Type column

        if ($quantityCol !== false && $priceCol !== false && $amountCol !== false) {
            $quantityColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($quantityCol + 1);
            $priceColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($priceCol + 1);
            $amountColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($amountCol + 1);
            $sheet->setCellValue("{$amountColLetter}2", "=IF({$quantityColLetter}2*{$priceColLetter}2>0,{$quantityColLetter}2*{$priceColLetter}2,0)");
        }
        if ($janCol !== false && $decCol !== false && $totalCol !== false) {
            $janColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($janCol + 1);
            $decColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($decCol + 1);
            $totalColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($totalCol + 1);
            $sheet->setCellValue("{$totalColLetter}2", "=SUM({$janColLetter}2:{$decColLetter}2)");
        }
        if ($typeCol !== false) {
            $typeColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($typeCol + 1);
            // Apply dropdown for Item Type (GID, Non-GID)
            $validation = $sheet->getCell("{typeColLetter}2")->getDataValidation();
            $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
            $validation->setAllowBlank(false);
            $validation->setShowDropDown(true);
            $validation->setFormula1('"EMPLOYEE COMPENSATION,EMPLOYEE COMPENSATION DIRECT LABOR,EMPLOYEE COMPENSATION INDIRECT LABOR"');
            // Apply to rows 2 to 100 (adjust range as needed)
            $validation->setSqref("{$typeColLetter}2:{$typeColLetter}100");
        }

        // Create other sheets and apply formulas
        foreach ($sheets as $title => $header) {
            if ($title === $firstSheet) continue;

            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle($title);
            $sheet->fromArray($header, null, 'A1');

            // Apply formulas to the current sheet
            $quantityCol = array_search('Quantity', $header);
            $priceCol = array_search('Price', $header);
            $amountCol = array_search('Amount', $header);
            $janCol = array_search('Jan', $header);
            $decCol = array_search('Dec', $header);
            $totalCol = array_search('Total', $header);
            $typeCol = array_search('Type', $header); // Identify Item Type column

            if ($quantityCol !== false && $priceCol !== false && $amountCol !== false) {
                $quantityColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($quantityCol + 1);
                $priceColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($priceCol + 1);
                $amountColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($amountCol + 1);
                $sheet->setCellValue("{$amountColLetter}2", "=IF({$quantityColLetter}2*{$priceColLetter}2>0,{$quantityColLetter}2*{$priceColLetter}2,0)");
            }
            if ($janCol !== false && $decCol !== false && $totalCol !== false) {
                $janColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($janCol + 1);
                $decColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($decCol + 1);
                $totalColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($totalCol + 1);
                $sheet->setCellValue("{$totalColLetter}2", "=SUM({$janColLetter}2:{$decColLetter}2)");
            }
            if ($typeCol !== false) {
                $typeColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($typeCol + 1);
                // Apply dropdown for Item Type (GID, Non-GID)
                $validation = $sheet->getCell("{$typeColLetter}2")->getDataValidation();
                $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
                $validation->setAllowBlank(false);
                $validation->setShowDropDown(true);
                $validation->setFormula1('"EMPLOYEE COMPENSATION,EMPLOYEE COMPENSATION DIRECT LABOR,EMPLOYEE COMPENSATION INDIRECT LABOR"');
                $validation->setSqref("{$typeColLetter}2:{$typeColLetter}100");
            }
        }

        $filename = 'Plan Master Budget.xlsx';
        $path = storage_path("app/public/$filename");
        $writer = new Xlsx($spreadsheet);
        $writer->save($path);

        return response()->download($path)->deleteFileAfterSend();
    }

    public function downloadTemplateExpend()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('CAPITAL EXPENDITURE');

        // === Title & Division/Department Info ===
        $sheet->mergeCells('A1:H1')->setCellValue('A1', 'PT. KAYABA INDONESIA');
        $sheet->mergeCells('A2:H2')->setCellValue('A2', 'CAPITAL EXPENDITURE');
        $sheet->setCellValue('A4', 'WORKCENTER');
        // $sheet->setCellValue('B4', ':');
        $sheet->setCellValue('A5', 'DEPARTMENT');
        // $sheet->setCellValue('B5', ':');

        // === Asset Class, Prioritas, Alasan Tables ===
        $sheet->mergeCells('A7:B7')->setCellValue('A7', 'Asset Class');
        $sheet->mergeCells('D7:E7')->setCellValue('D7', 'Prioritas');
        $sheet->mergeCells('G7:H7')->setCellValue('G7', 'Alasan');

        $headerCells = ['A7', 'D7', 'G7'];
        foreach ($headerCells as $cell) {
            $sheet->getStyle($cell)->getFont()->setBold(true);
            $sheet->getStyle($cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($cell)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle($cell)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        }

        $sheet->getStyle('A7:B7')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('D7:E7')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('G7:H7')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        $assetClasses = [
            '170' => 'Landright',
            '171' => 'Infrastructure',
            '172' => 'Building Improvement',
            '173' => 'Building Equipment',
            '175' => 'Machinery Eqp',
            '176' => 'Accessories',
            '177' => 'Office Equipment',
            '178' => 'Transportation',
        ];

        $priorities = ['H' => 'High', 'M' => 'Medium', 'L' => 'Low'];

        $reasons = [
            1 => 'Penambahan',
            2 => 'Penggantian',
            3 => 'Model Baru',
            4 => 'Quality Control',
            5 => 'Local Component',
            6 => 'Keselamatan Kerja',
            7 => 'Peningkatan produk',
            8 => 'Others',
        ];

        // === Write Asset Class / Prioritas / Alasan Data ===
        $row = 8;
        $maxRow = max(count($assetClasses), count($priorities), count($reasons));
        $assetKeys = array_keys($assetClasses);
        $priorityKeys = array_keys($priorities);
        $reasonKeys = array_keys($reasons);

        for ($i = 0; $i < $maxRow; $i++, $row++) {
            if (isset($assetKeys[$i])) {
                $sheet->setCellValue("A{$row}", $assetKeys[$i]);
                $sheet->setCellValue("B{$row}", $assetClasses[$assetKeys[$i]]);
                $sheet->getStyle("A{$row}:B{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            }

            if (isset($priorityKeys[$i])) {
                $sheet->setCellValue("D{$row}", $priorityKeys[$i]);
                $sheet->setCellValue("E{$row}", $priorities[$priorityKeys[$i]]);
                $sheet->getStyle("D{$row}:E{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            }

            if (isset($reasonKeys[$i])) {
                $sheet->setCellValue("G{$row}", $reasonKeys[$i]);
                $sheet->setCellValue("H{$row}", $reasons[$reasonKeys[$i]]);
                $sheet->getStyle("G{$row}:H{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            }
        }

        // === CAPEX Table Header ===
        $startHeaderRow = $row + 2;
        $headers = [
            "No.",
            "ITEM",
            "Asset Class",
            "Prioritas",
            "Alasan",
            "Keterangan",
            "Qty (Unit)",
            "Price",
            "Amount", // <-- Tambahan kolom Price
            "Jan",
            "Feb",
            "Mar",
            "Apr",
            "May",
            "Jun",
            "Jul",
            "Aug",
            "Sep",
            "Oct",
            "Nov",
            "Dec",
            "Total"
        ];

        foreach ($headers as $colIndex => $header) {
            $colLetter = Coordinate::stringFromColumnIndex($colIndex + 1);
            $cellRef = $colLetter . $startHeaderRow;

            $sheet->setCellValue($cellRef, $header);
            $style = $sheet->getStyle($cellRef);
            $style->getFont()->setBold(true);
            $style->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $style->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $style->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        }

        // === Input Rows ===
        $dataStartRow = $startHeaderRow + 1;
        $dataEndRow = $dataStartRow + 9;

        for ($i = $dataStartRow; $i <= $dataEndRow; $i++) {
            for ($col = 1; $col <= count($headers); $col++) {
                $colLetter = Coordinate::stringFromColumnIndex($col);
                $sheet->getStyle("{$colLetter}{$i}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            }

            // Rumus AMOUNT = Qty × Price (G × H)
            $sheet->setCellValue("I{$i}", "=G{$i}*H{$i}");

            // Rumus TOTAL = SUM(Jan:Dec) = J to U
            $sheet->setCellValue("V{$i}", "=SUM(J{$i}:U{$i})");
            $sheet->getStyle("V{$i}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        // === TOTAL Row ===
        $totalRow = $dataEndRow + 1;
        $sheet->mergeCells("A{$totalRow}:I{$totalRow}")->setCellValue("A{$totalRow}", "TOTAL");
        $sheet->getStyle("A{$totalRow}")->getFont()->setBold(true);
        $sheet->getStyle("A{$totalRow}:I{$totalRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle("A{$totalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // SUM per bulan (Jan to Dec)
        for ($col = 10; $col <= 21; $col++) { // J to U
            $colLetter = Coordinate::stringFromColumnIndex($col);
            $sheet->setCellValue("{$colLetter}{$totalRow}", "=SUM({$colLetter}{$dataStartRow}:{$colLetter}{$dataEndRow})");
            $sheet->getStyle("{$colLetter}{$totalRow}")->getFont()->setBold(true);
            $sheet->getStyle("{$colLetter}{$totalRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle("{$colLetter}{$totalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        // Kolom TOTAL = SUM dari Jan–Dec (col V)
        $sheet->setCellValue("V{$totalRow}", "=SUM(V{$dataStartRow}:V{$dataEndRow})");
        $sheet->getStyle("V{$totalRow}")->getFont()->setBold(true);
        $sheet->getStyle("V{$totalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("V{$totalRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Auto-size columns
        for ($i = 1; $i <= count($headers); $i++) {
            $colLetter = Coordinate::stringFromColumnIndex($i);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }

        // === Save & Download ===
        $filename = 'CAPEX_Template.xlsx';
        $path = storage_path("app/public/{$filename}");
        $writer = new Xlsx($spreadsheet);
        $writer->save($path);

        return response()->download($path)->deleteFileAfterSend(true);
    }


    // public function upload(Request $request)
    // {
    //     // Log request masuk
    //     Log::info('Upload request received', [
    //         'file' => $request->file('file') ? $request->file('file')->getClientOriginalName() : 'No file',
    //         'purpose' => $request->input('purpose'),
    //         'timestamp' => now()->toDateTimeString()
    //     ]);

    //     // Validasi input
    //     $request->validate([
    //         'file' => 'required|file|mimes:xlsx,xls',
    //         'purpose' => 'required|string'
    //     ]);

    //     // Check if user is authenticated
    //     if (!Auth::check()) {
    //         Log::error('User not authenticated');
    //         return response()->json(['message' => 'User not authenticated'], 401);
    //     }

    //     $file = $request->file('file');
    //     $purpose = $request->input('purpose');
    //     $npk = Auth::user()->npk; // Get NPK of logged-in user
    //     $userDept = Auth::user()->dept; // Get department of logged-in user

    //     // Load file Excel
    //     try {
    //         $spreadsheet = IOFactory::load($file);
    //         Log::info('Excel file loaded successfully', ['sheets' => $spreadsheet->getSheetNames()]);
    //     } catch (\Exception $e) {
    //         Log::error('Failed to load Excel file', ['error' => $e->getMessage()]);
    //         return response()->json(['message' => 'Failed to load Excel file: ' . $e->getMessage()], 500);
    //     }

    //     // Mapping sheet ke prefix, acc_id, model, dan template
    //     $sheetMappings = [
    //         'ADVERTISING & PROMOTION' => [
    //             'prefix' => 'ADP',
    //             'acc_id' => 'SGAADVERT',
    //             'model' => BudgetPlan::class,
    //             'template' => 'general'
    //         ],
    //         'COMMUNICATION' => [
    //             'prefix' => 'COM',
    //             'acc_id' => 'SGACOM',
    //             'model' => BudgetPlan::class,
    //             'template' => 'general'
    //         ],
    //         'OFFICE SUPPLY' => [
    //             'prefix' => 'OFS',
    //             'acc_id' => 'SGAOFFICESUP',
    //             'model' => BudgetPlan::class,
    //             'template' => 'general'
    //         ],
    //         'AFTER SALES SERVICE' => [
    //             'prefix' => 'AFS',
    //             'acc_id' => 'SGAAFTERSALES',
    //             'model' => BudgetPlan::class,
    //             'template' => 'aftersales'
    //         ],
    //         'INDIRECT MATERIAL' => [
    //             'prefix' => 'IDM',
    //             'acc_id' => 'FOHINDMAT',
    //             'model' => BudgetPlan::class,
    //             'template' => 'support'
    //         ],
    //         'FACTORY SUPPLY' => [
    //             'prefix' => 'FSU',
    //             'acc_id' => 'FOHFS',
    //             'model' => BudgetPlan::class,
    //             'template' => 'support'
    //         ],
    //         'REPAIR & MAINTENANCE FOH' => [
    //             'prefix' => 'RPMF',
    //             'acc_id' => 'FOHREPAIR',
    //             'model' => BudgetPlan::class,
    //             'template' => 'support'
    //         ],
    //         'CONS TOOLS' => [
    //             'prefix' => 'CTL',
    //             'acc_id' => 'FOHTOOLS',
    //             'model' => BudgetPlan::class,
    //             'template' => 'support'
    //         ],
    //         'INSURANCE PREM FOH' => [
    //             'prefix' => 'INSF',
    //             'acc_id' => 'FOHINSPREM',
    //             'model' => BudgetPlan::class,
    //             'template' => 'insurance'
    //         ],
    //         'INSURANCE PREM OPEX' => [
    //             'prefix' => 'INSO',
    //             'acc_id' => 'SGAINSURANCE',
    //             'model' => BudgetPlan::class,
    //             'template' => 'insurance'
    //         ],
    //         'TAX & PUBLIC DUES FOH' => [
    //             'prefix' => 'TAXF',
    //             'acc_id' => 'FOHTAXPUB',
    //             'model' => BudgetPlan::class,
    //             'template' => 'general'
    //         ],
    //         'TAX & PUBLIC DUES OPEX' => [
    //             'prefix' => 'TAXO',
    //             'acc_id' => 'SGATAXPUB',
    //             'model' => BudgetPlan::class,
    //             'template' => 'general'
    //         ],
    //         'PROFESIONAL FEE FOH' => [
    //             'prefix' => 'PRFF',
    //             'acc_id' => 'FOHPROF',
    //             'model' => BudgetPlan::class,
    //             'template' => 'general'
    //         ],
    //         'PROFESIONAL FEE OPEX' => [
    //             'prefix' => 'PRFO',
    //             'acc_id' => 'SGAPROF',
    //             'model' => BudgetPlan::class,
    //             'template' => 'general'
    //         ],
    //         'AUTOMOBILE FOH' => [
    //             'prefix' => 'AUTF',
    //             'acc_id' => 'FOHAUTOMOBILE',
    //             'model' => BudgetPlan::class,
    //             'template' => 'general'
    //         ],
    //         'AUTOMOBILE OPEX' => [
    //             'prefix' => 'AUTO',
    //             'acc_id' => 'SGAAUTOMOBILE',
    //             'model' => BudgetPlan::class,
    //             'template' => 'general'
    //         ],
    //         'RENT EXPENSE FOH' => [
    //             'prefix' => 'REXF',
    //             'acc_id' => 'FOHRENT',
    //             'model' => BudgetPlan::class,
    //             'template' => 'general'
    //         ],
    //         'PACKING & DELIVERY' => [
    //             'prefix' => 'PKD',
    //             'acc_id' => 'FOHPACKING',
    //             'model' => BudgetPlan::class,
    //             'template' => 'general'
    //         ],
    //         'BANK CHARGES' => [
    //             'prefix' => 'BKC',
    //             'acc_id' => 'SGABCHARGES',
    //             'model' => BudgetPlan::class,
    //             'template' => 'general'
    //         ],
    //         'ROYALTY' => [
    //             'prefix' => 'RYL',
    //             'acc_id' => 'SGARYLT',
    //             'model' => BudgetPlan::class,
    //             'template' => 'general'
    //         ],
    //         'CONTRIBUTION' => [
    //             'prefix' => 'CTR',
    //             'acc_id' => 'SGACONTRIBUTION',
    //             'model' => BudgetPlan::class,
    //             'template' => 'general'
    //         ],
    //         'ASSOCIATION' => [
    //             'prefix' => 'ASC',
    //             'acc_id' => 'SGAASSOCIATION',
    //             'model' => BudgetPlan::class,
    //             'template' => 'general'
    //         ],
    //         'UTILITIES FOH' => [
    //             'prefix' => 'UTLF',
    //             'acc_id' => 'FOHPOWER',
    //             'model' => BudgetPlan::class,
    //             'template' => 'utilities'
    //         ],
    //         'UTILITIES OPEX' => [
    //             'prefix' => 'UTLO',
    //             'acc_id' => 'SGAPOWER',
    //             'model' => BudgetPlan::class,
    //             'template' => 'utilities'
    //         ],
    //         'BUSINESS DUTY FOH' => [
    //             'prefix' => 'BSDF',
    //             'acc_id' => 'FOHTRAV',
    //             'model' => BudgetPlan::class,
    //             'template' => 'business'
    //         ],
    //         'BUSINESS DUTY OPEX' => [
    //             'prefix' => 'BSDO',
    //             'acc_id' => 'SGATRAV',
    //             'model' => BudgetPlan::class,
    //             'template' => 'business'
    //         ],
    //         'TRAINING & EDUCATION FOH' => [
    //             'prefix' => 'TEDF',
    //             'acc_id' => 'FOHTRAINING',
    //             'model' => BudgetPlan::class,
    //             'template' => 'training'
    //         ],
    //         'TRAINING & EDUCATION OPEX' => [
    //             'prefix' => 'TEDO',
    //             'acc_id' => 'SGATRAINING',
    //             'model' => BudgetPlan::class,
    //             'template' => 'training'
    //         ],
    //         'TECHNICAL DEVELOPMENT FOH' => [
    //             'prefix' => 'TCD',
    //             'acc_id' => 'FOHTECHDO',
    //             'model' => BudgetPlan::class,
    //             'template' => 'general'
    //         ],
    //         'RECRUITMENT FOH' => [
    //             'prefix' => 'RECF',
    //             'acc_id' => 'FOHRECRUITING',
    //             'model' => BudgetPlan::class,
    //             'template' => 'general'
    //         ],
    //         'RECRUITMENT OPEX' => [
    //             'prefix' => 'RECO',
    //             'acc_id' => 'SGARECRUITING',
    //             'model' => BudgetPlan::class,
    //             'template' => 'general'
    //         ],
    //         'RENT EXPENSE OPEX' => [
    //             'prefix' => 'REXO',
    //             'acc_id' => 'SGARENT',
    //             'model' => BudgetPlan::class,
    //             'template' => 'general'
    //         ],
    //         'MARKETING ACTIVITY' => [
    //             'prefix' => 'MKT',
    //             'acc_id' => 'SGAMARKT',
    //             'model' => BudgetPlan::class,
    //             'template' => 'general'
    //         ],
    //         'REPAIR & MAINTENANCE OPEX' => [
    //             'prefix' => 'RPMO',
    //             'acc_id' => 'SGAREPAIR',
    //             'model' => BudgetPlan::class,
    //             'template' => 'general'
    //         ],
    //         'BOOK NEWSPAPER' => [
    //             'prefix' => 'BKN',
    //             'acc_id' => 'SGABOOK',
    //             'model' => BudgetPlan::class,
    //             'template' => 'general'
    //         ],
    //         'ENTERTAINMENT FOH' => [
    //             'prefix' => 'ENTF',
    //             'acc_id' => 'FOHENTERTAINT',
    //             'model' => BudgetPlan::class,
    //             'template' => 'representation'
    //         ],
    //         'ENTERTAINMENT OPEX' => [
    //             'prefix' => 'ENTO',
    //             'acc_id' => 'SGAENTERTAINT',
    //             'model' => BudgetPlan::class,
    //             'template' => 'representation'
    //         ],
    //         'REPRESENTATION FOH' => [
    //             'prefix' => 'REPF',
    //             'acc_id' => 'FOHREPRESENTATION',
    //             'model' => BudgetPlan::class,
    //             'template' => 'representation'
    //         ],
    //         'REPRESENTATION OPEX' => [
    //             'prefix' => 'REPO',
    //             'acc_id' => 'SGAREPRESENTATION',
    //             'model' => BudgetPlan::class,
    //             'template' => 'representation'
    //         ],
    //     ];

    //     $months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    //     $processedRows = 0;
    //     $processedSheets = [];
    //     $errors = []; // Initialize errors array

    //     // Iterasi setiap sheet di file Excel
    //     foreach ($spreadsheet->getSheetNames() as $sheetName) {
    //         if (!isset($sheetMappings[$sheetName])) {
    //             Log::warning("Sheet '$sheetName' not found in sheetMappings");
    //             $errors[] = "Sheet '$sheetName' not found in sheetMappings";
    //             continue;
    //         }

    //         $sheetConfig = $sheetMappings[$sheetName];
    //         $prefix = $sheetConfig['prefix'];
    //         $acc_id = $sheetConfig['acc_id'];
    //         $model = $sheetConfig['model'];
    //         $template = $sheetConfig['template'];

    //         Log::info("Checking sheet: $sheetName, template: $template, model: $model");

    //         // Load data dari sheet
    //         $sheet = $spreadsheet->getSheetByName($sheetName);
    //         $data = $sheet->toArray();
    //         Log::info("Sheet '$sheetName' has " . count($data) . " rows");

    //         // Check if sheet has valid data rows (excluding header)
    //         $hasValidData = false;
    //         foreach ($data as $i => $row) {
    //             if ($i === 0) {
    //                 Log::info("Skipping header row for sheet: $sheetName");
    //                 continue; // Skip header
    //             }

    //             // Check if row has any non-empty values
    //             $rowHasData = array_filter($row, function ($value) {
    //                 return !is_null($value) && $value !== '';
    //             });

    //             if (!empty($rowHasData)) {
    //                 $hasValidData = true;
    //                 break; // Found at least one valid data row
    //             }
    //         }

    //         if (!$hasValidData) {
    //             Log::info("Sheet '$sheetName' has no valid data rows, skipping processing");
    //             $errors[] = "Sheet '$sheetName' has no valid data rows";
    //             continue;
    //         }

    //         // Generate sub_id sekali per worksheet dengan data valid
    //         $lastRecord = $model::where('sub_id', 'like', "$prefix%")
    //             ->orderBy('sub_id', 'desc')
    //             ->first();
    //         $nextNumber = $lastRecord ? ((int)str_replace($prefix, '', $lastRecord->sub_id) + 1) : 1;
    //         $sub_id = $prefix . str_pad($nextNumber, 7, '0', STR_PAD_LEFT);
    //         Log::info("Generated sub_id for sheet $sheetName: $sub_id");

    //         // Create approval record for the sub_id
    //         try {
    //             Approval::create([
    //                 'approve_by' => $npk,
    //                 'sub_id' => $sub_id,
    //                 'status' => 1,
    //                 'created_at' => now(),
    //                 'updated_at' => now(),
    //             ]);
    //             Log::info("Created approval record for sub_id: $sub_id, approve_by: $npk");
    //         } catch (\Exception $e) {
    //             Log::error("Failed to create approval record for sub_id: $sub_id, error: " . $e->getMessage());
    //             $errors[] = "Failed to create approval record for sub_id: $sub_id";
    //             return response()->json(['message' => 'Failed to create approval record: ' . $e->getMessage(), 'errors' => $errors], 500);
    //         }

    //         $processedSheets[] = $sheetName;

    //         // Process data rows
    //         foreach ($data as $i => $row) {
    //             if ($i === 0) {
    //                 Log::info("Skipping header row for sheet: $sheetName");
    //                 continue; // Skip header
    //             }

    //             // Validasi jumlah kolom
    //             $expectedColumns = $this->getExpectedColumns($template, $months);
    //             if (count($row) < $expectedColumns) {
    //                 Log::warning("Invalid column count in row $i of sheet $sheetName: expected at least $expectedColumns, got " . count($row));
    //                 $errors[] = "Invalid column count in row $i of sheet $sheetName: expected at least $expectedColumns, got " . count($row);
    //                 continue;
    //             }

    //             Log::info("Processing row $i in sheet '$sheetName': " . json_encode($row));

    //             try {
    //                 if ($template === 'general') {
    //                     [$no, $itm_id, $description, $quantity, $price, $amount, $wct_id, $dpt_id, $bdc_id] = array_slice($row, 0, 9);

    //                     // Validate dpt_id
    //                     if ($dpt_id !== $userDept) {
    //                         $errors[] = "Invalid dpt_id in row $i of sheet $sheetName: Expected $userDept, got $dpt_id";
    //                         Log::warning("Invalid dpt_id in row $i of sheet $sheetName: Expected $userDept, got $dpt_id");
    //                         continue; // Skip this row
    //                     }

    //                     // Validate itm_id for GID items and normalize
    //                     $original_itm_id = $itm_id;
    //                     if ($this->isGidItem($itm_id)) {
    //                         $itm_id = $this->validateItemId($itm_id, $i, $sheetName, $errors);
    //                         if ($itm_id === false) {
    //                             continue; // Skip this row if itm_id is invalid
    //                         }
    //                     }

    //                     // Validasi kolom wajib tidak boleh kosong
    //                     $requiredFields = [
    //                         'itm_id' => $itm_id,
    //                         'description' => $description,
    //                         'quantity' => $quantity,
    //                         'price' => $price,
    //                         'amount' => $amount,
    //                         'dpt_id' => $dpt_id,
    //                         'bdc_id' => $bdc_id,
    //                     ];

    //                     foreach ($requiredFields as $fieldName => $value) {
    //                         if (is_null($value) || $value === '' || trim($value) === '') {
    //                             $errors[] = "Invalid $fieldName in row $i of sheet $sheetName: $fieldName is empty or null";
    //                             Log::warning("Invalid $fieldName in row $i of sheet $sheetName: $fieldName is empty or null");
    //                             continue 2; // Skip the entire row
    //                         }
    //                     }

    //                     // Validasi quantity, price, dan amount harus numerik
    //                     if (!is_numeric($quantity) || !is_numeric($price) || !is_numeric($amount)) {
    //                         $errors[] = "Invalid numeric value in row $i of sheet $sheetName: quantity=$quantity, price=$price, amount=$amount";
    //                         Log::warning("Invalid numeric value in row $i: quantity=$quantity, price=$price, amount=$amount");
    //                         continue;
    //                     }

    //                     Log::info("Processing row $i in sheet $sheetName with itm_id: $itm_id");

    //                     foreach ($months as $index => $month) {
    //                         $value = $row[9 + $index] ?? 0;
    //                         if ($value == 0 || $value === null || trim($value) === '') {
    //                             Log::info("Skipping month $month for row $i: value is $value");
    //                             continue;
    //                         }

    //                         $model::create([
    //                             'sub_id' => $sub_id,
    //                             'purpose' => $purpose,
    //                             'acc_id' => $acc_id,
    //                             'itm_id' => $itm_id,
    //                             'description' => $description,
    //                             'quantity' => $quantity,
    //                             'price' => $price,
    //                             'amount' => $amount,
    //                             'wct_id' => $wct_id,
    //                             'dpt_id' => $dpt_id,
    //                             'bdc_id' => $bdc_id,
    //                             'month' => $month,
    //                             'status' => 1,
    //                         ]);
    //                         Log::info("Created record for sub_id: $sub_id, month: $month, value: $value, itm_id: $itm_id");
    //                         $processedRows++;
    //                     }
    //                 } elseif ($template === 'aftersales') {
    //                     [$no, $itm_id, $customer, $quantity, $price, $amount, $wct_id, $dpt_id, $bdc_id] = array_slice($row, 0, 9);

    //                     // Validate dpt_id
    //                     if ($dpt_id !== $userDept) {
    //                         $errors[] = "Invalid dpt_id in row $i of sheet $sheetName: Expected $userDept, got $dpt_id";
    //                         Log::warning("Invalid dpt_id in row $i of sheet $sheetName: Expected $userDept, got $dpt_id");
    //                         continue; // Skip this row
    //                     }

    //                     // Validate itm_id for GID items and normalize
    //                     $original_itm_id = $itm_id;
    //                     if ($this->isGidItem($itm_id)) {
    //                         $itm_id = $this->validateItemId($itm_id, $i, $sheetName, $errors);
    //                         if ($itm_id === false) {
    //                             continue; // Skip this row if itm_id is invalid
    //                         }
    //                     }

    //                     // Validasi kolom wajib tidak boleh kosong
    //                     $requiredFields = [
    //                         'itm_id' => $itm_id,
    //                         'customer' => $customer,
    //                         'quantity' => $quantity,
    //                         'price' => $price,
    //                         'amount' => $amount,
    //                         'dpt_id' => $dpt_id,
    //                         'bdc_id' => $bdc_id,
    //                     ];

    //                     foreach ($requiredFields as $fieldName => $value) {
    //                         if (is_null($value) || $value === '' || trim($value) === '') {
    //                             $errors[] = "Invalid $fieldName in row $i of sheet $sheetName: $fieldName is empty or null";
    //                             Log::warning("Invalid $fieldName in row $i of sheet $sheetName: $fieldName is empty or null");
    //                             continue 2; // Skip the entire row
    //                         }
    //                     }

    //                     // Validasi quantity, price, dan amount harus numerik
    //                     if (!is_numeric($quantity) || !is_numeric($price) || !is_numeric($amount)) {
    //                         $errors[] = "Invalid numeric value in row $i of sheet $sheetName: quantity=$quantity, price=$price, amount=$amount";
    //                         Log::warning("Invalid numeric value in row $i: quantity=$quantity, price=$price, amount=$amount");
    //                         continue;
    //                     }

    //                     Log::info("Processing row $i in sheet $sheetName with itm_id: $itm_id");

    //                     foreach ($months as $index => $month) {
    //                         $value = $row[9 + $index] ?? 0;
    //                         if ($value == 0 || $value === null || trim($value) === '') {
    //                             Log::info("Skipping month $month for row $i: value is $value");
    //                             continue;
    //                         }

    //                         $model::create([
    //                             'sub_id' => $sub_id,
    //                             'purpose' => $purpose,
    //                             'acc_id' => $acc_id,
    //                             'itm_id' => $itm_id,
    //                             'customer' => $customer,
    //                             'quantity' => $quantity,
    //                             'price' => $price,
    //                             'amount' => $amount,
    //                             'wct_id' => $wct_id,
    //                             'dpt_id' => $dpt_id,
    //                             'bdc_id' => $bdc_id,
    //                             'month' => $month,
    //                             'status' => 1,
    //                         ]);
    //                         Log::info("Created record for sub_id: $sub_id, month: $month, value: $value, itm_id: $itm_id");
    //                         $processedRows++;
    //                     }
    //                 } elseif ($template === 'support') {
    //                     [$no, $itm_id, $description, $unit, $quantity, $price, $amount, $wct_id, $dpt_id, $bdc_id, $lob_id] = array_slice($row, 0, 11);

    //                     // Validate dpt_id
    //                     if ($dpt_id !== $userDept) {
    //                         $errors[] = "Invalid dpt_id in row $i of sheet $sheetName: Expected $userDept, got $dpt_id";
    //                         Log::warning("Invalid dpt_id in row $i of sheet $sheetName: Expected $userDept, got $dpt_id");
    //                         continue; // Skip this row
    //                     }

    //                     // Validate itm_id for GID items and normalize
    //                     $original_itm_id = $itm_id;
    //                     if ($this->isGidItem($itm_id)) {
    //                         $itm_id = $this->validateItemId($itm_id, $i, $sheetName, $errors);
    //                         if ($itm_id === false) {
    //                             continue; // Skip this row if itm_id is invalid
    //                         }
    //                     }

    //                     // Validasi kolom wajib tidak boleh kosong
    //                     $requiredFields = [
    //                         'itm_id' => $itm_id,
    //                         'description' => $description,
    //                         'unit' => $unit,
    //                         'quantity' => $quantity,
    //                         'price' => $price,
    //                         'amount' => $amount,
    //                         'dpt_id' => $dpt_id,
    //                         'bdc_id' => $bdc_id,
    //                         'lob_id' => $lob_id,
    //                     ];

    //                     foreach ($requiredFields as $fieldName => $value) {
    //                         if (is_null($value) || $value === '' || trim($value) === '') {
    //                             $errors[] = "Invalid $fieldName in row $i of sheet $sheetName: $fieldName is empty or null";
    //                             Log::warning("Invalid $fieldName in row $i of sheet $sheetName: $fieldName is empty or null");
    //                             continue 2; // Skip the entire row
    //                         }
    //                     }

    //                     // Validasi quantity, price, dan amount harus numerik
    //                     if (!is_numeric($quantity) || !is_numeric($price) || !is_numeric($amount)) {
    //                         $errors[] = "Invalid numeric value in row $i of sheet $sheetName: quantity=$quantity, price=$price, amount=$amount";
    //                         Log::warning("Invalid numeric value in row $i: quantity=$quantity, price=$price, amount=$amount");
    //                         continue;
    //                     }

    //                     Log::info("Processing row $i in sheet $sheetName with itm_id: $itm_id");

    //                     foreach ($months as $index => $month) {
    //                         $value = $row[11 + $index] ?? 0;
    //                         if ($value == 0 || $value === null || trim($value) === '') {
    //                             Log::info("Skipping month $month for row $i: value is $value");
    //                             continue;
    //                         }

    //                         $model::create([
    //                             'sub_id' => $sub_id,
    //                             'purpose' => $purpose,
    //                             'acc_id' => $acc_id,
    //                             'itm_id' => $itm_id,
    //                             'description' => $description,
    //                             'unit' => $unit,
    //                             'quantity' => $quantity,
    //                             'price' => $price,
    //                             'amount' => $amount,
    //                             'wct_id' => $wct_id,
    //                             'dpt_id' => $dpt_id,
    //                             'bdc_id' => $bdc_id,
    //                             'lob_id' => $lob_id,
    //                             'month' => $month,
    //                             'status' => 1,
    //                         ]);
    //                         Log::info("Created record for sub_id: $sub_id, month: $month, value: $value, itm_id: $itm_id");
    //                         $processedRows++;
    //                     }
    //                 } elseif ($template === 'insurance') {
    //                     [$no, $description, $ins_id, $quantity, $price, $amount, $wct_id, $dpt_id] = array_slice($row, 0, 8);

    //                     // Validate dpt_id
    //                     if ($dpt_id !== $userDept) {
    //                         $errors[] = "Invalid dpt_id in row $i of sheet $sheetName: Expected $userDept, got $dpt_id";
    //                         Log::warning("Invalid dpt_id in row $i of sheet $sheetName: Expected $userDept, got $dpt_id");
    //                         continue; // Skip this row
    //                     }

    //                     // Validasi kolom wajib tidak boleh kosong
    //                     $requiredFields = [
    //                         'description' => $description,
    //                         'ins_id' => $ins_id,
    //                         'quantity' => $quantity,
    //                         'price' => $price,
    //                         'amount' => $amount,
    //                         'dpt_id' => $dpt_id,
    //                     ];

    //                     foreach ($requiredFields as $fieldName => $value) {
    //                         if (is_null($value) || $value === '' || trim($value) === '') {
    //                             $errors[] = "Invalid $fieldName in row $i of sheet $sheetName: $fieldName is empty or null";
    //                             Log::warning("Invalid $fieldName in row $i of sheet $sheetName: $fieldName is empty or null");
    //                             continue 2; // Skip the entire row
    //                         }
    //                     }

    //                     // Validasi quantity, price, dan amount harus numerik
    //                     if (!is_numeric($quantity) || !is_numeric($price) || !is_numeric($amount)) {
    //                         $errors[] = "Invalid numeric value in row $i of sheet $sheetName: quantity=$quantity, price=$price, amount=$amount";
    //                         Log::warning("Invalid numeric value in row $i: quantity=$quantity, price=$price, amount=$amount");
    //                         continue;
    //                     }

    //                     Log::info("Processing row $i in sheet $sheetName");

    //                     foreach ($months as $index => $month) {
    //                         $value = $row[8 + $index] ?? 0;
    //                         if ($value == 0 || $value === null || trim($value) === '') {
    //                             Log::info("Skipping month $month for row $i: value is $value");
    //                             continue;
    //                         }

    //                         $model::create([
    //                             'sub_id' => $sub_id,
    //                             'purpose' => $purpose,
    //                             'acc_id' => $acc_id,
    //                             'description' => $description,
    //                             'ins_id' => $ins_id,
    //                             'quantity' => $quantity,
    //                             'price' => $price,
    //                             'amount' => $amount,
    //                             'wct_id' => $wct_id,
    //                             'dpt_id' => $dpt_id,
    //                             'month' => $month,
    //                             'status' => 1,
    //                         ]);
    //                         Log::info("Created record for sub_id: $sub_id, month: $month, value: $value");
    //                         $processedRows++;
    //                     }
    //                 } elseif ($template === 'utilities') {
    //                     [$no, $itm_id, $kwh, $quantity, $price, $amount, $wct_id, $dpt_id, $bdc_id] = array_slice($row, 0, 9);

    //                     // Validate dpt_id
    //                     if ($dpt_id !== $userDept) {
    //                         $errors[] = "Invalid dpt_id in row $i of sheet $sheetName: Expected $userDept, got $dpt_id";
    //                         Log::warning("Invalid dpt_id in row $i of sheet $sheetName: Expected $userDept, got $dpt_id");
    //                         continue; // Skip this row
    //                     }

    //                     // Validate itm_id for GID items and normalize
    //                     $original_itm_id = $itm_id;
    //                     if ($this->isGidItem($itm_id)) {
    //                         $itm_id = $this->validateItemId($itm_id, $i, $sheetName, $errors);
    //                         if ($itm_id === false) {
    //                             continue; // Skip this row if itm_id is invalid
    //                         }
    //                     }

    //                     // Validasi kolom wajib tidak boleh kosong
    //                     $requiredFields = [
    //                         'itm_id' => $itm_id,
    //                         'kwh' => $kwh,
    //                         'quantity' => $quantity,
    //                         'price' => $price,
    //                         'amount' => $amount,
    //                         'dpt_id' => $dpt_id,
    //                         'bdc_id' => $bdc_id,
    //                     ];

    //                     foreach ($requiredFields as $fieldName => $value) {
    //                         if (is_null($value) || $value === '' || trim($value) === '') {
    //                             $errors[] = "Invalid $fieldName in row $i of sheet $sheetName: $fieldName is empty or null";
    //                             Log::warning("Invalid $fieldName in row $i of sheet $sheetName: $fieldName is empty or null");
    //                             continue 2; // Skip the entire row
    //                         }
    //                     }

    //                     // Validasi quantity, price, dan amount harus numerik
    //                     if (!is_numeric($quantity) || !is_numeric($price) || !is_numeric($amount)) {
    //                         $errors[] = "Invalid numeric value in row $i of sheet $sheetName: quantity=$quantity, price=$price, amount=$amount";
    //                         Log::warning("Invalid numeric value in row $i: quantity=$quantity, price=$price, amount=$amount");
    //                         continue;
    //                     }

    //                     Log::info("Processing row $i in sheet $sheetName with itm_id: $itm_id");

    //                     foreach ($months as $index => $month) {
    //                         $value = $row[9 + $index] ?? 0;
    //                         if ($value == 0 || $value === null || trim($value) === '') {
    //                             Log::info("Skipping month $month for row $i: value is $value");
    //                             continue;
    //                         }

    //                         $model::create([
    //                             'sub_id' => $sub_id,
    //                             'purpose' => $purpose,
    //                             'acc_id' => $acc_id,
    //                             'itm_id' => $itm_id,
    //                             'kwh' => $kwh,
    //                             'quantity' => $quantity,
    //                             'price' => $price,
    //                             'amount' => $amount,
    //                             'wct_id' => $wct_id,
    //                             'dpt_id' => $dpt_id,
    //                             'bdc_id' => $bdc_id,
    //                             'month' => $month,
    //                             'status' => 1,
    //                         ]);
    //                         Log::info("Created record for sub_id: $sub_id, month: $month, value: $value, itm_id: $itm_id");
    //                         $processedRows++;
    //                     }
    //                 } elseif ($template === 'business') {
    //                     [$no, $itm_id, $description, $days, $quantity, $price, $amount, $wct_id, $dpt_id, $bdc_id] = array_slice($row, 0, 10);

    //                     // Validate dpt_id
    //                     if ($dpt_id !== $userDept) {
    //                         $errors[] = "Invalid dpt_id in row $i of sheet $sheetName: Expected $userDept, got $dpt_id";
    //                         Log::warning("Invalid dpt_id in row $i of sheet $sheetName: Expected $userDept, got $dpt_id");
    //                         continue; // Skip this row
    //                     }

    //                     // Validate itm_id for GID items and normalize
    //                     $original_itm_id = $itm_id;
    //                     if ($this->isGidItem($itm_id)) {
    //                         $itm_id = $this->validateItemId($itm_id, $i, $sheetName, $errors);
    //                         if ($itm_id === false) {
    //                             continue; // Skip this row if itm_id is invalid
    //                         }
    //                     }

    //                     // Validasi kolom wajib tidak boleh kosong
    //                     $requiredFields = [
    //                         'itm_id' => $itm_id,
    //                         'description' => $description,
    //                         'days' => $days,
    //                         'quantity' => $quantity,
    //                         'price' => $price,
    //                         'amount' => $amount,
    //                         'dpt_id' => $dpt_id,
    //                         'bdc_id' => $bdc_id,
    //                     ];

    //                     foreach ($requiredFields as $fieldName => $value) {
    //                         if (is_null($value) || $value === '' || trim($value) === '') {
    //                             $errors[] = "Invalid $fieldName in row $i of sheet $sheetName: $fieldName is empty or null";
    //                             Log::warning("Invalid $fieldName in row $i of sheet $sheetName: $fieldName is empty or null");
    //                             continue 2; // Skip the entire row
    //                         }
    //                     }

    //                     // Validasi quantity, price, dan amount harus numerik
    //                     if (!is_numeric($quantity) || !is_numeric($price) || !is_numeric($amount)) {
    //                         $errors[] = "Invalid numeric value in row $i of sheet $sheetName: quantity=$quantity, price=$price, amount=$amount";
    //                         Log::warning("Invalid numeric value in row $i: quantity=$quantity, price=$price, amount=$amount");
    //                         continue;
    //                     }

    //                     Log::info("Processing row $i in sheet $sheetName with itm_id: $itm_id");

    //                     foreach ($months as $index => $month) {
    //                         $value = $row[10 + $index] ?? 0;
    //                         if ($value == 0 || $value === null || trim($value) === '') {
    //                             Log::info("Skipping month $month for row $i: value is $value");
    //                             continue;
    //                         }

    //                         $model::create([
    //                             'sub_id' => $sub_id,
    //                             'purpose' => $purpose,
    //                             'acc_id' => $acc_id,
    //                             'itm_id' => $itm_id,
    //                             'description' => $description,
    //                             'days' => $days,
    //                             'quantity' => $quantity,
    //                             'price' => $price,
    //                             'amount' => $amount,
    //                             'wct_id' => $wct_id,
    //                             'dpt_id' => $dpt_id,
    //                             'bdc_id' => $bdc_id,
    //                             'month' => $month,
    //                             'status' => 1,
    //                         ]);
    //                         Log::info("Created record for sub_id: $sub_id, month: $month, value: $value, itm_id: $itm_id");
    //                         $processedRows++;
    //                     }
    //                 } elseif ($template === 'representation') {
    //                     [$no, $itm_id, $description, $beneficiary, $quantity, $price, $amount, $wct_id, $dpt_id, $bdc_id] = array_slice($row, 0, 10);

    //                     // Validate dpt_id
    //                     if ($dpt_id !== $userDept) {
    //                         $errors[] = "Invalid dpt_id in row $i of sheet $sheetName: Expected $userDept, got $dpt_id";
    //                         Log::warning("Invalid dpt_id in row $i of sheet $sheetName: Expected $userDept, got $dpt_id");
    //                         continue; // Skip this row
    //                     }

    //                     // Validate itm_id for GID items and normalize
    //                     $original_itm_id = $itm_id;
    //                     if ($this->isGidItem($itm_id)) {
    //                         $itm_id = $this->validateItemId($itm_id, $i, $sheetName, $errors);
    //                         if ($itm_id === false) {
    //                             continue; // Skip this row if itm_id is invalid
    //                         }
    //                     }

    //                     // Validasi kolom wajib tidak boleh kosong
    //                     $requiredFields = [
    //                         'itm_id' => $itm_id,
    //                         'description' => $description,
    //                         'beneficiary' => $beneficiary,
    //                         'quantity' => $quantity,
    //                         'price' => $price,
    //                         'amount' => $amount,
    //                         'dpt_id' => $dpt_id,
    //                         'bdc_id' => $bdc_id,
    //                     ];

    //                     foreach ($requiredFields as $fieldName => $value) {
    //                         if (is_null($value) || $value === '' || trim($value) === '') {
    //                             $errors[] = "Invalid $fieldName in row $i of sheet $sheetName: $fieldName is empty or null";
    //                             Log::warning("Invalid $fieldName in row $i of sheet $sheetName: $fieldName is empty or null");
    //                             continue 2; // Skip the entire row
    //                         }
    //                     }

    //                     // Validasi quantity, price, dan amount harus numerik
    //                     if (!is_numeric($quantity) || !is_numeric($price) || !is_numeric($amount)) {
    //                         $errors[] = "Invalid numeric value in row $i of sheet $sheetName: quantity=$quantity, price=$price, amount=$amount";
    //                         Log::warning("Invalid numeric value in row $i: quantity=$quantity, price=$price, amount=$amount");
    //                         continue;
    //                     }

    //                     Log::info("Processing row $i in sheet $sheetName with itm_id: $itm_id");

    //                     foreach ($months as $index => $month) {
    //                         $value = $row[10 + $index] ?? 0;
    //                         if ($value == 0 || $value === null || trim($value) === '') {
    //                             Log::info("Skipping month $month for row $i: value is $value");
    //                             continue;
    //                         }

    //                         $model::create([
    //                             'sub_id' => $sub_id,
    //                             'purpose' => $purpose,
    //                             'acc_id' => $acc_id,
    //                             'itm_id' => $itm_id,
    //                             'description' => $description,
    //                             'beneficiary' => $beneficiary,
    //                             'quantity' => $quantity,
    //                             'price' => $price,
    //                             'amount' => $amount,
    //                             'wct_id' => $wct_id,
    //                             'dpt_id' => $dpt_id,
    //                             'bdc_id' => $bdc_id,
    //                             'month' => $month,
    //                             'status' => 1,
    //                         ]);
    //                         Log::info("Created record for sub_id: $sub_id, month: $month, value: $value, itm_id: $itm_id");
    //                         $processedRows++;
    //                     }
    //                 } elseif ($template === 'training') {
    //                     [$no, $participant, $jenis_training, $quantity, $price, $amount, $wct_id, $dpt_id, $bdc_id] = array_slice($row, 0, 9);

    //                     // Validate dpt_id
    //                     if ($dpt_id !== $userDept) {
    //                         $errors[] = "Invalid dpt_id in row $i of sheet $sheetName: Expected $userDept, got $dpt_id";
    //                         Log::warning("Invalid dpt_id in row $i of sheet $sheetName: Expected $userDept, got $dpt_id");
    //                         continue; // Skip this row
    //                     }

    //                     // Validasi kolom wajib tidak boleh kosong
    //                     $requiredFields = [
    //                         'participant' => $participant,
    //                         'jenis_training' => $jenis_training,
    //                         'quantity' => $quantity,
    //                         'price' => $price,
    //                         'amount' => $amount,
    //                         'dpt_id' => $dpt_id,
    //                         'bdc_id' => $bdc_id,
    //                     ];

    //                     foreach ($requiredFields as $fieldName => $value) {
    //                         if (is_null($value) || $value === '' || trim($value) === '') {
    //                             $errors[] = "Invalid $fieldName in row $i of sheet $sheetName: $fieldName is empty or null";
    //                             Log::warning("Invalid $fieldName in row $i of sheet $sheetName: $fieldName is empty or null");
    //                             continue 2; // Skip the entire row
    //                         }
    //                     }

    //                     // Validasi quantity, price, dan amount harus numerik
    //                     if (!is_numeric($quantity) || !is_numeric($price) || !is_numeric($amount)) {
    //                         $errors[] = "Invalid numeric value in row $i of sheet $sheetName: quantity=$quantity, price=$price, amount=$amount";
    //                         Log::warning("Invalid numeric value in row $i: quantity=$quantity, price=$price, amount=$amount");
    //                         continue;
    //                     }

    //                     Log::info("Processing row $i in sheet $sheetName");

    //                     foreach ($months as $index => $month) {
    //                         $value = $row[9 + $index] ?? 0;
    //                         if ($value == 0 || $value === null || trim($value) === '') {
    //                             Log::info("Skipping month $month for row $i: value is $value");
    //                             continue;
    //                         }

    //                         $model::create([
    //                             'sub_id' => $sub_id,
    //                             'purpose' => $purpose,
    //                             'acc_id' => $acc_id,
    //                             'participant' => $participant,
    //                             'jenis_training' => $jenis_training,
    //                             'quantity' => $quantity,
    //                             'price' => $price,
    //                             'amount' => $amount,
    //                             'wct_id' => $wct_id,
    //                             'dpt_id' => $dpt_id,
    //                             'bdc_id' => $bdc_id,
    //                             'month' => $month,
    //                             'status' => 1,
    //                         ]);
    //                         Log::info("Created record for sub_id: $sub_id, month: $month, value: $value");
    //                         $processedRows++;
    //                     }
    //                 }
    //             } catch (\Exception $e) {
    //                 Log::error("Failed to create record for sub_id: $sub_id, month: $month, sheet: $sheetName, error: " . $e->getMessage());
    //                 $errors[] = "Failed to process row $i in sheet $sheetName: " . $e->getMessage();
    //             }
    //         }
    //     }

    //     // Respons berdasarkan hasil pemrosesan
    //     if ($processedRows === 0) {
    //         Log::warning('No rows were processed', ['sheets_processed' => $processedSheets, 'errors' => $errors]);
    //         return response()->json([
    //             'message' => 'No data was processed. Please check the file content or sheet names.',
    //             'sheets_processed' => $processedSheets,
    //             'processed_rows' => $processedRows,
    //             'errors' => $errors
    //         ], 400);
    //     }

    //     Log::info('Upload completed successfully', [
    //         'sheets_processed' => $processedSheets,
    //         'processed_rows' => $processedRows,
    //         'errors' => $errors
    //     ]);

    //     return response()->json([
    //         'message' => 'Data uploaded successfully.',
    //         'sheets_processed' => $processedSheets,
    //         'processed_rows' => $processedRows,
    //         'errors' => $errors
    //     ]);
    // }


    // private function isGidItem($itm_id)
    // {
    //     if (is_null($itm_id) || $itm_id === '' || trim($itm_id) === '') {
    //         return false;
    //     }

    //     // Patterns for GID items:
    //     // 1. Ends with 'T' (e.g., 0.8T, 0,8T)
    //     // 2. Numeric with hyphens (e.g., 00850-30007-)
    //     // 3. Numeric with hyphen followed by letters (e.g., 02-BDI)
    //     $pattern = '/(\d+[,.]?\d*T$)|(\d+-\d+(-.*)?$)|(\d+-[A-Z]+)$/i';
    //     return preg_match($pattern, $itm_id) === 1;
    // }


    // private function validateItemId($itm_id, $rowIndex, $sheetName, &$errors)
    // {
    //     // Skip validation if itm_id is empty or null
    //     if (is_null($itm_id) || $itm_id === '' || trim($itm_id) === '') {
    //         return $itm_id; // Will be caught by requiredFields validation
    //     }

    //     // Normalize itm_id: replace comma with dot and convert to uppercase
    //     $normalized_itm_id = strtoupper(str_replace(',', '.', $itm_id));

    //     // Check if the item exists in the Item table
    //     $item = Item::where('itm_id', $normalized_itm_id)->first();

    //     if (!$item) {
    //         $errors[] = "Invalid GID itm_id in row $rowIndex of sheet $sheetName: '$itm_id' not found in item table";
    //         Log::warning("Invalid GID itm_id in row $rowIndex of sheet $sheetName: '$itm_id' not found in item table");
    //         return false;
    //     }

    //     // Return the normalized itm_id
    //     return $normalized_itm_id;
    // }


    //     public function upload(Request $request)
    // {
    //     // Log request masuk
    //     Log::info('Upload request received', [
    //         'file' => $request->file('file') ? $request->file('file')->getClientOriginalName() : 'No file',
    //         'purpose' => $request->input('purpose'),
    //         'timestamp' => now()->toDateTimeString()
    //     ]);

    //     // Validasi input
    //     $request->validate([
    //         'file' => 'required|file|mimes:xlsx,xls',
    //         'purpose' => 'required|string'
    //     ]);

    //     // Check if user is authenticated
    //     if (!Auth::check()) {
    //         Log::error('User not authenticated');
    //         return response()->json(['message' => 'User not authenticated'], 401);
    //     }

    //     $file = $request->file('file');
    //     $purpose = $request->input('purpose');
    //     $npk = Auth::user()->npk; // Get NPK of logged-in user
    //     $userDept = Auth::user()->dept; // Get department of logged-in user

    //     // Load file Excel
    //     try {
    //         $spreadsheet = IOFactory::load($file);
    //         Log::info('Excel file loaded successfully', ['sheets' => $spreadsheet->getSheetNames()]);
    //     } catch (\Exception $e) {
    //         Log::error('Failed to load Excel file', ['error' => $e->getMessage()]);
    //         return response()->json(['message' => 'Failed to load Excel file: ' . $e->getMessage()], 500);
    //     }

    //     // Define templates that require itm_id validation
    //     $templatesRequiringItemValidation = ['general', 'aftersales', 'support', 'utilities', 'business', 'representation'];

    //     // Mapping sheet ke prefix, acc_id, model, dan template
    //     $sheetMappings = [
    //             'ADVERTISING & PROMOTION' => [
    //                 'prefix' => 'ADP',
    //                 'acc_id' => 'SGAADVERT',
    //                 'model' => BudgetPlan::class,
    //                 'template' => 'general'
    //             ],
    //             'COMMUNICATION' => [
    //                 'prefix' => 'COM',
    //                 'acc_id' => 'SGACOM',
    //                 'model' => BudgetPlan::class,
    //                 'template' => 'general'
    //             ],
    //             'OFFICE SUPPLY' => [
    //                 'prefix' => 'OFS',
    //                 'acc_id' => 'SGAOFFICESUP',
    //                 'model' => BudgetPlan::class,
    //                 'template' => 'general'
    //             ],
    //             'AFTER SALES SERVICE' => [
    //                 'prefix' => 'AFS',
    //                 'acc_id' => 'SGAAFTERSALES',
    //                 'model' => BudgetPlan::class,
    //                 'template' => 'aftersales'
    //             ],
    //             'INDIRECT MATERIAL' => [
    //                 'prefix' => 'IDM',
    //                 'acc_id' => 'FOHINDMAT',
    //                 'model' => BudgetPlan::class,
    //                 'template' => 'support'
    //             ],
    //             'FACTORY SUPPLY' => [
    //                 'prefix' => 'FSU',
    //                 'acc_id' => 'FOHFS',
    //                 'model' => BudgetPlan::class,
    //                 'template' => 'support'
    //             ],
    //             'REPAIR & MAINTENANCE FOH' => [
    //                 'prefix' => 'RPMF',
    //                 'acc_id' => 'FOHREPAIR',
    //                 'model' => BudgetPlan::class,
    //                 'template' => 'support'
    //             ],
    //             'CONS TOOLS' => [
    //                 'prefix' => 'CTL',
    //                 'acc_id' => 'FOHTOOLS',
    //                 'model' => BudgetPlan::class,
    //                 'template' => 'support'
    //             ],
    //             'INSURANCE PREM FOH' => [
    //                 'prefix' => 'INSF',
    //                 'acc_id' => 'FOHINSPREM',
    //                 'model' => BudgetPlan::class,
    //                 'template' => 'insurance'
    //             ],
    //             'INSURANCE PREM OPEX' => [
    //                 'prefix' => 'INSO',
    //                 'acc_id' => 'SGAINSURANCE',
    //                 'model' => BudgetPlan::class,
    //                 'template' => 'insurance'
    //             ],
    //             'TAX & PUBLIC DUES FOH' => [
    //                 'prefix' => 'TAXF',
    //                 'acc_id' => 'FOHTAXPUB',
    //                 'model' => BudgetPlan::class,
    //                 'template' => 'general'
    //             ],
    //             'TAX & PUBLIC DUES OPEX' => [
    //                 'prefix' => 'TAXO',
    //                 'acc_id' => 'SGATAXPUB',
    //                 'model' => BudgetPlan::class,
    //                 'template' => 'general'
    //             ],
    //             'PROFESIONAL FEE FOH' => [
    //                 'prefix' => 'PRFF',
    //                 'acc_id' => 'FOHPROF',
    //                 'model' => BudgetPlan::class,
    //                 'template' => 'general'
    //             ],
    //             'PROFESIONAL FEE OPEX' => [
    //                 'prefix' => 'PRFO',
    //                 'acc_id' => 'SGAPROF',
    //                 'model' => BudgetPlan::class,
    //                 'template' => 'general'
    //             ],
    //             'AUTOMOBILE FOH' => [
    //                 'prefix' => 'AUTF',
    //                 'acc_id' => 'FOHAUTOMOBILE',
    //                 'model' => BudgetPlan::class,
    //                 'template' => 'general'
    //             ],
    //             'AUTOMOBILE OPEX' => [
    //                 'prefix' => 'AUTO',
    //                 'acc_id' => 'SGAAUTOMOBILE',
    //                 'model' => BudgetPlan::class,
    //                 'template' => 'general'
    //             ],
    //             'RENT EXPENSE FOH' => [
    //                 'prefix' => 'REXF',
    //                 'acc_id' => 'FOHRENT',
    //                 'model' => BudgetPlan::class,
    //                 'template' => 'general'
    //             ],
    //             'PACKING & DELIVERY' => [
    //                 'prefix' => 'PKD',
    //                 'acc_id' => 'FOHPACKING',
    //                 'model' => BudgetPlan::class,
    //                 'template' => 'general'
    //             ],
    //             'BANK CHARGES' => [
    //                 'prefix' => 'BKC',
    //                 'acc_id' => 'SGABCHARGES',
    //                 'model' => BudgetPlan::class,
    //                 'template' => 'general'
    //             ],
    //             'ROYALTY' => [
    //                 'prefix' => 'RYL',
    //                 'acc_id' => 'SGARYLT',
    //                 'model' => BudgetPlan::class,
    //                 'template' => 'general'
    //             ],
    //             'CONTRIBUTION' => [
    //                 'prefix' => 'CTR',
    //                 'acc_id' => 'SGACONTRIBUTION',
    //                 'model' => BudgetPlan::class,
    //                 'template' => 'general'
    //             ],
    //             'ASSOCIATION' => [
    //                 'prefix' => 'ASC',
    //                 'acc_id' => 'SGAASSOCIATION',
    //                 'model' => BudgetPlan::class,
    //                 'template' => 'general'
    //             ],
    //             'UTILITIES FOH' => [
    //                 'prefix' => 'UTLF',
    //                 'acc_id' => 'FOHPOWER',
    //                 'model' => BudgetPlan::class,
    //                 'template' => 'utilities'
    //             ],
    //             'UTILITIES OPEX' => [
    //                 'prefix' => 'UTLO',
    //                 'acc_id' => 'SGAPOWER',
    //                 'model' => BudgetPlan::class,
    //                 'template' => 'utilities'
    //             ],
    //             'BUSINESS DUTY FOH' => [
    //                 'prefix' => 'BSDF',
    //                 'acc_id' => 'FOHTRAV',
    //                 'model' => BudgetPlan::class,
    //                 'template' => 'business'
    //             ],
    //             'BUSINESS DUTY OPEX' => [
    //                 'prefix' => 'BSDO',
    //                 'acc_id' => 'SGATRAV',
    //                 'model' => BudgetPlan::class,
    //                 'template' => 'business'
    //             ],
    //             'TRAINING & EDUCATION FOH' => [
    //                 'prefix' => 'TEDF',
    //                 'acc_id' => 'FOHTRAINING',
    //                 'model' => BudgetPlan::class,
    //                 'template' => 'training'
    //             ],
    //             'TRAINING & EDUCATION OPEX' => [
    //                 'prefix' => 'TEDO',
    //                 'acc_id' => 'SGATRAINING',
    //                 'model' => BudgetPlan::class,
    //                 'template' => 'training'
    //             ],
    //             'TECHNICAL DEVELOPMENT FOH' => [
    //                 'prefix' => 'TCD',
    //                 'acc_id' => 'FOHTECHDO',
    //                 'model' => BudgetPlan::class,
    //                 'template' => 'general'
    //             ],
    //             'RECRUITMENT FOH' => [
    //                 'prefix' => 'RECF',
    //                 'acc_id' => 'FOHRECRUITING',
    //                 'model' => BudgetPlan::class,
    //                 'template' => 'general'
    //             ],
    //             'RECRUITMENT OPEX' => [
    //                 'prefix' => 'RECO',
    //                 'acc_id' => 'SGARECRUITING',
    //                 'model' => BudgetPlan::class,
    //                 'template' => 'general'
    //             ],
    //             'RENT EXPENSE OPEX' => [
    //                 'prefix' => 'REXO',
    //                 'acc_id' => 'SGARENT',
    //                 'model' => BudgetPlan::class,
    //                 'template' => 'general'
    //             ],
    //             'MARKETING ACTIVITY' => [
    //                 'prefix' => 'MKT',
    //                 'acc_id' => 'SGAMARKT',
    //                 'model' => BudgetPlan::class,
    //                 'template' => 'general'
    //             ],
    //             'REPAIR & MAINTENANCE OPEX' => [
    //                 'prefix' => 'RPMO',
    //                 'acc_id' => 'SGAREPAIR',
    //                 'model' => BudgetPlan::class,
    //                 'template' => 'general'
    //             ],
    //             'BOOK NEWSPAPER' => [
    //                 'prefix' => 'BKN',
    //                 'acc_id' => 'SGABOOK',
    //                 'model' => BudgetPlan::class,
    //                 'template' => 'general'
    //             ],
    //             'ENTERTAINMENT FOH' => [
    //                 'prefix' => 'ENTF',
    //                 'acc_id' => 'FOHENTERTAINT',
    //                 'model' => BudgetPlan::class,
    //                 'template' => 'representation'
    //             ],
    //             'ENTERTAINMENT OPEX' => [
    //                 'prefix' => 'ENTO',
    //                 'acc_id' => 'SGAENTERTAINT',
    //                 'model' => BudgetPlan::class,
    //                 'template' => 'representation'
    //             ],
    //             'REPRESENTATION FOH' => [
    //                 'prefix' => 'REPF',
    //                 'acc_id' => 'FOHREPRESENTATION',
    //                 'model' => BudgetPlan::class,
    //                 'template' => 'representation'
    //             ],
    //             'REPRESENTATION OPEX' => [
    //                 'prefix' => 'REPO',
    //                 'acc_id' => 'SGAREPRESENTATION',
    //                 'model' => BudgetPlan::class,
    //                 'template' => 'representation'
    //             ],
    //         ];

    //     $months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    //     $processedRows = 0;
    //     $processedSheets = [];
    //     $errors = []; // Array to collect validation errors

    //     // Iterasi setiap sheet di file Excel
    //     foreach ($spreadsheet->getSheetNames() as $sheetName) {
    //         if (!isset($sheetMappings[$sheetName])) {
    //             Log::warning("Sheet '$sheetName' not found in sheetMappings");
    //             continue;
    //         }

    //         $sheetConfig = $sheetMappings[$sheetName];
    //         $prefix = $sheetConfig['prefix'];
    //         $acc_id = $sheetConfig['acc_id'];
    //         $model = $sheetConfig['model'];
    //         $template = $sheetConfig['template'];

    //         Log::info("Checking sheet: $sheetName, template: $template, model: $model");

    //         // Load data dari sheet
    //         $sheet = $spreadsheet->getSheetByName($sheetName);
    //         $data = $sheet->toArray();
    //         Log::info("Sheet '$sheetName' has " . count($data) . " rows");

    //         // Check if sheet has valid data rows (excluding header)
    //         $hasValidData = false;
    //         foreach ($data as $i => $row) {
    //             if ($i === 0) {
    //                 Log::info("Skipping header row for sheet: $sheetName");
    //                 continue; // Skip header
    //             }

    //             // Check if row has any non-empty values
    //             $rowHasData = array_filter($row, function ($value) {
    //                 return !is_null($value) && $value !== '';
    //             });

    //             if (!empty($rowHasData)) {
    //                 $hasValidData = true;
    //                 break; // Found at least one valid data row
    //             }
    //         }

    //         if (!$hasValidData) {
    //             Log::info("Sheet '$sheetName' has no valid data rows, skipping processing");
    //             continue;
    //         }

    //         // Generate sub_id sekali per worksheet dengan data valid
    //         $lastRecord = $model::where('sub_id', 'like', "$prefix%")
    //             ->orderBy('sub_id', 'desc')
    //             ->first();
    //         $nextNumber = $lastRecord ? ((int)str_replace($prefix, '', $lastRecord->sub_id) + 1) : 1;
    //         $sub_id = $prefix . str_pad($nextNumber, 7, '0', STR_PAD_LEFT);
    //         Log::info("Generated sub_id for sheet $sheetName: $sub_id");

    //         // Create approval record for the sub_id
    //         try {
    //             Approval::create([
    //                 'approve_by' => $npk,
    //                 'sub_id' => $sub_id,
    //                 'status' => 1,
    //                 'created_at' => now(),
    //                 'updated_at' => now(),
    //             ]);
    //             Log::info("Created approval record for sub_id: $sub_id, approve_by: $npk");
    //         } catch (\Exception $e) {
    //             Log::error("Failed to create approval record for sub_id: $sub_id, error: " . $e->getMessage());
    //             return response()->json(['message' => 'Failed to create approval record: ' . $e->getMessage()], 500);
    //         }

    //         $processedSheets[] = $sheetName;

    //         // Process data rows
    //         foreach ($data as $i => $row) {
    //             if ($i === 0) {
    //                 Log::info("Skipping header row for sheet: $sheetName");
    //                 continue; // Skip header
    //             }

    //             // Validasi jumlah kolom
    //             $expectedColumns = $this->getExpectedColumns($template, $months);
    //             if (count($row) < $expectedColumns) {
    //                 Log::warning("Invalid column count in row $i of sheet $sheetName: expected at least $expectedColumns, got " . count($row));
    //                 $errors[] = "Invalid column count in row $i of sheet $sheetName: expected at least $expectedColumns, got " . count($row);
    //                 continue;
    //             }

    //             Log::info("Processing row $i in sheet '$sheetName': " . json_encode($row));

    //             try {
    //                 if ($template === 'general') {
    //                     [$no, $itm_id, $description, $quantity, $price, $amount, $wct_id, $dpt_id, $bdc_id] = array_slice($row, 0, 9);

    //                     // Validate itm_id against items table
    //                     if (in_array($template, $templatesRequiringItemValidation)) {
    //                         $normalizedItmId = str_replace(',', '.', strtoupper(trim($itm_id)));
    //                         $itemExists = Item::where('itm_id', $normalizedItmId)->exists();
    //                         if (!$itemExists) {
    //                             $errors[] = "Invalid itm_id in row $i of sheet $sheetName: '$itm_id' does not exist in items table";
    //                             Log::warning("Invalid itm_id in row $i of sheet $sheetName: '$itm_id' does not exist in items table");
    //                             continue;
    //                         }
    //                     }

    //                     // Validate dpt_id
    //                     if ($dpt_id !== $userDept) {
    //                         $errors[] = "Invalid dpt_id in row $i of sheet $sheetName: Expected $userDept, got $dpt_id";
    //                         Log::warning("Invalid dpt_id in row $i of sheet $sheetName: Expected $userDept, got $dpt_id");
    //                         continue;
    //                     }

    //                     // Validate required fields
    //                     $requiredFields = [
    //                         'itm_id' => $itm_id,
    //                         'description' => $description,
    //                         'quantity' => $quantity,
    //                         'price' => $price,
    //                         'amount' => $amount,
    //                         'dpt_id' => $dpt_id,
    //                         'bdc_id' => $bdc_id,
    //                     ];

    //                     foreach ($requiredFields as $fieldName => $value) {
    //                         if (is_null($value) || $value === '' || trim($value) === '') {
    //                             $errors[] = "Invalid $fieldName in row $i of sheet $sheetName: $fieldName is empty or null";
    //                             Log::warning("Invalid $fieldName in row $i of sheet $sheetName: $fieldName is empty or null");
    //                             continue 2;
    //                         }
    //                     }

    //                     // Validate numeric fields
    //                     if (!is_numeric($quantity) || !is_numeric($price) || !is_numeric($amount)) {
    //                         $errors[] = "Invalid numeric value in row $i of sheet $sheetName: quantity=$quantity, price=$price, amount=$amount";
    //                         Log::warning("Invalid numeric value in row $i: quantity=$quantity, price=$price, amount=$amount");
    //                         continue;
    //                     }

    //                     foreach ($months as $index => $month) {
    //                         $value = $row[9 + $index] ?? 0;
    //                         if ($value == 0 || $value === null || trim($value) === '') {
    //                             Log::info("Skipping month $month for row $i: value is $value");
    //                             continue;
    //                         }

    //                         $model::create([
    //                             'sub_id' => $sub_id,
    //                             'purpose' => $purpose,
    //                             'acc_id' => $acc_id,
    //                             'itm_id' => $normalizedItmId, // Use normalized itm_id
    //                             'description' => $description,
    //                             'quantity' => $quantity,
    //                             'price' => $price,
    //                             'amount' => $amount,
    //                             'wct_id' => $wct_id,
    //                             'dpt_id' => $dpt_id,
    //                             'bdc_id' => $bdc_id,
    //                             'month' => $month,
    //                             'status' => 1,
    //                         ]);
    //                         Log::info("Created record for sub_id: $sub_id, month: $month, value: $value, itm_id: $normalizedItmId");
    //                         $processedRows++;
    //                     }
    //                 } elseif ($template === 'aftersales') {
    //                     [$no, $itm_id, $customer, $quantity, $price, $amount, $wct_id, $dpt_id, $bdc_id] = array_slice($row, 0, 9);

    //                     // Validate itm_id against items table
    //                     if (in_array($template, $templatesRequiringItemValidation)) {
    //                         $normalizedItmId = str_replace(',', '.', strtoupper(trim($itm_id)));
    //                         $itemExists = Item::where('itm_id', $normalizedItmId)->exists();
    //                         if (!$itemExists) {
    //                             $errors[] = "Invalid itm_id in row $i of sheet $sheetName: '$itm_id' does not exist in items table";
    //                             Log::warning("Invalid itm_id in row $i of sheet $sheetName: '$itm_id' does not exist in items table");
    //                             continue;
    //                         }
    //                     }

    //                     if ($dpt_id !== $userDept) {
    //                         $errors[] = "Invalid dpt_id in row $i of sheet $sheetName: Expected $userDept, got $dpt_id";
    //                         Log::warning("Invalid dpt_id in row $i of sheet $sheetName: Expected $userDept, got $dpt_id");
    //                         continue;
    //                     }

    //                     $requiredFields = [
    //                         'itm_id' => $itm_id,
    //                         'customer' => $customer,
    //                         'quantity' => $quantity,
    //                         'price' => $price,
    //                         'amount' => $amount,
    //                         'dpt_id' => $dpt_id,
    //                         'bdc_id' => $bdc_id,
    //                     ];

    //                     foreach ($requiredFields as $fieldName => $value) {
    //                         if (is_null($value) || $value === '' || trim($value) === '') {
    //                             $errors[] = "Invalid $fieldName in row $i of sheet $sheetName: $fieldName is empty or null";
    //                             Log::warning("Invalid $fieldName in row $i of sheet $sheetName: $fieldName is empty or null");
    //                             continue 2;
    //                         }
    //                     }

    //                     if (!is_numeric($quantity) || !is_numeric($price) || !is_numeric($amount)) {
    //                         $errors[] = "Invalid numeric value in row $i of sheet $sheetName: quantity=$quantity, price=$price, amount=$amount";
    //                         Log::warning("Invalid numeric value in row $i: quantity=$quantity, price=$price, amount=$amount");
    //                         continue;
    //                     }

    //                     foreach ($months as $index => $month) {
    //                         $value = $row[9 + $index] ?? 0;
    //                         if ($value == 0 || $value === null || trim($value) === '') {
    //                             Log::info("Skipping month $month for row $i: value is $value");
    //                             continue;
    //                         }
    //                         $model::create([
    //                             'sub_id' => $sub_id,
    //                             'purpose' => $purpose,
    //                             'acc_id' => $acc_id,
    //                             'itm_id' => $normalizedItmId,
    //                             'customer' => $customer,
    //                             'quantity' => $quantity,
    //                             'price' => $price,
    //                             'amount' => $amount,
    //                             'wct_id' => $wct_id,
    //                             'dpt_id' => $dpt_id,
    //                             'bdc_id' => $bdc_id,
    //                             'month' => $month,
    //                             'status' => 1,
    //                         ]);
    //                         Log::info("Created record for sub_id: $sub_id, month: $month, value: $value");
    //                         $processedRows++;
    //                     }
    //                 } elseif ($template === 'support') {
    //                     [$no, $itm_id, $description, $unit, $quantity, $price, $amount, $wct_id, $dpt_id, $bdc_id, $lob_id] = array_slice($row, 0, 11);

    //                     // Validate itm_id against items table
    //                     if (in_array($template, $templatesRequiringItemValidation)) {
    //                         $normalizedItmId = str_replace(',', '.', strtoupper(trim($itm_id)));
    //                         $itemExists = Item::where('itm_id', $normalizedItmId)->exists();
    //                         if (!$itemExists) {
    //                             $errors[] = "Invalid itm_id in row $i of sheet $sheetName: '$itm_id' does not exist in items table";
    //                             Log::warning("Invalid itm_id in row $i of sheet $sheetName: '$itm_id' does not exist in items table");
    //                             continue;
    //                         }
    //                     }

    //                     if ($dpt_id !== $userDept) {
    //                         $errors[] = "Invalid dpt_id in row $i of sheet $sheetName: Expected $userDept, got $dpt_id";
    //                         Log::warning("Invalid dpt_id in row $i of sheet $sheetName: Expected $userDept, got $dpt_id");
    //                         continue;
    //                     }

    //                     $requiredFields = [
    //                         'itm_id' => $itm_id,
    //                         'description' => $description,
    //                         'unit' => $unit,
    //                         'quantity' => $quantity,
    //                         'price' => $price,
    //                         'amount' => $amount,
    //                         'dpt_id' => $dpt_id,
    //                         'bdc_id' => $bdc_id,
    //                         'lob_id' => $lob_id,
    //                     ];

    //                     foreach ($requiredFields as $fieldName => $value) {
    //                         if (is_null($value) || $value === '' || trim($value) === '') {
    //                             $errors[] = "Invalid $fieldName in row $i of sheet $sheetName: $fieldName is empty or null";
    //                             Log::warning("Invalid $fieldName in row $i of sheet $sheetName: $fieldName is empty or null");
    //                             continue 2;
    //                         }
    //                     }

    //                     if (!is_numeric($quantity) || !is_numeric($price) || !is_numeric($amount)) {
    //                         $errors[] = "Invalid numeric value in row $i of sheet $sheetName: quantity=$quantity, price=$price, amount=$amount";
    //                         Log::warning("Invalid numeric value in row $i: quantity=$quantity, price=$price, amount=$amount");
    //                         continue;
    //                     }

    //                     foreach ($months as $index => $month) {
    //                         $value = $row[9 + $index] ?? 0;
    //                         if ($value == 0 || $value === null || trim($value) === '') {
    //                             Log::info("Skipping month $month for row $i: value is $value");
    //                             continue;
    //                         }

    //                         $model::create([
    //                             'sub_id' => $sub_id,
    //                             'purpose' => $purpose,
    //                             'acc_id' => $acc_id,
    //                             'itm_id' => $normalizedItmId,
    //                             'description' => $description,
    //                             'unit' => $unit,
    //                             'quantity' => $quantity,
    //                             'price' => $price,
    //                             'amount' => $amount,
    //                             'wct_id' => $wct_id,
    //                             'dpt_id' => $dpt_id,
    //                             'bdc_id' => $bdc_id,
    //                             'lob_id' => $lob_id,
    //                             'month' => $month,
    //                             'status' => 1,
    //                         ]);
    //                         Log::info("Created record for sub_id: $sub_id, month: $month, value: $value");
    //                         $processedRows++;
    //                     }
    //                 } elseif ($template === 'insurance') {
    //                     [$no, $description, $ins_id, $quantity, $price, $amount, $wct_id, $dpt_id] = array_slice($row, 0, 8);

    //                     // No itm_id validation for insurance template
    //                     if ($dpt_id !== $userDept) {
    //                         $errors[] = "Invalid dpt_id in row $i of sheet $sheetName: Expected $userDept, got $dpt_id";
    //                         Log::warning("Invalid dpt_id in row $i of sheet $sheetName: Expected $userDept, got $dpt_id");
    //                         continue;
    //                     }

    //                     $requiredFields = [
    //                         'description' => $description,
    //                         'ins_id' => $ins_id,
    //                         'quantity' => $quantity,
    //                         'price' => $price,
    //                         'amount' => $amount,
    //                         'dpt_id' => $dpt_id,
    //                     ];

    //                     foreach ($requiredFields as $fieldName => $value) {
    //                         if (is_null($value) || $value === '' || trim($value) === '') {
    //                             $errors[] = "Invalid $fieldName in row $i of sheet $sheetName: $fieldName is empty or null";
    //                             Log::warning("Invalid $fieldName in row $i of sheet $sheetName: $fieldName is empty or null");
    //                             continue 2;
    //                         }
    //                     }

    //                     if (!is_numeric($quantity) || !is_numeric($price) || !is_numeric($amount)) {
    //                         $errors[] = "Invalid numeric value in row $i of sheet $sheetName: quantity=$quantity, price=$price, amount=$amount";
    //                         Log::warning("Invalid numeric value in row $i: quantity=$quantity, price=$price, amount=$amount");
    //                         continue;
    //                     }

    //                     foreach ($months as $index => $month) {
    //                         $value = $row[9 + $index] ?? 0;
    //                         if ($value == 0 || $value === null || trim($value) === '') {
    //                             Log::info("Skipping month $month for row $i: value is $value");
    //                             continue;
    //                         }
    //                         $model::create([
    //                             'sub_id' => $sub_id,
    //                             'purpose' => $purpose,
    //                             'acc_id' => $acc_id,
    //                             'description' => $description,
    //                             'ins_id' => $ins_id,
    //                             'quantity' => $quantity,
    //                             'price' => $price,
    //                             'amount' => $amount,
    //                             'wct_id' => $wct_id,
    //                             'dpt_id' => $dpt_id,
    //                             'month' => $month,
    //                             'status' => 1,
    //                         ]);
    //                         Log::info("Created record for sub_id: $sub_id, month: $month, value: $value");
    //                         $processedRows++;
    //                     }
    //                 } elseif ($template === 'utilities') {
    //                     [$no, $itm_id, $kwh, $quantity, $price, $amount, $wct_id, $dpt_id, $bdc_id] = array_slice($row, 0, 9);

    //                     // Validate itm_id against items table
    //                     if (in_array($template, $templatesRequiringItemValidation)) {
    //                         $normalizedItmId = str_replace(',', '.', strtoupper(trim($itm_id)));
    //                         $itemExists = Item::where('itm_id', $normalizedItmId)->exists();
    //                         if (!$itemExists) {
    //                             $errors[] = "Invalid itm_id in row $i of sheet $sheetName: '$itm_id' does not exist in items table";
    //                             Log::warning("Invalid itm_id in row $i of sheet $sheetName: '$itm_id' does not exist in items table");
    //                             continue;
    //                         }
    //                     }

    //                     if ($dpt_id !== $userDept) {
    //                         $errors[] = "Invalid dpt_id in row $i of sheet $sheetName: Expected $userDept, got $dpt_id";
    //                         Log::warning("Invalid dpt_id in row $i of sheet $sheetName: Expected $userDept, got $dpt_id");
    //                         continue;
    //                     }

    //                     $requiredFields = [
    //                         'itm_id' => $itm_id,
    //                         'kwh' => $kwh,
    //                         'quantity' => $quantity,
    //                         'price' => $price,
    //                         'amount' => $amount,
    //                         'dpt_id' => $dpt_id,
    //                         'bdc_id' => $bdc_id,
    //                     ];

    //                     foreach ($requiredFields as $fieldName => $value) {
    //                         if (is_null($value) || $value === '' || trim($value) === '') {
    //                             $errors[] = "Invalid $fieldName in row $i of sheet $sheetName: $fieldName is empty or null";
    //                             Log::warning("Invalid $fieldName in row $i of sheet $sheetName: $fieldName is empty or null");
    //                             continue 2;
    //                         }
    //                     }

    //                     if (!is_numeric($quantity) || !is_numeric($price) || !is_numeric($amount)) {
    //                         $errors[] = "Invalid numeric value in row $i of sheet $sheetName: quantity=$quantity, price=$price, amount=$amount";
    //                         Log::warning("Invalid numeric value in row $i: quantity=$quantity, price=$price, amount=$amount");
    //                         continue;
    //                     }

    //                     foreach ($months as $index => $month) {
    //                         $value = $row[9 + $index] ?? 0;
    //                         if ($value == 0 || $value === null || trim($value) === '') {
    //                             Log::info("Skipping month $month for row $i: value is $value");
    //                             continue;
    //                         }
    //                         $model::create([
    //                             'sub_id' => $sub_id,
    //                             'purpose' => $purpose,
    //                             'acc_id' => $acc_id,
    //                             'itm_id' => $normalizedItmId,
    //                             'kwh' => $kwh,
    //                             'quantity' => $quantity,
    //                             'price' => $price,
    //                             'amount' => $amount,
    //                             'wct_id' => $wct_id,
    //                             'dpt_id' => $dpt_id,
    //                             'bdc_id' => $bdc_id,
    //                             'month' => $month,
    //                             'status' => 1,
    //                         ]);
    //                         Log::info("Created record for sub_id: $sub_id, month: $month, value: $value");
    //                         $processedRows++;
    //                     }
    //                 } elseif ($template === 'business') {
    //                     [$no, $itm_id, $description, $days, $quantity, $price, $amount, $wct_id, $dpt_id, $bdc_id] = array_slice($row, 0, 10);

    //                     // Validate itm_id against items table
    //                     if (in_array($template, $templatesRequiringItemValidation)) {
    //                         $normalizedItmId = str_replace(',', '.', strtoupper(trim($itm_id)));
    //                         $itemExists = Item::where('itm_id', $normalizedItmId)->exists();
    //                         if (!$itemExists) {
    //                             $errors[] = "Invalid itm_id in row $i of sheet $sheetName: '$itm_id' does not exist in items table";
    //                             Log::warning("Invalid itm_id in row $i of sheet $sheetName: '$itm_id' does not exist in items table");
    //                             continue;
    //                         }
    //                     }

    //                     if ($dpt_id !== $userDept) {
    //                         $errors[] = "Invalid dpt_id in row $i of sheet $sheetName: Expected $userDept, got $dpt_id";
    //                         Log::warning("Invalid dpt_id in row $i of sheet $sheetName: Expected $userDept, got $dpt_id");
    //                         continue;
    //                     }

    //                     $requiredFields = [
    //                         'itm_id' => $itm_id,
    //                         'description' => $description,
    //                         'days' => $days,
    //                         'quantity' => $quantity,
    //                         'price' => $price,
    //                         'amount' => $amount,
    //                         'dpt_id' => $dpt_id,
    //                         'bdc_id' => $bdc_id,
    //                     ];

    //                     foreach ($requiredFields as $fieldName => $value) {
    //                         if (is_null($value) || $value === '' || trim($value) === '') {
    //                             $errors[] = "Invalid $fieldName in row $i of sheet $sheetName: $fieldName is empty or null";
    //                             Log::warning("Invalid $fieldName in row $i of sheet $sheetName: $fieldName is empty or null");
    //                             continue 2;
    //                         }
    //                     }

    //                     if (!is_numeric($quantity) || !is_numeric($price) || !is_numeric($amount)) {
    //                         $errors[] = "Invalid numeric value in row $i of sheet $sheetName: quantity=$quantity, price=$price, amount=$amount";
    //                         Log::warning("Invalid numeric value in row $i: quantity=$quantity, price=$price, amount=$amount");
    //                         continue;
    //                     }

    //                     foreach ($months as $index => $month) {
    //                         $value = $row[9 + $index] ?? 0;
    //                         if ($value == 0 || $value === null || trim($value) === '') {
    //                             Log::info("Skipping month $month for row $i: value is $value");
    //                             continue;
    //                         }
    //                         $model::create([
    //                             'sub_id' => $sub_id,
    //                             'purpose' => $purpose,
    //                             'acc_id' => $acc_id,
    //                             'itm_id' => $normalizedItmId,
    //                             'description' => $description,
    //                             'days' => $days,
    //                             'quantity' => $quantity,
    //                             'price' => $price,
    //                             'amount' => $amount,
    //                             'wct_id' => $wct_id,
    //                             'dpt_id' => $dpt_id,
    //                             'bdc_id' => $bdc_id,
    //                             'month' => $month,
    //                             'status' => 1,
    //                         ]);
    //                         Log::info("Created record for sub_id: $sub_id, month: $month, value: $value");
    //                         $processedRows++;
    //                     }
    //                 } elseif ($template === 'representation') {
    //                     [$no, $itm_id, $description, $beneficiary, $quantity, $price, $amount, $wct_id, $dpt_id, $bdc_id] = array_slice($row, 0, 10);

    //                     // Validate itm_id against items table
    //                     if (in_array($template, $templatesRequiringItemValidation)) {
    //                         $normalizedItmId = str_replace(',', '.', strtoupper(trim($itm_id)));
    //                         $itemExists = Item::where('itm_id', $normalizedItmId)->exists();
    //                         if (!$itemExists) {
    //                             $errors[] = "Invalid itm_id in row $i of sheet $sheetName: '$itm_id' does not exist in items table";
    //                             Log::warning("Invalid itm_id in row $i of sheet $sheetName: '$itm_id' does not exist in items table");
    //                             continue;
    //                         }
    //                     }

    //                     if ($dpt_id !== $userDept) {
    //                         $errors[] = "Invalid dpt_id in row $i of sheet $sheetName: Expected $userDept, got $dpt_id";
    //                         Log::warning("Invalid dpt_id in row $i of sheet $sheetName: Expected $userDept, got $dpt_id");
    //                         continue;
    //                     }

    //                     $requiredFields = [
    //                         'itm_id' => $itm_id,
    //                         'description' => $description,
    //                         'beneficiary' => $beneficiary,
    //                         'quantity' => $quantity,
    //                         'price' => $price,
    //                         'amount' => $amount,
    //                         'dpt_id' => $dpt_id,
    //                         'bdc_id' => $bdc_id,
    //                     ];

    //                     foreach ($requiredFields as $fieldName => $value) {
    //                         if (is_null($value) || $value === '' || trim($value) === '') {
    //                             $errors[] = "Invalid $fieldName in row $i of sheet $sheetName: $fieldName is empty or null";
    //                             Log::warning("Invalid $fieldName in row $i of sheet $sheetName: $fieldName is empty or null");
    //                             continue 2;
    //                         }
    //                     }

    //                     if (!is_numeric($quantity) || !is_numeric($price) || !is_numeric($amount)) {
    //                         $errors[] = "Invalid numeric value in row $i of sheet $sheetName: quantity=$quantity, price=$price, amount=$amount";
    //                         Log::warning("Invalid numeric value in row $i: quantity=$quantity, price=$price, amount=$amount");
    //                         continue;
    //                     }

    //                     foreach ($months as $index => $month) {
    //                         $value = $row[9 + $index] ?? 0;
    //                         if ($value == 0 || $value === null || trim($value) === '') {
    //                             Log::info("Skipping month $month for row $i: value is $value");
    //                             continue;
    //                         }
    //                         $model::create([
    //                             'sub_id' => $sub_id,
    //                             'purpose' => $purpose,
    //                             'acc_id' => $acc_id,
    //                             'itm_id' => $normalizedItmId,
    //                             'description' => $description,
    //                             'beneficiary' => $beneficiary,
    //                             'quantity' => $quantity,
    //                             'price' => $price,
    //                             'amount' => $amount,
    //                             'wct_id' => $wct_id,
    //                             'dpt_id' => $dpt_id,
    //                             'bdc_id' => $bdc_id,
    //                             'month' => $month,
    //                             'status' => 1,
    //                         ]);
    //                         Log::info("Created record for sub_id: $sub_id, month: $month, value: $value");
    //                         $processedRows++;
    //                     }
    //                 } elseif ($template === 'training') {
    //                     [$no, $participant, $jenis_training, $quantity, $price, $amount, $wct_id, $dpt_id, $bdc_id] = array_slice($row, 0, 9);

    //                     // No itm_id validation for training template
    //                     if ($dpt_id !== $userDept) {
    //                         $errors[] = "Invalid dpt_id in row $i of sheet $sheetName: Expected $userDept, got $dpt_id";
    //                         Log::warning("Invalid dpt_id in row $i of sheet $sheetName: Expected $userDept, got $dpt_id");
    //                         continue;
    //                     }

    //                     $requiredFields = [
    //                         'participant' => $participant,
    //                         'jenis_training' => $jenis_training,
    //                         'quantity' => $quantity,
    //                         'price' => $price,
    //                         'amount' => $amount,
    //                         'dpt_id' => $dpt_id,
    //                         'bdc_id' => $bdc_id,
    //                     ];

    //                     foreach ($requiredFields as $fieldName => $value) {
    //                         if (is_null($value) || $value === '' || trim($value) === '') {
    //                             $errors[] = "Invalid $fieldName in row $i of sheet $sheetName: $fieldName is empty or null";
    //                             Log::warning("Invalid $fieldName in row $i of sheet $sheetName: $fieldName is empty or null");
    //                             continue 2;
    //                         }
    //                     }

    //                     if (!is_numeric($quantity) || !is_numeric($price) || !is_numeric($amount)) {
    //                         $errors[] = "Invalid numeric value in row $i of sheet $sheetName: quantity=$quantity, price=$price, amount=$amount";
    //                         Log::warning("Invalid numeric value in row $i: quantity=$quantity, price=$price, amount=$amount");
    //                         continue;
    //                     }

    //                     foreach ($months as $index => $month) {
    //                         $value = $row[9 + $index] ?? 0;
    //                         if ($value == 0 || $value === null || trim($value) === '') {
    //                             Log::info("Skipping month $month for row $i: value is $value");
    //                             continue;
    //                         }
    //                         $model::create([
    //                             'sub_id' => $sub_id,
    //                             'purpose' => $purpose,
    //                             'acc_id' => $acc_id,
    //                             'participant' => $participant,
    //                             'jenis_training' => $jenis_training,
    //                             'quantity' => $quantity,
    //                             'price' => $price,
    //                             'amount' => $amount,
    //                             'wct_id' => $wct_id,
    //                             'dpt_id' => $dpt_id,
    //                             'bdc_id' => $bdc_id,
    //                             'month' => $month,
    //                             'status' => 1,
    //                         ]);
    //                         Log::info("Created record for sub_id: $sub_id, month: $month, value: $value");
    //                         $processedRows++;
    //                     }
    //                 }
    //             } catch (\Exception $e) {
    //                 Log::error("Failed to create record for sub_id: $sub_id, month: $month, sheet: $sheetName, error: " . $e->getMessage());
    //                 $errors[] = "Failed to process row $i of sheet $sheetName: " . $e->getMessage();
    //             }
    //         }
    //     }

    //     // Respons berdasarkan hasil pemrosesan
    //     if ($processedRows === 0) {
    //         Log::warning('No rows were processed', ['sheets_processed' => $processedSheets, 'errors' => $errors]);
    //         return response()->json([
    //             'message' => 'No data was processed. Please check the file content or sheet names.',
    //             'sheets_processed' => $processedSheets,
    //             'processed_rows' => $processedRows,
    //             'errors' => $errors
    //         ], 400);
    //     }

    //     Log::info('Upload completed successfully', [
    //         'sheets_processed' => $processedSheets,
    //         'processed_rows' => $processedRows,
    //         'errors' => $errors
    //     ]);

    //     return response()->json([
    //         'message' => 'Data uploaded successfully.',
    //         'sheets_processed' => $processedSheets,
    //         'processed_rows' => $processedRows,
    //         'errors' => $errors
    //     ]);
    // }

    //     public function upload(Request $request)
    // {
    //     // Log request masuk
    //     Log::info('Upload request received', [
    //         'file' => $request->file('file') ? $request->file('file')->getClientOriginalName() : 'No file',
    //         'purpose' => $request->input('purpose'),
    //         'timestamp' => now()->toDateTimeString()
    //     ]);

    //     // Validasi input
    //     $request->validate([
    //         'file' => 'required|file|mimes:xlsx,xls',
    //         'purpose' => 'required|string'
    //     ]);

    //     // Check if user is authenticated
    //     if (!Auth::check()) {
    //         Log::error('User not authenticated');
    //         return response()->json(['message' => 'User not authenticated'], 401);
    //     }

    //     $file = $request->file('file');
    //     $purpose = $request->input('purpose');
    //     $npk = Auth::user()->npk; // Get NPK of logged-in user
    //     $userDept = Auth::user()->dept; // Get department of logged-in user

    //     // Load file Excel
    //     try {
    //         $spreadsheet = IOFactory::load($file);
    //         Log::info('Excel file loaded successfully', ['sheets' => $spreadsheet->getSheetNames()]);
    //     } catch (\Exception $e) {
    //         Log::error('Failed to load Excel file', ['error' => $e->getMessage()]);
    //         return response()->json(['message' => 'Failed to load Excel file: ' . $e->getMessage()], 500);
    //     }

    //     // Mapping sheet ke prefix, acc_id, model, dan template
    //     $sheetMappings = [
    //         'ADVERTISING & PROMOTION' => [
    //             'prefix' => 'ADP',
    //             'acc_id' => 'SGAADVERT',
    //             'model' => BudgetPlan::class,
    //             'template' => 'general'
    //         ],
    //         'COMMUNICATION' => [
    //             'prefix' => 'COM',
    //             'acc_id' => 'SGACOM',
    //             'model' => BudgetPlan::class,
    //             'template' => 'general'
    //         ],
    //         'OFFICE SUPPLY' => [
    //             'prefix' => 'OFS',
    //             'acc_id' => 'SGAOFFICESUP',
    //             'model' => BudgetPlan::class,
    //             'template' => 'general'
    //         ],
    //         'AFTER SALES SERVICE' => [
    //             'prefix' => 'AFS',
    //             'acc_id' => 'SGAAFTERSALES',
    //             'model' => BudgetPlan::class,
    //             'template' => 'aftersales'
    //         ],
    //         'INDIRECT MATERIAL' => [
    //             'prefix' => 'IDM',
    //             'acc_id' => 'FOHINDMAT',
    //             'model' => BudgetPlan::class,
    //             'template' => 'support'
    //         ],
    //         'FACTORY SUPPLY' => [
    //             'prefix' => 'FSU',
    //             'acc_id' => 'FOHFS',
    //             'model' => BudgetPlan::class,
    //             'template' => 'support'
    //         ],
    //         'REPAIR & MAINTENANCE FOH' => [
    //             'prefix' => 'RPMF',
    //             'acc_id' => 'FOHREPAIR',
    //             'model' => BudgetPlan::class,
    //             'template' => 'support'
    //         ],
    //         'CONS TOOLS' => [
    //             'prefix' => 'CTL',
    //             'acc_id' => 'FOHTOOLS',
    //             'model' => BudgetPlan::class,
    //             'template' => 'support'
    //         ],
    //         'INSURANCE PREM FOH' => [
    //             'prefix' => 'INSF',
    //             'acc_id' => 'FOHINSPREM',
    //             'model' => BudgetPlan::class,
    //             'template' => 'insurance'
    //         ],
    //         'INSURANCE PREM OPEX' => [
    //             'prefix' => 'INSO',
    //             'acc_id' => 'SGAINSURANCE',
    //             'model' => BudgetPlan::class,
    //             'template' => 'insurance'
    //         ],
    //         'TAX & PUBLIC DUES FOH' => [
    //             'prefix' => 'TAXF',
    //             'acc_id' => 'FOHTAXPUB',
    //             'model' => BudgetPlan::class,
    //             'template' => 'general'
    //         ],
    //         'TAX & PUBLIC DUES OPEX' => [
    //             'prefix' => 'TAXO',
    //             'acc_id' => 'SGATAXPUB',
    //             'model' => BudgetPlan::class,
    //             'template' => 'general'
    //         ],
    //         'PROFESIONAL FEE FOH' => [
    //             'prefix' => 'PRFF',
    //             'acc_id' => 'FOHPROF',
    //             'model' => BudgetPlan::class,
    //             'template' => 'general'
    //         ],
    //         'PROFESIONAL FEE OPEX' => [
    //             'prefix' => 'PRFO',
    //             'acc_id' => 'SGAPROF',
    //             'model' => BudgetPlan::class,
    //             'template' => 'general'
    //         ],
    //         'AUTOMOBILE FOH' => [
    //             'prefix' => 'AUTF',
    //             'acc_id' => 'FOHAUTOMOBILE',
    //             'model' => BudgetPlan::class,
    //             'template' => 'general'
    //         ],
    //         'AUTOMOBILE OPEX' => [
    //             'prefix' => 'AUTO',
    //             'acc_id' => 'SGAAUTOMOBILE',
    //             'model' => BudgetPlan::class,
    //             'template' => 'general'
    //         ],
    //         'RENT EXPENSE FOH' => [
    //             'prefix' => 'REXF',
    //             'acc_id' => 'FOHRENT',
    //             'model' => BudgetPlan::class,
    //             'template' => 'general'
    //         ],
    //         'PACKING & DELIVERY' => [
    //             'prefix' => 'PKD',
    //             'acc_id' => 'FOHPACKING',
    //             'model' => BudgetPlan::class,
    //             'template' => 'general'
    //         ],
    //         'BANK CHARGES' => [
    //             'prefix' => 'BKC',
    //             'acc_id' => 'SGABCHARGES',
    //             'model' => BudgetPlan::class,
    //             'template' => 'general'
    //         ],
    //         'ROYALTY' => [
    //             'prefix' => 'RYL',
    //             'acc_id' => 'SGARYLT',
    //             'model' => BudgetPlan::class,
    //             'template' => 'general'
    //         ],
    //         'CONTRIBUTION' => [
    //             'prefix' => 'CTR',
    //             'acc_id' => 'SGACONTRIBUTION',
    //             'model' => BudgetPlan::class,
    //             'template' => 'general'
    //         ],
    //         'ASSOCIATION' => [
    //             'prefix' => 'ASC',
    //             'acc_id' => 'SGAASSOCIATION',
    //             'model' => BudgetPlan::class,
    //             'template' => 'general'
    //         ],
    //         'UTILITIES FOH' => [
    //             'prefix' => 'UTLF',
    //             'acc_id' => 'FOHPOWER',
    //             'model' => BudgetPlan::class,
    //             'template' => 'utilities'
    //         ],
    //         'UTILITIES OPEX' => [
    //             'prefix' => 'UTLO',
    //             'acc_id' => 'SGAPOWER',
    //             'model' => BudgetPlan::class,
    //             'template' => 'utilities'
    //         ],
    //         'BUSINESS DUTY FOH' => [
    //             'prefix' => 'BSDF',
    //             'acc_id' => 'FOHTRAV',
    //             'model' => BudgetPlan::class,
    //             'template' => 'business'
    //         ],
    //         'BUSINESS DUTY OPEX' => [
    //             'prefix' => 'BSDO',
    //             'acc_id' => 'SGATRAV',
    //             'model' => BudgetPlan::class,
    //             'template' => 'business'
    //         ],
    //         'TRAINING & EDUCATION FOH' => [
    //             'prefix' => 'TEDF',
    //             'acc_id' => 'FOHTRAINING',
    //             'model' => BudgetPlan::class,
    //             'template' => 'training'
    //         ],
    //         'TRAINING & EDUCATION OPEX' => [
    //             'prefix' => 'TEDO',
    //             'acc_id' => 'SGATRAINING',
    //             'model' => BudgetPlan::class,
    //             'template' => 'training'
    //         ],
    //         'TECHNICAL DEVELOPMENT FOH' => [
    //             'prefix' => 'TCD',
    //             'acc_id' => 'FOHTECHDO',
    //             'model' => BudgetPlan::class,
    //             'template' => 'general'
    //         ],
    //         'RECRUITMENT FOH' => [
    //             'prefix' => 'RECF',
    //             'acc_id' => 'FOHRECRUITING',
    //             'model' => BudgetPlan::class,
    //             'template' => 'general'
    //         ],
    //         'RECRUITMENT OPEX' => [
    //             'prefix' => 'RECO',
    //             'acc_id' => 'SGARECRUITING',
    //             'model' => BudgetPlan::class,
    //             'template' => 'general'
    //         ],
    //         'RENT EXPENSE OPEX' => [
    //             'prefix' => 'REXO',
    //             'acc_id' => 'SGARENT',
    //             'model' => BudgetPlan::class,
    //             'template' => 'general'
    //         ],
    //         'MARKETING ACTIVITY' => [
    //             'prefix' => 'MKT',
    //             'acc_id' => 'SGAMARKT',
    //             'model' => BudgetPlan::class,
    //             'template' => 'general'
    //         ],
    //         'REPAIR & MAINTENANCE OPEX' => [
    //             'prefix' => 'RPMO',
    //             'acc_id' => 'SGAREPAIR',
    //             'model' => BudgetPlan::class,
    //             'template' => 'general'
    //         ],
    //         'BOOK NEWSPAPER' => [
    //             'prefix' => 'BKN',
    //             'acc_id' => 'SGABOOK',
    //             'model' => BudgetPlan::class,
    //             'template' => 'general'
    //         ],
    //         'ENTERTAINMENT FOH' => [
    //             'prefix' => 'ENTF',
    //             'acc_id' => 'FOHENTERTAINT',
    //             'model' => BudgetPlan::class,
    //             'template' => 'representation'
    //         ],
    //         'ENTERTAINMENT OPEX' => [
    //             'prefix' => 'ENTO',
    //             'acc_id' => 'SGAENTERTAINT',
    //             'model' => BudgetPlan::class,
    //             'template' => 'representation'
    //         ],
    //         'REPRESENTATION FOH' => [
    //             'prefix' => 'REPF',
    //             'acc_id' => 'FOHREPRESENTATION',
    //             'model' => BudgetPlan::class,
    //             'template' => 'representation'
    //         ],
    //         'REPRESENTATION OPEX' => [
    //             'prefix' => 'REPO',
    //             'acc_id' => 'SGAREPRESENTATION',
    //             'model' => BudgetPlan::class,
    //             'template' => 'representation'
    //         ],
    //     ];

    //     $months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    //     $processedRows = 0;
    //     $processedGidRows = 0; // Counter for GID rows
    //     $processedNonGidRows = 0; // Counter for non-GID rows
    //     $processedSheets = [];
    //     $errors = []; // Initialize errors array

    //     // Iterasi setiap sheet di file Excel
    //     foreach ($spreadsheet->getSheetNames() as $sheetName) {
    //         if (!isset($sheetMappings[$sheetName])) {
    //             Log::warning("Sheet '$sheetName' not found in sheetMappings");
    //             continue;
    //         }

    //         $sheetConfig = $sheetMappings[$sheetName];
    //         $prefix = $sheetConfig['prefix'];
    //         $acc_id = $sheetConfig['acc_id'];
    //         $model = $sheetConfig['model'];
    //         $template = $sheetConfig['template'];

    //         Log::info("Checking sheet: $sheetName, template: $template, model: $model");

    //         // Load data dari sheet
    //         $sheet = $spreadsheet->getSheetByName($sheetName);
    //         $data = $sheet->toArray();
    //         Log::info("Sheet '$sheetName' has " . count($data) . " rows");

    //         // Check if sheet has valid data rows (excluding header)
    //         $hasValidData = false;
    //         foreach ($data as $i => $row) {
    //             if ($i === 0) {
    //                 Log::info("Skipping header row for sheet: $sheetName");
    //                 continue; // Skip header
    //             }

    //             // Check if row has any non-empty values
    //             $rowHasData = array_filter($row, function ($value) {
    //                 return !is_null($value) && $value !== '';
    //             });

    //             if (!empty($rowHasData)) {
    //                 $hasValidData = true;
    //                 break; // Found at least one valid data row
    //             }
    //         }

    //         if (!$hasValidData) {
    //             Log::info("Sheet '$sheetName' has no valid data rows, skipping processing");
    //             continue;
    //         }

    //         // Generate sub_id sekali per worksheet dengan data valid
    //         $lastRecord = $model::where('sub_id', 'like', "$prefix%")
    //             ->orderBy('sub_id', 'desc')
    //             ->first();
    //         $nextNumber = $lastRecord ? ((int)str_replace($prefix, '', $lastRecord->sub_id) + 1) : 1;
    //         $sub_id = $prefix . str_pad($nextNumber, 7, '0', STR_PAD_LEFT);
    //         Log::info("Generated sub_id for sheet $sheetName: $sub_id");

    //         // Create approval record for the sub_id
    //         try {
    //             Approval::create([
    //                 'approve_by' => $npk,
    //                 'sub_id' => $sub_id,
    //                 'status' => 1,
    //                 'created_at' => now(),
    //                 'updated_at' => now(),
    //             ]);
    //             Log::info("Created approval record for sub_id: $sub_id, approve_by: $npk");
    //         } catch (\Exception $e) {
    //             Log::error("Failed to create approval record for sub_id: $sub_id, error: " . $e->getMessage());
    //             return response()->json(['message' => 'Failed to create approval record: ' . $e->getMessage()], 500);
    //         }

    //         $processedSheets[] = $sheetName;

    //         // Process data rows
    //         foreach ($data as $i => $row) {
    //             if ($i === 0) {
    //                 Log::info("Skipping header row for sheet: $sheetName");
    //                 continue; // Skip header
    //             }

    //             // Validasi jumlah kolom
    //             $expectedColumns = $this->getExpectedColumns($template, $months);
    //             if (count($row) < $expectedColumns) {
    //                 Log::warning("Invalid column count in row $i of sheet $sheetName: expected at least $expectedColumns, got " . count($row));
    //                 continue;
    //             }

    //             Log::info("Processing row $i in sheet '$sheetName': " . json_encode($row));

    //             try {
    //                 if ($template === 'general') {
    //                     [$no, $itm_id, $description, $quantity, $price, $amount, $wct_id, $dpt_id, $bdc_id] = array_slice($row, 0, 9);

    //                     // Store original itm_id for logging
    //                     $original_itm_id = $itm_id;

    //                     // Check if itm_id is a GID item
    //                     $isGidItem = preg_match('/^[0-9]+[,.]?[0-9]*[A-Z0-9-/\s]*$/i', $itm_id);

    //                     if ($isGidItem) {
    //                         // For GID items: replace comma with dot and enforce uppercase
    //                         $itm_id = str_replace(',', '.', strtoupper(trim($itm_id)));
    //                         Log::info("GID item detected in row $i of sheet $sheetName: Original '$original_itm_id' corrected to '$itm_id'");

    //                         // Validate GID item format
    //                         if (!preg_match('/^[0-9]+[.]?[0-9]*[A-Z0-9-/\s]*$/', $itm_id)) {
    //                             $errors[] = "Invalid GID itm_id format in row $i of sheet $sheetName: '$itm_id' does not match required pattern";
    //                             Log::warning("Invalid GID itm_id format in row $i of sheet $sheetName: '$itm_id' does not match required pattern");
    //                             continue;
    //                         }
    //                     } else {
    //                         // For non-GID items: only trim whitespace
    //                         $itm_id = trim($itm_id);
    //                         Log::info("Non-GID item detected in row $i of sheet $sheetName: '$itm_id'");
    //                     }

    //                     // Validate department
    //                     if ($dpt_id !== $userDept) {
    //                         $errors[] = "Invalid dpt_id in row $i of sheet $sheetName: Expected $userDept, got $dpt_id";
    //                         Log::warning("Invalid dpt_id in row $i of sheet $sheetName: Expected $userDept, got $dpt_id");
    //                         continue;
    //                     }

    //                     // Validate required fields
    //                     $requiredFields = [
    //                         'itm_id' => $itm_id,
    //                         'description' => $description,
    //                         'quantity' => $quantity,
    //                         'price' => $price,
    //                         'amount' => $amount,
    //                         'dpt_id' => $dpt_id,
    //                         'bdc_id' => $bdc_id,
    //                     ];

    //                     foreach ($requiredFields as $fieldName => $value) {
    //                         if (is_null($value) || $value === '' || trim($value) === '') {
    //                             $errors[] = "Invalid $fieldName in row $i of sheet $sheetName: $fieldName is empty or null";
    //                             Log::warning("Invalid $fieldName in row $i of sheet $sheetName: $fieldName is empty or null");
    //                             continue 2;
    //                         }
    //                     }

    //                     // Validate numeric values
    //                     if (!is_numeric($quantity) || !is_numeric($price) || !is_numeric($amount)) {
    //                         $errors[] = "Invalid numeric value in row $i of sheet $sheetName: quantity=$quantity, price=$price, amount=$amount";
    //                         Log::warning("Invalid numeric value in row $i: quantity=$quantity, price=$price, amount=$amount");
    //                         continue;
    //                     }

    //                     Log::info("Processing row $i in sheet $sheetName with itm_id: $itm_id");

    //                     foreach ($months as $index => $month) {
    //                         $value = $row[9 + $index] ?? 0;
    //                         if ($value == 0 || $value === null || trim($value) === '') {
    //                             Log::info("Skipping month $month for row $i: value is $value");
    //                             continue;
    //                         }

    //                         $model::create([
    //                             'sub_id' => $sub_id,
    //                             'purpose' => $purpose,
    //                             'acc_id' => $acc_id,
    //                             'itm_id' => $itm_id,
    //                             'description' => $description,
    //                             'quantity' => $quantity,
    //                             'price' => $price,
    //                             'amount' => $amount,
    //                             'wct_id' => $wct_id,
    //                             'dpt_id' => $dpt_id,
    //                             'bdc_id' => $bdc_id,
    //                             'month' => $month,
    //                             'status' => 1,
    //                         ]);
    //                         Log::info("Created record for sub_id: $sub_id, month: $month, value: $value, itm_id: $itm_id");
    //                         $processedRows++;
    //                         if ($isGidItem) {
    //                             $processedGidRows++;
    //                         } else {
    //                             $processedNonGidRows++;
    //                         }
    //                     }
    //                 } elseif ($template === 'aftersales') {
    //                     [$no, $itm_id, $customer, $quantity, $price, $amount, $wct_id, $dpt_id, $bdc_id] = array_slice($row, 0, 9);

    //                     // Store original itm_id for logging
    //                     $original_itm_id = $itm_id;

    //                     // Check if itm_id is a GID item
    //                     $isGidItem = preg_match('/^[0-9]+[,.]?[0-9]*[A-Z0-9-/\s]*$/i', $itm_id);

    //                     if ($isGidItem) {
    //                         $itm_id = str_replace(',', '.', strtoupper(trim($itm_id)));
    //                         Log::info("GID item detected in row $i of sheet $sheetName: Original '$original_itm_id' corrected to '$itm_id'");
    //                         if (!preg_match('/^[0-9]+[.]?[0-9]*[A-Z0-9-/\s]*$/', $itm_id)) {
    //                             $errors[] = "Invalid GID itm_id format in row $i of sheet $sheetName: '$itm_id' does not match required pattern";
    //                             Log::warning("Invalid GID itm_id format in row $i of sheet $sheetName: '$itm_id' does not match required pattern");
    //                             continue;
    //                         }
    //                     } else {
    //                         $itm_id = trim($itm_id);
    //                         Log::info("Non-GID item detected in row $i of sheet $sheetName: '$itm_id'");
    //                     }

    //                     if ($dpt_id !== $userDept) {
    //                         $errors[] = "Invalid dpt_id in row $i of sheet $sheetName: Expected $userDept, got $dpt_id";
    //                         Log::warning("Invalid dpt_id in row $i of sheet $sheetName: Expected $userDept, got $dpt_id");
    //                         continue;
    //                     }

    //                     $requiredFields = [
    //                         'itm_id' => $itm_id,
    //                         'customer' => $customer,
    //                         'quantity' => $quantity,
    //                         'price' => $price,
    //                         'amount' => $amount,
    //                         'dpt_id' => $dpt_id,
    //                         'bdc_id' => $bdc_id,
    //                     ];

    //                     foreach ($requiredFields as $fieldName => $value) {
    //                         if (is_null($value) || $value === '' || trim($value) === '') {
    //                             $errors[] = "Invalid $fieldName in row $i of sheet $sheetName: $fieldName is empty or null";
    //                             Log::warning("Invalid $fieldName in row $i of sheet $sheetName: $fieldName is empty or null");
    //                             continue 2;
    //                         }
    //                     }

    //                     if (!is_numeric($quantity) || !is_numeric($price) || !is_numeric($amount)) {
    //                         $errors[] = "Invalid numeric value in row $i of sheet $sheetName: quantity=$quantity, price=$price, amount=$amount";
    //                         Log::warning("Invalid numeric value in row $i: quantity=$quantity, price=$price, amount=$amount");
    //                         continue;
    //                     }

    //                     Log::info("Processing row $i in sheet $sheetName with itm_id: $itm_id");

    //                     foreach ($months as $index => $month) {
    //                         $value = $row[9 + $index] ?? 0;
    //                         if ($value == 0 || $value === null || trim($value) === '') {
    //                             Log::info("Skipping month $month for row $i: value is $value");
    //                             continue;
    //                         }
    //                         $model::create([
    //                             'sub_id' => $sub_id,
    //                             'purpose' => $purpose,
    //                             'acc_id' => $acc_id,
    //                             'itm_id' => $itm_id,
    //                             'customer' => $customer,
    //                             'quantity' => $quantity,
    //                             'price' => $price,
    //                             'amount' => $amount,
    //                             'wct_id' => $wct_id,
    //                             'dpt_id' => $dpt_id,
    //                             'bdc_id' => $bdc_id,
    //                             'month' => $month,
    //                             'status' => 1,
    //                         ]);
    //                         Log::info("Created record for sub_id: $sub_id, month: $month, value: $value, itm_id: $itm_id");
    //                         $processedRows++;
    //                         if ($isGidItem) {
    //                             $processedGidRows++;
    //                         } else {
    //                             $processedNonGidRows++;
    //                         }
    //                     }
    //                 } elseif ($template === 'support') {
    //                     [$no, $itm_id, $description, $unit, $quantity, $price, $amount, $wct_id, $dpt_id, $bdc_id, $lob_id] = array_slice($row, 0, 11);

    //                     // Store original itm_id for logging
    //                     $original_itm_id = $itm_id;

    //                     // Check if itm_id is a GID item
    //                     $isGidItem = preg_match('/^[0-9]+[,.]?[0-9]*[A-Z0-9-/\s]*$/i', $itm_id);

    //                     if ($isGidItem) {
    //                         $itm_id = str_replace(',', '.', strtoupper(trim($itm_id)));
    //                         Log::info("GID item detected in row $i of sheet $sheetName: Original '$original_itm_id' corrected to '$itm_id'");
    //                         if (!preg_match('/^[0-9]+[.]?[0-9]*[A-Z0-9-/\s]*$/', $itm_id)) {
    //                             $errors[] = "Invalid GID itm_id format in row $i of sheet $sheetName: '$itm_id' does not match required pattern";
    //                             Log::warning("Invalid GID itm_id format in row $i of sheet $sheetName: '$itm_id' does not match required pattern");
    //                             continue;
    //                         }
    //                     } else {
    //                         $itm_id = trim($itm_id);
    //                         Log::info("Non-GID item detected in row $i of sheet $sheetName: '$itm_id'");
    //                     }

    //                     if ($dpt_id !== $userDept) {
    //                         $errors[] = "Invalid dpt_id in row $i of sheet $sheetName: Expected $userDept, got $dpt_id";
    //                         Log::warning("Invalid dpt_id in row $i of sheet $sheetName: Expected $userDept, got $dpt_id");
    //                         continue;
    //                     }

    //                     $requiredFields = [
    //                         'itm_id' => $itm_id,
    //                         'description' => $description,
    //                         'unit' => $unit,
    //                         'quantity' => $quantity,
    //                         'price' => $price,
    //                         'amount' => $amount,
    //                         'dpt_id' => $dpt_id,
    //                         'bdc_id' => $bdc_id,
    //                         'lob_id' => $lob_id,
    //                     ];

    //                     foreach ($requiredFields as $fieldName => $value) {
    //                         if (is_null($value) || $value === '' || trim($value) === '') {
    //                             $errors[] = "Invalid $fieldName in row $i of sheet $sheetName: $fieldName is empty or null";
    //                             Log::warning("Invalid $fieldName in row $i of sheet $sheetName: $fieldName is empty or null");
    //                             continue 2;
    //                         }
    //                     }

    //                     if (!is_numeric($quantity) || !is_numeric($price) || !is_numeric($amount)) {
    //                         $errors[] = "Invalid numeric value in row $i of sheet $sheetName: quantity=$quantity, price=$price, amount=$amount";
    //                         Log::warning("Invalid numeric value in row $i: quantity=$quantity, price=$price, amount=$amount");
    //                         continue;
    //                     }

    //                     Log::info("Processing row $i in sheet $sheetName with itm_id: $itm_id");

    //                     foreach ($months as $index => $month) {
    //                         $value = $row[9 + $index] ?? 0;
    //                         if ($value == 0 || $value === null || trim($value) === '') {
    //                             Log::info("Skipping month $month for row $i: value is $value");
    //                             continue;
    //                         }

    //                         $model::create([
    //                             'sub_id' => $sub_id,
    //                             'purpose' => $purpose,
    //                             'acc_id' => $acc_id,
    //                             'itm_id' => $itm_id,
    //                             'description' => $description,
    //                             'unit' => $unit,
    //                             'quantity' => $quantity,
    //                             'price' => $price,
    //                             'amount' => $amount,
    //                             'wct_id' => $wct_id,
    //                             'dpt_id' => $dpt_id,
    //                             'bdc_id' => $bdc_id,
    //                             'lob_id' => $lob_id,
    //                             'month' => $month,
    //                             'status' => 1,
    //                         ]);
    //                         Log::info("Created record for sub_id: $sub_id, month: $month, value: $value, itm_id: $itm_id");
    //                         $processedRows++;
    //                         if ($isGidItem) {
    //                             $processedGidRows++;
    //                         } else {
    //                             $processedNonGidRows++;
    //                         }
    //                     }
    //                 } elseif ($template === 'insurance') {
    //                     [$no, $description, $ins_id, $quantity, $price, $amount, $wct_id, $dpt_id] = array_slice($row, 0, 8);

    //                     if ($dpt_id !== $userDept) {
    //                         $errors[] = "Invalid dpt_id in row $i of sheet $sheetName: Expected $userDept, got $dpt_id";
    //                         Log::warning("Invalid dpt_id in row $i of sheet $sheetName: Expected $userDept, got $dpt_id");
    //                         continue;
    //                     }

    //                     $requiredFields = [
    //                         'description' => $description,
    //                         'ins_id' => $ins_id,
    //                         'quantity' => $quantity,
    //                         'price' => $price,
    //                         'amount' => $amount,
    //                         'dpt_id' => $dpt_id,
    //                     ];

    //                     foreach ($requiredFields as $fieldName => $value) {
    //                         if (is_null($value) || $value === '' || trim($value) === '') {
    //                             $errors[] = "Invalid $fieldName in row $i of sheet $sheetName: $fieldName is empty or null";
    //                             Log::warning("Invalid $fieldName in row $i of sheet $sheetName: $fieldName is empty or null");
    //                             continue 2;
    //                         }
    //                     }

    //                     if (!is_numeric($quantity) || !is_numeric($price) || !is_numeric($amount)) {
    //                         $errors[] = "Invalid numeric value in row $i of sheet $sheetName: quantity=$quantity, price=$price, amount=$amount";
    //                         Log::warning("Invalid numeric value in row $i: quantity=$quantity, price=$price, amount=$amount");
    //                         continue;
    //                     }

    //                     foreach ($months as $index => $month) {
    //                         $value = $row[9 + $index] ?? 0;
    //                         if ($value == 0 || $value === null || trim($value) === '') {
    //                             Log::info("Skipping month $month for row $i: value is $value");
    //                             continue;
    //                         }
    //                         $model::create([
    //                             'sub_id' => $sub_id,
    //                             'purpose' => $purpose,
    //                             'acc_id' => $acc_id,
    //                             'description' => $description,
    //                             'ins_id' => $ins_id,
    //                             'quantity' => $quantity,
    //                             'price' => $price,
    //                             'amount' => $amount,
    //                             'wct_id' => $wct_id,
    //                             'dpt_id' => $dpt_id,
    //                             'month' => $month,
    //                             'status' => 1,
    //                         ]);
    //                         Log::info("Created record for sub_id: $sub_id, month: $month, value: $value, itm_id: $itm_id");
    //                         $processedRows++;
    //                     }
    //                 } elseif ($template === 'utilities') {
    //                     [$no, $itm_id, $kwh, $quantity, $price, $amount, $wct_id, $dpt_id, $bdc_id] = array_slice($row, 0, 9);

    //                     // Store original itm_id for logging
    //                     $original_itm_id = $itm_id;

    //                     // Check if itm_id is a GID item
    //                     $isGidItem = preg_match('/^[0-9]+[,.]?[0-9]*[A-Z0-9-/\s]*$/i', $itm_id);

    //                     if ($isGidItem) {
    //                         $itm_id = str_replace(',', '.', strtoupper(trim($itm_id)));
    //                         Log::info("GID item detected in row $i of sheet $sheetName: Original '$original_itm_id' corrected to '$itm_id'");
    //                         if (!preg_match('/^[0-9]+[.]?[0-9]*[A-Z0-9-/\s]*$/', $itm_id)) {
    //                             $errors[] = "Invalid GID itm_id format in row $i of sheet $sheetName: '$itm_id' does not match required pattern";
    //                             Log::warning("Invalid GID itm_id format in row $i of sheet $sheetName: '$itm_id' does not match required pattern");
    //                             continue;
    //                         }
    //                     } else {
    //                         $itm_id = trim($itm_id);
    //                         Log::info("Non-GID item detected in row $i of sheet $sheetName: '$itm_id'");
    //                     }

    //                     if ($dpt_id !== $userDept) {
    //                         $errors[] = "Invalid dpt_id in row $i of sheet $sheetName: Expected $userDept, got $dpt_id";
    //                         Log::warning("Invalid dpt_id in row $i of sheet $sheetName: Expected $userDept, got $dpt_id");
    //                         continue;
    //                     }

    //                     $requiredFields = [
    //                         'itm_id' => $itm_id,
    //                         'kwh' => $kwh,
    //                         'quantity' => $quantity,
    //                         'price' => $price,
    //                         'amount' => $amount,
    //                         'dpt_id' => $dpt_id,
    //                         'bdc_id' => $bdc_id,
    //                     ];

    //                     foreach ($requiredFields as $fieldName => $value) {
    //                         if (is_null($value) || $value === '' || trim($value) === '') {
    //                             $errors[] = "Invalid $fieldName in row $i of sheet $sheetName: $fieldName is empty or null";
    //                             Log::warning("Invalid $fieldName in row $i of sheet $sheetName: $fieldName is empty or null");
    //                             continue 2;
    //                         }
    //                     }

    //                     if (!is_numeric($quantity) || !is_numeric($price) || !is_numeric($amount)) {
    //                         $errors[] = "Invalid numeric value in row $i of sheet $sheetName: quantity=$quantity, price=$price, amount=$amount";
    //                         Log::warning("Invalid numeric value in row $i: quantity=$quantity, price=$price, amount=$amount");
    //                         continue;
    //                     }

    //                     Log::info("Processing row $i in sheet $sheetName with itm_id: $itm_id");

    //                     foreach ($months as $index => $month) {
    //                         $value = $row[9 + $index] ?? 0;
    //                         if ($value == 0 || $value === null || trim($value) === '') {
    //                             Log::info("Skipping month $month for row $i: value is $value");
    //                             continue;
    //                         }
    //                         $model::create([
    //                             'sub_id' => $sub_id,
    //                             'purpose' => $purpose,
    //                             'acc_id' => $acc_id,
    //                             'itm_id' => $itm_id,
    //                             'kwh' => $kwh,
    //                             'quantity' => $quantity,
    //                             'price' => $price,
    //                             'amount' => $amount,
    //                             'wct_id' => $wct_id,
    //                             'dpt_id' => $dpt_id,
    //                             'bdc_id' => $bdc_id,
    //                             'month' => $month,
    //                             'status' => 1,
    //                         ]);
    //                         Log::info("Created record for sub_id: $sub_id, month: $month, value: $value, itm_id: $itm_id");
    //                         $processedRows++;
    //                         if ($isGidItem) {
    //                             $processedGidRows++;
    //                         } else {
    //                             $processedNonGidRows++;
    //                         }
    //                     }
    //                 } elseif ($template === 'business') {
    //                     [$no, $itm_id, $description, $days, $quantity, $price, $amount, $wct_id, $dpt_id, $bdc_id] = array_slice($row, 0, 10);

    //                     // Store original itm_id for logging
    //                     $original_itm_id = $itm_id;

    //                     // Check if itm_id is a GID item
    //                     $isGidItem = preg_match('/^[0-9]+[,.]?[0-9]*[A-Z0-9-/\s]*$/i', $itm_id);

    //                     if ($isGidItem) {
    //                         $itm_id = str_replace(',', '.', strtoupper(trim($itm_id)));
    //                         Log::info("GID item detected in row $i of sheet $sheetName: Original '$original_itm_id' corrected to '$itm_id'");
    //                         if (!preg_match('/^[0-9]+[.]?[0-9]*[A-Z0-9-/\s]*$/', $itm_id)) {
    //                             $errors[] = "Invalid GID itm_id format in row $i of sheet $sheetName: '$itm_id' does not match required pattern";
    //                             Log::warning("Invalid GID itm_id format in row $i of sheet $sheetName: '$itm_id' does not match required pattern");
    //                             continue;
    //                         }
    //                     } else {
    //                         $itm_id = trim($itm_id);
    //                         Log::info("Non-GID item detected in row $i of sheet $sheetName: '$itm_id'");
    //                     }

    //                     if ($dpt_id !== $userDept) {
    //                         $errors[] = "Invalid dpt_id in row $i of sheet $sheetName: Expected $userDept, got $dpt_id";
    //                         Log::warning("Invalid dpt_id in row $i of sheet $sheetName: Expected $userDept, got $dpt_id");
    //                         continue;
    //                     }

    //                     $requiredFields = [
    //                         'itm_id' => $itm_id,
    //                         'description' => $description,
    //                         'days' => $days,
    //                         'quantity' => $quantity,
    //                         'price' => $price,
    //                         'amount' => $amount,
    //                         'dpt_id' => $dpt_id,
    //                         'bdc_id' => $bdc_id,
    //                     ];

    //                     foreach ($requiredFields as $fieldName => $value) {
    //                         if (is_null($value) || $value === '' || trim($value) === '') {
    //                             $errors[] = "Invalid $fieldName in row $i of sheet $sheetName: $fieldName is empty or null";
    //                             Log::warning("Invalid $fieldName in row $i of sheet $sheetName: $fieldName is empty or null");
    //                             continue 2;
    //                         }
    //                     }

    //                     if (!is_numeric($quantity) || !is_numeric($price) || !is_numeric($amount)) {
    //                         $errors[] = "Invalid numeric value in row $i of sheet $sheetName: quantity=$quantity, price=$price, amount=$amount";
    //                         Log::warning("Invalid numeric value in row $i: quantity=$quantity, price=$price, amount=$amount");
    //                         continue;
    //                     }

    //                     Log::info("Processing row $i in sheet $sheetName with itm_id: $itm_id");

    //                     foreach ($months as $index => $month) {
    //                         $value = $row[9 + $index] ?? 0;
    //                         if ($value == 0 || $value === null || trim($value) === '') {
    //                             Log::info("Skipping month $month for row $i: value is $value");
    //                             continue;
    //                         }
    //                         $model::create([
    //                             'sub_id' => $sub_id,
    //                             'purpose' => $purpose,
    //                             'acc_id' => $acc_id,
    //                             'itm_id' => $itm_id,
    //                             'description' => $description,
    //                             'days' => $days,
    //                             'quantity' => $quantity,
    //                             'price' => $price,
    //                             'amount' => $amount,
    //                             'wct_id' => $wct_id,
    //                             'dpt_id' => $dpt_id,
    //                             'bdc_id' => $bdc_id,
    //                             'month' => $month,
    //                             'status' => 1,
    //                         ]);
    //                         Log::info("Created record for sub_id: $sub_id, month: $month, value: $value, itm_id: $itm_id");
    //                         $processedRows++;
    //                         if ($isGidItem) {
    //                             $processedGidRows++;
    //                         } else {
    //                             $processedNonGidRows++;
    //                         }
    //                     }
    //                 } elseif ($template === 'representation') {
    //                     [$no, $itm_id, $description, $beneficiary, $quantity, $price, $amount, $wct_id, $dpt_id, $bdc_id] = array_slice($row, 0, 10);

    //                     // Store original itm_id for logging
    //                     $original_itm_id = $itm_id;

    //                     // Check if itm_id is a GID item
    //                     $isGidItem = preg_match('/^[0-9]+[,.]?[0-9]*[A-Z0-9-/\s]*$/i', $itm_id);

    //                     if ($isGidItem) {
    //                         $itm_id = str_replace(',', '.', strtoupper(trim($itm_id)));
    //                         Log::info("GID item detected in row $i of sheet $sheetName: Original '$original_itm_id' corrected to '$itm_id'");
    //                         if (!preg_match('/^[0-9]+[.]?[0-9]*[A-Z0-9-/\s]*$/', $itm_id)) {
    //                             $errors[] = "Invalid GID itm_id format in row $i of sheet $sheetName: '$itm_id' does not match required pattern";
    //                             Log::warning("Invalid GID itm_id format in row $i of sheet $sheetName: '$itm_id' does not match required pattern");
    //                             continue;
    //                         }
    //                     } else {
    //                         $itm_id = trim($itm_id);
    //                         Log::info("Non-GID item detected in row $i of sheet $sheetName: '$itm_id'");
    //                     }

    //                     if ($dpt_id !== $userDept) {
    //                         $errors[] = "Invalid dpt_id in row $i of sheet $sheetName: Expected $userDept, got $dpt_id";
    //                         Log::warning("Invalid dpt_id in row $i of sheet $sheetName: Expected $userDept, got $dpt_id");
    //                         continue;
    //                     }

    //                     $requiredFields = [
    //                         'itm_id' => $itm_id,
    //                         'description' => $description,
    //                         'beneficiary' => $beneficiary,
    //                         'quantity' => $quantity,
    //                         'price' => $price,
    //                         'amount' => $amount,
    //                         'dpt_id' => $dpt_id,
    //                         'bdc_id' => $bdc_id,
    //                     ];

    //                     foreach ($requiredFields as $fieldName => $value) {
    //                         if (is_null($value) || $value === '' || trim($value) === '') {
    //                             $errors[] = "Invalid $fieldName in row $i of sheet $sheetName: $fieldName is empty or null";
    //                             Log::warning("Invalid $fieldName in row $i of sheet $sheetName: $fieldName is empty or null");
    //                             continue 2;
    //                         }
    //                     }

    //                     if (!is_numeric($quantity) || !is_numeric($price) || !is_numeric($amount)) {
    //                         $errors[] = "Invalid numeric value in row $i of sheet $sheetName: quantity=$quantity, price=$price, amount=$amount";
    //                         Log::warning("Invalid numeric value in row $i: quantity=$quantity, price=$price, amount=$amount");
    //                         continue;
    //                     }

    //                     Log::info("Processing row $i in sheet $sheetName with itm_id: $itm_id");

    //                     foreach ($months as $index => $month) {
    //                         $value = $row[9 + $index] ?? 0;
    //                         if ($value == 0 || $value === null || trim($value) === '') {
    //                             Log::info("Skipping month $month for row $i: value is $value");
    //                             continue;
    //                         }
    //                         $model::create([
    //                             'sub_id' => $sub_id,
    //                             'purpose' => $purpose,
    //                             'acc_id' => $acc_id,
    //                             'itm_id' => $itm_id,
    //                             'description' => $description,
    //                             'beneficiary' => $beneficiary,
    //                             'quantity' => $quantity,
    //                             'price' => $price,
    //                             'amount' => $amount,
    //                             'wct_id' => $wct_id,
    //                             'dpt_id' => $dpt_id,
    //                             'bdc_id' => $bdc_id,
    //                             'month' => $month,
    //                             'status' => 1,
    //                         ]);
    //                         Log::info("Created record for sub_id: $sub_id, month: $month, value: $value, itm_id: $itm_id");
    //                         $processedRows++;
    //                         if ($isGidItem) {
    //                             $processedGidRows++;
    //                         } else {
    //                             $processedNonGidRows++;
    //                         }
    //                     }
    //                 } elseif ($template === 'training') {
    //                     [$no, $participant, $jenis_training, $quantity, $price, $amount, $wct_id, $dpt_id, $bdc_id] = array_slice($row, 0, 9);

    //                     if ($dpt_id !== $userDept) {
    //                         $errors[] = "Invalid dpt_id in row $i of sheet $sheetName: Expected $userDept, got $dpt_id";
    //                         Log::warning("Invalid dpt_id in row $i of sheet $sheetName: Expected $userDept, got $dpt_id");
    //                         continue;
    //                     }

    //                     $requiredFields = [
    //                         'participant' => $participant,
    //                         'jenis_training' => $jenis_training,
    //                         'quantity' => $quantity,
    //                         'price' => $price,
    //                         'amount' => $amount,
    //                         'dpt_id' => $dpt_id,
    //                         'bdc_id' => $bdc_id,
    //                     ];

    //                     foreach ($requiredFields as $fieldName => $value) {
    //                         if (is_null($value) || $value === '' || trim($value) === '') {
    //                             $errors[] = "Invalid $fieldName in row $i of sheet $sheetName: $fieldName is empty or null";
    //                             Log::warning("Invalid $fieldName in row $i of sheet $sheetName: $fieldName is empty or null");
    //                             continue 2;
    //                         }
    //                     }

    //                     if (!is_numeric($quantity) || !is_numeric($price) || !is_numeric($amount)) {
    //                         $errors[] = "Invalid numeric value in row $i of sheet $sheetName: quantity=$quantity, price=$price, amount=$amount";
    //                         Log::warning("Invalid numeric value in row $i: quantity=$quantity, price=$price, amount=$amount");
    //                         continue;
    //                     }

    //                     foreach ($months as $index => $month) {
    //                         $value = $row[9 + $index] ?? 0;
    //                         if ($value == 0 || $value === null || trim($value) === '') {
    //                             Log::info("Skipping month $month for row $i: value is $value");
    //                             continue;
    //                         }
    //                         $model::create([
    //                             'sub_id' => $sub_id,
    //                             'purpose' => $purpose,
    //                             'acc_id' => $acc_id,
    //                             'participant' => $participant,
    //                             'jenis_training' => $jenis_training,
    //                             'quantity' => $quantity,
    //                             'price' => $price,
    //                             'amount' => $amount,
    //                             'wct_id' => $wct_id,
    //                             'dpt_id' => $dpt_id,
    //                             'bdc_id' => $bdc_id,
    //                             'month' => $month,
    //                             'status' => 1,
    //                         ]);
    //                         Log::info("Created record for sub_id: $sub_id, month: $month, value: $value, itm_id: $itm_id");
    //                         $processedRows++;
    //                     }
    //                 }
    //             } catch (\Exception $e) {
    //                 Log::error("Failed to create record for sub_id: $sub_id, month: $month, sheet: $sheetName, error: " . $e->getMessage());
    //                 $errors[] = "Failed to create record in row $i of sheet $sheetName: " . $e->getMessage();
    //             }
    //         }
    //     }

    //     // Respons berdasarkan hasil pemrosesan
    //     if ($processedRows === 0) {
    //         Log::warning('No rows were processed', ['sheets_processed' => $processedSheets]);
    //         return response()->json([
    //             'message' => 'No data was processed. Please check the file content or sheet names.',
    //             'sheets_processed' => $processedSheets,
    //             'processed_rows' => $processedRows,
    //             'processed_gid_rows' => $processedGidRows,
    //             'processed_non_gid_rows' => $processedNonGidRows,
    //             'errors' => $errors
    //         ], 400);
    //     }

    //     Log::info('Upload completed successfully', [
    //         'sheets_processed' => $processedSheets,
    //         'processed_rows' => $processedRows,
    //         'processed_gid_rows' => $processedGidRows,
    //         'processed_non_gid_rows' => $processedNonGidRows
    //     ]);

    //     return response()->json([
    //         'message' => empty($errors) ? 'Data uploaded successfully.' : 'Data uploaded with errors.',
    //         'sheets_processed' => $processedSheets,
    //         'processed_rows' => $processedRows,
    //         'processed_gid_rows' => $processedGidRows,
    //         'processed_non_gid_rows' => $processedNonGidRows,
    //         'errors' => $errors
    //     ], empty($errors) ? 200 : 207);
    // }

     public function upload(Request $request)
    {

                $monthNumberToName = [
                    1 => 'January',
                    2 => 'February',
                    3 => 'March',
                    4 => 'April',
                    5 => 'May',
                    6 => 'June',
                    7 => 'July',
                    8 => 'August',
                    9 => 'September',
                    10 => 'October',
                    11 => 'November',
                    12 => 'December'
                ];


        $errors = [];
        // Log request masuk
        Log::info('Upload request received', [
            'template' => $request->file('file') ? $request->file('file')->getClientOriginalName() : 'No file',
            'purpose' => $request->input('purpose'),
            'timestamp' => now()->toDateTimeString(),
            'upload_type' => 'required|in:asset'

        ]);

        // Validasi input
        $request->validate([
            'template' => 'required|file|mimes:xlsx,xls',
            'purpose' => 'required|string'
        ]);

        // Check if user is authenticated
        if (!Auth::check()) {
            Log::error('User not authenticated');
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        $file = $request->file('template');
        $purpose = $request->input('purpose');
        $npk = Auth::user()->npk; // Get NPK of logged-in user
        $userDept = Auth::user()->dept; // Get NPK of logged-in user

        // Load file Excel
        try {
            $spreadsheet = IOFactory::load($file);
            Log::info('Excel file loaded successfully', ['sheets' => $spreadsheet->getSheetNames()]);
        } catch (\Exception $e) {
            Log::error('Failed to load Excel file', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to load Excel file: ' . $e->getMessage()], 500);
        }

        // Mapping sheet ke prefix, acc_id, model, dan template
        $sheetMappings = [
            'ADVERTISING & PROMOTION' => [
                'prefix' => 'ADP',
                'acc_id' => 'SGAADVERT',
                'model' => BudgetPlan::class,
                'template' => 'general'
            ],
            'COMMUNICATION' => [
                'prefix' => 'COM',
                'acc_id' => 'SGACOM',
                'model' => BudgetPlan::class,
                'template' => 'general'
            ],
            'OFFICE SUPPLY' => [
                'prefix' => 'OFS',
                'acc_id' => 'SGAOFFICESUP',
                'model' => BudgetPlan::class,
                'template' => 'general'
            ],
            'AFTER SALES SERVICE' => [
                'prefix' => 'AFS',
                'acc_id' => 'SGAAFTERSALES',
                'model' => BudgetPlan::class,
                'template' => 'aftersales'
            ],
            'INDIRECT MATERIAL' => [
                'prefix' => 'IDM',
                'acc_id' => 'FOHINDMAT',
                'model' => BudgetPlan::class,
                'template' => 'support'
            ],
            'FACTORY SUPPLY' => [
                'prefix' => 'FSU',
                'acc_id' => 'FOHFS',
                'model' => BudgetPlan::class,
                'template' => 'support'
            ],
            'REPAIR & MAINTENANCE FOH' => [
                'prefix' => 'RPMF',
                'acc_id' => 'FOHREPAIR',
                'model' => BudgetPlan::class,
                'template' => 'support'
            ],
            'CONS TOOLS' => [
                'prefix' => 'CTL',
                'acc_id' => 'FOHTOOLS',
                'model' => BudgetPlan::class,
                'template' => 'support'
            ],
            'INSURANCE PREM FOH' => [
                'prefix' => 'INSF',
                'acc_id' => 'FOHINSPREM',
                'model' => BudgetPlan::class,
                'template' => 'insurance'
            ],
            'INSURANCE PREM OPEX' => [
                'prefix' => 'INSO',
                'acc_id' => 'SGAINSURANCE',
                'model' => BudgetPlan::class,
                'template' => 'insurance'
            ],
            'TAX & PUBLIC DUES FOH' => [
                'prefix' => 'TAXF',
                'acc_id' => 'FOHTAXPUB',
                'model' => BudgetPlan::class,
                'template' => 'general'
            ],
            'TAX & PUBLIC DUES OPEX' => [
                'prefix' => 'TAXO',
                'acc_id' => 'SGATAXPUB',
                'model' => BudgetPlan::class,
                'template' => 'general'
            ],
            'PROFESIONAL FEE FOH' => [
                'prefix' => 'PRFF',
                'acc_id' => 'FOHPROF',
                'model' => BudgetPlan::class,
                'template' => 'general'
            ],
            'PROFESIONAL FEE OPEX' => [
                'prefix' => 'PRFO',
                'acc_id' => 'SGAPROF',
                'model' => BudgetPlan::class,
                'template' => 'general'
            ],
            'AUTOMOBILE FOH' => [
                'prefix' => 'AUTF',
                'acc_id' => 'FOHAUTOMOBILE',
                'model' => BudgetPlan::class,
                'template' => 'general'
            ],
            'AUTOMOBILE OPEX' => [
                'prefix' => 'AUTO',
                'acc_id' => 'SGAAUTOMOBILE',
                'model' => BudgetPlan::class,
                'template' => 'general'
            ],
            'RENT EXPENSE FOH' => [
                'prefix' => 'REXF',
                'acc_id' => 'FOHRENT',
                'model' => BudgetPlan::class,
                'template' => 'general'
            ],
            'PACKING & DELIVERY' => [
                'prefix' => 'PKD',
                'acc_id' => 'FOHPACKING',
                'model' => BudgetPlan::class,
                'template' => 'general'
            ],
            'BANK CHARGES' => [
                'prefix' => 'BKC',
                'acc_id' => 'SGABCHARGES',
                'model' => BudgetPlan::class,
                'template' => 'general'
            ],
            'ROYALTY' => [
                'prefix' => 'RYL',
                'acc_id' => 'SGARYLT',
                'model' => BudgetPlan::class,
                'template' => 'general'
            ],
            'CONTRIBUTION' => [
                'prefix' => 'CTR',
                'acc_id' => 'SGACONTRIBUTION',
                'model' => BudgetPlan::class,
                'template' => 'general'
            ],
            'ASSOCIATION' => [
                'prefix' => 'ASC',
                'acc_id' => 'SGAASSOCIATION',
                'model' => BudgetPlan::class,
                'template' => 'general'
            ],
            'UTILITIES FOH' => [
                'prefix' => 'UTLF',
                'acc_id' => 'FOHPOWER',
                'model' => BudgetPlan::class,
                'template' => 'utilities'
            ],
            'UTILITIES OPEX' => [
                'prefix' => 'UTLO',
                'acc_id' => 'SGAPOWER',
                'model' => BudgetPlan::class,
                'template' => 'utilities'
            ],
            'BUSINESS DUTY FOH' => [
                'prefix' => 'BSDF',
                'acc_id' => 'FOHTRAV',
                'model' => BudgetPlan::class,
                'template' => 'business'
            ],
            'BUSINESS DUTY OPEX' => [
                'prefix' => 'BSDO',
                'acc_id' => 'SGATRAV',
                'model' => BudgetPlan::class,
                'template' => 'business'
            ],
            'TRAINING & EDUCATION FOH' => [
                'prefix' => 'TEDF',
                'acc_id' => 'FOHTRAINING',
                'model' => BudgetPlan::class,
                'template' => 'training'
            ],
            'TRAINING & EDUCATION OPEX' => [
                'prefix' => 'TEDO',
                'acc_id' => 'SGATRAINING',
                'model' => BudgetPlan::class,
                'template' => 'training'
            ],
            'TECHNICAL DEVELOPMENT FOH' => [
                'prefix' => 'TCD',
                'acc_id' => 'FOHTECHDO',
                'model' => BudgetPlan::class,
                'template' => 'general'
            ],
            'RECRUITMENT FOH' => [
                'prefix' => 'RECF',
                'acc_id' => 'FOHRECRUITING',
                'model' => BudgetPlan::class,
                'template' => 'recruitment'
            ],
            'RECRUITMENT OPEX' => [
                'prefix' => 'RECO',
                'acc_id' => 'SGARECRUITING',
                'model' => BudgetPlan::class,
                'template' => 'recruitment'
            ],
            'RENT EXPENSE OPEX' => [
                'prefix' => 'REXO',
                'acc_id' => 'SGARENT',
                'model' => BudgetPlan::class,
                'template' => 'general'
            ],
            'MARKETING ACTIVITY' => [
                'prefix' => 'MKT',
                'acc_id' => 'SGAMARKT',
                'model' => BudgetPlan::class,
                'template' => 'general'
            ],
            'REPAIR & MAINTENANCE OPEX' => [
                'prefix' => 'RPMO',
                'acc_id' => 'SGAREPAIR',
                'model' => BudgetPlan::class,
                'template' => 'general'
            ],
            'BOOK NEWSPAPER' => [
                'prefix' => 'BKN',
                'acc_id' => 'SGABOOK',
                'model' => BudgetPlan::class,
                'template' => 'general'
            ],
            'ENTERTAINMENT FOH' => [
                'prefix' => 'ENTF',
                'acc_id' => 'FOHENTERTAINT',
                'model' => BudgetPlan::class,
                'template' => 'representation'
            ],
            'ENTERTAINMENT OPEX' => [
                'prefix' => 'ENTO',
                'acc_id' => 'SGAENTERTAINT',
                'model' => BudgetPlan::class,
                'template' => 'representation'
            ],
            'REPRESENTATION FOH' => [
                'prefix' => 'REPF',
                'acc_id' => 'FOHREPRESENTATION',
                'model' => BudgetPlan::class,
                'template' => 'representation'
            ],
            'REPRESENTATION OPEX' => [
                'prefix' => 'REPO',
                'acc_id' => 'SGAREPRESENTATION',
                'model' => BudgetPlan::class,
                'template' => 'representation'
            ],
            'OUTSOURCING FEE' => [
                'prefix' => 'OSF',
                'acc_id' => 'SGAOUTSOURCING',
                'model' => BudgetPlan::class,
                'template' => 'general'
            ],
            'EMPLOYEE COMP' => [
                'prefix_map' => [ // Ganti 'prefix' dengan 'prefix_map'
                    'EMPLOYEE COMPENSATION' => 'EMC',
                    'EMPLOYEE COMPENSATION DIRECT LABOR' => 'EDL',
                    'EMPLOYEE COMPENSATION INDIRECT LABOR' => 'EIL'
                ],
                'acc_id_map' => [ // Ganti 'acc_id' dengan 'acc_id_map'
                    'EMPLOYEE COMPENSATION' => 'SGAEMPLOYCOMP',
                    'EMPLOYEE COMPENSATION DIRECT LABOR' => 'FOHEMPLOYCOMPDL',
                    'EMPLOYEE COMPENSATION INDIRECT LABOR' => 'FOHEMPLOYCOMPIL'
                ],
                'model' => BudgetPlan::class,
                'template' => 'employee'
            ],
            'PURCHASE MATERIAL' => [
                'prefix' => 'PRM',
                'acc_id' => 'PURCHASEMATERIAL',
                'model' => BudgetPlan::class,
                'template' => 'purchase'
            ],
        ];

        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $processedRows = 0;
        $processedSheets = [];

        // Iterasi setiap sheet di file Excel
        foreach ($spreadsheet->getSheetNames() as $sheetName) {
            if (!isset($sheetMappings[$sheetName])) {
                Log::warning("Sheet '$sheetName' not found in sheetMappings");
                continue;
            }

            $sheetConfig = $sheetMappings[$sheetName];

            // PERBAIKAN: Handle kedua tipe config (lama dan baru)
            if (isset($sheetConfig['prefix_map'])) {
                // Config baru (employee comp dengan multiple prefix)
                $prefixMap = $sheetConfig['prefix_map'];
                $accIdMap = $sheetConfig['acc_id_map'];
                $isMultiPrefix = true;
            } else {
                // Config lama (single prefix)
                $prefix = $sheetConfig['prefix'];
                $acc_id = $sheetConfig['acc_id'];
                $isMultiPrefix = false;
            }

            $model = $sheetConfig['model'];
            $template = $sheetConfig['template'];

            Log::info("Checking sheet: $sheetName, template: $template, model: $model");

            // Load data dari sheet
            $sheet = $spreadsheet->getSheetByName($sheetName);
            $data = $sheet->toArray();
            Log::info("Sheet '$sheetName' has " . count($data) . " rows");

            // Check if sheet has valid data rows (excluding header)
            $hasValidData = false;
            foreach ($data as $i => $row) {
                if ($i === 0) {
                    Log::info("Skipping header row for sheet: $sheetName");
                    continue; // Skip header
                }

                // Check if row has any non-empty values
                $rowHasData = array_filter($row, function ($value) {
                    return !is_null($value) && $value !== '';
                });

                if (!empty($rowHasData)) {
                    $hasValidData = true;
                    break; // Found at least one valid data row
                }
            }

            if (!$hasValidData) {
                Log::info("Sheet '$sheetName' has no valid data rows, skipping processing");
                continue;
            }

            $gidErrors = [];
            foreach ($data as $i => $row) {
                if ($i === 0) {
                    continue; // Skip header
                }

                if ($template === 'general') {
                    [$no, $itm_id, $description, $wct_id, $dpt_id] = array_slice($row, 0, 5);
                    // if (strtoupper(trim($item_type)) === 'GID') {
                    //     $itemExists = Item::where('itm_id', $itm_id)->exists();
                    //     if (!$itemExists) {
                    //         $gidErrors[] = "GID item not found in row $i of sheet $sheetName: itm_id=$itm_id";
                    //         Log::warning("GID item not found in row $i of sheet $sheetName: itm_id=$itm_id");
                    //     }
                    // }
                } elseif ($template === 'aftersales') {
                    [$no,  $itm_id, $customer, $wct_id, $dpt_id] = array_slice($row, 0, 5);
                    // if (strtoupper(trim($item_type)) === 'GID') {
                    //     $itemExists = Item::where('itm_id', $itm_id)->exists();
                    //     if (!$itemExists) {
                    //         $gidErrors[] = "GID item not found in row $i of sheet $sheetName: itm_id=$itm_id";
                    //         Log::warning("GID item not found in row $i of sheet $sheetName: itm_id=$itm_id");
                    //     }
                    // }
                } elseif ($template === 'support') {
                    [$no, $itm_id, $description, $wct_id, $dpt_id, $bdc_id, $lob_id] = array_slice($row, 0, 7);
                    // if (strtoupper(trim($item_type)) === 'GID') {
                    //     $itemExists = Item::where('itm_id', $itm_id)->exists();
                    //     if (!$itemExists) {
                    //         $gidErrors[] = "GID item not found in row $i of sheet $sheetName: itm_id=$itm_id";
                    //         Log::warning("GID item not found in row $i of sheet $sheetName: itm_id=$itm_id");
                    //     }
                    // }
                } elseif ($template === 'utilities') {
                    [$no,  $itm_id, $kwh, $price, $wct_id, $dpt_id, $lob_id] = array_slice($row, 0, 7);
                    // if (strtoupper(trim($item_type)) === 'GID') {
                    //     $itemExists = Item::where('itm_id', $itm_id)->exists();
                    //     if (!$itemExists) {
                    //         $gidErrors[] = "GID item not found in row $i of sheet $sheetName: itm_id=$itm_id";
                    //         Log::warning("GID item not found in row $i of sheet $sheetName: itm_id=$itm_id");
                    //     }
                    // }
                } elseif ($template === 'business') {
                    [$no, $trip_propose, $destination, $days, $wct_id, $dpt_id] = array_slice($row, 0, 6);
                    // if (strtoupper(trim($item_type)) === 'GID') {
                    //     $itemExists = Item::where('itm_id', $itm_id)->exists();
                    //     if (!$itemExists) {
                    //         $gidErrors[] = "GID item not found in row $i of sheet $sheetName: itm_id=$itm_id";
                    //         Log::warning("GID item not found in row $i of sheet $sheetName: itm_id=$itm_id");
                    //     }
                    // }
                } elseif ($template === 'representation') {
                    [$no, $itm_id, $description, $beneficiary, $wct_id, $dpt_id] = array_slice($row, 0, 6);
                    // if (strtoupper(trim($item_type)) === 'GID') {
                    //     $itemExists = Item::where('itm_id', $itm_id)->exists();
                    //     if (!$itemExists) {
                    //         $gidErrors[] = "GID item not found in row $i of sheet $sheetName: itm_id=$itm_id";
                    //         Log::warning("GID item not found in row $i of sheet $sheetName: itm_id=$itm_id");
                    //     }
                    // }
                } elseif ($template === 'insurance') {
                    [$no, $description, $ins_id, $price, $wct_id, $dpt_id] = array_slice($row, 0, 6);
                    // if (strtoupper(trim($item_type)) === 'GID') {
                    //     $itemExists = Item::where('itm_id', $itm_id)->exists();
                    //     if (!$itemExists) {
                    //         $gidErrors[] = "GID item not found in row $i of sheet $sheetName: itm_id=$itm_id";
                    //         Log::warning("GID item not found in row $i of sheet $sheetName: itm_id=$itm_id");
                    //     }
                    // }
                } elseif ($template === 'training') {
                    [$no, $participant, $jenis_training, $quantity, $price, $wct_id, $dpt_id] = array_slice($row, 0, 7);
                    // if (strtoupper(trim($item_type)) === 'GID') {
                    //     $itemExists = Item::where('itm_id', $itm_id)->exists();
                    //     if (!$itemExists) {
                    //         $gidErrors[] = "GID item not found in row $i of sheet $sheetName: itm_id=$itm_id";
                    //         Log::warning("GID item not found in row $i of sheet $sheetName: itm_id=$itm_id");
                    //     }
                    // }
                } elseif ($template === 'recruitment') {
                    [$no, $itm_id, $description, $position, $price, $wct_id, $dpt_id] = array_slice($row, 0, 7);
                    // if (strtoupper(trim($item_type)) === 'GID') {
                    //     $itemExists = Item::where('itm_id', $itm_id)->exists();
                    //     if (!$itemExists) {
                    //         $gidErrors[] = "GID item not found in row $i of sheet $sheetName: itm_id=$itm_id";
                    //         Log::warning("GID item not found in row $i of sheet $sheetName: itm_id=$itm_id");
                    //     }
                    // }
                } elseif ($template === 'employee') {
                    [$no, $type, $ledger_account, $ledger_account_description, $price, $wct_id, $dpt_id, $lob_id, $bdc_id] = array_slice($row, 0, 9);
                    // if (strtoupper(trim($item_type)) === 'GID') {
                    //     $itemExists = Item::where('itm_id', $itm_id)->exists();
                    //     if (!$itemExists) {
                    //         $gidErrors[] = "GID item not found in row $i of sheet $sheetName: itm_id=$itm_id";
                    //         Log::warning("GID item not found in row $i of sheet $sheetName: itm_id=$itm_id");
                    //     }
                    // }
                } elseif ($template === 'purchase') {
                    [$no, $itm_id, $business_partner, $description, $price, $wct_id, $dpt_id, $lob_id, $bdc_id] = array_slice($row, 0, 9);
                    // if (strtoupper(trim($item_type)) === 'GID') {
                    //     $itemExists = Item::where('itm_id', $itm_id)->exists();
                    //     if (!$itemExists) {
                    //         $gidErrors[] = "GID item not found in row $i of sheet $sheetName: itm_id=$itm_id";
                    //         Log::warning("GID item not found in row $i of sheet $sheetName: itm_id=$itm_id");
                    //     }
                    // }
                }
            }

            // Jika ada error GID, lewati seluruh sheet
            if (!empty($gidErrors)) {
                $errors = array_merge($errors, $gidErrors);
                Log::warning("Skipping sheet '$sheetName' due to GID validation errors", ['errors' => $gidErrors]);
                continue;
            }

            // Generate sub_id berdasarkan tipe config
            if ($isMultiPrefix) {
                // Untuk employee comp, kita akan generate sub_id per row nanti
                $trimmedType = trim($type ?? '');
                $prefix = $prefixMap[$trimmedType] ?? 'EMC';
                $acc_id = $accIdMap[$trimmedType] ?? 'SGAEMPLOYCOMP';

                // GENERATE sub_id PER ROW untuk employee comp
                $lastRecord = $model::where('sub_id', 'like', "$prefix%")
                    ->orderBy('sub_id', 'desc')
                    ->first();
                $nextNumber = $lastRecord ? ((int)str_replace($prefix, '', $lastRecord->sub_id) + 1) : 1;
                $sub_id = $prefix . str_pad($nextNumber, 7, '0', STR_PAD_LEFT);
            } else {
                // Untuk sheet lain, generate seperti biasa
                $lastRecord = $model::where('sub_id', 'like', "$prefix%")
                    ->orderBy('sub_id', 'desc')
                    ->first();
                $nextNumber = $lastRecord ? ((int)str_replace($prefix, '', $lastRecord->sub_id) + 1) : 1;
                $sub_id = $prefix . str_pad($nextNumber, 7, '0', STR_PAD_LEFT);
            }

            // Generate sub_id sekali per worksheet dengan data valid
            // $lastRecord = $model::where('sub_id', 'like', "$prefix%")
            //     ->orderBy('sub_id', 'desc')
            //     ->first();
            // $nextNumber = $lastRecord ? ((int)str_replace($prefix, '', $lastRecord->sub_id) + 1) : 1;
            // $sub_id = $prefix . str_pad($nextNumber, 7, '0', STR_PAD_LEFT);
            // Log::info("Generated sub_id for sheet $sheetName: $sub_id");

            // Create approval record for the sub_id
            try {
                Approval::create([
                    'approve_by' => $npk,
                    'sub_id' => $sub_id,
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                Log::info("Created approval record for sub_id: $sub_id, approve_by: $npk");
            } catch (\Exception $e) {
                Log::error("Failed to create approval record for sub_id: $sub_id, error: " . $e->getMessage());
                return response()->json(['message' => 'Failed to create approval record: ' . $e->getMessage()], 500);
            }

            $processedSheets[] = $sheetName;

            // Process data rows
            foreach ($data as $i => $row) {
                if ($i === 0) {
                    Log::info("Skipping header row for sheet: $sheetName");
                    continue; // Skip header
                }

                // Validasi jumlah kolom
                $expectedColumns = $this->getExpectedColumns($template, $months);
                if (count($row) < $expectedColumns) {
                    Log::warning("Invalid column count in row $i of sheet $sheetName: expected at least $expectedColumns, got " . count($row));
                    continue;
                }

                Log::info("Processing row $i in sheet '$sheetName': " . json_encode($row));

                try {
                    if ($template === 'general') {
        // Ekstrak data dari baris Excel berdasarkan template 'general'
        [$no, $itm_id, $description, $wct_id, $dpt_id] = array_slice($row, 0, 5);
        $amount = $row[17] ?? null;

        // Validasi departemen: Izinkan GA (4131) mengunggah untuk BOD (7111)
        if ($userDept === '4131' && in_array($dpt_id, ['4131', '1111', '1131', '1151', '1211', '1231', '7111'])) {
            Log::info("GA (4131) uploading untuk dpt_id $dpt_id diizinkan pada baris $i di sheet $sheetName");
        } elseif ($userDept === '4111' && in_array($dpt_id, ['4111', '1116', '1140', '1160', '1224', '1242', '7111'])) {
            Log::info("4111 uploading untuk dpt_id $dpt_id diizinkan pada baris $i di sheet $sheetName");
        } elseif ($dpt_id !== $userDept) {
            $errors[] = "Invalid dpt_id pada baris $i di sheet $sheetName: Diharapkan $userDept, mendapat $dpt_id";
            Log::warning("Invalid dpt_id pada baris $i di sheet $sheetName: Diharapkan $userDept, mendapat $dpt_id");
            continue;
        }

        // Kode asli yang dikomentari untuk validasi item_type (GID/Non-GID)
        // if (strtoupper(trim($item_type)) === 'GID') {
        //     $itemExists = Item::where('itm_id', $itm_id)->exists();
        //     if (!$itemExists) {
        //         $errors[] = "GID item not found in row $i of sheet $sheetName: itm_id=$itm_id";
        //         Log::warning("GID item not found in row $i of sheet $sheetName: itm_id=$itm_id");
        //         continue; // Skip this row
        //     }
        // } elseif (strtoupper(trim($item_type)) === 'Non-GID') {
        //     $errors[] = "Invalid Item Type in row $i of sheet $sheetName: expected 'GID' or 'Non-GID', got '$item_type'";
        //     Log::warning("Invalid Item Type in row $i of sheet $sheetName: expected 'GID' or 'Non-GID', got '$item_type'");
        //     continue; // Skip this row
        // }

        // Validasi kolom wajib tidak boleh kosong
        $requiredFields = [
            'itm_id' => $itm_id,
            'description' => $description,
            // 'quantity' => $quantity,
            // 'price' => $price,
            'amount' => $amount,
            'dpt_id' => $dpt_id,
            // 'bdc_id' => $bdc_id,
        ];

        foreach ($requiredFields as $fieldName => $value) {
            if (is_null($value) || $value === '' || trim($value) === '') {
                $errors[] = "Invalid $fieldName pada baris $i di sheet $sheetName: $fieldName kosong atau null";
                Log::warning("Invalid $fieldName pada baris $i di sheet $sheetName: $fieldName kosong atau null");
                continue 2; // Lewati iterasi foreach terluar (seluruh baris)
            }
        }

        // Kode asli yang dikomentari untuk validasi harga
        // $price = (float)$price;
        // // Validasi quantity, price, dan amount harus numerik
        // if (!is_numeric($price)) {
        //     $errors[] = "Invalid numeric value in row $i of sheet $sheetName: price=$price";
        //     Log::warning("Invalid numeric value in row $i: price=$price");
        //     continue;
        // }

        Log::info("Processing row $i in sheet $sheetName with itm_id: $itm_id");

        // Iterasi untuk setiap bulan
        foreach (array_keys($months) as $index => $monthIndex) {
            $monthValue = $row[5 + $index] ?? 0;
            if ($monthValue == 0 || $monthValue === null || trim($monthValue) === '') {
                Log::info("Skipping month $monthIndex for row $i: value is $monthValue");
                continue;
            }

            if (!is_numeric($monthValue)) {
                $errors[] = "Invalid numeric value for month $monthIndex in row $i of sheet $sheetName: value=$monthValue";
                Log::warning("Invalid numeric value for month $monthIndex in row $i: value=$monthValue");
                continue;
            }

            // Konversi angka bulan ke nama bulan
            $monthName = $monthNumberToName[$monthIndex + 1] ?? 'Unknown'; // +1 karena array dimulai dari 1

            // Simpan data ke database dengan penanganan error
            try {
                $model::create([
                    'sub_id' => $sub_id,
                    'purpose' => $purpose,
                    'acc_id' => $acc_id,
                    'itm_id' => $itm_id,
                    'description' => $description,
                    'price' => (float)$monthValue,
                    'amount' => (float)$amount, // Konversi eksplisit ke float
                    'wct_id' => $wct_id,
                    'dpt_id' => $dpt_id,
                    'month' => $monthName, // Simpan nama bulan bukan angka
                    'status' => 1,
                ]);
                Log::info("Created record for sub_id: $sub_id, month: $monthName, value: $monthValue, itm_id: $itm_id");
                $processedRows++;
            } catch (\Exception $e) {
                $errors[] = "Gagal membuat record untuk sub_id: $sub_id, bulan: $monthName, sheet: $sheetName, error: " . $e->getMessage();
                Log::error("Gagal membuat record untuk sub_id: $sub_id, bulan: $monthName, sheet: $sheetName, error: " . $e->getMessage());
                continue;
            }
        }

                                    } elseif ($template === 'aftersales') {
                                        [$no, $itm_id, $customer, $wct_id, $dpt_id] = array_slice($row, 0, 5);
                                        $amount = $row[17] ?? null;
                                        // if (!is_numeric($price)) {
                                        //     Log::warning("Invalid price in row $i: price=$price");
                                        //     continue;
                                        // }

                                        // if (strtoupper(trim($item_type)) === 'GID') {
                                        //     $itemExists = Item::where('itm_id', $itm_id)->exists();
                                        //     if (!$itemExists) {
                                        //         $errors[] = "GID item not found in row $i of sheet $sheetName: itm_id=$itm_id";
                                        //         Log::warning("GID item not found in row $i of sheet $sheetName: itm_id=$itm_id");
                                        //         continue; // Skip this row
                                        //     }
                                        // } elseif (strtoupper(trim($item_type)) !== 'Non-GID') {
                                        //     $errors[] = "Invalid Item Type in row $i of sheet $sheetName: expected 'GID' or 'Non-GID', got '$item_type'";
                                        //     Log::warning("Invalid Item Type in row $i of sheet $sheetName: expected 'GID' or 'Non-GID', got '$item_type'");
                                        //     continue; // Skip this row
                                        // }

                                        // Validasi departemen: Izinkan GA (4131) mengunggah untuk BOD (7111)
                                        if ($userDept === '4131' && in_array($dpt_id, ['4131', '1111', '1131', '1151', '1211', '1231', '7111'])) {
                                            Log::info("GA (4131) uploading untuk dpt_id $dpt_id diizinkan pada baris $i di sheet $sheetName");
                                        } elseif ($userDept === '4111' && in_array($dpt_id, ['4111', '1116', '1140', '1160', '1224', '1242', '7111'])) {
                                            Log::info("4111 uploading untuk dpt_id $dpt_id diizinkan pada baris $i di sheet $sheetName");
                                        } elseif ($dpt_id !== $userDept) {
                                            $errors[] = "Invalid dpt_id pada baris $i di sheet $sheetName: Diharapkan $userDept, mendapat $dpt_id";
                                            Log::warning("Invalid dpt_id pada baris $i di sheet $sheetName: Diharapkan $userDept, mendapat $dpt_id");
                                            continue;
                                        }
                                        $requiredFields = [
                                            'itm_id' => $itm_id,
                                            'customer' => $customer,
                                            // 'quantity' => $quantity,
                                            // 'price' => $price,
                                            'amount' => $amount,
                                            'dpt_id' => $dpt_id,
                                            // 'bdc_id' => $bdc_id,
                                        ];

                                        foreach ($requiredFields as $fieldName => $value) {
                                            if (is_null($value) || $value === '' || trim($value) === '') {
                                                $errors[] = "Invalid $fieldName in row $i of sheet $sheetName: $fieldName is empty or null";
                                                Log::warning("Invalid $fieldName in row $i of sheet $sheetName: $fieldName is empty or null");
                                                continue 2; // Lewati iterasi foreach terluar (seluruh baris)
                                            }
                                        }

                                        // $price = (float)$price;
                                        // // Validasi quantity, price, dan amount harus numerik
                                        // if (!is_numeric($price)) {
                                        //     $errors[] = "Invalid numeric value in row $i of sheet $sheetName:  price=$price ";
                                        //     Log::warning("Invalid numeric value in row $i: price=$price");
                                        //     continue;
                                        // }

                                        Log::info("Processing row $i in sheet $sheetName with itm_id: $itm_id");

                                        foreach (array_keys($months) as $index => $monthIndex) {
                    $monthValue = $row[5 + $index] ?? 0;
                    if ($monthValue == 0 || $monthValue === null || trim($monthValue) === '') {
                        Log::info("Skipping month $monthIndex for row $i: value is $monthValue");
                        continue;
                    }

                    if (!is_numeric($monthValue)) {
                        $errors[] = "Invalid numeric value for month $monthIndex in row $i of sheet $sheetName: value=$monthValue";
                        Log::warning("Invalid numeric value for month $monthIndex in row $i: value=$monthValue");
                        continue;
                    }

                    // Konversi angka bulan ke nama bulan
                    $monthName = $monthNumberToName[$monthIndex + 1] ?? 'Unknown'; // +1 karena array dimulai dari 1
                                            $model::create([
                                                'sub_id' => $sub_id,
                                                'purpose' => $purpose,
                                                'acc_id' => $acc_id,
                                                'itm_id' => $itm_id,
                                                'customer' => $customer,
                                                // 'quantity' => $quantity,
                                                'price' => (float)$monthValue,
                                                'amount' => $amount,
                                                'wct_id' => $wct_id,
                                                'dpt_id' => $dpt_id,
                                                // 'bdc_id' => $bdc_id,
                                                'month' => $monthName,
                                                'status' => 1,
                                            ]);
                                            Log::info("Created record for sub_id: $sub_id, month: $monthName, value: $value");
                                            $processedRows++;
                                        }
                                    } elseif ($template === 'support') {
                                        [$no, $itm_id, $description, $wct_id, $dpt_id, $bdc_id, $lob_id] = array_slice($row, 0, 7);
                                        $amount = $row[19] ?? null;
                                        // if (!is_numeric($quantity) || !is_numeric($price) || !is_numeric($amount)) {
                                        //     Log::warning("Invalid quantity, price, or amount in row $i: quantity=$quantity, price=$price, amount=$amount");
                                        //     continue;
                                        // }
                                        // if (strtoupper(trim($item_type)) === 'GID') {
                                        //     $itemExists = Item::where('itm_id', $itm_id)->exists();
                                        //     if (!$itemExists) {
                                        //         $errors[] = "GID item not found in row $i of sheet $sheetName: itm_id=$itm_id";
                                        //         Log::warning("GID item not found in row $i of sheet $sheetName: itm_id=$itm_id");
                                        //         continue; // Skip this row
                                        //     }
                                        // } elseif (strtoupper(trim($item_type)) !== 'Non-GID') {
                                        //     $errors[] = "Invalid Item Type in row $i of sheet $sheetName: expected 'GID' or 'Non-GID', got '$item_type'";
                                        //     Log::warning("Invalid Item Type in row $i of sheet $sheetName: expected 'GID' or 'Non-GID', got '$item_type'");
                                        //     continue; // Skip this row
                                        // }

                                        // Validasi departemen: Izinkan GA (4131) mengunggah untuk BOD (7111)
                                        if ($userDept === '4131' && in_array($dpt_id, ['4131', '1111', '1131', '1151', '1211', '1231', '7111'])) {
                                            Log::info("GA (4131) uploading untuk dpt_id $dpt_id diizinkan pada baris $i di sheet $sheetName");
                                        } elseif ($userDept === '4111' && in_array($dpt_id, ['4111', '1116', '1140', '1160', '1224', '1242', '7111'])) {
                                            Log::info("4111 uploading untuk dpt_id $dpt_id diizinkan pada baris $i di sheet $sheetName");
                                        } elseif ($dpt_id !== $userDept) {
                                            $errors[] = "Invalid dpt_id pada baris $i di sheet $sheetName: Diharapkan $userDept, mendapat $dpt_id";
                                            Log::warning("Invalid dpt_id pada baris $i di sheet $sheetName: Diharapkan $userDept, mendapat $dpt_id");
                                            continue;
                                        }
                                        $requiredFields = [
                                            'itm_id' => $itm_id,
                                            'description' => $description,
                                            // 'unit' => $unit,
                                            // 'quantity' => $quantity,
                                            // 'price' => $price,
                                            'amount' => $amount,
                                            'dpt_id' => $dpt_id,
                                            'bdc_id' => $bdc_id,
                                            'lob_id' => $lob_id,
                                        ];

                                        foreach ($requiredFields as $fieldName => $value) {
                                            if (is_null($value) || $value === '' || trim($value) === '') {
                                                $errors[] = "Invalid $fieldName in row $i of sheet $sheetName: $fieldName is empty or null";
                                                Log::warning("Invalid $fieldName in row $i of sheet $sheetName: $fieldName is empty or null");
                                                continue 2; // Lewati iterasi foreach terluar (seluruh baris)
                                            }
                                        }

                                        // $price = (float)$price;
                                        // // Validasi quantity, price, dan amount harus numerik
                                        // if (!is_numeric($price)) {
                                        //     $errors[] = "Invalid numeric value in row $i of sheet $sheetName:  price=$price";
                                        //     Log::warning("Invalid numeric value in row $i: price=$price");
                                        //     continue;
                                        // }

                                        Log::info("Processing row $i in sheet $sheetName with itm_id: $itm_id");

                                        foreach (array_keys($months) as $index => $monthIndex) {
                    $monthValue = $row[7 + $index] ?? 0;
                    if ($monthValue == 0 || $monthValue === null || trim($monthValue) === '') {
                        Log::info("Skipping month $monthIndex for row $i: value is $monthValue");
                        continue;
                    }

                    if (!is_numeric($monthValue)) {
                        $errors[] = "Invalid numeric value for month $monthIndex in row $i of sheet $sheetName: value=$monthValue";
                        Log::warning("Invalid numeric value for month $monthIndex in row $i: value=$monthValue");
                        continue;
                    }

                    // Konversi angka bulan ke nama bulan
                    $monthName = $monthNumberToName[$monthIndex + 1] ?? 'Unknown'; // +1 karena array dimulai dari 1


                                            $model::create([
                                                'sub_id' => $sub_id,
                                                'purpose' => $purpose,
                                                'acc_id' => $acc_id,
                                                'itm_id' => $itm_id,
                                                'description' => $description,
                                                // 'unit' => $unit,
                                                // 'quantity' => $quantity,
                                                'price' => (float)$monthValue,
                                                'amount' => $amount,
                                                'wct_id' => $wct_id,
                                                'dpt_id' => $dpt_id,
                                                'bdc_id' => $bdc_id,
                                                'lob_id' => $lob_id,
                                                'month' => $monthName,
                                                'status' => 1,
                                            ]);
                                            Log::info("Created record for sub_id: $sub_id, month: $monthName, value: $value");
                                            $processedRows++;
                                        }
                                    } elseif ($template === 'insurance') {
                                        [$no, $description, $ins_id, $wct_id, $dpt_id] = array_slice($row, 0, 5);
                                        // if (!is_numeric($price)) {
                                        //     Log::warning("Invalid price in row $i: price=$price");
                                        //     continue;
                                        // }

                                        // Validasi department dengan multiple department untuk 4131 dan 4111
                                        if ($userDept === '4131' && in_array($dpt_id, ['4131', '1111', '1131', '1151', '1211', '1231', '7111'])) {
                                            Log::info("GA (4131) uploading untuk dpt_id $dpt_id diizinkan pada baris $i di sheet $sheetName");
                                        } elseif ($userDept === '4111' && in_array($dpt_id, ['4111', '1116', '1140', '1160', '1224', '1242', '7111'])) {
                                            Log::info("4111 uploading untuk dpt_id $dpt_id diizinkan pada baris $i di sheet $sheetName");
                                        } elseif ($dpt_id !== $userDept) {
                                            $errors[] = "Invalid dpt_id in row $i of sheet $sheetName: Expected $userDept, got $dpt_id";
                                            Log::warning("Invalid dpt_id in row $i of sheet $sheetName: Expected $userDept, got $dpt_id");
                                            continue;
                                        }
                                        $requiredFields = [
                                            // 'itm_id' => $itm_id,
                                            'description' => $description,
                                            'ins_id' => $ins_id,
                                            // 'quantity' => $quantity,
                                            'price' => $price,
                                            // 'amount' => $amount,
                                            'dpt_id' => $dpt_id,
                                            // 'bdc_id' => $bdc_id,
                                        ];

                                        foreach ($requiredFields as $fieldName => $value) {
                                            if (is_null($value) || $value === '' || trim($value) === '') {
                                                $errors[] = "Invalid $fieldName in row $i of sheet $sheetName: $fieldName is empty or null";
                                                Log::warning("Invalid $fieldName in row $i of sheet $sheetName: $fieldName is empty or null");
                                                continue 2; // Lewati iterasi foreach terluar (seluruh baris)
                                            }
                                        }

                                        $price = (float)$price;

                                        // Validasi quantity, price, dan amount harus numerik
                                        if (!is_numeric($price)) {
                                            $errors[] = "Invalid numeric value in row $i of sheet $sheetName: price=$price";
                                            Log::warning("Invalid numeric value in row $i: price=$price");
                                            continue;
                                        }

                                        Log::info("Processing row $i in sheet $sheetName with itm_id: $itm_id");

                                        foreach (array_keys($months) as $index => $monthIndex) {
                    $monthValue = $row[5 + $index] ?? 0;
                    if ($monthValue == 0 || $monthValue === null || trim($monthValue) === '') {
                        Log::info("Skipping month $monthIndex for row $i: value is $monthValue");
                        continue;
                    }

                    if (!is_numeric($monthValue)) {
                        $errors[] = "Invalid numeric value for month $monthIndex in row $i of sheet $sheetName: value=$monthValue";
                        Log::warning("Invalid numeric value for month $monthIndex in row $i: value=$monthValue");
                        continue;
                    }

                    // Konversi angka bulan ke nama bulan
                    $monthName = $monthNumberToName[$monthIndex + 1] ?? 'Unknown'; // +1 karena array dimulai dari 1
                                            $model::create([
                                                'sub_id' => $sub_id,
                                                'purpose' => $purpose,
                                                'acc_id' => $acc_id,
                                                'description' => $description,
                                                'ins_id' => $ins_id,
                                                // 'quantity' => $quantity,
                                                'price' => (float)$monthValue,
                                                // 'amount' => $amount,
                                                'wct_id' => $wct_id,
                                                'dpt_id' => $dpt_id,
                                                // 'bdc_id' => $bdc_id,
                                                'month' => $monthName,
                                                'status' => 1,
                                            ]);
                                            Log::info("Created record for sub_id: $sub_id, month: $monthName, value: $value");
                                            $processedRows++;
                                        }
                                    } elseif ($template === 'utilities') {
                                        [$no, $itm_id, $kwh, $wct_id, $dpt_id, $lob_id] = array_slice($row, 0, 6);
                                        // if (!is_numeric($kwh) || !is_numeric($price)) {
                                        //     Log::warning("Invalid kwh or price in row $i: kwh=$kwh, price=$price");
                                        //     continue;
                                        // }

                                        // if (strtoupper(trim($item_type)) === 'GID') {
                                        //     $itemExists = Item::where('itm_id', $itm_id)->exists();
                                        //     if (!$itemExists) {
                                        //         $errors[] = "GID item not found in row $i of sheet $sheetName: itm_id=$itm_id";
                                        //         Log::warning("GID item not found in row $i of sheet $sheetName: itm_id=$itm_id");
                                        //         continue; // Skip this row
                                        //     }
                                        // } elseif (strtoupper(trim($item_type)) !== 'Non-GID') {
                                        //     $errors[] = "Invalid Item Type in row $i of sheet $sheetName: expected 'GID' or 'Non-GID', got '$item_type'";
                                        //     Log::warning("Invalid Item Type in row $i of sheet $sheetName: expected 'GID' or 'Non-GID', got '$item_type'");
                                        //     continue; // Skip this row
                                        // }

                                        // Validasi department dengan multiple department untuk 4131 dan 4111
                                        if ($userDept === '4131' && in_array($dpt_id, ['4131', '1111', '1131', '1151', '1211', '1231', '7111'])) {
                                            Log::info("GA (4131) uploading untuk dpt_id $dpt_id diizinkan pada baris $i di sheet $sheetName");
                                        } elseif ($userDept === '4111' && in_array($dpt_id, ['4111', '1116', '1140', '1160', '1224', '1242', '7111'])) {
                                            Log::info("4111 uploading untuk dpt_id $dpt_id diizinkan pada baris $i di sheet $sheetName");
                                        } elseif ($dpt_id !== $userDept) {
                                            $errors[] = "Invalid dpt_id in row $i of sheet $sheetName: Expected $userDept, got $dpt_id";
                                            Log::warning("Invalid dpt_id in row $i of sheet $sheetName: Expected $userDept, got $dpt_id");
                                            continue;
                                        }
                                        $requiredFields = [
                                            'itm_id' => $itm_id,
                                            'kwh' => $kwh,
                                            // 'quantity' => $quantity,
                                            'price' => $price,
                                            // 'amount' => $amount,
                                            'dpt_id' => $dpt_id,
                                            'lob_id' => $lob_id,
                                        ];

                                        foreach ($requiredFields as $fieldName => $value) {
                                            if (is_null($value) || $value === '' || trim($value) === '') {
                                                $errors[] = "Invalid $fieldName in row $i of sheet $sheetName: $fieldName is empty or null";
                                                Log::warning("Invalid $fieldName in row $i of sheet $sheetName: $fieldName is empty or null");
                                                continue 2; // Lewati iterasi foreach terluar (seluruh baris)
                                            }
                                        }

                                        $price = (float)$price;
                                        // Validasi quantity, price, dan amount harus numerik
                                        if (!is_numeric($price)) {
                                            $errors[] = "Invalid numeric value in row $i of sheet $sheetName: price=$price";
                                            Log::warning("Invalid numeric value in row $i: price=$price");
                                            continue;
                                        }

                                        Log::info("Processing row $i in sheet $sheetName with itm_id: $itm_id");

                                        foreach (array_keys($months) as $index => $monthIndex) {
                    $monthValue = $row[5 + $index] ?? 0;
                    if ($monthValue == 0 || $monthValue === null || trim($monthValue) === '') {
                        Log::info("Skipping month $monthIndex for row $i: value is $monthValue");
                        continue;
                    }

                    if (!is_numeric($monthValue)) {
                        $errors[] = "Invalid numeric value for month $monthIndex in row $i of sheet $sheetName: value=$monthValue";
                        Log::warning("Invalid numeric value for month $monthIndex in row $i: value=$monthValue");
                        continue;
                    }

                    // Konversi angka bulan ke nama bulan
                    $monthName = $monthNumberToName[$monthIndex + 1] ?? 'Unknown'; // +1 karena array dimulai dari 1

                                            $model::create([
                                                'sub_id' => $sub_id,
                                                'purpose' => $purpose,
                                                'acc_id' => $acc_id,
                                                'itm_id' => $itm_id,
                                                'kwh' => $kwh,
                                                // 'quantity' => $quantity,
                                                'price' => (float)$monthValue,
                                                // 'amount' => $amount,
                                                'wct_id' => $wct_id,
                                                'dpt_id' => $dpt_id,
                                                'lob_id' => $lob_id,
                                                'month' => $monthName,
                                                'status' => 1,
                                            ]);
                                            Log::info("Created record for sub_id: $sub_id, month: $monthName, value: $value");
                                            $processedRows++;
                                        }
                                    
            } elseif ($template === 'business') {
            // Ambil 6 kolom pertama sesuai data input
            [$no, $trip_propose, $destination, $days, $wct_id, $dpt_id] = array_slice($row, 0, 6);
            $amount = $$row[18] ?? null; 

            // Validasi department
            if ($dpt_id !== $userDept) {
                $errors[] = "Invalid dpt_id in row $i of sheet $sheetName: Expected $userDept, got $dpt_id";
                Log::warning("Invalid dpt_id in row $i of sheet $sheetName: Expected $userDept, got $dpt_id");
                continue;
            }

            // Validasi field required
            $requiredFields = [
                'trip_propose' => $trip_propose,
                'destination' => $destination,
                'days' => $days,
                'dpt_id' => $dpt_id,
            ];

            foreach ($requiredFields as $fieldName => $value) {
                if (is_null($value) || $value === '' || trim($value) === '') {
                    $errors[] = "Invalid $fieldName in row $i of sheet $sheetName: $fieldName is empty or null";
                    Log::warning("Invalid $fieldName in row $i of sheet $sheetName: $fieldName is empty or null");
                    continue 2; // Lewati iterasi foreach terluar (seluruh baris)
                }
            }

            // Validasi numeric fields
            if (!is_numeric($days)) {
                $errors[] = "Invalid numeric value for days in row $i of sheet $sheetName: days=$days";
                Log::warning("Invalid numeric value for days in row $i: days=$days");
                continue;
            }

            Log::info("Processing row $i in sheet $sheetName with trip_propose: $trip_propose");

            // Process setiap bulan, mulai dari kolom ke-7 (index 6)
            foreach (array_keys($months) as $index => $monthIndex) {
                $monthValue = $row[6 + $index] ?? 0; // Data bulan dimulai dari kolom ke-7 (Jan)

                if ($monthValue == 0 || $monthValue === null || trim($monthValue) === '') {
                    Log::info("Skipping month $monthIndex for row $i: value is $monthValue");
                    continue;
                }

                if (!is_numeric($monthValue)) {
                    $errors[] = "Invalid numeric value for month $monthIndex in row $i of sheet $sheetName: value=$monthValue";
                    Log::warning("Invalid numeric value for month $monthIndex in row $i: value=$monthValue");
                    continue;
                }

                // Konversi angka bulan ke nama bulan
                $monthName = $monthNumberToName[$monthIndex + 1] ?? 'Unknown';

                // Simpan data ke database
                $model::create([
                    'sub_id' => $sub_id,
                    'purpose' => $purpose,
                    'acc_id' => $acc_id,
                    'trip_propose' => $trip_propose,
                    'destination' => $destination,
                    'days' => (float)$days,
                    'wct_id' => $wct_id,
                    'dpt_id' => $dpt_id,
                    'price' => (float)$monthValue, // Nilai bulanan sebagai price
                    'month' => $monthName,
                    'status' => 1,
                    'amount' => $amount ? (float)$amount : null,
                ]);

                Log::info("Created record for sub_id: $sub_id, month: $monthName, value: $monthValue");
                $processedRows++;
            }
                                        } elseif ($template === 'representation') {
            // Ambil 6 kolom pertama
            [$no, $itm_id, $description, $beneficiary, $wct_id, $dpt_id] = array_slice($row, 0, 6);
            $amount = $row[18] ?? null;
            
            // // Ambil kolom tambahan yang diperlukan
            // $price = $row[6] ?? null; // Kolom ke-7 (index 6)
            // $quantity = $row[7] ?? null; // Kolom ke-8 (index 7)
            // $amount = $row[8] ?? null; // Kolom ke-9 (index 8)
            // $bdc_id = $row[9] ?? null; // Kolom ke-10 (index 9)

            // Validasi department dengan multiple department untuk 4131 dan 4111
            if ($userDept === '4131' && in_array($dpt_id, ['4131', '1111', '1131', '1151', '1211', '1231', '7111'])) {
                Log::info("GA (4131) uploading untuk dpt_id $dpt_id diizinkan pada baris $i di sheet $sheetName");
            } elseif ($userDept === '4111' && in_array($dpt_id, ['4111', '1116', '1140', '1160', '1224', '1242', '7111'])) {
                Log::info("4111 uploading untuk dpt_id $dpt_id diizinkan pada baris $i di sheet $sheetName");
            } elseif ($dpt_id !== $userDept) {
                $errors[] = "Invalid dpt_id in row $i of sheet $sheetName: Expected $userDept, got $dpt_id";
                Log::warning("Invalid dpt_id in row $i of sheet $sheetName: Expected $userDept, got $dpt_id");
                continue;
            }

            // Validasi field required
            $requiredFields = [
                'itm_id' => $itm_id,
                'description' => $description,
                'beneficiary' => $beneficiary,
                'dpt_id' => $dpt_id,
            ];

            foreach ($requiredFields as $fieldName => $value) {
                if (is_null($value) || $value === '' || trim($value) === '') {
                    $errors[] = "Invalid $fieldName in row $i of sheet $sheetName: $fieldName is empty or null";
                    Log::warning("Invalid $fieldName in row $i of sheet $sheetName: $fieldName is empty or null");
                    continue 2;
                }
            }

            // Validasi numeric fields
            // if (!is_numeric($price)) {
            //     $errors[] = "Invalid numeric value for price in row $i of sheet $sheetName: price=$price";
            //     Log::warning("Invalid numeric value for price in row $i: price=$price");
            //     continue;
            // }

            // $price = (float)$price;

            Log::info("Processing row $i in sheet $sheetName with itm_id: $itm_id");

            // Process each month - PERBAIKAN INDEX DI SINI
            foreach (array_keys($months) as $index => $monthIndex) {
                $monthValue = $row[6 + $index] ?? 0; // Data bulan dimulai setelah 10 kolom
                
                if ($monthValue == 0 || $monthValue === null || trim($monthValue) === '') {
                    Log::info("Skipping month $monthIndex for row $i: value is $monthValue");
                    continue;
                }

                if (!is_numeric($monthValue)) {
                    $errors[] = "Invalid numeric value for month $monthIndex in row $i of sheet $sheetName: value=$monthValue";
                    Log::warning("Invalid numeric value for month $monthIndex in row $i: value=$monthValue");
                    continue;
                }

                // Konversi angka bulan ke nama bulan
                $monthName = $monthNumberToName[$monthIndex + 1] ?? 'Unknown';

                $model::create([
                    'sub_id' => $sub_id,
                    'purpose' => $purpose,
                    'acc_id' => $acc_id,
                    'itm_id' => $itm_id,
                    'description' => $description,
                    'beneficiary' => $beneficiary,
                    // 'quantity' => $quantity ? (float)$quantity : null,
                    'price' => (float)$monthValue,
                    'amount' => $amount ? (float)$amount : null,
                    'wct_id' => $wct_id,
                    'dpt_id' => $dpt_id,
                    // 'bdc_id' => $bdc_id,
                    'month' => $monthName,
                    'status' => 1,
                ]);
                
                Log::info("Created record for sub_id: $sub_id, month: $monthName, value: $monthValue");
                $processedRows++;
            }
                                        } elseif ($template === 'training') {
            [$no, $participant, $jenis_training, $quantity, $price, $wct_id, $dpt_id] = array_slice($row, 0, 7);
            
            // Hapus validasi price karena di template training, price sebenarnya adalah nilai per unit
            // dan nilai aktual ada di kolom bulanan
            $price = (float)$price; // Ini adalah harga per unit
            
            // Validasi department dengan multiple department untuk 4131 dan 4111
            if ($userDept === '4131' && in_array($dpt_id, ['4131', '1111', '1131', '1151', '1211', '1231', '7111'])) {
                Log::info("GA (4131) uploading untuk dpt_id $dpt_id diizinkan pada baris $i di sheet $sheetName");
            } elseif ($userDept === '4111' && in_array($dpt_id, ['4111', '1116', '1140', '1160', '1224', '1242', '7111'])) {
                Log::info("4111 uploading untuk dpt_id $dpt_id diizinkan pada baris $i di sheet $sheetName");
            } elseif ($dpt_id !== $userDept) {
                $errors[] = "Invalid dpt_id in row $i of sheet $sheetName: Expected $userDept, got $dpt_id";
                Log::warning("Invalid dpt_id in row $i of sheet $sheetName: Expected $userDept, got $dpt_id");
                continue;
            }
            
            $requiredFields = [
                'participant' => $participant,
                'jenis_training' => $jenis_training,
                'quantity' => $quantity,
                'price' => $price, // Harga per unit
                'dpt_id' => $dpt_id,
            ];

            foreach ($requiredFields as $fieldName => $value) {
                if (is_null($value) || $value === '' || trim($value) === '') {
                    $errors[] = "Invalid $fieldName in row $i of sheet $sheetName: $fieldName is empty or null";
                    Log::warning("Invalid $fieldName in row $i of sheet $sheetName: $fieldName is empty or null");
                    continue 2;
                }
            }

            // Validasi numeric fields
            if (!is_numeric($quantity) || !is_numeric($price)) {
                $errors[] = "Invalid numeric value in row $i of sheet $sheetName: quantity=$quantity, price=$price";
                Log::warning("Invalid numeric value in row $i: quantity=$quantity, price=$price");
                continue;
            }

            Log::info("Processing row $i in sheet $sheetName: participant=$participant");

            // Process each month - mulai dari kolom 8 (Jan), bukan 7
            foreach (array_keys($months) as $index => $monthIndex) {
                $monthValue = $row[7 + $index] ?? 0; // Mulai dari kolom 8 (index 7)
                if ($monthValue == 0 || $monthValue === null || trim($monthValue) === '') {
                    Log::info("Skipping month $monthIndex for row $i: value is $monthValue");
                    continue;
                }

                if (!is_numeric($monthValue)) {
                    $errors[] = "Invalid numeric value for month $monthIndex in row $i of sheet $sheetName: value=$monthValue";
                    Log::warning("Invalid numeric value for month $monthIndex in row $i: value=$monthValue");
                    continue;
                }

                // Konversi angka bulan ke nama bulan
                $monthName = $monthNumberToName[$monthIndex + 1] ?? 'Unknown';

                $model::create([
                    'sub_id' => $sub_id,
                    'purpose' => $purpose,
                    'acc_id' => $acc_id,
                    'participant' => $participant,
                    'jenis_training' => $jenis_training,
                    'quantity' => (float)$quantity,
                    'price' => (float)$monthValue, // Gunakan nilai bulanan, bukan price dari template
                    'wct_id' => $wct_id,
                    'dpt_id' => $dpt_id,
                    'month' => $monthName,
                    'status' => 1,
                ]);
                Log::info("Created record for sub_id: $sub_id, month: $monthName, value: $monthValue");
                $processedRows++;
            }
                                        } elseif ($template === 'recruitment') {
                                            [$no, $itm_id, $description, $position, $price, $wct_id, $dpt_id] = array_slice($row, 0, 7);
                                            $amount = $row[19] ?? null;

                                            // if (strtoupper(trim($item_type)) === 'GID') {
                                            //     $itemExists = Item::where('itm_id', $itm_id)->exists();
                                            //     if (!$itemExists) {
                                            //         $errors[] = "GID item not found in row $i of sheet $sheetName: itm_id=$itm_id";
                                            //         Log::warning("GID item not found in row $i of sheet $sheetName: itm_id=$itm_id");
                                            //         continue; // Skip this row
                                            //     }
                                            // } elseif (strtoupper(trim($item_type)) === 'Non-GID') {
                                            //     $errors[] = "Invalid Item Type in row $i of sheet $sheetName: expected 'GID' or 'Non-GID', got '$item_type'";
                                            //     Log::warning("Invalid Item Type in row $i of sheet $sheetName: expected 'GID' or 'Non-GID', got '$item_type'");
                                            //     continue; // Skip this row
                                            // }

                                            // Validasi department dengan multiple department untuk 4131 dan 4111
                                            if ($userDept === '4131' && in_array($dpt_id, ['4131', '1111', '1131', '1151', '1211', '1231', '7111'])) {
                                                Log::info("GA (4131) uploading untuk dpt_id $dpt_id diizinkan pada baris $i di sheet $sheetName");
                                            } elseif ($userDept === '4111' && in_array($dpt_id, ['4111', '1116', '1140', '1160', '1224', '1242', '7111'])) {
                                                Log::info("4111 uploading untuk dpt_id $dpt_id diizinkan pada baris $i di sheet $sheetName");
                                            } elseif ($dpt_id !== $userDept) {
                                                $errors[] = "Invalid dpt_id in row $i of sheet $sheetName: Expected $userDept, got $dpt_id";
                                                Log::warning("Invalid dpt_id in row $i of sheet $sheetName: Expected $userDept, got $dpt_id");
                                                continue;
                                            }
                                            $requiredFields = [
                                                'itm_id' => $itm_id,
                                                'description' => $description,
                                                'position' => $position,
                                                // 'quantity' => $quantity,
                                                // 'price' => $price,
                                                'amount' => $amount,
                                                'dpt_id' => $dpt_id,
                                                // 'bdc_id' => $bdc_id,
                                            ];

                                            foreach ($requiredFields as $fieldName => $value) {
                                                if (is_null($value) || $value === '' || trim($value) === '') {
                                                    $errors[] = "Invalid $fieldName in row $i of sheet $sheetName: $fieldName is empty or null";
                                                    Log::warning("Invalid $fieldName in row $i of sheet $sheetName: $fieldName is empty or null");
                                                    continue; // Lewati iterasi foreach terluar (seluruh baris)
                                                }
                                            }

                                            // $price = (float)$price;
                                            // // Validasi quantity, price, dan amount harus numerik
                                            // if (!is_numeric($price)) {
                                            //     $errors[] = "Invalid numeric value in row $i of sheet $sheetName:price=$price";
                                            //     Log::warning("Invalid numeric value in row $i: price=$price, ");
                                            //     continue;
                                            // }

                                            Log::info("Processing row $i in sheet $sheetName with itm_id: $itm_id");

                                            foreach (array_keys($months) as $index => $monthIndex) {
                        $monthValue = $row[7 + $index] ?? 0;
                        if ($monthValue == 0 || $monthValue === null || trim($monthValue) === '') {
                            Log::info("Skipping month $monthIndex for row $i: value is $monthValue");
                            continue;
                        }

                        if (!is_numeric($monthValue)) {
                            $errors[] = "Invalid numeric value for month $monthIndex in row $i of sheet $sheetName: value=$monthValue";
                            Log::warning("Invalid numeric value for month $monthIndex in row $i: value=$monthValue");
                            continue;
                        }

                        // Konversi angka bulan ke nama bulan
                        $monthName = $monthNumberToName[$monthIndex + 1] ?? 'Unknown'; // +1 karena array dimulai dari 1

                                                $model::create([
                                                    'sub_id' => $sub_id,
                                                    'purpose' => $purpose,
                                                    'acc_id' => $acc_id,
                                                    'itm_id' => $itm_id,
                                                    'description' => $description,
                                                    'position' => $position,
                                                    // 'quantity' => $quantity,
                                                    'price' => $monthValue,
                                                    'amount' => $amount,
                                                    'wct_id' => $wct_id,
                                                    'dpt_id' => $dpt_id,
                                                    // 'bdc_id' => $bdc_id,
                                                    'month' => $monthName,
                                                    'status' => 1,
                                                ]);
                                                Log::info("Created record for sub_id: $sub_id, month: $monthName, value: $value, itm_id: $itm_id");
                                                $processedRows++;
                                            }
                                        } elseif ($template === 'employee') {
            [$no, $type, $ledger_account, $ledger_account_description, $wct_id, $dpt_id, $lob_id, $bdc_id] = array_slice($row, 0, 8);

            // Validasi department dengan multiple department untuk 4131 dan 4111
            if ($userDept === '4131' && in_array($dpt_id, ['4131', '1111', '1131', '1151', '1211', '1231', '7111'])) {
                Log::info("GA (4131) uploading untuk dpt_id $dpt_id diizinkan pada baris $i di sheet $sheetName");
            } elseif ($userDept === '4111' && in_array($dpt_id, ['4111', '1116', '1140', '1160', '1224', '1242', '7111'])) {
                Log::info("4111 uploading untuk dpt_id $dpt_id diizinkan pada baris $i di sheet $sheetName");
            } elseif ($dpt_id !== $userDept) {
                $errors[] = "Invalid dpt_id in row $i of sheet $sheetName: Expected $userDept, got $dpt_id";
                Log::warning("Invalid dpt_id in row $i of sheet $sheetName: Expected $userDept, got $dpt_id");
                continue;
            }

            // Validasi field required
            $requiredFields = [
                'type' => $type,
                'ledger_account' => $ledger_account,
                'ledger_account_description' => $ledger_account_description,
                'dpt_id' => $dpt_id,
                'lob_id' => $lob_id,
                'bdc_id' => $bdc_id,
            ];

            foreach ($requiredFields as $fieldName => $value) {
                if (empty($value)) {
                    $errors[] = "Invalid $fieldName in row $i of sheet $sheetName: $fieldName is empty";
                    Log::warning("Invalid $fieldName in row $i of sheet $sheetName: $fieldName is empty");
                    continue 2;
                }
            }

            // Process each month - PERBAIKAN INDEX DI SINI
            foreach (array_keys($months) as $index => $monthIndex) {
                $monthValue = $row[8 + $index] ?? 0; // Diubah dari 5 menjadi 8
                
                if ($monthValue == 0 || $monthValue === null || trim($monthValue) === '') {
                    Log::info("Skipping month $monthIndex for row $i: value is $monthValue");
                    continue;
                }

                if (!is_numeric($monthValue)) {
                    $errors[] = "Invalid numeric value for month $monthIndex in row $i of sheet $sheetName: value=$monthValue";
                    Log::warning("Invalid numeric value for month $monthIndex in row $i: value=$monthValue");
                    continue;
                }

                // Konversi angka bulan ke nama bulan
                $monthName = $monthNumberToName[$monthIndex + 1] ?? 'Unknown';

                $model::create([
                    'sub_id' => $sub_id,
                    'purpose' => $purpose,
                    'acc_id' => $acc_id,
                    'ledger_account' => $ledger_account,
                    'ledger_account_description' => $ledger_account_description,
                    'price' => (float)$monthValue,
                    'wct_id' => $wct_id,
                    'dpt_id' => $dpt_id,
                    'lob_id' => $lob_id,
                    'bdc_id' => $bdc_id,
                    'month' => $monthName,
                    'status' => 1,
                ]);
                $processedRows++;
                Log::info("Created employee record for type: $type, month: $monthName, value: $monthValue");
            }
                                        } elseif ($template === 'purchase') {
                                            [$no, $itm_id, $business_partner, $description, $wct_id, $dpt_id, $lob_id, $bdc_id] = array_slice($row, 0, 8);
                                            // if (!is_numeric($quantity) || !is_numeric($price) || !is_numeric($amount)) {
                                            //     Log::warning("Invalid quantity, price, or amount in row $i: quantity=$quantity, price=$price, amount=$amount");
                                            //     continue;
                                            // }
                                            // if (strtoupper(trim($item_type)) === 'GID') {
                                            //     $itemExists = Item::where('itm_id', $itm_id)->exists();
                                            //     if (!$itemExists) {
                                            //         $errors[] = "GID item not found in row $i of sheet $sheetName: itm_id=$itm_id";
                                            //         Log::warning("GID item not found in row $i of sheet $sheetName: itm_id=$itm_id");
                                            //         continue; // Skip this row
                                            //     }
                                            // } elseif (strtoupper(trim($item_type)) !== 'Non-GID') {
                                            //     $errors[] = "Invalid Item Type in row $i of sheet $sheetName: expected 'GID' or 'Non-GID', got '$item_type'";
                                            //     Log::warning("Invalid Item Type in row $i of sheet $sheetName: expected 'GID' or 'Non-GID', got '$item_type'");
                                            //     continue; // Skip this row
                                            // }

                                            // Validasi department dengan multiple department untuk 4131 dan 4111
                                            if ($userDept === '4131' && in_array($dpt_id, ['4131', '1111', '1131', '1151', '1211', '1231', '7111'])) {
                                                Log::info("GA (4131) uploading untuk dpt_id $dpt_id diizinkan pada baris $i di sheet $sheetName");
                                            } elseif ($userDept === '4111' && in_array($dpt_id, ['4111', '1116', '1140', '1160', '1224', '1242', '7111'])) {
                                                Log::info("4111 uploading untuk dpt_id $dpt_id diizinkan pada baris $i di sheet $sheetName");
                                            } elseif ($dpt_id !== $userDept) {
                                                $errors[] = "Invalid dpt_id in row $i of sheet $sheetName: Expected $userDept, got $dpt_id";
                                                Log::warning("Invalid dpt_id in row $i of sheet $sheetName: Expected $userDept, got $dpt_id");
                                                continue;
                                            }
                                            $requiredFields = [
                                                'itm_id' => $itm_id,
                                                'business_partner' => $business_partner,
                                                'description' => $description,
                                                // 'unit' => $unit,
                                                // 'quantity' => $quantity,
                                                'price' => $price,
                                                // 'amount' => $amount,
                                                'dpt_id' => $dpt_id,
                                                'lob_id' => $lob_id,
                                                'bdc_id' => $bdc_id,
                                            ];

                                            foreach ($requiredFields as $fieldName => $value) {
                                                if (is_null($value) || $value === '' || trim($value) === '') {
                                                    $errors[] = "Invalid $fieldName in row $i of sheet $sheetName: $fieldName is empty or null";
                                                    Log::warning("Invalid $fieldName in row $i of sheet $sheetName: $fieldName is empty or null");
                                                    continue 2; // Lewati iterasi foreach terluar (seluruh baris)
                                                }
                                            }

                                            $price = (float)$price;
                                            // Validasi quantity, price, dan amount harus numerik
                                            if (!is_numeric($price)) {
                                                $errors[] = "Invalid numeric value in row $i of sheet $sheetName:  price=$price";
                                                Log::warning("Invalid numeric value in row $i: price=$price");
                                                continue;
                                            }

                                            Log::info("Processing row $i in sheet $sheetName with itm_id: $itm_id");

                                            foreach (array_keys($months) as $index => $monthIndex) {
                        $monthValue = $row[8 + $index] ?? 0;
                        if ($monthValue == 0 || $monthValue === null || trim($monthValue) === '') {
                            Log::info("Skipping month $monthIndex for row $i: value is $monthValue");
                            continue;
                        }

                        if (!is_numeric($monthValue)) {
                            $errors[] = "Invalid numeric value for month $monthIndex in row $i of sheet $sheetName: value=$monthValue";
                            Log::warning("Invalid numeric value for month $monthIndex in row $i: value=$monthValue");
                            continue;
                        }

                        // Konversi angka bulan ke nama bulan
                        $monthName = $monthNumberToName[$monthIndex + 1] ?? 'Unknown'; // +1 karena array dimulai dari 1


                                                $model::create([
                                                    'sub_id' => $sub_id,
                                                    'purpose' => $purpose,
                                                    'acc_id' => $acc_id,
                                                    'itm_id' => $itm_id,
                                                    'business_partner' => $business_partner,
                                                    'description' => $description,
                                                    // 'unit' => $unit,
                                                    // 'quantity' => $quantity,
                                                    'price' => (float)$monthValue,
                                                    // 'amount' => $amount,
                                                    'wct_id' => $wct_id,
                                                    'dpt_id' => $dpt_id,
                                                    'lob_id' => $lob_id,
                                                    'bdc_id' => $bdc_id,
                                                    'month' => $monthName,
                                                    'status' => 1,
                                                ]);
                                                Log::info("Created record for sub_id: $sub_id, month: $monthName, value: $value");
                                                $processedRows++;
                                            }
                                        }
                                    } catch (\Exception $e) {
                                        $monthName = 'Unknown'; // Tambahkan ini
                                        Log::error("Failed to create record for sub_id: $sub_id, month: $monthName, sheet: $sheetName, error: " . $e->getMessage());

                                    }
                                }
                            }

                            // Respons berdasarkan hasil pemrosesan
                            if ($processedRows === 0) {
                                Log::warning('No rows were processed', ['sheets_processed' => $processedSheets]);
                                return response()->json([
                                    'message' => 'No data was processed. Please check the file content or sheet names.',
                                    'sheets_processed' => $processedSheets,
                                    'processed_rows' => $processedRows
                                ], 400);
                            }

                            Log::info('Upload completed successfully', [
                                'sheets_processed' => $processedSheets,
                                'processed_rows' => $processedRows
                            ]);

                            return response()->json([
                                'message' => 'Data uploaded successfully.',
                                'sheets_processed' => $processedSheets,
                                'processed_rows' => $processedRows
                            ]);
    }


    // public function uploadExpend(Request $request)
    // {
    //     // Log request masuk
    //     Log::info('Upload request received', [
    //         'file' => $request->file('file') ? $request->file('file')->getClientOriginalName() : 'No file',
    //         'purpose' => $request->input('purpose'),
    //         'timestamp' => now()->toDateTimeString()
    //     ]);

    //     // Validasi input
    //     $request->validate([
    //         'file' => 'required|file|mimes:xlsx,xls',
    //         'purpose' => 'required|string'
    //     ]);

    //     // Check if user is authenticated
    //     if (!Auth::check()) {
    //         Log::error('User not authenticated');
    //         return response()->json(['message' => 'User not authenticated'], 401);
    //     }

    //     $file = $request->file('file');
    //     $purpose = $request->input('purpose');
    //     $npk = Auth::user()->npk; // Get NPK of logged-in user
    //     $userDept = Auth::user()->dept; // Get NPK of logged-in user

    //     // Load file Excel
    //     try {
    //         $spreadsheet = IOFactory::load($file);
    //         Log::info('Excel file loaded successfully', ['sheets' => $spreadsheet->getSheetNames()]);
    //     } catch (\Exception $e) {
    //         Log::error('Failed to load Excel file', ['error' => $e->getMessage()]);
    //         return response()->json(['message' => 'Failed to load Excel file: ' . $e->getMessage()], 500);
    //     }

    //     // Mapping sheet ke prefix, acc_id, model, dan template
    //     $sheetMappings = [
    //         'CAPITAL EXPENDITURE' => [
    //             'prefix' => 'EXP',
    //             'acc_id' => 'CAPEX',
    //             'model' => BudgetPlan::class,
    //             'template' => 'expenditure'
    //         ]
    //     ];

    //     $months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    //     $processedRows = 0;
    //     $processedSheets = [];

    //     // Iterasi setiap sheet di file Excel
    //     foreach ($spreadsheet->getSheetNames() as $sheetName) {
    //         if (!isset($sheetMappings[$sheetName])) {
    //             Log::warning("Sheet '$sheetName' not found in sheetMappings");
    //             continue;
    //         }

    //         $sheetConfig = $sheetMappings[$sheetName];
    //         $prefix = $sheetConfig['prefix'];
    //         $acc_id = $sheetConfig['acc_id'];
    //         $model = $sheetConfig['model'];
    //         $template = $sheetConfig['template'];

    //         Log::info("Checking sheet: $sheetName, template: $template, model: $model");

    //         // Load data dari sheet
    //         $sheet = $spreadsheet->getSheetByName($sheetName);
    //         $data = $sheet->toArray();
    //         Log::info("Sheet '$sheetName' has " . count($data) . " rows");

    //         // Check if sheet has valid data rows (excluding header)
    //         $hasValidData = false;
    //         foreach ($data as $i => $row) {
    //             if ($i === 0) {
    //                 Log::info("Skipping header row for sheet: $sheetName");
    //                 continue; // Skip header
    //             }

    //             // Check if row has any non-empty values
    //             $rowHasData = array_filter($row, function ($value) {
    //                 return !is_null($value) && $value !== '';
    //             });

    //             if (!empty($rowHasData)) {
    //                 $hasValidData = true;
    //                 break; // Found at least one valid data row
    //             }
    //         }

    //         if (!$hasValidData) {
    //             Log::info("Sheet '$sheetName' has no valid data rows, skipping processing");
    //             continue;
    //         }

    //         // Generate sub_id sekali per worksheet dengan data valid
    //         $lastRecord = $model::where('sub_id', 'like', "$prefix%")
    //             ->orderBy('sub_id', 'desc')
    //             ->first();
    //         $nextNumber = $lastRecord ? ((int)str_replace($prefix, '', $lastRecord->sub_id) + 1) : 1;
    //         $sub_id = $prefix . str_pad($nextNumber, 7, '0', STR_PAD_LEFT);
    //         Log::info("Generated sub_id for sheet $sheetName: $sub_id");

    //         // Create approval record for the sub_id
    //         try {
    //             Approval::create([
    //                 'approve_by' => $npk,
    //                 'sub_id' => $sub_id,
    //                 'status' => 1,
    //                 'created_at' => now(),
    //                 'updated_at' => now(),
    //             ]);
    //             Log::info("Created approval record for sub_id: $sub_id, approve_by: $npk");
    //         } catch (\Exception $e) {
    //             Log::error("Failed to create approval record for sub_id: $sub_id, error: " . $e->getMessage());
    //             return response()->json(['message' => 'Failed to create approval record: ' . $e->getMessage()], 500);
    //         }

    //         $processedSheets[] = $sheetName;

    //         // Process data rows
    //         foreach ($data as $i => $row) {
    //             if ($i === 0) {
    //                 Log::info("Skipping header row for sheet: $sheetName");
    //                 continue; // Skip header
    //             }

    //             // Validasi jumlah kolom
    //             $expectedColumns = $this->getExpendColumns($template, $months);
    //             if (count($row) < $expectedColumns) {
    //                 Log::warning("Invalid column count in row $i of sheet $sheetName: expected at least $expectedColumns, got " . count($row));
    //                 continue;
    //             }

    //             Log::info("Processing row $i in sheet '$sheetName': " . json_encode($row));

    //             try {
    //                 if ($template === 'expenditure') {
    //                     [$no, $itm_id, $asset_class, $prioritas, $alasan, $keterangan, $quantity, $price, $amount, $wct_id, $dpt_id] = array_slice($row, 0, 11);


    //                     // Validasi kolom wajib tidak boleh kosong
    //                     if ($dpt_id !== $userDept) {
    //                         $errors[] = "Invalid dpt_id in row $i of sheet $sheetName: Expected $userDept, got $dpt_id";
    //                         Log::warning("Invalid dpt_id in row $i of sheet $sheetName: Expected $userDept, got $dpt_id");
    //                         continue; // Skip this row
    //                     }
    //                     $requiredFields = [
    //                         'itm_id' => $itm_id,
    //                         'asset_class' => $asset_class,
    //                         'prioritas' => $prioritas,
    //                         'alasan' => $alasan,
    //                         'keterangan' => $keterangan,
    //                         'quantity' => $quantity,
    //                         'price' => $price,
    //                         'amount' => $amount,
    //                         'dpt_id' => $dpt_id,
    //                         // 'bdc_id' => $bdc_id,
    //                     ];

    //                     foreach ($requiredFields as $fieldName => $value) {
    //                         if (is_null($value) || $value === '' || trim($value) === '') {
    //                             $errors[] = "Invalid $fieldName in row $i of sheet $sheetName: $fieldName is empty or null";
    //                             Log::warning("Invalid $fieldName in row $i of sheet $sheetName: $fieldName is empty or null");
    //                             continue 2; // Lewati iterasi foreach terluar (seluruh baris)
    //                         }
    //                     }

    //                     // Validasi quantity, price, dan amount harus numerik
    //                     if (!is_numeric($quantity) || !is_numeric($price) || !is_numeric($amount)) {
    //                         $errors[] = "Invalid numeric value in row $i of sheet $sheetName: quantity=$quantity, price=$price, amount=$amount";
    //                         Log::warning("Invalid numeric value in row $i: quantity=$quantity, price=$price, amount=$amount");
    //                         continue;
    //                     }

    //                     Log::info("Processing row $i in sheet $sheetName with itm_id: $itm_id");

    //                     foreach ($months as $index => $month) {
    //                         $value = $row[9 + $index] ?? 0;
    //                         if ($value == 0 || $value === null || trim($value) === '') {
    //                             Log::info("Skipping month $month for row $i: value is $value");
    //                             continue;
    //                         }

    //                         $model::create([
    //                             'sub_id' => $sub_id,
    //                             'purpose' => $purpose,
    //                             'acc_id' => $acc_id,
    //                             'itm_id' => $itm_id,
    //                         'asset_class' => $asset_class,
    //                         'prioritas' => $prioritas,
    //                         'alasan' => $alasan,
    //                         'keterangan' => $keterangan,
    //                             'quantity' => $quantity,
    //                             'price' => $price,
    //                             'amount' => $amount,
    //                             'wct_id' => $wct_id,
    //                             'dpt_id' => $dpt_id,
    //                             // 'bdc_id' => $bdc_id,
    //                             'month' => $month,
    //                             'status' => 1,
    //                         ]);
    //                         Log::info("Created record for sub_id: $sub_id, month: $month, value: $value, itm_id: $itm_id");
    //                         $processedRows++;
    //                     }
    //                 }
    //             } catch (\Exception $e) {
    //                 Log::error("Failed to create record for sub_id: $sub_id, month: $month, sheet: $sheetName, error: " . $e->getMessage());
    //             }
    //         }
    //     }

    //     // Respons berdasarkan hasil pemrosesan
    //     if ($processedRows === 0) {
    //         Log::warning('No rows were processed', ['sheets_processed' => $processedSheets]);
    //         return response()->json([
    //             'message' => 'No data was processed. Please check the file content or sheet names.',
    //             'sheets_processed' => $processedSheets,
    //             'processed_rows' => $processedRows
    //         ], 400);
    //     }

    //     Log::info('Upload completed successfully', [
    //         'sheets_processed' => $processedSheets,
    //         'processed_rows' => $processedRows
    //     ]);

    //     return response()->json([
    //         'message' => 'Data uploaded successfully.',
    //         'sheets_processed' => $processedSheets,
    //         'processed_rows' => $processedRows
    //     ]);
    // }

    // public function uploadExpend(Request $request)
    // {
    //     Log::info('Upload request received', [
    //         'file' => $request->file('file') ? $request->file('file')->getClientOriginalName() : 'No file',
    //         'purpose' => $request->input('purpose'),
    //         'timestamp' => now()->toDateTimeString()
    //     ]);

    //     $request->validate([
    //         'file' => 'required|file|mimes:xlsx,xls',
    //         'purpose' => 'required|string'
    //     ]);

    //     if (!Auth::check()) {
    //         Log::error('User not authenticated');
    //         return response()->json(['message' => 'User not authenticated'], 401);
    //     }

    //     $file = $request->file('file');
    //     $purpose = $request->input('purpose');
    //     $npk = Auth::user()->npk;

    //     try {
    //         $spreadsheet = IOFactory::load($file);
    //         Log::info('Excel file loaded successfully', ['sheets' => $spreadsheet->getSheetNames()]);
    //     } catch (\Exception $e) {
    //         Log::error('Failed to load Excel file', ['error' => $e->getMessage()]);
    //         return response()->json(['message' => 'Failed to load Excel file: ' . $e->getMessage()], 500);
    //     }

    //     $sheetMappings = [
    //         'CAPITAL EXPENDITURE' => [
    //             'prefix' => 'EXP',
    //             'acc_id' => 'CAPEX',
    //             'model' => BudgetPlan::class,
    //             'template' => 'expenditure'
    //         ]
    //     ];

    //     $months = [
    //         'January',
    //         'February',
    //         'March',
    //         'April',
    //         'May',
    //         'June',
    //         'July',
    //         'August',
    //         'September',
    //         'October',
    //         'November',
    //         'December'
    //     ];

    //     $processedRows = 0;
    //     $processedSheets = [];

    //     foreach ($spreadsheet->getSheetNames() as $sheetName) {
    //         if (!isset($sheetMappings[$sheetName])) {
    //             Log::warning("Sheet '$sheetName' not found in sheetMappings");
    //             continue;
    //         }

    //         $sheetConfig = $sheetMappings[$sheetName];
    //         $prefix = $sheetConfig['prefix'];
    //         $acc_id = $sheetConfig['acc_id'];
    //         $model = $sheetConfig['model'];
    //         $template = $sheetConfig['template'];

    //         $sheet = $spreadsheet->getSheetByName($sheetName);
    //         $data = $sheet->toArray();

    //         // Ambil nilai WORKCENTER dan DEPARTMENT dari sel B1 dan B2
    //         $workcenter = trim($sheet->getCell('B4')->getFormattedValue());
    //         $department = trim($sheet->getCell('B5')->getFormattedValue());

    //         Log::info("Loaded WORKCENTER: $workcenter, DEPARTMENT: $department");

    //         $hasValidData = false;
    //         foreach ($data as $i => $row) {
    //             if ($i === 0) continue;
    //             $rowHasData = array_filter($row, fn($value) => !is_null($value) && $value !== '');
    //             if (!empty($rowHasData)) {
    //                 $hasValidData = true;
    //                 break;
    //             }
    //         }

    //         if (!$hasValidData) {
    //             Log::info("Sheet '$sheetName' has no valid data rows, skipping");
    //             continue;
    //         }

    //         $lastRecord = $model::where('sub_id', 'like', "$prefix%")
    //             ->orderBy('sub_id', 'desc')
    //             ->first();
    //         $nextNumber = $lastRecord ? ((int)str_replace($prefix, '', $lastRecord->sub_id) + 1) : 1;
    //         $sub_id = $prefix . str_pad($nextNumber, 7, '0', STR_PAD_LEFT);

    //         try {
    //             Approval::create([
    //                 'approve_by' => $npk,
    //                 'sub_id' => $sub_id,
    //                 'status' => 1,
    //                 'created_at' => now(),
    //                 'updated_at' => now(),
    //             ]);
    //         } catch (\Exception $e) {
    //             Log::error("Failed to create approval record: " . $e->getMessage());
    //             return response()->json(['message' => 'Failed to create approval record: ' . $e->getMessage()], 500);
    //         }

    //         $processedSheets[] = $sheetName;

    //         foreach ($data as $i => $row) {
    //             if ($i === 0) continue;

    //             $expectedColumns = $this->getExpendColumns($template, $months);
    //             if (count($row) < $expectedColumns) {
    //                 Log::warning("Invalid column count in row $i of sheet $sheetName");
    //                 continue;
    //             }

    //             try {
    //                 if ($template === 'expenditure') {
    //                     [$no, $itm_id, $asset_class, $prioritas, $alasan, $keterangan, $quantity, $price, $amount] = array_slice($row, 0, 9);

    //                     if (!is_numeric($quantity) || !is_numeric($price)) {
    //                         Log::warning("Invalid quantity/price in row $i: quantity=$quantity, price=$price");
    //                         continue;
    //                     }

    //                     foreach ($months as $index => $month) {
    //                         $value = $row[9 + $index] ?? 0;
    //                         if ($value == 0 || $value === null) continue;

    //                         $model::create([
    //                             'sub_id' => $sub_id,
    //                             'purpose' => $purpose,
    //                             'acc_id' => $acc_id,
    //                             'itm_id' => $itm_id,
    //                             'asset_class' => $asset_class,
    //                             'prioritas' => $prioritas,
    //                             'alasan' => $alasan,
    //                             'keterangan' => $keterangan,
    //                             'quantity' => $quantity,
    //                             'price' => $price,
    //                             'amount' => $amount,
    //                             'wct_id' => $workcenter,
    //                             'dpt_id' => $department,
    //                             'month' => $month,
    //                             'status' => 1,
    //                         ]);

    //                         $processedRows++;
    //                     }
    //                 }
    //             } catch (\Exception $e) {
    //                 Log::error("Failed to insert row $i: " . $e->getMessage());
    //             }
    //         }
    //     }

    //     if ($processedRows === 0) {
    //         return response()->json([
    //             'message' => 'No data was processed. Please check the file content or sheet names.',
    //             'sheets_processed' => $processedSheets,
    //             'processed_rows' => $processedRows
    //         ], 400);
    //     }

    //     return response()->json([
    //         'message' => 'Data uploaded successfully.',
    //         'sheets_processed' => $processedSheets,
    //         'processed_rows' => $processedRows
    //     ]);
    // }

    public function uploadExpend(Request $request)
{
    Log::info('UploadExpend request received', [
        'template' => $request->file('template') ? $request->file('template')->getClientOriginalName() : 'No template',
        'proposal' => $request->file('proposal') ? $request->file('proposal')->getClientOriginalName() : 'No proposal',
        'purpose' => $request->input('purpose'),
        'timestamp' => now()->toDateTimeString()
    ]);

    $request->validate([
        'template' => 'required|file|mimes:xlsx,xls|max:2048',
        'proposal' => 'required|mimes:pdf|max:5120',
        'purpose' => 'required|string|max:255',
        'upload_type' => 'required|in:expenditure'
    ]);

    if (!Auth::check()) {
        Log::error('User not authenticated');
        return response()->json(['message' => 'User not authenticated'], 401);
    }

    $templateFile = $request->file('template');
    $proposalFile = $request->file('proposal');
    $purpose = $request->input('purpose');
    $npk = Auth::user()->npk;
    $deptId = Auth::user()->dept;

    // Process PDF proposal
    $proposalFileName = $proposalFile->getClientOriginalName();
    $proposalFileContent = file_get_contents($proposalFile->getRealPath());
    $proposalBase64 = base64_encode($proposalFileContent);
    $pdfData = [
        'name' => $proposalFileName,
        'content' => $proposalBase64
    ];
    $pdfAttachments = [$pdfData];
    session()->put('pdf_attachment', $pdfAttachments);

    // Process Excel file
    try {
        $spreadsheet = IOFactory::load($templateFile->getRealPath());
        Log::info('Excel file loaded successfully', ['sheets' => $spreadsheet->getSheetNames()]);
    } catch (\Exception $e) {
        Log::error('Failed to load Excel file', ['error' => $e->getMessage()]);
        return response()->json(['message' => 'Failed to load Excel file: ' . $e->getMessage()], 500);
    }

    $sheetMappings = [
        'CAPITAL EXPENDITURE' => [
            'prefix' => 'EXP',
            'acc_id' => 'CAPEX',
            'model' => BudgetPlan::class,
            'template' => 'expenditure'
        ]
    ];

    $months = [
        1 => 'January',
        2 => 'February',
        3 => 'March',
        4 => 'April',
        5 => 'May',
        6 => 'June',
        7 => 'July',
        8 => 'August',
        9 => 'September',
        10 => 'October',
        11 => 'November',
        12 => 'December'
    ];

    $processedRows = 0;
    $processedSheets = [];
    $errors = [];

    foreach ($spreadsheet->getSheetNames() as $sheetName) {
        if (!isset($sheetMappings[$sheetName])) {
            Log::warning("Sheet '$sheetName' not found in sheetMappings");
            continue;
        }

        $sheetConfig = $sheetMappings[$sheetName];
        $prefix = $sheetConfig['prefix'];
        $acc_id = $sheetConfig['acc_id'];
        $model = $sheetConfig['model'];
        $template = $sheetConfig['template'];

        $sheet = $spreadsheet->getSheetByName($sheetName);
        $data = $sheet->toArray();

        // Get WORKCENTER and DEPARTMENT
        $workcenter = trim($sheet->getCell('B4')->getFormattedValue());
        $department = trim($sheet->getCell('B5')->getFormattedValue());

        // Validasi departemen
        if ($deptId === '4131' && in_array($department, ['4131', '1111', '1131', '1151', '1211', '1231', '7111'])) {
            Log::info("GA (4131) uploading untuk dpt_id $department diizinkan pada sheet $sheetName");
        } elseif ($deptId === '4111' && in_array($department, ['4111', '1116', '1140', '1160', '1224', '1242', '7111'])) {
            Log::info("4111 uploading untuk dpt_id $department diizinkan pada sheet $sheetName");
        } elseif ($department !== $deptId) {
            Log::warning("Invalid dpt_id pada sheet $sheetName: Diharapkan $deptId, mendapat $department");
            $errors[] = "Invalid dpt_id pada sheet $sheetName: Diharapkan $deptId, mendapat $department";
            continue;
        }

        Log::info("Loaded WORKCENTER: $workcenter, DEPARTMENT: $department");

        $hasValidData = false;
        foreach ($data as $i => $row) {
            if ($i === 0) continue;
            $rowHasData = array_filter($row, fn($value) => !is_null($value) && $value !== '');
            if (!empty($rowHasData)) {
                $hasValidData = true;
                break;
            }
        }

        if (!$hasValidData) {
            Log::info("Sheet '$sheetName' has no valid data rows, skipping");
            continue;
        }

        $lastRecord = $model::where('sub_id', 'like', "$prefix%")->orderBy('sub_id', 'desc')->first();
        $nextNumber = $lastRecord ? ((int)str_replace($prefix, '', $lastRecord->sub_id) + 1) : 1;
        $sub_id = $prefix . str_pad($nextNumber, 7, '0', STR_PAD_LEFT);

        try {
            Approval::create([
                'approve_by' => $npk,
                'sub_id' => $sub_id,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to create approval record: " . $e->getMessage());
            return response()->json(['message' => 'Failed to create approval record: ' . $e->getMessage()], 500);
        }

        $processedSheets[] = $sheetName;

        foreach ($data as $i => $row) {
            if ($i === 0) continue; // Skip header row

            // Periksa apakah baris kosong atau tidak valid
            $rowData = array_slice($row, 0, 9); // Hanya ambil kolom 1-9 (No sampai Amount)
            $isEmptyRow = empty(array_filter($rowData, fn($value) => !is_null($value) && $value !== ''));

            if ($isEmptyRow) {
                continue; // Skip empty rows
            }

            // Validasi struktur kolom
            if (count($row) < 21) {
                Log::warning("Invalid column count in row $i of sheet $sheetName");
                continue;
            }

            try {
                // Ambil data sesuai struktur kolom yang benar
                $no = $row[0] ?? null;
                $itm_id = $row[1] ?? null;
                $asset_class = $row[2] ?? null;
                $prioritas = $row[3] ?? null;
                $alasan = $row[4] ?? null;
                $keterangan = $row[5] ?? null;
                $quantity = $row[6] ?? 0;
                $price = $row[7] ?? 0;
                $amount = $row[8] ?? 0;

                // Validasi data required
                if (empty($itm_id) || empty($asset_class) || empty($prioritas)) {
                    Log::warning("Missing required data in row $i: itm_id=$itm_id, asset_class=$asset_class, prioritas=$prioritas");
                    continue;
                }

                // Validasi data numerik
                if (!is_numeric($quantity) || $quantity <= 0 || 
                    !is_numeric($price) || $price <= 0 || 
                    !is_numeric($amount) || $amount <= 0) {
                    Log::warning("Invalid numeric data in row $i: quantity=$quantity, price=$price, amount=$amount");
                    continue;
                }

                // Process monthly values
                for ($monthIndex = 0; $monthIndex < 12; $monthIndex++) {
                    $monthValue = $row[9 + $monthIndex] ?? 0;

                    // Skip jika nilai bulan 0, kosong, atau null
                    if ($monthValue == 0 || $monthValue === null || trim($monthValue) === '') {
                        continue;
                    }

                    if (!is_numeric($monthValue) || $monthValue <= 0) {
                        $errors[] = "Invalid numeric value for month " . ($monthIndex + 1) . " in row $i of sheet $sheetName: value=$monthValue";
                        Log::warning("Invalid numeric value for month " . ($monthIndex + 1) . " in row $i: value=$monthValue");
                        continue;
                    }

                    // Dapatkan nama bulan yang benar
                    $monthName = $months[$monthIndex + 1] ?? 'Unknown';

                    $model::create([
                        'sub_id' => $sub_id,
                        'purpose' => $purpose,
                        'acc_id' => $acc_id,
                        'itm_id' => $itm_id,
                        'asset_class' => $asset_class,
                        'prioritas' => $prioritas,
                        'alasan' => $alasan,
                        'keterangan' => $keterangan,
                        'quantity' => (float)$quantity,
                        'price' => (float)$price,
                        'amount' => (float)$amount,
                        'wct_id' => $workcenter,
                        'dpt_id' => $department,
                        'month' => $monthName,
                        'month_value' => (float)$monthValue,
                        'status' => 1,
                        'pdf_attachment' => json_encode($pdfAttachments),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $processedRows++;
                    Log::info("Created record for month: $monthName with value: $monthValue");
                }
            } catch (\Exception $e) {
                Log::error("Failed to insert row $i: " . $e->getMessage());
                $errors[] = "Failed to insert row $i: " . $e->getMessage();
            }
        }
    }

    // Clear session data
    session()->forget(['temp_data', 'purpose', 'pdf_attachment']);

    // Log session state
    Log::info('Session data after uploadExpend', [
        'temp_data' => session('temp_data'),
        'purpose' => session('purpose'),
        'pdf_attachment' => session('pdf_attachment')
    ]);

    if ($processedRows === 0) {
        return response()->json([
            'message' => 'No data was processed. Please check the file content or sheet names.',
            'sheets_processed' => $processedSheets,
            'processed_rows' => $processedRows,
            'errors' => $errors
        ], 400);
    }

    return response()->json([
        'message' => 'Data and proposal uploaded successfully.',
        'sheets_processed' => $processedSheets,
        'processed_rows' => $processedRows,
        'errors' => $errors
    ]);
}
    /**
     * Mengembalikan jumlah kolom yang diharapkan berdasarkan template
     */
    private function getExpectedColumns($template, $months)
    {
        switch ($template) {
            case 'business':
            case 'utilities':
            case 'representation':
                return 6 + count($months) + 1; // No, Item, Description, Quantity, Price, Workcenter, Department + months + Total
            case 'general':
            case 'aftersales':
            case 'insurance':
                return 5 + count($months) + 1; // No, [specific fields], Workcenter, Department + months + Total
            case 'support':
            case 'training':
            case 'recruitment':
                return 7 + count($months) + 1; // No, Item, Description, Days, Quantity, Price, Amount, Workcenter, Department + months + Total
            default:
                return 0;
        }
    }

    private function getExpendColumns($template, $months)
{
    if ($template === 'expenditure') {
        return 9 + count($months); // 9 kolom dasar + 12 bulan
    }
    return 0;
}

    public function addItem(Request $request, $sub_id)
{
    $user = Auth::user();
    if (!$user) {
        return response()->json(['success' => false, 'message' => 'User tidak terautentikasi'], 401);
    }

    // [MODIFIKASI] Validasi input untuk memastikan data yang diperlukan ada
    $request->validate([
        'sub_id' => 'required|exists:budget_plans,sub_id',
        'acc_id' => 'required|exists:accounts,acc_id',
        'itm_id' => 'nullable|string', // [MODIFIKASI] itm_id selalu opsional
        'ins_id' => 'required_if:acc_id,FOHINSPREM,SGAINSURANCE|exists:insurance_companies,ins_id', // [MODIFIKASI] Validasi ins_id untuk FOHINSPREM dan SGAINSURANCE
        'kwh' => 'required_if:acc_id,FOHPOWER,SGAPOWER|numeric|min:0',
        'price' => 'required|numeric|min:0',
        'cur_id' => 'required|exists:currencies,cur_id',
        'wct_id' => 'nullable|exists:workcenters,wct_id', // [MODIFIKASI] wct_id opsional
        'dpt_id' => 'required|exists:departments,dpt_id',
        'month' => 'required|string|in:January,February,March,April,May,June,July,August,September,October,November,December',
        'lob_id' => 'required_if:acc_id,FOHPOWER,SGAPOWER|exists:line_of_businesses,lob_id',
        'quantity' => 'required_if:acc_id,FOHTRAINING,SGATRAINING|numeric|min:1', // [MODIFIKASI] Validasi quantity untuk pelatihan
        'participant' => 'required_if:acc_id,FOHTRAINING,SGATRAINING|string|max:255', // [MODIFIKASI] Validasi participant untuk pelatihan
        'jenis_training' => 'required_if:acc_id,FOHTRAINING,SGATRAINING|string|max:255', // [MODIFIKASI] Validasi jenis_training untuk pelatihan
        'bdc_id' => 'required_if:acc_id,PURCHASEMATERIAL|exists:budget_codes,bdc_id', // [MODIFIKASI] Ganti rnr dengan bdc_id
        'trip_propose' => 'required_if:acc_id,FOHTRAV,SGATRAV|string|max:255', // [MODIFIKASI] Validasi trip_propose untuk FOHTRAV dan SGATRAV
        'destination' => 'required_if:acc_id,FOHTRAV,SGATRAV|string|max:255', // [MODIFIKASI] Validasi destination untuk FOHTRAV dan SGATRAV
        'days' => 'required_if:acc_id,FOHTRAV,SGATRAV|numeric|min:1', // [MODIFIKASI] Validasi days untuk FOHTRAV dan SGATRAV
        'beneficiary' => 'required_if:acc_id,FOHENTERTAINT,SGAENTERTAINT|string|max:255', // [MODIFIKASI] Validasi beneficiary untuk FOHENTERTAINT dan SGAENTERTAINT
        'business_partner' => 'required_if:acc_id,PURCHASEMATERIAL|string|max:255', // [MODIFIKASI] Validasi business_partner
        'ledger_account' => 'required_if:acc_id,FOHEMPLOYCOMPDL,FOHEMPLOYCOMPIL,SGAEMPLOYCOMP|string|max:255', // [MODIFIKASI] Validasi ledger_account untuk akun employee
        'ledger_account_description' => 'required_if:acc_id,FOHEMPLOYCOMPDL,FOHEMPLOYCOMPIL,SGAEMPLOYCOMP|string|max:255', // [MODIFIKASI] Validasi ledger_account_description untuk akun employee
    ]);

    $acc_id = $request->acc_id;

    if (!$user->npk || !$user->sect || !$user->dept) {
        Log::error("User tidak memiliki atribut yang diperlukan untuk sub_id {$sub_id}");
        return response()->json(['success' => false, 'message' => 'Informasi user tidak lengkap'], 401);
    }

    // Determine status based on user role
    $status = 1; // Default status for regular user
    $approvalStatus = 1; // Default approval status
    $directDIC = ['4211', '6111', '6121']; // Departments that go directly to DIC

    if ($user->sect === 'Kadept') {
        $status = 2;
        $approvalStatus = 2;
    } elseif ($user->sect === 'Kadiv') {
        $status = 3;
        $approvalStatus = 3;
    } elseif ($user->sect === 'DIC') {
        $status = 4;
        $approvalStatus = 4;
    } elseif ($user->sect === 'PIC' && $user->dept === '6121') {
        $status = 5;
        $approvalStatus = 5;
    } elseif ($user->sect === 'Kadept' && $user->dept === '6121') {
        $status = 6;
        $approvalStatus = 6;
    }

    $existingRecord = BudgetPlan::where('sub_id', $sub_id)->first();

    $createdAt = $existingRecord ? $existingRecord->created_at : now();
    $submissionDept = $existingRecord ? $existingRecord->dpt_id : $request->dpt_id;

    if (!$submissionDept) {
        Log::error("Tidak ada departemen ditemukan untuk sub_id {$sub_id}");
        return response()->json(['success' => false, 'message' => 'Informasi departemen tidak ditemukan'], 400);
    }

    // [MODIFIKASI] Hitung amount berdasarkan acc_id
    $price = $request->price;
    $currency_id = $request->cur_id;

    if ($currency_id) {
        $currency = Currency::where('cur_id', $currency_id)->first();
        if ($currency && $currency->currency !== 'IDR') {
            $price = $price * $currency->nominal; // Convert to IDR
        }
    }

    // [MODIFIKASI] Hitung amount: gunakan quantity untuk FOHTRAINING dan SGATRAINING
    $amount = in_array($acc_id, ['FOHTRAV', 'SGATRAV','FOHINSPREM', 'SGAINSURANCE', 'FOHPOWER', 'SGAPOWER']) ? $price : ($request->quantity * $price);

    // Save item based on acc_id
    $submission = null;
    try {
        if (in_array($acc_id, ['SGABOOK', 'SGAREPAIR', 'SGAMARKT', 'FOHTECHDO', 'FOHRECRUITING', 'SGARECRUITING', 'SGARENT', 'SGAADVERT', 'SGACOM', 'SGAOFFICESUP', 'SGAASSOCIATION', 'SGABCHARGES', 'SGACONTRIBUTION', 'FOHPACKING', 'SGARYLT', 'FOHAUTOMOBILE', 'FOHPROF', 'FOHRENT', 'FOHTAXPUB', 'SGAAUTOMOBILE', 'SGAPROF', 'SGATAXPUB', 'SGAOUTSOURCING'])) {
            $submission = BudgetPlan::create([
                'sub_id' => $sub_id,
                'acc_id' => $acc_id,
                'purpose' => $request->purpose,
                'itm_id' => $request->itm_id,
                'description' => $request->description,
                'quantity' => $request->quantity,
                'price' => $price,
                'amount' => $amount,
                'wct_id' => $request->wct_id,
                'dpt_id' => $request->dpt_id,
                'month' => $request->month,
                'bdc_id' => $request->bdc_id,
                'status' => $status,
                'created_at' => $createdAt,
            ]);
        } elseif ($acc_id === 'PURCHASEMATERIAL') { // [MODIFIKASI] Tambah kondisi untuk PURCHASEMATERIAL
            $submission = BudgetPlan::create([
                'sub_id' => $sub_id,
                'acc_id' => $acc_id,
                'purpose' => $request->purpose,
                'itm_id' => $request->itm_id,
                'description' => $request->description,
                'price' => $price,
                'amount' => $amount,
                'wct_id' => $request->wct_id,
                'dpt_id' => $request->dpt_id,
                'month' => $request->month,
                'lob_id' => $request->lob_id,
                'bdc_id' => $request->bdc_id, // [MODIFIKASI] Simpan rnr sebagai bdc_id
                'business_partner' => $request->business_partner,
                'status' => $status,
                'created_at' => $createdAt,
            ]);
        } elseif (in_array($acc_id, ['FOHTOOLS', 'FOHFS', 'FOHINDMAT', 'FOHREPAIR'])) {
            $submission = BudgetPlan::create([
                'sub_id' => $sub_id,
                'acc_id' => $acc_id,
                'purpose' => $request->purpose,
                'itm_id' => $request->itm_id,
                'description' => $request->description,
                'unit' => $request->unit,
                'quantity' => $request->quantity,
                'price' => $price,
                'amount' => $amount,
                'wct_id' => $request->wct_id,
                'dpt_id' => $request->dpt_id,
                'month' => $request->month,
                'bdc_id' => $request->bdc_id,
                'lob_id' => $request->lob_id,
                'status' => $status,
                'created_at' => $createdAt,
            ]);
        } elseif (in_array($acc_id, ['FOHENTERTAINT', 'FOHREPRESENTATION', 'SGAENTERTAINT', 'SGAREPRESENTATION'])) {
            $submission = BudgetPlan::create([
                'sub_id' => $sub_id,
                'acc_id' => $acc_id,
                'purpose' => $request->purpose,
                'itm_id' => $request->itm_id,
                'description' => $request->description,
                'beneficiary' => $request->beneficiary,
                'quantity' => null,
                'price' => $price,
                'amount' => $amount,
                'wct_id' => $request->wct_id,
                'dpt_id' => $request->dpt_id,
                'month' => $request->month,
                'bdc_id' => null,
                'status' => $status,
                'created_at' => $createdAt,
            ]);
        } elseif (in_array($acc_id, ['FOHINSPREM', 'SGAINSURANCE'])) {
            $submission = BudgetPlan::create([
                'sub_id' => $sub_id,
                'acc_id' => $acc_id,
                'purpose' => $request->purpose,
                'description' => $request->description,
                'ins_id' => $request->ins_id,
                'quantity' => null, // [MODIFIKASI] Set quantity ke null
                'price' => $price,
                'amount' => $amount,
                'wct_id' => $request->wct_id,
                'dpt_id' => $request->dpt_id,
                'month' => $request->month,
                'bdc_id' => null, // [MODIFIKASI] Set bdc_id ke null
                'status' => $status,
                'created_at' => $createdAt,
            ]);
        } elseif (in_array($acc_id, ['FOHTRAINING', 'SGATRAINING'])) {
            $submission = BudgetPlan::create([
                'sub_id' => $sub_id,
                'acc_id' => $acc_id,
                'purpose' => $request->purpose,
                'participant' => $request->participant,
                'jenis_training' => $request->jenis_training,
                'quantity' => $request->quantity,
                'price' => $price,
                'amount' => $amount,
                'wct_id' => $request->wct_id,
                'dpt_id' => $request->dpt_id,
                'month' => $request->month,
                'bdc_id' => $request->bdc_id, // [MODIFIKASI] Gunakan bdc_id dari request
                'status' => $status,
                'created_at' => $createdAt,
            ]);
        } elseif ($acc_id === 'SGAAFTERSALES') {
            $submission = BudgetPlan::create([
                'sub_id' => $sub_id,
                'acc_id' => $acc_id,
                'purpose' => $request->purpose,
                'itm_id' => $request->itm_id,
                'customer' => $request->customer,
                'quantity' => $request->quantity,
                'price' => $price,
                'amount' => $amount,
                'wct_id' => $request->wct_id,
                'dpt_id' => $request->dpt_id,
                'month' => $request->month,
                'bdc_id' => $request->bdc_id,
                'status' => $status,
                'created_at' => $createdAt,
            ]);
        } elseif (in_array($acc_id, ['FOHPOWER', 'SGAPOWER'])) {
            $submission = BudgetPlan::create([
                'sub_id' => $sub_id,
                'acc_id' => $acc_id,
                'purpose' => $request->purpose,
                'itm_id' => $request->itm_id,
                'kwh' => $request->kwh,
                'price' => $price,
                'amount' => $amount,
                'wct_id' => $request->wct_id,
                'dpt_id' => $request->dpt_id,
                'month' => $request->month,
                'lob_id' => $request->lob_id,
                'status' => $status,
                'created_at' => $createdAt,
            ]);
        } elseif (in_array($acc_id, ['FOHTRAV', 'SGATRAV'])) {
            $submission = BudgetPlan::create([
                'sub_id' => $sub_id,
                'acc_id' => $acc_id,
                'purpose' => $request->purpose,
                'trip_propose' => $request->trip_propose, // [MODIFIKASI] Simpan trip_propose dari request
                'destination' => $request->destination, // [MODIFIKASI] Simpan destination dari request
                'days' => $request->days,
                'quantity' => null, // [MODIFIKASI] Set quantity ke null untuk FOHTRAV dan SGATRAV
                'price' => $price,
                'amount' => $amount,
                'wct_id' => $request->wct_id,
                'dpt_id' => $request->dpt_id,
                'month' => $request->month,
                'bdc_id' => null, // [MODIFIKASI] Set bdc_id ke null karena R/NR dihapus
                'status' => $status,
                'created_at' => $createdAt,
            ]);
        } elseif (in_array($acc_id, ['FOHEMPLOYCOMPDL', 'FOHEMPLOYCOMPIL', 'SGAEMPLOYCOMP'])) { // [MODIFIKASI] Tambahkan kondisi untuk akun employee compensation
            $submission = BudgetPlan::create([
                'sub_id' => $sub_id,
                'acc_id' => $acc_id,
                'purpose' => $request->purpose,
                'itm_id' => $request->itm_id,
                'ledger_account' => $request->ledger_account, // [MODIFIKASI] Simpan ledger_account
                'ledger_account_description' => $request->ledger_account_description, // [MODIFIKASI] Simpan ledger_account_description
                'price' => $price,
                'amount' => $amount,
                'wct_id' => $request->wct_id,
                'dpt_id' => $request->dpt_id,
                'month' => $request->month,
                'lob_id' => $request->lob_id,
                'bdc_id' => $request->bdc_id,
                'status' => $status,
                'created_at' => $createdAt,
            ]);
        } elseif ($acc_id === 'CAPEX') {
            $submission = BudgetPlan::create([
                'sub_id' => $sub_id,
                'acc_id' => $acc_id,
                'purpose' => $request->purpose,
                'itm_id' => $request->itm_id,
                'asset_class' => $request->asset_class,
                'prioritas' => $request->prioritas,
                'alasan' => $request->alasan,
                'keterangan' => $request->keterangan,
                'quantity' => $request->quantity,
                'price' => $price,
                'amount' => $amount,
                'wct_id' => $request->wct_id,
                'dpt_id' => $request->dpt_id,
                'month' => $request->month,
                'status' => $status,
                'created_at' => $createdAt,
            ]);
        }

        if ($submission) {
            // Send notifications to all previous approvers (except current user)
            $notificationMsg = "Pada pengajuan {$sub_id} akun {$acc_id}, item baru telah ditambahkan oleh {$user->sect}";
            $previousApprovers = Approval::where('sub_id', $sub_id)
                ->where('approve_by', '!=', $user->npk)
                ->groupBy('approve_by')
                ->pluck('approve_by');

            if ($previousApprovers->isNotEmpty()) {
                $users = User::whereIn('npk', $previousApprovers)
                    ->where('dept', $submissionDept)
                    ->get();

                Log::info("Pengguna yang akan diberi notifikasi untuk addItem sub_id {$sub_id}: " . $users->count());

                foreach ($users as $notifyUser) {
                    try {
                        NotificationController::createNotification(
                            $notifyUser->npk,
                            $notificationMsg,
                            $sub_id
                        );
                        Log::info("Notifikasi dikirim ke NPK {$notifyUser->npk} untuk addItem sub_id {$sub_id}");
                    } catch (\Exception $e) {
                        Log::error("Gagal mengirim notifikasi ke NPK {$notifyUser->npk} untuk sub_id {$sub_id}: " . $e->getMessage());
                    }
                }
            }

            return response()->json(['success' => true, 'message' => 'Item berhasil ditambahkan']);
        }

        return response()->json(['success' => false, 'message' => 'Gagal menambahkan item'], 500);
    } catch (\Exception $e) {
        Log::error("Gagal menambahkan item untuk sub_id {$sub_id}: " . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Gagal menambahkan item: ' . $e->getMessage()], 500);
    }
}
}
