<x-app-layout>
    <section class="pa-screen">
        <div class="pa-section-heading">
            <div><p class="pa-eyebrow">Espace RH</p><h1>Gestion des QR codes</h1><p class="pa-muted">{{ now()->translatedFormat('l d F Y') }} · {{ $site->name }}</p></div>
            <a class="pa-outline-button" href="{{ route('dashboard') }}">Retour</a>
        </div>

        @if(session('daily_qr_message'))<div class="pa-attendance-success" role="status">{{ session('daily_qr_message') }}</div>@endif

        <section class="pa-card pa-qr-management pa-qr-current">
            <div class="pa-qr-state"><span class="pa-qr-dot {{ $currentQr?->isValidForToday() ? 'is-active' : '' }}"></span><span>QR code du jour · {{ $currentQr ? 'Actif' : 'Non créé' }}</span></div>
            @if($currentQr && $currentToken)
                <p class="pa-muted">Créé le {{ $currentQr->created_at->format('d/m/Y à H:i') }} · Expire à {{ $currentQr->expires_at->format('H:i') }}</p>
                <canvas id="daily-qr-canvas" class="pa-qr-canvas" data-qr-token="{{ $currentToken }}" data-qr-date="{{ $currentQr->valid_on->format('d/m/Y') }}"></canvas>
                <form method="POST" action="{{ route('attendance.qr-tokens.export', $currentQr) }}" data-qr-export-form>@csrf<input type="hidden" name="qr_token" value="{{ $currentToken }}"><input type="hidden" name="qr_image" data-qr-image><button class="pa-outline-button" type="submit">Exporter le QR code</button></form>
                <div class="pa-qr-actions"><button class="pa-primary-button" type="button" data-qr-download>Télécharger PNG</button><button class="pa-outline-button" type="button" data-qr-print>Imprimer</button></div>
            @else
                <p class="pa-muted">Aucun QR actif pour aujourd’hui.</p>
            @endif
            <form method="POST" action="{{ route('attendance.qr-tokens.store') }}" class="pa-qr-action">@csrf<input type="hidden" name="site_id" value="{{ $site->id }}"><button class="pa-primary-button pa-full-button" type="submit">{{ $currentQr ? 'Vérifier le QR du jour' : 'Créer le QR du jour' }}</button></form>
            @if($currentQr)<form method="POST" action="{{ route('attendance.qr-tokens.regenerate') }}" class="pa-qr-action">@csrf<input type="hidden" name="site_id" value="{{ $site->id }}"><button class="pa-outline-button pa-full-button" type="submit">Régénérer</button></form>@endif
        </section>

        <section class="pa-card pa-qr-history"><h2>Historique des QR codes</h2>
            @forelse($tokens as $token)
                <div class="pa-qr-history-row"><div><strong>{{ $token->valid_on?->format('d/m/Y') ?? '—' }}</strong><span>Créé le {{ $token->created_at->format('d/m/Y à H:i') }} · {{ $token->creator?->name ?? 'Système' }}</span></div><span class="pa-badge {{ $token->isValidForToday() ? 'pa-badge-success' : ($token->is_active ? 'pa-badge-warning' : 'pa-badge-danger') }}">{{ $token->isValidForToday() ? 'Actif' : ($token->is_active ? 'Expiré' : 'Désactivé') }}</span></div>
            @empty<p class="pa-empty">Aucun QR enregistré.</p>@endforelse
            {{ $tokens->links() }}
        </section>
    </section>
</x-app-layout>
