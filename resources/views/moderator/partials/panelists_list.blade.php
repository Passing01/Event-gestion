@forelse($panelists as $panelist)
    <div class="card" style="margin-bottom: 1rem; border: 1px solid {{ $panelist->presentation_started_at ? 'var(--brand)' : 'var(--border)' }}; padding: 1.25rem; border-radius: 1.25rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); position: relative; overflow: hidden;">
        @if($panelist->presentation_started_at)
            <div style="position: absolute; top: 0; left: 0; width: 4px; height: 100%; background: var(--brand);"></div>
        @endif

        <div style="display: flex; justify-content: space-between; align-items: center; gap: 1rem;">
            {{-- Infos Expert --}}
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="position: relative;">
                    <div style="width: 3.5rem; height: 3.5rem; background: var(--brand-light); color: var(--brand); border-radius: 1rem; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.25rem; border: 2px solid #fff; box-shadow: 0 0 0 2px var(--brand-soft);">
                        {{ strtoupper(substr($panelist->user->name, 0, 1)) }}
                    </div>
                    @if($panelist->presentation_started_at)
                        <span style="position: absolute; -top: 5px; -right: 5px; width: 12px; height: 12px; background: #10b981; border: 2px solid #fff; border-radius: 50%; animation: pulse 1s infinite;"></span>
                    @endif
                </div>
                <div>
                    <h4 style="font-size: 0.95rem; font-weight: 800; margin: 0; color: var(--foreground);">{{ $panelist->user->name }}</h4>
                    <p style="font-size: 0.7rem; color: var(--muted-foreground); text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em; margin: 0.1rem 0 0.25rem;">{{ $panelist->sector }}</p>
                    @if($panelist->is_projecting)
                        <span style="font-size: 0.65rem; color: var(--brand); font-weight: 800; display: flex; align-items: center; gap: 0.25rem;">
                             📺 PROJECTION ACTIVE
                        </span>
                    @endif
                </div>
            </div>

            {{-- Contrôles & Chrono --}}
            <div style="text-align: right; min-width: 8rem;">
                @if($panelist->presentation_started_at)
                    @php
                        $startTime = \Carbon\Carbon::parse($panelist->presentation_started_at);
                        $totalDurationSeconds = $panelist->presentation_duration * 60;
                        $elapsedSeconds = now()->diffInSeconds($startTime);
                        $remainingSeconds = max(0, $totalDurationSeconds - $elapsedSeconds);
                        $isLowTime = $remainingSeconds <= 300;
                    @endphp
                    
                    <div class="moderator-timer-box" data-remaining="{{ $remainingSeconds }}" style="background: {{ $isLowTime ? '#fee2e2' : 'var(--brand-light)' }}; border: 1px solid {{ $isLowTime ? '#dc2626' : 'var(--brand-soft)' }}; padding: 0.4rem 0.6rem; border-radius: 0.75rem; display: inline-flex; align-items: center; gap: 0.4rem; margin-bottom: 0.5rem;">
                        <span style="font-size: 0.8rem;">⌛</span>
                        <span class="timer-display" style="font-family: monospace; font-weight: 900; font-size: 1.125rem; color: {{ $isLowTime ? '#dc2626' : 'var(--brand)' }};">
                            {{ sprintf('%02d:%02d', floor($remainingSeconds / 60), $remainingSeconds % 60) }}
                        </span>
                    </div>

                    <div style="display: flex; gap: 0.4rem; justify-content: flex-end;">
                        <form action="{{ route('dashboard.moderator.panelist.extend', $panelist->id) }}" method="POST" style="display: flex; gap: 2px;">
                            @csrf
                            <input type="number" name="minutes" value="5" min="1" style="width: 38px; border-radius: 6px; border: 1px solid var(--border); font-size: 11px; font-weight: 700; text-align: center;">
                            <button type="submit" class="btn-brand" style="padding: 0.35rem 0.75rem; font-size: 11px; border-radius: 6px; font-weight: 800;">+ Rallonger</button>
                        </form>
                        <form action="{{ route('dashboard.moderator.panelist.stop', $panelist->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn-brand" style="padding: 0.35rem 0.75rem; font-size: 11px; background: #fee2e2; color: #dc2626; border: 1px solid #fee2e2; border-radius: 6px; font-weight: 800;">Couper</button>
                        </form>
                    </div>
                @else
                    <form action="{{ route('dashboard.moderator.panelist.start', $panelist->id) }}" method="POST" style="display: flex; flex-direction: column; gap: 0.4rem;">
                        @csrf
                        <div style="display: flex; align-items: center; gap: 0.4rem;">
                            <input type="number" name="duration" value="15" style="flex: 1; border-radius: 8px; border: 1px solid var(--border); font-size: 12px; font-weight: 700; padding: 0.4rem; text-align: center;">
                            <span style="font-size: 11px; font-weight: 700; color: var(--muted-foreground);">MIN</span>
                        </div>
                        <button type="submit" class="btn-brand" style="width: 100%; border-radius: 8px; padding: 0.5rem; font-weight: 800; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">Lancer le chrono</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
@empty
    <div style="text-align: center; padding: 1.5rem; background: #f8fafc; border-radius: 1rem; border: 1px dashed var(--border);">
        <p style="font-size: 0.8rem; color: var(--muted-foreground); margin: 0;">Aucun panéliste inscrit.</p>
    </div>
@endforelse
