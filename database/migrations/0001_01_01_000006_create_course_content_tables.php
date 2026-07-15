<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('provider_id')->constrained('providers')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('account_subject_id')->constrained('account_subjects')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('academy_teacher_id')->nullable()->constrained('academy_teachers')->cascadeOnUpdate()->restrictOnDelete();
            $table->text('title');
            $table->text('description')->nullable();
            $table->string('thumbnail')->nullable();
            $table->integer('weekly_lectures_count')->nullable();
            $table->integer('num_of_lessons')->nullable();
            $table->integer('num_of_hours')->nullable();
            $table->decimal('academy_percentage', 10, 2)->default(50);
            $table->decimal('teacher_percentage', 10, 2)->default(40);
            $table->decimal('platform_percentage', 10, 2)->default(10);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('course_outcomes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnUpdate()->restrictOnDelete();
            $table->text('title');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('purchase_units', function (Blueprint $table): void {
            $table->id();
            $table->string('type')->unique();
            $table->text('name')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('course_prices', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('purchase_unit_id')->constrained('purchase_units')->cascadeOnUpdate()->restrictOnDelete();
            $table->decimal('price', 10, 2);
            $table->decimal('offer_price', 10, 2)->nullable();
            $table->timestamps();
            $table->unique(['course_id', 'purchase_unit_id'], 'course_prices_purchase_unit_unique');
        });

        Schema::create('course_periods', function (Blueprint $table): void {
            $table->id();
            $table->string('type')->unique(); // term_1 || term_2 || yearly
            $table->text('name')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('lessons', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('course_period_id')->nullable()->constrained('course_periods')->cascadeOnUpdate()->restrictOnDelete();
            $table->text('title');
            $table->text('description')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->unsignedInteger('num_of_video_views')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('assignments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('provider_id')->constrained('providers')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('lesson_id')->nullable()->constrained('lessons')->cascadeOnUpdate()->restrictOnDelete();
            $table->text('title');
            $table->text('description')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->decimal('max_score', 10, 2)->default(100);
            $table->boolean('allow_retake')->default(true);
            $table->integer('max_attempts')->nullable();
            $table->string('status')->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('exams', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('provider_id')->constrained('providers')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('lesson_id')->nullable()->constrained('lessons')->cascadeOnUpdate()->restrictOnDelete();
            $table->text('title');
            $table->text('description')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->decimal('max_score', 10, 2)->default(100);
            $table->decimal('pass_score', 10, 2)->nullable();
            $table->integer('max_attempts')->default(1);
            $table->boolean('stop_on_page_leave')->default(false);
            $table->string('status')->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('lesson_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('lesson_id')->constrained('lessons')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('type');
            $table->text('title');
            $table->text('description')->nullable();
            $table->string('video_url')->nullable();
            $table->string('file_url')->nullable();
            $table->string('link_url')->nullable();
            $table->foreignId('assignment_id')->nullable()->constrained('assignments')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('exam_id')->nullable()->constrained('exams')->cascadeOnUpdate()->restrictOnDelete();
            $table->integer('duration_minutes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_free')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('questions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('provider_id')->constrained('providers')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('questionable_type');
            $table->unsignedBigInteger('questionable_id');
            $table->string('type');
            $table->text('title');
            $table->decimal('points', 10, 2)->default(1);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->index([
                0 => 'questionable_type',
                1 => 'questionable_id',
            ]);
        });

        Schema::create('question_options', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('question_id')->constrained('questions')->cascadeOnUpdate()->restrictOnDelete();
            $table->text('title');
            $table->boolean('is_correct')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('question_options');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('lesson_items');
        Schema::dropIfExists('exams');
        Schema::dropIfExists('assignments');
        Schema::dropIfExists('lessons');
        Schema::dropIfExists('course_periods');
        Schema::dropIfExists('course_prices');
        Schema::dropIfExists('purchase_units');
        Schema::dropIfExists('course_outcomes');
        Schema::dropIfExists('courses');
    }
};
