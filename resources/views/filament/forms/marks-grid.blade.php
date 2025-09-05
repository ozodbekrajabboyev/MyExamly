{{-- resources/views/filament/forms/marks-grid.blade.php --}}

@if(!$exam || $students->isEmpty() || $problems->isEmpty())
    <div class="text-center py-12 bg-gray-50 dark:bg-gray-800/50 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600">
        @if(!$exam)
            <div class="text-gray-500 dark:text-gray-400">
                <x-heroicon-o-document-text class="mx-auto h-12 w-12 mb-4" />
                <p class="text-lg font-medium">Iltimos, avval imtihonni tanlang</p>
                <p class="text-sm">Imtihonni tanlagandan so'ng baholarni kiritish jadvali ko'rinadi</p>
            </div>
        @elseif($problems->isEmpty())
            <div class="text-red-500">
                <x-heroicon-o-exclamation-triangle class="mx-auto h-12 w-12 mb-4" />
                <p class="text-lg font-medium">Bu imtihonda topshiriqlar mavjud emas</p>
                <p class="text-sm">Iltimos, boshqa imtihonni tanlang</p>
            </div>
        @elseif($students->isEmpty())
            <div class="text-orange-500">
                <x-heroicon-o-user-group class="mx-auto h-12 w-12 mb-4" />
                <p class="text-lg font-medium">Bu sinfda o'quvchilar mavjud emas</p>
                <p class="text-sm">Avval sinfga o'quvchilar qo'shing</p>
            </div>
        @endif
    </div>
