@extends('layouts.dashboard')

@section('title', 'Gestion Marketplace')

@section('content')
<div class="dash-content">
    <div class="dash-header">
        <div>
            <h1 class="dash-title">Marketplace</h1>
            <p class="dash-subtitle">Gérez les événements publiés sur le marketplace.</p>
        </div>
    </div>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>Événement</th>
                <th>Organisateur</th>
                <th>Prix</th>
                <th>Date Publication</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($events as $event)
            <tr>
                <td>{{ $event->name }}</td>
                <td>{{ $event->user?->name }}</td>
                <td>{{ $event->marketplace_price ?? 0 }} XOF</td>
                <td>{{ $event->updated_at->format('d/m/Y') }}</td>
                <td>
                    <form action="{{ route('admin.marketplace.destroy', $event->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline btn-sm" style="color: #ef4444;">
                            <i class="fas fa-times"></i> Retirer du Marketplace
                        </button>
                    </form>
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
