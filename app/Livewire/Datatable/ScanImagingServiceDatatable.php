<?php

declare(strict_types=1);

namespace App\Livewire\Datatable;

use App\Models\ScanImagingService as ScanImagingServiceModel;

class ScanImagingServiceDatatable extends Datatable
{
    public string $model = ScanImagingServiceModel::class;
    public bool $showFilters = true;
    public array $relationships = ['provider'];
    public array $searchableColumns = ['service_name'];
    public array $filterableColumns = ['status' => ['active', 'inactive']];
    public array $sortableColumns = ['service_name', 'cost', 'status', 'created_at'];
    public array $bulkActions = [];
    
    // Export/Import Configuration
    public bool $enablePdf = false;
    public bool $enablePrint = false;
    public bool $enableExport = true;
    public bool $enableImport = true;

    public function getRoutes(): array
    {
        return [
            'index' => 'admin.scan-imaging-services.index',
            'create' => 'admin.scan-imaging-services.create',
            'show' => 'admin.scan-imaging-services.show',
            'edit' => 'admin.scan-imaging-services.edit',
            'export' => 'admin.scan-imaging-services.export',
            'import' => 'admin.scan-imaging-services.import',
            'sampleTemplate' => 'admin.scan-imaging-services.sample-template',
        ];
    }

    public function getPermissions(): array
    {
        return [
            'create' => 'scan_imaging_service.create',
            'view' => 'scan_imaging_service.view',
            'edit' => 'scan_imaging_service.edit',
            'delete' => 'scan_imaging_service.delete',
        ];
    }

    public function getModelNameSingular(): string
    {
        return 'Scan & Imaging Service';
    }

    public function getModelNamePlural(): string
    {
        return 'Scan & Imaging Services';
    }

    public function getSearchbarPlaceholder(): string
    {
        return __('Search services...');
    }

    public function getNewResourceLinkLabel(): string
    {
        return __('Add Service');
    }

    public function getNoResultsMessage(): string
    {
        return __('No services found.');
    }

    protected function getHeaders(): array
    {
        return [
            ['id' => 'id', 'title' => __('ID'), 'sortable' => false, 'width' => 'w-16'],
            ['id' => 'service_name', 'title' => __('Service Name'), 'sortable' => true, 'sortBy' => 'service_name', 'width' => 'w-1/4'],
            ['id' => 'provider', 'title' => __('Provider'), 'sortable' => false, 'width' => 'w-1/5', 'renderContent' => 'renderProviderColumn'],
            ['id' => 'pricing', 'title' => __('Pricing'), 'sortable' => false, 'width' => 'w-1/4', 'renderContent' => 'renderPricingColumn'],
            ['id' => 'status', 'title' => __('Status'), 'sortable' => true, 'sortBy' => 'status', 'width' => 'w-1/6', 'renderContent' => 'renderStatusColumn'],
            ['id' => 'actions', 'title' => __('Actions'), 'sortable' => false, 'width' => 'w-1/12'],
        ];
    }

    public function renderProviderColumn($service): string
    {
        return $service->provider ? '<div class="text-sm font-medium text-blue-600 dark:text-blue-400">' . e($service->provider->name) . '</div>' : '<span class="text-gray-400">-</span>';
    }

    public function renderPricingColumn($service): string
    {
        return '<div class="text-sm"><div class="font-medium text-gray-900 dark:text-white">Cost: $' . number_format((float)$service->cost, 2) . '</div><div class="text-xs text-gray-500 dark:text-gray-400">Commission: ' . number_format((float)$service->commission_percentage, 2) . '%</div><div class="text-xs font-semibold text-green-600 dark:text-green-400">Total: $' . number_format((float)$service->total, 2) . '</div></div>';
    }

    public function renderStatusColumn($service): string
    {
        $colorClass = $service->status === 'active' ? 'bg-green-100 text-green-800 border border-green-200 dark:bg-green-900/20 dark:text-green-400 dark:border-green-800' : 'bg-red-100 text-red-800 border border-red-200 dark:bg-red-900/20 dark:text-red-400 dark:border-red-800';
        return '<span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold ' . $colorClass . '">' . e(ucfirst($service->status)) . '</span>';
    }

    public function renderActionsColumn($service): string
    {
        $html = '<div class="flex items-center space-x-2">';
        $html .= '<a href="' . route('admin.scan-imaging-services.show', $service) . '" class="inline-flex items-center justify-center w-8 h-8 text-blue-600 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 hover:text-blue-700 transition-colors duration-200 dark:bg-blue-900/20 dark:text-blue-400 dark:border-blue-800"><iconify-icon icon="lucide:eye" class="w-4 h-4"></iconify-icon></a>';
        if (auth()->user()->can('scan_imaging_service.edit')) $html .= '<a href="' . route('admin.scan-imaging-services.edit', $service) . '" class="inline-flex items-center justify-center w-8 h-8 text-green-600 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 hover:text-green-700 transition-colors duration-200 dark:bg-green-900/20 dark:text-green-400 dark:border-green-800"><iconify-icon icon="lucide:edit" class="w-4 h-4"></iconify-icon></a>';
        if (auth()->user()->can('scan_imaging_service.delete')) $html .= '<button onclick="confirmDelete(' . $service->id . ')" class="inline-flex items-center justify-center w-8 h-8 text-red-600 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 hover:text-red-700 transition-colors duration-200 dark:bg-red-900/20 dark:text-red-400 dark:border-red-800"><iconify-icon icon="lucide:trash" class="w-4 h-4"></iconify-icon></button>';
        return $html . '</div>';
    }
}
