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
                <i class="fas fa-plus"></i> Nouvel Admin
            </a>
        </div>
    </div>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Email</th>
                <th>Statut</th>
                <th>Date d'ajout</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($admins as $admin)
            <tr>
                <td>{{ $admin->name }} @if($admin->id === auth()->id()) <span class="badge badge-info">(Moi)</span> @endif</td>
                <td>{{ $admin->email }}</td>
                <td>
                    <span class="badge {{ $admin->is_active ? 'badge-success' : 'badge-error' }}">
                        {{ $admin->is_active ? 'Actif' : 'Inactif' }}
                    </span>
                </td>
                <td>{{ $admin->created_at->format('d/m/Y') }}</td>
                <td>
                    <div style="display: flex; gap: 0.5rem;">
                        <form action="{{ route('admin.admins.toggle', $admin->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline btn-sm" title="{{ $admin->is_active ? 'Désactiver' : 'Activer' }}">
                                <i class="fas {{ $admin->is_active ? 'fa-user-slash' : 'fa-user-check' }}"></i>
                            </button>
                        </form>
                        <form action="{{ route('admin.admins.reset-password', $admin->id) }}" method="POST" onsubmit="return confirm('Générer un nouveau mot de passe et l\'envoyer par mail ?')">
                            @csrf
                            <button type="submit" class="btn btn-outline btn-sm" title="Réinitialiser le mot de passe">
                                <i class="fas fa-key"></i>
                            </button>
                        </form>
                        <a href="{{ route('admin.admins.edit', $admin->id) }}" class="btn btn-outline btn-sm">
                            <i class="fas fa-edit"></i>
                        </a>
                        @if($admin->id !== auth()->id())
                        <form action="{{ route('admin.admins.destroy', $admin->id) }}" method="POST" onsubmit="return confirm('Supprimer cet administrateur ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline btn-sm" style="color: #ef4444;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div style="margin-top: 1rem;">
        {{ $admins->links() }}
    </div>
</div>
</div>
@endsection
