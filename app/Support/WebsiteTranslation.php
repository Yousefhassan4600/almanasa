<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;

class WebsiteTranslation
{
    public static function value(?Model $model, string $attribute): ?string
    {
        if (! $model) {
            return null;
        }

        if (! method_exists($model, 'isTranslatableAttribute') || ! $model->isTranslatableAttribute($attribute)) {
            return $model->{$attribute};
        }

        $locale = app()->getLocale() === 'ar' ? 'ar' : 'en';
        $otherLocale = $locale === 'ar' ? 'en' : 'ar';

        foreach ([$locale, $otherLocale] as $candidate) {
            $translation = $model->getTranslation($attribute, $candidate, false);

            if (filled($translation)) {
                return $translation;
            }
        }

        return null;
    }
}
