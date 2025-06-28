<div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-8 transition-colors duration-300">
    <div class="flex flex-wrap gap-4 justify-between items-start mb-6">
        <div class="flex-1 min-w-[300px]">
            <div class="space-y-4 mb-6">
                <div>
                    <label for="exam-select" class="block text-sm font-medium text-gray-700 dark:text-white mb-1 text-lg">
                        Imtihon tanlang
                    </label>

                    <div class="flex flex-col sm:flex-row gap-3">
                        <select
                            wire:model="selectedExamId"
                            id="exam-select"
                            class="flex-1 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="">-- Imtihonni tanlang --</option>
                            @foreach ($exams as $exam)
                                @php
                                    $subject = \App\Models\Subject::find($exam->subject_id)?->name ?? 'No Subject';
                                    $class = \App\Models\Sinf::find($exam->sinf_id)?->name ?? 'NO SINF';
                                @endphp
                                <option value="{{ $exam->id }}">
                                    {{ $class }} sinf | {{ $subject }} | {{ $exam->serial_number }}-{{ $exam->type }}
                                </option>
                            @endforeach
                        </select>
                        <button
                            wire:click="generateTable"
                            class="px-4 py-2
                                   bg-white text-gray-800
                                   hover:bg-gray-100
                                   dark:bg-gray-800 dark:text-white dark:hover:bg-gray-700
                                   border border-gray-300 dark:border-gray-600
                                   text-sm font-medium
                                   rounded-md shadow-sm transition-colors
                                   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500
                                   dark:focus:ring-offset-gray-900"
                                                >
                                                    Hisobot qurish
                        </button>

                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (!$selectedExamId)
        <br>
        <p class="text-red-500 dark:text-red-400 mt-4">Iltimos, avval imtihonni tanlang.</p>
    @elseif (count($students) === 0)
        <p class="text-gray-600 dark:text-gray-400 mt-4">Ushbu imtihon uchun hali hech qanday ma'lumot mavjud emas.</p>
    @else
        <br>
        <div x-data="{ isDisabled: false, loading: false }" class="flex justify-end mb-4">
            <button
                type="button"
                @click="
    isDisabled = true;
    loading = true;
    $wire.downloadPdf();
    setTimeout(() => {
      isDisabled = false;
      loading = false;
    }, 10000);
  "
                :disabled="isDisabled"
                class="px-4 py-2 w-44 flex justify-center items-center
         bg-white text-gray-800 hover:bg-gray-100
         dark:bg-gray-800 dark:text-white dark:hover:bg-gray-700
         border border-gray-300 dark:border-gray-600
         text-sm font-medium rounded-md shadow-sm transition-colors
         focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500
         dark:focus:ring-offset-gray-900 disabled:opacity-50 disabled:cursor-not-allowed"
            >
                <!-- Loader Spinner (visible only when loading) -->
                <template x-if="loading">
                  <span class="flex items-center gap-2">
                    <x-filament::loading-indicator class="h-5 w-5 text-sm text-gray-700 dark:text-gray-300" />
                    <span class="text-sm text-gray-700 dark:text-gray-400">Loading...</span>
                  </span>
                </template>


                <!-- Button Text & Icon (visible only when NOT loading) -->
                <template x-if="!loading">
                    <span class="flex items-center gap-2">
                      <svg
                          class="w-5 h-5"
                          xmlns="http://www.w3.org/2000/svg"
                          fill="none"
                          viewBox="0 0 24 24"
                          stroke-width="2"
                          stroke="currentColor"
                      >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"
                        />
                      </svg>
                      Export to PDF
                    </span>
                </template>
            </button>


        </div>

        <div class="mt-6 overflow-x-auto">
            <table class="w-full border-collapse border border-gray-300 dark:border-gray-600 font-sans text-sm">
                <thead class="bg-gray-100 dark:bg-gray-700">
                <tr>
                    <th rowspan="2" class="border border-gray-300 dark:border-gray-600 py-2 px-3 text-sm font-bold text-center text-gray-800 dark:text-gray-200">№</th>
                    <th rowspan="2" class="border border-gray-300 dark:border-gray-600 py-2 px-3 text-sm font-bold text-left text-gray-800 dark:text-gray-200">F.I.Sh.</th>
                    @foreach($problems as $problem)
                        <th class="border border-gray-300 dark:border-gray-600 py-2 px-3 text-sm font-bold text-center text-gray-800 dark:text-gray-200">
                            {{ $problem->problem_number }}-topshiriq<br>
                            <span class="text-xs font-normal">({{ $problem->max_mark }})</span>
                        </th>
                    @endforeach
                    <th class="border border-gray-300 dark:border-gray-600 py-2 px-3 text-sm font-bold text-center text-gray-800 dark:text-gray-200">Jami ball<br><span class="text-xs font-normal">({{ $totalMaxScore }})</span></th>
                    <th class="border border-gray-300 dark:border-gray-600 py-2 px-3 text-sm font-bold text-center text-gray-800 dark:text-gray-200">Foiz (%)</th>
                </tr>
                </thead>

                <tbody>
                @php
                    $problemTotals = [];
                    $problemCounts = [];
                    $totalScores = [];
                @endphp

                @foreach($students as $index => $student)
                    @php $overall = 0; @endphp
                    <tr class="">
                        <td class="border border-gray-300 dark:border-gray-600 py-2 px-3 text-center text-gray-800 dark:text-gray-200">{{ $index + 1 }}</td>
                        <td class="border border-gray-300 dark:border-gray-600 py-2 px-3 text-left text-gray-800 dark:text-gray-200">{{ $student->full_name }}</td>

                        @foreach($problems as $problem)
                            @php
                                $mark = \App\Models\Mark::all()
                                   ->where('student_id', $student->id)
                                   ->where('problem_id', $problem->id)
                                   ->first();
                                $score = $mark->mark ?? 0;
                                $overall += $score;

                                if (!isset($problemTotals[$problem->id])) {
                                    $problemTotals[$problem->id] = 0;
                                    $problemCounts[$problem->id] = 0;
                                }
                                $problemTotals[$problem->id] += $score;
                                $problemCounts[$problem->id]++;
                            @endphp
                            <td class="border border-gray-300 dark:border-gray-600 py-2 px-3 text-center text-gray-800 dark:text-gray-200">
                                <x-rowblock :mark="$score" />
                            </td>
                        @endforeach

                        <td class="border border-gray-300 dark:border-gray-600 py-2 px-3 text-center font-bold text-gray-800 dark:text-gray-200">{{ $overall }}</td>

                        @php
                            $percentage = $totalMaxScore > 0 ? round(($overall / $totalMaxScore) * 100, 1) : 0;
                            $totalScores[] = $overall;
                        @endphp
                        <td class="border border-gray-300 dark:border-gray-600 py-2 px-3 text-center bg-yellow-100 dark:bg-yellow-900/30 text-gray-800 dark:text-yellow-200">
                            {{ $percentage }}%
                        </td>
                    </tr>
                @endforeach
                </tbody>

                <tfoot>
                <tr class="bg-yellow-100 dark:bg-yellow-900/30 font-bold">
                    <td class="border border-gray-300 dark:border-gray-600 py-2 px-3 text-gray-800 dark:text-yellow-200" colspan="2">O'rtacha ball</td>
                    @foreach ($problems as $problem)
                        @php
                            $average = isset($problemTotals[$problem->id]) && $problemCounts[$problem->id] > 0
                                ? round($problemTotals[$problem->id] / $problemCounts[$problem->id], 1)
                                : 0;
                        @endphp
                        <td class="border border-gray-300 dark:border-gray-600 py-2 px-3 text-center text-gray-800 dark:text-yellow-200">
                            {{ $average }}
                        </td>
                    @endforeach

                    @php
                        $avgTotal = count($totalScores) > 0 ? round(array_sum($totalScores) / count($totalScores), 1) : 0;
                        $avgPercentage = $totalMaxScore > 0 ? round(($avgTotal / $totalMaxScore) * 100, 1) : 0;
                    @endphp

                    <td class="border border-gray-300 dark:border-gray-600 py-2 px-3 text-center bg-green-100 dark:bg-green-900/30 font-bold text-gray-800 dark:text-green-200" rowspan="2">
                        {{ $avgTotal }}
                    </td>

                    <td class="border border-gray-300 dark:border-gray-600 py-2 px-3 text-center bg-green-100 dark:bg-green-900/30 font-bold text-gray-800 dark:text-green-200" rowspan="2">
                        {{$avgPercentage}}%
                    </td>
                </tr>

                <tr class="bg-yellow-100 dark:bg-yellow-900/30 font-bold">
                    <td class="border border-gray-300 dark:border-gray-600 py-2 px-3 text-gray-800 dark:text-yellow-200" colspan="2">O'zlashtirish foizi (%)</td>
                    @foreach ($problems as $problem)
                        @php
                            $average = isset($problemTotals[$problem->id]) && $problemCounts[$problem->id] > 0
                                ? round($problemTotals[$problem->id] / $problemCounts[$problem->id], 1)
                                : 0;
                            $masteryPercentage = $problem->max_mark > 0
                                ? round(($average / $problem->max_mark) * 100, 1)
                                : 0;
                        @endphp
                        <td class="border border-gray-300 dark:border-gray-600 py-2 px-3 text-center text-gray-800 dark:text-yellow-200">
                            <span>{{ $masteryPercentage }}%</span>
                        </td>
                    @endforeach
                </tr>
                </tfoot>
            </table>
        </div>
    @endif
</div>
