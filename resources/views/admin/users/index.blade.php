@extends('layouts.dashboard')

@section('title', 'Gestion des Utilisateurs')

@section('content')
<div class="dash-content">
    <div class="dash-header">
        <div>
            <h1 class="dash-title">Utilisateurs</h1>
            <p class="dash-subtitle">Gérez les modérateurs et les participants de la plateforme.</p>
        </div>
    </div>

    <div class="card admin-table-card">
        <div style="padding: 1rem; border-bottom: 1px solid var(--border);">
            <form action="{{ route('admin.users.index') }}" method="GET" style="display: flex; gap: 0.5rem; max-width: 400px;">
                <input type="text" name="search" value="{{ request('search') }}" 
                       class="form-input" placeholder="Rechercher par nom ou email..." style="flex: 1;">
                <button type="submit" class="btn btn-primary">Rechercher</button>
            </form>
        </div>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>Utilisateur</th>
                    <th>Rôle</th>
                    <th>Statut</th>
                    <th>Plan</th>
                    <th>Événements</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td>
                        <div style="font-weight: 600;">{{ $user->name }}</div>
                        <div style="font-size: 0.75rem; color: var(--muted-foreground);">{{ $user->email }}</div>
                    </td>
                    <td>
                        <span class="badge {{ $user->role === 'moderator' ? 'badge-info' : '' }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td>
                        <span class="badge {{ $user->is_active ? 'badge-success' : 'badge-error' }}">
                            {{ $user->is_active ? 'Actif' : 'Bloqué' }}
                        </span>
                    </td>
                    <td>
                        <span style="font-weight: 500;">{{ ucfirst($user->plan ?? 'Free') }}</span>
                    </td>
                    <td>{{ $user->events_count ?? 0 }}</td>
                    <td style="text-align: right;">
                        <div style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                            <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-outline btn-sm" title="Détails">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 1rem;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </a>
                            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-outline btn-sm" title="Modifier">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 1rem;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                </svg>
                            </a>
                            <form action="{{ route('admin.users.toggle', $user->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-outline btn-sm" title="{{ $user->is_active ? 'Bloquer' : 'Activer' }}">
                                    @if($user->is_active)
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 1rem;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                    </svg>
                                    @else
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 1rem;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    @endif
                                </button>
                            </form>
                            <form action="{{ route('admin.users.reset-password', $user->id) }}" method="POST" onsubmit="return confirm('Réinitialiser le mot de passe de cet utilisateur ?')">
                                @csrf
                                <button type="submit" class="btn btn-outline btn-sm" title="Réinitialiser le mot de passe">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 1rem;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @if($users->hasPages())
        <div style="padding: 1rem; border-top: 1px solid var(--border);">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
