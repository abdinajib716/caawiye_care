<?php

declare(strict_types=1);

namespace App\Livewire\Datatable;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Spatie\QueryBuilder\QueryBuilder;

class ServiceDatatable extends Datatable
{
    public string $model = Service::class;

    public bool $showFilters = false;
    public array $relationships = ['category'];

    public array $searchableColumns = ['name', 'short_description'];

    public array $filterableColumns = [
        'category_id' => 'service_categories',
        'status' => ['active', 'inactive', 'discontinued'],
        'is_featured' => [1 => 'Featured', 0 => 'Not Featured'],
    ];

    public array $sortableColumns = [
        'name',
        'price',
        'cost',
        'status',
        'is_featured',
        'created_at',
        'updated_at',
    ];

    public string $defaultSortColumn = 'created_at';
    public string $defaultSortDirection = 'desc';

    public array $bulkActions = [
        'bulkDelete' => 'Delete Selected',
        'bulkActivate' => 'Activate Selected',
        'bulkDeactivate' => 'Deactivate Selected',
        'bulkFeature' => 'Feature Selected',
        'bulkUnfeature' => 'Unfeature Selected',
    ];

    public function query(): Builder
    {
        return Service::query()
            ->with(['category'])
            ->when($this->search, function (Builder $query) {
                $query->where(function (Builder $q) {
                    foreach ($this->searchableColumns as $column) {
                        $q->orWhere($column, 'like', '%' . $this->search . '%');
                    }
                });
            })
            ->when($this->filters['category_id'] ?? null, function (Builder $query, $categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->when($this->filters['status'] ?? null, function (Builder $query, $status) {
                $query->where('status', $status);
            })
            ->when(isset($this->filters['is_featured']), function (Builder $query) {
                $query->where('is_featured', (bool) $this->filters['is_featured']);
            })
            ->when($this->filters['price_min'] ?? null, function (Builder $query, $priceMin) {
                $query->where('price', '>=', $priceMin);
            })
            ->when($this->filters['price_max'] ?? null, function (Builder $query, $priceMax) {
                $query->where('price', '<=', $priceMax);
            });
    }

    public function columns(): array
    {
        return [
            [
                'key' => 'name',
                'label' => __('Service Name'),
                'sortable' => true,
                'searchable' => true,
            ],
            [
                'key' => 'category.name',
                'label' => __('Category'),
                'sortable' => false,
            ],
            [
                'key' => 'price',
                'label' => __('Price'),
                'sortable' => true,
                'format' => 'currency',
            ],
            [
                'key' => 'cost',
                'label' => __('Cost'),
                'sortable' => true,
                'format' => 'currency',
            ],
            [
                'key' => 'profit_margin',
                'label' => __('Profit'),
                'sortable' => false,
                'format' => 'currency',
            ],
            [
                'key' => 'status',
                'label' => __('Status'),
                'sortable' => true,
                'format' => 'badge',
            ],
            [
                'key' => 'is_featured',
                'label' => __('Featured'),
                'sortable' => true,
                'format' => 'boolean',
            ],
            [
                'key' => 'created_at',
                'label' => __('Created'),
                'sortable' => true,
                'format' => 'date',
            ],
            [
                'key' => 'actions',
                'label' => __('Actions'),
                'sortable' => false,
            ],
        ];
    }

    public function getFilterOptions(): array
    {
        return [
            'category_id' => ServiceCategory::active()
                ->orderBy('name')
                ->pluck('name', 'id')
                ->toArray(),
            'status' => [
                'active' => __('Active'),
                'inactive' => __('Inactive'),
                'discontinued' => __('Discontinued'),
            ],
            'is_featured' => [
                '1' => __('Featured'),
                '0' => __('Not Featured'),
            ],
        ];
    }

    public function bulkDelete(): void
    {
        $this->authorize('delete', Service::class);

        Service::whereIn('id', $this->selectedItems)->delete();

        $this->selectedItems = [];
        $this->selectAll = false;

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => __('Selected services have been deleted.'),
        ]);
    }

