<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.entities.edit', $entity) }}" class="text-gray-400 hover:text-gray-600 text-sm">← Entity #{{ $entity->id }}</a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Relation</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg p-6">

                <form method="POST" action="{{ route('admin.entities.relations.update', [$entity, $relation]) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="block text-xs font-medium text-gray-600 mb-1">
                            Relation Type <span class="text-red-500">*</span>
                        </label>
                        <select name="relation_type"
                                class="w-full border-gray-300 rounded-md shadow-sm text-sm @error('relation_type') border-red-500 @enderror">
                            @foreach(['located_in', 'near', 'part_of'] as $rt)
                                <option value="{{ $rt }}" @selected(old('relation_type', $relation->relation_type) === $rt)>{{ $rt }}</option>
                            @endforeach
                        </select>
                        @error('relation_type')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="mb-6">
                        <label class="block text-xs font-medium text-gray-600 mb-1">
                            Target Entity <span class="text-red-500">*</span>
                        </label>
                        <select name="to_entity_id"
                                class="w-full border-gray-300 rounded-md shadow-sm text-sm @error('to_entity_id') border-red-500 @enderror">
                            @foreach($otherEntities as $e)
                                <option value="{{ $e->id }}"
                                        @selected((int) old('to_entity_id', $relation->to_entity_id) === $e->id)>
                                    #{{ $e->id }} [{{ $e->entityType?->code ?? '?' }}] {{ $e->translations->first()?->name ?? $e->slug }}
                                </option>
                            @endforeach
                        </select>
                        @error('to_entity_id')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="flex items-center justify-between">
                        <a href="{{ route('admin.entities.edit', $entity) }}" class="text-sm text-gray-500 hover:underline">Cancel</a>
                        <button type="submit"
                                class="px-5 py-2 bg-gray-800 text-white text-sm rounded hover:bg-gray-700">
                            Update Relation
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
