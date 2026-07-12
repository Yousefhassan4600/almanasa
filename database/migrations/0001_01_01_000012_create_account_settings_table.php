<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('account_settings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('provider_id')->unique()->constrained('providers')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('primary_color')->nullable();
            $table->string('secondary_color')->nullable();
            $table->boolean('website_enabled')->default(true);
            $table->boolean('registration_enabled')->default(true);
            $table->boolean('chat_enabled')->default(true);
            $table->boolean('payment_enabled')->default(true);
            $table->decimal('tax_percentage', 10, 2)->default(0);
            $table->integer('completion_watch_percentage')->default(70);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_settings');
    }
};
