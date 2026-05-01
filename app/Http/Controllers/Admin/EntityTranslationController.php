<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Entity;
use App\Models\EntityTranslation;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EntityTranslationController extends Controller
{
    public function store(Request $request, Entity $entity)
    {
        $validated = $request->validate([
            'locale' => [
                'required',
                'in:bg,en',
                Rule::unique('entity_translations', 'locale')->where('entity_id', $entity->id),
            ],
            'name'        => ['required', 'string', 'max:255'],
            'address'     => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $translation = new EntityTranslation();
        $translation->entity_id   = $entity->id;
        $translation->locale      = $validated['locale'];
        $translation->name        = $validated['name'];
        $translation->address     = $validated['address'] ?? null;
        $translation->description = $validated['description'] ?? null;
        $translation->save();

        if ($validated['locale'] === 'bg' && str_starts_with($entity->slug, 'entity-')) {
            $newSlug = Entity::generateSlugFromBgName($validated['name'], $entity->id);
            if ($newSlug !== '') {
                $entity->slug = $newSlug;
                $entity->save();
            }
        }

        return redirect()->route('admin.entities.edit', $entity)
            ->with('success', 'Translation added.');
    }

    public function edit(Entity $entity, EntityTranslation $translation)
    {
        abort_if($translation->entity_id !== $entity->id, 404);

        return view('admin.entities.translations.edit', compact('entity', 'translation'));
    }

    public function update(Request $request, Entity $entity, EntityTranslation $translation)
    {
        abort_if($translation->entity_id !== $entity->id, 404);

        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'address'     => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $translation->name        = $validated['name'];
        $translation->address     = $validated['address'] ?? null;
        $translation->description = $validated['description'] ?? null;
        $translation->save();

        return redirect()->route('admin.entities.edit', $entity)
            ->with('success', 'Translation updated.');
    }
}
