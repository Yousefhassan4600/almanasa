<?php

namespace App\Filament\Resources\PlanItems;

use App\Filament\Resources\PlanItems\Pages\CreatePlanItem;
use App\Filament\Resources\PlanItems\Pages\EditPlanItem;
use App\Filament\Resources\PlanItems\Pages\ListPlanItems;
use App\Filament\Resources\PlanItems\Pages\ViewPlanItem;
use App\Models\Course;
use App\Models\Plan;
use App\Models\PlanItem;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class PlanItemResource extends Resource
{
    protected static ?string $model = PlanItem::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Commerce';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('plan_id')
                    ->relationship('plan', 'name')
                    ->required(),
                MorphToSelect::make('item')
                    ->types([
                        MorphToSelect\Type::make(Course::class)
                            ->titleAttribute('title'),
                        MorphToSelect\Type::make(Plan::class)
                            ->titleAttribute('name'),
                    ])
                    ->required(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('plan.name')
                    ->label('Plan'),
                TextEntry::make('item_display_name')
                    ->label('Item'),
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
                TextColumn::make('plan.name')
                    ->searchable(),
                TextColumn::make('item_display_name')
                    ->label('Item')
                    ->searchable(),
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
            'index' => ListPlanItems::route('/'),
            'create' => CreatePlanItem::route('/create'),
            'view' => ViewPlanItem::route('/{record}'),
            'edit' => EditPlanItem::route('/{record}/edit'),
        ];
    }
}
