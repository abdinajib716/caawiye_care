# Doctors Data Source Added to Service Form Builder

## Status: ✅ COMPLETED

Date: October 4, 2025
Feature: Added "Doctors" as a data source option in the service custom fields form builder

---

## User Request

When creating services with custom fields, the user wanted to be able to add a "Doctor Name" dropdown field that loads doctors from the database, similar to how the "Hospital" field works.

---

## What Was Added

### ✅ Doctors Data Source Option

Added "Doctors" as a selectable data source in the form builder, alongside the existing "Hospitals" option.

**Before**:
```
Data Source Options:
- Manual Options
- Hospitals
```

**After**:
```
Data Source Options:
- Manual Options
- Hospitals
- Doctors  ← NEW!
```

---

## Implementation Details

### 1. **Form Builder Component** (`resources/views/components/form-builder.blade.php`)

**Added "Doctors" option to data source dropdown**:

```blade
<!-- Data Source for Select Type -->
<div x-show="field.type === 'select'" class="mt-4">
    <label class="form-label">{{ __('Or Use Data Source') }}</label>
    <select x-model="field.data_source" @change="updateJson()" class="form-control">
        <option value="">{{ __('-- Manual Options --') }}</option>
        <option value="hospitals">{{ __('Hospitals') }}</option>
        <option value="doctors">{{ __('Doctors') }}</option>  ← NEW!
    </select>
    <div class="text-xs text-gray-400 mt-1">{{ __('Load options from database') }}</div>
</div>
```

**Updated Appointment Template** to include doctor field:

```javascript
appointment: [
    { key: 'appointment_type', label: 'Appointment Type', type: 'select', required: true, options: [...] },
    { key: 'patient_name', label: 'Patient Name', type: 'text', required: true, show_if: {...} },
    { key: 'hospital_id', label: 'Select Hospital', type: 'select', required: true, data_source: 'hospitals' },
    { key: 'doctor_id', label: 'Select Doctor', type: 'select', required: true, data_source: 'doctors' },  ← NEW!
    { key: 'appointment_time', label: 'Appointment Date & Time', type: 'datetime', required: true, validation: 'future' }
]
```

---

### 2. **ServiceDetailsStep Livewire Component** (`app/Livewire/OrderZone/ServiceDetailsStep.php`)

**Added Doctor model import**:

```php
use App\Models\Doctor;
use App\Models\Hospital;
use App\Models\Service;
```

**Load doctors from database**:

```php
public function render()
{
    $hospitals = Hospital::active()->orderBy('name')->get();
    $doctors = Doctor::active()->orderBy('name')->get();  // ← NEW!

    return view('livewire.order-zone.service-details-step', [
        'hospitals' => $hospitals,
        'doctors' => $doctors,  // ← NEW!
    ]);
}
```

---

### 3. **Service Details Step View** (`resources/views/livewire/order-zone/service-details-step.blade.php`)

**Added doctors rendering logic**:

```blade
@elseif($field['type'] === 'select')
    <select id="{{ $fieldKey }}" wire:model.live="fieldData.{{ $fieldKey }}" class="form-control">
        <option value="">{{ __('Select') }} {{ $field['label'] }}</option>
        
        @if(!empty($field['data_source']) && $field['data_source'] === 'hospitals')
            @foreach($hospitals as $hospital)
                <option value="{{ $hospital->id }}">{{ $hospital->name }}</option>
            @endforeach
        
        @elseif(!empty($field['data_source']) && $field['data_source'] === 'doctors')  ← NEW!
            @foreach($doctors as $doctor)
                <option value="{{ $doctor->id }}">{{ $doctor->name }}</option>
            @endforeach
        
        @elseif(!empty($field['options']))
            @foreach($field['options'] as $option)
                <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
            @endforeach
        @endif
    </select>
@endif
```

---

## How to Use

### Creating a Service with Doctor Field

1. **Go to Services** → Click "Add Service"

2. **Fill in basic information**:
   - Service Name
   - Description
   - Price
   - Status

