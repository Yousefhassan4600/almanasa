<?php

namespace App\Filament\Resources\TeacherGradeSubjectAssignments;

use App\Filament\Resources\TeacherGradeSubjectAssignments\Pages\CreateTeacherGradeSubjectAssignment;
use App\Filament\Resources\TeacherGradeSubjectAssignments\Pages\EditTeacherGradeSubjectAssignment;
use App\Filament\Resources\TeacherGradeSubjectAssignments\Pages\ListTeacherGradeSubjectAssignments;
use App\Filament\Resources\TeacherGradeSubjectAssignments\Pages\ViewTeacherGradeSubjectAssignment;
use App\Models\TeacherGradeSubjectAssignment;
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

class TeacherGradeSubjectAssignmentResource extends Resource
{
    protected static ?string $model = TeacherGradeSubjectAssignment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Tenant Management';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->required(),
                Select::make('tenant_user_id')
                    ->relationship('tenantUser', 'id')
                    ->getOptionLabelFromRecordUsing(fn ($record): string => $record->display_name)
                    ->label('Tenant user')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('tenant_grade_subject_id')
                    ->relationship('tenantGradeSubject', 'id')
                    ->getOptionLabelFromRecordUsing(fn ($record): string => $record->display_name)
                    ->label('Tenant grade subject')
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
                TextEntry::make('tenantUser.display_name')
                    ->label('Tenant user'),
                TextEntry::make('tenantGradeSubject.display_name')
                    ->label('Tenant grade subject'),
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
                TextColumn::make('tenantUser.display_name')
                    ->label('Tenant user')
                    ->searchable(),
                TextColumn::make('tenantGradeSubject.display_name')
                    ->label('Tenant grade subject')
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
            'index' => ListTeacherGradeSubjectAssignments::route('/'),
            'create' => CreateTeacherGradeSubjectAssignment::route('/create'),
            'view' => ViewTeacherGradeSubjectAssignment::route('/{record}'),
            'edit' => EditTeacherGradeSubjectAssignment::route('/{record}/edit'),
        ];
    }
}
