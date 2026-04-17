<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. roles
        Schema::create('roles', function (Blueprint $table) {
            $table->unsignedInteger('id')->autoIncrement();
            $table->string('role_name', 50)->unique()->comment('Unique name for the role (e.g. superadmin, BK, TU)');
            $table->string('description', 255)->nullable()->comment('Human-readable explanation of the role responsibilities');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();
        });

        // 2. users (FK ke roles)
        Schema::create('users', function (Blueprint $table) {
            $table->unsignedInteger('id')->autoIncrement();
            $table->string('identifier', 50)->unique()->comment('Unique login ID — NIS for students, staff code for teachers/staff');
            $table->string('name', 100)->comment('Full name of the user');
            $table->string('email', 150)->nullable()->comment('Optional email address for notifications');
            $table->string('password', 255)->comment('Bcrypt-hashed password');
            $table->unsignedInteger('role_id')->comment('References roles.id to determine permissions');
            $table->enum('status', ['active', 'inactive'])->default('active')->comment('active = can login; inactive = account disabled');
            $table->timestamp('last_login')->nullable()->comment('Timestamp of the most recent successful login');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();

            $table->index('role_id', 'idx_users_role_id');
            $table->index('status', 'idx_users_status');
            $table->foreign('role_id', 'fk_users_role')
                ->references('id')->on('roles')->onUpdate('cascade');
        });

        // 3. password_reset_tokens
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // 4. sessions
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary()->comment('Unique session token');
            $table->unsignedBigInteger('user_id')->nullable()->comment('Authenticated user ID, NULL for guest sessions');
            $table->string('ip_address', 45)->nullable()->comment('Client IP address at session creation');
            $table->text('user_agent')->nullable()->comment('Client browser/app user-agent string');
            $table->longText('payload')->comment('Serialized (base64) session data');
            $table->integer('last_activity')->comment('Unix timestamp of the last request in this session');

            $table->index('user_id', 'idx_sessions_user_id');
            $table->index('last_activity', 'idx_sessions_last_activity');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
        Schema::dropIfExists('roles');
    }
};