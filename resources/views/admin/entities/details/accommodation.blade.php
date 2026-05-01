<form method="POST" action="{{ route('admin.entities.details.upsert', $entity) }}">
    @csrf

    <div class="grid grid-cols-2 gap-4 mb-4">
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Star Rating</label>
            <select name="star_rating" class="w-full border-gray-300 rounded-md shadow-sm text-sm @error('star_rating') border-red-500 @enderror">
                <option value="">—</option>
                @foreach([1,2,3,4,5] as $s)
                    <option value="{{ $s }}" @selected((old('star_rating', $detail?->star_rating)) == $s)>{{ $s }}</option>
                @endforeach
            </select>
            @error('star_rating')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4 mb-4">
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Check-in From</label>
            <input type="time" name="check_in_from"
                   value="{{ old('check_in_from', substr($detail?->check_in_from ?? '', 0, 5)) }}"
                   class="w-full border-gray-300 rounded-md shadow-sm text-sm @error('check_in_from') border-red-500 @enderror">
            @error('check_in_from')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Check-in To</label>
            <input type="time" name="check_in_to"
                   value="{{ old('check_in_to', substr($detail?->check_in_to ?? '', 0, 5)) }}"
                   class="w-full border-gray-300 rounded-md shadow-sm text-sm @error('check_in_to') border-red-500 @enderror">
            @error('check_in_to')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4 mb-6">
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Check-out From</label>
            <input type="time" name="check_out_from"
                   value="{{ old('check_out_from', substr($detail?->check_out_from ?? '', 0, 5)) }}"
                   class="w-full border-gray-300 rounded-md shadow-sm text-sm @error('check_out_from') border-red-500 @enderror">
            @error('check_out_from')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Check-out To</label>
            <input type="time" name="check_out_to"
                   value="{{ old('check_out_to', substr($detail?->check_out_to ?? '', 0, 5)) }}"
                   class="w-full border-gray-300 rounded-md shadow-sm text-sm @error('check_out_to') border-red-500 @enderror">
            @error('check_out_to')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
    </div>

    <div class="flex justify-end">
        <button type="submit" class="px-4 py-2 bg-gray-800 text-white text-sm rounded hover:bg-gray-700">
            Save Details
        </button>
    </div>
</form>
