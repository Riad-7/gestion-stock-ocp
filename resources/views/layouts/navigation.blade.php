@php
    $unreadNotifications = auth()->user()->unreadNotifications()->latest()->get();
    $notificationHistory = auth()->user()->notifications()->latest()->take(12)->get();
@endphp

<nav x-data="{ open: false }" class="page-wrap relative z-[110] pt-6">
    <div class="glass-panel px-4 py-4 sm:px-6">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                    <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-950 shadow-lg shadow-slate-950/15">
                        <x-application-logo class="h-8 w-8 rounded-xl object-contain" />
                    </span>

                    <div class="hidden sm:block">
                        <div class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-700">gs ocp</div>
                        <div class="text-sm font-semibold text-slate-900">GESTION STOCK</div>
                    </div>
                </a>

                <div class="hidden lg:flex lg:items-center lg:gap-2">
                    @php
                        $links = [
                            ['label' => 'Dashboard', 'route' => 'dashboard'],
                            ['label' => 'Clients', 'route' => 'clients.index'],
                            ['label' => 'Fournisseurs', 'route' => 'fournisseurs.index'],
                            ['label' => 'Articles', 'route' => 'articles.index'],
                            ['label' => 'Ventes', 'route' => 'ventes.index'],
                            ['label' => 'Commandes', 'route' => 'commandes.index'],
                        ];
                    @endphp

                    @foreach ($links as $link)
                        <a
                            href="{{ route($link['route']) }}"
                            class="{{ request()->routeIs($link['route']) ? 'bg-slate-950 text-white shadow-lg shadow-slate-950/10' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950' }} rounded-full px-4 py-2 text-sm font-medium transition"
                        >
                            {{ $link['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:gap-3">
                <div x-data="{ notificationsOpen: false }" class="relative" @keydown.escape.window="notificationsOpen = false">
                    <button
                        @click="notificationsOpen = !notificationsOpen"
                        class="relative inline-flex items-center gap-3 rounded-full border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:border-slate-300 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-cyan-400 focus:ring-offset-2"
                    >
                        <span class="flex h-9 w-9 items-center justify-center rounded-full bg-amber-100 text-amber-700">!</span>
                        <span class="hidden md:block">Notifications</span>
                        @if($unreadNotifications->count() > 0)
                            <span class="absolute -right-1 -top-1 inline-flex min-h-6 min-w-6 items-center justify-center rounded-full bg-rose-600 px-1.5 text-xs font-semibold text-white">
                                {{ $unreadNotifications->count() }}
                            </span>
                        @endif
                    </button>

                    <div
                        x-cloak
                        x-show="notificationsOpen"
                        x-transition.opacity
                        class="fixed inset-0 z-[90] bg-slate-950/20 backdrop-blur-[2px]"
                        @click="notificationsOpen = false"
                    ></div>

                    <div
                        x-cloak
                        x-show="notificationsOpen"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                        x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                        @click.outside="notificationsOpen = false"
                        class="fixed right-6 top-24 z-[100] w-[24rem] overflow-hidden rounded-[1.75rem] border border-slate-200 bg-white shadow-[0_30px_80px_-24px_rgba(15,23,42,0.45)]"
                    >
                        <div class="border-b border-slate-200 px-5 py-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">Alertes recentes</p>
                                    <p class="mt-1 text-xs text-slate-500">Popup visible au-dessus du contenu.</p>
                                </div>
                                <button @click="notificationsOpen = false" class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">
                                    Fermer
                                </button>
                            </div>
                        </div>

                        <div class="max-h-[30rem] overflow-y-auto">
                            <div class="border-b border-slate-100 px-5 py-3">
                                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">Nouvelles notifications</p>
                            </div>

                            @forelse($unreadNotifications as $notif)
                                <div class="border-b border-slate-100 px-5 py-4">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <p class="text-sm font-medium text-slate-800">
                                                {{ $notif->data['message'] ?? 'Nouvelle notification' }}
                                            </p>
                                            <p class="mt-1 text-xs text-slate-500">
                                                {{ optional($notif->created_at)->diffForHumans() }}
                                            </p>
                                            @if(isset($notif->data['date_expiration']))
                                                <p class="mt-1 text-xs text-slate-500">
                                                    Expire le {{ $notif->data['date_expiration'] }}
                                                </p>
                                            @endif
                                        </div>
                                        <span @class([
                                            'badge-soft',
                                            'badge-warn' => ($notif->data['type'] ?? 'warning') === 'warning' || ($notif->data['type'] ?? null) === 'stock_bas' || ($notif->data['type'] ?? null) === 'article_expire',
                                            'badge-danger' => ($notif->data['type'] ?? null) === 'danger',
                                            'badge-info' => in_array($notif->data['type'] ?? 'info', ['info', 'success']),
                                        ])>
                                            {{ str_replace('_', ' ', $notif->data['type'] ?? 'info') }}
                                        </span>
                                    </div>
                                </div>
                            @empty
                                <div class="border-b border-slate-100 px-5 py-6 text-center text-sm text-slate-500">
                                    Aucune nouvelle notification
                                </div>
                            @endforelse

                            <div class="border-b border-slate-100 px-5 py-3">
                                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">Historique notifications</p>
                            </div>

                            @forelse($notificationHistory as $notif)
                                <div class="border-b border-slate-100 px-5 py-4 last:border-b-0">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <p class="text-sm font-medium text-slate-800">
                                                {{ $notif->data['message'] ?? 'Nouvelle notification' }}
                                            </p>
                                            <p class="mt-1 text-xs text-slate-500">
                                                {{ optional($notif->created_at)->format('d/m/Y H:i') }}
                                            </p>
                                        </div>
                                        <span class="{{ $notif->read_at ? 'badge-soft badge-info' : 'badge-soft badge-warn' }}">
                                            {{ $notif->read_at ? 'lu' : 'non lu' }}
                                        </span>
                                    </div>
                                </div>
                            @empty
                                <div class="px-5 py-6 text-center text-sm text-slate-500">
                                    Aucun historique disponible
                                </div>
                            @endforelse
                        </div>

                        @if($unreadNotifications->count() > 0)
                            <form action="{{ route('notifications.markAsRead') }}" method="POST" class="border-t border-slate-200 p-3">
                                @csrf
                                <button type="submit" class="btn-secondary w-full">
                                    Tout marquer comme lu
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <x-dropdown align="right" width="64" contentClasses="overflow-hidden rounded-3xl border border-slate-200 bg-white p-2 shadow-2xl shadow-slate-900/10">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-3 rounded-full border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 transition hover:border-slate-300 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-cyan-400 focus:ring-offset-2">
                            <span class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-950 text-sm font-semibold text-white">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </span>
                            <span class="hidden text-left md:block">
                                <span class="block text-sm font-semibold text-slate-900">{{ Auth::user()->name }}</span>
                                <span class="block text-xs text-slate-500">{{ Auth::user()->email }}</span>
                            </span>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-3 py-2">
                            <a href="{{ route('profile.edit') }}" class="flex rounded-2xl px-4 py-3 text-sm font-medium text-slate-700 transition hover:bg-slate-50 hover:text-slate-950">
                                Profile
                            </a>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="mt-1 flex w-full rounded-2xl px-4 py-3 text-left text-sm font-medium text-rose-600 transition hover:bg-rose-50">
                                    Log Out
                                </button>
                            </form>
                        </div>
                    </x-slot>
                </x-dropdown>
            </div>

            <button @click="open = !open" class="inline-flex h-12 w-12 items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-700 transition hover:bg-slate-50 sm:hidden">
                <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path :class="{ 'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    <path :class="{ 'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div x-cloak x-show="open" x-transition class="mt-4 space-y-3 border-t border-slate-200 pt-4 sm:hidden">
            @foreach ([
                ['label' => 'Dashboard', 'route' => 'dashboard'],
                ['label' => 'Clients', 'route' => 'clients.index'],
                ['label' => 'Fournisseurs', 'route' => 'fournisseurs.index'],
                ['label' => 'Articles', 'route' => 'articles.index'],
                ['label' => 'Ventes', 'route' => 'ventes.index'],
                ['label' => 'Commandes', 'route' => 'commandes.index'],
            ] as $link)
                <a
                    href="{{ route($link['route']) }}"
                    class="{{ request()->routeIs($link['route']) ? 'bg-slate-950 text-white' : 'bg-white text-slate-700' }} flex rounded-2xl px-4 py-3 text-sm font-medium shadow-sm"
                >
                    {{ $link['label'] }}
                </a>
            @endforeach

            <div class="rounded-2xl border border-slate-200 bg-white p-4">
                <p class="text-sm font-semibold text-slate-900">{{ Auth::user()->name }}</p>
                <p class="mt-1 text-xs text-slate-500">{{ Auth::user()->email }}</p>
                <div class="mt-4 flex gap-2">
                    <a href="{{ route('profile.edit') }}" class="btn-secondary flex-1">Profile</a>
                    <form method="POST" action="{{ route('logout') }}" class="flex-1">
                        @csrf
                        <button type="submit" class="btn-primary w-full">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</nav>
