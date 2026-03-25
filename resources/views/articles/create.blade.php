<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2">
            <h2 class="text-3xl font-semibold tracking-tight text-slate-950">Ajouter un article</h2>
            <p class="text-sm text-slate-500">Remplissez le formulaire du lot/article avec le produit, le stock et le statut.</p>
        </div>
    </x-slot>

    <div class="mx-auto max-w-5xl">
        <div class="panel-soft p-6 sm:p-8">
            <form method="POST" action="{{ route('articles.store') }}" class="grid gap-6 md:grid-cols-2">
                @csrf

                <div class="md:col-span-2">
                    <label for="produit_id" class="block text-sm font-medium text-slate-700">Produit</label>
                    <select id="produit_id" name="produit_id" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3" required>
                        <option value="">Choisir un produit</option>
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

                <div class="md:col-span-2 flex justify-end gap-3">
                    <a href="{{ route('articles.index') }}" class="btn-secondary">Annuler</a>
                    <button type="submit" class="btn-primary">Enregistrer l'article</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
