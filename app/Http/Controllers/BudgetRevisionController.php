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
    public function index(Request $request)
    {
        try {
            $periode = $request->get('periode', '');
            $stats = [
                'total_revisions' => BudgetRevision::count(),
                'pending' => BudgetRevision::where('status', 0)->count(),
                'approved' => BudgetRevision::where('status', 1)->count(),
                'rejected' => BudgetRevision::where('status', 2)->count(),
                'total_amount' => BudgetRevision::sum('amount'),
            ];
            $query = BudgetUpload::with('uploader')
                ->where(function ($q) {
                    $q->where('data', 'like', '%"type":"revision"%')
                        ->orWhere('data', 'like', '%revision%')
                        ->orWhere('data', 'like', '%REV-%');
                });
            if ($periode) {
                $query->where('year', $periode);
            }
            $uploads = $query->orderBy('created_at', 'desc')->paginate(10);
            $summaryData = $this->getDepartmentSummaryData($periode);
            return view('budget-revision.index', compact('stats', 'uploads', 'summaryData', 'periode'));
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
    public function validateTotalBudgetPerMonth($deptId, $monthName, $year, $uploadData, $accId)
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

            $finalBudgetAmount = BudgetFinal::where('dept_code', $deptId)
                ->where('account', $accId)
                ->where('periode', $year)
                ->sum($monthColumn);

            $finalBudgetAmount = (float)($finalBudgetAmount ?? 0);

            if ($finalBudgetAmount == 0 && empty($uploadData)) {
                return [
                    'valid' => true,
                    'final_budget' => $finalBudgetAmount,
                    'existing_revision' => 0,
                    'upload_total' => 0
                ];
            }

            $uploadItmIds = array_unique(array_column($uploadData, 'itm_id'));
            $existingRevisionTotal = BudgetRevision::where('dpt_id', $deptId)
                ->where('acc_id', $accId)
                ->where('month', $monthName)
                ->whereYear('created_at', $year)
                ->whereNotIn('itm_id', $uploadItmIds)
                ->sum('price');
            $uploadTotal = array_sum(array_column($uploadData, 'price'));
            $totalAfterUpload = $existingRevisionTotal + $uploadTotal;

            $uploadTotal = 0;
            foreach ($uploadData as $item) {
                $uploadTotal += (float)$item['price'];
            }
            $totalAfterUpload = $existingRevisionTotal + $uploadTotal;

            Log::info('Budget Validation - TOTAL per Month', [
                'dept_id' => $deptId,
                'month' => $monthName,
                'year' => $year,
                'acc_id' => $accId,
                'final_budget' => $finalBudgetAmount,
                'existing_revision' => $existingRevisionTotal,
                'upload_total' => $uploadTotal,
                'total_after_upload' => $totalAfterUpload,
            ]);

            if ($totalAfterUpload != $finalBudgetAmount) {
                $difference = $totalAfterUpload - $finalBudgetAmount;
                return [
                    'valid' => false,
                    'message' => "Total budget $monthName tidak sesuai! " .
                        "Budget final: Rp " . number_format($finalBudgetAmount, 0, ',', '.') .
                        ", Total setelah upload: Rp " . number_format($totalAfterUpload, 0, ',', '.') .
                        " (Selisih: " . ($difference > 0 ? '+' : '') .
                        number_format($difference, 0, ',', '.') . ")",
                    'details' => [
                        'budget_final' => $finalBudgetAmount,
                        'total_upload' => $totalAfterUpload,
                        'difference' => $difference
                    ]
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
                'month' => $monthName,
                'acc_id' => $accId ?? 'unknown'
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
        $warnings = [];
        $successfulAccounts = [];
        $failedAccounts = [];
        $budgetErrors = [];
        $errorByType = [];
        $currentYear = date('Y');
        $employeeTypeMapping = [];
        $totalAmount = 0;
        $processedRows = 0;
        $processedSheets = [];
        $successByAccount = [];
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
        foreach ($spreadsheet->getSheetNames() as $sheetName) {
            if (!isset($sheetMappings[$sheetName])) {
                Log::warning("Sheet '$sheetName' not found in sheetMappings");
                continue;
            }
            if (!$this->checkBudgetFinalExists($userDept, $currentYear)) {
                $errors[] = "Tidak ada data budget final untuk departemen $userDept tahun $currentYear. Harap upload budget final terlebih dahulu.";
                Log::error("No budget final data for department: $userDept, year: $currentYear");
                $failedAccounts[] = $sheetName;
                continue;
            }
            $sheetConfig = $sheetMappings[$sheetName];
            $isMultiPrefix = isset($sheetConfig['prefix_map']);
            if ($isMultiPrefix) {
                $prefixMap = $sheetConfig['prefix_map'];
                $accIdMap = $sheetConfig['acc_id_map'];
            } else {
                $prefix = $sheetConfig['prefix'];
                $acc_id = $sheetConfig['acc_id'];
            }
            $model = $sheetConfig['model'];
            $template = $sheetConfig['template'];
            Log::info("Checking sheet: $sheetName, template: $template, model: $model");
            $sheet = $spreadsheet->getSheetByName($sheetName);
            $data = $sheet->toArray();
            Log::info("Sheet '$sheetName' has " . count($data) . " rows");
            $hasValidData = false;
            $hasAnyMonthlyData = false;
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
                    foreach (array_keys($months) as $index => $monthIndex) {
                        $monthValue = $row[5 + $index] ?? 0;
                        if ((float)$monthValue > 0) {
                            $hasAnyMonthlyData = true;
                            break 2;
                        }
                    }
                }
            }
            if (!$hasValidData) {
                Log::info("Sheet '$sheetName' has no valid data rows, skipping processing");
                continue;
            }
            if (!$hasAnyMonthlyData) {
                Log::info("Sheet '$sheetName' skipped: no monthly data >0, assuming intentional skip");
                continue;
            }
            $sub_id = null;
            $sheetRowCount = 0;
            if (!$isMultiPrefix) {
                $lastRecord = $model::where('sub_id', 'like', "$prefix%")
                    ->orderBy('sub_id', 'desc')
                    ->first();
                $nextNumber = $lastRecord ? ((int)str_replace($prefix, '', $lastRecord->sub_id) + 1) : 1;
                $sub_id = $prefix . str_pad($nextNumber, 7, '0', STR_PAD_LEFT);
            }
            $allMonthlyData = [];
            $localErrors = [];
            $localWarnings = [];
            $uploadItemsByMonth = [];
            if ($template === 'general') {
                foreach ($data as $i => $row) {
                    if ($i === 0) continue;
                    [$no, $itm_id, $description, $wct_id, $dpt_id] = array_slice($row, 0, 5);
                    $amount = $row[17] ?? null;
                    $sheetRowCount++;
                    $originalDpt = $dpt_id;
                    $hasRowMonthlyData = false;
                    foreach (array_keys($months) as $index => $monthIndex) {
                        $monthValue = $row[5 + $index] ?? 0;
                        if ((float)$monthValue > 0) {
                            $hasRowMonthlyData = true;
                            break;
                        }
                    }
                    if (!$hasRowMonthlyData) {
                        Log::info("Row $i in $sheetName skipped: no monthly data >0");
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
                            $errorMsg = "Invalid $fieldName pada baris $i di sheet $sheetName";
                            $localErrors[] = $errorMsg;
                            $errorType = 'missing_required';
                            if (!isset($errorByType[$errorType])) {
                                $errorByType[$errorType] = [
                                    'count' => 0,
                                    'description' => 'Missing Required Fields',
                                    'accounts' => []
                                ];
                            }
                            $errorByType[$errorType]['count']++;
                            if (!in_array($sheetName, $errorByType[$errorType]['accounts'])) {
                                $errorByType[$errorType]['accounts'][] = $sheetName;
                            }
                            $hasError = true;
                            break;
                        }
                    }
                    if ($hasError) continue;
                    foreach (array_keys($months) as $index => $monthIndex) {
                        $monthValue = $row[5 + $index] ?? 0;
                        if ($monthValue == 0 || $monthValue === null || trim($monthValue) === '') continue;
                        if (!is_numeric($monthValue)) {
                            $errorMsg = "Invalid numeric value for month $monthIndex in row $i";
                            $localErrors[] = $errorMsg;
                            $errorType = 'invalid_numeric';
                            if (!isset($errorByType[$errorType])) {
                                $errorByType[$errorType] = [
                                    'count' => 0,
                                    'description' => 'Invalid Numeric Value',
                                    'accounts' => []
                                ];
                            }
                            $errorByType[$errorType]['count']++;
                            if (!in_array($sheetName, $errorByType[$errorType]['accounts'])) {
                                $errorByType[$errorType]['accounts'][] = $sheetName;
                            }
                            continue;
                        }
                        $monthName = $monthNumberToName[$monthIndex + 1] ?? 'Unknown';
                        if (!isset($uploadItemsByMonth[$monthName])) {
                            $uploadItemsByMonth[$monthName] = [];
                        }
                        $uniqueKey = $acc_id . '_' . $itm_id . '_' . $monthName;
                        $existing = BudgetRevision::where('acc_id', $acc_id)
                            ->where('itm_id', $itm_id)
                            ->where('month', $monthName)
                            ->whereYear('created_at', $currentYear)
                            ->first();
                        $newPrice = (float)$monthValue;
                        $newAmount = (float)$amount;
                        $priceAdjustment = 0;
                        if ($existing) {
                            if ($existing->price == $newPrice) {
                                Log::info("Skipped: $uniqueKey (unchanged)");
                                continue;
                            } else {
                                $oldPrice = $existing->price;
                                $existing->update([
                                    'price' => $newPrice,
                                    'amount' => $newAmount
                                ]);
                                $priceAdjustment = $newPrice - $oldPrice;
                                Log::info("Replaced: $uniqueKey (old: $oldPrice, new: $newPrice)");
                            }
                        } else {
                            if (!isset($allMonthlyData[$monthName])) {
                                $allMonthlyData[$monthName] = [];
                            }
                            $allMonthlyData[$monthName][] = [
                                'sub_id' => $sub_id,
                                'acc_id' => $acc_id,
                                'itm_id' => $itm_id,
                                'description' => $description,
                                'price' => $newPrice,
                                'amount' => $newAmount,
                                'wct_id' => $wct_id,
                                'dpt_id' => $dpt_id,
                                'month' => $monthName,
                                'status' => 1,
                            ];
                            $priceAdjustment = $newPrice;
                            Log::info("Queued insert: $uniqueKey");
                        }
                        $uploadItemsByMonth[$monthName][] = [
                            'itm_id' => $itm_id,
                            'price' => $priceAdjustment
                        ];
                    }
                }
            } elseif ($template === 'aftersales') {
                foreach ($data as $i => $row) {
                    if ($i === 0) continue;
                    if (count($row) < $this->getExpectedColumns($template, $months)) continue;
                    [$no, $itm_id, $customer, $wct_id, $dpt_id] = array_slice($row, 0, 5);
                    $amount = $row[17] ?? null;
                    $sheetRowCount++;
                    $originalDpt = $dpt_id;
                    $dpt_id = trim($dpt_id ?? '') ?: $userDept;
                    if ($originalDpt !== $dpt_id) {
                        $localWarnings[] = "Auto-filled dpt_id ke $userDept untuk baris $i di sheet $sheetName";
                    }
                    if (!$this->validateDepartment($userDept, $dpt_id)) {
                        $localWarnings[] = "Invalid dpt_id pada baris $i di sheet $sheetName";
                        continue;
                    }
                    if (empty($itm_id) || empty($customer) || empty($dpt_id)) {
                        $errorMsg = "Missing required fields in row $i of sheet $sheetName";
                        $localErrors[] = $errorMsg;
                        $errorType = 'missing_required';
                        if (!isset($errorByType[$errorType])) {
                            $errorByType[$errorType] = [
                                'count' => 0,
                                'description' => 'Missing Required Fields',
                                'accounts' => []
                            ];
                        }
                        $errorByType[$errorType]['count']++;
                        if (!in_array($sheetName, $errorByType[$errorType]['accounts'])) {
                            $errorByType[$errorType]['accounts'][] = $sheetName;
                        }
                        continue;
                    }
                    foreach (array_keys($months) as $index => $monthIndex) {
                        $monthValue = $row[5 + $index] ?? 0;
                        if ($monthValue == 0 || !is_numeric($monthValue)) continue;
                        $monthName = $monthNumberToName[$monthIndex + 1] ?? 'Unknown';
                        if (!isset($uploadItemsByMonth[$monthName])) {
                            $uploadItemsByMonth[$monthName] = [];
                        }
                        $uniqueKey = $acc_id . '_' . $itm_id . '_' . $customer . '_' . $monthName;
                        $existing = BudgetRevision::where('acc_id', $acc_id)
                            ->where('itm_id', $itm_id)
                            ->where('customer', $customer)
                            ->where('month', $monthName)
                            ->whereYear('created_at', $currentYear)
                            ->first();
                        $newPrice = (float)$monthValue;
                        $newAmount = (float)$amount;
                        $priceAdjustment = 0;
                        if ($existing) {
                            if ($existing->price == $newPrice) {
                                Log::info("Skipped: $uniqueKey (unchanged)");
                                continue;
                            } else {
                                $oldPrice = $existing->price;
                                $existing->update([
                                    'price' => $newPrice,
                                    'amount' => $newAmount
                                ]);
                                $priceAdjustment = $newPrice - $oldPrice;
                                Log::info("Replaced: $uniqueKey (old: $oldPrice, new: $newPrice)");
                            }
                        } else {
                            if (!isset($allMonthlyData[$monthName])) {
                                $allMonthlyData[$monthName] = [];
                            }
                            $allMonthlyData[$monthName][] = [
                                'sub_id' => $sub_id,
                                'acc_id' => $acc_id,
                                'itm_id' => $itm_id,
                                'customer' => $customer,
                                'price' => $newPrice,
                                'amount' => $newAmount,
                                'wct_id' => $wct_id,
                                'dpt_id' => $dpt_id,
                                'month' => $monthName,
                                'status' => 1,
                            ];
                            $priceAdjustment = $newPrice;
                            Log::info("Queued insert: $uniqueKey");
                        }
                        $uploadItemsByMonth[$monthName][] = [
                            'itm_id' => $itm_id,
                            'price' => $priceAdjustment
                        ];
                    }
                }
            } elseif ($template === 'support') {
                foreach ($data as $i => $row) {
                    if ($i === 0) continue;
                    if (count($row) < $this->getExpectedColumns($template, $months)) continue;
                    [$no, $itm_id, $description, $wct_id, $dpt_id, $bdc_id, $lob_id] = array_slice($row, 0, 7);
                    $amount = $row[19] ?? null;
                    $sheetRowCount++;
                    $originalDpt = $dpt_id;
                    $dpt_id = trim($dpt_id ?? '') ?: $userDept;
                    if ($originalDpt !== $dpt_id) {
                        $localWarnings[] = "Auto-filled dpt_id ke $userDept untuk baris $i di sheet $sheetName";
                    }
                    if (!$this->validateDepartment($userDept, $dpt_id)) {
                        $localWarnings[] = "Invalid dpt_id pada baris $i di sheet $sheetName";
                        continue;
                    }
                    if (empty($itm_id) || empty($description) || empty($dpt_id)) {
                        $errorMsg = "Missing required fields in row $i of sheet $sheetName";
                        $localErrors[] = $errorMsg;
                        $errorType = 'missing_required';
                        if (!isset($errorByType[$errorType])) {
                            $errorByType[$errorType] = [
                                'count' => 0,
                                'description' => 'Missing Required Fields',
                                'accounts' => []
                            ];
                        }
                        $errorByType[$errorType]['count']++;
                        if (!in_array($sheetName, $errorByType[$errorType]['accounts'])) {
                            $errorByType[$errorType]['accounts'][] = $sheetName;
                        }
                        continue;
                    }
                    foreach (array_keys($months) as $index => $monthIndex) {
                        $monthValue = $row[7 + $index] ?? 0;
                        if ($monthValue == 0 || !is_numeric($monthValue)) continue;
                        $monthName = $monthNumberToName[$monthIndex + 1] ?? 'Unknown';
                        if (!isset($uploadItemsByMonth[$monthName])) {
                            $uploadItemsByMonth[$monthName] = [];
                        }
                        $uniqueKey = $acc_id . '_' . $itm_id . '_' . $monthName;
                        $existing = BudgetRevision::where('acc_id', $acc_id)
                            ->where('itm_id', $itm_id)
                            ->where('month', $monthName)
                            ->whereYear('created_at', $currentYear)
                            ->first();
                        $newPrice = (float)$monthValue;
                        $newAmount = (float)$amount;
                        $priceAdjustment = 0;
                        if ($existing) {
                            if ($existing->price == $newPrice) {
                                Log::info("Skipped: $uniqueKey (unchanged)");
                                continue;
                            } else {
                                $oldPrice = $existing->price;
                                $existing->update([
                                    'price' => $newPrice,
                                    'amount' => $newAmount
                                ]);
                                $priceAdjustment = $newPrice - $oldPrice;
                                Log::info("Replaced: $uniqueKey (old: $oldPrice, new: $newPrice)");
                            }
                        } else {
                            if (!isset($allMonthlyData[$monthName])) {
                                $allMonthlyData[$monthName] = [];
                            }
                            $allMonthlyData[$monthName][] = [
                                'sub_id' => $sub_id,
                                'acc_id' => $acc_id,
                                'itm_id' => $itm_id,
                                'description' => $description,
                                'price' => $newPrice,
                                'amount' => $newAmount,
                                'wct_id' => $wct_id,
                                'dpt_id' => $dpt_id,
                                'bdc_id' => $bdc_id,
                                'lob_id' => $lob_id,
                                'month' => $monthName,
                                'status' => 1,
                            ];
                            $priceAdjustment = $newPrice;
                            Log::info("Queued insert: $uniqueKey");
                        }
                        $uploadItemsByMonth[$monthName][] = [
                            'itm_id' => $itm_id,
                            'price' => $priceAdjustment
                        ];
                    }
                }
            } elseif ($template === 'insurance') {
                foreach ($data as $i => $row) {
                    if ($i === 0) continue;
                    if (count($row) < $this->getExpectedColumns($template, $months)) continue;
                    [$no, $description, $ins_id, $wct_id, $dpt_id] = array_slice($row, 0, 5);
                    $amount = $row[17] ?? null;
                    $sheetRowCount++;
                    $originalDpt = $dpt_id;
                    $dpt_id = trim($dpt_id ?? '') ?: $userDept;
                    if ($originalDpt !== $dpt_id) {
                        $localWarnings[] = "Auto-filled dpt_id ke $userDept untuk baris $i di sheet $sheetName";
                    }
                    if (!$this->validateDepartment($userDept, $dpt_id)) {
                        $localWarnings[] = "Invalid dpt_id pada baris $i di sheet $sheetName";
                        continue;
                    }
                    if (empty($description) || empty($ins_id)) {
                        $errorMsg = "Missing required fields in row $i of sheet $sheetName";
                        $localErrors[] = $errorMsg;
                        $errorType = 'missing_required';
                        if (!isset($errorByType[$errorType])) {
                            $errorByType[$errorType] = [
                                'count' => 0,
                                'description' => 'Missing Required Fields',
                                'accounts' => []
                            ];
                        }
                        $errorByType[$errorType]['count']++;
                        if (!in_array($sheetName, $errorByType[$errorType]['accounts'])) {
                            $errorByType[$errorType]['accounts'][] = $sheetName;
                        }
                        continue;
                    }
                    foreach (array_keys($months) as $index => $monthIndex) {
                        $monthValue = $row[5 + $index] ?? 0;
                        if ($monthValue == 0 || !is_numeric($monthValue)) continue;
                        $monthName = $monthNumberToName[$monthIndex + 1] ?? 'Unknown';
                        if (!isset($uploadItemsByMonth[$monthName])) {
                            $uploadItemsByMonth[$monthName] = [];
                        }
                        $uniqueKey = $acc_id . '_' . $ins_id . '_' . $monthName;
                        $existing = BudgetRevision::where('acc_id', $acc_id)
                            ->where('ins_id', $ins_id)
                            ->where('month', $monthName)
                            ->whereYear('created_at', $currentYear)
                            ->first();
                        $newPrice = (float)$monthValue;
                        $newAmount = (float)$amount;
                        $priceAdjustment = 0;
                        if ($existing) {
                            if ($existing->price == $newPrice) {
                                Log::info("Skipped: $uniqueKey (unchanged)");
                                continue;
                            } else {
                                $oldPrice = $existing->price;
                                $existing->update([
                                    'price' => $newPrice,
                                    'amount' => $newAmount
                                ]);
                                $priceAdjustment = $newPrice - $oldPrice;
                                Log::info("Replaced: $uniqueKey (old: $oldPrice, new: $newPrice)");
                            }
                        } else {
                            if (!isset($allMonthlyData[$monthName])) {
                                $allMonthlyData[$monthName] = [];
                            }
                            $allMonthlyData[$monthName][] = [
                                'sub_id' => $sub_id,
                                'acc_id' => $acc_id,
                                'description' => $description,
                                'ins_id' => $ins_id,
                                'price' => $newPrice,
                                'amount' => $newAmount,
                                'wct_id' => $wct_id,
                                'dpt_id' => $dpt_id,
                                'month' => $monthName,
                                'status' => 1,
                            ];
                            $priceAdjustment = $newPrice;
                            Log::info("Queued insert: $uniqueKey");
                        }
                        $uploadItemsByMonth[$monthName][] = [
                            'ins_id' => $ins_id,
                            'price' => $priceAdjustment
                        ];
                    }
                }
            } elseif ($template === 'utilities') {
                foreach ($data as $i => $row) {
                    if ($i === 0) continue;
                    if (count($row) < $this->getExpectedColumns($template, $months)) continue;
                    [$no, $itm_id, $kwh, $wct_id, $dpt_id, $lob_id] = array_slice($row, 0, 6);
                    $amount = $row[18] ?? null;
                    $sheetRowCount++;
                    $originalDpt = $dpt_id;
                    $dpt_id = trim($dpt_id ?? '') ?: $userDept;
                    if ($originalDpt !== $dpt_id) {
                        $localWarnings[] = "Auto-filled dpt_id ke $userDept untuk baris $i di sheet $sheetName";
                    }
                    if (!$this->validateDepartment($userDept, $dpt_id)) {
                        $localWarnings[] = "Invalid dpt_id pada baris $i di sheet $sheetName";
                        continue;
                    }
                    if (empty($itm_id) || empty($kwh)) {
                        $errorMsg = "Missing required fields in row $i of sheet $sheetName";
                        $localErrors[] = $errorMsg;
                        $errorType = 'missing_required';
                        if (!isset($errorByType[$errorType])) {
                            $errorByType[$errorType] = [
                                'count' => 0,
                                'description' => 'Missing Required Fields',
                                'accounts' => []
                            ];
                        }
                        $errorByType[$errorType]['count']++;
                        if (!in_array($sheetName, $errorByType[$errorType]['accounts'])) {
                            $errorByType[$errorType]['accounts'][] = $sheetName;
                        }
                        continue;
                    }
                    foreach (array_keys($months) as $index => $monthIndex) {
                        $monthValue = $row[6 + $index] ?? 0;
                        if ($monthValue == 0 || !is_numeric($monthValue)) continue;
                        $monthName = $monthNumberToName[$monthIndex + 1] ?? 'Unknown';
                        if (!isset($uploadItemsByMonth[$monthName])) {
                            $uploadItemsByMonth[$monthName] = [];
                        }
                        $uniqueKey = $acc_id . '_' . $itm_id . '_' . $monthName;
                        $existing = BudgetRevision::where('acc_id', $acc_id)
                            ->where('itm_id', $itm_id)
                            ->where('month', $monthName)
                            ->whereYear('created_at', $currentYear)
                            ->first();
                        $newPrice = (float)$monthValue;
                        $newAmount = (float)$amount;
                        $priceAdjustment = 0;
                        if ($existing) {
                            if ($existing->price == $newPrice) {
                                Log::info("Skipped: $uniqueKey (unchanged)");
                                continue;
                            } else {
                                $oldPrice = $existing->price;
                                $existing->update([
                                    'price' => $newPrice,
                                    'amount' => $newAmount
                                ]);
                                $priceAdjustment = $newPrice - $oldPrice;
                                Log::info("Replaced: $uniqueKey (old: $oldPrice, new: $newPrice)");
                            }
                        } else {
                            if (!isset($allMonthlyData[$monthName])) {
                                $allMonthlyData[$monthName] = [];
                            }
                            $allMonthlyData[$monthName][] = [
                                'sub_id' => $sub_id,
                                'acc_id' => $acc_id,
                                'itm_id' => $itm_id,
                                'kwh' => $kwh,
                                'price' => $newPrice,
                                'amount' => $newAmount,
                                'wct_id' => $wct_id,
                                'dpt_id' => $dpt_id,
                                'lob_id' => $lob_id,
                                'month' => $monthName,
                                'status' => 1,
                            ];
                            $priceAdjustment = $newPrice;
                            Log::info("Queued insert: $uniqueKey");
                        }
                        $uploadItemsByMonth[$monthName][] = [
                            'itm_id' => $itm_id,
                            'price' => $priceAdjustment
                        ];
                    }
                }
            } elseif ($template === 'business') {
                foreach ($data as $i => $row) {
                    if ($i === 0) continue;
                    if (count($row) < $this->getExpectedColumns($template, $months)) continue;
                    [$no, $trip_propose, $destination, $days, $wct_id, $dpt_id] = array_slice($row, 0, 6);
                    $amount = $row[18] ?? null;
                    $sheetRowCount++;
                    $originalDpt = $dpt_id;
                    $dpt_id = trim($dpt_id ?? '') ?: $userDept;
                    if ($originalDpt !== $dpt_id) {
                        $localWarnings[] = "Auto-filled dpt_id ke $userDept untuk baris $i di sheet $sheetName";
                    }
                    if (!$this->validateDepartment($userDept, $dpt_id)) {
                        $localWarnings[] = "Invalid dpt_id pada baris $i di sheet $sheetName";
                        continue;
                    }
                    if (empty($trip_propose) || empty($destination)) {
                        $errorMsg = "Missing required fields in row $i of sheet $sheetName";
                        $localErrors[] = $errorMsg;
                        $errorType = 'missing_required';
                        if (!isset($errorByType[$errorType])) {
                            $errorByType[$errorType] = [
                                'count' => 0,
                                'description' => 'Missing Required Fields',
                                'accounts' => []
                            ];
                        }
                        $errorByType[$errorType]['count']++;
                        if (!in_array($sheetName, $errorByType[$errorType]['accounts'])) {
                            $errorByType[$errorType]['accounts'][] = $sheetName;
                        }
                        continue;
                    }
                    foreach (array_keys($months) as $index => $monthIndex) {
                        $monthValue = $row[6 + $index] ?? 0;
                        if ($monthValue == 0 || !is_numeric($monthValue)) continue;
                        $monthName = $monthNumberToName[$monthIndex + 1] ?? 'Unknown';
                        if (!isset($uploadItemsByMonth[$monthName])) {
                            $uploadItemsByMonth[$monthName] = [];
                        }
                        $uniqueKey = $acc_id . '_' . $trip_propose . '_' . $destination . '_' . $monthName;
                        $existing = BudgetRevision::where('acc_id', $acc_id)
                            ->where('trip_propose', $trip_propose)
                            ->where('destination', $destination)
                            ->where('month', $monthName)
                            ->whereYear('created_at', $currentYear)
                            ->first();
                        $newPrice = (float)$monthValue;
                        $newAmount = (float)$amount;
                        $priceAdjustment = 0;
                        if ($existing) {
                            if ($existing->price == $newPrice) {
                                Log::info("Skipped: $uniqueKey (unchanged)");
                                continue;
                            } else {
                                $oldPrice = $existing->price;
                                $existing->update([
                                    'price' => $newPrice,
                                    'amount' => $newAmount,
                                    'days' => (float)$days
                                ]);
                                $priceAdjustment = $newPrice - $oldPrice;
                                Log::info("Replaced: $uniqueKey (old: $oldPrice, new: $newPrice)");
                            }
                        } else {
                            if (!isset($allMonthlyData[$monthName])) {
                                $allMonthlyData[$monthName] = [];
                            }
                            $allMonthlyData[$monthName][] = [
                                'sub_id' => $sub_id,
                                'acc_id' => $acc_id,
                                'trip_propose' => $trip_propose,
                                'destination' => $destination,
                                'days' => (float)$days,
                                'price' => $newPrice,
                                'amount' => $newAmount,
                                'wct_id' => $wct_id,
                                'dpt_id' => $dpt_id,
                                'month' => $monthName,
                                'status' => 1,
                            ];
                            $priceAdjustment = $newPrice;
                            Log::info("Queued insert: $uniqueKey");
                        }
                        $uploadItemsByMonth[$monthName][] = [
                            'trip_propose' => $trip_propose,
                            'price' => $priceAdjustment
                        ];
                    }
                }
            } elseif ($template === 'representation') {
                foreach ($data as $i => $row) {
                    if ($i === 0) continue;
                    if (count($row) < $this->getExpectedColumns($template, $months)) continue;
                    [$no, $itm_id, $description, $beneficiary, $wct_id, $dpt_id] = array_slice($row, 0, 6);
                    $amount = $row[18] ?? null;
                    $sheetRowCount++;
                    $originalDpt = $dpt_id;
                    $dpt_id = trim($dpt_id ?? '') ?: $userDept;
                    if ($originalDpt !== $dpt_id) {
                        $localWarnings[] = "Auto-filled dpt_id ke $userDept untuk baris $i di sheet $sheetName";
                    }
                    if (!$this->validateDepartment($userDept, $dpt_id)) {
                        $localWarnings[] = "Invalid dpt_id pada baris $i di sheet $sheetName";
                        continue;
                    }
                    if (empty($itm_id) || empty($description)) {
                        $errorMsg = "Missing required fields in row $i of sheet $sheetName";
                        $localErrors[] = $errorMsg;
                        $errorType = 'missing_required';
                        if (!isset($errorByType[$errorType])) {
                            $errorByType[$errorType] = [
                                'count' => 0,
                                'description' => 'Missing Required Fields',
                                'accounts' => []
                            ];
                        }
                        $errorByType[$errorType]['count']++;
                        if (!in_array($sheetName, $errorByType[$errorType]['accounts'])) {
                            $errorByType[$errorType]['accounts'][] = $sheetName;
                        }
                        continue;
                    }
                    foreach (array_keys($months) as $index => $monthIndex) {
                        $monthValue = $row[6 + $index] ?? 0;
                        if ($monthValue == 0 || !is_numeric($monthValue)) continue;
                        $monthName = $monthNumberToName[$monthIndex + 1] ?? 'Unknown';
                        if (!isset($uploadItemsByMonth[$monthName])) {
                            $uploadItemsByMonth[$monthName] = [];
                        }
                        $uniqueKey = $acc_id . '_' . $itm_id . '_' . $monthName;
                        $existing = BudgetRevision::where('acc_id', $acc_id)
                            ->where('itm_id', $itm_id)
                            ->where('month', $monthName)
                            ->whereYear('created_at', $currentYear)
                            ->first();
                        $newPrice = (float)$monthValue;
                        $newAmount = (float)$amount;
                        $priceAdjustment = 0;
                        if ($existing) {
                            if ($existing->price == $newPrice) {
                                Log::info("Skipped: $uniqueKey (unchanged)");
                                continue;
                            } else {
                                $oldPrice = $existing->price;
                                $existing->update([
                                    'price' => $newPrice,
                                    'amount' => $newAmount
                                ]);
                                $priceAdjustment = $newPrice - $oldPrice;
                                Log::info("Replaced: $uniqueKey (old: $oldPrice, new: $newPrice)");
                            }
                        } else {
                            if (!isset($allMonthlyData[$monthName])) {
                                $allMonthlyData[$monthName] = [];
                            }
                            $allMonthlyData[$monthName][] = [
                                'sub_id' => $sub_id,
                                'acc_id' => $acc_id,
                                'itm_id' => $itm_id,
                                'description' => $description,
                                'beneficiary' => $beneficiary,
                                'price' => $newPrice,
                                'amount' => $newAmount,
                                'wct_id' => $wct_id,
                                'dpt_id' => $dpt_id,
                                'month' => $monthName,
                                'status' => 1,
                            ];
                            $priceAdjustment = $newPrice;
                            Log::info("Queued insert: $uniqueKey");
                        }
                        $uploadItemsByMonth[$monthName][] = [
                            'itm_id' => $itm_id,
                            'price' => $priceAdjustment
                        ];
                    }
                }
            } elseif ($template === 'training') {
                foreach ($data as $i => $row) {
                    if ($i === 0) continue;
                    if (count($row) < $this->getExpectedColumns($template, $months)) continue;
                    [$no, $participant, $jenis_training, $quantity, $price, $wct_id, $dpt_id] = array_slice($row, 0, 7);
                    $amount = $row[19] ?? null;
                    $sheetRowCount++;
                    $originalDpt = $dpt_id;
                    $dpt_id = trim($dpt_id ?? '') ?: $userDept;
                    if ($originalDpt !== $dpt_id) {
                        $localWarnings[] = "Auto-filled dpt_id ke $userDept untuk baris $i di sheet $sheetName";
                    }
                    if (!$this->validateDepartment($userDept, $dpt_id)) {
                        $localWarnings[] = "Invalid dpt_id pada baris $i di sheet $sheetName";
                        continue;
                    }
                    if (empty($participant) || empty($jenis_training)) {
                        $errorMsg = "Missing required fields in row $i of sheet $sheetName";
                        $localErrors[] = $errorMsg;
                        $errorType = 'missing_required';
                        if (!isset($errorByType[$errorType])) {
                            $errorByType[$errorType] = [
                                'count' => 0,
                                'description' => 'Missing Required Fields',
                                'accounts' => []
                            ];
                        }
                        $errorByType[$errorType]['count']++;
                        if (!in_array($sheetName, $errorByType[$errorType]['accounts'])) {
                            $errorByType[$errorType]['accounts'][] = $sheetName;
                        }
                        continue;
                    }
                    foreach (array_keys($months) as $index => $monthIndex) {
                        $monthValue = $row[7 + $index] ?? 0;
                        if ($monthValue == 0 || !is_numeric($monthValue)) continue;
                        $monthName = $monthNumberToName[$monthIndex + 1] ?? 'Unknown';
                        if (!isset($uploadItemsByMonth[$monthName])) {
                            $uploadItemsByMonth[$monthName] = [];
                        }
                        $uniqueKey = $acc_id . '_' . $participant . '_' . $jenis_training . '_' . $monthName;
                        $existing = BudgetRevision::where('acc_id', $acc_id)
                            ->where('participant', $participant)
                            ->where('jenis_training', $jenis_training)
                            ->where('month', $monthName)
                            ->whereYear('created_at', $currentYear)
                            ->first();
                        $newPrice = (float)$monthValue;
                        $newAmount = (float)$amount;
                        $priceAdjustment = 0;
                        if ($existing) {
                            if ($existing->price == $newPrice) {
                                Log::info("Skipped: $uniqueKey (unchanged)");
                                continue;
                            } else {
                                $oldPrice = $existing->price;
                                $existing->update([
                                    'price' => $newPrice,
                                    'amount' => $newAmount,
                                    'quantity' => (float)$quantity
                                ]);
                                $priceAdjustment = $newPrice - $oldPrice;
                                Log::info("Replaced: $uniqueKey (old: $oldPrice, new: $newPrice)");
                            }
                        } else {
                            if (!isset($allMonthlyData[$monthName])) {
                                $allMonthlyData[$monthName] = [];
                            }
                            $allMonthlyData[$monthName][] = [
                                'sub_id' => $sub_id,
                                'acc_id' => $acc_id,
                                'participant' => $participant,
                                'jenis_training' => $jenis_training,
                                'quantity' => (float)$quantity,
                                'price' => $newPrice,
                                'amount' => $newAmount,
                                'wct_id' => $wct_id,
                                'dpt_id' => $dpt_id,
                                'month' => $monthName,
                                'status' => 1,
                            ];
                            $priceAdjustment = $newPrice;
                            Log::info("Queued insert: $uniqueKey");
                        }
                        $uploadItemsByMonth[$monthName][] = [
                            'participant' => $participant,
                            'price' => $priceAdjustment
                        ];
                    }
                }
            } elseif ($template === 'recruitment') {
                foreach ($data as $i => $row) {
                    if ($i === 0) continue;
                    if (count($row) < $this->getExpectedColumns($template, $months)) continue;
                    [$no, $itm_id, $description, $position, $price, $wct_id, $dpt_id] = array_slice($row, 0, 7);
                    $amount = $row[19] ?? null;
                    $sheetRowCount++;
                    $originalDpt = $dpt_id;
                    $dpt_id = trim($dpt_id ?? '') ?: $userDept;
                    if ($originalDpt !== $dpt_id) {
                        $localWarnings[] = "Auto-filled dpt_id ke $userDept untuk baris $i di sheet $sheetName";
                    }
                    if (!$this->validateDepartment($userDept, $dpt_id)) {
                        $localWarnings[] = "Invalid dpt_id pada baris $i di sheet $sheetName";
                        continue;
                    }
                    if (empty($itm_id) || empty($position)) {
                        $errorMsg = "Missing required fields in row $i of sheet $sheetName";
                        $localErrors[] = $errorMsg;
                        $errorType = 'missing_required';
                        if (!isset($errorByType[$errorType])) {
                            $errorByType[$errorType] = [
                                'count' => 0,
                                'description' => 'Missing Required Fields',
                                'accounts' => []
                            ];
                        }
                        $errorByType[$errorType]['count']++;
                        if (!in_array($sheetName, $errorByType[$errorType]['accounts'])) {
                            $errorByType[$errorType]['accounts'][] = $sheetName;
                        }
                        continue;
                    }
                    foreach (array_keys($months) as $index => $monthIndex) {
                        $monthValue = $row[7 + $index] ?? 0;
                        if ($monthValue == 0 || !is_numeric($monthValue)) continue;
                        $monthName = $monthNumberToName[$monthIndex + 1] ?? 'Unknown';
                        if (!isset($uploadItemsByMonth[$monthName])) {
                            $uploadItemsByMonth[$monthName] = [];
                        }
                        $uniqueKey = $acc_id . '_' . $itm_id . '_' . $position . '_' . $monthName;
                        $existing = BudgetRevision::where('acc_id', $acc_id)
                            ->where('itm_id', $itm_id)
                            ->where('position', $position)
                            ->where('month', $monthName)
                            ->whereYear('created_at', $currentYear)
                            ->first();
                        $newPrice = (float)$monthValue;
                        $newAmount = (float)$amount;
                        $priceAdjustment = 0;
                        if ($existing) {
                            if ($existing->price == $newPrice) {
                                Log::info("Skipped: $uniqueKey (unchanged)");
                                continue;
                            } else {
                                $oldPrice = $existing->price;
                                $existing->update([
                                    'price' => $newPrice,
                                    'amount' => $newAmount
                                ]);
                                $priceAdjustment = $newPrice - $oldPrice;
                                Log::info("Replaced: $uniqueKey (old: $oldPrice, new: $newPrice)");
                            }
                        } else {
                            if (!isset($allMonthlyData[$monthName])) {
                                $allMonthlyData[$monthName] = [];
                            }
                            $allMonthlyData[$monthName][] = [
                                'sub_id' => $sub_id,
                                'acc_id' => $acc_id,
                                'itm_id' => $itm_id,
                                'description' => $description,
                                'position' => $position,
                                'price' => $newPrice,
                                'amount' => $newAmount,
                                'wct_id' => $wct_id,
                                'dpt_id' => $dpt_id,
                                'month' => $monthName,
                                'status' => 1,
                            ];
                            $priceAdjustment = $newPrice;
                            Log::info("Queued insert: $uniqueKey");
                        }
                        $uploadItemsByMonth[$monthName][] = [
                            'itm_id' => $itm_id,
                            'price' => $priceAdjustment
                        ];
                    }
                }
            } elseif ($template === 'employee') {
                foreach ($data as $i => $row) {
                    if ($i === 0) continue;
                    if (count($row) < $this->getExpectedColumns($template, $months)) continue;
                    [$no, $type, $ledger_account, $ledger_account_description, $wct_id, $dpt_id, $bdc_id, $lob_id] = array_slice($row, 0, 8);
                    $amount = $row[20] ?? null;
                    $sheetRowCount++;
                    if (empty(trim($type ?? ''))) {
                        $errorMsg = "Type kosong di baris $i of sheet $sheetName";
                        $localErrors[] = $errorMsg;
                        $errorType = 'missing_required';
                        if (!isset($errorByType[$errorType])) {
                            $errorByType[$errorType] = [
                                'count' => 0,
                                'description' => 'Missing Required Fields',
                                'accounts' => []
                            ];
                        }
                        $errorByType[$errorType]['count']++;
                        if (!in_array($sheetName, $errorByType[$errorType]['accounts'])) {
                            $errorByType[$errorType]['accounts'][] = $sheetName;
                        }
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
                        try {
                            Approval::create([
                                'approve_by' => $npk,
                                'sub_id' => $currentSubId,
                                'status' => 1,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        } catch (\Exception $e) {
                            $localErrors[] = "Failed to create approval for type $trimmedType: " . $e->getMessage();
                            continue;
                        }
                        $employeeTypeMapping[$typeKey] = [
                            'sub_id' => $currentSubId,
                            'acc_id' => $currentAccId
                        ];
                    } else {
                        $currentSubId = $employeeTypeMapping[$typeKey]['sub_id'];
                        $currentAccId = $employeeTypeMapping[$typeKey]['acc_id'];
                    }
                    $originalDpt = $dpt_id;
                    $dpt_id = trim($dpt_id ?? '') ?: $userDept;
                    if ($originalDpt !== $dpt_id) {
                        $localWarnings[] = "Auto-filled dpt_id ke $userDept untuk baris $i di sheet $sheetName";
                    }
                    if (!$this->validateDepartment($userDept, $dpt_id)) {
                        $localWarnings[] = "Invalid dpt_id pada baris $i di sheet $sheetName";
                        continue;
                    }
                    foreach (array_keys($months) as $index => $monthIndex) {
                        $monthValue = $row[8 + $index] ?? 0;
                        if ($monthValue == 0 || !is_numeric($monthValue)) continue;
                        $monthName = $monthNumberToName[$monthIndex + 1] ?? 'Unknown';
                        if (!isset($uploadItemsByMonth[$monthName])) {
                            $uploadItemsByMonth[$monthName] = [];
                        }
                        $uniqueKey = $currentAccId . '_' . $ledger_account . '_' . $monthName;
                        $existing = BudgetRevision::where('acc_id', $currentAccId)
                            ->where('ledger_account', $ledger_account)
                            ->where('month', $monthName)
                            ->whereYear('created_at', $currentYear)
                            ->first();
                        $newPrice = (float)$monthValue;
                        $newAmount = (float)$amount;
                        $priceAdjustment = 0;
                        if ($existing) {
                            if ($existing->price == $newPrice) {
                                Log::info("Skipped: $uniqueKey (unchanged)");
                                continue;
                            } else {
                                $oldPrice = $existing->price;
                                $existing->update([
                                    'price' => $newPrice,
                                    'amount' => $newAmount
                                ]);
                                $priceAdjustment = $newPrice - $oldPrice;
                                Log::info("Replaced: $uniqueKey (old: $oldPrice, new: $newPrice)");
                            }
                        } else {
                            if (!isset($allMonthlyData[$monthName])) {
                                $allMonthlyData[$monthName] = [];
                            }
                            $allMonthlyData[$monthName][] = [
                                'sub_id' => $currentSubId,
                                'acc_id' => $currentAccId,
                                'ledger_account' => $ledger_account,
                                'ledger_account_description' => $ledger_account_description,
                                'price' => $newPrice,
                                'amount' => $newAmount,
                                'wct_id' => $wct_id,
                                'dpt_id' => $dpt_id,
                                'bdc_id' => $bdc_id,
                                'lob_id' => $lob_id,
                                'month' => $monthName,
                                'status' => 1,
                            ];
                            $priceAdjustment = $newPrice;
                            Log::info("Queued insert: $uniqueKey");
                        }
                        $uploadItemsByMonth[$monthName][] = [
                            'ledger_account' => $ledger_account,
                            'price' => $priceAdjustment
                        ];
                    }
                }
            } elseif ($template === 'purchase') {
                foreach ($data as $i => $row) {
                    if ($i === 0) continue;
                    if (count($row) < $this->getExpectedColumns($template, $months)) continue;
                    [$no, $itm_id, $business_partner, $description, $wct_id, $dpt_id, $lob_id, $bdc_id] = array_slice($row, 0, 8);
                    $amount = $row[20] ?? null;
                    $sheetRowCount++;
                    $originalDpt = $dpt_id;
                    $dpt_id = trim($dpt_id ?? '') ?: $userDept;
                    if ($originalDpt !== $dpt_id) {
                        $localWarnings[] = "Auto-filled dpt_id ke $userDept untuk baris $i di sheet $sheetName";
                    }
                    if (!$this->validateDepartment($userDept, $dpt_id)) {
                        $localWarnings[] = "Invalid dpt_id pada baris $i di sheet $sheetName";
                        continue;
                    }
                    if (empty($itm_id) || empty($business_partner)) {
                        $errorMsg = "Missing required fields in row $i of sheet $sheetName";
                        $localErrors[] = $errorMsg;
                        $errorType = 'missing_required';
                        if (!isset($errorByType[$errorType])) {
                            $errorByType[$errorType] = [
                                'count' => 0,
                                'description' => 'Missing Required Fields',
                                'accounts' => []
                            ];
                        }
                        $errorByType[$errorType]['count']++;
                        if (!in_array($sheetName, $errorByType[$errorType]['accounts'])) {
                            $errorByType[$errorType]['accounts'][] = $sheetName;
                        }
                        continue;
                    }
                    foreach (array_keys($months) as $index => $monthIndex) {
                        $monthValue = $row[8 + $index] ?? 0;
                        if ($monthValue == 0 || !is_numeric($monthValue)) continue;
                        $monthName = $monthNumberToName[$monthIndex + 1] ?? 'Unknown';
                        if (!isset($uploadItemsByMonth[$monthName])) {
                            $uploadItemsByMonth[$monthName] = [];
                        }
                        $uniqueKey = $acc_id . '_' . $itm_id . '_' . $business_partner . '_' . $monthName;
                        $existing = BudgetRevision::where('acc_id', $acc_id)
                            ->where('itm_id', $itm_id)
                            ->where('business_partner', $business_partner)
                            ->where('month', $monthName)
                            ->whereYear('created_at', $currentYear)
                            ->first();
                        $newPrice = (float)$monthValue;
                        $newAmount = (float)$amount;
                        $priceAdjustment = 0;
                        if ($existing) {
                            if ($existing->price == $newPrice) {
                                Log::info("Skipped: $uniqueKey (unchanged)");
                                continue;
                            } else {
                                $oldPrice = $existing->price;
                                $existing->update([
                                    'price' => $newPrice,
                                    'amount' => $newAmount
                                ]);
                                $priceAdjustment = $newPrice - $oldPrice;
                                Log::info("Replaced: $uniqueKey (old: $oldPrice, new: $newPrice)");
                            }
                        } else {
                            if (!isset($allMonthlyData[$monthName])) {
                                $allMonthlyData[$monthName] = [];
                            }
                            $allMonthlyData[$monthName][] = [
                                'sub_id' => $sub_id,
                                'acc_id' => $acc_id,
                                'itm_id' => $itm_id,
                                'business_partner' => $business_partner,
                                'description' => $description,
                                'price' => $newPrice,
                                'amount' => $newAmount,
                                'wct_id' => $wct_id,
                                'dpt_id' => $dpt_id,
                                'lob_id' => $lob_id,
                                'bdc_id' => $bdc_id,
                                'month' => $monthName,
                                'status' => 1,
                            ];
                            $priceAdjustment = $newPrice;
                            Log::info("Queued insert: $uniqueKey");
                        }
                        $uploadItemsByMonth[$monthName][] = [
                            'itm_id' => $itm_id,
                            'price' => $priceAdjustment
                        ];
                    }
                }
            }
            $validationErrors = [];
            foreach ($uploadItemsByMonth as $monthName => $items) {
                if (empty($items)) continue;
                $dpt_id = $items[0]['dpt_id'] ?? $userDept;
                $accIdForValidation = $isMultiPrefix ? $currentAccId : $acc_id;

                $budgetValidation = $this->validateTotalBudgetPerMonth(
                    $dpt_id,
                    $monthName,
                    $currentYear,
                    $items,
                    $accIdForValidation
                );
                if (!$budgetValidation['valid']) {
                    $validationErrors[] = "Bulan $monthName: " . $budgetValidation['message'];
                    if (isset($budgetValidation['details'])) {
                        $difference = $budgetValidation['details']['difference'];
                        $budgetErrors[] = [
                            'account' => $sheetName,
                            'month' => $monthName,
                            'budget_final' => number_format($budgetValidation['details']['budget_final'], 0, ',', '.'),
                            'total_upload' => number_format($budgetValidation['details']['total_upload'], 0, ',', '.'),
                            'difference' => ($difference > 0 ? '+' : '') . number_format(abs($difference), 0, ',', '.')
                        ];
                    }
                }
            }
            if (!empty($validationErrors)) {
                $localErrors = array_merge($localErrors, $validationErrors);
            }
            if (empty($allMonthlyData) && empty($uploadItemsByMonth)) {
                Log::info("Sheet $sheetName fully skipped: no data to process");
                continue;
            }

            $sheetSuccess = true;
            if (!empty($localErrors)) {
                $sheetSuccess = false;
                $errors = array_merge($errors, $localErrors);
                $failedAccounts[] = $sheetName . ' (' . implode('; ', $localErrors) . ')';
            } else {
                $warnings = array_merge($warnings, $localWarnings);
            }
            if ($sheetSuccess && !empty($allMonthlyData)) {
                foreach ($allMonthlyData as $monthName => $items) {
                    foreach ($items as $item) {
                        try {
                            $model::create($item);
                            $totalAmount += $item['price'];
                            $processedRows++;
                        } catch (\Exception $e) {
                            $sheetSuccess = false;
                            $errors[] = "Gagal insert row bulan $monthName: " . $e->getMessage();
                        }
                    }
                }
                $sheetTotal = 0;
                $sheetCount = 0;
                foreach ($allMonthlyData as $monthName => $items) {
                    if (!empty($items)) {
                        $sheetTotal += array_sum(array_column($items, 'price'));
                        $sheetCount += count($items);
                    }
                }
                $successByAccount[] = [
                    'account' => $sheetName,
                    'total' => $sheetTotal,
                    'count' => $sheetCount
                ];
            }
            if ($sheetSuccess && $sheetRowCount > 0) {
                if ($sub_id) {
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
                        $errors[] = "Failed to create approval for $sheetName: " . $e->getMessage();
                        $sheetSuccess = false;
                    }
                }
                $successfulAccounts[] = $sheetName;
                $processedSheets[] = $sheetName;
            } elseif (!$sheetSuccess) {
                $failedAccounts[] = $sheetName . ' (' . implode('; ', $localErrors) . ')';
            }
        }
        $isTotalFailure = ($processedRows === 0 && empty($successfulAccounts));
        $message = $isTotalFailure ? 'Upload Gagal. Periksa detail error di bawah.' : 'Upload selesai. ';
        $request->session()->flash('success', $message);
        $request->session()->flash('success_accounts', $successfulAccounts);
        $request->session()->flash('processed_rows', $processedRows);
        $request->session()->flash('total_amount', $totalAmount);
        if (!empty($successByAccount)) {
            $request->session()->flash('success_by_account', $successByAccount);
        }
        $failedAccounts = array_unique($failedAccounts);

        if (!empty($errors) || !empty($budgetErrors)) {
            $request->session()->flash('error_summary', [
                'total_failed' => count($errors),
                'budget_errors' => $budgetErrors,
                'by_type' => $errorByType,
                'failed_accounts' => $failedAccounts
            ]);
            $request->session()->flash('failed_accounts', $failedAccounts);
        }
        if ($isTotalFailure) {
            Log::warning('No rows were processed', ['sheets_processed' => $processedSheets]);
            return response()->json([
                'success' => false,
                'message' => $message,
                'data' => [
                    'sheets_processed' => $processedSheets,
                    'processed_rows' => $processedRows,
                    'total_amount' => $totalAmount ?? 0
                ]
            ], 400);
        }
        Log::info('Upload completed', [
            'successful_accounts' => $successfulAccounts,
            'failed_accounts' => $failedAccounts,
            'processed_rows' => $processedRows,
            'sheets_processed' => $processedSheets
        ]);
        if (!empty($warnings)) {
            $message .= ' Warnings: ' . implode('; ', $warnings) . '.';
        }
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'successful_accounts' => $successfulAccounts,
                'failed_accounts' => $failedAccounts,
                'sheets_processed' => $processedSheets,
                'processed_rows' => $processedRows,
                'total_amount' => $totalAmount ?? 0,
                'success_by_account' => $successByAccount,
                'processed_at' => now()->toDateTimeString()
            ]
        ], 200);
    }
    public function getDepartmentSummaryData($periode = '')
    {
        try {
            $query = BudgetRevision::query();
            if ($periode) {
                $query->whereYear('created_at', $periode);
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
                    $item->total_amount = number_format($item->total_amount ?? 0, 0, ',', '.');
                    $item->last_upload = $item->last_upload ? date('d/m/Y H:i', strtotime($item->last_upload)) : '-';
                    return $item;
                });
            return $summary;
        } catch (\Exception $e) {
            Log::error('Get summary error: ' . $e->getMessage());
            return collect();
        }
    }
    public function detail($revisionCode, Request $request)
    {
        try {
            $revisionData = BudgetRevision::where('sub_id', 'like', $revisionCode . '%')
                ->orderBy('itm_id')
                ->get();
            if ($revisionData->isEmpty()) {
                return response()->view('budget-revision.partials.no-data', [], 404);
            }
            $pivotedData = collect();
            $months = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];
            foreach ($revisionData->groupBy('itm_id') as $itmGroup) {
                $item = $itmGroup->first();
                $row = [
                    'acc_id' => $item->acc_id,
                    'itm_id' => $item->itm_id,
                    'description' => $item->description ?? $item->purpose ?? '-',
                    'wct_id' => $item->wct_id,
                    'dpt_id' => $item->dpt_id,
                    'periode' => $request->get('periode', date('Y')),
                    'created_at' => $item->created_at,
                ];
                foreach ($months as $month) {
                    $monthSum = $itmGroup->where('month', ucfirst($month))->sum('price') ?? 0;
                    $row[$month] = $monthSum;
                }
                $row['total'] = array_sum(array_column($itmGroup->toArray(), 'price')) ?? 0;
                $pivotedData->push((object) $row);
            }
            $revisionCode = $revisionCode;
            $title = $revisionData->first()->description ?? 'Revisi Budget ' . date('Y');
            $revisionData = $pivotedData;
            Log::info('Revision detail loaded', [
                'revision_code' => $revisionCode,
                'item_count' => $revisionData->count(),
                'total_amount' => $revisionData->sum('total')
            ]);
            return view('budget-revision.partials.revision-detail', compact('revisionCode', 'revisionData', 'title'));
        } catch (\Exception $e) {
            Log::error('Detail error: ' . $e->getMessage());
            return response()->view('budget-revision.partials.error', ['message' => 'Error loading detail'], 500);
        }
    }
    public function detailByDepartment($deptCode, Request $request)
    {
        try {
            $periode = $request->get('periode', date('Y'));
            $revisionData = BudgetRevision::where('dpt_id', $deptCode)
                ->whereYear('created_at', $periode)
                ->orderBy('created_at', 'desc')
                ->get();
            if ($revisionData->isEmpty()) {
                return response()->view('budget-revision.partials.no-data', [], 404);
            }
            $pivotedData = collect();
            $months = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];
            foreach ($revisionData->groupBy('itm_id') as $itmGroup) {
                $item = $itmGroup->first();
                $row = [
                    'acc_id' => $item->acc_id,
                    'itm_id' => $item->itm_id,
                    'description' => $item->description ?? $item->purpose ?? '-',
                    'wct_id' => $item->wct_id,
                    'dpt_id' => $item->dpt_id,
                    'periode' => $periode,
                    'created_at' => $item->created_at,
                ];
                foreach ($months as $month) {
                    $monthSum = $itmGroup->where('month', ucfirst($month))->sum('price') ?? 0;
                    $row[$month] = $monthSum;
                }
                $row['total'] = $itmGroup->sum('price') ?? 0;
                $pivotedData->push((object) $row);
            }
            $dept = Departments::where('dpt_id', $deptCode)->first();
            $title = "Budget Revision - " . ($dept->dpt_name ?? $deptCode) . " ($periode)";
            return view('budget-revision.partials.revision-detail', [
                'revisionCode' => $deptCode,
                'revisionData' => $pivotedData,
                'title' => $title,
                'periode' => $periode
            ]);
        } catch (\Exception $e) {
            Log::error('Detail by department error: ' . $e->getMessage());
            return response()->view(
                'budget-revision.partials.error',
                ['message' => 'Error loading detail: ' . $e->getMessage()],
                500
            );
        }
    }
    public function delete(Request $request)
    {
        try {
            $request->validate([
                'revision_code' => 'nullable|string',
                'dept' => 'nullable|string',
                'periode' => 'nullable|integer',
            ]);
            $query = BudgetRevision::query();
            if ($request->filled('revision_code')) {
                $query->where('sub_id', 'like', $request->revision_code . '%');
            } elseif ($request->filled('dept')) {
                $query->where('dpt_id', $request->dept);
                if ($request->filled('periode')) {
                    $query->whereYear('created_at', $request->periode);
                }
            } else {
                return response()->json(['success' => false, 'message' => 'Missing params'], 400);
            }
            $count = $query->count();
            $query->delete();
            Log::info('Budget revisions deleted', [
                'count' => $count,
                'year' => $request->periode,
                'revision_code' => $request->revision_code ?? 'dept:' . $request->dept,
                'deleted_by' => Auth::id(),
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                ], 200);
            }

            return redirect()->back();
        } catch (\Exception $e) {
            Log::error('Delete revisions error: ' . $e->getMessage());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error deleting data: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Error deleting data: ' . $e->getMessage());
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
