<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AmenityTranslation extends Model
{
    public function amenity(): BelongsTo
    {
        return $this->belongsTo(Amenity::class);
    }
}
