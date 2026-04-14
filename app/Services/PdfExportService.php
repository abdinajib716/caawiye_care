<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\View;

class PdfExportService
{
    /**
     * Generate PDF for appointments
     */
    public function generateAppointmentsPdf(Collection $appointments): string
    {
        $html = View::make('pdf.appointments', [
            'appointments' => $appointments,
            'generatedAt' => now()->format('Y-m-d H:i:s'),
            'title' => __('Appointments Report'),
        ])->render();

        return $this->generatePdfFromHtml($html, 'appointments-' . now()->format('Y-m-d'));
    }

    /**
     * Generate PDF for single appointment booking
     */
    public function generateAppointmentBookingPdf($appointment): string
    {
        $html = View::make('pdf.appointment-booking', [
            'appointment' => $appointment,
            'title' => __('Appointment Booking Confirmation'),
        ])->render();

        return $this->generatePdfFromHtml($html, 'appointment-booking-' . $appointment->id);
    }

    /**
     * Generate PDF for lab test bookings
     */
    public function generateLabTestBookingsPdf(Collection $bookings): string
    {
        $html = View::make('pdf.lab-test-bookings', [
            'bookings' => $bookings,
            'generatedAt' => now()->format('Y-m-d H:i:s'),
            'title' => __('Lab Test Bookings Report'),
        ])->render();

        return $this->generatePdfFromHtml($html, 'lab-test-bookings-' . now()->format('Y-m-d'));
    }

    /**
     * Generate PDF for scan/imaging bookings
     */
    public function generateScanImagingBookingsPdf(Collection $bookings): string
    {
        $html = View::make('pdf.scan-imaging-bookings', [
            'bookings' => $bookings,
            'generatedAt' => now()->format('Y-m-d H:i:s'),
            'title' => __('Scan/Imaging Bookings Report'),
        ])->render();

        return $this->generatePdfFromHtml($html, 'scan-imaging-bookings-' . now()->format('Y-m-d'));
    }

    /**
     * Generate PDF for medicine orders
     */
    public function generateMedicineOrdersPdf(Collection $orders): string
    {
        $html = View::make('pdf.medicine-orders', [
            'orders' => $orders,
            'generatedAt' => now()->format('Y-m-d H:i:s'),
            'title' => __('Medicine Orders Report'),
        ])->render();

        return $this->generatePdfFromHtml($html, 'medicine-orders-' . now()->format('Y-m-d'));
    }

    /**
     * Generate PDF for customers
     */
    public function generateCustomersPdf(Collection $customers): string
    {
        $html = View::make('pdf.customers', [
            'customers' => $customers,
            'generatedAt' => now()->format('Y-m-d H:i:s'),
            'title' => __('Customers Report'),
        ])->render();

        return $this->generatePdfFromHtml($html, 'customers-' . now()->format('Y-m-d'));
    }

    /**
     * Generate PDF from HTML content
     */
    protected function generatePdfFromHtml(string $html, string $filename): string
    {
        $tempDirectory = $this->getWritableTempDirectory();
        $pdfPath = $tempDirectory . DIRECTORY_SEPARATOR . $filename . '.pdf';
        $tempHtmlPath = $tempDirectory . DIRECTORY_SEPARATOR . $filename . '.html';

        try {
            // Try to use Browsershot if available
            if (class_exists(\Spatie\Browsershot\Browsershot::class)) {
                try {
                    \Spatie\Browsershot\Browsershot::html($html)
                        ->showBackground()
                        ->waitUntilNetworkIdle()
                        ->margins(10, 10, 10, 10)
                        ->format('A4')
                        ->save($pdfPath);

                    return $pdfPath;
                } catch (\Exception $e) {
                    \Log::warning('Browsershot PDF generation failed, falling back to wkhtmltopdf/html export', [
                        'error' => $e->getMessage(),
                        'filename' => $filename,
                    ]);
                }
            }

            // Fallback to wkhtmltopdf if available
            if ($this->isWkhtmltopdfAvailable()) {
                file_put_contents($tempHtmlPath, $html);
                
                exec(
                    'wkhtmltopdf --page-size A4 --orientation Portrait --enable-local-file-access ' .
                    escapeshellarg($tempHtmlPath) . ' ' . escapeshellarg($pdfPath) . ' 2>&1',
                    $output,
                    $returnVar
                );
                
                if ($returnVar === 0 && file_exists($pdfPath)) {
                    @unlink($tempHtmlPath);
                    return $pdfPath;
                }
            }

            // Last resort: Save as HTML
            file_put_contents($tempHtmlPath, $html);
            return $tempHtmlPath;
            
        } catch (\Exception $e) {
            \Log::error('PDF generation failed', [
                'error' => $e->getMessage(),
                'filename' => $filename,
                'trace' => $e->getTraceAsString(),
            ]);
            
            // Fallback: Save as HTML
            file_put_contents($tempHtmlPath, $html);
            return $tempHtmlPath;
        }
    }

    protected function getWritableTempDirectory(): string
    {
        $preferredDirectory = storage_path('app/temp');

        if ($this->ensureDirectoryIsWritable($preferredDirectory)) {
            return $preferredDirectory;
        }

        $fallbackDirectory = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'caawiyecare-pdf-temp';
        $this->ensureDirectoryIsWritable($fallbackDirectory);

        return $fallbackDirectory;
    }

    protected function ensureDirectoryIsWritable(string $directory): bool
    {
        if (! is_dir($directory)) {
            @mkdir($directory, 0755, true);
        }

        return is_dir($directory) && is_writable($directory);
    }

    /**
     * Check if wkhtmltopdf is installed
     */
    protected function isWkhtmltopdfAvailable(): bool
    {
        try {
            exec('which wkhtmltopdf 2>&1', $output, $returnVar);
            return $returnVar === 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Download PDF file
     */
    public function downloadPdf(string $path, string $filename): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $downloadName = $extension === 'pdf'
            ? $filename
            : preg_replace('/\.pdf$/i', '.html', $filename);

        $headers = [
            'Content-Type' => $extension === 'pdf' ? 'application/pdf' : 'text/html; charset=UTF-8',
        ];

        return response()->download($path, $downloadName, $headers)->deleteFileAfterSend(true);
    }

    /**
     * Generate PDF from a Blade view
     */
    public function generateFromView(string $view, array $data = []): self
    {
        $this->html = View::make($view, $data)->render();
        $this->filename = $data['filename'] ?? 'report-' . now()->format('Y-m-d');
        return $this;
    }

    /**
     * Download the generated PDF
     */
    public function download(string $filename): \Symfony\Component\HttpFoundation\Response
    {
        $pdfPath = $this->generatePdfFromHtml($this->html ?? '', pathinfo($filename, PATHINFO_FILENAME));
        
        $extension = pathinfo($pdfPath, PATHINFO_EXTENSION);
        $mimeType = $extension === 'pdf' ? 'application/pdf' : 'text/html';
        
        return response()->download($pdfPath, $filename, [
            'Content-Type' => $mimeType,
        ])->deleteFileAfterSend(true);
    }

    protected ?string $html = null;
    protected ?string $filename = null;
}
