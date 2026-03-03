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
        Schema::table('sinarx_submissions', function (Blueprint $table) {
            $table->string('nomor_surat'); // Nomor surat resmi dari Unit
            
            // Kolom Isian untuk Tabel Detail di Nota Dinas (Nullable jika tidak semua diisi)
            $table->string('bagian_diperbaiki')->nullable(); // E.g. Nama Instansi
            $table->text('ketidaksesuaian')->nullable();    // Data yang salah saat ini
            $table->text('data_sesuai')->nullable();        // Data perbaikan yang benar
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sinarx_submissions', function (Blueprint $table) {
            $table->dropColumn('nomor_surat');
            $table->dropColumn('bagian_diperbaiki');
            $table->dropColumn('ketidaksesuaian');
            $table->dropColumn('data_sesuai');
        });
    }
};
