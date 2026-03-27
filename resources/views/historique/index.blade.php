<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2">
            <h2 class="text-3xl font-semibold tracking-tight text-slate-950">Historique des actions</h2>
            <p class="text-sm text-slate-500">Suivez les operations recentes et lancez un backup de la base de donnees.</p>
        </div>
    </x-slot>

    <div class="grid gap-6">
        <section class="panel-soft overflow-hidden">
            <div class="border-b border-slate-200 px-6 py-5">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div>
                        <h3 class="section-title">Journal systeme</h3>
                        <p class="section-copy">Toutes les actions importantes sont centralisees ici.</p>
                    </div>

                    <form method="POST" action="{{ route('historique.backup') }}">
                        @csrf
                        <button type="submit" class="btn-primary">Lancer un backup</button>
                    </form>
                </div>

                <form method="GET" action="{{ route('historique.index') }}" class="mt-4 grid gap-3 lg:grid-cols-[minmax(0,1.3fr)_minmax(0,1fr)_minmax(0,1fr)_minmax(0,1fr)_minmax(0,1fr)_auto] lg:items-end">
                    <div>
                        <label for="search_logs" class="block text-sm font-medium text-slate-700">Recherche</label>
                        <input id="search_logs" name="search" type="text" value="{{ request('search') }}" placeholder="Description de l action" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-700">
                    </div>

                    <div>
                        <label for="action_logs" class="block text-sm font-medium text-slate-700">Action</label>
                        <select id="action_logs" name="action" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-700">
                            <option value="">Toutes</option>
                            @foreach($actions as $action)
                                <option value="{{ $action }}" @selected(request('action') === $action)>{{ $action }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="target_logs" class="block text-sm font-medium text-slate-700">Cible</label>
                        <select id="target_logs" name="target_type" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-700">
                            <option value="">Toutes</option>
                            @foreach($targetTypes as $targetType)
                                <option value="{{ $targetType }}" @selected(request('target_type') === $targetType)>{{ $targetType }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="date_debut_logs" class="block text-sm font-medium text-slate-700">Date debut</label>
                        <input id="date_debut_logs" name="date_debut" type="date" value="{{ request('date_debut') }}" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-700">
                    </div>

                    <div>
                        <label for="date_fin_logs" class="block text-sm font-medium text-slate-700">Date fin</label>
                        <input id="date_fin_logs" name="date_fin" type="date" value="{{ request('date_fin') }}" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-700">
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <button type="submit" class="btn-primary">Filtrer</button>
                        @if(request('search') || request('action') || request('target_type') || request('date_debut') || request('date_fin'))
                            <a href="{{ route('historique.index') }}" class="btn-secondary">Reinitialiser</a>
                        @endif
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Utilisateur</th>
                            <th>Action</th>
                            <th>Cible</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td>{{ $log->created_at?->format('d/m/Y H:i') }}</td>
                                <td>{{ $log->user?->name ?? 'Systeme' }}</td>
                                <td>
                                    <span class="badge-soft badge-info">{{ $log->action }}</span>
                                </td>
                                <td>
                                    {{ $log->target_type ?? '--' }}
                                    @if($log->target_id)
                                        #{{ $log->target_id }}
                                    @endif
                                </td>
                                <td>
                                    <div class="font-medium text-slate-900">{{ $log->description }}</div>
                                    @if($log->ip_address)
                                        <div class="mt-1 text-xs text-slate-500">IP: {{ $log->ip_address }}</div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-sm text-slate-500">Aucun historique disponible.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4">
                {{ $logs->links() }}
            </div>
        </section>
    </div>
</x-app-layout>