3. **Enable Custom Fields**:
   - Check "Custom Fields Configuration"

4. **Add Doctor Field**:
   - Click "Add Field"
   - Set **Field Key**: `doctor_name` (or `doctor_id`)
   - Set **Field Label**: `Doctor Name`
   - Set **Field Type**: `Dropdown`
   - Check **Required Field** (if needed)
   - In **"Or Use Data Source"**: Select **"Doctors"** ← NEW OPTION!

5. **Save the service**

---

## Example Configuration

### Manual Configuration

```json
{
  "fields": [
    {
      "key": "doctor_name",
      "label": "Doctor Name",
      "type": "select",
      "required": true,
      "data_source": "doctors"
    }
  ]
}
```

### Using Appointment Template

The "Appointment Service" template now includes both Hospital and Doctor fields:

```json
{
  "fields": [
    {
      "key": "appointment_type",
      "label": "Appointment Type",
      "type": "select",
      "required": true,
      "options": [
        {"value": "self", "label": "Self"},
        {"value": "someone_else", "label": "Someone Else"}
      ]
    },
    {
      "key": "patient_name",
      "label": "Patient Name",
      "type": "text",
      "required": true,
      "show_if": {
        "field": "appointment_type",
        "value": "someone_else"
      }
    },
    {
      "key": "hospital_id",
      "label": "Select Hospital",
      "type": "select",
      "required": true,
      "data_source": "hospitals"
    },
    {
      "key": "doctor_id",
      "label": "Select Doctor",
      "type": "select",
      "required": true,
      "data_source": "doctors"
    },
    {
      "key": "appointment_time",
      "label": "Appointment Date & Time",
      "type": "datetime",
      "required": true,
      "validation": "future"
    }
  ]
}
```

---

## What Happens in Order Zone

When a customer selects a service with a doctor field in the Order Zone:

1. **Service Selection**: Customer selects the service
2. **Custom Fields Appear**: The doctor dropdown appears
3. **Doctors Loaded**: All active doctors are loaded from the database
4. **Customer Selects**: Customer chooses a doctor from the dropdown
5. **Data Saved**: The selected doctor ID is saved with the order

---

## UI Flow

### Service Creation Form

```
┌─────────────────────────────────────────────────────────┐
│ Custom Fields Configuration                             │
├─────────────────────────────────────────────────────────┤
│                                                          │
│ Field 5                                    [select] [×]  │
│ ┌────────────────────────────────────────────────────┐  │
│ │ Field Key:        doctor_name                      │  │
│ │ Field Label:      Doctor Name                      │  │
│ │ Field Type:       [Dropdown ▼]                     │  │
│ │ ☑ Required Field                                   │  │
│ │                                                     │  │
│ │ Or Use Data Source:                                │  │
│ │ [Doctors ▼]  ← NEW OPTION!                         │  │
│ │ Load options from database                         │  │
│ └────────────────────────────────────────────────────┘  │
│                                                          │
│ [+ Add Field]                                            │
└─────────────────────────────────────────────────────────┘
```

### Order Zone - Service Details

```
┌─────────────────────────────────────────────────────────┐
│ Service Details                                          │
├─────────────────────────────────────────────────────────┤
│                                                          │
│ Doctor Name *                                            │
│ ┌────────────────────────────────────────────────────┐  │
│ │ Select Doctor Name                              ▼ │  │
│ ├────────────────────────────────────────────────────┤  │
│ │ Dr Najib                                           │  │
│ │ Dr Ahmed Hassan                                    │  │
│ │ Dr Fatima Mohamed                                  │  │
│ │ Dr Omar Ali                                        │  │
│ └────────────────────────────────────────────────────┘  │
│                                                          │
└─────────────────────────────────────────────────────────┘
```

---

## Files Changed

### Modified (3 files):

1. **resources/views/components/form-builder.blade.php**
   - Added "Doctors" option to data source dropdown
   - Updated appointment template to include doctor field

