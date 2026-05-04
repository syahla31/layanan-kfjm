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
        Schema::table('survailen_submissions', function (Blueprint $table) {
            $table->string('chairman_name')->nullable()->after('admin_note');
            $table->string('chairman_nip')->nullable()->after('chairman_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('survailen_submissions', function (Blueprint $table) {
            $table->dropColumn('chairman_name');
            $table->dropColumn('chairman_nip');
        });
    }
};
