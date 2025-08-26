<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Isd extends Model
{
    use HasFactory;
    protected $connection = 'mysql2'; // Koneksi ke database isd
        protected $table = 'hp'; // Specify the table name
    // protected $primaryKey = 'id';

    protected $fillable = [
        'npk',
        'no_hp',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'npk', 'npk');
    }
}
