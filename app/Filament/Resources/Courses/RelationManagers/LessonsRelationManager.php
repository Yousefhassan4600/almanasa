<?php

namespace App\Filament\Resources\Courses\RelationManagers;

use App\Filament\Base\RelationManagers\BaseRelationManager;
use App\Filament\Resources\Courses\RelationManagers\Tables\LessonsTable;
use App\Filament\Resources\Lessons\LessonResource;
use App\Models\CoursePeriod;
use App\Models\Lesson;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class LessonsRelationManager extends BaseRelationManager
{
    protected static string $relationship = 'lessons';

    protected static ?string $title = 'Lessons';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title.ar')
                    ->label(__('admin.labels.Title (Arabic)'))
                    ->required(),
                TextInput::make('title.en')
                    ->label(__('admin.labels.Title (English)'))
                    ->required(),
                Textarea::make('description.ar')
                    ->label(__('admin.labels.Description (Arabic)')),
                Textarea::make('description.en')
                    ->label(__('admin.labels.Description (English)')),
                Select::make('course_period_id')
                    ->label(__('admin.labels.Course Period'))
                    ->options(fn (): array => CoursePeriod::query()
                        ->where('is_active', true)
                        ->orderBy('sort_order')
                        ->get()
                        ->mapWithKeys(fn (CoursePeriod $coursePeriod): array => [
                            $coursePeriod->id => $coursePeriod->name,
                        ])
                        ->all())
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('num_of_video_views')
                    ->label(__('admin.labels.Number Of Video Views'))
                    ->numeric()
                    ->integer()
                    ->default(1)
                    ->minValue(0),
                DateTimePicker::make('starts_at')
                    ->label(__('admin.labels.Starts At')),
                DateTimePicker::make('ends_at')
                    ->label(__('admin.labels.Ends At')),
                Toggle::make('is_active')
                    ->label(__('admin.labels.Is Active'))
                    ->default(true),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return LessonsTable::configure($table)
            ->heading('Lessons')
            ->recordTitle(fn (Lesson $record): string => $record->name)
            ->headerActions($this->getTableHeaderActions())
            ->filters([])
            ->recordActions($this->getTableActions());
    }

    public function getTableFilters(): array
    {
        return [];
    }

    protected function extraTableActions(): array
    {
        return [
            Action::make('open_lesson')
                ->hiddenLabel()
                ->tooltip(__('admin.labels.Open Lesson'))
                ->icon(Heroicon::ArrowTopRightOnSquare)
                ->color('gray')
                ->openUrlInNewTab()
                ->url(fn (Lesson $record): string => LessonResource::getUrl('edit', [
                    'record' => $record,
                ])),
        ];
    }
}
