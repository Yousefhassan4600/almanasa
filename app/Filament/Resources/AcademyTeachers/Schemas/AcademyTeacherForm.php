<?php

namespace App\Filament\Resources\AcademyTeachers\Schemas;

use App\Enums\AccountType;
use App\Enums\ProviderType;
use App\Models\AcademyTeacher;
use App\Models\Account;
use App\Models\User;
use Closure;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class AcademyTeacherForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('provider_id')
                    ->label('Academy')
                    ->relationship(
                        name: 'provider',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn ($query) => $query->where('type', ProviderType::Academy->value)
                    )
                    ->live()
                    ->preload()
                    ->searchable()
                    ->required(),
                Select::make('teacher_user_id')
                    ->label('Teacher User')
                    ->options(fn (): array => User::query()
                        ->orderBy('first_name')
                        ->get()
                        ->mapWithKeys(fn (User $user): array => [$user->id => $user->name])
                        ->all())
                    ->live()
                    ->rules([
                        fn (Get $get, ?AcademyTeacher $record): Closure => function (string $attribute, mixed $value, Closure $fail) use ($get, $record): void {
                            $providerId = $get('provider_id');

                            if (blank($providerId) || blank($value)) {
                                return;
                            }

                            $existingAccount = Account::query()
                                ->where('provider_id', $providerId)
                                ->where('owner_user_id', $value)
                                ->when($record?->teacher_account_id, fn ($query) => $query->whereKeyNot($record->teacher_account_id))
                                ->first();

                            if ($existingAccount) {
                                $fail('This user already has an account for the selected academy.');

                                return;
                            }

                            $existingAssignment = AcademyTeacher::query()
                                ->where('provider_id', $providerId)
                                ->whereHas('teacher', fn ($query) => $query
                                    ->where('type', AccountType::AcademyTeacher->value)
                                    ->where('owner_user_id', $value))
                                ->when($record?->exists, fn ($query) => $query->whereKeyNot($record->getKey()))
                                ->exists();

                            if ($existingAssignment) {
                                $fail('This user is already assigned as a teacher for the selected academy.');
                            }
                        },
                    ])
                    ->preload()
                    ->searchable()
                    ->required(),
                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
            ]);
    }
}
