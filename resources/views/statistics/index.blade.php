@extends('layouts.dashboard')

@section('title', 'Statistiques Globales')

@section('content')

<div class="space-y-5">
    <div class="page-header">
        <h1>Statistiques Globales</h1>
        <p>Analysez l'engagement de votre audience sur l'ensemble de vos événements.</p>
    </div>

    {{-- ---- Cartes de Statistiques ---- --}}
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem;">
        <div class="card">
            <h3 style="font-size: 0.75rem; font-weight: 600; color: var(--muted-foreground); text-transform: uppercase;">Événements</h3>
            <p style="font-size: 2rem; font-weight: 700; margin-top: 0.5rem;">{{ $totalEvents }}</p>
        </div>
        <div class="card">
            <h3 style="font-size: 0.75rem; font-weight: 600; color: var(--muted-foreground); text-transform: uppercase;">Questions</h3>
            <p style="font-size: 2rem; font-weight: 700; margin-top: 0.5rem;">{{ $totalQuestions }}</p>
        </div>
        <div class="card">
            <h3 style="font-size: 0.75rem; font-weight: 600; color: var(--muted-foreground); text-transform: uppercase;">Participants (est.)</h3>
            <p style="font-size: 2rem; font-weight: 700; margin-top: 0.5rem;">{{ $totalParticipants }}</p>
        </div>
        <div class="card">
            <h3 style="font-size: 0.75rem; font-weight: 600; color: var(--muted-foreground); text-transform: uppercase;">Engagement (Votes)</h3>
            <p style="font-size: 2rem; font-weight: 700; margin-top: 0.5rem;">{{ $totalVotes }}</p>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
        
        {{-- ---- Graphique Questions par Événement ---- --}}
        <section class="card">
            <h2 class="section-title">Questions par Événement</h2>
            <div style="display: flex; align-items: flex-end; justify-content: space-between; height: 200px; padding-top: 2rem; margin-top: 1rem;">
                @forelse($chartData as $data)
                <div style="width: 12%; text-align: center;">
                    <div style="background: var(--brand); height: {{ $totalQuestions > 0 ? ($data['count'] / $totalQuestions) * 200 : 0 }}px; border-radius: 0.5rem 0.5rem 0 0; position: relative; transition: height 0.5s;">
                        <span style="position: absolute; top: -1.5rem; left: 50%; transform: translateX(-50%); font-size: 0.75rem; font-weight: 600;">{{ $data['count'] }}</span>
                    </div>
                    <p style="font-size: 0.625rem; color: var(--muted-foreground); margin-top: 0.5rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $data['name'] }}">
                        {{ $data['name'] }}
                    </p>
                </div>
                @empty
                <div style="width: 100%; text-align: center; color: var(--muted-foreground);">Aucune donnée disponible.</div>
                @endforelse
            </div>
        </section>

        {{-- ---- Répartition de l'Engagement ---- --}}
        <section class="card">
            <h2 class="section-title">Répartition</h2>
            <div style="margin-top: 1.5rem; display: grid; gap: 1.25rem;">
                <div>
                    <div style="display: flex; justify-content: space-between; font-size: 0.875rem; margin-bottom: 0.5rem;">
                        <span>Questions répondues</span>
                        <span style="font-weight: 600;">{{ $totalReplies }}</span>
                    </div>
                    <div style="height: 0.5rem; background: var(--muted); border-radius: 9999px; overflow: hidden;">
                        <div style="height: 100%; width: {{ $totalQuestions > 0 ? ($totalReplies / $totalQuestions) * 100 : 0 }}%; background: var(--brand);"></div>
                    </div>
                </div>
                <div>
                    <div style="display: flex; justify-content: space-between; font-size: 0.875rem; margin-bottom: 0.5rem;">
                        <span>Taux de vote (Votes/Question)</span>
                        <span style="font-weight: 600;">{{ $totalQuestions > 0 ? number_format($totalVotes / $totalQuestions, 1) : 0 }}</span>
                    </div>
                    <div style="height: 0.5rem; background: var(--muted); border-radius: 9999px; overflow: hidden;">
                        <div style="height: 100%; width: {{ $totalQuestions > 0 ? min(($totalVotes / $totalQuestions) * 20, 100) : 0 }}%; background: #0d9488;"></div>
                    </div>
                </div>
            </div>
        </section>

    </div>
</div>

@endsection
