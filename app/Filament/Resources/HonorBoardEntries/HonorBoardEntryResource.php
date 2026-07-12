<?php

namespace App\Filament\Resources\HonorBoardEntries;

use App\Filament\Resources\HonorBoardEntries\Pages\ManageHonorBoardEntries;
use App\Models\HonorBoardEntry;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class HonorBoardEntryResource extends Resource
{
    protected static ?string $model = HonorBoardEntry::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Communication & Website';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('account_id')
                    ->label('Account Id')
                    ->numeric(),
                TextInput::make('student_user_id')
                    ->label('Student User Id')
                    ->numeric(),
                TextInput::make('name')
                    ->label('Name')
                    ->required(),
                TextInput::make('grade_name')
                    ->label('Grade Name'),
                TextInput::make('score_percentage')
                    ->label('Score Percentage')
                    ->numeric(),
                TextInput::make('rank_label')
                    ->label('Rank Label'),
                TextInput::make('image')
                    ->label('Image'),
                Toggle::make('is_active')
                    ->label('Is Active'),
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
                TextColumn::make('student_user_id')
                    ->label('Student User Id')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('grade_name')
                    ->label('Grade Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('score_percentage')
                    ->label('Score Percentage')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('rank_label')
                    ->label('Rank Label')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('image')
                    ->label('Image')
                    ->searchable()
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('Is Active')
                    ->boolean(),
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
            'index' => ManageHonorBoardEntries::route('/'),
        ];
    }
}
