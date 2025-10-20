<?php

namespace App\Filament\Exports;

use App\Models\Student;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class StudentExporter extends Exporter
{
    protected static ?string $model = Student::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('full_name')
                ->label("O'quvchining IFSH"),
            ExportColumn::make('sinf.name')
                ->label('Sinf'),
            ExportColumn::make('maktab.name')
                ->label('Maktab'),
            ExportColumn::make('created_at')
                ->label("Qo'shilgan sana"),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = "O'quvchilar eksporti yakunlandi. " . number_format($export->successful_rows) . ' ta qator muvaffaqiyatli eksport qilindi.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ta qator eksport qilinmadi.';
        }

        return $body;
    }
}
