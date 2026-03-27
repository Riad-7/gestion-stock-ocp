<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2">
            <h2 class="text-3xl font-semibold tracking-tight text-slate-950">
                Dashboard
            </h2>
            <p class="max-w-2xl text-sm text-slate-500">
                Centralisez le suivi du stock, des ventes et des alertes critiques depuis une seule interface.
            </p>
        </div>
    </x-slot>

    <div class="space-y-8">
        @if($notifications->isNotEmpty())
            <section class="panel-soft overflow-hidden">
                <div class="flex flex-col gap-4 border-b border-amber-100 bg-gradient-to-r from-amber-50 to-orange-50 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm font-semibold text-amber-800">Alertes prioritaires</p>
                        <p class="mt-1 text-sm text-amber-700">
                            {{ $notifications->count() }} notification(s) non lue(s) necessitent votre attention.
                        </p>
                    </div>
                    <span class="badge-soft badge-warn">Monitoring actif</span>
                </div>

                <div class="grid gap-3 p-4 sm:p-6 lg:grid-cols-2">
                    @foreach($notifications as $notif)
                        <div class="rounded-2xl border border-amber-100 bg-white px-4 py-4 shadow-sm">
                            <p class="text-sm font-medium text-slate-800">
                                {{ $notif->data['message'] ?? 'Nouvelle notification' }}
                            </p>
                            @if(isset($notif->data['date_expiration']))
                                <p class="mt-2 text-xs text-slate-500">
                                    Date d'expiration: {{ $notif->data['date_expiration'] }}
                                </p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        <section class="grid gap-5 lg:grid-cols-12">
            <div class="stat-card lg:col-span-3">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="stat-kicker">Stock total</p>
                        <p class="stat-value">{{ number_format($data['stock_total']) }}</p>
                        <p class="stat-copy">Unites disponibles sur les articles actifs.</p>
                    </div>
                    <span class="badge-soft badge-info">Live</span>
                </div>
            </div>

            <div class="stat-card lg:col-span-3">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="stat-kicker">Stock bas</p>
                        <p class="stat-value">{{ $data['nb_stock_bas'] }}</p>
                        <p class="stat-copy">Articles a reapprovisionner rapidement.</p>
                    </div>
                    <span class="badge-soft badge-warn">Action</span>
                </div>
            </div>

            <div class="stat-card lg:col-span-3">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="stat-kicker">Articles expires</p>
                        <p class="stat-value">{{ $data['nb_expires'] }}</p>
                        <p class="stat-copy">Produits sortis du stock exploitable.</p>
                    </div>
                    <span class="badge-soft badge-danger">Critique</span>
                </div>
            </div>

            <div class="stat-card lg:col-span-3">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="stat-kicker">Commandes attente</p>
                        <p class="stat-value">{{ $data['commandes_attente'] }}</p>
                        <p class="stat-copy">Demandes fournisseurs encore ouvertes.</p>
                    </div>
                    <span class="badge-soft badge-info">Suivi</span>
                </div>
            </div>

            <div class="stat-card lg:col-span-3">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="stat-kicker">Operations aujourd'hui</p>
                        <p class="stat-value">{{ $data['operations_today'] }}</p>
                        <p class="stat-copy">Actions enregistrees dans l historique du jour.</p>
                    </div>
                    <span class="badge-soft badge-info">Journal</span>
                </div>
            </div>
        </section>

        <section class="grid gap-5 xl:grid-cols-12">
            <div class="panel-soft xl:col-span-8">
                <div class="flex flex-col gap-3 border-b border-slate-200 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="section-title">Performance du jour</h3>
                        <p class="section-copy">Vue rapide des ventes et du chiffre d'affaires de la journee.</p>
                    </div>
                    <a href="{{ route('ventes.index') }}" class="btn-secondary">
                        Voir les ventes
                    </a>
                </div>

                <div class="grid gap-4 p-6 md:grid-cols-2">
                    <div class="rounded-3xl bg-slate-950 p-6 text-white shadow-xl shadow-slate-950/10">
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-200">Ventes aujourd'hui</p>
                        <p class="mt-4 text-4xl font-semibold">{{ $data['ventes_jour'] }}</p>
                        <p class="mt-2 text-sm text-slate-300">Transactions enregistrees depuis minuit.</p>
                    </div>

                    <div class="rounded-3xl bg-gradient-to-br from-cyan-500 to-sky-700 p-6 text-white shadow-xl shadow-cyan-900/20">
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-50">Chiffre d'affaires</p>
                        <p class="mt-4 text-4xl font-semibold">{{ $data['ca_jour'] }} DH</p>
                        <p class="mt-2 text-sm text-cyan-50/90">Montant cumule realise sur la journee.</p>
                    </div>
                </div>
            </div>

            <div class="panel-soft xl:col-span-4">
                <div class="border-b border-slate-200 px-6 py-5">
                    <h3 class="section-title">Resume operationnel</h3>
                    <p class="section-copy">Indicateurs clefs pour piloter la journee.</p>
                </div>

                <div class="space-y-4 p-6">
                    <div class="rounded-2xl bg-slate-50 px-4 py-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-slate-600">Taux d'alerte stock</span>
                            <span class="text-sm font-semibold text-slate-900">
                                {{ $data['stock_total'] > 0 ? number_format(($data['nb_stock_bas'] / max($data['stock_total'], 1)) * 100, 2) : '0.00' }}%
                            </span>
                        </div>
                    </div>

                    <div class="rounded-2xl bg-slate-50 px-4 py-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-slate-600">Articles surveilles</span>
                            <span class="text-sm font-semibold text-slate-900">
                                {{ $articles_stock_bas->count() + $articles_expires->count() }}
                            </span>
                        </div>
                    </div>

                    <div class="rounded-2xl bg-slate-50 px-4 py-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-slate-600">Notifications actives</span>
                            <span class="text-sm font-semibold text-slate-900">{{ $notifications->count() }}</span>
                        </div>
                    </div>

                    <div class="rounded-2xl bg-slate-50 px-4 py-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <span class="text-sm font-medium text-slate-600">Dernier backup</span>
                                @if($latestBackup)
                                    <p class="mt-1 text-sm font-semibold text-slate-900">{{ $latestBackup['updated_at']->format('d/m/Y H:i') }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ $latestBackup['name'] }} · {{ number_format($latestBackup['size_kb'], 2) }} KB</p>
                                @else
                                    <p class="mt-1 text-sm font-semibold text-slate-900">Aucun backup</p>
                                    <p class="mt-1 text-xs text-slate-500">Lance un backup depuis l historique.</p>
                                @endif
                            </div>
                            <span class="badge-soft badge-info">{{ $latestBackup ? 'OK' : 'Vide' }}</span>
                        </div>
                    </div>

                    <a href="{{ route('commandes.create') }}" class="btn-primary w-full">
                        Creer une commande
                    </a>
                </div>
            </div>
        </section>

        <section class="grid gap-5 xl:grid-cols-12">
            <div class="panel-soft xl:col-span-7">
                <div class="flex items-center justify-between border-b border-slate-200 px-6 py-5">
                    <div>
                        <h3 class="section-title">Actions rapides</h3>
                        <p class="section-copy">Ajoutez rapidement des donnees sans passer par plusieurs pages.</p>
                    </div>
                    <span class="badge-soft badge-info">Creation rapide</span>
                </div>

                <div class="grid gap-4 p-6 md:grid-cols-3">
                    <a href="{{ route('fournisseurs.create') }}" class="rounded-3xl border border-slate-200 bg-white px-5 py-5 shadow-sm transition hover:-translate-y-0.5 hover:border-cyan-200">
                        <p class="text-base font-semibold text-slate-900">Form fournisseur</p>
                        <p class="mt-2 text-sm text-slate-500">Ajouter les informations du fournisseur.</p>
                    </a>

                    <a href="{{ route('clients.create') }}" class="rounded-3xl border border-slate-200 bg-white px-5 py-5 shadow-sm transition hover:-translate-y-0.5 hover:border-cyan-200">
                        <p class="text-base font-semibold text-slate-900">Form client</p>
                        <p class="mt-2 text-sm text-slate-500">Creer rapidement une fiche client.</p>
                    </a>

                    <a href="{{ route('articles.create') }}" class="rounded-3xl border border-slate-200 bg-white px-5 py-5 shadow-sm transition hover:-translate-y-0.5 hover:border-cyan-200 md:col-span-3">
                        <p class="text-base font-semibold text-slate-900">Ajouter article</p>
                        <p class="mt-2 text-sm text-slate-500">Remplir le formulaire complet du lot/article avec quantite, prix et statut.</p>
                    </a>
                </div>
            </div>

            <div class="panel-soft xl:col-span-5">
                <div class="border-b border-slate-200 px-6 py-5">
                    <h3 class="section-title">Gestion des etats</h3>
                    <p class="section-copy">Acces direct aux listes pour modifier et suivre les statuts.</p>
                </div>

                <div class="grid gap-3 p-6">
                    <a href="{{ route('articles.index') }}" class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-4 transition hover:bg-slate-100">
                        <span>
                            <span class="block text-sm font-semibold text-slate-900">Articles et statuts</span>
                            <span class="mt-1 block text-xs text-slate-500">Actif, expire, stock faible.</span>
                        </span>
                        <span class="badge-soft badge-danger">{{ $data['nb_expires'] }} expires</span>
                    </a>

                    <a href="{{ route('commandes.index', ['statut' => 'en_attente']) }}" class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-4 transition hover:bg-slate-100">
                        <span>
                            <span class="block text-sm font-semibold text-slate-900">Commandes en attente</span>
                            <span class="mt-1 block text-xs text-slate-500">Livraison, annulation ou mise a jour.</span>
                        </span>
                        <span class="badge-soft badge-warn">{{ $data['commandes_attente'] }} pending</span>
                    </a>

                    <a href="{{ route('fournisseurs.index') }}" class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-4 transition hover:bg-slate-100">
                        <span>
                            <span class="block text-sm font-semibold text-slate-900">Fournisseurs</span>
                            <span class="mt-1 block text-xs text-slate-500">Consulter et modifier les infos fournisseurs.</span>
                        </span>
                        <span class="badge-soft badge-info">Contacts</span>
                    </a>
                </div>
            </div>
        </section>

        <section class="grid gap-5 xl:grid-cols-12">
            <div class="panel-soft overflow-hidden xl:col-span-7">
                <div class="flex items-center justify-between border-b border-slate-200 px-6 py-5">
                    <div>
                        <h3 class="section-title">Articles en stock bas</h3>
                        <p class="section-copy">Lots a traiter avant rupture.</p>
                    </div>
                    <a href="{{ route('articles.index') }}" class="btn-secondary">
                        Tous les articles
                    </a>
                </div>

                @if($articles_stock_bas->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Produit</th>
                                    <th>Quantite</th>
                                    <th>Seuil</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($articles_stock_bas as $article)
                                    <tr>
                                        <td>
                                            <div class="font-medium text-slate-900">{{ $article->produit->nom_produit }}</div>
                                        </td>
                                        <td>
                                            <span class="badge-soft badge-danger">{{ $article->quantite }}</span>
                                        </td>
                                        <td>{{ $article->seuil_minimum }}</td>
                                        <td>
                                            <a href="{{ route('commandes.create') }}" class="text-sm font-medium text-cyan-700 hover:text-cyan-800">
                                                Commander
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="px-6 py-12 text-center text-sm text-slate-500">
                        Aucun article critique pour le moment.
                    </div>
                @endif
            </div>

            <div class="panel-soft overflow-hidden xl:col-span-5">
                <div class="border-b border-slate-200 px-6 py-5">
                    <h3 class="section-title">Articles expires</h3>
                    <p class="section-copy">Produits a isoler ou retirer du circuit.</p>
                </div>

                @if($articles_expires->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Produit</th>
                                    <th>Expiration</th>
                                    <th>Quantite</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($articles_expires as $article)
                                    <tr>
                                        <td class="font-medium text-slate-900">{{ $article->produit->nom_produit }}</td>
                                        <td>{{ $article->date_expiration->format('d/m/Y') }}</td>
                                        <td>{{ $article->quantite }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="px-6 py-12 text-center text-sm text-slate-500">
                        Aucun article expire detecte.
                    </div>
                @endif
            </div>
        </section>

        <section class="panel-soft overflow-hidden">
            <div class="flex items-center justify-between border-b border-slate-200 px-6 py-5">
                <div>
                    <h3 class="section-title">Ventes recentes</h3>
                    <p class="section-copy">Dernieres transactions enregistrees dans le systeme.</p>
                </div>
                <span class="badge-soft badge-info">{{ $ventes_recentes->count() }} lignes</span>
            </div>

            @if($ventes_recentes->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Produit</th>
                                <th>Client</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ventes_recentes as $vente)
                                <tr>
                                    <td>{{ optional($vente->date_vente)->format('d/m/Y') }}</td>
                                    <td class="font-medium text-slate-900">{{ $vente->article?->produit?->nom_produit ?? 'Produit non defini' }}</td>
                                    <td>{{ $vente->client?->nom ?? 'Client comptoir' }}</td>
                                    <td>{{ number_format($vente->prix_total, 2) }} DH</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="px-6 py-12 text-center text-sm text-slate-500">
                    Aucune vente recente a afficher.
                </div>
            @endif
        </section>
    </div>
</x-app-layout>
