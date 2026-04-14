<?php

declare(strict_types=1);

namespace App\Livewire\Datatable;

use App\Models\LabTest;

class LabTestDatatable extends Datatable
{
    public string $model = LabTest::class;

    public bool $showFilters = true;
    public array $relationships = ['provider'];

    public array $searchableColumns = ['name'];

    public array $filterableColumns = [
        'status' => ['active', 'inactive'],
    ];

    public array $sortableColumns = [
        'name',
        'cost',
        'status',
        'created_at',
    ];

    public array $bulkActions = [];
    
    // Export/Import Configuration
    public bool $enablePdf = false;
    public bool $enablePrint = false;
    public bool $enableExport = true;
    public bool $enableImport = true;

    public function getRoutes(): array
    {
        return [
            'index' => 'admin.lab-tests.index',
            'create' => 'admin.lab-tests.create',
            'show' => 'admin.lab-tests.show',
            'edit' => 'admin.lab-tests.edit',
            'export' => 'admin.lab-tests.export',
            'import' => 'admin.lab-tests.import',
            'sampleTemplate' => 'admin.lab-tests.sample-template',
        ];
    }

    public function getPermissions(): array
    {
        return [
            'create' => 'lab_test.create',
            'view' => 'lab_test.view',
            'edit' => 'lab_test.edit',
            'delete' => 'lab_test.delete',
        ];
    }

    public function getModelNameSingular(): string
    {
        return 'Lab Test';
    }

    public function getModelNamePlural(): string
    {
        return 'Lab Tests';
    }

    public function getSearchbarPlaceholder(): string
    {
        return __('Search lab tests...');
    }

    public function getNewResourceLinkLabel(): string
    {
        return __('Add Lab Test');
    }

    public function getNoResultsMessage(): string
    {
        return __('No lab tests found.');
    }

    protected function getHeaders(): array
    {
        return [
            [
                'id' => 'id',
                'title' => __('ID'),
                'sortable' => false,
                'width' => 'w-16',
            ],
            [
                'id' => 'name',
                'title' => __('Test Name'),
                'sortable' => true,
                'sortBy' => 'name',
                'width' => 'w-1/4',
            ],
            [
                'id' => 'provider',
                'title' => __('Provider'),
                'sortable' => false,
                'width' => 'w-1/5',
                'renderContent' => 'renderProviderColumn',
            ],
            [
                'id' => 'pricing',
                'title' => __('Pricing'),
                'sortable' => false,
                'width' => 'w-1/4',
                'renderContent' => 'renderPricingColumn',
            ],
            [
                'id' => 'status',
                'title' => __('Status'),
                'sortable' => true,
                'sortBy' => 'status',
                'width' => 'w-1/6',
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

    public function renderProviderColumn($labTest): string
    {
        if ($labTest->provider) {
            return '<div class="text-sm font-medium text-blue-600 dark:text-blue-400">' . e($labTest->provider->name) . '</div>';
        }
        return '<span class="text-gray-400">-</span>';
    }

    public function renderPricingColumn($labTest): string
    {
        return '<div class="text-sm">
            <div class="font-medium text-gray-900 dark:text-white">Cost: $' . number_format((float)$labTest->cost, 2) . '</div>
            <div class="text-xs text-gray-500 dark:text-gray-400">Commission: ' . number_format((float)$labTest->commission_percentage, 2) . '%</div>
            <div class="text-xs font-semibold text-green-600 dark:text-green-400">Total: $' . number_format((float)$labTest->total, 2) . '</div>
        </div>';
    }

    public function renderStatusColumn($labTest): string
    {
        $colorClass = match ($labTest->status) {
            'active' => 'bg-green-100 text-green-800 border border-green-200 dark:bg-green-900/20 dark:text-green-400 dark:border-green-800',
            'inactive' => 'bg-red-100 text-red-800 border border-red-200 dark:bg-red-900/20 dark:text-red-400 dark:border-red-800',
            default => 'bg-gray-100 text-gray-800 border border-gray-200 dark:bg-gray-900/20 dark:text-gray-400 dark:border-gray-800',
        };

        return '<span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold ' . $colorClass . '">'
            . e(ucfirst($labTest->status))
            . '</span>';
    }

    public function renderActionsColumn($labTest): string
    {
        $html = '<div class="flex items-center space-x-2">';

        // View button
        $html .= '<a href="' . route('admin.lab-tests.show', $labTest) . '" class="inline-flex items-center justify-center w-8 h-8 text-blue-600 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 hover:text-blue-700 transition-colors duration-200 dark:bg-blue-900/20 dark:text-blue-400 dark:border-blue-800" title="' . __('View Lab Test') . '">';
        $html .= '<iconify-icon icon="lucide:eye" class="w-4 h-4"></iconify-icon>';
        $html .= '</a>';

        // Edit button
        if (auth()->user()->can('lab_test.edit')) {
            $html .= '<a href="' . route('admin.lab-tests.edit', $labTest) . '" class="inline-flex items-center justify-center w-8 h-8 text-green-600 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 hover:text-green-700 transition-colors duration-200 dark:bg-green-900/20 dark:text-green-400 dark:border-green-800" title="' . __('Edit Lab Test') . '">';
            $html .= '<iconify-icon icon="lucide:edit" class="w-4 h-4"></iconify-icon>';
            $html .= '</a>';
        }

        // Delete button
        if (auth()->user()->can('lab_test.delete')) {
            $html .= '<button onclick="confirmDelete(' . $labTest->id . ')" class="inline-flex items-center justify-center w-8 h-8 text-red-600 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 hover:text-red-700 transition-colors duration-200 dark:bg-red-900/20 dark:text-red-400 dark:border-red-800" title="' . __('Delete Lab Test') . '">';
            $html .= '<iconify-icon icon="lucide:trash" class="w-4 h-4"></iconify-icon>';
            $html .= '</button>';
        }

        $html .= '</div>';

        return $html;
    }
}
