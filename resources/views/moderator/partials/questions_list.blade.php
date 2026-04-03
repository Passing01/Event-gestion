{{-- Flux de questions NORMALES --}}
<div class="space-y-4">
    @forelse($questions as $q)
    <div class="card" id="q-{{ $q->id }}" style="border-left: 4px solid {{ $q->status == 'answering' ? 'var(--brand)' : ($q->status == 'approved' ? '#059669' : ($q->status == 'answered' ? '#6b7280' : ($q->status == 'rejected' ? '#dc2626' : 'var(--border)'))) }};">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem;">
            <div>
                <span class="badge" style="margin-bottom: 0.5rem; background: {{ $q->status == 'answered' ? '#f3f4f6' : '' }}; color: {{ $q->status == 'answered' ? '#6b7280' : '' }};">
                    {{ strtoupper($q->status == 'answering' ? 'en cours' : ($q->status == 'answered' ? 'répondu' : $q->status)) }}
                </span>
                
                @if($q->type == 'contribution')
                    <span class="badge" style="background: #e0f2fe; color: #0369a1; font-size: 10px; margin-bottom: 0.5rem;">💡 APPORT</span>
                @else
                    <span class="badge" style="background: #f0fdf4; color: #15803d; font-size: 10px; margin-bottom: 0.5rem;">❓ QUESTION</span>
                @endif

                @if($q->panelist)
                    <span class="badge" style="background: #f3f4f6; color: #374151; font-size: 10px; border: 1px solid var(--border); margin-bottom: 0.5rem;">ADRESSÉ À : {{ $q->panelist->pseudo }}</span>
                @endif
                <p style="font-size: 1rem; font-weight: 500;">{{ $q->content }}</p>
                
                @if($q->audio_path)
                    <div style="margin-top: 0.5rem;">
                        <audio controls style="height: 30px; max-width: 100%;">
                            <source src="{{ asset('storage/' . $q->audio_path) }}" type="audio/webm">
                        </audio>
                    </div>
                @endif

                <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.75rem; color: var(--muted-foreground); margin-top: 0.5rem;">
                    <span>Par <strong>{{ $q->pseudo }}</strong></span>
                    <span>•</span>
                    <span>{{ $q->created_at->diffForHumans() }}</span>
                    <span>•</span>
                    <span>{{ $q->votes_count }} votes</span>
                </div>
            </div>
            
            {{-- Actions --}}
            <div style="display: flex; gap: 0.5rem;">
                @if($q->status == 'approved')
                <form action="{{ route('dashboard.moderator.status', $q->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="status" value="answering">
                    <button type="submit" class="btn-brand" style="padding: 0.375rem 0.75rem; font-size: 0.75rem;" title="Projeter la question">Projeter</button>
                </form>
                @endif

                @if($q->status == 'answering')
                <form action="{{ route('dashboard.moderator.status', $q->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="status" value="answered">
                    <button type="submit" class="btn-brand" style="background: #6b7280; padding: 0.375rem 0.75rem; font-size: 0.75rem;" title="Marquer comme répondu">Terminer</button>
                </form>
                @endif

                @if($q->status == 'pending' || $q->status == 'rejected')
                <form action="{{ route('dashboard.moderator.status', $q->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="status" value="approved">
                    <button type="submit" style="background: #ecfdf5; color: #059669; border: none; border-radius: 0.5rem; padding: 0.375rem 0.75rem; font-size: 0.75rem; cursor: pointer;">Approuver</button>
                </form>
                @endif

                @if($q->status != 'rejected' && $q->status != 'answered')
                <form action="{{ route('dashboard.moderator.status', $q->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="status" value="rejected">
                    <button type="submit" style="background: #fef2f2; color: #dc2626; border: none; border-radius: 0.5rem; padding: 0.375rem 0.75rem; font-size: 0.75rem; cursor: pointer;">Rejeter</button>
                </form>
                @endif

                <button onclick="openEditModal('{{ $q->id }}', '{{ addslashes($q->content) }}')" style="background: var(--muted); border: none; border-radius: 0.5rem; padding: 0.375rem; cursor: pointer;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 1rem; height: 1rem;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- Réponses --}}
        <div style="margin-top: 1rem; padding-left: 1.5rem; border-left: 2px solid var(--muted);">
            <div class="space-y-2">
                @foreach($q->replies as $reply)
                <div style="background: var(--muted); padding: 0.5rem 0.75rem; border-radius: 0.5rem; font-size: 0.875rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                        <span style="font-weight: 600;">{{ $reply->pseudo }}</span>
                        @if($reply->is_moderator)
                            @if($reply->pseudo == 'Modérateur')
                                <span class="badge" style="background: #f1f5f9; color: #475569; font-size: 0.625rem;">MA SUGGESTION</span>
                            @else
                                <span class="badge" style="background: var(--brand-light); color: var(--brand); font-size: 0.625rem;">RÉPONSE OFFICIELLE</span>
                            @endif
                        @endif
                        <span style="font-size: 0.75rem; color: var(--muted-foreground);">{{ $reply->created_at->diffForHumans() }}</span>
                    </div>
                    <p>{{ $reply->content }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @empty
    <div class="card" style="text-align: center; padding: 3rem; color: var(--muted-foreground);">
        Aucune question pour le moment.
    </div>
    @endforelse
</div>
