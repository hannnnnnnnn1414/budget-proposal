<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Divisions extends Model
{
    protected $table = 'divisions';

    protected $fillable = [
        'division_name',
        'created_at',
        'updated_at',
    ];

    /**
     * Relasi many-to-many dengan Departments melalui pivot table department_division
     */
    public function departments()
    {
        return $this->belongsToMany(Departments::class, 'department_division', 'division_id', 'department_id');
    }
}