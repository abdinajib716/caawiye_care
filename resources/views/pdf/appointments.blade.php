@extends('pdf.layout')

@section('title', __('Appointments Report'))

@section('content')
<div class="document-title">{{ __('Appointments Report') }}</div>

<div class="info-section">
    <div>
        <span class="info-label">{{ __('Report Date:') }}</span> {{ $generatedAt }}
    </div>
    <div>
        <span class="info-label">{{ __('Total Appointments:') }}</span> {{ $appointments->count() }}
    </div>
</div>

<table>
    <thead>
        <tr>
            <th style="width: 5%;">#</th>
            <th style="width: 25%;">{{ __('Patient Name') }}</th>
            <th style="width: 20%;">{{ __('Hospital') }}</th>
            <th style="width: 20%;">{{ __('Date & Time') }}</th>
            <th style="width: 15%;">{{ __('Status') }}</th>
            <th style="width: 15%; text-align: right;">{{ __('Amount') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($appointments as $index => $appointment)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $appointment->patient_name }}</td>
            <td>{{ $appointment->hospital->name ?? 'N/A' }}</td>
            <td>{{ $appointment->appointment_time?->format('Y-m-d H:i') ?? 'N/A' }}</td>
            <td>
                <span class="badge badge-{{ $appointment->status }}">
                    {{ ucfirst($appointment->status) }}
                </span>
            </td>
            <td style="text-align: right;">
                ${{ number_format($appointment->total_amount ?? 0, 2) }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="summary-section">
    <div class="summary-row">
        <span>{{ __('Total Appointments:') }}</span>
        <span>{{ $appointments->count() }}</span>
    </div>
    <div class="summary-row">
        <span>{{ __('Confirmed:') }}</span>
        <span>{{ $appointments->where('status', 'confirmed')->count() }}</span>
    </div>
    <div class="summary-row">
        <span>{{ __('Completed:') }}</span>
        <span>{{ $appointments->where('status', 'completed')->count() }}</span>
    </div>
    <div class="summary-row total">
        <span>{{ __('Total Amount:') }}</span>
        <span>${{ number_format($appointments->sum('total_amount'), 2) }}</span>
    </div>
</div>

<div style="clear: both;"></div>
@endsection
