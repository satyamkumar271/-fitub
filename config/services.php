<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'razorpay' => [
        'key' => env('RAZORPAY_KEY'),
        'secret' => env('RAZORPAY_SECRET'),
        'webhook_secret' => env('RAZORPAY_WEBHOOK_SECRET'),
    ],

    'invoice' => [
        'company_name' => env('INVOICE_COMPANY_NAME', 'Fitub'),
        'gst_number' => env('INVOICE_GST_NUMBER', ''),
        'gst_rate' => (float) env('INVOICE_GST_RATE', 18),
        'address_line1' => env('INVOICE_ADDRESS_LINE1', ''),
        'address_line2' => env('INVOICE_ADDRESS_LINE2', ''),
        'city' => env('INVOICE_ADDRESS_CITY', ''),
        'state' => env('INVOICE_ADDRESS_STATE', ''),
        'pincode' => env('INVOICE_ADDRESS_PINCODE', ''),
    ],

];
