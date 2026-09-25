<?php

return [
    'provider' => env('GEOLOCATION_PROVIDER', 'disabled'),
    'nominatim_endpoint' => env('NOMINATIM_ENDPOINT', 'https://nominatim.openstreetmap.org/reverse'),
    'user_agent' => env('NOMINATIM_USER_AGENT', env('APP_NAME', 'Laravel').'/attendance-backend'),
];
