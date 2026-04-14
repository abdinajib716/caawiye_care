<?php

declare(strict_types=1);

namespace App\Livewire\Datatable;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Builder;

class SupplierDatatable extends Datatable
{
    public string $model = Supplier::class;

    public array $searchableColumns = ['name', 'phone', 'email', 'address'];

    public array $filterableColumns = ['status'];

    public function query(): Builder
    {
        return Supplier::query()->orderBy('name');
    }

    public function getRoutes(): array
    {
        return [
            'index' => 'admin.suppliers.index',
            'show' => 'admin.suppliers.show',
            'create' => 'admin.suppliers.create',
            'edit' => 'admin.suppliers.edit',
        ];
    }

    public function getPermissions(): array
    {
        return [
            'view' => 'supplier.view',
            'create' => 'supplier.create',
            'edit' => 'supplier.edit',
            'delete' => 'supplier.delete',
        ];
    }

    public function getModelNameSingular(): string
    {
        return __('supplier');
    }

    public function getModelNamePlural(): string
    {
        return __('suppliers');
    }

    public function getSearchbarPlaceholder(): string
    {
        return __('Search suppliers by name, phone, email...');
    }

    protected function getHeaders(): array
    {
        return [
            [
                'id' => 'name',
                'title' => __('Name'),
                'sortable' => true,
                'sortBy' => 'name',
            ],
            [
                'id' => 'phone',
                'title' => __('Phone'),
                'sortable' => false,
            ],
            [
                'id' => 'email',
                'title' => __('Email'),
                'sortable' => false,
            ],
            [
                'id' => 'address',
                'title' => __('Address'),
                'sortable' => false,
            ],
            [
                'id' => 'status',
                'title' => __('Status'),
                'sortable' => true,
                'sortBy' => 'status',
                'renderContent' => 'renderStatusColumn',
            ],
            [
                'id' => 'actions',
                'title' => __('Actions'),
                'sortable' => false,
                'is_action' => true,
                'renderContent' => 'renderActionsColumn',
            ],
        ];
    }

    public function renderStatusColumn($supplier): string
    {
        $colorClass = $supplier->status === 'active' 
            ? 'bg-green-100 text-green-800 border border-green-200'
            : 'bg-gray-100 text-gray-800 border border-gray-200';

        return '<span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold ' . $colorClass . '">' . ucfirst($supplier->status) . '</span>';
    }

    public function renderActionsColumn($supplier): string
    {
        $modalId = 'deleteModal_' . $supplier->id;
        
        $html = '<div class="flex items-center space-x-2" x-data="{ ' . $modalId . ': false }">';

        // View button
        $html .= '<a href="' . route('admin.suppliers.show', $supplier) . '" class="inline-flex items-center justify-center w-8 h-8 text-blue-600 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 hover:text-blue-700 transition-colors duration-200" title="' . __('View Supplier') . '">';
        $html .= '<iconify-icon icon="lucide:eye" class="w-4 h-4"></iconify-icon>';
        $html .= '</a>';

        // Edit button
        $html .= '<a href="' . route('admin.suppliers.edit', $supplier) . '" class="inline-flex items-center justify-center w-8 h-8 text-green-600 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 hover:text-green-700 transition-colors duration-200" title="' . __('Edit Supplier') . '">';
        $html .= '<iconify-icon icon="lucide:edit" class="w-4 h-4"></iconify-icon>';
        $html .= '</a>';

        // Delete button
        $html .= '<button @click="' . $modalId . ' = true" class="inline-flex items-center justify-center w-8 h-8 text-red-600 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 hover:text-red-700 transition-colors duration-200" title="' . __('Delete Supplier') . '">';
        $html .= '<iconify-icon icon="lucide:trash-2" class="w-4 h-4"></iconify-icon>';
        $html .= '</button>';

        // Delete confirmation modal (moved outside to body level via teleport)
        $html .= '<template x-teleport="body">';
        $html .= '<div x-cloak x-show="' . $modalId . '" x-transition.opacity.duration.200ms @keydown.escape.window="' . $modalId . ' = false" @click.self="' . $modalId . ' = false" class="fixed inset-0 z-50 flex items-center justify-center bg-black/20 p-4 backdrop-blur-sm" role="dialog" aria-modal="true">';
        $html .= '<div x-show="' . $modalId . '" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="w-full max-w-md rounded-lg bg-white shadow-xl dark:bg-gray-800">';
        
        // Header
        $html .= '<div class="flex items-center justify-between border-b border-gray-200 px-6 py-4 dark:border-gray-700">';
        $html .= '<div class="flex items-center gap-3">';
        $html .= '<div class="flex items-center justify-center rounded-full bg-red-100 p-2 dark:bg-red-900/30">';
        $html .= '<svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>';
        $html .= '</div>';
        $html .= '<h3 class="text-lg font-semibold text-gray-900 dark:text-white">' . __('Delete Supplier') . '</h3>';
        $html .= '</div>';
        $html .= '<button @click="' . $modalId . ' = false" class="rounded-md p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-gray-700 dark:hover:text-gray-300">';
        $html .= '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>';
        $html .= '</button>';
        $html .= '</div>';
        
        // Content
        $html .= '<div class="px-6 py-4">';
        $html .= '<p class="text-sm text-gray-600 dark:text-gray-300">' . __('Are you sure you want to delete this supplier?') . '</p>';
        $html .= '<div class="mt-3 rounded-md bg-gray-50 p-3 dark:bg-gray-900/50">';
        $html .= '<p class="text-sm font-medium text-gray-900 dark:text-white">' . e($supplier->name) . '</p>';
        $html .= '<p class="text-xs text-gray-500 dark:text-gray-400 mt-1">' . e($supplier->phone) . '</p>';
        $html .= '</div>';
        $html .= '<p class="mt-3 text-xs text-red-600 dark:text-red-400">' . __('This action cannot be undone.') . '</p>';
        $html .= '</div>';
        
        // Footer
        $html .= '<div class="flex items-center justify-end gap-3 border-t border-gray-200 px-6 py-4 dark:border-gray-700">';
        $html .= '<button @click="' . $modalId . ' = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">' . __('Cancel') . '</button>';
        $html .= '<button wire:click="deleteItem(' . $supplier->id . ')" @click="' . $modalId . ' = false" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">' . __('Delete') . '</button>';
        $html .= '</div>';
        
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</template>';

        $html .= '</div>';

        return $html;
    }
}
