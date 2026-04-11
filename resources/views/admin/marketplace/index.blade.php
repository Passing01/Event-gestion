@extends('layouts.dashboard')

@section('title', 'Gestion Marketplace')

@section('content')
<div class="dash-content">
    <div class="dash-header">
        <div>
            <h1 class="dash-title">Marketplace</h1>
            <p class="dash-subtitle">Gérez les événements publiés pour la vente.</p>
        </div>
    </div>

    <div class="card admin-table-card">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Événement</th>
                    <th>Prix demandé</th>
                    <th>Vendeur</th>
                    <th>Date de publication</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($events as $event)
                <tr>
                    <td>
                        <div style="font-weight: 600;">{{ $event->name }}</div>
                        <div style="font-size: 0.75rem;">Code: <code>{{ $event->code }}</code></div>
                    </td>
                    <td>
                        <span style="font-weight: 600; color: var(--brand);">{{ number_format($event->marketplace_price ?? 0, 0, ',', ' ') }} XOF</span>
                    </td>
                    <td>{{ $event->user?->name }}</td>
                    <td>{{ $event->updated_at->format('d/m/Y') }}</td>
                    <td style="text-align: right;">
                        <div style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                            <form action="{{ route('admin.marketplace.remove', $event->id) }}" method="POST" onsubmit="return confirm('Retirer cet événement du marketplace ?')">
                                @csrf
                                <button type="submit" class="btn btn-outline btn-sm" style="color: var(--destructive);">
                                    Retirer
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @if($events->hasPages())
        <div style="padding: 1rem; border-top: 1px solid var(--border);">
            {{ $events->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
