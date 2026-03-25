<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2">
            <h2 class="text-3xl font-semibold tracking-tight text-slate-950">Ventes</h2>
            <p class="text-sm text-slate-500">Enregistrez une vente et suivez l'historique des transactions depuis la meme page.</p>
        </div>
    </x-slot>

    <div class="grid gap-6 xl:grid-cols-[0.95fr_1.05fr]">
        <section class="panel-soft p-6 sm:p-8">
            <div class="mb-6">
                <h3 class="section-title">Formulaire vente</h3>
                <p class="section-copy">Choisissez l'article, le client et le mode de paiement pour enregistrer la vente.</p>
            </div>

            <form method="POST" action="{{ route('ventes.store') }}" class="grid gap-5 md:grid-cols-2">
                @csrf
                <div class="md:col-span-2">
                    <label for="article_id" class="block text-sm font-medium text-slate-700">Article</label>
                    <select id="article_id" name="article_id" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3" required>
                        <option value="">Choisir un article</option>
                        @foreach($articles as $article)
                            <option value="{{ $article->id }}" @selected(old('article_id') == $article->id)>
                                {{ $article->produit?->nom_produit ?? 'Article' }} - Stock: {{ $article->quantite }}
                            </option>
                        @endforeach
                    </select>
                    @error('article_id') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="client_id" class="block text-sm font-medium text-slate-700">Client</label>
                    <select id="client_id" name="client_id" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3" required>
                        <option value="">Choisir un client</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" @selected(old('client_id') == $client->id)>
                                {{ $client->nom }} {{ $client->prenom }}
                            </option>
                        @endforeach
                    </select>
                    @error('client_id') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="quantite" class="block text-sm font-medium text-slate-700">Quantite</label>
                    <input id="quantite" name="quantite" type="number" min="1" value="{{ old('quantite', 1) }}" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3" required>
                    @error('quantite') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="mode_paiement" class="block text-sm font-medium text-slate-700">Mode de paiement</label>
                    <select id="mode_paiement" name="mode_paiement" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3" required>
                        <option value="especes" @selected(old('mode_paiement', 'especes') === 'especes')>Especes</option>
                        <option value="carte" @selected(old('mode_paiement') === 'carte')>Carte</option>
                        <option value="cheque" @selected(old('mode_paiement') === 'cheque')>Cheque</option>
                        <option value="virement" @selected(old('mode_paiement') === 'virement')>Virement</option>
                    </select>
                    @error('mode_paiement') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2 flex justify-end">
                    <button type="submit" class="btn-primary">Ajouter la vente</button>
                </div>
            </form>
        </section>

        <section class="panel-soft overflow-hidden">
            <div class="border-b border-slate-200 px-6 py-5">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="section-title">Historique des ventes</h3>
                        <p class="section-copy">Toutes les ventes enregistrees apparaissent ici.</p>
                    </div>
                    <div class="flex gap-3 text-sm">
                        <span class="badge-soft badge-info">CA total: {{ number_format($totalCA, 2) }} DH</span>
                        <span class="badge-soft badge-warn">Jour: {{ number_format($ventesJour, 2) }} DH</span>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Article</th>
                            <th>Client</th>
                            <th>Quantite</th>
                            <th>Total</th>
                            <th>Paiement</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ventes as $vente)
                            <tr>
                                <td class="font-medium text-slate-900">{{ $vente->article?->produit?->nom_produit ?? 'Article' }}</td>
                                <td>{{ $vente->client?->nom }} {{ $vente->client?->prenom }}</td>
                                <td>{{ $vente->quantite }}</td>
                                <td>{{ number_format($vente->prix_total, 2) }} DH</td>
                                <td>{{ ucfirst($vente->mode_paiement) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-sm text-slate-500">Aucune vente enregistree.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4">
                {{ $ventes->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
