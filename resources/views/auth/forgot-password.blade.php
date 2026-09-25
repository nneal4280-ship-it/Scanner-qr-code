<x-guest-layout>
    <div class="pa-auth-shell">
        <div class="pa-auth-brand"><span class="pa-brand-mark">◷</span><h1>PointageApp</h1><p>Système de gestion des présences</p></div>
        <div class="pa-auth-card">
            <h2>Mot de passe oublié</h2>
            <p class="pa-muted">Indiquez votre adresse email pour recevoir un lien de réinitialisation.</p>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="pa-form">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" class="pa-input" type="email" name="email" :value="old('email')" required autofocus placeholder="nom@centre.cm" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="pa-form-meta">
            <a href="{{ route('login') }}">Retour à la connexion</a>
            <x-primary-button class="pa-primary-button">
                Envoyer le lien
            </x-primary-button>
        </div>
    </form>
        </div>
        <p class="pa-auth-footer">CENADI Douala</p>
    </div>
</x-guest-layout>
