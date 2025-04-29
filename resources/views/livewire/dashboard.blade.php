<div>
    <div class="controls">
        <div class="exam-selector">
            <label for="exam-select">Select Exam</label>
            <div class="select-wrapper">
                <select wire:model="selectedExamId" id="exam-select">
                    <option value="">-- Choose Exam --</option>
                    @foreach ($exams as $exam)
                        @php
                            $subjectName = \App\Models\Subject::all()->firstWhere('id', $exam->subject_id)?->name ?? 'No Subject';
                            $sinf = \App\Models\Sinf::all()->firstWhere('id', $exam->sinf_id)?->name ?? 'NO SINF';
                        @endphp
                        <option value="{{ $exam->id }}"> {{ $sinf }} sinf | {{$subjectName}} | {{ $exam->serial_number }}-{{$exam->type }}</option>
                    @endforeach
                </select>
            </div>
            <button wire:click="generateTable" class="generate-btn">
                Generate Table
            </button>
        </div>
    </div>

    @if (!$selectedExamId)
        <p class="text-red-500 mt-4">Iltimos, avval imtihonni tanlang.</p>
    @elseif (count($students) === 0)
        <p class="text-gray-600 mt-4">Ushbu imtihon uchun hali hech qanday ma'lumot mavjud emas.</p>
    @else
        <div class="table-container mt-6 overflow-x-auto">
            <table class="marks-table w-full border-collapse">
                <thead class="bg-gray-200">
                <tr>
                    <th rowspan="2" class="border py-1 px-2 text-sm font-bold">№</th>
                    <th rowspan="2" class="border py-1 px-2 text-sm font-bold text-left">F.I.Sh.</th>
                    @foreach($problems as $problem)
                        <th class="border py-1 px-2 text-sm font-bold">
                            {{ $problem->problem_number }}-topshiriq<br>
                            <span class="text-xs font-normal">({{ $problem->max_mark }})</span>
                        </th>
                    @endforeach
                    <th class="border py-1 px-2 text-sm font-bold" >Jami ball<br><span class="text-xs font-normal">({{ $totalMaxScore }})</span></th>
                    <th class="border py-1 px-2 text-sm font-bold">Foiz (%)</th>
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
                    <tr>
                        <td class="border py-1 px-2 text-center">{{ $index + 1 }}</td>
                        <td class="border py-1 px-2 text-left">{{ $student->full_name }}</td>

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
                            <td class="border py-1 px-2 text-center">
                                <x-rowblock :mark="$score" />
                            </td>
                        @endforeach

                        <td class="border py-1 px-2 text-center font-bold">{{ $overall }}</td>

                        @php
                            $percentage = $totalMaxScore > 0 ? round(($overall / $totalMaxScore) * 100, 1) : 0;
                            $totalScores[] = $overall;
                        @endphp
                        <td class="border py-1 px-2 text-center {{ $percentage >= 80 ? 'bg-green-100' : ($percentage >= 70 ? 'bg-yellow-100' : 'bg-red-100') }}">
                            {{ $percentage }}%
                        </td>
                    </tr>
                @endforeach
                </tbody>

                <tfoot>
                <tr class="bg-yellow-100 font-bold">
                    <td class="border py-1 px-2" colspan="2">O'rtacha ball</td>
                    @foreach ($problems as $problem)
                        @php
                            $average = isset($problemTotals[$problem->id]) && $problemCounts[$problem->id] > 0
                                ? round($problemTotals[$problem->id] / $problemCounts[$problem->id], 1)
                                : 0;
                        @endphp
                        <td class="border py-1 px-2 text-center">
                            {{ $average }}
                        </td>
                    @endforeach

                    @php
                        $avgTotal = count($totalScores) > 0 ? round(array_sum($totalScores) / count($totalScores), 1) : 0;
                        $avgPercentage = $totalMaxScore > 0 ? round(($avgTotal / $totalMaxScore) * 100, 1) : 0;
                    @endphp

                    <td class="border py-1 px-2 text-center bg-green-100 font-bold" rowspan="2">
                        {{ $avgTotal }} ({{ $avgPercentage }}%)
                    </td>
                </tr>

                <tr class="bg-blue-50 font-bold">
                    <td class="border py-1 px-2" colspan="2">O'zlashtirish foizi (%)</td>
                    @foreach ($problems as $problem)
                        @php
                            $average = isset($problemTotals[$problem->id]) && $problemCounts[$problem->id] > 0
                                ? round($problemTotals[$problem->id] / $problemCounts[$problem->id], 1)
                                : 0;
                            $masteryPercentage = $problem->max_mark > 0
                                ? round(($average / $problem->max_mark) * 100, 1)
                                : 0;
                        @endphp
                        <td class="border py-1 px-2 text-center">
                <span class="{{ $masteryPercentage >= 70 ? 'text-green-600 font-bold' : 'text-red-600' }}">
                    {{ $masteryPercentage }}%
                </span>
                        </td>
                    @endforeach
                </tr>
                </tfoot>

            </table>
        </div>
    @endif
</div>
