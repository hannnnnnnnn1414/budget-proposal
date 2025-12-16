<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BudgetRevision extends Model
{
    use HasFactory;

    protected $table = 'budget_revisions';
    protected $primaryKey = 'id';

    protected $fillable = [
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
        'quantity',
        'price',
        'amount',
        'wct_id',
        'dpt_id',
        'bdc_id',
        'lob_id',
        'month',
        'month_value',
        'status',
        'pdf_attachment',
        'business_partner',
        'ledger_account',
        'ledger_account_description',
        'created_at'
    ];

    public function dept(): BelongsTo
    {
        return $this->belongsTo(Departments::class, 'dpt_id', 'dpt_id');
    }
    public function acc(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'acc_id', 'acc_id');
    }
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'itm_id', 'itm_id');
    }
    public function workcenter(): BelongsTo
    {
        return $this->belongsTo(Workcenter::class, 'wct_id', 'wct_id');
    }
    public function budget(): BelongsTo
    {
        return $this->belongsTo(BudgetCode::class, 'bdc_id', 'bdc_id');
    }

    public function approvals()
    {
        return $this->hasMany(Approval::class, 'sub_id', 'sub_id');
    }

    public function line_business(): BelongsTo
    {
        return $this->belongsTo(LineOfBusiness::class, 'lob_id', 'lob_id');
    }
    public function insurance(): BelongsTo
    {
        return $this->belongsTo(InsuranceCompany::class, 'ins_id', 'ins_id');
    }
    public function account()
    {
        return $this->belongsTo(Account::class, 'acc_id', 'acc_id');
    }
}
