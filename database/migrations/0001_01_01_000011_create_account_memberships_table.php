<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('account_id')->constrained('accounts')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('predefined_role')->nullable();
            $table->foreignId('role_id')->nullable()->constrained('roles')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['account_id', 'user_id']);
            $table->index(['account_id', 'is_active']);
            $table->index(['account_id', 'predefined_role']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
