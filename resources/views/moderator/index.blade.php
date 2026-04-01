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

    <div style="display: grid; grid-template-columns: 1fr 300px; gap: 1.5rem; align-items: start;">
        
        {{-- Flux de questions --}}
        <div class="space-y-4">
            @forelse($questions as $q)
            <div class="card" style="border-left: 4px solid {{ $q->status == 'answering' ? 'var(--brand)' : ($q->status == 'approved' ? '#059669' : ($q->status == 'answered' ? '#6b7280' : ($q->status == 'rejected' ? '#dc2626' : 'var(--border)'))) }};">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem;">
                    <div>
                        <span class="badge" style="margin-bottom: 0.5rem; background: {{ $q->status == 'answered' ? '#f3f4f6' : '' }}; color: {{ $q->status == 'answered' ? '#6b7280' : '' }};">
                            {{ strtoupper($q->status == 'answering' ? 'en cours' : ($q->status == 'answered' ? 'répondu' : $q->status)) }}
                        </span>
                        <p style="font-size: 1rem; font-weight: 500;">{{ $q->content }}</p>
                        <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.75rem; color: var(--muted-foreground); margin-top: 0.5rem;">
                            <span>Par <strong>{{ $q->pseudo }}</strong></span>
                            <span>•</span>
                            <span>{{ $q->created_at->diffForHumans() }}</span>
                            <span>•</span>
                            <span>{{ $q->votes_count }} votes</span>
                        </div>
                    </div>
                    
                    {{-- Actions --}}
                    <div style="display: flex; gap: 0.5rem;">
                        @if($q->status == 'approved')
                        <form action="{{ route('dashboard.moderator.status', $q->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="status" value="answering">
                            <button type="submit" class="btn-brand" style="padding: 0.375rem 0.75rem; font-size: 0.75rem;" title="Projeter la question">Projeter</button>
                        </form>
                        @endif

                        @if($q->status == 'answering')
                        <form action="{{ route('dashboard.moderator.status', $q->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="status" value="answered">
                            <button type="submit" class="btn-brand" style="background: #6b7280; padding: 0.375rem 0.75rem; font-size: 0.75rem;" title="Marquer comme répondu">Terminer</button>
                        </form>
                        @endif

                        @if($q->status == 'pending' || $q->status == 'rejected')
                        <form action="{{ route('dashboard.moderator.status', $q->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="status" value="approved">
                            <button type="submit" style="background: #ecfdf5; color: #059669; border: none; border-radius: 0.5rem; padding: 0.375rem 0.75rem; font-size: 0.75rem; cursor: pointer;">Approuver</button>
                        </form>
                        @endif

                        @if($q->status != 'rejected' && $q->status != 'answered')
                        <form action="{{ route('dashboard.moderator.status', $q->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="status" value="rejected">
                            <button type="submit" style="background: #fef2f2; color: #dc2626; border: none; border-radius: 0.5rem; padding: 0.375rem 0.75rem; font-size: 0.75rem; cursor: pointer;">Rejeter</button>
                        </form>
                        @endif

                        <button onclick="openEditModal('{{ $q->id }}', '{{ addslashes($q->content) }}')" style="background: var(--muted); border: none; border-radius: 0.5rem; padding: 0.375rem; cursor: pointer;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 1rem; height: 1rem;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Réponses --}}
                <div style="margin-top: 1rem; padding-left: 1.5rem; border-left: 2px solid var(--muted);">
                    <div class="space-y-2">
                        @foreach($q->replies as $reply)
                        <div style="background: var(--muted); padding: 0.5rem 0.75rem; border-radius: 0.5rem; font-size: 0.875rem;">
                            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                                <span style="font-weight: 600;">{{ $reply->pseudo }}</span>
                                @if($reply->is_moderator)
                                <span class="badge" style="background: var(--brand-light); color: var(--brand); font-size: 0.625rem;">MODÉRATEUR</span>
                                @endif
                                <span style="font-size: 0.75rem; color: var(--muted-foreground);">{{ $reply->created_at->diffForHumans() }}</span>
                            </div>
                            <p>{{ $reply->content }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @empty
            <div class="card" style="text-align: center; padding: 3rem; color: var(--muted-foreground);">
                Aucune question pour le moment.
            </div>
            @endforelse
        </div>

        {{-- Sidebar Stats & Participants --}}
        <div class="space-y-4">
            <div class="card">
                <h3 style="font-size: 0.875rem; font-weight: 600; margin-bottom: 1rem;">Statistiques</h3>
                <div style="display: grid; gap: 0.75rem;">
                    <div style="display: flex; justify-content: space-between;">
                        <span style="font-size: 0.875rem; color: var(--muted-foreground);">Total</span>
                        <span style="font-weight: 600;">{{ $questions->count() }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="font-size: 0.875rem; color: var(--muted-foreground);">En attente</span>
                        <span style="font-weight: 600; color: var(--brand);">{{ $questions->where('status', 'pending')->count() }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="font-size: 0.875rem; color: var(--muted-foreground);">Approuvées</span>
                        <span style="font-weight: 600; color: #059669;">{{ $questions->where('status', 'approved')->count() }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="font-size: 0.875rem; color: var(--muted-foreground);">Répondues</span>
                        <span style="font-weight: 600; color: #6b7280;">{{ $questions->where('status', 'answered')->count() }}</span>
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
        </div>

    </div>
</div>

{{-- Modal Édition --}}
<div id="edit-modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:100; align-items:center; justify-content:center; padding:1rem;">
    <div class="card" style="width:100%; max-width:28rem;">
        <div class="section-header">
            <h2 class="section-title">Corriger la question</h2>
            <button onclick="document.getElementById('edit-modal').style.display='none'" style="background:none; border:none; font-size:1.25rem; cursor:pointer;">&times;</button>
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
    function openEditModal(id, content) {
        const modal = document.getElementById('edit-modal');
        const form = document.getElementById('edit-form');
        const textarea = document.getElementById('edit-content');
        
        form.action = `/dashboard/question/${id}/edit`;
        textarea.value = content;
        modal.style.display = 'flex';
    }

    // --- Active Participants List ---
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

    setInterval(fetchParticipants, 5000);
    fetchParticipants();
</script>

<style>
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
