<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academy_teachers', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('academy_account_id')->constrained('accounts')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('teacher_account_id')->constrained('accounts')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('status')->default('pending');
            $table->timestamp('joined_at')->nullable();
            $table->timestamps();
            $table->unique(['academy_account_id', 'teacher_account_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academy_teachers');
    }
};
