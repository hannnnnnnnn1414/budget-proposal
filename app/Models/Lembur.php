<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $connection = 'mysql_lembur'; 
    protected $table = 'departments'; 
    protected $primaryKey = 'id'; 
    public $incrementing = true; 
    protected $fillable = ['dpt_id', 'department', 'level', 'parent', 'alloc', 'status', 'created_at', 'updated_at'];

}