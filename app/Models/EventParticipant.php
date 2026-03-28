<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventParticipant extends Model
{
    protected $fillable = [
        'event_id',
        'pseudo',
        'last_seen_at',
        'is_typing',
    ];

    protected $casts = [
        'is_typing' => 'boolean',
        'last_seen_at' => 'datetime',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
