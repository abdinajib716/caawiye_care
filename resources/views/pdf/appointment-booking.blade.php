<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ __('Appointment Booking Confirmation') }}</title>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11pt;
            line-height: 1.6;
            color: #333;
            padding: 20px;
            background: #ffffff;
        }

        @page {
            size: A4 portrait;
            margin: 20mm;
        }

        /* Header Section */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #2d572c;
        }

        .logo-section {
            flex: 1;
        }

        .logo {
            max-width: 180px;
            height: auto;
            margin-bottom: 10px;
        }

        .company-info {
            flex: 1;
            text-align: right;
            font-size: 10pt;
            line-height: 1.5;
            color: #555;
        }

        .company-name {
            font-weight: bold;
            font-size: 14pt;
            color: #2d572c;
            margin-bottom: 5px;
        }

        /* Document Title */
        .document-title {
            text-align: center;
            margin: 25px 0;
        }

        .document-title h1 {
            color: #2d572c;
            font-size: 24pt;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .document-subtitle {
            color: #d4a017;
            font-size: 12pt;
            font-weight: 600;
        }

        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 10pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 10px 0;
        }

        .status-confirmed {
            background-color: #10b981;
            color: white;
        }

        .status-scheduled {
            background-color: #f59e0b;
            color: white;
        }

        .status-pending {
            background-color: #f59e0b;
            color: white;
        }

        .status-completed {
            background-color: #3b82f6;
            color: white;
        }

        .status-cancelled {
            background-color: #ef4444;
            color: white;
        }

        /* Info Cards */
        .info-card {
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .info-card h3 {
            color: #2d572c;
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #d4a017;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: #64748b;
            flex: 0 0 40%;
        }

        .info-value {
            color: #1e293b;
            font-weight: 500;
            flex: 1;
            text-align: right;
        }

        /* Highlighted Row */
        .info-row-highlight {
            background: #fef3c7;
            padding: 12px;
            margin: 10px -12px;
            border-radius: 6px;
            border-left: 4px solid #d4a017;
        }

        /* Cost Breakdown */
        .cost-card {
            background: linear-gradient(135deg, #2d572c 0%, #3a6e39 100%);
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            color: white;
        }

        .cost-card h3 {
            color: #ffffff;
            font-size: 14pt;
            margin-bottom: 15px;
            border-bottom: 2px solid rgba(255, 255, 255, 0.3);
            padding-bottom: 10px;
        }

        .cost-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 11pt;
        }

        .cost-total {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px solid rgba(255, 255, 255, 0.3);
            font-size: 16pt;
            font-weight: bold;
        }

        .cost-total .cost-value {
            color: #d4a017;
            font-size: 18pt;
        }

        /* Important Notes */
        .notes-section {
            background: #fef3c7;
            border-left: 4px solid #d4a017;
            padding: 15px;
            margin: 20px 0;
            border-radius: 6px;
        }

        .notes-section h4 {
            color: #92400e;
            font-size: 12pt;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .notes-section ul {
            margin-left: 20px;
            color: #78350f;
        }

        .notes-section li {
            margin-bottom: 5px;
        }

        /* Footer */
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e2e8f0;
            text-align: center;
        }

        .footer-contact {
            display: flex;
            justify-content: space-around;
            margin-bottom: 15px;
            font-size: 10pt;
            color: #64748b;
        }

        .footer-item {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .footer-item strong {
            color: #2d572c;
            margin-bottom: 3px;
        }

        .footer-note {
            font-size: 9pt;
            color: #94a3b8;
            margin-top: 15px;
        }

        /* QR Code Section */
        .qr-section {
            text-align: center;
            margin: 20px 0;
            padding: 15px;
            background: #f8fafc;
            border-radius: 10px;
        }

        .qr-section p {
            color: #64748b;
            font-size: 9pt;
            margin-top: 10px;
        }

        /* Signature Section */
        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
            padding: 20px 0;
        }

        .signature-box {
            text-align: center;
            width: 45%;
        }

        .signature-line {
            border-top: 2px solid #2d572c;
            margin-top: 50px;
            padding-top: 10px;
            font-size: 10pt;
            color: #64748b;
        }

        /* Print Styles */
        @media print {
            body {
                padding: 0;
            }
            
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="logo-section">
            @if(file_exists(public_path('images/logo.svg')))
                <img src="{{ public_path('images/logo.svg') }}" alt="Logo" class="logo">
            @else
                <div style="font-weight: bold; font-size: 18pt; color: #2d572c;">
                    {{ config('app.name', 'Caawiye Care') }}
                </div>
            @endif
        </div>
        <div class="company-info">
            <div class="company-name">{{ config('app.name', 'Caawiye Care') }}</div>
            <div>{{ config('settings.address', 'Mogadishu, Somalia') }}</div>
            <div>📞 {{ config('settings.phone', '+252 61 XXX XXXX') }}</div>
            <div>✉️ {{ config('settings.email', 'info@caawiyecare.com') }}</div>
            <div>🌐 {{ config('app.url', 'www.caawiyecare.com') }}</div>
        </div>
    </div>

    <!-- Document Title -->
    <div class="document-title">
        <h1>{{ __('Appointment Confirmation') }}</h1>
        <div class="document-subtitle">{{ __('Booking Reference') }}: #{{ $appointment->id }}</div>
        <div style="margin-top: 10px;">
            <span class="status-badge status-{{ $appointment->status }}">
                {{ ucfirst($appointment->status) }}
            </span>
        </div>
    </div>

    <!-- Patient Information -->
    <div class="info-card">
        <h3>{{ __('Patient Information') }}</h3>
        <div class="info-row">
            <div class="info-label">{{ __('Patient Name') }}</div>
            <div class="info-value">{{ $appointment->patient_name }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">{{ __('Appointment Type') }}</div>
            <div class="info-value">{{ ucfirst(str_replace('_', ' ', $appointment->appointment_type)) }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">{{ __('Customer Name') }}</div>
            <div class="info-value">{{ $appointment->customer->name ?? 'N/A' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">{{ __('Phone Number') }}</div>
            <div class="info-value">{{ $appointment->customer->phone ?? 'N/A' }}</div>
        </div>
    </div>

    <!-- Appointment Details -->
    <div class="info-card">
        <h3>{{ __('Appointment Details') }}</h3>
        <div class="info-row info-row-highlight">
            <div class="info-label">{{ __('Date & Time') }}</div>
            <div class="info-value" style="font-size: 13pt; font-weight: bold; color: #d4a017;">
                {{ $appointment->appointment_time?->format('l, F d, Y \a\t h:i A') ?? 'N/A' }}
            </div>
        </div>
        <div class="info-row">
            <div class="info-label">{{ __('Hospital') }}</div>
            <div class="info-value">{{ $appointment->hospital->name ?? 'N/A' }}</div>
        </div>
        @if($appointment->hospital)
        <div class="info-row">
            <div class="info-label">{{ __('Hospital Address') }}</div>
            <div class="info-value">{{ $appointment->hospital->address ?? 'N/A' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">{{ __('Hospital Phone') }}</div>
            <div class="info-value">{{ $appointment->hospital->phone ?? 'N/A' }}</div>
        </div>
        @endif
        @if($appointment->orderItem)
        <div class="info-row">
            <div class="info-label">{{ __('Doctor/Service') }}</div>
            <div class="info-value">{{ $appointment->orderItem->service_name ?? 'N/A' }}</div>
        </div>
        @endif
        @if($appointment->notes)
        <div class="info-row">
            <div class="info-label">{{ __('Notes') }}</div>
            <div class="info-value">{{ $appointment->notes }}</div>
        </div>
        @endif
    </div>

    <!-- Order Information -->
    @if($appointment->order)
    <div class="cost-card">
        <h3>{{ __('Payment Information') }}</h3>
        <div class="cost-row">
            <div>{{ __('Order Number') }}</div>
            <div>#{{ $appointment->order->id }}</div>
        </div>
        @if($appointment->orderItem)
        <div class="cost-row">
            <div>{{ __('Service Fee') }}</div>
            <div>${{ number_format((float) ($appointment->orderItem->unit_price ?? 0), 2) }}</div>
        </div>
        @php
            $additionalCharges = (float) (($appointment->orderItem->total_price ?? 0) - ($appointment->orderItem->unit_price ?? 0));
        @endphp
        @if($additionalCharges > 0)
        <div class="cost-row">
            <div>{{ __('Service Charge') }}</div>
            <div>${{ number_format($additionalCharges, 2) }}</div>
        </div>
        @endif
        @endif
        <div class="cost-row cost-total">
            <div>{{ __('Total Amount') }}</div>
            <div class="cost-value">${{ number_format((float) ($appointment->order->total ?? 0), 2) }}</div>
        </div>
        <div class="cost-row">
            <div>{{ __('Payment Status') }}</div>
            <div>{{ ucfirst($appointment->order->payment_status ?? 'Pending') }}</div>
        </div>
        @if($appointment->order->created_at)
        <div class="cost-row">
            <div>{{ __('Payment Date') }}</div>
            <div>{{ $appointment->order->created_at->format('M d, Y h:i A') }}</div>
        </div>
        @endif
    </div>
    @endif

    <!-- Important Notes -->
    <div class="notes-section">
        <h4>⚠️ {{ __('Important Instructions') }}</h4>
        <ul>
            <li>{{ __('Please arrive 15 minutes before your scheduled appointment time.') }}</li>
            <li>{{ __('Bring a valid ID and this confirmation document.') }}</li>
            <li>{{ __('If you need to reschedule or cancel, please contact us at least 24 hours in advance.') }}</li>
            <li>{{ __('In case of emergency, please call the hospital directly.') }}</li>
            @if($appointment->order && $appointment->order->payment_status === 'pending')
            <li style="color: #dc2626; font-weight: bold;">{{ __('Please complete payment before your appointment.') }}</li>
            @endif
        </ul>
    </div>

    <!-- Appointment Status Timeline -->
    <div class="info-card">
        <h3>{{ __('Appointment Timeline') }}</h3>
        <div class="info-row">
            <div class="info-label">{{ __('Booked On') }}</div>
            <div class="info-value">{{ $appointment->created_at->format('M d, Y h:i A') }}</div>
        </div>
        @if($appointment->confirmed_at)
        <div class="info-row">
            <div class="info-label">{{ __('Confirmed At') }}</div>
            <div class="info-value">{{ $appointment->confirmed_at->format('M d, Y h:i A') }}</div>
        </div>
        @endif
        @if($appointment->completed_at)
        <div class="info-row">
            <div class="info-label">{{ __('Completed At') }}</div>
            <div class="info-value">{{ $appointment->completed_at->format('M d, Y h:i A') }}</div>
        </div>
        @endif
        @if($appointment->cancelled_at)
        <div class="info-row">
            <div class="info-label">{{ __('Cancelled At') }}</div>
            <div class="info-value">{{ $appointment->cancelled_at->format('M d, Y h:i A') }}</div>
        </div>
        @if($appointment->cancellation_reason)
        <div class="info-row">
            <div class="info-label">{{ __('Cancellation Reason') }}</div>
            <div class="info-value">{{ $appointment->cancellation_reason }}</div>
        </div>
        @endif
        @endif
    </div>

    <!-- Signature Section -->
    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line">
                {{ __('Patient/Customer Signature') }}
            </div>
        </div>
        <div class="signature-box">
            <div class="signature-line">
                {{ __('Authorized Signature') }}
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div class="footer-contact">
            <div class="footer-item">
                <strong>{{ __('Contact') }}</strong>
                <span>{{ config('settings.phone', '+252 61 XXX XXXX') }}</span>
            </div>
            <div class="footer-item">
                <strong>{{ __('Email') }}</strong>
                <span>{{ config('settings.email', 'info@caawiyecare.com') }}</span>
            </div>
            <div class="footer-item">
                <strong>{{ __('Website') }}</strong>
                <span>{{ str_replace(['http://', 'https://'], '', config('app.url', 'www.caawiyecare.com')) }}</span>
            </div>
        </div>
        <div class="footer-note">
            {{ __('This is a computer-generated document. Generated on') }} {{ now()->format('Y-m-d H:i:s') }}<br>
            {{ __('Thank you for choosing') }} {{ config('app.name', 'Caawiye Care') }} {{ __('for your healthcare needs.') }}
        </div>
    </div>
</body>
</html>
