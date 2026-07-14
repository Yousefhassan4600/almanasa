<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('provider_payment_methods', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('provider_id')->constrained('providers')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('payment_method_id')->constrained('payment_methods')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('account_number')->nullable();
            $table->string('account_holder')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('phone_holder')->nullable();
            $table->timestamps();

            $table->unique(['provider_id', 'payment_method_id'], 'provider_payment_methods_provider_method_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('provider_payment_methods');
    }
};
