<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // announcements (FK ke users)
        Schema::create('announcements', function (Blueprint $table) {
            $table->unsignedInteger('id')->autoIncrement();
            $table->string('title', 255)->comment('Short headline of the announcement');
            $table->text('content')->comment('Full body text of the announcement');
            $table->tinyInteger('is_published')->default(1)->comment('1 = visible to users; 0 = draft/hidden');
            $table->unsignedInteger('created_by')->comment('User ID of the staff member who created this announcement');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();

            $table->index('created_by', 'idx_announcements_created_by');
            $table->index('is_published', 'idx_announcements_published');
            $table->foreign('created_by', 'fk_announcements_user')
                ->references('id')->on('users')->onUpdate('cascade');
        });

        // events (FK ke event_categories, event_locations, users)
        Schema::create('events', function (Blueprint $table) {
            $table->unsignedInteger('id')->autoIncrement();
            $table->string('event_name', 255)->comment('Name/title of the event');
            $table->unsignedInteger('category_id')->comment('References event_categories.id');
            $table->unsignedInteger('location_id')->nullable()->comment('References event_locations.id');
            $table->date('event_date')->comment('Start date of the event');
            $table->date('event_date_end')->nullable()->comment('End date for multi-day events; NULL means single-day');
            $table->text('description')->nullable()->comment('Detailed description or agenda of the event');
            $table->tinyInteger('is_published')->default(1)->comment('1 = visible to users; 0 = draft/hidden');
            $table->unsignedInteger('created_by')->comment('User ID of the staff member who created this event');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();

            $table->index('category_id', 'idx_events_category_id');
            $table->index('location_id', 'idx_events_location_id');
            $table->index('created_by', 'idx_events_created_by');
            $table->index('event_date', 'idx_events_event_date');
            $table->index('is_published', 'idx_events_published');
            $table->foreign('category_id', 'fk_events_category')
                ->references('id')->on('event_categories')->onUpdate('cascade');
            $table->foreign('location_id', 'fk_events_location')
                ->references('id')->on('event_locations')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('created_by', 'fk_events_user')
                ->references('id')->on('users')->onUpdate('cascade');
        });

        // anonymous_reports (FK ke report_categories)
        Schema::create('anonymous_reports', function (Blueprint $table) {
            $table->unsignedInteger('id')->autoIncrement();
            $table->string('ticket_number', 20)->unique('uq_anonymous_reports_ticket')->comment('Unique public-facing code (e.g. TKT-001)');
            $table->unsignedInteger('category_id')->comment('References report_categories.id');
            $table->text('report_content')->comment('The full text of the anonymous report');
            $table->text('admin_notes')->nullable()->comment('Internal notes added by staff');
            $table->enum('status', ['pending', 'in_progress', 'solved'])->default('pending');
            $table->timestamp('resolved_at')->nullable()->comment('Timestamp when the report was marked as solved');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();

            $table->index('category_id', 'idx_anonymous_reports_category');
            $table->index('status', 'idx_anonymous_reports_status');
            $table->foreign('category_id', 'fk_anonymous_reports_category')
                ->references('id')->on('report_categories')->onUpdate('cascade');
        });

        // attachments (FK ke users)
        Schema::create('attachments', function (Blueprint $table) {
            $table->unsignedInteger('id')->autoIncrement();
            $table->enum('source_type', ['announcement', 'event'])->comment('Only announcements and events support attachments');
            $table->unsignedInteger('source_id')->comment('Primary key of the parent announcement or event');
            $table->enum('attachment_type', ['file', 'link'])->comment('file = uploaded document; link = external URL');
            $table->string('file_name', 255)->nullable()->comment('Original filename');
            $table->text('file_path')->nullable()->comment('Relative storage path');
            $table->string('file_type', 100)->nullable()->comment('MIME type');
            $table->integer('file_size')->nullable()->comment('File size in bytes — max 5,242,880 (5 MB)');
            $table->text('link_url')->nullable()->comment('Full URL for external link attachments');
            $table->string('link_label', 255)->nullable()->comment('Human-friendly display label for the link');
            $table->string('label', 255)->nullable()->comment('Optional short description of what this attachment is');
            $table->unsignedInteger('uploaded_by')->nullable()->comment('User ID who added this attachment');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();

            $table->index(['source_type', 'source_id'], 'idx_attachments_source');
            $table->index('attachment_type', 'idx_attachments_type');
            $table->index('uploaded_by', 'idx_attachments_uploaded_by');
            $table->foreign('uploaded_by', 'fk_attachments_user')
                ->references('id')->on('users')->onDelete('set null')->onUpdate('cascade');
        });

        // photos (FK ke users)
        Schema::create('photos', function (Blueprint $table) {
            $table->unsignedInteger('id')->autoIncrement();
            $table->enum('source_type', ['announcement', 'event', 'lost_found', 'anonymous_report'])->comment('Parent entity type this photo belongs to');
            $table->unsignedInteger('source_id')->comment('Primary key of the parent entity row');
            $table->string('file_name', 255)->comment('Original filename as uploaded');
            $table->text('file_path')->comment('Relative storage path (empty string if stored as base64)');
            $table->longText('file_data')->nullable()->comment('Base64 encoded image data (data:mime;base64,...)');
            $table->string('file_type', 100)->comment('MIME type');
            $table->integer('file_size')->comment('File size in bytes');
            $table->unsignedInteger('uploaded_by')->nullable()->comment('User who uploaded; NULL for anonymous report photos');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();

            $table->index(['source_type', 'source_id'], 'idx_photos_source');
            $table->index('uploaded_by', 'idx_photos_uploaded_by');
            $table->foreign('uploaded_by', 'fk_photos_user')
                ->references('id')->on('users')->onDelete('set null')->onUpdate('cascade');
        });

        // notifications (FK ke users)
        Schema::create('notifications', function (Blueprint $table) {
            $table->unsignedInteger('id')->autoIncrement();
            $table->unsignedInteger('user_id')->comment('Recipient user ID');
            $table->string('title', 255)->comment('Short notification headline');
            $table->text('body')->nullable()->comment('Full notification message');
            $table->string('type', 50)->comment('Category of notification (e.g. report_update, announcement, lost_found)');
            $table->integer('reference_id')->nullable()->comment('Optional ID of the related entity');
            $table->tinyInteger('is_read')->default(0)->comment('0 = unread; 1 = read');
            $table->timestamp('read_at')->nullable()->comment('Timestamp when the user marked it as read');
            $table->timestamp('created_at')->nullable()->useCurrent();

            $table->index('user_id', 'idx_notifications_user_id');
            $table->index('is_read', 'idx_notifications_is_read');
            $table->foreign('user_id', 'fk_notifications_user')
                ->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });

        // audit_logs (FK ke users)
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('user_id')->nullable()->comment('User who performed the action (NULL for system/unauthenticated actions)');
            $table->string('action', 100)->comment('Action performed (e.g. create, update, delete, login, logout)');
            $table->string('table_name', 100)->nullable()->comment('Database table affected by the action');
            $table->integer('record_id')->nullable()->comment('Primary key of the affected record');
            $table->json('old_values')->nullable()->comment('Snapshot of the record before the change');
            $table->json('new_values')->nullable()->comment('Snapshot of the record after the change');
            $table->string('ip_address', 45)->nullable()->comment('IP address of the user at the time of the action');
            $table->text('user_agent')->nullable()->comment('Browser/client user-agent string');
            $table->timestamp('created_at')->nullable()->useCurrent();

            $table->index('user_id', 'idx_audit_logs_user_id');
            $table->index(['table_name', 'record_id'], 'idx_audit_logs_table_record');
            $table->index('created_at', 'idx_audit_logs_created_at');
            $table->foreign('user_id', 'fk_audit_logs_user')
                ->references('id')->on('users')->onDelete('set null')->onUpdate('cascade');
        });

        // lost_founds
        Schema::create('lost_founds', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('User who submitted the report');
            $table->enum('type', ['found', 'lost']);
            $table->string('item_name', 100);
            $table->string('found_at', 150)->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('reject_reason')->nullable()->comment('Reason provided when admin rejects a lost/found report');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lost_founds');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('photos');
        Schema::dropIfExists('attachments');
        Schema::dropIfExists('anonymous_reports');
        Schema::dropIfExists('events');
        Schema::dropIfExists('announcements');
    }
};