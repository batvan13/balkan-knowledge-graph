<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Entity;
use App\Models\EntityAmenity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EntityAmenityController extends Controller
{
    public function sync(Request $request, Entity $entity)
    {
        $validated = $request->validate([
            'amenities'   => ['nullable', 'array'],
            'amenities.*' => ['integer', 'exists:amenities,id'],
        ]);

        $ids = array_unique($validated['amenities'] ?? []);

        DB::transaction(function () use ($entity, $ids) {
            EntityAmenity::where('entity_id', $entity->id)->delete();

            foreach ($ids as $amenityId) {
                $row = new EntityAmenity();
                $row->entity_id  = $entity->id;
                $row->amenity_id = (int) $amenityId;
                $row->save();
            }
        });

        return redirect()->route('admin.entities.edit', $entity)
            ->with('success', 'Amenities updated.');
    }
}
