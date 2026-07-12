<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lesson_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('lesson_id')->constrained('lessons')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('type');
            $table->text('title');
            $table->text('description')->nullable();
            $table->string('video_url')->nullable();
            $table->string('file_url')->nullable();
            $table->integer('duration_seconds')->nullable();
            $table->foreignId('assignment_id')->nullable()->constrained('assignments')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('exam_id')->nullable()->constrained('exams')->cascadeOnUpdate()->restrictOnDelete();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_required')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_items');
    }
};
