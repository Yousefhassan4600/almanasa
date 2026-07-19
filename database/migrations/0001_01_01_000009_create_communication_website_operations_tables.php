<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_rooms', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('provider_id')->constrained('providers')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('course_id')->nullable()->constrained('courses')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('lesson_id')->nullable()->constrained('lessons')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('title');
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('chat_members', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('chat_room_id')->constrained('chat_rooms')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('role');
            $table->timestamp('joined_at')->nullable();
            $table->timestamp('last_read_at')->nullable();
            $table->softDeletes();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique([
                0 => 'chat_room_id',
                1 => 'user_id',
            ]);
        });

        Schema::create('chat_messages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('chat_room_id')->constrained('chat_rooms')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('sender_user_id')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->text('message')->nullable();
            $table->string('file_url')->nullable();
            $table->string('file_name')->nullable();
            $table->string('file_size')->nullable();
            $table->softDeletes();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('notifications', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('provider_id')->nullable()->constrained('providers')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('title');
            $table->text('body')->nullable();
            $table->text('data')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->softDeletes();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('support_tickets', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('provider_id')->nullable()->constrained('providers')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('subject');
            $table->text('message');
            $table->string('status')->default('open');
            $table->softDeletes();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('support_ticket_replies', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('support_ticket_id')->constrained('support_tickets')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->text('message');
            $table->softDeletes();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('banners', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('provider_id')->nullable()->constrained('providers')->cascadeOnUpdate()->restrictOnDelete();
            $table->text('title');
            $table->text('subtitle')->nullable();
            $table->string('cover')->nullable();
            $table->string('url')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('testimonials', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('provider_id')->nullable()->constrained('providers')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('name');
            $table->string('role')->nullable();
            $table->integer('rating')->nullable();
            $table->text('message');
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('honor_board_entries', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('provider_id')->nullable()->constrained('providers')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('student_user_id')->nullable()->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->text('name');
            $table->string('grade_name')->nullable();
            $table->decimal('score_percentage', 10, 2)->nullable();
            $table->string('rank_label')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('provider_id')->nullable()->constrained('providers')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('action');
            $table->string('auditable_type')->nullable();
            $table->unsignedBigInteger('auditable_id')->nullable();
            $table->text('old_values')->nullable();
            $table->text('new_values')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->softDeletes();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index([
                0 => 'auditable_type',
                1 => 'auditable_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');

        Schema::dropIfExists('honor_board_entries');

        Schema::dropIfExists('testimonials');

        Schema::dropIfExists('banners');

        Schema::dropIfExists('support_ticket_replies');

        Schema::dropIfExists('support_tickets');

        Schema::dropIfExists('notifications');

        Schema::dropIfExists('chat_messages');

        Schema::dropIfExists('chat_members');

        Schema::dropIfExists('chat_rooms');
    }
};
