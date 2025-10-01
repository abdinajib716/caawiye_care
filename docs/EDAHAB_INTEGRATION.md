# eDahab Payment Integration Documentation

## 📋 Overview

eDahab is Somalia's mobile money payment gateway operated by **Somtel Telecom**. It enables businesses to accept payments from Somtel mobile wallet users. This documentation provides a comprehensive guide for implementing eDahab payment processing in the Caawiye Care healthcare application.

---

## 🏢 About eDahab

- **Provider**: Somtel Telecom
- **Service**: Mobile Money Payment Gateway
- **Phone Number Prefixes**: `62`, `65`
- **Supported Currencies**: USD, SLSH (Somali Shilling)
- **Payment Methods**: Web Portal, Mobile Pop-up

---

## 🏗️ Business Logic and Architecture

### **Payment Flow**

#### **1. Payment Initiation**
- Customer provides their eDahab phone number (starting with 62 or 65)
- Merchant specifies the payment amount and currency
- System generates an invoice using eDahab API
- Invoice ID is returned for payment processing

#### **2. Payment Processing Methods**

**Method A: Web Portal Payment**
- Customer is redirected to eDahab's web payment portal
- URL format: `https://edahab.net/API/Payment?invoiceId={{invoiceId}}`
- Customer enters their PIN on the eDahab website
- After completion, customer is redirected to the merchant's return URL

**Method B: Mobile Pop-up Payment**
- eDahab sends a payment request pop-up to the customer's phone
- Customer approves or declines the payment on their device
- System automatically receives the payment status
- No manual redirect needed

#### **3. Payment Verification**
- Merchant can check invoice status using the Invoice ID
- Status can be: Pending, Success, Failed, etc.
- Transaction ID is provided upon successful payment

#### **4. Account Credit (Withdrawal)**
- Merchant can withdraw funds from eDahab API balance to their account
- Supports both regular and merchant accounts
- Instant transfer with transaction confirmation

---

## 🔑 API Credentials

To integrate eDahab, you need the following credentials from the eDahab team:

| Credential | Description | Example Format |
|------------|-------------|----------------|
| **API_KEY** | Your unique API key | `api_key_xxxxxxxxxxxxx` |
| **SECRET_KEY** | Secret key for request signing | `secret_key_xxxxxxxxxxxxx` |
| **AGENT_CODE** | Your merchant agent code | `12345` |
| **ENVIRONMENT** | Test or Production | `test` / `production` |

---

## 📡 API Endpoints

### **Base URLs**

- **Production**: `https://edahab.net/api/`
- **Test/Sandbox**: *(To be confirmed with eDahab team)*

---

## 🔐 Authentication & Security

### **Request Signing**

All eDahab API requests must be signed using SHA-256 hashing:

1. Convert the request body to a JSON string
2. Concatenate: `requestAsString + SECRET_KEY`
3. Generate SHA-256 hash of the concatenated string
4. Include the hash in the request headers

**Example (PHP):**
```php
$requestAsString = json_encode($requestData);
$hashValue = hash('sha256', $requestAsString . $secretKey);

$headers = [
    'Content-Type: application/json',
    'X-Hash: ' . $hashValue
];
```

---

## 📝 API Methods

### **1. Create Invoice (Generate Payment)**

**Purpose**: Generate a payment invoice for a customer

**Request Body:**
```json
{
    "apiKey": "your_api_key",
    "edahabNumber": "62XXXXXXX",
    "amount": 1.00,
    "currency": "USD",
    "agentCode": "12345",
    "returnUrl": "https://yourdomain.com/payment/callback"
}
```

