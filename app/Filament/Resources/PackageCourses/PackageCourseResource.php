<?php

namespace App\Filament\Resources\PackageCourses;

use App\Filament\Resources\PackageCourses\Pages\ManagePackageCourses;
use App\Models\PackageCourse;
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

class PackageCourseResource extends Resource
{
    protected static ?string $model = PackageCourse::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Sales & Payments';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('package_id')
                    ->label('Package Id')
                    ->numeric()
                    ->required(),
                TextInput::make('course_id')
                    ->label('Course Id')
                    ->numeric()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('package_id')
                    ->label('Package Id')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('course_id')
                    ->label('Course Id')
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
            'index' => ManagePackageCourses::route('/'),
        ];
    }
}
