<?php

namespace Tests\Unit;

use App\Models\Site;
use App\Services\GeolocationService;
use Tests\TestCase;

class GeolocationServiceTest extends TestCase
{
    public function test_reference_coordinates_are_inside_the_geofence(): void
    {
        $site = new Site(['latitude' => 4.0503060, 'longitude' => 9.6940653, 'radius_meters' => 250]);
        $service = app(GeolocationService::class);

        $this->assertEqualsWithDelta(0, $service->distanceInMeters(4.0503060, 9.6940653, $site), 0.01);
        $this->assertTrue($service->isInside(4.0503060, 9.6940653, $site));
        $this->assertSame(250, $service->radius($site));
    }

    public function test_a_position_outside_the_configured_radius_is_rejected(): void
    {
        $site = new Site(['latitude' => 4.0503060, 'longitude' => 9.6940653, 'radius_meters' => 250]);
        $service = app(GeolocationService::class);

        $this->assertFalse($service->isInside(4.0600000, 9.6940653, $site));
        $this->assertGreaterThan(250, $service->distanceInMeters(4.0600000, 9.6940653, $site));
    }
}
