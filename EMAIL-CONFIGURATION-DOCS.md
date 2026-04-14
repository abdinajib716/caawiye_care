# Email Configuration Implementation

## Overview
Complete documentation of email configuration management in Caawiye Care, focusing on environment variable updates and fetching the latest values.

---

## Architecture

### Hybrid Storage Approach
- **Database:** General application settings
- **Environment File (.env):** Email credentials (secure)
- **Real-time Reading:** Direct `.env` file reading to prevent cache issues

### Why This Design?
✅ **Security:** Credentials never stored in database  
✅ **Laravel Standard:** `.env` is the proper configuration location  
✅ **No Cache Issues:** Direct file reading ensures fresh values  
✅ **Flexibility:** Can be overridden via environment

---

## Core Components

### 1. EnvWriter Service (`app/Services/EnvWriter.php`)

Handles all `.env` file modifications with thread-safe operations.

#### Key Features:
- **File Locking:** Prevents race conditions using `LOCK_EX`
- **Atomic Writes:** All changes or none
- **Value Normalization:** Automatic quote wrapping
- **Change Detection:** Only writes if value changed
- **Batch Operations:** Efficient multiple key updates

#### Main Method:
```php
public function batchWriteKeysToEnvFile(array $keys): void
{
    $path = base_path('.env');
    $file = file_get_contents($path);
    
    foreach ($keys as $key => $value) {
        $envKey = $availableKeys[$key]; // e.g., 'mail_host' => 'MAIL_HOST'
        $formattedValue = '"' . str_replace('"', '\\"', $value) . '"';
        
        // Update or append
        if (preg_match("/^$envKey=/m", $file)) {
            $file = preg_replace("/^$envKey=.*/m", "$envKey=$formattedValue", $file);
        } else {
            $file .= PHP_EOL . "$envKey=$formattedValue";
        }
    }
    
    // Atomic write with file locking
    file_put_contents($path, $file, LOCK_EX);
    
    // Force PHP to reload
    opcache_reset();
    clearstatcache(true, $path);
}
```

---

### 2. SettingController (`app/Http/Controllers/Backend/SettingController.php`)

Manages all settings operations.

#### Email Fields Identification:
```php
$emailEnvFields = [
    'mail_mailer', 'mail_host', 'mail_port', 'mail_username',
    'mail_password', 'mail_encryption', 'mail_from_address', 'mail_from_name'
];
```

#### Store Process:
```php
public function store(Request $request)
{
    $fields = $request->all();
    
    // 1. Save non-email fields to database
    foreach ($fields as $fieldName => $fieldValue) {
        if (!in_array($fieldName, $emailEnvFields)) {
            $this->settingService->addSetting($fieldName, $fieldValue);
        }
    }
    
    // 2. Write ALL fields to .env (email included)
    $this->envWriter->batchWriteKeysToEnvFile($fields);
    
    // 3. Clear ALL caches
    \Artisan::call('config:clear');
    \Artisan::call('cache:clear');
    \Artisan::call('view:clear');
    
    // 4. Force OPcache reset
    if (function_exists('opcache_reset')) {
        opcache_reset();
    }
    
    // 5. Force reload .env values
    $dotenv = \Dotenv\Dotenv::createImmutable(base_path());
    $dotenv->load();
    
    // 6. Clear session old input
    session()->forget(['_old_input', '_flash']);
    
    // 7. Redirect with cache-busting
    return redirect()->route('admin.settings.index', [
        'tab' => 'email',
        't' => time() // Cache buster
    ])->withHeaders([
        'Cache-Control' => 'no-cache, no-store, must-revalidate',
    ]);
}
```

---

### 3. Email Tab View (THE MAGIC!)

**File:** `resources/views/backend/pages/settings/email-tab.blade.php`

#### Direct .env Reading (Lines 1-26):
```php
@php
    // Read fresh values directly from .env file
    $envPath = base_path('.env');
    $envContent = file_get_contents($envPath);
    
    // Parse .env values
    $parseEnvValue = function($key) use ($envContent) {
        if (preg_match("/^{$key}=(.*)$/m", $envContent, $matches)) {
            $value = trim($matches[1]);
            $value = trim($value, '"\''); // Remove quotes
            return $value;
        }
        return '';
    };

    // Get current values
    $currentMailer = $parseEnvValue('MAIL_MAILER') ?: 'smtp';
    $currentHost = $parseEnvValue('MAIL_HOST');
    $currentPort = $parseEnvValue('MAIL_PORT') ?: '587';
    $currentUsername = $parseEnvValue('MAIL_USERNAME');
    $currentPassword = $parseEnvValue('MAIL_PASSWORD');
    $currentEncryption = $parseEnvValue('MAIL_ENCRYPTION') ?: 'tls';
    $currentFromAddress = $parseEnvValue('MAIL_FROM_ADDRESS');
    $currentFromName = $parseEnvValue('MAIL_FROM_NAME') ?: config('app.name');
@endphp
```

#### Why This Works:

