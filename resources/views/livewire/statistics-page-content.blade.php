<div>
    {{--
        This component uses Filament's <x-filament::section> for consistent styling
        with the rest of the Filament UI (padding, background, borders, etc.).
    --}}
    <x-filament::section>
        <div class="space-y-4">
            {{-- Filter Inputs --}}
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
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

                {{-- Start Date Picker --}}
                <x-filament::input.wrapper>
                    <x-slot name="label">
                        Boshlanish sanasi
                    </x-slot>
                    <x-filament::input type="date" wire:model="startDate" />
                </x-filament::input.wrapper>

                {{-- End Date Picker --}}
                <x-filament::input.wrapper>
                    <x-slot name="label">
                        Tugash sanasi
                    </x-slot>
                    <x-filament::input type="date" wire:model="endDate" />
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

                {{-- Quick filter buttons --}}
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Tezkor Filtrlar:</span>
                    <x-filament::button color="gray" wire:click="filterLast7Days">
                        So'nggi 7 kun
                    </x-filament::button>
                    <x-filament::button color="gray" wire:click="filterLast30Days">
                        So'nggi 30 kun
                    </x-filament::button>
                    <x-filament::button color="gray" wire:click="filterLast3Months">
                        So'nggi 3 oy
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
                                        <span class="inline-flex items-center gap-1">
                                            <span class="font-semibold">{{ $student['bsb']['total'] }}</span>
                                            <span class="text-gray-500">/</span>
                                            <span class="text-blue-600 dark:text-blue-400 font-semibold">{{ $student['bsb']['percentage'] }}%</span>
                                        </span>
                                    @else
                                        <span class="text-gray-400 dark:text-gray-500">-</span>
                                    @endif
                                </td>
                                <td class="border border-gray-300 dark:border-gray-600 px-4 py-3 text-center text-sm text-gray-900 dark:text-gray-100">
                                    @if($student['chsb']['total'] > 0)
                                        <span class="inline-flex items-center gap-1">
                                            <span class="font-semibold">{{ $student['chsb']['total'] }}</span>
                                            <span class="text-gray-500">/</span>
                                            <span class="text-green-600 dark:text-green-400 font-semibold">{{ $student['chsb']['percentage'] }}%</span>
                                        </span>
                                    @else
                                        <span class="text-gray-400 dark:text-gray-500">-</span>
                                    @endif
                                </td>
                                <td class="border border-gray-300 dark:border-gray-600 px-4 py-3 text-center text-sm">
                                    @if($student['overall_total'] > 0)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($student['overall_total'] >= 80)
                                                bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100
                                            @elseif($student['overall_total'] >= 60)
                                                bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100
                                            @else
                                                bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100
                                            @endif">
                                            {{ $student['overall_total'] }}%
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
                    <li><strong>Boshlanish sanasi:</strong> {{ $startDate }}</li>
                    <li><strong>Tugash sanasi:</strong> {{ $endDate }}</li>
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
