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

            // Skip empty rows
            if (count(array_filter($rowData)) === 0) {
                continue;
            }

            // Check if this is the header row (contains "ACCOUNT")
            if (in_array('ACCOUNT', $rowData)) {
                $headers = $rowData;
                $startCollecting = true;
                continue;
            }

            // Only collect data after headers are found
            if ($startCollecting) {
                $account = $rowData[1] ?? null; // Column B is the account
                $total = $rowData[20] ?? 0;     // Column U is the total

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

    // C:\laragon\www\budget-proposal\app\Http\Controllers\BudgetUploadController.php

    public function uploadFyLo(Request $request)
    {
        $user = Auth::user();
        if ($user->dept !== '6121') {
            return redirect()->back()->with('error', 'Hanya departemen 6121 yang diizinkan untuk mengupload data.');
        }

        // [MODIFIKASI] Validasi input, hapus validasi untuk year
        $request->validate([
            'type' => 'required|in:last_year,outlook,proposal', // Validasi tipe data (last_year, outlook, atau proposal)
            'file' => 'required|file|mimes:xlsx,xls|max:10240', // Validasi file Excel, maksimum 10 MB
        ]);

        $file = $request->file('file');
        $type = $request->type;
        // [MODIFIKASI] Tentukan tahun secara otomatis berdasarkan tipe
        $currentYear = date('Y'); // Ambil tahun saat ini (2025)
        $year = ($type === 'last_year') ? $currentYear - 1 : $currentYear; // Last Year: 2024, Outlook/Proposal: 2025
        $user = Auth::user();

        try {
            $spreadsheet = IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // [MODIFIKASI] Validasi header
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

            // Hapus header jika ada
            array_shift($rows); // Hapus baris pertama (judul)
            array_shift($rows); // Hapus baris kedua (header kolom)

            $dataToSave = [];
            $dataForBudgetFyLos = [];

            foreach ($rows as $index => $row) {
                // Lewati baris kosong
                if (empty(array_filter($row))) {
                    Log::info("Baris $index dilewati karena kosong");
                    continue;
                }

                // [MODIFIKASI] Validasi kolom ACCOUNT dan Periode
                if (!isset($row[1]) || empty($row[1]) || !isset($row[21]) || !is_numeric($row[21])) {
                    Log::warning("Baris $index dilewati karena ACCOUNT atau Periode tidak valid: " . json_encode($row));
                    continue; // Lewati jika kolom ACCOUNT atau Periode kosong/invalid
                }

                // [MODIFIKASI] Gunakan periode dari Excel, tapi validasi sesuai type
                $excelPeriode = (int) $row[21];
                if (($type === 'last_year' && $excelPeriode != $year) || ($type === 'outlook' && $excelPeriode != $year)) {
                    Log::warning("Baris $index dilewati karena periode ($excelPeriode) tidak sesuai dengan type ($type) dan year ($year)");
                    continue; // Lewati jika periode tidak sesuai dengan type
                }

                // [MODIFIKASI] Konversi nilai bulanan ke numerik
                $monthlyValues = array_slice($row, 8, 12); // Kolom Jan–Dec (indeks 8–19)
                $monthlyValues = array_map(function ($value) {
                    // Hapus spasi, koma, atau karakter non-numerik lainnya
                    $value = str_replace(',', '', $value); // Menghapus koma jika ada
                    return is_numeric($value) ? (float) $value : 0; // Konversi ke float, default 0 jika tidak valid
                }, $monthlyValues);

                // [MODIFIKASI] Hitung total dari bulanan jika kolom Total tidak valid
                $total = is_numeric($row[20]) ? (float) str_replace(',', '', $row[20]) : array_sum($monthlyValues);
                if ($total === 0 && array_sum($monthlyValues) === 0) {
                    Log::warning("Baris $index memiliki total 0 dan semua nilai bulanan tidak valid: " . json_encode($row));
                }

                // [MODIFIKASI] Mapping data Excel ke struktur database
                $dataForBudgetFyLos[] = [
                    'tipe' => $type,
                    'periode' => $excelPeriode, // Gunakan periode dari Excel
                    'r_nr' => $row[0] ?? null, // Kolom A (NR/R)
                    'account' => $row[1] ?? null, // Kolom B (ACCOUNT)
                    'budget_code' => $row[2] ?? null, // Kolom C (BUDG)
                    'line_of_business' => $row[3] ?? null, // Kolom D (BUSS)
                    'wc' => $row[4] ?? null, // Kolom E (WC)
                    'dept' => $row[6] ?? $user->dept, // Gunakan dept dari user
                    'dept_code' => $row[6] ?? null, // Kolom G (DEPT CODE)
                    'criteria_to_master' => $row[7] ?? null, // Kolom H (CRITERIA TO MASTER)
                    'jan' => $monthlyValues[0], // Kolom I (Jan)
                    'feb' => $monthlyValues[1], // Kolom J (Feb)
                    'mar' => $monthlyValues[2], // Kolom K (Mar)
                    'apr' => $monthlyValues[3], // Kolom L (Apr)
                    'may' => $monthlyValues[4], // Kolom M (May)
                    'jun' => $monthlyValues[5], // Kolom N (Jun)
                    'jul' => $monthlyValues[6], // Kolom O (Jul)
                    'aug' => $monthlyValues[7], // Kolom P (Aug)
                    'sep' => $monthlyValues[8], // Kolom Q (Sep)
                    'oct' => $monthlyValues[9], // Kolom R (Oct)
                    'nov' => $monthlyValues[10], // Kolom S (Nov)
                    'dec' => $monthlyValues[11], // Kolom T (Dec)
                    'total' => $total, // Kolom U (Total) atau hasil jumlah bulanan
                    'created_at' => now(),
                    'updated_at' => now()
                ];

                // Format data untuk BudgetUpload (dipertahankan untuk kompatibilitas)
                $dataToSave[] = [
                    'account' => $row[1] ?? null,
                    'amount' => $total
                ];
            }

            // [MODIFIKASI] Log jumlah data yang akan disimpan
            Log::info("Jumlah baris yang akan disimpan ke budget_fy_los: " . count($dataForBudgetFyLos));

            // [MODIFIKASI] Simpan ke BudgetUpload (dipertahankan untuk kompatibilitas, opsional)
            BudgetUpload::create([
                'year' => $year,
                'type' => $type,
                'data' => $dataToSave,
                'uploaded_by' => $user->id,
                'file_path' => $file->store('budget_uploads')
            ]);

            // [MODIFIKASI] Hapus data lama dan simpan yang baru ke budget_fy_los
            BudgetFyLo::where('tipe', $type)
                ->where('periode', $year)
                ->where('dept', $user->dept)
                ->delete();

            // Insert data dalam batch untuk efisiensi
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