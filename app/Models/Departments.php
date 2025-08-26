<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Departments extends Model
{
    use HasFactory;

    protected $table = 'departments';
    protected $primaryKey = 'id';

    protected $fillable = [
        'dpt_id',
        'department',
        'level',
        'parent',
        'alloc',
        'status'
    ];
}
