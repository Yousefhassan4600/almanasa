<?php

namespace App\Filament\Actions;

use App\Actions\Questions\ImportQuestionsFromSpreadsheet;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class ImportQuestionsFromExcelAction
{
    public static function make(?int $lessonId = null, ?int $courseId = null): Action
    {
        return Action::make('import_questions_from_excel')
            ->color('success')
            ->label('Import Questions From Excel')
            ->modalHeading('Import Questions From Excel')
            ->schema([
                FileUpload::make('file')
                    ->label('Excel / CSV File')
                    ->disk('local')
                    ->directory('imports/questions')
                    ->acceptedFileTypes([
                        'text/csv',
                        'text/plain',
                        'application/csv',
                        'application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'application/vnd.oasis.opendocument.spreadsheet',
                    ])
                    ->required(),
            ])
            ->action(function (array $data) use ($lessonId, $courseId): void {
                $file = is_array($data['file']) ? reset($data['file']) : $data['file'];
                $path = Storage::disk('local')->path($file);

                $result = app(ImportQuestionsFromSpreadsheet::class)->handle(
                    path: $path,
                    lessonId: $lessonId,
                    courseId: $courseId,
                );

                Notification::make()
                    ->title('Questions imported')
                    ->body("Imported {$result['imported']} questions. Skipped {$result['skipped']} empty rows.")
                    ->success()
                    ->send();
            });
    }
}
