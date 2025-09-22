<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $notificationMessage }} - {{ $appName }}</title>
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
        .notification-badge {
            padding: 15px 20px;
            border-radius: 5px;
            text-align: center;
            margin: 20px 0;
            border: 1px solid;
        }
        .notification-info {
            background: #d1ecf1;
            color: #0c5460;
            border-color: #bee5eb;
        }
        .notification-success {
            background: #d4edda;
            color: #155724;
            border-color: #c3e6cb;
        }
        .notification-warning {
            background: #fff3cd;
            color: #856404;
            border-color: #ffeaa7;
        }
        .notification-error {
            background: #f8d7da;
            color: #721c24;
            border-color: #f5c6cb;
        }
        .data-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #007bff;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        .data-table th,
        .data-table td {
            padding: 8px 12px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        .data-table th {
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
            <h1>{{ $appName }}</h1>
            <p>System Notification</p>
        </div>

        <div class="notification-badge notification-{{ $notificationType }}">
            @switch($notificationType)
                @case('success')
                    <strong>✅ Success:</strong>
                    @break
                @case('warning')
                    <strong>⚠️ Warning:</strong>
                    @break
                @case('error')
                    <strong>❌ Error:</strong>
                    @break
                @default
                    <strong>ℹ️ Information:</strong>
            @endswitch
            {{ $notificationMessage }}
        </div>

        @if(!empty($notificationData))
        <div class="data-section">
            <h3>📋 Additional Details</h3>
            <table class="data-table">
                @foreach($notificationData as $key => $value)
                <tr>
                    <th>{{ ucfirst(str_replace('_', ' ', $key)) }}</th>
                    <td>
                        @if(is_array($value))
                            {{ json_encode($value, JSON_PRETTY_PRINT) }}
                        @elseif(is_bool($value))
                            {{ $value ? 'Yes' : 'No' }}
                        @else
                            {{ $value }}
                        @endif
                    </td>
                </tr>
                @endforeach
            </table>
        </div>
        @endif

        <div class="data-section">
            <h3>🕒 Notification Details</h3>
            <table class="data-table">
                <tr>
                    <th>Timestamp</th>
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
                <tr>
                    <th>Notification Type</th>
                    <td>{{ ucfirst($notificationType) }}</td>
                </tr>
            </table>
        </div>

        <div class="footer">
            <p>This is an automated notification from <strong>{{ $appName }}</strong></p>
            <p>If you received this email unexpectedly, please contact your system administrator.</p>
            <p><small>Generated at {{ $timestamp }}</small></p>
        </div>
    </div>
</body>
</html>
