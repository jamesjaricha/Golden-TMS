# WhatsApp Ticket Creation Wizard

## Overview

The WhatsApp Wizard provides a guided, step-by-step conversation flow for agents to create tickets. This reduces errors and makes ticket creation easier, especially on mobile devices.

## How to Start the Wizard

Agents can start the wizard by sending any of these messages:
- `WIZARD`
- `NEW`
- `START`
- `CREATE`
- `HI`
- `HELLO`
- `HELP`

## Wizard Flow

The wizard guides agents through the following steps:

### Step 1: Client Name
```
👤 Step 1 of 8: Client Name
Please enter the client's full name:
```

### Step 2: Phone Number
```
📱 Step 2 of 8: Phone Number
Please enter the client's phone number:
(e.g., 0771234567)
```

### Step 3: Branch Selection
```
🏢 Step 3 of 8: Branch
Please select a branch by number:

1️⃣ Head Office
2️⃣ Bulawayo Branch
3️⃣ Gweru Branch
...
```

### Step 4: Department Selection
```
📂 Step 4 of 8: Department
Please select a department by number:

1️⃣ IT Support
2️⃣ Customer Service
3️⃣ Finance
...
```

### Step 5: Subject
```
📋 Step 5 of 8: Subject
Please enter a brief subject/title for this ticket:
```

### Step 6: Description
```
📝 Step 6 of 8: Description
Please describe the issue in detail:
```

### Step 7: Priority
```
🔴 Step 7 of 8: Priority
Please select a priority level:

1️⃣ Low - Can wait
2️⃣ Medium - Standard
3️⃣ High - Needs attention soon
4️⃣ Urgent - Critical issue
```

### Step 8: Confirmation
```
✅ Step 8 of 8: Confirmation
Please review the ticket details:

👤 Client: John Doe
📱 Phone: 0771234567
🏢 Branch: Head Office
📂 Dept: IT Support
📋 Subject: Login issue
📝 Description: Customer cannot login...
🔴 Priority: High

Reply YES to create the ticket, or NO to cancel.
```

## Commands During Wizard

- **CANCEL** - Abort the wizard and start over
- **MENU** or **RESTART** - Go back to the beginning

## Legacy Format (Still Supported)

For quick entries, agents can still use the direct format:

### Quick Format (Pipe-separated)
```
TICKET Client Name | Phone | Subject | Description | Priority
```

### Detailed Format (Line-by-line)
```
TICKET
Client: John Doe
Phone: 0771234567
Subject: Issue title
Description: Full details here
Priority: high
```

## Features

1. **Step Validation** - Each step validates input before proceeding
2. **Numbered Menus** - Branches, departments, and priorities can be selected by number
3. **Session Timeout** - Conversations expire after 30 minutes of inactivity
4. **Confirmation Step** - Review all details before ticket creation
5. **Cancel Anytime** - Type CANCEL to abort and start fresh

## Technical Details

- Conversations are stored in `whatsapp_conversations` table
- Each conversation tracks: phone number, agent, current step, collected data
- Conversations expire after 30 minutes
- Service: `App\Services\WhatsAppWizardService`
- Model: `App\Models\WhatsAppConversation`

## Error Handling

If something goes wrong during the wizard:
1. An error message is sent to the agent
2. The agent can type CANCEL to reset
3. The wizard can be restarted with WIZARD or NEW
