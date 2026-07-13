<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('provider_id')->nullable()->constrained('providers')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('type');
            $table->foreignId('owner_user_id')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamp('approved_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->index('provider_id');
            $table->index(['type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