    public function bulkActivate(): void
    {
        $this->authorize('update', Service::class);

        Service::whereIn('id', $this->selectedItems)->update(['status' => 'active']);

        $this->selectedItems = [];
        $this->selectAll = false;

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => __('Selected services have been activated.'),
        ]);
    }

    public function bulkDeactivate(): void
    {
        $this->authorize('update', Service::class);

        Service::whereIn('id', $this->selectedItems)->update(['status' => 'inactive']);

        $this->selectedItems = [];
        $this->selectAll = false;

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => __('Selected services have been deactivated.'),
        ]);
    }

    public function bulkFeature(): void
    {
        $this->authorize('update', Service::class);

        Service::whereIn('id', $this->selectedItems)->update(['is_featured' => true]);

        $this->selectedItems = [];
        $this->selectAll = false;

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => __('Selected services have been featured.'),
        ]);
    }

    public function bulkUnfeature(): void
    {
        $this->authorize('update', Service::class);

        Service::whereIn('id', $this->selectedItems)->update(['is_featured' => false]);

        $this->selectedItems = [];
        $this->selectAll = false;

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => __('Selected services have been unfeatured.'),
        ]);
    }





    public function getSearchbarPlaceholder(): string
    {
        return __('Search services by name, description, or SKU...');
    }

    public function getFilters(): array
    {
        return [
            [
                'id' => 'category_id',
                'label' => __('Category'),
                'filterLabel' => __('Filter by Category'),
                'icon' => 'lucide:tag',
                'allLabel' => __('All Categories'),
                'options' => ServiceCategory::active()->orderBy('name')->pluck('name', 'id')->toArray(),
                'selected' => $this->filters['category_id'] ?? '',
            ],
            [
                'id' => 'status',
                'label' => __('Status'),
                'filterLabel' => __('Filter by Status'),
                'icon' => 'lucide:activity',
                'allLabel' => __('All Statuses'),
                'options' => [
                    'active' => __('Active'),
                    'inactive' => __('Inactive'),
                    'discontinued' => __('Discontinued'),
                ],
                'selected' => $this->filters['status'] ?? '',
            ],
            [
                'id' => 'is_featured',
                'label' => __('Featured'),
                'filterLabel' => __('Filter by Featured'),
                'icon' => 'lucide:star',
                'allLabel' => __('All Services'),
                'options' => [
                    '1' => __('Featured'),
                    '0' => __('Not Featured'),
                ],
                'selected' => $this->filters['is_featured'] ?? '',
            ],
        ];
    }

    protected function getHeaders(): array
    {
        return [
            [
                'id' => 'name',
                'title' => __('Service Name'),
                'sortable' => true,
                'searchable' => true,
                'sortBy' => 'name',
                'width' => 'w-2/5', // 40% width for service name
            ],
            [
                'id' => 'category',
                'title' => __('Category'),
                'sortable' => false,
                'width' => 'w-1/6', // ~16% width
            ],
            [
                'id' => 'price',
                'title' => __('Price'),
                'sortable' => true,
                'sortBy' => 'price',
                'width' => 'w-1/8', // ~12% width
            ],
            [
                'id' => 'profit_margin',
                'title' => __('Profit'),
                'sortable' => false,
                'width' => 'w-1/8', // ~12% width
            ],
            [
                'id' => 'status',
                'title' => __('Status'),
                'sortable' => true,
                'sortBy' => 'status',
                'width' => 'w-1/12', // ~8% width
            ],
            [
                'id' => 'created_at',
                'title' => __('Created'),
                'sortable' => true,
                'sortBy' => 'created_at',
                'width' => 'w-1/12', // ~8% width
            ],
            [
                'id' => 'actions',
                'title' => __('Actions'),
                'sortable' => false,
                'width' => 'w-1/12', // ~8% width
            ],
        ];
    }

    public function renderNameColumn($service): string
    {
        $html = '<div class="flex items-center">';
        $html .= '<div>';
        $html .= '<div class="text-sm font-medium text-gray-900">' . e($service->name) . '</div>';
        if ($service->short_description) {
            $html .= '<div class="text-sm text-gray-500">' . e(Str::limit($service->short_description, 60)) . '</div>';
        }
        $html .= '</div>';
        $html .= '</div>';
        return $html;
    }

    public function renderCategoryColumn($service): string
    {
        if ($service->category) {
            return '<div class="flex items-center">
                        <div class="flex-shrink-0 w-2 h-2 bg-blue-500 rounded-full mr-2"></div>
                        <div class="text-sm font-medium text-gray-900">' . e($service->category->name) . '</div>
                    </div>';
        }
        return '<div class="flex items-center">
                    <div class="flex-shrink-0 w-2 h-2 bg-gray-300 rounded-full mr-2"></div>
                    <div class="text-sm text-gray-400">' . __('No Category') . '</div>
                </div>';
    }

    public function renderPriceColumn($service): string
    {
        $html = '<div class="text-sm font-medium text-gray-900">$' . number_format((float) $service->price, 2) . '</div>';
        if ((float) $service->cost > 0) {
            $html .= '<div class="text-xs text-gray-500">' . __('Cost: $:cost', ['cost' => number_format((float) $service->cost, 2)]) . '</div>';
        }
        return $html;
    }

    public function renderProfitMarginColumn($service): string
    {
        if ((float) $service->cost > 0) {
            $html = '<div class="text-sm font-medium text-green-600">$' . number_format((float) $service->profit_margin, 2) . '</div>';
            $html .= '<div class="text-xs text-gray-500">' . number_format((float) $service->profit_percentage, 1) . '%</div>';
            return $html;
        }
        return '<span class="text-sm text-gray-400">-</span>';
    }

    public function renderStatusColumn($service): string
    {
        $colorClass = match ($service->status) {
            'active' => 'bg-green-100 text-green-800 border border-green-200',
            'inactive' => 'bg-red-100 text-red-800 border border-red-200',
            'discontinued' => 'bg-gray-100 text-gray-800 border border-gray-200',
            default => 'bg-gray-100 text-gray-800 border border-gray-200',
        };

        return '<span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold ' . $colorClass . '">' . e(ucfirst($service->status)) . '</span>';
    }

    public function renderCreatedAtColumn($service): string
    {
        return '<div class="text-sm text-gray-900">' . $service->created_at->format('d M') . '</div>' .
               '<div class="text-xs text-gray-500">' . $service->created_at->format('Y') . '</div>';
    }

    public function renderActionsColumn($service): string
    {
        $html = '<div class="flex items-center space-x-2">';

        // View button
        $html .= '<a href="' . route('admin.services.show', $service) . '" class="inline-flex items-center justify-center w-8 h-8 text-blue-600 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 hover:text-blue-700 transition-colors duration-200" title="' . __('View Service') . '">';
        $html .= '<iconify-icon icon="lucide:eye" class="w-4 h-4"></iconify-icon>';
        $html .= '</a>';

        // Edit button
        $html .= '<a href="' . route('admin.services.edit', $service) . '" class="inline-flex items-center justify-center w-8 h-8 text-green-600 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 hover:text-green-700 transition-colors duration-200" title="' . __('Edit Service') . '">';
        $html .= '<iconify-icon icon="lucide:edit" class="w-4 h-4"></iconify-icon>';
        $html .= '</a>';

        // Delete button using system functionality
        $html .= '<div x-data="{ deleteModalOpen: false }">';
        $html .= '<button @click="deleteModalOpen = true" class="inline-flex items-center justify-center w-8 h-8 text-red-600 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 hover:text-red-700 transition-colors duration-200" title="' . __('Delete Service') . '">';
        $html .= '<iconify-icon icon="lucide:trash-2" class="w-4 h-4"></iconify-icon>';
        $html .= '</button>';

        // System confirm delete modal
        $html .= '<div x-cloak x-show="deleteModalOpen" x-transition.opacity.duration.200ms x-trap.inert.noscroll="deleteModalOpen" x-on:keydown.esc.window="deleteModalOpen = false" x-on:click.self="deleteModalOpen = false" class="fixed inset-0 z-50 flex items-center justify-center bg-black/20 p-4 backdrop-blur-md" role="dialog" aria-modal="true">';
        $html .= '<div x-show="deleteModalOpen" x-transition:enter="transition ease-out duration-200 delay-100" x-transition:enter-start="opacity-0 scale-50" x-transition:enter-end="opacity-100 scale-100" class="w-full max-w-md rounded-lg bg-white p-0 shadow-xl dark:bg-gray-800">';
        $html .= '<div class="flex items-center justify-between border-b border-gray-100 p-4 dark:border-gray-800">';
        $html .= '<h3 class="font-semibold tracking-wide text-gray-700 dark:text-white">' . __('Delete Service') . '</h3>';
        $html .= '<button x-on:click="deleteModalOpen = false" class="text-gray-400 hover:bg-gray-200 hover:text-gray-700 rounded-md p-1 dark:hover:bg-gray-600 dark:hover:text-white"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-width="1.4" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>';
        $html .= '</div>';
        $html .= '<div class="px-4 text-center">';
        $html .= '<p class="text-gray-500 dark:text-gray-300">' . __('Are you sure you want to delete this service?') . '</p>';
        $html .= '<p class="font-medium text-gray-900 dark:text-white mt-2">' . e($service->name) . '</p>';
        $html .= '<p class="text-sm text-gray-400 mt-1">' . __('This action cannot be undone.') . '</p>';
        $html .= '</div>';
        $html .= '<div class="flex items-center justify-end gap-3 border-t border-gray-100 p-4 dark:border-gray-800">';
        $html .= '<button type="button" x-on:click="deleteModalOpen = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:text-white dark:focus:ring-gray-700">' . __('No, Cancel') . '</button>';
        $html .= '<button type="button" wire:click="deleteItem(' . $service->id . ')" @click="deleteModalOpen = false" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-300 dark:focus:ring-red-800">' . __('Yes, Delete') . '</button>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '</div>';

        return $html;
    }
}
