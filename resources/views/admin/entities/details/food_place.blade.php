<form method="POST" action="{{ route('admin.entities.details.upsert', $entity) }}">
    @csrf

    <div class="grid grid-cols-2 gap-x-8 gap-y-3 mb-6 text-sm">
        @foreach([
            'accepts_reservations' => 'Accepts Reservations',
            'takeaway_available'   => 'Takeaway Available',
            'delivery_available'   => 'Delivery Available',
            'serves_breakfast'     => 'Serves Breakfast',
            'serves_lunch'         => 'Serves Lunch',
            'serves_dinner'        => 'Serves Dinner',
        ] as $field => $label)
            <div class="flex items-center gap-2">
                <input type="hidden" name="{{ $field }}" value="0">
                <input type="checkbox"
                       name="{{ $field }}"
                       value="1"
                       id="fp_{{ $field }}"
                       @checked((bool) old($field, $detail?->{$field} ?? false))
                       class="rounded border-gray-300 text-gray-800">
                <label for="fp_{{ $field }}" class="text-xs font-medium text-gray-600 select-none">{{ $label }}</label>
            </div>
        @endforeach
    </div>

    <div class="mb-6">
        <label class="block text-xs font-medium text-gray-600 mb-1">Price Range</label>
        <select name="price_range" class="w-48 border-gray-300 rounded-md shadow-sm text-sm @error('price_range') border-red-500 @enderror">
            <option value="">—</option>
            @foreach(['budget', 'midrange', 'premium', 'luxury'] as $pr)
                <option value="{{ $pr }}" @selected(old('price_range', $detail?->price_range) === $pr)>{{ ucfirst($pr) }}</option>
            @endforeach
        </select>
        @error('price_range')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    <div class="flex justify-end">
        <button type="submit" class="px-4 py-2 bg-gray-800 text-white text-sm rounded hover:bg-gray-700">
            Save Details
        </button>
    </div>
</form>
