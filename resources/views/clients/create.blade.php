<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2">
            <h2 class="text-3xl font-semibold tracking-tight text-slate-950">Ajouter un client</h2>
            <p class="text-sm text-slate-500">Creez rapidement une fiche client avec ses coordonnees principales.</p>
        </div>
    </x-slot>

    <div class="mx-auto max-w-4xl">
        <div class="panel-soft p-6 sm:p-8">
            <form method="POST" action="{{ route('clients.store') }}" class="grid gap-6 md:grid-cols-2">
                @csrf

                <div>
                    <label for="nom" class="block text-sm font-medium text-slate-700">Nom</label>
                    <input id="nom" name="nom" type="text" value="{{ old('nom') }}" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3" required>
                    @error('nom') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="prenom" class="block text-sm font-medium text-slate-700">Prenom</label>
                    <input id="prenom" name="prenom" type="text" value="{{ old('prenom') }}" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3" required>
                    @error('prenom') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="telephone" class="block text-sm font-medium text-slate-700">Telephone</label>
                    <input id="telephone" name="telephone" type="text" value="{{ old('telephone') }}" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3">
                    @error('telephone') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3">
                    @error('email') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="adresse" class="block text-sm font-medium text-slate-700">Adresse</label>
                    <textarea id="adresse" name="adresse" rows="4" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3">{{ old('adresse') }}</textarea>
                    @error('adresse') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2 flex justify-end gap-3">
                    <a href="{{ route('clients.index') }}" class="btn-secondary">Annuler</a>
                    <button type="submit" class="btn-primary">Enregistrer le client</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
