<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @include('layouts.partials.page-meta')

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="app-shell">
        @php
            $bottomNotifications = auth()->check()
                ? auth()->user()->notifications()->latest()->take(3)->get()
                : collect();
            $latestBottomNotification = $bottomNotifications->first();
            $latestStockAlert = auth()->check()
                ? auth()->user()->unreadNotifications()
                    ->where('type', '!=', '')
                    ->get()
                    ->first(fn ($notification) => in_array($notification->data['type'] ?? null, ['stock_bas', 'article_expire']))
                : null;
            $bottomAlertNotification = $latestStockAlert ?? $latestBottomNotification;
            $showBottomAlert = session('success') || session('warning') || $errors->any() || filled($latestStockAlert);
        @endphp

        <div class="absolute inset-x-0 top-0 -z-10 h-80 bg-[radial-gradient(circle_at_top,rgba(34,211,238,0.18),transparent_45%)]"></div>

        <div
            x-data="{
                successOpen: {{ session('success') ? 'true' : 'false' }},
                warningOpen: {{ session('warning') ? 'true' : 'false' }},
                errorOpen: {{ $errors->any() ? 'true' : 'false' }},
                bottomAlertOpen: {{ $showBottomAlert ? 'true' : 'false' }}
            }"
            class="relative min-h-screen pb-10"
        >
            @include('layouts.navigation')

            <div class="pointer-events-none fixed inset-x-0 top-5 z-[140] flex flex-col items-center gap-3 px-4">
                @if(session('success'))
                    <div
                        x-cloak
                        x-show="successOpen"
                        x-init="setTimeout(() => successOpen = false, 4500)"
                        x-transition
                        class="toast-popup border-emerald-200/80 bg-emerald-50/95 text-emerald-900"
                    >
                        <div class="flex items-start gap-3">
                            <span class="mt-0.5 inline-flex h-9 w-9 items-center justify-center rounded-full bg-emerald-100 text-sm font-bold text-emerald-700">OK</span>
                            <div>
                                <p class="text-sm font-semibold">Operation reussie</p>
                                <p class="mt-1 text-sm text-emerald-800">{{ session('success') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if(session('warning'))
                    <div
                        x-cloak
                        x-show="warningOpen"
                        x-init="setTimeout(() => warningOpen = false, 5500)"
                        x-transition
                        class="toast-popup border-amber-200/80 bg-amber-50/95 text-amber-900"
                    >
                        <div class="flex items-start gap-3">
                            <span class="mt-0.5 inline-flex h-9 w-9 items-center justify-center rounded-full bg-amber-100 text-sm font-bold text-amber-700">!</span>
                            <div>
                                <p class="text-sm font-semibold">Alerte stock</p>
                                <p class="mt-1 text-sm text-amber-800">{{ session('warning') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if($errors->any())
                    <div
                        x-cloak
                        x-show="errorOpen"
                        x-init="setTimeout(() => errorOpen = false, 6500)"
                        x-transition
                        class="toast-popup border-rose-200/80 bg-rose-50/95 text-rose-900"
                    >
                        <div class="flex items-start gap-3">
                            <span class="mt-0.5 inline-flex h-9 w-9 items-center justify-center rounded-full bg-rose-100 text-sm font-bold text-rose-700">!</span>
                            <div>
                                <p class="text-sm font-semibold">Attention</p>
                                <p class="mt-1 text-sm text-rose-800">{{ $errors->first('error') ?? $errors->first() }}</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            @isset($header)
                <header class="page-wrap pt-8">
                    <div class="glass-panel overflow-hidden px-6 py-6 sm:px-8">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                            <div class="space-y-2">
                                <div class="badge-soft badge-info">Operations overview</div>
                                <div>
                                    {{ $header }}
                                </div>
                            </div>

                            <div class="flex flex-wrap items-center gap-3 text-sm text-slate-500">
                                <div class="rounded-full border border-slate-200 bg-slate-50 px-4 py-2">
                                    {{ now()->format('d M Y') }}
                                </div>
                                <div class="rounded-full border border-emerald-200 bg-emerald-50 px-4 py-2 text-emerald-700">
                                    System online
                                </div>
                            </div>
                        </div>
                    </div>
                </header>
            @endisset

            <main class="page-wrap pt-8">
                {{ $slot }}
            </main>

            @auth
                @if($showBottomAlert && $bottomAlertNotification)
                    <div class="pointer-events-none fixed inset-x-0 bottom-5 z-[135] flex justify-end px-4 sm:px-6">
                        <div
                            x-cloak
                            x-show="bottomAlertOpen"
                            x-init="setTimeout(() => bottomAlertOpen = false, 5000)"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 translate-y-6"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 translate-y-4"
                            class="notification-dock pointer-events-auto"
                        >
                            <div class="flex items-center justify-between gap-3 border-b border-slate-200 px-4 py-3">
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">Alert notification</p>
                                    <p class="text-xs text-slate-500">Elle apparait seulement quand une action se produit.</p>
                                </div>
                                <button
                                    type="button"
                                    @click="bottomAlertOpen = false"
                                    class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600"
                                >
                                    Fermer
                                </button>
                            </div>

                            <div class="px-4 py-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-medium text-slate-800">
                                            {{ $bottomAlertNotification->data['message'] ?? 'Nouvelle notification' }}
                                        </p>
                                        <p class="mt-1 text-xs text-slate-500">
                                            {{ optional($bottomAlertNotification->created_at)->diffForHumans() }}
                                        </p>
                                    </div>
                                    <span @class([
                                        'badge-soft',
                                        'badge-warn' => in_array($bottomAlertNotification->data['type'] ?? 'warning', ['warning', 'stock_bas', 'article_expire']),
                                        'badge-danger' => ($bottomAlertNotification->data['type'] ?? null) === 'danger',
                                        'badge-info' => in_array($bottomAlertNotification->data['type'] ?? 'info', ['info', 'success']),
                                    ])>
                                        {{ str_replace('_', ' ', $bottomAlertNotification->data['type'] ?? 'info') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endauth
        </div>
    </body>
</html>
