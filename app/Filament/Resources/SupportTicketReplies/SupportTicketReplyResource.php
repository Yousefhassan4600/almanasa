<?php

namespace App\Filament\Resources\SupportTicketReplies;

use App\Filament\Resources\SupportTicketReplies\Pages\ManageSupportTicketReplies;
use App\Models\SupportTicketReply;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class SupportTicketReplyResource extends Resource
{
    protected static ?string $model = SupportTicketReply::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Communication & Website';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('support_ticket_id')
                    ->label('Support Ticket Id')
                    ->numeric()
                    ->required(),
                TextInput::make('user_id')
                    ->label('User Id')
                    ->numeric()
                    ->required(),
                Textarea::make('message')
                    ->label('Message')
                    ->columnSpanFull()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('support_ticket_id')
                    ->label('Support Ticket Id')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user_id')
                    ->label('User Id')
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
            'index' => ManageSupportTicketReplies::route('/'),
        ];
    }
}
