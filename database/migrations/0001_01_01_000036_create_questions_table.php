<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('provider_id')->constrained('providers')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('questionable_type');
            $table->unsignedBigInteger('questionable_id');
            $table->string('type');
            $table->text('title');
            $table->decimal('points', 10, 2)->default(1);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->index([
                0 => 'questionable_type',
                1 => 'questionable_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
