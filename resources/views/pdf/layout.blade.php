<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', config('app.name'))</title>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #333;
            padding: 20px;
        }

        /* A5 page size */
        @page {
            size: A5 portrait;
            margin: 15mm;
        }

        /* Header */
        .header {
            margin-bottom: 20px;
            border-bottom: 2px solid #2d572c;
            padding-bottom: 15px;
        }

        .logo {
            width: 100px;
            height: auto;
            margin-bottom: 10px;
        }

        .company-info {
            text-align: right;
            font-size: 9pt;
            line-height: 1.3;
        }

        .company-name {
            font-weight: bold;
            font-size: 11pt;
            color: #2d572c;
            margin-bottom: 2px;
        }

        .document-title {
            color: #d4a017;
            font-size: 18pt;
            font-weight: bold;
            margin: 15px 0;
        }

        /* Info Section */
        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 9pt;
        }

        .info-label {
            font-weight: bold;
            color: #555;
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        table th {
            background-color: #f8f8f8;
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
            font-size: 9pt;
            font-weight: bold;
        }

        table td {
            border: 1px solid #ddd;
            padding: 6px;
            font-size: 9pt;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        /* Summary Section */
        .summary-section {
            float: right;
            width: 45%;
            margin-top: 10px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 4px 8px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
        }

        .summary-row.total {
            background-color: #d4a017;
            color: white;
            font-weight: bold;
            font-size: 10pt;
        }

        /* Footer */
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            font-size: 8pt;
            text-align: center;
            color: #666;
        }

        .contact-info {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            font-size: 8pt;
        }

        .page-number {
            text-align: right;
            color: #d4a017;
            font-weight: bold;
        }

        /* Status Badges */
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8pt;
            font-weight: bold;
        }

        .badge-confirmed {
            background-color: #10b981;
            color: white;
        }

        .badge-pending {
            background-color: #f59e0b;
            color: white;
        }

        .badge-cancelled {
            background-color: #ef4444;
            color: white;
        }

        .badge-completed {
            background-color: #3b82f6;
            color: white;
        }

        /* Print-specific */
        @media print {
            body {
                padding: 0;
            }
            
            .no-print {
                display: none !important;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Header -->
    <div class="header">
        <table style="border: none; margin: 0;">
            <tr>
                <td style="border: none; width: 30%; vertical-align: top;">
                    @if(config('settings.logo'))
                        <img src="{{ public_path(config('settings.logo')) }}" alt="Logo" class="logo">
                    @else
                        <div style="font-weight: bold; font-size: 14pt; color: #2d572c;">
                            {{ config('app.name') }}
                        </div>
                    @endif
                </td>
                <td style="border: none; width: 70%; text-align: right; vertical-align: top;">
                    <div class="company-info">
                        <div class="company-name">{{ config('app.name') }}</div>
                        <div>{{ config('settings.address', 'Mogadishu, Somalia') }}</div>
                        <div>{{ config('settings.phone', '+252 61 XXX XXXX') }}</div>
                        <div>{{ config('settings.email', 'info@caawiyecare.com') }}</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Content -->
    @yield('content')

    <!-- Footer -->
    <div class="footer">
        <div class="contact-info">
            <div>
                Contact us at: {{ config('settings.phone', '+252 61 XXX XXXX') }} or via email at:
                {{ config('settings.email', 'info@caawiyecare.com') }}
            </div>
            <div class="page-number">
                {{ config('app.name') }}<br>
                Page 1 / 1
            </div>
        </div>
        <div style="margin-top: 10px; color: #999;">
            Generated on {{ now()->format('Y-m-d H:i:s') }}
        </div>
    </div>

    @stack('scripts')
</body>
</html>
