<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parent_students', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('parent_user_id')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('student_user_id')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('relation')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->string('occupation')->nullable();
            $table->timestamps();
            $table->unique([
                0 => 'parent_user_id',
                1 => 'student_user_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parent_students');
    }
};
