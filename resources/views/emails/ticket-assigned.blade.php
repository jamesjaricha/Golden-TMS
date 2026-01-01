<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ticket Assigned</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #007AFF 0%, #0051D5 100%);
            color: white;
            padding: 30px;
            border-radius: 12px 12px 0 0;
            text-align: center;
        }
        .content {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-top: none;
            padding: 30px;
            border-radius: 0 0 12px 12px;
        }
        .ticket-info {
            background: #f9fafb;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .ticket-info dl {
            margin: 0;
            display: grid;
            grid-template-columns: 140px 1fr;
            gap: 10px;
        }
        .ticket-info dt {
            font-weight: 600;
            color: #6b7280;
        }
        .ticket-info dd {
            margin: 0;
            color: #111827;
        }
        .button {
            display: inline-block;
            background: #007AFF;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 8px;
            margin: 20px 0;
            font-weight: 600;
        }
        .button:hover {
            background: #0051D5;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 14px;
        }
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge-urgent { background: #fee2e2; color: #991b1b; }
        .badge-high { background: #fed7aa; color: #9a3412; }
        .badge-medium { background: #dbeafe; color: #1e40af; }
        .badge-low { background: #f3f4f6; color: #374151; }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="margin: 0; font-size: 24px;">🎫 New Ticket Assigned</h1>
        <p style="margin: 10px 0 0 0; opacity: 0.9;">GKTMS - Ticket Management System</p>
    </div>

    <div class="content">
        <p>Hello <strong>{{ $assignedUser->name }}</strong>,</p>

        <p>A new ticket has been assigned to you:</p>

        <div class="ticket-info">
            <dl>
                <dt>Ticket Number:</dt>
                <dd><strong>{{ $complaint->ticket_number }}</strong></dd>

                <dt>Client Name:</dt>
                <dd>{{ $complaint->full_name }}</dd>

                <dt>Policy Number:</dt>
                <dd>{{ $complaint->policy_number }}</dd>

                <dt>Department:</dt>
                <dd>{{ $complaint->department->name ?? 'N/A' }}</dd>

                <dt>Priority:</dt>
                <dd>
                    <span class="badge badge-{{ $complaint->priority }}">
                        {{ ucfirst($complaint->priority) }}
                    </span>
                </dd>

                <dt>Status:</dt>
                <dd>{{ ucwords(str_replace('_', ' ', $complaint->status)) }}</dd>

                <dt>Created:</dt>
                <dd>{{ $complaint->created_at->format('M d, Y h:i A') }}</dd>
            </dl>
        </div>

        <p><strong>Complaint Details:</strong></p>
        <p style="background: #f9fafb; padding: 15px; border-left: 4px solid #007AFF; border-radius: 4px;">
            {{ Str::limit($complaint->complaint_text, 200) }}
        </p>

        <div style="text-align: center;">
            <a href="{{ route('complaints.show', $complaint) }}" class="button">
                View Ticket
            </a>
        </div>

        <p style="margin-top: 30px; color: #6b7280; font-size: 14px;">
            Please review and attend to this ticket at your earliest convenience.
        </p>
    </div>

    <div class="footer">
        <p>This is an automated notification from GKTMS.</p>
    </div>
</body>
</html>
