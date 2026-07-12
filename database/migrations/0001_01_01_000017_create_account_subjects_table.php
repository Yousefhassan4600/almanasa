<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('account_subjects', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('account_id')->constrained('accounts')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('grade_subject_id')->constrained('grade_subjects')->cascadeOnUpdate()->restrictOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['account_id', 'grade_subject_id'], 'account_subjects_account_grade_subject_unique');
        });

        Schema::create('academy_teacher_grade_subjects', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('academy_teacher_id')->constrained('academy_teachers')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('account_subject_id')->constrained('account_subjects')->cascadeOnUpdate()->cascadeOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['academy_teacher_id', 'account_subject_id'], 'academy_teacher_subject_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academy_teacher_grade_subjects');
        Schema::dropIfExists('account_subjects');
    }
};
