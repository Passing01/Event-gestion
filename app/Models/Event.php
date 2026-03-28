<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'code',
        'date',
        'moderation_enabled',
        'anonymous_allowed',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
        'moderation_enabled' => 'boolean',
        'anonymous_allowed' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function raisedHands(): HasMany
    {
        return $this->hasMany(RaisedHand::class);
    }

    public function participants(): HasMany
    {
        return $this->hasMany(EventParticipant::class);
    }
}
