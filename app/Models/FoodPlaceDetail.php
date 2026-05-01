<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FoodPlaceDetail extends Model
{
    public function entity(): BelongsTo
    {
        return $this->belongsTo(Entity::class);
    }
}
