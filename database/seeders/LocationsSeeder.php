<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Country;

class LocationsSeeder extends BaseSeeder
{
    public function run(): void
    {
        $egypt = Country::query()->firstOrCreate([
            'code' => 'EG',
        ], [
            'name' => $this->translation('Egypt', 'مصر'),
            'phone_code' => '+20',
        ]);

        City::query()->firstOrCreate([
            'country_id' => $egypt->id,
            'name->en' => 'Cairo',
        ], [
            'name' => $this->translation('Cairo', 'القاهرة'),
        ]);
    }
}
