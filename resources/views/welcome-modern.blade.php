<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        @include('layouts.partials.page-meta')

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-100">
        <div class="relative overflow-hidden">
            <div class="absolute inset-x-0 top-0 -z-10 h-[32rem] bg-[radial-gradient(circle_at_top_left,rgba(34,211,238,0.24),transparent_30%),radial-gradient(circle_at_top_right,rgba(14,165,233,0.18),transparent_24%)]"></div>

            <main class="mx-auto flex min-h-screen w-full max-w-7xl items-center px-4 py-10 sm:px-6 lg:px-8">
                <div class="grid w-full gap-8 lg:grid-cols-[1.15fr_0.85fr]">
                    <section class="glass-panel px-6 py-8 sm:px-10 sm:py-10">
                        <div class="max-w-2xl">
                            <div class="badge-soft badge-info">Gestion de stock professionnelle</div>
                            <h1 class="mt-6 text-4xl font-semibold tracking-tight text-slate-950 sm:text-5xl">
                                Pilotez vos articles, commandes, clients et ventes depuis une seule plateforme.
                            </h1>
                            <p class="mt-6 text-lg leading-8 text-slate-600">
                                Organisez le stock, surveillez les alertes critiques et gardez une vision claire sur les operations quotidiennes avec une interface simple et moderne.
                            </p>

                            <div class="mt-8 flex flex-wrap gap-3">
                                <a href="{{ route('login') }}" class="btn-primary">
                                    Connexion
                                </a>
                                <a href="{{ route('register') }}" class="btn-secondary">
                                    Creer un compte
                                </a>
                            </div>

                            <div class="mt-10 grid gap-4 sm:grid-cols-3">
                                <div class="panel-soft px-5 py-5">
                                    <p class="text-sm font-semibold text-slate-900">Stock en temps reel</p>
                                    <p class="mt-2 text-sm text-slate-500">Suivez les niveaux, les seuils minimums et les expirations.</p>
                                </div>
                                <div class="panel-soft px-5 py-5">
                                    <p class="text-sm font-semibold text-slate-900">Commandes centralisees</p>
                                    <p class="mt-2 text-sm text-slate-500">Lancez et suivez les commandes fournisseurs facilement.</p>
                                </div>
                                <div class="panel-soft px-5 py-5">
                                    <p class="text-sm font-semibold text-slate-900">Notifications visibles</p>
                                    <p class="mt-2 text-sm text-slate-500">Recevez les alertes importantes en popup au bon moment.</p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="grid gap-6">
                        <div class="glass-panel px-6 py-7">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <p class="text-sm font-semibold uppercase tracking-[0.24em] text-cyan-700">Acces rapide</p>
                                    <h2 class="mt-2 text-2xl font-semibold text-slate-950">Connexion / Register</h2>
                                </div>
                                <div class="badge-soft badge-warn">Start here</div>
                            </div>

                            <div class="mt-6 grid gap-4 sm:grid-cols-2">
                                <a href="{{ route('login') }}" class="panel-soft px-5 py-5 transition hover:-translate-y-0.5">
                                    <p class="text-base font-semibold text-slate-900">Connexion</p>
                                    <p class="mt-2 text-sm text-slate-500">Acceder au dashboard et gerer l'activite.</p>
                                    <p class="mt-4 text-sm font-medium text-cyan-700">Se connecter</p>
                                </a>

                                <a href="{{ route('register') }}" class="panel-soft px-5 py-5 transition hover:-translate-y-0.5">
                                    <p class="text-base font-semibold text-slate-900">Register</p>
                                    <p class="mt-2 text-sm text-slate-500">Creer un compte utilisateur pour demarrer.</p>
                                    <p class="mt-4 text-sm font-medium text-cyan-700">S'inscrire</p>
                                </a>
                            </div>
                        </div>

                        <div class="glass-panel px-6 py-7">
                            <p class="text-sm font-semibold uppercase tracking-[0.24em] text-slate-500">Modules disponibles</p>
                            <div class="mt-6 grid gap-3 sm:grid-cols-2">
                                <div class="rounded-2xl bg-slate-950 px-5 py-5 text-white">
                                    <p class="text-sm font-semibold">Articles</p>
                                    <p class="mt-2 text-sm text-slate-300">Ajout, edition et suivi du stock actif.</p>
                                </div>
                                <div class="rounded-2xl bg-white px-5 py-5 shadow-sm ring-1 ring-slate-200">
                                    <p class="text-sm font-semibold text-slate-900">Commandes</p>
                                    <p class="mt-2 text-sm text-slate-500">Suivi des commandes fournisseurs et etats.</p>
                                </div>
                                <div class="rounded-2xl bg-white px-5 py-5 shadow-sm ring-1 ring-slate-200">
                                    <p class="text-sm font-semibold text-slate-900">Clients</p>
                                    <p class="mt-2 text-sm text-slate-500">Gestion des fiches clients et ventes associees.</p>
                                </div>
                                <div class="rounded-2xl bg-white px-5 py-5 shadow-sm ring-1 ring-slate-200">
                                    <p class="text-sm font-semibold text-slate-900">Notifications</p>
                                    <p class="mt-2 text-sm text-slate-500">Alertes de stock bas et produits expires.</p>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </main>
        </div>
    </body>
</html>
