<div x-data="{ 
    showTeacherModal: false, 
    selectedTeacher: null,
    openTeacherModal(teacherId) {
        this.selectedTeacher = teacherId;
        this.showTeacherModal = true;
    }
}" class="space-y-6">
    
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Teachers -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="p-3 bg-blue-500 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Jami O'qituvchilar</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($statistics['total']) }}</p>
                </div>
            </div>
        </div>

        <!-- Malaka Statistics -->
        @php
            $malakaColors = [
                '1-toifa' => 'bg-purple-500',
                '2-toifa' => 'bg-yellow-500', 
                'oliy-toifa' => 'bg-green-500',
                'mutaxasis' => 'bg-blue-500'
            ];
            $malakaLabels = [
                '1-toifa' => '1-toifa',
                '2-toifa' => '2-toifa', 
                'oliy-toifa' => 'Oliy toifa',
                'mutaxasis' => 'Mutaxasis'
            ];
        @endphp
        @foreach(['1-toifa', '2-toifa', 'oliy-toifa', 'mutaxasis'] as $malaka)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="p-3 {{ $malakaColors[$malaka] }} rounded-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">{{ $malakaLabels[$malaka] }}</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($statistics['malaka_stats'][$malaka] ?? 0) }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Filters Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
            <h2 class="text-lg font-semibold text-gray-900">Filtrlar</h2>
            <button 
                wire:click="clearFilters" 
                class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Filterni Tozalash
            </button>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- Search -->
            <div class="lg:col-span-1">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Qidirish</label>
                <input 
                    type="text" 
                    id="search"
                    wire:model.live.debounce.300ms="search"
                    placeholder="O'qituvchi nomi, email..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                >
            </div>
            
            <!-- Region Filter -->
            <div class="lg:col-span-1">
                <label for="region" class="block text-sm font-medium text-gray-700 mb-2">Viloyat</label>
                <select 
                    id="region"
                    wire:model.live="selectedRegion"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                >
                    <option value="">Barcha viloyatlar</option>
                    @foreach($regions as $region)
                        <option value="{{ $region->id }}">{{ $region->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <!-- District Filter -->
            <div class="lg:col-span-1">
                <label for="district" class="block text-sm font-medium text-gray-700 mb-2">Tuman/Shahar</label>
                <select 
                    id="district"
                    wire:model.live="selectedDistrict"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    @if(!$selectedRegion) disabled @endif
                >
                    <option value="">Barcha tumanlar</option>
                    @foreach($districts as $district)
                        <option value="{{ $district->id }}">{{ $district->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <!-- School Filter -->
            <div class="lg:col-span-1">
                <label for="school" class="block text-sm font-medium text-gray-700 mb-2">Maktab</label>
                <select 
                    id="school"
                    wire:model.live="selectedMaktab"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    @if(!$selectedRegion) disabled @endif
                >
                    <option value="">Barcha maktablar</option>
                    @foreach($maktabs as $maktab)
                        <option value="{{ $maktab->id }}">{{ $maktab->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <!-- Malaka Filter -->
            <div class="lg:col-span-1">
                <label for="malaka" class="block text-sm font-medium text-gray-700 mb-2">Malaka Daraja</label>
                <select 
                    id="malaka"
                    wire:model.live="selectedMalakaDaraja"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                >
                    <option value="">Barcha malaka darajalar</option>
                    @foreach($malakaOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Teachers Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">O'qituvchilar Ro'yxati</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            O'qituvchi
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Maktab
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Telefon
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Malaka Daraja
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Fanlar
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Amallar
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($teachers as $teacher)
                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        @if($teacher->passport_photo_path)
                                            <img class="h-10 w-10 rounded-full object-cover" src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($teacher->passport_photo_path) }}" alt="{{ $teacher->full_name }}">
                                        @else
                                            <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                <svg class="h-6 w-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $teacher->full_name }}</div>
                                        <div class="text-sm text-gray-500">{{ $teacher->user->email ?? 'Email kiritilmagan' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $teacher->maktab->name ?? 'Maktab belgilanmagan' }}</div>
                                <div class="text-sm text-gray-500">
                                    {{ $teacher->maktab->district->name ?? '' }}{{ $teacher->maktab->district->name && $teacher->maktab->district->region->name ? ', ' : '' }}{{ $teacher->maktab->district->region->name ?? '' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $teacher->phone ?: 'Kiritilmagan' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($teacher->malaka_toifa_daraja)
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                        @switch($teacher->malaka_toifa_daraja)
                                            @case('oliy-toifa')
                                                bg-green-100 text-green-800
                                                @break
                                            @case('mutaxasis')
                                                bg-blue-100 text-blue-800
                                                @break
                                            @case('1-toifa')
                                                bg-purple-100 text-purple-800
                                                @break
                                            @case('2-toifa')
                                                bg-yellow-100 text-yellow-800
                                                @break
                                            @default
                                                bg-gray-100 text-gray-800
                                        @endswitch
                                    ">
                                        {{ ucfirst(str_replace('-', ' ', $teacher->malaka_toifa_daraja)) }}
                                    </span>
                                @else
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                        Belgilanmagan
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($teacher->subjects->count() > 0)
                                    {{ $teacher->subjects->pluck('name')->implode(', ') }}
                                @else
                                    Fanlar belgilanmagan
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a 
                                    href="{{ route('filament.app.resources.teachers.view', $teacher) }}"
                                    target="_blank"
                                    class="text-indigo-600 hover:text-indigo-900 transition-colors duration-200"
                                >
                                    Ko'rish
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">O'qituvchi topilmadi</h3>
                                    <p class="text-gray-500">Tanlangan filtrlar bo'yicha hech qanday o'qituvchi topilmadi.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($teachers->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $teachers->links() }}
            </div>
        @endif
    </div>
</div>