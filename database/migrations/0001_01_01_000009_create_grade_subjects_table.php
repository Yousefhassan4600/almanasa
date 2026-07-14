<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grade_subjects', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('grade_id')->constrained('grades')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnUpdate()->restrictOnDelete();
            $table->timestamps();
            $table->unique([
                0 => 'grade_id',
                1 => 'subject_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grade_subjects');
    }
};
