<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

     protected $table = 'items'; // Specify the table name
protected $primaryKey = 'itm_id';
    protected $keyType = 'string'; // If itm_id is a string
    public $incrementing = false; // If itm_id is not auto-incrementing
    
    protected $fillable = [
        'itm_id',
        'item',
    ];
}
