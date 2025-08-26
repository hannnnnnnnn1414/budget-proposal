<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Approval extends Model
{
    use HasFactory;

    protected $table = 'approvals'; // Specify the table name
    // protected $primaryKey = 'tmp_id';

    protected $fillable = [
        'approve_by',
        'sub_id',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approve_by', 'npk');
    }
}