**Field Descriptions:**

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `apiKey` | string | Yes | Your API key |
| `edahabNumber` | string | Yes | Customer's eDahab phone number (62XXXXXXX or 65XXXXXXX) |
| `amount` | decimal | Yes | Payment amount |
| `currency` | string | No | Currency code (USD or SLSH). Default: USD |
| `agentCode` | string | Yes | Your merchant agent code |
| `returnUrl` | string | Yes | Callback URL after payment (must start with https://) |

**Response:**
```json
{
    "InvoiceId": 1122334,
    "StatusCode": 0,
    "RequestId": 332211,
    "StatusDescription": "Success",
    "ValidationErrors": null
}
```

**Status Codes:**

| Code | Description |
|------|-------------|
| 0 | Success |
| 1 | API Error |
| 2 | Invalid JSON |
| 3 | Validation Error |
| 4 | Invalid API Credentials |
| 5 | Insufficient Customer Balance |
| 6 | Invoice Not Found |

---

### **2. Check Invoice Status**

**Purpose**: Verify the payment status of an invoice

**Request Body:**
```json
{
    "apiKey": "your_api_key",
    "invoiceId": 1122334
}
```

**Response:**
```json
{
    "InvoiceStatus": "Pending",
    "TransactionId": "TX123456789",
    "InvoiceId": 1122334,
    "StatusCode": 0,
    "RequestId": 33221,
    "StatusDescription": "Success",
    "ValidationErrors": null
}
```

**Invoice Status Values:**
- `Pending` - Payment not yet completed
- `Success` - Payment completed successfully
- `Failed` - Payment failed
- `Cancelled` - Payment cancelled by user
- `Expired` - Invoice expired

---

### **3. Credit Account (Withdrawal)**

**Purpose**: Withdraw funds from eDahab API balance to your account

**Request Body:**
```json
{
    "apiKey": "your_api_key",
    "phoneNumber": "62XXXXXXX",
    "transactionAmount": 100.00,
    "currency": "USD"
}
```

**Field Descriptions:**

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `apiKey` | string | Yes | Your API key |
| `phoneNumber` | string | Yes | Your eDahab account number |
| `transactionAmount` | decimal | Yes | Amount to withdraw |
| `currency` | string | No | Currency code (USD or SLSH). Default: USD |

**Response:**
```json
{
    "TransactionStatus": "Approved",
    "TransactionMessage": "Your withdrawal of $100.00 has been processed successfully",
    "PhoneNumber": "62XXXXXXX",
    "TransactionId": "CX23322.4444.S22222"
}
```

---

## 📱 Phone Number Validation

### **eDahab Phone Number Format**

- **Country Code**: +252
- **Prefixes**: 62, 65 (Somtel)
- **Length**: 9 digits (without country code)
- **Full Format**: +252 62XXXXXXX or +252 65XXXXXXX

**Validation Rules:**
```php
function validateEdahabNumber($phone) {
    // Remove spaces and country code
    $phone = preg_replace('/[^0-9]/', '', $phone);
    $phone = preg_replace('/^252/', '', $phone);
    
    // Check if starts with 62 or 65 and is 9 digits
    return preg_match('/^(62|65)\d{7}$/', $phone);
}
```

**Examples:**
- ✅ Valid: `62XXXXXXX`, `65XXXXXXX`, `+252 62XXXXXXX`, `252 65XXXXXXX`
- ❌ Invalid: `61XXXXXXX`, `63XXXXXXX`, `62XXXXX` (too short)

---

## 💻 Implementation Examples

### **PHP/Laravel Example**

```php
class EdahabService
{
    private $apiKey;
    private $secretKey;
    private $agentCode;
    private $isProduction;
    
    public function __construct()
    {
        $this->apiKey = config('services.edahab.api_key');
        $this->secretKey = config('services.edahab.secret_key');
        $this->agentCode = config('services.edahab.agent_code');
        $this->isProduction = config('services.edahab.is_production');
    }
    
    public function createInvoice($edahabNumber, $amount, $currency = 'USD', $returnUrl)
    {
        $requestData = [
            'apiKey' => $this->apiKey,
            'edahabNumber' => $this->formatPhoneNumber($edahabNumber),
            'amount' => $amount,
            'currency' => $currency,
            'agentCode' => $this->agentCode,
            'returnUrl' => $returnUrl
        ];
        
        $requestAsString = json_encode($requestData);
        $hashValue = hash('sha256', $requestAsString . $this->secretKey);
        
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'X-Hash' => $hashValue
        ])->post($this->getBaseUrl() . '/invoice/create', $requestData);
        
        return $response->json();
    }
    
    public function checkInvoice($invoiceId)
    {
        $requestData = [
            'apiKey' => $this->apiKey,
            'invoiceId' => $invoiceId
        ];
        
        $requestAsString = json_encode($requestData);
        $hashValue = hash('sha256', $requestAsString . $this->secretKey);
        
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'X-Hash' => $hashValue
        ])->post($this->getBaseUrl() . '/invoice/check', $requestData);
        
        return $response->json();
    }
    
    private function formatPhoneNumber($phone)
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Remove country code if present
        $phone = preg_replace('/^252/', '', $phone);
        
        return $phone;
    }
    
    private function getBaseUrl()
    {
        return $this->isProduction 
            ? 'https://edahab.net/api' 
            : 'https://edahab.net/api'; // Update with test URL when available
    }
}
```

---

## 🔄 Integration Workflow

### **Step-by-Step Implementation**

1. **Configuration Setup**
   - Store API credentials securely in environment variables
   - Configure test/production environment
   - Set up return URLs for payment callbacks

2. **Payment Initiation**
   - Validate customer's eDahab phone number
   - Create invoice using eDahab API
   - Store invoice details in database

3. **Payment Processing**
   - Redirect customer to eDahab payment portal OR
   - Wait for mobile pop-up confirmation
   - Handle payment callback

4. **Payment Verification**
   - Check invoice status periodically
   - Update order/transaction status
   - Send confirmation to customer

5. **Webhook/Callback Handling**
   - Receive payment status updates
   - Validate callback authenticity
   - Update database records

---

## 🗄️ Database Schema

### **Recommended Tables**

**edahab_transactions**
```sql
CREATE TABLE edahab_transactions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_id BIGINT UNSIGNED,
    invoice_id VARCHAR(50) UNIQUE,
    request_id VARCHAR(50),
    transaction_id VARCHAR(100) NULL,
    edahab_number VARCHAR(20),
    amount DECIMAL(10, 2),
    currency VARCHAR(10) DEFAULT 'USD',
    status VARCHAR(20) DEFAULT 'pending',
    status_code INT,
    status_description TEXT,
    return_url TEXT,
    payment_method VARCHAR(20) DEFAULT 'web',
    request_payload JSON,
    response_payload JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_customer_id (customer_id),
    INDEX idx_invoice_id (invoice_id),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
);
```

---

## ⚠️ Error Handling

### **Common Errors and Solutions**

| Error | Cause | Solution |
|-------|-------|----------|
| Invalid API Credentials | Wrong API key or secret key | Verify credentials with eDahab team |
| Invalid Phone Number | Phone doesn't start with 62/65 | Validate phone number format |
| Insufficient Balance | Customer has insufficient funds | Ask customer to top up their account |
| Invalid Return URL | URL doesn't start with https:// | Ensure return URL uses HTTPS |
| Invoice Not Found | Invalid invoice ID | Check invoice ID and try again |

---

## 🧪 Testing

### **Test Credentials**
- Request test credentials from eDahab team
- Use test phone numbers provided by eDahab
- Test with small amounts (e.g., $1)

### **Test Scenarios**
1. ✅ Successful payment
2. ❌ Insufficient balance
3. ⏱️ Payment timeout
4. 🚫 User cancellation
5. 🔄 Payment status check

---

## 📚 Resources

- **Blog Tutorial**: [https://abdorizak.dev/blog/e-dahab-integration](https://abdorizak.dev/blog/e-dahab-integration)
- **GitHub SDK**: [https://github.com/iamshabell/edahab](https://github.com/iamshabell/edahab)
- **eDahab Website**: [https://edahab.net](https://edahab.net)

---

## 🔜 Next Steps for Implementation

### **Phase 1: Configuration & Setup**
- [ ] Obtain API credentials from eDahab team
- [ ] Create configuration settings in admin panel
- [ ] Set up database tables
- [ ] Implement EdahabService class

### **Phase 2: Core Integration**
- [ ] Implement invoice creation
- [ ] Implement invoice status checking
- [ ] Build payment callback handler
- [ ] Add phone number validation

### **Phase 3: UI/UX**
- [ ] Create payment form
- [ ] Build payment status page
- [ ] Add transaction history
- [ ] Implement test payment modal

### **Phase 4: Testing & Deployment**
- [ ] Test with sandbox credentials
- [ ] Perform end-to-end testing
- [ ] Deploy to production
- [ ] Monitor transactions

---

## 📞 Support

For eDahab API support and credentials:
- **Contact**: eDahab Support Team
- **Website**: [https://edahab.net](https://edahab.net)

---

## 📝 Notes

- eDahab is specifically for **Somtel** customers (phone numbers starting with 62 or 65)
- For other telecom providers, use **WaafiPay** (already implemented)
- Always use HTTPS for return URLs
- Store credentials securely using environment variables
- Implement proper error logging for debugging

---

**Document Version**: 1.0  
**Last Updated**: 2025-10-01  
**Status**: Ready for Implementation

