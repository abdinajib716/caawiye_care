<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? __('Lab Test Bookings') }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #111827; font-size: 12px; }
        h1 { margin-bottom: 6px; }
        .meta { color: #6b7280; margin-bottom: 18px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #d1d5db; padding: 8px; text-align: left; }
        th { background: #f3f4f6; }
    </style>
</head>
<body>
    <h1>{{ $title ?? __('Lab Test Bookings') }}</h1>
    <p class="meta">{{ __('Generated at') }}: {{ $generatedAt }}</p>

    <table>
        <thead>
            <tr>
                <th>{{ __('Booking #') }}</th>
                <th>{{ __('Customer') }}</th>
                <th>{{ __('Patient') }}</th>
                <th>{{ __('Amount') }}</th>
                <th>{{ __('Payment') }}</th>
                <th>{{ __('Status') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bookings as $booking)
                <tr>
                    <td>{{ $booking->booking_number }}</td>
                    <td>{{ $booking->customer?->name ?? __('N/A') }}</td>
                    <td>{{ $booking->patient_name }}</td>
                    <td>${{ number_format((float) $booking->total, 2) }}</td>
                    <td>{{ ucfirst($booking->payment_status) }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $booking->status)) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
