# WaafiPay Payment Integration Documentation

## 📋 Overview

WaafiPay is Somalia's leading mobile money payment gateway that enables businesses to accept payments from various mobile wallet providers (EVC PLUS, JEEB, ZAAD, SAHAL). This documentation provides a comprehensive guide for implementing WaafiPay payment processing in any application.

---

## 🏗️ Business Logic and Format

### **Payment Flow Architecture**

#### **1. Payment Initiation**
- Customer selects mobile wallet provider (EVC PLUS, JEEB, ZAAD, SAHAL)
- Customer enters phone number (252XXXXXXXX format)
- Customer provides name for transaction reference
- System validates phone number format and provider compatibility

#### **2. Payment Processing**
- System creates payment record with pending status
- API request sent to WaafiPay with official payload format
- Customer receives USSD prompt on their mobile device
- Customer enters PIN to authorize payment
- System polls for payment status or receives response

#### **3. Payment Completion**
- Success: Payment marked as completed, order processed
- Failure: Payment marked as failed, error message displayed
- Timeout: Payment marked as expired, customer can retry

### **Data Format Requirements**

#### **Phone Number Format**
```
Input Formats Accepted For Each Provider Telecome :
- 252619821172 (International)
- 619821172 (National)


Output Format (API):
- 252619821172 (Always international format)
```

#### **API Payload Structure**
```json
{
  "schemaVersion": "1.0",
  "requestId": "REQ-20241203123456-ABC123",
  "timestamp": "2024-12-03T12:34:56Z",
  "channelName": "WEB",
  "serviceName": "API_PURCHASE",
  "serviceParams": {
    "merchantUid": "M1234567",
    "apiUserId": "API-123456789ABC",
    "apiKey": "your-api-key",
    "paymentMethod": "MWALLET_ACCOUNT",
    "payerInfo": {
      "accountNo": "252619821172"
    },
    "transactionInfo": {
      "referenceId": "ORDER-123",
      "invoiceId": "INV-123",
      "amount": 10.00,
      "currency": "USD",
      "description": "Order payment"
    }
  }
}
```


Phone Formats For All WaafiPay providers telcome 

Hormuud (EVC Plus) → prefixes 61, 77
SomNet (Jeeb) → prefix 68
Telesom (Zaad) → prefix 63
Golis (Sahal) → prefixes 90
SomNet (Jeeb) → prefix 68

UI/UX 
WaafiPay Phone Input UX Fix

## 🚨 Problem Identified

When users entered their phone number (e.g., `619821172`), the system was adding the "252" prefix internally but then displaying the full number `252619821172` in the input field, making it appear as if the user had entered the prefix themselves.

### **Before (Problematic Behavior):**

```
User enters: 619821172
System adds prefix internally: 252619821172
Input field shows: 252619821172  ❌ WRONG!
```

**Visual Issue:**
```
┌─────┬─────────────────────────────────────────────────────┐
│+252 │ 252619821172                                        │ ❌
└─────┴─────────────────────────────────────────────────────┘
```

**Problems:**
- ❌ User sees duplicate "252" (visual prefix + in input)
- ❌ Input field shows 12 digits instead of 9
- ❌ Confusing UX - looks like user entered wrong format
- ❌ Input length restriction prevents full number entry

## ✅ Solution Implemented

The fix ensures **complete separation** between:
1. **Visual Display**: Input field shows ONLY user's number (without 252)
2. **Internal Processing**: 252 prefix added only for API calls
3. **Data Storage**: Livewire receives formatted number with 252 prefix

### **After (Fixed Behavior):**

```
User enters: 619821172
Input field shows: 619821172  ✅ CORRECT!
Internal value: 252619821172  ✅ CORRECT!
```

**Visual Result:**
```
┌─────┬─────────────────────────────────────────────────────┐
│+252 │ 619821172                                           │ ✅
└─────┴─────────────────────────────────────────────────────┘
✓ Valid - Hormuud EVC Plus
```


### **Business Rules**

#### **Validation Rules**
- Phone number must be valid Somalia mobile number
- Amount must be greater than 0
- Currency must be supported (USD, etc.)
- Customer name is required for transaction reference
- Provider selection affects phone number validation

#### **Error Handling**
- Invalid phone number: Show format requirements
- Insufficient funds: Suggest customer check balance
- Network timeout: Allow retry with same payment
- User rejection: Clear explanation and retry option
- API errors: User-friendly error messages

---

## 🎨 UI/UX Design Patterns

### **Provider Selection Interface**
```
┌─────────────────────────────────┐
│ Choose Your Mobile Wallet      │
├─────────────────────────────────┤
│ ○ [EVC] EVC PLUS              │
│ ○ [JEEB] JEEB                  │
│ ○ [ZAAD] ZAAD                  │
│ ○ [SAHAL] SAHAL                │
└─────────────────────────────────┘
```

