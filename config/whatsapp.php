<?php

/*
|--------------------------------------------------------------------------
| WhatsApp (ChatHub by mbg card)
|--------------------------------------------------------------------------
| OTP codes and order notifications are sent as plain text through the
| ChatHub public API (POST /whatsapp/send_text). Put the real values in .env
| (never commit the API key):
|
|   WHATSAPP_ENABLED=true
|   WHATSAPP_API_KEY=...            (ChatHub dashboard > API Settings)
|   WHATSAPP_ADMIN_NUMBER=918074111406
*/

return [
    'enabled' => filter_var(env('WHATSAPP_ENABLED', false), FILTER_VALIDATE_BOOLEAN),

    'base_url' => env('WHATSAPP_API_BASE', 'https://consolewebwhatsapp.digitalmbg.com/api'),
    'api_key' => env('WHATSAPP_API_KEY'),

    // optional: phone-number ID of the WhatsApp number to send from (otherwise ChatHub picks the default one)
    'from' => env('WHATSAPP_FROM'),

    // used when a stored phone number has only 10 digits
    'country_code' => env('WHATSAPP_COUNTRY_CODE', '91'),
    // the ChatHub WhatsApp number customers must message first (24-hour window); shown for reference
    'phone_id' => env('WHATSAPP_PHONE_ID'),
    'business_number' => env('WHATSAPP_BUSINESS_NUMBER'),

    // new-order and cancellation alerts go here (country code included, no +)
    'admin_number' => env('WHATSAPP_ADMIN_NUMBER', '918074111406'),

    'timeout' => (int) env('WHATSAPP_TIMEOUT', 8),
];
