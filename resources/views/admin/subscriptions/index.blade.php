@extends('layouts.dashboard')

@section('title', 'Gestion des Abonnements')

@section('content')
<div class="dash-content">
    <div class="dash-header">
        <div>
            <h1 class="dash-title">Abonnements</h1>
            <p class="dash-subtitle">Gérez les plans et privilèges des utilisateurs.</p>
        </div>
    </div>

    <div class="card admin-table-card">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Utilisateur</th>
                    <th>Plan Actuel</th>
                    <th>Dernière mise à jour</th>
                    <th style="text-align: right;">Changer le plan</th>
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
                        <span class="badge {{ $user->plan === 'Enterprise' ? 'badge-info' : ($user->plan === 'Premium' ? 'badge-success' : 'badge-warning') }}">
                            {{ ucfirst($user->plan ?? 'Free') }}
                        </span>
                    </td>
                    <td>{{ $user->updated_at->format('d/m/Y') }}</td>
                    <td style="text-align: right;">
                        <form action="{{ route('admin.subscriptions.update', $user->id) }}" method="POST" style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                            @csrf
                            @method('PUT')
                            <select name="plan" class="form-input" style="height: 2.1rem; padding: 0 0.5rem; width: 120px;">
                                <option value="Free" {{ $user->plan === 'Free' ? 'selected' : '' }}>Free</option>
                                <option value="Premium" {{ $user->plan === 'Premium' ? 'selected' : '' }}>Premium</option>
                                <option value="Enterprise" {{ $user->plan === 'Enterprise' ? 'selected' : '' }}>Enterprise</option>
                            </select>
                            <button type="submit" class="btn btn-primary btn-sm">Mettre à jour</button>
                        </form>
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
