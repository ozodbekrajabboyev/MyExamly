<?php

namespace App\Filament\Imports;

use App\Models\Student;
use App\Models\Sinf;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StudentImporter extends Importer
{
    protected static ?string $model = Student::class;

    protected ?int $maktabId = null;

    // Add chunk size for better memory management
    protected int $chunkSize = 100;

    protected function getMaktabId(): int
    {
        if ($this->maktabId === null) {
            $user = auth()->user();
            
            if (!$user || !$user->maktab_id) {
                Log::error('StudentImporter: User not authenticated or missing maktab_id', [
                    'user_id' => $user?->id,
                    'import_id' => $this->import?->id
                ]);
                throw new \Exception('Foydalanuvchi autentifikatsiya qilinmagan yoki maktab ID mavjud emas');
            }
            
            $this->maktabId = $user->maktab_id;
        }
        
        return $this->maktabId;
    }

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('full_name')
                ->label('To\'liq ismi')
                ->requiredMapping()
                ->rules([
                    'required',
                    'string',
                    'max:255',
                    'min:2',
                    'regex:/^[a-zA-ZÀ-ÿ\s\'\-\.]+$/u' // Only letters, spaces, apostrophes, hyphens, dots
                ])
                ->example('Abdullayev Sardor Akmalovich'),

            ImportColumn::make('sinf_id')
                ->label('Sinf ID')
                ->requiredMapping()
                ->rules([
                    'required',
                    'integer',
                    'min:1',
                    'exists:sinfs,id',
                ])
                ->example('1'),
        ];
    }

    public function resolveRecord(): ?Student
    {
        try {
            $maktabId = $this->getMaktabId();

            // Sanitize input data
            $fullName = trim($this->data['full_name']);
            $sinfId = (int) $this->data['sinf_id'];

            // Check for existing student to prevent duplicates
            $existingStudent = Student::where('full_name', $fullName)
                ->where('sinf_id', $sinfId)
                ->where('maktab_id', $maktabId)
                ->first();

            if ($existingStudent) {
                Log::info('StudentImporter: Found existing student', [
                    'student_id' => $existingStudent->id,
                    'full_name' => $fullName,
                    'sinf_id' => $sinfId
                ]);
                return $existingStudent;
            }

            return new Student([
                'maktab_id' => $maktabId,
            ]);

        } catch (\Exception $e) {
            Log::error('StudentImporter: Error in resolveRecord', [
                'error' => $e->getMessage(),
                'data' => $this->data,
                'import_id' => $this->import?->id
            ]);
            throw $e;
        }
    }

    public function beforeSave(): void
    {
        try {
            $maktabId = $this->getMaktabId();

            // Sanitize and validate input
            $fullName = trim($this->data['full_name']);
            $sinfId = (int) $this->data['sinf_id'];

            // Additional validation
            if (empty($fullName)) {
                throw new \Exception('To\'liq ism bo\'sh bo\'lishi mumkin emas');
            }

            if ($sinfId <= 0) {
                throw new \Exception('Sinf ID musbat son bo\'lishi kerak');
            }

            // Verify the sinf belongs to the user's maktab with caching
            $sinf = Sinf::where('id', $sinfId)
                ->where('maktab_id', $maktabId)
                ->first();

            if (!$sinf) {
                Log::warning('StudentImporter: Sinf not found or not belongs to maktab', [
                    'sinf_id' => $sinfId,
                    'maktab_id' => $maktabId,
                    'import_id' => $this->import?->id
                ]);
                throw new \Exception("Sinf (ID: {$sinfId}) sizning maktabingizga tegishli emas!");
            }

            // Set the correct attributes
            $this->record->fill([
                'maktab_id' => $maktabId,
                'sinf_id' => $sinfId,
                'full_name' => $fullName,
            ]);

            Log::info('StudentImporter: Successfully prepared student record', [
                'full_name' => $fullName,
                'sinf_id' => $sinfId,
                'maktab_id' => $maktabId
            ]);

        } catch (\Exception $e) {
            Log::error('StudentImporter: Error in beforeSave', [
                'error' => $e->getMessage(),
                'data' => $this->data,
                'import_id' => $this->import?->id
            ]);
            throw $e;
        }
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $successful = number_format($import->successful_rows);
        $body = "O'quvchilarni import qilish yakunlandi: {$successful} " .
            'ta o\'quvchi' . ' muvaffaqiyatli import qilindi.';

        if ($failed = $import->getFailedRowsCount()) {
            $failedCount = number_format($failed);
            $body .= " {$failedCount} " . 'ta o\'quvchi' . ' import qilinmadi.';
        }

        // Log completion for monitoring
        Log::info('StudentImporter: Import completed', [
            'import_id' => $import->id,
            'successful_rows' => $import->successful_rows,
            'failed_rows' => $failed,
            'total_rows' => $import->total_rows
        ]);

        return $body;
    }

    public static function getOptionsFormComponents(): array
    {
        return [
            // You can add batch size options, validation modes, etc.
        ];
    }

    // Add method to handle cleanup on failure
    public function onFailure(\Throwable $e): void
    {
        Log::error('StudentImporter: Import failed', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'import_id' => $this->import?->id,
            'data' => $this->data ?? 'No data available'
        ]);
        
        parent::onFailure($e);
    }
}
