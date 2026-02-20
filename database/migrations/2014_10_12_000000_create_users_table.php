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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            
            // --- KOLOM PENTING ---
            $table->string('category'); // pelatihan, uji, sinarx
            $table->string('role');     // user, admin
            $table->string('kode_instansi')->nullable();
            
            // TAMBAHAN BARU: Status Akun
            // 'pending' = Baru daftar (Gak bisa login)
            // 'active'  = Sudah disetujui Admin (Bisa login)
            $table->string('status')->default('pending'); 
            
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
