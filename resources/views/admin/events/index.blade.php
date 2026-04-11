@extends('layouts.dashboard')

@section('title', 'Gestion des Événements')

@section('content')
<div class="dash-content">
    <div class="dash-header">
        <div>
            <h1 class="dash-title">Événements</h1>
            <p class="dash-subtitle">Surveillez tous les événements de la plateforme.</p>
        </div>
    </div>

<div class="card">
    <div style="margin-bottom: 2rem;">
        <form action="{{ route('admin.events.index') }}" method="GET" style="display: flex; gap: 1rem;">
            <input type="text" name="search" placeholder="Rechercher un événement..." value="{{ request('search') }}" style="max-width: 400px;">
            <button type="submit" class="btn btn-primary">Rechercher</button>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>Événement</th>
                <th>Organisateur</th>
                <th>Code</th>
                <th>Date</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($events as $event)
            <tr>
                <td>
                    <div style="font-weight: 600;">{{ $event->name }}</div>
                </td>
                <td>{{ $event->user?->name }}</td>
                <td><code style="background: var(--glass); padding: 0.25rem 0.5rem; border-radius: 4px;">{{ $event->code }}</code></td>
                <td>{{ $event->created_at->format('d/m/Y') }}</td>
                <td>
                    <span class="badge {{ $event->status === 'active' ? 'badge-success' : 'badge-error' }}">
                        {{ ucfirst($event->status) }}
                    </span>
                </td>
                <td>
                    <div style="display: flex; gap: 0.5rem;">
                        <a href="{{ route('admin.events.show', $event->id) }}" class="btn btn-outline btn-sm">
                            <i class="fas fa-eye"></i> Détails
                        </a>
                        <form action="{{ route('admin.events.destroy', $event->id) }}" method="POST" onsubmit="return confirm('Détruire cet événement ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline btn-sm" style="color: #ef4444;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div style="margin-top: 1rem;">
        {{ $events->links() }}
    </div>
</div>
</div>
@endsection
