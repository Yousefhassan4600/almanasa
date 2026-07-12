<?php

namespace App\Filament\Resources\Courses;

use App\Enums\ContentStatus;
use App\Filament\Resources\Courses\Pages\ManageCourses;
use App\Models\Course;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Learning Content';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('account_id')
                    ->label('Account Id')
                    ->numeric()
                    ->required(),
                TextInput::make('teacher_account_id')
                    ->label('Teacher Account Id')
                    ->numeric(),
                Select::make('account_subject_id')
                    ->relationship('accountSubject', 'id')
                    ->getOptionLabelFromRecordUsing(fn ($record): string => $record->name)
                    ->searchable()
                    ->required(),
                TextInput::make('title')
                    ->label('Title')
                    ->required(),
                TextInput::make('slug')
                    ->label('Slug')
                    ->required(),
                Textarea::make('description')
                    ->label('Description')
                    ->columnSpanFull(),
                TextInput::make('thumbnail')
                    ->label('Thumbnail'),
                TextInput::make('term')
                    ->label('Term'),
                TextInput::make('price')
                    ->label('Price')
                    ->numeric()
                    ->required(),
                TextInput::make('monthly_price')
                    ->label('Monthly Price')
                    ->numeric(),
                TextInput::make('weekly_lectures_count')
                    ->label('Weekly Lectures Count')
                    ->numeric(),
                Select::make('status')
                    ->label('Status')
                    ->options(ContentStatus::options())
                    ->required(),
                Toggle::make('is_featured')
                    ->label('Is Featured'),
                DateTimePicker::make('published_at')
                    ->label('Published At'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('account_id')
                    ->label('Account Id')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('teacher_account_id')
                    ->label('Teacher Account Id')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('accountSubject.name')
                    ->label('Grade Subject')
                    ->searchable(),
                TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('thumbnail')
                    ->label('Thumbnail')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageCourses::route('/'),
        ];
    }
}
