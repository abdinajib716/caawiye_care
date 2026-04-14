<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Profit & Loss Report') }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11pt; line-height: 1.4; color: #333; padding: 30px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #2563eb; padding-bottom: 20px; }
        .header h1 { color: #2563eb; font-size: 24pt; margin-bottom: 5px; }
        .header p { color: #666; font-size: 10pt; }
        .period { background: #f5f5f5; padding: 10px 15px; border-radius: 5px; margin-bottom: 20px; font-size: 10pt; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th { padding: 10px; text-align: left; font-size: 10pt; }
        td { border: 1px solid #ddd; padding: 8px; font-size: 10pt; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        h3 { margin: 25px 0 10px 0; font-size: 14pt; }
        h3.revenue { color: #10b981; }
        h3.expense { color: #ef4444; }
        .text-right { text-align: right; }
        .text-red { color: #ef4444; }
        .revenue-total { background-color: #d1fae5 !important; font-weight: bold; }
        .expense-total { background-color: #fee2e2 !important; font-weight: bold; }
        .profit-row { font-weight: bold; font-size: 12pt; }
        .profit-positive { background-color: #d4a017 !important; color: white; }
        .profit-negative { background-color: #ef4444 !important; color: white; }
        .profit-row td { border: none; padding: 12px; }
        .footer { margin-top: 40px; text-align: center; font-size: 9pt; color: #999; border-top: 1px solid #ddd; padding-top: 15px; }
        .margin-note { margin-top: 10px; text-align: right; font-size: 9pt; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ config('app.name', 'Caawiye Care') }}</h1>
        <p>{{ __('Profit & Loss Statement') }}</p>
    </div>

    <div class="period">
        <strong>{{ __('Period:') }}</strong> {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}
    </div>

    <h3 class="revenue">{{ __('REVENUE') }}</h3>
    <table>
        <tr>
            <td style="width: 60%;">{{ __('Gross Revenue') }}</td>
            <td class="text-right">${{ number_format($report['gross_revenue'], 2) }}</td>
        </tr>
        <tr>
            <td>{{ __('Less: Refunded Revenue') }}</td>
            <td class="text-right text-red">(${{ number_format($report['refunded_revenue'], 2) }})</td>
        </tr>
        <tr class="revenue-total">
            <td>{{ __('Net Revenue') }}</td>
            <td class="text-right">${{ number_format($report['net_revenue'], 2) }}</td>
        </tr>
    </table>

    <h3 class="expense">{{ __('EXPENSES') }}</h3>
    <table>
        @forelse($report['expenses_by_category'] as $category => $amount)
        <tr>
            <td style="width: 60%;">{{ $category }}</td>
            <td class="text-right">${{ number_format($amount, 2) }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="2" style="text-align: center; color: #999;">{{ __('No expenses recorded') }}</td>
        </tr>
        @endforelse
        <tr class="expense-total">
            <td>{{ __('Total Expenses') }}</td>
            <td class="text-right">${{ number_format($report['total_expenses'], 2) }}</td>
        </tr>
    </table>

    <table style="margin-top: 25px;">
        <tr class="profit-row {{ $report['net_profit'] >= 0 ? 'profit-positive' : 'profit-negative' }}">
            <td style="width: 60%;">{{ __('NET PROFIT / (LOSS)') }}</td>
            <td class="text-right">
                {{ $report['net_profit'] >= 0 ? '' : '(' }}${{ number_format(abs($report['net_profit']), 2) }}{{ $report['net_profit'] >= 0 ? '' : ')' }}
            </td>
        </tr>
    </table>

    @if($report['profit_margin'] != 0)
    <div class="margin-note">
        {{ __('Profit Margin:') }} {{ number_format($report['profit_margin'], 1) }}%
    </div>
    @endif

    <div class="footer">
        {{ __('Generated on') }} {{ now()->format('M d, Y H:i:s') }} | {{ config('app.name', 'Caawiye Care') }}
    </div>
</body>
</html>
