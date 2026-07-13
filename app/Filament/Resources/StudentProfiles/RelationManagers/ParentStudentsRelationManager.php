<?php

namespace App\Filament\Resources\StudentProfiles\RelationManagers;

use App\Enums\AccountType;
use App\Enums\RelationEnum;
use App\Filament\Base\RelationManagers\BaseRelationManager;
use App\Models\Account;
use App\Models\ParentStudent;
use App\Models\User;
use Closure;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ParentStudentsRelationManager extends BaseRelationManager
{
    protected static string $relationship = 'parentStudents';

    public function getTableHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->color('primary')
                ->after(fn(ParentStudent $record): Account => $this->createParentAccount($record)),
        ];
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('parent_user_id')
                    ->label('Parent')
                    ->relationship('parent', 'phone')
                    ->getOptionLabelFromRecordUsing(fn(User $record): string => trim("{$record->name} {$record->phone}"))
                    ->rules([
                        fn(?ParentStudent $record): Closure => function (string $attribute, mixed $value, Closure $fail) use ($record): void {
                            $studentUserId = $this->getOwnerRecord()->user_id;

                            if (blank($value) || blank($studentUserId)) {
                                return;
                            }

                            if ((int) $value === (int) $studentUserId) {
                                $fail('The parent user cannot be the same as the student user.');

                                return;
                            }

                            $parentStudentExists = ParentStudent::query()
                                ->where('parent_user_id', $value)
                                ->where('student_user_id', $studentUserId)
                                ->when($record?->exists, fn($query) => $query->whereKeyNot($record->getKey()))
                                ->exists();

                            if ($parentStudentExists) {
                                $fail('This parent is already linked to this student.');
                            }
                        },
                    ])
                    ->preload()
                    ->searchable()
                    ->required(),
                Select::make('relation')
                    ->label('Relation')
                    ->options(RelationEnum::options())
                    ->required(),
                TextInput::make('occupation')
                    ->label('Occupation'),
                Toggle::make('is_primary')
                    ->label('Primary')
                    ->default(false),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('parent_user_id')
            ->columns([
                TextColumn::make('parent.name')
                    ->label('Parent')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('parent.phone')
                    ->label('Phone')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('relation')
                    ->label('Relation')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('occupation')
                    ->label('Occupation')
                    ->searchable()
                    ->sortable(),
                IconColumn::make('is_primary')
                    ->label('Primary')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([])
            ->headerActions($this->getTableHeaderActions())
            ->recordActions($this->getTableActions())
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public function getTableFilters(): array
    {
        return [];
    }

    private function createParentAccount(ParentStudent $parentStudent): Account
    {
        return Account::query()->firstOrCreate([
            'provider_id' => $this->parentAccountProviderId(),
            'type' => AccountType::Parent->value,
            'owner_user_id' => $parentStudent->parent_user_id,
        ], [
            'is_active' => true,
            'approved_at' => now(),
        ]);
    }

    private function parentAccountProviderId(): ?int
    {
        $currentAccount = request()->attributes->get('current_account');

        if ($currentAccount instanceof Account && $currentAccount->provider_id) {
            return $currentAccount->provider_id;
        }

        $currentProviderId = (int) request()->session()->get('current_provider_id');

        if ($currentProviderId > 0) {
            return $currentProviderId;
        }

        return Account::query()
            ->where('owner_user_id', $this->getOwnerRecord()->user_id)
            ->where('type', AccountType::Student->value)
            ->value('provider_id');
    }
}
