<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExcelExportService
{
    /**
     * Export data to CSV (Simple Excel compatible format)
     */
    public function exportToCsv(Collection $data, array $headers, string $filename): StreamedResponse
    {
        $callback = function () use ($data, $headers) {
            $file = fopen('php://output', 'w');

            // Add UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Write headers
            fputcsv($file, $headers);

            // Write data rows
            foreach ($data as $row) {
                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '.csv"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    /**
     * Export data to Excel (XLSX format using simple XML)
     */
    public function exportToExcel(Collection $data, array $headers, string $filename): StreamedResponse
    {
        // For now, use CSV as it's Excel-compatible
        // In production, consider installing phpoffice/phpspreadsheet
        return $this->exportToCsv($data, $headers, $filename);
    }

    /**
     * Generate sample template file
     */
    public function generateSampleTemplate(array $headers, array $sampleRow, string $filename): StreamedResponse
    {
        $callback = function () use ($headers, $sampleRow) {
            $file = fopen('php://output', 'w');

            // Add UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Write headers
            fputcsv($file, $headers);

            // Write sample data row
            fputcsv($file, $sampleRow);

            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '_sample_template.csv"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }
}
