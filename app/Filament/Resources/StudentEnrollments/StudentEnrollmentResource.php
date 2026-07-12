<?php

namespace App\Filament\Resources\StudentEnrollments;

use App\Enums\EnrollmentStatus;
use App\Filament\Resources\StudentEnrollments\Pages\ManageStudentEnrollments;
use App\Models\StudentEnrollment;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class StudentEnrollmentResource extends Resource
{
    protected static ?string $model = StudentEnrollment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Students & Families';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('student_user_id')
                    ->label('Student User Id')
                    ->numeric()
                    ->required(),
                TextInput::make('account_id')
                    ->label('Account Id')
                    ->numeric()
                    ->required(),
                TextInput::make('course_id')
                    ->label('Course Id')
                    ->numeric(),
                TextInput::make('package_id')
                    ->label('Package Id')
                    ->numeric(),
                TextInput::make('subscription_id')
                    ->label('Subscription Id')
                    ->numeric(),
                Select::make('status')
                    ->label('Status')
                    ->options(EnrollmentStatus::options())
                    ->required(),
                DateTimePicker::make('started_at')
                    ->label('Started At'),
                DateTimePicker::make('expires_at')
                    ->label('Expires At'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student_user_id')
                    ->label('Student User Id')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('account_id')
                    ->label('Account Id')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('course_id')
                    ->label('Course Id')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('package_id')
                    ->label('Package Id')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('subscription_id')
                    ->label('Subscription Id')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('started_at')
                    ->label('Started At')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('expires_at')
                    ->label('Expires At')
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
            'index' => ManageStudentEnrollments::route('/'),
        ];
    }
}