2. **app/Livewire/OrderZone/ServiceDetailsStep.php**
   - Added Doctor model import
   - Load active doctors in render() method
   - Pass doctors to view

3. **resources/views/livewire/order-zone/service-details-step.blade.php**
   - Added conditional rendering for doctors data source
   - Displays doctor dropdown when data_source is 'doctors'

---

## Benefits

### 1. **Flexibility**
- ✅ Can create services that require doctor selection
- ✅ Can create services that require hospital selection
- ✅ Can create services that require both
- ✅ Can still use manual options for other dropdowns

### 2. **Consistency**
- ✅ Same UI/UX as hospital field
- ✅ Same configuration process
- ✅ Same data loading mechanism

### 3. **Real-Time Data**
- ✅ Always loads current active doctors
- ✅ Automatically includes new doctors
- ✅ Excludes inactive doctors

### 4. **Use Cases**
- ✅ Doctor appointments
- ✅ Specialist consultations
- ✅ Follow-up visits
- ✅ Medical procedures
- ✅ Lab tests with specific doctors

---

## Data Sources Available

| Data Source | Description | Loads From |
|-------------|-------------|------------|
| **Manual Options** | Custom dropdown options | Configured manually |
| **Hospitals** | Hospital selection | `hospitals` table (active only) |
| **Doctors** | Doctor selection | `doctors` table (active only) |

---

## Testing Checklist

### Service Creation
- [x] "Doctors" option appears in data source dropdown
- [x] Can select "Doctors" as data source
- [x] Field configuration saves correctly
- [x] Appointment template includes doctor field

### Order Zone
- [x] Doctor dropdown appears for services with doctor field
- [x] Dropdown loads all active doctors
- [x] Doctor names display correctly
- [x] Can select a doctor
- [x] Selected doctor ID saves with order
- [x] Inactive doctors don't appear in dropdown

---

## Example Services That Can Use This

### 1. **Doctor Appointment Service**
```json
{
  "fields": [
    {
      "key": "hospital_id",
      "label": "Select Hospital",
      "type": "select",
      "required": true,
      "data_source": "hospitals"
    },
    {
      "key": "doctor_id",
      "label": "Select Doctor",
      "type": "select",
      "required": true,
      "data_source": "doctors"
    },
    {
      "key": "appointment_time",
      "label": "Appointment Date & Time",
      "type": "datetime",
      "required": true,
      "validation": "future"
    }
  ]
}
```

### 2. **Specialist Consultation**
```json
{
  "fields": [
    {
      "key": "doctor_id",
      "label": "Select Specialist",
      "type": "select",
      "required": true,
      "data_source": "doctors"
    },
    {
      "key": "consultation_reason",
      "label": "Reason for Consultation",
      "type": "textarea",
      "required": true
    }
  ]
}
```

### 3. **Lab Test with Doctor**
```json
{
  "fields": [
    {
      "key": "test_type",
      "label": "Test Type",
      "type": "select",
      "required": true,
      "options": [
        {"value": "blood", "label": "Blood Test"},
        {"value": "urine", "label": "Urine Test"}
      ]
    },
    {
      "key": "doctor_id",
      "label": "Referring Doctor",
      "type": "select",
      "required": false,
      "data_source": "doctors"
    }
  ]
}
```

---

## Summary

| Feature | Status | Details |
|---------|--------|---------|
| Doctors data source option | ✅ Added | Available in form builder dropdown |
| Load active doctors | ✅ Implemented | Loads from database in Order Zone |
| Display doctor dropdown | ✅ Working | Shows in service details step |
| Appointment template updated | ✅ Done | Includes doctor field by default |
| Consistent with hospitals | ✅ Yes | Same pattern and behavior |

---

## Result

✅ **You can now add "Doctor Name" fields to services!**

When creating a service:
1. Add a dropdown field
2. Select "Doctors" as the data source
3. The field will automatically load all active doctors from the database
4. Customers can select a doctor when ordering the service

**The feature is fully functional and ready to use!** 🎉

---

*Last Updated: October 4, 2025*
*Status: COMPLETED*

