<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Entity;
use App\Models\EntityTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
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
            $newSlug = $this->generateEntitySlug($validated['name'], $entity->id);
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

    private function transliterateBg(string $name): string
    {
        $map = [
            'а' => 'a',   'б' => 'b',   'в' => 'v',   'г' => 'g',   'д' => 'd',
            'е' => 'e',   'ж' => 'zh',  'з' => 'z',   'и' => 'i',   'й' => 'y',
            'к' => 'k',   'л' => 'l',   'м' => 'm',   'н' => 'n',   'о' => 'o',
            'п' => 'p',   'р' => 'r',   'с' => 's',   'т' => 't',   'у' => 'u',
            'ф' => 'f',   'х' => 'h',   'ц' => 'ts',  'ч' => 'ch',  'ш' => 'sh',
            'щ' => 'sht', 'ъ' => 'a',   'ь' => '',    'ю' => 'yu',  'я' => 'ya',
            'А' => 'A',   'Б' => 'B',   'В' => 'V',   'Г' => 'G',   'Д' => 'D',
            'Е' => 'E',   'Ж' => 'Zh',  'З' => 'Z',   'И' => 'I',   'Й' => 'Y',
            'К' => 'K',   'Л' => 'L',   'М' => 'M',   'Н' => 'N',   'О' => 'O',
            'П' => 'P',   'Р' => 'R',   'С' => 'S',   'Т' => 'T',   'У' => 'U',
            'Ф' => 'F',   'Х' => 'H',   'Ц' => 'Ts',  'Ч' => 'Ch',  'Ш' => 'Sh',
            'Щ' => 'Sht', 'Ъ' => 'A',   'Ь' => '',    'Ю' => 'Yu',  'Я' => 'Ya',
        ];

        return strtr($name, $map);
    }

    private function generateEntitySlug(string $name, int $entityId): string
    {
        $base = Str::slug($this->transliterateBg($name));

        if ($base === '') {
            return '';
        }

        if (!Entity::where('slug', $base)->exists()) {
            return $base;
        }

        return $base . '-' . $entityId;
    }
}
