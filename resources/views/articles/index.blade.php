<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2">
            <h2 class="text-3xl font-semibold tracking-tight text-slate-950">Articles</h2>
            <p class="text-sm text-slate-500">Ajoutez un article avec le nom que vous voulez puis retrouvez-le juste en dessous.</p>
        </div>
    </x-slot>

    <div class="grid gap-6 xl:grid-cols-[0.98fr_1.02fr]">
        <section class="panel-soft p-6 sm:p-8">
            <div class="mb-6">
                <h3 class="section-title">Formulaire article</h3>
                <p class="section-copy">Vous pouvez selectionner un produit existant ou taper un nouveau nom de produit/article.</p>
            </div>

            <form method="POST" action="{{ route('articles.store') }}" class="grid gap-5 md:grid-cols-2">
                @csrf

                <div class="md:col-span-2">
                    <label for="nom_produit" class="block text-sm font-medium text-slate-700">Nom du produit / article</label>
                    <input id="nom_produit" name="nom_produit" type="text" value="{{ old('nom_produit') }}" placeholder="Ex: Paracetamol 500mg" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3">
                    @error('nom_produit') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="produit_id" class="block text-sm font-medium text-slate-700">Ou choisir un produit existant</label>
                    <select id="produit_id" name="produit_id" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3">
                        <option value="">Aucun - creer avec le nom saisi</option>
                        @foreach($produits as $produit)
                            <option value="{{ $produit->id }}" @selected(old('produit_id') == $produit->id)>
                                {{ $produit->nom_produit }} - {{ $produit->reference }}
                            </option>
                        @endforeach
                    </select>
                    @error('produit_id') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="quantite" class="block text-sm font-medium text-slate-700">Quantite</label>
                    <input id="quantite" name="quantite" type="number" min="0" value="{{ old('quantite', 0) }}" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3" required>
                    @error('quantite') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="seuil_minimum" class="block text-sm font-medium text-slate-700">Seuil minimum</label>
                    <input id="seuil_minimum" name="seuil_minimum" type="number" min="0" value="{{ old('seuil_minimum', 5) }}" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3" required>
                    @error('seuil_minimum') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="prix_unitaire" class="block text-sm font-medium text-slate-700">Prix unitaire</label>
                    <input id="prix_unitaire" name="prix_unitaire" type="number" step="0.01" min="0" value="{{ old('prix_unitaire') }}" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3" required>
                    @error('prix_unitaire') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="statut" class="block text-sm font-medium text-slate-700">Statut</label>
                    <select id="statut" name="statut" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3" required>
                        <option value="actif" @selected(old('statut', 'actif') === 'actif')>Actif</option>
                        <option value="expire" @selected(old('statut') === 'expire')>Expire</option>
                        <option value="epuise" @selected(old('statut') === 'epuise')>Epuise</option>
                    </select>
                    @error('statut') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="date_fabrication" class="block text-sm font-medium text-slate-700">Date fabrication</label>
                    <input id="date_fabrication" name="date_fabrication" type="date" value="{{ old('date_fabrication') }}" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3">
                    @error('date_fabrication') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="date_expiration" class="block text-sm font-medium text-slate-700">Date expiration</label>
                    <input id="date_expiration" name="date_expiration" type="date" value="{{ old('date_expiration') }}" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3">
                    @error('date_expiration') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2 flex justify-end">
                    <button type="submit" class="btn-primary">Ajouter l'article</button>
                </div>
            </form>
        </section>

        <section class="space-y-6">
            <div class="grid gap-4 md:grid-cols-3">
                <div class="stat-card">
                    <p class="stat-kicker">Stock bas</p>
                    <p class="stat-value">{{ $stats['stock_bas'] }}</p>
                    <p class="stat-copy">Articles sous le seuil minimal.</p>
                </div>
                <div class="stat-card">
                    <p class="stat-kicker">Articles expires</p>
                    <p class="stat-value">{{ $stats['expires'] }}</p>
                    <p class="stat-copy">Statut expire a surveiller.</p>
                </div>
                <div class="stat-card">
                    <p class="stat-kicker">Quantite commandee</p>
                    <p class="stat-value">{{ $stats['quantite_commandee'] }}</p>
                    <p class="stat-copy">Total des quantites commandees.</p>
                </div>
            </div>

            @if($stockBasArticles->isNotEmpty())
                <div class="panel-soft border border-amber-200 bg-amber-50/70 p-6">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="section-title text-amber-900">Alertes stock minimal</h3>
                            <p class="section-copy text-amber-800">Ces articles sont deja sous le seuil minimum.</p>
                        </div>
                        <span class="badge-soft badge-warn">{{ $stockBasArticles->count() }} alertes</span>
                    </div>

                    <div class="mt-4 grid gap-3">
                        @foreach($stockBasArticles as $stockArticle)
                            <div class="rounded-2xl border border-amber-200 bg-white/80 px-4 py-3 text-sm text-amber-900">
                                <span class="font-semibold">{{ $stockArticle->produit?->nom_produit ?? 'Article' }}</span>
                                : stock {{ $stockArticle->quantite }} / minimum {{ $stockArticle->seuil_minimum }}
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="panel-soft overflow-hidden">
                <div class="border-b border-slate-200 px-6 py-5">
                    <h3 class="section-title">Liste des articles</h3>
                    <p class="section-copy">Changez l etat de chaque article et suivez les quantites commandees depuis le tableau.</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Article / produit</th>
                                <th>Stock</th>
                                <th>Quantite commandee</th>
                                <th>Seuil min</th>
                                <th>Etat</th>
                                <th>Prix</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($articles as $article)
                                <tr>
                                    <td class="font-medium text-slate-900">
                                        <div>{{ $article->produit?->nom_produit ?? 'Produit non defini' }}</div>
                                        @if($article->date_expiration)
                                            <div class="mt-1 text-xs text-slate-500">Expire le {{ $article->date_expiration->format('d/m/Y') }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <span @class([
                                            'badge-soft',
                                            'badge-warn' => $article->quantite < $article->seuil_minimum,
                                            'badge-info' => $article->quantite >= $article->seuil_minimum,
                                        ])>
                                            {{ $article->quantite }}
                                        </span>
                                    </td>
                                    <td>{{ (int) ($article->quantite_commandee ?? 0) }}</td>
                                    <td>{{ $article->seuil_minimum }}</td>
                                    <td>
                                        <form method="POST" action="{{ route('articles.statut', $article) }}" class="min-w-[10rem]">
                                            @csrf
                                            @method('PATCH')
                                            <select
                                                name="statut"
                                                onchange="this.form.submit()"
                                                class="w-full rounded-full border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700"
                                            >
                                                <option value="actif" @selected($article->statut === 'actif')>Actif</option>
                                                <option value="expire" @selected($article->statut === 'expire')>Expire</option>
                                                <option value="epuise" @selected($article->statut === 'epuise')>Epuise</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td>{{ number_format($article->prix_unitaire, 2) }} DH</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-10 text-center text-sm text-slate-500">Aucun article enregistre.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4">
                    {{ $articles->links() }}
                </div>
            </div>
        </section>
    </div>
</x-app-layout>
