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
        Schema::create('submission_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained('submissions')->onDelete('cascade');
            
            $table->string('file_path');    // Lokasi file user
            $table->string('file_name');    // Nama asli file
            $table->integer('version');     // Versi ke-1, 2, 3...
            
            // Catatan revisi dari admin untuk versi ini (opsional, jika spesifik per file)
            $table->text('admin_note')->nullable(); 
            $table->string('admin_file')->nullable(); 

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submission_files');
    }
};
