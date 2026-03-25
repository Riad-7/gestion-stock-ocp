<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2">
            <h2 class="text-3xl font-semibold tracking-tight text-slate-950">Fournisseurs</h2>
            <p class="text-sm text-slate-500">Ajoutez et consultez les fournisseurs depuis une seule page.</p>
        </div>
    </x-slot>

    <div class="grid gap-6 xl:grid-cols-[0.95fr_1.05fr]">
        <section class="panel-soft p-6 sm:p-8">
            <div class="mb-6">
                <h3 class="section-title">Formulaire fournisseur</h3>
                <p class="section-copy">Remplissez les informations du fournisseur et enregistrez-les directement.</p>
            </div>

            @if(session('success'))
                <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('fournisseurs.store') }}" class="grid gap-5 md:grid-cols-2">
                @csrf
                <div>
                    <label for="nom" class="block text-sm font-medium text-slate-700">Nom</label>
                    <input id="nom" name="nom" type="text" value="{{ old('nom') }}" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3" required>
                    @error('nom') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="prenom" class="block text-sm font-medium text-slate-700">Prenom</label>
                    <input id="prenom" name="prenom" type="text" value="{{ old('prenom') }}" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3">
                    @error('prenom') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="entreprise" class="block text-sm font-medium text-slate-700">Entreprise</label>
                    <input id="entreprise" name="entreprise" type="text" value="{{ old('entreprise') }}" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3">
                    @error('entreprise') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="telephone" class="block text-sm font-medium text-slate-700">Telephone</label>
                    <input id="telephone" name="telephone" type="text" value="{{ old('telephone') }}" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3">
                    @error('telephone') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div class="md:col-span-2">
                    <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3">
                    @error('email') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div class="md:col-span-2">
                    <label for="adresse" class="block text-sm font-medium text-slate-700">Adresse</label>
                    <textarea id="adresse" name="adresse" rows="4" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3">{{ old('adresse') }}</textarea>
                    @error('adresse') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div class="md:col-span-2 flex justify-end">
                    <button type="submit" class="btn-primary">Ajouter le fournisseur</button>
                </div>
            </form>
        </section>

        <section class="panel-soft overflow-hidden">
            <div class="border-b border-slate-200 px-6 py-5">
                <h3 class="section-title">Liste des fournisseurs</h3>
                <p class="section-copy">Les fournisseurs ajoutes s'affichent ici apres enregistrement.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Entreprise</th>
                            <th>Telephone</th>
                            <th>Email</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($fournisseurs as $fournisseur)
                            <tr>
                                <td class="font-medium text-slate-900">{{ $fournisseur->nom }} {{ $fournisseur->prenom }}</td>
                                <td>{{ $fournisseur->entreprise ?: '-' }}</td>
                                <td>{{ $fournisseur->telephone ?: '-' }}</td>
                                <td>{{ $fournisseur->email ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-sm text-slate-500">Aucun fournisseur enregistre.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4">
                {{ $fournisseurs->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
