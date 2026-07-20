<?php

namespace App\Filament\Resources\Courses\RelationManagers;

use App\Enums\LessonTypeEnum;
use App\Filament\Base\RelationManagers\BaseRelationManager;
use App\Filament\Resources\Courses\RelationManagers\Tables\LessonsTable;
use App\Models\Assignment;
use App\Models\CoursePeriod;
use App\Models\Exam;
use App\Models\Lesson;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
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
                Repeater::make('items')
                    ->label(__('admin.labels.Lesson Items'))
                    ->relationship()
                    ->collapsible()
                    ->collapsed(true)
                    ->itemLabel(fn (array $state): ?string => $state['title']['en'] ?? $state['title']['ar'] ?? null)
                    ->schema([
                        Select::make('type')
                            ->label(__('admin.labels.Type'))
                            ->options(fn (): array => $this->lessonItemTypeOptions())
                            ->live()
                            ->required()
                            ->columnSpanFull(),
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
                        FileUpload::make('video_url')
                            ->label(__('admin.labels.Video'))
                            ->acceptedFileTypes([
                                'video/mp4',
                            ])
                            ->disk('public')
                            ->visibility('public')
                            ->directory(fn (): string => 'courses/lesson_'.$this->currentLessonFolderKey().'/videos')
                            ->visible(fn (Get $get): bool => $get('type') === LessonTypeEnum::Video->value)
                            ->required(fn (Get $get): bool => $get('type') === LessonTypeEnum::Video->value)
                            ->columnSpanFull(),
                        FileUpload::make('file_url')
                            ->label(__('admin.labels.File'))
                            ->disk('public')
                            ->visibility('public')
                            ->directory(fn (): string => 'courses/lesson_'.$this->currentLessonFolderKey().'/files')
                            ->visible(fn (Get $get): bool => $get('type') === LessonTypeEnum::File->value)
                            ->required(fn (Get $get): bool => $get('type') === LessonTypeEnum::File->value)
                            ->columnSpanFull(),
                        TextInput::make('link_url')
                            ->label(__('admin.labels.Link Url'))
                            ->visible(fn (Get $get): bool => $get('type') === LessonTypeEnum::Link->value)
                            ->required(fn (Get $get): bool => $get('type') === LessonTypeEnum::Link->value)
                            ->columnSpanFull(),
                        $this->singleAssignmentSelect($this->getOwnerRecord()->getKey()),
                        $this->singleExamSelect($this->getOwnerRecord()->getKey()),
                        TextInput::make('duration_minutes')
                            ->label(__('admin.labels.Duration Minutes'))
                            ->numeric()
                            ->columnSpanFull(),
                        DateTimePicker::make('starts_at')
                            ->label(__('admin.labels.Starts At')),
                        DateTimePicker::make('ends_at')
                            ->label(__('admin.labels.Ends At')),
                        Toggle::make('is_active')
                            ->label(__('admin.labels.Is Active'))
                            ->default(true),
                        Toggle::make('is_free')
                            ->label(__('admin.labels.Is Free'))
                            ->default(false),
                    ])
                    ->columns(2)
                    ->defaultItems(0)
                    ->orderColumn('sort_order')
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return LessonsTable::configure($table)
            ->heading('Lessons')
            ->recordTitleAttribute('title')
            ->headerActions($this->getTableHeaderActions())
            ->filters([])
            ->recordActions($this->getTableActions());
    }

    public function getTableFilters(): array
    {
        return [];
    }

    protected function lessonItemTypeOptions(): array
    {
        return LessonTypeEnum::options();
    }

    protected function currentLessonId(): ?int
    {
        $record = $this->getMountedAction()?->getRecord();

        return $record instanceof Lesson ? $record->getKey() : null;
    }

    protected function currentLessonFolderKey(): string|int
    {
        return $this->currentLessonId() ?? 'new';
    }

    private function singleAssignmentSelect(int|string|null $courseId): Select
    {
        return Select::make('assignment_id')
            ->label(__('admin.labels.Assignment'))
            ->options(fn (): array => Assignment::query()
                ->where('course_id', $courseId)
                ->pluck('title', 'id')
                ->all())
            ->visible(fn (Get $get): bool => $get('type') === LessonTypeEnum::Assignments->value)
            ->required(fn (Get $get): bool => $get('type') === LessonTypeEnum::Assignments->value)
            ->searchable()
            ->preload()
            ->columnSpanFull();
    }

    private function singleExamSelect(int|string|null $courseId): Select
    {
        return Select::make('exam_id')
            ->label(__('admin.labels.Exam'))
            ->options(fn (): array => Exam::query()
                ->where('course_id', $courseId)
                ->pluck('title', 'id')
                ->all())
            ->visible(fn (Get $get): bool => $get('type') === LessonTypeEnum::Exams->value)
            ->required(fn (Get $get): bool => $get('type') === LessonTypeEnum::Exams->value)
            ->searchable()
            ->preload()
            ->columnSpanFull();
    }
}
