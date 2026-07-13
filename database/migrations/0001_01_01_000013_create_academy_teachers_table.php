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
            $table->foreignId('provider_id')->constrained('providers')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('teacher_account_id')->constrained('accounts')->cascadeOnUpdate()->restrictOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['provider_id', 'teacher_account_id']);
            $table->index(['provider_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academy_teachers');
    }
};
