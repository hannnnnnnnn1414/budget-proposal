<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LineOfBusiness extends Model
{
    use HasFactory;

    protected $table = 'line_of_businesses';
    protected $primaryKey = 'id';

    protected $fillable = [
        'lob_id',
        'line_business',
        'status'
    ];
}
