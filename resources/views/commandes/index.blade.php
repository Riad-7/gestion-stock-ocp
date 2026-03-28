<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2">
            <h2 class="text-3xl font-semibold tracking-tight text-slate-950">Commandes</h2>
            <p class="text-sm text-slate-500">Creez une commande et suivez son statut de livraison depuis la meme interface.</p>
        </div>
    </x-slot>

    <div class="grid gap-6 xl:grid-cols-[0.95fr_1.05fr]">
        <section class="panel-soft p-6 sm:p-8">
            <div class="mb-6">
                <h3 class="section-title">Formulaire commande</h3>
                <p class="section-copy">Commandez un nouvel article directement ici. Il apparaitra dans la page articles avec un stock en attente jusqu a la livraison.</p>
            </div>

            <form method="POST" action="{{ route('commandes.store') }}" class="grid gap-5 md:grid-cols-2">
                @csrf
                <div class="md:col-span-2">
                    <label for="nom_produit" class="block text-sm font-medium text-slate-700">Nom du produit / article</label>
                    <input id="nom_produit" name="nom_produit" type="text" value="{{ old('nom_produit') }}" placeholder="Ex: Gants nitrile taille M" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3" required>
                    @error('nom_produit') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="fournisseur_id" class="block text-sm font-medium text-slate-700">Fournisseur</label>
                    <select id="fournisseur_id" name="fournisseur_id" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3" required>
                        <option value="">Choisir un fournisseur</option>
                        @foreach($fournisseurs as $fournisseur)
                            <option value="{{ $fournisseur->id }}" @selected(old('fournisseur_id') == $fournisseur->id)>
                                {{ $fournisseur->nom }} {{ $fournisseur->prenom }}
                            </option>
                        @endforeach
                    </select>
                    @error('fournisseur_id') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="quantite" class="block text-sm font-medium text-slate-700">Quantite</label>
                    <input id="quantite" name="quantite" type="number" min="1" value="{{ old('quantite', 1) }}" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3" required>
                    @error('quantite') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="prix_unitaire" class="block text-sm font-medium text-slate-700">Prix unitaire</label>
                    <input id="prix_unitaire" name="prix_unitaire" type="number" step="0.01" min="0" value="{{ old('prix_unitaire') }}" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3" required>
                    @error('prix_unitaire') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="seuil_minimum" class="block text-sm font-medium text-slate-700">Seuil minimum</label>
                    <input id="seuil_minimum" name="seuil_minimum" type="number" min="0" value="{{ old('seuil_minimum', 5) }}" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3" required>
                    @error('seuil_minimum') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="date_commande" class="block text-sm font-medium text-slate-700">Date commande</label>
                    <input id="date_commande" name="date_commande" type="date" value="{{ old('date_commande', now()->toDateString()) }}" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3" required>
                    @error('date_commande') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="date_livraison" class="block text-sm font-medium text-slate-700">Date livraison</label>
                    <input id="date_livraison" name="date_livraison" type="date" value="{{ old('date_livraison') }}" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3">
                    @error('date_livraison') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
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

                <div>
                    <label for="reference_commande" class="block text-sm font-medium text-slate-700">Reference</label>
                    <input id="reference_commande" name="reference_commande" type="text" value="{{ old('reference_commande') }}" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3">
                    @error('reference_commande') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2">
                    <div class="rounded-2xl border border-cyan-200 bg-cyan-50 px-4 py-3 text-sm text-cyan-900">
                        La commande sera creee en <strong>En attente</strong>. Le stock de l article restera a 0 jusqu a la livraison.
                    </div>
                </div>

                <div class="md:col-span-2 flex justify-end">
                    <button type="submit" class="btn-primary">Ajouter la commande</button>
                </div>
            </form>
        </section>

        <section class="panel-soft overflow-hidden">
            <div class="border-b border-slate-200 px-6 py-5">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="section-title">Suivi des commandes</h3>
                        <p class="section-copy">Passez les commandes en livree, annulee ou en attente directement depuis le tableau.</p>
                    </div>
                    <div class="flex flex-wrap gap-3 text-sm">
                        <span class="badge-soft badge-warn">En attente: {{ $stats['en_attente'] }}</span>
                        <span class="badge-soft badge-info">Livrees: {{ $stats['livree'] }}</span>
                        <span class="badge-soft badge-danger">Annulees: {{ $stats['annulee'] }}</span>
                    </div>
                </div>

                <form method="GET" action="{{ route('commandes.index') }}" class="mt-4 grid gap-3 lg:grid-cols-[minmax(0,1.2fr)_minmax(0,1fr)_minmax(0,1fr)_minmax(0,1fr)_minmax(0,1fr)_auto] lg:items-end">
                    <div>
                        <label for="search_commandes" class="block text-sm font-medium text-slate-700">Recherche</label>
                        <input
                            id="search_commandes"
                            name="search"
                            type="text"
                            value="{{ request('search') }}"
                            placeholder="Reference, article ou fournisseur"
                            class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-700"
                        >
                    </div>

                    <div>
                        <label for="filtre_statut_commande" class="block text-sm font-medium text-slate-700">Filtrer par statut</label>
                        <select id="filtre_statut_commande" name="statut" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-700">
                            <option value="">Tous les statuts</option>
                            <option value="en_attente" @selected(request('statut') === 'en_attente')>En attente</option>
                            <option value="livree" @selected(request('statut') === 'livree')>Livree</option>
                            <option value="annulee" @selected(request('statut') === 'annulee')>Annulee</option>
                        </select>
                    </div>

                    <div>
                        <label for="filtre_fournisseur" class="block text-sm font-medium text-slate-700">Filtrer par fournisseur</label>
                        <select id="filtre_fournisseur" name="fournisseur_id" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-700">
                            <option value="">Tous les fournisseurs</option>
                            @foreach($fournisseurs as $fournisseur)
                                <option value="{{ $fournisseur->id }}" @selected((string) request('fournisseur_id') === (string) $fournisseur->id)>
                                    {{ $fournisseur->nom }} {{ $fournisseur->prenom }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="date_debut_commande" class="block text-sm font-medium text-slate-700">Date debut</label>
                        <input id="date_debut_commande" name="date_debut" type="date" value="{{ request('date_debut') }}" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-700">
                    </div>

                    <div>
                        <label for="date_fin_commande" class="block text-sm font-medium text-slate-700">Date fin</label>
                        <input id="date_fin_commande" name="date_fin" type="date" value="{{ request('date_fin') }}" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-700">
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <button type="submit" class="btn-primary">Filtrer</button>
                        @if(request('statut') || request('fournisseur_id') || request('search') || request('date_debut') || request('date_fin'))
                            <a href="{{ route('commandes.index') }}" class="btn-secondary">Reinitialiser</a>
                        @endif
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Reference</th>
                            <th>Article</th>
                            <th>Fournisseur</th>
                            <th>Statut</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($commandes as $commande)
                            <tr>
                                <td class="font-medium text-slate-900">{{ $commande->reference_commande }}</td>
                                <td>{{ $commande->article?->produit?->nom_produit ?? 'Article' }}</td>
                                <td>{{ $commande->fournisseur?->nom }} {{ $commande->fournisseur?->prenom }}</td>
                                <td>
                                    <form method="POST" action="{{ route('commandes.statut', $commande) }}" class="min-w-[10rem]">
                                        @csrf
                                        @method('PATCH')
                                        <select
                                            name="statut"
                                            onchange="this.form.submit()"
                                            class="w-full rounded-full border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700"
                                        >
                                            <option value="en_attente" @selected($commande->statut === 'en_attente')>En attente</option>
                                            <option value="livree" @selected($commande->statut === 'livree')>Livree</option>
                                            <option value="annulee" @selected($commande->statut === 'annulee')>Annulee</option>
                                        </select>
                                    </form>
                                </td>
                                <td>{{ number_format($commande->prix_total, 2) }} DH</td>
                                <td>
                                    <div class="flex flex-wrap gap-2">
                                        @if($commande->statut === 'en_attente')
                                            <form method="POST" action="{{ route('commandes.livrer', $commande) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn-secondary">Livrer</button>
                                            </form>
                                        @endif

                                        @if($commande->statut !== 'livree')
                                            <form method="POST" action="{{ route('commandes.destroy', $commande) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    type="submit"
                                                    onclick="return confirm('Voulez-vous vraiment supprimer cette commande ?')"
                                                    class="rounded-full border border-rose-200 bg-rose-50 px-4 py-2 text-sm font-medium text-rose-600"
                                                >
                                                    Supprimer
                                                </button>
                                            </form>
                                        @endif

                                        <a href="{{ route('commandes.show', $commande) }}" class="btn-secondary">
                                            Facture
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-sm text-slate-500">Aucune commande enregistree.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4">
                {{ $commandes->links() }}
            </div>
        </section>
    </div>
</x-app-layout>
