<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2">
            <h2 class="text-3xl font-semibold tracking-tight text-slate-950">Commandes</h2>
            <p class="text-sm text-slate-500">Creez une commande et suivez son statut de livraison depuis la meme interface.</p>
        </div>
    </x-slot>

    <div class="grid gap-6 xl:grid-cols-[0.92fr_1.08fr]">
        <section class="panel-soft p-6 sm:p-8">
            <div class="mb-6">
                <h3 class="section-title">Formulaire commande</h3>
                <p class="section-copy">Ajoutez une commande fournisseur avec toutes les informations de suivi.</p>
            </div>

            <form method="POST" action="{{ route('commandes.store') }}" class="grid gap-5 md:grid-cols-2">
                @csrf
                <div class="md:col-span-2">
                    <label for="article_id" class="block text-sm font-medium text-slate-700">Article</label>
                    <select id="article_id" name="article_id" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3" required>
                        <option value="">Choisir un article</option>
                        @foreach($articles as $article)
                            <option value="{{ $article->id }}" @selected(old('article_id') == $article->id)>
                                {{ $article->produit?->nom_produit ?? 'Article' }}
                            </option>
                        @endforeach
                    </select>
                    @error('article_id') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
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
                    <label for="reference_commande" class="block text-sm font-medium text-slate-700">Reference</label>
                    <input id="reference_commande" name="reference_commande" type="text" value="{{ old('reference_commande') }}" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3">
                    @error('reference_commande') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="statut" class="block text-sm font-medium text-slate-700">Statut initial</label>
                    <select id="statut" name="statut" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3" required>
                        <option value="en_attente" @selected(old('statut', 'en_attente') === 'en_attente')>En attente</option>
                        <option value="livree" @selected(old('statut') === 'livree')>Livree</option>
                        <option value="annulee" @selected(old('statut') === 'annulee')>Annulee</option>
                    </select>
                    @error('statut') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2 flex justify-end">
                    <button type="submit" class="btn-primary">Ajouter la commande</button>
                </div>
            </form>
        </section>

        <section class="space-y-6">
            <div class="grid gap-4 md:grid-cols-3">
                <div class="stat-card">
                    <p class="stat-kicker">En attente</p>
                    <p class="stat-value">{{ $stats['en_attente'] }}</p>
                    <p class="stat-copy">Commandes a suivre.</p>
                </div>
                <div class="stat-card">
                    <p class="stat-kicker">Livrees</p>
                    <p class="stat-value">{{ $stats['livree'] }}</p>
                    <p class="stat-copy">Commandes deja recues.</p>
                </div>
                <div class="stat-card">
                    <p class="stat-kicker">Annulees</p>
                    <p class="stat-value">{{ $stats['annulee'] }}</p>
                    <p class="stat-copy">Commandes stoppees.</p>
                </div>
            </div>

            <div class="panel-soft overflow-hidden">
                <div class="border-b border-slate-200 px-6 py-5">
                    <h3 class="section-title">Suivi des commandes</h3>
                    <p class="section-copy">Passez les commandes en livree, annulee ou en attente directement depuis le tableau.</p>
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
            </div>
        </div>
    </div>
</x-app-layout>
