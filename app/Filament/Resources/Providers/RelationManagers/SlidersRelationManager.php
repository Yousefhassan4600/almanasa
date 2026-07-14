<?php

namespace App\Filament\Resources\Providers\RelationManagers;

use App\Filament\Base\RelationManagers\BaseRelationManager;
use App\Filament\Resources\Providers\RelationManagers\Tables\SlidersTable;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class SlidersRelationManager extends BaseRelationManager
{
    protected static string $relationship = 'banners';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('cover')
                    ->label('Cover')
                    ->image()
                    ->directory('banners')
                    ->columnSpanFull()
                    ->required(),
                TextInput::make('title.ar')
                    ->label('Title (Arabic)')
                    ->required(),
                TextInput::make('title.en')
                    ->label('Title (English)')
                    ->required(),
                TextInput::make('subtitle.ar')
                    ->label('Subtitle (Arabic)'),
                TextInput::make('subtitle.en')
                    ->label('Subtitle (English)'),
                TextInput::make('url')
                    ->label('URL')
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->label('Is Active')
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return SlidersTable::configure($table)
            ->heading('Sliders')
            ->recordTitleAttribute('title')
            ->headerActions($this->getTableHeaderActions())
            ->filters($this->getTableFilters())
            ->recordActions($this->getTableActions());
    }

    public function getTableFilters(): array
    {
        return [];
    }
}
