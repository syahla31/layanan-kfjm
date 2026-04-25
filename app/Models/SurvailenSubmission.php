<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurvailenSubmission extends Model
{
    use HasFactory;

    protected $table = 'survailen_submissions';

    protected $fillable = [
        'user_id', 'category', 'scope', 'title', 'status',
        'self_assessment_scores', 'evaluator_scores', 'evaluator_comments',
        'final_score', 'predikat', 'admin_note', 'admin_file', 'certificate_file'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke tabel survailen_files
    public function files()
    {
        return $this->hasMany(SurvailenFile::class, 'survailen_submission_id');
    }
}