### **Payment Form Design**
```
┌─────────────────────────────────┐
│ Phone Number                    │
│ ┌─────┬─────────────────────────┐│
│ │+252 │ 61 9821172             ││
│ └─────┴─────────────────────────┘│
│                                 │
│ Customer Name                   │
│ ┌─────────────────────────────┐ │
│ │ Karshe               │ │
│ └─────────────────────────────┘ │
│                                 │
│ ┌─────────────────────────────┐ │
│ │    Pay $10.00               │ │
│ └─────────────────────────────┘ │
└─────────────────────────────────┘
```

### **Processing State Design**
```
┌─────────────────────────────────┐
│        Check Your Phone         │
├─────────────────────────────────┤
│    [📱] Payment request sent    │
│                                 │
│    ████████████░░░░ 75%         │
│                                 │
│    Time remaining: 0:25         │
│                                 │
│    │
└─────────────────────────────────┘
```

### **UI Components**

#### **Color Scheme**
- Use system Color Exsit and Respect




#### **Interactive Elements**
- Provider selection with visual feedback
- Real-time phone number validation
- Progress indicators during processing
- Clear error states with actionable messages
- Loading states with appropriate animations

---

## 🔗 API Endpoints

### **Payment Processing Endpoints**

#### **Process Payment**
```http
POST /waafipay/process-payment
Content-Type: application/json

{
  "order_id": 123,
  "phone_number": "252619821172",
  "customer_name": "John Doe",
  "provider": "EVC PLUS"
}

Response:
{
  "success": true,
  "payment_id": 456,
  "status": "pending",
  "message": "Payment request sent to customer"
}
```

#### **Check Payment Status**
```http
GET /waafipay/payment-status/{payment_id}

Response:
{
  "payment_status": "completed",
  "transaction_id": "TXN123456",
  "amount": 10.00,
  "phone_number": "252619821172"
}
```


```

### **Testing Endpoints**

#### **Test Payment Gateway**
```http
POST /test-waafipay-restaurant
Content-Type: application/json

{
  "phone": "252619821172",
  "amount": 1.00,
  "test_mode": true
}
```

### **Settings Endpoints**

#### **Update Payment Settings**
```http
PUT /settings/payment-gateways
Content-Type: application/json

{
  "waafipay_status": true,
  "waafipay_mode": "live",
  "waafipay_merchant_uid": "M1234567",
  "waafipay_api_user_id": "API-123456789ABC",
  "waafipay_api_key": "your-api-key",
  "waafipay_merchant_no": "123456789" 
  "Waafi Api Url endpoint ":"Api-url"
}
```

---

## ⚙️ How It Works

### **Step-by-Step Process**

#### **1. Customer Initiates Payment**
1. Users 
2. Selects "Pay with Mobile Wallet"
3. Chooses preferred provider (EVC+, JEEB, ZAAD, SAHAL)
4. Enters phone number and name
5. Clicks "Pay [Amount]" button

#### **2. System Processing**
1. Validates phone number format
2. Creates payment record in database
3. Builds API payload with credentials
4. Sends HTTP request to WaafiPay API
5. Starts polling for payment status

#### **3. Customer Mobile Experience**
1. Receives USSD prompt on phone
2. Reviews payment details
3. Enters mobile wallet PIN
4. Confirms or cancels payment

#### **4. Payment Resolution**
1. WaafiPay processes the payment
2. System receives response/polls status
3. Updates payment record
4. Notifies customer of result
5. Processes order if successful

### **Technical Flow**

```
Customer → Frontend → Controller → Service → WaafiPay API
    ↓         ↓          ↓          ↓           ↓
   UI     Validation   Business   HTTP      Mobile
 Input    & Rules     Logic    Request     Wallet
    ↓         ↓          ↓          ↓           ↓
Response ← UI Update ← Database ← Response ← Customer
```

### **Error Handling Strategy**

#### **Network Errors**
- Connection timeout: Retry mechanism
- API unavailable: Graceful degradation
- Invalid response: Error logging and user notification

#### **Business Errors**
- Invalid credentials: Settings validation
- Insufficient funds: Customer guidance
- User rejection: Clear messaging and retry option

#### **System Errors**
- Database issues: Transaction rollback
- Service unavailable: Maintenance mode
- Validation failures: Field-specific error messages

---

## 🔧 Settings and Configuration

### **Administrative Settings**

#### **Super Admin Level**
- Global WaafiPay enable/disable
- Default API URL configuration
- System-wide payment limits
- Security and compliance settings



### **Credential Management**

#### **Required Credentials**
```
Live Environment:
- MERCHANT_U_ID: M1234567
- API_USER_ID: API-123456789ABC  
- API_KEY: your-secret-api-key
- MERCHANT_NO: 123456789
- Api Url endpoint 

