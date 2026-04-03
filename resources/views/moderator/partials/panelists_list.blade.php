@forelse($panelists as $panelist)
    <div class="card" style="margin-bottom: 0.75rem; border-left: 3px solid {{ $panelist->presentation_started_at ? '#10b981' : 'var(--border)' }};">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div style="width: 2.5rem; height: 2.5rem; background: var(--brand-light); color: var(--brand); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700;">
                    {{ substr($panelist->user->name, 0, 1) }}
                </div>
                <div>
                    <h4 style="font-size: 0.875rem; font-weight: 600;">{{ $panelist->user->name }}</h4>
                    <p style="font-size: 0.75rem; color: var(--muted-foreground);">{{ $panelist->sector }}</p>
                </div>
            </div>

            <div style="text-align: right;">
                @if($panelist->presentation_started_at)
                    @php
                        $elapsed = now()->diffInMinutes($panelist->presentation_started_at);
                        $remaining = max(0, $panelist->presentation_duration - $elapsed);
                        $isLowTime = $remaining <= 5;
                    @endphp
                    <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 0.25rem;">
                        <span class="badge" style="background: {{ $isLowTime ? '#fee2e2' : '#dcfce7' }}; color: {{ $isLowTime ? '#dc2626' : '#16a34a' }}; font-size: 11px; font-weight: 700; border: 1px solid currentColor;">
                            ⏱️ {{ $remaining }} MIN RESTANTES
                        </span>
                        <div style="display: flex; gap: 0.25rem;">
                            <form action="{{ route('dashboard.moderator.panelist.extend', $panelist->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="minutes" value="5">
                                <button type="submit" class="btn-brand" style="padding: 0.2rem 0.5rem; font-size: 10px; background: #f3f4f6; color: #374151;">+5 min</button>
                            </form>
                            <form action="{{ route('dashboard.moderator.panelist.stop', $panelist->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-brand" style="padding: 0.2rem 0.5rem; font-size: 10px; background: #fee2e2; color: #dc2626;">Couper</button>
                            </form>
                        </div>
                    </div>
                @else
                    <form action="{{ route('dashboard.moderator.panelist.start', $panelist->id) }}" method="POST" style="display: flex; gap: 0.5rem; align-items: center;">
                        @csrf
                        <input type="number" name="duration" value="15" style="width: 50px; padding: 0.25rem; font-size: 11px; border: 1px solid var(--border); border-radius: 4px;">
                        <button type="submit" class="btn-brand" style="padding: 0.25rem 0.75rem; font-size: 11px;">Démarrer</button>
                    </form>
                @endif
            </div>
        </div>

        @if($panelist->is_projecting)
            <div style="margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px dashed var(--border); display: flex; align-items: center; justify-content: space-between;">
                <span style="font-size: 11px; color: var(--brand); font-weight: 600;">📺 EN TRAIN DE PROJETER</span>
                @if($panelist->presentation_path)
                    <a href="{{ asset('storage/' . $panelist->presentation_path) }}" target="_blank" style="font-size: 11px; color: #6b7280; text-decoration: underline;">Voir le doc</a>
                @endif
            </div>
        @endif
    </div>
@empty
    <p style="text-align: center; font-size: 0.75rem; color: var(--muted-foreground); padding: 1rem;">Aucun panéliste pour le moment.</p>
@endforelse
