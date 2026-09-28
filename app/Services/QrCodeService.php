<?php

namespace App\Services;

use App\Models\QrToken;
use App\Models\Site;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\ValidationException;

class QrCodeService
{
    public function issue(Site $site, \App\Models\User $creator): array
    {
        return $this->createDaily($site, $creator);
    }

    public function createDaily(Site $site, \App\Models\User $creator): array
    {
        return DB::transaction(function () use ($site, $creator): array {
            $lockedSite = Site::query()->whereKey($site->id)->lockForUpdate()->firstOrFail();
            $today = today();
            $active = $lockedSite->qrTokens()->whereDate('valid_on', $today)->where('is_active', true)->first();
            if ($active && $active->isValidForToday()) {
                return ['token' => null, 'qr_token' => $active, 'expires_at' => $active->expires_at, 'site_id' => $lockedSite->id, 'created' => false];
            }
            if ($active) {
                $active->deactivate();
            }

            $plainToken = Str::random(64);
            $token = $lockedSite->qrTokens()->create([
                'created_by' => $creator->id,
                'token_hash' => hash('sha256', $plainToken),
                'token_ciphertext' => Crypt::encryptString($plainToken),
                'context' => 'attendance',
                'valid_on' => $today,
                'expires_at' => $today->copy()->endOfDay(),
                'is_active' => true,
            ]);

            return ['token' => $plainToken, 'qr_token' => $token, 'expires_at' => $token->expires_at, 'site_id' => $lockedSite->id, 'created' => true];
        });
    }

    public function validateForAttendance(string $plainToken, int $siteId): QrToken
    {
        $token = QrToken::query()->where('token_hash', hash('sha256', trim($plainToken)))->lockForUpdate()->first();
        if (! $token || $token->site_id !== $siteId || $token->context !== 'attendance' || ! $token->isValidForToday()) {
            throw ValidationException::withMessages(['qr_token' => 'Le QR code est invalide ou n’est pas le QR actif du jour.']);
        }
        return $token;
    }

    public function current(Site $site): ?QrToken
    {
        return $site->qrTokens()->whereDate('valid_on', today())->where('is_active', true)->first();
    }

    public function plainToken(QrToken $token): ?string
    {
        return $token->token_ciphertext ? Crypt::decryptString($token->token_ciphertext) : null;
    }

    public function regenerate(Site $site, \App\Models\User $creator): array
    {
        return DB::transaction(function () use ($site, $creator): array {
            $lockedSite = Site::query()->whereKey($site->id)->lockForUpdate()->firstOrFail();
            $lockedSite->qrTokens()->whereDate('valid_on', today())->where('is_active', true)->get()->each->deactivate();
            return $this->createDaily($lockedSite, $creator);
        });
    }

    public function deactivate(QrToken $token): void
    {
        DB::transaction(fn () => $token->deactivate());
    }

    public function consume(string $plainToken, int $siteId): QrToken
    {
        return $this->validateForAttendance($plainToken, $siteId);

        return DB::transaction(function () use ($plainToken, $siteId): QrToken {
            if (false && $this->isStaticCentreQr($plainToken)) {
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
