<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lost_founds', function (Blueprint $table) {
            $table->id();
            
            // Kita pake unsignedBigInteger tapi tanpa ->constrained() dulu
            $table->unsignedBigInteger('user_id'); 
            
            $table->enum('type', ['found', 'lost']);
            $table->string('item_name', 100);
            $table->string('found_at', 150)->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
            
            // HAPUS ATAU KOMENTARI BAGIAN FOREIGN KEY DI BAWAH INI
            // $table->foreign('user_id')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lost_founds');
    }
};