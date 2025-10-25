<?php
return [
    'client_id'        => env('EFI_CLIENT_ID'),
    'client_secret'    => env('EFI_CLIENT_SECRET'),
    'certificate'      => env('EFI_CERT_PATH'),        // relativo ao base_path()
    'certificate_pass' => env('EFI_CERT_PASS', null),
    'sandbox'          => filter_var(env('EFI_PIX_SANDBOX', true), FILTER_VALIDATE_BOOL),
    'pix_key'          => env('EFI_PIX_KEY'),
    'webhook_base'     => rtrim(env('EFI_WEBHOOK_BASE', ''), '/'),
];