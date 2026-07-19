<?php

namespace App\Enums;

enum PurchaseType: string
{
    case SingleCourse = 'single_course';
    case AllSubjectsOffer = 'all_subjects_offer';

    public static function options(): array
    {
        return [
            self::SingleCourse->value => 'Single Course',
            self::AllSubjectsOffer->value => 'All Subjects Offer',
        ];
    }
}
