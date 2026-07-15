<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_methods', function (Blueprint $table): void {
            $table->id();
            $table->integer('sort_order')->default(0);
            $table->text('name')->nullable();
            $table->string('slug')->nullable()->unique();
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_bank')->default(false);
            $table->boolean('require_proof')->default(false);
            $table->boolean('is_code')->default(false);
            $table->timestamps();
        });

        Schema::create('packages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('provider_id')->constrained('providers')->cascadeOnUpdate()->restrictOnDelete();
            $table->text('name');
            $table->text('description')->nullable();
            $table->integer('duration_days');
            $table->decimal('price', 10, 2);
            $table->boolean('is_all_subjects')->default(false);
            $table->boolean('is_custom')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->string('status')->default('draft');
            $table->timestamps();
        });

        Schema::create('package_courses', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('package_id')->constrained('packages')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnUpdate()->restrictOnDelete();
            $table->timestamps();
            $table->unique([
                0 => 'package_id',
                1 => 'course_id',
            ]);
        });

        Schema::create('subscriptions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('student_user_id')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('provider_id')->constrained('providers')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('package_id')->nullable()->constrained('packages')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('course_id')->nullable()->constrained('courses')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('status')->default('pending');
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->boolean('auto_renew')->default(false);
            $table->timestamps();
        });

        Schema::create('student_enrollments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('student_user_id')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('provider_id')->constrained('providers')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('course_id')->nullable()->constrained('courses')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('package_id')->nullable()->constrained('packages')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('subscription_id')->nullable()->constrained('subscriptions')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('status')->default('pending');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        Schema::create('carts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('student_user_id')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('provider_id')->constrained('providers')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('status')->default('open');
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('cart_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('cart_id')->constrained('carts')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('course_id')->nullable()->constrained('courses')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('package_id')->nullable()->constrained('packages')->cascadeOnUpdate()->restrictOnDelete();
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total', 10, 2);
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('provider_id')->constrained('providers')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('student_user_id')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('cart_id')->nullable()->constrained('carts')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('order_number')->unique();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->string('status')->default('pending');
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('course_id')->nullable()->constrained('courses')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('package_id')->nullable()->constrained('packages')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('subscription_id')->nullable()->constrained('subscriptions')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('title');
            $table->decimal('unit_price', 10, 2);
            $table->integer('quantity')->default(1);
            $table->decimal('total', 10, 2);
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('provider_id')->constrained('providers')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('student_user_id')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('method');
            $table->string('status')->default('pending');
            $table->decimal('amount', 10, 2);
            $table->string('transaction_reference')->nullable();
            $table->string('payment_code')->nullable();
            $table->string('sender_phone')->nullable();
            $table->string('transfer_image')->nullable();
            $table->text('gateway_response')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->foreignId('reviewed_by_user_id')->nullable()->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('payment_codes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('provider_id')->constrained('providers')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('code')->unique();
            $table->decimal('amount', 10, 2)->nullable();
            $table->integer('duration_days')->nullable();
            $table->integer('max_uses')->default(1);
            $table->integer('used_count')->default(0);
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('provider_codes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('provider_id')->constrained('providers')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('code');
            $table->foreignId('purchase_unit_id')->constrained('purchase_units')->cascadeOnUpdate()->restrictOnDelete();
            $table->date('expiry_date')->nullable();
            $table->unsignedInteger('num_of_uses')->default(1);
            $table->timestamps();
            $table->unique(['provider_id', 'code'], 'provider_codes_provider_code_unique');
            $table->index(['provider_id', 'purchase_unit_id']);
            $table->index('expiry_date');
        });

        Schema::create('coupons', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('provider_id')->nullable()->constrained('providers')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('code')->unique();
            $table->string('discount_type');
            $table->decimal('value', 10, 2);
            $table->integer('usage_limit')->nullable();
            $table->integer('usage_limit_per_user')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

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

        Schema::dropIfExists('coupons');

        Schema::dropIfExists('provider_codes');
        Schema::dropIfExists('payment_codes');

        Schema::dropIfExists('payments');

        Schema::dropIfExists('order_items');

        Schema::dropIfExists('orders');

        Schema::dropIfExists('cart_items');

        Schema::dropIfExists('carts');

        Schema::dropIfExists('student_enrollments');

        Schema::dropIfExists('subscriptions');

        Schema::dropIfExists('package_courses');

        Schema::dropIfExists('packages');

        Schema::dropIfExists('payment_methods');
    }
};
