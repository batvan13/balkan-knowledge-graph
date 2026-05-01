<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Entity;
use App\Models\EntityContact;
use Illuminate\Http\Request;

class EntityContactController extends Controller
{
    public function store(Request $request, Entity $entity)
    {
        $validated = $request->validate([
            'type'       => ['required', 'in:phone,mobile,email,viber,whatsapp'],
            'value'      => ['required', 'string', 'max:255'],
            'is_primary' => ['boolean'],
        ]);

        $contact = new EntityContact();
        $contact->entity_id  = $entity->id;
        $contact->type       = $validated['type'];
        $contact->value      = $validated['value'];
        $contact->is_primary = (bool) $validated['is_primary'];
        $contact->save();

        return redirect()->route('admin.entities.edit', $entity)
            ->with('success', 'Contact added.');
    }

    public function edit(Entity $entity, EntityContact $contact)
    {
        abort_if($contact->entity_id !== $entity->id, 404);

        return view('admin.entities.contacts.edit', compact('entity', 'contact'));
    }

    public function update(Request $request, Entity $entity, EntityContact $contact)
    {
        abort_if($contact->entity_id !== $entity->id, 404);

        $validated = $request->validate([
            'type'       => ['required', 'in:phone,mobile,email,viber,whatsapp'],
            'value'      => ['required', 'string', 'max:255'],
            'is_primary' => ['boolean'],
        ]);

        $contact->type       = $validated['type'];
        $contact->value      = $validated['value'];
        $contact->is_primary = (bool) $validated['is_primary'];
        $contact->save();

        return redirect()->route('admin.entities.edit', $entity)
            ->with('success', 'Contact updated.');
    }
}
