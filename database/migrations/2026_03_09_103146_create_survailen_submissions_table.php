<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('survailen_submissions', function (Blueprint $table) {
            // 1. DATA DASAR & IDENTITAS
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('category'); // pelatihan / uji
            $table->string('scope')->nullable(); // Lingkup (opsional)
            $table->string('title')->nullable(); // Judul Pengajuan
            $table->string('status')->default('new'); // new, uploading, verification, completed

            // 2. BERKAS DOKUMEN (BAB III - 7 KATEGORI)
            // Semua dibuat nullable agar bisa disimpan bertahap (Self Assessment dulu baru Upload)
            $table->string('file_legalitas')->nullable();   // Kategori 1
            $table->string('file_mutu')->nullable();        // Kategori 2
            $table->string('file_rekaman')->nullable();     // Kategori 3
            $table->string('file_kinerja')->nullable();     // Kategori 4
            $table->string('file_sdm')->nullable();         // Kategori 5
            $table->string('file_sarpras')->nullable();     // Kategori 6
            $table->string('file_kurikulum')->nullable();   // Kategori 7
            
            // 3. DATA PENILAIAN (SKOR & CATATAN)
            $table->longText('self_assessment_scores')->nullable(); // Skor dari User (JSON)
            $table->longText('evaluator_scores')->nullable();      // Skor dari Admin/Asesor (JSON)
            $table->longText('evaluator_comments')->nullable();    // Komentar per item (JSON)
            
            // 4. HASIL AKHIR EVALUASI & SERTIFIKASI
            $table->decimal('final_score', 5, 2)->nullable(); // Persentase Akhir (misal: 87.50)
            $table->string('predikat', 2)->nullable();       // A, B, C, D
            $table->text('admin_note')->nullable();          // Kesimpulan / Rekomendasi
            $table->string('admin_file')->nullable();        // Laporan Hasil Surveilan (LHS) PDF
            $table->string('certificate_file')->nullable();  // File Sertifikat Akreditasi Baru

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survailen_submissions');
    }
};