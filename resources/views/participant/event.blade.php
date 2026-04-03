@extends('layouts.auth')

@section('title', $event->name . ' – Q&A')

@section('content')

<main class="auth-page" style="padding: 0.5rem; background: #f1f5f9; min-height: 100vh;">
    <div class="auth-card" style="max-width: 42rem; width: 100%; padding: 1.25rem; border-radius: 1.5rem; margin-bottom: 2rem;">

        {{-- Header Responsif --}}
        <div style="display: flex; flex-direction: row; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; gap: 1rem;">
            <div style="flex: 1;">
                <h1 style="font-size: 1.125rem; font-weight: 800; margin-bottom: 0.125rem; line-height: 1.2;">{{ $event->name }}</h1>
                <p style="font-size: 0.75rem; color: var(--muted-foreground); display: flex; align-items: center; gap: 0.4rem;">
                    <span style="width: 0.5rem; height: 0.5rem; background: #10b981; border-radius: 50%;"></span>
                    En direct • {{ session('participant_pseudo') }}
                </p>
            </div>
            <div style="background: var(--brand); color: #fff; padding: 0.4rem 0.8rem; border-radius: 0.75rem; font-size: 0.75rem; font-weight: 700; white-space: nowrap; box-shadow: 0 4px 12px var(--brand-soft);">
                #{{ $event->code }}
            </div>
        </div>

        {{-- Main Levée & Statut --}}
        @php
            $myHand = $event->raisedHands()->where('pseudo', session('participant_pseudo'))->first();
            $rank = $myHand ? $event->raisedHands()->where('status', 'pending')->where('created_at', '<', $myHand->created_at)->count() + 1 : null;
        @endphp

        <div style="background: #fff; padding: 1rem; border-radius: 1rem; border: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.03);">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="width: 2.75rem; height: 2.75rem; background: {{ $myHand ? 'var(--brand)' : '#f8fafc' }}; color: {{ $myHand ? '#fff' : 'var(--brand)' }}; border-radius: 50%; display: grid; place-items: center; font-size: 1.25rem; transition: all 0.3s; border: 1px solid {{ $myHand ? 'var(--brand)' : 'var(--border)' }};">
                    ✋
                </div>
                <div>
                    @if($myHand)
                        <p style="font-size: 0.9rem; font-weight: 700; margin: 0; color: var(--foreground);">{{ $myHand->status == 'called' ? 'C\'est à vous !' : 'Main levée' }}</p>
                        <p style="font-size: 0.75rem; color: var(--muted-foreground); margin: 0;">{{ $myHand->status == 'called' ? 'Parlez, on vous écoute.' : 'Rang #' . $rank . ' dans la file' }}</p>
                    @else
                        <p style="font-size: 0.9rem; font-weight: 700; margin: 0; color: var(--foreground);">Besoin de parler ?</p>
                        <p style="font-size: 0.75rem; color: var(--muted-foreground); margin: 0;">Signalez-vous oralement.</p>
                    @endif
                </div>
            </div>
            
            @if($myHand)
                <form action="{{ route('participant.lower-hand', $event->code) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-brand" style="background: #fee2e2; color: #dc2626; border: none; width: auto; padding: 0.5rem 0.75rem; font-size: 0.75rem; border-radius: 0.5rem;">Baisser</button>
                </form>
            @else
                <form action="{{ route('participant.raise-hand', $event->code) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-brand" style="width: auto; padding: 0.5rem 0.75rem; font-size: 0.75rem; border-radius: 0.5rem;">Lever</button>
                </form>
            @endif
        </div>

        @if(session('success'))
            <div style="background:#ecfdf5;border:1px solid #a7f3d0;color:#059669;border-radius:0.75rem;padding:0.75rem;margin-bottom:1rem;font-size:0.875rem; animation: slideIn 0.3s ease;">
                {{ session('success') }}
            </div>
        @endif

        {{-- Input Section --}}
        <div class="card" style="margin-bottom: 2rem; padding: 1.25rem; border: 1px solid var(--brand-soft); box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); background: #fff;">
            <form id="question-form" action="{{ route('participant.ask', $event->code) }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                {{-- Sélecteurs Responsifs --}}
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 1rem;">
                    <div>
                        <label style="font-size: 0.65rem; font-weight: 800; color: var(--muted-foreground); display: block; margin-bottom: 0.4rem; text-transform: uppercase; letter-spacing: 0.05em;">Catégorie</label>
                        <select name="type" class="form-input" style="font-size: 0.85rem; padding: 0.6rem; border-radius: 0.75rem; height: auto; appearance: none; background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2224%22%20height%3D%2224%22%20viewBox%3D%220%200%2024%2024%22%20fill%3D%22none%22%20stroke%3D%22%2364748b%22%20stroke-width%3D%222%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%3E%3Cpolyline%20points%3D%226%209%2012%2015%2018%209%22%3E%3C%2Fpolyline%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 0.7rem center; background-size: 1rem;">
                            <option value="question">❓ Question</option>
                            <option value="contribution">💡 Apport</option>
                        </select>
                    </div>
                    <div>
                        <label style="font-size: 0.65rem; font-weight: 800; color: var(--muted-foreground); display: block; margin-bottom: 0.4rem; text-transform: uppercase; letter-spacing: 0.05em;">Adresser à</label>
                        <input type="hidden" name="panelist_id" id="panelist_id" value="">
                        <button type="button" onclick="openPanelistModal()" id="panelist-selector-btn" class="form-input" style="width:100%; font-size: 0.85rem; padding: 0.6rem; text-align: left; background: #f8fafc; border: 1px solid var(--border); border-radius: 0.75rem; height: auto; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            🎯 Tout le panel
                        </button>
                    </div>
                </div>
                
                <div style="position: relative; margin-bottom: 1rem;">
                    <textarea name="content" id="question-content" class="form-input" rows="3" placeholder="Tapez votre question ici..." style="resize: none; border-radius: 1rem; padding: 1rem; font-size: 0.95rem; border-color: var(--border);" maxlength="5000">{{ old('content') }}</textarea>
                    
                    <div id="voice-status" style="display: none; position: absolute; bottom: 0.75rem; left: 0.75rem; align-items: center; gap: 0.5rem; background: #fee2e2; color: #dc2626; padding: 0.4rem 0.8rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; z-index: 10;">
                        <span style="width: 0.5rem; height: 0.5rem; background: #dc2626; border-radius: 50%; animation: pulse 1s infinite;"></span>
                        <span id="voice-timer">0s</span>
                    </div>
                </div>

                <div id="normal-form-actions" style="display: flex; justify-content: space-between; align-items: center; gap: 1fr; width: 100%;">
                    <div style="display: flex; gap: 0.5rem;">
                        <button type="button" id="voice-btn" class="btn-brand" style="background: #f1f5f9; color: #475569; width: auto; padding: 0.6rem 1rem; font-size: 0.8rem; display: flex; align-items: center; gap: 0.5rem; border-radius: 0.75rem; border: none;" onclick="toggleVoiceRecording()">
                            <span id="voice-icon" style="font-size: 1rem;">🎤</span> <span id="voice-text">Vocal</span>
                        </button>
                    </div>
                    
                    <button type="submit" class="btn-brand" style="width: auto; padding: 0.6rem 2rem; border-radius: 0.75rem; font-weight: 700; box-shadow: 0 4px 12px var(--brand-soft);">
                        Envoyer
                    </button>
                </div>

                {{-- Barre d'enregistrement (Style WhatsApp) --}}
                <div id="recording-bar" style="display: none; align-items: center; gap: 1rem; width: 100%; background: #f8fafc; padding: 0.5rem 1rem; border-radius: 1rem; border: 1px solid #fee2e2;">
                    <div style="display: flex; align-items: center; gap: 0.75rem; flex: 1;">
                        <span style="width: 0.75rem; height: 0.75rem; background: #ef4444; border-radius: 50%; animation: pulse 1s infinite;"></span>
                        <span id="rec-timer" style="font-family: monospace; font-weight: 700; color: #ef4444; font-size: 0.9rem;">00:00</span>
                        <canvas id="waveform" style="flex: 1; height: 30px;"></canvas>
                    </div>
                    <div style="display: flex; gap: 0.5rem;">
                        <button type="button" onclick="togglePauseResume()" id="pause-btn" style="background: #fff; border: 1px solid #e2e8f0; border-radius: 50%; width: 2.25rem; height: 2.25rem; cursor: pointer;"><span id="pause-icon">⏸</span></button>
                        <button type="button" onclick="cancelRecording()" style="background: #fee2e2; border: none; color: #ef4444; border-radius: 50%; width: 2.25rem; height: 2.25rem; cursor: pointer;">✕</button>
                        <button type="button" onclick="stopRecordingAndReview()" style="background: var(--brand); border: none; color: #fff; border-radius: 50%; width: 2.25rem; height: 2.25rem; cursor: pointer;">✅</button>
                    </div>
                </div>

                {{-- Barre de Pré-écoute & Filtres --}}
                <div id="voice-preview-bar" style="display: none; flex-direction: column; gap: 0.75rem; width: 100%; background: #fdfcfe; padding: 1rem; border-radius: 1rem; border: 1px solid var(--brand-soft);">
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <audio id="preview-audio" controls style="flex: 1; height: 35px;"></audio>
                        <button type="button" onclick="resetVoice()" style="background: none; border: none; color: #94a3b8; cursor: pointer; padding: 0.5rem;">🗑️</button>
                    </div>
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem;">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span style="font-size: 0.7rem; font-weight: 800; color: var(--muted-foreground); text-transform: uppercase;">Filtre :</span>
                            <select id="voice-filter" onchange="applyVoiceFilter()" style="font-size: 0.75rem; padding: 0.25rem 0.5rem; border-radius: 0.5rem; border: 1px solid var(--border);">
                                <option value="none">Normal</option>
                                <option value="robot">🤖 Robot</option>
                                <option value="deep">🌑 Grave</option>
                                <option value="high">🐿️ Aigu</option>
                            </select>
                        </div>
                        <button type="submit" class="btn-brand" style="width: auto; padding: 0.5rem 1.5rem; font-size: 0.8rem; border-radius: 0.5rem;">Confirmer</button>
                    </div>
                </div>

                <input type="file" name="audio" id="audio-input" style="display: none;">
            </form>
        </div>

        {{-- Flux des Questions --}}
        <div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
                <h2 style="font-size: 1rem; font-weight: 800; color: var(--foreground);">Fil de discussion</h2>
                <div id="questions-count-badge" style="font-size: 0.65rem; background: var(--muted); padding: 0.25rem 0.6rem; border-radius: 999px; font-weight: 700; color: var(--muted-foreground);">
                    {{ $questions->count() }} question(s)
                </div>
            </div>

            <div id="questions-container" style="display: grid; gap: 1rem;">
                @include('participant.partials.questions_list', ['questions' => $questions])
            </div>
        </div>

        {{-- Participants --}}
        <div style="border-top: 2px solid #f1f5f9; margin-top: 2rem; padding-top: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h2 style="font-size: 0.85rem; font-weight: 800; color: var(--muted-foreground); text-transform: uppercase; letter-spacing: 0.05em;">En ligne</h2>
                <span id="participant-count" style="font-size: 0.75rem; font-weight: 600; color: var(--brand);">0 participants</span>
            </div>
            <div id="participants-list" style="display: flex; flex-wrap: wrap; gap: 0.5rem;"></div>
        </div>

        <div style="margin-top: 3rem; text-align: center;">
            <a href="{{ route('participant.join') }}" style="font-size: 0.8rem; font-weight: 600; color: #94a3b8; text-decoration: none; padding: 0.5rem 1rem; border-radius: 0.5rem; transition: all 0.2s;">
                ← Quitter la session
            </a>
        </div>
    </div>
</main>

{{-- MODAL SELECTEUR DE PANELISTE --}}
<div id="panelist-modal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.8); backdrop-filter: blur(8px); z-index: 2000; align-items: center; justify-content: center; padding: 1.5rem;">
    <div style="background: #fff; width: 100%; max-width: 25rem; border-radius: 1.5rem; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); animation: zoomIn 0.3s ease;">
        <div style="padding: 1.25rem; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; background: #f8fafc;">
            <h3 style="font-size: 1rem; font-weight: 800; margin: 0;">À qui s'adresser ?</h3>
            <button onclick="closePanelistModal()" style="background: #f1f5f9; border: none; width: 2rem; height: 2rem; border-radius: 50%; font-size: 1.25rem; cursor: pointer; color: #64748b;">&times;</button>
        </div>
        <div style="padding: 0.5rem; max-height: 25rem; overflow-y: auto;">
            <div onclick="selectPanelist('', '🎯 Tout le panel')" style="padding: 1rem; display: flex; align-items: center; gap: 1rem; cursor: pointer; border-radius: 1rem; transition: background 0.2s;" class="panelist-option">
                <div style="width: 2.5rem; height: 2.5rem; background: var(--brand-light); border-radius: 50%; display: grid; place-items: center; font-size: 1.25rem;">🎯</div>
                <div style="font-weight: 700; font-size: 0.95rem;">Tout le panel</div>
            </div>
            
            @foreach($panelists as $p)
                <div onclick="selectPanelist('{{ $p->id }}', '@ {{ $p->pseudo }}')" style="padding: 1rem; display: flex; align-items: center; gap: 1rem; cursor: pointer; border-radius: 1rem; transition: background 0.2s;" class="panelist-option">
                    <div style="width: 2.5rem; height: 2.5rem; background: #f1f5f9; border-radius: 50%; display: grid; place-items: center; font-weight: 800; color: var(--brand); font-size: 1rem; border: 2px solid #fff; box-shadow: 0 0 0 2px var(--brand-soft);">
                        {{ strtoupper(substr($p->pseudo, 0, 1)) }}
                    </div>
                    <div>
                        <div style="font-weight: 700; font-size: 0.95rem;">{{ $p->pseudo }}</div>
                        <div style="font-size: 0.7rem; color: #64748b; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">{{ $p->sector }}</div>
                    </div>
                </div>
            @endforeach
        </div>
        <div style="padding: 1.25rem; text-align: center; background: #f8fafc;">
            <p style="font-size: 0.75rem; color: #94a3b8; font-style: italic;">Votre question sera mise en avant pour cet expert.</p>
        </div>
    </div>
</div>

<script>
    // --- Gestion Modal Panéliste ---
    function openPanelistModal() {
        document.getElementById('panelist-modal').style.display = 'flex';
    }
    function closePanelistModal() {
        document.getElementById('panelist-modal').style.display = 'none';
    }
    function selectPanelist(id, name) {
        document.getElementById('panelist_id').value = id;
        document.getElementById('panelist-selector-btn').textContent = name;
        document.getElementById('panelist-selector-btn').style.borderColor = id ? 'var(--brand)' : 'var(--border)';
        document.getElementById('panelist-selector-btn').style.color = id ? 'var(--brand)' : 'var(--foreground)';
        closePanelistModal();
    }

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
            
            count.textContent = `${data.length} participant(s)`;
            
            list.innerHTML = data.map(p => `
                <div style="background: #fff; border: 1px solid ${p.is_speaking ? 'var(--brand)' : '#e2e8f0'}; padding: 0.4rem 0.8rem; border-radius: 9999px; font-size: 0.75rem; display: flex; align-items: center; gap: 0.5rem; transition: all 0.3s; ${p.is_speaking ? 'box-shadow: 0 4px 10px var(--brand-soft);' : ''}">
                    <span style="width: 0.4rem; height: 0.4rem; background: ${p.is_speaking ? 'var(--brand)' : '#10b981'}; border-radius: 50%; ${p.is_speaking ? 'animation: pulse-green 1s infinite;' : ''}"></span>
                    <span style="font-weight: 700; color: ${p.is_speaking ? 'var(--brand)' : '#475569'};">${p.pseudo}</span>
                    ${p.is_typing ? '<span class="typing-dot">...</span>' : ''}
                    ${p.is_speaking ? '🎤' : ''}
                </div>
            `).join('');
        } catch (e) {}
    }

    // --- NOUVEAU SYSTÈME VOCAL (STYLE WHATSAPP) ---
    let mediaRecorder;
    let audioChunks = [];
    let audioContext;
    let analyser;
    let dataArray;
    let animationId;
    let voiceTimerInterval;
    let voiceSeconds = 0;
    let originalBlob = null;
    let filteredBlob = null;
    let isRecording = false;

    function toggleVoiceRecording() {
        if (!isRecording) {
            startVoiceRecording();
            isRecording = true;
        } else {
            stopRecordingAndReview();
            isRecording = false;
        }
    }

    async function startVoiceRecording() {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
            mediaRecorder = new MediaRecorder(stream);
            audioChunks = [];

            // Setup Visualizer
            audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const source = audioContext.createMediaStreamSource(stream);
            analyser = audioContext.createAnalyser();
            analyser.fftSize = 64;
            source.connect(analyser);
            dataArray = new Uint8Array(analyser.frequencyBinCount);

            mediaRecorder.ondataavailable = (event) => audioChunks.push(event.data);
            mediaRecorder.onstop = async () => {
                originalBlob = new Blob(audioChunks, { type: 'audio/webm' });
                showPreview(originalBlob);
            };

            mediaRecorder.start();
            drawWaveform();
            
            // UI Switch
            document.getElementById('normal-form-actions').style.display = 'none';
            document.getElementById('recording-bar').style.display = 'flex';
            
            voiceSeconds = 0;
            voiceTimerInterval = setInterval(() => {
                voiceSeconds++;
                const mins = String(Math.floor(voiceSeconds / 60)).padStart(2, '0');
                const secs = String(voiceSeconds % 60).padStart(2, '0');
                document.getElementById('rec-timer').textContent = `${mins}:${secs}`;
            }, 1000);

        } catch (err) {
            alert("Microphone inaccessible.");
        }
    }

    function drawWaveform() {
        const canvas = document.getElementById('waveform');
        const ctx = canvas.getContext('2d');
        animationId = requestAnimationFrame(drawWaveform);
        analyser.getByteFrequencyData(dataArray);

        ctx.clearRect(0, 0, canvas.width, canvas.height);
        const barWidth = (canvas.width / dataArray.length) * 2.5;
        let x = 0;

        for(let i = 0; i < dataArray.length; i++) {
            const barHeight = (dataArray[i] / 255) * canvas.height;
            ctx.fillStyle = i % 2 === 0 ? '#8b5cf6' : '#c084fc';
            ctx.fillRect(x, (canvas.height - barHeight)/2, barWidth, barHeight);
            x += barWidth + 2;
        }
    }

    function togglePauseResume() {
        const icon = document.getElementById('pause-icon');
        if (mediaRecorder.state === 'recording') {
            mediaRecorder.pause();
            icon.textContent = '▶️';
            clearInterval(voiceTimerInterval);
            cancelAnimationFrame(animationId);
        } else {
            mediaRecorder.resume();
            icon.textContent = '⏸';
            drawWaveform();
            voiceTimerInterval = setInterval(() => {
                voiceSeconds++;
                const mins = String(Math.floor(voiceSeconds / 60)).padStart(2, '0');
                const secs = String(voiceSeconds % 60).padStart(2, '0');
                document.getElementById('rec-timer').textContent = `${mins}:${secs}`;
            }, 1000);
        }
    }

    function cancelRecording() {
        if (mediaRecorder && mediaRecorder.state !== 'inactive') mediaRecorder.stop();
        stopAudioTracks();
        isRecording = false;
        resetVoice();
    }

    function stopRecordingAndReview() {
        if (mediaRecorder && mediaRecorder.state !== 'inactive') mediaRecorder.stop();
        stopAudioTracks();
        isRecording = false;
    }

    function stopAudioTracks() {
        clearInterval(voiceTimerInterval);
        cancelAnimationFrame(animationId);
        if (mediaRecorder && mediaRecorder.stream) {
            mediaRecorder.stream.getTracks().forEach(track => track.stop());
        }
    }

    function showPreview(blob) {
        document.getElementById('recording-bar').style.display = 'none';
        document.getElementById('voice-preview-bar').style.display = 'flex';
        const url = URL.createObjectURL(blob);
        document.getElementById('preview-audio').src = url;
        filteredBlob = blob; // Par défaut
        updateFileInput(blob);
    }

    function resetVoice() {
        document.getElementById('recording-bar').style.display = 'none';
        document.getElementById('voice-preview-bar').style.display = 'none';
        document.getElementById('normal-form-actions').style.display = 'flex';
        document.getElementById('audio-input').value = '';
        originalBlob = null;
        filteredBlob = null;
    }

    async function applyVoiceFilter() {
        const filter = document.getElementById('voice-filter').value;
        if (filter === 'none') {
            filteredBlob = originalBlob;
            const url = URL.createObjectURL(originalBlob);
            document.getElementById('preview-audio').src = url;
            updateFileInput(originalBlob);
            return;
        }

        const arrayBuffer = await originalBlob.arrayBuffer();
        const offlineCtx = new OfflineAudioContext(1, 44100 * 300, 44100); // Max 5 min
        const buffer = await offlineCtx.decodeAudioData(arrayBuffer);
        const source = offlineCtx.createBufferSource();
        source.buffer = buffer;

        // Effets
        if (filter === 'robot') {
            const oscillator = offlineCtx.createOscillator();
            oscillator.type = 'sawtooth';
            oscillator.frequency.value = 50;
            const oscGain = offlineCtx.createGain();
            oscGain.gain.value = 0.1;
            oscillator.connect(oscGain);
            const delay = offlineCtx.createDelay();
            delay.delayTime.value = 0.01;
            source.connect(delay);
            oscGain.connect(delay.delayTime);
            delay.connect(offlineCtx.destination);
            oscillator.start();
        } else if (filter === 'deep') {
            source.playbackRate.value = 0.7;
            source.connect(offlineCtx.destination);
        } else if (filter === 'high') {
            source.playbackRate.value = 1.5;
            source.connect(offlineCtx.destination);
        }

        source.start(0);
        const renderedBuffer = await offlineCtx.startRendering();
        
        // Conversion buffer vers Blob
        const wavBlob = bufferToWav(renderedBuffer);
        filteredBlob = wavBlob;
        const url = URL.createObjectURL(wavBlob);
        document.getElementById('preview-audio').src = url;
        updateFileInput(wavBlob);
    }

    function updateFileInput(blob) {
        const file = new File([blob], "vocal.wav", { type: 'audio/wav' });
        const dt = new DataTransfer();
        dt.items.add(file);
        document.getElementById('audio-input').files = dt.files;
    }

    // Helper pour convertir un AudioBuffer en WAV (simplifié)
    function bufferToWav(abuffer) {
        let numOfChan = abuffer.numberOfChannels,
            length = abuffer.length * numOfChan * 2 + 44,
            buffer = new ArrayBuffer(length),
            view = new DataView(buffer),
            channels = [], i, sample,
            offset = 0,
            pos = 0;

        function setUint16(data) { view.setUint16(pos, data, true); pos += 2; }
        function setUint32(data) { view.setUint32(pos, data, true); pos += 4; }

        setUint32(0x46464952);                         // "RIFF"
        setUint32(length - 8);                         // file length - 8
        setUint32(0x45564157);                         // "WAVE"
        setUint32(0x20746d66);                         // "fmt " chunk
        setUint32(16);                                 // length = 16
        setUint16(1);                                  // PCM (uncompressed)
        setUint16(numOfChan);
        setUint32(abuffer.sampleRate);
        setUint32(abuffer.sampleRate * 2 * numOfChan); // avg. bytes/sec
        setUint16(numOfChan * 2);                      // block-align
        setUint16(16);                                 // 16nd-bit
        setUint32(0x61746164);                         // "data" - chunk
        setUint32(length - pos - 4);                   // chunk length

        for(i = 0; i < abuffer.numberOfChannels; i++) channels.push(abuffer.getChannelData(i));
        while(pos < length) {
            for(i = 0; i < numOfChan; i++) {             // interleave channels
                sample = Math.max(-1, Math.min(1, channels[i][offset])); // clamp
                sample = (sample < 0 ? sample * 0x8000 : sample * 0x7FFF) | 0; // scale to 16nd-bit signed int
                view.setInt16(pos, sample, true);          // write 16nd-bit sample
                pos += 2;
            }
            offset++;
        }
        return new Blob([buffer], {type: "audio/wav"});
    }

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

    async function fetchQuestions() {
        const activeElement = document.activeElement;
        const isTyping = activeElement && (activeElement.tagName === 'TEXTAREA' || activeElement.tagName === 'INPUT');
        const isAnyReplyFormOpen = Array.from(document.querySelectorAll('form[id^="reply-form-"]')).some(f => f.style.display === 'flex');

        if (isTyping || isRecording || isAnyReplyFormOpen || document.getElementById('panelist-modal').style.display === 'flex') return;

        try {
            const response = await fetch(`/e/${eventCode}/participant/questions-fetch`);
            const data = await response.json();
            
            if (document.getElementById('questions-container')) {
                document.getElementById('questions-container').innerHTML = data.html;
            }
            if (document.getElementById('questions-count-badge')) {
                document.getElementById('questions-count-badge').textContent = `${data.count} question(s)`;
            }
        } catch (e) {}
    }

    setInterval(sendHeartbeat, 15000);
    setInterval(fetchParticipants, 5000);
    setInterval(fetchQuestions, 5000);
    sendHeartbeat();
    fetchParticipants();
    fetchQuestions();
</script>

<style>
.panelist-option:hover {
    background: #f8fafc !important;
}
@keyframes zoomIn {
    from { transform: scale(0.95); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}
@keyframes slideIn {
    from { transform: translateY(-10px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}
@keyframes pulse-green {
    0% { transform: scale(0.9); opacity: 1; }
    50% { transform: scale(1.2); opacity: 0.5; }
    100% { transform: scale(0.9); opacity: 1; }
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
