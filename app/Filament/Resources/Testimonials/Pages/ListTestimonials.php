<?php

namespace App\Filament\Resources\Testimonials\Pages;

use App\Filament\Base\Pages\BaseListRecords;
use App\Filament\Resources\Testimonials\TestimonialResource;

class ListTestimonials extends BaseListRecords
{
    protected static string $resource = TestimonialResource::class;
}
