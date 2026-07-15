<?php

namespace App\Filament\Resources\Providers\Schemas;

use App\Enums\CoursePeriodType;
use App\Enums\ProviderType;
use App\Models\GradeSubject;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProviderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->schema([
                        Select::make('type')
                            ->label('Type')
                            ->options(ProviderType::options())
                            ->required(),
                        Select::make('owner_user_id')
                            ->label('Owner User Id')
                            ->relationship('owner', 'first_name')
                            ->getOptionLabelFromRecordUsing(fn ($record): string => $record->name)
                            ->preload()
                            ->searchable()
                            ->required(),
                        FileUpload::make('logo')
                            ->label('Logo')
                            ->image()
                            ->directory('providers/logos')
                            ->required(),
                        TextInput::make('name')
                            ->label('Name')
                            ->required(),
                        TextInput::make('slug')
                            ->label('Slug')
                            ->required(),
                        Select::make('country_id')
                            ->label('Country')
                            ->relationship('country', 'name')
                            ->preload()
                            ->searchable()
                            ->required(),
                        Select::make('city_id')
                            ->label('City')
                            ->relationship('city', 'name')
                            ->preload()
                            ->searchable()
                            ->required(),
                        Toggle::make('use_custom_domain')
                            ->label('Use Custom Domain')
                            ->reactive(),
                        TextInput::make('subdomain')
                            ->label('Subdomain')
                            ->visible(fn ($get): bool => ! $get('use_custom_domain'))
                            ->required(fn ($get): bool => ! $get('use_custom_domain'))
                            ->dehydrated(fn ($get): bool => ! $get('use_custom_domain')),
                        TextInput::make('custom_domain')
                            ->label('Custom Domain')
                            ->visible(fn ($get): bool => $get('use_custom_domain'))
                            ->required(fn ($get): bool => $get('use_custom_domain'))
                            ->dehydrated(fn ($get): bool => $get('use_custom_domain')),
                    ]),
                Section::make('Additional Information')
                    ->schema([
                        FileUpload::make('cover_image')
                            ->label('Cover Image')
                            ->image()
                            ->directory('providers/cover_images'),
                        Textarea::make('bio.ar')
                            ->label('Bio (Arabic)')
                            ->columnSpanFull(),
                        Textarea::make('bio.en')
                            ->label('Bio (English)')
                            ->columnSpanFull(),
                        Textarea::make('address')
                            ->label('Address')
                            ->columnSpanFull(),
                        TextInput::make('latitude')
                            ->label('Latitude')
                            ->numeric(),
                        TextInput::make('longitude')
                            ->label('Longitude')
                            ->numeric(),
                        Toggle::make('is_active')
                            ->label('Is Active'),
                        Toggle::make('pause_website')
                            ->label('Pause Website')
                            ->default(false),
                    ]),
                Section::make('Contact Information')
                    ->schema([
                        TextInput::make('contact_phone')
                            ->label('Contact Phone')
                            ->tel(),
                        TextInput::make('contact_whatsapp')
                            ->label('Contact Whatsapp')
                            ->tel(),
                        TextInput::make('contact_email')
                            ->label('Contact Email')
                            ->email(),
                        TextInput::make('youtube_link')
                            ->label('Youtube Link')
                            ->url(),
                        TextInput::make('facebook_link')
                            ->label('Facebook Link')
                            ->url(),
                        TextInput::make('instagram_link')
                            ->label('Instagram Link')
                            ->url(),
                        TextInput::make('linkedin_link')
                            ->label('Linkedin Link')
                            ->url(),
                        TextInput::make('x_link')
                            ->label('X Link')
                            ->url(),
                        TextInput::make('snapchat_link')
                            ->label('Snapchat Link')
                            ->url(),
                    ])
                    ->columns(1),
                Section::make('Settings')
                    ->schema([
                        ColorPicker::make('primary_color')
                            ->label('Primary Color'),
                        ColorPicker::make('secondary_color')
                            ->label('Secondary Color'),
                        TextInput::make('completion_watch_percentage')
                            ->label('Completion Watch Percentage')
                            ->numeric()
                            ->default(70)
                            ->suffix('%')
                            ->minValue(1)
                            ->maxValue(100),
                        Select::make('current_course_period_type')
                            ->label('Current Course Period')
                            ->options(CoursePeriodType::options())
                            ->default(CoursePeriodType::Term1->value)
                            ->required(),
                        RichEditor::make('terms_conditions')
                            ->label('Terms Conditions')
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull()
                    ->columns(2),
                Section::make('Subjects')
                    ->schema([
                        Repeater::make('accountSubjects')
                            ->label('Subjects')
                            ->relationship()
                            ->schema([
                                Select::make('grade_subject_id')
                                    ->label('Grade Subject')
                                    ->options(fn (): array => GradeSubject::query()
                                        ->with(['grade.educationStage', 'subject.track'])
                                        ->get()
                                        ->mapWithKeys(fn (GradeSubject $gradeSubject): array => [
                                            $gradeSubject->id => $gradeSubject->full_name,
                                        ])
                                        ->all())
                                    ->searchable()
                                    ->preload()
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                    ->required(),
                                Toggle::make('is_active')
                                    ->label('Is Active')
                                    ->default(true),
                            ])
                            ->columns(1)
                            ->defaultItems(0)
                            ->addActionLabel('Add Subject')
                            ->grid(3)
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ])->columns(3);
    }
}
