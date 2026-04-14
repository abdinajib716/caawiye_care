<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <x-slot name="breadcrumbsData">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-xl font-semibold text-gray-700 dark:text-white/90">
                {{ __('Provider Payout Management') }}
            </h2>
        </div>
        <x-messages />
    </x-slot>

    @livewire('provider-payout-manager')
</x-layouts.backend-layout>
