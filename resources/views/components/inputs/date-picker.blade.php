@props([
    'label' => null,
    'name' => null,
    'value' => null,
    'placeholder' => '',
    'hint' => null,
    'required' => false,
    'disabled' => false,
    'min' => null,
    'max' => null,
])
<div>
    @if($label)
        <label class="form-label" for="{{ $name }}">{{ $label }}</label>
    @endif
    <div class="relative">
        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none z-10">
            <iconify-icon icon="lucide:calendar" class="text-gray-400 dark:text-gray-500"></iconify-icon>
        </div>
        <input
            type="text"
            name="{{ $name }}"
            id="{{ $name }}"
            value="{{ old($name, $value) }}"
            placeholder="{{ $placeholder }}"
            @if($required) required @endif
            @if($disabled) disabled @endif
            @if($min) min="{{ $min }}" @endif
            @if($max) max="{{ $max }}" @endif
            {{ $attributes->class(['form-control !ps-10', 'datepicker']) }}
            x-data
            x-init="
                const fp = flatpickr($el, {
                    enableTime: false,
                    dateFormat: 'Y-m-d',
                    altInput: true,
                    altFormat: 'F j, Y',
                    clickOpens: true,
                    allowInput: false,
                    onReady: function(selectedDates, dateStr, instance) {
                        if (instance.altInput) {
                            instance.altInput.style.cursor = 'pointer';
                            instance.altInput.readOnly = true;  // Prevent manual editing
                            instance.altInput.addEventListener('click', function(e) {
                                e.preventDefault();
                                instance.open();
                            });
                            // Prevent keyboard input
                            instance.altInput.addEventListener('keydown', function(e) {
                                e.preventDefault();
                                instance.open();
                            });
                            // Open on focus
                            instance.altInput.addEventListener('focus', function() {
                                instance.open();
                            });
                        }
                        // Hide the real input
                        instance.input.style.display = 'none';
                    }
                });
                // Additional safeguard
                $el.addEventListener('focus', function() {
                    fp.open();
                });
            "
            autocomplete="off"
        >
    </div>
    @if($hint)
        <div class="text-xs text-gray-400 mt-1">{{ $hint }}</div>
    @endif
</div>
