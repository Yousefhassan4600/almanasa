<?php

namespace App\Filament\Resources\ProviderCodes\Schemas;

use App\Filament\Support\CurrentAccount;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\ProviderCode;
use App\Models\PurchaseUnit;
use Closure;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class ProviderCodeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                CurrentAccount::providerSelect(Select::make('provider_id'))
                    ->label(__('admin.labels.Provider'))
                    ->relationship('provider', 'name')
                    ->live()
                    ->afterStateUpdated(function (Set $set): void {
                        $set('course_id', null);
                        $set('lesson_id', null);
                    })
                    ->preload()
                    ->searchable()
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('code')
                    ->label(__('admin.labels.Code'))
                    ->maxLength(255)
                    ->rules([
                        fn (Get $get, ?ProviderCode $record): Closure => function (string $attribute, mixed $value, Closure $fail) use ($get, $record): void {
                            $providerId = $get('provider_id');

                            if (blank($providerId) || blank($value)) {
                                return;
                            }

                            $exists = ProviderCode::query()
                                ->where('provider_id', $providerId)
                                ->where('code', $value)
                                ->when($record?->exists, fn (Builder $query): Builder => $query->whereKeyNot($record->getKey()))
                                ->exists();

                            if ($exists) {
                                $fail(__('admin.messages.provider_code_already_exists'));
                            }
                        },
                    ])
                    ->required()
                    ->columnSpanFull(),
                Select::make('purchase_unit_id')
                    ->label(__('admin.labels.Purchase Unit'))
                    ->options(fn (): array => PurchaseUnit::query()
                        ->where('is_active', true)
                        ->orderBy('sort_order')
                        ->get()
                        ->mapWithKeys(fn (PurchaseUnit $purchaseUnit): array => [
                            $purchaseUnit->id => $purchaseUnit->name,
                        ])
                        ->all())
                    ->searchable()
                    ->preload()
                    ->required()
                    ->columnSpanFull(),
                Select::make('course_id')
                    ->label(__('admin.labels.Course'))
                    ->options(fn (Get $get): array => blank(self::selectedProviderId($get))
                        ? []
                        : Course::query()
                            ->where('provider_id', self::selectedProviderId($get))
                            ->tap(fn (Builder $query) => CurrentAccount::scopeCoursesToCurrentAccount($query))
                            ->oldest('id')
                            ->get()
                            ->mapWithKeys(fn (Course $course): array => [
                                $course->id => $course->title,
                            ])
                            ->all())
                    ->live()
                    ->afterStateUpdated(fn (Set $set) => $set('lesson_id', null))
                    ->rules([
                        fn (Get $get): Closure => function (string $attribute, mixed $value, Closure $fail) use ($get): void {
                            $providerId = self::selectedProviderId($get);

                            if (blank($providerId) || blank($value)) {
                                return;
                            }

                            $belongsToProvider = Course::query()
                                ->whereKey($value)
                                ->where('provider_id', $providerId)
                                ->exists();

                            if (! $belongsToProvider) {
                                $fail(__('admin.messages.selected_course_not_in_provider'));
                            }
                        },
                    ])
                    ->disabled(fn (Get $get): bool => blank(self::selectedProviderId($get)))
                    ->preload()
                    ->searchable(),
                Select::make('lesson_id')
                    ->label(__('admin.labels.Lesson'))
                    ->options(fn (Get $get): array => blank($get('course_id'))
                        ? []
                        : Lesson::query()
                            ->where('course_id', $get('course_id'))
                            ->tap(fn (Builder $query) => CurrentAccount::scopeLessonsToCurrentAccount($query))
                            ->oldest('sort_order')
                            ->oldest('id')
                            ->get()
                            ->mapWithKeys(fn (Lesson $lesson): array => [
                                $lesson->id => $lesson->title,
                            ])
                            ->all())
                    ->rules([
                        fn (Get $get): Closure => function (string $attribute, mixed $value, Closure $fail) use ($get): void {
                            $courseId = $get('course_id');

                            if (blank($courseId) || blank($value)) {
                                return;
                            }

                            $belongsToCourse = Lesson::query()
                                ->whereKey($value)
                                ->where('course_id', $courseId)
                                ->exists();

                            if (! $belongsToCourse) {
                                $fail(__('admin.messages.selected_lesson_not_in_course'));
                            }
                        },
                    ])
                    ->disabled(fn (Get $get): bool => blank($get('course_id')))
                    ->preload()
                    ->searchable(),
                TextInput::make('num_of_uses')
                    ->label(__('admin.labels.Number Of Uses'))
                    ->numeric()
                    ->default(1)
                    ->minValue(1)
                    ->required(),
                DatePicker::make('expiry_date')
                    ->label(__('admin.labels.Expiry Date'))
                    ->native(false),
            ])
            ->columns(2);
    }

    private static function selectedProviderId(Get $get): ?int
    {
        $providerId = $get('provider_id') ?: CurrentAccount::providerId();

        return filled($providerId) ? (int) $providerId : null;
    }
}
