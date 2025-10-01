# Caawiye Care - Database Design Documentation

## Overview
This document outlines the complete database design for the Caawiye Care business management system, including entity relationships, indexing strategies, and data integrity constraints.

## Database Architecture

### Design Principles
1. **Normalization**: Follow 3NF to reduce data redundancy
2. **Performance**: Strategic indexing for query optimization
3. **Scalability**: Design for future growth and expansion
4. **Integrity**: Enforce data consistency through constraints
5. **Security**: Implement proper access controls and encryption

## Entity Relationship Diagram (ERD)

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│     Users       │    │   Departments   │    │    Services     │
│                 │    │                 │    │                 │
│ • id (PK)       │    │ • id (PK)       │    │ • id (PK)       │
│ • name          │◄──┐│ • name          │    │ • name          │
│ • email         │   ││ • description   │    │ • description   │
│ • role          │   ││ • manager_id(FK)│    │ • price         │
└─────────────────┘   │└─────────────────┘    │ • category_id   │
         │            │                       │ • status        │
         │            │                       └─────────────────┘
         ▼            │                                │
┌─────────────────┐   │                                │
│Employee Profiles│   │                                │
│                 │   │                                ▼
│ • id (PK)       │   │                       ┌─────────────────┐
│ • user_id (FK)  │───┘                       │  Transactions   │
│ • employee_id   │                           │                 │
│ • department_id │                           │ • id (PK)       │
│ • position      │                           │ • service_id(FK)│
│ • hire_date     │                           │ • customer_id   │
│ • salary        │                           │ • amount        │
└─────────────────┘                           │ • status        │
         │                                    │ • payment_method│
         │                                    └─────────────────┘
         ▼                                             │
┌─────────────────┐                                    │
│   Attendance    │                                    ▼
│                 │                           ┌─────────────────┐
│ • id (PK)       │                           │   Deliveries    │
│ • employee_id   │                           │                 │
│ • date          │                           │ • id (PK)       │
│ • clock_in      │                           │ • transaction_id│
│ • clock_out     │                           │ • assigned_to   │
│ • status        │                           │ • status        │
└─────────────────┘                           │ • delivery_addr │
                                              └─────────────────┘
