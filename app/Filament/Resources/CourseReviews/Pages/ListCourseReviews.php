<?php

namespace App\Filament\Resources\CourseReviews\Pages;

use App\Filament\Base\Pages\BaseListRecords;
use App\Filament\Resources\CourseReviews\CourseReviewResource;

class ListCourseReviews extends BaseListRecords
{
    protected static string $resource = CourseReviewResource::class;
}
