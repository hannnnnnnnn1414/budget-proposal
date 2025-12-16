<?php

namespace App\Http\Controllers;

use App\Models\BudgetRevision;
use App\Models\BudgetUpload;
use App\Models\Departments;
use App\Models\Account;
use App\Imports\BudgetRevisionImport;
use App\Models\Approval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;

class BudgetRevisionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // Statistics
            $stats = [
                'total_revisions' => BudgetRevision::count(),
                'pending' => BudgetRevision::where('status', 0)->count(),
                'approved' => BudgetRevision::where('status', 1)->count(),
                'rejected' => BudgetRevision::where('status', 2)->count(),
                'total_amount' => BudgetRevision::sum('amount'),
            ];

            // Get available years
            $availableYears = BudgetRevision::select(DB::raw('YEAR(created_at) as year'))
                ->distinct()
                ->orderBy('year', 'desc')
                ->pluck('year')
                ->filter()
                ->values()
                ->toArray();

            if (empty($availableYears)) {
                $currentYear = date('Y');
                $availableYears = [$currentYear, $currentYear + 1];
            }

            // Upload history
            $uploads = BudgetUpload::with('uploader')
                ->where(function ($query) {
                    $query->where('data', 'like', '%"type":"revision"%')
                        ->orWhere('data', 'like', '%revision%')
                        ->orWhere('data', 'like', '%REV-%');
                })
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return view('budget-revision.index', compact('stats', 'availableYears', 'uploads'));
        } catch (\Exception $e) {
            Log::error('Error in BudgetRevisionController@index: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat halaman.');
        }
    }

    /**
     * Upload budget revision file
     */
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
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        // Check if user is authenticated
        if (!Auth::check()) {
            Log::error('User not authenticated');
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        $file = $request->file('file');
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
                'model' => BudgetRevision::class,
                'template' => 'general'
            ],
            'COMMUNICATION' => [
                'prefix' => 'COM',
                'acc_id' => 'SGACOM',
                'model' => BudgetRevision::class,
                'template' => 'general'
            ],
            'OFFICE SUPPLY' => [
                'prefix' => 'OFS',
                'acc_id' => 'SGAOFFICESUP',
                'model' => BudgetRevision::class,
                'template' => 'general'
            ],
            'AFTER SALES SERVICE' => [
                'prefix' => 'AFS',
                'acc_id' => 'SGAAFTERSALES',
                'model' => BudgetRevision::class,
                'template' => 'aftersales'
            ],
            'INDIRECT MATERIAL' => [
                'prefix' => 'IDM',
                'acc_id' => 'FOHINDMAT',
                'model' => BudgetRevision::class,
                'template' => 'support'
            ],
            'FACTORY SUPPLY' => [
                'prefix' => 'FSU',
                'acc_id' => 'FOHFS',
                'model' => BudgetRevision::class,
                'template' => 'support'
            ],
            'REPAIR & MAINTENANCE FOH' => [
                'prefix' => 'RPMF',
                'acc_id' => 'FOHREPAIR',
                'model' => BudgetRevision::class,
                'template' => 'support'
            ],
            'DEPRECIATION OPEX' => [
                'prefix' => 'DPR',
                'acc_id' => 'SGADEPRECIATION',
                'model' => BudgetRevision::class,
                'template' => 'support'
            ],
            'CONS TOOLS' => [
                'prefix' => 'CTL',
                'acc_id' => 'FOHTOOLS',
                'model' => BudgetRevision::class,
                'template' => 'support'
            ],
            'INSURANCE PREM FOH' => [
                'prefix' => 'INSF',
                'acc_id' => 'FOHINSPREM',
                'model' => BudgetRevision::class,
                'template' => 'insurance'
            ],
            'INSURANCE PREM OPEX' => [
                'prefix' => 'INSO',
                'acc_id' => 'SGAINSURANCE',
                'model' => BudgetRevision::class,
                'template' => 'insurance'
            ],
            'TAX & PUBLIC DUES FOH' => [
                'prefix' => 'TAXF',
                'acc_id' => 'FOHTAXPUB',
                'model' => BudgetRevision::class,
                'template' => 'general'
            ],
            'TAX & PUBLIC DUES OPEX' => [
                'prefix' => 'TAXO',
                'acc_id' => 'SGATAXPUB',
                'model' => BudgetRevision::class,
                'template' => 'general'
            ],
            'PROFESIONAL FEE FOH' => [
                'prefix' => 'PRFF',
                'acc_id' => 'FOHPROF',
                'model' => BudgetRevision::class,
                'template' => 'general'
            ],
            'PROFESIONAL FEE OPEX' => [
                'prefix' => 'PRFO',
                'acc_id' => 'SGAPROF',
                'model' => BudgetRevision::class,
                'template' => 'general'
            ],
            'AUTOMOBILE FOH' => [
                'prefix' => 'AUTF',
                'acc_id' => 'FOHAUTOMOBILE',
                'model' => BudgetRevision::class,
                'template' => 'general'
            ],
            'AUTOMOBILE OPEX' => [
                'prefix' => 'AUTO',
                'acc_id' => 'SGAAUTOMOBILE',
                'model' => BudgetRevision::class,
                'template' => 'general'
            ],
            'RENT EXPENSE FOH' => [
                'prefix' => 'REXF',
                'acc_id' => 'FOHRENT',
                'model' => BudgetRevision::class,
                'template' => 'general'
            ],
            'PACKING & DELIVERY' => [
                'prefix' => 'PKD',
                'acc_id' => 'FOHPACKING',
                'model' => BudgetRevision::class,
                'template' => 'general'
            ],
            'BANK CHARGES' => [
                'prefix' => 'BKC',
                'acc_id' => 'SGABCHARGES',
                'model' => BudgetRevision::class,
                'template' => 'general'
            ],
            'ROYALTY' => [
                'prefix' => 'RYL',
                'acc_id' => 'SGARYLT',
                'model' => BudgetRevision::class,
                'template' => 'general'
            ],
            'CONTRIBUTION' => [
                'prefix' => 'CTR',
                'acc_id' => 'SGACONTRIBUTION',
                'model' => BudgetRevision::class,
                'template' => 'general'
            ],
            'ASSOCIATION' => [
                'prefix' => 'ASC',
                'acc_id' => 'SGAASSOCIATION',
                'model' => BudgetRevision::class,
                'template' => 'general'
            ],
            'UTILITIES FOH' => [
                'prefix' => 'UTLF',
                'acc_id' => 'FOHPOWER',
                'model' => BudgetRevision::class,
                'template' => 'utilities'
            ],
            'UTILITIES OPEX' => [
                'prefix' => 'UTLO',
                'acc_id' => 'SGAPOWER',
                'model' => BudgetRevision::class,
                'template' => 'utilities'
            ],
            'BUSINESS DUTY FOH' => [
                'prefix' => 'BSDF',
                'acc_id' => 'FOHTRAV',
                'model' => BudgetRevision::class,
                'template' => 'business'
            ],
            'BUSINESS DUTY OPEX' => [
                'prefix' => 'BSDO',
                'acc_id' => 'SGATRAV',
                'model' => BudgetRevision::class,
                'template' => 'business'
            ],
            'TRAINING & EDUCATION FOH' => [
                'prefix' => 'TEDF',
                'acc_id' => 'FOHTRAINING',
                'model' => BudgetRevision::class,
                'template' => 'training'
            ],
            'TRAINING & EDUCATION OPEX' => [
                'prefix' => 'TEDO',
                'acc_id' => 'SGATRAINING',
                'model' => BudgetRevision::class,
                'template' => 'training'
            ],
            'TECHNICAL DEVELOPMENT FOH' => [
                'prefix' => 'TCD',
                'acc_id' => 'FOHTECHDO',
                'model' => BudgetRevision::class,
                'template' => 'general'
            ],
            'RECRUITMENT FOH' => [
                'prefix' => 'RECF',
                'acc_id' => 'FOHRECRUITING',
                'model' => BudgetRevision::class,
                'template' => 'recruitment'
            ],
            'RECRUITMENT OPEX' => [
                'prefix' => 'RECO',
                'acc_id' => 'SGARECRUITING',
                'model' => BudgetRevision::class,
                'template' => 'recruitment'
            ],
            'RENT EXPENSE OPEX' => [
                'prefix' => 'REXO',
                'acc_id' => 'SGARENT',
                'model' => BudgetRevision::class,
                'template' => 'general'
            ],
            'MARKETING ACTIVITY' => [
                'prefix' => 'MKT',
                'acc_id' => 'SGAMARKT',
                'model' => BudgetRevision::class,
                'template' => 'general'
            ],
            'REPAIR & MAINTENANCE OPEX' => [
                'prefix' => 'RPMO',
                'acc_id' => 'SGAREPAIR',
                'model' => BudgetRevision::class,
                'template' => 'general'
            ],
            'BOOK NEWSPAPER' => [
                'prefix' => 'BKN',
                'acc_id' => 'SGABOOK',
                'model' => BudgetRevision::class,
                'template' => 'general'
            ],
            'ENTERTAINMENT FOH' => [
                'prefix' => 'ENTF',
                'acc_id' => 'FOHENTERTAINT',
                'model' => BudgetRevision::class,
                'template' => 'representation'
            ],
            'ENTERTAINMENT OPEX' => [
                'prefix' => 'ENTO',
                'acc_id' => 'SGAENTERTAINT',
                'model' => BudgetRevision::class,
                'template' => 'representation'
            ],
            'REPRESENTATION FOH' => [
                'prefix' => 'REPF',
                'acc_id' => 'FOHREPRESENTATION',
                'model' => BudgetRevision::class,
                'template' => 'representation'
            ],
            'REPRESENTATION OPEX' => [
                'prefix' => 'REPO',
                'acc_id' => 'SGAREPRESENTATION',
                'model' => BudgetRevision::class,
                'template' => 'representation'
            ],
            'OUTSOURCING FEE' => [
                'prefix' => 'OSF',
                'acc_id' => 'SGAOUTSOURCING',
                'model' => BudgetRevision::class,
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
                'model' => BudgetRevision::class,
                'template' => 'employee'
            ],
            'PURCHASE MATERIAL' => [
                'prefix' => 'PRM',
                'acc_id' => 'PURCHASEMATERIAL',
                'model' => BudgetRevision::class,
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
                } elseif ($template === 'aftersales') {
                    [$no,  $itm_id, $customer, $wct_id, $dpt_id] = array_slice($row, 0, 5);
                } elseif ($template === 'support') {
                    [$no, $itm_id, $description, $wct_id, $dpt_id, $bdc_id, $lob_id] = array_slice($row, 0, 7);
                } elseif ($template === 'utilities') {
                    [$no,  $itm_id, $kwh, $wct_id, $dpt_id, $lob_id] = array_slice($row, 0, 6);
                } elseif ($template === 'business') {
                    [$no, $trip_propose, $destination, $days, $wct_id, $dpt_id] = array_slice($row, 0, 6);
                } elseif ($template === 'representation') {
                    [$no, $itm_id, $description, $beneficiary, $wct_id, $dpt_id] = array_slice($row, 0, 6);
                } elseif ($template === 'insurance') {
                    [$no, $description, $ins_id, $price, $wct_id, $dpt_id] = array_slice($row, 0, 6);
                } elseif ($template === 'training') {
                    [$no, $participant, $jenis_training, $quantity, $price, $wct_id, $dpt_id] = array_slice($row, 0, 7);
                } elseif ($template === 'recruitment') {
                    [$no, $itm_id, $description, $position, $price, $wct_id, $dpt_id] = array_slice($row, 0, 7);
                } elseif ($template === 'employee') {
                    [$no, $type, $ledger_account, $ledger_account_description, $price, $wct_id, $dpt_id, $lob_id, $bdc_id] = array_slice($row, 0, 9);
                } elseif ($template === 'purchase') {
                    [$no, $itm_id, $business_partner, $description, $price, $wct_id, $dpt_id, $lob_id, $bdc_id] = array_slice($row, 0, 9);
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
                        } elseif ($userDept === '1332' && in_array($dpt_id, ['1332', '1333'])) {
                            Log::info("Kadept 1332 uploading untuk dpt_id $dpt_id diizinkan pada baris $i di sheet $sheetName");
                        } elseif ($dpt_id !== $userDept) {
                            $errors[] = "Invalid dpt_id pada baris $i di sheet $sheetName: Diharapkan $userDept, mendapat $dpt_id";
                            Log::warning("Invalid dpt_id pada baris $i di sheet $sheetName: Diharapkan $userDept, mendapat $dpt_id");
                            continue;
                        }

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

                        // Validasi departemen: Izinkan GA (4131) mengunggah untuk BOD (7111)
                        if ($userDept === '4131' && in_array($dpt_id, ['4131', '1111', '1131', '1151', '1211', '1231', '7111'])) {
                            Log::info("GA (4131) uploading untuk dpt_id $dpt_id diizinkan pada baris $i di sheet $sheetName");
                        } elseif ($userDept === '4111' && in_array($dpt_id, ['4111', '1116', '1140', '1160', '1224', '1242', '7111'])) {
                            Log::info("4111 uploading untuk dpt_id $dpt_id diizinkan pada baris $i di sheet $sheetName");
                        } elseif ($userDept === '1332' && in_array($dpt_id, ['1332', '1333'])) {
                            Log::info("Kadept 1332 uploading untuk dpt_id $dpt_id diizinkan pada baris $i di sheet $sheetName");
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

                        // Validasi departemen: Izinkan GA (4131) mengunggah untuk BOD (7111)
                        if ($userDept === '4131' && in_array($dpt_id, ['4131', '1111', '1131', '1151', '1211', '1231', '7111'])) {
                            Log::info("GA (4131) uploading untuk dpt_id $dpt_id diizinkan pada baris $i di sheet $sheetName");
                        } elseif ($userDept === '4111' && in_array($dpt_id, ['4111', '1116', '1140', '1160', '1224', '1242', '7111'])) {
                            Log::info("4111 uploading untuk dpt_id $dpt_id diizinkan pada baris $i di sheet $sheetName");
                        } elseif ($userDept === '1332' && in_array($dpt_id, ['1332', '1333'])) {
                            Log::info("Kadept 1332 uploading untuk dpt_id $dpt_id diizinkan pada baris $i di sheet $sheetName");
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
                        $amount = $row[17] ?? null;


                        // Validasi department dengan multiple department untuk 4131 dan 4111
                        if ($userDept === '4131' && in_array($dpt_id, ['4131', '1111', '1131', '1151', '1211', '1231', '7111'])) {
                            Log::info("GA (4131) uploading untuk dpt_id $dpt_id diizinkan pada baris $i di sheet $sheetName");
                        } elseif ($userDept === '4111' && in_array($dpt_id, ['4111', '1116', '1140', '1160', '1224', '1242', '7111'])) {
                            Log::info("4111 uploading untuk dpt_id $dpt_id diizinkan pada baris $i di sheet $sheetName");
                        } elseif ($userDept === '1332' && in_array($dpt_id, ['1332', '1333'])) {
                            Log::info("Kadept 1332 uploading untuk dpt_id $dpt_id diizinkan pada baris $i di sheet $sheetName");
                        } elseif ($dpt_id !== $userDept) {
                            $errors[] = "Invalid dpt_id pada baris $i di sheet $sheetName: Diharapkan $userDept, mendapat $dpt_id";
                            Log::warning("Invalid dpt_id pada baris $i di sheet $sheetName: Diharapkan $userDept, mendapat $dpt_id");
                            continue;
                        }
                        $requiredFields = [
                            // 'itm_id' => $itm_id,
                            'description' => $description,
                            'ins_id' => $ins_id,
                            // 'quantity' => $quantity,
                            'price' => $price,
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
                                'acc_id' => $acc_id,
                                'description' => $description,
                                'ins_id' => $ins_id,
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
                    } elseif ($template === 'utilities') {
                        [$no, $itm_id, $kwh, $wct_id, $dpt_id, $lob_id] = array_slice($row, 0, 6);
                        $amount = $row[18] ?? null;

                        // Validasi department dengan multiple department untuk 4131 dan 4111
                        if ($userDept === '4131' && in_array($dpt_id, ['4131', '1111', '1131', '1151', '1211', '1231', '7111'])) {
                            Log::info("GA (4131) uploading untuk dpt_id $dpt_id diizinkan pada baris $i di sheet $sheetName");
                        } elseif ($userDept === '4111' && in_array($dpt_id, ['4111', '1116', '1140', '1160', '1224', '1242', '7111'])) {
                            Log::info("4111 uploading untuk dpt_id $dpt_id diizinkan pada baris $i di sheet $sheetName");
                        } elseif ($userDept === '1332' && in_array($dpt_id, ['1332', '1333'])) {
                            Log::info("Kadept 1332 uploading untuk dpt_id $dpt_id diizinkan pada baris $i di sheet $sheetName");
                        } elseif ($dpt_id !== $userDept) {
                            $errors[] = "Invalid dpt_id pada baris $i di sheet $sheetName: Diharapkan $userDept, mendapat $dpt_id";
                            Log::warning("Invalid dpt_id pada baris $i di sheet $sheetName: Diharapkan $userDept, mendapat $dpt_id");
                            continue;
                        }
                        $requiredFields = [
                            'itm_id' => $itm_id,
                            'kwh' => $kwh,
                            // 'quantity' => $quantity,
                            // 'price' => $price,
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
                            $monthValue = $row[6 + $index] ?? 0;
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
                                'acc_id' => $acc_id,
                                'itm_id' => $itm_id,
                                'kwh' => $kwh,
                                // 'quantity' => $quantity,
                                'price' => (float)$monthValue,
                                'amount' => $amount,
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
                        $amount = $row[18] ?? null;

                        // Validasi departemen: Izinkan GA (4131) mengunggah untuk BOD (7111)
                        if ($userDept === '4131' && in_array($dpt_id, ['4131', '1111', '1131', '1151', '1211', '1231', '7111'])) {
                            Log::info("GA (4131) uploading untuk dpt_id $dpt_id diizinkan pada baris $i di sheet $sheetName");
                        } elseif ($userDept === '4111' && in_array($dpt_id, ['4111', '1116', '1140', '1160', '1224', '1242', '7111'])) {
                            Log::info("4111 uploading untuk dpt_id $dpt_id diizinkan pada baris $i di sheet $sheetName");
                        } elseif ($userDept === '1332' && in_array($dpt_id, ['1332', '1333'])) {
                            Log::info("Kadept 1332 uploading untuk dpt_id $dpt_id diizinkan pada baris $i di sheet $sheetName");
                        } elseif ($dpt_id !== $userDept) {
                            $errors[] = "Invalid dpt_id pada baris $i di sheet $sheetName: Diharapkan $userDept, mendapat $dpt_id";
                            Log::warning("Invalid dpt_id pada baris $i di sheet $sheetName: Diharapkan $userDept, mendapat $dpt_id");
                            continue;
                        }

                        // // Validasi department
                        // if ($dpt_id !== $userDept) {
                        //     $errors[] = "Invalid dpt_id in row $i of sheet $sheetName: Expected $userDept, got $dpt_id";
                        //     Log::warning("Invalid dpt_id in row $i of sheet $sheetName: Expected $userDept, got $dpt_id");
                        //     continue;
                        // }

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
                        } elseif ($userDept === '1332' && in_array($dpt_id, ['1332', '1333'])) {
                            Log::info("Kadept 1332 uploading untuk dpt_id $dpt_id diizinkan pada baris $i di sheet $sheetName");
                        } elseif ($dpt_id !== $userDept) {
                            $errors[] = "Invalid dpt_id pada baris $i di sheet $sheetName: Diharapkan $userDept, mendapat $dpt_id";
                            Log::warning("Invalid dpt_id pada baris $i di sheet $sheetName: Diharapkan $userDept, mendapat $dpt_id");
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
                        $amount = $row[19] ?? null;

                        // Hapus validasi price karena di template training, price sebenarnya adalah nilai per unit
                        // dan nilai aktual ada di kolom bulanan
                        $price = (float)$price; // Ini adalah harga per unit

                        // Validasi department dengan multiple department untuk 4131 dan 4111
                        if ($userDept === '4131' && in_array($dpt_id, ['4131', '1111', '1131', '1151', '1211', '1231', '7111'])) {
                            Log::info("GA (4131) uploading untuk dpt_id $dpt_id diizinkan pada baris $i di sheet $sheetName");
                        } elseif ($userDept === '4111' && in_array($dpt_id, ['4111', '1116', '1140', '1160', '1224', '1242', '7111'])) {
                            Log::info("4111 uploading untuk dpt_id $dpt_id diizinkan pada baris $i di sheet $sheetName");
                        } elseif ($userDept === '1332' && in_array($dpt_id, ['1332', '1333'])) {
                            Log::info("Kadept 1332 uploading untuk dpt_id $dpt_id diizinkan pada baris $i di sheet $sheetName");
                        } elseif ($dpt_id !== $userDept) {
                            $errors[] = "Invalid dpt_id pada baris $i di sheet $sheetName: Diharapkan $userDept, mendapat $dpt_id";
                            Log::warning("Invalid dpt_id pada baris $i di sheet $sheetName: Diharapkan $userDept, mendapat $dpt_id");
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
                                'acc_id' => $acc_id,
                                'participant' => $participant,
                                'jenis_training' => $jenis_training,
                                'quantity' => (float)$quantity,
                                'price' => (float)$monthValue, // Gunakan nilai bulanan, bukan price dari template
                                'wct_id' => $wct_id,
                                'dpt_id' => $dpt_id,
                                'month' => $monthName,
                                'status' => 1,
                                'amount' => $amount ? (float)$amount : null,
                            ]);
                            Log::info("Created record for sub_id: $sub_id, month: $monthName, value: $monthValue");
                            $processedRows++;
                        }
                    } elseif ($template === 'recruitment') {
                        [$no, $itm_id, $description, $position, $price, $wct_id, $dpt_id] = array_slice($row, 0, 7);
                        $amount = $row[19] ?? null;

                        // Validasi department dengan multiple department untuk 4131 dan 4111
                        if ($userDept === '4131' && in_array($dpt_id, ['4131', '1111', '1131', '1151', '1211', '1231', '7111'])) {
                            Log::info("GA (4131) uploading untuk dpt_id $dpt_id diizinkan pada baris $i di sheet $sheetName");
                        } elseif ($userDept === '4111' && in_array($dpt_id, ['4111', '1116', '1140', '1160', '1224', '1242', '7111'])) {
                            Log::info("4111 uploading untuk dpt_id $dpt_id diizinkan pada baris $i di sheet $sheetName");
                        } elseif ($userDept === '1332' && in_array($dpt_id, ['1332', '1333'])) {
                            Log::info("Kadept 1332 uploading untuk dpt_id $dpt_id diizinkan pada baris $i di sheet $sheetName");
                        } elseif ($dpt_id !== $userDept) {
                            $errors[] = "Invalid dpt_id pada baris $i di sheet $sheetName: Diharapkan $userDept, mendapat $dpt_id";
                            Log::warning("Invalid dpt_id pada baris $i di sheet $sheetName: Diharapkan $userDept, mendapat $dpt_id");
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
                        [$no, $type, $ledger_account, $ledger_account_description, $wct_id, $dpt_id, $bdc_id, $lob_id] = array_slice($row, 0, 8);
                        $amount = $row[20] ?? null;

                        // Skip row jika data kosong
                        if (empty(trim($type ?? '')) || empty(trim($dpt_id ?? ''))) {
                            Log::info("Skipping empty row $i in sheet $sheetName");
                            continue;
                        }

                        // Validasi type untuk menentukan prefix dan acc_id
                        $trimmedType = trim($type);

                        // Tentukan prefix dan acc_id berdasarkan type
                        if (isset($prefixMap[$trimmedType])) {
                            $currentPrefix = $prefixMap[$trimmedType];
                            $currentAccId = $accIdMap[$trimmedType];
                        } else {
                            // Default fallback
                            $currentPrefix = 'EMC';
                            $currentAccId = 'SGAEMPLOYCOMP';
                            Log::warning("Unknown type '$trimmedType' in row $i, using default mapping");
                        }

                        // **PERBAIKAN: Gunakan sub_id yang sama untuk type yang sama dalam sheet yang sama**
                        // Buat key unik berdasarkan type + sheet untuk grouping
                        $typeKey = $trimmedType . '_' . $sheetName;

                        if (!isset($employeeTypeMapping[$typeKey])) {
                            // Generate sub_id baru untuk type ini (hanya sekali per type per sheet)
                            $lastRecord = $model::where('sub_id', 'like', "$currentPrefix%")
                                ->orderBy('sub_id', 'desc')
                                ->first();
                            $nextNumber = $lastRecord ? ((int)str_replace($currentPrefix, '', $lastRecord->sub_id) + 1) : 1;
                            $currentSubId = $currentPrefix . str_pad($nextNumber, 7, '0', STR_PAD_LEFT);

                            // Create approval record untuk sub_id yang baru (hanya sekali)
                            try {
                                $existingApproval = Approval::where('sub_id', $currentSubId)->first();
                                if (!$existingApproval) {
                                    Approval::create([
                                        'approve_by' => $npk,
                                        'sub_id' => $currentSubId,
                                        'status' => 1,
                                        'created_at' => now(),
                                        'updated_at' => now(),
                                    ]);
                                    Log::info("Created approval record for sub_id: $currentSubId, type: $trimmedType, sheet: $sheetName");
                                }
                            } catch (\Exception $e) {
                                Log::error("Failed to create approval record for sub_id: $currentSubId, error: " . $e->getMessage());
                                continue; // Skip row ini
                            }

                            $employeeTypeMapping[$typeKey] = [
                                'sub_id' => $currentSubId,
                                'prefix' => $currentPrefix,
                                'acc_id' => $currentAccId
                            ];

                            Log::info("Generated NEW sub_id for type '$trimmedType' in sheet '$sheetName': $currentSubId, acc_id: $currentAccId");
                        } else {
                            // Gunakan sub_id yang sudah ada untuk type yang sama
                            $currentSubId = $employeeTypeMapping[$typeKey]['sub_id'];
                            $currentAccId = $employeeTypeMapping[$typeKey]['acc_id'];
                            Log::info("Using EXISTING sub_id for type '$trimmedType' in sheet '$sheetName': $currentSubId, acc_id: $currentAccId");
                        }

                        // Validasi department
                        if ($userDept === '4131' && in_array($dpt_id, ['4131', '1111', '1131', '1151', '1211', '1231', '7111'])) {
                            Log::info("GA (4131) uploading untuk dpt_id $dpt_id diizinkan pada baris $i di sheet $sheetName");
                        } elseif ($userDept === '4111' && in_array($dpt_id, ['4111', '1116', '1140', '1160', '1224', '1242', '7111'])) {
                            Log::info("4111 uploading untuk dpt_id $dpt_id diizinkan pada baris $i di sheet $sheetName");
                        } elseif ($userDept === '1332' && in_array($dpt_id, ['1332', '1333'])) {
                            Log::info("Kadept 1332 uploading untuk dpt_id $dpt_id diizinkan pada baris $i di sheet $sheetName");
                        } elseif ($dpt_id !== $userDept) {
                            $errors[] = "Invalid dpt_id pada baris $i di sheet $sheetName: Diharapkan $userDept, mendapat $dpt_id";
                            Log::warning("Invalid dpt_id pada baris $i di sheet $sheetName: Diharapkan $userDept, mendapat $dpt_id");
                            continue;
                        }

                        // Validasi field required
                        $requiredFields = [
                            'type' => $type,
                            'ledger_account' => $ledger_account,
                            'ledger_account_description' => $ledger_account_description,
                            'wct_id' => $wct_id,
                            'dpt_id' => $dpt_id,
                            'lob_id' => $lob_id,
                            'bdc_id' => $bdc_id,
                            'amount' => $amount,
                        ];

                        foreach ($requiredFields as $fieldName => $value) {
                            if (empty($value)) {
                                $errors[] = "Invalid $fieldName in row $i of sheet $sheetName: $fieldName is empty";
                                Log::warning("Invalid $fieldName in row $i of sheet $sheetName: $fieldName is empty");
                                continue 2;
                            }
                        }

                        // Process each month
                        foreach (array_keys($months) as $index => $monthIndex) {
                            $monthValue = $row[8 + $index] ?? 0;

                            if ($monthValue == 0 || $monthValue === null || trim($monthValue) === '') {
                                Log::info("Skipping month $monthIndex for row $i: value is $monthValue");
                                continue;
                            }

                            if (!is_numeric($monthValue)) {
                                $errors[] = "Invalid numeric value for month $monthIndex in sheet $sheetName: value=$monthValue";
                                Log::warning("Invalid numeric value for month $monthIndex in row $i: value=$monthValue");
                                continue;
                            }

                            // Konversi angka bulan ke nama bulan
                            $monthName = $monthNumberToName[$monthIndex + 1] ?? 'Unknown';

                            $model::create([
                                'sub_id' => $currentSubId,
                                'acc_id' => $currentAccId,
                                'ledger_account' => $ledger_account,
                                'ledger_account_description' => $ledger_account_description,
                                'price' => (float)$monthValue,
                                'wct_id' => $wct_id,
                                'dpt_id' => $dpt_id,
                                'bdc_id' => $bdc_id,
                                'lob_id' => $lob_id,
                                'month' => $monthName,
                                'status' => 1,
                                'amount' => $amount
                            ]);
                            $processedRows++;
                            Log::info("Created employee record for type: $type, sub_id: $currentSubId, month: $monthName, value: $monthValue");
                        }
                    } elseif ($template === 'purchase') {
                        [$no, $itm_id, $business_partner, $description, $wct_id, $dpt_id, $lob_id, $bdc_id] = array_slice($row, 0, 8);
                        $amount = $row[20] ?? null;

                        // Validasi department dengan multiple department untuk 4131 dan 4111
                        if ($userDept === '4131' && in_array($dpt_id, ['4131', '1111', '1131', '1151', '1211', '1231', '7111'])) {
                            Log::info("GA (4131) uploading untuk dpt_id $dpt_id diizinkan pada baris $i di sheet $sheetName");
                        } elseif ($userDept === '4111' && in_array($dpt_id, ['4111', '1116', '1140', '1160', '1224', '1242', '7111'])) {
                            Log::info("4111 uploading untuk dpt_id $dpt_id diizinkan pada baris $i di sheet $sheetName");
                        } elseif ($userDept === '1332' && in_array($dpt_id, ['1332', '1333'])) {
                            Log::info("Kadept 1332 uploading untuk dpt_id $dpt_id diizinkan pada baris $i di sheet $sheetName");
                        } elseif ($dpt_id !== $userDept) {
                            $errors[] = "Invalid dpt_id pada baris $i di sheet $sheetName: Diharapkan $userDept, mendapat $dpt_id";
                            Log::warning("Invalid dpt_id pada baris $i di sheet $sheetName: Diharapkan $userDept, mendapat $dpt_id");
                            continue;
                        }
                        $requiredFields = [
                            'itm_id' => $itm_id,
                            'business_partner' => $business_partner,
                            'description' => $description,
                            // 'unit' => $unit,
                            // 'quantity' => $quantity,
                            'price' => $price,
                            'amount' => $amount,
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
                                'acc_id' => $acc_id,
                                'itm_id' => $itm_id,
                                'business_partner' => $business_partner,
                                'description' => $description,
                                // 'unit' => $unit,
                                // 'quantity' => $quantity,
                                'price' => (float)$monthValue,
                                'amount' => $amount,
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

    /**
     * Get summary data
     */
    public function getDepartmentSummary(Request $request)
    {
        try {
            $query = BudgetRevision::query();

            if ($request->filled('periode')) {
                $query->whereYear('created_at', $request->periode);
            }

            // Get department summary
            $summary = $query->select([
                'dpt_id',
                DB::raw('MAX(created_at) as last_upload'),
                DB::raw('COUNT(DISTINCT acc_id) as account_count'),
                DB::raw('COUNT(*) as item_count'),
                DB::raw('SUM(amount) as total_amount'),
            ])
                ->groupBy('dpt_id')
                ->orderBy('last_upload', 'desc')
                ->get()
                ->map(function ($item) {
                    // Get department name
                    if ($item->dpt_id) {
                        $dept = Departments::where('dpt_id', $item->dpt_id)->first();
                        $item->department = $dept ? $dept->dpt_name : $item->dpt_id;
                        $item->dept_code = $item->dpt_id;
                    } else {
                        $item->department = 'Unknown Department';
                        $item->dept_code = '-';
                    }

                    // Format amounts
                    $item->total_amount = number_format($item->total_amount, 0, ',', '.');
                    $item->last_upload = $item->last_upload ? date('d/m/Y H:i', strtotime($item->last_upload)) : '-';

                    return $item;
                });

            return response()->json([
                'success' => true,
                'data' => $summary,
            ]);
        } catch (\Exception $e) {
            Log::error('Get summary error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading summary data.',
            ], 500);
        }
    }

    /**
     * Delete revision data
     */
    public function delete(Request $request)
    {
        try {
            $request->validate([
                'periode' => 'nullable|integer',
                'revision_code' => 'nullable|string',
            ]);

            $query = BudgetRevision::query();

            if ($request->filled('periode')) {
                $query->whereYear('created_at', $request->periode);
            }

            if ($request->filled('revision_code')) {
                $query->where('sub_id', 'like', $request->revision_code . '%');
            }

            $count = $query->count();
            $query->delete();

            Log::info('Budget revisions deleted', [
                'count' => $count,
                'year' => $request->periode,
                'revision_code' => $request->revision_code,
                'deleted_by' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Berhasil menghapus ' . $count . ' data revision.',
            ]);
        } catch (\Exception $e) {
            Log::error('Delete revisions error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting data.',
            ], 500);
        }
    }

    public function getExpectedColumns($template, $months)
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
}
