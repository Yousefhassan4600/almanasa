<?php

namespace App\Filament\Resources\Enrollments;

use App\Enums\AccessType;
use App\Enums\EnrollmentStatus;
use App\Filament\Resources\Enrollments\Pages\CreateEnrollment;
use App\Filament\Resources\Enrollments\Pages\EditEnrollment;
use App\Filament\Resources\Enrollments\Pages\ListEnrollments;
use App\Filament\Resources\Enrollments\Pages\ViewEnrollment;
use App\Models\Enrollment;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class EnrollmentResource extends Resource
{
    protected static ?string $model = Enrollment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Commerce';

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
                Select::make('course_id')
                    ->relationship('course', 'title')
                    ->required(),
                Select::make('subscription_id')
                    ->relationship('subscription', 'id')
                    ->getOptionLabelFromRecordUsing(fn ($record): string => $record->display_name)
                    ->label('Subscription')
                    ->searchable()
                    ->preload(),
                Select::make('order_item_id')
                    ->relationship('orderItem', 'title'),
                DateTimePicker::make('starts_at'),
                DateTimePicker::make('expires_at'),
                Select::make('status')
                    ->options(EnrollmentStatus::options())
                    ->required()
                    ->default(EnrollmentStatus::Pending->value),
                Select::make('access_type')
                    ->options(AccessType::options())
                    ->required()
                    ->default(AccessType::Paid->value),
                Select::make('granted_by')
                    ->relationship('grantedBy', 'name')
                    ->label('Granted by')
                    ->searchable()
                    ->preload(),
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
                TextEntry::make('course.title')
                    ->label('Course'),
                TextEntry::make('subscription.display_name')
                    ->label('Subscription')
                    ->placeholder('-'),
                TextEntry::make('orderItem.title')
                    ->label('Order item')
                    ->placeholder('-'),
                TextEntry::make('starts_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('expires_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('status'),
                TextEntry::make('access_type'),
                TextEntry::make('grantedBy.name')
                    ->label('Granted by')
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
                TextColumn::make('course.title')
                    ->searchable(),
                TextColumn::make('subscription.display_name')
                    ->label('Subscription')
                    ->searchable(),
                TextColumn::make('orderItem.title')
                    ->searchable(),
                TextColumn::make('starts_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('expires_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('status')
                    ->searchable(),
                TextColumn::make('access_type')
                    ->searchable(),
                TextColumn::make('grantedBy.name')
                    ->label('Granted by')
                    ->sortable(),
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
            'index' => ListEnrollments::route('/'),
            'create' => CreateEnrollment::route('/create'),
            'view' => ViewEnrollment::route('/{record}'),
            'edit' => EditEnrollment::route('/{record}/edit'),
        ];
    }
}
