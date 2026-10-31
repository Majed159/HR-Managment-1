<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payslip — {{ $payslip->employee->full_name }}</title>
    <style>
        :root {
            --bg: #f3f6fb;
            --paper: #ffffff;
            --ink: #172033;
            --muted: #64748b;
            --line: #e2e8f0;
            --primary: #0f172a;
            --primary-soft: #1d4ed8;
            --accent: #e2e8f0;
            --success: #0f766e;
            --shadow: rgba(15, 23, 42, 0.08);
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            padding: 40px 24px;
            background: linear-gradient(180deg, #edf3ff 0%, var(--bg) 100%);
            color: var(--ink);
            font-family: "Segoe UI", Arial, sans-serif;
        }

        .sheet {
            max-width: 820px;
            margin: 0 auto;
            background: var(--paper);
            border: 1px solid var(--line);
            border-radius: 18px;
            box-shadow: 0 12px 32px var(--shadow);
            overflow: hidden;
        }

        .topbar {
            background: linear-gradient(135deg, var(--primary) 0%, #243b5a 100%);
            color: #fff;
            padding: 28px 36px 22px;
        }

        .topbar-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand-mark {
            width: 42px;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.12);
            font-weight: 700;
            letter-spacing: 0.08em;
        }

        .brand-text {
            font-size: 22px;
            font-weight: 700;
            letter-spacing: 0.02em;
        }

        .doc-title {
            text-align: right;
        }

        .doc-title h1 {
            margin: 0;
            font-size: 14px;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: rgba(255,255,255,0.8);
            font-weight: 600;
        }

        .doc-title .period {
            margin-top: 8px;
            font-size: 14px;
            color: rgba(255, 255, 255, 0.9);
        }

        .content {
            padding: 32px 36px 28px;
        }

        .employee-card {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
            padding: 18px 0 22px;
            border-bottom: 1px solid var(--line);
            margin-bottom: 24px;
        }

        .info-block {
            background: #f8fafc;
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 14px 16px;
        }

        .label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: var(--muted);
            font-weight: 700;
        }

        .value {
            margin-top: 8px;
            font-size: 16px;
            font-weight: 700;
            color: var(--ink);
        }

        .muted {
            margin-top: 6px;
            font-size: 13px;
            color: var(--muted);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        th, td {
            padding: 14px 12px;
            border-bottom: 1px solid var(--line);
            font-size: 14px;
        }

        thead th {
            background: #f8fafc;
            color: var(--muted);
            font-size: 12px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            font-weight: 700;
            text-align: left;
        }

        .num {
            text-align: right;
            font-variant-numeric: tabular-nums;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        .total-row td {
            font-weight: 700;
            font-size: 16px;
            background: #f8fafc;
            border-top: 2px solid var(--primary);
        }

        .total-row td:first-child {
            color: var(--primary);
        }

        .footer-note {
            margin-top: 26px;
            padding-top: 18px;
            border-top: 1px solid var(--line);
            font-size: 13px;
            color: var(--muted);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .badge {
            display: inline-block;
            background: rgba(15, 118, 110, 0.08);
            color: var(--success);
            border: 1px solid rgba(15, 118, 110, 0.2);
            border-radius: 999px;
            padding: 7px 10px;
            font-weight: 600;
            font-size: 12px;
        }

        .actions {
            text-align: center;
            padding: 20px 0 0;
        }

        .print-btn {
            display: inline-block;
            background: var(--primary);
            border: none;
            color: #fff;
            padding: 11px 20px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 10px 18px rgba(15, 23, 42, 0.2);
        }

        @media print {
            body {
                background: #fff;
                padding: 0;
            }

            .actions {
                display: none;
            }

            .sheet {
                max-width: none;
                border: none;
                border-radius: 0;
                box-shadow: none;
            }
        }

        @media (max-width: 640px) {
            .employee-card {
                grid-template-columns: 1fr;
            }

            .topbar-inner,
            .footer-note {
                flex-direction: column;
                align-items: flex-start;
            }

            .doc-title {
                text-align: left;
            }
        }
    </style>
</head>
<body>
    <div class="actions">
        <button class="print-btn" onclick="window.print()">Print / Save as PDF</button>
    </div>

    <div class="sheet">
        <div class="topbar">
            <div class="topbar-inner">
                <div class="brand">
                    <div class="brand-mark">HR</div>
                    <div class="brand-text">{{ config('app.name', 'HR Management') }}</div>
                </div>

                <div class="doc-title">
                    <h1>Payslip</h1>
                    <div class="period">
                        {{ $payslip->period_start->format('d M Y') }} – {{ $payslip->period_end->format('d M Y') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="content">
            <div class="employee-card">
                <div class="info-block">
                    <div class="label">Employee</div>
                    <div class="value">{{ $payslip->employee->full_name }}</div>
                    <div class="muted">{{ $payslip->employee->email }}</div>
                </div>

                <div class="info-block">
                    <div class="label">Position</div>
                    <div class="value">{{ $payslip->employee->position?->title ?? '—' }}</div>
                    <div class="muted">{{ $payslip->employee->department?->name ?? '—' }}</div>
                </div>

                <div class="info-block">
                    <div class="label">Payment Details</div>
                    <div class="value">{{ $payslip->issued_at ? $payslip->issued_at->format('d M Y') : '—' }}</div>
                    <div class="muted">Paid for this pay period</div>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Description</th>
                        <th class="num">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Gross Pay</td>
                        <td class="num">${{ number_format((float) $payslip->gross_pay, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Deductions</td>
                        <td class="num">- ${{ number_format((float) $payslip->deductions, 2) }}</td>
                    </tr>
                    <tr class="total-row">
                        <td>Net Pay</td>
                        <td class="num">${{ number_format((float) $payslip->net_pay, 2) }}</td>
                    </tr>
                </tbody>
            </table>

            <div class="footer-note">
                <div>
                    This is a computer-generated payslip and does not require a signature.
                </div>
                <span class="badge">Confidential</span>
            </div>
        </div>
    </div>
</body>
</html>
