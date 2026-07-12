<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('teacher_grade_subject_assignment_id')->constrained()->restrictOnDelete();
            $table->foreignId('academic_year_id')->constrained()->restrictOnDelete();
            $table->foreignId('term_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->json('learning_outcomes')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('intro_video')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->string('currency', 3)->default('EGP');
            $table->string('status')->default('draft');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_free')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamp('available_from')->nullable();
            $table->timestamp('available_until')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'slug'], 'course_tenant_slug_unique');
        });

        Schema::create('course_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('course_sections')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });

        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_section_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_free')->default(false);
            $table->boolean('is_preview')->default(false);
            $table->boolean('is_published')->default(false);
            $table->timestamp('available_at')->nullable();
            $table->unsignedInteger('estimated_duration')->nullable();
            $table->unsignedTinyInteger('completion_percentage_required')->default(70);
            $table->timestamps();

            $table->unique(['course_id', 'slug'], 'lesson_course_slug_unique');
        });

        Schema::create('lesson_contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->string('title');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_required')->default(true);
            $table->boolean('is_preview')->default(false);
            $table->timestamp('available_at')->nullable();
            $table->nullableMorphs('contentable');
            $table->timestamps();
        });

        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lesson_content_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('provider');
            $table->string('provider_video_id')->nullable();
            $table->text('video_url')->nullable();
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('processing_status')->default('pending');
            $table->string('visibility')->default('private');
            $table->timestamps();
        });

        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->morphs('resourceable');
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('file_path')->nullable();
            $table->string('disk')->default('private');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->boolean('is_downloadable')->default(false);
            $table->string('external_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resources');
        Schema::dropIfExists('videos');
        Schema::dropIfExists('lesson_contents');
        Schema::dropIfExists('lessons');
        Schema::dropIfExists('course_sections');
        Schema::dropIfExists('courses');
    }
};
