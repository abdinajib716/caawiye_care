# AI Coding Guidelines

## Purpose
This document provides comprehensive guidelines for implementing new features in the codebase while maintaining consistency, preventing duplication, and avoiding common integration issues.

---

## 🎯 Core Principles

### 1. **Study First, Code Later**
Never write code before thoroughly understanding existing patterns. Analysis time is never wasted—it prevents hours of debugging.

### 2. **Pattern Consistency Over Innovation**
The system already works. Your job is to extend it, not reinvent it. Follow existing patterns exactly.

### 3. **Reuse Before Create**
If similar functionality exists, adapt it. Don't create new patterns unless absolutely necessary and formally approved.

### 4. **Small, Verified Steps**
Implement incrementally. Verify each step matches existing patterns before proceeding.

---

## 📋 Pre-Implementation Checklist

Before writing any code for a new feature:

- [ ] Identify 2-3 similar existing features in the codebase
- [ ] Document their structure, patterns, and conventions
- [ ] List all files involved in those features
- [ ] Note naming conventions used
- [ ] Identify which traits/interfaces they use (or don't use)
- [ ] Document method signatures and return types
- [ ] Note property visibility (public/protected/private)
- [ ] Check database schema patterns
- [ ] Review routing patterns
- [ ] Examine permission patterns
- [ ] Study service layer patterns
- [ ] Review view/component patterns

---

## 🔍 Pattern Analysis Process

### Step 1: Identify Reference Implementations
Find 2-3 existing features that are most similar to what you're building.

**Example:**
```
Building: Payment Transaction Management
Reference 1: Order Management
Reference 2: Customer Management
Reference 3: Service Category Management
```

### Step 2: Document File Structure
List all files involved in the reference implementations:

```
Reference Feature: [Name]
├── Model: app/Models/[Name].php
├── Controller: app/Http/Controllers/Backend/[Name]Controller.php
├── Service: app/Services/[Name]Service.php
├── Datatable: app/Livewire/Datatable/[Name]Datatable.php
├── Views:
│   ├── resources/views/backend/pages/[name]/index.blade.php
│   ├── resources/views/backend/pages/[name]/show.blade.php
│   └── resources/views/backend/pages/[name]/create.blade.php
├── Migration: database/migrations/[timestamp]_create_[names]_table.php
├── Seeder: database/seeders/[Name]PermissionSeeder.php
└── Routes: routes/web.php (admin.[names].*)
```

### Step 3: Document Code Patterns

#### A. Class Structure Pattern
```php
// Does the reference class use traits?
// If NO, don't use traits in your implementation
// If YES, use the EXACT same traits

// Example from reference:
class ReferenceClass extends BaseClass
{
    // NO traits used
    
    public string $property1;
    public array $property2 = [];
    
    public function method1(): ReturnType { }
    public function method2(): ReturnType { }
}

// Your implementation should match:
class YourClass extends BaseClass
{
    // NO traits - match the reference
    
    public string $property1;
    public array $property2 = [];
    
    public function method1(): ReturnType { }
    public function method2(): ReturnType { }
}
```

#### B. Method Signature Pattern
Document exact method signatures from reference:

```php
// Reference methods:
public function query(): Builder { }
public function getRoutes(): array { }
public function getPermissions(): array { }
protected function getHeaders(): array { }

// Your methods MUST match exactly:
// - Same visibility (public/protected/private)
// - Same return type
// - Same parameter types
```

#### C. Property Definition Pattern
```php
// Reference properties:
public string $model = Model::class;
public bool $showFilters = true;
public array $relationships = ['relation1', 'relation2'];
public array $searchableColumns = ['col1', 'col2'];

// Your properties MUST match:
// - Same visibility
// - Same type hints
// - Same default values structure
```

---

## 🏗️ Implementation Rules by Layer

### 1. Database Layer

#### Migrations
- [ ] Follow existing table naming conventions (singular/plural)
- [ ] Use same column types as similar tables
- [ ] Include same standard columns (id, timestamps, soft deletes if applicable)
- [ ] Use same foreign key naming pattern
- [ ] Use same index naming pattern

#### Models
- [ ] Extend the same base model as similar features
- [ ] Use same fillable/guarded pattern
- [ ] Define relationships using same pattern
- [ ] Use same casting pattern
- [ ] Include same traits (SoftDeletes, HasFactory, etc.) as similar models

**Checklist:**
```php
- [ ] namespace matches pattern
- [ ] extends correct base class
- [ ] uses same traits as similar models
- [ ] $fillable or $guarded defined consistently
- [ ] $casts defined if needed
- [ ] relationships follow naming convention
- [ ] accessor/mutator patterns match
```

### 2. Service Layer

#### Service Classes
- [ ] Place in same directory as similar services
- [ ] Use same constructor injection pattern
- [ ] Follow same method naming convention
- [ ] Use same transaction handling pattern
- [ ] Return same data structures

**Checklist:**
```php
- [ ] namespace matches pattern
- [ ] constructor dependencies match pattern
- [ ] method names follow convention (get*, create*, update*, delete*)
- [ ] uses DB transactions where similar services do
- [ ] returns same types (Model, Collection, array, etc.)
- [ ] error handling matches pattern
```

### 3. Controller Layer

#### Controllers
- [ ] Extend same base controller
- [ ] Use same method names (index, show, create, store, edit, update, destroy)
- [ ] Follow same authorization pattern
- [ ] Return same view structure
- [ ] Pass same data structure to views

**Checklist:**
```php
- [ ] namespace matches pattern
- [ ] extends correct base controller
- [ ] uses same middleware pattern
- [ ] method signatures match RESTful pattern
- [ ] authorization checks match pattern (policies, gates, permissions)
- [ ] view names follow convention
- [ ] breadcrumbs structure matches
- [ ] flash messages follow pattern
- [ ] redirects follow pattern
```

### 4. Livewire Component Layer

#### Datatable Components
- [ ] Extend same base datatable class
- [ ] DO NOT use traits if reference doesn't use them
- [ ] Define same properties with same visibility
- [ ] Implement same required methods
- [ ] Use same query builder pattern

**Critical Checklist:**
```php
- [ ] Check if reference uses traits (if NO, don't use any)
- [ ] public string $model defined
- [ ] public array $relationships defined (if reference has it)
- [ ] public array $searchableColumns defined
- [ ] public array $filterableColumns defined
- [ ] public function query(): Builder implemented
- [ ] public function getRoutes(): array implemented
- [ ] public function getPermissions(): array implemented
- [ ] public function getModelNameSingular(): string implemented
- [ ] public function getModelNamePlural(): string implemented
- [ ] public function getSearchbarPlaceholder(): string implemented
- [ ] protected function getHeaders(): array implemented
- [ ] render methods for custom columns implemented
```

#### Other Livewire Components
- [ ] Follow same property definition pattern
- [ ] Use same lifecycle hook pattern
- [ ] Follow same event handling pattern
- [ ] Use same validation pattern

### 5. View Layer

#### Blade Templates
- [ ] Use same layout component
- [ ] Follow same section structure
- [ ] Use same component naming pattern
- [ ] Follow same styling pattern (Tailwind classes)
- [ ] Use same icon system

**Checklist:**
```blade
- [ ] extends/uses correct layout
- [ ] breadcrumbs passed correctly
- [ ] sections match pattern (header, content, scripts)
- [ ] components use same naming (<x-layouts.*, <x-components.*)
- [ ] Tailwind classes match existing patterns
- [ ] icons use same system (iconify-icon, lucide set)
- [ ] forms follow same structure
- [ ] tables follow same structure
- [ ] modals follow same structure
```

### 6. Route Layer

#### Route Definitions
- [ ] Group routes same way as similar features
- [ ] Use same naming convention
- [ ] Apply same middleware
- [ ] Follow same prefix pattern

**Checklist:**
```php
- [ ] routes grouped correctly (admin, api, etc.)
- [ ] middleware matches pattern
- [ ] route names follow convention (admin.resource.action)
- [ ] parameters follow convention
- [ ] only define routes that will be implemented
```

### 7. Permission Layer

#### Permission Definitions
- [ ] Follow same naming convention
- [ ] Use same guard
- [ ] Group same way
- [ ] Seed same way

**Checklist:**
```php
- [ ] permission names follow pattern (resource.action)
- [ ] guard_name matches ('web', 'api', etc.)
- [ ] group_name matches pattern
- [ ] seeder follows same structure
- [ ] assigned to same roles as similar permissions
```

---

## ⚠️ Common Pitfalls to Avoid

### 1. Trait Usage Mismatch
**Problem:** Using traits when reference implementation doesn't
**Solution:** Check if similar classes use traits. If not, don't use them.

```php
// ❌ WRONG: Reference doesn't use traits, but you do
class YourClass extends Base
{
    use SomeTrait; // Reference doesn't have this!
}

// ✅ CORRECT: Match the reference exactly
class YourClass extends Base
{
    // No traits, just like reference
}
```

### 2. Property Visibility Mismatch
**Problem:** Defining properties with different visibility than reference
**Solution:** Match visibility exactly (public/protected/private)

```php
// Reference has:
public array $relationships = [];

// ❌ WRONG:
protected array $relationships = [];

// ✅ CORRECT:
public array $relationships = [];
```

### 3. Missing Required Methods
**Problem:** Not implementing all methods that reference implements
**Solution:** Document all methods from reference and implement them all

### 4. Route/Permission Definition Errors
**Problem:** Defining routes or permissions that don't exist
**Solution:** Only define what actually exists in your implementation

```php
// ❌ WRONG: Defining routes that don't exist
public function getRoutes(): array
{
    return [
        'create' => '', // Empty string causes "Route [] not defined"
        'edit' => '',
    ];
}

// ✅ CORRECT: Only define existing routes
public function getRoutes(): array
{
    return [
        'index' => 'admin.resource.index',
        'show' => 'admin.resource.show',
    ];
}
```

### 5. Return Type Mismatches
**Problem:** Method returns different type than declared
**Solution:** Match return types exactly with reference

```php
// Reference returns:
protected function getPermission(): string

// ❌ WRONG: Returning different type
protected function getPermission(): string
{
    return true; // Returns bool, expects string!
}

// ✅ CORRECT: Return correct type
protected function getPermission(): string
{
    return 'resource.view';
}
```

---

## ✅ Implementation Workflow

### Phase 1: Analysis (30% of time)
1. Identify 2-3 reference implementations
2. Document their complete structure
3. Note all patterns, conventions, signatures
4. Create implementation checklist
5. Get approval on approach if needed

### Phase 2: Implementation (50% of time)
1. Create files following exact structure
2. Copy-paste reference code as starting point
3. Adapt to your feature (names, logic, data)
4. Verify each file matches pattern before moving to next
5. Implement in order: Database → Model → Service → Controller → Component → View

### Phase 3: Verification (20% of time)
1. Run through all checklists
2. Compare side-by-side with reference
3. Test all functionality
4. Clear all caches
5. Verify no errors in logs

---

## 🔧 Verification Checklist

Before considering implementation complete:

### Code Structure
- [ ] All files in correct directories matching pattern
- [ ] All namespaces follow convention
- [ ] All class names follow convention
- [ ] All method names follow convention
- [ ] All property names follow convention

### Pattern Matching
- [ ] Trait usage matches reference (or lack thereof)
- [ ] Property visibility matches reference
- [ ] Method signatures match reference
- [ ] Return types match reference
- [ ] Import statements match pattern

### Functionality
- [ ] All routes defined and working
- [ ] All permissions defined and seeded
- [ ] All database tables created
- [ ] All relationships working
- [ ] All queries optimized (N+1 prevention)

### Integration
- [ ] Sidebar menu added (if applicable)
- [ ] Breadcrumbs working
- [ ] Permissions enforced
- [ ] Validation working
- [ ] Error handling working

### Testing
- [ ] Manual testing completed
- [ ] All CRUD operations work
- [ ] Search/filter/sort work (if applicable)
- [ ] No console errors
- [ ] No PHP errors in logs

---

## 📚 Quick Reference

### When Adding New Feature:
1. **Find similar feature** → Study it completely
2. **Document patterns** → Write down everything
3. **Match exactly** → Don't deviate without reason
4. **Verify constantly** → Check against reference frequently
5. **Test thoroughly** → Ensure it works like reference

### When Stuck:
1. **Compare with reference** → What's different?
2. **Check all checklists** → What did you miss?
3. **Review error messages** → What's the exact error?
4. **Search codebase** → How do others handle this?
5. **Ask for clarification** → Don't guess

---

## 🎓 Learning from Past Issues

### Issue: Property Conflicts with Traits
**Lesson:** Always check if reference uses traits before using them yourself

### Issue: Route Not Defined Errors
**Lesson:** Only define routes/permissions that actually exist, not empty strings

### Issue: Return Type Mismatches
**Lesson:** Match method signatures exactly, including return types

### Issue: Missing Required Methods
**Lesson:** Document ALL methods from reference and implement them all

### Issue: Inconsistent Patterns
**Lesson:** When in doubt, copy-paste from reference and adapt, don't write from scratch

### Issue: Bulk Delete Not Clearing Selection
**Lesson:** Always reset both backend AND frontend state after bulk operations
```php
// CRITICAL: Both lines required
$this->selectedItems = [];
$this->dispatch('resetSelectedItems');
```

### Issue: Missing Authorization in Bulk Actions
**Lesson:** All bulk action methods MUST have authorization checks
```php
public function bulkDelete(): void
{
    $this->authorize('resource.delete'); // REQUIRED!
    // ...
}
```

### Issue: Logging Sensitive Data in Action Logs
**Lesson:** Filter sensitive fields before logging to prevent security risks
```php
$sensitiveFields = ['password', 'remember_token', 'two_factor_secret'];
$filteredData = collect($data)->except($sensitiveFields)->toArray();
```

### Issue: Modal Z-Index Conflicts
**Lesson:** Use high z-index (z-[9999]) for modals to prevent conflicts
```blade
<div class="fixed inset-0 z-[9999]">
```

---

## 📖 Summary

**Golden Rule:** If a similar feature exists and works, your implementation should look almost identical in structure, just different in data and specific logic.

**Success Criteria:** A developer familiar with the reference feature should be able to understand your implementation immediately because it follows the exact same patterns.

**Remember:** Consistency > Cleverness. The goal is maintainable, predictable code, not innovative solutions.

