<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_profiles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('country_id')->nullable()->constrained('countries')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('city_id')->nullable()->constrained('cities')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('education_stage_id')->nullable()->constrained('education_stages')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('grade_id')->nullable()->constrained('grades')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('school_name')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_profiles');
    }
};
