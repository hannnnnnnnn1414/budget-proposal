<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OtpVerification extends Model
{
    protected $table = 'otp_verifications';
    protected $fillable = [
        'id_user',
        'otp',
        'expiry_date',
        'send',
        'send_date',
        'use',
        'use_date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }
}
