<?php

namespace App\Imports;

use App\Models\BudgetRevision;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BudgetRevisionImport
{
    private $year;
    private $uploadedBy;
    private $rowCount = 0;
    private $importedData = [];
    private $revisionCode;

    public function __construct($year, $uploadedBy)
    {
        $this->year = $year;
        $this->uploadedBy = $uploadedBy;
        $this->revisionCode = 'REV-' . date('Ymd') . '-' . strtoupper(Str::random(6));

        Log::info('BudgetRevisionImport initialized', [
            'year' => $year,
            'revision_code' => $this->revisionCode
        ]);
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
            $headerRow = 1;

            foreach (range('A', $highestColumn) as $col) {
                $value = $worksheet->getCell($col . $headerRow)->getValue();
                if ($value) {
                    $headers[$col] = $this->normalizeHeader($value);
                }
            }

            Log::info('Headers detected', ['headers' => $headers]);

            $columnMap = $this->mapColumns($headers);

            if (empty($columnMap['account']) && empty($columnMap['acc_id'])) {
                throw new \Exception('Kolom Account tidak ditemukan di file Excel');
            }

            Log::info('Column mapping', ['map' => $columnMap]);

            for ($row = 2; $row <= $highestRow; $row++) {
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
            'sub_id' => ['subid', 'submissionid', 'sid'],
            'purpose' => ['purpose', 'tujuan', 'keperluan'],
            'acc_id' => ['accid', 'accountid', 'account', 'acc'],
            'itm_id' => ['itmid', 'itemid', 'item'],
            'ins_id' => ['insid', 'insuranceid', 'insurance'],
            'description' => ['description', 'deskripsi', 'desc'],
            'asset_class' => ['assetclass', 'asset'],
            'prioritas' => ['prioritas', 'priority', 'prior'],
            'alasan' => ['alasan', 'reason'],
            'keterangan' => ['keterangan', 'note', 'notes', 'remark'],
            'customer' => ['customer', 'cust'],
            'position' => ['position', 'pos', 'jabatan'],
            'beneficiary' => ['beneficiary', 'penerima'],
            'trip_propose' => ['trippropose', 'trip'],
            'destination' => ['destination', 'tujuan', 'dest'],
            'days' => ['days', 'hari', 'day'],
            'kwh' => ['kwh', 'kilowatt'],
            'participant' => ['participant', 'peserta', 'partisipan'],
            'jenis_training' => ['jenistraining', 'training', 'trainingtype'],
            'unit' => ['unit', 'satuan'],
            'quantity' => ['quantity', 'qty', 'jumlah'],
            'price' => ['price', 'harga', 'unitprice'],
            'amount' => ['amount', 'total', 'totalamount'],
            'wct_id' => ['wctid', 'workcenterid', 'workcenter', 'wc'],
            'dpt_id' => ['dptid', 'departmentid', 'department', 'dept'],
            'bdc_id' => ['bdcid', 'budgetcodeid', 'budgetcode'],
            'lob_id' => ['lobid', 'lineofbusinessid', 'lob', 'lineofbusiness'],
            'month' => ['month', 'bulan', 'mon'],
            'month_value' => ['monthvalue', 'value', 'nilai'],
            'business_partner' => ['businesspartner', 'bp', 'partner'],
            'ledger_account' => ['ledgeraccount', 'ledger'],
            'ledger_account_description' => ['ledgeraccountdescription', 'ledgerdesc']
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
            $accountCol = $columnMap['acc_id'] ?? $columnMap['account'] ?? null;
            if (!$accountCol) {
                return null;
            }

            $account = $worksheet->getCell($accountCol . $rowNumber)->getValue();

            if (empty($account) || trim($account) === '') {
                return null;
            }

            if (strtoupper($account) === 'ACCOUNT' || strtoupper($account) === 'ACC_ID') {
                return null;
            }

            $rowData = [];

            $fields = [
                'sub_id',
                'purpose',
                'acc_id',
                'itm_id',
                'ins_id',
                'description',
                'asset_class',
                'prioritas',
                'alasan',
                'keterangan',
                'customer',
                'position',
                'beneficiary',
                'trip_propose',
                'destination',
                'days',
                'kwh',
                'participant',
                'jenis_training',
                'unit',
                'wct_id',
                'dpt_id',
                'bdc_id',
                'lob_id',
                'month',
                'business_partner',
                'ledger_account',
                'ledger_account_description'
            ];

            foreach ($fields as $field) {
                $col = $columnMap[$field] ?? null;
                $value = $col ? $worksheet->getCell($col . $rowNumber)->getValue() : '';
                $rowData[$field] = $value ? trim($value) : '';
            }

            if (empty($rowData['acc_id'])) {
                $rowData['acc_id'] = trim($account);
            }

            $numericFields = ['quantity', 'price', 'amount', 'month_value'];
            foreach ($numericFields as $field) {
                $col = $columnMap[$field] ?? null;
                if ($col) {
                    $value = $worksheet->getCell($col . $rowNumber)->getValue();
                    $rowData[$field] = $this->parseNumber($value);
                } else {
                    $rowData[$field] = 0;
                }
            }

            if ($rowData['amount'] == 0 && $rowData['quantity'] > 0 && $rowData['price'] > 0) {
                $rowData['amount'] = $rowData['quantity'] * $rowData['price'];
            }

            if (empty($rowData['sub_id'])) {
                $rowData['sub_id'] = $this->revisionCode . '-' . str_pad($this->rowCount + 1, 4, '0', STR_PAD_LEFT);
            }

            Log::debug('Row data parsed', [
                'row' => $rowNumber,
                'sub_id' => $rowData['sub_id'],
                'account' => $rowData['acc_id'],
                'amount' => $rowData['amount']
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

            BudgetRevision::create([
                'sub_id' => $rowData['sub_id'],
                'purpose' => $rowData['purpose'],
                'acc_id' => $rowData['acc_id'],
                'itm_id' => $rowData['itm_id'],
                'ins_id' => $rowData['ins_id'],
                'description' => $rowData['description'],
                'asset_class' => $rowData['asset_class'] ?? null,
                'prioritas' => $rowData['prioritas'] ?? null,
                'alasan' => $rowData['alasan'] ?? null,
                'keterangan' => $rowData['keterangan'] ?? null,
                'customer' => $rowData['customer'] ?? null,
                'position' => $rowData['position'] ?? null,
                'beneficiary' => $rowData['beneficiary'],
                'trip_propose' => $rowData['trip_propose'] ?? null,
                'destination' => $rowData['destination'] ?? null,
                'days' => $rowData['days'],
                'kwh' => $rowData['kwh'],
                'participant' => $rowData['participant'],
                'jenis_training' => $rowData['jenis_training'],
                'unit' => $rowData['unit'],
                'quantity' => $rowData['quantity'],
                'price' => $rowData['price'],
                'amount' => $rowData['amount'],
                'wct_id' => $rowData['wct_id'],
                'dpt_id' => $rowData['dpt_id'],
                'bdc_id' => $rowData['bdc_id'],
                'lob_id' => $rowData['lob_id'],
                'month' => $rowData['month'],
                'month_value' => $rowData['month_value'],
                'status' => 0,
                'business_partner' => $rowData['business_partner'] ?? null,
                'ledger_account' => $rowData['ledger_account'] ?? null,
                'ledger_account_description' => $rowData['ledger_account_description'] ?? null,
            ]);

            $this->importedData[] = [
                'sub_id' => $rowData['sub_id'],
                'account' => $rowData['acc_id'],
                'amount' => $rowData['amount']
            ];
        } catch (\Exception $e) {
            Log::error('Error saving row', [
                'sub_id' => $rowData['sub_id'],
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

    public function getRevisionCode()
    {
        return $this->revisionCode;
    }
}
