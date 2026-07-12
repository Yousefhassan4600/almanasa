<?php

namespace App\Enums\Concerns;

trait HasOptions
{
    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(static::cases())
            ->mapWithKeys(fn (self $case): array => [$case->value => $case->label()])
            ->all();
    }

    public function label(): string
    {
        return str($this->value)->replace('_', ' ')->title()->toString();
    }
}
