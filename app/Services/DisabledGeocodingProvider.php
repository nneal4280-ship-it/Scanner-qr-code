<?php

namespace App\Services;

use App\Contracts\GeocodingProviderInterface;

class DisabledGeocodingProvider implements GeocodingProviderInterface
{
    public function reverse(float $latitude, float $longitude): ?array
    {
        return null;
    }
}
