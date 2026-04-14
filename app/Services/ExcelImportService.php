<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ExcelImportService
{
    protected array $errors = [];
    protected int $successCount = 0;
    protected int $errorCount = 0;

    /**
     * Import data from CSV file
     */
    public function importFromCsv(UploadedFile $file, array $rules, callable $callback): array
    {
        $this->resetCounters();

        // Open and read CSV file
        $handle = fopen($file->getRealPath(), 'r');
        
        if (!$handle) {
            throw new \Exception('Unable to read file');
        }

        // Read header row
        $headers = fgetcsv($handle);
        
        if (!$headers) {
            fclose($handle);
            throw new \Exception('Invalid CSV file - no headers found');
        }

        $rowNumber = 1; // Start from 1 (after header)

        // Read data rows
        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;

            // Skip empty rows
            if (empty(array_filter($row))) {
                continue;
            }

            // Combine headers with row data
            $data = array_combine($headers, $row);

            // Validate row
            $validator = Validator::make($data, $rules);

            if ($validator->fails()) {
                $this->errorCount++;
                $this->errors[] = [
                    'row' => $rowNumber,
                    'data' => $data,
                    'errors' => $validator->errors()->all(),
                ];
                continue;
            }

            // Process valid row
            try {
                $callback($data, $rowNumber);
                $this->successCount++;
            } catch (\Exception $e) {
                $this->errorCount++;
                $this->errors[] = [
                    'row' => $rowNumber,
                    'data' => $data,
                    'errors' => [$e->getMessage()],
                ];
            }
        }

        fclose($handle);

        return [
            'success' => $this->successCount,
            'errors' => $this->errorCount,
            'error_details' => $this->errors,
            'total' => $rowNumber - 1, // Minus header row
        ];
    }

    /**
     * Validate CSV file structure
     */
    public function validateCsvStructure(UploadedFile $file, array $requiredHeaders): bool
    {
        $handle = fopen($file->getRealPath(), 'r');
        
        if (!$handle) {
            return false;
        }

        $headers = fgetcsv($handle);
        fclose($handle);

        if (!$headers) {
            return false;
        }

        // Check if all required headers are present
        $missingHeaders = array_diff($requiredHeaders, $headers);

        return empty($missingHeaders);
    }

    /**
     * Get missing headers from CSV file
     */
    public function getMissingHeaders(UploadedFile $file, array $requiredHeaders): array
    {
        $handle = fopen($file->getRealPath(), 'r');
        
        if (!$handle) {
            return $requiredHeaders;
        }

        $headers = fgetcsv($handle);
        fclose($handle);

        if (!$headers) {
            return $requiredHeaders;
        }

        return array_diff($requiredHeaders, $headers);
    }

    /**
     * Reset counters
     */
    protected function resetCounters(): void
    {
        $this->errors = [];
        $this->successCount = 0;
        $this->errorCount = 0;
    }

    /**
     * Get errors
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Get success count
     */
    public function getSuccessCount(): int
    {
        return $this->successCount;
    }

    /**
     * Get error count
     */
    public function getErrorCount(): int
    {
        return $this->errorCount;
    }
}
