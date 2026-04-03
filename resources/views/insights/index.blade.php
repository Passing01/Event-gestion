@extends('layouts.dashboard')

@section('title', 'IA Insights & Rapports')

@section('content')

<div class="space-y-5">
    <div class="page-header">
        <h1>IA Insights & Rapports</h1>
        <p>Analysez vos événements passés grâce à l'intelligence artificielle.</p>
    </div>

    <div class="card">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="border-bottom: 1px solid var(--border);">
                        <th style="padding: 1rem; font-size: 0.75rem; font-weight: 600; color: var(--muted-foreground); text-transform: uppercase;">Événement</th>
                        <th style="padding: 1rem; font-size: 0.75rem; font-weight: 600; color: var(--muted-foreground); text-transform: uppercase;">Questions</th>
                        <th style="padding: 1rem; font-size: 0.75rem; font-weight: 600; color: var(--muted-foreground); text-transform: uppercase;">Date</th>
                        <th style="padding: 1rem; font-size: 0.75rem; font-weight: 600; color: var(--muted-foreground); text-transform: uppercase; text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($events as $event)
                    <tr style="border-bottom: 1px solid var(--border);">
                        <td style="padding: 1rem;">
                            <span style="font-weight: 600; color: var(--foreground);">{{ $event->name }}</span>
                        </td>
                        <td style="padding: 1rem;">
                            <span class="badge" style="background: var(--muted); color: var(--foreground);">{{ $event->questions_count }} questions</span>
                        </td>
                        <td style="padding: 1rem; font-size: 0.875rem;">{{ $event->date->format('d/m/Y') }}</td>
                        <td style="padding: 1rem; text-align: right;">
                            <div style="display: flex; gap: 0.5rem; justify-content: flex-end; align-items: center;">
                                @if($event->closed_at)
                                    <a href="{{ route('dashboard.insights.show', $event->id) }}" class="btn-brand" style="padding: 0.375rem 0.75rem; font-size: 0.75rem;">Analyse IA ↗</a>
                                    <a href="{{ route('dashboard.insights.export', $event->id) }}" target="_blank" style="padding: 0.375rem; color: var(--muted-foreground);" title="Exporter PDF">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 1rem; height: 1rem;">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M7.5 12L12 16.5m0 0L16.5 12M12 16.5V3" />
                                        </svg>
                                    </a>
                                @else
                                    <span style="font-size: 0.65rem; background: var(--muted); padding: 0.35rem 0.6rem; border-radius: 0.5rem; color: var(--muted-foreground); font-weight: 700; display: flex; align-items: center; gap: 0.35rem;">
                                        🔒 Clôture requise
                                    </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="padding: 3rem; text-align: center; color: var(--muted-foreground);">
                            Aucun événement disponible pour analyse.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
