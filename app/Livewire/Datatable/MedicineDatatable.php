<?php

declare(strict_types=1);

namespace App\Livewire\Datatable;

use App\Models\Medicine;
use Illuminate\Database\Eloquent\Builder;

class MedicineDatatable extends Datatable
{
    public string $model = Medicine::class;

    public array $searchableColumns = ['name'];

    public function query(): Builder
    {
        return Medicine::query()->orderBy('name');
    }

    public function getRoutes(): array
    {
        return [
            'index' => 'admin.medicines.index',
            'create' => 'admin.medicines.create',
            'edit' => 'admin.medicines.edit',
        ];
    }

    public function getPermissions(): array
    {
        return [
            'view' => 'medicine.view',
            'create' => 'medicine.create',
            'edit' => 'medicine.edit',
            'delete' => 'medicine.delete',
        ];
    }

    public function getModelNameSingular(): string
    {
        return __('medicine');
    }

    public function getModelNamePlural(): string
    {
        return __('medicines');
    }

    public function getSearchbarPlaceholder(): string
    {
        return __('Search medicines by name...');
    }

    protected function getHeaders(): array
    {
        return [
            [
                'id' => 'name',
                'title' => __('Medicine Name'),
                'sortable' => true,
                'sortBy' => 'name',
            ],
            [
                'id' => 'created_at',
                'title' => __('Added On'),
                'sortable' => true,
                'sortBy' => 'created_at',
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

    public function renderActionsColumn($medicine): string
    {
        $html = '<div class="flex items-center space-x-2">';

        // Edit button
        $html .= '<a href="' . route('admin.medicines.edit', $medicine) . '" class="inline-flex items-center justify-center w-8 h-8 text-green-600 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 hover:text-green-700 transition-colors duration-200" title="' . __('Edit Medicine') . '">';
        $html .= '<iconify-icon icon="lucide:edit" class="w-4 h-4"></iconify-icon>';
        $html .= '</a>';

        // Delete button with modal
        $html .= '<div x-data="{ deleteModalOpen: false }">';
        $html .= '<button @click="deleteModalOpen = true" class="inline-flex items-center justify-center w-8 h-8 text-red-600 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 hover:text-red-700 transition-colors duration-200" title="' . __('Delete Medicine') . '">';
        $html .= '<iconify-icon icon="lucide:trash-2" class="w-4 h-4"></iconify-icon>';
        $html .= '</button>';

        // Delete confirmation modal
        $html .= '<div x-cloak x-show="deleteModalOpen" x-transition.opacity.duration.200ms x-trap.inert.noscroll="deleteModalOpen" x-on:keydown.esc.window="deleteModalOpen = false" x-on:click.self="deleteModalOpen = false" class="fixed inset-0 z-50 flex items-center justify-center bg-black/20 p-4 backdrop-blur-md" role="dialog" aria-modal="true">';
        $html .= '<div x-show="deleteModalOpen" x-transition:enter="transition ease-out duration-200 delay-100" x-transition:enter-start="opacity-0 scale-50" x-transition:enter-end="opacity-100 scale-100" class="w-full max-w-md rounded-lg bg-white p-0 shadow-xl dark:bg-gray-800">';
        $html .= '<div class="flex items-center justify-between border-b border-gray-100 p-4 dark:border-gray-800">';
        $html .= '<h3 class="font-semibold tracking-wide text-gray-700 dark:text-white">' . __('Delete Medicine') . '</h3>';
        $html .= '<button x-on:click="deleteModalOpen = false" class="text-gray-400 hover:bg-gray-200 hover:text-gray-700 rounded-md p-1 dark:hover:bg-gray-600 dark:hover:text-white"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-width="1.4" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>';
        $html .= '</div>';
        $html .= '<div class="px-4 py-6 text-center">';
        $html .= '<p class="text-gray-500 dark:text-gray-300">' . __('Are you sure you want to delete this medicine?') . '</p>';
        $html .= '<p class="font-medium text-gray-900 dark:text-white mt-2">' . e($medicine->name) . '</p>';
        $html .= '<p class="text-sm text-gray-400 mt-1">' . __('This action cannot be undone.') . '</p>';
        $html .= '</div>';
        $html .= '<div class="flex items-center justify-end gap-3 border-t border-gray-100 p-4 dark:border-gray-800">';
        $html .= '<button type="button" x-on:click="deleteModalOpen = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:text-white dark:focus:ring-gray-700">' . __('No, Cancel') . '</button>';
        $html .= '<button type="button" wire:click="deleteItem(' . $medicine->id . ')" @click="deleteModalOpen = false" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-300 dark:focus:ring-red-800">' . __('Yes, Delete') . '</button>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '</div>';

        return $html;
    }
}
