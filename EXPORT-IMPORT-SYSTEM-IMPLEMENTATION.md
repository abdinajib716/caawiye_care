# 🎯 Export/Import System Implementation Guide

## ✅ Progress Status

### Completed Tasks
1. ✅ **Datatable Configuration** - Configured 5 modules with export/import enabled
   - Customer: PDF/Print disabled, Export/Import enabled
   - Hospital: PDF/Print disabled, Export/Import enabled  
   - Provider: PDF/Print disabled, Export/Import enabled
   - LabTest (Services): PDF/Print disabled, Export/Import enabled
   - ScanImagingService (Services): PDF/Print disabled, Export/Import enabled

2. ✅ **Core Services Created**
   - `ExcelExportService.php` - Native PHP CSV export (Excel compatible)
   - `ExcelImportService.php` - CSV import with validation

### In Progress
3. 🔄 **Module-Specific Configurations**
4. 🔄 **Import Modal Component**
5. 🔄 **Controller Methods**
6. 🔄 **Routes**

---

## 📋 Export Configuration by Module

### Customer Export Fields
```php
Headers: ['ID', 'Name', 'Email', 'Phone', 'Country Code', 'Address', 'Status', 'Created At']

Sample Data:
1, John Doe, john@example.com, 617123456, +252, Mogadishu Somalia, active, 2025-01-15
```

### Hospital Export Fields
```php
Headers: ['ID', 'Name', 'Email', 'Phone', 'Address', 'Status', 'Created At']

Sample Data:
1, Central Hospital, info@central.com, 617123456, Main Street Mogadishu, active, 2025-01-15
```

### Provider Export Fields
```php
Headers: ['ID', 'Name', 'Email', 'Phone', 'Status', 'Created At']

Sample Data:
1, Lab Services Ltd, lab@services.com, 617123456, active, 2025-01-15
```

### Lab Test Export Fields
```php
Headers: ['ID', 'Name', 'Description', 'Cost', 'Provider', 'Status', 'Created At']

Sample Data:
1, Blood Test, Complete Blood Count, 25.00, Lab Services Ltd, active, 2025-01-15
```

### Scan/Imaging Service Export Fields
```php
Headers: ['ID', 'Service Name', 'Description', 'Cost', 'Provider', 'Status', 'Created At']

Sample Data:
1, X-Ray Chest, Chest X-Ray Examination, 50.00, Imaging Center, active, 2025-01-15
```

---

## 🔒 Import Validation Rules

### Customer Import Validation
```php
'Name' => 'required|string|max:255',
'Email' => 'nullable|email|unique:customers,email',
'Phone' => 'required|string|max:20',
'Country Code' => 'required|string|max:5',
'Address' => 'nullable|string',
'Status' => 'required|in:active,inactive'
```

### Hospital Import Validation
```php
'Name' => 'required|string|max:255|unique:hospitals,name',
'Email' => 'required|email|unique:hospitals,email',
'Phone' => 'required|string|max:20',
'Address' => 'required|string',
'Status' => 'required|in:active,inactive'
```

### Provider Import Validation
```php
'Name' => 'required|string|max:255|unique:providers,name',
'Email' => 'required|email|unique:providers,email',
'Phone' => 'required|string|max:20',
'Status' => 'required|in:active,inactive'
```

### Service Import Validation (Lab Test & Scan/Imaging)
```php
'Service Name' => 'required|string|max:255',
'Description' => 'nullable|string',
'Cost' => 'required|numeric|min:0',
'Provider' => 'required|exists:providers,name', 
'Status' => 'required|in:active,inactive'
```

---

## 🎨 Import Modal Design

### Modal Features
1. **Header**: Module name + Import instructions
2. **Upload Area**: Drag & drop or click to upload CSV
3. **Sample Template Download**: Button to download template
4. **Validation Info**: Required fields and format
5. **Progress Indicator**: During import
6. **Results Display**: Success/Error summary

