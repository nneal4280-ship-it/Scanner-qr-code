<?php

namespace App\Services;

use App\Models\QrToken;
use App\Models\Site;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class QrCodeService
{
    public function issue(Site $site): array
    {
        $plainToken = Str::random(64);
        $token = QrToken::create([
            'site_id' => $site->id,
            'token_hash' => hash('sha256', $plainToken),
            'context' => 'attendance',
            'expires_at' => now()->addSeconds((int) config('attendance.qr_ttl_seconds', 60)),
        ]);

        return ['token' => $plainToken, 'expires_at' => $token->expires_at, 'site_id' => $site->id];
    }

    public function consume(string $plainToken, int $siteId): QrToken
    {
        return DB::transaction(function () use ($plainToken, $siteId): QrToken {
            if ($this->isStaticCentreQr($plainToken)) {
                return QrToken::firstOrCreate(
                    [
                        'site_id' => $siteId,
                        'token_hash' => hash('sha256', config('attendance.static_qr_value')),
                    ],
                    [
                        'context' => 'static_centre_qr',
                        // expires_at est un TIMESTAMP MySQL : rester avant 2038.
                        'expires_at' => now()->addYears(10),
                    ],
                );
            }

            $token = QrToken::query()->where('token_hash', hash('sha256', $plainToken))->lockForUpdate()->first();

            if (! $token || $token->site_id !== $siteId || ! $token->isUsable()) {
                throw ValidationException::withMessages(['qr_token' => 'Le QR code est invalide, expiré ou déjà utilisé.']);
            }

            $token->update(['consumed_at' => now()]);
            return $token;
        });
    }

    private function isStaticCentreQr(string $plainToken): bool
    {
        $expectedValue = (string) config('attendance.static_qr_value');

        return $expectedValue !== '' && hash_equals($expectedValue, trim($plainToken));
    }
}
