<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Entity;
use App\Models\EntityLink;
use Illuminate\Http\Request;

class EntityLinkController extends Controller
{
    public function store(Request $request, Entity $entity)
    {
        $validated = $request->validate([
            'type'       => ['required', 'in:website,facebook,instagram,tiktok,youtube,menu,booking'],
            'url'        => ['required', 'string', 'url', 'max:2048'],
            'is_primary' => ['boolean'],
        ]);

        $link = new EntityLink();
        $link->entity_id  = $entity->id;
        $link->type       = $validated['type'];
        $link->url        = $validated['url'];
        $link->is_primary = (bool) $validated['is_primary'];
        $link->save();

        return redirect()->route('admin.entities.edit', $entity)
            ->with('success', 'Link added.');
    }

    public function edit(Entity $entity, EntityLink $link)
    {
        abort_if($link->entity_id !== $entity->id, 404);

        return view('admin.entities.links.edit', compact('entity', 'link'));
    }

    public function update(Request $request, Entity $entity, EntityLink $link)
    {
        abort_if($link->entity_id !== $entity->id, 404);

        $validated = $request->validate([
            'type'       => ['required', 'in:website,facebook,instagram,tiktok,youtube,menu,booking'],
            'url'        => ['required', 'string', 'url', 'max:2048'],
            'is_primary' => ['boolean'],
        ]);

        $link->type       = $validated['type'];
        $link->url        = $validated['url'];
        $link->is_primary = (bool) $validated['is_primary'];
        $link->save();

        return redirect()->route('admin.entities.edit', $entity)
            ->with('success', 'Link updated.');
    }
}