@else
    <div class="marks-table-wrapper">
        {{-- Header with controls --}}
        <div class="fi-section-content-ctn rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="fi-section-content p-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                    <div class="flex items-center space-x-4">
                        <h3 class="text-lg font-semibold text-gray-950 dark:text-white">
                            Baholarni kiritish jadvali
                        </h3>
                        <button
                            type="button"
                            onclick="fillAllZeros()"
                            class="fi-btn fi-btn-size-sm fi-color-gray fi-btn-color-gray inline-flex items-center justify-center gap-1 font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg fi-size-sm fi-btn-size-sm gap-1 px-2.5 py-1.5 text-sm bg-white text-gray-950 hover:bg-gray-50 focus-visible:ring-primary-600 dark:bg-white/5 dark:text-white dark:hover:bg-white/10 dark:focus-visible:ring-primary-500 fi-ac-action fi-ac-btn-action"
                        >
                            <x-heroicon-m-arrow-path class="h-4 w-4" />
                            Hammasini 0 qiling
                        </button>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="fi-badge inline-flex items-center justify-center gap-x-1 rounded-md text-xs font-medium ring-1 ring-inset px-2 min-w-[theme(spacing.6)] py-1 fi-color-primary fi-badge-color-primary bg-primary-50 text-primary-600 ring-primary-600/10 dark:bg-primary-400/10 dark:text-primary-400 dark:ring-primary-400/30">
                            {{ $students->count() }} o'quvchi
                        </span>
                        <span class="fi-badge inline-flex items-center justify-center gap-x-1 rounded-md text-xs font-medium ring-1 ring-inset px-2 min-w-[theme(spacing.6)] py-1 fi-color-success fi-badge-color-success bg-success-50 text-success-600 ring-success-600/10 dark:bg-success-400/10 dark:text-success-400 dark:ring-success-400/30">
                            {{ $problems->count() }} topshiriq
                        </span>
                        <span class="fi-badge inline-flex items-center justify-center gap-x-1 rounded-md text-xs font-medium ring-1 ring-inset px-2 min-w-[theme(spacing.6)] py-1 fi-color-info fi-badge-color-info bg-info-50 text-info-600 ring-info-600/10 dark:bg-info-400/10 dark:text-info-400 dark:ring-info-400/30">
                            {{ $students->count() * $problems->count() }} baho
                        </span>
                    </div>
                </div>

                {{-- Scrollable table container --}}
                <div class="marks-table-container">
                    <div class="table-scroll-wrapper">
                        <table class="marks-table">
                            <thead>
                            <tr>
                                {{-- Sticky student name column --}}
                                <th class="student-name-header">
                                    <div class="flex items-center space-x-2">
                                        <x-heroicon-o-user class="h-4 w-4" />
                                        <span>O'quvchi</span>
                                    </div>
                                </th>

                                {{-- Problem columns --}}
                                @foreach($problems as $problem)
                                    <th class="problem-header">
                                        <div class="text-center">
                                            <div class="font-bold text-primary-600 dark:text-primary-400">
                                                {{ $problem['id'] }}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                Max: {{ $problem['max_mark'] }}
                                            </div>
                                        </div>
                                    </th>
                                @endforeach

                                {{-- Sticky total column --}}
                                <th class="total-header">
                                    Jami
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($students as $student)
                                <tr class="student-row">
                                    {{-- Sticky student name cell --}}
                                    <td class="student-name-cell">
                                        <div class="flex items-center space-x-3">
                                            <div class="student-avatar">
                                                {{ mb_substr($student->full_name, 0, 1, 'UTF-8') }}
                                            </div>
                                            <div>
                                                <div class="student-name">{{ $student->full_name }}</div>
                                                @if($student->student_id)
                                                    <div class="student-id">ID: {{ $student->student_id }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Mark input cells --}}
                                    @php $studentTotal = 0; @endphp
                                    @foreach($problems as $problem)
                                        @php
                                            // Use pre-loaded marks for better performance
                                            $markKey = "{$student->id}_{$problem['id']}";
                                            $existingMark = isset($existingMarks) ? $existingMarks->get($markKey) : null;
                                            $currentMark = $existingMark ? $existingMark->mark : 0;
                                            if (is_numeric($currentMark) && $currentMark > 0) {
                                                $studentTotal += $currentMark;
                                            }
                                        @endphp
                                        <td class="mark-input-cell">
                                            <input
                                                type="number"
                                                name="marks.{{ $student->id }}_{{ $problem['id'] }}"
                                                class="mark-input fi-input block w-full border-none py-1.5 text-base text-gray-950 transition duration-75 placeholder:text-gray-400 focus:ring-0 disabled:text-gray-500 disabled:[-webkit-text-fill-color:theme(colors.gray.500)] disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.400)] dark:text-white dark:placeholder:text-gray-500 dark:disabled:text-gray-400 dark:disabled:[-webkit-text-fill-color:theme(colors.gray.400)] dark:disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.500)] sm:text-sm sm:leading-6 bg-white dark:bg-white/5 [&:not(:focus)]:shadow-none !shadow-sm !ring-1 !ring-inset focus:!ring-2 !ring-gray-950/10 !focus:ring-primary-600 dark:!ring-white/20 dark:!focus:ring-primary-500 rounded-lg"
                                                min="0"
                                                max="{{ $problem['max_mark'] }}"
                                                step="0.1"
                                                value="{{ $currentMark > 0 ? $currentMark : '' }}"
                                                placeholder="0"
                                                data-student="{{ $student->id }}"
                                                data-problem="{{ $problem['id'] }}"
                                                onchange="updateStudentTotal({{ $student->id }});updateFormData(this);"
                                                oninput="syncFormData(this);"
                                            />
                                        </td>
                                    @endforeach

                                    {{-- Sticky total cell --}}
                                    <td class="total-cell">
                                        <span id="total_{{ $student->id }}" class="total-value">
                                            {{ $studentTotal > 0 ? rtrim(rtrim(number_format($studentTotal, 2, '.', ''), '0'), '.') : '0' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Embedded styles using Filament's approach --}}
    <style>
        .marks-table-wrapper {
            --student-name-width: 280px;
            --total-column-width: 100px;
            --problem-column-width: 90px;
        }

        .marks-table-container {
            position: relative;
            border: 1px solid rgb(229 231 235);
            border-radius: 0.5rem;
            overflow: hidden;
        }

        .dark .marks-table-container {
            border-color: rgb(75 85 99);
        }

        .table-scroll-wrapper {
            position: relative;
            max-height: 70vh;
            overflow: auto;
        }

        .marks-table {
            width: 100%;
            table-layout: fixed;
            border-collapse: separate;
            border-spacing: 0;
            min-width: calc(var(--student-name-width) + var(--total-column-width) + (var(--problem-column-width) * {{ $problems->count() }}));
        }

        /* Header styles */
        .marks-table thead th {
            position: sticky;
            top: 0;
            z-index: 10;
            background: rgb(249 250 251);
            border-bottom: 1px solid rgb(229 231 235);
            padding: 0.75rem 0.5rem;
            text-align: left;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: rgb(107 114 128);
        }

        .dark .marks-table thead th {
            background: rgb(17 24 39);
            border-color: rgb(75 85 99);
            color: rgb(156 163 175);
        }

        .student-name-header {
            position: sticky !important;
            left: 0;
            z-index: 20 !important;
            width: var(--student-name-width);
            border-right: 1px solid rgb(229 231 235);
        }

        .dark .student-name-header {
            border-right-color: rgb(75 85 99);
        }

        .problem-header {
            width: var(--problem-column-width);
            text-align: center;
        }

        .total-header {
            position: sticky !important;
            right: 0;
            z-index: 20 !important;
            width: var(--total-column-width);
            text-align: center;
            border-left: 1px solid rgb(229 231 235);
        }

        .dark .total-header {
            border-left-color: rgb(75 85 99);
        }

        /* Row styles */
        .student-row {
            transition: background-color 0.2s ease-in-out;
        }

        .student-row:hover {
            background: rgb(249 250 251);
        }

        .dark .student-row:hover {
            background: rgb(31 41 55 / 0.5);
        }

        /* Cell styles */
        .marks-table td {
            padding: 0.5rem;
            border-bottom: 1px solid rgb(229 231 235);
            background: white;
        }

        .dark .marks-table td {
            border-color: rgb(75 85 99);
            background: rgb(17 24 39);
        }

        .student-row:hover td {
            background: rgb(249 250 251);
        }

        .dark .student-row:hover td {
            background: rgb(31 41 55 / 0.5);
        }

        .student-name-cell {
            position: sticky !important;
            left: 0;
            z-index: 10 !important;
            width: var(--student-name-width);
            border-right: 1px solid rgb(229 231 235);
        }

        .dark .student-name-cell {
            border-right-color: rgb(75 85 99);
        }

        .mark-input-cell {
            width: var(--problem-column-width);
            text-align: center;
            padding: 0.25rem;
        }

        .total-cell {
            position: sticky !important;
            right: 0;
            z-index: 10 !important;
            width: var(--total-column-width);
            text-align: center;
            border-left: 1px solid rgb(229 231 235);
        }

        .dark .total-cell {
            border-left-color: rgb(75 85 99);
        }

        /* Student info styles */
        .student-avatar {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 2rem;
            height: 2rem;
            border-radius: 50%;
            background: rgb(239 246 255);
            color: rgb(37 99 235);
            font-size: 0.875rem;
            font-weight: 600;
            flex-shrink: 0;
            text-transform: uppercase;
        }

        .dark .student-avatar {
            background: rgb(37 99 235 / 0.2);
            color: rgb(147 197 253);
        }

        .student-name {
            font-size: 0.875rem;
            font-weight: 500;
            color: rgb(17 24 39);
            line-height: 1.25rem;
        }

        .dark .student-name {
            color: rgb(243 244 246);
        }

        .student-id {
            font-size: 0.75rem;
            color: rgb(107 114 128);
        }

        .dark .student-id {
            color: rgb(156 163 175);
        }

        /* Mark input styles */
        .mark-input {
            width: 70px !important;
            text-align: center;
            font-size: 0.875rem;
        }

        .mark-input:focus {
            outline: none;
        }

        /* Total value styles */
        .total-value {
            font-weight: 600;
            color: rgb(17 24 39);
        }

        .dark .total-value {
            color: rgb(243 244 246);
        }

        /* Scrollbar styles */
        .table-scroll-wrapper::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        .table-scroll-wrapper::-webkit-scrollbar-track {
            background: rgb(241 245 249);
            border-radius: 4px;
        }

        .table-scroll-wrapper::-webkit-scrollbar-thumb {
            background: rgb(203 213 225);
            border-radius: 4px;
        }

        .table-scroll-wrapper::-webkit-scrollbar-thumb:hover {
            background: rgb(148 163 184);
        }

        .dark .table-scroll-wrapper::-webkit-scrollbar-track {
            background: rgb(31 41 55);
        }

        .dark .table-scroll-wrapper::-webkit-scrollbar-thumb {
            background: rgb(75 85 99);
        }

        .dark .table-scroll-wrapper::-webkit-scrollbar-thumb:hover {
            background: rgb(107 114 128);
        }

        /* Responsive adjustments */
        @media (max-width: 640px) {
            .marks-table-wrapper {
                --student-name-width: 200px;
                --problem-column-width: 70px;
                --total-column-width: 80px;
            }

            .student-name {
                font-size: 0.8rem;
            }

            .mark-input {
                width: 60px !important;
                font-size: 0.8rem;
            }
        }
    </style>

    {{-- JavaScript functionality --}}
    <script>
        // Function to get the form data state and update it
        function getFormDataState() {
            // Find the hidden marks input field
            const marksInput = document.querySelector('input[name="marks"]');
            if (!marksInput) {
                console.error('Marks hidden input not found');
                return {};
            }

            try {
                return marksInput.value ? JSON.parse(marksInput.value) : {};
            } catch (e) {
                console.error('Error parsing marks data:', e);
                return {};
            }
        }

        function setFormDataState(data) {
            const marksInput = document.querySelector('input[name="marks"]');
            if (!marksInput) {
                console.error('Marks hidden input not found');
                return;
            }

            marksInput.value = JSON.stringify(data);

            // Trigger Alpine.js state update if available
            if (marksInput._x_model) {
                marksInput.dispatchEvent(new Event('input'));
            }

            // Also trigger change event for Livewire compatibility
            marksInput.dispatchEvent(new Event('change', { bubbles: true }));
        }

        function syncFormData(inputElement) {
            const key = inputElement.getAttribute('name').replace('marks.', '');
            const value = inputElement.value;

            const formData = getFormDataState();

            if (value === '' || value === null) {
                delete formData[key];
            } else {
                formData[key] = parseFloat(value) || 0;
            }

            setFormDataState(formData);
        }

        function updateFormData(inputElement) {
            syncFormData(inputElement);
        }

        function fillAllZeros() {
            const inputs = document.querySelectorAll('input[type="number"][name^="marks."]');
            const formData = {};

            inputs.forEach(input => {
                input.value = '0';
                const key = input.getAttribute('name').replace('marks.', '');
                formData[key] = 0;

                input.dispatchEvent(new Event('input', { bubbles: true }));
                input.dispatchEvent(new Event('change', { bubbles: true }));
            });

            setFormDataState(formData);
            updateAllTotals();
        }

        function updateStudentTotal(studentId) {
            const inputs = document.querySelectorAll(`input[data-student="${studentId}"]`);
            let total = 0;

            inputs.forEach(input => {
                const value = parseFloat(input.value);
                if (!isNaN(value) && value > 0) {
                    total += value;
                }
            });

            const totalElement = document.getElementById(`total_${studentId}`);
            if (totalElement) {
                totalElement.textContent = total > 0 ? parseFloat(total.toFixed(2)).toString() : '0';
            }
        }

        function updateAllTotals() {
            const studentIds = [...new Set(Array.from(document.querySelectorAll('input[data-student]')).map(el => el.dataset.student))];
            studentIds.forEach(studentId => {
                updateStudentTotal(parseInt(studentId));
            });
        }

        function initializeFormData() {
            const inputs = document.querySelectorAll('input[type="number"][name^="marks."]');
            const formData = {};

            inputs.forEach(input => {
                const key = input.getAttribute('name').replace('marks.', '');
                const value = input.value;
                if (value && value !== '' && value !== '0') {
                    formData[key] = parseFloat(value) || 0;
                }
            });

            setFormDataState(formData);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('input[type="number"][name^="marks."]');

            inputs.forEach(input => {
                // Input validation
                input.addEventListener('input', function() {
                    const max = parseFloat(this.getAttribute('max'));
                    let value = parseFloat(this.value);

                    if (isNaN(value)) {
                        syncFormData(this);
                        return;
                    }

                    if (value > max) {
                        this.value = max;
                    }
                    if (value < 0) {
                        this.value = 0;
                    }

                    syncFormData(this);
                });

                // Focus behavior
                input.addEventListener('focus', function() {
                    this.select();
                });

                // Update totals on change
                input.addEventListener('change', function() {
                    const studentId = parseInt(this.dataset.student);
                    if (studentId) {
                        updateStudentTotal(studentId);
                    }
                    updateFormData(this);
                });

                // Handle keyboard navigation
                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === 'Tab') {
                        const studentId = parseInt(this.dataset.student);
                        if (studentId) {
                            updateStudentTotal(studentId);
                        }
                        updateFormData(this);
                    }
                });
            });

            // Initialize form data and calculate totals
            initializeFormData();
            updateAllTotals();
        });
    </script>
@endif
