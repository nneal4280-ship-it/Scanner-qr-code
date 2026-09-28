<?php

namespace App\Services;

use App\Models\Site;

class GeolocationService
{
    public function distanceInMeters(float $latitude, float $longitude, Site $site): float
    {
        $referenceLatitude = (float) config('attendance.latitude', $site->latitude);
        $referenceLongitude = (float) config('attendance.longitude', $site->longitude);
        $earthRadius = 6371000;
        $latDelta = deg2rad($referenceLatitude - $latitude);
        $lonDelta = deg2rad($referenceLongitude - $longitude);
        $a = sin($latDelta / 2) ** 2
            + cos(deg2rad($latitude)) * cos(deg2rad($referenceLatitude)) * sin($lonDelta / 2) ** 2;

        return 2 * $earthRadius * asin(min(1, sqrt($a)));
    }

    public function isInside(float $latitude, float $longitude, Site $site): bool
    {
        return $this->distanceInMeters($latitude, $longitude, $site) <= $this->radius($site);
    }

    public function radius(Site $site): int
    {
        return (int) config('attendance.radius_meters', $site->radius_meters);
    }
}
