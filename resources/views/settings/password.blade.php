<x-app-layout>
    <section class="pa-screen pa-screen-narrow">
        <div class="pa-back-heading"><a href="{{ route('settings.index') }}">←</a><h1>Modifier le mot de passe</h1></div>
        @if(session('status'))<div class="pa-attendance-success" role="status">{{ session('status') }}</div>@endif
        <section class="pa-card pa-form-card">
            <p class="pa-muted">Saisissez votre mot de passe actuel, puis choisissez un nouveau mot de passe sécurisé.</p>
            <form method="POST" action="{{ route('password.update') }}" class="pa-form">
                @csrf
                @method('PUT')
                <div><label for="current_password">Mot de passe actuel</label><input id="current_password" type="password" name="current_password" class="pa-input" required autofocus>@error('current_password', 'updatePassword')<span class="pa-alert">{{ $message }}</span>@enderror</div>
                <div><label for="password">Nouveau mot de passe</label><input id="password" type="password" name="password" class="pa-input" required>@error('password', 'updatePassword')<span class="pa-alert">{{ $message }}</span>@enderror</div>
                <div><label for="password_confirmation">Confirmer le nouveau mot de passe</label><input id="password_confirmation" type="password" name="password_confirmation" class="pa-input" required></div>
                <button class="pa-primary-button pa-full-button" type="submit">Enregistrer le nouveau mot de passe</button>
            </form>
        </section>
    </section>
</x-app-layout>
