<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Remarks extends Model
{
    use HasFactory;

    protected $table = 'remarks'; // Specify the table name
    // protected $primaryKey = 'tmp_id';

    protected $fillable = [
        'remark_by',
        'sub_id',
        'remark',
        'remark_type',
        'status'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'remark_by', 'npk');
    }
}
