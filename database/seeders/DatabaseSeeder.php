<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PaymentMethodsSeeder::class,
            LearningStructureSeeder::class,
            LocationsSeeder::class,
            EducationCatalogSeeder::class,
            ProvidersAndAccountsSeeder::class,
            CoursesSeeder::class,
            CommerceSeeder::class,
        ]);
    }
}
