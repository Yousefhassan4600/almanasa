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
    }

    public function down(): void
    {
        Schema::dropIfExists('student_attempts');
    }
};
