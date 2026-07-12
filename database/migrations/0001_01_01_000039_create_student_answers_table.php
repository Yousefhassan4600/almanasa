<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
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
    }

    public function down(): void
    {
        Schema::dropIfExists('student_answers');
    }
};
