<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetFyLo extends Model
{
    use HasFactory;

    protected $table = 'budget_fy_los';
    protected $primaryKey = 'id';

    protected $fillable = [
        'tipe',
        'periode',
        'r_nr',
        'account',
        'budget_code',
        'line_of_business',
        'wc',
        'dept',
        'dept_code',
        'criteria_to_master',
        'jan',
        'feb',
        'mar',
        'apr',
        'may',
        'jun',
        'jul',
        'aug',
        'sep',
        'oct',
        'nov',
        'dec',
        'total'
    ];

    protected $casts = [
        'jan' => 'decimal:2',
        'feb' => 'decimal:2',
        'mar' => 'decimal:2',
        'apr' => 'decimal:2',
        'may' => 'decimal:2',
        'jun' => 'decimal:2',
        'jul' => 'decimal:2',
        'aug' => 'decimal:2',
        'sep' => 'decimal:2',
        'oct' => 'decimal:2',
        'nov' => 'decimal:2',
        'dec' => 'decimal:2',
        'total' => 'decimal:2'
    ];
}