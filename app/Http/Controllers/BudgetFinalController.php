<?php

namespace App\Http\Controllers;

use App\Models\BudgetFinal;
use App\Models\BudgetUpload;
use Illuminate\Http\Request;
use App\Imports\BudgetFinalImport;
use App\Models\Departments;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BudgetFinalController extends Controller
{
    public function index()
    {
        $uploads = BudgetUpload::where('type', 'final')
            ->with('uploader')
            ->latest()
            ->paginate(10);

        $departments = Departments::all();
        $availableYears = BudgetFinal::select('periode')
            ->distinct()
            ->pluck('periode');

        return view('budget-final.index', compact('uploads', 'departments', 'availableYears'));
    }

    public function upload(Request $request)
    {
        try {
            $request->validate([
                'periode' => 'required|digits:4',
                'tipe' => 'required|in:final',
                'file' => 'required|file|mimes:xlsx,xls|max:10240', // max 10MB
            ]);

            Log::info('Upload started', [
                'periode' => $request->periode,
                'tipe' => $request->tipe,
                'filename' => $request->file('file')->getClientOriginalName()
            ]);

            if (!$request->hasFile('file')) {
                throw new \Exception('File tidak ditemukan');
            }

            $file = $request->file('file');

            if (!$file->isValid()) {
                throw new \Exception('File tidak valid atau corrupt');
            }

            $filePath = $file->getRealPath();

            DB::beginTransaction();

            try {
                $deleted = BudgetFinal::where('periode', $request->periode)
                    ->where('tipe', $request->tipe)
                    ->delete();

                Log::info('Old data deleted', ['count' => $deleted]);

                $import = new BudgetFinalImport(
                    $request->periode,
                    $request->tipe,
                    auth()->id()
                );

                $import->import($filePath);

                $rowCount = $import->getRowCount();
                $importedData = $import->getImportedData();

                Log::info('Import completed', [
                    'rows' => $rowCount,
                    'sample_data' => array_slice($importedData, 0, 3)
                ]);

                if ($rowCount === 0) {
                    throw new \Exception('Tidak ada data yang berhasil diimport. Periksa format file Excel Anda.');
                }

                $storedPath = $file->store('budget_final_uploads');

                $historyData = [];
                foreach ($importedData as $row) {
                    $historyData[] = [
                        'account' => $row['account'],
                        'amount' => $row['total']
                    ];
                }

                BudgetUpload::create([
                    'year' => $request->periode,
                    'type' => 'final',
                    'file_path' => $storedPath,
                    'uploaded_by' => auth()->id(),
                    'data' => $historyData
                ]);

                DB::commit();

                Log::info('Upload successful', ['count' => $rowCount]);

                return response()->json([
                    'success' => true,
                    'message' => "Data final budget berhasil diupload! ($rowCount baris data)",
                    'count' => $rowCount
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', ['errors' => $e->errors()]);
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Budget Final Upload Error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'debug' => config('app.debug') ? [
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ] : null
            ], 500);
        }
    }

    public function getDepartmentSummary(Request $request)
    {
        try {
            $query = BudgetFinal::query();

            if ($request->filled('periode')) {
                $query->where('periode', $request->periode);
            }

            if ($request->filled('tipe')) {
                $query->where('tipe', $request->tipe);
            }

            if ($request->filled('dept')) {
                $query->where('dept_code', $request->dept);
            }

            $summary = $query->selectRaw('
                    dept_code,
                    dept as department,
                    COUNT(*) as account_count,
                    SUM(total) as total_amount,
                    MAX(created_at) as last_upload
                ')
                ->groupBy('dept_code', 'dept')
                ->get()
                ->map(function ($item) {
                    $item->total_amount = number_format($item->total_amount, 0, ',', '.');
                    $item->last_upload = $item->last_upload
                        ? date('d/m/Y H:i', strtotime($item->last_upload))
                        : '-';
                    return $item;
                });

            return response()->json([
                'success' => true,
                'data' => $summary
            ]);
        } catch (\Exception $e) {
            Log::error('Get summary error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function delete(Request $request)
    {
        try {
            $deleted = BudgetFinal::where('dept_code', $request->dept)
                ->where('periode', $request->periode)
                ->where('tipe', $request->tipe)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => "Berhasil menghapus $deleted data final budget"
            ]);
        } catch (\Exception $e) {
            Log::error('Delete error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function downloadTemplate()
    {
        $path = public_path('templates/template_final_budget.xlsx');

        if (!file_exists($path)) {
            return response()->json([
                'success' => false,
                'message' => 'Template tidak ditemukan'
            ], 404);
        }

        return response()->download($path, 'Template_Final_Budget.xlsx');
    }
}
