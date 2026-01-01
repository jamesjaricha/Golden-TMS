<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket {{ $complaint->ticket_number }}</title>
    <style>
        @media print {
            .no-print { display: none; }
        }
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 15px;
        }
        .ticket-info {
            background: #f3f4f6;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        .info-item {
            margin-bottom: 10px;
        }
        .info-label {
            font-weight: bold;
            color: #6b7280;
            font-size: 12px;
            text-transform: uppercase;
        }
        .info-value {
            color: #111827;
            font-size: 14px;
            margin-top: 4px;
        }
        .complaint-text {
            background: white;
            border: 1px solid #e5e7eb;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        .print-btn {
            background: #2563eb;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
        }
        .print-btn:hover {
            background: #1d4ed8;
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: right; margin-bottom: 20px;">
        <button onclick="window.print()" class="print-btn">🖨️ Print</button>
        <button onclick="window.close()" class="print-btn" style="background: #6b7280;">✕ Close</button>
    </div>

    <div class="header">
        <h1 style="margin: 0; color: #111827;">Ticket Details</h1>
        <h2 style="margin: 10px 0; color: #2563eb;">{{ $complaint->ticket_number }}</h2>
        <p style="margin: 0; color: #6b7280;">Generated on {{ now()->format('F d, Y \a\t H:i') }}</p>
    </div>

    <div class="ticket-info">
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Client Name</div>
                <div class="info-value">{{ $complaint->full_name }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Policy Number</div>
                <div class="info-value">{{ $complaint->policy_number }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Phone Number</div>
                <div class="info-value">{{ $complaint->phone_number }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Location</div>
                <div class="info-value">{{ $complaint->location }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Branch Visited</div>
                <div class="info-value">{{ $complaint->visited_branch }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Department</div>
                <div class="info-value">{{ $complaint->department->name ?? 'N/A' }}</div>
            </div>
            <div class="info-item">
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
            <div class="info-item">
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
            <div class="info-item">
                <div class="info-label">Captured By</div>
                <div class="info-value">{{ $complaint->capturedBy?->name ?? 'N/A' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Assigned To</div>
                <div class="info-value">{{ $complaint->assignedTo?->name ?? 'Unassigned' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Created Date</div>
                <div class="info-value">{{ $complaint->created_at->format('F d, Y H:i') }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Last Updated</div>
                <div class="info-value">{{ $complaint->updated_at->format('F d, Y H:i') }}</div>
            </div>
        </div>
    </div>

    <div>
        <h3 style="margin-bottom: 10px; color: #111827;">Complaint Description</h3>
        <div class="complaint-text">
            {{ $complaint->complaint_text }}
        </div>
    </div>

    @if($complaint->resolution_notes)
        <div>
            <h3 style="margin-bottom: 10px; color: #111827;">Resolution Notes</h3>
            <div class="complaint-text">
                {{ $complaint->resolution_notes }}
            </div>
        </div>
    @endif

    @if($complaint->comments && $complaint->comments->count() > 0)
        <div>
            <h3 style="margin-bottom: 10px; color: #111827;">Comments ({{ $complaint->comments->count() }})</h3>
            @foreach($complaint->comments as $comment)
                <div class="complaint-text" style="margin-bottom: 10px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                        <strong>{{ $comment->user->name }}</strong>
                        <span style="color: #6b7280; font-size: 12px;">{{ $comment->created_at->format('M d, Y H:i') }}</span>
                    </div>
                    <div>{{ $comment->comment }}</div>
                </div>
            @endforeach
        </div>
    @endif
</body>
</html>
