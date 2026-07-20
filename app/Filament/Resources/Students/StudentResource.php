<?php

namespace App\Filament\Resources\Students;

use App\Enums\AccountType;
use App\Filament\Base\BaseResource;
use App\Filament\Resources\Students\Pages\CreateStudent;
use App\Filament\Resources\Students\Pages\EditStudent;
use App\Filament\Resources\Students\Pages\ListStudents;
use App\Filament\Resources\Students\RelationManagers\CourseReviewsRelationManager;
use App\Filament\Resources\Students\RelationManagers\LessonProgressesRelationManager;
use App\Filament\Resources\Students\RelationManagers\ParentStudentsRelationManager;
use App\Filament\Resources\Students\RelationManagers\StudentAttemptsRelationManager;
use App\Filament\Resources\Students\RelationManagers\SubscriptionsRelationManager;
use App\Filament\Resources\Students\Schemas\StudentForm;
use App\Filament\Resources\Students\Tables\StudentsTable;
use App\Filament\Support\CurrentAccount;
use App\Models\Account;
use App\Support\AdminPermissions;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class StudentResource extends BaseResource
{
    protected static ?string $model = Account::class;

    protected static ?string $modelLabel = 'Student';

    protected static ?string $pluralModelLabel = 'Students';

    protected static string|UnitEnum|null $navigationGroup = 'Students & Families';

    protected static ?int $navigationSort = 0;

    public static function form(Schema $schema): Schema
    {
        return StudentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StudentsTable::configure($table);
    }

    public static function canViewAny(): bool
    {
        if (CurrentAccount::isAcademyTeacher() && AdminPermissions::hasViewHisOnly(static::class)) {
            return true;
        }

        return parent::canViewAny();
    }

    public static function getEloquentQuery(): Builder
    {
        $query = CurrentAccount::isAcademyTeacher() && AdminPermissions::hasViewHisOnly(static::class)
            ? Account::query()
            : parent::getEloquentQuery();

        $query
            ->where('type', AccountType::Student->value)
            ->with(['owner.studentProfile.grade', 'provider']);

        if (CurrentAccount::isAcademyTeacher() && AdminPermissions::hasViewHisOnly(static::class)) {
            return self::scopeStudentsToCurrentTeacher($query);
        }

        return $query;
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        $query = parent::getRecordRouteBindingEloquentQuery()
            ->where('type', AccountType::Student->value);

        if (CurrentAccount::isAcademyTeacher() && AdminPermissions::hasViewHisOnly(static::class)) {
            return self::scopeStudentsToCurrentTeacher($query);
        }

        return $query;
    }

    public static function getRelations(): array
    {
        return [
            SubscriptionsRelationManager::class,
            ParentStudentsRelationManager::class,
            StudentAttemptsRelationManager::class,
            LessonProgressesRelationManager::class,
            CourseReviewsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStudents::route('/'),
            'create' => CreateStudent::route('/create'),
            'edit' => EditStudent::route('/{record}/edit'),
        ];
    }

    private static function scopeStudentsToCurrentTeacher(Builder $query): Builder
    {
        $account = CurrentAccount::account();

        return $query->where(function (Builder $query) use ($account): void {
            $query
                ->whereHas('studentAttempts.course.academyTeacher', fn (Builder $query): Builder => $query
                    ->where('teacher_account_id', $account?->id))
                ->orWhereHas('lessonProgresses.course.academyTeacher', fn (Builder $query): Builder => $query
                    ->where('teacher_account_id', $account?->id))
                ->orWhereHas('courseReviews.course.academyTeacher', fn (Builder $query): Builder => $query
                    ->where('teacher_account_id', $account?->id))
                ->orWhereHas('subscriptions.course.academyTeacher', fn (Builder $query): Builder => $query
                    ->where('teacher_account_id', $account?->id));
        });
    }
}
