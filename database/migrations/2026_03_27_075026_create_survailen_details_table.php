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
        Schema::create('survailen_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survailen_submission_id')->constrained()->onDelete('cascade');
            
            // Simpan semua path file di sini
            $table->string('file_oss')->nullable();
            $table->string('file_mou')->nullable();
            $table->string('file_izin_lainnya')->nullable();
            $table->string('file_manual_mutu')->nullable();
            $table->string('file_prosedur_pelatihan')->nullable();
            $table->string('file_pantau_mutu')->nullable();
            $table->string('file_rekaman_lainnya')->nullable();
            $table->string('file_lapkin')->nullable();
            $table->string('file_kak')->nullable();
            $table->string('file_daftar_manajemen')->nullable();
            $table->string('file_daftar_pengajar')->nullable();
            $table->string('file_daftar_sarana')->nullable();
            $table->string('file_daftar_prasarana')->nullable();
            $table->string('file_kurikulum')->nullable();
            $table->string('file_modul')->nullable();
            $table->string('file_bahan_ajar')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survailen_details');
    }
};
