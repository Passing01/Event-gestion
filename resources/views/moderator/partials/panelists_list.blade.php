@forelse($panelists as $panelist)
    <div class="card" style="margin: 0; border: 1px solid {{ $panelist->presentation_started_at ? 'var(--brand)' : 'var(--border)' }}; padding: 1rem; border-radius: 1.25rem; box-shadow: 0 4px 15px rgba(0,0,0,0.05); position: relative; transition: all 0.3s ease; background: #fff; flex: 1 1 350px; max-width: 420px; display: flex; flex-direction: column; gap: 1rem;">
        @if($panelist->presentation_started_at)
            <div style="position: absolute; top: 0; left: 0; width: 4px; height: 100%; background: var(--brand); border-radius: 4px 0 0 4px;"></div>
        @endif

        <div style="display: flex; align-items: flex-start; gap: 1rem;">
            {{-- Zone A : Identité --}}
            <div style="flex-shrink: 0; position: relative;">
                <div style="width: 3.5rem; height: 3.5rem; background: {{ $panelist->presentation_started_at ? 'var(--brand-light)' : '#f1f5f9' }}; color: var(--brand); border-radius: 1rem; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.25rem; border: 2px solid #fff;">
                    {{ strtoupper(substr($panelist->user->name, 0, 1)) }}
                </div>
                @if($panelist->presentation_started_at)
                    <span style="position: absolute; -bottom: 2px; -right: 2px; width: 12px; height: 12px; background: #10b981; border: 2px solid #fff; border-radius: 50%; animation: pulse 1s infinite;"></span>
                @endif
            </div>

            {{-- Zone B : Détails --}}
            <div style="flex-grow: 1; overflow: hidden;">
                <h4 style="font-size: 1rem; font-weight: 800; margin: 0; color: var(--foreground); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $panelist->user->name }}</h4>
                <p style="font-size: 0.7rem; color: var(--muted-foreground); text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em; margin: 0.1rem 0 0.5rem;">{{ $panelist->sector }}</p>
                
                @if($panelist->is_projecting)
                    <div style="display: inline-flex; align-items: center; gap: 0.4rem; background: var(--brand-light); color: var(--brand); padding: 0.25rem 0.6rem; border-radius: 0.5rem; font-size: 0.65rem; font-weight: 800; border: 1px solid var(--brand-soft);">
                        📡 DIRECT
                    </div>
                @elseif($panelist->presentation_path)
                     <span style="font-size: 0.65rem; color: var(--muted-foreground); font-weight: 600; display: flex; align-items: center; gap: 0.25rem;">
                        📄 Prêt
                    </span>
                @endif
            </div>
        </div>

        {{-- Zone C : Chrono & Actions --}}
        <div style="border-top: 1px solid var(--border); padding-top: 1rem;">
            @if($panelist->presentation_started_at)
                @php
                    $totalDurationSeconds = (int) $panelist->presentation_duration * 60;
                    if (isset($panelist->remaining_seconds)) {
                        $remainingSeconds = $panelist->remaining_seconds;
                    } else {
                        $startTime = \Carbon\Carbon::parse($panelist->presentation_started_at);
                        $elapsedSeconds = $startTime->diffInSeconds(now(), false);
                        $remainingSeconds = max(0, $totalDurationSeconds - max(0, $elapsedSeconds));
                    }
                    $isLowTime = $remainingSeconds <= 300;
                @endphp
                
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem;">
                    <div class="moderator-timer-box" data-remaining="{{ $remainingSeconds }}" style="background: {{ $isLowTime ? '#fee2e2' : 'var(--brand-light)' }}; padding: 0.5rem 0.75rem; border-radius: 0.75rem; flex: 1; text-align: center; border: 1px solid {{ $isLowTime ? '#dc2626' : 'var(--brand-soft)' }};">
                        <span class="timer-display" style="font-family: monospace; font-weight: 900; font-size: 1.25rem; color: {{ $isLowTime ? '#dc2626' : 'var(--brand)' }};">
                            {{ sprintf('%02d:%02d', floor($remainingSeconds / 60), $remainingSeconds % 60) }}
                        </span>
                    </div>

                    <div style="display: flex; gap: 0.4rem; flex: 1;">
                        <form action="{{ route('dashboard.moderator.panelist.extend', $panelist->id) }}" method="POST" style="display: flex; gap: 2px; flex: 1;">
                            @csrf
                            <input type="number" name="minutes" value="5" min="1" style="width: 2rem; border-radius: 0.5rem; border: 1px solid var(--border); font-size: 0.75rem; text-align: center;">
                            <button type="submit" class="btn-brand" style="flex: 1; padding: 0.4rem; font-size: 0.65rem; border-radius: 0.5rem; font-weight: 800;">+ RALLONGER</button>
                        </form>
                        <form action="{{ route('dashboard.moderator.panelist.stop', $panelist->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn-brand" style="padding: 0.4rem 0.6rem; font-size: 0.65rem; background: #fee2e2; color: #dc2626; border: 1px solid #fee2e2; border-radius: 0.5rem; font-weight: 800;">COUPE</button>
                        </form>
                    </div>
                </div>
            @else
                <form action="{{ route('dashboard.moderator.panelist.start', $panelist->id) }}" method="POST" style="display: flex; align-items: center; gap: 0.5rem;">
                    @csrf
                    <div style="display: flex; align-items: center; gap: 0.3rem; background: #f8fafc; border: 1px solid var(--border); padding: 0.25rem 0.5rem; border-radius: 0.5rem; width: 6rem;">
                        <input type="number" name="duration" value="15" style="width: 100%; border: none; background: transparent; font-size: 0.9rem; font-weight: 800; text-align: center; color: var(--brand);">
                        <span style="font-size: 0.6rem; font-weight: 700; color: var(--muted-foreground);">MIN</span>
                    </div>
                    <button type="submit" class="btn-brand" style="flex: 1; border-radius: 0.5rem; padding: 0.6rem; font-weight: 800; font-size: 0.7rem; text-transform: uppercase;">
                        ⚡ LANCER CHRONO
                    </button>
                </form>
            @endif
        </div>
    </div>
@empty
    <div style="grid-column: 1 / -1; text-align: center; padding: 4rem 2rem; background: #f8fafc; border-radius: 2rem; border: 2px dashed var(--border);">
        <div style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;">🧤</div>
        <p style="font-size: 1rem; color: var(--muted-foreground); font-weight: 600;">Aucun expert n'est inscrit pour le moment.</p>
    </div>
@endforelse
