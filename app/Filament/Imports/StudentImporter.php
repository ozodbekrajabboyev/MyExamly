<?php

namespace App\Filament\Imports;

use App\Models\Student;
use App\Models\Sinf;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Validation\Rule;

class StudentImporter extends Importer
{
    protected static ?string $model = Student::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('full_name')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:255']),

            ImportColumn::make('sinf')
                ->requiredMapping()
                ->relationship(resolveUsing: 'name')
                ->rules([
                    'required',
                    Rule::exists('sinfs', 'name')
                ])
                ->example('10-A'),
        ];
    }

    public function resolveRecord(): ?Student
    {
        // Check for existing student to prevent duplicates
        $existingStudent = Student::where('full_name', $this->data['full_name'])
            ->whereHas('sinf', function ($query) {
                $query->where('name', $this->data['sinf']);
            })
            ->where('maktab_id', auth()->user()->maktab_id)
            ->first();

        if ($existingStudent) {
            return $existingStudent; // Update existing
        }

        return new Student(); // Create new
    }

    public function beforeSave(): void
    {
        // Ensure maktab_id is set from authenticated user
        $this->record->maktab_id = auth()->user()->maktab_id;

        // Resolve sinf relationship
        $sinf = Sinf::where('name', $this->data['sinf'])
            ->where('maktab_id', auth()->user()->maktab_id)
            ->first();

        if ($sinf) {
            $this->record->sinf_id = $sinf->id;
        }
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $successful = number_format($import->successful_rows);
        $body = "O'quvchilarni import qilish yakunlandi: {$successful} " .
            'qator'. ' muvaffaqiyatli import qilindi.';

        if ($failed = $import->getFailedRowsCount()) {
            $failedCount = number_format($failed);
            $body .= " {$failedCount} " . 'qator' . ' import qilinmadi.';
        }

        return $body;
    }

    public static function getOptionsFormComponents(): array
    {
        return [
            // Add options for batch size, validation mode, etc.
        ];
    }
}
