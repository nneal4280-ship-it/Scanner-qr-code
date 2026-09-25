@php($currentUser = auth()->user())
@php($role = $currentUser?->role?->value ?? 'personnel')
<header class="pa-topbar">
    <a class="pa-brand" href="{{ route('dashboard') }}"><span class="pa-brand-mark">◷</span><span>Pointage<span>App</span></span></a>
    <nav class="pa-desktop-nav" aria-label="Navigation principale">
        <a class="{{ request()->routeIs('dashboard') ? 'is-active' : '' }}" href="{{ route('dashboard') }}">Accueil</a>
        @if($role === 'personnel')
            <a href="{{ route('attendance.history') }}">Historique</a><a href="{{ route('profile.edit') }}">Profil</a>
        @elseif(in_array($role, ['responsable_personnel','chef_centre']))
            <a href="{{ route('supervision.dashboard') }}">Suivi</a><a href="{{ route('reports.index') }}">Rapports</a>
        @else
            <a href="{{ route('admin.dashboard') }}">Utilisateurs</a><a href="{{ route('reports.index') }}">Rapports</a>
        @endif
    </nav>
    <div class="pa-top-actions"><button class="pa-icon-button" type="button" data-theme-toggle aria-label="Changer le thème">☼</button><div class="pa-avatar">{{ collect(explode(' ', $currentUser->name ?? 'U'))->map(fn($part) => strtoupper(substr($part, 0, 1)))->join('') }}</div><form method="POST" action="{{ route('logout') }}" class="pa-logout-form">@csrf<button class="pa-text-button" type="submit">Sortir</button></form></div>
</header>
<nav class="pa-bottom-nav" aria-label="Navigation mobile">
    <a class="{{ request()->routeIs('dashboard') ? 'is-active' : '' }}" href="{{ route('dashboard') }}"><span>⌂</span>Accueil</a>
    @if($role === 'personnel')
        <a class="{{ request()->routeIs('attendance.*') ? 'is-active' : '' }}" href="{{ route('attendance.history') }}"><span>◴</span>Historique</a><a href="{{ route('profile.edit') }}"><span>♙</span>Profil</a>
    @else
        <a href="{{ $role === 'administrateur' ? route('admin.dashboard') : route('supervision.dashboard') }}"><span>▦</span>{{ $role === 'administrateur' ? 'Utilisateurs' : 'Suivi' }}</a><a href="{{ route('reports.index') }}"><span>▥</span>Rapports</a>
    @endif
</nav>
{{-- Legacy Breeze navigation is intentionally kept below only as source context in this migration. --}}
{{--
<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav> --}}
