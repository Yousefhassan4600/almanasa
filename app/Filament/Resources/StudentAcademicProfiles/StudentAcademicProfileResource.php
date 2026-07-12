<?php

namespace App\Filament\Resources\StudentAcademicProfiles;

use App\Filament\Resources\StudentAcademicProfiles\Pages\CreateStudentAcademicProfile;
use App\Filament\Resources\StudentAcademicProfiles\Pages\EditStudentAcademicProfile;
use App\Filament\Resources\StudentAcademicProfiles\Pages\ListStudentAcademicProfiles;
use App\Filament\Resources\StudentAcademicProfiles\Pages\ViewStudentAcademicProfile;
use App\Models\StudentAcademicProfile;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class StudentAcademicProfileResource extends Resource
{
    protected static ?string $model = StudentAcademicProfile::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Education Catalog';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->required(),
                Select::make('student_id')
                    ->relationship('student', 'name')
                    ->required(),
                Select::make('academic_year_id')
                    ->relationship('academicYear', 'name'),
                Select::make('grade_id')
                    ->relationship('grade', 'name'),
                TextInput::make('school_name'),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label('Tenant'),
                TextEntry::make('student.name')
                    ->label('Student'),
                TextEntry::make('academicYear.name')
                    ->label('Academic year')
                    ->placeholder('-'),
                TextEntry::make('grade.name')
                    ->label('Grade')
                    ->placeholder('-'),
                TextEntry::make('school_name')
                    ->placeholder('-'),
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
                TextColumn::make('student.name')
                    ->searchable(),
                TextColumn::make('academicYear.name')
                    ->searchable(),
                TextColumn::make('grade.name')
                    ->searchable(),
                TextColumn::make('school_name')
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
            'index' => ListStudentAcademicProfiles::route('/'),
            'create' => CreateStudentAcademicProfile::route('/create'),
            'view' => ViewStudentAcademicProfile::route('/{record}'),
            'edit' => EditStudentAcademicProfile::route('/{record}/edit'),
        ];
    }
}
