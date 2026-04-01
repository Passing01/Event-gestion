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
            <div style="display: flex; gap: 0.75rem;">
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
        <div style="background: #ecfdf5; border: 1px solid #10b981; color: #065f46; padding: 1rem; border-radius: 0.75rem; display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; margin-bottom: 1.5rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:1.5rem;height:1.5rem;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <p style="font-weight: 600;">Document chargé</p>
                    <p style="font-size: 0.875rem;">L'IA analyse votre document pour vous assister dans les réponses.</p>
                </div>
            </div>
            <button class="btn-brand" style="width: auto; padding: 0.5rem 1rem; font-size: 0.75rem; background: #fff; color: #065f46; border: 1px solid #10b981;" onclick="document.getElementById('view-doc-modal').style.display='flex'">
                Voir le document
            </button>
        </div>
    @endif

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
        <!-- Liste des Questions -->
        <div class="card">
            <h2 class="section-title">Questions du Public</h2>
            <div id="questions-list" class="space-y-4" style="margin-top: 1rem;">
                @forelse($questions as $question)
                    <div class="question-card" style="background: var(--muted); padding: 1.25rem; border-radius: 1rem; border: 1px solid var(--border);">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem;">
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <div style="width: 2rem; height: 2rem; background: var(--brand); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.75rem;">
                                    {{ substr($question->pseudo ?? 'A', 0, 1) }}
                                </div>
                                <div>
                                    <p style="font-weight: 600; font-size: 0.875rem;">{{ $question->pseudo ?? 'Anonyme' }}</p>
                                    <p style="font-size: 0.75rem; color: var(--muted-foreground);">{{ $question->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            <div style="display: flex; gap: 0.5rem; align-items: center;">
                                @if($question->status === 'pending')
                                    <form action="{{ route('dashboard.moderator.status', $question->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" class="btn-brand" style="padding: 0.25rem 0.75rem; font-size: 0.75rem; background: #10b981;">
                                            Approuver
                                        </button>
                                    </form>
                                    <form action="{{ route('dashboard.moderator.status', $question->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit" class="btn-brand" style="padding: 0.25rem 0.75rem; font-size: 0.75rem; background: #ef4444;">
                                            Rejeter
                                        </button>
                                    </form>
                                @endif
                                <button class="btn-brand" style="padding: 0.25rem 0.75rem; font-size: 0.75rem;" onclick="suggestAI('{{ $question->id }}')">
                                    💡 Suggestion IA
                                </button>
                                <span class="badge" style="font-size: 0.625rem; padding: 0.25rem 0.5rem; border-radius: 9999px; background: {{ $question->status === 'pending' ? '#f59e0b' : ($question->status === 'approved' ? '#10b981' : ($question->status === 'answering' ? '#3b82f6' : '#6b7280')) }}; color: white;">
                                    {{ strtoupper($question->status) }}
                                </span>
                            </div>
                        </div>
                        <p style="font-size: 1rem; line-height: 1.5; margin-bottom: 1rem;">{{ $question->content }}</p>
                        
                        @if($question->audio_path)
                            <div style="margin-bottom: 1rem;">
                                <audio controls style="height: 30px; max-width: 100%;">
                                    <source src="{{ asset('storage/' . $question->audio_path) }}" type="audio/webm">
                                    Votre navigateur ne supporte pas l'élément audio.
                                </audio>
                            </div>
                        @endif
                        
                        <!-- Réponses existantes -->
                        <div id="replies-{{ $question->id }}" class="space-y-2" style="margin-left: 1rem; border-left: 2px solid var(--border); padding-left: 1rem;">
                            @foreach($question->replies as $reply)
                                <div style="font-size: 0.875rem; background: white; padding: 0.75rem; border-radius: 0.5rem;">
                                    <p style="font-weight: 600;">{{ $reply->user->name ?? 'Panéliste' }}</p>
                                    <p>{{ $reply->content }}</p>
                                    @if($reply->audio_path)
                                        <div style="margin-top: 0.5rem;">
                                            <audio controls style="height: 25px; max-width: 100%;">
                                                <source src="{{ asset('storage/' . $reply->audio_path) }}" type="audio/webm">
                                            </audio>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <!-- Formulaire de réponse -->
                        <form action="{{ route('dashboard.moderator.reply', $question->id) }}" method="POST" enctype="multipart/form-data" style="margin-top: 1rem;">
                            @csrf
                            <div style="display: flex; gap: 0.5rem; flex-direction: column;">
                                <textarea name="content" id="ai-response-{{ $question->id }}" class="form-input" rows="2" placeholder="Votre réponse..." style="font-size: 0.875rem;"></textarea>
                                <input type="file" name="audio" id="audio-input-{{ $question->id }}" style="display: none;">
                                
                                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
                                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                                        <div id="voice-status-{{ $question->id }}" style="display: none; align-items: center; gap: 0.5rem; background: #fee2e2; color: #dc2626; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600;">
                                            <span style="width: 0.4rem; height: 0.4rem; background: #dc2626; border-radius: 50%; animation: pulse 1s infinite;"></span>
                                            <span id="voice-timer-{{ $question->id }}">0s</span>
                                        </div>
                                        <button type="button" id="voice-btn-{{ $question->id }}" class="btn-brand" style="background: #f3f4f6; color: #374151; width: auto; padding: 0.25rem 0.75rem; font-size: 0.75rem; display: flex; align-items: center; gap: 0.375rem;" onclick="toggleVoiceRecording('{{ $question->id }}')">
                                            <span id="voice-icon-{{ $question->id }}">🎤</span> <span id="voice-text-{{ $question->id }}">Vocal</span>
                                        </button>
                                    </div>
                                    <button type="submit" class="btn-brand" style="width: auto; padding: 0.4rem 1.5rem; font-size: 0.875rem;">Répondre</button>
                                </div>
                            </div>
                        </form>
                    </div>
                @empty
                    <p style="text-align: center; color: var(--muted-foreground); padding: 2rem;">Aucune question pour le moment.</p>
                @endforelse
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
    function suggestAI(questionId) {
        const textarea = document.getElementById('ai-response-' + questionId);
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
                textarea.value = data.suggestion;
            } else {
                alert("L'IA n'a pas pu générer de suggestion.");
            }
            textarea.placeholder = "Votre réponse...";
        })
        .catch(error => {
            console.error('Error:', error);
            textarea.placeholder = "Votre réponse...";
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
            if (isRecording && currentRecordingId) {
                e.preventDefault();
                const currentForm = this;
                const recordingId = currentRecordingId;
                window.onRecordingStopped = () => {
                    currentForm.submit();
                };
                mediaRecorder.stop();
                stopRecordingUI(recordingId);
            }
        });
    });
</script>
@endpush

@endsection
