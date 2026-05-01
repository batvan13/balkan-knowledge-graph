<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Entity;
use App\Models\EntityRelation;
use Illuminate\Http\Request;

class EntityRelationController extends Controller
{
    private function otherEntities(Entity $entity)
    {
        return Entity::with(['entityType', 'translations'])
            ->where('id', '!=', $entity->id)
            ->orderBy('id')
            ->get();
    }

    public function store(Request $request, Entity $entity)
    {
        $validated = $request->validate([
            'to_entity_id'  => ['required', 'integer', 'exists:entities,id'],
            'relation_type' => ['required', 'in:located_in,near,part_of'],
        ]);

        if ((int) $validated['to_entity_id'] === $entity->id) {
            return back()->withInput()->withErrors(['to_entity_id' => 'An entity cannot relate to itself.']);
        }

        $exists = EntityRelation::where('from_entity_id', $entity->id)
            ->where('to_entity_id', $validated['to_entity_id'])
            ->where('relation_type', $validated['relation_type'])
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors(['relation_type' => 'This relation already exists for the selected target.']);
        }

        $relation = new EntityRelation();
        $relation->from_entity_id = $entity->id;
        $relation->to_entity_id   = (int) $validated['to_entity_id'];
        $relation->relation_type  = $validated['relation_type'];
        $relation->save();

        return redirect()->route('admin.entities.edit', $entity)
            ->with('success', 'Relation added.');
    }

    public function edit(Entity $entity, EntityRelation $relation)
    {
        abort_if($relation->from_entity_id !== $entity->id, 404);

        $otherEntities = $this->otherEntities($entity);

        return view('admin.entities.relations.edit', compact('entity', 'relation', 'otherEntities'));
    }

    public function update(Request $request, Entity $entity, EntityRelation $relation)
    {
        abort_if($relation->from_entity_id !== $entity->id, 404);

        $validated = $request->validate([
            'to_entity_id'  => ['required', 'integer', 'exists:entities,id'],
            'relation_type' => ['required', 'in:located_in,near,part_of'],
        ]);

        if ((int) $validated['to_entity_id'] === $entity->id) {
            return back()->withInput()->withErrors(['to_entity_id' => 'An entity cannot relate to itself.']);
        }

        $exists = EntityRelation::where('from_entity_id', $entity->id)
            ->where('to_entity_id', $validated['to_entity_id'])
            ->where('relation_type', $validated['relation_type'])
            ->where('id', '!=', $relation->id)
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors(['relation_type' => 'This relation already exists for the selected target.']);
        }

        $relation->to_entity_id  = (int) $validated['to_entity_id'];
        $relation->relation_type = $validated['relation_type'];
        $relation->save();

        return redirect()->route('admin.entities.edit', $entity)
            ->with('success', 'Relation updated.');
    }
}