Test Environment:
- TEST_MERCHANT_U_ID: M0000000
- TEST_API_USER_ID: API-000000000XXX
- TEST_API_KEY: test-api-key
- TEST_MERCHANT_NO: 000000000
- Api Url endpoint 

```

#### **Security Features**
- Environment-based configuration
- Access control and permissions
- Audit logging for changes
- Credential validation before saving

### **Configuration Options**



#### **UI Customization**
- Provider logo display
- Color scheme integration


---

## 🏢 Centralization Architecture

### **Service Layer Architecture**

#### **Core Services**
1. **WaafipayService**: API communication and payload handling
2. **PaymentService**: Centralized payment processing logic
3. **WaafipayPaymentProcessor**: UI component for payment flow

#### **Centralized Components**
```
┌─────────────────────────────────┐
│        Payment Service          │
├─────────────────────────────────┤
│  - Unified payment processing   │
│  - Error handling               │
│  - Status management            │
│  - Notification handling        │
└─────────────────────────────────┘
           ↓
┌─────────────────────────────────┐
│       WaafiPay Service          │
├─────────────────────────────────┤
│  - API communication            │
│  - Payload formatting           │
│  - Response processing          │
│  - Credential management        │
└─────────────────────────────────┘
```

### **Database Centralization**

#### **Payment Records**
- Centralized payment tracking
- Unified status management
- Cross-reference with orders
- Audit trail maintenance

#### **Settings Management**

- Environment-specific settings
- Credential encryption and storage
- Change tracking and versioning

### **UI Component Reusability**

#### **Reusable Components**
1. **waafipay-payment**: Main payment component
2. **waafipay-modal-payment**: Modal-based payment
3. **waafipay-phone-input**: Phone number input with validation
4. **waafipay-provider-selector**: Provider selection interface



### **Benefits of Centralization**

#### **Consistency**
- Uniform payment experience across all flows
- Consistent error handling and messaging
- Standardized UI components and styling

#### **Maintainability**
- Single source of truth for payment logic
- Centralized credential management
- Unified testing and debugging

#### **Scalability**
- Easy addition of new payment flows
- Reusable components for different contexts
- Simplified integration for new features

---

## 📚 Implementation Guidelines

### **Integration Checklist**
- [ ] Configure WaafiPay credentials
- [ ] Test payment flow in sandbox
- [ ] Implement error handling
- [ ] Add payment status polling
- [ ] Configure webhook endpoints (if available)
- [ ] Test with real mobile wallets
- [ ] Implement proper logging
- [ ] Add monitoring and alerts

### **Best Practices**
- Always validate phone numbers before API calls
- Implement proper timeout handling (90+ seconds)
- Use centralized error message mapping
- Log all payment attempts for debugging
- Implement proper security measures
- Test with all supported mobile wallet providers
- Provide clear user feedback throughout the process

This documentation provides a complete foundation for implementing WaafiPay payment processing in any application, ensuring consistency, reliability, and excellent user experience.

UI Structure
The Payment Methods tab now looks like this

┌─────────────────────────────────────────────────────────┐
│ [WaafiPay Logo] WaafiPay Configuration                  │
│                 Configure WaafiPay payment gateway...    │
├─────────────────────────────────────────────────────────┤
│ ☑ Enable WaafiPay                                       │
│                                                          │
│ Select Environment        MERCHANT_U_ID                 │
│ [LIVE ▼]                  [M0914022]                    │
│                                                          │
│ API_USER_ID               MERCHANT_NO                   │
│ [1008345]                 [619994432]                   │
│                                                          │
│ API_KEY                   API URL                       │
│ [••••••••••] 👁           [https://api.waafipay...]    │
│                                                          │
│ ┌─────────────────────────────────────────────────────┐ │
│ │ 🧪 Test WaafiPay Configuration                      │ │
│ │ After saving your WaafiPay settings, you can test   │ │
│ │ the configuration by sending a test payment.        │ │
│ │                                                      │ │
│ │ [Test WaafiPay]                                     │ │
│ └─────────────────────────────────────────────────────┘ │
│                                                          │
│ ℹ️ WaafiPay Credentials Format:                         │
│   MERCHANT_U_ID=M1234567                                │
│   API_USER_ID=1234567                                   │
│   ...                                                    │
└─────────────────────────────────────────────────────────┘

[Save] [Cancel]  ← Standard form buttons at bottom
