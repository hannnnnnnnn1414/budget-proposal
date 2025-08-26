<?php

// app/Models/BudgetUpload.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BudgetUpload extends Model
{
    protected $fillable = [
        'year', 
        'type', 
        'file_path', 
        'data',
        'uploaded_by'
    ];
    
    protected $casts = [
        'data' => 'array'
    ];
    
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
