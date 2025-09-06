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
            $table->string('passport_serial_number')->nullable();
            $table->string('passport_jshshir')->nullable();
            $table->string('passport_photo_path')->nullable();
            $table->string('diplom_path')->nullable();
            $table->string('malaka_toifa_path')->nullable();
            $table->string('milliy_sertifikat_path')->nullable();
            $table->string('xalqaro_sertifikat_path')->nullable();
            $table->string('malumotnoma_path')->nullable();
            $table->string('signature_path')->nullable();
            $table->string('telegram_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropColumn([
                'passport_serial_number',
                'passport_jshshir',
                'passport_photo_path',
                'diplom_path',
                'malaka_toifa_path',
                'milliy_sertifikat_path',
                'xalqaro_sertifikat_path',
                'malumotnoma_path',
                'signature_path',
                'telegram_id'
            ]);
        });
    }
};
