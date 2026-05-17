@extends('layouts.dashboard')

@section('title', 'Mes Ventes Marketplace')

@section('content')
<div class="space-y-6" style="font-family: 'Inter', sans-serif;">
    <div class="dash-header">
        <div>
            <h1 class="dash-title">Mon Espace Ventes Marketplace</h1>
            <p class="dash-subtitle">Gérez et suivez les ventes de vos replays d'événements.</p>
        </div>
        <div class="badge" style="background: var(--brand); color: #fff; padding: 0.5rem 1rem; font-weight: 700; border-radius: 0.5rem;">
            Plan {{ ucfirst($plan) }} ({{ $plan === 'premium' ? '75%' : '50%' }} de gains)
        </div>
    </div>

    <!-- KPI Grid -->
    <div class="admin-stats-grid">
        <!-- Ventes Totales -->
        <div class="card" style="padding: 1.5rem; display: flex; align-items: center; gap: 1.5rem; border: 1px solid var(--border);">
            <div style="background: var(--brand-light); width: 3.5rem; height: 3.5rem; border-radius: 1rem; display: grid; place-items: center; font-size: 1.75rem;">
                📈
            </div>
            <div>
                <span style="font-size: 0.875rem; color: var(--muted-foreground); display: block; font-weight: 500;">Ventes Totales</span>
                <span style="font-size: 1.75rem; font-weight: 800; color: var(--foreground); display: block; line-height: 1;">{{ $totalSales }}</span>
            </div>
        </div>

        <!-- Revenu Global -->
        <div class="card" style="padding: 1.5rem; display: flex; align-items: center; gap: 1.5rem; border: 1px solid var(--border);">
            <div style="background: var(--brand-light); width: 3.5rem; height: 3.5rem; border-radius: 1rem; display: grid; place-items: center; font-size: 1.75rem;">
                💰
            </div>
            <div>
                <span style="font-size: 0.875rem; color: var(--muted-foreground); display: block; font-weight: 500;">Chiffre d'Affaires</span>
                <span style="font-size: 1.75rem; font-weight: 800; color: var(--foreground); display: block; line-height: 1;">{{ number_format($totalRevenue, 0, ',', ' ') }} XOF</span>
            </div>
        </div>

        <!-- Ma part -->
        <div class="card" style="padding: 1.5rem; display: flex; align-items: center; gap: 1.5rem; border: 1px solid var(--border); background: var(--brand-light);">
            <div style="background: #22c55e; width: 3.5rem; height: 3.5rem; border-radius: 1rem; display: grid; place-items: center; font-size: 1.75rem; color: #fff;">
                ✓
            </div>
            <div>
                <span style="font-size: 0.875rem; color: var(--brand); display: block; font-weight: 600;">Mes Gains ({{ $plan === 'premium' ? '75%' : '50%' }})</span>
                <span style="font-size: 1.75rem; font-weight: 800; color: var(--brand); display: block; line-height: 1;">{{ number_format($moderatorShare, 0, ',', ' ') }} XOF</span>
            </div>
        </div>

        <!-- Part Plateforme -->
        <div class="card" style="padding: 1.5rem; display: flex; align-items: center; gap: 1.5rem; border: 1px solid var(--border);">
            <div style="background: rgba(239, 68, 68, 0.1); width: 3.5rem; height: 3.5rem; border-radius: 1rem; display: grid; place-items: center; font-size: 1.75rem; color: #ef4444;">
                %
            </div>
            <div>
                <span style="font-size: 0.875rem; color: var(--muted-foreground); display: block; font-weight: 500;">Frais Plateforme ({{ $plan === 'premium' ? '25%' : '50%' }})</span>
                <span style="font-size: 1.75rem; font-weight: 800; color: var(--foreground); display: block; line-height: 1;">{{ number_format($platformShare, 0, ',', ' ') }} XOF</span>
            </div>
        </div>
    </div>

    <!-- Table of Events -->
    <div class="card admin-table-card" style="padding: 1.5rem; border: 1px solid var(--border);">
        <h2 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 1.5rem; color: var(--foreground);">Replays et Ventes par Événement</h2>
        
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Événement</th>
                    <th>Date Clôture</th>
                    <th>Prix Public</th>
                    <th>Ventes</th>
                    <th>Revenu Brut</th>
                    <th>Mes Gains</th>
                    <th>Marketplace</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($events as $e)
                <tr>
                    <td style="font-weight: 700; color: var(--foreground);">
                        {{ $e->name }}
                        @if($e->replay_path)
                            <span class="badge badge-success" style="font-size: 0.6rem; padding: 0.1rem 0.4rem; margin-left: 0.5rem; font-weight: 900;">📹 VIDÉO</span>
                        @endif
                    </td>
                    <td>{{ $e->closed_at ? $e->closed_at->format('d/m/Y H:i') : '-' }}</td>
                    <td style="font-weight: 600;">
                        @if($e->is_on_marketplace)
                            {{ number_format($e->simulated_price, 0, ',', ' ') }} XOF
                        @else
                            -
                        @endif
                    </td>
                    <td style="font-weight: 700;">{{ $e->is_on_marketplace ? $e->simulated_sales : '0' }}</td>
                    <td style="font-weight: 600;">
                        {{ $e->is_on_marketplace ? number_format($e->simulated_revenue, 0, ',', ' ') . ' XOF' : '-' }}
                    </td>
                    <td style="font-weight: 700; color: #22c55e;">
                        {{ $e->is_on_marketplace ? number_format($e->simulated_revenue * ($plan === 'premium' ? 0.75 : 0.50), 0, ',', ' ') . ' XOF' : '-' }}
                    </td>
                    <td>
                        <form action="{{ route('dashboard.insights.toggle-marketplace', $e->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="badge {{ $e->is_on_marketplace ? 'badge-success' : 'badge-error' }}" style="border: none; cursor: pointer; font-weight: 700;">
                                {{ $e->is_on_marketplace ? 'PUBLIÉ' : 'BROUILLON' }}
                            </button>
                        </form>
                    </td>
                    <td style="text-align: right;">
                        <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                            @if($e->is_on_marketplace)
                            <a href="{{ route('dashboard.my-marketplace.show', $e->id) }}" class="btn btn-outline btn-sm" style="font-weight: 700; display: inline-flex; align-items: center; gap: 0.25rem;">
                                📊 Ventes
                            </a>
                            @endif
                            <a href="{{ route('marketplace.replay', $e->id) }}" class="btn btn-primary btn-sm" style="font-weight: 700; display: inline-flex; align-items: center; gap: 0.25rem;">
                                🎬 Visionner
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align: center; color: var(--muted-foreground); padding: 3rem 1rem;">
                        <span style="font-size: 2.5rem; display: block; margin-bottom: 1rem;">📭</span>
                        Aucun événement clôturé disponible. Vos replays s'afficheront ici après la clôture de vos sessions en direct.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
