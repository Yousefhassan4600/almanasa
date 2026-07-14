<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tracks', function (Blueprint $table): void {
            $table->id();
            $table->text('name');
            $table->string('code')->nullable()->unique();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('subjects', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('track_id')->constrained('tracks')->cascadeOnUpdate()->restrictOnDelete();
            $table->text('name');
            $table->string('icon')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subjects');
        Schema::dropIfExists('tracks');
    }
};
