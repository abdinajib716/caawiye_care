# Caawiye Care - Development Guide & Best Practices

## Table of Contents
1. [Project Overview](#project-overview)
2. [Architecture Guidelines](#architecture-guidelines)
3. [Development Standards](#development-standards)
4. [Module Development Process](#module-development-process)
5. [Database Design Principles](#database-design-principles)
6. [API Development Standards](#api-development-standards)
7. [Frontend Guidelines](#frontend-guidelines)
8. [Testing Strategy](#testing-strategy)

## Project Overview

**Caawiye Care** is a comprehensive business management web application built on Laravel 12.x, transforming from a healthcare management system to a full-featured business operations platform.

### Core Technology Stack
- **Backend**: Laravel 12.x
- **Frontend**: Tailwind CSS 4.x, Alpine.js, Livewire 3.x
- **Database**: MySQL 8.0+
- **Authentication**: Laravel Sanctum
- **Permissions**: Spatie Laravel Permission
- **Testing**: Pest PHP

### System Architecture
```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Frontend      │    │   Backend       │    │   Database      │
│   (Livewire)    │◄──►│   (Laravel)     │◄──►│   (MySQL)       │
│   - Components  │    │   - Controllers │    │   - Tables      │
│   - Views       │    │   - Services    │    │   - Relations   │
│   - Alpine.js   │    │   - Models      │    │   - Indexes     │
└─────────────────┘    └─────────────────┘    └─────────────────┘
```

## Architecture Guidelines

### 1. Service-Oriented Architecture
- **Controllers**: Handle HTTP requests, delegate to services
- **Services**: Contain business logic and complex operations
- **Models**: Handle data relationships and basic operations
- **Repositories**: Optional for complex queries

### 2. Module Structure
Each module should follow this structure:
```
app/
├── Http/
│   ├── Controllers/Backend/{Module}/
│   ├── Requests/{Module}/
│   └── Resources/{Module}/
├── Services/{Module}/
├── Models/{Module}/
├── Livewire/{Module}/
└── Policies/{Module}/

resources/views/backend/pages/{module}/
database/migrations/{module}/
tests/Feature/{Module}/
```

### 3. Naming Conventions
- **Controllers**: `{Module}Controller` (e.g., `TransactionController`)
- **Services**: `{Module}Service` (e.g., `TransactionService`)
- **Models**: Singular form (e.g., `Transaction`)
- **Tables**: Plural form (e.g., `transactions`)
- **Livewire**: `{Module}{Action}` (e.g., `TransactionDatatable`)

## Development Standards

### 1. Code Quality Standards
- Follow PSR-12 coding standards
- Use type hints for all parameters and return types
- Write descriptive method and variable names
- Keep methods under 20 lines when possible
- Use dependency injection

### 2. Laravel Best Practices
```php
// Good: Service injection
class TransactionController extends Controller
{
    public function __construct(
        private TransactionService $transactionService
    ) {}

    public function index(): View
    {
        return view('backend.pages.transactions.index');
    }
}

// Good: Request validation
class StoreTransactionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'service_id' => 'required|exists:services,id',
            'customer_id' => 'required|exists:customers,id',
            'amount' => 'required|numeric|min:0',
        ];
    }
}
```

### 3. Database Best Practices
- Use migrations for all database changes
- Add proper indexes for performance
- Use foreign key constraints
- Follow naming conventions
- Add timestamps to all tables

## Module Development Process

### Phase 1: Planning & Design
1. **Requirements Analysis**
   - Define module functionality
   - Identify user roles and permissions
   - Map out user workflows

2. **Database Design**
   - Create ERD diagrams
   - Define table structures
   - Plan relationships and indexes

3. **API Design**
   - Define endpoints
   - Plan request/response structures
   - Document authentication requirements

### Phase 2: Backend Development
1. **Database Layer**
   ```bash
   php artisan make:migration create_{table}_table
   php artisan make:model {Model}
   php artisan make:factory {Model}Factory
   php artisan make:seeder {Model}Seeder
   ```

2. **Service Layer**
   ```bash
   php artisan make:class Services/{Module}/{Module}Service
   ```

3. **API Layer**
   ```bash
   php artisan make:controller Api/{Module}Controller --api
   php artisan make:request {Module}/Store{Module}Request
   php artisan make:resource {Module}Resource
   ```

4. **Web Layer**
   ```bash
   php artisan make:controller Backend/{Module}Controller
   php artisan make:livewire {Module}/{Module}Datatable
   ```

### Phase 3: Frontend Development
1. **Livewire Components**
2. **Blade Templates**
3. **Alpine.js Interactions**
4. **Tailwind Styling**

### Phase 4: Testing
1. **Unit Tests**
2. **Feature Tests**
3. **API Tests**
4. **Browser Tests**

## Database Design Principles

### 1. Table Naming
- Use plural nouns (e.g., `transactions`, `services`)
- Use snake_case for table and column names
- Prefix junction tables with both table names (e.g., `service_categories`)

### 2. Column Standards
```sql
-- Standard columns for all tables
id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT
created_at TIMESTAMP NULL
updated_at TIMESTAMP NULL

-- Soft deletes when needed
deleted_at TIMESTAMP NULL

-- Foreign keys
{table}_id BIGINT UNSIGNED
FOREIGN KEY ({table}_id) REFERENCES {table}(id)
```

### 3. Indexing Strategy
- Primary keys (automatic)
- Foreign keys (always index)
- Frequently queried columns
- Composite indexes for multi-column queries

## API Development Standards

### 1. RESTful Conventions
```
GET    /api/transactions       - List transactions
POST   /api/transactions       - Create transaction
GET    /api/transactions/{id}  - Show transaction
PUT    /api/transactions/{id}  - Update transaction
DELETE /api/transactions/{id}  - Delete transaction
```

### 2. Response Format
```json
{
    "success": true,
    "data": {
        "id": 1,
        "service_name": "Medical Report",
        "amount": 150.00
    },
    "message": "Transaction created successfully"
}
```

### 3. Error Handling
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "amount": ["The amount field is required."]
    }
}
```

## Frontend Guidelines

### 1. Livewire Components
- Keep components focused on single responsibility
- Use proper lifecycle hooks
- Implement loading states
- Handle errors gracefully

### 2. Blade Templates
- Use components for reusable elements
- Follow consistent naming
- Implement proper escaping
- Use slots for flexibility

### 3. Tailwind CSS
- Use utility classes
- Create custom components for repeated patterns
- Maintain dark/light mode compatibility
- Ensure responsive design

## Testing Strategy

### 1. Test Types
- **Unit Tests**: Test individual methods and classes
- **Feature Tests**: Test complete workflows
- **API Tests**: Test API endpoints
- **Browser Tests**: Test user interactions

### 2. Test Structure
```php
class TransactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_transaction(): void
    {
        $user = User::factory()->create();
        $service = Service::factory()->create();
        
        $response = $this->actingAs($user)
            ->post('/api/transactions', [
                'service_id' => $service->id,
                'amount' => 100.00
            ]);
            
        $response->assertStatus(201);
        $this->assertDatabaseHas('transactions', [
            'service_id' => $service->id,
            'amount' => 100.00
        ]);
    }
}
```

### 3. Testing Guidelines
- Write tests before or alongside code
- Test both happy path and edge cases
- Use factories for test data
- Mock external services
- Maintain test database separation

---

**Next Steps**: Review this guide and proceed with individual module documentation and implementation planning.
