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
            $table->softDeletes();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
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
            $table->softDeletes();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['provider_id', 'payment_method_id'], 'provider_payment_methods_provider_method_unique');
        });

        Schema::create('provider_codes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('provider_id')->constrained('providers')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('course_id')->nullable()->constrained('courses')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('lesson_id')->nullable()->constrained('lessons')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('code');
            $table->foreignId('purchase_unit_id')->constrained('purchase_units')->cascadeOnUpdate()->restrictOnDelete();
            $table->date('expiry_date')->nullable();
            $table->unsignedInteger('num_of_uses')->default(1);
            $table->softDeletes();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['provider_id', 'code'], 'provider_codes_provider_code_unique');
            $table->index(['provider_id', 'purchase_unit_id']);
            $table->index(['provider_id', 'course_id']);
            $table->index(['provider_id', 'lesson_id']);
            $table->index('expiry_date');
        });

        Schema::create('order_status_types', function (Blueprint $table): void {
            $table->id();
            $table->text('name');
            $table->string('slug')->unique();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('carts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('student_user_id')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('provider_id')->constrained('providers')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('purchase_type')->default('single_course');
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->softDeletes();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['provider_id', 'student_user_id']);
            $table->index(['provider_id', 'purchase_type']);
        });

        Schema::create('cart_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('cart_id')->constrained('carts')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('course_price_id')->nullable()->constrained('course_prices')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('purchase_unit_id')->constrained('purchase_units')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('purchase_type')->default('single_course');
            $table->string('title');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total', 10, 2);
            $table->softDeletes();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['cart_id', 'course_id'], 'cart_items_cart_course_unique');
            $table->index(['course_id', 'purchase_unit_id']);
        });

        Schema::create('orders', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('provider_id')->constrained('providers')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('student_user_id')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('cart_id')->nullable()->constrained('carts')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('order_number')->unique();
            $table->string('purchase_type')->default('single_course');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('total', 10, 2);
            $table->softDeletes();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['provider_id', 'student_user_id']);
            $table->index(['provider_id', 'purchase_type']);
        });

        Schema::create('order_statuses', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('order_status_type_id')->constrained('order_status_types')->cascadeOnUpdate()->restrictOnDelete();
            $table->boolean('is_current')->default(true);
            $table->timestamp('status_at')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->softDeletes();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['order_id', 'is_current']);
            $table->index(['order_status_type_id', 'status_at']);
        });

        Schema::create('order_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('course_price_id')->nullable()->constrained('course_prices')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('purchase_unit_id')->constrained('purchase_units')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('purchase_type')->default('single_course');
            $table->string('title');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total', 10, 2);
            $table->softDeletes();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['order_id', 'course_id'], 'order_items_order_course_unique');
            $table->index(['course_id', 'purchase_unit_id']);
        });

        Schema::create('subscriptions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('student_user_id')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('provider_id')->constrained('providers')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('order_item_id')->nullable()->constrained('order_items')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('purchase_unit_id')->constrained('purchase_units')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('purchase_type')->default('single_course');
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->softDeletes();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['student_user_id', 'course_id']);
            $table->index(['provider_id', 'student_user_id']);
        });

        Schema::create('payments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('provider_id')->constrained('providers')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('student_user_id')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('provider_payment_method_id')->nullable()->constrained('provider_payment_methods')->cascadeOnUpdate()->restrictOnDelete();
            $table->decimal('amount', 10, 2);
            $table->string('transaction_reference')->nullable();
            $table->foreignId('provider_code_id')->nullable()->constrained('provider_codes')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('sender_phone')->nullable();
            $table->string('transfer_image')->nullable();
            $table->text('gateway_response')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->foreignId('reviewed_by_user_id')->nullable()->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->softDeletes();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['provider_id', 'student_user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');

        Schema::dropIfExists('subscriptions');

        Schema::dropIfExists('order_items');

        Schema::dropIfExists('order_statuses');

        Schema::dropIfExists('orders');

        Schema::dropIfExists('cart_items');

        Schema::dropIfExists('carts');

        Schema::dropIfExists('order_status_types');

        Schema::dropIfExists('provider_codes');

        Schema::dropIfExists('provider_payment_methods');

        Schema::dropIfExists('payment_methods');
    }
};
