<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

<div class="container-fluid py-4">

    {{-- ── Notifications non lues ── --}}
    @if($notifications->isNotEmpty())
    <div class="alert alert-warning">
        <strong>⚠️ Alertes :</strong>
        @foreach($notifications as $notif)
            <div>{{ $notif->data['message'] }}</div>
        @endforeach
    </div>
    @endif

    {{-- ── KPI Cards ── --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-primary text-white">
                <div class="card-body">
                    <h6>📦 Stock Total</h6>
                    <h2>{{ number_format($data['stock_total']) }}</h2>
                    <small>unités en stock actif</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-warning text-dark">
                <div class="card-body">
                    <h6>⚠️ Stock Bas</h6>
                    <h2>{{ $data['nb_stock_bas'] }}</h2>
                    <small>articles sous le seuil minimum</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-danger text-white">
                <div class="card-body">
                    <h6>🗓️ Articles Expirés</h6>
                    <h2>{{ $data['nb_expires'] }}</h2>
                    <small>à retirer du stock</small>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Ventes du jour ── --}}
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm bg-success text-white">
                <div class="card-body">
                    <h6>🛒 Ventes Aujourd'hui</h6>
                    <h2>{{ $data['ventes_jour'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm" style="background:#6f42c1; color:white">
                <div class="card-body">
                    <h6>💰 CA Aujourd'hui</h6>
                    <h2>{{ number_format($data['ca_jour'], 2) }} DH</h2>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Tableau Stock Bas ── --}}
    @if($articles_stock_bas->isNotEmpty())
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-warning text-dark fw-bold">⚠️ Articles en stock bas</div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead><tr>
                    <th>Produit</th><th>Quantité</th><th>Seuil</th><th>Action</th>
                </tr></thead>
                <tbody>
                @foreach($articles_stock_bas as $article)
                <tr>
                    <td>{{ $article->produit->nom_produit }}</td>
                    <td><span class="badge bg-danger">{{ $article->quantite }}</span></td>
                    <td>{{ $article->seuil_minimum }}</td>
                    <td><a href="{{ route('commandes.create') }}" class="btn btn-sm btn-outline-primary">Commander</a></td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- ── Tableau Articles Expirés ── --}}
    @if($articles_expires->isNotEmpty())
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-danger text-white fw-bold">🚫 Articles expirés</div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead><tr>
                    <th>Produit</th><th>Date expiration</th><th>Quantité restante</th>
                </tr></thead>
                <tbody>
                @foreach($articles_expires as $article)
                <tr>
                    <td>{{ $article->produit->nom_produit }}</td>
                    <td>{{ $article->date_expiration->format('d/m/Y') }}</td>
                    <td>{{ $article->quantite }}</td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>

</x-app-layout>
