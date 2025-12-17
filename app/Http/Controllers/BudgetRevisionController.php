<?php

namespace App\Http\Controllers;

use App\Models\BudgetRevision;
use App\Models\BudgetUpload;
use App\Models\Departments;
use App\Models\Account;
use App\Imports\BudgetRevisionImport;
use App\Models\Approval;
use App\Models\BudgetFinal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;

class BudgetRevisionController extends Controller
{
    private function validateDepartment($userDept, $dpt_id)
    {
        if ($userDept === '4131' && in_array($dpt_id, ['4131', '1111', '1131', '1151', '1211', '1231', '7111'])) {
            return true;
        }

        if ($userDept === '4111' && in_array($dpt_id, ['4111', '1116', '1140', '1160', '1224', '1242', '7111'])) {
            return true;
        }

        if ($userDept === '1332' && in_array($dpt_id, ['1332', '1333'])) {
            return true;
        }

        return $dpt_id === $userDept;
    }
    public function index()
    {
        try {
            $stats = [
                'total_revisions' => BudgetRevision::count(),
                'pending' => BudgetRevision::where('status', 0)->count(),
                'approved' => BudgetRevision::where('status', 1)->count(),
                'rejected' => BudgetRevision::where('status', 2)->count(),
                'total_amount' => BudgetRevision::sum('amount'),
            ];

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

    public function checkBudgetFinalExists($deptId, $year)
    {
        $exists = BudgetFinal::where('dept_code', $deptId)
            ->where('periode', $year)
            ->exists();

        if (!$exists) {
            Log::warning("No budget final data found for department: $deptId, year: $year");
            return false;
        }

        return true;
    }

    public function validateTotalBudgetPerMonth($deptId, $monthName, $year, $uploadData)
    {
        try {
            $monthMap = [
                'January' => 'jan',
                'February' => 'feb',
                'March' => 'mar',
                'April' => 'apr',
                'May' => 'may',
                'June' => 'jun',
                'July' => 'jul',
                'August' => 'aug',
                'September' => 'sep',
                'October' => 'oct',
                'November' => 'nov',
                'December' => 'dec'
            ];

            $monthColumn = strtolower($monthMap[$monthName] ?? $monthName);

            $budgetFinal = BudgetFinal::where('dept_code', $deptId)
                ->where('periode', $year)
                ->first();

            if (!$budgetFinal) {
                Log::warning("No budget final found for dept: $deptId, year: $year");
                return [
                    'valid' => false,
                    'message' => "Tidak ada data budget final untuk departemen $deptId tahun $year"
                ];
            }

            $finalBudgetAmount = (float)($budgetFinal->{$monthColumn} ?? 0);

            $existingRevisionTotal = BudgetRevision::where('dpt_id', $deptId)
                ->where('month', $monthName)
                ->whereYear('created_at', $year)
                ->sum('price');

            $uploadTotal = 0;
            foreach ($uploadData as $item) {
                $uploadTotal += (float)$item['price'];
            }

            $totalAfterUpload = $existingRevisionTotal + $uploadTotal;

            Log::info('Budget Validation - TOTAL per Month', [
                'dept_id' => $deptId,
                'month' => $monthName,
                'year' => $year,
                'final_budget' => $finalBudgetAmount,
                'existing_revision' => $existingRevisionTotal,
                'upload_total' => $uploadTotal,
                'total_after_upload' => $totalAfterUpload,
                'budget_final_data' => [
                    'periode' => $budgetFinal->periode,
                    'dept' => $budgetFinal->dept,
                    'account' => $budgetFinal->account,
                    $monthColumn => $finalBudgetAmount
                ]
            ]);

            if ($totalAfterUpload != $finalBudgetAmount) {
                $difference = $totalAfterUpload - $finalBudgetAmount;
                return [
                    'valid' => false,
                    'message' => "Total budget $monthName tidak sesuai! " .
                        "Budget final: Rp " . number_format($finalBudgetAmount, 0, ',', '.') .
                        ", Total setelah upload: Rp " . number_format($totalAfterUpload, 0, ',', '.') .
                        " (Selisih: " . ($difference > 0 ? '+' : '') .
                        number_format($difference, 0, ',', '.') . ")"
                ];
            }

            return [
                'valid' => true,
                'final_budget' => $finalBudgetAmount,
                'existing_revision' => $existingRevisionTotal,
                'upload_total' => $uploadTotal
            ];
        } catch (\Exception $e) {
            Log::error('Budget validation error', [
                'error' => $e->getMessage(),
                'dept_id' => $deptId,
                'month' => $monthName
            ]);
            return [
                'valid' => false,
                'message' => 'Error validasi budget: ' . $e->getMessage()
            ];
        }
    }

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
        $currentYear = date('Y');
        $employeeTypeMapping = [];
        $totalAmount = 0;
        $processedRows = 0;
        $processedSheets = [];
        Log::info('Upload request received', [
            'template' => $request->file('file') ? $request->file('file')->getClientOriginalName() : 'No file',
            'purpose' => $request->input('purpose'),
            'timestamp' => now()->toDateTimeString(),
            'upload_type' => 'required|in:asset'

        ]);

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        if (!Auth::check()) {
            Log::error('User not authenticated');
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated',
                'data' => null
            ], 401);
        }

