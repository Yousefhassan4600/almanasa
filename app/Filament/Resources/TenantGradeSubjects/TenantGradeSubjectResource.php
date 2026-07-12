<?php

namespace App\Filament\Resources\TenantGradeSubjects;

use App\Filament\Resources\TenantGradeSubjects\Pages\CreateTenantGradeSubject;
use App\Filament\Resources\TenantGradeSubjects\Pages\EditTenantGradeSubject;
use App\Filament\Resources\TenantGradeSubjects\Pages\ListTenantGradeSubjects;
use App\Filament\Resources\TenantGradeSubjects\Pages\ViewTenantGradeSubject;
use App\Models\TenantGradeSubject;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class TenantGradeSubjectResource extends Resource
{
    protected static ?string $model = TenantGradeSubject::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Tenant Management';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->required(),
                Select::make('grade_subject_id')
                    ->relationship('gradeSubject', 'id')
                    ->getOptionLabelFromRecordUsing(fn ($record): string => $record->display_name)
                    ->label('Grade subject')
                    ->searchable()
                    ->preload()
                    ->required(),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label('Tenant'),
                TextEntry::make('gradeSubject.display_name')
                    ->label('Grade subject'),
                IconEntry::make('is_active')
                    ->boolean(),
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
                TextColumn::make('tenant.name')
                    ->searchable(),
                TextColumn::make('gradeSubject.display_name')
                    ->label('Grade subject')
                    ->searchable(),
                IconColumn::make('is_active')
                    ->boolean(),
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
            'index' => ListTenantGradeSubjects::route('/'),
            'create' => CreateTenantGradeSubject::route('/create'),
            'view' => ViewTenantGradeSubject::route('/{record}'),
            'edit' => EditTenantGradeSubject::route('/{record}/edit'),
        ];
    }
}
