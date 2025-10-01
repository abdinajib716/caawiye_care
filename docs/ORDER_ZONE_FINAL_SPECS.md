# Order Zone - Final Specifications & Requirements

## 📋 Document Purpose

This document contains the **FINAL approved specifications** for the Order Zone feature based on client feedback and requirements.

**Related Documents:**
- `ORDER_ZONE_SYSTEM_ANALYSIS.md` - Deep dive analysis of existing system capabilities
- `ORDER_ZONE_UI_UX_REVIEW.md` - UI/UX review and design decisions
- `WAAFIPAY_INTEGRATION.MD` - Payment integration reference
- `EDAHAB_INTEGRATION.md` - eDahab payment integration (future)

---

## ✅ **Client Approved Requirements**

### **1. Stepper UI Component** 🎯

**Requirement**: Add visual stepper at the top showing workflow progress

**Design Specification:**
```
┌─────────────────────────────────────────────────────────┐
│  ①──────────②──────────③                                │
│  ✓          ✏️          ○                                │
│  Services   Customer   Payment                          │
└─────────────────────────────────────────────────────────┘
```

**States:**
- **Complete**: ✓ (checkmark) - Green circle (#10B981)
- **Active**: ✏️ (edit icon) - Blue circle (#3B82F6)
- **Pending**: Number - Gray circle (#D1D5DB)

**Steps:**
1. **Services** - Select services for the order (search and add)
2. **Customer** - Enter/find customer information (lookup or create)
3. **Payment** - Process payment and complete transaction

**⚠️ Important**: Workflow changed from "Customer → Services" to "Services → Customer" per client request.

---

### **2. Payment Methods Display** 💳

**Requirement**: Only show payment methods that are ENABLED in settings

**Implementation:**
```php
// Check settings
$waafipayEnabled = config('settings.waafipay_enabled');
$edahabEnabled = config('settings.edahab_enabled');

// Auto-detect from phone number
$phonePrefix = substr($phone, 0, 2);

// WaafiPay: 61, 63, 77, 90, 68
// eDahab: 62, 65
```

**Provider Logos:**
- Location: `/public/images/waafi/providers-telecome/`
- Files:
  - `evcplus.png` - Hormuud (61, 77)
  - `jeeb.png` - SomNet (68)
  - `Zaad.png` - Telesom (63)
  - `Sahal.png` - Golis (90)

**Display:**
```
Payment Method:
● WaafiPay [EVC Plus Logo]
  Auto-detected from phone: +252 61 XXX XXXX
```

---

### **3. Customer Details Panel - Best Practice** 📏

**Requirement**: Optimize space usage with best practices

**Initial State (Compact):**
```
┌─────────────────────────────────────┐
│ Customer Details                    │
├─────────────────────────────────────┤
│ Phone Number                        │
│ ┌──────┬────────────────────────┐   │
│ │ +252 │ 619821172              │   │
│ └──────┴────────────────────────┘   │
│                                     │
│ Full Name                           │
│ [                    ]              │
│                                     │
│ [🔍 Find Customer] [➕ New]         │
└─────────────────────────────────────┘
```

**After Customer Found (Expanded):**
```
┌─────────────────────────────────────┐
│ Customer Details              [Edit]│
├─────────────────────────────────────┤
│ ✅ Existing Customer                │
│                                     │
│ 👤 Ahmed Mohamed                    │
│ 📞 +252 61 982 1172                 │
│                                     │
│ 📊 Quick Stats:                     │
│ • Orders: 5                         │
│ • Spent: $450.00                    │
│ • Last: 2 days ago                  │
│                                     │
│ [View Profile]                      │
└─────────────────────────────────────┘
```

**Best Practices Applied:**
- Progressive disclosure (show more when needed)
- Minimal initial fields
- Expand to show context when customer found
- Quick actions (Find/New)

---

### **4. Service Search - Real-time** 🔍

**Requirement**: Real-time search WITHOUT category selection

**Implementation:**
```
┌─────────────────────────────────────┐
│ Order Services                      │
├─────────────────────────────────────┤
│ 🔍 Search services...               │
│ [Type to search in real-time]      │
├─────────────────────────────────────┤
│ ☐ Blood Test           $50.00  [1] │
│ ☐ X-Ray                $75.00  [1] │
│ ☐ Ultrasound          $100.00  [1] │
│ ☐ Consultation         $80.00  [1] │
├─────────────────────────────────────┤
│ Selected: 0 service(s)              │
│ Subtotal: $0.00                     │
└─────────────────────────────────────┘
```

**Features:**
- Search as user types (debounced 300ms)
- Filter services instantly
- Show matching results only
- Highlight matching text
- Show result count
- Clear search button (X icon)

**Example Search:**
```
User types: "blood"

Results:
☐ Blood Test           $50.00  [1]
☐ Blood Pressure Check $30.00  [1]
☐ Blood Sugar Test     $20.00  [1]

3 results found
```

---

### **5. Follow System Design** 🎨

**Requirement**: Use existing system design patterns and colors

**Colors (from existing system):**
- Primary: Blue (#3B82F6)
- Success: Green (#10B981)
- Warning: Yellow (#F59E0B)
- Danger: Red (#EF4444)
- Secondary: Gray (#6B7280)
- Info: Teal (#14B8A6)

**Components:**
- Use `form-control` class for inputs
- Use `btn-primary`, `btn-secondary`, `btn-danger` for buttons
- Use existing card/panel styles
- Use existing badge styles
- Use existing modal styles

**Typography:**
- Labels: `mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300`
- Helper text: `mt-1 text-xs text-gray-500 dark:text-gray-400`
- Headings: Follow existing heading classes

---

### **6. Payment Integration** 💰

**Requirement**: Follow WaafiPay integration docs (skip config - already done)

**Reference**: `/docs/WAAFIPAY_INTEGRATION.MD`

**Key Points:**
- Use existing `WaafipayService` class
- Phone number validation and formatting
- Provider auto-detection
- Payment status tracking
- Error handling and retry logic

**Payment Flow:**
1. Validate phone number
2. Auto-detect payment method (WaafiPay/eDahab)
3. Show provider logo
4. Create payment transaction record
5. Send payment request to customer's phone
6. Show loading state with countdown
7. Handle success/failure
8. Update transaction status
9. Show receipt/confirmation

---

## 📐 **Final Layout Design**

```
┌─────────────────────────────────────────────────────────────────┐
│  Order Zone                                                     │
├─────────────────────────────────────────────────────────────────┤
│  ①──────────②──────────③                                        │
│  ✏️          ○          ○                                        │
│  Services   Customer   Payment                                  │
├──────────────────────────┬──────────────────────────────────────┤
│                          │                                      │
│  Order Services          │  Customer Details                    │
│  ┌────────────────────┐  │  ┌────────────────────────────────┐ │
│  │ 🔍 Search...       │  │  │ Phone Number                   │ │
│  │ [Real-time]        │  │  │ ┌──────┬──────────────────┐   │ │
│  │                    │  │  │ │ +252 │ 619821172        │   │ │
│  │ ☐ Blood Test       │  │  │ └──────┴──────────────────┘   │ │
│  │   $50.00      [1]  │  │  │                                │ │
│  │ ☐ X-Ray            │  │  │ Full Name                      │ │
│  │   $75.00      [1]  │  │  │ [                    ]         │ │
│  │ ☐ Ultrasound       │  │  │                                │ │
│  │   $100.00     [1]  │  │  │ [🔍 Find] [➕ New]             │ │
│  │                    │  │  └────────────────────────────────┘ │
│  │ Selected: 0        │  │                                      │
│  │ Subtotal: $0.00    │  │                                      │
│  └────────────────────┘  │                                      │
│                          │                                      │
├──────────────────────────┴──────────────────────────────────────┤
│  Order Preview                                                  │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │ Customer: [Not selected]                                 │  │
│  │ Services: [No services selected]                         │  │
│  │ ───────────────────────────────────────────────────────  │  │
│  │ TOTAL: $0.00                                             │  │
│  │                                                           │  │
│  │ Payment Method: [Auto-detect from phone]                 │  │
│  │                                                           │  │
│  │ [Process Payment] [Calculator] [Clear]                   │  │
│  └──────────────────────────────────────────────────────────┘  │
├─────────────────────────────────────────────────────────────────┤
│  Transaction History                        [🔍 Search] [Filter]│
│  ┌────┬──────────┬─────────────┬──────────┬────────┬─────────┐ │
│  │ ID │ DateTime │ Customer    │ Services │ Amount │ Status  │ │
│  ├────┼──────────┼─────────────┼──────────┼────────┼─────────┤ │
│  │ 1  │ 10:30 AM │ Ahmed M.    │ Blood... │ $50.00 │ ✅ Paid │ │
│  │ 2  │ 10:15 AM │ Fatima A.   │ X-Ray... │ $75.00 │ ⏳ Pend.│ │
│  │ 3  │ 09:45 AM │ Hassan K.   │ Consul...│ $100   │ ❌ Fail │ │
│  └────┴──────────┴─────────────┴──────────┴────────┴─────────┘ │
└─────────────────────────────────────────────────────────────────┘
```

---

## 🔄 **Workflow Steps**

### **Step 1: Services (Stepper Active)** 🎯

1. Agent searches for services (real-time search, no categories)
2. Agent selects one or more services (checkboxes)
3. Agent adjusts quantities if needed (+/- buttons)
4. System updates subtotal in real-time
5. Preview panel shows selected services
6. Click "Continue" or automatically move to Step 2

### **Step 2: Customer (Stepper Active)** 👤

1. Agent enters customer phone number
2. System auto-detects payment method (WaafiPay/eDahab)
3. Agent clicks "Find Customer"
4. If found: Load customer details and show stats
5. If not found: Show "New Customer" form
6. Agent enters/confirms customer name
7. Click "Continue" or automatically move to Step 3

### **Step 3: Payment (Stepper Active)** 💳

1. System shows complete order summary
2. System displays payment method with provider logo
3. Agent reviews total amount
4. Agent clicks "Process Payment"
5. System sends payment request to customer's phone
6. Show loading modal with countdown (2-3 minutes)
7. Customer confirms payment on their phone
8. System receives payment status
9. Show success/failure message
10. Add transaction to history
11. Clear form for next order

**Note**: Workflow order changed from "Customer → Services → Payment" to "Services → Customer → Payment" to allow agents to quickly select services first, then collect customer information.

---

## 📊 **Transaction History Datatable**

### **Columns:**

| Column | Description | Width | Sortable |
|--------|-------------|-------|----------|
| **ID** | Order ID | 80px | Yes |
| **Date/Time** | Created timestamp | 120px | Yes |
| **Customer** | Name + Phone | 200px | Yes |
| **Services** | Service names (truncated) | 250px | No |
| **Amount** | Total amount | 100px | Yes |
| **Payment Method** | WaafiPay/eDahab with logo | 150px | Yes |
| **Status** | Paid/Pending/Failed badge | 100px | Yes |
| **Actions** | Dropdown menu | 80px | No |

### **Actions Dropdown:**
- 👁️ View Details
- 🖨️ Print Receipt
- 📧 Email Receipt
- 📱 SMS Receipt
- 🔄 Retry Payment (for failed)
- ❌ Cancel Order (for pending)

### **Filters:**
- Date range picker
- Status filter (All/Paid/Pending/Failed/Cancelled)
- Payment method filter (All/WaafiPay/eDahab)
- Customer search
- Amount range

### **Export Options:**
- Export to Excel
- Export to PDF
- Print Report

---

## 🗄️ **Database Schema**

### **orders table**

```sql
CREATE TABLE orders (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    customer_id BIGINT UNSIGNED NOT NULL,
    agent_id BIGINT UNSIGNED NOT NULL,
    
    -- Order Details
    subtotal DECIMAL(10, 2) NOT NULL,
    tax DECIMAL(10, 2) DEFAULT 0.00,
    discount DECIMAL(10, 2) DEFAULT 0.00,
    total DECIMAL(10, 2) NOT NULL,
    
    -- Payment Details
    payment_method VARCHAR(20) NOT NULL, -- 'waafipay' or 'edahab'
    payment_provider VARCHAR(50), -- 'EVC Plus', 'Zaad', etc.
    payment_phone VARCHAR(20) NOT NULL,
    payment_status VARCHAR(20) DEFAULT 'pending',
    payment_transaction_id VARCHAR(100),
    
    -- Status
    status VARCHAR(20) DEFAULT 'pending',
    notes TEXT,
    
    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL,
    
    -- Indexes
    INDEX idx_customer_id (customer_id),
    INDEX idx_agent_id (agent_id),
    INDEX idx_order_number (order_number),
    INDEX idx_payment_status (payment_status),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at),
    
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
    FOREIGN KEY (agent_id) REFERENCES users(id) ON DELETE CASCADE
);
```

### **order_items table**

```sql
CREATE TABLE order_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id BIGINT UNSIGNED NOT NULL,
    service_id BIGINT UNSIGNED NOT NULL,
    
    -- Item Details
    service_name VARCHAR(255) NOT NULL,
    quantity INT DEFAULT 1,
    unit_price DECIMAL(10, 2) NOT NULL,
    total_price DECIMAL(10, 2) NOT NULL,
    
    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Indexes
    INDEX idx_order_id (order_id),
    INDEX idx_service_id (service_id),
    
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
);
```

---

## 🚀 **Implementation Phases**

### **Phase 1: Core Structure** (Week 1)
- [ ] Create database migrations (orders, order_items)
- [ ] Create Order and OrderItem models
- [ ] Create OrderController with basic CRUD
- [ ] Create order zone page layout (3 panels)
- [ ] Add stepper UI component
- [ ] Implement basic routing

### **Phase 2: Customer & Services** (Week 2)
- [ ] Implement customer search functionality
- [ ] Add customer creation form
- [ ] Implement real-time service search
- [ ] Add service selection with checkboxes
- [ ] Add quantity selectors
- [ ] Implement real-time subtotal calculation
- [ ] Add order preview panel

### **Phase 3: Payment Integration** (Week 3)
- [ ] Integrate WaafipayService
- [ ] Add payment method auto-detection
- [ ] Show provider logos
- [ ] Implement payment processing
- [ ] Add loading state with countdown
- [ ] Handle payment success/failure
- [ ] Add retry logic for failed payments

### **Phase 4: Transaction History** (Week 4)
- [ ] Create transaction history datatable
- [ ] Add filters and search
- [ ] Implement actions dropdown
- [ ] Add receipt generation (print/email/SMS)
- [ ] Add export functionality
- [ ] Implement pagination

### **Phase 5: Testing & Polish** (Week 5)
- [ ] End-to-end testing
- [ ] Error handling and validation
- [ ] Responsive design testing
- [ ] Performance optimization
- [ ] User acceptance testing
- [ ] Bug fixes and refinements

---

## ✅ **Acceptance Criteria**

1. ✅ Stepper UI shows current step clearly
2. ✅ Only enabled payment methods are shown
3. ✅ Customer panel uses space efficiently
4. ✅ Service search works in real-time
5. ✅ Payment method auto-detects from phone
6. ✅ Provider logos display correctly
7. ✅ Order preview shows all details
8. ✅ Payment processing works end-to-end
9. ✅ Transaction history shows all orders
10. ✅ Filters and search work correctly
11. ✅ Receipts can be printed/emailed/SMS
12. ✅ Design follows system patterns
13. ✅ Responsive on all devices
14. ✅ Dark mode support
15. ✅ No console errors

---

**Document Version**: 1.0  
**Last Updated**: 2025-10-01  
**Status**: ✅ Approved - Ready for Implementation  
**Client Rating**: Good and Quite Rate (Approved)

