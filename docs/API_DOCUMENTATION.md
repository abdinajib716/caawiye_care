# Caawiye Care - API Documentation

## Overview
This document provides comprehensive API documentation for the Caawiye Care business management system. All APIs follow RESTful conventions and return JSON responses.

## Base Configuration

### Base URL
```
Production: https://api.caawiyecare.com/api/v1
Development: http://localhost:8000/api/v1
```

### Authentication
All API endpoints require authentication using Laravel Sanctum tokens.

```http
Authorization: Bearer {your-api-token}
Content-Type: application/json
Accept: application/json
```

### Standard Response Format
```json
{
    "success": true,
    "data": {
        // Response data
    },
    "message": "Operation completed successfully",
    "meta": {
        "current_page": 1,
        "per_page": 15,
        "total": 100
    }
}
```

### Error Response Format
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "field_name": ["Error message"]
    },
    "error_code": "VALIDATION_ERROR"
}
```

## Authentication Endpoints

### POST /auth/login
Authenticate user and receive API token.

**Request:**
```json
{
    "email": "user@example.com",
    "password": "password123"
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "user@example.com",
            "role": "admin"
        },
        "token": "1|abc123def456...",
        "expires_at": "2024-12-31T23:59:59Z"
    },
    "message": "Login successful"
}
```

### POST /auth/logout
Revoke current API token.

**Response:**
```json
{
    "success": true,
    "message": "Logged out successfully"
}
```

## Services API

### GET /services
Retrieve paginated list of services.

**Query Parameters:**
- `page` (int): Page number (default: 1)
- `per_page` (int): Items per page (default: 15, max: 100)
- `search` (string): Search in name and description
- `category_id` (int): Filter by category
- `status` (string): Filter by status (active, inactive)
- `sort` (string): Sort field (name, price, created_at)
- `order` (string): Sort order (asc, desc)

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "Medical Report Analysis",
            "description": "Comprehensive medical report analysis service",
            "price": 150.00,
            "category": {
                "id": 1,
                "name": "Medical Services"
            },
            "status": "active",
            "created_at": "2024-01-15T10:30:00Z"
        }
    ],
    "meta": {
        "current_page": 1,
        "per_page": 15,
        "total": 25,
        "last_page": 2
    }
}
```

### POST /services
Create a new service.

**Request:**
```json
{
    "name": "Blood Test Analysis",
    "description": "Complete blood work analysis and reporting",
    "price": 75.50,
    "category_id": 1,
    "status": "active"
}
```

**Validation Rules:**
- `name`: required, string, max:255, unique
- `description`: nullable, string
- `price`: required, numeric, min:0
- `category_id`: nullable, exists:service_categories,id
- `status`: required, in:active,inactive

### GET /services/{id}
Retrieve specific service details.

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "Medical Report Analysis",
        "description": "Comprehensive medical report analysis service",
        "price": 150.00,
        "cost": 50.00,
        "category": {
            "id": 1,
            "name": "Medical Services"
        },
        "status": "active",
        "transaction_count": 45,
        "total_revenue": 6750.00,
        "created_at": "2024-01-15T10:30:00Z",
        "updated_at": "2024-01-20T14:22:00Z"
    }
}
```

### PUT /services/{id}
Update existing service.

**Request:**
```json
{
    "name": "Updated Service Name",
    "price": 200.00,
    "status": "inactive"
}
```

### DELETE /services/{id}
Delete service (soft delete).

**Response:**
```json
{
    "success": true,
    "message": "Service deleted successfully"
}
```

## Transactions API

### GET /transactions
Retrieve paginated list of transactions.

**Query Parameters:**
- `page`, `per_page`: Pagination
- `search`: Search in transaction_id, customer name
- `status`: Filter by status
- `service_id`: Filter by service
- `customer_id`: Filter by customer
- `date_from`, `date_to`: Date range filter
- `amount_min`, `amount_max`: Amount range filter
- `payment_method`: Filter by payment method

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "transaction_id": "TXN-2024-001",
            "service": {
                "id": 1,
                "name": "Medical Report Analysis"
            },
            "customer": {
                "id": 1,
                "name": "Jane Smith",
                "email": "jane@example.com"
            },
            "amount": 150.00,
            "total_amount": 165.00,
            "tax_amount": 15.00,
            "status": "succeeded",
            "payment_method": "card",
            "created_at": "2024-01-15T14:30:00Z"
        }
    ]
}
```

### POST /transactions
Create new transaction.

**Request:**
```json
{
    "service_id": 1,
    "customer_id": 1,
    "amount": 150.00,
    "payment_method": "card",
    "payment_reference": "ch_1234567890",
    "notes": "Rush processing requested"
}
```

**Validation Rules:**
- `service_id`: required, exists:services,id
- `customer_id`: required, exists:customers,id
- `amount`: required, numeric, min:0
- `payment_method`: required, in:cash,card,bank_transfer,mobile_money,other
- `payment_reference`: nullable, string, max:255
- `notes`: nullable, string

