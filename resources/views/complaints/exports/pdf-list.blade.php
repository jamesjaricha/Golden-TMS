<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tickets Export - {{ $generatedAt->format('Y-m-d') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .filters {
            background: #f3f4f6;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #374151;
            color: white;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
        }
        .badge-pending { background: #fef3c7; color: #92400e; }
        .badge-assigned { background: #dbeafe; color: #1e40af; }
        .badge-in-progress { background: #e0e7ff; color: #3730a3; }
        .badge-resolved { background: #d1fae5; color: #065f46; }
        .badge-closed { background: #e5e7eb; color: #1f2937; }
        .badge-escalated { background: #fee2e2; color: #991b1b; }
        .badge-low { background: #e5e7eb; color: #1f2937; }
        .badge-medium { background: #dbeafe; color: #1e40af; }
        .badge-high { background: #fed7aa; color: #9a3412; }
        .badge-urgent { background: #fee2e2; color: #991b1b; }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Tickets & Complaints Export</h1>
        <p>Generated on {{ $generatedAt->format('F d, Y \a\t H:i') }}</p>
    </div>

    <div class="filters">
        <strong>Filters Applied:</strong> {{ $filters }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Ticket #</th>
                <th>Client</th>
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
                    <td>{{ $complaint->ticket_number }}</td>
                    <td>
                        <strong>{{ $complaint->full_name }}</strong><br>
                        <small>{{ $complaint->phone_number }}</small>
                    </td>
                    <td>{{ $complaint->department }}</td>
                    <td>
                        <span class="badge badge-{{ $complaint->status }}">
                            {{ ucwords(str_replace('_', ' ', $complaint->status)) }}
                        </span>
                    </td>
                    <td>
                        <span class="badge badge-{{ $complaint->priority }}">
                            {{ ucfirst($complaint->priority) }}
                        </span>
                    </td>
                    <td>{{ $complaint->assignedTo?->name ?? 'Unassigned' }}</td>
                    <td>{{ $complaint->created_at->format('M d, Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center;">No tickets found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Total Tickets: {{ $complaints->count() }} | GKTMS - Golden Knot Ticket Management System</p>
    </div>
</body>
</html>
