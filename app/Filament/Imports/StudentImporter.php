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

    protected ?int $maktabId = null;

    protected function getMaktabId(): int
    {
        if ($this->maktabId === null) {
            $this->maktabId = auth()->user()->maktab_id ??
                throw new \Exception('User not authenticated');
        }
        return $this->maktabId;
    }

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('full_name')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:255']),

            ImportColumn::make('sinf_id')
                ->label('Sinf ID')
                ->requiredMapping()
                ->rules([
                    'required',
                    'integer',
                    'exists:sinfs,id',
                ])
                ->example('1'),
        ];
    }

    public function resolveRecord(): ?Student
    {
        $maktabId = $this->getMaktabId();

        // Check for existing student to prevent duplicates
        $existingStudent = Student::where('full_name', $this->data['full_name'])
            ->where('sinf_id', $this->data['sinf_id'])
            ->where('maktab_id', $maktabId)
            ->first();

        if ($existingStudent) {
            return $existingStudent;
        }

        return new Student([
            'maktab_id' => $maktabId,
        ]);
    }

    public function beforeSave(): void
    {
        $maktabId = $this->getMaktabId();

        // Verify the sinf belongs to the user's maktab
        $sinf = Sinf::where('id', $this->data['sinf_id'])
            ->where('maktab_id', $maktabId)
            ->first();

        if (!$sinf) {
            throw new \Exception("Sinf sizning maktabingizga tegishli emas!");
        }

        // Set the correct attributes
        $this->record->fill([
            'maktab_id' => $maktabId,
            'sinf_id' => (int) $this->data['sinf_id'],
            'full_name' => $this->data['full_name'],
        ]);
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
