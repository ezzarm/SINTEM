<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah file_data ke photos (untuk menyimpan base64 encoded image)
        Schema::table('photos', function (Blueprint $table) {
            $table->longText('file_data')->nullable()->after('file_path')
                ->comment('Base64 encoded image data (data:mime;base64,...)');
        });

        // Tambah reject_reason ke lost_founds
        Schema::table('lost_founds', function (Blueprint $table) {
            $table->text('reject_reason')->nullable()->after('status')
                ->comment('Reason provided when admin rejects a lost/found report');
        });
    }

    public function down(): void
    {
        Schema::table('photos', function (Blueprint $table) {
            $table->dropColumn('file_data');
        });

        Schema::table('lost_founds', function (Blueprint $table) {
            $table->dropColumn('reject_reason');
        });
    }
};