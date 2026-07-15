<?php

namespace App\Filament\Resources\Lessons\RelationManagers;

use App\Enums\LessonTypeEnum;
use App\Filament\Base\RelationManagers\BaseRelationManager;
use App\Filament\Resources\Assignments\AssignmentResource;
use App\Filament\Resources\Exams\ExamResource;
use App\Filament\Resources\Lessons\RelationManagers\Tables\LessonItemsTable;
use App\Models\Assignment;
use App\Models\Exam;
use App\Models\LessonItem;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LessonItemsRelationManager extends BaseRelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = 'Lesson Items';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
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
                Select::make('type')
                    ->label('Type')
                    ->options(LessonTypeEnum::options())
                    ->live()
                    ->required()
                    ->columnSpanFull(),
                FileUpload::make('video_url')
                    ->label('Video')
                    ->acceptedFileTypes([
                        'video/mp4',
                    ])
                    ->disk('public')
                    ->visibility('public')
                    ->directory(fn(): string => 'courses/lesson_' . $this->getOwnerRecord()->getKey() . '/videos')
                    ->visible(fn(Get $get): bool => $get('type') === LessonTypeEnum::Video->value)
                    ->required(fn(Get $get): bool => $get('type') === LessonTypeEnum::Video->value)
                    ->columnSpanFull(),
                FileUpload::make('file_url')
                    ->label('File')
                    ->directory(fn(): string => 'courses/lesson_' . $this->getOwnerRecord()->getKey() . '/files')
                    ->visible(fn(Get $get): bool => $get('type') === LessonTypeEnum::File->value)
                    ->required(fn(Get $get): bool => $get('type') === LessonTypeEnum::File->value)
                    ->columnSpanFull(),
                TextInput::make('link_url')
                    ->label('Link Url')
                    ->visible(fn(Get $get): bool => $get('type') === LessonTypeEnum::Link->value)
                    ->required(fn(Get $get): bool => $get('type') === LessonTypeEnum::Link->value)
                    ->columnSpanFull(),
                Select::make('assignment_id')
                    ->label('Assignment')
                    ->options(fn(): array => Assignment::query()
                        ->where('lesson_id', $this->getOwnerRecord()->getKey())
                        ->pluck('title', 'id')
                        ->all())
                    ->visible(fn(Get $get): bool => $get('type') === LessonTypeEnum::Assignment->value)
                    ->required(fn(Get $get): bool => $get('type') === LessonTypeEnum::Assignment->value)
                    ->searchable()
                    ->preload()
                    ->columnSpanFull(),
                Select::make('exam_id')
                    ->label('Exam')
                    ->options(fn(): array => Exam::query()
                        ->where('lesson_id', $this->getOwnerRecord()->getKey())
                        ->pluck('title', 'id')
                        ->all())
                    ->visible(fn(Get $get): bool => $get('type') === LessonTypeEnum::Exam->value)
                    ->required(fn(Get $get): bool => $get('type') === LessonTypeEnum::Exam->value)
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
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return LessonItemsTable::configure($table)
            ->heading('Lesson Items')
            ->recordTitleAttribute('title')
            ->headerActions($this->getTableHeaderActions())
            ->filters([])
            ->recordActions($this->getTableActions());
    }

    protected function extraTableActions(): array
    {
        return [
            Action::make('link')
                ->hiddenLabel()
                ->tooltip('Open')
                ->url(fn(LessonItem $record): ?string => $this->resolveItemUrl($record))
                ->visible(fn(LessonItem $record): bool => filled($this->resolveItemUrl($record)))
                ->openUrlInNewTab()
                ->icon('heroicon-o-link'),
        ];
    }

    protected function resolveItemUrl(LessonItem $record): ?string
    {
        $type = $record->type instanceof LessonTypeEnum
            ? $record->type
            : LessonTypeEnum::tryFrom((string) $record->type);

        return match ($type) {
            LessonTypeEnum::Video => $this->resolveFileUrl($record->video_url),
            LessonTypeEnum::File => $this->resolveFileUrl($record->file_url),
            LessonTypeEnum::Link => $this->normalizeUrl($record->link_url),
            // LessonTypeEnum::Assignment => $record->assignment_id
            //     ? AssignmentResource::getUrl('edit', ['record' => $record->assignment_id])
            //     : null,
            // LessonTypeEnum::Exam => $record->exam_id
            //     ? ExamResource::getUrl('edit', ['record' => $record->exam_id])
            //     : null,
            default => null,
        };
    }

    protected function normalizeUrl(?string $url): ?string
    {
        if (blank($url)) {
            return null;
        }

        return Str::startsWith($url, ['http://', 'https://'])
            ? $url
            : url($url);
    }

    protected function resolveFileUrl(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        return Storage::disk('public')->url($path);
    }
}
