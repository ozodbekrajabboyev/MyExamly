<div class="space-y-6">
    {{-- Filter Section --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Filter</h3>
            <button
                wire:click="resetFilters"
                class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium"
            >
                Barchasini tozalash
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Region Select --}}
            <div>
                <label for="region" class="block text-sm font-medium text-gray-700 mb-1">
                    <p class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium">Viloyat</p>
                </label>
                <select
                    wire:model.live="regionId"
                    id="region"
                    class="block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700
                           text-gray-900 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                >
                    <option value="">Viloyatni tanlang</option>
                    @foreach($regions as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- District Select --}}
            <div>
                <label for="district" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Tuman
                </label>
                <select
                    wire:model.live="districtId"
                    id="district"
                    class="block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700
                           text-gray-900 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
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
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Bu viloyatda tumanlar topilmadi</p>
                @endif
            </div>

            {{-- School Select --}}
            <div>
                <label for="school" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Maktab
                </label>
                <select
                    wire:model.live="maktabId"
                    id="school"
                    class="block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700
                           text-gray-900 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                    @if(empty($maktabs)) disabled @endif
                >
                    <option value="">Maktabni tanlang</option>
                    @if(!empty($maktabs))
                        @foreach($maktabs as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    @endif
                </select>
                @if(empty($maktabs) && $districtId)
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Bu tumanda maktablar topilmadi</p>
                @endif
            </div>

            {{-- Qualification Level Select --}}
            <div>
                <label for="qualification" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Malaka darajasi
                </label>
                <select
                    wire:model.live="malakaDarajaId"
                    id="qualification"
                    class="block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700
                           text-gray-900 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                >
                    <option value="">Malaka darajasini tanlang</option>
                    @if(!empty($malakaDarajas))
                        @foreach($malakaDarajas as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>
    </div>
</div>
