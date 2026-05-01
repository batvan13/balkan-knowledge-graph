<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Entities</h2>
            <a href="{{ route('admin.entities.create') }}"
               class="px-4 py-2 bg-gray-800 text-white text-sm rounded hover:bg-gray-700">
                New Entity
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <form method="GET" action="{{ route('admin.entities.index') }}" class="mb-4 flex items-center gap-3">
                <select name="status" class="border-gray-300 rounded text-sm">
                    <option value="">All statuses</option>
                    @foreach (['draft', 'published', 'archived'] as $s)
                        <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-3 py-1.5 bg-gray-600 text-white text-sm rounded hover:bg-gray-500">Filter</button>
                @if(request('status'))
                    <a href="{{ route('admin.entities.index') }}" class="text-sm text-gray-500 hover:underline">Clear</a>
                @endif
            </form>

            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                @if($entities->isEmpty())
                    <div class="p-6 text-gray-500 text-sm">
                        No entities found.
                        <a href="{{ route('admin.entities.create') }}" class="text-blue-600 hover:underline ml-1">Create the first one.</a>
                    </div>
                @else
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 border-b border-gray-200 text-gray-500 uppercase text-xs tracking-wide">
                            <tr>
                                <th class="px-4 py-3">ID</th>
                                <th class="px-4 py-3">Type</th>
                                <th class="px-4 py-3">Place</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Slug</th>
                                <th class="px-4 py-3">Created</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($entities as $entity)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-gray-400">{{ $entity->id }}</td>
                                    <td class="px-4 py-3 font-mono">{{ $entity->entityType?->code ?? '—' }}</td>
                                    <td class="px-4 py-3">{{ $entity->place?->slug ?? '—' }}</td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-0.5 rounded text-xs font-medium
                                            @if($entity->status === 'published') bg-green-100 text-green-800
                                            @elseif($entity->status === 'archived') bg-gray-200 text-gray-600
                                            @else bg-yellow-100 text-yellow-800
                                            @endif">
                                            {{ $entity->status }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 font-mono text-xs text-gray-400">{{ $entity->slug }}</td>
                                    <td class="px-4 py-3 text-gray-400">{{ $entity->created_at->format('Y-m-d') }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('admin.entities.edit', $entity) }}"
                                           class="text-blue-600 hover:underline text-sm">Edit</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    @if($entities->hasPages())
                        <div class="px-4 py-3 border-t border-gray-100">
                            {{ $entities->links() }}
                        </div>
                    @endif
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
