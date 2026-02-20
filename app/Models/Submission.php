<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'category', 
        'type', 
        'title', 
        'file_path', 
        'status', 
        'admin_note',
        'admin_file'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // --- TAMBAHKAN KODE INI ---
    
    // Relasi ke Riwayat File (One to Many)
    public function files()
    {
        // Pastikan Anda sudah membuat Model SubmissionFile juga
        return $this->hasMany(SubmissionFile::class)->orderBy('version', 'desc');
    }
    
    // Helper untuk mengambil file versi terbaru
    public function latestFile()
    {
        return $this->hasOne(SubmissionFile::class)->latestOfMany('version');
    }
    // --------------------------
}