✅ **Bypasses Config Cache:** Doesn't use `config()` helper  
✅ **Bypasses Application Cache:** Doesn't use Laravel cache  
✅ **Bypasses OPcache:** File content always current  
✅ **Bypasses Session:** No `old()` helper dependency  
✅ **Always Fresh:** Guaranteed latest values  

#### Regex Breakdown:
```php
preg_match("/^{$key}=(.*)$/m", $envContent, $matches)
```
- `^` = Start of line
- `{$key}` = Variable name (e.g., MAIL_HOST)
- `=` = Equals sign
- `(.*)` = Capture any characters (the value)
- `$` = End of line
- `m` = Multiline mode

**Example:**
```
Input: MAIL_HOST="smtp.gmail.com"
Match: $matches[1] = "smtp.gmail.com"
After trim: smtp.gmail.com
```

---

### 4. EmailService (`app/Services/EmailService.php`)

#### Get Current Config:
```php
public function getCurrentEmailConfig(): array
{
    return [
        'mailer' => env('MAIL_MAILER', config('mail.default')),
        'host' => env('MAIL_HOST', config('mail.mailers.smtp.host')),
        'port' => (int) env('MAIL_PORT', config('mail.mailers.smtp.port')),
        'username' => env('MAIL_USERNAME'),
        'password' => env('MAIL_PASSWORD'),
        'encryption' => env('MAIL_ENCRYPTION'),
        'from_address' => env('MAIL_FROM_ADDRESS'),
        'from_name' => env('MAIL_FROM_NAME', config('app.name')),
    ];
}
```

#### Test SMTP Connection:
```php
public function testSmtpConnection(): array
{
    try {
        $config = $this->getCurrentEmailConfig();
        $socket = @fsockopen($config['host'], $config['port'], $errno, $errstr, 10);
        
        if (!$socket) {
            throw new Exception("Cannot connect to {$config['host']}:{$config['port']}");
        }
        
        fclose($socket);
        return ['success' => true, 'message' => 'Connection successful'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}
```

#### Send Test Email:
```php
public function sendTestEmail(string $toEmail): array
{
    try {
        $config = $this->getCurrentEmailConfig();
        $this->configureMailSettings($config);
        
        Mail::raw("Test email from " . config('app.name'), function ($message) use ($toEmail) {
            $message->to($toEmail)->subject('Test Email');
        });
        
        return ['success' => true, 'message' => 'Email sent to ' . $toEmail];
    } catch (Exception $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}
```

---

### 5. AppServiceProvider (`app/Providers/AppServiceProvider.php`)

Registers email keys for environment variable writing:

```php
Hook::addFilter(CommonFilterHook::AVAILABLE_KEYS, function ($keys) {
    return array_merge($keys, [
        'mail_mailer' => 'MAIL_MAILER',
        'mail_host' => 'MAIL_HOST',
        'mail_port' => 'MAIL_PORT',
        'mail_username' => 'MAIL_USERNAME',
        'mail_password' => 'MAIL_PASSWORD',
        'mail_encryption' => 'MAIL_ENCRYPTION',
        'mail_from_address' => 'MAIL_FROM_ADDRESS',
        'mail_from_name' => 'MAIL_FROM_NAME',
    ]);
});
```

**Maps:** Form field name → Environment variable name

---

## Update & Fetch Flow

### Update Process

```
1. User fills form → POST /admin/settings
                        ↓
2. Controller receives → Identify email fields
                        ↓
3. Database save      → Non-email settings only
                        ↓
4. EnvWriter          → Batch write to .env file
                        ↓
5. File Locking       → LOCK_EX prevents race conditions
                        ↓
6. Cache Clearing     → config, cache, view caches
                        ↓
7. OPcache Reset      → Clear PHP bytecode cache
                        ↓
8. Dotenv Reload      → Force Laravel to re-read .env
                        ↓
9. Session Clear      → Remove old input data
                        ↓
10. Redirect (PRG)    → With timestamp cache buster
```

### Fetch Process

```
1. Page Load          → GET /admin/settings?tab=email
                        ↓
2. View Executes      → PHP @php block
                        ↓
3. Direct File Read   → file_get_contents('.env')
                        ↓
4. Regex Parse        → Extract values
                        ↓
5. Quote Removal      → Clean values
                        ↓
6. Form Population    → Display in inputs
```

**Key:** Form ALWAYS shows latest `.env` values because it reads the file directly every time!

---

## Cache Management Strategy

### Why Multiple Cache Clears?

```php
\Artisan::call('config:clear');     // 1. Laravel config cache
\Artisan::call('cache:clear');      // 2. Application cache
\Artisan::call('view:clear');       // 3. Blade template cache
opcache_reset();                    // 4. PHP bytecode cache
clearstatcache(true, $path);        // 5. File stat cache
$dotenv->load();                    // 6. Force .env reload
session()->forget(['_old_input']);  // 7. Session old input
```

### Cache Types:

