@props([
    'enablePdf' => true,
    'enablePrint' => true,
    'enableExport' => true,
    'enableImport' => true,
    'pdfRoute' => null,
    'printRoute' => null,
    'exportRoute' => null,
    'importRoute' => null,
])

<div class="flex items-center gap-2">
    {{-- Download PDF Button --}}
    @if($enablePdf)
        <button 
            type="button"
            @if($pdfRoute)
                onclick="window.location.href='{{ $pdfRoute }}'"
            @else
                onclick="downloadPDF()"
            @endif
            class="inline-flex items-center justify-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 transition-colors duration-200"
            title="{{ __('Download PDF') }}"
        >
            <iconify-icon icon="lucide:download" class="w-4 h-4"></iconify-icon>
            <span class="hidden sm:inline">{{ __('PDF') }}</span>
        </button>
    @endif

    {{-- Print Button --}}
    @if($enablePrint)
        <button 
            type="button"
            onclick="printTable()"
            class="inline-flex items-center justify-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 transition-colors duration-200"
            title="{{ __('Print') }}"
        >
            <iconify-icon icon="lucide:printer" class="w-4 h-4"></iconify-icon>
            <span class="hidden sm:inline">{{ __('Print') }}</span>
        </button>
    @endif

    {{-- Export Button --}}
    @if($enableExport)
        <button 
            type="button"
            @if($exportRoute)
                onclick="window.location.href='{{ $exportRoute }}'"
            @else
                onclick="alert('{{ __('Export route not configured') }}')"
            @endif
            class="inline-flex items-center justify-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 transition-colors duration-200"
            title="{{ __('Export to Excel/CSV') }}"
        >
            <iconify-icon icon="lucide:file-down" class="w-4 h-4"></iconify-icon>
            <span class="hidden sm:inline">{{ __('Export') }}</span>
        </button>
    @endif

    {{-- Import Button --}}
    @if($enableImport)
        <button 
            type="button"
            onclick="openImportModal()"
            class="inline-flex items-center justify-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 transition-colors duration-200"
            title="{{ __('Import from Excel/CSV') }}"
        >
            <iconify-icon icon="lucide:file-up" class="w-4 h-4"></iconify-icon>
            <span class="hidden sm:inline">{{ __('Import') }}</span>
        </button>
    @endif
</div>

@push('scripts')
<script>
function printTable() {
    window.print();
}

function downloadPDF() {
    // This will be overridden by specific component implementations
    if (typeof window.generatePDF === 'function') {
        window.generatePDF();
    } else {
        alert('{{ __("PDF generation not configured for this page") }}');
    }
}
</script>
@endpush
