<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2">
            <h2 class="text-3xl font-semibold tracking-tight text-slate-950">Nouvelle commande</h2>
            <p class="text-sm text-slate-500">Le formulaire commande et le suivi sont centralises dans la page commandes.</p>
        </div>
    </x-slot>

    <div class="mx-auto max-w-3xl">
        <div class="panel-soft p-8 text-center">
            <h3 class="section-title">Passez par la page Commandes</h3>
            <p class="mt-3 text-sm text-slate-500">Vous y trouverez le formulaire et le bloc de suivi des statuts.</p>
            <div class="mt-6">
                <a href="{{ route('commandes.index') }}" class="btn-primary">Ouvrir la page commandes</a>
            </div>
        </div>
    </div>
</x-app-layout>
