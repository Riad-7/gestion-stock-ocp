<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2">
            <h2 class="text-3xl font-semibold tracking-tight text-slate-950">Nouvelle vente</h2>
            <p class="text-sm text-slate-500">Le formulaire de vente est disponible directement dans la page ventes.</p>
        </div>
    </x-slot>

    <div class="mx-auto max-w-3xl">
        <div class="panel-soft p-8 text-center">
            <h3 class="section-title">Passez par la page Ventes</h3>
            <p class="mt-3 text-sm text-slate-500">Le formulaire complet et l'historique sont regroupes dans une seule interface.</p>
            <div class="mt-6">
                <a href="{{ route('ventes.index') }}" class="btn-primary">Ouvrir la page ventes</a>
            </div>
        </div>
    </div>
</x-app-layout>
