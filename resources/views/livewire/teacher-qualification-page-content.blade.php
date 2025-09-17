<div class="space-y-6">
    {{-- Filter Section --}}
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">Filter</h3>
            <button
                wire:click="resetFilters"
                class="text-sm text-blue-600 hover:text-blue-800 font-medium"
            >
                Barchasini tozalash
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Region Select --}}
            <div>
                <label for="region" class="block text-sm font-medium text-gray-700 mb-1">
                    Viloyat
                </label>
                <select
                    wire:model.live="regionId"
                    id="region"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                >
                    <option value="">Viloyatni tanlang</option>
                    @foreach($regions as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- District Select --}}
            <div>
                <label for="district" class="block text-sm font-medium text-gray-700 mb-1">
                    Tuman
                </label>
                <select
                    wire:model.live="districtId"
                    id="district"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                    @if(empty($districts)) disabled @endif
                >
                    <option value="">Tumanni tanlang</option>
                    @if(!empty($districts))
                        @foreach($districts as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    @endif
                </select>
                @if(empty($districts) && $regionId)
                    <p class="text-xs text-gray-500 mt-1">Bu viloyatda tumanlar topilmadi</p>
                @endif
            </div>
        </div>
    </div>
</div>
