<?php

namespace App\Services;

use App\Models\Site;

class GeolocationService
{
    public function distanceInMeters(float $latitude, float $longitude, Site $site): float
    {
        $earthRadius = 6371000;
        $latDelta = deg2rad($site->latitude - $latitude);
        $lonDelta = deg2rad($site->longitude - $longitude);
        $a = sin($latDelta / 2) ** 2
            + cos(deg2rad($latitude)) * cos(deg2rad($site->latitude)) * sin($lonDelta / 2) ** 2;

        return 2 * $earthRadius * asin(min(1, sqrt($a)));
    }

    public function isInside(float $latitude, float $longitude, Site $site): bool
    {
        return $this->distanceInMeters($latitude, $longitude, $site) <= $site->radius_meters;
    }
}