### GET /transactions/{id}
Get transaction details with full information.

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "transaction_id": "TXN-2024-001",
        "service": {
            "id": 1,
            "name": "Medical Report Analysis",
            "price": 150.00
        },
        "customer": {
            "id": 1,
            "name": "Jane Smith",
            "email": "jane@example.com",
            "phone": "+252-123-456789"
        },
        "amount": 150.00,
        "discount_amount": 0.00,
        "tax_amount": 15.00,
        "total_amount": 165.00,
        "status": "succeeded",
        "payment_method": "card",
        "payment_reference": "ch_1234567890",
        "processed_by": {
            "id": 2,
            "name": "Admin User"
        },
        "processed_at": "2024-01-15T14:35:00Z",
        "delivery": {
            "id": 1,
            "status": "delivered",
            "delivered_at": "2024-01-16T10:00:00Z"
        },
        "created_at": "2024-01-15T14:30:00Z"
    }
}
```

### PUT /transactions/{id}/status
Update transaction status.

**Request:**
```json
{
    "status": "succeeded",
    "notes": "Payment confirmed"
}
```

**Valid Status Transitions:**
- `pending` → `processing`, `failed`, `cancelled`
- `processing` → `succeeded`, `failed`
- `succeeded` → `refunded`

### POST /transactions/{id}/refund
Process transaction refund.

**Request:**
```json
{
    "refund_amount": 150.00,
    "refund_reason": "Customer requested cancellation"
}
```

## Customers API

### GET /customers
Retrieve paginated list of customers.

**Query Parameters:**
- Standard pagination and search parameters
- `customer_type`: Filter by individual/corporate
- `status`: Filter by active/inactive

### POST /customers
Create new customer.

**Request:**
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "+252-123-456789",
    "address": "123 Main Street, Mogadishu",
    "customer_type": "individual"
}
```

### GET /customers/{id}/transactions
Get customer's transaction history.

## Deliveries API

### GET /deliveries
Retrieve deliveries with filtering options.

**Query Parameters:**
- `status`: Filter by delivery status
- `assigned_to`: Filter by delivery person
- `date_from`, `date_to`: Date range
- `priority`: Filter by priority level

### POST /deliveries
Create new delivery.

**Request:**
```json
{
    "transaction_id": 1,
    "delivery_address": "123 Main Street, Mogadishu",
    "delivery_phone": "+252-123-456789",
    "scheduled_date": "2024-01-16",
    "scheduled_time": "10:00:00",
    "priority": "normal"
}
```

### PUT /deliveries/{id}/assign
Assign delivery to personnel.

**Request:**
```json
{
    "assigned_to": 5,
    "notes": "Handle with care - fragile items"
}
```

### PUT /deliveries/{id}/status
Update delivery status.

**Request:**
```json
{
    "status": "delivered",
    "delivery_notes": "Delivered successfully to customer",
    "delivery_proof": "base64_encoded_image_or_signature"
}
```

## Dashboard API

### GET /dashboard/metrics
Get dashboard KPIs and metrics.

**Query Parameters:**
- `period`: today, week, month, quarter, year
- `date_from`, `date_to`: Custom date range

**Response:**
```json
{
    "success": true,
    "data": {
        "revenue": {
            "total": 15750.00,
            "growth": 12.5,
            "previous_period": 14000.00
        },
        "transactions": {
            "total": 105,
            "success_rate": 94.3,
            "failed": 6
        },
        "customers": {
            "total": 45,
            "new": 8,
            "returning": 37
        },
        "deliveries": {
            "pending": 12,
            "in_transit": 8,
            "delivered": 85,
            "failed": 2
        }
    }
}
```

### GET /dashboard/recent-transactions
Get recent transactions for dashboard.

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "transaction_id": "TXN-2024-001",
            "service_name": "Medical Report Analysis",
            "customer_name": "Jane Smith",
            "amount": 150.00,
            "status": "succeeded",
            "created_at": "2024-01-15T14:30:00Z"
        }
    ]
}
```

### GET /dashboard/top-services
Get top-performing services.

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "service_id": 1,
            "service_name": "Medical Report Analysis",
            "transaction_count": 45,
            "total_revenue": 6750.00,
            "percentage": 42.9
        }
    ]
}
```

## Error Codes

| Code | Description |
|------|-------------|
| `VALIDATION_ERROR` | Request validation failed |
| `AUTHENTICATION_REQUIRED` | Valid authentication token required |
| `AUTHORIZATION_DENIED` | Insufficient permissions |
| `RESOURCE_NOT_FOUND` | Requested resource does not exist |
| `DUPLICATE_RESOURCE` | Resource already exists |
| `BUSINESS_RULE_VIOLATION` | Business logic constraint violated |
| `EXTERNAL_SERVICE_ERROR` | Third-party service unavailable |
| `RATE_LIMIT_EXCEEDED` | Too many requests |

## Rate Limiting

- **General API**: 1000 requests per hour per user
- **Authentication**: 10 requests per minute per IP
- **Dashboard**: 100 requests per minute per user

## Webhooks (Future Implementation)

### Transaction Events
- `transaction.created`
- `transaction.succeeded`
- `transaction.failed`
- `transaction.refunded`

### Delivery Events
- `delivery.assigned`
- `delivery.in_transit`
- `delivery.delivered`
- `delivery.failed`

---

**Next Steps**: 
1. Implement API endpoints following this specification
2. Set up API testing suite
3. Create Postman collection for testing
4. Implement rate limiting and security measures
