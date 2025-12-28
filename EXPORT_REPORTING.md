# Export & Reporting Features - GKTMS

## Overview
Comprehensive export and reporting system for the Golden Knot Ticket Management System.

## Features Implemented

### 1. **Bulk Export (Complaints Index Page)**
- **Export to Excel**: Download all filtered tickets as Excel spreadsheet
- **Export to PDF**: Download all filtered tickets as PDF document
- **Respects Filters**: Exports only tickets matching current search/filter criteria
- **Location**: Complaints index page → Export dropdown button

### 2. **Individual Ticket Export**
- **Print View**: Opens print-friendly view in new tab
- **Download PDF**: Downloads individual ticket as PDF
- **Includes**: All ticket details, comments, and resolution notes
- **Location**: Individual ticket page → Export dropdown button

### 3. **Monthly Reports**
- **Excel Format**: Multi-sheet report with summary and details
  - Summary sheet: Statistics by status, priority, and department
  - Details sheet: All tickets for the month
- **PDF Format**: Professional formatted report with charts
- **Customizable**: Select any month/year
- **Location**: Complaints index page → Export dropdown → Monthly Reports section

## How to Use

### Exporting Filtered Tickets
1. Go to `/complaints`
2. Apply any filters (status, priority, search)
3. Click "Export" button
4. Choose "Export to Excel" or "Export to PDF"
5. File downloads automatically with timestamp

### Exporting Single Ticket
1. Open any ticket detail page
2. Click "Export" dropdown
3. Choose "Print Ticket" (opens in new tab) or "Download PDF"

### Generating Monthly Reports
1. Go to `/complaints`
2. Click "Export" → Scroll to "Monthly Reports"
3. Select month from date picker
4. Click "Excel" or "PDF" button
5. Report downloads automatically

## API Endpoints

```php
// Bulk exports (with filters)
GET /complaints/export/excel?status=pending&priority=high
GET /complaints/export/pdf?search=policy123

// Individual ticket exports
GET /complaints/{id}/print          // Print view
GET /complaints/{id}/export-pdf     // Download PDF

// Monthly reports
GET /reports/monthly?month=2025-12&format=excel
GET /reports/monthly?month=2025-12&format=pdf
```

## File Naming Convention

- **Bulk Exports**: `tickets_YYYY-MM-DD_HHMMSS.xlsx/pdf`
- **Single Ticket**: `ticket_TICKET-NUMBER.pdf`
- **Monthly Reports**: `report_YYYY-MM.xlsx/pdf`

## Technical Details

### Dependencies
- `maatwebsite/excel`: Excel file generation
- `barryvdh/laravel-dompdf`: PDF generation

### Controllers
- `ComplaintExportController`: Handles all export requests

### Export Classes
- `ComplaintsExport`: Bulk Excel export
- `MonthlyReportExport`: Monthly Excel report (multi-sheet)
- `MonthlyReportSummarySheet`: Summary statistics
- `MonthlyReportDetailsSheet`: Detailed ticket list

### Views
- `complaints/exports/pdf-list.blade.php`: Bulk PDF template
- `complaints/exports/print.blade.php`: Print-friendly ticket view
- `complaints/exports/ticket-pdf.blade.php`: Single ticket PDF
- `complaints/exports/monthly-report.blade.php`: Monthly report PDF

## Customization

### Excel Styling
Edit `app/Exports/ComplaintsExport.php` → `styles()` method

### PDF Styling
Edit the respective blade template in `resources/views/complaints/exports/`

### Add More Data Fields
1. Update export class `map()` method
2. Update `headings()` method
3. Update blade template if PDF

## Future Enhancements
- Quarterly reports
- Yearly summary reports
- CSV export option
- Email reports directly
- Schedule automatic reports
- Custom report builder
