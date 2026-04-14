@props([
    'title' => 'Import Data',
    'instructions' => [],
    'sampleTemplateUrl' => '#',
    'importUrl' => '#',
    'requiredFields' => [],
])

<div x-data="importModal()" x-show="open" x-cloak
    @open-import-modal.window="open = true"
    @close-import-modal.window="open = false; reset()"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-50 overflow-y-auto"
    aria-labelledby="import-modal-title"
    role="dialog"
    aria-modal="true"
    style="display: none;">
    
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-75 transition-opacity"></div>

    <!-- Modal Container -->
    <div class="flex min-h-screen items-center justify-center p-4">
        <div @click.away="open && !uploading ? open = false : null"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="relative w-full max-w-2xl bg-white dark:bg-gray-800 rounded-lg shadow-xl">
            
            <!-- Header -->
            <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 px-6 py-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white" id="import-modal-title">
                    <iconify-icon icon="lucide:file-up" class="inline-block w-5 h-5 mr-2"></iconify-icon>
                    {{ $title }}
                </h3>
                <button @click="open = false" type="button" 
                    class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300"
                    :disabled="uploading">
                    <iconify-icon icon="lucide:x" class="w-5 h-5"></iconify-icon>
                </button>
            </div>

            <!-- Body -->
            <div class="px-6 py-4 max-h-[70vh] overflow-y-auto">
                
                <!-- Instructions -->
                @if(count($instructions) > 0)
                <div class="mb-6">
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">
                        {{ __('Instructions') }}
                    </h4>
                    <ol class="list-decimal list-inside space-y-1 text-sm text-gray-600 dark:text-gray-400">
                        @foreach($instructions as $instruction)
                            <li>{{ $instruction }}</li>
                        @endforeach
                    </ol>
                </div>
                @endif

                <!-- Sample Template Download -->
                <div class="mb-6">
                    <a href="{{ $sampleTemplateUrl }}" 
                        class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors duration-200">
                        <iconify-icon icon="lucide:download" class="w-4 h-4"></iconify-icon>
                        {{ __('Download Sample Template') }}
                    </a>
                </div>

                <!-- File Upload Area -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('Upload CSV File') }}
                    </label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-md hover:border-gray-400 dark:hover:border-gray-500 transition-colors duration-200"
                        @dragover.prevent="dragging = true"
                        @dragleave.prevent="dragging = false"
                        @drop.prevent="handleDrop($event)"
                        :class="{'border-primary bg-primary-50 dark:bg-primary-900/20': dragging}">
                        <div class="space-y-1 text-center">
                            <iconify-icon icon="lucide:upload-cloud" class="mx-auto h-12 w-12 text-gray-400"></iconify-icon>
                            <div class="flex text-sm text-gray-600 dark:text-gray-400">
                                <label for="file-upload" class="relative cursor-pointer rounded-md font-medium text-primary hover:text-primary-dark">
                                    <span>{{ __('Upload a file') }}</span>
                                    <input id="file-upload" name="file-upload" type="file" class="sr-only" 
                                        accept=".csv" @change="handleFileSelect($event)">
                                </label>
                                <p class="pl-1">{{ __('or drag and drop') }}</p>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ __('CSV files only') }}
                            </p>
                        </div>
                    </div>
                    
                    <!-- Selected File Info -->
                    <div x-show="selectedFile" class="mt-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-md">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <iconify-icon icon="lucide:file-text" class="w-5 h-5 text-gray-500"></iconify-icon>
                                <span class="text-sm text-gray-700 dark:text-gray-300" x-text="selectedFile?.name"></span>
                            </div>
                            <button @click="selectedFile = null; $refs.fileInput.value = ''" type="button" 
                                class="text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                                <iconify-icon icon="lucide:x" class="w-4 h-4"></iconify-icon>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Required Fields Info -->
                @if(count($requiredFields) > 0)
                <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-md">
                    <h4 class="text-sm font-semibold text-blue-900 dark:text-blue-300 mb-2">
                        <iconify-icon icon="lucide:info" class="inline-block w-4 h-4 mr-1"></iconify-icon>
                        {{ __('Required Fields') }}
                    </h4>
                    <ul class="list-disc list-inside text-sm text-blue-800 dark:text-blue-400 space-y-1">
                        @foreach($requiredFields as $field)
                            <li>{{ $field }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <!-- Progress Bar -->
                <div x-show="uploading" class="mb-4">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Uploading...') }}</span>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300" x-text="progress + '%'"></span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                        <div class="bg-primary h-2.5 rounded-full transition-all duration-300" 
                            :style="'width: ' + progress + '%'"></div>
                    </div>
                </div>

                <!-- Import Results -->
                <div x-show="importResults" class="space-y-3">
                    <div x-show="importResults?.success > 0" 
                        class="p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-md">
                        <div class="flex items-center">
                            <iconify-icon icon="lucide:check-circle" class="w-5 h-5 text-green-600 dark:text-green-400 mr-2"></iconify-icon>
                            <span class="text-sm text-green-800 dark:text-green-300">
                                <strong x-text="importResults?.success"></strong> {{ __('records imported successfully') }}
                            </span>
                        </div>
                    </div>

                    <div x-show="importResults?.errors > 0" 
                        class="p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-md">
                        <div class="flex items-center mb-2">
                            <iconify-icon icon="lucide:alert-circle" class="w-5 h-5 text-red-600 dark:text-red-400 mr-2"></iconify-icon>
                            <span class="text-sm text-red-800 dark:text-red-300">
                                <strong x-text="importResults?.errors"></strong> {{ __('records failed') }}
                            </span>
                        </div>
                        <div x-show="importResults?.error_details?.length > 0" class="mt-2 max-h-32 overflow-y-auto">
                            <ul class="text-xs text-red-700 dark:text-red-400 space-y-1">
                                <template x-for="(error, index) in importResults.error_details?.slice(0, 5)" :key="index">
                                    <li>
                                        <strong>Row <span x-text="error.row"></span>:</strong>
                                        <span x-text="error.errors.join(', ')"></span>
                                    </li>
                                </template>
                            </ul>
                            <p x-show="importResults?.error_details?.length > 5" class="text-xs text-red-600 dark:text-red-400 mt-1">
                                {{ __('and') }} <span x-text="importResults.error_details.length - 5"></span> {{ __('more errors...') }}
                            </p>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Footer -->
            <div class="flex items-center justify-end gap-3 border-t border-gray-200 dark:border-gray-700 px-6 py-4">
                <button @click="open = false" type="button" 
                    :disabled="uploading"
                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200">
                    {{ __('Cancel') }}
                </button>
                <button @click="handleImport()" type="button"
                    :disabled="!selectedFile || uploading"
                    class="px-4 py-2 text-sm font-medium text-white bg-primary rounded-md hover:bg-primary-dark disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200">
                    <span x-show="!uploading">{{ __('Import Data') }}</span>
                    <span x-show="uploading">{{ __('Importing...') }}</span>
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function importModal() {
    return {
        open: false,
        selectedFile: null,
        dragging: false,
        uploading: false,
        progress: 0,
        importResults: null,

        handleFileSelect(event) {
            const file = event.target.files[0];
            if (file && file.type === 'text/csv') {
                this.selectedFile = file;
                this.importResults = null;
            } else {
                alert('{{ __("Please select a valid CSV file") }}');
            }
        },

        handleDrop(event) {
            this.dragging = false;
            const file = event.dataTransfer.files[0];
            if (file && file.type === 'text/csv') {
                this.selectedFile = file;
                this.importResults = null;
            } else {
                alert('{{ __("Please select a valid CSV file") }}');
            }
        },

        async handleImport() {
            if (!this.selectedFile) return;

            this.uploading = true;
            this.progress = 0;
            this.importResults = null;

            const formData = new FormData();
            formData.append('file', this.selectedFile);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

            try {
                const xhr = new XMLHttpRequest();

                // Track upload progress
                xhr.upload.addEventListener('progress', (e) => {
                    if (e.lengthComputable) {
                        this.progress = Math.round((e.loaded / e.total) * 100);
                    }
                });

                xhr.addEventListener('load', () => {
                    if (xhr.status === 200) {
                        const response = JSON.parse(xhr.responseText);
                        this.importResults = response;
                        this.uploading = false;
                        this.selectedFile = null;

                        if (response.success > 0 && response.errors === 0) {
                            setTimeout(() => {
                                window.location.reload();
                            }, 2000);
                        }
                    } else {
                        this.uploading = false;
                        alert('{{ __("Import failed. Please try again.") }}');
                    }
                });

                xhr.addEventListener('error', () => {
                    this.uploading = false;
                    alert('{{ __("Network error. Please try again.") }}');
                });

                xhr.open('POST', '{{ $importUrl }}');
                xhr.send(formData);

            } catch (error) {
                this.uploading = false;
                alert('{{ __("Import failed. Please try again.") }}');
            }
        },

        reset() {
            this.selectedFile = null;
            this.uploading = false;
            this.progress = 0;
            this.importResults = null;
        }
    };
}

function openImportModal() {
    window.dispatchEvent(new CustomEvent('open-import-modal'));
}
</script>
@endpush
