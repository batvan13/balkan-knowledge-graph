<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.entities.index') }}" class="text-gray-400 hover:text-gray-600 text-sm">← Entities</a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">New Entity</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg p-6">

                <form method="POST" action="{{ route('admin.entities.store') }}">
                    @csrf

                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Entity Type <span class="text-red-500">*</span>
                        </label>
                        <select name="entity_type_id"
                                class="w-full border-gray-300 rounded-md shadow-sm text-sm @error('entity_type_id') border-red-500 @enderror">
                            <option value="">— select type —</option>
                            @foreach($entityTypes as $type)
                                <option value="{{ $type->id }}" @selected(old('entity_type_id') == $type->id)>
                                    {{ $type->code }}
                                </option>
                            @endforeach
                        </select>
                        @error('entity_type_id')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Place <span class="text-red-500">*</span>
                        </label>
                        <select name="place_id"
                                class="w-full border-gray-300 rounded-md shadow-sm text-sm @error('place_id') border-red-500 @enderror">
                            <option value="">— select place —</option>
                            @foreach($places as $place)
                                <option value="{{ $place->id }}" @selected(old('place_id') == $place->id)>
                                    {{ $place->slug }} [{{ $place->type }}]
                                </option>
                            @endforeach
                        </select>
                        @error('place_id')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status"
                                class="w-full border-gray-300 rounded-md shadow-sm text-sm @error('status') border-red-500 @enderror">
                            @foreach(['draft', 'published', 'archived'] as $s)
                                <option value="{{ $s }}" @selected(old('status', 'draft') === $s)>{{ ucfirst($s) }}</option>
                            @endforeach
                        </select>
                        @error('status')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('admin.entities.index') }}" class="text-sm text-gray-500 hover:underline">Cancel</a>
                        <button type="submit"
                                class="px-5 py-2 bg-gray-800 text-white text-sm rounded hover:bg-gray-700">
                            Create Entity
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
