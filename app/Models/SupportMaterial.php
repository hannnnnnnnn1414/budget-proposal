<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportMaterial extends Model
{
    use HasFactory;

    protected $table = 'support_materials'; // Specify the table name
    protected $primaryKey = 'id';

    protected $fillable = [
        'sub_id',
        'purpose',
        'acc_id',
        'itm_id',
        'description',
        'unit',
        'quantity',
        'price',
        'amount',
        'wct_id',
        'dpt_id',
        'month',
        'bdc_id',
        'line_business',
        'status',
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
    public function budget_code(): BelongsTo
    {
        return $this->belongsTo(BudgetCode::class, 'bdc_id', 'bdc_id');
    }
    public function line_business(): BelongsTo
    {
        return $this->belongsTo(LineOfBusiness::class, 'lob_id', 'lob_id');
    }
}
