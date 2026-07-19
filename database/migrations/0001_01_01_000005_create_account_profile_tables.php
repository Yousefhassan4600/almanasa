<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('provider_id')->nullable()->constrained('providers')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('type');
            $table->foreignId('owner_user_id')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamp('approved_at')->nullable();
            $table->softDeletes();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index('provider_id');
            $table->index(['type']);
            $table->unique(['provider_id', 'type', 'owner_user_id']);
        });

        Schema::create('roles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('provider_id')->nullable()->constrained('providers')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('created_by_account_id')->nullable()->constrained('accounts')->cascadeOnUpdate()->nullOnDelete();
            $table->string('name');
            $table->string('guard_name')->default('web');
            $table->boolean('is_assignable')->default(true);
            $table->softDeletes();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['provider_id', 'name']);
            $table->index('provider_id');
        });

        Schema::create('employees', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('account_id')->constrained('accounts')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('predefined_role')->nullable();
            $table->foreignId('role_id')->nullable()->constrained('roles')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['account_id', 'user_id']);
            $table->index(['account_id', 'is_active']);
            $table->index(['account_id', 'predefined_role']);
        });

        //

        Schema::create('academy_teachers', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('provider_id')->constrained('providers')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('teacher_account_id')->constrained('accounts')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('image')->nullable();
            $table->integer('experience_years')->default(1);
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['provider_id', 'teacher_account_id']);
            $table->index(['provider_id', 'is_active']);
        });

        Schema::create('student_profiles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('email')->nullable()->unique();
            $table->string('avatar')->nullable();
            $table->string('gender')->nullable();
            $table->foreignId('country_id')->nullable()->constrained('countries')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('city_id')->nullable()->constrained('cities')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('education_stage_id')->nullable()->constrained('education_stages')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('grade_id')->nullable()->constrained('grades')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('school_name')->nullable();
            $table->softDeletes();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('parent_students', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('parent_user_id')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('student_user_id')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('relation')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->string('occupation')->nullable();
            $table->softDeletes();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['parent_user_id', 'student_user_id']);
        });

        Schema::create('account_subjects', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('provider_id')->constrained('providers')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('grade_subject_id')->constrained('grade_subjects')->cascadeOnUpdate()->restrictOnDelete();
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['provider_id', 'grade_subject_id'], 'account_subjects_provider_grade_subject_unique');
        });

        Schema::create('academy_teacher_grade_subjects', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('academy_teacher_id')->constrained('academy_teachers')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('account_subject_id')->constrained('account_subjects')->cascadeOnUpdate()->cascadeOnDelete();
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['academy_teacher_id', 'account_subject_id'], 'academy_teacher_subject_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academy_teacher_grade_subjects');
        Schema::dropIfExists('account_subjects');

        Schema::dropIfExists('parent_students');

        Schema::dropIfExists('student_profiles');

        Schema::dropIfExists('academy_teachers');

        //

        Schema::dropIfExists('employees');

        Schema::dropIfExists('roles');
        Schema::dropIfExists('accounts');
    }
};
