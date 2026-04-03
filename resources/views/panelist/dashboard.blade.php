@extends('layouts.dashboard')

@section('title', 'Console Panéliste - ' . $event->name)

@section('content')

<div class="space-y-5">
    <div class="page-header">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1 style="margin-top: 0.5rem;">Console Panéliste : {{ $event->name }}</h1>
                <p>Bienvenue, <strong>{{ Auth::user()->name }}</strong> ({{ $panelist->sector }})</p>
            </div>
            <div style="display: flex; gap: 0.75rem; align-items: center;">
                @if($panelist->presentation_started_at)
                    @php
                        $startTime = \Carbon\Carbon::parse($panelist->presentation_started_at);
                        $totalDurationSeconds = $panelist->presentation_duration * 60;
                        $elapsedSeconds = now()->diffInSeconds($startTime);
                        $remainingSeconds = max(0, $totalDurationSeconds - $elapsedSeconds);
                        $isLowTime = $remainingSeconds <= 300; // Moins de 5 minutes
                    @endphp
                    <div id="live-timer-box" data-remaining="{{ $remainingSeconds }}" style="background: {{ $isLowTime ? '#fee2e2' : 'var(--brand-light)' }}; border: 2px solid {{ $isLowTime ? '#dc2626' : 'var(--brand)' }}; padding: 0.5rem 1rem; border-radius: 0.75rem; display: flex; align-items: center; gap: 0.5rem; transition: all 0.3s; {{ $isLowTime ? 'animation: pulse 1s infinite;' : '' }}">
                        <span style="font-size: 1.25rem;">⏱️</span>
                        <div style="text-align: right;">
                            <p style="font-size: 0.625rem; font-weight: 700; color: {{ $isLowTime ? '#dc2626' : 'var(--brand)' }}; margin: 0; text-transform: uppercase;">Temps restant</p>
                            <p id="timer-display" style="font-size: 1.25rem; font-weight: 900; color: {{ $isLowTime ? '#dc2626' : 'var(--brand)' }}; line-height: 1; font-family: monospace;">
                                {{ sprintf('%02d:%02d', floor($remainingSeconds / 60), $remainingSeconds % 60) }}
                            </p>
                        </div>
                    </div>
                @endif

                <button class="btn-brand" onclick="document.getElementById('upload-modal').style.display='flex'">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:1.25rem;height:1.25rem;margin-right:0.5rem;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                    </svg>
                    Uploader PowerPoint/Notes
                </button>
            </div>
        </div>
    </div>

    @if($panelist->presentation_path)
        <div style="background: #fff; border: 1px solid var(--brand-soft); padding: 1.25rem; border-radius: 1.5rem; margin-bottom: 2rem; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05);">
            <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="width: 3.5rem; height: 3.5rem; background: var(--brand-light); border-radius: 1rem; display: flex; align-items: center; justify-content: center; font-size: 1.75rem;">
                        {{ in_array(strtolower(pathinfo($panelist->presentation_path, PATHINFO_EXTENSION)), ['pdf']) ? '📕' : '📊' }}
                    </div>
                    <div>
                        <p style="font-weight: 800; font-size: 1.125rem; margin: 0;">{{ $panelist->is_projecting ? 'Projection en cours...' : 'Document prêt' }}</p>
                        <p style="font-size: 0.8125rem; color: var(--muted-foreground); margin: 0;">{{ $panelist->is_projecting ? 'Vous contrôlez l\'écran géant' : 'Prêt pour l\'analyse IA' }}</p>
                    </div>
                </div>
                
                <div style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center;">
                    @if($panelist->is_projecting)
                        <div style="display: flex; align-items: center; gap: 0.5rem; background: #f1f5f9; padding: 0.4rem; border-radius: 1rem; border: 1px solid #e2e8f0; margin-right: 0.5rem;">
                            <button onclick="changePage(-1)" style="width: 2.5rem; height: 2.5rem; border-radius: 0.75rem; border: none; background: #fff; cursor: pointer; font-weight: 800; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">◀</button>
                            <div style="padding: 0 1rem; text-align: center;">
                                <div style="font-size: 0.6rem; font-weight: 800; color: #94a3b8; text-transform: uppercase;">PAGE</div>
                                <div id="current-page-display" style="font-size: 1rem; font-weight: 900; color: var(--brand);">{{ $panelist->current_page }}</div>
                            </div>
                            <button onclick="changePage(1)" style="width: 2.5rem; height: 2.5rem; border-radius: 0.75rem; border: none; background: #fff; cursor: pointer; font-weight: 800; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">▶</button>
                        </div>
                    @endif

                    <button class="btn-brand" style="width: auto; padding: 0.6rem 1.25rem; font-size: 0.85rem; background: #f8fafc; color: var(--foreground); border: 1px solid #e2e8f0; font-weight: 700;" onclick="document.getElementById('view-doc-modal').style.display='flex'">
                        👁️ Aperçu
                    </button>

                    <form action="{{ route('panelist.toggle-project', $event->code) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-brand" style="width: auto; padding: 0.6rem 1.25rem; font-size: 0.85rem; background: {{ $panelist->is_projecting ? '#ef4444' : 'var(--brand)' }}; color: #fff; border: none; font-weight: 700; box-shadow: 0 4px 12px {{ $panelist->is_projecting ? '#fee2e2' : 'var(--brand-soft)' }};">
                            {{ $panelist->is_projecting ? '⏹️ Arrêter' : '📺 Diffuser' }}
                        </button>
                    </form>

                    <form action="{{ route('panelist.delete-doc', $event->code) }}" method="POST" onsubmit="return confirm('Supprimer ce document ?');">
                        @csrf
                        <button type="submit" style="background: none; border: none; color: #94a3b8; cursor: pointer; padding: 0.5rem; transition: color 0.2s;" onmouseover="this.style.color='#ef4444'" onmouseout="this.style.color='#94a3b8'">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:1.25rem;height:1.25rem;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
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
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
            <!-- Liste des Questions -->
            <div class="card">
                <h2 class="section-title">Questions du Public</h2>
                <div id="questions-container" class="space-y-4" style="margin-top: 1rem;">
                    @include('panelist.partials.questions_list', ['questions' => $questions])
                </div>
            </div>

            <!-- Sidebar Panéliste -->
            <div class="space-y-5">
                <div class="card">
                    <h2 class="section-title">Thème de l'événement</h2>
                    <p style="font-size: 0.875rem; color: var(--muted-foreground); line-height: 1.6;">
                        {{ $event->description ?? 'Aucune description fournie.' }}
                    </p>
                </div>
                
                <div class="card">
                    <h2 class="section-title">Actions Rapides</h2>
                    <div style="display: grid; gap: 0.75rem;">
                        <a href="{{ route('projection.index', $event->code) }}" target="_blank" class="btn-brand" style="background: var(--muted); color: var(--foreground); text-align: center;">
                            Voir la Projection
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Onglet Filtré par l'IA --}}
    <div id="tab-content-filtered" class="tab-content" style="display:none;">
        <div id="questions-container-filtered">
            @include('panelist.partials.filtered_list', ['filteredByAI' => $filteredByAI])
        </div>
    </div>
