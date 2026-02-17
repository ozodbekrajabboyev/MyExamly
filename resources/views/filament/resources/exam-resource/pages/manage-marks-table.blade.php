<x-filament-panels::page>
    <style>
        /* Table container */
        .marks-table-container {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .marks-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .marks-table th,
        .marks-table td {
            padding: 0.5rem 0.75rem;
            border-bottom: 1px solid #e5e7eb;
            white-space: nowrap;
        }

        .marks-table thead th {
            background-color: #f9fafb;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            color: #6b7280;
            border-bottom: 2px solid #e5e7eb;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        /* Zebra striping */
        .marks-table tbody tr:nth-child(even) td {
            background-color: #f9fafb;
        }

        .marks-table tbody tr:hover td {
            background-color: #f3f4f6;
        }

        /* Number column */
        .marks-table .col-num {
            width: 40px;
            text-align: center;
            color: #9ca3af;
            font-size: 0.75rem;
        }

        /* Sticky student name column */
        .marks-table .col-name {
            position: sticky;
            left: 0;
            z-index: 5;
            background-color: white;
            border-right: 2px solid #e5e7eb;
            min-width: 200px;
        }

        .marks-table thead .col-name {
            z-index: 15;
            background-color: #f9fafb;
        }

        .marks-table tbody tr:nth-child(even) .col-name {
            background-color: #f9fafb;
        }

        .marks-table tbody tr:hover .col-name {
            background-color: #f3f4f6;
        }

        /* Sticky total column */
        .marks-table .col-total {
            position: sticky;
            right: 0;
            z-index: 5;
            background-color: #eff6ff;
            border-left: 2px solid #3b82f6;
            font-weight: 700;
            text-align: center;
            min-width: 90px;
        }

        .marks-table thead .col-total {
            z-index: 15;
            background-color: #dbeafe;
        }

        /* Mark input */
        .mark-input {
            width: 80px;
            padding: 0.375rem 0.5rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            text-align: center;
            font-size: 0.875rem;
            background-color: white;
            transition: border-color 0.15s, box-shadow 0.15s;
        }

        .mark-input:focus {
            outline: none;
            border-color: #f59e0b;
            box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.2);
            background-color: #fef3c7;
        }

        /* ======================== */
        /* DARK MODE                */
        /* ======================== */
        .dark .marks-table th {
            background-color: rgb(17 24 39);
            color: #9ca3af;
            border-bottom-color: #4b5563;
        }

        .dark .marks-table {
            background-color: rgb(31 41 55);
        }

        .dark .marks-table td {
            background-color: rgb(31 41 55);
            color: #d1d5db;
            border-bottom-color: #374151;
        }

        .dark .marks-table tbody tr:nth-child(even) td {
            background-color: rgb(38 47 61);
        }

        .dark .marks-table tbody tr:hover td {
            background-color: rgb(55 65 81);
        }

        .dark .marks-table .col-name {
            background-color: rgb(31 41 55);
            border-right-color: #4b5563;
        }

        .dark .marks-table thead .col-name {
            background-color: rgb(17 24 39);
        }

        .dark .marks-table tbody tr:nth-child(even) .col-name {
            background-color: rgb(38 47 61) !important;
        }

        .dark .marks-table tbody tr:hover .col-name {
            background-color: rgb(55 65 81) !important;
        }

        .dark .marks-table .col-total {
            background-color: rgb(30 41 66);
            border-left-color: #60a5fa;
            color: #93c5fd;
        }

        .dark .marks-table thead .col-total {
            background-color: rgb(24 34 58);
        }

        .dark .marks-table tbody tr:nth-child(even) .col-total {
            background-color: rgb(33 45 72);
        }

        .dark .marks-table tbody tr:hover .col-total {
            background-color: rgb(40 52 80);
        }

        .dark .mark-input {
            background-color: rgb(55 65 81);
            border-color: #4b5563;
            color: #f3f4f6;
        }

        .dark .mark-input:focus {
            border-color: #fbbf24;
            box-shadow: 0 0 0 2px rgba(251, 191, 36, 0.2);
            background-color: rgba(120, 53, 15, 0.2);
        }

        /* ======================== */
        /* MOBILE / TABLET          */
        /* ======================== */
        @media (max-width: 1024px) {
            .marks-table-container {
                scrollbar-width: thin;
                scrollbar-color: #cbd5e0 #f7fafc;
            }

            .marks-table-container::-webkit-scrollbar {
                height: 8px;
            }

            .marks-table-container::-webkit-scrollbar-track {
                background: #f7fafc;
                border-radius: 4px;
            }

            .marks-table-container::-webkit-scrollbar-thumb {
                background: #cbd5e0;
                border-radius: 4px;
            }
        }

        @media (max-width: 768px) {
            .marks-table .col-name {
                min-width: 150px;
                max-width: 180px;
                font-size: 0.8125rem;
            }

            .mark-input {
                width: 70px;
                min-height: 44px;
                padding: 0.5rem 0.25rem;
            }

            .marks-table .col-total {
                min-width: 80px;
            }

            .marks-table th {
                font-size: 0.6875rem;
                padding: 0.375rem 0.5rem;
            }

            .marks-table td {
                padding: 0.375rem 0.5rem;
            }

            .marks-table .col-name {
                box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            }

            .marks-table .col-total {
                box-shadow: -2px 0 5px rgba(0, 0, 0, 0.1);
            }

            .dark .marks-table .col-name {
                box-shadow: 2px 0 5px rgba(0, 0, 0, 0.3);
            }

            .dark .marks-table .col-total {
                box-shadow: -2px 0 5px rgba(0, 0, 0, 0.3);
            }
        }

        @media (max-width: 480px) {
            .marks-table .col-name {
                min-width: 120px;
                max-width: 140px;
                font-size: 0.75rem;
            }

            .mark-input {
                width: 60px;
                font-size: 0.8125rem;
            }

            .marks-table .col-total {
                min-width: 70px;
            }
        }

        /* Remove mobile shadows on desktop */
        @media (min-width: 1025px) {
            .marks-table .col-name,
            .marks-table .col-total {
                box-shadow: none;
            }
        }

        /* Dark mode table container */
        .dark .marks-table-container {
            background-color: rgb(31 41 55);
        }

        /* Remove bottom border on last row */
        .marks-table tbody tr:last-child td {
            border-bottom: none;
        }
    </style>

    <script>
        function markRow(initialTotal) {
            return {
                total: initialTotal,
                recalcTotal() {
                    let sum = 0;
                    this.$el.querySelectorAll('input[type=number]').forEach(input => {
                        const max = parseFloat(input.dataset.max) || Infinity;
                        const val = parseFloat(input.value) || 0;
                        sum += Math.min(val, max);
                    });
                    this.total = sum.toFixed(1);
                }
            }
        }
    </script>

    <div class="space-y-6">
        <!-- Exam Information Header -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-600 px-6 py-4 border-b border-gray-200 dark:border-gray-600">
                <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">Imtihon ma'lumotlari</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Left Column -->
                    <div class="space-y-6">
                        <div class="bg-white dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Fan</p>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $this->exam->subject->name }}</p>
                                </div>
                                <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Sinf</p>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $this->exam->sinf->name }}</p>
                                </div>
                                <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-6">
                        <div class="bg-white dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Imtihon</p>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $this->exam->serial_number }} - {{ $this->exam->type }}</p>
                                </div>
                                <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- Main Table -->
        @if($this->students->count() > 0 && count($this->problems) > 0)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <!-- Table header with Save button -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-600">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Baholar jadvali</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            Baholarni kiriting va <strong>"Saqlash"</strong> tugmasini bosing.
                        </p>
                    </div>
                    <x-filament::button
                        wire:click="saveAll"
                        color="success"
                        icon="heroicon-o-check"
                        size="lg"
                    >
                        Saqlash
                    </x-filament::button>
                </div>

                <!-- Mobile scroll hint -->
                <div class="block lg:hidden bg-blue-50 dark:bg-blue-900/20 border-b border-blue-200 dark:border-blue-600 px-4 py-2">
                    <div class="flex items-center justify-between text-sm text-blue-800 dark:text-blue-200">
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18m-2 4l4-4m0 0l-4-4"></path>
                            </svg>
                            <span>Chapga-o'nga surib ko'ring</span>
                        </div>
                        <span class="text-xs opacity-75">{{ count($this->problems) }} ta topshiriq</span>
                    </div>
                </div>

                <!-- Table -->
                <div class="marks-table-container">
                    <table class="marks-table">
                        <thead>
                            <tr>
                                <th class="col-num">#</th>
                                <th class="col-name">O'quvchi IFSH</th>
                                @foreach($this->problems as $problem)
                                    <th class="text-center">
                                        T-{{ $problem['id'] }}<br>
                                        <span class="text-xs font-normal text-gray-400 dark:text-gray-500">Max: {{ $problem['max_mark'] }}</span>
                                    </th>
                                @endforeach
                                <th class="col-total">
                                    Jami<br>
                                    <span class="text-xs font-normal">Max: {{ collect($this->problems)->sum('max_mark') }}</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($this->students as $student)
                                @php
                                    $initialTotal = collect($this->problems)->sum(function ($p) use ($student) {
                                        $val = (float) ($this->marks[$student->id . '_' . $p['id']] ?? 0);
                                        return min($val, $p['max_mark']);
                                    });
                                @endphp
                                <tr
                                    wire:key="student-{{ $student->id }}"
                                    x-data="markRow('{{ number_format($initialTotal, 1) }}')"
                                >
                                    <td class="col-num">{{ $loop->iteration }}</td>
                                    <td class="col-name font-medium text-gray-900 dark:text-gray-100">
                                        {{ $student->full_name }}
                                    </td>
                                    @foreach($this->problems as $problem)
                                        <td class="text-center">
                                            <input
                                                type="number"
                                                wire:model="marks.{{ $student->id }}_{{ $problem['id'] }}"
                                                @input="recalcTotal()"
                                                class="mark-input"
                                                step="0.1"
                                                min="0"
                                                max="{{ $problem['max_mark'] }}"
                                                data-max="{{ $problem['max_mark'] }}"
                                            >
                                        </td>
                                    @endforeach
                                    <td class="col-total" x-text="total"></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Bottom Save button (for long tables) -->
                @if($this->students->count() > 10)
                    <div class="flex justify-end px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                        <x-filament::button
                            wire:click="saveAll"
                            color="success"
                            icon="heroicon-o-check"
                            size="lg"
                        >
                            Saqlash
                        </x-filament::button>
                    </div>
                @endif
            </div>
        @else
            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-600 rounded-lg p-6 text-center">
                <div class="text-yellow-800 dark:text-yellow-200">
                    @if($this->students->count() === 0)
                        <x-heroicon-o-users class="mx-auto h-8 w-8 text-yellow-600 dark:text-yellow-400 mb-3" />
                        <h3 class="text-lg font-medium mb-2">Sinfda o'quvchilar yo'q</h3>
                        <p>Baholarni kiritish uchun avval sinfga o'quvchilar qo'shing.</p>
                    @elseif(count($this->problems) === 0)
                        <x-heroicon-o-clipboard-document-list class="mx-auto h-8 w-8 text-yellow-600 dark:text-yellow-400 mb-3" />
                        <h3 class="text-lg font-medium mb-2">Imtihonda topshiriqlar yo'q</h3>
                        <p>Baholarni kiritish uchun avval imtihonga topshiriqlar qo'shing.</p>
                    @endif
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
