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
        Schema::create('ktun_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('category'); // 'uji' atau 'pelatihan'
            
            // 3 File Utama dari Admin
            $table->string('file_surat_pengantar');
            $table->string('file_ktun');
            $table->string('file_kwintansi');
            
            // Status Konfirmasi Survey (Google Form)
            // Cukup gunakan boolean dan timestamp konfirmasi
            $table->boolean('is_survey_filled')->default(false);
            $table->timestamp('survey_confirmed_at')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ktun_deliveries');
    }
};
