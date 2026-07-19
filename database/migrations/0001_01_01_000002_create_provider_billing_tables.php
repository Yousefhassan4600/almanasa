<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('providers', function (Blueprint $table): void {
            $table->id();
            $table->string('type');
            $table->foreignId('owner_user_id')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->unsignedBigInteger('subject_id')->nullable()->index();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('subdomain')->nullable()->unique();
            $table->string('custom_domain')->nullable()->unique();
            $table->string('logo')->nullable();
            $table->string('cover_image')->nullable();
            $table->text('bio')->nullable();
            $table->unsignedBigInteger('country_id')->nullable();
            $table->unsignedBigInteger('city_id')->nullable();
            $table->string('address')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_whatsapp')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('youtube_link')->nullable();
            $table->string('facebook_link')->nullable();
            $table->string('instagram_link')->nullable();
            $table->string('linkedin_link')->nullable();
            $table->string('x_link')->nullable();
            $table->string('snapchat_link')->nullable();
            $table->text('terms_conditions')->nullable();
            $table->decimal('latitude', 10, 2)->nullable();
            $table->decimal('longitude', 10, 2)->nullable();
            $table->string('primary_color')->nullable();
            $table->string('secondary_color')->nullable();
            $table->boolean('pause_website')->default(false);
            $table->string('current_course_period_type')->default('term_1');
            $table->integer('completion_watch_percentage')->default(70)->min(1)->max(100);
            $table->boolean('is_active')->default(true);
            $table->boolean('use_custom_domain')->default(false);
            $table->softDeletes();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index('type');
        });

        Schema::create('provider_plans', function (Blueprint $table): void {
            $table->id();
            $table->text('name');
            $table->text('description')->nullable();
            $table->unsignedInteger('max_students')->nullable();
            $table->unsignedInteger('max_courses')->nullable();
            $table->unsignedInteger('max_teachers')->nullable();
            $table->text('features')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->softDeletes();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['is_active', 'sort_order']);
        });

        Schema::create('provider_plan_options', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('provider_plan_id')->constrained('provider_plans')->cascadeOnUpdate()->cascadeOnDelete();
            $table->unsignedInteger('billing_period_days');
            $table->decimal('price', 10, 2)->default(0);
            $table->unsignedInteger('sort_order')->default(0);
            $table->softDeletes();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['provider_plan_id', 'billing_period_days'], 'ppo_plan_period_unique');
            $table->index(['provider_plan_id', 'sort_order']);
        });

        Schema::create('provider_subscriptions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('provider_id')->constrained('providers')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('provider_plan_option_id')->constrained('provider_plan_options')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('status')->default('pending');
            $table->decimal('amount', 10, 2)->default(0);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->json('metadata')->nullable();
            $table->softDeletes();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['provider_id', 'status']);
            $table->index(['status', 'ends_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('provider_subscriptions');
        Schema::dropIfExists('provider_plan_options');
        Schema::dropIfExists('provider_plans');
        Schema::dropIfExists('providers');
    }
};