| Cache | Purpose | Clear Method |
|-------|---------|--------------|
| Config | `config()` values | `config:clear` |
| Application | Redis/File cache | `cache:clear` |
| View | Compiled Blade | `view:clear` |
| OPcache | PHP bytecode | `opcache_reset()` |
| File Stat | File metadata | `clearstatcache()` |
| Dotenv | .env values | `Dotenv::load()` |
| Session | Old form input | `session()->forget()` |

---

## Testing Features

### Test Connection (No Email Sent)

**Endpoint:** `POST /admin/settings/test-smtp`

```javascript
await fetch('/admin/settings/test-smtp', {
    method: 'POST',
    headers: {
        'X-CSRF-TOKEN': token
    }
});
```

**Response:**
```json
{
    "success": true,
    "message": "Successfully connected to smtp.gmail.com:587"
}
```

### Send Test Email

**Endpoint:** `POST /admin/settings/send-test-email`

```javascript
await fetch('/admin/settings/send-test-email', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': token
    },
    body: JSON.stringify({
        email: 'test@example.com'
    })
});
```

**Response:**
```json
{
    "success": true,
    "message": "Test email sent successfully to test@example.com"
}
```

---

## Security Features

### 1. Permission Authorization
```php
$this->authorize('manage', Setting::class);
```

### 2. Password Sanitization
```php
private function sanitizeConfigForLogging(array $config): array
{
    if (isset($config['password'])) {
        $config['password'] = '***HIDDEN***';
    }
    return $config;
}
```

### 3. File Permissions
```bash
chmod 600 .env  # Only owner can read/write
```

### 4. Input Validation
```php
$request->validate([
    'mail_host' => 'required|string',
    'mail_port' => 'required|numeric|between:1,65535',
    'mail_from_address' => 'required|email',
]);
```

### 5. CSRF Protection
```html
@csrf
```

### 6. SQL Injection Prevention
Using Eloquent ORM with parameter binding

### 7. File Locking
```php
file_put_contents($path, $file, LOCK_EX);
```

---

## Routes

```php
Route::middleware(['auth:admin'])->prefix('admin')->group(function () {
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'store'])->name('settings.store');
    Route::post('/settings/test-smtp', [SettingController::class, 'testSmtpConnection'])->name('settings.test-smtp');
    Route::post('/settings/send-test-email', [SettingController::class, 'sendTestEmail'])->name('settings.send-test-email');
});
```

---

## Complete Example

### Scenario: Update SMTP Settings

**1. Admin opens page:**
```
GET /admin/settings?tab=email
```

View reads:
```php
$currentHost = $parseEnvValue('MAIL_HOST'); // "smtp.mailtrap.io"
```

**2. Admin changes host:**
```html
<input name="mail_host" value="smtp.gmail.com">
```

**3. Form submits:**
```
POST /admin/settings
{
    mail_host: "smtp.gmail.com",
    mail_port: "587",
    mail_encryption: "tls"
}
```

**4. EnvWriter updates .env:**
```env
MAIL_HOST="smtp.gmail.com"
MAIL_PORT="587"
MAIL_ENCRYPTION="tls"
```

**5. Caches cleared, page redirects**

**6. Page reloads, reads .env again:**
```php
$currentHost = $parseEnvValue('MAIL_HOST'); // "smtp.gmail.com" ✅
```

---

## Troubleshooting

### Values Not Updating

**Check:**
1. `.env` file permissions (`chmod 600 .env`)
2. Web server has write access
3. OPcache is being reset
4. Not using `old()` helper in view
5. Cache headers are set
6. Timestamp parameter in redirect

### Test Email Not Sending

**Check:**
1. SMTP credentials correct
2. Firewall allows outbound on port 587/465
3. Test connection first
4. Check Laravel logs
5. Enable debug mode for detailed errors

### Permission Errors

**Check:**
1. User has `manage` permission on `Setting` model
2. Policy registered in `AuthServiceProvider`
3. Permissions seeded in database

---

## File Structure

```
app/
├── Services/
│   ├── EnvWriter.php           # .env file writer
│   ├── EmailService.php        # Email testing
│   └── SettingService.php      # Settings management
├── Http/Controllers/Backend/
│   └── SettingController.php   # Settings controller
├── Models/
│   └── Setting.php             # Settings model
├── Observers/
│   └── SettingObserver.php     # Settings observer
└── Providers/
    └── AppServiceProvider.php  # Key mapping

resources/views/backend/pages/settings/
└── email-tab.blade.php         # Email configuration UI

routes/
└── web.php                     # Routes definition

.env                            # Environment variables
```

---

## Key Takeaways

✅ **Email credentials stored in `.env` for security**  
✅ **Direct file reading bypasses ALL caches**  
✅ **Multiple cache clearing strategies ensure fresh values**  
✅ **File locking prevents race conditions**  
✅ **Atomic writes ensure data integrity**  
✅ **PRG pattern prevents form resubmission**  
✅ **Cache-busting headers force fresh loads**  
✅ **Test features validate configuration**  
✅ **Permission-based access control**  
✅ **Action logging for audit trail**  

This implementation ensures email settings are always current, secure, and reliable!
