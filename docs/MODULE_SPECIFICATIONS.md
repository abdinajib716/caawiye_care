# Caawiye Care - Module Specifications

## Overview
This document provides detailed specifications for each module in the Caawiye Care business management system.

## Module Priority & Implementation Order

### Phase 1: Core Business Operations
1. **Services Module** (Foundation)
2. **Transactions Module** (Core functionality)
3. **Dashboard Module** (Overview & metrics)

### Phase 2: Operations Management
4. **Delivery Module** (Service fulfillment)
5. **HRM Module** (Staff management)

### Phase 3: Financial Management
6. **Finances Module** (Reporting)
7. **Expenses Module** (Cost tracking)
8. **Payroll Module** (Staff compensation)

---

## 1. Services Module

### Purpose
Manage the catalog of services offered by the business.

### Database Schema
```sql
-- Services table
CREATE TABLE services (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    category_id BIGINT UNSIGNED,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX idx_category_id (category_id),
    INDEX idx_status (status)
);

-- Service categories table
CREATE TABLE service_categories (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

### Key Features
- CRUD operations for services
- Service categorization
- Dynamic pricing management
- Service status management (active/inactive)
- Bulk operations (import/export)

### API Endpoints
```
GET    /api/services           - List all services
POST   /api/services           - Create new service
GET    /api/services/{id}      - Get service details
PUT    /api/services/{id}      - Update service
DELETE /api/services/{id}      - Delete service
GET    /api/service-categories - List categories
```

### User Interface Components
- Service listing with search and filters
- Service creation/editing form
- Category management interface
- Pricing history tracking
- Service performance metrics

---

## 2. Transactions Module

### Purpose
Track all business transactions and payment processing.

### Database Schema
```sql
-- Transactions table
CREATE TABLE transactions (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    transaction_id VARCHAR(100) UNIQUE NOT NULL,
    service_id BIGINT UNSIGNED NOT NULL,
    customer_id BIGINT UNSIGNED NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'succeeded', 'failed', 'refunded') DEFAULT 'pending',
    payment_method VARCHAR(50),
    payment_reference VARCHAR(255),
    notes TEXT,
    processed_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (service_id) REFERENCES services(id),
    FOREIGN KEY (customer_id) REFERENCES customers(id),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at),
    INDEX idx_customer_service (customer_id, service_id)
);

-- Customers table
CREATE TABLE customers (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE,
    phone VARCHAR(20),
    address TEXT,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX idx_email (email)
);
```

### Key Features
- Real-time transaction processing
- Transaction history with filtering
- Payment method tracking
- Transaction status management
- Refund processing
- Customer transaction history
- Transaction analytics and reporting

### API Endpoints
```
GET    /api/transactions              - List transactions
POST   /api/transactions              - Create transaction
GET    /api/transactions/{id}         - Get transaction details
PUT    /api/transactions/{id}/status  - Update transaction status
POST   /api/transactions/{id}/refund  - Process refund
GET    /api/customers                 - List customers
POST   /api/customers                 - Create customer
```

### Real-time Features
- Auto-refresh transaction list
- Live status updates
- Payment notifications
- Transaction alerts

---

## 3. Dashboard Module

### Purpose
Provide business overview and key performance indicators.

### Key Metrics
- **Revenue Metrics**: Total revenue, monthly growth, average transaction value
- **Transaction Metrics**: Total transactions, success rate, failed transactions
- **Service Metrics**: Top-selling services, service performance
- **Customer Metrics**: New customers, customer retention, customer lifetime value

### Dashboard Components
1. **KPI Cards**
   - Total Revenue (current month)
   - Total Expenses (current month)
   - New Customers (current month)
   - Transaction Success Rate

2. **Recent Transactions Table**
   - Last 10-15 transactions
   - Columns: Service, Customer, Amount, Status, Timestamp
   - Real-time updates

3. **Top Services Chart**
   - Bar/Pie chart showing top 5 services by revenue
   - Interactive with drill-down capability

4. **Revenue Trends**
   - Line chart showing revenue over time
   - Comparison with previous periods

### Data Refresh Strategy
- Real-time updates using Livewire polling
- Cached aggregated data for performance
- Background job for heavy calculations

---

## 4. Delivery Module

### Purpose
Manage delivery of medical reports and other services.

### Database Schema
```sql
-- Deliveries table
CREATE TABLE deliveries (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    transaction_id BIGINT UNSIGNED NOT NULL,
    delivery_address TEXT NOT NULL,
    assigned_to BIGINT UNSIGNED NULL,
    status ENUM('new', 'assigned', 'in_transit', 'delivered', 'failed') DEFAULT 'new',
    scheduled_at TIMESTAMP NULL,
    delivered_at TIMESTAMP NULL,
    notes TEXT,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (transaction_id) REFERENCES transactions(id),
    FOREIGN KEY (assigned_to) REFERENCES users(id),
    INDEX idx_status (status),
    INDEX idx_assigned_to (assigned_to),
    INDEX idx_scheduled_at (scheduled_at)
);

-- Delivery tracking table
CREATE TABLE delivery_tracking (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    delivery_id BIGINT UNSIGNED NOT NULL,
    status VARCHAR(50) NOT NULL,
    location VARCHAR(255),
    notes TEXT,
    created_at TIMESTAMP NULL,
    
    FOREIGN KEY (delivery_id) REFERENCES deliveries(id),
    INDEX idx_delivery_id (delivery_id)
);
```

### Key Features
- Delivery assignment system
- Status tracking and updates
- Route optimization
- Delivery performance metrics
- Customer delivery notifications
- Delivery personnel management

### Delivery Workflow
1. **New Order**: Delivery created from transaction
2. **Assignment**: Dispatcher assigns to delivery personnel
3. **In Transit**: Delivery person picks up and starts delivery
4. **Delivered**: Successful delivery confirmation
5. **Failed**: Failed delivery with reason

---

## 5. HRM Module

### Purpose
Manage human resources, staff, and organizational structure.

### Database Schema
```sql
-- Departments table
CREATE TABLE departments (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    manager_id BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (manager_id) REFERENCES users(id)
);

