<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetFinal extends Model
{
    use HasFactory;

    protected $table = 'budget_finals';

    protected $fillable = [
        'periode',
        'tipe',
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
        'total',
        'uploaded_by'
    ];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
