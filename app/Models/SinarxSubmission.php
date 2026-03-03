<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SinarxSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'no_sertifikat',
        'no_registrasi',
        'alasan_amandemen',
        'file_path',
        'status',
        'admin_note',
        'nomor_surat',
        'bagian_diperbaiki',
        'ketidaksesuaian',
        'data_sesuai',
        'no_registrasi' // Kolom baru untuk nomor registrasi
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}