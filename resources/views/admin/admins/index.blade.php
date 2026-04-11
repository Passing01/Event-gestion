@extends('layouts.dashboard')

@section('title', 'Gestion des Administrateurs')

@section('content')
<div class="dash-content">
    <div class="dash-header">
        <div>
            <h1 class="dash-title">Administrateurs</h1>
            <p class="dash-subtitle">Gérez les comptes d'accès à la plateforme.</p>
        </div>
        <div class="dash-actions">
            <a href="{{ route('admin.admins.create') }}" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 1.25rem;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Nouvel Admin
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="card" style="background: rgba(34, 197, 94, 0.1); color: #22c55e; border: 1px solid rgba(34, 197, 94, 0.2); margin-bottom: 1.5rem; padding: 1rem;">
        {{ session('success') }}
    </div>
    @endif

    <div class="card admin-table-card">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Statut</th>
                    <th>Date d'ajout</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($admins as $admin)
                <tr>
                    <td>
                        <div style="font-weight: 600;">{{ $admin->name }}</div>
                        @if(auth()->id() == $admin->id) <span class="badge" style="font-size: 0.6rem;">(Moi)</span> @endif
                    </td>
                    <td style="color: var(--muted-foreground);">{{ $admin->email }}</td>
                    <td>
                        <span class="badge {{ $admin->is_active ? 'badge-success' : 'badge-error' }}">
                            {{ $admin->is_active ? 'Actif' : 'Inactif' }}
                        </span>
                    </td>
                    <td>{{ $admin->created_at->format('d/m/Y') }}</td>
                    <td style="text-align: right;">
                        <div style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                            <a href="{{ route('admin.admins.edit', $admin->id) }}" class="btn btn-outline btn-sm" title="Modifier">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 1rem;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                </svg>
                            </a>
                            
                            @if(auth()->id() != $admin->id)
                            <form action="{{ route('admin.admins.toggle', $admin->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-outline btn-sm" title="{{ $admin->is_active ? 'Désactiver' : 'Activer' }}">
                                    @if($admin->is_active)
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
                            @endif

                            <form action="{{ route('admin.admins.reset-password', $admin->id) }}" method="POST" onsubmit="return confirm('Réinitialiser le mot de passe ?')">
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
        
        @if($admins->hasPages())
        <div style="padding: 1rem; border-top: 1px solid var(--border);">
            {{ $admins->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
