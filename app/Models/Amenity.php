<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Amenity extends Model
{
    public function translations(): HasMany
    {
        return $this->hasMany(AmenityTranslation::class);
    }

    public function entityAmenities(): HasMany
    {
        return $this->hasMany(EntityAmenity::class);
    }
}
