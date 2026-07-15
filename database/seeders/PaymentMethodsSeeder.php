<?php

namespace Database\Seeders;

use App\Enums\PaymentMethodSlugs;
use App\Models\PaymentMethod;

class PaymentMethodsSeeder extends BaseSeeder
{
    public function run(): void
    {
        $paymentMethods = [
            [
                'slug' => PaymentMethodSlugs::Bank,
                'name' => $this->translation('Bank Transfer', 'تحويل بنكي'),
                'is_bank' => true,
                'require_proof' => true,
            ],
            [
                'slug' => PaymentMethodSlugs::InstaPay,
                'name' => $this->translation('InstaPay', 'إنستا باي'),
                'require_proof' => true,
            ],
            [
                'slug' => PaymentMethodSlugs::VodafoneCash,
                'name' => $this->translation('Vodafone Cash', 'فودافون كاش'),
                'require_proof' => true,
            ],
            [
                'slug' => PaymentMethodSlugs::OrangeCash,
                'name' => $this->translation('Orange Cash', 'أورنج كاش'),
                'require_proof' => true,
            ],
            [
                'slug' => PaymentMethodSlugs::ECash,
                'name' => $this->translation('e& Cash', 'إي آند كاش'),
                'require_proof' => true,
            ],
            [
                'slug' => PaymentMethodSlugs::Code,
                'name' => $this->translation('Code', 'كود'),
                'is_code' => true,
            ],
        ];

        foreach ($paymentMethods as $sort => $paymentMethod) {
            PaymentMethod::query()->updateOrCreate([
                'slug' => $paymentMethod['slug']->value,
            ], [
                'sort_order' => $sort + 1,
                'name' => $paymentMethod['name'],
                'is_active' => true,
                'is_bank' => $paymentMethod['is_bank'] ?? false,
                'require_proof' => $paymentMethod['require_proof'] ?? false,
                'is_code' => $paymentMethod['is_code'] ?? false,
            ]);
        }
    }
}
