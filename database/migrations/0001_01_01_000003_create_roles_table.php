<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('account_id');
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();
            $table->string('name');
            $table->string('guard_name')->default('web');
            $table->boolean('is_assignable')->default(true);
            $table->timestamps();
            $table->unique(['account_id', 'name']);
            $table->index('account_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