```

## Core Tables

### 1. Users Table (Enhanced)
```sql
CREATE TABLE users (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    phone VARCHAR(20),
    avatar VARCHAR(500),
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    last_login_at TIMESTAMP NULL,
    remember_token VARCHAR(100),
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    INDEX idx_email (email),
    INDEX idx_status (status),
    INDEX idx_last_login (last_login_at)
);
```

### 2. Service Categories
```sql
CREATE TABLE service_categories (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    parent_id BIGINT UNSIGNED NULL,
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (parent_id) REFERENCES service_categories(id) ON DELETE SET NULL,
    INDEX idx_parent_id (parent_id),
    INDEX idx_sort_order (sort_order),
    INDEX idx_is_active (is_active)
);
```

### 3. Services
```sql
CREATE TABLE services (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT,
    short_description VARCHAR(500),
    price DECIMAL(10,2) NOT NULL,
    cost DECIMAL(10,2) DEFAULT 0,
    category_id BIGINT UNSIGNED,
    sku VARCHAR(100) UNIQUE,
    status ENUM('active', 'inactive', 'discontinued') DEFAULT 'active',
    is_featured BOOLEAN DEFAULT FALSE,
    meta_title VARCHAR(255),
    meta_description TEXT,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (category_id) REFERENCES service_categories(id) ON DELETE SET NULL,
    INDEX idx_category_id (category_id),
    INDEX idx_status (status),
    INDEX idx_is_featured (is_featured),
    INDEX idx_slug (slug),
    FULLTEXT idx_search (name, description)
);
```

### 4. Customers
```sql
CREATE TABLE customers (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    customer_code VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE,
    phone VARCHAR(20),
    date_of_birth DATE,
    gender ENUM('male', 'female', 'other'),
    address TEXT,
    city VARCHAR(100),
    state VARCHAR(100),
    postal_code VARCHAR(20),
    country VARCHAR(100) DEFAULT 'Somalia',
    customer_type ENUM('individual', 'corporate') DEFAULT 'individual',
    status ENUM('active', 'inactive') DEFAULT 'active',
    notes TEXT,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    INDEX idx_email (email),
    INDEX idx_phone (phone),
    INDEX idx_customer_code (customer_code),
    INDEX idx_status (status),
    INDEX idx_customer_type (customer_type),
    FULLTEXT idx_search (name, email, phone)
);
```

### 5. Transactions
```sql
CREATE TABLE transactions (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    transaction_id VARCHAR(100) UNIQUE NOT NULL,
    service_id BIGINT UNSIGNED NOT NULL,
    customer_id BIGINT UNSIGNED NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    discount_amount DECIMAL(10,2) DEFAULT 0,
    tax_amount DECIMAL(10,2) DEFAULT 0,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'processing', 'succeeded', 'failed', 'cancelled', 'refunded') DEFAULT 'pending',
    payment_method ENUM('cash', 'card', 'bank_transfer', 'mobile_money', 'other'),
    payment_reference VARCHAR(255),
    currency VARCHAR(3) DEFAULT 'USD',
    exchange_rate DECIMAL(10,4) DEFAULT 1.0000,
    notes TEXT,
    processed_by BIGINT UNSIGNED,
    processed_at TIMESTAMP NULL,
    refunded_at TIMESTAMP NULL,
    refund_reason TEXT,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (service_id) REFERENCES services(id),
    FOREIGN KEY (customer_id) REFERENCES customers(id),
    FOREIGN KEY (processed_by) REFERENCES users(id) ON DELETE SET NULL,
    
    INDEX idx_transaction_id (transaction_id),
    INDEX idx_service_id (service_id),
    INDEX idx_customer_id (customer_id),
    INDEX idx_status (status),
    INDEX idx_payment_method (payment_method),
    INDEX idx_created_at (created_at),
    INDEX idx_processed_at (processed_at),
    INDEX idx_amount (amount),
    INDEX idx_customer_service (customer_id, service_id),
    INDEX idx_status_date (status, created_at)
);
```

## Business Operations Tables

### 6. Departments
```sql
CREATE TABLE departments (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(50) UNIQUE NOT NULL,
    description TEXT,
    manager_id BIGINT UNSIGNED NULL,
    parent_id BIGINT UNSIGNED NULL,
    budget DECIMAL(12,2) DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (manager_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (parent_id) REFERENCES departments(id) ON DELETE SET NULL,
    INDEX idx_manager_id (manager_id),
    INDEX idx_parent_id (parent_id),
    INDEX idx_is_active (is_active)
);
```

### 7. Employee Profiles
```sql
CREATE TABLE employee_profiles (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    employee_id VARCHAR(50) UNIQUE NOT NULL,
    department_id BIGINT UNSIGNED,
    position VARCHAR(255),
    employment_type ENUM('full_time', 'part_time', 'contract', 'intern') DEFAULT 'full_time',
    hire_date DATE NOT NULL,
    probation_end_date DATE,
    salary DECIMAL(10,2),
    hourly_rate DECIMAL(8,2),
    bank_account VARCHAR(100),
    tax_id VARCHAR(50),
    emergency_contact_name VARCHAR(255),
    emergency_contact_phone VARCHAR(20),
    emergency_contact_relationship VARCHAR(100),
    address TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    termination_date DATE NULL,
    termination_reason TEXT,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL,
    
    UNIQUE KEY unique_employee_id (employee_id),
    INDEX idx_department_id (department_id),
    INDEX idx_hire_date (hire_date),
    INDEX idx_is_active (is_active),
    INDEX idx_employment_type (employment_type)
);
```

### 8. Deliveries
```sql
CREATE TABLE deliveries (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    delivery_code VARCHAR(50) UNIQUE NOT NULL,
    transaction_id BIGINT UNSIGNED NOT NULL,
    customer_id BIGINT UNSIGNED NOT NULL,
    delivery_address TEXT NOT NULL,
    delivery_city VARCHAR(100),
    delivery_phone VARCHAR(20),
    assigned_to BIGINT UNSIGNED NULL,
    status ENUM('new', 'assigned', 'picked_up', 'in_transit', 'delivered', 'failed', 'cancelled') DEFAULT 'new',
    priority ENUM('low', 'normal', 'high', 'urgent') DEFAULT 'normal',
    scheduled_date DATE,
    scheduled_time TIME,
    picked_up_at TIMESTAMP NULL,
    delivered_at TIMESTAMP NULL,
    delivery_attempts INT DEFAULT 0,
    delivery_notes TEXT,
    failure_reason TEXT,
    delivery_proof VARCHAR(500), -- Photo/signature path
    estimated_delivery_time TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (transaction_id) REFERENCES transactions(id),
    FOREIGN KEY (customer_id) REFERENCES customers(id),
    FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL,
    
    INDEX idx_delivery_code (delivery_code),
    INDEX idx_transaction_id (transaction_id),
    INDEX idx_customer_id (customer_id),
    INDEX idx_assigned_to (assigned_to),
    INDEX idx_status (status),
    INDEX idx_priority (priority),
    INDEX idx_scheduled_date (scheduled_date),
    INDEX idx_status_assigned (status, assigned_to)
);
```

## Financial Management Tables

### 9. Expense Categories
```sql
CREATE TABLE expense_categories (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(50) UNIQUE NOT NULL,
    description TEXT,
    parent_id BIGINT UNSIGNED NULL,
    is_tax_deductible BOOLEAN DEFAULT FALSE,
    budget_limit DECIMAL(12,2) DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (parent_id) REFERENCES expense_categories(id) ON DELETE SET NULL,
    INDEX idx_parent_id (parent_id),
    INDEX idx_is_active (is_active),
    INDEX idx_code (code)
);
```

### 10. Expenses
```sql
CREATE TABLE expenses (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    expense_number VARCHAR(50) UNIQUE NOT NULL,
    category_id BIGINT UNSIGNED NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    tax_amount DECIMAL(10,2) DEFAULT 0,
    total_amount DECIMAL(10,2) NOT NULL,
    description TEXT NOT NULL,
    expense_date DATE NOT NULL,
    receipt_path VARCHAR(500),
    vendor_name VARCHAR(255),
    vendor_contact VARCHAR(255),
    payment_method ENUM('cash', 'card', 'bank_transfer', 'check', 'other'),
    reference_number VARCHAR(100),
    status ENUM('draft', 'pending', 'approved', 'rejected', 'paid') DEFAULT 'draft',
    approved_by BIGINT UNSIGNED NULL,
    approved_at TIMESTAMP NULL,
    rejection_reason TEXT,
    created_by BIGINT UNSIGNED NOT NULL,
    department_id BIGINT UNSIGNED,
    is_recurring BOOLEAN DEFAULT FALSE,
    recurring_frequency ENUM('weekly', 'monthly', 'quarterly', 'yearly') NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (category_id) REFERENCES expense_categories(id),
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL,
    
    INDEX idx_expense_number (expense_number),
    INDEX idx_category_id (category_id),
    INDEX idx_expense_date (expense_date),
    INDEX idx_status (status),
    INDEX idx_created_by (created_by),
    INDEX idx_approved_by (approved_by),
    INDEX idx_department_id (department_id),
    INDEX idx_amount (amount),
    INDEX idx_status_date (status, expense_date)
);
```

## Performance Optimization

### Indexing Strategy
1. **Primary Keys**: Automatic clustered indexes
2. **Foreign Keys**: Always indexed for join performance
3. **Search Fields**: Full-text indexes for name/description searches
4. **Filter Fields**: Composite indexes for common filter combinations
5. **Date Fields**: Indexed for time-based queries

### Query Optimization Guidelines
```sql
-- Example: Optimized transaction query with proper indexing
SELECT t.*, s.name as service_name, c.name as customer_name
FROM transactions t
JOIN services s ON t.service_id = s.id
JOIN customers c ON t.customer_id = c.id
WHERE t.status = 'succeeded'
  AND t.created_at >= '2024-01-01'
  AND t.created_at < '2024-02-01'
ORDER BY t.created_at DESC
LIMIT 50;

-- Supported by index: idx_status_date (status, created_at)
```

### Partitioning Strategy
For high-volume tables, consider partitioning:
```sql
-- Partition transactions by month
ALTER TABLE transactions
PARTITION BY RANGE (YEAR(created_at) * 100 + MONTH(created_at)) (
    PARTITION p202401 VALUES LESS THAN (202402),
    PARTITION p202402 VALUES LESS THAN (202403),
    -- ... continue for each month
);
```

## Data Integrity & Constraints

### Business Rules
1. **Transaction Amount**: Must be positive
2. **Employee ID**: Must be unique and follow format
3. **Delivery Status**: Must follow logical progression
4. **Expense Approval**: Cannot approve own expenses

### Triggers Example
```sql
-- Trigger to update transaction total when tax/discount changes
DELIMITER $$
CREATE TRIGGER tr_transaction_total_update
    BEFORE UPDATE ON transactions
    FOR EACH ROW
BEGIN
    SET NEW.total_amount = NEW.amount - NEW.discount_amount + NEW.tax_amount;
END$$
DELIMITER ;
```

## Backup & Recovery Strategy

### Backup Schedule
- **Full Backup**: Daily at 2:00 AM
- **Incremental Backup**: Every 4 hours
- **Transaction Log Backup**: Every 15 minutes

### Recovery Procedures
1. **Point-in-time Recovery**: Using transaction logs
2. **Table-level Recovery**: For specific data corruption
3. **Full System Recovery**: Complete database restoration

## Security Considerations

### Data Encryption
- **At Rest**: Encrypt sensitive columns (salary, bank_account, tax_id)
- **In Transit**: Use SSL/TLS for all connections
- **Application Level**: Hash passwords using bcrypt

### Access Control
- **Role-based Access**: Implement through Laravel permissions
- **Column-level Security**: Restrict access to sensitive data
- **Audit Logging**: Track all data modifications

---

**Next Steps**: 
1. Review and approve database design
2. Create migration files for all tables
3. Set up database seeding for initial data
4. Implement backup and monitoring procedures
