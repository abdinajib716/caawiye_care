<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\DB;

class SequentialIdService
{
    /**
     * Generate a sequential ID with format: PREFIX-YYYY-NNNN
     * 
     * @param string $prefix The prefix (e.g., 'ORD', 'MED', 'APT', 'TXN')
     * @param string $table The database table to check
     * @param string $column The column name that stores the ID
     * @return string The generated ID
     */
    public function generate(string $prefix, string $table, string $column = 'order_number'): string
    {
        $year = now()->format('Y');
        $pattern = "{$prefix}-{$year}-%";

        // Get the last number for this year
        $lastRecord = DB::table($table)
            ->where($column, 'like', $pattern)
            ->orderBy($column, 'desc')
            ->first();

        if ($lastRecord) {
            // Extract the number from the last ID
            $lastId = $lastRecord->$column;
            $parts = explode('-', $lastId);
            $lastNumber = (int) end($parts);
            $nextNumber = $lastNumber + 1;
        } else {
            // First record of the year
            $nextNumber = 1;
        }

        // Format with 4 digits
        return sprintf('%s-%s-%04d', $prefix, $year, $nextNumber);
    }

    /**
     * Generate order number
     */
    public function generateOrderNumber(): string
    {
        return $this->generate('ORD', 'orders', 'order_number');
    }

    /**
     * Generate medicine order number
     */
    public function generateMedicineOrderNumber(): string
    {
        return $this->generate('MED', 'medicine_orders', 'order_number');
    }

    /**
     * Generate appointment order number (stored in orders table)
     */
    public function generateAppointmentOrderNumber(): string
    {
        // Appointments are also stored in orders table, so we need to check the prefix
        $year = now()->format('Y');
        $pattern = "APT-{$year}-%";

        $lastRecord = DB::table('orders')
            ->where('order_number', 'like', $pattern)
            ->orderBy('order_number', 'desc')
            ->first();

        if ($lastRecord) {
            $lastId = $lastRecord->order_number;
            $parts = explode('-', $lastId);
            $lastNumber = (int) end($parts);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return sprintf('APT-%s-%04d', $year, $nextNumber);
    }

    /**
     * Generate transaction reference ID
     */
    public function generateTransactionReference(): string
    {
        return $this->generate('TXN', 'payment_transactions', 'reference_id');
    }
}
