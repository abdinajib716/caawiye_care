<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Expense Report') }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11pt; line-height: 1.4; color: #333; padding: 30px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #dc2626; padding-bottom: 20px; }
        .header h1 { color: #dc2626; font-size: 24pt; margin-bottom: 5px; }
        .header p { color: #666; font-size: 10pt; }
        .period { background: #f5f5f5; padding: 10px 15px; border-radius: 5px; margin-bottom: 20px; font-size: 10pt; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th { background-color: #dc2626; color: white; padding: 10px; text-align: left; font-size: 10pt; }
        td { border: 1px solid #ddd; padding: 8px; font-size: 10pt; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .total-row { background-color: #dc2626 !important; color: white; font-weight: bold; }
        .total-row td { border-color: #dc2626; }
        h3 { color: #dc2626; margin: 25px 0 10px 0; font-size: 14pt; }
        .text-right { text-align: right; }
        .footer { margin-top: 40px; text-align: center; font-size: 9pt; color: #999; border-top: 1px solid #ddd; padding-top: 15px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ config('app.name', 'Caawiye Care') }}</h1>
        <p>{{ __('Expense Report') }}</p>
    </div>

    <div class="period">
        <strong>{{ __('Period:') }}</strong> {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}
    </div>

    <h3>{{ __('Summary') }}</h3>
    <table>
        <tr>
            <th style="width: 60%;">{{ __('Metric') }}</th>
            <th style="width: 40%; text-align: right;">{{ __('Amount') }}</th>
        </tr>
        <tr>
            <td>{{ __('Total Expenses') }}</td>
            <td class="text-right">${{ number_format($report['total_expenses'], 2) }}</td>
        </tr>
        <tr>
            <td>{{ __('Provider Expenses') }}</td>
            <td class="text-right">${{ number_format($report['provider_expenses'], 2) }}</td>
        </tr>
    </table>

    @if(count($report['by_category']) > 0)
    <h3>{{ __('Expenses by Category') }}</h3>
    <table>
        <tr>
            <th>{{ __('Category') }}</th>
            <th class="text-right">{{ __('Amount') }}</th>
            <th class="text-right">{{ __('% of Total') }}</th>
        </tr>
        @foreach($report['by_category'] as $category => $amount)
        <tr>
            <td>{{ $category }}</td>
            <td class="text-right">${{ number_format($amount, 2) }}</td>
            <td class="text-right">{{ $report['total_expenses'] > 0 ? number_format(($amount / $report['total_expenses']) * 100, 1) : 0 }}%</td>
        </tr>
        @endforeach
    </table>
    @endif

    @if(count($report['by_date']) > 0)
    <h3>{{ __('Expenses by Date') }}</h3>
    <table>
        <tr>
            <th>{{ __('Date') }}</th>
            <th class="text-right">{{ __('Amount') }}</th>
        </tr>
        @foreach($report['by_date'] as $date => $amount)
        <tr>
            <td>{{ \Carbon\Carbon::parse($date)->format('M d, Y') }}</td>
            <td class="text-right">${{ number_format($amount, 2) }}</td>
        </tr>
        @endforeach
    </table>
    @endif

    <div class="footer">
        {{ __('Generated on') }} {{ now()->format('M d, Y H:i:s') }} | {{ config('app.name', 'Caawiye Care') }}
    </div>
</body>
</html>
