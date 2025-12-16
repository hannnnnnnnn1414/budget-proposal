<?php

namespace App\Imports;

use App\Models\BudgetFinal;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Log;

class BudgetFinalImport
{
    private $periode;
    private $tipe;
    private $uploadedBy;
    private $rowCount = 0;
    private $importedData = [];

    public function __construct($periode, $tipe, $uploadedBy)
    {
        $this->periode = $periode;
        $this->tipe = $tipe;
        $this->uploadedBy = $uploadedBy;
        Log::info('BudgetFinalImport initialized', ['periode' => $periode, 'tipe' => $tipe]);
    }

    public function import($filePath)
    {
        try {
            Log::info('Starting import from file', ['path' => $filePath]);

            $spreadsheet = IOFactory::load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();

            $highestRow = $worksheet->getHighestRow();
            $highestColumn = $worksheet->getHighestColumn();

            Log::info('Excel loaded', [
                'rows' => $highestRow,
                'columns' => $highestColumn
            ]);

            $headers = [];
            $headerRow = 2;

            foreach (range('A', $highestColumn) as $col) {
                $value = $worksheet->getCell($col . $headerRow)->getValue();
                if ($value) {
                    $headers[$col] = $this->normalizeHeader($value);
                }
            }

            Log::info('Headers detected', ['headers' => $headers]);

            $columnMap = $this->mapColumns($headers);

            if (empty($columnMap['account'])) {
                throw new \Exception('Kolom ACCOUNT tidak ditemukan di file Excel');
            }

            Log::info('Column mapping', ['map' => $columnMap]);

            for ($row = 3; $row <= $highestRow; $row++) {
                $rowData = $this->readRow($worksheet, $row, $columnMap);

                if ($rowData) {
                    $this->saveRow($rowData);
                }
            }

            Log::info('Import completed', ['total_rows' => $this->rowCount]);

            return true;
        } catch (\Exception $e) {
            Log::error('Import failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            throw $e;
        }
    }

    private function normalizeHeader($header)
    {
        return strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $header));
    }

    private function mapColumns($headers)
    {
        $map = [];

        $headerMappings = [
            'account' => ['account', 'acc'],
            'nr_r' => ['nrr', 'nr', 'nrr', 'r'],
            'budg' => ['budg', 'budget', 'budgetcode'],
            'buss' => ['buss', 'business', 'lineofbusiness', 'lob'],
            'wc' => ['wc', 'workcenter'],
            'dept' => ['dept', 'department'],
            'dept_code' => ['deptcode', 'departmentcode'],
            'criteria_to_master' => ['criteriatomaster', 'criteria'],
            'jan' => ['jan', 'january'],
            'feb' => ['feb', 'february'],
            'mar' => ['mar', 'march'],
            'apr' => ['apr', 'april'],
            'may' => ['may'],
            'jun' => ['jun', 'june'],
            'jul' => ['jul', 'july'],
            'aug' => ['aug', 'august'],
            'sep' => ['sep', 'september'],
            'oct' => ['oct', 'october'],
            'nov' => ['nov', 'november'],
            'dec' => ['dec', 'december'],
            'total' => ['total', 'sum']
        ];

        foreach ($headers as $col => $header) {
            foreach ($headerMappings as $key => $patterns) {
                foreach ($patterns as $pattern) {
                    if ($header === $pattern) {
                        $map[$key] = $col;
                        break 2;
                    }
                }
            }
        }

        return $map;
    }

    private function readRow($worksheet, $rowNumber, $columnMap)
    {
        try {
            $accountCol = $columnMap['account'] ?? null;
            if (!$accountCol) {
                return null;
            }

            $account = $worksheet->getCell($accountCol . $rowNumber)->getValue();

            if (empty($account) || trim($account) === '') {
                return null;
            }

            if (strtoupper($account) === 'ACCOUNT' || preg_match('/^Q\d+/i', trim($account))) {
                Log::debug('Skipping row', ['row' => $rowNumber, 'account' => $account]);
                return null;
            }

            $rowData = [
                'account' => trim($account)
            ];

            $fields = ['nr_r', 'budg', 'buss', 'wc', 'dept', 'dept_code', 'criteria_to_master'];
            foreach ($fields as $field) {
                $col = $columnMap[$field] ?? null;
                $rowData[$field] = $col ? trim($worksheet->getCell($col . $rowNumber)->getValue() ?? '') : '';
            }

            $months = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];
            $monthlyValues = [];

            foreach ($months as $month) {
                $col = $columnMap[$month] ?? null;
                if ($col) {
                    $value = $worksheet->getCell($col . $rowNumber)->getValue();
                    $monthlyValues[$month] = $this->parseNumber($value);
                } else {
                    $monthlyValues[$month] = 0;
                }
            }

            $totalCol = $columnMap['total'] ?? null;
            if ($totalCol) {
                $total = $this->parseNumber($worksheet->getCell($totalCol . $rowNumber)->getValue());
            } else {
                $total = 0;
            }

            if ($total == 0) {
                $total = array_sum($monthlyValues);
            }

            $rowData['monthly'] = $monthlyValues;
            $rowData['total'] = $total;

            Log::debug('Row data parsed', [
                'row' => $rowNumber,
                'account' => $rowData['account'],
                'total' => $total
            ]);

            return $rowData;
        } catch (\Exception $e) {
            Log::error('Error reading row', [
                'row' => $rowNumber,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    private function parseNumber($value)
    {
        if (empty($value) || $value === '-' || $value === ' ') {
            return 0;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        if (is_string($value)) {
            $value = trim($value);

            $commaPos = strrpos($value, ',');
            $dotPos = strrpos($value, '.');

            if ($commaPos !== false && $dotPos !== false) {
                if ($commaPos > $dotPos) {
                    $value = str_replace('.', '', $value);
                    $value = str_replace(',', '.', $value);
                } else {
                    $value = str_replace(',', '', $value);
                }
            } elseif ($commaPos !== false) {
                $afterComma = substr($value, $commaPos + 1);
                if (strlen($afterComma) <= 2) {
                    $value = str_replace('.', '', $value);
                    $value = str_replace(',', '.', $value);
                } else {
                    $value = str_replace(',', '', $value);
                }
            } elseif ($dotPos !== false) {
                $afterDot = substr($value, $dotPos + 1);
                if (strlen($afterDot) > 2) {
                    $value = str_replace('.', '', $value);
                }
            }

            return is_numeric($value) ? (float) $value : 0;
        }

        return 0;
    }

    private function saveRow($rowData)
    {
        try {
            $this->rowCount++;

            BudgetFinal::create([
                'periode' => $this->periode,
                'tipe' => $this->tipe,
                'r_nr' => $rowData['nr_r'],
                'account' => $rowData['account'],
                'budget_code' => $rowData['budg'],
                'line_of_business' => $rowData['buss'],
                'wc' => $rowData['wc'],
                'dept' => $rowData['dept'],
                'dept_code' => $rowData['dept_code'],
                'criteria_to_master' => $rowData['criteria_to_master'],
                'jan' => $rowData['monthly']['jan'],
                'feb' => $rowData['monthly']['feb'],
                'mar' => $rowData['monthly']['mar'],
                'apr' => $rowData['monthly']['apr'],
                'may' => $rowData['monthly']['may'],
                'jun' => $rowData['monthly']['jun'],
                'jul' => $rowData['monthly']['jul'],
                'aug' => $rowData['monthly']['aug'],
                'sep' => $rowData['monthly']['sep'],
                'oct' => $rowData['monthly']['oct'],
                'nov' => $rowData['monthly']['nov'],
                'dec' => $rowData['monthly']['dec'],
                'total' => $rowData['total'],
                'uploaded_by' => $this->uploadedBy
            ]);

            // Simpan untuk history
            $this->importedData[] = [
                'account' => $rowData['account'],
                'total' => $rowData['total']
            ];
        } catch (\Exception $e) {
            Log::error('Error saving row', [
                'account' => $rowData['account'],
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function getRowCount()
    {
        return $this->rowCount;
    }

    public function getImportedData()
    {
        return $this->importedData;
    }
}
