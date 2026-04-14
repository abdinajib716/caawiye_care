<?php

declare(strict_types=1);

namespace App\Livewire\Datatable;

use App\Models\Provider;

class ProviderDatatable extends Datatable
{
    public string $model = Provider::class;

    public bool $showFilters = true;
    public array $relationships = [];

    public array $searchableColumns = ['name', 'phone', 'email'];

    public array $filterableColumns = [
        'status' => ['active', 'inactive'],
    ];

    public array $sortableColumns = [
        'name',
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
            'index' => 'admin.providers.index',
            'create' => 'admin.providers.create',
            'show' => 'admin.providers.show',
            'edit' => 'admin.providers.edit',
            'export' => 'admin.providers.export',
            'import' => 'admin.providers.import',
            'sampleTemplate' => 'admin.providers.sample-template',
        ];
    }

    public function getPermissions(): array
    {
        return [
            'create' => 'provider.create',
            'view' => 'provider.view',
            'edit' => 'provider.edit',
            'delete' => 'provider.delete',
        ];
    }

    public function getModelNameSingular(): string
    {
        return 'Provider';
    }

    public function getModelNamePlural(): string
    {
        return 'Providers';
    }

    public function getSearchbarPlaceholder(): string
    {
        return __('Search providers...');
    }

    public function getNewResourceLinkLabel(): string
    {
        return __('Add Provider');
    }

    public function getNoResultsMessage(): string
    {
        return __('No providers found.');
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
                'title' => __('Name'),
                'sortable' => true,
                'sortBy' => 'name',
                'width' => 'w-1/4',
            ],
            [
                'id' => 'contact',
                'title' => __('Contact'),
                'sortable' => false,
                'width' => 'w-1/4',
                'renderContent' => 'renderContactColumn',
            ],
            [
                'id' => 'address',
                'title' => __('Address'),
                'sortable' => false,
                'width' => 'w-1/4',
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

    public function renderContactColumn($provider): string
    {
        return '<div class="text-sm">
            <div class="font-medium text-gray-900 dark:text-white">' . e($provider->phone) . '</div>
            <div class="text-xs text-gray-500 dark:text-gray-400">' . e($provider->email ?? '-') . '</div>
        </div>';
    }

    public function renderStatusColumn($provider): string
    {
        $colorClass = match ($provider->status) {
            'active' => 'bg-green-100 text-green-800 border border-green-200 dark:bg-green-900/20 dark:text-green-400 dark:border-green-800',
            'inactive' => 'bg-red-100 text-red-800 border border-red-200 dark:bg-red-900/20 dark:text-red-400 dark:border-red-800',
            default => 'bg-gray-100 text-gray-800 border border-gray-200 dark:bg-gray-900/20 dark:text-gray-400 dark:border-gray-800',
        };

        return '<span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold ' . $colorClass . '">'
            . e(ucfirst($provider->status))
            . '</span>';
    }

    public function renderActionsColumn($provider): string
    {
        $html = '<div class="flex items-center space-x-2">';

        // View button
        $html .= '<a href="' . route('admin.providers.show', $provider) . '" class="inline-flex items-center justify-center w-8 h-8 text-blue-600 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 hover:text-blue-700 transition-colors duration-200 dark:bg-blue-900/20 dark:text-blue-400 dark:border-blue-800" title="' . __('View Provider') . '">';
        $html .= '<iconify-icon icon="lucide:eye" class="w-4 h-4"></iconify-icon>';
        $html .= '</a>';

        // Edit button
        if (auth()->user()->can('provider.edit')) {
            $html .= '<a href="' . route('admin.providers.edit', $provider) . '" class="inline-flex items-center justify-center w-8 h-8 text-green-600 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 hover:text-green-700 transition-colors duration-200 dark:bg-green-900/20 dark:text-green-400 dark:border-green-800" title="' . __('Edit Provider') . '">';
            $html .= '<iconify-icon icon="lucide:edit" class="w-4 h-4"></iconify-icon>';
            $html .= '</a>';
        }

        // Delete button
        if (auth()->user()->can('provider.delete')) {
            $html .= '<button onclick="confirmDelete(' . $provider->id . ')" class="inline-flex items-center justify-center w-8 h-8 text-red-600 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 hover:text-red-700 transition-colors duration-200 dark:bg-red-900/20 dark:text-red-400 dark:border-red-800" title="' . __('Delete Provider') . '">';
            $html .= '<iconify-icon icon="lucide:trash" class="w-4 h-4"></iconify-icon>';
            $html .= '</button>';
        }

        $html .= '</div>';

        return $html;
    }
}
