<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_attempts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('student_user_id')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('provider_id')->constrained('providers')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('attemptable_type');
            $table->unsignedBigInteger('attemptable_id');
            $table->integer('attempt_number')->default(1);
            $table->string('status')->default('in_progress');
            $table->decimal('score', 10, 2)->nullable();
            $table->decimal('max_score', 10, 2)->nullable();
            $table->decimal('percentage', 10, 2)->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('graded_at')->nullable();
            $table->integer('time_spent_seconds')->nullable();
            $table->timestamps();
            $table->index([
                0 => 'attemptable_type',
                1 => 'attemptable_id',
            ]);
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
            $table->foreignId('provider_id')->constrained('providers')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('lesson_id')->constrained('lessons')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('status')->default('not_started');
            $table->integer('watched_seconds')->default(0);
            $table->integer('required_seconds')->nullable();
            $table->decimal('completion_percentage', 10, 2)->default(0);
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('last_watched_at')->nullable();
            $table->timestamps();
            $table->unique([
                0 => 'student_user_id',
                1 => 'lesson_id',
            ]);
        });

        Schema::create('download_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('student_user_id')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('lesson_item_id')->constrained('lesson_items')->cascadeOnUpdate()->restrictOnDelete();
            $table->timestamp('downloaded_at');
            $table->timestamps();
        });

        Schema::create('course_reviews', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('student_user_id')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('provider_id')->constrained('providers')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnUpdate()->restrictOnDelete();
            $table->integer('rating');
            $table->text('comment')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_reviews');

        Schema::dropIfExists('download_logs');

        Schema::dropIfExists('lesson_progress');

        Schema::dropIfExists('student_answers');

        Schema::dropIfExists('student_attempts');
    }
};
