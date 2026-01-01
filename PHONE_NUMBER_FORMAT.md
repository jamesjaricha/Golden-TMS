# Phone Number Format for WhatsApp Integration

## Format Requirement

All phone numbers in the system are stored in **WhatsApp International Format**:
- **Format**: `263XXXXXXXXX` (12 digits total)
- **Country Code**: `263` (Zimbabwe)
- **No special characters**: No spaces, dashes, parentheses, or + sign
- **Example**: `263776905912`

## Auto-Formatting

The complaint creation form automatically formats phone numbers:

1. **User enters**: `077 690 5912` or `0776905912`
2. **System stores**: `263776905912`

### Conversion Rules

- If number starts with `0`, it's replaced with `263`
- If number doesn't start with `263`, the country code is prepended
- All non-numeric characters are removed
- Length is limited to exactly 12 digits

## Validation

Phone numbers are validated with:
- **Size**: Exactly 12 characters
- **Pattern**: Must match `263XXXXXXXXX` format
- **Custom Error Messages**: User-friendly validation messages

## Database Migration

A migration was created to update any existing phone numbers to this format:
- File: `2025_12_28_085803_update_phone_numbers_to_whatsapp_format.php`
- Converts all existing phone numbers to WhatsApp format

## WhatsApp Integration

This format ensures phone numbers are ready for:
- WhatsApp Business API calls
- Direct message sending without format conversion
- Template message notifications
- No preprocessing needed when sending WhatsApp messages

## Usage in Code

```php
// Phone number is already in WhatsApp format
$complaint->phone_number; // "263776905912"

// Use directly with WhatsApp service
WhatsAppService::sendMessage($complaint->phone_number, $message);
```

## User Interface

The form includes:
- Clear placeholder showing the expected format
- Real-time formatting as user types
- WhatsApp icon indicator
- Helpful hint text below the field
- Validation feedback

## Important Notes

1. **No + symbol**: WhatsApp API expects the number without the + prefix
2. **Country code required**: Always includes the 263 prefix
3. **Read-only on edit**: Phone numbers cannot be changed after ticket creation
4. **Consistent format**: All numbers follow the same format for reliability
