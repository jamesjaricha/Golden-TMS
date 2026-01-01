<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ticket {{ $complaint->ticket_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.6;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 15px;
        }
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .info-row {
            display: table-row;
        }
        .info-label {
            display: table-cell;
            font-weight: bold;
            color: #6b7280;
            padding: 8px;
            width: 150px;
            background: #f3f4f6;
        }
        .info-value {
            display: table-cell;
            padding: 8px;
            border-bottom: 1px solid #e5e7eb;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            background: #374151;
            color: white;
            padding: 10px;
            margin-bottom: 10px;
            font-weight: bold;
        }
        .content-box {
            border: 1px solid #e5e7eb;
            padding: 15px;
            background: #f9fafb;
        }
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="margin: 0; color: #111827;">Ticket Details</h1>
        <h2 style="margin: 10px 0; color: #2563eb;">{{ $complaint->ticket_number }}</h2>
        <p style="margin: 0; color: #6b7280;">Generated on {{ $generatedAt->format('F d, Y \a\t H:i') }}</p>
    </div>

    <div class="section">
        <div class="section-title">Client Information</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Full Name</div>
                <div class="info-value">{{ $complaint->full_name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Policy Number</div>
                <div class="info-value">{{ $complaint->policy_number }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Phone Number</div>
                <div class="info-value">{{ $complaint->phone_number }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Location</div>
                <div class="info-value">{{ $complaint->location }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Branch Visited</div>
                <div class="info-value">{{ $complaint->visited_branch }}</div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Ticket Information</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Department</div>
                <div class="info-value">{{ $complaint->department->name ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Status</div>
                <div class="info-value">
                    <span class="badge" style="
                        @if($complaint->status === 'pending') background: #fef3c7; color: #92400e;
                        @elseif($complaint->status === 'assigned') background: #dbeafe; color: #1e40af;
                        @elseif($complaint->status === 'in_progress') background: #e0e7ff; color: #3730a3;
                        @elseif($complaint->status === 'resolved') background: #d1fae5; color: #065f46;
                        @elseif($complaint->status === 'closed') background: #e5e7eb; color: #1f2937;
                        @elseif($complaint->status === 'escalated') background: #fee2e2; color: #991b1b;
                        @endif">
                        {{ ucwords(str_replace('_', ' ', $complaint->status)) }}
                    </span>
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Priority</div>
                <div class="info-value">
                    <span class="badge" style="
                        @if($complaint->priority === 'low') background: #e5e7eb; color: #1f2937;
                        @elseif($complaint->priority === 'medium') background: #dbeafe; color: #1e40af;
                        @elseif($complaint->priority === 'high') background: #fed7aa; color: #9a3412;
                        @elseif($complaint->priority === 'urgent') background: #fee2e2; color: #991b1b;
                        @endif">
                        {{ ucfirst($complaint->priority) }}
                    </span>
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Captured By</div>
                <div class="info-value">{{ $complaint->capturedBy?->name ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Assigned To</div>
                <div class="info-value">{{ $complaint->assignedTo?->name ?? 'Unassigned' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Created Date</div>
                <div class="info-value">{{ $complaint->created_at->format('F d, Y H:i') }}</div>
            </div>
            @if($complaint->resolved_at)
            <div class="info-row">
                <div class="info-label">Resolved Date</div>
                <div class="info-value">{{ $complaint->resolved_at->format('F d, Y H:i') }}</div>
            </div>
            @endif
            @if($complaint->closed_at)
            <div class="info-row">
                <div class="info-label">Closed Date</div>
                <div class="info-value">{{ $complaint->closed_at->format('F d, Y H:i') }}</div>
            </div>
            @endif
        </div>
    </div>

    <div class="section">
        <div class="section-title">Complaint Description</div>
        <div class="content-box">
            {{ $complaint->complaint_text }}
        </div>
    </div>

    @if($complaint->resolution_notes)
    <div class="section">
        <div class="section-title">Resolution Notes</div>
        <div class="content-box">
            {{ $complaint->resolution_notes }}
        </div>
    </div>
    @endif
</body>
</html>