### Modal HTML Structure
```html
<div class="import-modal">
    <h2>Import {ModuleName}</h2>
    
    <div class="instructions">
        <h3>Instructions</h3>
        <ol>
            <li>Download the sample template below</li>
            <li>Fill in your data following the format</li>
            <li>Upload the completed file</li>
            <li>Review and confirm the import</li>
        </ol>
    </div>
    
    <button class="download-template">Download Sample Template</button>
    
    <div class="upload-area">
        <input type="file" accept=".csv" />
        <p>Drag & drop or click to upload CSV</p>
    </div>
    
    <div class="validation-rules">
        <h3>Required Fields</h3>
        <ul>
            <li><strong>Name</strong>: Required, max 255 characters</li>
            <li><strong>Email</strong>: Required, valid email format</li>
            <!-- More fields... -->
        </ul>
    </div>
    
    <div class="import-actions">
        <button class="cancel">Cancel</button>
        <button class="import">Import Data</button>
    </div>
</div>
```

---

## 🚀 Next Implementation Steps

### Step 3-4: Create Export/Import Classes (Per Module)
Create these files:
- `app/Exports/CustomerExport.php`
- `app/Imports/CustomerImport.php`
- `app/Exports/HospitalExport.php`
- `app/Imports/HospitalImport.php`
- (Similar for Provider, LabTest, ScanImagingService)

### Step 5: Create Import Modal Component
- `resources/views/components/import-modal.blade.php`
- `resources/js/components/import-handler.js`

### Step 6: Generate Sample Templates
Sample template generation in each controller

### Step 7: Add Controller Methods
Add to each controller:
- `export()` - Export data
- `import()` - Import data  
- `downloadSampleTemplate()` - Download template

### Step 8: Add Routes
```php
// Customers
Route::get('customers/export', [CustomerController::class, 'export'])->name('admin.customers.export');
Route::post('customers/import', [CustomerController::class, 'import'])->name('admin.customers.import');
Route::get('customers/sample-template', [CustomerController::class, 'downloadSampleTemplate'])->name('admin.customers.sample-template');

// Repeat for other modules...
```

---

## 💡 Professional Recommendations

### 1. **Data Integrity**
- ✅ Always validate before import
- ✅ Use database transactions for bulk imports
- ✅ Provide detailed error reporting
- ✅ Log all import activities

### 2. **Performance**
- ✅ Process large files in chunks (1000 rows at a time)
- ✅ Use queue jobs for imports > 5000 rows
- ✅ Show progress bar for user feedback
- ✅ Set reasonable file size limits (5MB max)

### 3. **User Experience**
- ✅ Provide clear instructions
- ✅ Offer sample templates
- ✅ Show validation errors with row numbers
- ✅ Allow export of current filtered data
- ✅ Confirm before overwriting data

### 4. **Security**
- ✅ Validate file types (CSV only)
- ✅ Sanitize all input data
- ✅ Implement rate limiting on imports
- ✅ Require appropriate permissions
- ✅ Scan for malicious content

### 5. **Error Handling**
- ✅ Gracefully handle malformed CSV
- ✅ Provide downloadable error reports
- ✅ Allow partial imports (skip errors, continue)
- ✅ Rollback on critical errors

### 6. **Best Practices**
- ✅ UTF-8 BOM for Excel compatibility
- ✅ Consistent date formats (Y-m-d)
- ✅ Clear column headers
- ✅ Include example rows in templates
- ✅ Version control for import formats

---

## 📊 Testing Checklist

- [ ] Export with no data (empty CSV)
- [ ] Export with filters applied
- [ ] Export large datasets (1000+ rows)
- [ ] Import valid sample template
- [ ] Import with missing required fields
- [ ] Import with duplicate data
- [ ] Import with invalid formats
- [ ] Import large file (5000+ rows)
- [ ] Cancel import mid-process
- [ ] Download sample template for each module

---

## 🎯 Success Criteria

1. **Export Functionality**
   - ✅ Exports all visible/filtered data
   - ✅ Maintains column structure
   - ✅ Opens correctly in Excel
   - ✅ Downloads with descriptive filename

2. **Import Functionality**
   - ✅ Validates all fields
   - ✅ Shows detailed error messages
   - ✅ Imports valid data successfully
   - ✅ Preserves data integrity
   - ✅ Provides import summary

3. **User Interface**
   - ✅ Clear buttons in datatable
   - ✅ Intuitive import modal
   - ✅ Helpful instructions
   - ✅ Progress indicators
   - ✅ Error feedback

---

**Status**: 🔄 **In Progress** - Core services ready, module implementations pending
