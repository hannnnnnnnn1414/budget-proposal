<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workcenter extends Model
{
    use HasFactory;

    protected $table = 'workcenters';
    protected $primaryKey = 'id';

    protected $fillable = [
        'wct_id',
        'workcenter',
        'status'
    ];

    public function budget_plans()
    {
        return $this->hasMany(BudgetPlan::class, 'wct_id', 'wct_id');
    }
}
