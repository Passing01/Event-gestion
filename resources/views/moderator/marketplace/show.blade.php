@extends('layouts.dashboard')

@section('title', 'Détails des Ventes : ' . $event->name)

@section('content')
<div class="space-y-6" style="font-family: 'Inter', sans-serif;">
    <div class="page-header">
        <div>
            <a href="{{ route('dashboard.my-marketplace.index') }}" style="font-size: 0.875rem; color: var(--muted-foreground); text-decoration: none; display: inline-flex; align-items: center; gap: 0.25rem; font-weight: 600;">
                ← Retour au Marketplace
            </a>
            <h1 class="dash-title" style="margin-top: 0.5rem;">Ventes : {{ $event->name }}</h1>
            <p class="dash-subtitle">Consultez l'historique complet et la répartition des gains pour cet événement.</p>
        </div>
    </div>

    <!-- KPI Grid -->
    <div class="admin-stats-grid">
        <!-- Prix Public -->
        <div class="card" style="padding: 1.5rem; display: flex; align-items: center; gap: 1.5rem; border: 1px solid var(--border);">
            <div style="background: var(--brand-light); width: 3.5rem; height: 3.5rem; border-radius: 1rem; display: grid; place-items: center; font-size: 1.75rem;">
                🏷️
            </div>
            <div>
                <span style="font-size: 0.875rem; color: var(--muted-foreground); display: block; font-weight: 500;">Prix Unitaire</span>
                <span style="font-size: 1.75rem; font-weight: 800; color: var(--foreground); display: block; line-height: 1;">{{ number_format($price, 0, ',', ' ') }} XOF</span>
            </div>
        </div>

        <!-- Ventes -->
        <div class="card" style="padding: 1.5rem; display: flex; align-items: center; gap: 1.5rem; border: 1px solid var(--border);">
            <div style="background: var(--brand-light); width: 3.5rem; height: 3.5rem; border-radius: 1rem; display: grid; place-items: center; font-size: 1.75rem;">
                👥
            </div>
            <div>
                <span style="font-size: 0.875rem; color: var(--muted-foreground); display: block; font-weight: 500;">Licences Vendues</span>
                <span style="font-size: 1.75rem; font-weight: 800; color: var(--foreground); display: block; line-height: 1;">{{ $salesCount }}</span>
            </div>
        </div>

        <!-- Chiffre d'affaires brut -->
        <div class="card" style="padding: 1.5rem; display: flex; align-items: center; gap: 1.5rem; border: 1px solid var(--border);">
            <div style="background: var(--brand-light); width: 3.5rem; height: 3.5rem; border-radius: 1rem; display: grid; place-items: center; font-size: 1.75rem;">
                📊
            </div>
            <div>
                <span style="font-size: 0.875rem; color: var(--muted-foreground); display: block; font-weight: 500;">Revenu Brut</span>
                <span style="font-size: 1.75rem; font-weight: 800; color: var(--foreground); display: block; line-height: 1;">{{ number_format($eventRevenue, 0, ',', ' ') }} XOF</span>
            </div>
        </div>

        <!-- Ma part -->
        <div class="card" style="padding: 1.5rem; display: flex; align-items: center; gap: 1.5rem; border: 1px solid var(--border); background: var(--brand-light);">
            <div style="background: #22c55e; width: 3.5rem; height: 3.5rem; border-radius: 1rem; display: grid; place-items: center; font-size: 1.75rem; color: #fff;">
                ✓
            </div>
            <div>
                <span style="font-size: 0.875rem; color: var(--brand); display: block; font-weight: 600;">Mes Gains ({{ $plan === 'premium' ? '75%' : '50%' }})</span>
                <span style="font-size: 1.75rem; font-weight: 800; color: var(--brand); display: block; line-height: 1;">{{ number_format($eventModeratorShare, 0, ',', ' ') }} XOF</span>
            </div>
        </div>
    </div>

    <!-- Main Section with split layout -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; align-items: flex-start;">
        <!-- Buyers Table -->
        <div class="card admin-table-card" style="padding: 1.5rem; border: 1px solid var(--border);">
            <h2 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 1.5rem; color: var(--foreground);">Historique des Acheteurs</h2>
            
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Acheteur</th>
                        <th>Email</th>
                        <th>Date d'Achat</th>
                        <th style="text-align: right;">Montant</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($buyers as $b)
                    <tr>
                        <td style="font-weight: 700; color: var(--foreground);">{{ $b['name'] }}</td>
                        <td>{{ $b['email'] }}</td>
                        <td>{{ $b['date']->format('d/m/Y H:i') }}</td>
                        <td style="text-align: right; font-weight: 700; color: #22c55e;">
                            {{ number_format($b['amount'], 0, ',', ' ') }} XOF
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Sidebar Actions & Details -->
        <div class="space-y-6">
            <!-- Event Replay View Card -->
            <div class="card" style="padding: 1.5rem; border: 1px solid var(--border); text-align: center;">
                <div style="background: rgba(124, 58, 237, 0.1); width: 4.5rem; height: 4.5rem; border-radius: 50%; display: grid; place-items: center; font-size: 2.25rem; margin: 0 auto 1.5rem;">
                    🎬
                </div>
                <h3 style="font-size: 1.125rem; font-weight: 700; color: var(--foreground); margin-bottom: 0.5rem;">Replay Interactif</h3>
                <p style="color: var(--muted-foreground); font-size: 0.875rem; margin-bottom: 1.5rem; line-height: 1.5;">
                    Accédez directement à l'écran de visionnage du replay interactif avec la vidéo réelle, les audios et les questions de votre événement.
                </p>
                <a href="{{ route('marketplace.replay', $event->id) }}" class="btn btn-primary" style="width: 100%; display: flex; justify-content: center; font-weight: 700;">
                    Visionner le Replay
                </a>
            </div>

            <!-- Share & Commission Card -->
            <div class="card" style="padding: 1.5rem; border: 1px solid var(--border);">
                <h3 style="font-size: 1.125rem; font-weight: 700; color: var(--foreground); margin-bottom: 1rem;">Répartition de la Commission</h3>
                <div style="display: grid; gap: 1rem; font-size: 0.875rem;">
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--muted-foreground);">Commission Plateforme :</span>
                        <strong style="color: var(--foreground);">{{ $plan === 'premium' ? '25%' : '50%' }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--muted-foreground);">Votre Part :</span>
                        <strong style="color: #22c55e;">{{ $plan === 'premium' ? '75%' : '50%' }}</strong>
                    </div>
                    <div style="border-top: 1px solid var(--border); padding-top: 1rem; display: flex; justify-content: space-between;">
                        <span style="color: var(--muted-foreground);">Cumul Plateforme :</span>
                        <strong style="color: var(--foreground);">{{ number_format($eventPlatformShare, 0, ',', ' ') }} XOF</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--brand); font-weight: 600;">Cumul Modérateur :</span>
                        <strong style="color: #22c55e;">{{ number_format($eventModeratorShare, 0, ',', ' ') }} XOF</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
