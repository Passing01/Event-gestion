<!DOCTYPE html>
<html lang="fr" data-brand="purple" id="html-root">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projection : {{ $event->name }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap">
    <link rel="stylesheet" href="{{ asset('dashboard.css') }}">
    @vite('resources/js/projection.js')
    <style>
        body {
            background: #000;
            color: #fff;
            height: 100vh;
            width: 100vw;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            font-family: 'Inter', sans-serif;
            position: relative;
        }
        .event-header {
            position: absolute;
            top: 2rem;
            left: 2rem;
            z-index: 200;
            display: flex;
            align-items: center;
            gap: 1.25rem;
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(20px);
            padding: 0.75rem 1.75rem;
            border-radius: 999px;
            border: 1px solid rgba(255,255,255,0.1);
            box-shadow: 0 15px 35px rgba(0,0,0,0.4);
            animation: slideDown 1s cubic-bezier(0.23, 1, 0.32, 1);
        }
        @keyframes slideDown {
            from { transform: translateY(-100%); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .event-logo-box {
            background: var(--brand);
            color: #fff;
            padding: 0.4rem 0.8rem;
            border-radius: 0.5rem;
            font-weight: 800;
            font-size: 1rem;
            letter-spacing: 0.1em;
        }
        .event-title {
            font-size: 1.125rem;
            font-weight: 700;
            letter-spacing: 0.02em;
        }
        .projection-container {
            display: grid;
            grid-template-columns: 1fr 30%;
            width: 100%;
            height: 100%;
            padding: 2rem;
            gap: 2rem;
            transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .projection-container.full-mode {
            grid-template-columns: 1fr 0%;
            padding: 0;
            gap: 0;
        }
        .projection-container.full-mode .qa-sidebar {
            display: none !important;
        }

        .main-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            position: relative;
        }
        .question-box {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            width: 100%;
        }
        .question-content {
            font-size: 3.5rem;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 2rem;
            text-shadow: 0 4px 20px rgba(0,0,0,0.5);
        }
        .question-author {
            font-size: 1.5rem;
            color: var(--brand);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }
        .qa-sidebar {
            background: rgba(255,255,255,0.03);
            border-radius: 2rem;
            padding: 1.75rem;
            backdrop-filter: blur(40px);
            border: 1px solid rgba(255,255,255,0.08);
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
            overflow-y: auto;
            position: relative;
        }
        .qa-item {
            background: rgba(255,255,255,0.05);
            padding: 0;
            border-radius: 1.25rem;
            font-size: 0.9375rem;
            overflow: hidden;
            border: 1px solid rgba(255,255,255,0.1);
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
        }
        .qa-item:hover { transform: translateX(-5px); border-color: var(--brand); }
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 1rem;
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            background: var(--brand);
            color: #fff;
        }
        .qa-item {
            cursor: pointer;
            position: relative;
        }
        .qa-item.active-playing {
            border: 2px solid var(--brand) !important;
            box-shadow: 0 0 20px var(--brand-soft);
        }
        .qa-body {
            padding: 1rem;
        }
        .qa-item .q { font-weight: 600; line-height: 1.5; color: #fff; }
        .qa-item .a { 
            font-size: 0.8125rem; 
            opacity: 0.9; 
            border-top: 1px dotted rgba(255,255,255,0.2); 
            padding-top: 0.75rem; 
            margin-top: 0.75rem;
            color: var(--brand);
            font-style: italic;
        }

        .hands-footer {
            position: absolute;
            bottom: 2rem;
            left: 50%;
            transform: translateX(-50%);
            z-index: 300;
            display: flex;
            gap: 1rem;
            padding: 0.5rem;
            background: rgba(0,0,0,0.6);
            backdrop-filter: blur(20px);
            border-radius: 999px;
            border: 1px solid rgba(255,255,255,0.15);
            max-width: 80%;
            overflow-x: auto;
            scrollbar-width: none;
        }
        .hands-footer::-webkit-scrollbar { display: none; }
        .hand-card {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            background: #fff;
            color: #000;
            padding: 0.5rem 1.25rem;
            border-radius: 999px;
            white-space: nowrap;
            animation: fadeIn 0.5s ease;
        }
        .hand-card .status-dot {
            width: 0.625rem;
            height: 0.625rem;
            background: #10b981;
            border-radius: 50%;
            animation: pulse-green 1.5s infinite;
        }
        @keyframes pulse-green {
            0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
            70% { box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
            100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }
        
        .fullscreen-btn {
            position: absolute;
            bottom: 2rem;
            right: 2rem;
            background: rgba(255,255,255,0.1);
            border: none;
            color: #fff;
            padding: 0.75rem;
            border-radius: 50%;
            cursor: pointer;
            z-index: 100;
        }
        .fullscreen-btn:hover { background: var(--brand); }
        
        .voice-indicator {
            display: flex;
            gap: 4px;
            height: 20px;
            align-items: center;
            margin-top: 1rem;
        }
        .voice-bar {
            width: 4px;
            background: var(--brand);
            border-radius: 2px;
            animation: voice-pulse 1s infinite ease-in-out;
        }
        @keyframes voice-pulse {
            0%, 100% { height: 4px; }
            50% { height: 20px; }
        }

        /* Indicateur Micro Live */
        .live-mic-status {
            position: absolute;
            top: 2rem;
            right: 2rem;
            display: none;
            align-items: center;
            gap: 0.75rem;
            background: rgba(220, 38, 38, 0.2);
            padding: 0.75rem 1.25rem;
            border-radius: 999px;
            border: 1px solid #dc2626;
            color: #fff;
            animation: pulse-red 2s infinite;
            z-index: 500;
        }
        @keyframes pulse-red {
            0% { box-shadow: 0 0 0 0 rgba(220, 38, 38, 0.4); }
            70% { box-shadow: 0 0 0 15px rgba(220, 38, 38, 0); }
            100% { box-shadow: 0 0 0 0 rgba(220, 38, 38, 0); }
        }
    </style>
    <script src="https://unpkg.com/peerjs@1.5.2/dist/peerjs.min.js"></script>
</head>
<body data-brand="{{ $event->user->brand_color ?? 'purple' }}">

    <div id="audio-init-overlay" style="position: fixed; inset: 0; background: rgba(0,0,0,0.92); z-index: 9999; display: flex; flex-direction: column; align-items: center; justify-content: center; backdrop-filter: blur(10px);">
        <div style="background: var(--brand); width: 100px; height: 100px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 3rem; margin-bottom: 2rem; box-shadow: 0 0 50px var(--brand-soft); animation: pulse 2s infinite;">🎬</div>
        <h1 style="color: #fff; margin-bottom: 1rem; font-weight: 800; font-size: 2.5rem;">Démarrer l'Événement & Enregistrement</h1>
        <p style="color: rgba(255,255,255,0.7); margin-bottom: 2rem; font-size: 1.25rem; max-width: 45rem; text-align: center; line-height: 1.6;">
            Cliquez sur le bouton ci-dessous pour lancer la session. <br>
            <strong style="color: var(--brand);">Important :</strong> Pour l'enregistrement automatique pour le Marketplace, sélectionnez l'option <strong>"Partager cet onglet"</strong> et veillez à cocher la case <strong>"Partager l'audio de l'onglet"</strong> dans la fenêtre système qui s'affiche.
        </p>
        <button onclick="enableAudioOnProjector()" class="btn-brand" style="padding: 1.5rem 4rem; font-size: 1.5rem; border-radius: 999px; border: none; box-shadow: 0 20px 40px rgba(0,0,0,0.3); cursor: pointer; font-weight: 900; background: var(--brand);">DÉMARRER LA SESSION</button>
    </div>

    <div class="event-header">
        <div class="event-logo-box">LIVE</div>
        <span class="event-title">{{ $event->name }}</span>
    </div>

    {{-- Indicateur d'enregistrement Marketplace --}}
    <div id="marketplace-rec-indicator" style="position: absolute; top: 2.25rem; left: 50%; transform: translateX(-50%); display: none; align-items: center; gap: 0.5rem; background: rgba(239, 68, 68, 0.15); border: 1px solid #ef4444; padding: 0.5rem 1.25rem; border-radius: 999px; z-index: 500; font-family: sans-serif; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; color: #fecaca; letter-spacing: 0.05em;">
        <span style="width: 0.625rem; height: 0.625rem; background: #ef4444; border-radius: 50%; display: inline-block; animation: pulse-red 1s infinite;"></span>
        Enregistrement Marketplace en cours
    </div>

    {{-- Indicateur de micro en direct --}}
    <div id="live-mic-alert" class="live-mic-status">
        <span style="font-weight: 800; font-size: 0.8rem; text-transform: uppercase;">Direct Live</span>
        <span id="live-mic-author" style="font-weight: 700;"></span>
        <span style="font-size: 1.25rem;">🎙️</span>
    </div>

    {{-- Bandeau Permanent des Intervenants (Reste visible même en projection) --}}
    <div id="hands-footer" class="hands-footer" style="display: none;">
        <!-- Rempli par JS -->
    </div>

    <div class="projection-container" id="projection-wrap">
        <div class="main-content">
            <div class="question-box" id="projection-content">
                @if($projectingPanelist)
                    {{-- Initial State if already projecting --}}
                @elseif($answering)
                    <div class="question-content">
                        @if($answering->content)
                            "{{ $answering->content }}"
                        @else
                            <span style="font-size: 5rem;">🎙️</span><br>
                            <span style="font-size: 1.5rem; opacity: 0.7;">Message Vocal</span>
                        @endif
                    </div>
                    <div class="question-author">{{ $answering->pseudo }}</div>
                @else
                    <div class="empty-state" style="font-size: 2rem; color: var(--muted-foreground); font-style: italic;">En attente de la prochaine question...</div>
                @endif
            </div>

            {{-- Live Presentation Badge --}}
            <div id="presentation-badge" style="display: none; position: absolute; bottom: 2rem; left: 2rem; background: var(--brand); color: #fff; padding: 0.75rem 1.5rem; border-radius: 9999px; font-weight: 700; align-items: center; gap: 1rem; box-shadow: 0 10px 30px rgba(0,0,0,0.5); z-index: 101;">
                <span style="width: 0.75rem; height: 0.75rem; background: #fff; border-radius: 50%; animation: pulse 1s infinite;"></span>
                <span id="presenter-name"></span>
            </div>
            
            <div id="voice-indicator" class="voice-indicator" style="display: none;">
                <div class="voice-bar" style="animation-delay: 0s;"></div>
                <div class="voice-bar" style="animation-delay: 0.2s;"></div>
                <div class="voice-bar" style="animation-delay: 0.4s;"></div>
                <div class="voice-bar" style="animation-delay: 0.6s;"></div>
                <div class="voice-bar" style="animation-delay: 0.8s;"></div>
            </div>

            <button class="fullscreen-btn" onclick="toggleFullscreen()" title="Plein écran">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 1.5rem; height: 1.5rem;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15" />
                </svg>
            </button>
        </div>

        <div class="qa-sidebar">
            <h3 style="font-size: 1rem; text-transform: uppercase; letter-spacing: 0.1em; opacity: 0.7; margin-bottom: 1rem;">Questions & Réponses</h3>
            <div id="qa-list" style="display: grid; gap: 1rem;">
                <!-- Rempli par JS -->
            </div>
        </div>
    </div>

    <script>
        const eventCode = '{{ $event->code }}';
        let currentQuestionId = null;
        let currentStatus = null;
        let wasProjecting = false;
        let lastProjectingPath = null;
        let lastProjectingPage = null;
        let lastPlayedAudioId = null;
        let audioEnabled = false;
        let audioContext = null;
        let isScreenSharingActive = false;

        // --- Enregistrement Automatique de la Session (Marketplace) ---
        let mediaRecorder = null;
        let recordedChunks = [];
        let recordingStream = null;
        let db = null;

        // Initialisation de la base IndexedDB locale
        const dbRequest = indexedDB.open("ReplayDB", 1);
        dbRequest.onupgradeneeded = function(e) {
            db = e.target.result;
            db.createObjectStore("chunks", { autoIncrement: true });
        };
        dbRequest.onsuccess = function(e) {
            db = e.target.result;
            console.log("IndexedDB initialisée avec succès.");
            checkAndRecoverReplay();
        };

        function preventTabClose(e) {
            if (mediaRecorder && mediaRecorder.state !== 'inactive') {
                e.preventDefault();
                e.returnValue = ''; // Requis pour forcer l'affichage de l'alerte sur tous les navigateurs
            }
        }

        function saveChunkToIndexedDB(chunk) {
            if (!db) return;
            try {
                const transaction = db.transaction(["chunks"], "readwrite");
                const store = transaction.objectStore("chunks");
                store.add(chunk);
            } catch (err) {
                console.error("Erreur de sauvegarde locale du chunk :", err);
            }
        }

        function clearIndexedDB() {
            if (!db) return;
            try {
                const transaction = db.transaction(["chunks"], "readwrite");
                const store = transaction.objectStore("chunks");
                store.clear();
                console.log("IndexedDB locale nettoyée.");
            } catch (err) {
                console.error("Erreur de nettoyage IndexedDB :", err);
            }
        }

        function checkAndRecoverReplay() {
            if (!db) return;
            const transaction = db.transaction(["chunks"], "readonly");
            const store = transaction.objectStore("chunks");
            const getAllRequest = store.getAll();

            getAllRequest.onsuccess = function(e) {
                const chunks = e.target.result;
                if (chunks && chunks.length > 0 && (!mediaRecorder || mediaRecorder.state === 'inactive')) {
                    console.log("Replay interrompu détecté dans IndexedDB. Restauration...");
                    recoverAndUpload(chunks);
                }
            };
        }

        async function recoverAndUpload(chunks) {
            // Afficher un overlay d'envoi magnifique
            const recoveryOverlay = document.createElement('div');
            recoveryOverlay.id = 'recovery-replay-overlay';
            recoveryOverlay.style = "position: fixed; inset: 0; background: rgba(0,0,0,0.95); z-index: 10001; display: flex; flex-direction: column; align-items: center; justify-content: center; backdrop-filter: blur(15px); font-family: sans-serif;";
            recoveryOverlay.innerHTML = `
                <div style="background: #eab308; width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; margin-bottom: 2rem; box-shadow: 0 0 40px rgba(234, 179, 8, 0.4); animation: pulse 1.5s infinite;">📂</div>
                <h2 style="color: #fff; margin-bottom: 1rem; font-weight: 800; font-size: 2rem;">Replay récupéré !</h2>
                <p id="recovery-status-text" style="color: rgba(255,255,255,0.7); font-size: 1.1rem; text-align: center; max-width: 30rem; line-height: 1.6;">
                    Un enregistrement précédent a été interrompu suite à une fermeture sauvage. Envoi automatique sur le serveur pour le Marketplace... Veuillez patienter.
                </p>
            `;
            document.body.appendChild(recoveryOverlay);

            const blob = new Blob(chunks, { type: 'video/webm' });
            const formData = new FormData();
            formData.append('video', blob, `replay_recovered_${eventCode}.webm`);

            try {
                const response = await fetch(`/e/${eventCode}/projection/upload-replay`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                });

                const data = await response.json();
                
                if (data.success) {
                    clearIndexedDB();
                    document.getElementById('recovery-status-text').innerHTML = `
                        <span style="color: #22c55e; font-weight: 900; font-size: 3rem; display: block; margin-bottom: 1rem;">🎉</span>
                        <strong style="color: #22c55e; font-size: 1.3rem;">Récupération Réussie !</strong><br><br>
                        Le replay interrompu a été récupéré, sauvegardé et mis en vente sur le Marketplace avec succès.<br>
                        Vous pouvez démarrer la nouvelle session.
                        <br><br>
                        <button onclick="document.getElementById('recovery-replay-overlay').remove()" class="btn-brand" style="background: var(--brand); padding: 0.75rem 2rem; border-radius: 999px; border:none; cursor:pointer; color:#fff; font-weight:700;">Continuer</button>
                    `;
                } else {
                    throw new Error(data.message || "Erreur de sauvegarde.");
                }
            } catch (err) {
                console.error("Recovery upload error:", err);
                document.getElementById('recovery-status-text').innerHTML = `
                    <span style="color: #ef4444; font-weight: 900; font-size: 3rem; display: block; margin-bottom: 1rem;">⚠️</span>
                    <strong style="color: #ef4444; font-size: 1.3rem;">Erreur de Récupération</strong><br><br>
                    Impossible d'envoyer le replay récupéré au serveur.<br><br>
                    <button onclick="window.location.reload()" class="btn-brand" style="background: var(--brand); padding: 0.75rem 2rem; border:none; cursor:pointer; color:#fff; font-weight:700;">Réessayer</button>
                    <button onclick="clearIndexedDB(); document.getElementById('recovery-replay-overlay').remove()" class="btn-brand" style="background: rgba(255,255,255,0.1); padding: 0.75rem 2rem; border:none; cursor:pointer; color:#fff; font-weight:700; margin-left: 0.5rem;">Ignorer et effacer</button>
                `;
            }
        }

        async function startRecording() {
            try {
                // Nettoyer IndexedDB avant de commencer le nouvel enregistrement
                clearIndexedDB();

                // Demande de capture de l'onglet/écran avec l'audio du système/onglet
                recordingStream = await navigator.mediaDevices.getDisplayMedia({
                    video: { displaySurface: "browser" },
                    audio: true
                });

                recordedChunks = [];
                mediaRecorder = new MediaRecorder(recordingStream, {
                    mimeType: 'video/webm;codecs=vp9,opus'
                });

                mediaRecorder.ondataavailable = function(e) {
                    if (e.data.size > 0) {
                        recordedChunks.push(e.data);
                        saveChunkToIndexedDB(e.data);
                    }
                };

                mediaRecorder.onstop = async function() {
                    console.log("Enregistrement arrêté, préparation de l'envoi...");
                    uploadReplayVideo();
                };

                // Protéger l'onglet contre l'actualisation ou la fermeture accidentelle
                window.addEventListener('beforeunload', preventTabClose);

                mediaRecorder.start(1000); // Acquisition continue par tranches de 1s
                document.getElementById('marketplace-rec-indicator').style.display = 'flex';
                console.log("Enregistrement de l'événement démarré.");
            } catch (err) {
                console.warn("L'enregistrement n'a pas pu démarrer ou a été refusé :", err);
                alert("Attention : L'enregistrement automatique de la session pour le Marketplace n'a pas pu être lancé. Si vous souhaitez l'activer, veuillez rafraîchir et autoriser le partage de l'onglet.");
            }
        }

        async function uploadReplayVideo() {
            // Afficher un overlay d'envoi magnifique
            const uploadOverlay = document.createElement('div');
            uploadOverlay.id = 'upload-replay-overlay';
            uploadOverlay.style = "position: fixed; inset: 0; background: rgba(0,0,0,0.95); z-index: 10000; display: flex; flex-direction: column; align-items: center; justify-content: center; backdrop-filter: blur(15px); font-family: sans-serif;";
            uploadOverlay.innerHTML = `
                <div style="background: var(--brand); width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; margin-bottom: 2rem; box-shadow: 0 0 40px var(--brand-soft); animation: pulse 1.5s infinite;">⏳</div>
                <h2 style="color: #fff; margin-bottom: 1rem; font-weight: 800; font-size: 2rem;">Clôture de l'Événement</h2>
                <p id="upload-status-text" style="color: rgba(255,255,255,0.7); font-size: 1.1rem; text-align: center; max-width: 30rem; line-height: 1.6;">
                    Génération du replay vidéo final pour le Marketplace en cours... Veuillez ne pas fermer cette fenêtre.
                </p>
            `;
            document.body.appendChild(uploadOverlay);

            const blob = new Blob(recordedChunks, { type: 'video/webm' });
            const formData = new FormData();
            formData.append('video', blob, `replay_${eventCode}.webm`);

            try {
                const response = await fetch(`/e/${eventCode}/projection/upload-replay`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                });

                const data = await response.json();
                
                if (data.success) {
                    // Désactiver la protection anti-fermeture de l'onglet
                    window.removeEventListener('beforeunload', preventTabClose);

                    document.getElementById('upload-status-text').innerHTML = `
                        <span style="color: #22c55e; font-weight: 900; font-size: 3rem; display: block; margin-bottom: 1rem;">🎉</span>
                        <strong style="color: #22c55e; font-size: 1.3rem;">Sauvegarde Réussie !</strong><br><br>
                        Le replay a été sauvegardé avec succès et est maintenant disponible sur le Marketplace.<br>
                        Vous pouvez fermer cet onglet.
                    `;
                } else {
                    throw new Error(data.message || "Erreur de sauvegarde.");
                }
            } catch (err) {
                console.error("Upload error:", err);
                document.getElementById('upload-status-text').innerHTML = `
                    <span style="color: #ef4444; font-weight: 900; font-size: 3rem; display: block; margin-bottom: 1rem;">⚠️</span>
                    <strong style="color: #ef4444; font-size: 1.3rem;">Erreur de Sauvegarde</strong><br><br>
                    Impossible d'envoyer le replay automatiquement.<br>
                    <button onclick="window.location.reload()" class="btn-brand" style="margin-top: 1.5rem; background: var(--brand);">Réessayer</button>
                `;
            }
        }

        async function enableAudioOnProjector() {
            audioEnabled = true;
            document.getElementById('audio-init-overlay').style.display = 'none';
            console.log("Session démarrée.");
            audioContext = new (window.AudioContext || window.webkitAudioContext)();
            
            // Lancer l'enregistrement automatique
            await startRecording();

            // Lancer le polling
            fetchAnswering();
            setInterval(fetchAnswering, 3000);
        }


        // Initialisation PeerJS avec ID UNIQUE et Heartbeat
        let peer = null;
        const myUniqueId = `${eventCode}-PROJ-${Math.random().toString(36).substr(2, 6)}`;
        
        try {
            peer = new Peer(myUniqueId, {

                debug: 2,
                config: {
                    'iceServers': [
                        { urls: 'stun:stun.l.google.com:19302' },
                        { urls: 'stun:stun1.l.google.com:19302' },
                        { urls: 'stun:stun2.l.google.com:19302' },
                        { urls: 'stun:stun3.l.google.com:19302' },
                        { urls: 'stun:stun4.l.google.com:19302' }
                    ]
                }
            });
            
            peer.on('open', (id) => {
                console.log('Projecteur en ligne, ID:', id);
                
                // Signalement au serveur
                registerProjector(id);
                setInterval(() => registerProjector(id), 15000);

                const debugInfo = document.createElement('div');
                debugInfo.id = "peer-status";
                debugInfo.style = "position:fixed;bottom:10px;left:10px;font-size:10px;opacity:0.3;color:#fff;z-index:9999;";
                debugInfo.textContent = "Signal: OK (" + id + ")";
                document.body.appendChild(debugInfo);
            });

            async function registerProjector(id) {
                try {
                    await fetch(`/e/${eventCode}/projector/register`, {
                        method: 'POST',
                        headers: { 
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ peer_id: id })
                    });
                } catch (e) { console.error("Heartbeat error:", e); }
            }

            peer.on('error', (err) => {
                console.error("Erreur PeerJS:", err.type, err);
                const debugInfo = document.getElementById('peer-status');
                if (debugInfo) debugInfo.textContent = "Signal: ERREUR (" + err.type + ")";
                
                if (err.type === 'unavailable-id') {
                    alert("ERREUR CRITIQUE : Cette fenêtre de projection ne peut pas recevoir le partage d'écran car une autre fenêtre de projection est déjà ouverte ailleurs. Veuillez fermer les autres onglets du projecteur.");
                }
            });
            
            peer.on('call', (call) => {

                console.log("Appel entrant reçu de:", call.metadata?.name || 'Inconnu');

                
                // Si c'est un partage d'écran, on bloque le polling immédiatement
                if (call.metadata?.type === 'screenshare') {
                    isScreenSharingActive = true;
                }

                call.answer(); 
                
                call.on('stream', (remoteStream) => {
                    if (remoteStream.getVideoTracks().length > 0 || call.metadata?.type === 'screenshare') {
                        isScreenSharingActive = true;
                        const container = document.getElementById('projection-content');
                        container.innerHTML = `
                            <div style="width: 100vw; height: 100vh; background: #000; display: flex; align-items: center; justify-content: center; position: fixed; inset: 0; z-index: 100;">
                                <video id="screenshare-video" autoplay playsinline style="width: 100%; height: 100%; object-fit: contain;"></video>
                                <button onclick="toggleFullscreen()" style="position: absolute; top: 2rem; right: 2rem; background: var(--brand); color: #fff; border: none; padding: 1rem 2rem; border-radius: 999px; font-weight: 800; cursor: pointer; box-shadow: 0 10px 20px rgba(0,0,0,0.4); z-index: 110;">📺 Passer en plein écran</button>
                            </div>
                        `;

                        const video = document.getElementById('screenshare-video');
                        video.srcObject = remoteStream;

                        document.getElementById('projection-wrap').classList.add('full-mode');
                        document.getElementById('presentation-badge').style.display = 'flex';
                        document.getElementById('presenter-name').textContent = "PARTAGE D'ÉCRAN : " + (call.metadata?.name || 'Panéliste');
                        
                        remoteStream.getVideoTracks()[0].onended = () => {
                            isScreenSharingActive = false;
                            document.getElementById('projection-wrap').classList.remove('full-mode');
                            document.getElementById('presentation-badge').style.display = 'none';
                            fetchAnswering();
                        };
                    } else {
                        // Cas Audio
                        document.getElementById('live-mic-alert').style.display = 'flex';
                        document.getElementById('live-mic-author').textContent = call.metadata?.name || 'Participant';
                        document.getElementById('voice-indicator').style.display = 'flex';

                        const audio = document.createElement('audio');
                        audio.srcObject = remoteStream;
                        audio.onloadedmetadata = () => {
                            audio.play().catch(e => console.error("Erreur audio:", e));
                        };
                    }
                });
                call.on('close', () => {
                    isScreenSharingActive = false;
                    document.getElementById('projection-wrap').classList.remove('full-mode');
                    document.getElementById('presentation-badge').style.display = 'none';
                    document.getElementById('live-mic-alert').style.display = 'none';
                    document.getElementById('voice-indicator').style.display = 'none';
                    fetchAnswering();
                });
                call.on('error', (err) => {
                    console.error("Erreur PeerJS Appel:", err);
                    isScreenSharingActive = false;
                });
            });

        } catch (e) {
            console.error("PeerJS non disponible :", e);
        }



        async function fetchAnswering() {
            try {
                const response = await fetch('{{ route("projection.api", $event->code) }}');
                const data = await response.json();

                // Si l'événement est clôturé, on arrête automatiquement l'enregistrement et on l'envoie
                if (data.is_closed) {
                    if (mediaRecorder && mediaRecorder.state !== 'inactive') {
                        document.getElementById('marketplace-rec-indicator').style.display = 'none';
                        mediaRecorder.stop();
                        if (recordingStream) {
                            recordingStream.getTracks().forEach(track => track.stop());
                        }
                    }
                    return; // Fin du polling
                }
                
                // Si un partage d'écran est en cours, on ne met pas à jour le CONTENU principal
                // mais on continue de mettre à jour la sidebar et le footer.
                const container = document.getElementById('projection-content');
                const wrap = document.getElementById('projection-wrap');
                const badge = document.getElementById('presentation-badge');
                const nameSpan = document.getElementById('presenter-name');


                // Auto-play Vocal si c'est une intervention "Direct"
                if (data.type === 'contribution' && data.audio_path && lastPlayedAudioId !== data.id) {
                    lastPlayedAudioId = data.id;
                    const audio = new Audio(`/storage/${data.audio_path}`);
                    audio.play().catch(e => console.log("Lecture auto bloquée par le navigateur."));
                }

                // 1. GESTION DE LA PROJECTION (PANÉLISTE) OU QUESTIONS
                if (!isScreenSharingActive) {
                    if (data.projecting_panelist) {
                        wasProjecting = true;
                        wrap.classList.add('full-mode');
                        badge.style.display = 'flex';
                        nameSpan.textContent = "EN DIRECT : " + data.projecting_panelist.name;

                        const newPath = data.projecting_panelist.path;
                        const newPage = data.projecting_panelist.current_page || 1;

                        if (newPath !== lastProjectingPath || newPage !== lastProjectingPage) {
                            lastProjectingPath = newPath;
                            lastProjectingPage = newPage;
                            
                            let docHtml = '';
                            const ext = data.projecting_panelist.extension.toLowerCase();
                            if (ext === 'pdf') {
                                docHtml = `<iframe src="${data.projecting_panelist.url}#page=${newPage}" style="width:100%; height:100vh; border:none; background: #fff;"></iframe>`;
                            } else if (['ppt', 'pptx'].includes(ext)) {
                                docHtml = `
                                    <div id="pptx-projection-wrap" style="width: 100%; height: 100vh; background: #000; display: grid; place-items: center; overflow: hidden;">
                                        <canvas id="pptx-projection-canvas" style="max-width: 100%; max-height: 100vh; object-fit: contain;"></canvas>
                                    </div>
                                `;
                            } else {
                                docHtml = `
                                    <div style="width: 100%; height: 100vh; display: grid; place-items: center; background: #1a1a1a;">
                                        <div style="font-size: 2rem; color: #fff;">Document : ${data.projecting_panelist.path}</div>
                                    </div>
                                `;
                            }
                            container.innerHTML = docHtml;

                            if (['ppt', 'pptx'].includes(ext) && window.PptxProjection) {
                                try {
                                    await window.PptxProjection.load(data.projecting_panelist.url);
                                    await window.PptxProjection.renderSlide(newPage);
                                } catch (e) {
                                    console.error('PPTX error:', e);
                                }
                            }
                        }
                    } else {
                        wrap.classList.remove('full-mode');
                        badge.style.display = 'none';
                        if (window.PptxProjection) window.PptxProjection.reset();
                        lastProjectingPath = null;
                        lastProjectingPage = null;

                        // Gestion des questions si pas de PPT
                        if (data.id !== currentQuestionId || data.status !== currentStatus || wasProjecting) {
                            wasProjecting = false; 
                            currentQuestionId = data.id;
                            currentStatus = data.status;
                            
                            if (data.id) {
                                let contentHtml = data.content ? `<div class="question-content" style="opacity: 0; transform: translateY(20px); transition: all 0.5s;">"${data.content}"</div>` : '';
                                let audioHtml = data.audio_path ? `
                                    <div style="margin-top: 1rem; opacity: 0; transform: translateY(10px); transition: all 0.5s;" class="audio-container">
                                        <audio controls style="height: 50px; width: 400px;">
                                            <source src="/storage/${data.audio_path}" type="audio/webm">
                                        </audio>
                                    </div>
                                ` : '';
                                
                                container.innerHTML = `
                                    ${contentHtml}
                                    ${audioHtml}
                                    <div class="question-author" style="opacity: 0; transform: translateY(10px); transition: all 0.5s;">${data.pseudo}</div>
                                `;
                                setTimeout(() => {
                                    container.querySelectorAll('.question-content, .audio-container, .question-author').forEach(el => {
                                        el.style.opacity = '1';
                                        el.style.transform = 'translateY(0)';
                                    });
                                }, 50);
                            } else {
                                container.innerHTML = `<div class="empty-state" style="font-size: 2rem; color: var(--muted-foreground); font-style: italic;">En attente de la prochaine question...</div>`;
                            }
                        }
                    }
                }



                const qaList = document.getElementById('qa-list');
                if (data.all_questions) {
                    const colors = ['#8b5cf6', '#3b82f6', '#10b981', '#f59e0b', '#ef4444'];
                    qaList.innerHTML = data.all_questions.map((q, idx) => {
                        const color = colors[idx % colors.length];
                        const isActive = q.id === currentQuestionId;
                        return `
                        <div class="qa-item ${isActive ? 'active-playing' : ''}" style="border-left: 4px solid ${color}" onclick="setMainQuestion(${q.id})">
                            <div class="qa-header" style="background: ${color}">
                                <span>${q.pseudo}</span>
                                <span>#${q.id}</span>
                            </div>
                            <div class="qa-body">
                                <div class="q">
                                    ${q.content ? q.content : ''}
                                    ${q.audio_path ? '<div style="font-size: 0.75rem; color: ' + color + '; margin-top: 0.25rem;">🎤 Message Vocal</div>' : ''}
                                </div>
                                ${q.replies.length > 0 ? `<div class="a">${q.replies[0].content}</div>` : ''}
                            </div>
                        </div>
                    `}).join('');
                }

                // Update hands footer
                updateHandsFooter(data.raised_hands);

                const voiceIndicator = document.getElementById('voice-indicator');
                if (data.raised_hands && data.raised_hands.some(h => h.status === 'called')) {
                    voiceIndicator.style.display = 'flex';
                } else {
                    voiceIndicator.style.display = 'none';
                }

            } catch (error) {
                console.error('Erreur polling:', error);
            }
        }

        function updateHandsFooter(hands) {
            const footer = document.getElementById('hands-footer');
            if (hands && hands.some(h => h.status === 'called')) {
                footer.style.display = 'flex';
                footer.innerHTML = hands.filter(h => h.status === 'called').map(h => `
                    <div class="hand-card">
                        <div class="status-dot"></div>
                        <span style="font-weight: 800; font-size: 0.8rem; text-transform: uppercase;">Direct</span>
                        <span style="font-weight: 600;">${h.pseudo}</span>
                    </div>
                `).join('');
            } else {
                footer.style.display = 'none';
            }
        }

        function toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen();
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                }
            }
        }

        async function setMainQuestion(questionId) {
            try {
                await fetch(`{{ url('/projection') }}/{{ $event->code }}/set-question/${questionId}`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });
                fetchAnswering(); // Refresh immediately
            } catch (err) {
                console.error("Error setting main question:", err);
            }
        }

        // --- Défilement Automatique de la Sidebar ---
        const sidebar = document.querySelector('.qa-sidebar');
        let scrollSpeed = 0.5; // Vitesse de défilement (pixels/frame)
        let isPaused = false;

        sidebar.addEventListener('mouseenter', () => isPaused = true);
        sidebar.addEventListener('mouseleave', () => isPaused = false);

        function autoScroll() {
            if (!isPaused && sidebar.scrollHeight > sidebar.clientHeight) {
                sidebar.scrollTop += scrollSpeed;
                // Si on arrive au bout, on repart à zéro
                if (sidebar.scrollTop + sidebar.clientHeight >= sidebar.scrollHeight - 1) {
                    sidebar.scrollTop = 0;
                }
            }
            requestAnimationFrame(autoScroll);
        }
        autoScroll();

        setInterval(fetchAnswering, 3000);
        fetchAnswering();
    </script>

</body>
</html>
