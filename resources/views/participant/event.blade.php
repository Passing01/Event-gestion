@extends('layouts.auth')

@section('title', $event->name . ' – Q&A')

@section('content')

<main class="auth-page" style="padding: 0.5rem; background: #f1f5f9; min-height: 100vh;">
    <div class="auth-card" style="max-width: 42rem; width: 100%; padding: 1.25rem; border-radius: 1.5rem; margin-bottom: 2rem;">

        {{-- Bannière de l'événement --}}
        @if($event->image_path)
        <div style="margin: -1.25rem -1.25rem 1.5rem -1.25rem; height: 10rem; position: relative; overflow: hidden; border-radius: 1.5rem 1.5rem 0 0;">
            <img src="{{ asset('storage/' . $event->image_path) }}" alt="{{ $event->name }}" style="width: 100%; height: 100%; object-fit: cover;">
            <div style="position: absolute; inset: 0; background: linear-gradient(to bottom, transparent, rgba(0,0,0,0.4));"></div>
        </div>
        @endif

        {{-- Header Responsif --}}
        <div style="display: flex; flex-direction: row; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem; gap: 1rem;">
            <div style="flex: 1;">
                <h1 style="font-size: 1.25rem; font-weight: 900; margin-bottom: 0.25rem; line-height: 1.2; color: var(--foreground);">{{ $event->name }}</h1>
                <p style="font-size: 0.75rem; color: var(--muted-foreground); display: flex; align-items: center; gap: 0.4rem;">
                    <span style="width: 0.5rem; height: 0.5rem; background: #10b981; border-radius: 50%;"></span>
                    En direct • {{ session('participant_pseudo') }}
                </p>
            </div>
            <div style="background: var(--brand); color: #fff; padding: 0.4rem 0.8rem; border-radius: 0.75rem; font-size: 0.75rem; font-weight: 700; white-space: nowrap; box-shadow: 0 4px 12px var(--brand-soft);">
                #{{ $event->code }}
            </div>
        </div>

        {{-- Description de l'événement --}}
        @if($event->description)
        <div style="margin-bottom: 1.5rem; padding: 0.75rem 1rem; background: #f8fafc; border-radius: 0.75rem; border-left: 3px solid var(--brand-soft);">
            <p style="font-size: 0.85rem; color: var(--muted-foreground); line-height: 1.5; margin: 0;">{{ $event->description }}</p>
        </div>
        @endif

        {{-- Main Levée & Statut --}}
        @php
            $myHand = $event->raisedHands()->where('pseudo', session('participant_pseudo'))->first();
            $rank = $myHand ? $event->raisedHands()->where('status', 'pending')->where('created_at', '<', $myHand->created_at)->count() + 1 : null;
        @endphp

        <div id="mic-container" style="background: #fff; padding: 1.25rem; border-radius: 1.5rem; border: 2px solid {{ $myHand && $myHand->status == 'called' ? '#ef4444' : 'var(--border)' }}; display: flex; flex-direction: column; align-items: center; justify-content: center; margin-bottom: 2rem; box-shadow: 0 10px 30px rgba(0,0,0,0.05); text-align: center; position: relative; overflow: hidden;">
            @if($myHand && $myHand->status == 'called')
                <div style="position: absolute; top: 0; left: 0; right: 0; height: 4px; background: #ef4444; animation: progress-infinite 2s linear infinite;"></div>
                <div id="live-mic-visual" style="font-size: 3rem; margin-bottom: 1rem; animation: pulse-mic 1.5s infinite;">🎙️</div>
                <h3 id="live-mic-title" style="font-weight: 900; font-size: 1.25rem; color: #ef4444;">MICRO OUVERT - DIRECT</h3>
                <p style="font-size: 0.85rem; color: var(--muted-foreground); margin-bottom: 1.5rem;">La salle vous écoute. Parlez tranquillement.</p>
                
                <div id="mic-controls" style="display: flex; flex-direction: column; gap: 1rem; width: 100%;">
                    <button type="button" id="live-mic-btn" class="btn-brand" style="flex: 2; padding: 1.25rem; border-radius: 1.25rem; font-weight: 800; display: flex; align-items: center; justify-content: center; gap: 0.75rem; box-shadow: 0 10px 20px var(--brand-soft); border:none;" onclick="startLiveMicFromButton()">
                        <span style="font-size: 1.5rem;">🎤</span> Activer mon micro
                    </button>
                    
                    <button type="button" class="btn-brand" style="background: #f1f5f9; color: #475569; padding: 0.75rem; border-radius: 1rem; font-weight: 700; border: none; font-size: 0.8rem;" onclick="stopLiveMicManually()">
                        ❌ Terminer l'intervention
                    </button>
                </div>

                {{-- État "En Ligne" --}}
                <div id="mic-active-status" style="display: none; width: 100%;">
                    <div style="background: #ecfdf5; border: 1px solid #10b981; color: #059669; padding: 1rem; border-radius: 1rem; font-weight: 800; display: flex; align-items: center; justify-content: center; gap: 0.75rem; margin-bottom: 1rem;">
                        <span id="mic-pulse-dot" style="width: 0.75rem; height: 0.75rem; background: #10b981; border-radius: 50%; animation: pulse-green 1s infinite;"></span>
                        <span id="mic-status-text">MICRO EN DIRECT</span>
                    </div>

                    <div style="display: flex; gap: 0.75rem; width: 100%;">
                        <button type="button" id="mute-btn" class="btn-brand" style="background: #f1f5f9; color: #475569; flex: 1; padding: 1rem; border-radius: 1rem; font-weight: 800; border: none;" onclick="toggleMute()">
                            <span id="mute-icon">🔇</span> <span id="mute-text">Couper le son</span>
                        </button>
                        
                        <button type="button" class="btn-brand" style="background: #fee2e2; color: #dc2626; flex: 1; padding: 1rem; border-radius: 1rem; font-weight: 800; border: none;" onclick="stopLiveMicManually()">
                            Déconnecter
                        </button>
                    </div>
                </div>
            @else
                {{-- UI Attente ou Bouton Lever Main (IDEM PRECEDENT) --}}
                <div style="display: flex; align-items: center; gap: 1rem; width: 100%; justify-content: space-between;">
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <div style="width: 3rem; height: 3rem; background: {{ $myHand ? 'var(--brand)' : '#f8fafc' }}; color: {{ $myHand ? '#fff' : 'var(--brand)' }}; border-radius: 50%; display: grid; place-items: center; font-size: 1.5rem; transition: all 0.3s; border: 1px solid {{ $myHand ? 'var(--brand)' : 'var(--border)' }};">
                            ✋
                        </div>
                        <div style="text-align: left;">
                            @if($myHand)
                                <p style="font-size: 0.95rem; font-weight: 800; margin: 0; color: var(--foreground);">Main levée</p>
                                <p style="font-size: 0.75rem; color: var(--muted-foreground); margin: 0;">Rang #{{ $rank }} • Attente modérateur.</p>
                            @else
                                <p style="font-size: 0.95rem; font-weight: 800; margin: 0; color: var(--foreground);">Demander la parole</p>
                                <p style="font-size: 0.75rem; color: var(--muted-foreground); margin: 0;">Signalez-vous pour intervenir en direct.</p>
                            @endif
                        </div>
                    </div>
                    
                    @if($myHand)
                        <form action="{{ route('participant.lower-hand', $event->code) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn-brand" style="background: #fee2e2; color: #dc2626; border: none; width: auto; padding: 0.6rem 1rem; font-size: 0.8rem; border-radius: 0.75rem; font-weight: 700;">Annuler</button>
                        </form>
                    @else
                        <form action="{{ route('participant.raise-hand', $event->code) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn-brand" style="width: auto; padding: 0.6rem 1.5rem; font-size: 0.8rem; border-radius: 0.75rem; font-weight: 700; box-shadow: 0 4px 12px var(--brand-soft); border:none;">Prendre Place</button>
                        </form>
                    @endif
                </div>
            @endif
        </div>

        <style>
        @keyframes pulse-mic {
            0% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.1); opacity: 0.8; }
            100% { transform: scale(1); opacity: 1; }
        }
        @keyframes progress-infinite {
            0% { left: -100%; width: 100%; }
            100% { left: 100%; width: 100%; }
        }
        </style>

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
                        <input type="hidden" name="is_intervention" value="{{ ($myHand && $myHand->status == 'called') ? '1' : '0' }}">
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

<script src="https://unpkg.com/peerjs@1.5.2/dist/peerjs.min.js"></script>
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
    const participantPseudo = '{{ session("participant_pseudo") }}';

    // --- PeerJS : Micro Live ---
    let myPeer;
    let currentCall;
    let lastHandStatus = '{{ $myHand ? $myHand->status : "none" }}';

    function initPeer() {
        myPeer = new Peer();
        myPeer.on('open', (id) => console.log('Connecté au réseau audio, ID:', id));
    }

    async function startLiveMicFromButton() {
        const btn = document.getElementById('live-mic-btn');
        btn.disabled = true;
        btn.innerHTML = "⌛ Connexion...";
        
        await startLiveMic();
        
        document.getElementById('mic-controls').style.display = 'none';
        document.getElementById('mic-active-status').style.display = 'block';
    }

    let isMuted = false;
    let localStream;

    async function startLiveMic() {
        console.log("Démarrage du Micro Live...");
        try {
            // Configuration audio HD avec annulation d'écho et suppression de bruit
            localStream = await navigator.mediaDevices.getUserMedia({ 
                audio: {
                    echoCancellation: true,
                    noiseSuppression: true,
                    autoGainControl: true,
                    sampleRate: 44100
                } 
            });
            
            // Appel 1 : Au Projecteur (pour le son dans la salle)
            currentCall = myPeer.call(`${eventCode}-PROJECTOR`, localStream, {
                metadata: { name: participantPseudo }
            });
            console.log("Appel en cours vers le projecteur...");

            // Appel 2 : Au Modérateur (pour l'enregistrement)
            myPeer.call(`${eventCode}-MODERATOR`, localStream, {
                metadata: { name: participantPseudo }
            });
            console.log("Appel en cours vers le modérateur (enregistrement)...");

        } catch (err) {
            console.error("Erreur micro:", err);
            alert("Microphone requis pour intervenir en direct.");
            location.reload();
        }
    }

    function toggleMute() {
        if (!localStream) return;
        
        isMuted = !isMuted;
        localStream.getAudioTracks().forEach(track => {
            track.enabled = !isMuted;
        });

        // UI Update
        const btn = document.getElementById('mute-btn');
        const icon = document.getElementById('mute-icon');
        const text = document.getElementById('mute-text');
        const statusText = document.getElementById('mic-status-text');
        const pulse = document.getElementById('mic-pulse-dot');

        if (isMuted) {
            btn.style.background = 'var(--brand)';
            btn.style.color = '#fff';
            icon.textContent = '🎙️';
            text.textContent = 'Réactiver son';
            statusText.textContent = 'MICRO COUPÉ';
            pulse.style.animation = 'none';
            pulse.style.background = '#94a3b8';
        } else {
            btn.style.background = '#f1f5f9';
            btn.style.color = '#475569';
            icon.textContent = '🔇';
            text.textContent = 'Couper le son';
            statusText.textContent = 'MICRO EN DIRECT';
            pulse.style.animation = 'pulse-green 1s infinite';
            pulse.style.background = '#10b981';
        }
    }

    function stopLiveMic() {
        if (currentCall) {
            currentCall.close();
            currentCall = null;
            console.log("Micro Live arrêté.");
        }
    }

    function stopLiveMicManually() {
        stopLiveMic();
        // Envoyer une requête pour baisser la main
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/e/${eventCode}/lower-hand`;
        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = csrfToken;
        form.appendChild(csrf);
        document.body.appendChild(form);
        form.submit();
    }

    initPeer();

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

            // --- GESTION MICRO LIVE ---
            if (data.my_hand_status !== lastHandStatus) {
                if (data.my_hand_status === 'called') {
                    // On ne lance plus le micro auto, on refresh juste pour voir le bouton "Activer mon micro"
                    location.reload(); 
                } else if (lastHandStatus === 'called' && data.my_hand_status !== 'called') {
                    stopLiveMic();
                    location.reload();
                }
                lastHandStatus = data.my_hand_status;
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
