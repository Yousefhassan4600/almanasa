<?php

namespace App\Filament\Resources\Banners;

use App\Filament\Resources\Banners\Pages\ManageBanners;
use App\Models\Banner;
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

class BannerResource extends Resource
{
    protected static ?string $model = Banner::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Communication & Website';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('account_id')
                    ->label('Account Id')
                    ->numeric(),
                TextInput::make('title')
                    ->label('Title')
                    ->required(),
                TextInput::make('subtitle')
                    ->label('Subtitle'),
                TextInput::make('image')
                    ->label('Image'),
                TextInput::make('button_text')
                    ->label('Button Text'),
                TextInput::make('button_url')
                    ->label('Button Url'),
                TextInput::make('sort_order')
                    ->label('Sort Order')
                    ->numeric()
                    ->required(),
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
                TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('subtitle')
                    ->label('Subtitle')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('image')
                    ->label('Image')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('button_text')
                    ->label('Button Text')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('button_url')
                    ->label('Button Url')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('sort_order')
                    ->label('Sort Order')
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
            'index' => ManageBanners::route('/'),
        ];
    }
}
