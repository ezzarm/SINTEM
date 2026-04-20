<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // report_categories (FK ke roles)
        Schema::create('report_categories', function (Blueprint $table) {
            $table->unsignedInteger('id')->autoIncrement();
            $table->string('category_name', 100)->comment('Display name of the report category');
            $table->string('description', 255)->nullable()->comment('What kind of issues belong in this category');
            $table->unsignedInteger('responsible_role_id')->comment('Role ID of the staff responsible for reviewing these reports');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();

            $table->index('responsible_role_id', 'idx_report_categories_role');
            $table->foreign('responsible_role_id', 'fk_report_categories_role')
                ->references('id')->on('roles')->onUpdate('cascade');
        });

        // event_categories (tidak ada FK)
        Schema::create('event_categories', function (Blueprint $table) {
            $table->unsignedInteger('id')->autoIncrement();
            $table->string('name', 100)->unique('uq_event_categories_name')->comment('Category label (e.g. Upacara, Workshop, Olahraga)');
            $table->string('color', 7)->nullable()->comment('Hex color code for calendar UI display (e.g. #FF5733)');
            $table->string('description', 255)->nullable()->comment('Brief explanation of what events fall under this category');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();
        });

        // event_locations (tidak ada FK)
        Schema::create('event_locations', function (Blueprint $table) {
            $table->unsignedInteger('id')->autoIncrement();
            $table->string('name', 150)->unique('uq_event_locations_name')->comment('Display name of the venue (e.g. Aula Utama, Lapangan Sepak Bola)');
            $table->string('description', 255)->nullable()->comment('Optional detail about the location (capacity, floor, building)');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_locations');
        Schema::dropIfExists('event_categories');
        Schema::dropIfExists('report_categories');
    }
};