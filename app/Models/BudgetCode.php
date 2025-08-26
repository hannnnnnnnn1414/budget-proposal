<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetCode extends Model
{
    use HasFactory;

    protected $table = 'budget_codes';
    protected $primaryKey = 'id';

    protected $fillable = [
        'bdc_id',
        'budget_name',
        'status'
    ];
}