</div>

<!-- Modal View Document -->
<div id="view-doc-modal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 1000;">
    <div class="card" style="max-width: 50rem; width: 90%; max-height: 80vh; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h2 class="section-title" style="margin-bottom: 0;">Document chargé</h2>
            <button onclick="document.getElementById('view-doc-modal').style.display='none'" style="background: none; border: none; font-size: 1.5rem; cursor: pointer;">&times;</button>
        </div>
        
        <div style="background: var(--muted); padding: 1rem; border-radius: 0.75rem; height: 60vh;">
            @php
                $extension = pathinfo($panelist->presentation_path, PATHINFO_EXTENSION);
                $fileUrl = asset('storage/' . $panelist->presentation_path);
            @endphp

            @if(in_array(strtolower($extension), ['pdf']))
                <iframe src="{{ $fileUrl }}" style="width: 100%; height: 100%; border: none; border-radius: 0.5rem;"></iframe>
            @elseif(in_array(strtolower($extension), ['ppt', 'pptx']))
                <iframe src="https://view.officeapps.live.com/op/embed.aspx?src={{ urlencode($fileUrl) }}" style="width: 100%; height: 100%; border: none; border-radius: 0.5rem;"></iframe>
            @elseif(in_array(strtolower($extension), ['txt']))
                <div style="font-size: 0.875rem; line-height: 1.6; white-space: pre-wrap; height: 100%; overflow-y: auto; padding: 0.5rem;">
                    {{ $panelist->notes }}
                </div>
            @else
                <div style="display: grid; place-items: center; height: 100%; text-align: center;">
                    <div>
                        <p style="margin-bottom: 1rem;">Aperçu non disponible pour ce type de fichier ({{ $extension }}).</p>
                        <a href="{{ $fileUrl }}" target="_blank" class="btn-brand" style="width: auto; padding: 0.5rem 1.5rem;">Ouvrir le fichier</a>
                    </div>
                </div>
            @endif
        </div>
        
        <div style="margin-top: 1.5rem; display: flex; justify-content: flex-end;">
            <a href="{{ asset('storage/' . $panelist->presentation_path) }}" target="_blank" class="btn-brand" style="width: auto; padding: 0.5rem 1.5rem;">Télécharger le fichier original</a>
        </div>
    </div>
