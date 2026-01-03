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
        Schema::table('teachers', function (Blueprint $table) {
            // Add expiry date columns for each certificate type
            $table->date('malaka_toifa_expdate')->nullable()->after('malaka_toifa_path');
            $table->date('milliy_sertifikat1_expdate')->nullable()->after('milliy_sertifikat1_path');
            $table->date('milliy_sertifikat2_expdate')->nullable()->after('milliy_sertifikat2_path');
            $table->date('xalqaro_sertifikat_expdate')->nullable()->after('xalqaro_sertifikat_path');
            $table->date('ustama_sertifikat_expdate')->nullable()->after('ustama_sertifikat_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropColumn([
                'malaka_toifa_expdate',
                'milliy_sertifikat1_expdate',
                'milliy_sertifikat2_expdate',
                'xalqaro_sertifikat_expdate',
                'ustama_sertifikat_expdate'
            ]);
        });
    }
};
