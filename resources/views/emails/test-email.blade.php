<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Email - {{ $appName }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .email-container {
            background: white;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #2c3e50;
            margin: 0;
            font-size: 24px;
        }
        .success-badge {
            background: #d4edda;
            color: #155724;
            padding: 10px 20px;
            border-radius: 5px;
            text-align: center;
            margin: 20px 0;
            border: 1px solid #c3e6cb;
        }
        .info-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #007bff;
        }
        .config-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        .config-table th,
        .config-table td {
            padding: 8px 12px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        .config-table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #495057;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            color: #6c757d;
            font-size: 14px;
        }
        .timestamp {
            font-family: 'Courier New', monospace;
            background: #f8f9fa;
            padding: 5px 10px;
            border-radius: 3px;
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>✅ Email Test Successful</h1>
            <p>{{ $appName }} Email Configuration Test</p>
        </div>

        <div class="success-badge">
            <strong>🎉 Congratulations!</strong> Your email configuration is working correctly.
        </div>

        <div class="info-section">
            <h3>📧 Test Details</h3>
            <table class="config-table">
                <tr>
                    <th>Recipient Email</th>
                    <td>{{ $testData['recipient_email'] }}</td>
                </tr>
                <tr>
                    <th>Test Time</th>
                    <td><span class="timestamp">{{ $timestamp }}</span></td>
                </tr>
                <tr>
                    <th>Application</th>
                    <td>{{ $appName }}</td>
                </tr>
                <tr>
                    <th>Application URL</th>
                    <td><a href="{{ $appUrl }}">{{ $appUrl }}</a></td>
                </tr>
            </table>
        </div>

        @if(isset($testData['config_used']))
        <div class="info-section">
            <h3>⚙️ Email Configuration Used</h3>
            <table class="config-table">
                <tr>
                    <th>Mail Driver</th>
                    <td>{{ $testData['config_used']['mailer'] ?? 'Not specified' }}</td>
                </tr>
                <tr>
                    <th>SMTP Host</th>
                    <td>{{ $testData['config_used']['host'] ?? 'Not specified' }}</td>
                </tr>
                <tr>
                    <th>SMTP Port</th>
                    <td>{{ $testData['config_used']['port'] ?? 'Not specified' }}</td>
                </tr>
                <tr>
                    <th>Encryption</th>
                    <td>{{ $testData['config_used']['encryption'] ?: 'None' }}</td>
                </tr>
                <tr>
                    <th>From Address</th>
                    <td>{{ $testData['config_used']['from_address'] ?? 'Not specified' }}</td>
                </tr>
                <tr>
                    <th>From Name</th>
                    <td>{{ $testData['config_used']['from_name'] ?? 'Not specified' }}</td>
                </tr>
            </table>
        </div>
        @endif

        <div class="info-section">
            <h3>✨ What This Means</h3>
            <ul>
                <li><strong>Email Delivery:</strong> Your application can successfully send emails</li>
                <li><strong>SMTP Connection:</strong> Connection to your email server is working</li>
                <li><strong>Authentication:</strong> Email credentials are valid (if required)</li>
                <li><strong>Configuration:</strong> All email settings are properly configured</li>
            </ul>
        </div>

        <div class="info-section">
            <h3>🚀 Next Steps</h3>
            <p>Your email system is ready for:</p>
            <ul>
                <li>User registration confirmations</li>
                <li>Password reset emails</li>
                <li>System notifications</li>
                <li>Application alerts and updates</li>
            </ul>
        </div>

        <div class="footer">
            <p>This is an automated test email from <strong>{{ $appName }}</strong></p>
            <p>If you received this email unexpectedly, please contact your system administrator.</p>
            <p><small>Generated at {{ $timestamp }}</small></p>
        </div>
    </div>
</body>
</html>