-- Employee profiles (extends users table)
CREATE TABLE employee_profiles (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    employee_id VARCHAR(50) UNIQUE,
    department_id BIGINT UNSIGNED,
    position VARCHAR(255),
    hire_date DATE,
    salary DECIMAL(10,2),
    phone VARCHAR(20),
    emergency_contact VARCHAR(255),
    emergency_phone VARCHAR(20),
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (department_id) REFERENCES departments(id),
    UNIQUE KEY unique_employee_id (employee_id)
);

-- Shifts table
CREATE TABLE shifts (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    days_of_week JSON, -- ['monday', 'tuesday', ...]
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- Employee shifts assignment
CREATE TABLE employee_shifts (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    employee_id BIGINT UNSIGNED NOT NULL,
    shift_id BIGINT UNSIGNED NOT NULL,
    effective_date DATE NOT NULL,
    end_date DATE NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (employee_id) REFERENCES employee_profiles(id),
    FOREIGN KEY (shift_id) REFERENCES shifts(id)
);

-- Attendance table
CREATE TABLE attendance (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    employee_id BIGINT UNSIGNED NOT NULL,
    date DATE NOT NULL,
    clock_in TIME,
    clock_out TIME,
    break_duration INT DEFAULT 0, -- minutes
    status ENUM('present', 'absent', 'late', 'half_day') DEFAULT 'present',
    notes TEXT,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (employee_id) REFERENCES employee_profiles(id),
    UNIQUE KEY unique_employee_date (employee_id, date),
    INDEX idx_date (date)
);
```

### Key Features
- Employee profile management
- Department organization
- Shift scheduling with calendar interface
- Attendance tracking
- Employee performance metrics
- Role and permission management
- Employee onboarding workflow

---

## 6. Finances Module

### Purpose
Financial reporting and analysis for business decision making.

### Key Reports
1. **Profit & Loss Statement**
   - Revenue breakdown by service
   - Operating expenses
   - Net profit calculation
   - Period comparison

2. **Balance Sheet**
   - Assets and liabilities
   - Equity calculation
   - Financial position overview

3. **Cash Flow Statement**
   - Operating cash flow
   - Investment activities
   - Financing activities

### Features
- Automated report generation
- Date range selection
- Export functionality (PDF/CSV/Excel)
- Interactive charts and graphs
- Budget vs actual comparison
- Financial trend analysis

---

## 7. Expenses Module

### Purpose
Track and categorize business expenses for financial management.

### Database Schema
```sql
-- Expense categories
CREATE TABLE expense_categories (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    parent_id BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (parent_id) REFERENCES expense_categories(id)
);

-- Expenses table
CREATE TABLE expenses (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    category_id BIGINT UNSIGNED NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    description TEXT NOT NULL,
    expense_date DATE NOT NULL,
    receipt_path VARCHAR(500),
    vendor VARCHAR(255),
    payment_method VARCHAR(50),
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    approved_by BIGINT UNSIGNED NULL,
    approved_at TIMESTAMP NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (category_id) REFERENCES expense_categories(id),
    FOREIGN KEY (approved_by) REFERENCES users(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    INDEX idx_expense_date (expense_date),
    INDEX idx_status (status)
);
```

### Key Features
- Expense categorization
- Receipt attachment and storage
- Approval workflow
- Expense reporting by category/period
- Budget tracking and alerts
- Vendor management
- Tax-related expense tracking

---

## 8. Payroll Module

### Purpose
Automate payroll processing and manage employee compensation.

### Database Schema
```sql
-- Payroll periods
CREATE TABLE payroll_periods (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    pay_date DATE NOT NULL,
    status ENUM('draft', 'processing', 'completed') DEFAULT 'draft',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- Payroll entries
CREATE TABLE payroll_entries (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    payroll_period_id BIGINT UNSIGNED NOT NULL,
    employee_id BIGINT UNSIGNED NOT NULL,
    gross_pay DECIMAL(10,2) NOT NULL,
    deductions DECIMAL(10,2) DEFAULT 0,
    net_pay DECIMAL(10,2) NOT NULL,
    hours_worked DECIMAL(5,2) DEFAULT 0,
    overtime_hours DECIMAL(5,2) DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (payroll_period_id) REFERENCES payroll_periods(id),
    FOREIGN KEY (employee_id) REFERENCES employee_profiles(id),
    UNIQUE KEY unique_period_employee (payroll_period_id, employee_id)
);
```

### Key Features
- Automated payroll calculation
- Digital paystub generation
- Tax calculation and deductions
- Payroll history and reporting
- Direct deposit integration
- Payroll approval workflow
- Year-end tax reporting

---

## Implementation Guidelines

### Development Order
1. Start with Services Module (foundation)
2. Build Transactions Module (core business logic)
3. Create Dashboard Module (business overview)
4. Implement remaining modules based on business priority

### Quality Assurance
- Each module must include comprehensive tests
- API documentation for all endpoints
- User acceptance testing for each feature
- Performance testing for data-heavy operations

### Security Considerations
- Role-based access control for all modules
- Data encryption for sensitive information
- Audit logging for financial transactions
- Regular security assessments

---

**Next Steps**: Begin with detailed technical specifications for the Services Module as the foundation.
