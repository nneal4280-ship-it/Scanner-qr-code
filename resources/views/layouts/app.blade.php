<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>try { if (localStorage.getItem('pointage-theme') === 'dark') document.documentElement.classList.add('pa-dark'); } catch (_) {}</script>
    <title>{{ config('app.name', 'PointageApp') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="pa-body">
    <div class="pa-frame">
        @include('layouts.navigation')
        @isset($header)<div class="pa-page-heading">{{ $header }}</div>@endisset
        <main class="pa-main">
            @if ($errors->any())
                <div class="pa-validation-error" role="alert">
                    <strong>Pointage non enregistré</strong>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif
            @if (session('attendance_success'))
                <div class="pa-attendance-success" role="status">
                    <strong>✓ {{ session('attendance_success') }}</strong>
                    <span>{{ session('attendance_type') }} validée à {{ session('attendance_time') }}</span>
                </div>
            @endif
            {{ $slot }}
        </main>
    </div>
    @stack('scripts')
</body>
</html>
