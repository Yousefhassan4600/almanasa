<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attempt_status_types', function (Blueprint $table): void {
            $table->id();
            $table->integer('sort_order')->default(0);
            $table->text('name');
            $table->string('slug')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('lesson_progress_status_types', function (Blueprint $table): void {
            $table->id();
            $table->integer('sort_order')->default(0);
            $table->text('name');
            $table->string('slug')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('student_attempts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('student_user_id')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('exam_model_id')->nullable()->constrained('exam_models')->cascadeOnUpdate()->nullOnDelete();
            $table->string('attemptable_type');
            $table->unsignedBigInteger('attemptable_id');
            $table->integer('attempt_number')->default(1);
            $table->decimal('max_score', 10, 2)->nullable();
            $table->timestamps();
            $table->index([
                0 => 'attemptable_type',
                1 => 'attemptable_id',
            ]);
        });

        Schema::create('attempt_statuses', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('student_attempt_id')->constrained('student_attempts')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('attempt_status_type_id')->constrained('attempt_status_types')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();
            $table->boolean('is_current')->default(true);
            $table->text('notes')->nullable();
            $table->timestamp('status_at')->nullable();
            $table->timestamps();
            $table->index([
                0 => 'student_attempt_id',
                1 => 'is_current',
            ], 'attempt_status_current_idx');
        });

        Schema::create('student_answers', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('student_attempt_id')->constrained('student_attempts')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('question_id')->constrained('questions')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('question_option_id')->nullable()->constrained('question_options')->cascadeOnUpdate()->restrictOnDelete();
            $table->text('answer_text')->nullable();
            $table->boolean('is_correct')->nullable();
            $table->decimal('score', 10, 2)->nullable();
            $table->timestamps();
        });

        Schema::create('lesson_progress', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('student_user_id')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('lesson_id')->constrained('lessons')->cascadeOnUpdate()->restrictOnDelete();
            $table->timestamps();
            $table->unique([
                0 => 'student_user_id',
                1 => 'lesson_id',
            ]);
        });

        Schema::create('lesson_progress_statuses', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('lesson_progress_id')->constrained('lesson_progress')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('lesson_progress_status_type_id')->constrained('lesson_progress_status_types')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();
            $table->boolean('is_current')->default(true);
            $table->text('notes')->nullable();
            $table->timestamp('status_at')->nullable();
            $table->timestamps();
            $table->index([
                0 => 'lesson_progress_id',
                1 => 'is_current',
            ], 'lesson_progress_status_current_idx');
        });

        Schema::create('course_reviews', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('student_user_id')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnUpdate()->restrictOnDelete();
            $table->unsignedTinyInteger('rating');
            $table->text('comment')->nullable();
            $table->boolean('is_approved')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_reviews');

        Schema::dropIfExists('lesson_progress_statuses');

        Schema::dropIfExists('lesson_progress');

        Schema::dropIfExists('student_answers');

        Schema::dropIfExists('attempt_statuses');

        Schema::dropIfExists('student_attempts');

        Schema::dropIfExists('lesson_progress_status_types');

        Schema::dropIfExists('attempt_status_types');
    }
};
