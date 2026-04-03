@extends('layouts.dashboard')

@section('title', 'Replay Interactif : ' . $event->name)

@section('content')

<div class="space-y-5">
    <div class="page-header">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <a href="{{ route('marketplace.show', $event->id) }}" style="font-size: 0.75rem; color: var(--muted-foreground); text-decoration: none;">← Retour aux détails</a>
                <h1 style="margin-top: 0.5rem;">Replay : {{ $event->name }}</h1>
            </div>
            <div class="badge" style="background: var(--brand); color: #fff; padding: 0.5rem 1rem;">SESSION ARCHIVÉE</div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
        {{-- Zone Projection Replay --}}
        <div class="card" style="padding: 0; overflow: hidden; background: #000; display: flex; flex-direction: column; height: 70vh;">
            <div id="replay-viewer" style="flex-grow: 1; position: relative;">
                @php
                    $panelist = $event->panelists->first();
                    $fileUrl = $panelist ? asset('storage/' . $panelist->presentation_path) : null;
                @endphp
                @if($fileUrl)
                    <iframe id="presentation-iframe" src="{{ $fileUrl }}#page=1" style="width: 100%; height: 100%; border: none;"></iframe>
                @else
                    <div style="display: grid; place-items: center; height: 100%; color: #fff;">Aucun document disponible</div>
                @endif
            </div>
            
            {{-- Barre de Contrôle du Replay --}}
            <div style="background: #1a1a1a; padding: 1rem; display: flex; align-items: center; gap: 1.5rem; color: #fff;">
                <button onclick="prevLog()" class="btn-brand" style="width: auto; background: #333; border: none; padding: 0.5rem 1rem; border-radius: 0.5rem; cursor: pointer;">◄ Précédent</button>
                <div style="flex-grow: 1; text-align: center;">
                    <span id="log-counter" style="font-weight: 800; font-size: 1.25rem;">Moment 1 / {{ $logs->count() }}</span>
                    <p id="log-time" style="font-size: 0.75rem; color: #888; margin: 0;">Chargement...</p>
                </div>
                <button onclick="nextLog()" class="btn-brand" style="width: auto; border: none; padding: 0.5rem 1rem; border-radius: 0.5rem; cursor: pointer;">Suivant ►</button>
            </div>
        </div>

        {{-- Flux des Questions Synchronisées --}}
        <div class="card" style="overflow-y: auto; height: 70vh;">
            <h2 class="section-title">Flux Q&A</h2>
            <div id="questions-timeline" class="space-y-4" style="margin-top: 1rem;">
                @forelse($questions as $q)
                <div class="q-item" data-time="{{ $q->created_at->format('H:i:s') }}" style="padding: 1rem; border-radius: 0.75rem; background: var(--muted); border: 2px solid transparent; transition: all 0.3s; margin-bottom: 1rem;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <span style="font-size: 0.6rem; color: var(--muted-foreground); font-weight: 800;">{{ $q->created_at->format('H:i') }}</span>
                        <span style="font-size: 0.6rem; color: var(--brand); font-weight: 800;">#{{ $q->id }}</span>
                    </div>
                    <p style="font-weight: 700; margin: 0.5rem 0; font-size: 0.875rem;">{{ $q->content }}</p>
                    @if($q->audio_path)
                        <audio src="{{ asset('storage/' . $q->audio_path) }}" controls style="width: 100%; height: 2rem; margin-top: 0.5rem;"></audio>
                    @endif
                    
                    @if($q->replies->count() > 0)
                    <div style="margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px dashed var(--border);">
                        @foreach($q->replies as $r)
                        <p style="font-size: 0.75rem; color: var(--foreground); font-style: italic;">"{{ $r->content }}"</p>
                        @endforeach
                    </div>
                    @endif
                </div>
                @empty
                <p style="text-align: center; color: var(--muted-foreground); font-size: 0.875rem;">Pas de questions durant cette séance.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script>
    const logs = @json($logs);
    // On crée une map des documents par ID de panéliste pour un switch rapide
    const panelistDocs = {
        @foreach($event->panelists as $p)
            "{{ $p->id }}": "{{ asset('storage/' . $p->presentation_path) }}",
        @endforeach
    };

    let currentLogIndex = 0;
    let currentPanelistId = null;

    function updateReplay() {
        if (logs.length === 0) return;
        const log = logs[currentLogIndex];
        const iframe = document.getElementById('presentation-iframe');
        const counter = document.getElementById('log-counter');
        const timeBox = document.getElementById('log-time');

        // Changement de document si le panéliste change
        if (log.panelist_id != currentPanelistId) {
            currentPanelistId = log.panelist_id;
            const docUrl = panelistDocs[currentPanelistId];
            if (docUrl && iframe) {
                // Charger le nouveau document à la page enregistrée
                iframe.src = `${docUrl}#page=${log.slide_number}`;
            }
        } else if (iframe && log.slide_number) {
            // Même panéliste, juste changement de page
            const baseUrl = iframe.src.split('#')[0];
            iframe.src = `${baseUrl}#page=${log.slide_number}`;
        }

        // Mise à jour UI
        counter.textContent = `Moment ${currentLogIndex + 1} / ${logs.length}`;
        timeBox.textContent = `Enregistré le ${new Date(log.created_at).toLocaleTimeString()}`;

        // Highlight questions correspondantes (celles posées AVANT ou pendant ce log)
        const currentLogTime = new Date(log.created_at).toTimeString().split(' ')[0];
        
        document.querySelectorAll('.q-item').forEach(item => {
            const qTime = item.dataset.time;
            if (qTime <= currentLogTime) {
                item.style.borderColor = 'var(--brand)';
                item.style.background = '#fff';
                item.style.opacity = '1';
                item.style.boxShadow = '0 4px 6px -1px rgba(0, 0, 0, 0.1)';
            } else {
                item.style.borderColor = 'transparent';
                item.style.background = 'var(--muted)';
                item.style.opacity = '0.4';
                item.style.boxShadow = 'none';
            }
        });
    }

    function nextLog() {
        if (currentLogIndex < logs.length - 1) {
            currentLogIndex++;
            updateReplay();
        }
    }

    function prevLog() {
        if (currentLogIndex > 0) {
            currentLogIndex--;
            updateReplay();
        }
    }

    // Navigation clavier
    document.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowRight') nextLog();
        if (e.key === 'ArrowLeft') prevLog();
    });

    window.onload = updateReplay;
</script>

@endsection
