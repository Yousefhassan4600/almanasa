<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lesson_progress', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('student_user_id')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('account_id')->constrained('accounts')->cascadeOnUpdate()->restrictOnDelete();
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
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_progress');
    }
};
