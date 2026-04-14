<?php

declare(strict_types=1);

namespace App\Livewire\Datatable;

use App\Models\Hospital;

class HospitalDatatable extends Datatable
{
    public string $model = Hospital::class;

    public bool $showFilters = false;
    public array $relationships = [];

    public array $searchableColumns = ['name', 'phone', 'email', 'address'];

    public array $filterableColumns = [
        'status' => ['active', 'inactive'],
    ];

    public array $sortableColumns = [
        'name',
        'phone',
        'email',
        'address',
        'status',
        'created_at',
        'updated_at',
    ];

    public array $bulkActions = [
        'delete' => 'Delete Selected',
    ];
    
    // Export/Import Configuration
    public bool $enablePdf = false;
    public bool $enablePrint = false;
    public bool $enableExport = true;
    public bool $enableImport = true;

    public function getRoutes(): array
    {
        return [
            'index' => 'admin.hospitals.index',
            'create' => 'admin.hospitals.create',
            'show' => 'admin.hospitals.show',
            'edit' => 'admin.hospitals.edit',
            'destroy' => 'admin.hospitals.destroy',
            'export' => 'admin.hospitals.export',
            'import' => 'admin.hospitals.import',
            'sampleTemplate' => 'admin.hospitals.sample-template',
        ];
    }

    public function getPermissions(): array
    {
        return [
            'create' => 'hospital.create',
            'view' => 'hospital.view',
            'edit' => 'hospital.edit',
            'delete' => 'hospital.delete',
        ];
    }

    public function getModelNameSingular(): string
    {
        return 'Hospital';
    }

    public function getModelNamePlural(): string
    {
        return 'Hospitals';
    }

    public function getSearchbarPlaceholder(): string
    {
        return __('Search hospitals...');
    }

    public function getNewResourceLinkLabel(): string
    {
        return __('Add Hospital');
    }

    public function getNoResultsMessage(): string
    {
        return __('No hospitals found.');
    }

    protected function getHeaders(): array
    {
        return [
            [
                'id' => 'name',
                'title' => __('Name'),
                'sortable' => true,
                'searchable' => true,
                'sortBy' => 'name',
                'width' => 'w-1/5',
            ],
            [
                'id' => 'contact',
                'title' => __('Contact'),
                'sortable' => false,
                'width' => 'w-1/5',
                'renderContent' => 'renderContactColumn',
            ],
            [
                'id' => 'address',
                'title' => __('Address'),
                'sortable' => true,
                'sortBy' => 'address',
                'width' => 'w-2/5',
            ],
            [
                'id' => 'status',
                'title' => __('Status'),
                'sortable' => true,
                'sortBy' => 'status',
                'width' => 'w-1/12',
                'renderContent' => 'renderStatusColumn',
            ],
            [
                'id' => 'actions',
                'title' => __('Actions'),
                'sortable' => false,
                'width' => 'w-1/12',
            ],
        ];
    }

    public function renderContactColumn($hospital): string
    {
        $html = '<div class="text-sm">';

        if ($hospital->phone) {
            $html .= '<div class="font-medium text-gray-900">' . e($hospital->phone) . '</div>';
        }

        if ($hospital->email) {
            $html .= '<div class="text-xs text-gray-500">' . e($hospital->email) . '</div>';
        }

        if (!$hospital->phone && !$hospital->email) {
            $html .= '<span class="text-gray-400">-</span>';
        }

        $html .= '</div>';

        return $html;
    }

    public function renderStatusColumn($hospital): string
    {
        $colorClass = match ($hospital->status) {
            'active' => 'bg-green-100 text-green-800 border border-green-200',
            'inactive' => 'bg-red-100 text-red-800 border border-red-200',
            default => 'bg-gray-100 text-gray-800 border border-gray-200',
        };

        return '<span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold ' . $colorClass . '">'
            . e(ucfirst($hospital->status))
            . '</span>';
    }

    public function renderActionsColumn($hospital): string
    {
        $html = '<div class="flex items-center space-x-2">';

        // View button
        $html .= '<a href="' . route('admin.hospitals.show', $hospital) . '" class="inline-flex items-center justify-center w-8 h-8 text-blue-600 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 hover:text-blue-700 transition-colors duration-200" title="' . __('View Hospital') . '">';
        $html .= '<iconify-icon icon="lucide:eye" class="w-4 h-4"></iconify-icon>';
        $html .= '</a>';

        // Edit button
        $html .= '<a href="' . route('admin.hospitals.edit', $hospital) . '" class="inline-flex items-center justify-center w-8 h-8 text-green-600 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 hover:text-green-700 transition-colors duration-200" title="' . __('Edit Hospital') . '">';
        $html .= '<iconify-icon icon="lucide:edit" class="w-4 h-4"></iconify-icon>';
        $html .= '</a>';

        // Delete button using system functionality
        $html .= '<div x-data="{ deleteModalOpen: false }">';
        $html .= '<button @click="deleteModalOpen = true" class="inline-flex items-center justify-center w-8 h-8 text-red-600 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 hover:text-red-700 transition-colors duration-200" title="' . __('Delete Hospital') . '">';
        $html .= '<iconify-icon icon="lucide:trash-2" class="w-4 h-4"></iconify-icon>';
        $html .= '</button>';

        // System confirm delete modal
        $html .= '<div x-cloak x-show="deleteModalOpen" x-transition.opacity.duration.200ms x-trap.inert.noscroll="deleteModalOpen" x-on:keydown.esc.window="deleteModalOpen = false" x-on:click.self="deleteModalOpen = false" class="fixed inset-0 z-50 flex items-center justify-center bg-black/20 p-4 backdrop-blur-md" role="dialog" aria-modal="true">';
        $html .= '<div x-show="deleteModalOpen" x-transition:enter="transition ease-out duration-200 delay-100" x-transition:enter-start="opacity-0 scale-50" x-transition:enter-end="opacity-100 scale-100" class="w-full max-w-md rounded-lg bg-white p-0 shadow-xl dark:bg-gray-800">';
        $html .= '<div class="flex items-center justify-between border-b border-gray-100 p-4 dark:border-gray-800">';
        $html .= '<h3 class="font-semibold tracking-wide text-gray-700 dark:text-white">' . __('Delete Hospital') . '</h3>';
        $html .= '<button x-on:click="deleteModalOpen = false" class="text-gray-400 hover:bg-gray-200 hover:text-gray-700 rounded-md p-1 dark:hover:bg-gray-600 dark:hover:text-white"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-width="1.4" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>';
        $html .= '</div>';
        $html .= '<div class="px-4 text-center">';
        $html .= '<p class="text-gray-500 dark:text-gray-300">' . __('Are you sure you want to delete this hospital?') . '</p>';
        $html .= '<p class="font-medium text-gray-900 dark:text-white mt-2">' . e($hospital->name) . '</p>';
        $html .= '<p class="text-sm text-gray-400 mt-1">' . __('This action cannot be undone.') . '</p>';
        $html .= '</div>';
        $html .= '<div class="flex items-center justify-center gap-2 border-t border-gray-100 p-4 dark:border-gray-800">';
        $html .= '<button x-on:click="deleteModalOpen = false" class="btn-secondary">' . __('Cancel') . '</button>';
        $html .= '<button wire:click="deleteItem(' . $hospital->id . ')" x-on:click="deleteModalOpen = false" class="btn-danger">' . __('Delete') . '</button>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '</div>';

        return $html;
    }
}

