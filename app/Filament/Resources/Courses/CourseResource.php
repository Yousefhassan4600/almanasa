<?php

namespace App\Filament\Resources\Courses;

use App\Enums\CourseStatus;
use App\Enums\Currency;
use App\Filament\Resources\Courses\Pages\CreateCourse;
use App\Filament\Resources\Courses\Pages\EditCourse;
use App\Filament\Resources\Courses\Pages\ListCourses;
use App\Filament\Resources\Courses\Pages\ViewCourse;
use App\Models\Course;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Course Content';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->required(),
                Select::make('teacher_grade_subject_assignment_id')
                    ->relationship('teacherAssignment', 'id')
                    ->getOptionLabelFromRecordUsing(fn ($record): string => $record->display_name)
                    ->label('Teacher assignment')
                    ->searchable()
                    ->preload()
                    ->required()
                    ,
                Select::make('academic_year_id')
                    ->relationship('academicYear', 'name')
                    ->required(),
                Select::make('term_id')
                    ->relationship('term', 'name'),
                TextInput::make('title')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                Textarea::make('learning_outcomes')
                    ->columnSpanFull(),
                TextInput::make('thumbnail'),
                TextInput::make('intro_video'),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->prefix('$'),
                Select::make('currency')
                    ->options(Currency::options())
                    ->required()
                    ->default(Currency::Egp->value),
                Select::make('status')
                    ->options(CourseStatus::options())
                    ->required()
                    ->default(CourseStatus::Draft->value),
                Toggle::make('is_featured')
                    ->required(),
                Toggle::make('is_free')
                    ->required(),
                DateTimePicker::make('published_at'),
                DateTimePicker::make('available_from'),
                DateTimePicker::make('available_until'),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label('Tenant'),
                TextEntry::make('teacherAssignment.display_name')
                    ->label('Teacher assignment'),
                TextEntry::make('academicYear.name')
                    ->label('Academic year'),
                TextEntry::make('term.name')
                    ->label('Term')
                    ->placeholder('-'),
                TextEntry::make('title'),
                TextEntry::make('slug'),
                TextEntry::make('description')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('learning_outcomes')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('thumbnail')
                    ->placeholder('-'),
                TextEntry::make('intro_video')
                    ->placeholder('-'),
                TextEntry::make('price')
                    ->money(),
                TextEntry::make('currency'),
                TextEntry::make('status'),
                IconEntry::make('is_featured')
                    ->boolean(),
                IconEntry::make('is_free')
                    ->boolean(),
                TextEntry::make('published_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('available_from')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('available_until')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->searchable(),
                TextColumn::make('teacherAssignment.display_name')
                    ->label('Teacher assignment')
                    ->sortable(),
                TextColumn::make('academicYear.name')
                    ->searchable(),
                TextColumn::make('term.name')
                    ->searchable(),
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('slug')
                    ->searchable(),
                TextColumn::make('thumbnail')
                    ->searchable(),
                TextColumn::make('intro_video')
                    ->searchable(),
                TextColumn::make('price')
                    ->money()
                    ->sortable(),
                TextColumn::make('currency')
                    ->searchable(),
                TextColumn::make('status')
                    ->searchable(),
                IconColumn::make('is_featured')
                    ->boolean(),
                IconColumn::make('is_free')
                    ->boolean(),
                TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('available_from')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('available_until')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCourses::route('/'),
            'create' => CreateCourse::route('/create'),
            'view' => ViewCourse::route('/{record}'),
            'edit' => EditCourse::route('/{record}/edit'),
        ];
    }
}
