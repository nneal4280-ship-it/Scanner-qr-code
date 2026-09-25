<?php

namespace App\Contracts;

interface GeocodingProviderInterface
{
    /** @return array<string, mixed>|null */
    public function reverse(float $latitude, float $longitude): ?array;
}
