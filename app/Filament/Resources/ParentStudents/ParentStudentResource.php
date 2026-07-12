<?php

namespace App\Filament\Resources\ParentStudents;

use App\Filament\Resources\ParentStudents\Pages\ManageParentStudents;
use App\Models\ParentStudent;
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

class ParentStudentResource extends Resource
{
    protected static ?string $model = ParentStudent::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Students & Families';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('parent_user_id')
                    ->label('Parent User Id')
                    ->numeric()
                    ->required(),
                TextInput::make('student_user_id')
                    ->label('Student User Id')
                    ->numeric()
                    ->required(),
                TextInput::make('relation')
                    ->label('Relation'),
                Toggle::make('is_primary')
                    ->label('Is Primary'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('parent_user_id')
                    ->label('Parent User Id')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('student_user_id')
                    ->label('Student User Id')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('relation')
                    ->label('Relation')
                    ->searchable()
                    ->sortable(),
                IconColumn::make('is_primary')
                    ->label('Is Primary')
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
            'index' => ManageParentStudents::route('/'),
        ];
    }
}
