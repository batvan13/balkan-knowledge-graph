<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccommodationDetail;
use App\Models\AttractionDetail;
use App\Models\Entity;
use App\Models\FoodPlaceDetail;
use Illuminate\Http\Request;

class EntityDetailController extends Controller
{
    public function upsert(Request $request, Entity $entity)
    {
        $entity->loadMissing('entityType');
        $family = $entity->detailFamily();

        if ($family === 'accommodation') {
            $validated = $request->validate([
                'star_rating'    => ['nullable', 'integer', 'min:1', 'max:5'],
                'check_in_from'  => ['nullable', 'date_format:H:i'],
                'check_in_to'    => ['nullable', 'date_format:H:i'],
                'check_out_from' => ['nullable', 'date_format:H:i'],
                'check_out_to'   => ['nullable', 'date_format:H:i'],
            ]);

            $detail = AccommodationDetail::where('entity_id', $entity->id)->first()
                ?? new AccommodationDetail();
            $detail->entity_id      = $entity->id;
            $detail->star_rating    = $validated['star_rating'] ?? null;
            $detail->check_in_from  = $validated['check_in_from'] ?? null;
            $detail->check_in_to    = $validated['check_in_to'] ?? null;
            $detail->check_out_from = $validated['check_out_from'] ?? null;
            $detail->check_out_to   = $validated['check_out_to'] ?? null;
            $detail->save();

        } elseif ($family === 'food_place') {
            $validated = $request->validate([
                'accepts_reservations' => ['boolean'],
                'takeaway_available'   => ['boolean'],
                'delivery_available'   => ['boolean'],
                'serves_breakfast'     => ['boolean'],
                'serves_lunch'         => ['boolean'],
                'serves_dinner'        => ['boolean'],
                'price_range'          => ['nullable', 'in:budget,midrange,premium,luxury'],
            ]);

            $detail = FoodPlaceDetail::where('entity_id', $entity->id)->first()
                ?? new FoodPlaceDetail();
            $detail->entity_id            = $entity->id;
            $detail->accepts_reservations = (bool) $validated['accepts_reservations'];
            $detail->takeaway_available   = (bool) $validated['takeaway_available'];
            $detail->delivery_available   = (bool) $validated['delivery_available'];
            $detail->serves_breakfast     = (bool) $validated['serves_breakfast'];
            $detail->serves_lunch         = (bool) $validated['serves_lunch'];
            $detail->serves_dinner        = (bool) $validated['serves_dinner'];
            $detail->price_range          = $validated['price_range'] ?? null;
            $detail->save();

        } elseif ($family === 'attraction') {
            $validated = $request->validate([
                'is_natural'              => ['nullable', 'boolean'],
                'is_cultural'             => ['nullable', 'boolean'],
                'is_indoor'               => ['nullable', 'boolean'],
                'is_outdoor'              => ['nullable', 'boolean'],
                'is_free'                 => ['nullable', 'boolean'],
                'has_entry_fee'           => ['nullable', 'boolean'],
                'estimated_visit_minutes' => ['nullable', 'integer', 'min:1', 'max:1440'],
                'is_family_friendly'      => ['nullable', 'boolean'],
                'is_accessible'           => ['nullable', 'boolean'],
                'is_seasonal'             => ['nullable', 'boolean'],
            ]);

            $boolOrNull = fn ($v) => $v !== null ? (bool) $v : null;

            $detail = AttractionDetail::where('entity_id', $entity->id)->first()
                ?? new AttractionDetail();
            $detail->entity_id              = $entity->id;
            $detail->is_natural             = $boolOrNull($validated['is_natural'] ?? null);
            $detail->is_cultural            = $boolOrNull($validated['is_cultural'] ?? null);
            $detail->is_indoor              = $boolOrNull($validated['is_indoor'] ?? null);
            $detail->is_outdoor             = $boolOrNull($validated['is_outdoor'] ?? null);
            $detail->is_free                = $boolOrNull($validated['is_free'] ?? null);
            $detail->has_entry_fee          = $boolOrNull($validated['has_entry_fee'] ?? null);
            $detail->estimated_visit_minutes = $validated['estimated_visit_minutes'] ?? null;
            $detail->is_family_friendly     = $boolOrNull($validated['is_family_friendly'] ?? null);
            $detail->is_accessible          = $boolOrNull($validated['is_accessible'] ?? null);
            $detail->is_seasonal            = $boolOrNull($validated['is_seasonal'] ?? null);
            $detail->save();

        } else {
            abort(422);
        }

        return redirect()->route('admin.entities.edit', $entity)
            ->with('success', 'Details saved.');
    }
}
