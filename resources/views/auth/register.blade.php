<x-guest-layout>
    <div class="pa-auth-shell">
        <div class="pa-auth-brand"><x-brand-logo class="pa-auth-logo" /><h1>PointageApp</h1><p>Système de gestion des présences</p></div>
        <div class="pa-auth-card">
            <h2>Créer un compte</h2>
            <form method="POST" action="{{ route('register') }}" class="pa-form">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" value="Nom complet" />
            <x-text-input id="name" class="pa-input" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Votre nom complet" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" class="pa-input" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="nom@centre.cm" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" value="Mot de passe" />

            <x-text-input id="password" class="pa-input"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" value="Confirmer le mot de passe" />

            <x-text-input id="password_confirmation" class="pa-input"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="pa-form-meta">
            <a href="{{ route('login') }}">
                J’ai déjà un compte
            </a>

            <x-primary-button class="pa-primary-button">
                Créer mon compte
            </x-primary-button>
        </div>
            </form>
        </div>
        <p class="pa-auth-footer">CENADI Douala</p>
    </div>
</x-guest-layout>
