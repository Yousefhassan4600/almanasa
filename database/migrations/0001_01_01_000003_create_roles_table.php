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
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('subdomain')->nullable()->unique();
            $table->string('custom_domain')->nullable()->unique();
            $table->string('logo')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('bio')->nullable();
            $table->unsignedBigInteger('country_id')->nullable();
            $table->unsignedBigInteger('city_id')->nullable();
            $table->string('address')->nullable();
            $table->decimal('latitude', 10, 2)->nullable();
            $table->decimal('longitude', 10, 2)->nullable();
            $table->string('primary_color')->nullable();
            $table->string('secondary_color')->nullable();
            $table->boolean('website_enabled')->default(true);
            $table->boolean('registration_enabled')->default(true);
            $table->boolean('chat_enabled')->default(true);
            $table->boolean('payment_enabled')->default(true);
            $table->integer('completion_watch_percentage')->default(70);
            $table->boolean('is_active')->default(true);
            $table->boolean('use_custom_domain')->default(false);
            $table->softDeletes();
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
            $table->timestamps();
            $table->index(['is_active', 'sort_order']);
        });

        Schema::create('provider_plan_options', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('provider_plan_id')->constrained('provider_plans')->cascadeOnUpdate()->cascadeOnDelete();
            $table->unsignedInteger('billing_period_days');
            $table->decimal('price', 10, 2)->default(0);
            $table->unsignedInteger('sort_order')->default(0);
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
