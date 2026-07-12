<?php

namespace App\Filament\Resources\AcademyTeachers;

use App\Enums\AccountStatus;
use App\Filament\Resources\AcademyTeachers\Pages\ManageAcademyTeachers;
use App\Models\AcademyTeacher;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class AcademyTeacherResource extends Resource
{
    protected static ?string $model = AcademyTeacher::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Identity & Accounts';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('academy_account_id')
                    ->label('Academy Account Id')
                    ->numeric()
                    ->required(),
                TextInput::make('teacher_account_id')
                    ->label('Teacher Account Id')
                    ->numeric()
                    ->required(),
                Select::make('status')
                    ->label('Status')
                    ->options(AccountStatus::options())
                    ->required(),
                DateTimePicker::make('joined_at')
                    ->label('Joined At'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('academy_account_id')
                    ->label('Academy Account Id')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('teacher_account_id')
                    ->label('Teacher Account Id')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('joined_at')
                    ->label('Joined At')
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
            'index' => ManageAcademyTeachers::route('/'),
        ];
    }
}
