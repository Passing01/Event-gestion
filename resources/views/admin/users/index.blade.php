@extends('layouts.dashboard')

@section('title', 'Gestion des Utilisateurs')

@section('content')
<div class="dash-content">
    <div class="dash-header">
        <div>
            <h1 class="dash-title">Utilisateurs</h1>
            <p class="dash-subtitle">Gérez les modérateurs et les participants.</p>
        </div>
    </div>

<div class="card">
    <div style="margin-bottom: 2rem;">
        <form action="{{ route('admin.users.index') }}" method="GET" style="display: flex; gap: 1rem;">
            <input type="text" name="search" placeholder="Rechercher par nom ou email..." value="{{ request('search') }}" style="max-width: 400px;">
            <button type="submit" class="btn btn-primary">Rechercher</button>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>Utilisateur</th>
                <th>Rôle</th>
                <th>Statut</th>
                <th>Plan</th>
                <th>Événements</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td>
                    <div style="display: flex; flex-direction: column;">
                        <span style="font-weight: 600;">{{ $user->name }}</span>
                        <span style="font-size: 0.875rem; color: #94a3b8;">{{ $user->email }}</span>
                    </div>
                </td>
                <td><span class="badge badge-info">{{ ucfirst($user->role) }}</span></td>
                <td>
                    <span class="badge {{ $user->is_active ? 'badge-success' : 'badge-error' }}">
                        {{ $user->is_active ? 'Actif' : 'Bloqué' }}
                    </span>
                </td>
                <td>{{ ucfirst($user->plan ?? 'Gratuit') }}</td>
                <td>{{ $user->events()->count() }}</td>
                <td>
                    <div style="display: flex; gap: 0.5rem;">
                        <form action="{{ route('admin.users.toggle', $user->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline btn-sm" title="{{ $user->is_active ? 'Désactiver' : 'Activer' }}">
                                <i class="fas {{ $user->is_active ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                            </button>
                        </form>
                        <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-outline btn-sm">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div style="margin-top: 1rem;">
        {{ $users->links() }}
    </div>
</div>
</div>
@endsection
