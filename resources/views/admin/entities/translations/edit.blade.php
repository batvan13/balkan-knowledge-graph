<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.entities.edit', $entity) }}" class="text-gray-400 hover:text-gray-600 text-sm">← Entity #{{ $entity->id }}</a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Translation
                <span class="text-gray-400 font-normal text-base ml-1 font-mono">{{ $translation->locale }}</span>
            </h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg p-6">

                <form method="POST" action="{{ route('admin.entities.translations.update', [$entity, $translation]) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Locale</label>
                        <div class="text-sm font-mono text-gray-700 bg-gray-50 border border-gray-200 rounded px-3 py-2">
                            {{ $translation->locale }}
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-xs font-medium text-gray-600 mb-1">
                            Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="name"
                               value="{{ old('name', $translation->name) }}"
                               maxlength="255"
                               class="w-full border-gray-300 rounded-md shadow-sm text-sm @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Address</label>
                        <input type="text"
                               name="address"
                               value="{{ old('address', $translation->address) }}"
                               maxlength="255"
                               class="w-full border-gray-300 rounded-md shadow-sm text-sm @error('address') border-red-500 @enderror">
                        @error('address')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Description</label>
                        <textarea name="description"
                                  rows="5"
                                  class="w-full border-gray-300 rounded-md shadow-sm text-sm @error('description') border-red-500 @enderror">{{ old('description', $translation->description) }}</textarea>
                        @error('description')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between">
                        <a href="{{ route('admin.entities.edit', $entity) }}" class="text-sm text-gray-500 hover:underline">Cancel</a>
                        <button type="submit"
                                class="px-5 py-2 bg-gray-800 text-white text-sm rounded hover:bg-gray-700">
                            Update Translation
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
