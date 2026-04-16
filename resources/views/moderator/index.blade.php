@extends('layouts.dashboard')

@section('title', 'Modération : ' . $event->name)

@section('content')

<div class="space-y-5">
    {{-- ---- Mains Levées ---- --}}
    <section class="card" style="margin-bottom: 2rem; border-left: 4px solid var(--brand);">
        <h2 class="section-title">✋ Mains Levées</h2>
        <div id="hands-container" style="display: flex; gap: 1rem; overflow-x: auto; padding-bottom: 0.5rem;">
            @include('moderator.partials.hands_list', ['hands' => $event->raisedHands()->where('status', '!=', 'dismissed')->orderBy('created_at', 'asc')->get()])
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
                @if(!$event->closed_at)
                <button type="button" onclick="openCloseModal()" class="btn-brand" style="background: #ef4444; color: #fff; border: none;">
                    🔒 Clôturer l'événement
                </button>
                @else
                <div class="badge" style="background: var(--muted); color: var(--muted-foreground); padding: 0.5rem 1rem;">
                    📁 Événement Archivé
                </div>
                @endif
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
        <button onclick="switchTab('active')" id="tab-btn-active" class="tab-btn active-tab" style="padding: 0.75rem 1rem; cursor: pointer; border: none; background: none; font-weight: 800; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em;">
            🎯 Flux Actif ({{ $questions->count() }})
        </button>
        <button onclick="switchTab('panelists')" id="tab-btn-panelists" class="tab-btn" style="padding: 0.75rem 1rem; cursor: pointer; border: none; background: none; font-weight: 800; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--muted-foreground);">
            📊 Experts & Chronos
        </button>
        <button onclick="switchTab('filtered')" id="tab-btn-filtered" class="tab-btn" style="padding: 0.75rem 1rem; cursor: pointer; border: none; background: none; font-weight: 800; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--muted-foreground);">
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

            </div>
        </div>
    </div>

    {{-- --- Nouvel Onglet : Experts & Chronos --- --}}
    <div id="tab-content-panelists" class="tab-content" style="display:none;">
        <div style="background: var(--brand-light); border-radius: 1.5rem; padding: 2.5rem; border: 1px solid var(--brand-soft); margin-bottom: 2rem; display: flex; align-items: center; justify-content: space-between; position: relative; overflow: hidden;">
            <div style="position: relative; z-index: 2;">
                <h2 style="font-size: 1.75rem; font-weight: 950; color: var(--brand); margin: 0; letter-spacing: -0.02em;">Gestion des Experts</h2>
                <p style="color: var(--muted-foreground); margin: 0.6rem 0 0; font-size: 1.1rem; font-weight: 500;">Pilotez les temps de parole et les projections en temps réel.</p>
            </div>
            <div style="font-size: 5rem; opacity: 0.1; position: absolute; right: -1rem; bottom: -1.5rem; transform: rotate(-15deg);">📊</div>
        </div>

        <div id="panelists-container" style="display: flex; flex-wrap: wrap; gap: 1.5rem; justify-content: flex-start; align-items: flex-start;">
            @include('moderator.partials.panelists_list', ['panelists' => $panelists])
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

    {{-- Fenêtre flottante pour voir le partage d'écran --}}
    <div id="mod-screenshare-wrap" style="display:none; position:fixed; bottom:20px; right:20px; width:600px; background:#000; border-radius:1rem; overflow:hidden; box-shadow:0 25px 50px -12px rgba(0,0,0,0.5); z-index:100; border:2px solid var(--brand); transition: width 0.3s ease;">
        <div style="background:var(--brand); color:#fff; padding:0.5rem 1rem; font-size:0.8rem; display:flex; justify-content:space-between; align-items:center;">
            <span style="font-weight:700;">📺 APERÇU PRÉSENTATION EN DIRECT</span>
            <div style="display: flex; gap: 0.5rem;">
                <button onclick="toggleModPreviewSize()" style="background:none; border:none; color:#fff; cursor:pointer; font-size: 1.2rem;" title="Agrandir/Réduire">↕️</button>
                <button onclick="document.getElementById('mod-screenshare-wrap').style.display='none'" style="background:none; border:none; color:#fff; cursor:pointer; font-size: 1.2rem;">&times;</button>
            </div>
        </div>
        <video id="mod-screenshare-video" autoplay playsinline style="width:100%; aspect-ratio: 16/9; display:block; background:#000;"></video>
    </div>

    <script>
        function toggleModPreviewSize() {
            const wrap = document.getElementById('mod-screenshare-wrap');
            if (wrap.style.width === '600px' || wrap.style.width === '') {
                wrap.style.width = '900px';
            } else {
                wrap.style.width = '600px';
            }
        }
    </script>

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

{{-- Modal Confirmation Clôture --}}
<div id="close-event-modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:100; align-items:center; justify-content:center; padding:1.5rem; backdrop-filter: blur(4px);">
    <div class="card" style="width:100%; max-width:32rem; padding: 2rem; border-radius: 1.5rem; text-align: center; border: none; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);">
        
        {{-- Contenu Confirmation --}}
        <div id="close-step-confirm">
            <div style="font-size: 4rem; margin-bottom: 1.5rem;">🔒</div>
            <h2 style="font-size: 1.5rem; font-weight: 900; color: var(--foreground); margin-bottom: 1rem;">Clôturer définitivement ?</h2>
            <p style="color: var(--muted-foreground); margin-bottom: 2rem; line-height: 1.6;">
                Cette action va <strong>figer l'événement</strong>. Notre IA va générer le rapport final complet, la synthèse et l'analyse de sentiment. <br><br>
                <span style="font-size: 0.8rem; background: var(--brand-light); color: var(--brand); padding: 0.25rem 0.75rem; border-radius: 999px; font-weight: 700;">Note : Les accès au public seront coupés.</span>
            </p>
            
            <form action="{{ route('dashboard.events.close', $event->id) }}" method="POST" onsubmit="showLoadingState()">
                @csrf
                <div style="display: grid; grid-template-columns: 1fr 1.5fr; gap: 1rem;">
                    <button type="button" onclick="document.getElementById('close-event-modal').style.display='none'" class="btn-brand" style="background: var(--muted); color: var(--foreground); border: none;">
                        Annuler
                    </button>
                    <button type="submit" class="btn-brand" style="background: #ef4444; border: none; font-weight: 800;">
                        Oui, clôturer & analyser
                    </button>
                </div>
            </form>
        </div>

        {{-- Contenu Chargement IA --}}
        <div id="close-step-loading" style="display: none; padding: 3rem 0;">
            <div class="ai-loader" style="margin: 0 auto 2rem;">
                <div style="font-size: 4rem; animation: float 3s ease-in-out infinite;">🤖</div>
            </div>
            <h2 style="font-size: 1.5rem; font-weight: 900; color: var(--brand); margin-bottom: 0.5rem;">Analyse IA en cours...</h2>
            <p style="color: var(--muted-foreground);">Notre moteur génère votre rapport professionnel et synchronise le Marketplace. Veuillez patienter quelques secondes.</p>
            
            <div style="margin-top: 2rem; height: 10px; background: var(--muted); border-radius: 5px; overflow: hidden; position: relative;">
                <div class="progress-bar-ia" style="height: 100%; background: var(--brand); border-radius: 5px; width: 0%; transition: width 0.3s;"></div>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes float {
    0%, 100% { transform: translateY(0) rotate(0deg); }
    50% { transform: translateY(-20px) rotate(5deg); }
}
.progress-bar-ia {
    animation: charging 10s linear forwards;
}
@keyframes charging {
    0% { width: 0%; }
    20% { width: 15%; }
    50% { width: 45%; }
    80% { width: 85%; }
    100% { width: 95%; }
}
</style>

<script>
function showLoadingState() {
    document.getElementById('close-step-confirm').style.display = 'none';
    document.getElementById('close-step-loading').style.display = 'block';
}
</script>

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
<script src="https://unpkg.com/peerjs@1.5.2/dist/peerjs.min.js"></script>
<script>
    let isEditing = false; 
    let isRecordingLive = false; 
    let liveRecorder;
    let liveChunks = [];
    let isRecording = false;
    let mediaRecorder;
    let audioChunks = [];
    let recordTimer;
    let seconds = 0;
    const eventId = '{{ $event->id }}';
    const eventCode = '{{ $event->code }}';

    // --- PeerJS : Enregistreur Micro Live ---
    const moderatorPeer = new Peer(`${eventCode}-MODERATOR`);
    moderatorPeer.on('open', (id) => console.log('Console Modérateur prête pour l\'enregistrement, ID:', id));

    moderatorPeer.on('call', (call) => {
        console.log("Appel entrant reçu de:", call.metadata?.name || 'Inconnu');
        
        call.answer(); 
        
        call.on('stream', (remoteStream) => {
            // Si c'est de la VIDÉO (partage d'écran)
            if (remoteStream.getVideoTracks().length > 0) {
                console.log("Flux VIDÉO (partage d'écran) reçu sur la console modérateur.");
                const wrap = document.getElementById('mod-screenshare-wrap');
                const video = document.getElementById('mod-screenshare-video');
                wrap.style.display = 'block';
                video.srcObject = remoteStream;

                remoteStream.getVideoTracks()[0].onended = () => {
                    wrap.style.display = 'none';
                };
                return; // On ne fait pas l'enregistrement audio si c'est de la vidéo (évite les doublons)
            }

            // Si c'est de l'AUDIO uniquement (Intervention en direct)
            console.log("Démarrage de l'enregistrement du flux audio...");
            
            // CRITIQUE : Pour que MediaRecorder reçoive des données, le flux DOIT être rattaché à un élément audio (même muet)
            const hiddenAudio = document.createElement('audio');
            hiddenAudio.srcObject = remoteStream;
            hiddenAudio.muted = true; // Pour éviter l'écho chez le modérateur
            hiddenAudio.play();

            // On l'enregistre
            liveChunks = [];
            
            // Utilisation d'un type MIME plus robuste si supporté
            const options = { mimeType: 'audio/webm;codecs=opus' };
            if (!MediaRecorder.isTypeSupported(options.mimeType)) {
                delete options.mimeType;
            }
            
            liveRecorder = new MediaRecorder(remoteStream, options);
            
            liveRecorder.ondataavailable = (e) => {
                if (e.data && e.data.size > 0) {
                    liveChunks.push(e.data);
                    console.log("Chunk reçu, taille:", e.data.size);
                }
            };

            liveRecorder.onstop = async () => {
                console.log("Enregistrement live terminé. Chunks collectés:", liveChunks.length);
                if (liveChunks.length === 0) {
                    console.error("Aucune donnée audio capturée !");
                    return;
                }
                
                const audioBlob = new Blob(liveChunks, { type: 'audio/webm' });
                console.log("Blob final généré, taille:", audioBlob.size);
                
                const formData = new FormData();
                formData.append('audio', audioBlob, 'live_contribution.webm');
                formData.append('pseudo', call.metadata?.name || 'Participant');
                formData.append('_token', '{{ csrf_token() }}');

                try {
                    const response = await fetch(`/e/${eventCode}/save-live-audio`, {
                        method: 'POST',
                        body: formData
                    });
                    const resData = await response.json();
                    console.log("Intervention live sauvegardée !", resData);
                } catch (err) {
                    console.error("Erreur sauvegarde live audio:", err);
                } finally {
                    hiddenAudio.remove(); // Nettoyage
                }
            };

            liveRecorder.start(1000); // Collecter des chunks toutes les secondes
            isRecordingLive = true;
        });

        call.on('close', () => {
            if (liveRecorder && liveRecorder.state !== 'inactive') {
                liveRecorder.stop();
            }
            isRecordingLive = false;
        });

        call.on('error', (err) => {
            console.error("Erreur appel PeerJS:", err);
            if (liveRecorder && liveRecorder.state !== 'inactive') liveRecorder.stop();
        });
    });

    // --- Gestion des onglets ---
    window.switchTab = function(tab) {
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
    };

    // --- Fonctions existantes ---
    window.openEditModal = function(id, content) {
        isEditing = true;
        const modal = document.getElementById('edit-modal');
        const form = document.getElementById('edit-form');
        const textarea = document.getElementById('edit-content');
        
        form.action = `/dashboard/question/${id}/edit`;
        textarea.value = content;
        modal.style.display = 'flex';
    };

    window.openSettingsModal = function() {
        document.getElementById('settings-modal').style.display = 'flex';
    };

    window.openCloseModal = function() {
        document.getElementById('close-event-modal').style.display = 'flex';
    };

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
        const isAudioPlaying = Array.from(document.querySelectorAll('audio')).some(audio => !audio.paused && !audio.ended);
        const focusedTextarea = document.querySelector('textarea:focus');
        
        if (isEditing || isRecording || isAudioPlaying || focusedTextarea) return; 

        try {
            const response = await fetch(`/dashboard/${eventId}/moderator/questions-fetch`);
            const data = await response.json();
            
            // Mise à jour des containers
            document.getElementById('questions-container-active').innerHTML = data.main_html;
            document.getElementById('questions-container-filtered').innerHTML = data.filtered_html;
            document.getElementById('panelists-container').innerHTML = data.panelists_html;
            document.getElementById('hands-container').innerHTML = data.hands_html;
            
            // Relancer les timers après mise à jour HTML
            initModeratorTimers();

            // Mise à jour des badges d'onglets
            document.getElementById('tab-btn-active').innerHTML = `🎯 Flux Actif <span class="badge" style="margin-left: 5px; background: var(--brand); color:#fff; border-radius: 50%; min-width: 20px; height: 20px; display: inline-flex; align-items: center; justify-content: center;">${data.counts.active}</span>`;
            document.getElementById('tab-btn-filtered').innerHTML = `🤖 Filtré par IA <span class="badge" style="margin-left: 5px; background: #6b7280; color:#fff; border-radius: 50%; min-width: 20px; height: 20px; display: inline-flex; align-items: center; justify-content: center;">${data.counts.filtered}</span>`;
            
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

    // --- Logique Audio Premium (Style WhatsApp) ---
    window.toggleVocalRecording = async function(qId) {
        if (!isRecording) {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                mediaRecorder = new MediaRecorder(stream);
                audioChunks = [];
                isRecording = true;
                
                mediaRecorder.ondataavailable = e => audioChunks.push(e.data);
                mediaRecorder.onstop = () => {
                    if (!isRecording) return; // Annulé

                    const audioBlob = new Blob(audioChunks, { type: 'audio/webm' });
                    const audioFile = new File([audioBlob], "recording.webm", { type: 'audio/webm' });
                    
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(audioFile);
                    
                    const form = document.getElementById('reply-form-' + qId);
                    let fileInput = form.querySelector('input[type="file"]');
                    if (!fileInput) {
                        fileInput = document.createElement('input');
                        fileInput.type = 'file';
                        fileInput.name = 'audio';
                        fileInput.style.display = 'none';
                        form.appendChild(fileInput);
                    }
                    fileInput.files = dataTransfer.files;
                    form.submit();
                };

                mediaRecorder.start();
                startVocalUI(qId);
            } catch (err) {
                alert("Microphone inaccessible.");
            }
        } else {
            stopVocalRecording();
        }
    };

    function startVocalUI(qId) {
        const preview = document.getElementById('vocal-preview-' + qId);
        const timerSpan = document.getElementById('vocal-timer-' + qId);
        const btn = document.getElementById('btn-vocal-' + qId);
        
        preview.style.display = 'flex';
        btn.style.background = 'var(--brand)';
        btn.style.color = '#fff';
        btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 1.5rem; height: 1.5rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>';
        
        seconds = 0;
        recordTimer = setInterval(() => {
            seconds++;
            const mins = String(Math.floor(seconds / 60)).padStart(2, '0');
            const secs = String(seconds % 60).padStart(2, '0');
            timerSpan.textContent = `${mins}:${secs}`;
        }, 1000);
    }

    function stopVocalRecording() {
        if (mediaRecorder && mediaRecorder.state !== 'inactive') {
            mediaRecorder.stop();
            const tracks = mediaRecorder.stream.getTracks();
            tracks.forEach(t => t.stop());
            clearInterval(recordTimer);
        }
    }

    window.cancelVocal = function(qId) {
        isRecording = false; 
        stopVocalRecording();
        const preview = document.getElementById('vocal-preview-' + qId);
        const btn = document.getElementById('btn-vocal-' + qId);
        
        preview.style.display = 'none';
        btn.style.background = '#f1f5f9';
        btn.style.color = '#475569';
        btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 1.5rem; height: 1.5rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18.75a6 6 0 006-6v-1.5m-6 7.5a6 6 0 01-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15.75a3 3 0 01-3-3V4.5a3 3 0 116 0v8.25a3 3 0 01-3 3z" /></svg>';
    };

    // --- Gestion Chronos Modérateur ---
    function initModeratorTimers() {
        document.querySelectorAll('.moderator-timer-box').forEach(box => {
            const display = box.querySelector('.timer-display');
            if (box.dataset.intervalId) clearInterval(box.dataset.intervalId);

            let remaining = parseInt(box.dataset.remaining);
            const interval = setInterval(() => {
                if (remaining <= 0) {
                    display.textContent = "00:00";
                    clearInterval(interval);
                    return;
                }
                remaining--;
                box.dataset.remaining = remaining;
                const mins = String(Math.floor(remaining / 60)).padStart(2, '0');
                const secs = String(remaining % 60).padStart(2, '0');
                display.textContent = `${mins}:${secs}`;
            }, 1000);
            box.dataset.intervalId = interval;
        });
    }

    setInterval(fetchParticipants, 5000);
    setInterval(fetchQuestions, 5000); 
    fetchParticipants();
    fetchQuestions();
</script>

<style>
.active-tab {
    border-bottom: 2px solid var(--brand) !important;
    color: var(--foreground) !important;
    font-weight: 950 !important;
}
.tab-btn {
    transition: all 0.2s;
    font-weight: 600;
    color: var(--muted-foreground);
    border-bottom: 2px solid transparent;
    padding: 0.75rem 1.25rem !important;
}
.tab-btn:hover {
    background: rgba(0,0,0,0.02);
    border-radius: 0.75rem 0.75rem 0 0;
}
@keyframes voice-pulse {
    from { transform: scaleY(1); opacity: 0.5; }
    to { transform: scaleY(2); opacity: 1; }
}
</style>
@endpush
@endsection
