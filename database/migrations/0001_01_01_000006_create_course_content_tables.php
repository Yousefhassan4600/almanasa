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
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('course_outcomes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnUpdate()->restrictOnDelete();
            $table->text('title');
            $table->integer('sort_order')->default(0);
            $table->softDeletes();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('purchase_units', function (Blueprint $table): void {
            $table->id();
            $table->string('type')->unique();
            $table->text('name')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('course_prices', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('purchase_unit_id')->constrained('purchase_units')->cascadeOnUpdate()->restrictOnDelete();
            $table->decimal('price', 10, 2);
            $table->decimal('offer_price', 10, 2)->nullable();
            $table->softDeletes();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['course_id', 'purchase_unit_id'], 'course_prices_purchase_unit_unique');
        });

        Schema::create('course_periods', function (Blueprint $table): void {
            $table->id();
            $table->string('type')->unique(); // term_1 || term_2 || yearly
            $table->text('name')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
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
            $table->softDeletes();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('assignments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnUpdate()->restrictOnDelete();
            $table->text('title');
            $table->text('description')->nullable();
            $table->unsignedInteger('num_of_questions')->nullable();
            $table->unsignedInteger('num_of_easy_questions')->nullable();
            $table->unsignedInteger('num_of_medium_questions')->nullable();
            $table->unsignedInteger('num_of_hard_questions')->nullable();
            $table->unsignedInteger('duration_minutes')->nullable();
            $table->unsignedInteger('num_of_attempts')->nullable();
            $table->json('lesson_ids')->nullable();
            $table->json('question_ids')->nullable();
            $table->softDeletes();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('exams', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnUpdate()->restrictOnDelete();
            $table->text('title');
            $table->text('description')->nullable();
            $table->unsignedInteger('num_of_questions')->nullable();
            $table->unsignedInteger('num_of_easy_questions')->nullable();
            $table->unsignedInteger('num_of_medium_questions')->nullable();
            $table->unsignedInteger('num_of_hard_questions')->nullable();
            $table->unsignedInteger('duration_minutes')->nullable();
            $table->unsignedInteger('num_of_attempts')->default(1)->nullable();
            $table->decimal('max_degree', 10, 2)->nullable();
            $table->unsignedInteger('num_of_models')->default(1);
            $table->json('lesson_ids')->nullable();
            $table->softDeletes();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('exam_models', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->cascadeOnUpdate()->cascadeOnDelete();
            $table->unsignedInteger('model_number');
            $table->json('question_ids')->nullable();
            $table->softDeletes();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['exam_id', 'model_number']);
        });

        Schema::create('lesson_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('lesson_id')->constrained('lessons')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('assignment_id')->nullable()->constrained('assignments')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('exam_id')->nullable()->constrained('exams')->cascadeOnUpdate()->nullOnDelete();
            $table->string('type');
            $table->text('title');
            $table->text('description')->nullable();
            $table->string('video_url')->nullable();
            $table->string('file_url')->nullable();
            $table->string('link_url')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_free')->default(false);
            $table->integer('sort_order')->default(0);
            $table->softDeletes();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('questions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('lesson_id')->constrained('lessons')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('title');
            $table->string('media')->nullable();
            $table->string('type');
            $table->string('difficulty');
            $table->integer('sort_order')->default(0);
            $table->softDeletes();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['lesson_id', 'type', 'difficulty']);
        });

        Schema::create('question_options', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('question_id')->constrained('questions')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('title');
            $table->string('media')->nullable();
            $table->boolean('is_correct')->default(false);
            $table->integer('sort_order')->default(0);
            $table->softDeletes();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('question_options');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('lesson_items');
        Schema::dropIfExists('exam_models');
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
