<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KtunDelivery extends Model
{
    // Tentukan tabel jika nama class tidak otomatis (opsional)
    protected $table = 'ktun_deliveries';

    protected $fillable = [
        'user_id', 
        'category', 
        'file_surat_pengantar', 
        'file_ktun', 
        'file_kwintansi', 
        'is_survey_filled', 
        'survey_confirmed_at'
    ];

    protected $casts = [
        'is_survey_filled' => 'boolean',
        'survey_confirmed_at' => 'datetime'
    ];

    /**
     * Relasi ke User (Instansi Penerima)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}