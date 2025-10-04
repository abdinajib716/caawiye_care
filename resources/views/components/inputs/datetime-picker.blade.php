@props([
    'id' => 'datetime-picker',
    'name' => 'datetime',
    'label' => 'Date and Time',
    'value' => '',
    'required' => false,
    'placeholder' => 'Select date and time',
    'minDate' => null,
    'maxDate' => null,
    'enableTime' => true,
    'dateFormat' => 'Y-m-d H:i',
    'altFormat' => 'F j, Y at h:i K',
    'showAltFormat' => true,
    'class' => '',
    'helpText' => '',
])

<div x-data="{
    init() {
        const options = {
            enableTime: {{ $enableTime ? 'true' : 'false' }},
            dateFormat: '{{ $dateFormat }}',
            altInput: {{ $showAltFormat ? 'true' : 'false' }},
            altFormat: '{{ $altFormat }}',
            time_24hr: false,
            defaultDate: '{{ $value }}',
            minDate: {{ $minDate ? '\'' . $minDate . '\'' : 'null' }},
            maxDate: {{ $maxDate ? '\'' . $maxDate . '\'' : 'null' }},
            disableMobile: true,
            static: true,
            position: 'auto',
            clickOpens: true,  // Ensure calendar opens on click
            allowInput: false,  // Prevent manual input, force picker usage
            locale: {
                firstDayOfWeek: 1
            },
            onChange: function(selectedDates, dateStr, instance) {
                // Dispatch an input event to ensure Alpine.js and other listeners are notified
                instance.element.dispatchEvent(new Event('input', { bubbles: true }));
            },
            // Fix for the form validation issue with unnamed inputs
            onReady: function(selectedDates, dateStr, instance) {
                // Add names to hour and minute inputs to prevent validation errors
                const hourInput = instance.hourElement;
                const minuteInput = instance.minuteElement;

                if (hourInput) {
                    hourInput.name = '{{ $name }}_hour';
                    hourInput.setAttribute('form', 'none'); // Prevent it from being included in form submission
                }

                if (minuteInput) {
                    minuteInput.name = '{{ $name }}_minute';
                    minuteInput.setAttribute('form', 'none'); // Prevent it from being included in form submission
                }

                // Ensure the altInput (display input) is always clickable and readonly
                if (instance.altInput) {
                    instance.altInput.style.cursor = 'pointer';
                    instance.altInput.readOnly = true;  // Prevent manual editing
                    instance.altInput.addEventListener('click', function(e) {
                        e.preventDefault();
                        instance.open();
                    });
                    // Prevent keyboard input on altInput
                    instance.altInput.addEventListener('keydown', function(e) {
                        e.preventDefault();
                        instance.open();
                    });
                    // Prevent focus from allowing input
                    instance.altInput.addEventListener('focus', function() {
                        instance.open();
                    });
                }

                // Hide the real input completely
                instance.input.style.display = 'none';
            }
        };

        const fp = flatpickr(this.$refs.datetimePicker, options);

        // Additional safeguard: reopen calendar if user tries to edit
        this.$refs.datetimePicker.addEventListener('focus', function() {
            fp.open();
        });
    }
}">
    @if ($label)
        <label for="{{ $id }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            {{ $label }}
            @if ($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <div class="relative">
        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none z-10">
            <iconify-icon icon="lucide:calendar" class="text-gray-400 dark:text-gray-500"></iconify-icon>
        </div>
        <input x-ref="datetimePicker" type="text" id="{{ $id }}" name="{{ $name }}"
            value="{{ $value ?: now()->format($dateFormat) }}" placeholder="{{ $placeholder }}"
            {{ $required ? 'required' : '' }} {{ $attributes->merge(['class' => 'form-control !ps-10 ' . $class]) }} />
    </div>

    @if ($helpText)
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-300">{{ $helpText }}</p>
    @endif
</div>