</div>

<!-- Modal Upload -->
<div id="upload-modal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 1000;">
    <div class="card" style="max-width: 30rem; width: 90%;">
        <h2 class="section-title">Uploader un document</h2>
        <p style="margin-bottom: 1.5rem; font-size: 0.875rem; color: var(--muted-foreground);">L'IA utilisera ce document pour vous proposer des réponses pertinentes.</p>
        
        <form action="{{ route('panelist.upload', $event->code) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <input type="file" name="presentation" class="form-input" required>
            </div>
            <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                <button type="button" class="btn-brand" style="background: var(--muted); color: var(--foreground);" onclick="document.getElementById('upload-modal').style.display='none'">Annuler</button>
                <button type="submit" class="btn-brand">Uploader</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function suggestAI(questionId, aiBtn) {
        const textarea = document.getElementById('ai-response-' + questionId);
        const submitBtn = document.getElementById('submit-btn-' + questionId);
        
        const originalAiText = aiBtn.innerText;
        aiBtn.disabled = true;
        aiBtn.innerText = "⌛...";
        textarea.placeholder = "L'IA réfléchit...";
        
        fetch("{{ route('panelist.ai-suggest', $event->code) }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ question_id: questionId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.suggestion) {
                // Nettoyer les éventuels # ou * restants au cas où
                let cleanText = data.suggestion.replace(/[#*]/g, '').trim();
                textarea.value = cleanText;
            } else {
                alert("L'IA n'a pas pu générer de suggestion.");
            }
            textarea.placeholder = "Votre réponse...";
        })
        .catch(error => {
            console.error('Error:', error);
            textarea.placeholder = "Votre réponse...";
        })
        .finally(() => {
            aiBtn.disabled = false;
            aiBtn.innerText = originalAiText;
        });
    }

    let mediaRecorder;
    let audioChunks = [];
    let isRecording = false;
    let voiceTimerInterval;
    let voiceSeconds = 0;
    let currentRecordingId = null;

    async function toggleVoiceRecording(questionId) {
        const btn = document.getElementById('voice-btn-' + questionId);
        const icon = document.getElementById('voice-icon-' + questionId);
        const text = document.getElementById('voice-text-' + questionId);
        const status = document.getElementById('voice-status-' + questionId);
        const timer = document.getElementById('voice-timer-' + questionId);
        const audioInput = document.getElementById('audio-input-' + questionId);

        if (!isRecording) {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                mediaRecorder = new MediaRecorder(stream);
                audioChunks = [];

                mediaRecorder.ondataavailable = (event) => {
                    audioChunks.push(event.data);
                };

                mediaRecorder.onstop = () => {
                    const audioBlob = new Blob(audioChunks, { type: 'audio/webm' });
                    const file = new File([audioBlob], "vocal_reply.webm", { type: 'audio/webm' });
                    
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    audioInput.files = dataTransfer.files;
                    
                    if (window.onRecordingStopped) {
                        window.onRecordingStopped();
                        window.onRecordingStopped = null;
                    }
                };

                mediaRecorder.start();
                isRecording = true;
                currentRecordingId = questionId;
                btn.style.background = '#dc2626';
                btn.style.color = '#fff';
                icon.textContent = '⏹';
                text.textContent = 'Stop';
                status.style.display = 'flex';
                
                voiceSeconds = 0;
                timer.textContent = '0s';
                voiceTimerInterval = setInterval(() => {
                    voiceSeconds++;
                    timer.textContent = voiceSeconds + 's';
                }, 1000);

            } catch (err) {
                console.error("Microphone error:", err);
                alert("Erreur micro. Vérifiez les permissions.");
            }
        } else {
            if (currentRecordingId === questionId) {
                mediaRecorder.stop();
                stopRecordingUI(questionId);
            }
        }
    }

    function stopRecordingUI(id) {
        isRecording = false;
        currentRecordingId = null;
        const b = document.getElementById('voice-btn-' + id);
        const i = document.getElementById('voice-icon-' + id);
        const t = document.getElementById('voice-text-' + id);
        const s = document.getElementById('voice-status-' + id);
        
        if (b) {
            b.style.background = '#f3f4f6';
            b.style.color = '#374151';
        }
        if (i) i.textContent = '🎤';
        if (t) t.textContent = 'Vocal';
        if (s) s.style.display = 'none';
        clearInterval(voiceTimerInterval);
        if (mediaRecorder && mediaRecorder.stream) {
            mediaRecorder.stream.getTracks().forEach(track => track.stop());
        }
    }

    // Gérer l'envoi auto pour tous les formulaires de réponse
    document.querySelectorAll('form[action*="reply"]').forEach(form => {
        form.addEventListener('submit', function(e) {
            console.log("Form submission detected for action:", form.action);
            if (isRecording && currentRecordingId) {
                console.log("Recording in progress, stopping before submit...");
                e.preventDefault();
                const currentForm = this;
                const recordingId = currentRecordingId;
                window.onRecordingStopped = () => {
                    console.log("Recording stopped, submitting form now.");
                    currentForm.submit();
                };
                mediaRecorder.stop();
                stopRecordingUI(recordingId);
            } else {
                console.log("No recording in progress, submitting normally.");
            }
        });
    });

    // --- TEMPS RÉEL : Polling Panéliste ---
    let isInteracting = false;
    const eventId = '{{ $event->id }}';

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

    async function fetchQuestions() {
        // Détecter si un vocal est en cours d'écoute
        const isAudioPlaying = Array.from(document.querySelectorAll('audio')).some(audio => !audio.paused && !audio.ended);
        
        // Détecter si un modal est ouvert
        const isModalOpen = document.getElementById('upload-modal').style.display === 'flex' || 
                           document.getElementById('view-doc-modal').style.display === 'flex';
        
        // Détecter si on est en train de taper dans un textarea
        const activeElement = document.activeElement;
        const isTyping = activeElement && (activeElement.tagName === 'TEXTAREA' || activeElement.tagName === 'INPUT');

        if (isInteracting || isModalOpen || isTyping || isRecording || isAudioPlaying) return;

        try {
            const response = await fetch(`/dashboard/${eventId}/panelist/questions-fetch`);
            const data = await response.json();
            
            // Mise à jour des containers
            if (document.getElementById('questions-container')) {
                document.getElementById('questions-container').innerHTML = data.main_html;
            }
            if (document.getElementById('questions-container-filtered')) {
                document.getElementById('questions-container-filtered').innerHTML = data.filtered_html;
            }

            // Mise à jour des badges d'onglets
            document.getElementById('tab-btn-active').textContent = `🎯 Flux Actif (${data.counts.active})`;
            document.getElementById('tab-btn-filtered').textContent = `🤖 Filtrées par l'IA (${data.counts.filtered})`;

        } catch (e) {
            console.error("Polling error:", e);
        }
    }

    // Lancer le polling
    setInterval(fetchQuestions, 5000);
    // --- Gestion Télécommande Projection ---
    let currentPage = {{ $panelist->current_page ?? 1 }};
    
    function changePage(delta) {
        currentPage = Math.max(1, currentPage + delta);
        const display = document.getElementById('current-page-display');
        if (display) display.textContent = currentPage;
        
        // Sync with server
        fetch("{{ route('panelist.sync-page', $event->code) }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ page: currentPage })
        })
        .catch(err => console.error("Sync error:", err));
    }

    // --- Gestion Chrono Live ---
    function initLiveTimer() {
        const timerBox = document.getElementById('live-timer-box');
        const display = document.getElementById('timer-display');
        if (!timerBox || !display) return;

        let remaining = parseInt(timerBox.getAttribute('data-remaining'));
        
        const interval = setInterval(() => {
            if (remaining <= 0) {
                display.textContent = "00:00";
                timerBox.style.background = "#fee2e2";
                timerBox.style.borderColor = "#dc2626";
                clearInterval(interval);
                return;
            }
            
            remaining--;
            const mins = String(Math.floor(remaining / 60)).padStart(2, '0');
            const secs = String(remaining % 60).padStart(2, '0');
            display.textContent = `${mins}:${secs}`;
            
            if (remaining <= 300) { // Alerte 5 min
                timerBox.style.background = "#fee2e2";
                timerBox.style.borderColor = "#dc2626";
                timerBox.style.animation = "pulse 1s infinite";
                display.style.color = "#dc2626";
            }
        }, 1000);
    }

    initLiveTimer();
</script>

<style>
.active-tab {
    border-bottom: 2px solid var(--brand) !important;
    color: var(--foreground) !important;
}
@keyframes pulse {
    0% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.1); opacity: 0.7; }
    100% { transform: scale(1); opacity: 1; }
}
</style>
@endpush
@endsection
