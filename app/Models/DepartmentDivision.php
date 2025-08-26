<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepartmentDivision extends Model
{
    protected $table = 'department_division';

    protected $fillable = [
        'department_id',
        'division_id',
        'created_at',
        'updated_at',
    ];

    /**
     * Relasi ke Departments
     */
    public function department()
    {
        return $this->belongsTo(Departments::class, 'department_id');
    }

    /**
     * Relasi ke Divisions
     */
    public function division()
    {
        return $this->belongsTo(Divisions::class, 'division_id');
    }
}