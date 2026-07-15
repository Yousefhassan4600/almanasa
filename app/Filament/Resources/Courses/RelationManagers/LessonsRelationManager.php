<?php

namespace App\Filament\Resources\Courses\RelationManagers;

use App\Enums\LessonTypeEnum;
use App\Filament\Base\RelationManagers\BaseRelationManager;
use App\Filament\Resources\Courses\RelationManagers\Tables\LessonsTable;
use App\Models\Assignment;
use App\Models\CoursePeriod;
use App\Models\Exam;
use App\Models\Lesson;
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
                Select::make('course_period_id')
                    ->label('Course Period')
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
                TextInput::make('title.ar')
                    ->label('Title (Arabic)')
                    ->required(),
                TextInput::make('title.en')
                    ->label('Title (English)')
                    ->required(),
                Textarea::make('description.ar')
                    ->label('Description (Arabic)'),
                Textarea::make('description.en')
                    ->label('Description (English)'),
                Toggle::make('is_active')
                    ->label('Is Active')
                    ->default(true),
                Repeater::make('items')
                    ->label('Lesson Items')
                    ->relationship()
                    ->collapsible()
                    ->collapsed(true)
                    ->itemLabel(fn (array $state): ?string => $state['title']['en'] ?? $state['title']['ar'] ?? null)
                    ->schema([
                        Select::make('type')
                            ->label('Type')
                            ->options(fn (): array => $this->lessonItemTypeOptions())
                            ->live()
                            ->required()
                            ->columnSpanFull(),
                        TextInput::make('title.ar')
                            ->label('Title (Arabic)')
                            ->required(),
                        TextInput::make('title.en')
                            ->label('Title (English)')
                            ->required(),
                        Textarea::make('description.ar')
                            ->label('Description (Arabic)'),
                        Textarea::make('description.en')
                            ->label('Description (English)'),
                        FileUpload::make('video_url')
                            ->label('Video')
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
                            ->label('File')
                            ->disk('public')
                            ->visibility('public')
                            ->directory(fn (): string => 'courses/lesson_'.$this->currentLessonFolderKey().'/files')
                            ->visible(fn (Get $get): bool => $get('type') === LessonTypeEnum::File->value)
                            ->required(fn (Get $get): bool => $get('type') === LessonTypeEnum::File->value)
                            ->columnSpanFull(),
                        TextInput::make('link_url')
                            ->label('Link Url')
                            ->visible(fn (Get $get): bool => $get('type') === LessonTypeEnum::Link->value)
                            ->required(fn (Get $get): bool => $get('type') === LessonTypeEnum::Link->value)
                            ->columnSpanFull(),
                        Select::make('assignment_id')
                            ->label('Assignment')
                            ->options(fn (): array => Assignment::query()
                                ->where('lesson_id', $this->currentLessonId())
                                ->pluck('title', 'id')
                                ->all())
                            ->visible(fn (Get $get): bool => $get('type') === LessonTypeEnum::Assignment->value)
                            ->required(fn (Get $get): bool => $get('type') === LessonTypeEnum::Assignment->value)
                            ->searchable()
                            ->preload()
                            ->columnSpanFull(),
                        Select::make('exam_id')
                            ->label('Exam')
                            ->options(fn (): array => Exam::query()
                                ->where('lesson_id', $this->currentLessonId())
                                ->pluck('title', 'id')
                                ->all())
                            ->visible(fn (Get $get): bool => $get('type') === LessonTypeEnum::Exam->value)
                            ->required(fn (Get $get): bool => $get('type') === LessonTypeEnum::Exam->value)
                            ->searchable()
                            ->preload()
                            ->columnSpanFull(),
                        TextInput::make('duration_minutes')
                            ->label('Duration Minutes')
                            ->numeric()
                            ->columnSpanFull(),
                        Toggle::make('is_active')
                            ->label('Is Active')
                            ->default(true),
                        Toggle::make('is_free')
                            ->label('Is Free')
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
        if ($this->currentLessonId()) {
            return LessonTypeEnum::options();
        }

        return [
            LessonTypeEnum::Video->value => 'Video',
            LessonTypeEnum::File->value => 'File',
            LessonTypeEnum::Link->value => 'Link',
        ];
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
}
