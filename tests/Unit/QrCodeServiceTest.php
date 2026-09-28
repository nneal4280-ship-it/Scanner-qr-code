<?php

namespace Tests\Unit;

use App\Models\Site;
use App\Models\User;
use App\Services\QrCodeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class QrCodeServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_and_validates_the_daily_qr_code(): void
    {
        $site = Site::factory()->create();
        $creator = User::factory()->create();
        $service = app(QrCodeService::class);

        $result = $service->createDaily($site, $creator);

        $this->assertTrue($result['created']);
        $this->assertNotEmpty($result['token']);
        $this->assertSame($site->id, $result['site_id']);
        $this->assertSame($result['token'], $service->plainToken($result['qr_token']));
        $this->assertSame($result['qr_token']->id, $service->validateForAttendance($result['token'], $site->id)->id);
    }

    public function test_it_reuses_the_active_qr_code_for_the_same_day(): void
    {
        $site = Site::factory()->create();
        $creator = User::factory()->create();
        $service = app(QrCodeService::class);

        $first = $service->createDaily($site, $creator);
        $second = $service->createDaily($site, $creator);

        $this->assertFalse($second['created']);
        $this->assertNull($second['token']);
        $this->assertSame($first['qr_token']->id, $second['qr_token']->id);
        $this->assertSame(1, $site->qrTokens()->count());
    }

    public function test_it_rejects_an_invalid_qr_code(): void
    {
        $site = Site::factory()->create();
        $service = app(QrCodeService::class);

        $this->expectException(ValidationException::class);
        $service->validateForAttendance('token-inconnu', $site->id);
    }
}
