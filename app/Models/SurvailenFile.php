<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurvailenFile extends Model
{
    use HasFactory;

    // Sesuai gambar 1 database kamu
    protected $table = 'survailen_files';

    // Kolom yang boleh diisi
    protected $fillable = [
        'survailen_submission_id', // Foreign key ke survailen_submissions
        'category_key', 
        'file_path', 
        'file_name'
    ];

    public function submission()
    {
        return $this->belongsTo(SurvailenSubmission::class, 'survailen_submission_id');
    }
}