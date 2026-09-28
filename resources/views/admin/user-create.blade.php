<x-app-layout>
    <section class="pa-screen pa-screen-narrow">
        <div class="pa-back-heading"><a href="{{ route('admin.users.index') }}">←</a><h1>Ajouter un utilisateur</h1></div>
        <section class="pa-card pa-form-card">
            <p class="pa-muted">Créez un compte avec ses informations professionnelles.</p>
            <form method="POST" action="{{ route('admin.users.store') }}" class="pa-form">
                @csrf
                <div class="pa-form-grid">
                    <div><label for="first_name">Prénom</label><input id="first_name" name="first_name" class="pa-input" value="{{ old('first_name') }}" required autofocus>@error('first_name')<span class="pa-alert">{{ $message }}</span>@enderror</div>
                    <div><label for="last_name">Nom</label><input id="last_name" name="last_name" class="pa-input" value="{{ old('last_name') }}" required>@error('last_name')<span class="pa-alert">{{ $message }}</span>@enderror</div>
                </div>
                <div><label for="email">Adresse e-mail</label><input id="email" type="email" name="email" class="pa-input" value="{{ old('email') }}" required>@error('email')<span class="pa-alert">{{ $message }}</span>@enderror</div>
                <div class="pa-form-grid">
                    <div><label for="position">Poste</label><input id="position" name="position" class="pa-input" value="{{ old('position') }}" required>@error('position')<span class="pa-alert">{{ $message }}</span>@enderror</div>
                    <div><label for="department">Département</label><input id="department" name="department" class="pa-input" value="{{ old('department') }}" required>@error('department')<span class="pa-alert">{{ $message }}</span>@enderror</div>
                </div>
                <div><label for="role">Rôle</label><select id="role" name="role" class="pa-input" required>@foreach($roles as $role)<option value="{{ $role->value }}" @selected(old('role', 'personnel') === $role->value)>{{ match($role->value) { 'personnel' => 'Personnel', 'responsable_personnel' => 'Responsable du personnel', 'chef_centre' => 'Chef de centre', 'administrateur' => 'Administrateur' } }}</option>@endforeach</select>@error('role')<span class="pa-alert">{{ $message }}</span>@enderror</div>
                <div><label for="supervisor_id">Responsable (facultatif)</label><select id="supervisor_id" name="supervisor_id" class="pa-input"><option value="">Aucun responsable</option>@foreach($supervisors as $supervisor)<option value="{{ $supervisor->id }}" @selected((string) old('supervisor_id') === (string) $supervisor->id)>{{ $supervisor->name }}</option>@endforeach</select>@error('supervisor_id')<span class="pa-alert">{{ $message }}</span>@enderror</div>
                <div class="pa-form-grid">
                    <div><label for="password">Mot de passe</label><input id="password" type="password" name="password" class="pa-input" required>@error('password')<span class="pa-alert">{{ $message }}</span>@enderror</div>
                    <div><label for="password_confirmation">Confirmation</label><input id="password_confirmation" type="password" name="password_confirmation" class="pa-input" required></div>
                </div>
                <button class="pa-primary-button pa-full-button" type="submit">Créer l'utilisateur</button>
            </form>
        </section>
    </section>
</x-app-layout>
