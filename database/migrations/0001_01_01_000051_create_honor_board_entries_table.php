<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('honor_board_entries', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('provider_id')->nullable()->constrained('providers')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('student_user_id')->nullable()->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->text('name');
            $table->string('grade_name')->nullable();
            $table->decimal('score_percentage', 10, 2)->nullable();
            $table->string('rank_label')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('honor_board_entries');
    }
};
