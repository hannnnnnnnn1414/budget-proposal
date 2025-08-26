<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Dimension extends Model
{
    use HasFactory;

    protected $table = 'dimensions';
    protected $primaryKey = 'dim_id';

    protected $fillable = [
        'dim_id',
        'dimension',
        'dimension_name'
    ];
}
