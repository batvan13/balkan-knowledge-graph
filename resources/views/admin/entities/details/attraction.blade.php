@php
    $triState = fn(string $field) => old($field, $detail ? (
        $detail->{$field} === null ? '' : ($detail->{$field} ? '1' : '0')
    ) : '');
@endphp

<form method="POST" action="{{ route('admin.entities.details.upsert', $entity) }}">
    @csrf

    <div class="grid grid-cols-2 gap-x-8 gap-y-4 mb-6 text-sm">
        @foreach([
            'is_natural'        => 'Natural',
            'is_cultural'       => 'Cultural',
            'is_indoor'         => 'Indoor',
            'is_outdoor'        => 'Outdoor',
            'is_free'           => 'Free Entry',
            'has_entry_fee'     => 'Has Entry Fee',
            'is_family_friendly'=> 'Family Friendly',
            'is_accessible'     => 'Accessible',
            'is_seasonal'       => 'Seasonal',
        ] as $field => $label)
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">{{ $label }}</label>
                <select name="{{ $field }}"
                        class="w-full border-gray-300 rounded-md shadow-sm text-sm @error($field) border-red-500 @enderror">
                    <option value="" @selected($triState($field) === '')>—</option>
                    <option value="1" @selected($triState($field) === '1')>Yes</option>
                    <option value="0" @selected($triState($field) === '0')>No</option>
                </select>
                @error($field)<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        @endforeach
    </div>

    <div class="mb-6">
        <label class="block text-xs font-medium text-gray-600 mb-1">Estimated Visit (minutes)</label>
        <input type="number"
               name="estimated_visit_minutes"
               value="{{ old('estimated_visit_minutes', $detail?->estimated_visit_minutes) }}"
               min="1" max="1440"
               class="w-32 border-gray-300 rounded-md shadow-sm text-sm @error('estimated_visit_minutes') border-red-500 @enderror">
        @error('estimated_visit_minutes')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    <div class="flex justify-end">
        <button type="submit" class="px-4 py-2 bg-gray-800 text-white text-sm rounded hover:bg-gray-700">
            Save Details
        </button>
    </div>
</form>
