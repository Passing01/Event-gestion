@extends('layouts.dashboard')

@section('title', 'Gestion des Abonnements')

@section('content')
<div class="dash-content">
    <div class="dash-header">
        <div>
            <h1 class="dash-title">Abonnements</h1>
            <p class="dash-subtitle">Gérez les plans des utilisateurs.</p>
        </div>
    </div>

<div class="card">
    <p style="color: #94a3b8; margin-bottom: 2rem;">Liste des utilisateurs et leurs plans actuels.</p>
    <table>
        <thead>
            <tr>
                <th>Utilisateur</th>
                <th>Plan Actuel</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td>{{ $user->name }} ({{ $user->email }})</td>
                <td>
                    <span class="badge {{ $user->plan === 'enterprise' ? 'badge-primary' : ($user->plan === 'premium' ? 'badge-info' : 'badge-outline') }}" 
                          style="{{ $user->plan === 'enterprise' ? 'background: #7c3aed; color: #fff;' : '' }}">
                        {{ ucfirst($user->plan ?? 'Gratuit') }}
                    </span>
                </td>
                <td>
                    <form action="{{ route('admin.subscriptions.update', $user->id) }}" method="POST" style="display: flex; gap: 0.5rem;">
                        @csrf
                        @method('PUT')
                        <select name="plan" class="btn btn-outline btn-sm" style="background: var(--dark); border-radius: 4px;">
                            <option value="free" {{ ($user->plan ?? 'free') == 'free' ? 'selected' : '' }}>Gratuit</option>
                            <option value="premium" {{ $user->plan == 'premium' ? 'selected' : '' }}>Premium</option>
                            <option value="enterprise" {{ $user->plan == 'enterprise' ? 'selected' : '' }}>Entreprise</option>
                        </select>
                        <button type="submit" class="btn btn-primary btn-sm">Changer</button>
                    </form>
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
