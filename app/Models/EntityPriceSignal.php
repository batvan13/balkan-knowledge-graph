<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EntityPriceSignal extends Model
{
    protected $fillable = [
        'entity_id',
        'signal_type',
        'price_category',
        'currency',
        'amount_min',
        'amount_max',
        'observed_at',
    ];

    protected $casts = [
        'observed_at' => 'datetime',
    ];

    public function entity(): BelongsTo
    {
        return $this->belongsTo(Entity::class);
    }
}
