<?php

return [
    // Get your key from https://console.developers.google.com
    'api_key' => env('NOVA_GOOGLE_POLYGON_API_KEY', ''),
    'center' => [
        'lat' => env('NOVA_GOOGLE_POLYGON_CENTER_LAT', 48.858361),
        'lng' => env('NOVA_GOOGLE_POLYGON_CENTER_LNG', 2.336164),
    ]
];
