<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlaceTranslation extends Model
{
    public function place(): BelongsTo
    {
        return $this->belongsTo(Place::class);
    }
}
