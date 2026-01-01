<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ ucwords(str_replace('_', ' ', $filters['report_type'] ?? 'Custom')) }} Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #0071e3;
        }
        .header h1 {
            margin: 0;
            color: #0071e3;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .filters {
            background: #f5f5f7;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
        }
        .filters h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
        }
        .filter-item {
            display: inline-block;
            background: #fff;
            padding: 5px 10px;
            margin: 5px 5px 5px 0;
            border-radius: 4px;
            font-size: 11px;
        }
        .statistics {
            margin-bottom: 30px;
        }
        .stat-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .stat-row {
            display: table-row;
        }
        .stat-cell {
            display: table-cell;
            width: 25%;
            padding: 15px;
            background: #f5f5f7;
            border: 2px solid #fff;
            text-align: center;
        }
        .stat-label {
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
        }
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #0071e3;
            margin-top: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background: #0071e3;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 11px;
            font-weight: bold;
        }
        td {
            padding: 8px 10px;
            border-bottom: 1px solid #e5e5e7;
            font-size: 10px;
        }
        tr:nth-child(even) {
            background: #f9f9f9;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: bold;
        }
        .badge-success { background: #d4edda; color: #155724; }
        .badge-warning { background: #fff3cd; color: #856404; }
        .badge-danger { background: #f8d7da; color: #721c24; }
        .badge-info { background: #d1ecf1; color: #0c5460; }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e5e7;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ ucwords(str_replace('_', ' ', $filters['report_type'] ?? 'Custom')) }} Report</h1>
        <p>Generated on {{ now()->format('F d, Y \a\t h:i A') }}</p>
        <p>Golden Ticket Management System</p>
    </div>

    @if(!empty(array_filter($filters)))
        <div class="filters">
            <h3>Applied Filters:</h3>
            @if(isset($filters['date_from']) && $filters['date_from'])
                <span class="filter-item">From: {{ \Carbon\Carbon::parse($filters['date_from'])->format('M d, Y') }}</span>
            @endif
            @if(isset($filters['date_to']) && $filters['date_to'])
                <span class="filter-item">To: {{ \Carbon\Carbon::parse($filters['date_to'])->format('M d, Y') }}</span>
            @endif
            @if(isset($filters['branch_id']) && $filters['branch_id'])
                @php
                    $branch = \App\Models\Branch::find($filters['branch_id']);
                @endphp
                <span class="filter-item">Branch: {{ $branch?->name ?? 'N/A' }}</span>
            @endif
            @if(isset($filters['assigned_to']) && $filters['assigned_to'])
                @php
                    $agent = \App\Models\User::find($filters['assigned_to']);
                @endphp
                <span class="filter-item">Agent: {{ $agent?->name ?? 'N/A' }}</span>
            @endif
            @if(isset($filters['status']) && $filters['status'])
                <span class="filter-item">Status: {{ ucwords(str_replace('_', ' ', $filters['status'])) }}</span>
            @endif
            @if(isset($filters['priority']) && $filters['priority'])
                <span class="filter-item">Priority: {{ ucfirst($filters['priority']) }}</span>
            @endif
            @if(isset($filters['department']) && $filters['department'])
                <span class="filter-item">Department: {{ ucwords(str_replace('_', ' ', $filters['department'])) }}</span>
            @endif
            @if(isset($filters['employer_id']) && $filters['employer_id'])
                @php
                    $employer = \App\Models\Employer::find($filters['employer_id']);
                @endphp
                <span class="filter-item">Employer: {{ $employer?->name ?? 'N/A' }}</span>
            @endif
            @if(isset($filters['payment_method_id']) && $filters['payment_method_id'])
                @php
                    $paymentMethod = \App\Models\PaymentMethod::find($filters['payment_method_id']);
                @endphp
                <span class="filter-item">Payment Method: {{ $paymentMethod?->name ?? 'N/A' }}</span>
            @endif
        </div>
    @endif

    <div class="statistics">
        <h3>Summary Statistics</h3>
        <div class="stat-grid">
            <div class="stat-row">
                <div class="stat-cell">
                    <div class="stat-label">Total Tickets</div>
                    <div class="stat-value">{{ $statistics['total'] }}</div>
                </div>
                <div class="stat-cell">
                    <div class="stat-label">Resolved</div>
                    <div class="stat-value">{{ $statistics['by_status']['resolved'] ?? 0 }}</div>
                </div>
                <div class="stat-cell">
                    <div class="stat-label">In Progress</div>
                    <div class="stat-value">{{ $statistics['by_status']['in_progress'] ?? 0 }}</div>
                </div>
                <div class="stat-cell">
                    <div class="stat-label">Avg. Resolution</div>
                    <div class="stat-value">{{ $statistics['avg_resolution_hours'] }}h</div>
                </div>
            </div>
        </div>
    </div>

    <h3>Ticket Details ({{ $complaints->count() }} results)</h3>
    <table>
        <thead>
            <tr>
                <th>Ticket #</th>
                <th>Customer</th>
                <th>Department</th>
                <th>Status</th>
                <th>Priority</th>
                <th>Assigned To</th>
                <th>Created</th>
            </tr>
        </thead>
        <tbody>
            @forelse($complaints as $complaint)
                <tr>
                    <td><strong>{{ $complaint->ticket_number }}</strong></td>
                    <td>{{ $complaint->full_name }}</td>
                    <td>{{ $complaint->department->name ?? 'N/A' }}</td>
                    <td>
                        @if($complaint->status === 'resolved')
                            <span class="badge badge-success">{{ ucwords(str_replace('_', ' ', $complaint->status)) }}</span>
                        @elseif($complaint->status === 'in_progress')
                            <span class="badge badge-warning">{{ ucwords(str_replace('_', ' ', $complaint->status)) }}</span>
                        @elseif($complaint->status === 'escalated')
                            <span class="badge badge-danger">{{ ucwords(str_replace('_', ' ', $complaint->status)) }}</span>
                        @else
                            <span class="badge badge-info">{{ ucwords(str_replace('_', ' ', $complaint->status)) }}</span>
                        @endif
                    </td>
                    <td>
                        @if($complaint->priority === 'urgent')
                            <span class="badge badge-danger">{{ ucfirst($complaint->priority) }}</span>
                        @elseif($complaint->priority === 'high')
                            <span class="badge badge-warning">{{ ucfirst($complaint->priority) }}</span>
                        @else
                            <span class="badge badge-info">{{ ucfirst($complaint->priority) }}</span>
                        @endif
                    </td>
                    <td>{{ $complaint->assignedTo?->name ?? 'Unassigned' }}</td>
                    <td>{{ $complaint->created_at->format('M d, Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 20px;">No tickets found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>This report was generated automatically by the Golden Ticket Management System.</p>
        <p>&copy; {{ date('Y') }} GKTMS. All rights reserved.</p>
    </div>
</body>
</html>
