<?php

namespace App\Filament\Resources\AccountSubjects\Schemas;

use App\Models\AccountSubject;
use App\Models\GradeSubject;
use Closure;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class AccountSubjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('provider_id')
                    ->relationship('provider', 'name')
                    ->live()
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('grade_subject_id')
                    ->options(fn(): array => GradeSubject::query()
                        ->with([
                            'grade.educationStage',
                            'subject.track',
                        ])
                        ->get()
                        ->mapWithKeys(fn(GradeSubject $gradeSubject): array => [$gradeSubject->id => $gradeSubject->full_name])
                        ->all())
                    ->searchable()
                    ->preload()
                    ->rules([
                        fn(Get $get, ?AccountSubject $record): Closure => function (string $attribute, mixed $value, Closure $fail) use ($get, $record): void {
                            $providerId = $get('provider_id');

                            if (blank($providerId) || blank($value)) {
                                return;
                            }

                            $exists = AccountSubject::query()
                                ->where('provider_id', $providerId)
                                ->where('grade_subject_id', $value)
                                ->when($record?->exists, fn($query) => $query->whereKeyNot($record->getKey()))
                                ->exists();

                            if ($exists) {
                                $fail('This provider already covers the selected grade subject.');
                            }
                        },
                    ])
                    ->required(),
                Toggle::make('is_active')
                    ->label('Is Active')
                    ->default(true),
            ]);
    }
}
