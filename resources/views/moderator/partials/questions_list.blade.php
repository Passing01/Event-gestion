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
            <div class="space-y-3" id="replies-{{ $q->id }}">
                @foreach($q->replies as $reply)
                <div style="background: var(--muted); padding: 0.75rem 1rem; border-radius: 1rem; font-size: 0.875rem; border: 1px solid rgba(0,0,0,0.02);">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.4rem;">
                        <div style="display: flex; align-items: center; gap: 0.6rem;">
                            <span style="font-weight: 800; color: var(--foreground);">{{ $reply->pseudo }}</span>
                            @if($reply->is_moderator)
                                <span class="badge" style="background: var(--brand-light); color: var(--brand); font-size: 10px; font-weight: 900; border: 1px solid var(--brand-soft);">💡 SUGGESTION MODO</span>
                            @endif
                        </div>
                        <span style="font-size: 0.7rem; color: var(--muted-foreground); font-weight: 600;">{{ $reply->created_at->diffForHumans() }}</span>
                    </div>

                    @if($reply->audio_path)
                        <div style="margin: 0.5rem 0;">
                            <audio controls style="height: 35px; width: 100%;">
                                <source src="{{ asset('storage/' . $reply->audio_path) }}" type="audio/webm">
                            </audio>
                        </div>
                    @endif
                    
                    @if($reply->content)
                        <p style="margin: 0; line-height: 1.5; color: var(--foreground); opacity: 0.9;">{{ $reply->content }}</p>
                    @endif
                </div>
                @endforeach
            </div>

            {{-- Formulaire de réponse pour le Modérateur --}}
            @if($q->status != 'rejected')
            <div style="margin-top: 1.5rem; background: #fff; border: 1px solid var(--border); padding: 1rem; border-radius: 1.25rem; box-shadow: 0 5px 15px rgba(0,0,0,0.02);">
                <form action="{{ route('dashboard.moderator.reply', $q->id) }}" method="POST" id="reply-form-{{ $q->id }}" enctype="multipart/form-data">
                    @csrf
                    <div style="display: flex; gap: 0.75rem; align-items: flex-end;">
                        <div style="flex: 1; position: relative;">
                            <textarea name="content" placeholder="Suggérer une réponse ou une précision..." style="width: 100%; border: 1px solid var(--border); border-radius: 1rem; padding: 0.75rem 1rem; font-size: 0.875rem; outline: none; transition: border-color 0.2s; min-height: 60px; resize: none;" onfocus="this.style.borderColor='var(--brand)'" onblur="this.style.borderColor='var(--border)'"></textarea>
                            
                            {{-- Visualisation Audio (Masqué par défaut) --}}
                            <div id="vocal-preview-{{ $q->id }}" style="display:none; position: absolute; inset: 0; background: #fff; border-radius: 1rem; align-items: center; gap: 0.75rem; padding: 0 1rem; z-index: 10;">
                                <div class="voice-indicator" style="display: flex; gap: 3px; align-items: center;">
                                    <div style="width: 3px; height: 12px; background: var(--brand); border-radius: 2px; animation: voice-pulse 1s infinite alternate;"></div>
                                    <div style="width: 3px; height: 18px; background: var(--brand); border-radius: 2px; animation: voice-pulse 1s infinite alternate 0.2s;"></div>
                                    <div style="width: 3px; height: 10px; background: var(--brand); border-radius: 2px; animation: voice-pulse 1s infinite alternate 0.4s;"></div>
                                </div>
                                <span id="vocal-timer-{{ $q->id }}" style="font-family: monospace; font-weight: 800; font-size: 0.85rem; color: var(--brand);">00:00</span>
                                <button type="button" onclick="cancelVocal('{{ $q->id }}')" style="margin-left: auto; background: none; border: none; color: #dc2626; cursor: pointer; font-size: 0.75rem; font-weight: 800; text-transform: uppercase;">Annuler</button>
                            </div>
                        </div>

                        <div style="display: flex; gap: 0.5rem; flex-shrink: 0;">
                            <button type="button" id="btn-vocal-{{ $q->id }}" onclick="toggleVocalRecording('{{ $q->id }}')" style="background: #f1f5f9; color: #475569; width: 3rem; height: 3rem; border: none; border-radius: 1rem; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s;" class="vocal-trigger">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 1.5rem; height: 1.5rem;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 18.75a6 6 0 006-6v-1.5m-6 7.5a6 6 0 01-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15.75a3 3 0 01-3-3V4.5a3 3 0 116 0v8.25a3 3 0 01-3 3z" />
                                </svg>
                            </button>
                            <button type="submit" class="btn-brand" style="width: 3rem; height: 3rem; padding: 0; border-radius: 1rem; display: flex; align-items: center; justify-content: center;">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width: 1.5rem; height: 1.5rem;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            @endif
        </div>
    </div>
    @empty
    <div class="card" style="text-align: center; padding: 3rem; color: var(--muted-foreground);">
        Aucune question pour le moment.
    </div>
    @endforelse
</div>
