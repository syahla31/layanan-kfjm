<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            // Relasi ke User pengirim
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Kolom Data
            $table->string('category'); // pelatihan, uji, sinarx
            $table->string('type');     // Laporan Tahunan, Survailen, KAK, dll
            $table->string('title');    // Judul Dokumen
            $table->string('admin_file')->nullable(); // Lokasi file PDF
            
            // Status Verifikasi: pending, approved, rejected
            $table->string('status')->default('pending'); 
            $table->string('file_path')->nullable(); // Lokasi file PDF
            $table->text('admin_note')->nullable(); // Catatan revisi jika ada
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
