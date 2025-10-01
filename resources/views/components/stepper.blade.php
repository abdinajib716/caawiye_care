@props([
    'steps' => [],
    'currentStep' => 1,
])

<div class="w-full py-4">
    <div class="flex items-center justify-center">
        @foreach($steps as $index => $step)
            @php
                $stepNumber = $index + 1;
                $isActive = $stepNumber === $currentStep;
                $isCompleted = $stepNumber < $currentStep;
                $isPending = $stepNumber > $currentStep;
            @endphp

            <!-- Step Item -->
            <div class="flex items-center {{ $loop->last ? '' : 'flex-1' }}">
                <!-- Step Circle -->
                <div class="flex flex-col items-center">
                    <div class="relative flex items-center justify-center">
                        @if($isCompleted)
                            <!-- Completed Step -->
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-green-500 text-white shadow">
                                <iconify-icon icon="lucide:check" class="h-5 w-5"></iconify-icon>
                            </div>
                        @elseif($isActive)
                            <!-- Active Step -->
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-500 text-white shadow ring-2 ring-blue-100 dark:ring-blue-900">
                                <iconify-icon icon="lucide:edit-3" class="h-5 w-5"></iconify-icon>
                            </div>
                        @else
                            <!-- Pending Step -->
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-200 text-gray-500 dark:bg-gray-700 dark:text-gray-400">
                                <span class="text-base font-semibold">{{ $stepNumber }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Step Label -->
                    <div class="mt-2 text-center">
                        <p class="text-xs font-medium {{ $isActive ? 'text-blue-600 dark:text-blue-400' : ($isCompleted ? 'text-green-600 dark:text-green-400' : 'text-gray-500 dark:text-gray-400') }}">
                            {{ $step['label'] }}
                        </p>
                        @if(isset($step['description']))
                            <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                                {{ $step['description'] }}
                            </p>
                        @endif
                    </div>
                </div>

                <!-- Connector Line -->
                @if(!$loop->last)
                    <div class="mx-3 h-0.5 flex-1 {{ $stepNumber < $currentStep ? 'bg-green-500' : 'bg-gray-200 dark:bg-gray-700' }}"></div>
                @endif
            </div>
        @endforeach
    </div>
</div>

