<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurvailenDetail extends Model
{
    protected $guarded = []; // Izinkan semua kolom

    public function submission()
    {
        return $this->belongsTo(SurvailenSubmission::class);
    }
}
