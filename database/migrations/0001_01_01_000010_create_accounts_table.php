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
            $table->unique(['provider_id', 'type', 'owner_user_id']);
        });

        Schema::create('roles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('provider_id')->nullable()->constrained('providers')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('created_by_account_id')->nullable()->constrained('accounts')->cascadeOnUpdate()->nullOnDelete();
            $table->string('name');
            $table->string('guard_name')->default('web');
            $table->boolean('is_assignable')->default(true);
            $table->timestamps();
            $table->unique(['provider_id', 'name']);
            $table->index('provider_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
        Schema::dropIfExists('accounts');
    }
};
