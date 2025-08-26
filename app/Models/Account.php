<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    use HasFactory;

    protected $table = 'accounts'; // Specify the table name
    protected $primaryKey = 'acc_id';
    protected $keyType = 'string'; // If acc_id is a string

    protected $fillable = [
        'acc_id',
        'account',
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
    public function afterSalesServices(): HasMany
    {
        return $this->hasMany(AfterSalesService::class, 'acc_id', 'acc_id');
    }
}
