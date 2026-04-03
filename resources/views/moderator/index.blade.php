@extends('layouts.dashboard')

@section('title', 'Modération : ' . $event->name)

@section('content')

<div class="space-y-5">
    {{-- ---- Mains Levées ---- --}}
    <section class="card" style="margin-bottom: 2rem; border-left: 4px solid var(--brand);">
        <h2 class="section-title">✋ Mains Levées</h2>
        <div style="display: flex; gap: 1rem; overflow-x: auto; padding-bottom: 0.5rem;">
            @forelse($event->raisedHands()->where('status', '!=', 'dismissed')->orderBy('created_at', 'asc')->get() as $index => $hand)
            <div style="min-width: 12rem; background: var(--muted); padding: 1rem; border-radius: 0.75rem; border: 1px solid {{ $hand->status == 'called' ? 'var(--brand)' : 'transparent' }};">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
                    <span class="badge" style="background: var(--brand); color: #fff;">#{{ $index + 1 }}</span>
                    <span style="font-size: 0.625rem; color: var(--muted-foreground);">{{ $hand->created_at->diffForHumans() }}</span>
                </div>
                <p style="font-weight: 700; margin-bottom: 1rem;">{{ $hand->pseudo }}</p>
                <div style="display: flex; gap: 0.5rem;">
                    @if($hand->status == 'pending')
                    <form action="{{ route('dashboard.moderator.hand-status', $hand->id) }}" method="POST" style="flex: 1;">
                        @csrf
                        <input type="hidden" name="status" value="called">
                        <button type="submit" class="btn-brand" style="padding: 0.375rem; font-size: 0.75rem;">Appeler</button>
                    </form>
                    @endif
                    <form action="{{ route('dashboard.moderator.hand-status', $hand->id) }}" method="POST" style="flex: 1;">
                        @csrf
                        <input type="hidden" name="status" value="dismissed">
                        <button type="submit" class="btn-brand" style="background: #fff; color: var(--foreground); border: 1px solid var(--border); padding: 0.375rem; font-size: 0.75rem;">Terminer</button>
                    </form>
                </div>
            </div>
            @empty
            <p style="font-size: 0.875rem; color: var(--muted-foreground); padding: 1rem 0;">Aucune main levée pour le moment.</p>
            @endforelse
        </div>
    </section>

    <div class="page-header">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1>Console de Modération</h1>
                <p>Événement : <strong>{{ $event->name }}</strong> (Code : {{ $event->code }})</p>
            </div>
            <div style="display: flex; gap: 0.75rem;">
                <button onclick="openSettingsModal()" class="btn-brand" style="background: var(--muted); color: var(--foreground);">
                    ⚙️ Paramètres
                </button>
                <a href="{{ route('projection.index', $event->code) }}" target="_blank" class="btn-brand" style="background: var(--muted); color: var(--foreground);">
                    Ouvrir la Projection ↗
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div style="background:#ecfdf5;border:1px solid #a7f3d0;color:#059669;border-radius:0.5rem;padding:0.75rem;margin-bottom:1rem;font-size:0.875rem;">
            {{ session('success') }}
        </div>
    @endif

    {{-- Tabs de navigation --}}
    <div style="display: flex; gap: 1rem; border-bottom: 1px solid var(--border); margin-bottom: 1.5rem;">
        <button onclick="switchTab('active')" id="tab-btn-active" class="tab-btn active-tab" style="padding: 0.75rem 1rem; cursor: pointer; border: none; background: none; font-weight: 500; font-size: 0.875rem;">
            🎯 Flux Actif ({{ $questions->count() }})
        </button>
        <button onclick="switchTab('filtered')" id="tab-btn-filtered" class="tab-btn" style="padding: 0.75rem 1rem; cursor: pointer; border: none; background: none; font-weight: 500; font-size: 0.875rem; color: var(--muted-foreground);">
            🤖 Filtrées par l'IA ({{ $filteredByAI->count() }})
        </button>
    </div>

    <div id="tab-content-active" class="tab-content">
        <div style="display: grid; grid-template-columns: 1fr 300px; gap: 1.5rem; align-items: start;">
            
            {{-- Flux de questions NORMALES --}}
            <div id="questions-container-active" class="space-y-4">
                @include('moderator.partials.questions_list', ['questions' => $questions])
            </div>

            {{-- Sidebar Stats & Participants --}}
            <div class="space-y-4">
                <div class="card">
                    <h3 style="font-size: 0.875rem; font-weight: 600; margin-bottom: 1rem;">Statistiques</h3>
                    <div id="stats-container" style="display: grid; gap: 0.75rem;">
                        <div style="display: flex; justify-content: space-between;">
                            <span style="font-size: 0.875rem; color: var(--muted-foreground);">Total questions</span>
                            <span id="stat-total" style="font-weight: 600;">{{ $questions->count() + $filteredByAI->count() }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span style="font-size: 0.875rem; color: var(--muted-foreground);">🎯 Flux Actif</span>
                            <span id="stat-active" style="font-weight: 600;">{{ $questions->count() }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span style="font-size: 0.875rem; color: var(--muted-foreground);">🤖 Filtrées par l'IA</span>
                            <span id="stat-filtered" style="font-weight: 600; color: #f97316;">{{ $filteredByAI->count() }}</span>
                        </div>
                        <hr style="border: 0.5px solid var(--border); margin: 0.25rem 0;">
                        <div style="display: flex; justify-content: space-between;">
                            <span style="font-size: 0.875rem; color: var(--muted-foreground);">En attente</span>
                            <span id="stat-pending" style="font-weight: 600; color: var(--brand);">{{ $questions->where('status', 'pending')->count() }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span style="font-size: 0.875rem; color: var(--muted-foreground);">Répondues</span>
                            <span id="stat-answered" style="font-weight: 600; color: #6b7280;">{{ $questions->where('status', 'answered')->count() }}</span>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                        <h3 style="font-size: 0.875rem; font-weight: 600;">Participants en ligne</h3>
                        <span id="participant-count" style="font-size: 0.75rem; color: var(--muted-foreground);">0</span>
                    </div>
                    <div id="participants-list" style="display: grid; gap: 0.5rem; max-height: 300px; overflow-y: auto;">
                        <!-- Rempli par JS -->
                    </div>
                </div>

                {{-- Panelistes & Chronos --}}
                <div class="card" style="border-top: 3px solid #10b981;">
                    <h3 style="font-size: 0.875rem; font-weight: 600; margin-bottom: 1rem;">📊 Experts & Chronos</h3>
                    <div id="panelists-container">
                        @include('moderator.partials.panelists_list', ['panelists' => $panelists])
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- --- Onglet Filtré par IA --- --}}
    <div id="tab-content-filtered" class="tab-content" style="display:none;">
        <div style="background: var(--muted); border-radius: 1rem; padding: 2rem; text-align: center; margin-bottom: 2rem;">
            <h2 style="font-size: 1.25rem; font-weight: 700; color: var(--brand);">Réception des questions filtrées par l'Assistant IA</h2>
            <p style="color: var(--muted-foreground);">Ces questions ont été classées comme doublons ou hors-sujet. Vous pouvez les réviser et les remettre dans le flux principal.</p>
        </div>

        <div id="questions-container-filtered" class="space-y-4">
            @include('moderator.partials.filtered_list', ['filteredByAI' => $filteredByAI])
        </div>
    </div>
</div>

{{-- Modal Paramètres --}}
<div id="settings-modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:100; align-items:center; justify-content:center; padding:1rem;">
    <div class="card" style="width:100%; max-width:28rem;">
        <div class="section-header">
            <h2 class="section-title">Paramètres de l'événement</h2>
            <button onclick="document.getElementById('settings-modal').style.display='none'" style="background:none; border:none; font-size:1.25rem; cursor:pointer;">&times;</button>
        </div>
        <form action="{{ route('dashboard.moderator.settings', $event->id) }}" method="POST">
            @csrf
            <div class="form-group">
                <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem;">Date et Heure de démarrage</label>
                <input type="datetime-local" name="scheduled_at" class="form-input" value="{{ $event->scheduled_at ? $event->scheduled_at->format('Y-m-d\TH:i') : '' }}">
                <p style="font-size: 0.75rem; color: var(--muted-foreground); margin-top: 0.25rem;">L'accès sera bloqué pour le public avant cette heure.</p>
            </div>
            <div style="margin-top: 1rem; display: flex; flex-direction: column; gap: 0.75rem;">
                <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem;">
                    <input type="checkbox" name="moderation_enabled" {{ $event->moderation_enabled ? 'checked' : '' }}>
                    Activer la modération manuelle
                </label>
                <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem;">
                    <input type="checkbox" name="anonymous_allowed" {{ $event->anonymous_allowed ? 'checked' : '' }}>
                    Autoriser l'anonymat
                </label>
            </div>
            <div style="margin-top:1.5rem;">
                <button type="submit" class="btn-brand">Sauvegarder les paramètres</button>
            </div>
        </form>
    </div>
</div>

<div id="edit-modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:100; align-items:center; justify-content:center; padding:1rem;">
    <div class="card" style="width:100%; max-width:28rem;">
        <div class="section-header">
            <h2 class="section-title">Corriger la question</h2>
            <button onclick="isEditing=false; document.getElementById('edit-modal').style.display='none'" style="background:none; border:none; font-size:1.25rem; cursor:pointer;">&times;</button>
        </div>
        <form id="edit-form" method="POST">
            @csrf
            <div class="form-group">
                <textarea name="content" id="edit-content" class="form-input" rows="4" required></textarea>
            </div>
            <div style="margin-top:1.5rem;">
                <button type="submit" class="btn-brand">Enregistrer les modifications</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    let isEditing = false; // Flag pour savoir si on est en train d'éditer
    const eventId = '{{ $event->id }}';

    // --- Gestion des onglets ---
    function switchTab(tab) {
        document.querySelectorAll('.tab-content').forEach(c => c.style.display = 'none');
        document.querySelectorAll('.tab-btn').forEach(b => {
            b.style.color = 'var(--muted-foreground)';
            b.style.borderBottom = 'none';
            b.classList.remove('active-tab');
        });

        document.getElementById('tab-content-' + tab).style.display = 'block';
        const btn = document.getElementById('tab-btn-' + tab);
        btn.style.color = 'var(--foreground)';
        btn.style.borderBottom = '2px solid var(--brand)';
        btn.classList.add('active-tab');
    }

    // --- Fonctions existantes ---
    function openEditModal(id, content) {
        isEditing = true;
        const modal = document.getElementById('edit-modal');
        const form = document.getElementById('edit-form');
        const textarea = document.getElementById('edit-content');
        
        form.action = `/dashboard/question/${id}/edit`;
        textarea.value = content;
        modal.style.display = 'flex';
    }

    function openSettingsModal() {
        document.getElementById('settings-modal').style.display = 'flex';
    }

    const eventCode = '{{ $event->code }}';
    async function fetchParticipants() {
        try {
            const response = await fetch(`/e/${eventCode}/active-participants`);
            const data = await response.json();
            const list = document.getElementById('participants-list');
            const count = document.getElementById('participant-count');
            count.textContent = data.length;
            list.innerHTML = data.map(p => `
                <div style="background: var(--muted); padding: 0.5rem 0.75rem; border-radius: 0.5rem; font-size: 0.75rem; display: flex; align-items: center; gap: 0.5rem; ${p.is_speaking ? 'border: 1px solid var(--brand); background: var(--brand-light);' : ''}">
                    <span style="width: 0.5rem; height: 0.5rem; background: ${p.is_speaking ? 'var(--brand)' : '#10b981'}; border-radius: 50%;"></span>
                    <span style="font-weight: 600;">${p.pseudo}</span>
                    ${p.is_typing ? '<span class="typing-dot">...</span>' : ''}
                    ${p.is_speaking ? '🎤' : ''}
                </div>
            `).join('');
        } catch (e) {}
    }

    // --- TEMPS RÉEL : Questions Polling ---
    async function fetchQuestions() {
        if (isEditing) return; // Ne pas rafraîchir si on édite

        try {
            const response = await fetch(`/dashboard/${eventId}/moderator/questions-fetch`);
            const data = await response.json();
            
            // Mise à jour des containers
            document.getElementById('questions-container-active').innerHTML = data.main_html;
            document.getElementById('questions-container-filtered').innerHTML = data.filtered_html;
            document.getElementById('panelists-container').innerHTML = data.panelists_html;
            
            // Mise à jour des badges d'onglets
            document.getElementById('tab-btn-active').textContent = `🎯 Flux Actif (${data.counts.active})`;
            document.getElementById('tab-btn-filtered').textContent = `🤖 Filtrées par l'IA (${data.counts.filtered})`;
            
            // Mise à jour de la sidebar
            document.getElementById('stat-total').textContent = data.counts.total;
            document.getElementById('stat-active').textContent = data.counts.active;
            document.getElementById('stat-filtered').textContent = data.counts.filtered;
            document.getElementById('stat-pending').textContent = data.counts.pending;
            document.getElementById('stat-answered').textContent = data.counts.answered;

        } catch (e) {
            console.error("Polling error:", e);
        }
    }

    setInterval(fetchParticipants, 5000);
    setInterval(fetchQuestions, 5000); // Rafraîchir toutes les 5 secondes
    fetchParticipants();
</script>

<style>
.active-tab {
    border-bottom: 2px solid var(--brand) !important;
    color: var(--foreground) !important;
}
.typing-dot {
    font-weight: 800;
    color: var(--brand);
    animation: blink 1s infinite;
}
@keyframes blink {
    0%, 100% { opacity: 0; }
    50% { opacity: 1; }
}
</style>
@endpush

@endsection
