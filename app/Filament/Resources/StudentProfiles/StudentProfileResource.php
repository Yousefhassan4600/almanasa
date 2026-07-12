<?php

namespace App\Filament\Resources\StudentProfiles;

use App\Filament\Resources\StudentProfiles\Pages\ManageStudentProfiles;
use App\Models\StudentProfile;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class StudentProfileResource extends Resource
{
    protected static ?string $model = StudentProfile::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Students & Families';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id')
                    ->label('User Id')
                    ->numeric()
                    ->required(),
                TextInput::make('country_id')
                    ->label('Country Id')
                    ->numeric(),
                TextInput::make('city_id')
                    ->label('City Id')
                    ->numeric(),
                TextInput::make('education_stage_id')
                    ->label('Education Stage Id')
                    ->numeric(),
                TextInput::make('grade_id')
                    ->label('Grade Id')
                    ->numeric(),
                TextInput::make('school_name')
                    ->label('School Name'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user_id')
                    ->label('User Id')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('country_id')
                    ->label('Country Id')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('city_id')
                    ->label('City Id')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('education_stage_id')
                    ->label('Education Stage Id')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('grade_id')
                    ->label('Grade Id')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('school_name')
                    ->label('School Name')
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
            'index' => ManageStudentProfiles::route('/'),
        ];
    }
}
