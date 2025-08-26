<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    // protected $connection = 'mysql'; // Koneksi ke database master_budget (opsional, jika default sudah mysql)
    protected $primaryKey = 'id';

    protected $fillable = [
        'npk',
        'message',
        'is_read',
        'sub_id',
        'no_hp'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'npk', 'npk');
    }
}
