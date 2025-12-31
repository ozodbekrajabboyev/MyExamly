<div>
    {{--
        This component uses Filament's <x-filament::section> for consistent styling
        with the rest of the Filament UI (padding, background, borders, etc.).
    --}}
    <x-filament::section>
        <div class="space-y-4">
            {{-- Filter Inputs --}}
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                {{-- Sinf (Class) Dropdown --}}
                <x-filament::input.wrapper>
                    <x-slot name="label">
                        Sinfni tanlang
                    </x-slot>

                    <x-filament::input.select wire:model="sinfId">
                        <option value="">Sinfni tanlang</option>
                        @foreach($sinfs as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </x-filament::input.select>
                </x-filament::input.wrapper>

                {{-- Subject Dropdown --}}
                <x-filament::input.wrapper>
                    <x-slot name="label">
                        Fanni tanlang
                    </x-slot>

                    <x-filament::input.select wire:model="subjectId">
                        <option value="">Fanni tanlang</option>
                        @foreach($subjects as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </x-filament::input.select>
                </x-filament::input.wrapper>

                {{-- Quarter Dropdown --}}
                <x-filament::input.wrapper>
                    <x-slot name="label">
                        Chorakni tanlang
                    </x-slot>

                    <x-filament::input.select wire:model="quarter">
                        <option value="">Barcha choraklar</option>
                        <option value="I">I chorak</option>
                        <option value="II">II chorak</option>
                        <option value="III">III chorak</option>
                        <option value="IV">IV chorak</option>
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </div>

            {{-- Action Buttons --}}
            <div class="flex flex-wrap items-center justify-between gap-4">
                {{-- Main "Apply" button --}}
                <div>
                    <x-filament::button wire:click="applyFilters">
                        Hisobot qurish
                    </x-filament::button>
                </div>

                {{-- Quick quarter filter buttons --}}
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Tezkor Chorak Filtrlari:</span>
                    <x-filament::button color="gray" wire:click="filterByQuarter('I')">
                        I chorak
                    </x-filament::button>
                    <x-filament::button color="gray" wire:click="filterByQuarter('II')">
                        II chorak
                    </x-filament::button>
                    <x-filament::button color="gray" wire:click="filterByQuarter('III')">
                        III chorak
                    </x-filament::button>
                    <x-filament::button color="gray" wire:click="filterByQuarter('IV')">
                        IV chorak
                    </x-filament::button>
                    <x-filament::button color="gray" wire:click="clearQuarterFilter">
                        Barchasi
                    </x-filament::button>
                </div>
            </div>
        </div>
    </x-filament::section>

    {{-- Results Table Section --}}
    @if(!empty($studentsData))
        <x-filament::section class="mt-6">
            <x-slot name="heading">
                O'quvchilar natijalari
            </x-slot>

            <x-slot name="description">
                Tanlangan sinf va fan bo'yicha o'quvchilarning BSB va CHSB natijalari
            </x-slot>

            {{-- PDF Download Button --}}
            <div class="flex justify-end mb-4">
                <x-filament::button
                    color="success"
                    icon="heroicon-o-arrow-down-tray"
                    wire:click="downloadPdf"
                    wire:loading.attr="disabled"
                    wire:target="downloadPdf"
                >
                    <span wire:loading.remove wire:target="downloadPdf">
                        PDF yuklab olish
                    </span>
                    <span wire:loading wire:target="downloadPdf">
                        Yuklanmoqda...
                    </span>
                </x-filament::button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full border-collapse border border-gray-300 dark:border-gray-600">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="border border-gray-300 dark:border-gray-600 px-4 py-3 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">
                                №
                            </th>
                            <th class="border border-gray-300 dark:border-gray-600 px-4 py-3 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">
                                O'quvchi F.I.Sh.
                            </th>
                            <th class="border border-gray-300 dark:border-gray-600 px-4 py-3 text-center text-sm font-semibold text-gray-900 dark:text-gray-100">
                                BSB
                            </th>
                            <th class="border border-gray-300 dark:border-gray-600 px-4 py-3 text-center text-sm font-semibold text-gray-900 dark:text-gray-100">
                                CHSB
                            </th>
                            <th class="border border-gray-300 dark:border-gray-600 px-4 py-3 text-center text-sm font-semibold text-gray-900 dark:text-gray-100">
                                @if($quarter)
                                    FB ({{ $quarter }} chorak)
                                @else
                                    FB (Jami)
                                @endif
                            </th>
                            <th class="border border-gray-300 dark:border-gray-600 px-4 py-3 text-center text-sm font-semibold text-gray-900 dark:text-gray-100">
                                Umumiy natija
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($studentsData as $index => $student)
                            <tr class="{{ $index % 2 === 0 ? 'bg-white dark:bg-gray-900' : 'bg-gray-50 dark:bg-gray-800' }}">
                                <td class="border border-gray-300 dark:border-gray-600 px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                    {{ $index + 1 }}
                                </td>
                                <td class="border border-gray-300 dark:border-gray-600 px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $student['full_name'] }}
                                </td>
                                <td class="border border-gray-300 dark:border-gray-600 px-4 py-3 text-center text-sm text-gray-900 dark:text-gray-100">
                                    @if($student['bsb']['total'] > 0)
                                        <span class="font-semibold">{{ $student['bsb']['total'] }}</span>
                                    @else
                                        <span class="text-gray-400 dark:text-gray-500">-</span>
                                    @endif
                                </td>
                                <td class="border border-gray-300 dark:border-gray-600 px-4 py-3 text-center text-sm text-gray-900 dark:text-gray-100">
                                    @if($student['chsb']['total'] > 0)
                                        <span class="font-semibold">{{ $student['chsb']['total'] }}</span>
                                    @else
                                        <span class="text-gray-400 dark:text-gray-500">-</span>
                                    @endif
                                </td>
                                <td class="border border-gray-300 dark:border-gray-600 px-4 py-3 text-center text-sm text-gray-900 dark:text-gray-100">
                                    @if(isset($student['fb_marks']) && !$student['fb_marks']['is_sum'] && $canEditFbMarks)
                                        {{-- Editable FB mark for specific quarter --}}
                                        <div class="inline-flex items-center space-x-1">
                                            <input
                                                type="number"
                                                min="0"
                                                max="10"
                                                value="{{ $student['fb_marks']['fb_value'] }}"
                                                class="w-16 px-2 py-1 text-center text-sm border border-gray-300 dark:border-gray-600 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-100"
                                                wire:change="updateFbMark({{ $student['id'] }}, $event.target.value)"
                                                wire:loading.attr="disabled"
                                                wire:target="updateFbMark"
                                            />
                                            <div wire:loading wire:target="updateFbMark" class="text-xs text-gray-500">
                                                <svg class="animate-spin h-3 w-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                            </div>
                                        </div>
                                    @elseif(isset($student['fb_marks']))
                                        {{-- Read-only FB mark --}}
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100">
                                            {{ $student['fb_marks']['fb_value'] }}
                                            @if($student['fb_marks']['is_sum'])
                                                <span class="ml-1 text-xs opacity-75">(Jami)</span>
                                            @endif
                                        </span>
                                    @else
                                        <span class="text-gray-400 dark:text-gray-500">-</span>
                                    @endif
                                </td>
                                <td class="border border-gray-300 dark:border-gray-600 px-4 py-3 text-center text-sm">
                                    @php
                                        $totalSum = $student['bsb']['total'] + $student['chsb']['total'];
                                        // Add FB marks to total sum if available
                                        if (isset($student['fb_marks']) && $student['fb_marks']['fb_value']) {
                                            $totalSum += $student['fb_marks']['fb_value'];
                                        }
                                    @endphp
                                    @if($totalSum > 0)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($totalSum >= 24)
                                                bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100
                                            @elseif($totalSum >= 18)
                                                bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100
                                            @else
                                                bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100
                                            @endif">
                                            {{ $totalSum }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 dark:text-gray-500">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if(empty($studentsData))
                <div class="text-center py-8">
                    <p class="text-gray-500 dark:text-gray-400">Ma'lumot topilmadi. Iltimos, filtrlash parametrlarini tekshiring.</p>
                </div>
            @endif
        </x-filament::section>
    @elseif($sinfId && $subjectId)
        {{-- Debug Information Section --}}
        <x-filament::section class="mt-6">
            <x-slot name="heading">
                Debug Ma'lumotlari
            </x-slot>

            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                <h4 class="font-medium text-yellow-800 dark:text-yellow-200 mb-2">Tanlangan parametrlar:</h4>
                <ul class="text-sm text-yellow-700 dark:text-yellow-300 space-y-1">
                    <li><strong>Sinf ID:</strong> {{ $sinfId }}</li>
                    <li><strong>Fan ID:</strong> {{ $subjectId }}</li>
                    <li><strong>Chorak:</strong> {{ $quarter ?: 'Barcha choraklar' }}</li>
                    <li><strong>StudentsData miqdori:</strong> {{ count($studentsData) }}</li>
                </ul>
            </div>

            <div class="text-center py-8">
                <p class="text-gray-500 dark:text-gray-400">Tanlangan parametrlar bo'yicha ma'lumot topilmadi.</p>
                <p class="text-sm text-gray-400 dark:text-gray-500 mt-2">Bu quyidagi sabablar bo'lishi mumkin:</p>
                <ul class="text-sm text-gray-400 dark:text-gray-500 mt-2 space-y-1">
                    <li>• Ushbu sinf va fan uchun imtihonlar mavjud emas</li>
                    <li>• Tanlangan sana oralig'ida imtihonlar bo'lmagan</li>
                    <li>• Student_exams jadvalidagi ma'lumotlar to'ldirilmagan</li>
                </ul>

                <div class="mt-4">
                    <x-filament::button color="warning" wire:click="applyFilters">
                        Qayta urinib ko'ring
                    </x-filament::button>
                </div>
            </div>
        </x-filament::section>
    @else
        <x-filament::section class="mt-6">
            <div class="text-center py-8">
                <p class="text-gray-500 dark:text-gray-400">Hisobot ko'rish uchun sinf va fan tanlang.</p>
            </div>
        </x-filament::section>
    @endif
</div>

