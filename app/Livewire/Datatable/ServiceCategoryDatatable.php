<?php

declare(strict_types=1);

namespace App\Livewire\Datatable;

use App\Models\ServiceCategory;
use Illuminate\Database\Eloquent\Builder;

class ServiceCategoryDatatable extends Datatable
{
    public string $model = ServiceCategory::class;

    public string $statusFilter = '';

    public array $queryString = [
        ...parent::QUERY_STRING_DEFAULTS,
        'statusFilter' => ['except' => ''],
    ];

    public function getSearchbarPlaceholder(): string
    {
        return __('Search categories...');
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function query(): Builder
    {
        return ServiceCategory::query()
            ->withCount(['services'])
            ->when($this->search, function (Builder $query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->when($this->statusFilter === 'active', function (Builder $query) {
                $query->where('is_active', true);
            })
            ->when($this->statusFilter === 'inactive', function (Builder $query) {
                $query->where('is_active', false);
            });
    }

    protected function getHeaders(): array
    {
        return [
            [
                'id' => 'name',
                'title' => __('Name'),
                'sortBy' => 'name',
                'searchable' => true,
                'sortable' => true,
            ],
            [
                'id' => 'services_count',
                'title' => __('Services'),
                'sortBy' => 'services_count',
                'searchable' => false,
                'sortable' => true,
            ],
            [
                'id' => 'is_active',
                'title' => __('Status'),
                'sortBy' => 'is_active',
                'searchable' => false,
                'sortable' => true,
                'renderer' => 'renderIsActiveColumn',
            ],
            [
                'id' => 'created_at',
                'title' => __('Created'),
                'sortBy' => 'created_at',
                'searchable' => false,
                'sortable' => true,
                'renderer' => 'renderCreatedAtColumn',
            ],
            [
                'id' => 'actions',
                'title' => __('Actions'),
                'sortBy' => '',
                'searchable' => false,
                'sortable' => false,
                'is_action' => true,
            ],
        ];
    }

    public function renderIsActiveColumn($category): string
    {
        $colorClass = $category->is_active
            ? 'bg-green-100 text-green-800 border border-green-200'
            : 'bg-red-100 text-red-800 border border-red-200';

        $text = $category->is_active ? __('Active') : __('Inactive');

        return '<span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold ' . $colorClass . '">' . $text . '</span>';
    }

    public function renderCreatedAtColumn($category): string
    {
        return '<div class="text-sm text-gray-900">' . $category->created_at->format('d M') . '</div>' .
               '<div class="text-xs text-gray-500">' . $category->created_at->format('Y') . '</div>';
    }

    public function renderActionsColumn($category): string
    {
        $html = '<div class="flex items-center space-x-2">';

        // View button
        $html .= '<a href="' . route('admin.service-categories.show', $category) . '" class="inline-flex items-center justify-center w-8 h-8 text-blue-600 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 hover:text-blue-700 transition-colors duration-200" title="' . __('View Category') . '">';
        $html .= '<iconify-icon icon="lucide:eye" class="w-4 h-4"></iconify-icon>';
        $html .= '</a>';

        // Edit button
        $html .= '<a href="' . route('admin.service-categories.edit', $category) . '" class="inline-flex items-center justify-center w-8 h-8 text-green-600 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 hover:text-green-700 transition-colors duration-200" title="' . __('Edit Category') . '">';
        $html .= '<iconify-icon icon="lucide:edit" class="w-4 h-4"></iconify-icon>';
        $html .= '</a>';

        // Delete button
        $html .= '<form method="POST" action="' . route('admin.service-categories.destroy', $category) . '" class="inline-block" onsubmit="return confirm(\'' . __('Are you sure you want to delete this category?') . '\')">';
        $html .= csrf_field();
        $html .= method_field('DELETE');
        $html .= '<button type="submit" class="inline-flex items-center justify-center w-8 h-8 text-red-600 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 hover:text-red-700 transition-colors duration-200" title="' . __('Delete Category') . '">';
        $html .= '<iconify-icon icon="lucide:trash" class="w-4 h-4"></iconify-icon>';
        $html .= '</button>';
        $html .= '</form>';

        $html .= '</div>';
        return $html;
    }
}
