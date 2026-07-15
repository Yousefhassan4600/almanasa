<?php

namespace App\Filament\Resources\Courses\Schemas;

use App\Models\AcademyTeacher;
use App\Models\AccountSubject;
use App\Models\PurchaseUnit;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class CourseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->schema([
                        Select::make('provider_id')
                            ->label('Provider')
                            ->relationship('provider', 'name')
                            ->live()
                            ->preload()
                            ->searchable()
                            ->required(),
                        Select::make('account_subject_id')
                            ->label('Grade Subject')
                            ->options(fn (Get $get): array => AccountSubject::query()
                                ->with(['gradeSubject.grade.educationStage', 'gradeSubject.subject.track'])
                                ->when($get('provider_id'), fn (Builder $query, int $providerId): Builder => $query->where('provider_id', $providerId))
                                ->get()
                                ->mapWithKeys(fn (AccountSubject $accountSubject): array => [
                                    $accountSubject->id => $accountSubject->name,
                                ])
                                ->all())
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('academy_teacher_id')
                            ->label('Academy Teacher')
                            ->options(fn (Get $get): array => AcademyTeacher::query()
                                ->with(['teacher.owner'])
                                ->when($get('provider_id'), fn (Builder $query, int $providerId): Builder => $query->where('provider_id', $providerId))
                                ->get()
                                ->mapWithKeys(fn (AcademyTeacher $academyTeacher): array => [
                                    $academyTeacher->id => $academyTeacher->teacher?->owner?->name ?? "Teacher #{$academyTeacher->id}",
                                ])
                                ->all())
                            ->searchable()
                            ->preload(),
                        TextInput::make('title.ar')
                            ->label('Title (Arabic)')
                            ->required(),
                        TextInput::make('title.en')
                            ->label('Title (English)')
                            ->required(),
                        Textarea::make('description.ar')
                            ->label('Description (Arabic)')
                            ->columnSpanFull(),
                        Textarea::make('description.en')
                            ->label('Description (English)')
                            ->columnSpanFull(),
                        TextInput::make('thumbnail')
                            ->label('Thumbnail'),
                    ])
                    ->columns(3),
                Section::make('Course Details')
                    ->schema([
                        TextInput::make('weekly_lectures_count')
                            ->label('Weekly Lectures Count')
                            ->numeric(),
                        TextInput::make('num_of_lessons')
                            ->label('Number Of Lessons')
                            ->numeric(),
                        TextInput::make('num_of_hours')
                            ->label('Number Of Hours')
                            ->numeric(),
                        TextInput::make('academy_percentage')
                            ->label('Academy Percentage')
                            ->numeric()
                            ->default(50)
                            ->suffix('%')
                            ->required(),
                        TextInput::make('teacher_percentage')
                            ->label('Teacher Percentage')
                            ->numeric()
                            ->default(40)
                            ->suffix('%')
                            ->required(),
                        TextInput::make('platform_percentage')
                            ->label('Platform Percentage')
                            ->numeric()
                            ->default(10)
                            ->suffix('%')
                            ->required(),
                    ])
                    ->columns(3),
                Section::make('Prices')
                    ->schema([
                        Repeater::make('prices')
                            ->label('Course Prices')
                            ->relationship()
                            ->schema([
                                Select::make('purchase_unit_id')
                                    ->label('Purchase Unit')
                                    ->options(fn (): array => PurchaseUnit::query()
                                        ->where('is_active', true)
                                        ->orderBy('sort_order')
                                        ->get()
                                        ->mapWithKeys(fn (PurchaseUnit $purchaseUnit): array => [
                                            $purchaseUnit->id => $purchaseUnit->name,
                                        ])
                                        ->all())
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                TextInput::make('price')
                                    ->label('Price')
                                    ->numeric()
                                    ->required(),
                                TextInput::make('offer_price')
                                    ->label('Offer Price')
                                    ->numeric(),
                            ])
                            ->columns(3)
                            ->defaultItems(0)
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ])->columns(1);
    }
}
