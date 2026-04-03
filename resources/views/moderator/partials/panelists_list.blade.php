@forelse($panelists as $panelist)
    <div class="card" style="margin-bottom: 1.5rem; border: 1px solid {{ $panelist->presentation_started_at ? 'var(--brand)' : 'var(--border)' }}; padding: 1.5rem; border-radius: 1.75rem; box-shadow: 0 10px 25px -5px rgba(0,0,0,{{ $panelist->presentation_started_at ? '0.1' : '0.05' }}); position: relative; transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); background: #fff;">
        @if($panelist->presentation_started_at)
            <div style="position: absolute; top: 0; left: 0; width: 6px; height: 100%; background: var(--brand); border-radius: 6px 0 0 6px;"></div>
        @endif

        <div style="display: grid; grid-template-columns: auto 1fr auto; align-items: center; gap: 2rem;">
            
            {{-- Zone A : Identité --}}
            <div style="position: relative;">
                <div style="width: 4.5rem; height: 4.5rem; background: {{ $panelist->presentation_started_at ? 'var(--brand-light)' : '#f1f5f9' }}; color: var(--brand); border-radius: 1.25rem; display: flex; align-items: center; justify-content: center; font-weight: 900; font-size: 1.75rem; border: 2px solid #fff; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
                    {{ strtoupper(substr($panelist->user->name, 0, 1)) }}
                </div>
                @if($panelist->presentation_started_at)
                    <span style="position: absolute; bottom: -4px; right: -4px; width: 1.25rem; height: 1.25rem; background: #10b981; border: 3px solid #fff; border-radius: 50%; box-shadow: 0 0 10px rgba(16, 185, 129, 0.4); animation: pulse 1.5s infinite;"></span>
                @endif
            </div>

            {{-- Zone B : Détails --}}
            <div>
                <h4 style="font-size: 1.25rem; font-weight: 900; margin: 0; color: var(--foreground); letter-spacing: -0.02em;">{{ $panelist->user->name }}</h4>
                <p style="font-size: 0.8rem; color: var(--muted-foreground); text-transform: uppercase; font-weight: 700; letter-spacing: 0.08em; margin: 0.2rem 0 0.5rem; opacity: 0.7;">{{ $panelist->sector }}</p>
                
                @if($panelist->is_projecting)
                    <div style="display: inline-flex; align-items: center; gap: 0.5rem; background: var(--brand-light); color: var(--brand); padding: 0.4rem 1rem; border-radius: 0.75rem; font-size: 0.75rem; font-weight: 900; border: 1px solid var(--brand-soft);">
                        <span style="width: 8px; height: 8px; background: var(--brand); border-radius: 50%; animation: pulse 1s infinite;"></span>
                        📺 PROJECTION ACTIVE
                    </div>
                @elseif($panelist->presentation_path)
                     <span style="font-size: 0.75rem; color: var(--muted-foreground); font-weight: 600; display: flex; align-items: center; gap: 0.35rem;">
                        📄 Document prêt
                    </span>
                @endif
            </div>

            {{-- Zone C : Chrono & Actions --}}
            <div style="min-width: 14rem;">
                @if($panelist->presentation_started_at)
                    @php
                        $startTime = \Carbon\Carbon::parse($panelist->presentation_started_at);
                        $totalDurationSeconds = (int) $panelist->presentation_duration * 60;
                        $elapsedSeconds = $startTime->diffInSeconds(now(), false);
                        $remainingSeconds = max(0, $totalDurationSeconds - $elapsedSeconds);
                        $isLowTime = $remainingSeconds <= 300;
                    @endphp
                    
                    <div style="display: flex; flex-direction: column; gap: 0.75rem; align-items: flex-end;">
                        <div class="moderator-timer-box" data-remaining="{{ $remainingSeconds }}" style="background: {{ $isLowTime ? '#fee2e2' : 'var(--brand-light)' }}; border: 2px solid {{ $isLowTime ? '#dc2626' : 'var(--brand-soft)' }}; padding: 0.75rem 1.25rem; border-radius: 1.25rem; display: flex; align-items: center; gap: 0.75rem; width: 100%; justify-content: center; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                            <span style="font-size: 1.25rem;">{{ $isLowTime ? '🚨' : '⏱️' }}</span>
                            <span class="timer-display" style="font-family: monospace; font-weight: 950; font-size: 1.75rem; color: {{ $isLowTime ? '#dc2626' : 'var(--brand)' }}; letter-spacing: -0.01em;">
                                {{ sprintf('%02d:%02d', floor($remainingSeconds / 60), $remainingSeconds % 60) }}
                            </span>
                        </div>

                        <div style="display: flex; gap: 0.5rem; width: 100%;">
                            <form action="{{ route('dashboard.moderator.panelist.extend', $panelist->id) }}" method="POST" style="flex: 1; display: flex; gap: 4px;">
                                @csrf
                                <input type="number" name="minutes" value="5" min="1" style="width: 2.5rem; border-radius: 0.75rem; border: 1px solid var(--border); font-size: 0.85rem; font-weight: 800; text-align: center;">
                                <button type="submit" class="btn-brand" style="flex: 1; padding: 0.5rem; font-size: 0.75rem; border-radius: 0.75rem; font-weight: 900; text-transform: uppercase;">+ Rallonger</button>
                            </form>
                            <form action="{{ route('dashboard.moderator.panelist.stop', $panelist->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-brand" style="padding: 0.5rem 1rem; font-size: 0.75rem; background: #fee2e2; color: #dc2626; border: 1px solid #fee2e2; border-radius: 0.75rem; font-weight: 900; text-transform: uppercase;">Couper</button>
                            </form>
                        </div>
                    </div>
                @else
                    <form action="{{ route('dashboard.moderator.panelist.start', $panelist->id) }}" method="POST" style="background: #f8fafc; padding: 0.75rem; border-radius: 1.25rem; border: 1px solid var(--border);">
                        @csrf
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                            <input type="number" name="duration" value="15" style="width: 100%; border-radius: 0.75rem; border: 1px solid var(--border); font-size: 1rem; font-weight: 900; padding: 0.5rem; text-align: center; color: var(--brand);">
                            <span style="font-size: 0.75rem; font-weight: 800; color: var(--muted-foreground); text-transform: uppercase;">Min</span>
                        </div>
                        <button type="submit" class="btn-brand" style="width: 100%; border-radius: 0.75rem; padding: 0.75rem; font-weight: 900; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                            <span>⚡</span> LANCER LE CHRONO
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
@empty
    <div style="grid-column: 1 / -1; text-align: center; padding: 4rem 2rem; background: #f8fafc; border-radius: 2rem; border: 2px dashed var(--border);">
        <div style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;">🧤</div>
        <p style="font-size: 1rem; color: var(--muted-foreground); font-weight: 600;">Aucun expert n'est inscrit pour le moment.</p>
    </div>
@endforelse
