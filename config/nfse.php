<?php

return [
    'layout_version' => env('NFSE_LAYOUT_VERSION', '1.01'),
    'app_version' => env('NFSE_VER_APLIC', 'SpeedNFE-1.0'),
    'schemas_path' => resource_path('schemas/nfse/v1.01'),

    'sefin' => [
        'timeout' => (int) env('NFSE_SEFIN_TIMEOUT', 30),
        'restrita_base_url' => env('NFSE_SEFIN_RESTRITA_URL', 'https://sefin.producaorestrita.nfse.gov.br/SefinNacional'),
        'producao_base_url' => env('NFSE_SEFIN_PRODUCAO_URL', 'https://sefin.nfse.gov.br/SefinNacional'),
    ],
];
