<?php

namespace App\Http\Controllers;

use App\Models\BudgetFyLo;
use Illuminate\Http\Request;
use App\Models\BudgetUpload;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class BudgetUploadController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'year' => 'required|numeric|min:2000|max:2100',
            'type' => 'required|in:last_year,outlook,proposal',
            'file' => 'required|file|mimes:xlsx,xls|max:10240'
        ]);

        $file = $request->file('file');
        $spreadsheet = IOFactory::load($file->getRealPath());
        $worksheet = $spreadsheet->getActiveSheet();

        $data = [];
        $headers = [];
        $startCollecting = false;

        foreach ($worksheet->getRowIterator() as $row) {
            $rowData = [];
            foreach ($row->getCellIterator() as $cell) {
                $rowData[] = $cell->getValue();
            }

            if (count(array_filter($rowData)) === 0) {
                continue;
            }

            if (in_array('ACCOUNT', $rowData)) {
                $headers = $rowData;
                $startCollecting = true;
                continue;
            }

            if ($startCollecting) {
                $account = $rowData[1] ?? null;
                $total = $rowData[20] ?? 0;

                if ($account && $account !== 'ACCOUNT') {
                    $data[] = [
                        'account' => $account,
                        'amount' => is_numeric($total) ? (float)$total : 0
                    ];
                }
            }
        }

        $path = $file->store('budget_uploads');

        $upload = BudgetUpload::create([
            'year' => $request->year,
            'type' => $request->type,
            'file_path' => $path,
            'uploaded_by' => auth()->id(),
            'data' => $data
        ]);

        return back()->with('success', 'Budget data uploaded successfully!');
    }

    public function uploadFyLo(Request $request)
    {
        $user = Auth::user();
        if ($user->dept !== '6121') {
            return redirect()->back()->with('error', 'Hanya departemen 6121 yang diizinkan untuk mengupload data.');
        }

        $request->validate([
            'type' => 'required|in:last_year,outlook,proposal',
            'file' => 'required|file|mimes:xlsx,xls|max:10240',
        ]);

        $file = $request->file('file');
        $type = $request->type;
        $currentYear = date('Y');
        $year = ($type === 'last_year') ? $currentYear : ($type === 'outlook' ? $currentYear + 1 : $currentYear);
        $user = Auth::user();

        try {
            $spreadsheet = IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            $headerRow = $rows[1] ?? [];
            $requiredHeaders = ['NR/R', 'ACCOUNT', 'BUDG', 'BUSS', 'WC', 'DEPT', 'DEPT CODE', 'CRITERIA TO MASTER'];
            $headerValid = true;
            foreach ($requiredHeaders as $header) {
                if (!in_array($header, $headerRow)) {
                    $headerValid = false;
                    break;
                }
            }
            if (!$headerValid) {
                Log::error('Format file Excel tidak valid. Header yang diharapkan: ' . implode(', ', $requiredHeaders));
                return back()->with('error', 'Format file Excel tidak valid. Pastikan header berisi: ' . implode(', ', $requiredHeaders));
            }

            array_shift($rows);
            array_shift($rows);

            $dataToSave = [];
            $dataForBudgetFyLos = [];

            foreach ($rows as $index => $row) {
                if (empty(array_filter($row))) {
                    Log::info("Baris $index dilewati karena kosong");
                    continue;
                }

                if (!isset($row[1]) || empty($row[1]) || !isset($row[21]) || !is_numeric($row[21])) {
                    Log::warning("Baris $index dilewati karena ACCOUNT atau Periode tidak valid: " . json_encode($row));
                    continue;
                }

                $excelPeriode = (int) $row[21];
                if (($type === 'last_year' && $excelPeriode != $year) || ($type === 'outlook' && $excelPeriode != $year)) {
                    Log::warning("Baris $index dilewati karena periode ($excelPeriode) tidak sesuai dengan type ($type) dan year ($year)");
                    continue;
                }

                $monthlyValues = array_slice($row, 8, 12);
                $monthlyValues = array_map(function ($value) {
                    $value = str_replace(',', '', $value);
                    return is_numeric($value) ? (float) $value : 0;
                }, $monthlyValues);

                $total = is_numeric($row[20]) ? (float) str_replace(',', '', $row[20]) : array_sum($monthlyValues);
                if ($total === 0 && array_sum($monthlyValues) === 0) {
                    Log::warning("Baris $index memiliki total 0 dan semua nilai bulanan tidak valid: " . json_encode($row));
                }

                $dataForBudgetFyLos[] = [
                    'tipe' => $type,
                    'periode' => $excelPeriode,
                    'r_nr' => $row[0] ?? null,
                    'account' => $row[1] ?? null,
                    'budget_code' => $row[2] ?? null,
                    'line_of_business' => $row[3] ?? null,
                    'wc' => $row[4] ?? null,
                    'dept' => $row[6] ?? $user->dept,
                    'dept_code' => $row[6] ?? null,
                    'criteria_to_master' => $row[7] ?? null,
                    'jan' => $monthlyValues[0],
                    'feb' => $monthlyValues[1],
                    'mar' => $monthlyValues[2],
                    'apr' => $monthlyValues[3],
                    'may' => $monthlyValues[4],
                    'jun' => $monthlyValues[5],
                    'jul' => $monthlyValues[6],
                    'aug' => $monthlyValues[7],
                    'sep' => $monthlyValues[8],
                    'oct' => $monthlyValues[9],
                    'nov' => $monthlyValues[10],
                    'dec' => $monthlyValues[11],
                    'total' => $total,
                    'created_at' => now(),
                    'updated_at' => now()
                ];

                $dataToSave[] = [
                    'account' => $row[1] ?? null,
                    'amount' => $total
                ];
            }

            Log::info("Jumlah baris yang akan disimpan ke budget_fy_los: " . count($dataForBudgetFyLos));

            BudgetUpload::create([
                'year' => $year,
                'type' => $type,
                'data' => $dataToSave,
                'uploaded_by' => $user->id,
                'file_path' => $file->store('budget_uploads')
            ]);

            BudgetFyLo::where('tipe', $type)
                ->where('periode', $year)
                ->where('dept', $user->dept)
                ->delete();

            if (!empty($dataForBudgetFyLos)) {
                foreach (array_chunk($dataForBudgetFyLos, 1000) as $chunk) {
                    BudgetFyLo::insert($chunk);
                }
                Log::info("Data berhasil disimpan ke budget_fy_los untuk tipe: $type, periode: $year, dept: {$user->dept}");
            } else {
                Log::warning("Tidak ada data valid untuk disimpan ke budget_fy_los");
                return redirect()->back()->with('error', 'Tidak ada data valid untuk disimpan. Pastikan periode di file Excel sesuai dengan tipe yang dipilih.');
            }

            return redirect()->back()->with('success', 'Data berhasil diupload dan disimpan ke budget_fy_los!');
        } catch (\Exception $e) {
            Log::error('Kesalahan saat mengupload file: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengupload: ' . $e->getMessage());
        }
    }
}
