<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurvailenSubmission extends Model
{
    use HasFactory;

    /**
     * Atribut yang dapat diisi secara massal.
     * Disesuaikan dengan struktur migrasi BAB III.
     */
    protected $fillable = [
        'user_id',
        'category',
        'scope',
        'title',
        'status',
        
        // Berkas Dokumen (7 Kategori BAB III)
        'file_legalitas',
        'file_mutu',
        'file_rekaman',
        'file_kinerja',
        'file_sdm',
        'file_sarpras',
        'file_kurikulum',
        
        // Data Penilaian
        'self_assessment_scores',
        'evaluator_scores',
        'evaluator_comments',
        
        // Hasil Akhir & Sertifikasi
        'final_score',
        'predikat',
        'admin_note',
        'admin_file',
        'certificate_file'
    ];

    /**
     * Relasi ke model User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasOne(SurvailenDetail::class);
    }
}