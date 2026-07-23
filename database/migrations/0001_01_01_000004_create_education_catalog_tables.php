<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('education_stages', function (Blueprint $table): void {
            $table->id();
            $table->text('name');
            $table->integer('sort_order')->default(0);
            $table->softDeletes();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('grades', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('education_stage_id')->constrained('education_stages')->cascadeOnUpdate()->restrictOnDelete();
            $table->text('name');
            $table->integer('sort_order')->default(0);
            $table->softDeletes();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('tracks', function (Blueprint $table): void {
            $table->id();
            $table->text('name');
            $table->string('code')->nullable()->unique();
            $table->integer('sort_order')->default(0);
            $table->softDeletes();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('subjects', function (Blueprint $table): void {
            $table->id();
            $table->text('name');
            $table->string('icon')->nullable();
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->softDeletes();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('grade_subjects', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('grade_id')->constrained('grades')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('track_id')->constrained('tracks')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnUpdate()->restrictOnDelete();
            $table->softDeletes();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique([
                0 => 'grade_id',
                1 => 'track_id',
                2 => 'subject_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grade_subjects');

        Schema::dropIfExists('subjects');
        Schema::dropIfExists('tracks');

        Schema::dropIfExists('grades');

        Schema::dropIfExists('education_stages');
    }
};