        $file = $request->file('file');
        $npk = Auth::user()->npk;
        $userDept = Auth::user()->dept;

        try {
            $spreadsheet = IOFactory::load($file);
            Log::info('Excel file loaded successfully', ['sheets' => $spreadsheet->getSheetNames()]);
        } catch (\Exception $e) {
            Log::error('Failed to load Excel file', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to load Excel file: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }

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
                'prefix_map' => [
                    'EMPLOYEE COMPENSATION' => 'EMC',
                    'EMPLOYEE COMPENSATION DIRECT LABOR' => 'EDL',
                    'EMPLOYEE COMPENSATION INDIRECT LABOR' => 'EIL'
                ],
                'acc_id_map' => [
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

        foreach ($spreadsheet->getSheetNames() as $sheetName) {
            if (!isset($sheetMappings[$sheetName])) {
                Log::warning("Sheet '$sheetName' not found in sheetMappings");
                continue;
            }

            if (!$this->checkBudgetFinalExists($userDept, $currentYear)) {
                $errors[] = "Tidak ada data budget final untuk departemen $userDept tahun $currentYear. Harap upload budget final terlebih dahulu.";
                Log::error("No budget final data for department: $userDept, year: $currentYear");

                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat memproses revision. Data budget final tidak ditemukan.',
                    'data' => null
                ], 400);
            }

            $sheetConfig = $sheetMappings[$sheetName];

            if (isset($sheetConfig['prefix_map'])) {
                $prefixMap = $sheetConfig['prefix_map'];
                $accIdMap = $sheetConfig['acc_id_map'];
                $isMultiPrefix = true;
            } else {
                $prefix = $sheetConfig['prefix'];
                $acc_id = $sheetConfig['acc_id'];
                $isMultiPrefix = false;
            }

            $model = $sheetConfig['model'];
            $template = $sheetConfig['template'];

            Log::info("Checking sheet: $sheetName, template: $template, model: $model");

            $sheet = $spreadsheet->getSheetByName($sheetName);
            $data = $sheet->toArray();
            Log::info("Sheet '$sheetName' has " . count($data) . " rows");

            $hasValidData = false;
            foreach ($data as $i => $row) {
                if ($i === 0) {
                    Log::info("Skipping header row for sheet: $sheetName");
                    continue;
                }

                $rowHasData = array_filter($row, function ($value) {
                    return !is_null($value) && $value !== '';
                });

                if (!empty($rowHasData)) {
                    $hasValidData = true;
                    break;
                }
            }

            if (!$hasValidData) {
                Log::info("Sheet '$sheetName' has no valid data rows, skipping processing");
                continue;
            }

            $gidErrors = [];
            foreach ($data as $i => $row) {
                if ($i === 0) {
                    continue;
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

            if (!empty($gidErrors)) {
                $errors = array_merge($errors, $gidErrors);
                Log::warning("Skipping sheet '$sheetName' due to GID validation errors", ['errors' => $gidErrors]);
                continue;
            }

            if ($isMultiPrefix) {
                $trimmedType = trim($type ?? '');
                $prefix = $prefixMap[$trimmedType] ?? 'EMC';
                $acc_id = $accIdMap[$trimmedType] ?? 'SGAEMPLOYCOMP';

                $lastRecord = $model::where('sub_id', 'like', "$prefix%")
                    ->orderBy('sub_id', 'desc')
                    ->first();
                $nextNumber = $lastRecord ? ((int)str_replace($prefix, '', $lastRecord->sub_id) + 1) : 1;
                $sub_id = $prefix . str_pad($nextNumber, 7, '0', STR_PAD_LEFT);
            } else {
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

            if ($template === 'general') {
                $allMonthlyData = [];
                $rowsToProcess = [];
                foreach ($data as $i => $row) {
                    if ($i === 0) continue;
                    [$no, $itm_id, $description, $wct_id, $dpt_id] = array_slice($row, 0, 5);
                    $amount = $row[17] ?? null;
                    if ($userDept === '4131' && in_array($dpt_id, ['4131', '1111', '1131', '1151', '1211', '1231', '7111'])) {
                    } elseif ($userDept === '4111' && in_array($dpt_id, ['4111', '1116', '1140', '1160', '1224', '1242', '7111'])) {
                    } elseif ($userDept === '1332' && in_array($dpt_id, ['1332', '1333'])) {
                    } elseif ($dpt_id !== $userDept) {
                        $errors[] = "Invalid dpt_id pada baris $i di sheet $sheetName: Diharapkan $userDept, mendapat $dpt_id";
                        continue;
                    }
                    $requiredFields = [
                        'itm_id' => $itm_id,
                        'description' => $description,
                        'amount' => $amount,
                        'dpt_id' => $dpt_id,
                    ];
                    $hasError = false;
                    foreach ($requiredFields as $fieldName => $value) {
                        if (is_null($value) || $value === '' || trim($value) === '') {
                            $errors[] = "Invalid $fieldName pada baris $i di sheet $sheetName: $fieldName kosong";
                            $hasError = true;
                            break;
                        }
                    }
                    if ($hasError) continue;
                    foreach (array_keys($months) as $index => $monthIndex) {
                        $monthValue = $row[5 + $index] ?? 0;
                        if ($monthValue == 0 || $monthValue === null || trim($monthValue) === '') {
                            continue;
                        }
                        if (!is_numeric($monthValue)) {
                            $errors[] = "Invalid numeric value for month $monthIndex in row $i: value=$monthValue";
                            continue;
                        }
                        $monthName = $monthNumberToName[$monthIndex + 1] ?? 'Unknown';
                        if (!isset($allMonthlyData[$monthName])) {
                            $allMonthlyData[$monthName] = [];
                        }
                        $allMonthlyData[$monthName][] = [
                            'row' => $i,
                            'sub_id' => $sub_id,
                            'acc_id' => $acc_id,
                            'itm_id' => $itm_id,
                            'description' => $description,
                            'price' => (float)$monthValue,
                            'amount' => (float)$amount,
                            'wct_id' => $wct_id,
                            'dpt_id' => $dpt_id,
                            'month' => $monthName
                        ];
                    }
                    $rowsToProcess[] = [
                        'row_number' => $i,
                        'itm_id' => $itm_id,
                        'dpt_id' => $dpt_id
                    ];
                }
                $validationErrors = [];
                foreach ($allMonthlyData as $monthName => $items) {
                    if (empty($items)) continue;
                    $dpt_id = $items[0]['dpt_id'];
                    $budgetValidation = $this->validateTotalBudgetPerMonth(
                        $dpt_id,
                        $monthName,
                        $currentYear,
                        $items
                    );
                    if (!$budgetValidation['valid']) {
                        $validationErrors[] = "Bulan $monthName: " . $budgetValidation['message'];
                    }
                }
                if (!empty($validationErrors)) {
                    $errors = array_merge($errors, $validationErrors);
                    continue;
                }
                foreach ($allMonthlyData as $monthName => $items) {
                    foreach ($items as $item) {
                        try {
                            $model::create([
                                'sub_id' => $item['sub_id'],
                                'acc_id' => $item['acc_id'],
                                'itm_id' => $item['itm_id'],
                                'description' => $item['description'],
                                'price' => $item['price'],
                                'amount' => $item['amount'],
                                'wct_id' => $item['wct_id'],
                                'dpt_id' => $item['dpt_id'],
                                'month' => $item['month'],
                                'status' => 1,
                            ]);
                            $totalAmount += $item['price'];
                            $processedRows++;
                        } catch (\Exception $e) {
                            $errors[] = "Gagal membuat record untuk sub_id: {$item['sub_id']}, bulan: $monthName, sheet: $sheetName, error: " . $e->getMessage();
                        }
                    }
                }
            } else {
                foreach ($data as $i => $row) {
                    if ($i === 0) {
                        continue;
                    }
                    $expectedColumns = $this->getExpectedColumns($template, $months);
                    if (count($row) < $expectedColumns) {
                        continue;
                    }
                    try {
                        if ($template === 'aftersales') {
                            [$no, $itm_id, $customer, $wct_id, $dpt_id] = array_slice($row, 0, 5);
                            $amount = $row[17] ?? null;
                            if (!$this->validateDepartment($userDept, $dpt_id)) {
                                $errors[] = "Invalid dpt_id pada baris $i di sheet $sheetName";
                                continue;
                            }
                            if (empty($itm_id) || empty($customer) || empty($dpt_id)) {
                                $errors[] = "Missing required fields in row $i of sheet $sheetName";
                                continue;
                            }
                            foreach (array_keys($months) as $index => $monthIndex) {
                                $monthValue = $row[5 + $index] ?? 0;
                                if ($monthValue == 0 || !is_numeric($monthValue)) continue;
                                $monthName = $monthNumberToName[$monthIndex + 1] ?? 'Unknown';
                                $model::create([
                                    'sub_id' => $sub_id,
                                    'acc_id' => $acc_id,
                                    'itm_id' => $itm_id,
                                    'customer' => $customer,
                                    'price' => (float)$monthValue,
                                    'amount' => $amount,
                                    'wct_id' => $wct_id,
                                    'dpt_id' => $dpt_id,
                                    'month' => $monthName,
                                    'status' => 1,
                                ]);
                                $totalAmount += (float)$monthValue;
                                $processedRows++;
                            }
                        } elseif ($template === 'support') {
                            [$no, $itm_id, $description, $wct_id, $dpt_id, $bdc_id, $lob_id] = array_slice($row, 0, 7);
                            $amount = $row[19] ?? null;
                            if (!$this->validateDepartment($userDept, $dpt_id)) {
                                $errors[] = "Invalid dpt_id pada baris $i di sheet $sheetName";
                                continue;
                            }
                            if (empty($itm_id) || empty($description) || empty($dpt_id)) {
                                $errors[] = "Missing required fields in row $i of sheet $sheetName";
                                continue;
                            }
                            foreach (array_keys($months) as $index => $monthIndex) {
                                $monthValue = $row[7 + $index] ?? 0;
                                if ($monthValue == 0 || !is_numeric($monthValue)) continue;
                                $monthName = $monthNumberToName[$monthIndex + 1] ?? 'Unknown';
                                $model::create([
                                    'sub_id' => $sub_id,
                                    'acc_id' => $acc_id,
                                    'itm_id' => $itm_id,
                                    'description' => $description,
                                    'price' => (float)$monthValue,
                                    'amount' => $amount,
                                    'wct_id' => $wct_id,
                                    'dpt_id' => $dpt_id,
                                    'bdc_id' => $bdc_id,
                                    'lob_id' => $lob_id,
                                    'month' => $monthName,
                                    'status' => 1,
                                ]);
                                $totalAmount += (float)$monthValue;
                                $processedRows++;
                            }
                        } elseif ($template === 'insurance') {
                            [$no, $description, $ins_id, $wct_id, $dpt_id] = array_slice($row, 0, 5);
                            $amount = $row[17] ?? null;
                            if (!$this->validateDepartment($userDept, $dpt_id)) continue;
                            if (empty($description) || empty($ins_id)) continue;
                            foreach (array_keys($months) as $index => $monthIndex) {
                                $monthValue = $row[5 + $index] ?? 0;
                                if ($monthValue == 0 || !is_numeric($monthValue)) continue;
                                $monthName = $monthNumberToName[$monthIndex + 1] ?? 'Unknown';
                                $model::create([
                                    'sub_id' => $sub_id,
                                    'acc_id' => $acc_id,
                                    'description' => $description,
                                    'ins_id' => $ins_id,
                                    'price' => (float)$monthValue,
                                    'amount' => $amount,
                                    'wct_id' => $wct_id,
                                    'dpt_id' => $dpt_id,
                                    'month' => $monthName,
                                    'status' => 1,
                                ]);
                                $totalAmount += (float)$monthValue;
                                $processedRows++;
                            }
                        } elseif ($template === 'utilities') {
                            [$no, $itm_id, $kwh, $wct_id, $dpt_id, $lob_id] = array_slice($row, 0, 6);
                            $amount = $row[18] ?? null;
                            if (!$this->validateDepartment($userDept, $dpt_id)) continue;
                            if (empty($itm_id) || empty($kwh)) continue;
                            foreach (array_keys($months) as $index => $monthIndex) {
                                $monthValue = $row[6 + $index] ?? 0;
                                if ($monthValue == 0 || !is_numeric($monthValue)) continue;
                                $monthName = $monthNumberToName[$monthIndex + 1] ?? 'Unknown';
                                $model::create([
                                    'sub_id' => $sub_id,
                                    'acc_id' => $acc_id,
                                    'itm_id' => $itm_id,
                                    'kwh' => $kwh,
                                    'price' => (float)$monthValue,
                                    'amount' => $amount,
                                    'wct_id' => $wct_id,
                                    'dpt_id' => $dpt_id,
                                    'lob_id' => $lob_id,
                                    'month' => $monthName,
                                    'status' => 1,
                                ]);
                                $totalAmount += (float)$monthValue;
                                $processedRows++;
                            }
                        } elseif ($template === 'business') {
                            [$no, $trip_propose, $destination, $days, $wct_id, $dpt_id] = array_slice($row, 0, 6);
                            $amount = $row[18] ?? null;
                            if (!$this->validateDepartment($userDept, $dpt_id)) continue;
                            if (empty($trip_propose) || empty($destination)) continue;
                            foreach (array_keys($months) as $index => $monthIndex) {
                                $monthValue = $row[6 + $index] ?? 0;
                                if ($monthValue == 0 || !is_numeric($monthValue)) continue;
                                $monthName = $monthNumberToName[$monthIndex + 1] ?? 'Unknown';
                                $model::create([
                                    'sub_id' => $sub_id,
                                    'acc_id' => $acc_id,
                                    'trip_propose' => $trip_propose,
                                    'destination' => $destination,
                                    'days' => (float)$days,
                                    'wct_id' => $wct_id,
                                    'dpt_id' => $dpt_id,
                                    'price' => (float)$monthValue,
                                    'month' => $monthName,
                                    'status' => 1,
                                    'amount' => $amount,
                                ]);
                                $totalAmount += (float)$monthValue;
                                $processedRows++;
                            }
                        } elseif ($template === 'representation') {
                            [$no, $itm_id, $description, $beneficiary, $wct_id, $dpt_id] = array_slice($row, 0, 6);
                            $amount = $row[18] ?? null;
                            if (!$this->validateDepartment($userDept, $dpt_id)) continue;
                            if (empty($itm_id) || empty($description)) continue;
                            foreach (array_keys($months) as $index => $monthIndex) {
                                $monthValue = $row[6 + $index] ?? 0;
                                if ($monthValue == 0 || !is_numeric($monthValue)) continue;
                                $monthName = $monthNumberToName[$monthIndex + 1] ?? 'Unknown';
                                $model::create([
                                    'sub_id' => $sub_id,
                                    'acc_id' => $acc_id,
                                    'itm_id' => $itm_id,
                                    'description' => $description,
                                    'beneficiary' => $beneficiary,
                                    'price' => (float)$monthValue,
                                    'amount' => $amount,
                                    'wct_id' => $wct_id,
                                    'dpt_id' => $dpt_id,
                                    'month' => $monthName,
                                    'status' => 1,
                                ]);
                                $totalAmount += (float)$monthValue;
                                $processedRows++;
                            }
                        } elseif ($template === 'training') {
                            [$no, $participant, $jenis_training, $quantity, $price, $wct_id, $dpt_id] = array_slice($row, 0, 7);
                            $amount = $row[19] ?? null;
                            if (!$this->validateDepartment($userDept, $dpt_id)) continue;
                            if (empty($participant) || empty($jenis_training)) continue;
                            foreach (array_keys($months) as $index => $monthIndex) {
                                $monthValue = $row[7 + $index] ?? 0;
                                if ($monthValue == 0 || !is_numeric($monthValue)) continue;
                                $monthName = $monthNumberToName[$monthIndex + 1] ?? 'Unknown';
                                $model::create([
                                    'sub_id' => $sub_id,
                                    'acc_id' => $acc_id,
                                    'participant' => $participant,
                                    'jenis_training' => $jenis_training,
                                    'quantity' => (float)$quantity,
                                    'price' => (float)$monthValue,
                                    'wct_id' => $wct_id,
                                    'dpt_id' => $dpt_id,
                                    'month' => $monthName,
                                    'status' => 1,
                                    'amount' => $amount,
                                ]);
                                $totalAmount += (float)$monthValue;
                                $processedRows++;
                            }
                        } elseif ($template === 'recruitment') {
                            [$no, $itm_id, $description, $position, $price, $wct_id, $dpt_id] = array_slice($row, 0, 7);
                            $amount = $row[19] ?? null;
                            if (!$this->validateDepartment($userDept, $dpt_id)) continue;
                            if (empty($itm_id) || empty($position)) continue;
                            foreach (array_keys($months) as $index => $monthIndex) {
                                $monthValue = $row[7 + $index] ?? 0;
                                if ($monthValue == 0 || !is_numeric($monthValue)) continue;
                                $monthName = $monthNumberToName[$monthIndex + 1] ?? 'Unknown';
                                $model::create([
                                    'sub_id' => $sub_id,
                                    'acc_id' => $acc_id,
                                    'itm_id' => $itm_id,
                                    'description' => $description,
                                    'position' => $position,
                                    'price' => (float)$monthValue,
                                    'amount' => $amount,
                                    'wct_id' => $wct_id,
                                    'dpt_id' => $dpt_id,
                                    'month' => $monthName,
                                    'status' => 1,
                                ]);
                                $totalAmount += (float)$monthValue;
                                $processedRows++;
                            }
                        } elseif ($template === 'employee') {
                            [$no, $type, $ledger_account, $ledger_account_description, $wct_id, $dpt_id, $bdc_id, $lob_id] = array_slice($row, 0, 8);
                            $amount = $row[20] ?? null;
                            if (empty(trim($type ?? '')) || empty(trim($dpt_id ?? ''))) {
                                continue;
                            }
                            $trimmedType = trim($type);
                            if (isset($prefixMap[$trimmedType])) {
                                $currentPrefix = $prefixMap[$trimmedType];
                                $currentAccId = $accIdMap[$trimmedType];
                            } else {
                                $currentPrefix = 'EMC';
                                $currentAccId = 'SGAEMPLOYCOMP';
                            }
                            $typeKey = $trimmedType . '_' . $sheetName;
                            if (!isset($employeeTypeMapping[$typeKey])) {
                                $lastRecord = $model::where('sub_id', 'like', "$currentPrefix%")
                                    ->orderBy('sub_id', 'desc')
                                    ->first();
                                $nextNumber = $lastRecord ? ((int)str_replace($currentPrefix, '', $lastRecord->sub_id) + 1) : 1;
                                $currentSubId = $currentPrefix . str_pad($nextNumber, 7, '0', STR_PAD_LEFT);
                                Approval::create([
                                    'approve_by' => $npk,
                                    'sub_id' => $currentSubId,
                                    'status' => 1,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]);
                                $employeeTypeMapping[$typeKey] = [
                                    'sub_id' => $currentSubId,
                                    'prefix' => $currentPrefix,
                                    'acc_id' => $currentAccId
                                ];
                            } else {
                                $currentSubId = $employeeTypeMapping[$typeKey]['sub_id'];
                                $currentAccId = $employeeTypeMapping[$typeKey]['acc_id'];
                            }
                            if (!$this->validateDepartment($userDept, $dpt_id)) continue;
                            foreach (array_keys($months) as $index => $monthIndex) {
                                $monthValue = $row[8 + $index] ?? 0;
                                if ($monthValue == 0 || !is_numeric($monthValue)) continue;
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
                                $totalAmount += (float)$monthValue;
                                $processedRows++;
                            }
                        } elseif ($template === 'purchase') {
                            [$no, $itm_id, $business_partner, $description, $wct_id, $dpt_id, $lob_id, $bdc_id] = array_slice($row, 0, 8);
                            $amount = $row[20] ?? null;
                            if (!$this->validateDepartment($userDept, $dpt_id)) continue;
                            if (empty($itm_id) || empty($business_partner)) continue;
                            foreach (array_keys($months) as $index => $monthIndex) {
                                $monthValue = $row[8 + $index] ?? 0;
                                if ($monthValue == 0 || !is_numeric($monthValue)) continue;
                                $monthName = $monthNumberToName[$monthIndex + 1] ?? 'Unknown';
                                $model::create([
                                    'sub_id' => $sub_id,
                                    'acc_id' => $acc_id,
                                    'itm_id' => $itm_id,
                                    'business_partner' => $business_partner,
                                    'description' => $description,
                                    'price' => (float)$monthValue,
                                    'amount' => $amount,
                                    'wct_id' => $wct_id,
                                    'dpt_id' => $dpt_id,
                                    'lob_id' => $lob_id,
                                    'bdc_id' => $bdc_id,
                                    'month' => $monthName,
                                    'status' => 1,
                                ]);
                                $totalAmount += (float)$monthValue;
                                $processedRows++;
                            }
                        }
                    } catch (\Exception $e) {
                        $errors[] = "Error processing row $i in sheet $sheetName: " . $e->getMessage();
                    }
                }
            }
        }

        if (!empty($errors)) {
            Log::warning('Upload completed with errors', [
                'errors' => $errors,
                'processed_rows' => $processedRows,
                'sheets_processed' => $processedSheets
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Upload completed with errors',
                'errors' => $errors,
                'data' => [
                    'sheets_processed' => $processedSheets,
                    'processed_rows' => $processedRows,
                    'total_amount' => $totalAmount ?? 0
                ]
            ], 400);
        }

        if ($processedRows === 0) {
            Log::warning('No rows were processed', ['sheets_processed' => $processedSheets]);
            return response()->json([
                'success' => false,
                'message' => 'No data was processed. Please check the file content or sheet names.',
                'data' => [
                    'sheets_processed' => $processedSheets,
                    'processed_rows' => $processedRows,
                    'total_amount' => $totalAmount ?? 0
                ]
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Upload berhasil diproses!',
            'data' => [
                'sheets_processed' => $processedSheets,
                'processed_rows' => $processedRows,
                'total_amount' => $totalAmount ?? 0,
                'processed_at' => now()->toDateTimeString()
            ]
        ]);
    }

    public function getDepartmentSummary(Request $request)
    {
        try {
            $query = BudgetRevision::query();

            if ($request->filled('periode')) {
                $query->whereYear('created_at', $request->periode);
            }

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
                    if ($item->dpt_id) {
                        $dept = Departments::where('dpt_id', $item->dpt_id)->first();
                        $item->department = $dept ? $dept->dpt_name : $item->dpt_id;
                        $item->dept_code = $item->dpt_id;
                    } else {
                        $item->department = 'Unknown Department';
                        $item->dept_code = '-';
                    }

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

        return response()->json([
            'success' => true,
            'message' => 'Upload berhasil diproses!',
            'data' => [
                'sheets_processed' => $processedSheets,
                'processed_rows' => $processedRows,
                'total_amount' => $totalAmount ?? 0,
                'processed_at' => now()->toDateTimeString()
            ]
        ]);
    }

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
                return 6 + count($months) + 1;
            case 'general':
            case 'aftersales':
            case 'insurance':
                return 5 + count($months) + 1;
            case 'support':
            case 'training':
            case 'recruitment':
                return 7 + count($months) + 1;
            default:
                return 0;
        }
    }
}
