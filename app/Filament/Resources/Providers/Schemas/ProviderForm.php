<?php

namespace App\Filament\Resources\Providers\Schemas;

use App\Enums\CoursePeriodType;
use App\Enums\ProviderType;
use App\Models\GradeSubject;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class ProviderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin.labels.Basic Information'))
                    ->schema([
                        Select::make('type')
                            ->label(__('admin.labels.Type'))
                            ->options(ProviderType::options())
                            ->live()
                            ->afterStateUpdated(function (Set $set): void {
                                $set('grade_subject_ids', []);
                            })
                            ->required(),
                        Select::make('owner_user_id')
                            ->label(__('admin.labels.Owner User Id'))
                            ->relationship('owner', 'first_name')
                            ->getOptionLabelFromRecordUsing(fn ($record): string => $record->name)
                            ->preload()
                            ->searchable()
                            ->required(),
                        FileUpload::make('logo')
                            ->label(__('admin.labels.Logo'))
                            ->image()
                            ->directory('providers/logos')
                            ->required(),
                        TextInput::make('name')
                            ->label(__('admin.labels.Name'))
                            ->required(),
                        TextInput::make('slug')
                            ->label(__('admin.labels.Slug'))
                            ->required(),
                        Select::make('country_id')
                            ->label(__('admin.labels.Country'))
                            ->relationship('country', 'name')
                            ->preload()
                            ->searchable()
                            ->required(),
                        Select::make('city_id')
                            ->label(__('admin.labels.City'))
                            ->relationship('city', 'name')
                            ->preload()
                            ->searchable()
                            ->required(),
                        Toggle::make('use_custom_domain')
                            ->label(__('admin.labels.Use Custom Domain'))
                            ->reactive(),
                        TextInput::make('subdomain')
                            ->label(__('admin.labels.Subdomain'))
                            ->visible(fn ($get): bool => ! $get('use_custom_domain'))
                            ->required(fn ($get): bool => ! $get('use_custom_domain'))
                            ->dehydrated(fn ($get): bool => ! $get('use_custom_domain')),
                        TextInput::make('custom_domain')
                            ->label(__('admin.labels.Custom Domain'))
                            ->visible(fn ($get): bool => $get('use_custom_domain'))
                            ->required(fn ($get): bool => $get('use_custom_domain'))
                            ->dehydrated(fn ($get): bool => $get('use_custom_domain')),
                    ]),
                Section::make(__('admin.labels.Additional Information'))
                    ->schema([
                        FileUpload::make('cover_image')
                            ->label(__('admin.labels.Cover Image'))
                            ->image()
                            ->directory('providers/cover_images'),
                        Textarea::make('bio.ar')
                            ->label(__('admin.labels.Bio (Arabic)'))
                            ->columnSpanFull(),
                        Textarea::make('bio.en')
                            ->label(__('admin.labels.Bio (English)'))
                            ->columnSpanFull(),
                        Textarea::make('address')
                            ->label(__('admin.labels.Address'))
                            ->columnSpanFull(),
                        TextInput::make('latitude')
                            ->label(__('admin.labels.Latitude'))
                            ->numeric(),
                        TextInput::make('longitude')
                            ->label(__('admin.labels.Longitude'))
                            ->numeric(),
                        Toggle::make('is_active')
                            ->label(__('admin.labels.Is Active')),
                        Toggle::make('pause_website')
                            ->label(__('admin.labels.Pause Website'))
                            ->default(false),
                    ]),
                Section::make(__('admin.labels.Contact Information'))
                    ->schema([
                        TextInput::make('contact_phone')
                            ->label(__('admin.labels.Contact Phone'))
                            ->tel(),
                        TextInput::make('contact_whatsapp')
                            ->label(__('admin.labels.Contact Whatsapp'))
                            ->tel(),
                        TextInput::make('contact_email')
                            ->label(__('admin.labels.Contact Email'))
                            ->email(),
                        TextInput::make('youtube_link')
                            ->label(__('admin.labels.Youtube Link'))
                            ->url(),
                        TextInput::make('facebook_link')
                            ->label(__('admin.labels.Facebook Link'))
                            ->url(),
                        TextInput::make('instagram_link')
                            ->label(__('admin.labels.Instagram Link'))
                            ->url(),
                        TextInput::make('linkedin_link')
                            ->label(__('admin.labels.Linkedin Link'))
                            ->url(),
                        TextInput::make('x_link')
                            ->label(__('admin.labels.X Link'))
                            ->url(),
                        TextInput::make('snapchat_link')
                            ->label(__('admin.labels.Snapchat Link'))
                            ->url(),
                    ])
                    ->columns(1),
                Section::make(__('admin.labels.Settings'))
                    ->schema([
                        ColorPicker::make('primary_color')
                            ->label(__('admin.labels.Primary Color')),
                        ColorPicker::make('secondary_color')
                            ->label(__('admin.labels.Secondary Color')),
                        TextInput::make('completion_watch_percentage')
                            ->label(__('admin.labels.Completion Watch Percentage'))
                            ->numeric()
                            ->default(70)
                            ->suffix('%')
                            ->minValue(1)
                            ->maxValue(100),
                        Select::make('current_course_period_type')
                            ->label(__('admin.labels.Current Course Period'))
                            ->options(CoursePeriodType::options())
                            ->default(CoursePeriodType::Term1->value)
                            ->required(),
                        RichEditor::make('terms_conditions')
                            ->label(__('admin.labels.Terms Conditions'))
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull()
                    ->columns(2),
                Section::make(__('admin.labels.Subjects'))
                    ->schema([
                        Select::make('grade_subject_ids')
                            ->label(__('admin.labels.Grade Subjects'))
                            ->multiple()
                            ->options(fn (): array => self::gradeSubjectOptions())
                            ->afterStateHydrated(function (Select $component, $record): void {
                                $component->state($record?->accountSubjects()
                                    ->where('is_active', true)
                                    ->pluck('grade_subject_id')
                                    ->all() ?? []);
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ])->columns(3);
    }

    private static function gradeSubjectOptions(): array
    {
        return GradeSubject::query()
            ->with(['grade.educationStage', 'track', 'subject'])
            ->get()
            ->mapWithKeys(fn (GradeSubject $gradeSubject): array => [
                $gradeSubject->id => $gradeSubject->full_name,
            ])
            ->all();
    }
}
