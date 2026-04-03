@extends('layouts.auth')

@section('title', $event->name . ' – Q&A')

@section('content')

<main class="auth-page" style="padding: 1rem; background: var(--muted);">
    <div class="auth-card" style="max-width: 40rem; width: 100%; padding: 1.5rem;">

        {{-- Header --}}
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem;">
            <div>
                <h1 style="font-size: 1.25rem; margin-bottom: 0.25rem;">{{ $event->name }}</h1>
                <p style="font-size: 0.75rem; color: var(--muted-foreground);">Connecté en tant que <strong>{{ session('participant_pseudo') }}</strong></p>
            </div>
            <div style="background: var(--brand); color: #fff; padding: 0.25rem 0.75rem; border-radius: 0.5rem; font-size: 0.75rem; font-weight: 600;">
                {{ $event->code }}
            </div>
        </div>

               {{-- Panelists Display --}}
        @if($panelists->count() > 0)
        <div style="margin-bottom: 1.5rem;">
            <p style="font-size: 0.75rem; font-weight: 700; color: var(--muted-foreground); text-transform: uppercase; margin-bottom: 0.75rem; letter-spacing: 0.05em;">Panel d'experts</p>
            <div style="display: flex; flex-wrap: wrap; gap: 0.75rem;">
                @foreach($panelists as $p)
                <div style="background: #fff; border: 1px solid var(--border); padding: 0.5rem 1rem; border-radius: 1rem; display: flex; align-items: center; gap: 0.75rem; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                    <div style="width: 2rem; height: 2rem; background: var(--brand-light); color: var(--brand); border-radius: 50%; display: grid; place-items: center; font-weight: 700; font-size: 0.875rem;">
                        {{ substr($p->pseudo, 0, 1) }}
                    </div>
                    <div>
                        <p style="font-size: 0.875rem; font-weight: 600; margin: 0; line-height: 1;">{{ $p->pseudo }}</p>
                        <p style="font-size: 0.625rem; color: var(--muted-foreground); margin: 0.25rem 0 0 0;">Panéliste</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
        @php
            $myHand = $event->raisedHands()->where('pseudo', session('participant_pseudo'))->first();
            $rank = $myHand ? $event->raisedHands()->where('status', 'pending')->where('created_at', '<', $myHand->created_at)->count() + 1 : null;
        @endphp

        <div style="background: var(--muted); padding: 1rem; border-radius: 0.75rem; display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div style="width: 2.5rem; height: 2.5rem; background: {{ $myHand ? 'var(--brand)' : '#fff' }}; color: {{ $myHand ? '#fff' : 'var(--brand)' }}; border-radius: 50%; display: grid; place-items: center; font-size: 1.25rem; transition: all 0.3s;">
                    ✋
                </div>
                <div>
                    @if($myHand)
                        <p style="font-size: 0.875rem; font-weight: 700; margin: 0;">Main levée !</p>
                        <p style="font-size: 0.75rem; color: var(--muted-foreground); margin: 0;">
                            @if($myHand->status == 'called')
                                <span style="color: var(--brand); font-weight: 800;">C'est à vous de parler !</span>
                            @else
                                Votre rang dans la file : <strong>#{{ $rank }}</strong>
                            @endif
                        </p>
                    @else
                        <p style="font-size: 0.875rem; font-weight: 700; margin: 0;">Une question à l'oral ?</p>
                        <p style="font-size: 0.75rem; color: var(--muted-foreground); margin: 0;">Signalez-vous au modérateur.</p>
                    @endif
                </div>
            </div>
            
            @if($myHand)
                <form action="{{ route('participant.lower-hand', $event->code) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-brand" style="background: #fff; color: var(--foreground); border: 1px solid var(--border); width: auto; padding: 0.5rem 1rem; font-size: 0.75rem;">Baisser la main</button>
                </form>
            @else
                <form action="{{ route('participant.raise-hand', $event->code) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-brand" style="width: auto; padding: 0.5rem 1rem; font-size: 0.75rem;">Lever la main</button>
                </form>
            @endif
        </div>

        @if(session('success'))
            <div style="background:#ecfdf5;border:1px solid #a7f3d0;color:#059669;border-radius:0.5rem;padding:0.75rem;margin-bottom:1rem;font-size:0.875rem;">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div style="background:#fef2f2;border:1px solid #fecaca;color:#dc2626;border-radius:0.5rem;padding:0.75rem;margin-bottom:1rem;font-size:0.875rem;">
                {{ session('error') }}
            </div>
        @endif

        {{-- Input Section --}}
        <div class="card" style="margin-bottom: 2rem; padding: 1.25rem; border: 1px solid var(--brand); position: relative;">
            <h2 style="font-size: 0.875rem; font-weight: 600; margin-bottom: 0.75rem;">Posez votre question</h2>
            <form id="question-form" action="{{ route('participant.ask', $event->code) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div style="display: flex; gap: 0.75rem; margin-bottom: 0.75rem;">
                    <div style="flex: 1;">
                        <label style="font-size: 0.7rem; font-weight: 700; color: var(--muted-foreground); display: block; margin-bottom: 0.25rem;">TYPE</label>
                        <select name="type" class="form-input" style="font-size: 0.875rem; padding: 0.5rem;">
                            <option value="question">❓ Question</option>
                            <option value="contribution">💡 Apport / Témoignage</option>
                        </select>
                    </div>
                    <div style="flex: 1;">
                        <label style="font-size: 0.7rem; font-weight: 700; color: var(--muted-foreground); display: block; margin-bottom: 0.25rem;">ADRESSÉ À (Optionnel)</label>
                        <select name="panelist_id" class="form-input" style="font-size: 0.875rem; padding: 0.5rem;">
                            <option value="">Tout le panel</option>
                            @foreach($panelists as $p)
                                <option value="{{ $p->id }}">@ {{ $p->pseudo }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <textarea name="content" id="question-content" class="form-input" rows="3" placeholder="Votre message ici..." style="resize: none; margin-bottom: 0.75rem;" maxlength="5000">{{ old('content') }}</textarea>
                <input type="file" name="audio" id="audio-input" style="display: none;">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem;">
                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                        <div id="voice-status" style="display: none; align-items: center; gap: 0.5rem; background: #fee2e2; color: #dc2626; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600;">
                            <span style="width: 0.5rem; height: 0.5rem; background: #dc2626; border-radius: 50%; animation: pulse 1s infinite;"></span>
                            <span id="voice-timer">0s</span>
                        </div>
                        <button type="button" id="voice-btn" class="btn-brand" style="background: #f3f4f6; color: #374151; width: auto; padding: 0.5rem 1rem; font-size: 0.75rem; display: flex; align-items: center; gap: 0.375rem;" onclick="toggleVoiceRecording()">
                            <span id="voice-icon">🎤</span> <span id="voice-text">Vocal</span>
                        </button>
                        <span style="font-size: 0.75rem; color: var(--muted-foreground);">Max 5000 car.</span>
                    </div>
                    <button type="submit" class="btn-brand" style="width: auto; padding: 0.5rem 1.5rem;">Envoyer</button>
                </div>
            </form>
        </div>

        {{-- Questions List --}}
        <div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h2 style="font-size: 1rem; font-weight: 600;">Questions du public</h2>
                <span id="questions-count-badge" style="font-size: 0.75rem; color: var(--muted-foreground);">{{ $questions->count() }} questions visibles</span>
            </div>

            <div id="questions-container">
                @include('participant.partials.questions_list', ['questions' => $questions])
            </div>
        </div>

        {{-- Participants List --}}
        <div style="border-top: 1px solid var(--border); padding-top: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h2 style="font-size: 1rem; font-weight: 600;">Participants connectés</h2>
                <span id="participant-count" style="font-size: 0.75rem; color: var(--muted-foreground);">0 en ligne</span>
            </div>
            <div id="participants-list" style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                <!-- Rempli par JS -->
            </div>
        </div>

        <div style="margin-top: 2rem; text-align: center;">
            <a href="{{ route('participant.join') }}" style="font-size: 0.75rem; color: var(--muted-foreground); text-decoration: none;">Quitter l'événement</a>
        </div>
    </div>
</main>

<script>
    // --- Heartbeat & Typing ---
    const eventCode = '{{ $event->code }}';
    const csrfToken = '{{ csrf_token() }}';

    async function sendHeartbeat() {
        try {
            await fetch(`/e/${eventCode}/heartbeat`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken }
            });
        } catch (e) {}
    }

    let typingTimer;
    async function setTyping(isTyping) {
        try {
            await fetch(`/e/${eventCode}/typing`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
                body: JSON.stringify({ is_typing: isTyping })
            });
        } catch (e) {}
    }

    document.querySelector('textarea[name="content"]')?.addEventListener('input', () => {
        clearTimeout(typingTimer);
        setTyping(true);
        typingTimer = setTimeout(() => setTyping(false), 3000);
    });

    // --- Active Participants List ---
    async function fetchParticipants() {
        try {
            const response = await fetch(`/e/${eventCode}/active-participants`);
            const data = await response.json();
            
            const list = document.getElementById('participants-list');
            const count = document.getElementById('participant-count');
            
            count.textContent = `${data.length} en ligne`;
            
            list.innerHTML = data.map(p => `
                <div style="background: #fff; border: 1px solid var(--border); padding: 0.375rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; display: flex; align-items: center; gap: 0.5rem; ${p.is_speaking ? 'border-color: var(--brand); background: var(--brand-light);' : ''}">
                    <span style="width: 0.5rem; height: 0.5rem; background: ${p.is_speaking ? 'var(--brand)' : '#10b981'}; border-radius: 50%;"></span>
                    <span style="font-weight: 600;">${p.pseudo}</span>
                    ${p.is_typing ? '<span class="typing-dot">...</span>' : ''}
                    ${p.is_speaking ? '🎤' : ''}
                </div>
            `).join('');
        } catch (e) {}
    }

    let mediaRecorder;
    let audioChunks = [];
    let isRecording = false;
    let voiceTimerInterval;
    let voiceSeconds = 0;

    async function toggleVoiceRecording() {
        const btn = document.getElementById('voice-btn');
        const icon = document.getElementById('voice-icon');
        const text = document.getElementById('voice-text');
        const status = document.getElementById('voice-status');
        const timer = document.getElementById('voice-timer');
        const audioInput = document.getElementById('audio-input');

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
                    const file = new File([audioBlob], "vocal.webm", { type: 'audio/webm' });
                    
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    audioInput.files = dataTransfer.files;
                    
                    // Si on a un callback de fin (pour l'envoi auto), on l'appelle
                    if (window.onRecordingStopped) {
                        window.onRecordingStopped();
                        window.onRecordingStopped = null;
                    }
                };

                mediaRecorder.start();
                isRecording = true;
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
            mediaRecorder.stop();
            stopRecordingUI();
        }
    }

    function stopRecordingUI() {
        const btn = document.getElementById('voice-btn');
        const icon = document.getElementById('voice-icon');
        const text = document.getElementById('voice-text');
        const status = document.getElementById('voice-status');
        
        isRecording = false;
        btn.style.background = '#f3f4f6';
        btn.style.color = '#374151';
        icon.textContent = '🎤';
        text.textContent = 'Vocal';
        status.style.display = 'none';
        clearInterval(voiceTimerInterval);
        if (mediaRecorder && mediaRecorder.stream) {
            mediaRecorder.stream.getTracks().forEach(track => track.stop());
        }
    }

    // Gérer l'envoi auto si on enregistre
    document.getElementById('question-form').addEventListener('submit', function(e) {
        if (isRecording) {
            e.preventDefault();
            const form = this;
            window.onRecordingStopped = () => {
                form.submit();
            };
            mediaRecorder.stop();
            stopRecordingUI();
        }
    });

    // --- TEMPS RÉEL : Questions ---
    async function fetchQuestions() {
        // Bloquer si on enregistre un vocal ou si un formulaire est ouvert/focus
        const activeElement = document.activeElement;
        const isTyping = activeElement && (activeElement.tagName === 'TEXTAREA' || activeElement.tagName === 'INPUT');
        
        // On vérifie aussi si un formulaire de réponse est affiché (pour ne pas le fermer)
        const isAnyReplyFormOpen = Array.from(document.querySelectorAll('form[id^="reply-form-"]')).some(f => f.style.display === 'flex');

        if (isTyping || isRecording || isAnyReplyFormOpen) return;

        try {
            const response = await fetch(`/e/${eventCode}/participant/questions-fetch`);
            const data = await response.json();
            
            if (document.getElementById('questions-container')) {
                document.getElementById('questions-container').innerHTML = data.html;
            }
            if (document.getElementById('questions-count-badge')) {
                document.getElementById('questions-count-badge').textContent = `${data.count} questions visibles`;
            }
        } catch (e) {
            console.error("Polling error:", e);
        }
    }

    function toggleReplyForm(id) {
        const form = document.getElementById('reply-form-' + id);
        if (form.style.display === 'none' || !form.style.display) {
            form.style.display = 'flex';
            form.querySelector('input').focus();
        } else {
            form.style.display = 'none';
        }
    }

    setInterval(sendHeartbeat, 15000);
    setInterval(fetchParticipants, 5000);
    setInterval(fetchQuestions, 5000);
    sendHeartbeat();
    fetchParticipants();
    fetchQuestions();
</script>

<style>
@keyframes pulse {
    0% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.5; transform: scale(1.2); }
    100% { opacity: 1; transform: scale(1); }
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

@endsection
