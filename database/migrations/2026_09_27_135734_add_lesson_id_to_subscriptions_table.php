<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->foreignId('lesson_id')->nullable()->after('course_id')->constrained('lessons')->cascadeOnUpdate()->nullOnDelete();
            $table->index(['student_user_id', 'course_id', 'lesson_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropIndex(['student_user_id', 'course_id', 'lesson_id']);
            $table->dropConstrainedForeignId('lesson_id');
        });
    }
};
