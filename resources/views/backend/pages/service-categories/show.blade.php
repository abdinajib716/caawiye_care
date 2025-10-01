<x-layouts.backend-layout>
    <x-slot name="title">{{ $breadcrumbs['title'] }}</x-slot>

    <x-breadcrumbs :breadcrumbs="$breadcrumbs" />

    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    @if($category->is_active)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                            {{ __('Active') }}
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                            {{ __('Inactive') }}
                        </span>
                    @endif
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                <x-buttons.button
                    variant="secondary"
                    as="a"
                    href="{{ route('admin.service-categories.edit', $category) }}"
                    icon="lucide:edit"
                >
                    {{ __('Edit Category') }}
                </x-buttons.button>

                <x-buttons.button
                    variant="secondary"
                    as="a"
                    href="{{ route('admin.service-categories.index') }}"
                    icon="lucide:arrow-left"
                >
                    {{ __('Back to Categories') }}
                </x-buttons.button>
            </div>
        </div>

        <!-- Category Information -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Information -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Category Details -->
                <x-card>
                    <x-slot name="header">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('Category Details') }}</h3>
                    </x-slot>

                    <div class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Name') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $category->name }}</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Sort Order') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $category->sort_order }}</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Parent Category') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    @if($category->parent)
                                        <a href="{{ route('admin.service-categories.show', $category->parent) }}" class="text-blue-600 hover:text-blue-500 dark:text-blue-400">
                                            {{ $category->parent->name }}
                                        </a>
                                    @else
                                        <span class="text-gray-500 dark:text-gray-400">{{ __('Root Category') }}</span>
                                    @endif
                                </dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Full Path') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $category->full_path }}</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Created') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $category->created_at->format('M d, Y \a\t g:i A') }}</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Last Updated') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $category->updated_at->format('M d, Y \a\t g:i A') }}</dd>
                            </div>
                        </div>

                        @if($category->description)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Description') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $category->description }}</dd>
                        </div>
                        @endif
                    </div>
                </x-card>

                <!-- Subcategories -->
                @if($category->children->count() > 0)
                <x-card>
                    <x-slot name="header">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('Subcategories') }} ({{ $category->children->count() }})</h3>
                    </x-slot>

                    <div class="space-y-3">
                        @foreach($category->children as $child)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg dark:bg-gray-800">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    @if($child->is_active)
                                        <div class="w-2 h-2 bg-green-400 rounded-full"></div>
                                    @else
                                        <div class="w-2 h-2 bg-red-400 rounded-full"></div>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $child->name }}</p>
                                    @if($child->description)
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ Str::limit($child->description, 60) }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $child->services_count }} {{ __('services') }}</span>
                                <a href="{{ route('admin.service-categories.show', $child) }}" class="text-blue-600 hover:text-blue-500 dark:text-blue-400">
                                    <iconify-icon icon="lucide:external-link" class="w-4 h-4"></iconify-icon>
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </x-card>
                @endif

                <!-- Services in this Category -->
                @if($category->services->count() > 0)
                <x-card>
                    <x-slot name="header">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('Services in this Category') }} ({{ $category->services->count() }})</h3>
                    </x-slot>

                    <div class="space-y-3">
                        @foreach($category->services->take(10) as $service)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg dark:bg-gray-800">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="w-2 h-2 bg-{{ $service->status_color }}-400 rounded-full"></div>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $service->name }}</p>
                                    @if($service->short_description)
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ Str::limit($service->short_description, 60) }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="text-xs font-medium text-gray-900 dark:text-white">{{ $service->formatted_price }}</span>
                                <a href="{{ route('admin.services.show', $service) }}" class="text-blue-600 hover:text-blue-500 dark:text-blue-400">
                                    <iconify-icon icon="lucide:external-link" class="w-4 h-4"></iconify-icon>
                                </a>
                            </div>
                        </div>
                        @endforeach

                        @if($category->services->count() > 10)
                        <div class="text-center pt-3">
                            <a href="{{ route('admin.services.index', ['category_id' => $category->id]) }}" class="text-sm text-blue-600 hover:text-blue-500 dark:text-blue-400">
                                {{ __('View all :count services', ['count' => $category->services->count()]) }}
                            </a>
                        </div>
                        @endif
                    </div>
                </x-card>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Statistics -->
                <x-card>
                    <x-slot name="header">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('Statistics') }}</h3>
                    </x-slot>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Total Services') }}</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $category->services_count ?? 0 }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Active Services') }}</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $category->active_services_count ?? 0 }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Subcategories') }}</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $category->children->count() }}</span>
                        </div>
                    </div>
                </x-card>

                <!-- Quick Actions -->
                <x-card>
                    <x-slot name="header">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('Quick Actions') }}</h3>
                    </x-slot>

                    <div class="space-y-3">
                        <a href="{{ route('admin.service-categories.edit', $category) }}" class="flex items-center p-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800">
                            <iconify-icon icon="lucide:edit" class="w-4 h-4 mr-3"></iconify-icon>
                            {{ __('Edit Category') }}
                        </a>
                        
                        <a href="{{ route('admin.services.create', ['category_id' => $category->id]) }}" class="flex items-center p-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800">
                            <iconify-icon icon="lucide:plus" class="w-4 h-4 mr-3"></iconify-icon>
                            {{ __('Add Service to Category') }}
                        </a>
                        
                        <a href="{{ route('admin.service-categories.create', ['parent_id' => $category->id]) }}" class="flex items-center p-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800">
                            <iconify-icon icon="lucide:folder-plus" class="w-4 h-4 mr-3"></iconify-icon>
                            {{ __('Add Subcategory') }}
                        </a>
                        
                        @if($category->services->count() > 0)
                        <a href="{{ route('admin.services.index', ['category_id' => $category->id]) }}" class="flex items-center p-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800">
                            <iconify-icon icon="lucide:list" class="w-4 h-4 mr-3"></iconify-icon>
                            {{ __('View All Services') }}
                        </a>
                        @endif
                    </div>
                </x-card>
            </div>
        </div>
    </div>

</x-layouts.backend-layout>
