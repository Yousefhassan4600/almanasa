<?php

namespace App\Filament\Resources\Courses\Schemas;

use App\Enums\ProviderType;
use App\Filament\Support\CurrentAccount;
use App\Models\AcademyTeacher;
use App\Models\AccountSubject;
use App\Models\Provider;
use App\Models\PurchaseUnit;
use BackedEnum;
use Filament\Forms\Components\Contracts\CanDisableOptions;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class CourseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin.labels.Basic Information'))
                    ->schema([
                        CurrentAccount::providerSelect(Select::make('provider_id'))
                            ->label(__('admin.labels.Provider'))
                            ->relationship('provider', 'name')
                            ->live()
                            ->afterStateUpdated(function (Set $set, mixed $state): void {
                                $set('account_subject_id', null);
                                $set(
                                    'academy_teacher_id',
                                    self::shouldShowAcademyTeacherField($state) ? CurrentAccount::academyTeacherId() : null,
                                );
                            })
                            ->preload()
                            ->searchable()
                            ->required(),
                        Select::make('academy_teacher_id')
                            ->label(__('admin.labels.Academy Teacher'))
                            ->default(fn (): ?int => CurrentAccount::academyTeacherId())
                            ->options(fn (Get $get): array => AcademyTeacher::query()
                                ->with(['teacher.owner'])
                                ->when($get('provider_id'), fn (Builder $query, int $providerId): Builder => $query->where('provider_id', $providerId))
                                ->when(CurrentAccount::isAcademyTeacher(), fn (Builder $query): Builder => $query->whereKey(CurrentAccount::academyTeacherId()))
                                ->where('is_active', true)
                                ->get()
                                ->mapWithKeys(fn (AcademyTeacher $academyTeacher): array => [
                                    $academyTeacher->id => $academyTeacher->teacher?->owner?->name ?? "Teacher #{$academyTeacher->id}",
                                ])
                                ->all())
                            ->live()
                            ->afterStateUpdated(fn (Set $set): mixed => $set('account_subject_id', null))
                            ->disabled(fn (): bool => CurrentAccount::isAcademyTeacher())
                            ->visible(fn (Get $get): bool => self::shouldShowAcademyTeacherField($get('provider_id')))
                            ->dehydrated(true)
                            ->searchable()
                            ->preload()
                            ->required(fn (Get $get): bool => self::shouldShowAcademyTeacherField($get('provider_id'))),
                        Select::make('account_subject_id')
                            ->label(__('admin.labels.Grade Subject'))
                            ->options(fn (Get $get): array => self::accountSubjectOptions($get))
                            ->searchable()
                            ->preload()
                            ->disabled(fn (Get $get): bool => self::shouldDisableAccountSubjectField($get))
                            ->required(),
                        TextInput::make('title.ar')
                            ->label(__('admin.labels.Title (Arabic)'))
                            ->required(),
                        TextInput::make('title.en')
                            ->label(__('admin.labels.Title (English)'))
                            ->required(),
                        Textarea::make('description.ar')
                            ->label(__('admin.labels.Description (Arabic)'))
                            ->columnSpanFull(),
                        Textarea::make('description.en')
                            ->label(__('admin.labels.Description (English)'))
                            ->columnSpanFull(),
                        FileUpload::make('thumbnail')
                            ->label(__('admin.labels.Thumbnail'))
                            ->image()
                            ->directory('courses/thumbnails')
                            ->columnSpanFull(),
                    ])
                    ->columns(1)
                    ->columnSpan(2),
                Section::make(__('admin.labels.Prices'))
                    ->schema([
                        Repeater::make('prices')
                            ->label(__('admin.labels.Course Prices'))
                            ->relationship()
                            ->schema([
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
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                    ->disableOptionWhen(
                                        static function (Component&CanDisableOptions $component, string $value, mixed $state): bool {
                                            $repeater = $component->getParentRepeater();

                                            if (! $repeater) {
                                                return false;
                                            }

                                            $fieldPath = (string) str($component->getStatePath())
                                                ->after("{$repeater->getStatePath()}.")
                                                ->after('.');

                                            $selectedSiblingIds = collect($repeater->getRawState())
                                                ->pluck($fieldPath)
                                                ->flatten()
                                                ->filter(fn (mixed $selectedId): bool => filled($selectedId))
                                                ->map(fn (mixed $selectedId): string => (string) ($selectedId instanceof BackedEnum ? $selectedId->value : $selectedId));

                                            $currentRowIds = collect(is_array($state) ? $state : [$state])
                                                ->filter(fn (mixed $selectedId): bool => filled($selectedId))
                                                ->map(fn (mixed $selectedId): string => (string) ($selectedId instanceof BackedEnum ? $selectedId->value : $selectedId));

                                            return $selectedSiblingIds
                                                ->diff($currentRowIds)
                                                ->contains((string) $value);
                                        },
                                        merge: true,
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                TextInput::make('price')
                                    ->label(__('admin.labels.Price'))
                                    ->numeric()
                                    ->default(0)
                                    ->required(),
                                TextInput::make('offer_price')
                                    ->label(__('admin.labels.Offer Price'))
                                    ->numeric()
                                    ->default(0)
                                    ->required(),

                            ])
                            ->columns(3)
                            ->defaultItems(fn (): int => self::activePurchaseUnitsCount())
                            ->minItems(fn (): int => self::activePurchaseUnitsCount())
                            ->maxItems(fn (): int => self::activePurchaseUnitsCount())
                            ->columnSpanFull(),
                    ])
                    ->columnSpan(3),
                Section::make(__('admin.labels.Course Details'))
                    ->schema([
                        TextInput::make('weekly_lectures_count')
                            ->label(__('admin.labels.Weekly Lectures Count'))
                            ->numeric(),
                        TextInput::make('num_of_lessons')
                            ->label(__('admin.labels.Number Of Lessons'))
                            ->numeric(),
                        TextInput::make('num_of_hours')
                            ->label(__('admin.labels.Number Of Hours'))
                            ->numeric(),
                        TextInput::make('academy_percentage')
                            ->label(__('admin.labels.Academy Percentage'))
                            ->numeric()
                            ->default(50)
                            ->suffix('%')
                            ->required(),
                        TextInput::make('teacher_percentage')
                            ->label(__('admin.labels.Teacher Percentage'))
                            ->numeric()
                            ->default(40)
                            ->suffix('%')
                            ->required(),
                        TextInput::make('platform_percentage')
                            ->label(__('admin.labels.Platform Percentage'))
                            ->numeric()
                            ->default(10)
                            ->suffix('%')
                            ->required(),
                    ])
                    ->columns(1)
                    ->columnSpan(1),
                Section::make(__('admin.labels.Outcomes'))
                    ->schema([
                        Repeater::make('outcomes')
                            ->label(__('admin.labels.Course Outcomes'))
                            ->relationship()
                            ->schema([
                                TextInput::make('title.ar')
                                    ->label(__('admin.labels.Title (Arabic)'))
                                    ->required(),
                                TextInput::make('title.en')
                                    ->label(__('admin.labels.Title (English)'))
                                    ->required(),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->grid(2)
                            ->orderColumn('sort_order')
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ])->columns(6);
    }

    private static function shouldShowAcademyTeacherField(mixed $providerId): bool
    {
        if (blank($providerId)) {
            return true;
        }

        return Provider::query()
            ->whereKey($providerId)
            ->where('type', '!=', ProviderType::StandaloneTeacher)
            ->exists();
    }

    private static function shouldDisableAccountSubjectField(Get $get): bool
    {
        if (blank($get('provider_id'))) {
            return true;
        }

        return self::shouldShowAcademyTeacherField($get('provider_id')) && blank($get('academy_teacher_id'));
    }

    /**
     * @return array<int, string>
     */
    private static function accountSubjectOptions(Get $get): array
    {
        $providerId = $get('provider_id');

        if (blank($providerId)) {
            return [];
        }

        return AccountSubject::query()
            ->with(['gradeSubject.grade.educationStage', 'gradeSubject.subject.track'])
            ->where('provider_id', $providerId)
            ->where('is_active', true)
            ->when(
                self::shouldShowAcademyTeacherField($providerId),
                fn (Builder $query): Builder => $query->whereHas(
                    'teacherAssignments',
                    fn (Builder $query): Builder => $query
                        ->where('academy_teacher_id', $get('academy_teacher_id'))
                        ->where('is_active', true),
                ),
            )
            ->get()
            ->mapWithKeys(fn (AccountSubject $accountSubject): array => [
                $accountSubject->id => $accountSubject->name,
            ])
            ->all();
    }

    private static function activePurchaseUnitsCount(): int
    {
        return PurchaseUnit::query()
            ->where('is_active', true)
            ->count();
    }
}
