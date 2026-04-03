@extends('layouts.dashboard')

@section('title', 'Mes Événements')

@section('content')

<div class="space-y-5">
    <div class="page-header">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1>Mes Événements</h1>
                <p>Gérez vos sessions Q&A passées et à venir.</p>
            </div>
            <button onclick="openCreateModal()" class="btn-brand">+ Créer un événement</button>
        </div>
    </div>

    {{-- Modal de Succès --}}
    @if(session('success'))
    <div id="success-modal" style="position:fixed; inset:0; background:rgba(0,0,0,0.4); z-index:2000; display:flex; align-items:center; justify-content:center; backdrop-filter: blur(4px); animation: fadeIn 0.3s ease;">
        <div class="card" style="width:100%; max-width:24rem; text-align:center; padding:2.5rem; border-radius:1.5rem; border:none; box-shadow:0 20px 25px -5px rgba(0,0,0,0.1);">
            <div style="font-size:4rem; margin-bottom:1rem; animation: tada 1s ease;">🎉</div>
            <h2 style="font-size:1.5rem; font-weight:900; color:var(--foreground); margin-bottom:0.5rem;">Félicitations !</h2>
            <p style="color:var(--muted-foreground); margin-bottom:1.5rem;">{{ session('success') }}</p>
            <button onclick="document.getElementById('success-modal').remove()" class="btn-brand" style="width:100%;">Génial !</button>
        </div>
    </div>
    @endif

    {{-- Modal de Création d'Événement --}}
    <div id="create-modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:1000; align-items:center; justify-content:center; padding:1.5rem; backdrop-filter: blur(8px); overflow-y: auto;">
        <div class="card" style="width:100%; max-width:32rem; padding: 2rem; border-radius: 1.5rem; border: none; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); position: relative;">
            <button onclick="closeCreateModal()" style="position: absolute; top: 1.25rem; right: 1.25rem; background: var(--muted); border: none; width: 2rem; height: 2rem; border-radius: 50%; font-size: 1.25rem; cursor: pointer; color: var(--muted-foreground);">&times;</button>
            
            <h2 style="font-size: 1.5rem; font-weight: 900; color: var(--foreground); margin-bottom: 0.5rem;">Nouvel événement</h2>
            <p style="color: var(--muted-foreground); margin-bottom: 2rem; font-size: 0.875rem;">Configurez votre session interactive en quelques secondes.</p>
            
            <form method="POST" action="{{ route('dashboard.events.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label class="form-label" for="image">Image de couverture / Bannière</label>
                    <input type="file" id="image" name="image" class="form-input" style="padding: 0.5rem; font-size: 0.75rem;" accept="image/*">
                </div>

                <div class="form-group">
                    <label class="form-label" for="name">Nom de l'événement</label>
                    <input type="text" id="name" name="name" class="form-input" required placeholder="Ex: Masterclass IA 2026">
                </div>

                <div class="form-group">
                    <label class="form-label" for="description">Description (Optionnel)</label>
                    <textarea id="description" name="description" class="form-input" rows="3" placeholder="De quoi s'agit-il ?"></textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label" for="date">Date</label>
                        <input type="date" id="date" name="date" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="scheduled_at">Heure (Début)</label>
                        <input type="datetime-local" id="scheduled_at" name="scheduled_at" class="form-input">
                    </div>
                </div>

                <div style="margin-top: 2rem; display: flex; gap: 1rem;">
                    <button type="button" onclick="closeCreateModal()" class="btn-brand" style="background: var(--muted); color: var(--foreground); flex: 1; border: none;">Annuler</button>
                    <button type="submit" class="btn-brand" style="flex: 2; border: none;">Lancer l'événement</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="border-bottom: 1px solid var(--border);">
                        <th style="padding: 1rem; font-size: 0.75rem; font-weight: 600; color: var(--muted-foreground); text-transform: uppercase;">Nom</th>
                        <th style="padding: 1rem; font-size: 0.75rem; font-weight: 600; color: var(--muted-foreground); text-transform: uppercase;">Code</th>
                        <th style="padding: 1rem; font-size: 0.75rem; font-weight: 600; color: var(--muted-foreground); text-transform: uppercase;">Date</th>
                        <th style="padding: 1rem; font-size: 0.75rem; font-weight: 600; color: var(--muted-foreground); text-transform: uppercase;">Statut</th>
                        <th style="padding: 1rem; font-size: 0.75rem; font-weight: 600; color: var(--muted-foreground); text-transform: uppercase; text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($events as $event)
                    <tr style="border-bottom: 1px solid var(--border);">
                        <td style="padding: 1rem;">
                            <a href="{{ route('dashboard.events.show', $event->id) }}" style="font-weight: 600; color: var(--foreground); text-decoration: none;">{{ $event->name }}</a>
                        </td>
                        <td style="padding: 1rem;">
                            <code style="background: var(--muted); padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.875rem;">{{ $event->code }}</code>
                        </td>
                        <td style="padding: 1rem; font-size: 0.875rem;">{{ $event->date->format('d/m/Y') }}</td>
                        <td style="padding: 1rem;">
                            <span class="badge" style="background: {{ $event->status == 'active' ? '#ecfdf5' : '#f3f4f6' }}; color: {{ $event->status == 'active' ? '#059669' : '#6b7280' }};">
                                {{ ucfirst($event->status) }}
                            </span>
                        </td>
                        <td style="padding: 1rem; text-align: right;">
                            <div style="display: flex; gap: 0.5rem; justify-content: flex-end; align-items: center;">
                                <form action="{{ route('dashboard.events.toggle-status', $event->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn-brand" style="padding: 0.375rem 0.75rem; font-size: 0.75rem; background: {{ $event->status == 'active' ? '#f3f4f6' : '#ecfdf5' }}; color: {{ $event->status == 'active' ? '#374151' : '#059669' }};">
                                        {{ $event->status == 'active' ? 'Désactiver' : 'Activer' }}
                                    </button>
                                </form>
                                <a href="{{ route('dashboard.moderator.index', $event->id) }}" class="btn-brand" style="padding: 0.375rem 0.75rem; font-size: 0.75rem;">Console</a>
                                <a href="{{ route('dashboard.events.edit', $event->id) }}" style="padding: 0.375rem; color: var(--muted-foreground);">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 1rem; height: 1rem;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                    </svg>
                                </a>
                                <form action="{{ route('dashboard.events.destroy', $event->id) }}" method="POST" onsubmit="return confirm('Supprimer cet événement ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="padding: 0.375rem; color: #dc2626; background: none; border: none; cursor: pointer;">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 1rem; height: 1rem;">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="padding: 3rem; text-align: center; color: var(--muted-foreground);">
                            Vous n'avez pas encore d'événement.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function openCreateModal() {
        document.getElementById('create-modal').style.display = 'flex';
    }
    function closeCreateModal() {
        document.getElementById('create-modal').style.display = 'none';
    }
</script>
@endpush

<style>
@keyframes fadeIn {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
}
@keyframes tada {
    0% { transform: scale(1); }
    10%, 20% { transform: scale(0.9) rotate(-3deg); }
    30%, 50%, 70%, 90% { transform: scale(1.1) rotate(3deg); }
    40%, 60%, 80% { transform: scale(1.1) rotate(-3deg); }
    100% { transform: scale(1) rotate(0); }
}
</style>
