<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectionLog extends Model
{
    protected $fillable = ['event_id', 'panelist_id', 'slide_number'];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function panelist()
    {
        return $this->belongsTo(Panelist::class);
    }
}
