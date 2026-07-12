<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('account_id')->constrained('accounts')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('account_subject_id')->constrained('account_subjects')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('teacher_account_id')->nullable()->constrained('accounts')->cascadeOnUpdate()->restrictOnDelete();
            $table->text('title');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('term')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('monthly_price', 10, 2)->nullable();
            $table->integer('weekly_lectures_count')->nullable();
            $table->string('status')->default('draft');
            $table->boolean('is_featured')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->unique([
                0 => 'account_id',
                1 => 'slug',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
