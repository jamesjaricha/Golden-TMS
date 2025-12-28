<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Monthly Report - {{ $period }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 15px;
        }
        .summary-grid {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        .summary-row {
            display: table-row;
        }
        .summary-cell {
            display: table-cell;
            border: 1px solid #e5e7eb;
            padding: 20px;
            text-align: center;
            width: 25%;
        }
        .summary-value {
            font-size: 32px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 5px;
        }
        .summary-label {
            color: #6b7280;
            font-size: 12px;
            text-transform: uppercase;
        }
        .chart-section {
            margin-bottom: 30px;
        }
        .chart-title {
            background: #374151;
            color: white;
            padding: 10px;
            margin-bottom: 10px;
            font-weight: bold;
        }
        .chart-row {
            display: flex;
            margin-bottom: 5px;
            align-items: center;
        }
        .chart-label {
            width: 150px;
            font-weight: bold;
        }
        .chart-bar {
            background: #2563eb;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="margin: 0;">Monthly Ticket Report</h1>
        <h2 style="margin: 10px 0; color: #2563eb;">{{ $period }}</h2>
        <p style="margin: 0; color: #6b7280;">Generated on {{ $generatedAt->format('F d, Y \a\t H:i') }}</p>
    </div>

    <div class="summary-grid">
        <div class="summary-row">
            <div class="summary-cell">
                <div class="summary-value">{{ $total }}</div>
                <div class="summary-label">Total Tickets</div>
            </div>
            <div class="summary-cell">
                <div class="summary-value">{{ $resolved }}</div>
                <div class="summary-label">Resolved</div>
            </div>
            <div class="summary-cell">
                <div class="summary-value">{{ $pending }}</div>
                <div class="summary-label">Pending</div>
            </div>
            <div class="summary-cell">
                <div class="summary-value">{{ $escalated }}</div>
                <div class="summary-label">Escalated</div>
            </div>
        </div>
    </div>

    <div class="chart-section">
        <div class="chart-title">Status Breakdown</div>
        @foreach($byStatus as $status => $count)
            <div class="chart-row">
                <div class="chart-label">{{ ucwords(str_replace('_', ' ', $status)) }}</div>
                <div class="chart-bar">{{ $count }}</div>
            </div>
        @endforeach
    </div>

    <div class="chart-section">
        <div class="chart-title">Priority Breakdown</div>
        @foreach($byPriority as $priority => $count)
            <div class="chart-row">
                <div class="chart-label">{{ ucfirst($priority) }}</div>
                <div class="chart-bar">{{ $count }}</div>
            </div>
        @endforeach
    </div>

    <div class="chart-section">
        <div class="chart-title">Department Breakdown</div>
        @foreach($byDepartment as $department => $count)
            <div class="chart-row">
                <div class="chart-label">{{ $department }}</div>
                <div class="chart-bar">{{ $count }}</div>
            </div>
        @endforeach
    </div>

    <div style="text-align: center; margin-top: 40px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
        <p style="color: #6b7280; font-size: 10px;">GKTMS - Golden Knot Ticket Management System</p>
    </div>
</body>
</html>
